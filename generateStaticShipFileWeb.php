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
// NOTE: memory_limit is deliberately FINITE, not '-1'.
//
// This host runs LiteSpeed, whose persistent lsphp workers have their own Memory
// Hard Limit (configured in the LiteSpeed/panel config, NOT php.ini). With
// memory_limit='-1' PHP will happily balloon past that limit, at which point LSWS
// kills the worker mid-request and the browser gets an opaque 503. A finite limit
// makes PHP fail first, with a real, readable "allowed memory exhausted" error and
// a line number, instead of the process being SIGKILLed out from under us.
ini_set('memory_limit', '1024M');
set_time_limit(300); // 5 minutes

// ----------------------
// Progress + memory log (survives a worker kill) — DISABLED by default
// ----------------------
// Diagnostic instrumentation from the 2026-07 intermittent-503 investigation. It is
// OFF; flip GEN_MEMORY_LOG to true to turn it back on. Leave it off in normal use —
// it writes a file on every deploy and we don't need it once the generator is healthy.
//
// Turn it ON if the 503 (or any mid-run death) ever returns. If LiteSpeed kills the
// lsphp worker on its memory hard limit, the response is discarded and any echo'd
// diagnostics are lost with it — so this appends progress to a file instead. After a
// failure, read source/public/static/generator-log.txt: the LAST line tells you which
// faction was being processed and how much memory was in use when the process died,
// and the 'boot' line's rss= of a SECOND consecutive run tells you whether the worker
// came in already bloated from the previous run (the original root cause).
define('GEN_MEMORY_LOG', false);

$genLogFile = __DIR__ . '/source/public/static/generator-log.txt';
$genLogStart = microtime(true);

// Process RSS as the OS sees it. THIS is the figure LiteSpeed's lsphp Memory Hard
// Limit is checked against — not php.ini's memory_limit, and not memory_get_usage(),
// which only reports PHP's own arena and is reset per request. On a persistent lsphp
// worker the RSS is what carries over between runs, so if the 'boot' line of a run
// already shows a high rss=, the worker is bloated from the PREVIOUS run and is a
// prime candidate to be killed (→ 503) partway through this one.
$genRss = static function (): float {
    if (!@is_readable('/proc/self/status')) {
        return -1.0; // non-Linux (e.g. Windows dev box)
    }
    $status = @file_get_contents('/proc/self/status');
    if ($status !== false && preg_match('/^VmRSS:\s+(\d+)\s+kB/mi', $status, $m)) {
        return ((float)$m[1]) / 1024.0; // kB -> MB
    }
    return -1.0;
};

$genLog = function (string $msg) use ($genLogFile, $genLogStart, $genRss): void {
    if (!GEN_MEMORY_LOG) {
        return; // instrumentation disabled — all $genLog() call sites become no-ops
    }
    $rss = $genRss();
    $line = sprintf(
        "[%s] +%05.1fs  rss=%s  cur=%6.1fMB  peak=%6.1fMB  %s\n",
        date('Y-m-d H:i:s'),
        microtime(true) - $genLogStart,
        $rss < 0 ? '   n/a  ' : sprintf('%6.1fMB', $rss),
        memory_get_usage(true) / 1048576,
        memory_get_peak_usage(true) / 1048576,
        $msg
    );
    file_put_contents($genLogFile, $line, FILE_APPEND | LOCK_EX);
};

// Append (do NOT truncate): when enabled we want to compare a FIRST run against a
// SECOND run in the same lsphp worker — the second run's boot rss= is the whole
// diagnosis. A run marker keeps them apart.
if (GEN_MEMORY_LOG) {
    file_put_contents($genLogFile, "\n=== generator run started (pid " . getmypid() . ") ===\n", FILE_APPEND | LOCK_EX);
}
$genLog('boot (before includes)');

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
                      'individualNotesTransfer','outputType','linkedFiringGroup','linkedFiringSpread'];
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

//// ─── Config ────────────────────────────────────────────────────────
$fileBase = __DIR__ . '/source/public/static/ships';
$combinedFile = $fileBase . 'Combined.js';

$jsonDir = __DIR__ . '/source/public/static/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

$encodeFlags = JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE;

//// ─── STREAMING GENERATION ──────────────────────────────────────────
//
// We deliberately do NOT call ShipLoader::getAllShipsStatic(null) here any more.
//
// That helper instantiates every ship class in the game (~2700 ships, each with its
// full system list, beforeTurn(), enhancement options and notesFill()) and returns
// them ALL at once. Measured, that single call allocates ~350MB and the script peaks
// near 385MB — and PHP's allocator does not hand that memory back to the OS, so a
// persistent LiteSpeed lsphp worker stays bloated after the run. On the NEXT run that
// warm worker allocates on top of the residue, crosses LiteSpeed's lsphp Memory Hard
// Limit, and LSWS kills it mid-request → the intermittent 503 on every second run.
//
// Nothing here needs all the ships simultaneously — only one ship at a time, long
// enough to encode it. So we stream: instantiate → encode → discard. Only the compact
// JSON text is retained, spooled to a per-faction temp fragment on disk, which keeps
// peak memory flat regardless of how many ships exist.
//
// This replicates getAllShipsStatic()'s semantics EXACTLY so the output is unchanged:
//   - same class-name iteration order (getShipClassnamesStatic)
//   - $count incremented once per existing class, and used as the ship id
//   - same construction args, beforeTurn() on every system, setEnhancementOptions(),
//     notesFill()
//   - factions keyed in first-seen order
$genLog('streaming generation: start');

$names = ShipLoader::getShipClassnamesStatic();
if (!$names) {
    exit("<b>Error:</b> No ships found.");
}

// Per-faction fragment spool. One open handle per faction (~90), not per ship.
$fragDir = sys_get_temp_dir() . '/fv_shipgen_' . getmypid();
if (!is_dir($fragDir)) {
    mkdir($fragDir, 0777, true);
}

$factionOrder = [];   // faction names, in first-seen order (mirrors old array key order)
$fragHandles  = [];   // faction => resource
$fragPaths    = [];   // faction => path
$factionFirst = [];   // faction => bool, is the next ship the first of this faction?
$shipCount    = 0;
$count        = 0;    // MUST mirror getAllShipsStatic()'s $count — it becomes the ship id

foreach ($names as $name) {
    if (!class_exists($name)) {
        continue;
    }
    $count++;

    $ship = new $name($count, 0, "", 0, 0, false, false, array());
    if (!($ship instanceof BaseShip)) {
        unset($ship);
        continue;
    }

    foreach ($ship->systems as $system) {
        $system->beforeTurn($ship, 0, 0);
    }
    Enhancements::setEnhancementOptions($ship); // enhancements (for fleet selection)
    $ship->notesFill();

    $faction = $ship->faction;

    if (!isset($fragHandles[$faction])) {
        $factionOrder[] = $faction;
        $fragPaths[$faction]    = $fragDir . '/' . md5($faction) . '.frag';
        $fragHandles[$faction]  = fopen($fragPaths[$faction], 'wb');
        $factionFirst[$faction] = true;

        echo "<br/><br/><strong>$faction</strong>:<br/>\n";
    }

    echo " &nbsp; - {$ship->phpclass}<br/>\n";

    // Encode this one ship, exactly as encodeCompactFactionData() used to per-ship.
    $shipArray = json_decode(json_encode($ship, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE), true);
    $shipJson  = is_array($shipArray)
        ? json_encode(compactShipForStaticJson($shipArray), $encodeFlags)
        : json_encode($ship, $encodeFlags); // fallback, mirrors the old code path

    // Write "phpclass":{...} — assembling the object map by hand, comma-separated.
    // Key encoding uses the same flags as before, so escaping is identical.
    fwrite(
        $fragHandles[$faction],
        ($factionFirst[$faction] ? '' : ',')
        . json_encode((string)$ship->phpclass, JSON_UNESCAPED_UNICODE)
        . ':' . $shipJson
    );
    $factionFirst[$faction] = false;
    $shipCount++;

    // Drop everything for this ship before moving to the next one. This is the whole
    // point: at no moment do we hold more than a single ship's object graph.
    unset($ship, $shipArray, $shipJson, $system);

    // Periodic memory sample. A FLAT curve here means we are holding only one ship at
    // a time as intended (the residual footprint is just the ~2500 loaded ship CLASS
    // definitions, which is unavoidable). A curve that keeps CLIMBING with ship count
    // would mean something is retaining ships and is a real leak worth chasing.
    if ($shipCount % 250 === 0) {
        $genLog("  ..progress: {$shipCount} ships");
    }
}

$genLog("streamed {$shipCount} ships across " . count($factionOrder) . ' factions');

// ─── Assemble the output files from the fragments ───
// Fragments are copied through in chunks (stream_copy_to_stream), so even a large
// faction is never fully resident in PHP memory.
$combinedHandle = fopen($combinedFile, 'wb');
fwrite($combinedHandle, 'window.staticShips = {};' . PHP_EOL);

foreach ($factionOrder as $factionName) {
    fclose($fragHandles[$factionName]);
    $fragPath = $fragPaths[$factionName];

    // shipsCombined.js: window.staticShips["Faction"]={ ...fragment... };
    fwrite($combinedHandle, 'window.staticShips["' . $factionName . '"]={');
    $in = fopen($fragPath, 'rb');
    stream_copy_to_stream($in, $combinedHandle);
    fclose($in);
    fwrite($combinedHandle, '};' . PHP_EOL);

    // Per-faction JSON for server-side caching (Game Lobby): {"Faction":{ ...fragment... }}
    $jsonPath   = $jsonDir . '/' . $factionName . '.json';
    $jsonHandle = fopen($jsonPath, 'wb');
    fwrite($jsonHandle, '{' . json_encode((string)$factionName, JSON_UNESCAPED_UNICODE) . ':{');
    $in = fopen($fragPath, 'rb');
    stream_copy_to_stream($in, $jsonHandle);
    fclose($in);
    fwrite($jsonHandle, '}}');
    fclose($jsonHandle);

    unlink($fragPath);
}

fclose($combinedHandle);
@rmdir($fragDir);

$genLog('all factions written');

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

// OPcache = cached compiled PHP BYTECODE. Independent of APCu.
//
// We deliberately DO NOT call opcache_reset() here.
//
// This host runs LiteSpeed (SAPI: litespeed) with opcache.validate_timestamps=On
// and revalidate_freq=30s. That means freshly-uploaded .php files are auto-detected
// and lazily recompiled per-file within the revalidate window — no manual reset is
// needed to pick up a patch.
//
// A blanket opcache_reset() from inside a live web request on LiteSpeed empties the
// entire shared-SHM bytecode cache (~2500 scripts) at once, forcing the lsphp worker
// pool to recompile the whole codebase simultaneously. That recompile storm can
// exhaust the pool / trip a worker limit, and LiteSpeed responds with an intermittent
// 503 to whichever request lands in the window (fixed by an lsphp pool restart, e.g.
// re-saving PHP settings in the host panel). Since validate_timestamps already gives
// us correct pickup for free, the reset is pure downside here and is intentionally
// omitted. If instant (sub-30s) pickup is ever required, lower revalidate_freq at the
// host level instead of resetting from a web request.
echo "<br/>OPcache reset skipped by design (validate_timestamps=On handles pickup; "
   . "reset would risk a LiteSpeed recompile-storm 503).<br/>\n";

// --- Original blanket reset, disabled (kept for reference / quick restore). ---
// Re-enable ONLY if validate_timestamps is turned Off at the host level, and be
// aware it can cause the intermittent LiteSpeed 503 documented above.
// if (function_exists('opcache_reset')) {
//     $opcacheReset = opcache_reset();
//     echo $opcacheReset
//         ? "<br/>OPcache reset.<br/>\n"
//         : "<br/><strong>Warning:</strong> opcache_reset() returned false (may be disabled or restricted).<br/>\n";
// } else {
//     echo "<br/>OPcache not available — reset skipped.<br/>\n";
// }

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

//// ─── Memory report ─────────────────────────────────────────────────
// The number that matters for the intermittent 503: if PEAK approaches the
// LiteSpeed lsphp Memory Hard Limit, a warm (already-bloated) worker on a second
// consecutive run will cross it and be killed → 503. Compare this figure against
// the host's LiteSpeed memory limit, not against php.ini's memory_limit.
$genLog('run complete');
$peakMb = memory_get_peak_usage(true) / 1048576;
echo "<br/><strong>Peak memory:</strong> " . number_format($peakMb, 1) . " MB"
   . " &nbsp;(php.ini memory_limit: " . htmlspecialchars((string)ini_get('memory_limit')) . ")<br/>\n";
echo " &nbsp; <em>Compare this against the LiteSpeed lsphp Memory Hard Limit — if it is "
   . "close, a warm worker on a repeat run will be killed and you get a 503.</em><br/>\n";

//// ─── Output Result ─────────────────────────────────────────────────
echo "<br/><br/><big>Ships generated (Bundled)!</big><br/>\n";

exit;