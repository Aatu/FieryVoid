<?php

/*Marcin Sawicki problems during debug: copuying Firing class method after method*/
/*old version moved to .old file*/

class Firing{
    public $gamedata;
	
    public static function validateFireOrders($fireOrders, $gamedata){
            return true;
    }
	

    //compares weapons' capability as interceptor
    //if intercept rating is the same, faster-firing weapon would go first
    public static function compareInterceptAbility($weaponA, $weaponB){	    
        if ($weaponA->intercept > $weaponB->intercept){
            return 1;
        }else if ($weaponA->intercept < $weaponB->intercept){
            return -1;
        }else if ($weaponA->loadingtime < $weaponB->loadingtime){
            return 1;
        }else if ($weaponA->loadingtime > $weaponB->loadingtime){
            return -1;
        }else{
            return 0;
        }   
    } //endof function compareInterceptAbility
	
	
	/*gets all ready intercept-capable weapons that aren't otherwise assigned*/
    public static function getUnassignedInterceptors($gamedata, $ship){	    
	    $currTurn = $gamedata->turn;
	    $toReturn = array();	    
	    if($ship instanceof FighterFlight){ //separate procedure for fighters
		    $exclusiveWasFired = false;
		    foreach($ship->systems as $fighter){
			    if ($fighter->isDestroyed()) continue;
			    foreach ($fighter->systems as $weapon){
				    if(($weapon instanceof Weapon) && ($weapon->ballistic != true)){
					    if(($weapon->exclusive) && $weapon->firedOnTurn($currTurn)){
						    $exclusiveWasFired = true;
						    continue;
					    }else if((!$weapon->firedOnTurn($currTurn)) && ($weapon->intercept > 0) && (self::isValidInterceptor($gamedata, $weapon))){//not fired this turn, intercept-capable, and valid interceptor
						    if((!isset($weapon->ammunition)) || ($weapon->ammunition > 0)) {//unlimited ammo or still has ammo available							    
						    	$toReturn[] = $weapon;  
						    }
					    }
				    }
			    }
		    }
		    if($exclusiveWasFired) $toReturn = array(); //exclusive weapon was fired, nothing can intercept!
	    }else{ //proper ship
		if(!(($ship->unavailable === true) || $ship->isDisabled())){ //ship itself can fight this turn
			foreach($ship->systems as $weapon){
				if((!($weapon instanceof Weapon)) || ($weapon->ballistic)) continue; //not a weapon, or a ballistic weapon
				if((!$weapon->firedOnTurn($currTurn)) && ($weapon->intercept > 0) && (self::isValidInterceptor($gamedata, $weapon))){//not fired this turn, intercept-capable, and valid interceptor
					$toReturn[] = $weapon;  
				}
			}
		}
	    }
	    return $toReturn;
    } //endof getUnassignedInterceptors
	
	
} //endof class Firing




?>
