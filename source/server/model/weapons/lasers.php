<?php

    class Raking extends Weapon{
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        public $raking = 10;
        private $damages = array();
        
        public function damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){
            
            $rake = $this->raking;
            
            $totalDamage = $damage;
            
            if ($this->piercing && $fireOrder->firingMode == 2)
                $rake = $totalDamage;
            
            $this->damages = array();
            while(true){
                                
                if ($totalDamage <= 0)
                    break;
            
                if ($target->isDestroyed())
                    return;
            
                $system = $target->getHitSystem($pos, $shooter, $fireOrder, $this, $location);
                
                if ($system == null)
                    return;
                    
                if ($totalDamage - $this->raking >= 0){
                    $this->doDamage($target, $shooter, $system, $rake, $fireOrder, $pos, $gamedata);
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
        public $overloadable = true;
        public $extraoverloadshots = 2;
        
        public $damageType = "raking";
        public $raking = 10;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 4)+20;   }
        public function setMinDamage(){     $this->minDamage = 24 - $this->dp;      }
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
        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+12;   }
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
        public $overloadable = true;
        public $extraoverloadshots = 2;
        
        public $damageType = "raking";
        public $raking = 10;
        
        public $firingModes = array(
            1 => "Standard",
            2 => "Piercing"
            );
        public $piercing = true;
        
        public $rangePenalty = 0.25;
        public $fireControl = array(-3, 3, 4); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 4)+12;   }
        public function setMinDamage(){     $this->minDamage = 24 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 42 - $this->dp;      }
        
        
        
    }

// Jasper
class NeutronLaser extends Laser{

        public $name = "neutronLaser";
        public $displayName = "Neutron Laser";
        public $animation = "laser";
        public $animationColor = array(175, 225, 175);
        public $animationWidth = 4;

        public $extraoverloadshots = 2;
        public $loadingtime = 3;
        public $overloadable = true;

        public $damageType = "raking";
        public $raking = 10;

        public $firingModes = array(
            1 => "Standard",
            2 => "Piercing"
            );
        public $piercing = true;

        public $rangePenalty = 0.25;
        public $fireControl = array(1, 4, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 4)+15; }
        public function setMinDamage(){ $this->minDamage = 19 - $this->dp; }
        public function setMaxDamage(){ $this->maxDamage = 55 - $this->dp; }
    }

    class ImprovedNeutronLaser extends Laser{

        public $name = "improvedNeutronLaser";
        public $displayName = "Improved Neutron Laser";
        public $animation = "laser";
        public $animationColor = array(175, 225, 175);
        public $animationWidth = 5;

        public $extraoverloadshots = 3;
        public $loadingtime = 3;
        public $overloadable = true;

        public $damageType = "raking";
        public $raking = 10;

        public $firingModes = array(
            1 => "Standard",
            2 => "Piercing"
            );
        public $piercing = true;

        public $rangePenalty = 0.25;
        public $fireControl = array(1, 4, 5); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 4)+18; }
        public function setMinDamage(){ $this->minDamage = 19 - $this->dp; }
        public function setMaxDamage(){ $this->maxDamage = 55 - $this->dp; }
    }
?>
