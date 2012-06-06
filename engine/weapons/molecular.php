<?php
// Added by Jasper

    class Molecular extends Weapon{


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Molecular";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }



    }

    class FusionCannon extends Molecular{

        // needs to be looked into
        public $trailColor = array(110, 225, 110);

        public $name = "fusionCannon";
        public $displayName = "Fusion Cannon";
        // needs to be looked into
        public $animation = "beam";
        // needs to be looked into
        public $animationColor =  array(175, 225, 175);
        // needs to be looked into
        public $animationExplosionScale = 0.20;
        // needs to be looked into
        public $projectilespeed = 12;
        // needs to be looked into
        public $animationWidth = 7;
        // needs to be looked into
        public $trailLength = 20;

        public $intercept = 2;


        public $loadingtime = 1;


        public $rangePenalty = 1;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage(){        return Dice::d(10)+9;   }
        public function setMinDamage(){     $this->minDamage = 10 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 19 - $this->dp;      }

    }

    class LightFusionCannon extends Molecular{

        // take a look
        public $trailColor = array(30, 170, 255);

        public $name = "lightFusionCannon";
        public $displayName = "Light Fusion Cannon";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;

        public $intercept = 0;
        public $loadingtime = 1;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;


        function __construct($startArc, $endArc, $damagebonus){
			$this->damagebonus = $damagebonus;
            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function getDamage(){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

    }

    class LightFusionCannon3 extends LightFusionCannon
    {
        public $shots = 3;
        public $defaultShots = 3;

        function __construct($startArc, $endArc, $damagebonus){
			$this->damagebonus = $damagebonus;
            parent::__construct(0, 1, 0, $startArc, $endArc);

        }
    }

    class LightFusionCannon2 extends LightFusionCannon
    {
        public $shots = 2;
        public $defaultShots = 2;

        function __construct($startArc, $endArc, $damagebonus){
			$this->damagebonus = $damagebonus;
            parent::__construct(0, 1, 0, $startArc, $endArc);

        }
    }

    // mhhh... extended from Raking as that involves less code duplication
    class MolecularDisruptor extends Raking
    {
        public $trailColor = array(30, 170, 255);

        public $name = "molecularDisruptor";
        public $displayName = "Molecular Disruptor";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;

        public $intercept = 0;
        public $loadingtime = 4;

        public $firingModes = array(
            1 => "Standard",
            2 => "Piercing"
            );
        public $piercing = true;

        public $rangePenalty = 1;
        public $fireControl = array(-4, 2, 4); // fighters, <mediums, <capitals
        private $damagebonus = 30;


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){

            parent::damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null);

            $locTarget = $target->getHitSection($shooter, $fireOrder, $this);
            $structTarget = $target->getStructureSystem($locTarget);

            $crit = new ArmorReduced(-1, $target->id, $structTarget->id, "ArmorReduced", $gamedata->turn);
            $crit->updated = true;
            $crit->inEffect = false;
            $structTarget->setCriticals($crit, $gamedata->turn);

        }

        public function getDamage(){        return Dice::d(10, 2)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 2+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 20+$this->damagebonus - $this->dp;      }
    }
?>
