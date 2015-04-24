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

        public $name = "fusionCannon";
        public $displayName = "Fusion Cannon";
        public $animation = "beam";
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 12;
        public $animationWidth = 7;
        public $trailLength = 20;

        public $intercept = 2;
        public $priority = 8;


        public $loadingtime = 1;


        public $rangePenalty = 1;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+9;   }
        public function setMinDamage(){     $this->minDamage = 10 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 19 - $this->dp;      }
        
    }


    class HeavyFusionCannon extends FusionCannon{

   //     public $name = "hvyFusionCannon";
        
        public $name = "heavyFusionCannon";
        public $displayName = "Heavy Fusion Cannon";
        public $animation = "beam";

        public $animationExplosionScale = 0.30;
        public $projectilespeed = 18;
        public $animationWidth = 9;
        public $trailLength = 25;

        public $loadingtime = 2;
        public $intercept = 1;

        public $rangePenalty = 0.5;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        public function getDamage($fireOrder){        return Dice::d(10, 2)+14;   }
        public function setMinDamage(){     $this->minDamage = 16 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 34 - $this->dp;      }
    }

    class LightFusionCannon extends LinkedWeapon{

        // take a look
        public $trailColor = array(30, 170, 255);

        public $name = "lightfusionCannon";
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


        function __construct($startArc, $endArc, $damagebonus, $shots){
			$this->damagebonus = $damagebonus;
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            $this->iconPath = "lightfusionCannon$shots.png";
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Molecular";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

    }

    // mhhh... extended from Raking as that involves less code duplication
    class MolecularDisruptor extends Raking
    {
        public $trailColor = array(30, 170, 255);

        public $name = "molecularDisruptor";
        public $displayName = "Molecular Disruptor";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 25;
        public $priority = 4;

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

            if ( $target instanceof FighterFlight || $target instanceof SuperHeavyFighter)
            {
                return;
            }

            $structTarget = null;

            if ( $target instanceof MediumShip ){
                $structTarget = $target->getStructureSystem(0);
            }
            else{
                $locTarget = $target->getHitSection($pos, $shooter, $fireOrder->turn, $this);
                $structTarget = $target->getStructureSystem($locTarget);
            }

            $crit = new ArmorReduced(-1, $target->id, $structTarget->id, "ArmorReduced", $gamedata->turn);
            $crit->updated = true;
            $crit->inEffect = false;

            if ( $structTarget != null &&  $structTarget->armour > 0){
                $structTarget->criticals[] = $crit;
            }
            //$structTarget->setCriticals($crit, $gamedata->turn);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 2+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 20+$this->damagebonus - $this->dp;      }
    }


    class DestabilizerBeam extends Molecular{

        public $trailColor = array(30, 170, 255);

        public $name = "destabilizerBeam";
        public $displayName = "Destabilizer Beam";
        public $animation = "laser";
        public $animationColor = array(100, 100, 255);
        public $animationWidth = 4.5;
        public $animationWidth2 = 0.3;
        public $priority = 4;

        public $animationExplosionScale = 0.35;

        public $intercept = 0;
        public $loadingtime = 4;

        public $firingModes = array(
            1 => "Piercing"
            );
        public $piercing = true;

        public $rangePenalty = 0.33;
        public $fireControl = array(-5, 1, 6); // fighters, <mediums, <capitals
        private $damagebonus = 30;


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 6+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 60+$this->damagebonus - $this->dp;      }
    }


    class MolecularFlayer extends Molecular{

        public $name = "molecularFlayer";
        public $displayName = "Molecular Flayer";
        public $animation = "trail";
        public $animationColor = array(0, 200, 200);
        public $trailColor = array(0, 200, 200);
        public $animationExplosionScale = 0.50;
        public $projectilespeed = 15;
        public $animationWidth = 6;
        public $trailLength = 15;
        public $priority = 1;

        public $intercept = 0;
        public $loadingtime = 1;

        public $firingModes = array(
            1 => "Special"
            );

        public $rangePenalty = 0.33;
        public $fireControl = array(null, 0, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }


        public function damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){

            parent::damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null);

            $structTarget = null;
            $targets = null;

            if ($target instanceof MediumShip){

                foreach ($target->systems as $system){

                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = false;

                    if($system->armor > 0) {
                        $system->criticals[] = $crit;
                    }
                }
            }
            else{
                $locTarget = $target->getHitSection($pos, $shooter, $fireOrder->turn, $this);

                foreach ($target->systems as $system){

                    if ($system->location === $locTarget){

                        $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = false;

                        if ( $system->armour > 0) {
                            $system->criticals[] = $crit;
                        }
                    }
                }
            }
        }
    }


    class FusionAgitator extends Raking{

        public $trailColor = array(30, 170, 255);

        public $name = "fusionAgitator";
        public $displayName = "Fusion Agitator";
        public $animation = "laser";
        public $animationColor = array(0, 200, 200);
        public $animationWidth = 2;
        public $animationWidth2 = 0.3;

        public $animationExplosionScale = 0.15;

        public $intercept = 0;
        public $loadingtime = 1;
        public $raking = 6;
        public $addedDice;
        public $priority = 4;

        public $boostable = true;
        public $boostEfficiency = 4;
        public $maxBoostLevel = 4;

        public $firingModes = array(
            1 => "Raking"
            );

        public $rangePenalty = 0.33;
        public $fireControl = array(null, 4, 4); // fighters, <mediums, <capitals
        private $damagebonus = 10;


        public function setSystemDataWindow($turn){

            $boost = $this->getExtraDicebyBoostlevel($turn);

            $this->data["Weapon type"] = "Molecular";
            $this->data["Damage type"] = "Raking (6)";
            $this->data["Boostlevel"] = $boost;

            parent::setSystemDataWindow($turn);
        }


        private function getExtraDicebyBoostlevel($turn){

            $add = 0;
            
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 1;
                    break;
                case 2:
                    $add = 2;
                    break;
                case 3:
                    $add = 3;
                    break;
                case 4:
                    $add = 4;
                    break;
                default:
                    break;
            }            
            return $add;
        }


         private function getBoostLevel($turn){

            $boostLevel = 0;

            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }

        protected function getSystemArmour($system, $gamedata, $fireOrder){

            $armor = $system->armour;
            $newArmor = $armor - 1;

            if ($newArmor > 0){
                return $newArmor;
            }
            else return 0;
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){

            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);

            $dmg = Dice::d(10, (5 + $add))+$this->damagebonus;

           // debug::log("addedDice: ".$add);
          //  debug::log("damage rolled: ".$dmg);

            return $dmg;
        }


        public function setMinDamage(){     $this->minDamage = 5 + ($this->addedDice * 1)+ $this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 50 + ($this->addedDice * 10) + $this->damagebonus - $this->dp;      }
    }

        class LightMolecularDisruptor extends Weapon{
        public $name = "molecularDisruptor";
        public $displayName = "Light Molecular Distruptor";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 10;
        public $animationWidth = 5;
        public $trailLength = 12;
        
        public $loadingtime = 3;
        public $damageType = "raking";
        public $raking = 10;
        public $exclusive = true;
        
        public $rangePenalty = 1;
        public $fireControl = array(-4, 0, 3); // fighters, <mediums, <capitals 
 
        function __construct($startArc, $endArc, $damagebonus){
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Molecular";
            $this->data["Damage type"] = "Raking";
            
            parent::setSystemDataWindow($turn);
        }

        public function damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){
            Debug::log("in damage");

            parent::damage( $target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null);

            if(LightMolecularDisrupterHandler::doArmorReduction($target, $shooter)){

                if ( $target instanceof FighterFlight || $target instanceof SuperHeavyFighter)
                {
                    return;
                }

                $structTarget = null;

                if ( $target instanceof MediumShip ){
                    $structTarget = $target->getStructureSystem(0);
                }
                else{
                    $tf = $target->getFacingAngle();
                    $shooterCompassHeading = mathlib::getCompassHeadingOfShip($target, $shooter);
                    $location =  $target->doGetHitSection($tf, $shooterCompassHeading, TacGamedata::$currentTurn, $this);

                    $rolled = Movement::isRolled($target);

                    if ($rolled && $location == 3){
                        $location = 4;
                    }else if ($rolled && $location == 4){
                        $location = 3;
                    }

                    if ($location != 0){
                        $structure = $target->getStructureSystem($location);
                        if ($structure != null && $structure->isDestroyed(TacGamedata::$currentTurn-1))
                            $location = 0;
                    }

                    $structTarget = $target->getStructureSystem($location);
//                    $locTarget = $target->getHitSection($pos, $shooter, $fireOrder->turn, $this);
//                    $structTarget = $target->getStructureSystem($locTarget);
                }

                $crit = new ArmorReduced(-1, $target->id, $structTarget->id, "ArmorReduced", $gamedata->turn);
                $crit->updated = true;
                $crit->inEffect = false;

                if ( $structTarget != null && $structTarget->armour > 0){
                    $structTarget->criticals[] = $crit;
                }
            }
        }

        public function getDamage($fireOrder){        return Dice::d(2, 10)+10;   }
        public function setMinDamage(){   return  $this->minDamage = 12 - $this->dp;      }
        public function setMaxDamage(){   return  $this->maxDamage = 30 - $this->dp;      }
    }

    class LightMolecularDisrupterHandler{

        private static $hits = array();

        public static function doArmorReduction($target, $shooter){
            $currentTurn = TacGamedata::$currentTurn;

            Debug::log("doArmorReduction");

            // Always clean-up first.
            foreach (LightMolecularDisrupterHandler::$hits as $hit){
                Debug::log("1");
                if($hit['turn'] != $currentTurn){
                    Debug::log("2");
                    unset($hit);
                    Debug::log("3");
                }
            }

            Debug::log("4");
            // add new hit to the array
            LightMolecularDisrupterHandler::$hits[] = array('turn'=>$currentTurn, 'shooter'=>$shooter->id ,'target'=>$target->id);
            Debug::log("5");

            // Check if this was number 3 for a certain target. If so, decrease armor of the structure
            $count = 0;

            foreach(LightMolecularDisrupterHandler::$hits as $hit){
                Debug::log("Checking array");
                if($hit['shooter'] == $shooter->id && $hit['target'] == $target->id){
                    $count++;
                    Debug::log("Count is ".$count." for shooter id ".$shooter->id);
                }
            }

            if($count===3){
                Debug::log("Count is 3 for shooter id ".$shooter->id." and target id ".$target->id);
                return true;
            }

            return false;
        }
    }




