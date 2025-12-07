<?php

	class ShipLoader{
	
	
		public static function getShipClassnames(){
			$dir = dirname(__DIR__) ."/model/ships/";
			$handle = opendir($dir);
			$list = array();
			while ($handle !== false && false !== ($entry = readdir($handle))) {
			
				if (is_dir($dir.$entry) && $entry != "." && $entry != ".."){
					$handle2 = opendir($dir.$entry);
					
					while ($handle2 !== false && false !== ($entry2 = readdir($handle2))){
						if ($entry2 != "." && $entry2 != "..")
							$list[] = substr($entry2, 0, -4);
					}
					if ($handle2 !== false) { closedir($handle2); }
				}
					
			}
			if ($handle !== false) { closedir($handle); }
			
			return $list;
		}
		
		public static function getAllShips($faction){
			$names = self::getShipClassnames();
			$ships = array();
            		$count = 0;
			foreach ($names as $name){
				if (class_exists($name)){
                    			$count++;
					$ship = new $name($count, 0, "", 0, 0, false, false, array());
					if($faction && $ship->faction != $faction){
						continue;
					}
					    foreach ($ship->systems as $system){
						$system->beforeTurn($ship, 0, 0);
					    }
        

					//enhancements (for fleet selection)
					Enhancements::setEnhancementOptions($ship);
					
					if (!isset($ships[$ship->faction])){
						$ships[$ship->faction] = array();
					}
					
					$ships[$ship->faction][] = $ship;
				}
			}
			
			return $ships;
		}
		
		public static function getAllFactions(){
			$names = self::getShipClassnames();
			$factions = array();
			$factionSet = array();
			$count = 0;
			
			foreach($names as $name){
				if (class_exists($name)){
					$count++;
					$ship = new $name($count, 0, "", 0, 0, false, false, array());
					if (!isset($factionSet[$ship->faction])){
						$factionSet[$ship->faction] = true;
						$factions[] = $ship->faction;
					}
				}
			}
			
			return $factions;
		}
	}
	
	

?>
