<?php

class Criticals{


    public static function setCriticals($gamedata){
	
        $crits = array();
		//print("criticals");
        foreach ($gamedata->ships as $ship){
						
            if ($ship->isDestroyed()) continue;
			               
            foreach ($ship->systems as $system){
				

		    /*no longer needed?...
    		if ($system instanceof MissileLauncher){
	                if ($system->isDamagedOnTurn($gamedata->turn)){       
						$crits = $system->testCritical($ship, $gamedata, $crits);
             	   }
                }
		*/
                if (/*$ship instanceof StarBase && */ $system instanceof SubReactor){ //destroying any Subreactor, not just on base!
                	if ($system->wasDestroyedThisTurn($gamedata->turn)){
	                	//if ($system->location != 0){ ///on any location, PRIMARY too...
	                		$ship->destroySection($system, $gamedata);
	                	//}		
                	}
                }

				/* eliminate block but NOT continue!
				if ($system->isDestroyed() && (!($system instanceof MissileLauncher))){ //missile launchers may still explode
					continue;
				}
				*/
				if (! ($system->isDestroyed() && (!($system instanceof MissileLauncher))) ){
				
					if ($system instanceof Thruster){				
						$chan = Movement::getAmountChanneled($system, $ship, $gamedata->turn);
						$overthrust = $chan - ($system->output + $system->outputMod );
						if ($overthrust > 0){
							$crits = $system->testCritical($ship, $gamedata, $crits, $overthrust);
						}
					}
						
					/*not needed
					$stru = $ship->getStructureSystem($system->location);
					if ($stru && $stru->isDestroyed())
						continue;
					*/	
					
					if ($system->isDamagedOnTurn($gamedata->turn)){       
						$crits = $system->testCritical($ship, $gamedata, $crits);
					}
				}
				
				$system->criticalPhaseEffects($ship, $gamedata); //hook for Critical phase effects
				
            }
        }
        
		//print(var_dump($crits));
        return $crits;
    
    }


}
    

?>
