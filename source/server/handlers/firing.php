<?php
    class Intercept{
        
        public $weapon, $intercepts, $done, $ship;
        
        function __construct($ship, $weapon, $intercepts ){
            
            $this->ship = $ship;             
            $this->weapon = $weapon;
            $this->intercepts = $intercepts;
        }
        
        public function chooseTarget($gd){
            $best = null;
            foreach ($this->intercepts as $candidate){
                $fire = $candidate->fire;

                $target = $gd->getShipById($fire->targetid);
          //      debug::log("itnercept for ".$target->phpclass);


                if ( $target->isDisabled() && $target->shipSizeClass > 0){
                    continue;
                }
                
                $shooter = $gd->getShipById($fire->shooterid);
                $firingweapon = $shooter->getSystemById($fire->weaponid);


                $hitLocation = $target->getHitSection($shooter->getCoPos(), $shooter, $fire->turn, $firingweapon);
                $armour;

        //        debug::log("intercepting fire from: ".$target->phpclass." versus opposing ".$firingweapon->displayName." from ".$shooter->phpclass);

                if ($target->shipSizeClass == -1){
                    $armour = $target->systems[1]->armour[$hitLocation];
                //    debug::log("flight armour: ".$armour);

                }
                else if ($target->shipSizeClass == 1){
                    $structure = $target->getStructureByIndex(0);
                    $armour = $structure->armour;
                //    debug::log("MCV armour: ".$armour);
                }
                else {
                    $structure = $target->getStructureByIndex($hitLocation);
                    $armour = $structure->armour;
                 //   debug::log("non-flight armour: ".$armour);
                }

                if ($firingweapon instanceof Matter){
                    $armour = 0;
                } else if ($firingweapon instanceof Plasma){
                    $armour = ceil($armour/2);
                } else if ($firingweapon instanceof FusionAgitator){                    
                    $armour = $armour - 3;
                }

                if ($armour < 0 || !$armour){
                    $armour = 0;
                }
                      

                if ($firingweapon instanceof Raking){
                    $avg = $firingweapon->getAvgDamage();
                    $rakes = $avg / $firingweapon->raking;
                    $totalReduction = floor($armour *  $rakes); //floor to account for ability for rakes to hit same sys, hence NO armour
                    $damage = $avg - $totalReduction;
                }
                else {                     
                   $damage = ($firingweapon->getAvgDamage() - $armour) * (ceil($fire->shots / 2));
                }


                //disable interception of low-threat weapons with medium reload weapons
                if ($damage < 15){
                    if ($this->weapon->loadingtime > 1){
                        continue;
                    }
                }

            //    debug::log($firingweapon->displayName.", total estimated dmg: ".$damage.", considering armour of:".$armour." and shots: ".(ceil($fire->shots / 2)));
                $numInter = $firingweapon->getNumberOfIntercepts($gd, $fire);
                
                $perc = 0;
     //               debug::log($this->weapon->displayName." running intercept.");
                for ($i = 0; $i<$this->weapon->guns;$i++){
                    $perc += (($this->weapon->getInterceptRating($gd->turn)*5) - (($numInter+$i)*5));
                }
                $perc *= 0.01;

                if ($perc<=0)
                    $candidate->blocked = 0;
                else
                    $candidate->blocked = $damage*$perc;
                    
                if (!$best || $best->blocked < $candidate->blocked )
                    $best = $candidate;
            }
            
            if ($best && $best->blocked > 0){

                $shooter = $gd->getShipById($best->fire->shooterid);
                $firingweapon = $shooter->getSystemById($best->fire->weaponid);
        //        debug::log("intercepting: ".$firingweapon->displayName." for a reduction of: ".$perc. " using: ".$this->weapon->displayName);


                $interceptor = $target->getSystemById($this->weapon->id);

                for ($i = 0; $i<$this->weapon->guns;$i++){
                    if ($this->weapon->displayName == "Point Pulsar"){
            //            debug::log("shots: ".$this->weapon->defaultsShots);
                    }    
                    $interceptFire = new FireOrder(-1, "intercept", $this->ship->id, $best->fire->id, $this->weapon->id, -1, 
                    $gd->turn, $this->weapon->firingMode, 0, 0, $this->weapon->defaultShots, 0, 0, null, null);
//                    var_dump($interceptFire);

                    $interceptFire->addToDB = true;
                    $interceptor->fireOrders[] = $interceptFire;
                }
                if (sizeof($interceptor->fireOrders == 2) && $interceptor->fireOrders[0]->type == "selfIntercept"){
                    checkForSelfInterceptFire::setFired($interceptor->id, $gd->turn);
                }
            }
        }
    }
    
    class InterceptCandidate{
        public $fire;
        public $blocked = 0;
        
        function __construct($fire ){
            
            $this->fire = $fire;             
            
           
        
        }
        
    }


class Firing{
    public $gamedata;
    
    public static function validateFireOrders($fireOrders, $gamedata){
    
        return true;
    
    }
    
    public static function automateIntercept($gd){
        
        foreach ($gd->ships as $ship){
            if (!$ship instanceof FighterFlight){
                if($ship->unavailable === true || $ship->isDisabled()){
                continue;
                }
            }              
            
            if ($ship instanceof FighterFlight)
            {
                $intercepts = self::getFighterIntercepts($gd, $ship);
            }
            else
            {
                $intercepts = self::getShipIntercepts($gd, $ship);
            }
            
            self::doIntercept($gd, $ship, $intercepts);
        }
        
    }
    
    private static function getFighterIntercepts($gd, $ship){

        $intercepts = Array(); 
        foreach($ship->systems as $fighter)
        {
            $exclusiveWasFired = false;
            
            if ($fighter->isDestroyed()){
                continue;
            }
            
            // check if fighter is firing weapons that exclude other
            // weapons from firing. (Like IonBolt on a Rutarian.)
            foreach ($fighter->systems as $weapon){
                if($weapon instanceof Weapon){
                    if($weapon->exclusive && $weapon->firedOnTurn($gd->turn)){
                        $exclusiveWasFired = true;
                        break;
                    }
                }
            }
            
            if($exclusiveWasFired){
                continue;
            }
            
            foreach ($fighter->systems as $weapon)
            {
                if($weapon instanceof PairedGatlingGun && $weapon->ammunition < 1){
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


     }
    
    private static function getShipIntercepts($gd, $ship)
    {
        $intercepts = Array(); 
        
        foreach($ship->systems as $weapon)
        {
            if (self::isValidInterceptor($gd, $weapon) === false)
               continue;

    //    debug::log($weapon->displayName." intercepts");

            $possibleIntercepts = self::getPossibleIntercept($gd, $ship, $weapon, $gd->turn);
            $intercepts[] = new Intercept($ship, $weapon, $possibleIntercepts);
        }
        return $intercepts;
    }
    
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
    
    public static function isLegalIntercept($gd, $ship, $weapon, $fire){
        
        //Debug::log("\n\nIS LEGAL INTERCEPT: ". $ship->name . ' weapon: ' . $weapon->name . '(' .$weapon->id . ")\n");
        
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
         
        $pos = $shooter->getCoPos();   
        $shooterCompassHeading = null;

        if ($firingweapon->ballistic){
            $movement = $shooter->getLastTurnMovement($fire->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
            $shooterCompassHeading = mathlib::getCompassHeadingOfPos($ship, $pos);
        }else{
			$shooterCompassHeading = mathlib::getCompassHeadingOfShip($ship, $shooter);
		}
        
        $tf = $ship->getFacingAngle();
        
      
        if (!mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection($weapon->startArc,$tf), Mathlib::addToDirection($weapon->endArc,$tf) )){
            //Debug::log("Fire is not on weapon arc\n");
            return false;
        }
        
        if ($target->id == $ship->id){
            //Debug::log("Target is this ship\n");
            //Debug::log("VALID INTERCEPT\n");
            return true;
        }else{
            if (!$weapon->freeintercept){
                //Debug::log("Target is another ship, and this weapon is not freeintercept \n");
                return false;
            }
            //Debug::log("Target is this another ship\n");
            $distanceShipTarget = mathlib::getDistanceHex($target->getCoPos(), $ship->getCoPos());
            $distancePosShip = mathlib::getDistance($pos, $ship->getCoPos());
            $distancePosTarget = (mathlib::getDistance($target->getCoPos(), $pos));
            
            //Debug::log("distanceShipTarget: $distanceShipTarget, distancePosShip: $distancePosShip, distancePosTarget: $distancePosTarget \n");
            
            if ($distanceShipTarget<=3 &&  ($distancePosShip < $distancePosTarget)){
                //Debug::log("VALID INTERCEPT\n");
                return true;
            }
            
        }
         //Debug::log("INVALID INTERCEPT\n");   
         return false;   
        
    }


    
    public static function fireWeapons($gamedata){


        $fireOrders  = array();

        
        foreach ($gamedata->ships as $ship){
            if ($ship instanceof FighterFlight){
                continue;
            }

            foreach($ship->getAllFireOrders() as $fire){

                if ($fire->type === "intercept" || $fire->type === "selfIntercept"){
                    continue;
                }

                $weapon = $ship->getSystemById($fire->weaponid);

         //       debug::log($ship->id."___".$weapon->displayName." fireOrder");

                if ($weapon instanceof Thruster || $weapon instanceof Structure){
                    debug::log("DING");
                    debug::log($ship->phpclass);
                    debug::log($weapon->location);
                    debug::log($weapon->displayName);
                    debug::log("DING_2");
             //       debug::log($weapon->fireOrders[0]);
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
                }
                else if ($a->priority !== $b->priority){
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
    }
    

    
    private static function fire($ship, $fire, $gamedata){
        
		if ($fire->turn != $gamedata->turn)
			return;
			
		if ($fire->type == "intercept" || $fire->type == "selfIntercept")
			return;
			
		if ($fire->rolled > 0)
			return;

		
		$weapon = $ship->getSystemById($fire->weaponid);
		

		$weapon->fire($gamedata, $fire);
		
	}
}

?>
