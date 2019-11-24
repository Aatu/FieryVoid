<?php
/*old version of this file moved to .old file*/

class Firing
{
    public $gamedata;

    public static function validateFireOrders($fireOrders, $gamedata)
    {
        return true;
    }


    //compares weapons' capability as interceptor
    //if intercept rating is the same, faster-firing weapon would go first
    public static function compareInterceptAbility($weaponA, $weaponB)
    {
        if ($weaponA->intercept > $weaponB->intercept) {
            return -1;
        } else if ($weaponA->intercept < $weaponB->intercept) {
            return 1;
        } else if ($weaponA->loadingtime < $weaponB->loadingtime) {
            return -1;
        } else if ($weaponA->loadingtime > $weaponB->loadingtime) {
            return 1;
        } else {
            return 0;
        }
    } //endof function compareInterceptAbility


    /*gets all ready intercept-capable weapons that aren't otherwise assigned*/
    public static function getUnassignedInterceptors($gamedata, $ship)
    {
        $currTurn = $gamedata->turn;
        $toReturn = array();
        if ($ship instanceof FighterFlight) { //separate procedure for fighters
            $exclusiveWasFired = false;
            foreach ($ship->systems as $fighter) {
                if ($fighter->isDestroyed()) continue;
                foreach ($fighter->systems as $weapon) {
                    if (($weapon instanceof Weapon) && ($weapon->ballistic != true)) {
                        if (($weapon->exclusive) && $weapon->firedOnTurn($currTurn)) {
                            $exclusiveWasFired = true;
                            continue;
                        } else if ((!$weapon->firedOnTurn($currTurn)) && ($weapon->intercept > 0) && (self::isValidInterceptor($gamedata, $weapon))) {//not fired this turn, intercept-capable, and valid interceptor
                            if ((!isset($weapon->ammunition)) || ($weapon->ammunition > 0)) {//unlimited ammo or still has ammo available
                                $toReturn[] = $weapon;
                            }
                        }
                    }
                }
            }
            if ($exclusiveWasFired) $toReturn = array(); //exclusive weapon was fired, nothing can intercept!
        } else { //proper ship
            if (!(($ship->unavailable === true) || $ship->isDisabled())) { //ship itself can fight this turn
                foreach ($ship->systems as $weapon) {
                    if ((!($weapon instanceof Weapon)) || ($weapon->ballistic)) continue; //not a weapon, or a ballistic weapon
                    if ((!$weapon->firedOnTurn($currTurn)) && ($weapon->intercept > 0)) {
                        if (self::isValidInterceptor($gamedata, $weapon)) {//not fired this turn, intercept-capable, and valid interceptor
                            $toReturn[] = $weapon;
                        }
                    }
                }
            }
        }

        return $toReturn;
    } //endof getUnassignedInterceptors


    /* returns best possible shot to intercept (or null if none is available)
    */
    public static function getBestInterception($gamedata, $currInterceptor, $incomingShots)
    {
        $bestInterception = null;
        $bestInterceptionVal = 0;
        foreach ($incomingShots as $firingOrder) {
            $isLegal = self::isLegalIntercept($gamedata, $currInterceptor, $firingOrder);
            if (!$isLegal) continue; //not a legal interception at all for this weapon
            $currInterceptionMod = $currInterceptor->getInterceptionMod($gamedata, $firingOrder);
            if ($currInterceptionMod <= 0) continue; //can't effectively intercept

            $shooter = $gamedata->getShipById($firingOrder->shooterid);
            $target = $gamedata->getShipById($firingOrder->targetid);
            $firingWeapon = $shooter->getSystemById($firingOrder->weaponid);

            $chosenLoc = $firingOrder->chosenLocation;
            if (!($chosenLoc > 0)) $chosenLoc = 0; //just in case it's not set/not a number!
            if ($target instanceof FighterFlight) {
                $exampleFighter = $target->getSampleFighter();
                $armour = $exampleFighter->getArmour($target, $shooter, $firingWeapon->damageType);
                //$armour = 0; //let's simplify here...
            } else {
                $structureSystem = $target->getStructureSystem($chosenLoc);
                $armour = $structureSystem->getArmour($target, $shooter, $firingWeapon->damageType); //shooter relevant only for fighters - and they don't care about calculating ambiguous damage!
            }
            $expectedDamageMax = $firingWeapon->maxDamage;
            $expectedDamageMin = $firingWeapon->minDamage;
            $expectedDamage = (($expectedDamageMin + $expectedDamageMax) / 2) - $armour;
            $expectedDamage = max(0.5, $expectedDamage); //assume some damage is always possible!
            //reduce damage for non-Standard modes...
            switch ($firingWeapon->damageType) {
                case 'Raking': //Raking damage gets reduced multiple times, account for that a bit! - another armour down!
                    if ($expectedDamage > 10) { ///simplified, assuming Raking will be in 10-strong rakes
                        $expectedDamage = $expectedDamage - $armour; //from second rake - let's simplify that two full weights of armor will be deduced from damage
                        $expectedDamage = max(10, $expectedDamage);
                    }
                    break;
                case 'Piercing': //Piercing does little damage to actual outer section... but it does PRIMARY damage! very dangerous!
                    $expectedDamage = $expectedDamage * 1.1;
                    break;
                case 'Pulse': //multiple hits - assume half of max pulses hit!
                    $expectedDamage = 0.5 * $expectedDamage * max(2, $firingWeapon->maxpulses);
                    break;
                default: //something else: can't be as good as Standard!
                    $expectedDamage = $expectedDamage * 0.9;
                    break;
            }
            //if weapon does no damage by itself, assume it has other, very relvant effect - comparable to 10 damage!
            if ($firingWeapon->maxDamage == 0) $expectedDamage = 10;
            $expectedDamage = max(0.1, $expectedDamage);//estimate _some_ damage always...
            //multiply by Shots...
            $expectedDamage = $expectedDamage * max(1, $firingWeapon->shots);

            //called shots are more important...
            if ($firingOrder->calledid != -1) {
                $expectedDamage = $expectedDamage * 1.1;
            }

            //how much is actually reduced?
            $hitChanceBefore = $firingOrder->needed - $firingOrder->totalIntercept;
            $hitChanceAfter = $hitChanceBefore - $currInterceptionMod;
            $hitChanceAfter = max(0, $hitChanceAfter);//negative numbers are irrelevant, effectively You can interept to 0
            $modifier = min(100, $hitChanceBefore) - $hitChanceAfter;
            if ($modifier <= 0) { //after interception hit chance is still over 100%... let's count as something, but much less - say, multiply by 0.1!
                $modifier = 0.1 * ($hitChanceBefore - $hitChanceAfter);
            }

            //...how much damage is actually stopped?
            $stoppedDamage = $modifier * $expectedDamage;//to get actual damage statistically stopped, You need to multiply this by 0.01 - but it's completely irrelevant for higher/lower comparision

            if ($stoppedDamage > $bestInterceptionVal) { //this is best interception candidate found so far!
                $bestInterception = $firingOrder;
                $bestInterceptionVal = $stoppedDamage;
            }
        }
        return $bestInterception;
    }//endof getBestInterception


    /*adds indicated weapon's capabilities to total interception variables
    	may create intercept order itself if needed
    */
    public static function addToInterceptionTotal($gamedata, $intercepted, $interceptor, $prepareOrder = false)
    {
        //update numbers appropriately
        $intercepted->totalIntercept += $interceptor->getInterceptionMod($gamedata, $intercepted);
        $intercepted->numInterceptors++;

        if ($prepareOrder) { //new firing order (intercept) should be prepared?
            $interceptFire = new FireOrder(-1, "intercept", $interceptor->getUnit()->id, $intercepted->id, $interceptor->id, -1,
                $gamedata->turn, $interceptor->firingMode, 0, 0, $interceptor->defaultShots, 0, 0, null, null
            );
            $interceptFire->addToDB = true;
            $interceptor->fireOrders[] = $interceptFire;
        }
	    
	//fireDefensivaly call is needed for weapons that suffer some side effect when firing defensively
	$interceptor->fireDefensively($gamedata, $intercepted);
    } //endof function addToInterceptionTotal


    /*Marcin Sawicki, October 2017: change approach: allocate interception fire before ANY fire is actually resolved!
        this allows for auto-intercepting ballistics, too.
    */
    public static function automateIntercept($gamedata)
    { //automate allocation of intercept weapons
        //prepare list of all potential intercepts and all incoming fire
        $allInterceptWeapons = array();
        $allIncomingShots = array();
        foreach ($gamedata->ships as $ship) {
            $interceptWeapons = self::getUnassignedInterceptors($gamedata, $ship);
            $allInterceptWeapons = array_merge($allInterceptWeapons, $interceptWeapons);
            $incomingShots = $ship->getAllFireOrders($gamedata->turn);
            $allIncomingShots = array_merge($allIncomingShots, $incomingShots);
        }

        //update intercepion totals!
        $shotsStillComing = $allIncomingShots;
        foreach ($allIncomingShots as $fireOrder) {
            if (($fireOrder->type != "selfIntercept") && ($fireOrder->type != "intercept")) continue; //manually assigned interception - no others exist at this point
            //let's find WHAT is being intercepted and update interception totals!
            foreach ($shotsStillComing as $intercepted) {
                if ($fireOrder->targetid == $intercepted->id) {
                    $shooter = $gamedata->getShipById($fireOrder->shooterid);
                    $firingWeapon = $shooter->getSystemById($fireOrder->weaponid);
                    self::addToInterceptionTotal($gamedata, $intercepted, $firingWeapon);
                    break; //loop
                }
            }
        }


        //delete fire orders that are intercept orders or are hex-targeted or have no chance of hitting
        $shotsStillComing = array();
        foreach ($allIncomingShots as $fireOrder) {
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

        //assign interception
        while ((count($allInterceptWeapons) > 0)) {//weapons can still intercept!
            $currInterceptor = array_shift($allInterceptWeapons); //most capable interceptor available
            for ($i = 0; $i < $currInterceptor->guns; $i++) { //a single weapon can intercept multiple times...
                //find shot it would be most profitable to intercept with this weapon, and intercept it!
                $shotToIntercept = self::getBestInterception($gamedata, $currInterceptor, $allIncomingShots);
                if ($shotToIntercept != null) {
                    self::addToInterceptionTotal($gamedata, $shotToIntercept, $currInterceptor, true); //add numbers AND create order
                }
            }
        }

        //all possible interceptions have been made!
    } //endof function automateIntercept


    private static function isValidInterceptor($gd, $weapon)
    {
        if (!($weapon instanceof Weapon)) return false;
        $weapon = $weapon->getWeaponForIntercept();

        if (!$weapon) {
            return false;
        }

        if ($weapon->intercept == 0) {
            return false;
        }
        if ($weapon->isDestroyed()) {
            //print($weapon->displayName . " is destroyed and cannot intercept " . $weapon->id);
            return false;
        }
        if ($weapon->isOfflineOnTurn($gd->turn)) {
            return false;
        }

        // not loaded yet
        if ($weapon->getTurnsloaded() < $weapon->getLoadingTime()) {
            return false;
        }

        if ($weapon->getLoadingTime() > 1) {
            if (isset($weapon->fireOrders[0])) {
                if ($weapon->fireOrders[0]->type != "selfIntercept") {
                    return false;
                }
            } else {
                return false;
            }
        }

        if ($weapon->getLoadingTime() == 1 && $weapon->firedOnTurn($gd->turn)) {
            return false;
        }

        return true;
    } //endof function isValidInterceptor


    public static function doIntercept($gd, $ship, $intercepts)
    {
        //returns all valid interceptors as $intercepts
        if (sizeof($intercepts) == 0) {
            //    debug::log($ship->phpclass." has nothing to intercept.");
            return;
        };
        usort($intercepts, "self::compareIntercepts");
        foreach ($intercepts as $intercept) {
            $intercept->chooseTarget($gd);
        }
    }


    public static function compareIntercepts($a, $b)
    {
        if (sizeof($a->intercepts) > sizeof($b->intercepts)) {
            return -1;
        } else if (sizeof($b->intercepts) > sizeof($a->intercepts)) {
            return 1;
        } else {
            return 0;
        }
    }


    /*would this be a legal interception?...*/
    public static function isLegalIntercept($gd, $weapon, $fire)
    {
        if ($fire->type == "intercept") {
            //Debug::log("Fire is intercept\n");
            return false;
        }
        if ($fire->type == "selfIntercept") {
            //Debug::log("Fire is intercept\n");
            return false;
        }
        if ($weapon instanceof DualWeapon) $weapon->getFiringWeapon($fire);

        if ($weapon->intercept == 0) {
            //Debug::log("Weapon has intercept of zero\n");
            return false;
        }


        $shooter = $gd->getShipById($fire->shooterid);
        $target = $gd->getShipById($fire->targetid);
        $interceptingShip = $weapon->getUnit();
        $firingweapon = $shooter->getSystemById($fire->weaponid);

        if ($firingweapon->doNotIntercept){ //some attacks simply aren't subject to interception - like being in a field, or ramming attacks
            //Debug::log("Target weapon cannot be intercepted\n");
            return false;
        }

        if (($firingweapon->uninterceptable) && (!($weapon->canInterceptUninterceptable))) { //some weapons can intercept normally unintereptable shots
            //Debug::log("Target weapon is uninterceptable\n");
            return false;
        }

        if ($shooter->team == $interceptingShip->team) {
            //Debug::log("Fire is friendly\n");
            return false;
        }


        if ((!($firingweapon->ballistic)) && $weapon->ballisticIntercept) {
            //Debug::log("Can only intercept ballistics, and this is not ballistic\n");
            return false;
        }

        if ($firingweapon->ballistic) {
            $movement = $shooter->getLastTurnMovement($fire->turn);
            $pos = mathlib::hexCoToPixel($movement->position); //launch hex
            $relativeBearing = $interceptingShip->getBearingOnPos($pos);
        } else {
            $pos = $shooter->getCoPos(); //current hex of firing unit
            $relativeBearing = $interceptingShip->getBearingOnUnit($shooter);
        }

        if (!mathlib::isInArc($relativeBearing, $weapon->startArc, $weapon->endArc)) {
            //Debug::log("Fire is not on weapon arc\n");
            return false;
        }


        if ($interceptingShip->id == $target->id) { //ship intercepting fire directed at it - usual case
            return true;
        } else { //fire directed at third party - only particular weapons are able to do so
            //Debug::log("Target is this another ship\n");
            if ($interceptingShip instanceof FighterFlight) { //can intercept ballistics IF together with target ship form start of turn
                if ($firingweapon->ballistic) { //only ballistic weapons can be intercepted this way
                    if ($target instanceof FighterFlight) {
                        return false; //cannot intercept fire at other fighters
                    } else {//target is ship
                        $selfPosNow = $interceptingShip->getCoPos();
                        $targetPosNow = $target->getCoPos();
                        if ($fire->turn == 1) { //first turn - assume starting positions did match (technical reasons - units cannot start on same hex!)
                            $selfPosPrevious = $selfPosNow;
                            $targetPosPrevious = $targetPosNow;
                        } else {//standard - check actual position at the end of previous turn
                            $movement = $interceptingShip->getLastTurnMovement($fire->turn);
                            $selfPosPrevious = mathlib::hexCoToPixel($movement->position); //at start of turn
                            $movement = $target->getLastTurnMovement($fire->turn);
                            $targetPosPrevious = mathlib::hexCoToPixel($movement->position); //at start of turn
                        }

                        if (($selfPosNow == $targetPosNow) && ($selfPosPrevious == $targetPosPrevious)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else { //incoming weapon is not ballistic
                    return false;
                }
            } else { //ship
                if (!$weapon->freeintercept) {
                    //Debug::log("Target is another ship, and this weapon is not freeintercept \n");
                    return false;
                }

                //new approach: bearing to target is opposite to bearing shooter, +/- 60 degrees
                //$oppositeBearing = mathlib::addToDirection($relativeBearing,180);//bearing exactly opposite to incoming shot
                $oppositeBearingFrom = mathlib::addToDirection($relativeBearing, 120);//bearing exactly opposite to incoming shot, minus 60 degrees
                $oppositeBearingTo = mathlib::addToDirection($oppositeBearingFrom, 120);//bearing exactly opposite to incoming shot, plus 60 degrees
                $targetBearing = $interceptingShip->getBearingOnUnit($target);
                if (mathlib::isInArc($targetBearing, $oppositeBearingFrom, $oppositeBearingTo)) {
                    //Debug::log("VALID INTERCEPT\n");
                    return true;
                }
            }
        }

        //Debug::log("INVALID INTERCEPT\n"); //should not rech here!
        return false;
    } //endof function isLegalIntercept


    /*Marcin Sawicki: count hit chances for starting fire phase fire*/
    public static function prepareFiring($gamedata){
	//additional call for weapons needing extra preparation
        foreach ($gamedata->ships as $ship){
            foreach ($ship->systems as $system){
                $system->beforeFiringOrderResolution($gamedata);
            }
        }

        $ambiguousFireOrders  = array();
        foreach ($gamedata->ships as $ship){
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"){
                    continue;
                }
                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)){ //this isn't a weapon after all...
                    continue;
                }		
		    
                $fire->priority = $weapon->priority;
				//take different AF priority into account!
				if($fire->targetid !== -1){ //actually directed at an unit!
					$target = $gamedata->getShipById($fire->targetid); 
					if ($target instanceof FighterFlight){
						$fire->priority = $weapon->priorityAF;
					}
				}	
				
                if($weapon->isTargetAmbiguous($gamedata, $fire)){
                    $ambiguousFireOrders[] = $fire;
                }else{
                    $weapon->calculateHitBase($gamedata, $fire);
                }
            }
        }

        //calculate hit chances for ambiguous firing!
        foreach($ambiguousFireOrders as $fireOrder){
            $ship = $gamedata->getShipById($fireOrder->shooterid);
            $weapon = $ship->getSystemById($fireOrder->weaponid);
            $weapon->calculateHitBase($gamedata, $fireOrder);
        }

    }//endof function prepareFiring	
	
	

    /* sorts firing orders*/
    public static function compareFiringOrders($a, $b){
		/*
        if ($a->targetid !== $b->targetid){
            return $a->targetid - $b->targetid;
        }else if($a->calledid!==$b->calledid){ //called shots first!
            return $a->calledid - $b->calledid;
        }else if ($a->priority !== $b->priority){
                return $a->priority - $b->priority;
        }
		*/
		//let's sort by calledid then priority first, display may be by  target but not actual firing!
		if($a->calledid!==$b->calledid){ //called shots first!
            return $a->calledid - $b->calledid;
        }else if ($a->priority !== $b->priority){
            return $a->priority - $b->priority;
        }else if ($a->targetid !== $b->targetid){
            return $a->targetid - $b->targetid;
        }
        else {
            $val = $a->shooterid - $b->shooterid; //by shooter
            if ($val == 0) $val = $a->id - $b->id; //let's use database ID as final sorting element!
            return $val;
        }
    } //endof function compareFiringOrders



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
	    /* fighter attacks are taken into account here, but only ramming attacks!
            if ($ship instanceof FighterFlight){
                continue;
            }*/
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
				/*
                if ($weapon instanceof Thruster || $weapon instanceof Structure) {
                    continue;
                }*/
				if (!($weapon instanceof Weapon)){//...just in case...
					continue;
				};
				

				//fighter attack: only if ramming
				if ($ship instanceof FighterFlight){
					if (!$weapon->isRammingAttack) continue;
				}

                //$fire->priority = $weapon->priority; //fire order priority already set, and may differ from basic weapon priority!
                $fireOrders[] = $fire;
            }
        }
        usort($fireOrders, "self::compareFiringOrders");
	    
        foreach ($fireOrders as $fire){
                $ship = $gamedata->getShipById($fire->shooterid);
                //$wpn = $ship->getSystemById($fire->weaponid);
                //$p = $wpn->priority;
                // debug::log("resolve --- Ship: ".$ship->shipClass.", id: ".$fire->shooterid." wpn: ".$wpn->displayName.", priority: ".$p." versus: ".$fire->targetid);
                self::fire($ship, $fire, $gamedata);
        }

        // From here on, only fighter units are left.
        $chosenfires = array();
        foreach ($gamedata->ships as $ship) {
            // Remember: ballistics that have been fired must still be
            // resolved! So don't continue on destroyed units/fighters.
            if (!($ship instanceof FighterFlight)) {
                continue;
            }

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

				//ramming attacks are already allocated!
				if ($weapon->isRammingAttack) continue;

                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed() )
                        && !$weapon->ballistic){
                    continue;
                }
                //$fire->priority = $weapon->priority; priority already set!
                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, "self::compareFiringOrders");

		//FIRE fighters at other fighters
		foreach ($chosenfires as $fire){
				$shooter = $gamedata->getShipById($fire->shooterid);
				$target = $gamedata->getShipById($fire->targetid);

				if ($target == null || ($target instanceof FighterFlight)) {
					self::fire($shooter, $fire, $gamedata);
				}
			}

			$chosenfires = array();
			foreach ($gamedata->ships as $ship) {
				// Remember: ballistics that have been fired must still be
				// resolved! So don't continue on destroyed units/fighters.
				if (!($ship instanceof FighterFlight)) {
					continue;
				}
				
				foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
					if ($fire->turn != $gamedata->turn){
						continue;
					}

				//ramming attacks are already allocated!
				if ($weapon->isRammingAttack) continue;
				
				$weapon = $ship->getSystemById($fire->weaponid);
				if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed())
					&& !$weapon->ballistic) {
					continue;
				}

				//$fire->priority = $weapon->priority; //fire order priority already declared!
				$chosenfires[] = $fire;
			}
		}

        usort($chosenfires, "self::compareFiringOrders");

		//FIRE rest of fighters
		foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            self::fire($shooter, $fire, $gamedata);
        }
    } //endof method fireWeapons


    private static function fire($ship, $fire, $gamedata)
    {
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
