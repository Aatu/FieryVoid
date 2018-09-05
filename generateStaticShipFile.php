<?php

ob_start("ob_gzhandler"); 
include_once './source/public/global.php';

$ships = ShipLoader::getAllShips(null);

$data = [];

foreach ($ships as $faction) {
    foreach ($faction as $ship) {
        //print(gettype($ship));
        if ($ship && $ship instanceof BaseShip) {
            print("generating: ");
			print($ship->faction);
			print(" ");
			print($ship->phpclass);
			print("\n");
            if ($ship && $ship instanceof BaseShip) {
                $data[$ship->faction][$ship->phpclass] = $ship;
            }
        }
    }
}

file_put_contents('./source/public/static/ships.js', 'window.staticShips = ' . json_encode($data));

print("\n ships generated!\n\n");