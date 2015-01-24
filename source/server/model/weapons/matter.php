<?php

    class Matter extends Weapon
    {
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        protected function getSystemArmour($system, $gamedata, $fireOrder)
        {
            return 0;
        }

        protected function getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata)
        {
            return null;
        }

        public function setSystemDataWindow($turn)
        {
            $this->data["Weapon type"] = "Matter";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public $priority = 8;
    }

    class MatterCannon extends Matter
    {
        public $name = "matterCannon";
        public $displayName = "Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-2, 3, 3); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 22 - $this->dp;      }
    }
    
    class HeavyRailGun extends Matter
    {
        public $name = "heavyRailGun";
        public $displayName = "Heavy Railgun";
        public $animation = "trail";
        public $iconPath = "matterCannon.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 20;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.30;

        public $loadingtime = 4;

        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 5)+7;   }
        public function setMinDamage(){     $this->minDamage = 12 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 57 - $this->dp;      }    
    }
    
    class RailGun extends Matter
    {
        public $name = "railGun";
        public $displayName = "Railgun";
        public $animation = "trail";
        public $iconPath = "matterCannon.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.25;
        
        public $loadingtime = 3;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){  return Dice::d(10, 3)+3;   }
        public function setMinDamage(){     $this->minDamage = 6 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 33 - $this->dp;      }    
    }
    
    class LightRailGun extends Matter
    {
        public $name = "lightRailGun";
        public $displayName = "Light Railgun";
        public $animation = "trail";
        public $iconPath = "matterCannon.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 1.0;
        public $fireControl = array(3, 2, 0); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
    	public function getDamage($fireOrder){        return Dice::d(10, 1)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }    
    }
    
    class MassDriver extends Matter
    {
        public $name = "massDriver";
        public $displayName = "Mass Driver";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 10;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.90;
        public $targetImmobile = true;
        
        public $loadingtime = 4;
		
        public $rangePenalty = 0.17;
        public $fireControl = array(null, null, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 8)+ 60;   }
        public function setMinDamage(){     $this->minDamage = 68 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 140 - $this->dp;      }
    }
?>
