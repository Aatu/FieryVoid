<?php

ob_start("ob_gzhandler"); 
include_once './source/public/global.php';

$ships = ShipLoader::getAllShips(null);

$data = [];

/* original - everything in one file
foreach ($ships as $faction) {
    foreach ($faction as $ship) {
        //print(gettype($ship));
        if ($ship && $ship instanceof BaseShip) {
            print("generating: ");
			print($ship->faction);
			print(" ");
			print($ship->phpclass);
			print("\n");
            $data[$ship->faction][$ship->phpclass] = $ship;
        }
    }
}

file_put_contents('./source/public/static/ships.js', 'window.staticShips = ' . json_encode($data));
*/


// attempting to split into multiple smaller files (so intermediate caches don't notice)
foreach ($ships as $faction) {
    foreach ($faction as $ship) {
        //print(gettype($ship));
        if ($ship && $ship instanceof BaseShip) {
            print("generating: ");
			print($ship->faction);
			print(" ");
			print($ship->phpclass);
			print("\n");
            $data[$ship->faction][$ship->phpclass] = $ship;
        }
    }
}

$fileBase = './source/public/static/ships';
$factionNo = 0;
foreach($data as $factionName=>$preparedFaction){
	$factionNo++;
	$fileName = $fileBase . $factionNo . '.js';
	$varBase = "window.staticShips";
	$varBase .= '["' . $factionName . '"]='; //like: window.staticShips["Abbai"]=
	file_put_contents($fileName, $varBase  . json_encode($preparedFaction) . ';');
}	

//set up "file 0" with variable definition
$fileName = $fileBase . '0.js';
file_put_contents($fileName, 'window.staticShips = {};');


//add base file - with includes:
//<script src="static/ships.php"></script>
$fileName = $fileBase . '.php';
$includeText = '';
for ($i=0;$i<=$factionNo;$i++){
	$includeText .= '<script src="static/ships' . $i . '.js"></script>';
}
file_put_contents($fileName, $includeText);
$includeText = '';

print("\n ships generated!\n\n");