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

        public $priority = 9;
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


        class GaussCannon extends MatterCannon
    {
        public $name = "gaussCannon";
        public $displayName = "Gauss Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 20;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.20;
        public $trailLength = 8;
        
        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(-3, 1, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+10;   }
        public function setMinDamage(){     $this->minDamage = 11 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;      }
    }


        class HeavyGaussCannon extends GaussCannon{

        public $name = "heavyGaussCannon";
        public $displayName = "Heavy Gauss Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 16;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.25;
        public $trailLength = 10;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.66;
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 3)+10;   }
        public function setMinDamage(){     $this->minDamage = 13 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 40 - $this->dp;      }

        }


        class RapidGatling extends Matter
    {
        public $name = "rapidGatling";
        public $displayName = "Rapid Gatling Railgun";
        public $animation = "trail";
        public $trailColor = array(225, 255, 150);
        public $animationColor = array(225, 225, 150);
        public $projectilespeed = 2;
        public $animationWidth = 2;
        public $trailLength = 14;
        public $animationExplosionScale = 0.15;
        public $guns = 2;
        public $intercept = 1;
        public $loadingtime = 1;
        public $ballisticIntercept = true;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, 2, 0); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 12 - $this->dp;      }
    }


    class PairedGatlingGun extends LinkedWeapon{

        // take a look
        public $name = "pairedGatlingGun";
        public $displayName = "Paired Gatling Guns";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;
        
        public $loadingtime = 1;

        public $intercept = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;


        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){


            $this->data["Weapon type"] = "Matter";
            $this->data["Damage type"] = "Standard";
            parent::setSystemDataWindow($turn);


            $this->data["<font color='red'>Ammunition</font color>"] = $this->ammunition;
        }

        protected function getSystemArmour($system, $gamedata, $fireOrder)
        {
            return 0;
        }

        protected function getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata)
        {
            return null;
        }


        public function getDamage($fireOrder){
            $dmg = Dice::d(6, 2);
            return $dmg;
       }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }


       public function fire($gamedata, $fireOrder){
            parent::fire($gamedata, $fireOrder);

            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, TacGamedata::$currentGameID, $this->firingMode, $this->ammunition);
        }
    
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }

    }

?>
