<?php
/*old version of this file moved to .old file*/

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
	
    

    /*adds indicated weapon's capabilities to total interception variables
    	may create intercept order itself if needed
    */	
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
    } //endof function addToInterceptionTotal

	
	
  /*Marcin Sawicki, October 2017: change approach: allocate interception fire before ANY fire is actually resolved!
  	this allows for auto-intercepting ballistics, too.
  */
    public static function automateIntercept($gamedata){ //automate allocation of intercept weapons
	//prepare list of all potential intercepts and all incoming fire

	$allInterceptWeapons = array();
	$allIncomingShots = array();
	foreach($gamedata->ships as $ship){      
		$interceptWeapons = self::getUnassignedInterceptors($gamedata, $ship);
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
		if (($fireOrder->needed - $fireOrder->totalIntercept) <= 0) continue;//no chance of hitting
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
	    
	    
$aaa = "No of int weapons: " + count($allInterceptWeapons) + " , no of incoming shots: " +count( $incomingShots);
throw new Exception("$aaa - firing automateIntercept");	    

	//assign interception
	while((count($allInterceptWeapons)>0) ){//weapons can still intercept!
		$currInterceptor = array_shift($allInterceptWeapons); //most capable interceptor available
		for($i = 0; $i<$currInterceptor->guns;$i++){ //a single weapon can intercept multiple times...
			//find shot it would be most profitable to intercept with this weapon, and intercept it!
			$shotToIntercept = self::getBestInterception($gamedata, $ship, $currInterceptor, $incomingShots);
			if ($shotToIntercept != null){
				self::addToInterceptionTotal($gamedata, $shotToIntercept, $currInterceptor, true); //add numbers AND create order
			}
		}
	}    

	//all possible interceptions have been made!	
    } //endof function automateIntercept
	
	

	
    private static function getFighterIntercepts($gd, $ship){
        $intercepts = Array(); 
        foreach($ship->systems as $fighter){
            $exclusiveWasFired = false;
            
            if ($fighter->isDestroyed())  continue;
            
            // check if fighter is firing weapons that exclude other
            // weapons from firing. (Like IonBolt on a Rutarian.)
            foreach($fighter->systems as $weapon){
                if (($weapon instanceof Weapon) && ($weapon->ballistic != true)){
                    if ($weapon->exclusive && $weapon->firedOnTurn($gd->turn)){
                        $exclusiveWasFired = true;
                        break;
                    }
                }
            }
            
            if ($exclusiveWasFired) continue;

            
            foreach($fighter->systems as $weapon)
            {
                if ($weapon instanceof PairedGatlingGun && $weapon->ammunition < 1){
                    continue;
                }
                if (self::isValidInterceptor($gd, $weapon) === false){
                    continue;
                }
                $possibleIntercepts = self::getPossibleIntercept($gd, $ship, $weapon, $gd->turn);
                $intercepts[] = new Intercept($ship, $weapon, $possibleIntercepts);
            }
        }
        return $intercepts;
     } //endof function getFighterIntercepts
    
	
    private static function getShipIntercepts($gd, $ship)
    {
        $intercepts = Array(); 
        
        foreach($ship->systems as $weapon)
        {
            if (self::isValidInterceptor($gd, $weapon) === false) continue;
            $possibleIntercepts = self::getPossibleIntercept($gd, $ship, $weapon, $gd->turn);
            $intercepts[] = new Intercept($ship, $weapon, $possibleIntercepts);
        }
        return $intercepts;
    } //endof function getShipIntercepts
	
	
	
	

    private static function isValidInterceptor($gd, $weapon)
    {
        if (!($weapon instanceof Weapon))
            return false;
        $weapon = $weapon->getWeaponForIntercept();
        
        if (!$weapon)
            return false;
        if(property_exists($weapon, "ballisticIntercept")){
            return false;
        }
        
        if ($weapon->intercept == 0)
            return false;
        if ($weapon->isDestroyed()){
            //print($weapon->displayName . " is destroyed and cannot intercept " . $weapon->id);
            return false;
        }
        if ($weapon->isOfflineOnTurn($gd->turn))
            return false;
        if ($weapon->ballistic)
            return false;
            // not loaded yet
        if ($weapon->getTurnsloaded() < $weapon->getLoadingTime()){
            return false;
        }
        
        if ($weapon->getLoadingTime() > 1){
            if (isset($weapon->fireOrders[0])){
                if ($weapon->fireOrders[0]->type != "selfIntercept"){
                    return false;
                }
            } else return false;
        }
        
        if ($weapon->getLoadingTime() == 1 && $weapon->firedOnTurn($gd->turn)){
            return false;
        }
        return true;
    }
    
    public static function doIntercept($gd, $ship, $intercepts){
        //returns all valid interceptors as $intercepts
        if (sizeof($intercepts) == 0){
        //    debug::log($ship->phpclass." has nothing to intercept.");
            return;
        };
        usort ( $intercepts , "self::compareIntercepts" );        
        foreach ($intercepts as $intercept){
            $intercept->chooseTarget($gd);
        }
    }
    
    public static function compareIntercepts($a, $b){
        if (sizeof($a->intercepts)>sizeof($b->intercepts)){
            return -1;
        }else if (sizeof($b->intercepts)>sizeof($a->intercepts)){
            return 1;
        }else{
            return 0;
        }
    }
	
	
	

    public static function getPossibleIntercept($gd, $ship, $weapon, $turn){
        
        $intercepts = array();
        
        foreach($gd->ships as $shooter){
            if ($shooter->id == $ship->id)
                continue;
            
            if ($shooter->team == $ship->team)
                continue;
            
            $fireOrders = $shooter->getAllFireOrders();
            foreach($fireOrders as $fire){
                if ($fire->turn != $turn)
                    continue;
                
                if ($fire->type == "ballistic")
                    continue;
                
                if (self::isLegalIntercept($gd, $ship, $weapon, $fire)){
                    $intercepts[] = new InterceptCandidate($fire);
                }
            }
        }
        return $intercepts;
    }
    
	
	
	/*would this be a legal interception?...*/
    public static function isLegalIntercept($gd, $ship, $weapon, $fire){
        if ($fire->type=="intercept"){
            //Debug::log("Fire is intercept\n");
            return false;
        }
        if ($fire->type=="selfIntercept"){
            //Debug::log("Fire is intercept\n");
            return false;
        }
        if ($weapon instanceof DualWeapon)
            $weapon->getFiringWeapon($fire);
        
        if ($weapon->intercept == 0){
            //Debug::log("Weapon has intercept of zero\n");
            return false;
        }
        
        $shooter = $gd->getShipById($fire->shooterid);
        $target = $gd->getShipById($fire->targetid);
        $firingweapon = $shooter->getSystemById($fire->weaponid);
    
        if ($firingweapon->uninterceptable){
            //Debug::log("Target weapon is uninterceptable\n");
            return false;
        }
                
        if ($shooter->id == $ship->id){
            //Debug::log("Fire is my own\n");
            return false;
        }
            
        if ($shooter->team == $ship->team){
            //Debug::log("Fire is friendly\n");
            return false;
        }
	    
	    if($firingweapon->ballistic){
		$movement = $shooter->getLastTurnMovement($fire->turn);
		$pos = mathlib::hexCoToPixel($movement->x, $movement->y); //launch hex	    
		$relativeBearing = $ship->getBearingOnPos($pos);    
	    }else{
		    $pos = $shooter->getCoPos(); //current hex of firing unit
		$relativeBearing = $ship->getBearingOnUnit($shooter);
	    }
      
        if (!mathlib::isInArc($relativeBearing, $weapon->startArc, $weapon->endArc)){
            //Debug::log("Fire is not on weapon arc\n");
            return false;
        }
        
        if ($target->id == $ship->id){
            return true;
        }else{
            if (!$weapon->freeintercept){
                //Debug::log("Target is another ship, and this weapon is not freeintercept \n");
                return false;
            }
            //Debug::log("Target is this another ship\n");
		
		/*new approach: bearing to target is opposite to bearing shooter, +/- 60 degrees*/
		//$oppositeBearing = mathlib::addToDirection($relativeBearing,180);//bearing exactly opposite to incoming shot
		$oppositeBearingFrom = mathlib::addToDirection($relativeBearing,120);//bearing exactly opposite to incoming shot, minus 60 degrees
		$oppositeBearingTo = mathlib::addToDirection($oppositeBearingFrom,120);//bearing exactly opposite to incoming shot, plus 60 degrees
		$targetBearing = $ship->getBearingOnUnit($target);
		if( mathlib::isInArc($targetBearing, $oppositeBearingFrom, $oppositeBearingTo)){
			//Debug::log("VALID INTERCEPT\n");
			return true;
		}
        }
         //Debug::log("INVALID INTERCEPT\n");   
         return false;   
    } //endof function isLegalIntercept
	
	
	
    /*Marcin Sawicki: count hit chances for starting fire phase fire*/
    public static function prepareFiring($gamedata){
        $currFireOrders  = array();   
	$ambiguousFireOrders  = array();   
	foreach ($gamedata->ships as $ship){	    
		foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
			if ($fire->type === "intercept" || $fire->type === "selfIntercept"){
			    continue;
			}
			$weapon = $ship->getSystemById($fire->weaponid);
			if ($weapon instanceof Thruster || $weapon instanceof Structure){
			    continue;
			}
			$fire->priority = $weapon->priority;
			$currFireOrders[] = $fire;
		}
		//calculate hit chances if no ambiguousness exists...
		foreach($currFireOrders as $fireOrder){
			$weapon = $ship->getSystemById($fireOrder->weaponid);
			if($weapon->isTargetAmbiguous($gamedata, $fireOrder)){
				$ambiguousFireOrders[] = $fireOrder;
			}else{
				$weapon->calculateHitBase($gamedata, $fireOrder);			
			}
		}
		//calculate hit chances for ambiguous firing!
		foreach($ambiguousFireOrders as $fireOrder){
			$weapon = $ship->getSystemById($fireOrder->weaponid);
			$weapon->calculateHitBase($gamedata, $fireOrder);
		} 
	}
    }//endof function prepareFiring	
	
	
	

	/*actual firing of weapons
	Marcin Sawicki, October 2017: at this stage, assume all necessary calculations (hit chance, target section), and only raw rolling remains!
	*/
    public static function fireWeapons($gamedata){	
        $fireOrders  = array();
        foreach ($gamedata->ships as $ship){		
		/*account for possible reactor overload!*/
		$reactorList = $ship->getSystemsByName('Reactor');
		foreach($reactorList as $reactorCurr){
			//is it overloading?...
			if( $reactorCurr->isOverloading($gamedata->turn) ){ //primed for self destruct!
				$remaining =  $reactorCurr->getRemainingHealth();
				$armour =  $reactorCurr->armour;
				$toDo = $remaining + $armour;
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $reactorCurr->id, $toDo, $armour, 0, -1, true, "", "plasma");
				$damageEntry->updated = true;
				$reactorCurr->damage[] = $damageEntry;
			}
		}
            if ($ship instanceof FighterFlight){
                continue;
            }
            foreach($ship->getAllFireOrders() as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"){
                    continue;
                }
                $weapon = $ship->getSystemById($fire->weaponid);
                if ($weapon instanceof Thruster || $weapon instanceof Structure){
                    continue;
                }
                $fire->priority = $weapon->priority;
                $fireOrders[] = $fire;
            }
        }
        usort($fireOrders, 
            function($a, $b) use ($gamedata){
		if ($a->targetid !== $b->targetid){
                    return $a->targetid - $b->targetid;
                }else if($a->calledid!==$b->calledid){ //called shots first!
                    return $a->targetid - $b->targetid;
                }else if ($a->priority !== $b->priority){
                    return $a->priority - $b->priority;
                }
                else {
                    return $a->shooterid - $b->shooterid;
                }
            }
        );
	    
        foreach ($fireOrders as $fire){
                $ship = $gamedata->getShipById($fire->shooterid);
                $wpn = $ship->getSystemById($fire->weaponid);
                $p = $wpn->priority;
                // debug::log("resolve --- Ship: ".$ship->shipClass.", id: ".$fire->shooterid." wpn: ".$wpn->displayName.", priority: ".$p." versus: ".$fire->targetid);
                self::fire($ship, $fire, $gamedata);
        }
        // From here on, only fighter units are left.
        $chosenfires = array();
        foreach($gamedata->ships as $ship){
            // Remember: ballistics that have been fired must still be
            // resolved! So don't continue on destroyed units/fighters.
            if (!($ship instanceof FighterFlight)){
                continue;
            }
            
            foreach($ship->getAllFireOrders() as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }
                
                $weapon = $ship->getSystemById($fire->weaponid);
                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed() )
                        && !$weapon->ballistic){
                    continue;
                }
                
                $chosenfires[] = $fire;
            }
        }
		
	//FIRE fighters at other fighters
	foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            
            if ($target == null || ($target instanceof FighterFlight)){
                self::fire($shooter, $fire, $gamedata);
            }
	}
		
	$chosenfires = array();
        foreach($gamedata->ships as $ship){
            // Remember: ballistics that have been fired must still be
            // resolved! So don't continue on destroyed units/fighters.
            if (!($ship instanceof FighterFlight)){
                continue;
            }
            
            foreach($ship->getAllFireOrders() as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }
		
                $weapon = $ship->getSystemById($fire->weaponid);
                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed() )
                        && !$weapon->ballistic){
                    continue;
                }
		
                $chosenfires[] = $fire;
            }
	}
		
	//FIRE rest of fighters
	foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            self::fire($shooter, $fire, $gamedata);
        }
    } //endof method fireWeapons
	
	
	

    
    private static function fire($ship, $fire, $gamedata){
        	if ($fire->turn != $gamedata->turn)
			return;
			
		if ($fire->type == "intercept" || $fire->type == "selfIntercept")
			return;
			
		if ($fire->rolled > 0)
			return;
		
		$weapon = $ship->getSystemById($fire->weaponid);
		
		$weapon->fire($gamedata, $fire);
		
	} //endof method fire	
	
	
} //endof class Firing

?>
