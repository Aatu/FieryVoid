<?php
declare(strict_types=1);
// Optimised version: Bundles all ships into one file to avoid HTTP/2 request overload
// 12.2025 - Refactored to generate single shipsCombined.js
// 04.2026 - Added compactShipForStaticJson() to strip default/empty values (Optimisation #4)
/**
 * generateStaticShipFileWeb.php
 * Generates static JS files for all ships, BUNDLED into one file.
 * Updated for PHP 8 + Apache + Brotli/Gzip
 */

//// ─── Output Compression ─────────────────────────────────────────────
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

// ----------------------
// High Resource Limits for Generator
// ----------------------
ini_set('memory_limit', '-1'); // Unlimited memory
set_time_limit(300); // 5 minutes

//// ─── Includes ──────────────────────────────────────────────────────
define('IN_STATIC_GENERATION', true);
require_once __DIR__ . '/source/public/global.php';

/**
 * Strip default/empty values from a ship's JSON representation before writing
 * to static files. Mirrors the Optimisation #2 applied to the live gamedata
 * payload in ShipSystem::stripForJson() — the client's ShipSystem constructor
 * already re-applies these exact defaults on load, so omitting them is safe.
 *
 * Fields removed when they equal their default:
 *   - Empty arrays : damage, criticals, fireOrders, power, specialAbilities, critData
 *   - Boolean false: destroyed, jsClass, boostable, canOffLine, fighter, preFires,
 *                    primary, isPrimaryTargetable, forceCriticalRoll,
 *                    advancedArmor, hardAdvancedArmor, fixedPower
 *   - Zero integers: outputMod, critRollMod
 *   - Null / empty string: outputDisplay, specialAbilityValue, imagePath, iconPath,
 *                          individualNotesTransfer, outputType
 */
function compactSystemForStaticJson(array $system): array {
    // Empty arrays
    $emptyArrayKeys = ['damage','criticals','fireOrders','power','specialAbilities','critData'];
    foreach ($emptyArrayKeys as $key) {
        if (isset($system[$key]) && is_array($system[$key]) && empty($system[$key])) {
            unset($system[$key]);
        }
    }
    // Boolean false defaults
    $falseKeys = ['destroyed','jsClass','boostable','canOffLine','fighter','preFires',
                  'primary','isPrimaryTargetable','forceCriticalRoll',
                  'advancedArmor','hardAdvancedArmor','fixedPower'];
    foreach ($falseKeys as $key) {
        if (isset($system[$key]) && $system[$key] === false) {
            unset($system[$key]);
        }
    }
    // Zero integer defaults
    $zeroKeys = ['outputMod','critRollMod'];
    foreach ($zeroKeys as $key) {
        if (isset($system[$key]) && $system[$key] === 0) {
            unset($system[$key]);
        }
    }
    // Null / empty-string defaults
    $nullEmptyKeys = ['outputDisplay','specialAbilityValue','imagePath','iconPath',
                      'individualNotesTransfer','outputType'];
    foreach ($nullEmptyKeys as $key) {
        if (array_key_exists($key, $system) && ($system[$key] === null || $system[$key] === '')) {
            unset($system[$key]);
        }
    }
    return $system;
}

function compactShipForStaticJson(array $ship): array {
    if (!empty($ship['systems'])) {
        foreach ($ship['systems'] as $i => $system) {
            if (is_array($system)) {
                $ship['systems'][$i] = compactSystemForStaticJson($system);
            }
        }
    }
    return $ship;
}

/**
 * Encode a faction's ship data with default-value stripping applied.
 * $data is keyed by phpclass => ship object.
 */
function encodeCompactFactionData(array $data, int $flags = 0): string {
    $compacted = [];
    foreach ($data as $phpclass => $ship) {
        $shipArray = json_decode(json_encode($ship, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE), true);
        if (is_array($shipArray)) {
            $compacted[$phpclass] = compactShipForStaticJson($shipArray);
        } else {
            $compacted[$phpclass] = $ship; // fallback
        }
    }
    return json_encode($compacted, $flags | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
}

//// ─── Config ────────────────────────────────────────────────────────
$fileBase = __DIR__ . '/source/public/static/ships';
$combinedFile = $fileBase . 'Combined.js';

// ----------------------
// OPTIMIZATION: Fetch ALL ships at once O(N) instead of O(N*M)
// ----------------------
$shipsByFaction = ShipLoader::getAllShipsStatic(null);

if (!$shipsByFaction) {
    exit("<b>Error:</b> No ships found.");
}

// Initialize combined file with empty object
file_put_contents($combinedFile, 'window.staticShips = {};' . PHP_EOL);

// Ensure JSON directory exists
$jsonDir = __DIR__ . '/source/public/static/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

//// ─── Generate Per-Faction JS Files ──────────────────────────────────
foreach ($shipsByFaction as $factionName => $shipsOfFaction) {
    $data = [];
    
    echo "<br/><br/><strong>$factionName</strong>:<br/>\n";

    foreach ($shipsOfFaction as $ship) {
        if ($ship && $ship instanceof BaseShip) {
            // Debug output
            echo " &nbsp; - {$ship->phpclass}<br/>\n";

            // Store only what is needed for the static cache
            $data[$ship->phpclass] = $ship;
        }
    }

    // Append this faction to the combined file
    // Use compact encoding to strip default/empty values (Optimisation #4)
    $compactJson = encodeCompactFactionData($data, JSON_NUMERIC_CHECK);
    $chunk = 'window.staticShips["' . $factionName . '"]=' . $compactJson . ';' . PHP_EOL;
    file_put_contents($combinedFile, $chunk, FILE_APPEND);

    // Save pure JSON for server-side caching (Game Lobby)
    $jsonPath = $jsonDir . '/' . $factionName . '.json';
    $jsonPayload = [$factionName => json_decode($compactJson, true)];
    file_put_contents($jsonPath, json_encode($jsonPayload, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE));
    
    unset($data); // Free memory
}

//// ─── Base Files ─────────────────────────────────────────────────────

// PHP file includes the single bundled JS file
$includeText = '<script src="static/shipsCombined.js"></script>';
file_put_contents("{$fileBase}.php", $includeText);

//// ─── Flush server-side caches ───────────────────────────────────────
// This generator is run (in the browser) on every deploy, so it's the natural
// place to flush server caches — belt-and-suspenders alongside the deploy-
// versioned cache prefix in Manager::getCachePrefix(). On shared Apache hosting
// (mod_php / single FPM pool) both caches below are one shared segment across all
// workers, so a single browser hit reaches every live entry.

// APCu = cached DATA (the per-game gamedata JSON). Validated only against each
// game's last_update timestamp, so without a flush it can survive a patch and
// keep serving old-shape data to clients running the new bundle until the game
// is next touched or the 1-hour TTL expires.
if (function_exists('apcu_clear_cache')) {
    $apcuCleared = apcu_clear_cache();
    echo $apcuCleared
        ? "<br/>APCu cache cleared.<br/>\n"
        : "<br/><strong>Warning:</strong> apcu_clear_cache() returned false.<br/>\n";
} else {
    echo "<br/>APCu not available — cache clear skipped.<br/>\n";
}

// OPcache = cached compiled PHP BYTECODE. Independent of APCu. On shared hosting
// with conservative validate_timestamps/revalidate_freq, freshly-uploaded .php
// files can keep executing the OLD compiled code for a while — producing the same
// old-shape gamedata symptom even after APCu is cleared. Resetting here forces a
// recompile of the patched sources on the next request.
if (function_exists('opcache_reset')) {
    $opcacheReset = opcache_reset();
    echo $opcacheReset
        ? "<br/>OPcache reset.<br/>\n"
        : "<br/><strong>Warning:</strong> opcache_reset() returned false (may be disabled or restricted).<br/>\n";
} else {
    echo "<br/>OPcache not available — reset skipped.<br/>\n";
}

// ─── OPcache diagnostics ───
// One-time-per-deploy readout so we can confirm empirically (rather than guess)
// how this shared host runs OPcache. Key questions this answers:
//   - enabled?            → is OPcache even active for web requests
//   - restrict_api        → if set and doesn't match this script's path, the
//                           reset above is silently refused
//   - validate_timestamps → if On (with a short revalidate_freq), the host auto-
//                           notices uploaded .php files and the reset is optional;
//                           if Off, opcache_reset() is the ONLY way to pick up a patch
//   - SAPI                → mod_php (reset reaches all workers) vs fpm-fcgi
//                           (web-request reset may not propagate cleanly)
echo "<br/><strong>OPcache diagnostics</strong> (SAPI: " . htmlspecialchars(PHP_SAPI) . "):<br/>\n";
if (function_exists('opcache_get_status') && function_exists('opcache_get_configuration')) {
    // Pass false: we don't need the (large) per-script list, just the summary.
    $status = @opcache_get_status(false);
    $config = @opcache_get_configuration();

    if ($status === false || $config === false) {
        echo " &nbsp; - opcache_get_status()/get_configuration() returned false "
           . "(API likely restricted via opcache.restrict_api).<br/>\n";
    } else {
        $d = $config['directives'] ?? [];
        $boolStr = function ($v) { return $v ? 'On' : 'Off'; };

        $enabled       = $status['opcache_enabled'] ?? false;
        $restrictApi   = $d['opcache.restrict_api'] ?? '';
        $validateTs    = $d['opcache.validate_timestamps'] ?? null;
        $revalidate    = $d['opcache.revalidate_freq'] ?? null;
        $cachedScripts = $status['opcache_statistics']['num_cached_scripts'] ?? 'n/a';

        echo " &nbsp; - opcache_enabled: " . $boolStr($enabled) . "<br/>\n";
        echo " &nbsp; - validate_timestamps: "
           . ($validateTs === null ? 'n/a' : $boolStr($validateTs))
           . " &nbsp; revalidate_freq: "
           . ($revalidate === null ? 'n/a' : (int)$revalidate) . "s<br/>\n";
        echo " &nbsp; - restrict_api: "
           . ($restrictApi === '' ? '(not set — reset allowed)' : htmlspecialchars($restrictApi))
           . "<br/>\n";
        echo " &nbsp; - cached scripts (after reset): " . htmlspecialchars((string)$cachedScripts) . "<br/>\n";

        // Plain-language guidance based on what we found.
        if (!$enabled) {
            echo " &nbsp; <em>→ OPcache is off for web requests; stale-bytecode is not a concern here.</em><br/>\n";
        } elseif ($validateTs === false) {
            echo " &nbsp; <em>→ validate_timestamps is Off: the reset above is REQUIRED to pick up patched PHP.</em><br/>\n";
        } elseif ($validateTs === true) {
            echo " &nbsp; <em>→ validate_timestamps is On: the host auto-detects uploaded files within revalidate_freq; the reset just makes it instant.</em><br/>\n";
        }
    }
} else {
    echo " &nbsp; - opcache_get_status()/get_configuration() not available.<br/>\n";
}

//// ─── Output Result ─────────────────────────────────────────────────
echo "<br/><br/><big>Ships generated (Bundled)!</big><br/>\n";

exit;