<?php
declare(strict_types=1);
// Optimised version: Bundles all ships into one file to avoid HTTP/2 request overload
// 12.2025 - Refactored to generate single shipsCombined.js
/**
 * generateStaticShipFileWeb.php
 * Generates static JS files for all ships, BUNDLED into one file.
 * Updated for PHP 8 + Apache + Brotli/Gzip
 */

//// ─── Output Compression ─────────────────────────────────────────────
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    // Apache with mod_brotli will auto-handle Brotli if client supports it.
    // ob_gzhandler is safe fallback for gzip/deflate.
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

//// ─── Includes ──────────────────────────────────────────────────────
require_once __DIR__ . '/source/public/global.php';

//// ─── Config ────────────────────────────────────────────────────────
$fileBase = __DIR__ . '/source/public/static/ships';
$combinedFile = $fileBase . 'Combined.js';

//// ─── Fetch All Factions ─────────────────────────────────────────────
//$allFactions = ShipLoader::getAllFactions();
$allFactions = ShipLoader::getAllFactionsStatic(); //Changed to static method - 12.12.25
if (!$allFactions) {
    exit("<b>Error:</b> No factions found.");
}

// Initialize combined file with empty object
file_put_contents($combinedFile, 'window.staticShips = {};' . PHP_EOL);

// Ensure JSON directory exists
$jsonDir = __DIR__ . '/source/public/static/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

//// ─── Generate Per-Faction JS Files ──────────────────────────────────
foreach ($allFactions as $factionName) {
    $data = [];

    //$shipsCurr = ShipLoader::getAllShips($factionName);
    $shipsCurr = ShipLoader::getAllShipsStatic($factionName); //Changed to static method - 12.12.25   
    echo "<br/><br/><strong>$factionName</strong>:<br/>\n";

    foreach ($shipsCurr as $factionKey => $shipsOfFaction) {
        foreach ($shipsOfFaction as $ship) {
            if ($ship && $ship instanceof BaseShip) {
                // Debug output
                echo " &nbsp; - {$ship->phpclass}<br/>\n";

                // Store only what is needed for the static cache
                $data[$ship->phpclass] = $ship;
            }
        }
    }

    // Free memory after each faction
    unset($shipsCurr);

    // Append this faction to the combined file
    $chunk = 'window.staticShips["' . $factionName . '"]=' . json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) . ';' . PHP_EOL;
    file_put_contents($combinedFile, $chunk, FILE_APPEND);

    // Save pure JSON for server-side caching (Game Lobby)
    $jsonPath = $jsonDir . '/' . $factionName . '.json';
    $jsonPayload = [$factionName => $data];
    file_put_contents($jsonPath, json_encode($jsonPayload, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE));

    unset($data); // Free memory
}

//// ─── Base Files ─────────────────────────────────────────────────────

// PHP file includes the single bundled JS file
$includeText = '<script src="static/shipsCombined.js"></script>';
file_put_contents("{$fileBase}.php", $includeText);

//// ─── Output Result ─────────────────────────────────────────────────
echo "<br/><br/><big>Ships generated (Bundled)!</big><br/>\n";

exit;