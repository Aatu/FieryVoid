<?php

    class Raking extends Weapon{
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        public $raking = 10;
        private $damages = array();
        
        public function damage( $target, $shooter, $fireOrder, $pos, $gamedata){
            
            
            $totalDamage = $this->getFinalDamage($shooter, $target, $pos, $gamedata);
            $this->damages = array();
            while(true){
                                
                if ($totalDamage <= 0)
                    break;
            
                if ($target->isDestroyed())
                    return;
            
                $system = $target->getHitSystem($pos, $shooter, $fireOrder->turn, $this);
                
                if ($system == null)
                    return;
                    
                if ($totalDamage - $this->raking >= 0){
                    $this->doDamage($target, $shooter, $system, $this->raking, $fireOrder, $pos, $gamedata);
                    $totalDamage -= $this->raking;
                }else if ($totalDamage > 0){
                    $this->doDamage($target, $shooter, $system, $totalDamage, $fireOrder, $pos, $gamedata);
                    break;
                }else{
                    break;
                }
                    
            }
            
       
            
            
        
        }
        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata){

            $damages = array();
            $armour = $this->getSystemArmour($system, $gamedata, $fireOrder);
            
            foreach ($this->damages as $previous){
                if ($previous->systemid == $system->id)
                    $armour -= $previous->damage;
            }
            
            $systemHealth = $system->getRemainingHealth();
            $modifiedDamage = $damage;
            
            if ($armour < 0)
                $armour = 0;
            
            $destroyed = false;
            if ($damage-$armour >= $systemHealth){
                $destroyed = true;
                $modifiedDamage = $systemHealth + $armour;
            }
            
            $damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $system->id, $modifiedDamage, $armour, 0, $fireOrder->id, $destroyed, "");
            $damageEntry->updated = true;
            $system->damage[] = $damageEntry;
            $this->damages[] = $damageEntry;
            $this->onDamagedSystem($target, $system, $modifiedDamage, $armour, $gamedata);
            if ($damage-$armour > $systemHealth){
            
                $damage = $damage-$modifiedDamage;
                 
                $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata);
                if ($overkillSystem != null)
                    $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata);
            }
        
            
        
        }
        
        
    
    }
    
    class Laser extends Raking{
    
        public $uninterceptable = true;
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Laser";
            $this->data["Damage type"] = "Raking";
            
            parent::setSystemDataWindow($turn);
        }
    
        
    
    }

    class HeavyLaser extends Laser{
        
        public $name = "heavyLaser";
        public $displayName = "Heavy laser";
        public $animation = "laser";
        public $animationColor = array(255, 11, 11);
        public $animationWidth = 4;
        
        public $loadingtime = 4;
        public $overloadturns = 8;
        
        public $damageType = "raking";
        public $raking = 10;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 3, 4); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage(){        return Dice::d(10, 2)+30;   }
        public function setMinDamage(){     $this->minDamage = 32 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 50 - $this->dp;      }
        
        
        
    }
    
    class MediumLaser extends Laser{
        
        public $name = "mediumLaser";
        public $displayName = "Medium laser";
        public $animation = "laser";
        public $animationColor = array(255, 11, 11);
        public $animationExplosionScale = 0.18;
        public $animationWidth = 3;
        
        public $loadingtime = 3;
        
        public $damageType = "raking";
        public $raking = 10;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage(){        return Dice::d(10, 2)+17;   }
        public function setMinDamage(){     $this->minDamage = 19 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 37 - $this->dp;      }
        
        
        
    }
    
    class BattleLaser extends Laser{
        
        public $name = "battleLaser";
        public $displayName = "Battle laser";
        public $animation = "laser";
        public $animationColor = array(255, 11, 115);
        public $animationWidth = 4;
        
        public $loadingtime = 3;
        public $overloadturns = 6;
        
        public $damageType = "raking";
        public $raking = 10;
        
        
        public $rangePenalty = 0.25;
        public $fireControl = array(-3, 3, 4); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage(){        return Dice::d(10, 2)+22;   }
        public function setMinDamage(){     $this->minDamage = 24 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 42 - $this->dp;      }
        
        
        
    }


?>
