<?php


    class Particle extends Weapon{

    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
        }
    
        
    
    }
    
    class StdParticleBeam extends Particle{

        public $trailColor = array(30, 170, 255);
        
        public $name = "stdParticleBeam";
        public $displayName = "Standard Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 10;
        
        public $intercept = 2;
   
        
        public $loadingtime = 1;
      
        
        public $rangePenalty = 1;
        public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 16 - $this->dp;      }

    }

    class TwinArray extends Particle{

        public $trailColor = array(30, 170, 255);
        
        public $name = "twinArray";
        public $displayName = "Twin array";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;
        
        public $intercept = 2;
   
        
        public $loadingtime = 1;
        public $guns = 2;
        
        public $rangePenalty = 2;
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 14 - $this->dp;      }

    }
    
    class HeavyArray extends Particle{

        public $trailColor = array(30, 170, 255);
        
        public $name = "heavyArray";
        public $displayName = "Heavy array";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 20;
        public $animationWidth = 4;
        public $trailLength = 15;
        
        public $intercept = 2;
        
        public $loadingtime = 1;
        public $guns = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 26 - $this->dp;      }

    }
    
    class PairedParticleGun extends LinkedWeapon{

        public $trailColor = array(30, 170, 255);
        
        public $name = "pairedParticleGun";
        public $displayName = "Paired Particle guns";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        
        public $intercept = 2;
        
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
        private $damagebonus = 0;


        function __construct($startArc, $endArc, $damagebonus){
			$this->damagebonus = $damagebonus;
            parent::__construct(0, 1, 0, $startArc, $endArc);
           
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

    }

?>
