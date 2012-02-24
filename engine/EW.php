<?php
	class EW{
	
	
		public static function validateEW($EW, $gamedata){
			
			$turn = $gamedata->turn;
			$used = 0;
			$ship = null;
			
			foreach ($EW as $entry){
			
				if ($turn != $entry->turn)
					continue;
				

				$ship = $gamedata->getShipById($entry->shipid);
				$used += $entry->amount;
			}
			
			if ($ship == null)
				return false;
				
			$available = self::getScannerOutput($ship, $gamedata->turn);

			if ($available >= $used){
				return true;
			}
			
			return false;
		}
		
		public static function getScannerOutput($ship, $turn){
		
			$output = 0;
			foreach ($ship->systems as $system){
			
				if ($system instanceof Scanner){
					$output += $system->getScannerOutput($turn);
				}
			}
			
			return $output;
				
		}
	}
?>