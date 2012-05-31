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
                $shooter = $gd->getShipById($fire->shooterid);
                $target = $gd->getShipById($fire->targetid);
                $firingweapon = $shooter->getSystemById($fire->weaponid);
                            
                
                $damage = $firingweapon->getAvgDamage() * ceil($fire->shots/2);
                $hitChance = $firingweapon->calculateHit($gd, $fire);
                $numInter = $firingweapon->getNumberOfIntercepts($gd, $fire);
                
                $perc = 0;
                for ($i = 0; $i<$this->weapon->guns;$i++){
                    $perc += (($this->weapon->intercept*5) - (($numInter+$i)*5));
                }
                $perc *= 0.01;
                
                if ($perc<=0)
                    $candidate->blocked = 0;
                else
                    $candidate->blocked = $damage*$perc;
                    
                if (!$best || $best->blocked < $candidate->blocked )
                    $best = $candidate;
                
            }
            
            if ($best){
                for ($i = 0; $i<$this->weapon->guns;$i++){
                    $interceptFire = new FireOrder(-1, "intercept", $this->ship->id, $best->fire->id, $this->weapon->id, -1, 
                    $gd->turn, $this->weapon->firingMode, 0, 0, $this->weapon->defaultShots, 0, 0, null, null);
                    $interceptFire->addToDB = true;
                    $this->ship->fireOrders[] = $interceptFire;
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
    
    public static function validateFireOrders($fireOrders, $gamedata){
    
        return true;
    
    }
    
   
    
    public static function automateIntercept($gd){
        
        foreach ($gd->ships as $ship){
            $intercepts = Array(); 
            foreach($ship->systems as $weapon){
                                
                if (!($weapon instanceof Weapon))
                    continue;
                
                $weapon = $weapon->getWeaponForIntercept();
                
                if (!$weapon)
                    continue;
                
                if ($weapon->intercept == 0)
                    continue;
                    
                if ($weapon->isDestroyed()){
                    //print($weapon->displayName . " is destroyed and cannot intercept " . $weapon->id);
                    continue;
                }
                
                //CHECK IF THIS GUN IS ALREADY FIRING!
                if ($weapon->firedOnTurn($ship, $gd->turn)){
					continue;
				}
				
                if ($weapon->isOfflineOnTurn($gd->turn))
                    continue;
                
                if ($weapon->ballistic)
                    continue;
                    
                $weapon->setLoading($ship, $gd->turn-1, 3);
                if ($weapon->loadingtime > 1 || $weapon->turnsloaded < $weapon->loadingtime)
                    continue;
                   
                $possibleIntercepts = self::getPossibleIntercept($gd, $ship, $weapon);
                $intercepts[] = new Intercept($ship, $weapon, $possibleIntercepts);
                    
            }
            
            self::doIntercept($gd, $ship, $intercepts);
            
            
        }
        
    }
    
    public static function doIntercept($gd, $ship, $intercepts){
        
        if (sizeof($intercepts)==0)
            return;
            
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
    
    public static function getPossibleIntercept($gd, $ship, $weapon){
        
        $intercepts = array();
        
        foreach($gd->ships as $shooter){
            if ($shooter->id == $ship->id)
                continue;
            
            if ($shooter->team == $ship->team)
                continue;
                      
            foreach($shooter->fireOrders as $fire){
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

        $turn = $gamedata->turn;
        
        foreach ($gamedata->ships as $ship){
			if ($ship instanceof FighterFlight)
				continue;
			
			//FIRE all ships
            foreach($ship->fireOrders as $fire){
				self::fire($ship, $fire, $gamedata);
			}
            
        }
        
		$chosenfires = array();
		foreach($gamedata->ships as $ship){
			if ($ship->isDestroyed())
				continue;
			
			if (!($ship instanceof FighterFlight))
				continue;
				
			foreach($ship->fireOrders as $fire){
							
				$weapon = $ship->getSystemById($fire->weaponid);
				if ($ship->getFighterBySystem($weapon->id)->isDestroyed())
					continue;
					
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
			if ($ship->isDestroyed())
				continue;
			
			if (!($ship instanceof FighterFlight))
				continue;
				
			foreach($ship->fireOrders as $fire){
							
				$weapon = $ship->getSystemById($fire->weaponid);
				if ($ship->getFighterBySystem($weapon->id)->isDestroyed())
					continue;
					
				$chosenfires[] = $fire;
			}
		}
		
		//FIRE rest of fighters
		foreach ($chosenfires as $fire){
			$shooter = $gamedata->getShipById($fire->shooterid);
			self::fire($shooter, $fire, $gamedata);
			
		}
        
        //throw new Exception("kaadu");
	}
    
    
    private static function fire($ship, $fire, $gamedata){
		
		if ($fire->turn != $gamedata->turn)
			return;
			
		if ($fire->type == "intercept")
			return;
			
		if ($fire->rolled > 0)
			return;
		
		$weapon = $ship->getSystemById($fire->weaponid);
		
		//if ($weapon->ballistic)
		//	return;
			
		if ($fire->rolled>0)
			return;
			
		//print("Firing " . $fire->id . "\n");
		$weapon->setLoading($ship, $gamedata->turn-1, 3);
		$weapon->fire($gamedata, $fire);
		
	}
}

?>
