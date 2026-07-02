<?php
/*old version of this file moved to .old file*/

class Firing
{
    public $gamedata;

    public static function validateFireOrders($fireOrders, $gamedata)
    {
        //Submit-time corruption guard. A stale client blueprint (out-of-date
        //staticShips for a phpclass) posts fire orders whose weaponid belongs to
        //an older system layout — system ids are positional (BaseShip::addSystem
        //assigns id = current system count), so any change to a ship's system
        //list shifts every later id and desynchronises older clients. Such orders
        //either reference a weaponid that no longer exists or one that now maps to
        //a NON-weapon system (e.g. a Thruster). If written to tac_fireorder they
        //crash the next game load in getFireOrdersForShips / TacGamedata (a
        //Thruster has no ->ballistic). DBManager::getFireOrdersForShips guards the
        //load path defensively; this is the DURABLE fix — reject the bad order
        //before it ever reaches the DB.
        //
        //An invalid order is detached from its owning system in place so the very
        //next $ship->getAllFireOrders() (used for the actual submit) no longer
        //includes it. We still return true: a corrupt order is dropped silently
        //rather than failing the whole ship's submission (some callers throw on
        //false, which would abort turn processing over one bad shot).
        foreach ($fireOrders as $fire) {
            //Intercept/self-intercept orders legitimately reference another fire
            //order id (or carry no weapon), so don't weapon-validate them here.
            if ($fire->type === 'intercept' || $fire->type === 'selfIntercept')
                continue;

            $shooter = $gamedata->getShipById($fire->shooterid);

            //Uncontrolled-flight guard. A remote-controlled Hunter-Killer flight whose
            //command link is severed (node shortfall or ELINT jamming) is driven entirely
            //by the server: AutomatedMovement moves it (drift/seek) and, if it ends co-located
            //with an enemy, createAutomatedRamOrders generates its ram. The player has no
            //control, so any fire order they managed to submit for it is spurious and would
            //DOUBLE the attack (player order + automated order) - exactly the 12-fire-order /
            //broken-return-damage bug on uncontrolled HK rams. Reject it before it reaches the
            //DB. Same detach + ->rejected convention as the corrupt-order path below.
            if ($shooter && self::isShooterUncontrolled($shooter, $gamedata)) {
                $fire->rejected = true;
                Debug::log("validateFireOrders: rejecting player fire order for UNCONTROLLED flight "
                    . "(game {$gamedata->id}, shooter {$fire->shooterid}, weaponid {$fire->weaponid}, "
                    . "type {$fire->type}) - server drives uncontrolled flights.");
                self::detachFireOrder($shooter, $fire);
                continue;
            }

            $weapon = $shooter ? $shooter->getSystemById($fire->weaponid) : null;

            if ($weapon instanceof Weapon)
                continue; //valid — leave it attached

            //Invalid: shooter gone, weaponid missing, or it resolved to a
            //non-weapon system. Mark the order rejected AND detach it from its
            //owning system. The flag covers callers that submit the same array
            //they validated (e.g. the mine-ballistic path passes $newFireOrders to
            //both validate and submit); the detach covers callers that re-fetch
            //$ship->getAllFireOrders() for the submit. submitFireorders skips any
            //order with ->rejected set, so neither pattern can persist it.
            $fire->rejected = true;

            $sysType = $weapon ? get_class($weapon) : ($shooter ? 'missing weaponid' : 'missing shooter');
            Debug::log("validateFireOrders: rejecting corrupt fire order "
                . "(game {$gamedata->id}, shooter {$fire->shooterid}, weaponid {$fire->weaponid}, "
                . "type {$fire->type}) — resolved to: $sysType. Likely stale client blueprint.");

            if ($shooter)
                self::detachFireOrder($shooter, $fire);
        }

        return true;
    }

    /* True when $shooter is a remote-controlled flight that is Uncontrolled THIS turn
     * (command link severed - node shortfall or ELINT jamming), so the server drives it
     * and any player fire order for it is spurious. Mirrors AutomatedMovement::isUncontrolled:
     * Uncontrolled is a oneturn crit on the sample fighter placed on turn T (effect T+1),
     * and hasCritical's oneturn handling matches it on the effect turn. Cheap-guarded on
     * remoteControl so ordinary ships short-circuit immediately. */
    private static function isShooterUncontrolled($shooter, $gamedata)
    {
        if (empty($shooter->remoteControl)) return false;
        if (!($shooter instanceof FighterFlight)) return false;
        $sample = $shooter->getSampleFighter();
        if (!$sample) return false;
        return $sample->hasCritical("Uncontrolled", $gamedata->turn) > 0;
    }

    /* Remove a specific FireOrder object from any system (or fighter subsystem)
     * on $ship that currently holds it. Used by validateFireOrders to drop a
     * corrupt order before submit. Matches by object identity, not weaponid,
     * because the whole point is that the order's weaponid is untrustworthy. */
    private static function detachFireOrder($ship, $badFire)
    {
        foreach ($ship->systems as $system) {
            self::spliceFireOrder($system, $badFire);
            if (!empty($system->systems) && is_array($system->systems)) {
                foreach ($system->systems as $sub) {
                    self::spliceFireOrder($sub, $badFire);
                }
            }
        }
    }

    private static function spliceFireOrder($system, $badFire)
    {
        if (empty($system->fireOrders) || !is_array($system->fireOrders))
            return;
        foreach ($system->fireOrders as $i => $existing) {
            if ($existing === $badFire) {
                unset($system->fireOrders[$i]);
            }
        }
        //Reindex so downstream count()/[0] access stays well-behaved.
        $system->fireOrders = array_values($system->fireOrders);
    }


    //compares weapons' capability as interceptor
    //if intercept rating is the same, faster-firing weapon would go first
    public static function compareInterceptAbility($weaponA, $weaponB)
    {
        if ($weaponA->intercept > $weaponB->intercept) {
            return -1;
        } else if ($weaponA->intercept < $weaponB->intercept) {
            return 1;
        } else if ( max($weaponA->loadingtime, $weaponA->normalload) < max($weaponB->loadingtime, $weaponB->normalload) ) {
            return -1;
        } else if ( max($weaponA->loadingtime, $weaponA->normalload) > max($weaponB->loadingtime, $weaponB->normalload) ) {
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
//                    if (($weapon instanceof Weapon) && ($weapon->ballistic != true)) {
                    if (($weapon instanceof Weapon)) {  //Changed line to allow ballistics to intercept  //GTS 07 Aug 2022
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
                    if ((!($weapon instanceof Weapon))) continue; //not a weapon, or a ballistic weapon         
					if ($weapon->canModesIntercept && (!($weapon->firedOnTurn($currTurn)))) $weapon->switchModeForIntercept(); //To check for intercept values in non-default modes if weapon has appropriate marker and hasn't fired e.g. Intercept Missile etc                    
                    
                    /* //Old method before the additiona of split shot weapons, keep for now
                    if ((!$weapon->firedOnTurn($currTurn) || $weapon->canSplitShots) && ($weapon->intercept > 0)) {
                        if (self::isValidInterceptor($gamedata, $weapon)) {//not fired this turn, intercept-capable, and valid interceptor                 	
                            $toReturn[] = $weapon;
                        }
                    }
                    */

                        if (
                            (!$weapon->firedOnTurn($currTurn) || $weapon->canSplitShots)
                            && ($weapon->intercept > 0)
                        ) {
                            if (self::isValidInterceptor($gamedata, $weapon)) {
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
                $exampleFighter = $target->getSampleFighter(); //not necessarily correct for adaptive armor, but have to base on something...
                $armour = $exampleFighter->getArmourComplete($target, $shooter, $firingWeapon->weaponClass);
                //$armour = 0; //let's simplify here...
            } else {
                $structureSystem = $target->getStructureSystem($chosenLoc);
                $armour = $structureSystem->getArmourComplete($target, $shooter, $firingWeapon->weaponClass); //shooter relevant only for fighters - and they don't care about calculating ambiguous damage!
            }
            $expectedDamageMax = $firingWeapon->maxDamage;
            $expectedDamageMin = $firingWeapon->minDamage;
            $expectedDamage = (($expectedDamageMin + $expectedDamageMax) / 2) - $armour;
            $expectedDamage = max(0.5, $expectedDamage); //assume some damage is always possible!
            //reduce damage for non-Standard modes...
            switch ($firingWeapon->damageType) {
                case 'Flash': //increase expected damage on account of collateral! 
                    $expectedDamage = $expectedDamage * 1.25;
                    break;
                case 'Raking': //Raking damage gets reduced multiple times, account for that a bit! - another armour down!
                    if ($expectedDamage > 10) { ///simplified, assuming Raking will be in 10-strong rakes
                        $expectedDamage = $expectedDamage - $armour; //from second rake - let's simplify that two full weights of armor will be deduced from damage
                        $expectedDamage = min(10, $expectedDamage);
                    }
                    break;
                case 'Piercing': //Piercing does little damage to actual outer section... but it does PRIMARY damage! very dangerous!
                    $expectedDamage = $expectedDamage * 1.1;
                    break;
                case 'Pulse': //multiple hits - assume half of max pulses hit !
                    $expectedDamage = 0.5 * $expectedDamage * max(2, $firingWeapon->maxpulses);
                    break;
                case 'Standard': //default damage!
                    $expectedDamage = $expectedDamage ;
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
			checkForSelfInterceptFire::setFired($interceptor->getUnit()->id, $interceptor->id, $gamedata->turn);
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
            if($ship->getTurnDeployed($gamedata) > $gamedata->turn)	continue; //Ship not deployed yet. Remove to avoid problems.            
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
            $firingWeapon->notActuallyHexTargeted($fireOrder);//Some weapons start hex targeted, but become normal e.g. BM Launcher - 4.3.24 DK
            if ($firingWeapon->hextarget) continue;//hex-targeted
            $shotsStillComing[] = $fireOrder;
        }
        $allIncomingShots = $shotsStillComing;
        $shotsStillComing = null; //just free memory

        //sort list of all potential intercepts - most effective first
        usort($allInterceptWeapons, [self::class, 'compareInterceptAbility']);

        //assign interception
        while ((count($allInterceptWeapons) > 0)) {//weapons can still intercept!
            $currInterceptor = array_shift($allInterceptWeapons); //most capable interceptor available

            //A split shot weapon may have fired some shots offensively, deduct these from intercept guns.                
            if ($currInterceptor->canSplitShots) {
                $currGuns = $currInterceptor->guns - count($currInterceptor->fireOrders); //Normally we just deduct fireOrders from total guns.
                foreach($currInterceptor->fireOrders as $fired){ //However, accelerator weapons like Discharge Gun have to be manually set, and this fireOrder shouldn't count against total.
                    if($fired->type == "selfIntercept") $currGuns++; //So if selfIntercept, add shot back on.
                }    
                $currInterceptor->guns = $currGuns; //Deduct fireOrders from intercept.                          
            }

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
	
	$loadingTimeActual = max($weapon->getLoadingTime(),$weapon->normalload);//Accelerator (or multi-mode) weapons may have loading time of 1, yet reach full potential only after longer charging 
        /*  //Old method  
	    if ($loadingTimeActual > 1) { 
            if (isset($weapon->fireOrders[0])) {
                if ($weapon->fireOrders[0]->type != "selfIntercept") {
                    return false;
                }
            } else {
                return false;
            }
        }                     
        */   
        //New Method taking itno account Split Shot weapons.
        if ($loadingTimeActual > 1) {
                $hasSelfIntercept = false;
        
                // If the weapon can split shots, check all fireOrders
                if ($weapon->canSplitShots) {
                    foreach ($weapon->fireOrders as $order) {
                        if ($order->type == "selfIntercept") {
                            $hasSelfIntercept = true;
                            break;
                        }
                    }
                } else {
                    // Fallback for non-splitting weapons: check only the first
                    if (isset($weapon->fireOrders[0]) && $weapon->fireOrders[0]->type == "selfIntercept") {
                        $hasSelfIntercept = true;
                    }
                }
        
                if (!$hasSelfIntercept) {
                    return false;
                }
        }

        //Added new checks for split shots weapons so they don't get ruled out at this moment.   
        if ($loadingTimeActual == 1 
            && $weapon->firedOnTurn($gd->turn) 
            && !$weapon->canSplitShots) { //Retain normal check for weapon that can't split, but leave room for split shot exceptions.
            return false;
        }

        //Now check if split shot weapons have any spare guns to intercept with.
        if ($weapon->canSplitShots) { //Weapon that might have fired some shots, but still have some remaining to intercept with.
            $count = count($weapon->fireOrders); //How many fireOrders were made?
            foreach ($weapon->fireOrders as $order1) {
                if ($order1->type == "selfIntercept") {
                    $count--;
                }
            }
            if($count >= $weapon->guns){ //If fireOrders have used up all shots, cannot intercept.
                return false;
            }
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
        usort($intercepts, [self::class, 'compareIntercepts']);
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

        if ($weapon->intercept == 0) {
            //Debug::log("Weapon has intercept of zero\n");
            return false;
        }

        $shooter = $gd->getShipById($fire->shooterid);
        $target = $gd->getShipById($fire->targetid);
        $interceptingShip = $weapon->getUnit();
        $firingweapon = $shooter->getSystemById($fire->weaponid);	
        
        if($interceptingShip instanceof Mine){
            if(!$interceptingShip->getCommandControl()){
                return false; //Mines generally can't intercept using their weapons, unless they have command controller upgrade.        
            }
        }

        if ($firingweapon->doNotIntercept){ //some attacks simply aren't subject to interception - like being in a field, or ramming attacks
            //Debug::log("Target weapon cannot be intercepted\n");
            return false;
        }

        if (($firingweapon->uninterceptable) && (!($weapon->canInterceptUninterceptable))) { //some weapons can intercept normally unintereptable shots
            //Debug::log("Target weapon is uninterceptable\n");
            return false;
        }
        
        /* //Removed to allow shooting at own team.
        if ($shooter->team == $interceptingShip->team) {
            //Debug::log("Fire is friendly\n");
            return false;
        }
        */

        if ((!($firingweapon->ballistic)) && $weapon->ballisticIntercept) {
            //Debug::log("Can only intercept ballistics, and this is not ballistic\n");
            return false;
        }



        $relativeBearing = $firingweapon->getIncomingBearing($interceptingShip, $fire, $gd);

        //New arc check that checks split arcs like Heavy Slicer as well = DK Dec 2025
        if (!mathlib::isInAnyArc(
            $relativeBearing,
            $weapon->startArc,
            $weapon->endArc,
            $weapon->startArcArray ?? [],
            $weapon->endArcArray ?? []
        )) {
            return false;
        }		

        /* //Old check for just main arcs
        if (!mathlib::isInArc($relativeBearing, $weapon->startArc, $weapon->endArc)) {
            //Debug::log("Fire is not on weapon arc\n");
            return false;
        }
        */
	
        if (!$firingweapon->ballistic && isset($shooter->skinDancing[$target->id]) && $shooter->skinDancing[$target->id] === true) {          
            return false; // Can't intercept for ships skindancing on you.
        }

        // Check if the Intercepting Ship is a failed Skindancer
        if (!empty($interceptingShip->skinDancing)) {
            foreach ($interceptingShip->skinDancing as $status) {
                if ($status === 'Failed') {
                    return false; // Failed skindancers cannot intercept
                }
            }
        }

		//added for Vorlon weapons, also used for Interceptor missile.
		if(!$weapon->canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)) return false; //some weapons do have exotic rules whether they can intercept at all

        if ($interceptingShip->id == $target->id) { //ship intercepting fire directed at it - usual case
            return true;
        } else { //fire directed at third party - only particular weapons are able to do so
            //Debug::log("Target is this another ship\n");
            if ($interceptingShip instanceof FighterFlight) { //can intercept ballistics IF together with target ship form start of turn
            	
				//if($weapon->freeinterceptspecial){ //weapon has own routine that handles whether it's capable of intercepting the shot
				if($weapon->freeinterceptspecial && $target->team == $interceptingShip->team && $shooter->id != $interceptingShip->id){ //Special freeintercept and target from same team.                
					return $weapon->canFreeInterceptShot($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon);					
				}            	
            	
                if ($firingweapon->ballistic) { //only ballistic weapons can be intercepted this way
                    if ($target instanceof FighterFlight) {
                        return false; //cannot intercept fire at other fighters
                    } else {//target is ship
                        $selfPosNow = $interceptingShip->getCoPos();
                        $targetPosNow = $target->getCoPos();
                        //if ($fire->turn == 1) { //first turn - assume starting positions did match (technical reasons - units cannot start on same hex!)|| Now they can - DK 27.3.26
                        //    $selfPosPrevious = $selfPosNow;
                        //    $targetPosPrevious = $targetPosNow;
                        //} else {//standard - check actual position at the end of previous turn
                            //Null-guard: getLastTurnMovement can return null when a unit
                            //has no eligible movement entry for the start-of-turn lookup
                            //(immobile terrain with only a "start" move on turn-1 firing;
                            //or any future call site where the filter excludes every entry).
                            //An immobile unit's previous-turn position equals its current
                            //position, so falling back to getCoPos() preserves the same-hex
                            //check semantics without changing established movement rules.
                            $movement = $interceptingShip->getLastTurnMovement($fire->turn);
                            $selfPosPrevious = $movement ? mathlib::hexCoToPixel($movement->position) : $selfPosNow;
                            $movement = $target->getLastTurnMovement($fire->turn);
                            $targetPosPrevious = $movement ? mathlib::hexCoToPixel($movement->position) : $targetPosNow;
                        //}

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

				//if($weapon->freeinterceptspecial){ //weapon has own routine that handles whether it's capable of intercepting the shot
				if($weapon->freeinterceptspecial && $target->team == $interceptingShip->team && $shooter->id != $interceptingShip->id){ //Special freeintercept and target from same team.
					return $weapon->canFreeInterceptShot($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon);					
				}else if ($target->team == $interceptingShip->team && $shooter->id != $interceptingShip->id){ //standard $freeintercept - must be between firing unit and target
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
        }

        //Debug::log("INVALID INTERCEPT\n"); //should not reach here!
        return false;
    } //endof function isLegalIntercept


    public static function preparePreFiring($gamedata){
	//additional call for weapons needing extra preparation
        foreach ($gamedata->ships as $ship){
            if($ship instanceof FighterFlight){
                foreach ($ship->systems as $ftr){
                    foreach ($ftr->systems as $system){                    
                        $system->beforePreFiringOrderResolution($gamedata);
                    }    
                }
            }else{
                foreach ($ship->systems as $system){
                    $system->beforePreFiringOrderResolution($gamedata);
                }
            }
        }

        $ambiguousFireOrders  = array();
        foreach ($gamedata->ships as $ship){
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                //Remove ballistic and potential intercepts from firing pool
                if ($fire->type === "intercept" || $fire->type === "selfIntercept" || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)){ //this isn't a weapon after all...
                    continue;
                }		
               
		        $weapon->changeFiringMode($fire->firingMode); //For Chaff Missile
		
		    
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

    }//endof function preparePreFiring	    


public static function firePreFiringWeapons($gamedata){	
        $rammingOrders  = array();

        //Ramming Orders first
        foreach ($gamedata->ships as $ship){		           
            
            //Now fire Ramming Orders before other weapons while we're looking through ships in this section.    
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!$weapon->isRammingAttack) continue; //Only interested in Ramming Attacks!

                $rammingOrders[] = $fire;
            }
            
        }    
        
        usort($rammingOrders, [self::class, 'compareFiringOrders']);

        foreach ($rammingOrders as $ramming){
            $ship = $gamedata->getShipById($ramming->shooterid);
            self::fire($ship, $ramming, $gamedata);
        }        
        
        $fireOrders  = array();  //Array for non-ramming ship fire.      
        //Now fire ship weapons.
        foreach ($gamedata->ships as $ship){	

            if ($ship instanceof FighterFlight) continue; //No fighter attacks handled here now that Ramming Attacks are handled above - DK 03.25      
            if($ship->isDestroyed()) continue; //Ship could be destroyed by ramming now.

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                //Remove ballisitic and potential intercepts from firing pool, normal types don#t exist yet.
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"  || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)) continue; //...just in case...               
                if($weapon->isDestroyed($gamedata->turn) && !$weapon->ballistic) continue; //now individual weapons can be destroyed by Ramming before firing, but not ballistics.                
                if ($weapon->isRammingAttack) continue; //Ramming Attacks have already been resolved.

                //$fire->priority = $weapon->priority; //fire order priority already set, and may differ from basic weapon priority!
                $fireOrders[] = $fire;
            }
            
        }
        usort($fireOrders, [self::class, 'compareFiringOrders']);

        //Now fire ship weapons.
        foreach ($fireOrders as $fire){
            $ship = $gamedata->getShipById($fire->shooterid);
            //$wpn = $ship->getSystemById($fire->weaponid);
            //$p = $wpn->priority;
            // debug::log("resolve --- Ship: ".$ship->shipClass.", id: ".$fire->shooterid." wpn: ".$wpn->displayName.", priority: ".$p." versus: ".$fire->targetid);
            self::fire($ship, $fire, $gamedata);
        }

        // From here on, only fighter units are left.

	    //FIRE fighters at fighters
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

                //Remove ballisitic and potential intercepts from firing pool
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"  || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

                //ramming attacks are already allocated!
                if ($weapon->isRammingAttack) continue;
                    
                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;

                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);

        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if ( ($target == null) || ($target instanceof FighterFlight) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

        //FIRE fighters at ships
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

                //Remove ballisitic and potential intercepts from firing pool
                if ($fire->type === "intercept" || $fire->type === "selfIntercept"  || $fire->type === "ballistic"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

                //ramming attacks are already allocated!
                if ($weapon->isRammingAttack) continue;

                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;

                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);

        //FIRE rest of fighters
        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if  ( ($target != null) && (!($target instanceof FighterFlight)) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

    } //endof method firePreFiringWeapons





    /*Marcin Sawicki: count hit chances for starting fire phase fire*/
    public static function prepareFiring($gamedata, $dbManager = null){
	//additional call for weapons needing extra preparation
        foreach ($gamedata->ships as $ship){
            foreach ($ship->systems as $system){
                $system->beforeFiringOrderResolution($gamedata);
            }
        }

        //Uncontrolled Hunter-Killers that ended movement co-located with an enemy ram it
        //(no player to submit the ram order). Done before ram orders are gathered below.
        //$dbManager is threaded through so each automated ram FireOrder is persisted
        //IMMEDIATELY (real DB id) before fireWeapons resolves it - otherwise its return-damage
        //DamageEntry captures the in-memory id (-1) and the firing fighter's destruction never
        //links to a combat-log row (the controlled/player-submitted ram already has a real id).
        AutomatedMovement::createAutomatedRamOrders($gamedata, $dbManager);

        $ambiguousFireOrders  = array();
        foreach ($gamedata->ships as $ship){
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept" || $fire->type === "prefiring"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)){ //this isn't a weapon after all...
                    continue;
                }		
               
		        $weapon->changeFiringMode($fire->firingMode); //For Chaff Missile
		
		    
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

    

	/*actual firing of weapons in normal Firing Phase
	Marcin Sawicki, October 2017: at this stage, assume all necessary calculations (hit chance, target section), and only raw rolling remains!
	*/
    public static function fireWeapons($gamedata){	
        $rammingOrders  = array();

        //Reactor explosions and Ramming Orders first    
        foreach ($gamedata->ships as $ship){		
            /*account for possible reactor overload!*/
            $reactorList = $ship->getSystemsByName('Reactor');
            foreach($reactorList as $reactorCurr){
                //is it overloading?...
                if( $reactorCurr->isOverloading($gamedata->turn) ){ //primed for self destruct!
                    $remaining =  $reactorCurr->getRemainingHealth();
                    //$armour =  $reactorCurr->armour; //just mark 0 armour
                    $toDo = $remaining;// + $armour;
                    
                    //try to make actual attack to show in log - use Ramming Attack system!				
                    $rammingSystem = $ship->getSystemByName("RammingAttack");
                    if($rammingSystem){ //actually exists! - it should on every ship!				
                        $newFireOrder = new FireOrder(
                            -1, "normal", $ship->id, $ship->id,
                            $rammingSystem->id, -1, $gamedata->turn, 1, 
                            100, 100, 1, 1, 0,
                            0,0,'SelfDestruct',10000
                        );
                        /*new FireOrder(
                            $id, $type, $shooterid, $targetid,
                            $weaponid, $calledid, $turn, $firingMode, $needed,
                            $rolled, $shots, $shotshit, $intercepted, $x, $y, $damageclass, $resolutionOrder
                        );*/
                        $newFireOrder->pubnotes = " self-destructs.";
                        $newFireOrder->addToDB = true;
                        $rammingSystem->fireOrders[] = $newFireOrder;
                    }
                    $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $reactorCurr->id, $toDo, 0, 0, -1, true, false, "", "SelfDestruct");
                    $damageEntry->updated = true;
                    if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
                            $damageEntry->shooterid = $ship->id; //additional field
                            $damageEntry->weaponid = $rammingSystem->id; //additional field
                    }
                    $reactorCurr->damage[] = $damageEntry;
                }
            }

            //Now fire Ramming Orders before other weapons while we're looking through ships in this section.    
            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->turn != $gamedata->turn){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!$weapon->isRammingAttack) continue; //Only interested in Ramming Attacks!

                $rammingOrders[] = $fire;
            }
         
        }    

        usort($rammingOrders, [self::class, 'compareFiringOrders']);

        foreach ($rammingOrders as $ramming){
            $ship = $gamedata->getShipById($ramming->shooterid);
            self::fire($ship, $ramming, $gamedata);
        }        

        $fireOrders  = array();  //Array for non-ramming ship fire.      
        //Now fire ship weapons.
        foreach ($gamedata->ships as $ship){	

            if ($ship instanceof FighterFlight) continue; //No fighter attacks handled here now that Ramming Attacks are handled above - DK 03.25      
            if($ship->isDestroyed()) continue; //Ship could be destroyed by ramming now.

            foreach($ship->getAllFireOrders($gamedata->turn) as $fire){
                if ($fire->type === "intercept" || $fire->type === "selfIntercept" || $fire->type === "prefiring"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)) continue; //...just in case...               
                if($weapon->isDestroyed($gamedata->turn) && !$weapon->ballistic) continue; //now individual weapons can be destroyed by Ramming before firing, but not ballistics.                
                if ($weapon->isRammingAttack) continue; //Ramming Attacks have already been resolved.

                //$fire->priority = $weapon->priority; //fire order priority already set, and may differ from basic weapon priority!
                $fireOrders[] = $fire;
            }
            
        }
        usort($fireOrders, [self::class, 'compareFiringOrders']);

        //Now fire ship weapons.
        foreach ($fireOrders as $fire){
            $ship = $gamedata->getShipById($fire->shooterid);
            //$wpn = $ship->getSystemById($fire->weaponid);
            //$p = $wpn->priority;
            // debug::log("resolve --- Ship: ".$ship->shipClass.", id: ".$fire->shooterid." wpn: ".$wpn->displayName.", priority: ".$p." versus: ".$fire->targetid);
            self::fire($ship, $fire, $gamedata);
        }

        // From here on, only fighter units are left.
	    //FIRE fighters at fighters
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
                    
                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;
		        /* simplified above
                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed() )
                        && !$weapon->ballistic){
                    continue;
                }*/
		    
                //$fire->priority = $weapon->priority; priority already set!
                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);

        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if ( ($target == null) || ($target instanceof FighterFlight) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

        //FIRE fighters at ships
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

                //ballistic weapons will still reach their targets, but direct fire from fighters previously destroyed will not happen
                if ( (!$weapon->ballistic) && ($ship->getFighterBySystem($weapon->id)->isDestroyed()) ) continue;
                /*simplified above	
                $weapon = $ship->getSystemById($fire->weaponid);
                if (($ship->getFighterBySystem($weapon->id)->isDestroyed() || $ship->isDestroyed())
                    && !$weapon->ballistic) {
                    continue;
                }
                */
                //$fire->priority = $weapon->priority; //fire order priority already declared!
                $chosenfires[] = $fire;
            }
        }
        usort($chosenfires, [self::class, 'compareFiringOrders']);
        //FIRE rest of fighters
        foreach ($chosenfires as $fire){
            $shooter = $gamedata->getShipById($fire->shooterid);
            $target = $gamedata->getShipById($fire->targetid);
            if  ( ($target != null) && (!($target instanceof FighterFlight)) ) {
                self::fire($shooter, $fire, $gamedata);
            }
        }

        //Check if any ships have activate jump engines and do this after all other fire (in case they or their jump engine got destroyed)
        foreach ($gamedata->ships as $ship) {   
            
            $jumpList = $ship->getSystemsByName('Jump Engine'); //Won't return if Jump engine destroyed.
            if ($ship->faction === 'Shadow Association') { //PhasingDrive extends JumpEngine but has a different display name; only Shadow Association ships have one.
                $jumpList = array_merge($jumpList, $ship->getSystemsByName('Phasing Drive'));
            }
            foreach($jumpList as $jumpEngine){
                //is it overloading?...
                if( $jumpEngine->isOverloading($gamedata->turn) ){ //primed for entering hyperspace!				
                    $jumpEngine->doHyperspaceJump($ship, $gamedata); //Actually create damage entry to destroy ship.
                }
            }
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
        $target = $gamedata->getShipById($fire->targetid);

        // If the target is an attached pod, weapon fires against it normally, but we also spawn a duplicate automatic hit against the host ship
        if ($target && !empty($target->attached) && $target instanceof FighterFlight) {
            $hostShipId = key($target->attached);
            $hostShip = $gamedata->getShipById($hostShipId);
            if ($hostShip && !$hostShip->isDestroyed() && $hostShip->userid !== -5) {
                $savedAmmo = null;
                if (property_exists($weapon, 'ammunition')) $savedAmmo = $weapon->ammunition;
                
                $hostFire = new FireOrder(-1, $fire->type, $fire->shooterid, $hostShipId, $fire->weaponid, -1, $fire->turn, $fire->firingMode, 100, 1, $fire->shots, $fire->shotshit, $fire->intercepted, $fire->x, $fire->y, $fire->damageclass);
                $hostFire->needed = 100;
                $hostFire->rolled = 1;
                $hostFire->pubnotes = " Automatically on ship from shooting at an attached pod.";
                $hostFire->targetid = $hostShipId;
                $hostFire->id = -1; // New order
                $hostFire->addToDB = true;
                $hostFire->shotshit = 0;
                $hostFire->intercepted = 0;
                
                $weapon->fire($gamedata, $hostFire);
                $weapon->fireOrders[] = $hostFire;
                
                if ($savedAmmo !== null) $weapon->ammunition = $savedAmmo;
            }
        }

        $weapon->fire($gamedata, $fire);

    } //endof method fire


} //endof class Firing

?>
