<?php
// Optimised version: Bundles all ships into one file to avoid HTTP/2 request overload
// 12.2025 - Refactored to generate single shipsCombined.js
// 04.2026 - Added compactShipForStaticJson() to strip default/empty values (Optimisation #4)

ob_start("ob_gzhandler"); 	
define('IN_STATIC_GENERATION', true);
include_once './source/public/global.php';

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

// ----------------------
// High Resource Limits
// ----------------------
ini_set('memory_limit', '-1'); 
set_time_limit(300);

$fileBase = './source/public/static/ships';
$combinedFile = $fileBase . 'Combined.js';
file_put_contents($combinedFile, 'window.staticShips = {};' . PHP_EOL);

// Clear server-side cache (ShipLoader)
$cacheDir = sys_get_temp_dir() . '/fv_cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
    print("Server-side cache cleared.\n");
}

// Ensure JSON directory exists
$jsonDir = './source/public/static/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

// ----------------------
// OPTIMIZATION: Fetch ALL ships at once
// ----------------------
$shipsByFaction = ShipLoader::getAllShipsStatic(null);

if (!$shipsByFaction) {
    die("Error: No ships found.\n");
}

foreach($shipsByFaction as $factionName => $shipsOfFaction){
	$data = [];
	print($factionName . "\n");

    foreach ($shipsOfFaction as $ship) {
        if ($ship && $ship instanceof BaseShip) {
            print("generating: " . $ship->faction . " " . $ship->phpclass . "\n");
            $data[$ship->phpclass] = $ship;
        }
    }
	
    // Append this faction to the combined file to save memory
    // Use compact encoding to strip default/empty values (Optimisation #4)
    $compactJson = encodeCompactFactionData($data, JSON_NUMERIC_CHECK);
	$chunk = 'window.staticShips["' . $factionName . '"]=' . $compactJson . ';' . PHP_EOL;
	file_put_contents($combinedFile, $chunk, FILE_APPEND);
    
    // Save pure JSON for server-side caching (Game Lobby)
    $jsonPath = $jsonDir . '/' . $factionName . '.json';
    $jsonPayload = [$factionName => json_decode($compactJson, true)];
    file_put_contents($jsonPath, json_encode($jsonPayload, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE));

    $data = null; // free memory
}

// Update ships.php to point to the combined file
$fileName = $fileBase . '.php'; // c:\FV_env\FieryVoid\source\public\static\ships.php
$includeText = '<script src="static/shipsCombined.js?v=' . time() . '"></script>';
file_put_contents($fileName, $includeText);

print("\n ships generated!\n\n");