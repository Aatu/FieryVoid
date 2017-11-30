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
	    if ($ship instanceof FighterFlight){ //separate procedure for fighters
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
		    if ($exclusiveWasFired) $toReturn = array(); //exclusive weapon was fired, nothing can intercept!
	    }else{ //proper ship
		if (!(($ship->unavailable === true) || $ship->isDisabled())){ //ship itself can fight this turn
			foreach($ship->systems as $weapon){
				if ((!($weapon instanceof Weapon)) || ($weapon->ballistic)) continue; //not a weapon, or a ballistic weapon
				if ((!$weapon->firedOnTurn($currTurn)) && ($weapon->intercept > 0) && (self::isValidInterceptor($gamedata, $weapon))){//not fired this turn, intercept-capable, and valid interceptor
					$toReturn[] = $weapon;  
				}
			}
		}
	    }
	    return $toReturn;
    } //endof getUnassignedInterceptors
	
	
	

	/* returns best possible shot to intercept (or null if none is available)
	*/
	/*
	public static function getBestInterception($gamedata, $ship, $currInterceptor, $incomingShots){
		$bestInterception = null;
		$bestInterceptionVal = 0;		
		foreach($incomingShots as $firingOrder){
			$isLegal = self::isLegalIntercept($gamedata, $ship,$currInterceptor, $firingOrder);
			if (!$isLegal)continue; //not a legal interception at all for this weapon
			$currInterceptionMod = $currInterceptor->getInterceptionMod($gamedata, $firingOrder);
			if ($currInterceptionMod <= 0)continue; //can't effectively intercept
			
			$shooter = $gamedata->getShipById($firingOrder->shooterid);
			$firingWeapon = $shooter->getSystemById($firingOrder->weaponid);
		
			$chosenLoc = $firingOrder->chosenLocation;
			if (!($chosenLoc>0)) $chosenLoc = 0; //just in case it's not set/not a number!
			if ($ship instanceof FighterFlight){
				$armour = 0; //let's simplify here...
			}else{
				$structureSystem = $ship->getStructureSystem($chosenLoc);
				$armour = $structureSystem->getArmour($this, null, $firingWeapon->damageType); //shooter relevant only for fighters - and they don't care about calculating ambiguous damage!
			}
			$expectedDamageMax = $firingWeapon->maxDamage-$armour;
			$expectedDamageMin = $firingWeapon->minDamage-$armour;
			$expectedDamageMax = max(0,$expectedDamageMax);
			$expectedDamageMin = max(0,$expectedDamageMin);
			$expectedDamage = ($expectedDamageMin+$expectedDamageMax)/2; 
			//reduce damage for non-Standard modes...
			switch($firingWeapon->damageType) {
			    case 'Raking': //Raking damage gets reduced multiple times
				$expectedDamage = $expectedDamage * 0.9;
				break;
			    case 'Piercing': //Piercing does little damage to actual outer section...
				$expectedDamage = $expectedDamage * 0.4;
				break;
			    case 'Pulse': //multiple hits - assume half of max pulses hit!
				$expectedDamage = 0.5 * $expectedDamage * max(1,$weapon->maxpulses);
				break;			
			    default: //something else: can't be as good as Standard!
				$expectedDamage = $expectedDamage * 0.9;
				break;
			}
			//if weapon does no damage by itself, assume it has other, very relvant effect - comparable to 10 damage!
			if ($weapon->maxDamage == 0 ) $expectedDamage = 10;
			$expectedDamage = max(0.1,$expectedDamage);//estimate _some_ damage always...
			//multiply by Shots or Maxpulses...
			if ($firingWeapon->damageType == 'Pulse'){
				$expectedDamage = $expectedDamage *max(1,$firingWeapon->maxpulses);
			}else{
				$expectedDamage = $expectedDamage *max(1,$firingWeapon->shots);
			}			
			
			//how much is actually reduced?
			$hitChanceBefore = $firingOrder->needed - $firingOrder->totalIntercept;
			$hitChanceAfter = $hitChanceBefore - $currInterceptionMod;
			$hitChanceAfter = max(0,$hitChanceAfter);//negative numbers are irrelevant
			$modifier = min(100,$hitChanceBefore) - $hitChanceAfter;
			if ($modifier <= 0){ //after interception hit chance is still over 100%... let's count as something, but much less - say, multiply by 0.1!
				$modifier = 0.1 * ($hitChanceBefore - $hitChanceAfter);
			}
			
			//...how much damage is actually stopped?
			$stoppedDamage = $modifier * $expectedDamage;//to get actual damage statistically stopped, You need to multiply this by 0.01 - but it's completely irrelevant for higher/lower comparision
			
			if ($stoppedDamage > $bestInterceptionVal){ //this is best interception candidate found so far!
				$bestInterception = $firingOrder;
				$bestInterceptionVal = $stoppedDamage;
			}
		}
		return $bestInterception;
	}//endof getBestInterception
	*/
	
    

    /*adds indicated weapon's capabilities to total interception variables
    	may create intercept order itself if needed
    */	
	/*
    public static function addToInterceptionTotal($gamedata, $intercepted, $interceptor, $prepareOrder = false){
		//update numbers appropriately	    
	        $intercepted->totalIntercept -= $interceptor->getInterceptionMod($gamedata, $intercepted);
	        $intercepted->numInterceptors++;
	    
		if ($prepareOrder){ //new firing order (intercept) should be prepared?
			$interceptFire = new FireOrder(-1, "intercept", $interceptor->getUnit()->id, $intercepted->id, $interceptor->id, -1, 
				$gamedata->turn, $interceptor->firingMode, 0, 0, $interceptor->defaultShots, 0, 0, null, null
			);
			$interceptFire->addToDB = true;
			$interceptor->fireOrders[] = $interceptFire;
		}
    } //endof addToInterceptionTotal
	*/
	
  /*Marcin Sawicki, October 2017: change approach: allocate interception fire before ANY fire is actually resolved!
  	this allows for auto-intercepting ballistics, too.
  */
    public static function automateIntercept($gamedata){ //automate allocation of intercept weapons
	//prepare list of all potential intercepts and all incoming fire
	$allInterceptWeapons = array();
	$allIncomingShots = array();
	foreach($gamedata->ships as $ship){      
		$interceptWeapons = self::getUnassignedInterceptors($gamedata, $ship)
		$allInterceptWeapons = array_merge($allInterceptWeapons, $interceptWeapons);
		$incomingShots = $ship->getAllFireOrders($gamedata->turn);
		$allIncomingShots = array_merge($allIncomingShots, $incomingShots);
	}
	    
	//update intercepion totals!
	$shotsStillComing = $allIncomingShots;
	foreach($allIncomingShots as $fireOrder){
		if (($fireOrder->type != "selfIntercept") && ($fireOrder->type != "intercept")) continue; //manually assigned interception - no others exist at this point
		//let's find WHAT is being intercepted and update interception totals!
		foreach($shotsStillComing as $intercepted){
			if ($fireOrder->targetid == $intercepted->id){
				$shooter = $gamedata->getShipById($fireOrder->shooterid);
				$firingWeapon = $shooter->getSystemById($fireOrder->weaponid);
				self::addToInterceptionTotal($gamedata, $intercepted, $firingWeapon);
				break; //loop
			}
		}
	}
	    
	//delete fire orders that intercept orders or are hex-targeted or have no chance of hitting
	$shotsStillComing = array();
	foreach($allIncomingShots as $fireOrder){
		if (($fireOrder->needed - $fireOrder->totalIntercept) > 0) continue;//no chance of hitting
		if (($fireOrder->type == "selfIntercept") || ($fireOrder->type == "intercept")) continue; //interception shot
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$firingWeapon = $shooter->getSystemById($fireOrder->weaponid);
		if ($firingWeapon->hextarget) continue;//hex-targeted
		$shotsStillComing[] = $fireOrder;
	}
	$allIncomingShots = $shotsStillComing;
	$shotsStillComing = null; //just free memory
	    
	//sort list of all potential intercepts - most effective first
	usort($allInterceptWeapons, "self::compareInterceptAbility");	    
	    
	//assign interception
	while((count($allInterceptWeapons)>0) ){//weapons can still intercept!
		$currInterceptor = array_shift($allInterceptWeapons); //most capable interceptor available
		for($i = 0; $i<$currInterceptor->guns;$i++){ //a single weapon can intercept multiple times...
			//find shot it would be most profitable to intercept with this weapon, and intercept it!
			$shotToIntercept = self::getBestInterception($gamedata, $ship, $currInterceptor, $incomingShots)
			if ($shotToIntercept != null){
				self::addToInterceptionTotal($gamedata, $shotToIntercept, $currInterceptor, true); //add numbers AND create order
			}
		}
	}    
	    
	//all possible interceptions have been made!	
    } //endof function automateIntercept
	
	
	
	
	
} //endof class Firing




?>
