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
            $this->data["DEFENSIVE BONUS:"] = "-15 to hit on arc";
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
        
        /*already declared in MkI
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
        */
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["DEFENSIVE BONUS:"] = "-20 to hit on arc";
        }
    }
    

    class InterceptorPrototype extends InterceptorMkI{
        public $name = "interceptorPrototype";
        public $displayName = "Interceptor Prototype";
        public $priority = 4;
        
        public $output = 2;
        public $intercept = 2;
        
        /*already declared in MkI
        public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
            if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
                return 0;

            $output = $this->output;
            $output -= $this->outputMod;
            return $output;
        }

        public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
            return 0;
        }*/
        
        public function setSystemDataWindow($turn){
            $this->data["DEFENSIVE BONUS:"] = "-10 to hit on arc";
            parent::setSystemDataWindow($turn);
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


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
    } //endof class SentinelPointDefense



    class ParticleImpeder extends Weapon implements DefensiveSystem{
        /*Abbai defensive system*/
        /*changed so it can be boosted for power, instead of EW; boost part affects fighters (only!) hit chance*/

        public $trailColor = array(30, 170, 255);
        
        public $name = "ParticleImpeder";
        public $displayName = "Particle Impeder";
        public $animation = "trail";
        public $iconPath = "particleImpeder.png";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $priority = 1; //will never fire except defensively, purely a defensive system

        public $animationWidth = 1;
            
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
    
        public function getDefensiveType()
        {
            return "Interceptor";
        }
        
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->tohitPenalty = $this->getOutput();
            $this->damagePenalty = 0;
            $this->intercept = $this->getInterceptRating($turn);
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
        
        public function setSystemDataWindow($turn){
            $this->data["Special"] = "Can intercept uninterceptable weapons.<br>";
            $this->data["Special"] .= "Can be boosted for increased intercept rating (up to +" . $this->maxboost . ".<br>";
            $this->data["Special"] .= "Additionally, boost itself reduces fighter hit chance.";
            parent::setSystemDataWindow($turn);
            
            $this->intercept = $this->getInterceptRating($turn);
        }
        
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $this->output);
        }
        
        private function checkIsFighterUnderShield($target, $shooter){ //no flying under Impeder
            return false;
        }
        
        private funtion getInterceptRating($turn){
            $ir = 3 + $this->getBoostLevel($turn);
            return $ir;
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
        
        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }
    }




    class EMWaveDisruptor extends Weapon{

        public $trailColor = array(30, 170, 255);
        
        public $name = "eMWaveDisruptor";
        public $displayName = "EM-Wave Disruptor";
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $animationWidth = 1;
        public $animationWidth2 = 0;
        public $boostable = true;
        public $boostEfficiency = 4;            
        public $intercept = 3;
        public $loadingtime = 1;
        public $charges = 2;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, null, null); // fighters, <mediums, <capitals 

    public $damageType = "Standard"; 
    public $weaponClass = "Electromagnetic";
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
            //$this->data["Weapon type"] = "Electromagnetic";
            //$this->data["Damage type"] = "Intercept / Dropout";
            $this->data["Special"] = "Intercept / Dropout";
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

        public function getBonusCharges($turn){
            return $this->getBoostLevel($turn);
        }
    }


?>
