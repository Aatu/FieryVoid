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
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals 
        
        public $output = 3;
        
        public $tohitPenalty = 0;
        public $damagePenalty = 0;

        public $damageType = "Standard"; 
        public $weaponClass = "Particle";

	    public $boostable = true;
	    public $boostEfficiency = 0;
	    public $maxBoostLevel = 1;
    
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

         protected function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }

	    public function beforeFiringOrderResolution($gamedata){		
		    parent::beforeFiringOrderResolution($gamedata);
	        if($this->getBoostLevel($gamedata->turn) > 0){
	            $this->intercept = 0; //If wepaon is boosted for Offensive Mode, cannot intercept!      	
            	$this->fireControl = array(6, null, null);
	        }        
		} //endof function beforeFiringOrderResolution
        
        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";
            if($this->getBoostLevel($turn) > 0){ //Boosted i.e. switched to Offeensive Mode this turn.
            	$this->fireControl = array(6, null, null); //To allow Offensive firing.       	
	            $this->intercept = 0; //If weapon is boosted for Offensive Mode, cannot intercept!            
            } 
            $this->data["Special"] = "Energy Web: -15 to hit on arc with active Interceptor.";
            $this->data["Special"] .= "<br>Can be fired offensively at fighters by boosting this system in Initial Orders Phase (at zero cost).";
            $this->data["Special"] .= "<br>However, when boosted system loses its Intercept Rating (E-Web is unaffected).";             
            parent::setSystemDataWindow($turn);
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct(
                $armour, $maxhealth, $powerReq, $startArc, $endArc, $this->output);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      } 
        
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->fireControl = $this->fireControl;
			$strippedSystem->intercept = $this->intercept;								
			return $strippedSystem;
		}        
                      
    } //endof class Interceptor MkI
    


    class InterceptorMkII extends InterceptorMkI{
        public $name = "interceptorMkII";
        public $displayName = "Interceptor II";
        
        public $output = 4;
        public $intercept = 4;
        public $fireControl = array(null, null, null);

	    public function beforeFiringOrderResolution($gamedata){		
		    parent::beforeFiringOrderResolution($gamedata);
	        if($this->getBoostLevel($gamedata->turn) > 0){
	            $this->intercept = 0; //If wepaon is boosted for Offensive Mode, cannot intercept!      	
            	$this->fireControl = array(8, null, null);
	        }        
		} //endof function beforeFiringOrderResolution
                        
        public function setSystemDataWindow($turn){
        	parent::setSystemDataWindow($turn);
            if($this->getBoostLevel($turn) > 0){           	
            	$this->fireControl = array(8, null, null); //To allow Offensive firing.       	
	            $this->intercept = 0; //If weapon is boosted for Offensive Mode, cannot intercept!            
            }          
            $this->data["Special"] = "Energy Web: -20 to hit on arc with active Interceptor.";
            $this->data["Special"] .= "<br>Can be fired offensively at fighters by boosting this system in Initial Orders Phase (at zero cost).";
            $this->data["Special"] .= "<br>However, when boosted system loses its Intercept Rating (E-Web is unaffected).";              
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
            if($this->getBoostLevel($turn) > 0){ //Boosted i.e. switched to Offeensive Mode this turn.
            	$this->fireControl = array(6, null, null); //To allow Offensive firing.       	
	            $this->intercept = 0; //If weapon is boosted for Offensive Mode, cannot intercept!            
            }            
            $this->data["Special"] = "Energy Web: -10 to hit on arc with active Interceptor.";
            $this->data["Special"] .= "<br>Can be fired offensively at fighters by boosting this system in Initial Orders Phase (at zero cost).";
            $this->data["Special"] .= "<br>However, when boosted system loses its Intercept Rating (E-Web is unaffected).";  
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
//		}elseif (!$system->hardAdvancedArmor){//hardened advanced armor prevents effect - GTS
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
        public $fireControl = array(null, null, null); 

	    public function beforeFiringOrderResolution($gamedata){		
		    parent::beforeFiringOrderResolution($gamedata);
	        if($this->getBoostLevel($gamedata->turn) > 0){
	            $this->intercept = 0; //If weapon is boosted for Offensive Mode, cannot intercept!      	
            	$this->fireControl = array(10, null, null);
	        }        
		} //endof function beforeFiringOrderResolution
                                 
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if($this->getBoostLevel($turn) > 0){ //Boosted i.e. switched to Offeensive Mode this turn.
            	$this->fireControl = array(10, null, null); //To allow Offensive firing.       	
	            $this->intercept = 0; //If weapon is boosted for Offensive Mode, cannot intercept!            
            }                         
            $this->data["Special"] = "Energy Web: -20 to hit on arc with active Interceptor.";
            $this->data["Special"] .= "<br>Can be fired offensively at fighters by boosting this system in Initial Orders Phase (at zero cost).";
            $this->data["Special"] .= "<br>However, when boosted system loses its Intercept Rating (E-Web is unaffected).";  
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
		
		protected $doCountForCombatValue = false;//To ignore projection for combat value calculations
		public $baseRating = 0; //Will be set when contructed.		
		public $changeThisTurn = 0;	//When shields moved around, change it tracked here to be made into Damage Entry.	
		public $currentHealth = 0; //Value for front-end when moving shield power around.
		public $side = '';//Required for prioritising a shield using Generator Presets.		
	    
	    function __construct($armor, $startHealth, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $startHealth, $Rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$this->iconPath = 'ThirdspaceShield' . $side . '.png';
			parent::__construct($armor, $startHealth, 0, $rating, $startArc, $endArc);
			$this->baseRating = $rating;			
			$this->side = $side;						
		}


	    public function setCritical($critical, $turn = 0){ //do nothing, shield projection should not receive any criticals
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
			$this->data["Special"] .= "<br>Absorbs up to its maximum capacity before allowing damage to ship.";		
			$this->data["Special"] .= "<br>Cannot be flown under, and does not reduce the damage dealt or hit chance of enemy weapons.";
	        $this->data["Special"] .= "<br>The Shield's Generator will regenerate Shields up to their Base Rating at the end of each turn, any excess will be allocate to another shield where possible.";			
			$this->data["Special"] .= "<br>Has a +3 Armor against attacks by Fighters.";	
 			$this->data["Base Rating"] = $this->baseRating; 									
			$this->currentHealth = $this->getRemainingCapacity();//override on-icon display default
			$this->outputDisplay = $this->currentHealth;//override on-icon display default					
		}	


		public function getRemainingCapacity(){
			return $this->getRemainingHealth();
		}
		
		public function getUsedCapacity(){
			return $this->getTotalDamage();
		}

		public function setShields($ship,$turn, $phase, $value){
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $turn, $this->id, $value, 0, 0, -1, false, false, "SetShield!", "ThirdspaceShield");
			if($phase == 4) $damageEntry->updated = true; //Don't duplicate a damage save in PreFiring phase.
			$this->damage[] = $damageEntry;
		}

		private function checkArmourDeduction($gamedata, $fireOrder){
			$deductArmour = true;

			foreach($this->damage as $damage){
				if($damage->turn != $gamedata->turn) continue;//Only interested in this turn.
				if($damage->fireorderid == $fireOrder->id) {
					$deductArmour = false;	//Previous damage this turn has been found from this raking weapon.
					break;//No need to search futher.
				}		
			}					
			return 	$deductArmour;		
		}//endof checkArmourDeduction
		
		public function absorbDamage($ship,$gamedata,$value, $fireOrderid = -1){ //or dissipate, with negative value
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Regenerate!", "ThirdspaceShield");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;
		}
			
		
		//decision whether this system can protect from damage - value used only for choosing strongest shield to balance load.
		public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false, $inflictingShots = 1, $isUnderShield = false) {	
			
			if($isUnderShield) return 0; //shield does not protect from internal damage

			$remainingCapacity = $this->getRemainingCapacity();
			$protectionValue = 0;
			if($remainingCapacity>0){
				$protectionValue = ($remainingCapacity / $inflictingShots) + $this->armour; //distribute capacity over shots
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
				$absorbedForFree = 0;
				
				//first, armor takes part
				$armour = $this->armour;	
				if($shooter instanceof FighterFlight) $armour = $armour + 3; //Increase armour against fighters only.			
				$deductArmour = true;//Do we want to deduct armour?

				if($weapon instanceof Raking) $deductArmour = $this->checkArmourDeduction($gamedata, $fireOrder);//Check armour assumption on Raking weapons.
				if(!$deductArmour) $armour = 0; //If returns false, Armour already taken into account for raking weapon.
	
				//Then check that damage is actually greater than Armour.		
				$absorbedForFree = min($armour, $damageToAbsorb);//So either armour value, or if damage remaining is less than armour then it's that.
				$damageToAbsorb += -$absorbedForFree;//Reduce amount to absorb by Armour or reduce to 0 if less than Armour.
				
				//next, actual absorbtion
				$absorbedDamage = min($remainingCapacity, $damageToAbsorb ); //no more than remaining capacity; no more than damage incoming
				$damageToAbsorb += -$absorbedDamage;//Deduct the amount of shield absorption from initial damage.
				
				if($absorbedDamage>0){ //Damage was absorbed, update Shield!
					$this->absorbDamage($target,$gamedata,$absorbedDamage, $fireOrder->id);
				}
				$returnValues['dmg'] = $damageToAbsorb;
				$returnValues['armor'] = min($damageToAbsorb, $returnValues['armor']);
			}
			
			return $returnValues;
		} //endof function doProtect

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

		if($gamedata->turn == 1){//Shields will spawn at max possible capacity, reduce this to give starting amount on Turn 1.

			$startRating = $this->baseRating;
			$currentRating = $this->getRemainingHealth();
			
			//Find and account for any Enhancements
			foreach ($ship->enhancementOptions as $enhancement) {
			    $enhID = $enhancement[0];
				$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
			        if ($enhID == 'IMPR_THSD'){
			        	$startRating += $enhCount;						        	
			        	if($ship->shipSizeClass == 3){
			        		$currentRating += $enhCount * 3;
						}else{
			        		$currentRating += $enhCount;							
						}				        	     	
					}
				}	
			}			
			
			$adjustment = $currentRating - $startRating;
					    	
			$this->setShields($ship, $gamedata->turn, $gamedata->phase, $adjustment);
		}

		
		//Now make any changes as a result of rearrangements by player.					
	    foreach ($this->individualNotes as $currNote) {
	  		if($currNote->turn == $gamedata->turn) {  				    	
	        $damageValue = $currNote->notevalue; //Positive if decreased, negative if increased.
			}
		}				
		//actual change(damage) entry
		if($damageValue != 0){
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageValue, 0, 0, -1, false, false, 'shieldChange', 'shieldChange');
			if($gamedata->phase == 4) $damageEntry->updated = true; //Don't duplicate a damage save in PreFiring phase.
			$this->damage[] = $damageEntry;	
		}	
        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
        $this->individualNotes = array();
	 		  
	}//endof onIndividualNotesLoaded

		
	public function stripForJson() {
	    $strippedSystem = parent::stripForJson();
		$strippedSystem->currentHealth = $this->currentHealth;
		$strippedSystem->outputDisplay = $this->outputDisplay;
		$strippedSystem->side = $this->side;
					
	    return $strippedSystem;
	} 
	
}//endof class ThirdspaceShield


class ThoughtShield extends Shield implements DefensiveSystem {
	    public $name = "ThoughtShield";
	    public $displayName = "Shield Projection";
	    public $primary = true;
		public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
		public $isTargetable = false; //cannot be targeted ever!
	    public $iconPath = "ThirdspaceShieldF.png"; //overridden anyway - to indicate proper direction
	    
		protected $possibleCriticals = array(); //no criticals possible
		
		//Shield Projections cannot be repaired at all!
		public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
		
		protected $doCountForCombatValue = false;//To ignore projection for combat value calculations
		
		public $changeThisTurn = 0;	//When shields moved around, change it tracked here to be made into Damage Entry.	
		public $currentHealth = 0; //Value for front-end when moving shield power around.
		public $side = '';//Required for prioritising a shield using Generator Presets.
		public $baseRating = 0; //Will be set when contructed.
		public $defenceMod = 0;	//To allow shield to be reinfored and act as an EM shield as well.	
	    
	    function __construct($armor, $startHealth, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $startHealth, $Rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$this->iconPath = 'ThirdspaceShield' . $side . '.png';
			parent::__construct($armor, $startHealth, 0, $rating, $startArc, $endArc);
			//rating not currently used.
			$this->side = $side;
			$this->baseRating = $rating;						
		}

	    public function setCritical($critical, $turn = 0){ //do nothing, shield projection should not receive any criticals
	    }	
		
		//ThoughtShield CAN be reinforced to act as a EM Shield as well, so we need to add some checks and not just return 0.		
	    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
	        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon)) return 0;
 
	        return $this->defenceMod;
	    }
		public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){ //no shield-like damage reduction
	        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon)) return 0;
       
	        return $this->defenceMod;
		}
	    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under shield
			if(!($shooter instanceof FighterFlight)) return false; //only fighters may fly under shield!
			if($weapon && $weapon->ballistic) return false; //fighter missiles may NOT fly under shield
		        $dis = mathlib::getDistanceOfShipInHex($target, $shooter);
		        if ( $dis == 0 ){ //If shooter are fighers and range is 0, they are under the shield
		            return true;
		        }
		        return false;
		}
	   		

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);  
			$this->data["Special"] = "Defensive system which absorbs damage from incoming shots within its arc.";
			$this->data["Special"] .= "<br>Can absorb up to its maximum capacity before allowing damage to ship.";		
			$this->data["Special"] .= "<br>Shield system's structure represents damage capacity, if it is reduced to zero system will cease to function.";
			$this->data["Special"] .= "<br>Can't be destroyed unless associated structure block is also destroyed.";
			$this->data["Special"] .= "<br>Cannot be flown under, and does not reduce the damage dealt or hit chance of enemy weapons.";
			$this->currentHealth = $this->getRemainingCapacity();//override on-icon display default
			$this->outputDisplay = $this->currentHealth;//override on-icon display default					
		}	


		public function getRemainingCapacity(){
			return $this->getRemainingHealth();
		}
		
		public function getUsedCapacity(){
			return $this->getTotalDamage();
		}

		public function applyContraction($ship, $gamedata, $value){
			//Check if Contraction has already been applied this turn
			foreach($this->damage as $damage){
				if($damage->turn != $gamedata->turn) continue;
				if($damage->damageclass == "Contraction") return; //Already applied
			}

			//If not, apply it
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Contraction", "Contraction");
			if($gamedata->phase == 4) $damageEntry->updated = true; //Don't duplicate a damage save in PreFiring phase.
			$this->damage[] = $damageEntry;
		}

		public function setShields($ship,$turn, $phase, $adjustment){//On Turn 1, reduce shields to their correct starting amount.
			//For Mind's Eye, need to adjust on Turn 1 if Contraction is used to change shields.
			$mindriderEngine = $ship->getSystemByName("MindriderEngine");			    
			if($mindriderEngine) $adjustment -= $mindriderEngine->contraction;		

			$damageEntry = new DamageEntry(-1, $ship->id, -1, $turn, $this->id, $adjustment, 0, 0, -1, false, false, "SetShield!", "ThoughtShield");
			if($phase == 4) $damageEntry->updated = true; //Don't duplicate a damage save in PreFiring phase.
			$this->damage[] = $damageEntry;
		}
		
		public function absorbDamage($ship,$gamedata,$value, $fireOrderid = -1){ //or dissipate, with negative value
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Regenerate!", "ThoughtShield");
			if($gamedata->phase == 4) $damageEntry->updated = true; //Don't duplicate a damage save in PreFiring phase.
			$this->damage[] = $damageEntry;
		}
		

		private function checkShieldDeduction($gamedata, $fireOrder){
			$deductShieldRating = true;
			//how to check it's this turn?  Check damage entries for this turn instead, with Shooter and Weapon ids?

			foreach($this->damage as $damage){
				if($damage->turn != $gamedata->turn) continue;//Only interested in this turn.
				if($damage->fireorderid == $fireOrder->id) {
					$deductShieldRating = false;	//Previous damage this turn has been found from this raking weapon.
					break;
				}
			}
					
			return 	$deductShieldRating;		
		}//endof checkShieldDeduction		
		
		//decision whether this system can protect from damage - value used only for choosing strongest shield to balance load.
		public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false, $inflictingShots = 1, $isUnderShield = false) {
			
			if($isUnderShield) return 0; //shield does not protect from internal damage

			$remainingCapacity = $this->getRemainingCapacity();
			$protectionValue = 0;
			if($remainingCapacity>0){
				$protectionValue = $remainingCapacity;
				
				//If there are multiple shots, we need to return the AVERAGE protection over the shots - accounting for the fact that the shield may be depleted by the first shot.
				if($inflictingShots > 1){
					$totalProtection = min($remainingCapacity, $expectedDmg * $inflictingShots);
					$protectionValue = $totalProtection / $inflictingShots;
				}
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

				$armour = $this->armour;				
				$AddShieldRating = true;//Do we want to deduct armour?
				if($weapon instanceof Raking) $AddShieldRating = $this->checkShieldDeduction($gamedata, $fireOrder);//Check armour assumption on Raking weapons.
				if($AddShieldRating) $damageToAbsorb += $this->defenceMod; //Usually 0, but if not (due to reinforcement) add Shield Rating to damage in 1st rake.

				//next, actual absorbtion				
				$absorbedDamage = min($remainingCapacity, $damageToAbsorb ); //no more than remaining capacity; no more than damage incoming
				$damageToAbsorb += -$absorbedDamage;//Reduce amount to absorb by Armour or reduce to 0 if less than Armour.
					
				if($absorbedDamage>0){ //Damage was absorbed, update Shield!
					$this->absorbDamage($target,$gamedata,$absorbedDamage, $fireOrder->id);
				}
				$returnValues['dmg'] = $damageToAbsorb;
				$returnValues['armor'] = min($damageToAbsorb, $returnValues['armor']);
			}
			
			return $returnValues;
		} //endof function doProtect


		//effects that happen in Critical phase (after criticals are rolled) - replenishment from active Generator 
		public function criticalPhaseEffects($ship, $gamedata){
			
			parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.
	
			if($this->isDestroyed()) return; //destroyed shield does not work...shouldn't happen.

				$baseRegen = $this->baseRating;
				$generator = $ship->getSystemByName("ThoughtShieldGenerator");
				if($generator){//Fighters don't have Generators, and can't offline anyway!	
          	 		if($generator->isOfflineOnTurn($gamedata->turn)) return; //Generator is offline, can't restore shields.
          	 	}
					
				if(!$ship instanceof FighterFlight){//Fighters don't have CnC!	
					$cnc = $ship->getSystemByName("CnC");
					$secondaryCnC = $ship->getSystemByName("SecondaryCnC");					
					if($secondaryCnC->isDestroyed()) $baseRegen = round($baseRegen/2);
					if($cnc->isDestroyed()) $baseRegen = 0;				
				}
					
		    	$currentRating = $this->getRemainingHealth();
		    	
		    	$toReplenish = 0;
				$toReplenish = $currentRating - $baseRegen; //Create a plus or minus figure to restore shield to start strength.
				
				if($generator){//Fighters don't have Generators!
					if($generator->isDestroyed()) $toReplenish = $currentRating; //Generator was destroyed, reduce shields to zero.
				}
																									
				if($toReplenish != 0){ //something changes!
					$this->absorbDamage($ship,$gamedata,$toReplenish);
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

		if($gamedata->turn == 1){//Shields will spawn at max possible capacity, reduce this to give starting amount on Turn 1.

			$startRating = $this->baseRating;
			$currentRating = $this->getRemainingHealth();

			//Find and account for any Enhancements
			foreach ($ship->enhancementOptions as $enhancement) {
			    $enhID = $enhancement[0];
				$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
			        if ($enhID == 'IMPR_TS'){
			        	$startRating += $enhCount;						        	
			        	$currentRating += $enhCount * 2;			        	     	
					}
				}	
			}
			
			//Adjust the adjustment
			$adjustment = $currentRating - $startRating;

			if(!$ship instanceof FighterFlight){//Fighters don't have Generators, and can't offline anyway!	
				$generator = $ship->getSystemByName("ThoughtShieldGenerator");				
		        if($generator->isOfflineOnTurn($gamedata->turn)) {
		            $adjustment = $currentRating;//In the bizarre situation where player deactivates Generator on Turn 1...	
				}			
			}
			//Actually adjust shields		    	
			$this->setShields($ship, $gamedata->turn, $gamedata->phase, $adjustment);
		}
		
		//Now make any manual readjustments the player has made to shield strengths					
	    foreach ($this->individualNotes as $currNote) {
	  		if($currNote->turn == $gamedata->turn) {  				    	
	        $damageValue = $currNote->notevalue; //Positive if decreased, negative if increased.
			}
		}	
				
		//actual change(damage) entry
		if($damageValue != 0){
		$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $damageValue, 0, 0, -1, false, false, 'shieldChange', 'shieldChange');
		if($gamedata->phase == 4) $damageEntry->updated = true; //Don't duplicate a damage save in PreFiring phase.
		$this->damage[] = $damageEntry;	
		}	
        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
        $this->individualNotes = array();
	 		  
	}//endof onIndividualNotesLoaded

		
	public function stripForJson() {
	    $strippedSystem = parent::stripForJson();
		$strippedSystem->currentHealth = $this->currentHealth;
		$strippedSystem->outputDisplay = $this->outputDisplay;
		$strippedSystem->side = $this->side;
		$strippedSystem->defenceMod = $this->defenceMod;
		$strippedSystem->baseRating = $this->baseRating;							
	    return $strippedSystem;
	} 

	
}//endof class ThoughtShield


?>
