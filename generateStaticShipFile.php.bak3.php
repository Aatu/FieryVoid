<?php
/*11.12.2025 - remade so every faction is compiled separately (one of programmers encountered memory allocation problems)
.bak file saved just in case
*/
	
ob_start("ob_gzhandler"); 	
include_once './source/public/global.php';


/*
$factionTest = ShipLoader::getAllShips("Minbari");
	print("TEST HERE");
	print("\n");
	return;
*/


//$ships = ShipLoader::getAllShips(null);

//$allFactions = ShipLoader::getAllFactions(); 
$allFactions = ShipLoader::getAllFactionsStatic(); //Changed to static method - 12.12.25

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



$fileBase = './source/public/static/ships';
$factionNo = 0;



foreach($allFactions as $factionName){
	$data = [];
	
	//$shipsCurr = ShipLoader::getAllShips($factionName);
	$shipsCurr = ShipLoader::getAllShipsStatic($factionName); //Changed to static method - 12.12.25

	print($factionName);
	print("\n");

	// attempting to split into multiple smaller files (so intermediate caches don't notice)
	foreach ($shipsCurr as $factionKey=>$shipsOfFaction) foreach ($shipsOfFaction as $ship) {
		if ($ship && $ship instanceof BaseShip) {
			print("generating: ");
			print($ship->faction);
			print(" ");
			print($ship->phpclass);
			print("\n");
			$data[$ship->phpclass] = $ship;
		}
	}
	
	
	$shipsCurr = ''; //just clear memory...
	
	$factionNo++;
	$fileName = $fileBase . $factionNo . '.js';
	$varBase = "window.staticShips";
	$varBase .= '["' . $factionName . '"]='; 
	file_put_contents($fileName, $varBase  . json_encode($data) . ';');
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
	//$includeText .= '<script defer src="static/ships' . $i . '.js"></script>' . PHP_EOL;   Alternative defer method that's slower but more stable - DK 	
}
file_put_contents($fileName, $includeText);

print("\n ships generated!\n\n");