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
		/*SubReactor is now obsoleted! replaced by SubReactorUniversal
                if ($system instanceof SubReactor){ //destroying any Subreactor, not just on base!
                	if ($system->wasDestroyedThisTurn($gamedata->turn)){
	                	//if ($system->location != 0){ ///on any location, PRIMARY too...
	                		$ship->destroySection($system, $gamedata);
	                	//}		
                	}
                }
		*/

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

				if($system instanceof Weapon){
					//for last segment of Sustained shot - force shutdown!
					if(!$system->isDestroyed() && !$system->isOfflineOnTurn()){
						$newExtraShots = $system->overloadshots - 1; 	
						if( $newExtraShots == 0 ) {
							$crit = new ForcedOfflineOneTurn(-1, $ship->id, $system->id, "ForcedOfflineOneTurn", $gamedata->turn);
							$crit->updated = true;
							$crit->newCrit = true; //force save even if crit is not for current turn
							$system->criticals[] =  $crit;
						}
					}	
				}					

				$system->criticalPhaseEffects($ship, $gamedata); //hook for Critical phase effects
				
					//Now check for any EngineShorted Crits added by testCritical or criticalPhaseEffects()
					if ($system instanceof Engine) {
					    foreach ($system->criticals as $critical) {
					        if ($critical->phpclass === "EngineShorted" && $critical->inEffect) {
					            // Check if it matches the current turn
					            if ($critical->turn == $gamedata->turn) {		                
					                // Found a matching "Engine Shorted" critical for this turn
					                $system->doEngineShorted($ship, $gamedata);
					                break;
					            }
					        }
					    }
					}
            }
        }
        
		//print(var_dump($crits));
        return $crits;
    
    }


}
    

?>
