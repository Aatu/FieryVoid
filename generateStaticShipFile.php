<?php
// Optimised version: Bundles all ships into one file to avoid HTTP/2 request overload
// 12.2025 - Refactored to generate single shipsCombined.js

ob_start("ob_gzhandler"); 	
include_once './source/public/global.php';

$allFactions = ShipLoader::getAllFactionsStatic(); 
$fileBase = './source/public/static/ships';
$combinedFile = $fileBase . 'Combined.js';

// Initialize combined file with empty object
file_put_contents($combinedFile, 'window.staticShips = {};' . PHP_EOL);

// Ensure JSON directory exists
$jsonDir = './source/public/static/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}


foreach($allFactions as $factionName){
	$data = [];
	$shipsCurr = ShipLoader::getAllShipsStatic($factionName);

	print($factionName . "\n");

	foreach ($shipsCurr as $factionKey=>$shipsOfFaction) {
        foreach ($shipsOfFaction as $ship) {
            if ($ship && $ship instanceof BaseShip) {
                print("generating: " . $ship->faction . " " . $ship->phpclass . "\n");
                $data[$ship->phpclass] = $ship;
            }
        }
    }
	
    // Append this faction to the combined file to save memory
	$chunk = 'window.staticShips["' . $factionName . '"]=' . json_encode($data) . ';' . PHP_EOL;
	file_put_contents($combinedFile, $chunk, FILE_APPEND);
    
    // Save pure JSON for server-side caching (Game Lobby)
    $jsonPath = $jsonDir . '/' . $factionName . '.json';
    $jsonPayload = [$factionName => $data];
    file_put_contents($jsonPath, json_encode($jsonPayload, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE));

	$shipsCurr = null; // free memory
    $data = null;
}

// Update ships.php to point to the combined file
$fileName = $fileBase . '.php'; // c:\FV_env\FieryVoid\source\public\static\ships.php
$includeText = '<script src="static/shipsCombined.js"></script>';
file_put_contents($fileName, $includeText);

print("\n ships generated!\n\n");