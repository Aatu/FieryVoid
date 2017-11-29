<?php
    class Intercept{
        
        public $weapon, $intercepts, $done, $ship;
        
        function __construct($ship, $weapon, $intercepts ){            
            $this->ship = $ship;             
            $this->weapon = $weapon;
            $this->intercepts = $intercepts;
        }
        /* Marcin Sawicki, October 2017: ENTIRE CLASS is no longer needed?... leaving for now.*/
        public function chooseTarget($gd){
            $best = null;
            foreach ($this->intercepts as $candidate){
                $armour;
                $fire = $candidate->fire;

                $target = $gd->getShipById($fire->targetid);
          //      debug::log("itnercept for ".$target->phpclass);

                if ( $target->isDisabled() && $target->shipSizeClass > 0){
                    continue;
                }
                
                $shooter = $gd->getShipById($fire->shooterid);
                $firingweapon = $shooter->getSystemById($fire->weaponid);



                if ($shooter instanceof FighterFlight){
                    $armour = 2;
                }
                else if ($shooter instanceof MediumShip){
                    $armour = 4;
                }
                else if ($shooter instanceof StarBase){
                    $armour = 6;
                }
                else {
                    $armour = 5;
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

                if ($firingweapon instanceof SolarCannon){
                    $damage = $damage * 2;
                }


                //disable interception of low-threat weapons with medium reload weapons
                if ($damage < 10){
                    if ($this->weapon->loadingtime > 1){
                        continue;
                    }
                }

            //    debug::log($firingweapon->displayName.", total estimated dmg: ".$damage.", considering armour of:".$armour." and shots: ".(ceil($fire->shots / 2)));
                
		if($firingweapon->noInterceptDegradation){
			$numInter = 0;//target shot can be intercepted without degradation!
		}else{                
		    $numInter = $firingweapon->getNumberOfIntercepts($gd, $fire); //to calculate degradation
		}
		    
		    
                $perc = 0;
		
                for ($i = 0; $i<$this->weapon->guns;$i++){
                        $perc += (($this->weapon->getInterceptRating($gd->turn)*5);
			if(!$firingweapon->noInterceptDegradation) $perc += -(($numInter+$i)*5));
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
                    $interceptFire = new FireOrder(-1, "intercept", $this->ship->id, $best->fire->id, $this->weapon->id, -1, 
                    $gd->turn, $this->weapon->firingMode, 0, 0, $this->weapon->defaultShots, 0, 0, null, null);

                    $interceptFire->addToDB = true;
                    $interceptor->fireOrders[] = $interceptFire;
                }
                if (sizeof($interceptor->fireOrders == 2) && $interceptor->fireOrders[0]->type == "selfIntercept"){
                    checkForSelfInterceptFire::setFired($interceptor->id, $gd->turn);
                }
            }
        }
    }//endof class Intercept
    
/* Marcin Sawicki, October 2017: ENTIRE CLASS is no longer needed?... leaving for now.*/
    class InterceptCandidate{
        public $fire;
        public $blocked = 0;     
	    
        function __construct($fire ){            
            $this->fire = $fire;  
        }
        
    }

/*Marcin Sawicki problems during debug: copuying Firing class method after method*/
/*old version moved to .old file*/
class Firing{
    public $gamedata;
	
    public static function validateFireOrders($fireOrders, $gamedata){
            return true;
    }
	
    public static function firingExists(){	//just a test function
	return true;    
    }
	
	
}




?>
