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
        
        public static function getBlanketDEW($gamedata, $target)
        {
            $FDEW = 0;
            foreach ($gamedata->ships as $ship)
            {
                if ($ship->team == $target->team && $ship->elint
                     && Mathlib::getDistanceHex( $target->getCoPos(), $ship->getCoPos() ) <= 20){
                    $blanket = $ship->getBlanketDEW($gamedata->turn);
                    
                    if ( $blanket > $FDEW )
                        $FDEW += $ship->getBlanketDEW($gamedata->turn);
                }
            }
            
            return $FDEW;
        }
	}
