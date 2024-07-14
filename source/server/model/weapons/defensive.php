<?php
    
    class InterceptorMkI extends Weapon implements DefensiveSystem{

        public $trailColor = array(30, 170, 255);
        
        public $name = "interceptorMkI";
        public $displayName = "Interceptor I";
        public $animation = "trail";
        public $iconPath = "interceptor.png";
        public $animationColor = array(30, 170, 255);
        //public $animationExplosionScale = 0.15;
        public $priority = 4;

        public $animationWidth = 1;
            
        public $intercept = 3;
             
        public $loadingtime = 1;
  
        public $rangePenalty = 2;
        public $fireControl = array(6, null, null); // fighters, <mediums, <capitals 
        
        public $output = 3;
        
        public $tohitPenalty = 0;
        public $damagePenalty = 0;

        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
    
        public function getDefensiveType()
        {
            return "Interceptor";
        }
        
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->tohitPenalty = $this->getOutput();
            $this->damagePenalty = 0;
     
        }
        
        public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
            if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
                return 0;

            $output = $this->output;
            $output -= $this->outputMod;
            return $output;
        }

        public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
            return 0;
        }
        
        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";
            $this->data["Special"] = "Energy Web: -15 to hit on arc with active Interceptor.";
            parent::setSystemDataWindow($turn);
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct(
                $armour, $maxhealth, $powerReq, $startArc, $endArc, $this->output);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      }               
    } //endof class Interceptor MkI
    


    class InterceptorMkII extends InterceptorMkI{
        public $name = "interceptorMkII";
        public $displayName = "Interceptor II";
        
        public $output = 4;
        public $intercept = 4;
        public $fireControl = array(8, null, null);
                        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Energy Web: -20 to hit on arc with active Interceptor.";
        }
    }
    

    class InterceptorPrototype extends InterceptorMkI{
        public $name = "interceptorPrototype";
        public $displayName = "Interceptor Prototype";
        public $priority = 3;
        
        public $output = 2;
        public $intercept = 2;
                
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Energy Web: -10 to hit on arc with active Interceptor.";
        }

        public function getDamage($fireOrder){        return Dice::d(10)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
    }



    class GuardianArray extends Weapon{
        public $trailColor = array(30, 170, 255);
        
        public $name = "guardianArray";
        public $displayName = "Guardian Array";
        public $animation = "bolt"; //originally Laser, but bolt seems more appropriate
        public $animationColor = array(30, 170, 255);
        //public $animationExplosionScale = 0.15;
        public $priority = 4;
            
        public $intercept = 3;
             
        public $freeintercept = true;
        public $loadingtime = 1;
  
        
        public $rangePenalty = 3;
        public $fireControl = array(8, null, null); // fighters, <mediums, <capitals 

        public $damageType = "Standard";
        public $weaponClass = "Particle";
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Can intercept fire directed at other ships, as long as Guardian is between firing unit and target and has firing unit in arc.";
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      }        

    }



    class SentinelPointDefense extends GuardianArray{

        public $trailColor = array(30, 170, 255);
        
        public $name = "sentinelPointDefense";
        public $displayName = "Sentinel Point Defense";
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15; //irrelevant with no offensive mode...

            
        public $intercept = 3;
             
        public $freeintercept = true;
        public $loadingtime = 1;

        public $rangePenalty = 6;
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals 

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Can intercept fire directed at other ships, as long as Guardian is between firing unit and target and has firing unit in arc.";
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
    } //endof class SentinelPointDefense



    class Particleimpeder extends Weapon implements DefensiveSystem{
        /*Abbai defensive system*/
        /*changed so it can be boosted for power, instead of EW; boost part affects fighters (only!) hit chance*/      
        public $name = "Particleimpeder";
        public $displayName = "Particle Impeder";
        public $iconPath = "particleImpeder.png";
	    
        public $animation = "laser";
        public $animationColor = array(160, 160, 160);
        public $animationExplosionScale = 0.15; //irrelevant with no offensive mode...
	    
	    
        public $priority = 1; //will never fire anyway except defensively, purely a defensive system
            
        public $intercept = 3;
             
        public $loadingtime = 1;
  
        public $rangePenalty = 2; //irrelevant
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals 
        
        public $output = 0;
	
        
        public $boostable = true; //can be boosted for additional effect
	    public $boostEfficiency = 0; //cost to boost by 1 - FREE (it will impact EW instead)
        public $maxBoostLevel = 20; //maximum boost allowed - just technical limitation, rules dont set any maximum; 20 seems close enough to "unlimited" :)
        public $canInterceptUninterceptable = true; //can intercept weapons that are normally uninterceptable
        
        public $tohitPenalty = 0;
        public $damagePenalty = 0;
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
        
    	protected $ewBoosted = true;          
        
     	protected $possibleCriticals = array( //different than usual B5Wars weapon - simplification
            16=>"ForcedOfflineOneTurn"
	);
	    
	    
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn) continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }
	            
        public function getInterceptRating($turn){
            $ir = 3 + $this->getBoostLevel($turn);
            return $ir;
        }
	    
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->tohitPenalty = $this->getOutput();
            $this->damagePenalty = 0;
            $this->intercept = $this->getInterceptRating($turn);
            $this->output = $this->getOutput();
        }
        public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
            if ($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn)) return 0;
            //now it affects everything, as per rules :)
            //if (!($shooter instanceof FighterFlight)) return 0;//affects fighters only!
            $output = $this->getBoostLevel($turn);
            $output -= $this->outputMod;
            return $output;
        }
        public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
            //no effect on actual damage
            return 0;
        }
	    
        public function getDefensiveType()
        {
            return "Impeder";
        }
	    
  
        public function setSystemDataWindow($turn){
			$this->data["Boostlevel"] = $this->getBoostLevel($turn);
            $this->data["Special"] = "Can intercept uninterceptable weapons.";
            $this->data["Special"] .= "<br>Can be boosted for increased intercept rating (5% per step, up to +" . $this->maxBoostLevel . " steps).";
            $this->data["Special"] .= "<br>Boost means channeling EW through Impeder - it does not cost Power, but EW available."; //this effect is handled by EW routines in front end! - function getEWLeft
            $this->data["Special"] .= "<br>Boost itself reduces hit chance of all incoming fire (shield-like, but cumulative with regular shields). Impeder cannot be flown under.";
			$this->data["Special"] .= "<br>Does not reduce damage.";
            parent::setSystemDataWindow($turn);
            
            $this->intercept = $this->getInterceptRating($turn);
			if ($this->data["Boostlevel"] > 0) {
				$this->outputDisplay = $this->data["Boostlevel"]; //display boost level on weapon - this equals to shield rating, which is important - while arming status is irrelevant for 1/turn weapon
			}else{
				$this->outputDisplay = '-'; //because '0' is interpreted as empty and display resets to default!
			}
        }
          
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    if ($maxhealth == 0) $maxhealth = 6;
	    if ($powerReq == 0) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $this->output);
        }
   
        private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under Impeder
            return false;
        }
	    
        
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }
        
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->ewBoosted = $this->ewBoosted;													
			return $strippedSystem;
		}           
        
    }//endof class ParticleImpeder




    class EMWaveDisruptor extends Weapon{
        public $trailColor = array(30, 170, 255);        
        public $name = "eMWaveDisruptor";
        public $displayName = "EM-Wave Disruptor";
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 1;
        public $animationWidth2 = 0;
        public $boostable = true; //no limit to number of boosts; +1 shot ber boost. Originally can combine 2 shots for -6 interception (and no further than that), in FV there are no special rules regarding this (eg. may use as many as desired but is affected by degradation as normal)
        public $boostEfficiency = 4;            
        public $intercept = 3;
        public $loadingtime = 1;
        public $guns = 2; //2 separate shots; can be boosted
		public $iconPath = "emWaveDisruptor.png";
		public $priority = 2; //important for AF fire only, but priority 2 stays 2
        
        public $rangePenalty = 2;
        public $fireControl = array(4, null, null); // fighters, <mediums, <capitals 

    public $damageType = "Standard"; 
    public $weaponClass = "Electromagnetic";
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    

        public function setSystemDataWindow($turn){
	    $this->guns = 2+$this->getBoostLevel($turn);
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Intercept / Dropout";
            $this->data["Special"] .= "<br>Can be boosted to increase number of shots.";
        }
        
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }


        protected function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;

                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }
	    
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		/*causes fighter hit to drop out*/
		$crit = null;
		
		if (!$system->advancedArmor){
			if ($system instanceof Fighter && !($ship->superheavy)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}
		}
	}		

        public function getBonusCharges($turn){
            return $this->getBoostLevel($turn);
        }
    } //endof class EMWaveDisruptor


    class Interdictor extends Weapon{
        public $name = "Interdictor";
        public $displayName = "Interdictor";
		public $iconPath = "Interdictor.png";

        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal

        public $loadingtime = 1;
        public $priority = 1; //will never fire anyway except defensively, purely a defensive system
		public $autoFireOnly = true; //this weapon cannot be fired by player

        public $rangePenalty = 2; //irrelevant
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals

		public $firingMode = 'Intercept'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
        
        public $intercept = 4;
		protected static $alreadyIntercepted = array(); //Only one Interdictor can intercept any shot, including ballistics.

     	protected $possibleCriticals = array( //different than usual B5Wars weapon
            16=>"ForcedOfflineOneTurn"
		);

		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes.";
            $this->data["Special"] .= "<br>Only one interdictor can be applied to any incoming shot, including ballistics.";
        }
                
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
//			InterdictorHandler::addInterdictor($this);//so all Interdictors are accessible together, and firing orders can be uniformly created
        }

		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 5 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 5) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}

	    /*return 0 if given fire order was already intercepted by this weapon - this should prevent such assignment*/

	public function getInterceptionMod($gamedata, $intercepted)
	{
		$wasIntercepted = false;
		$interceptMod = 0;
		
		foreach(Interdictor::$alreadyIntercepted as $alreadyAssignedAgainst){
			if ($alreadyAssignedAgainst->id == $intercepted->id){ //this fire order was already intercepted by this weapon, this Scattergun cannot do so again
				$wasIntercepted = true;
				break;//foreach
			}
		}
		if(!$wasIntercepted) $interceptMod = parent::getInterceptionMod($gamedata, $intercepted);
		return $interceptMod;
	}//endof  getInterceptionMod


        
	//on weapon being ordered to intercept - note which shot (fireorder, actually) was intercepted!
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		parent::fireDefensively($gamedata, $interceptedWeapon);
		Interdictor::$alreadyIntercepted[] = $interceptedWeapon;
	}	    
		
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    }  // end of class Interdictor
	



/*fighter-mounted variant*/
    class FtrInterdictor extends Interdictor{
        public $name = "FtrInterdictor";
        public $displayName = "Light Interdictor";
		public $iconPath = "Interdictor.png";

        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal

        public $loadingtime = 1;
        public $priority = 1; //will never fire anyway except defensively, purely a defensive system
		public $autoFireOnly = true; //this weapon cannot be fired by player

        public $rangePenalty = 2; //irrelevant
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals

		public $firingMode = 'Intercept'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
        
        public $intercept = 3;
        
		private $flightIntercepted = array(); //To track when weapon intercept fighter direct fire and may be able to intercept more.

		function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 3 hexes.";
            $this->data["Special"] .= "<br>Only one interdictor can be applied to any incoming shot, including ballistics.";
            $this->data["Special"] .= "<br>When intercepting non-ballistic fire from an enemy fighter flight, all enemy fighters have their chance to hit reduced by 15.";
            $this->data["Special"] .= "<br>Note - Automated intercept routines will often prioritise ship or missile fire, so aim Interdictor carefully to make full use of it against enemy fighter fire.";            
        }

		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 3 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 3) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}

	//return 0 if given fire order was already intercepted by this weapon - this should prevent such assignment
	public function getInterceptionMod($gamedata, $intercepted)
	{
		$wasIntercepted = false;	
		$interceptMod = 0;
		
		foreach(FtrInterdictor::$alreadyIntercepted as $alreadyAssignedAgainst){
			if ($alreadyAssignedAgainst->id == $intercepted->id){ //this fire order was already intercepted by this weapon, cannot do so again
				$wasIntercepted = true;
				break;//foreach
			}
		}
 
		$sameFlight = false;
		         		
		if (!empty($this->flightIntercepted)){ //Is the array populated i.e. at least the 2nd intercepting shot?  Will skip this part if not populated i.e. 1 intercepting shot.     		
			if (isset($this->flightIntercepted[$intercepted->shooterid])){  //Is it the same fighter flight?  		
			$sameFlight = true;	//Mark true if above conditions are met.		
			}				
		}

//If shot hasn't been intercepted by an Interdictor and it's the same flight as this weapon has intercepted (or the first shot it's intercepting from flight), go to usual routine.		
		if($sameFlight){
	        $interceptMod = parent::getInterceptionMod($gamedata, $intercepted);
	        return $interceptMod;
	    }else if(!$wasIntercepted){	    	
	        $interceptMod = parent::getInterceptionMod($gamedata, $intercepted);
	        return $interceptMod;	    	
	    }		
		//Otherwise, return the intialised value of 0 as Intercept value.		
		return $interceptMod;
	}//endof  getInterceptionMod

        
	//on weapon being ordered to intercept - note which shot (fireorder, actually) was intercepted!
	public function fireDefensively($gamedata, $intercepted) //Gamedata and a fireOrder passed.
	{
		parent::fireDefensively($gamedata, $intercepted);
		FtrInterdictor::$alreadyIntercepted[] = $intercepted;
		
        $shooter = $gamedata->getShipById($intercepted->shooterid);
		$target =  $gamedata->getShipById($intercepted->targetid);       					
		if ($shooter instanceof FighterFlight){ //We're only interested in fighters.
				if (empty($this->flightIntercepted) && $intercepted->type != 'ballistic'){ //Is the array empty?  If so let's fill it with the Fighter Flight fire order that was intercepted, but not if ballistic shot was intercepted.			. 		
					$this->flightIntercepted[] = $intercepted;//Note the specific flight that can be intercepted further.
			        $this->guns += 1; //Add an extra shot for anymore direct fire from this flight, 
				}else{
					foreach($this->flightIntercepted as $interceptedFlight){					
						if ($interceptedFlight->shooterid == $intercepted->shooterid){ //this fire order was already intercepted by this weapon, cannot do so again
				        	$this->guns += 1; //Add an extra shot for anymore direct fire from this flight.
							break;//foreach
						}	
					}	
				}
		}
	}	    

        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }

		
	}// endof FtrInterdictor


/*
	B5Wars-style Shield for fighters (doesn't matter whether it's Gravitic or EM, effect is the same)
	can't fly under fighter shield!
*/
class FtrShield extends Shield implements DefensiveSystem{
    public $name = "FtrShield";
    public $displayName = "Shield";
    public $iconPath = "shield.png";
    public $boostable = false;
    public $baseOutput = 0; //base output, before boost
		
 	protected $possibleCriticals = array( //irrelevant for fighter system
            16=>"OutputReduced1"
	);
	
    function __construct($shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct(0, 1, 0, $shieldFactor, $startArc, $endArc);
		$this->baseOutput = $shieldFactor;
    }
	
    public function onConstructed($ship, $turn, $phase){
        	parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = $this->getOutput();
		$this->damagePenalty = $this->getOutput();
    }
	
    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under fighter shield
        return false;
    }
	
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ 
            return $this->output;
    }
	
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
	    return $this->output;
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Basic Strength"] = $this->output;      
		if (!array_key_exists ( "Special" , $this->data )) {
			$this->data["Special"] = "";
		}else{
			$this->data["Special"] .= "<br>";
		}
		$this->data["Special"] .= "Cannot fly under fighter shield."; 
    }
	
} //endof class  FtrShield

class HeavyInterceptorBattery extends InterceptorMkI{
        public $name = "HeavyInterceptorBattery";
        public $displayName = "Heavy Interceptor Battery";
        public $iconPath = "HeavyInterceptor.png";        
        public $priority = 6; //that's heavy weapon damage output!
        
        public $output = 4;
        public $intercept = 4;
        public $fireControl = array(10, null, null); 
                                 
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Energy Web: -20 to hit on arc with active Interceptor.";
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }
   
}  //end of class HeavyInterceptorBattery

//Adding Thirdspace as unique systems to use different icon and allow modifictions from the Trek systems
class ThirdspaceShield extends Shield implements DefensiveSystem { //defensive values of zero, but still formally there to display arcs!
	    public $name = "ThirdspaceShield";
	    public $displayName = "Shield Projection";
	    public $primary = true;
		public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
		public $isTargetable = false; //cannot be targeted ever!
	    public $iconPath = "ThirdspaceShieldF.png"; //overridden anyway - to indicate proper direction
	    
		protected $possibleCriticals = array(); //no criticals possible
		
		//Shield Projections cannot be repaired at all!
		public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

//		private $projectorList = array();
		
		protected $doCountForCombatValue = false;		//To ignore projection for combat value calculations
		public $changeThisTurn = 0;		
		public $currentHealth = 0; //Value for front-end when moving shield power around.
		public $maxStrength = 0;//Maximum capacity of shield even after boosting with others.
	    
	    function __construct($armor, $startHealth, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $startHealth, $Rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$this->iconPath = 'ThirdspaceShield' . $side . '.png';
			parent::__construct($armor, $startHealth, 0, $rating, $startArc, $endArc);
			$this->maxStrength = $startHealth*2;
			//rating not currently used.			
		}
		
		
		
	    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
	            return 0;
	    }
		public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){ //no shield-like damage reduction
			return 0;
		}
	    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under shield
	        return false;
	    }
		

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);  
			$this->data["Special"] = "Defensive system which absorbs damage from incoming shots within its arc.";
			$this->data["Special"] .= "<br>Can absorb up to its maximum capacity before allowing damage to ship.";		
			$this->data["Special"] .= "<br>Shield system's structure represents damage capacity, if it is reduced to zero system will cease to function.";
			$this->data["Special"] .= "<br>Can't be destroyed unless associated structure block is also destroyed.";
			$this->data["Special"] .= "<br>Cannot be flown under, and does not reduce the damage dealt or hit chance of enemy weapons.";
			$this->data["Special"] .= "<br>Has an Armor value of "  . $this->armour . ".";				
			$this->data["Max Strength"] = $this->maxStrength;			
			$this->currentHealth = $this->getRemainingCapacity();//override on-icon display default
			$this->outputDisplay = $this->currentHealth;//override on-icon display default					
		}	
		
		public function getRemainingCapacity(){
			return $this->getRemainingHealth();
		}
		
		public function getUsedCapacity(){
			$shieldHeadroom = $this->maxStrength - $this->getRemainingHealth();
			return $shieldHeadroom;
		}
		
		public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Regenerate!", "ThirdspaceShield");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;
		}
		
		
		
		//decision whether this system can protect from damage - value used only for choosing strongest shield to balance load.
		public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false) {
	//		if($damageWasDealt) return 0; //Thirdspace shields do protect from overkill
			
			$remainingCapacity = $this->getRemainingCapacity();
			$protectionValue = 0;
			if($remainingCapacity>0){
				$protectionValue = $remainingCapacity+$this->armour; //this is actually more than this system can protect from - but allows to balance load between systems in arc
			}
			return $protectionValue;
		}
		//actual protection
		public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
			$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
			$damageToAbsorb=$effectiveDamage; //shield works BEFORE armor
			$damageAbsorbed=0;
			
			if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
			
			$remainingCapacity = $this->getRemainingCapacity();
			$absorbedDamage = 0;
			
			if($remainingCapacity>0) { //else projection does not protect
				$absorbedFreely = 0;
				//first, armor takes part
				$absorbedFreely = min($this->armour, $damageToAbsorb);//So either armour value, or if damage remaining is less than armour then it's that.
				$damageToAbsorb += -$absorbedFreely;//Re-added 26.6.24 - But applies to every rake, which I might not want actually.
				//next, actual absorbtion
				$absorbedDamage = min($this->output - $this->armour , $remainingCapacity, $damageToAbsorb ); //no more than output (modified by already accounted for armor); no more than remaining capacity; no more than damage incoming
				$damageToAbsorb += -$absorbedDamage;
				if($absorbedDamage>0){ //mark!
					$this->absorbDamage($target,$gamedata,$absorbedDamage);
				}
				$returnValues['dmg'] = $damageToAbsorb;
				$returnValues['armor'] = min($damageToAbsorb, $returnValues['armor']);
			}
			
			return $returnValues;
		} //endof function doProtect

/*		    
		function addProjector($projector){
			if($projector) $this->projectorList[] = $projector;
		}
*/
/*		
		//effects that happen in Critical phase (after criticals are rolled) - replenishment from active projectors 
		public function criticalPhaseEffects($ship, $gamedata){
			
			parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.
			
			if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
			
			$activeProjectors = 0;
			$projectorOutput = 0;
			$toReplenish = 0;
			
			foreach($this->projectorList as $projector){
				if ( ($projector->isDestroyed($gamedata->turn))
				     || ($projector->isOfflineOnTurn($gamedata->turn))
				) continue;
				$activeProjectors++;
				$projectorOutput += $projector->getOutputOnTurn($gamedata->turn);
			}

			if($activeProjectors > 0){ //active projectors present - reinforce shield!
				$toReplenish = min($projectorOutput,$this->getUsedCapacity());		
			}
			
			if($toReplenish != 0){ //something changes!
				$this->absorbDamage($ship,$gamedata,-$toReplenish);
			}
		} //endof function criticalPhaseEffects
*/	

		//effects that happen in Critical phase (after criticals are rolled) - replenishment from active Generator 
		public function criticalPhaseEffects($ship, $gamedata){
			
			parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.
			
			if($this->isDestroyed()) return; //destroyed shield does not work...
			//Shields should not be destroyed, check damage this turn for anything that destroyed it and undestroy.

			$generator = $ship->getSystemByName("ThirdspaceShieldGenerator");	
			$generatorOutput = 0;

			if($generator){//Generator exists and is not destroyed!
				if($generator->isDestroyed()) return; //Double-check. Just in case.

				$generatorOutput = $generator->getOutput(); 				
				$toReplenish = min($generatorOutput,$this->getUsedCapacity());		
											
				if($toReplenish != 0){ //something changes!
					$this->absorbDamage($ship,$gamedata,-$toReplenish);
				}				
			}

		} //endof function criticalPhaseEffects

	// this method generates additional non-standard information in the form of individual system notes, in this case: - Initial phase: check setting changes made by user, convert to notes.	
	public function doIndividualNotesTransfer(){
	//data received in variable individualNotesTransfer, positive if Shield decreased, negative if Shield increased.
	    if (is_array($this->individualNotesTransfer) && isset($this->individualNotesTransfer[0])) { // Check if it's an array and the key exists
	        $shieldChange = $this->individualNotesTransfer[0];
	        $this->changeThisTurn = $shieldChange;
	    }	    
	    // Clear the individualNotesTransfer array
	    $this->individualNotesTransfer = array();
	}
	
		
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
					
				case 1: //Initial phase
					//data returned as a number to create a new damage entry.
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);	 								
							}
						}
						$this->onIndividualNotesLoaded($gameData);		

						$changeValue = $this->changeThisTurn;//Extract change value for shield this turn.																
												
						$notekey = 'change';
						$noteHuman = 'Shield value has been changed';
						$notevalue = $changeValue;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue         
					}			
										
			break;				
		}
	} //endof function generateIndividualNotes
	

	public function onIndividualNotesLoaded($gamedata)
	{
		$ship = $this->getUnit();
		$damageValue = 0;
							
	    foreach ($this->individualNotes as $currNote) {
	  		if($currNote->turn == $gamedata->turn) {  				    	
	        $damageValue = $currNote->notevalue; //Positive if decreased, negative if increased.
			}
		}				
		//actual change(damage) entry
		if($damageValue != 0){
		$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageValue, 0, 0, -1, false, false, 'shieldChange', 'shieldChange');
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;	
		}	
        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
        $this->individualNotes = array();
	 		  
	}//endof onIndividualNotesLoaded

		
	public function stripForJson() {
	    $strippedSystem = parent::stripForJson();
		$strippedSystem->currentHealth = $this->currentHealth;
		$strippedSystem->outputDisplay = $this->outputDisplay;
		$strippedSystem->maxStrength = $this->maxStrength;
					
	    return $strippedSystem;
	} 
	
}//endof class ThirdspaceShield


/*
class ThirdspaceShieldProjector  extends Shield implements DefensiveSystem { //defensive values of zero, but still formally there to display arcs!
	    public $name = "ThirdspaceShieldProjector";
	    public $displayName = "Shield Projector";
		public $isPrimaryTargetable = false; //projector can be targeted even on PRIMARY, like a weapon!
	    public $iconPath = "ThirdspaceProjectorF.png"; //overridden anyway - to indicate proper direction
	    public $boostable = true; //$this->boostEfficiency and $this->maxBoostLevel in __construct()  
		public $boostEfficiency = 4;
	    public $baseOutput = 0; //base output, before boost
	    
		
	    protected $possibleCriticals = array(
	            19=>"OutputReduced1",
	            28=>"OutputReduced2" );
		
		public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	    
	    function __construct($armor, $maxhealth, $power, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R/C suggests whether to use left or right graphics
			$this->iconPath = 'ThirdspaceProjector' . $side . '.png';
            if ( $power == 0 ){
                $power = 4;
            }			
			parent::__construct($armor, $maxhealth, $power, $rating, $startArc, $endArc);
			$this->baseOutput = $rating;
			$this->maxBoostLevel = floor($rating/2); //maximum double effect -1	
		}
		
		
	    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
	            return 0;
	    }
		public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){ //no shield-like damage reduction
			return 0;
		}
	    private function checkIsFighterUnderShield($target, $shooter){ //no flying under shield
	        return false;
	    }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn); 
			$this->data["Special"] = "Regenerates 4 health for the associated Shield per point of Projector output at the end of each turn .";
		$this->data["Special"] .= "<br> Output can be boosted " . $this->maxBoostLevel . " time(s) for " . $this->boostEfficiency . " power.";
		}	
		
	    public function getOutputOnTurn($turn){
	        $output = ($this->getOutput() + $this->getBoostLevel($turn))*4;
	        return $output;
	    }
		
		
		private function getBoostLevel($turn){
			$boostLevel = 0;
			foreach ($this->power as $i){
					if ($i->turn != $turn) continue;
					if ($i->type == 2){
							$boostLevel += $i->amount;
					}
			}
			return $boostLevel;
		}
		
} //endof class ThirdspaceShieldProjector
*/

?>
