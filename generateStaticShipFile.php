<?php

ob_start("ob_gzhandler"); 
include_once './source/public/global.php';

$ships = ShipLoader::getAllShips(null);

$data = [];

foreach ($ships as $faction) {
    foreach ($faction as $ship) {
        if ($ship && $ship instanceof BaseShip) {
            $data[$ship->faction][$ship->phpclass] = $ship;
        }
    }
}

file_put_contents('./source/public/static/ships.js', 'window.staticShips = ' . json_encode($data));

print("ships generated");