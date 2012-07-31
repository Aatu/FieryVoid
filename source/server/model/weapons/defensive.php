<?php
    
    class InterceptorMkI extends Weapon implements DefensiveSystem{

        public $trailColor = array(30, 170, 255);
        
        public $name = "interceptorMkI";
        public $displayName = "Interceptor MK I";
        public $animation = "trail";
        public $iconPath = "interceptor.png";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;

        public $animationWidth = 1;
            
        public $intercept = 3;
             
        public $loadingtime = 1;
  
        public $rangePenalty = 2;
        public $fireControl = array(6, -20, -20); // fighters, <mediums, <capitals 
        
        
        public $tohitPenalty = 0;
        public $damagePenalty = 0;
    
        public function getDefensiveType()
        {
            return "Interceptor";
        }
        
        public function onConstructed($ship, $turn, $phase){
            $this->tohitPenalty = $this->getOutput();
            $this->damagePenalty = $this->getOutput();
     
        }
        
        public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn){
            return 3;
        }

        public function getDefensiveDamageMod($target, $shooter, $pos, $turn){
            return 0;
        }
        
        public function setSystemDataWindow($turn){
            $this->data["DEFENSIVE BONUS:"] = "-15 to hit on arc";
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }
        

    }
    
    class GuardianArray extends Weapon{

        public $trailColor = array(30, 170, 255);
        
        public $name = "guardianArray";
        public $displayName = "Guardian Array";
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;

        public $animationWidth = 1;
            
        public $intercept = 3;
             
        public $freeintercept = true;
        public $loadingtime = 1;
  
        
        public $rangePenalty = 3;
        public $fireControl = array(8, -20, -20); // fighters, <mediums, <capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+8;   }
        public function setMinDamage(){     $this->minDamage = 9 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 18 - $this->dp;      }
        

    }
?>
