<?php
    
    class InterceptorMkI extends Weapon implements DefensiveSystem{

        public $trailColor = array(30, 170, 255);
        
        public $name = "interceptorMkI";
        public $displayName = "Interceptor I";
        public $animation = "trail";
        public $iconPath = "interceptor.png";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
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
                
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Energy Web: -20 to hit on arc with active Interceptor.";
        }
    }
    

    class InterceptorPrototype extends InterceptorMkI{
        public $name = "interceptorPrototype";
        public $displayName = "Interceptor Prototype";
        public $priority = 4;
        
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
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $priority = 4;

        public $animationWidth = 1;
        public $animationWidth2 = 0;
            
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
        public $animationExplosionScale = 0.15;

        public $animationWidth = 1;
        public $animationWidth2 = 0;
            
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
        public $animationExplosionScale = 0.15;
        public $animationWidth = 2;
        public $animationWidth2 = 0;
	    
	    
        public $priority = 1; //will never fire anyway except defensively, purely a defensive system
            
        public $intercept = 3;
             
        public $loadingtime = 1;
  
        public $rangePenalty = 2; //irrelevant
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals 
        
        public $output = 0;
	
        
        public $boostable = true; //can be boosted for additional effect
	    public $boostEfficiency = 3; //cost to boost by 1
        public $maxBoostLevel = 4; //maximum boost allowed
        public $canInterceptUninterceptable = true; //can intercept weapons that are normally uninterceptable
        
        public $tohitPenalty = 0;
        public $damagePenalty = 0;
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";
     	public $possibleCriticals = array( //different than usual B5Wars weapon
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
            
            if (!($shooter instanceof FighterFlight)) return 0;//affects fighters only!
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
            $this->data["Special"] = "Can intercept uninterceptable weapons.<br>";
            $this->data["Special"] .= "Can be boosted for increased intercept rating (up to +" . $this->maxBoostLevel . ").<br>";
            $this->data["Special"] .= "Additionally, boost itself reduces fighter hit chance.";
            parent::setSystemDataWindow($turn);
            
            $this->intercept = $this->getInterceptRating($turn);
        }
          
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    if ($maxhealth == 0) $maxhealth = 6;
	    if ($powerReq == 0) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $this->output);
        }
   
        private function checkIsFighterUnderShield($target, $shooter){ //no flying under Impeder
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
	    
	    /*
 	public function stripForJson() {
        	$strippedSystem = parent::stripForJson();	
		$strippedSystem->guns = $this->guns;
	}*/

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
	
	
 	public $possibleCriticals = array( //irrelevant for fighter system
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
	
    private function checkIsFighterUnderShield($target, $shooter){ //no flying under fighter shield
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
	if (!array_key_exists ( "Special" , $this->data )) $this->data["Special"] = "";
	$this->data["Special"] .= "<br>Cannot fly under fighter shield (too small)."; 
    }
	
} //endof class  FtrShield




?>
