<?php

class Criticals{


    public static function setCriticals($gamedata){
	
        $crits = array();
		//print("criticals");
        foreach ($gamedata->ships as $ship){
            if ($ship->isDestroyed())
                continue;
                
            foreach ($ship->systems as $system){

    			if ($system instanceof MissileLauncher){
	                if ($system->isDamagedOnTurn($gamedata->turn)){       
						$crits = $system->testCritical($ship, $gamedata, $crits);
             	   }
                }

				if ($system->isDestroyed()){
					continue;
				}
				
				if ($system instanceof Thruster){
				
					$chan = Movement::getAmountChanneled($system, $ship, $gamedata->turn);
					$overthrust = $chan - ($system->output + $system->outputMod );
					
					if ($overthrust > 0){
						$crits = $system->testCritical($ship, $gamedata, $crits, $overthrust);
					}
				}
					
				$stru = $ship->getStructureSystem($system->location);
				if ($stru && $stru->isDestroyed())
					continue;
					
				
                if ($system->isDamagedOnTurn($gamedata->turn)){       
					$crits = $system->testCritical($ship, $gamedata, $crits);
                }
            }
        }
        
		//print(var_dump($crits));
        return $crits;
    
    }


}
    

?>
