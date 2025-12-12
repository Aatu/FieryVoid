<?php
declare(strict_types=1);

/**
 * generateStaticShipFileWeb.php
 * Generates static JS files for all ships, split per faction.
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
$factionNo = 0;

//// ─── Fetch All Factions ─────────────────────────────────────────────
//$allFactions = ShipLoader::getAllFactions();
$allFactions = ShipLoader::getAllFactionsStatic(); //Changed to static method - 12.12.25
if (!$allFactions) {
    exit("<b>Error:</b> No factions found.");
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

    // Write faction file
    $factionNo++;
    $fileName = "{$fileBase}{$factionNo}.js";
    $varBase = "window.staticShips[\"{$factionName}\"]=";
    file_put_contents($fileName, $varBase . json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE) . ';');

    unset($data); // Free memory
}

//// ─── Base Files ─────────────────────────────────────────────────────

// JS file 0 initializes the variable
file_put_contents("{$fileBase}0.js", 'window.staticShips = {};');

// PHP file includes all JS files sequentially
$includeText = '';
for ($i = 0; $i <= $factionNo; $i++) {
    $includeText .= '<script src="static/ships' . $i . '.js"></script>' . PHP_EOL;
    //$includeText .= '<script defer src="static/ships' . $i . '.js"></script>' . PHP_EOL;   Alternative defer method that's slower but more stable - DK 
}
file_put_contents("{$fileBase}.php", $includeText);

//// ─── Output Result ─────────────────────────────────────────────────
echo "<br/><br/><big>Ships generated for {$factionNo} factions!</big><br/>\n";

exit;