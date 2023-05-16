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
class ThirdspaceShieldProjection extends Shield implements DefensiveSystem { //defensive values of zero, but still formally there to display arcs!
	    public $name = "ThirdspaceShieldProjection";
	    public $displayName = "Shield Projection";
	    public $primary = true;
		public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
		public $isTargetable = false; //cannot be targeted ever!
	    public $iconPath = "TrekShieldProjection.png"; //overridden anyway - to indicate proper direction
	    
		protected $possibleCriticals = array(); //no criticals possible
		
		//Shield Projections cannot be repaired at all!
		public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

		private $projectorList = array();
		
	    
	    function __construct($armor, $maxhealth, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$this->iconPath = 'TrekShieldProjection' . $side . '.png';
			parent::__construct($armor, $maxhealth, 0, $rating, $startArc, $endArc);
			$this->output=$rating;//output is displayed anyway, make it show something useful... in this case - number of points absorbed per hit
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
			$absorb = $this->output - $this->armour;
			$this->data["Special"] = "Defensive system which absorbs damage from incoming shots wihtin its arc before they damage ship hull.";
			$this->data["Special"] .= "<br>Can absorb up to its maximum capacity before allowing damage to ship.";		
			$this->data["Special"] .= "<br>Shield system's structure represents damage capacity, if it is reduced to zero system will cease to function.";
			$this->data["Special"] .= "<br>Can't be destroyed unless associated structure block is also destroyed.";
			$this->data["Special"] .= "<br>Cannot be flown under, and does not reduce the damage dealt or hit chance of enemy weapons.";
			$this->data["Special"] .= "<br>Has an Armor value of 2.";				
			
			$this->outputDisplay = $this->getRemainingCapacity();//override on-icon display default
		}	
		
		public function getRemainingCapacity(){
			return $this->getRemainingHealth();
		}
		
		public function getUsedCapacity(){
			return $this->getTotalDamage();
		}
		
		public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Regenerate!", "ThirdspaceShieldProjection");
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
				$absorbedFreely = min($this->armour, $damageToAbsorb);
	//			$damageToAbsorb += -$absorbedFreely;
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
		    
		function addProjector($projector){
			if($projector) $this->projectorList[] = $projector;
		}
		
		//effects that happen in Critical phase (after criticals are rolled) - replenishment from active projectors 
		public function criticalPhaseEffects($ship, $gamedata){
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
			/*after all - shield will NOT fall!
			if($activeProjectors <= 0){ //no active projectors - shield is falling!
				$toReplenish = -$this->getRemainingCapacity();	
				*/
			if($activeProjectors > 0){ //active projectors present - reinforce shield!
				$toReplenish = min($projectorOutput,$this->getUsedCapacity());		
			}
			
			if($toReplenish != 0){ //something changes!
				$this->absorbDamage($ship,$gamedata,-$toReplenish);
			}
		} //endof function criticalPhaseEffects
		
		public function stripForJson() {
	        $strippedSystem = parent::stripForJson();

			$strippedSystem->outputDisplay = $this->outputDisplay;
			
	        return $strippedSystem;
		}
}//endof class ThirdspaceProjection


/* ThirdspaceShieldProjector: reinforces shield projection (and prevents it from falling)
 actual reinforcing (and falling) is done from Projection's own end, Projector just is (needs to be plugged into appropriate projection at design stage
*/
class ThirdspaceShieldProjector  extends Shield implements DefensiveSystem { //defensive values of zero, but still formally there to display arcs!
	    public $name = "ThirdspaceShieldProjector";
	    public $displayName = "Shield Projector";
		public $isPrimaryTargetable = false; //projector can be targeted even on PRIMARY, like a weapon!
	    public $iconPath = "TrekShieldProjectorF.png"; //overridden anyway - to indicate proper direction
	    public $boostable = true; //$this->boostEfficiency and $this->maxBoostLevel in __construct()  
		public $boostEfficiency = 5;
	    public $baseOutput = 0; //base output, before boost
	    
		
	    protected $possibleCriticals = array(
	            19=>"OutputReduced1",
	            28=>"OutputReduced2" );
		
		public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	    
	    function __construct($armor, $maxhealth, $power, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$this->iconPath = 'TrekShieldProjector' . $side . '.png';
			parent::__construct($armor, $maxhealth, $power, $rating, $startArc, $endArc);
			$this->baseOutput = $rating;
			$this->maxBoostLevel = $rating; //maximum double effect	
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
			$this->data["Special"] = "Regenerates 5 health for the associated Shield per point of Projector rating at the end of each turn .";
		$this->data["Special"] .= "<br> Output can be boosted up to " . $this->maxBoostLevel . " times at " . $this->boostEfficiency . " power per extra point of self repair.";
		}	
		
	    public function getOutputOnTurn($turn){
	        $output = ($this->getOutput() + $this->getBoostLevel($turn))*5;
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


?>
