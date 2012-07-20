<?php

	class ShipLoader{
	
	
		public static function getShipClassnames(){
			$dir = dirname(__DIR__) ."/model/ships/";
			$handle = opendir($dir);
			$list = array();
			while (false !== ($entry = readdir($handle))) {
			
				if (is_dir($dir.$entry) && $entry != "." && $entry != ".."){
					$handle2 = opendir($dir.$entry);
					
					while (false !== ($entry2 = readdir($handle2))){
						if ($entry2 != "." && $entry2 != "..")
							$list[] = substr($entry2, 0, strlen($entry2)-4);
					}
				}
					
			}
			
			return $list;
		}
		
		public static function getAllShips(){
			$names = self::getShipClassnames();
			$ships = array();
			foreach ($names as $name){
				if (class_exists($name)){
					$ship = new $name(0, 0, "name", 0, 0, false, false, array());
					if (!isset($ships[$ship->faction])){
						$ships[$ship->faction] = array();
						
					}
					$ships[$ship->faction][] = $ship;
				}
			}
			
			return $ships;
		
		}
		
	
	}
	
	

?>