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

    class ParticleBlaster extends Particle{

        public $trailColor = array(30, 170, 255);

        public $name = "stdParticleBeam";
        public $displayName = "Standard Particle Beam";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 5;
        public $trailLength = 10;

        public $loadingtime = 2;


        public $rangePenalty = 0.5;
        public $fireControl = array(0, 4, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 22 - $this->dp;      }

    }
    
    class AdvParticleBeam extends Particle{

        public $trailColor = array(30, 170, 255);

        public $name = "advParticleBeam";
        public $displayName = "Advanced Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 14;
        public $animationWidth = 5;
        public $trailLength = 10;
        public $iconPath = "stdParticleBeam.png";

        public $intercept = 2;
        public $loadingtime = 1;
        public $rangePenalty = 0.66;
        public $fireControl = array(5, 5, 5); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+8;   }
        public function setMinDamage(){     $this->minDamage = 9 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 18 - $this->dp;      }

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

    class ParticleCannon extends Raking{

        public $trailColor = array(30, 170, 255);

        public $name = "particleCannon";
        public $displayName = "Particle Cannon";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 15;
        public $animationWidth = 8;
        public $trailLength = 24;
        public $damageType = "raking";

        public $intercept = 1;
        public $loadingtime = 2;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 5); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+15;   }
        public function setMinDamage(){     $this->minDamage = 17 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 35 - $this->dp;      }
    }

    class HvyParticleCannon extends Raking{

        public $trailColor = array(252, 252, 252);

        public $name = "hvyParticleCannon";
        public $displayName = "Heavy Particle Cannon";
        public $animation = "beam";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 33;
        public $animationWidth = 7;
        public $trailLength = 2400;
        public $damageType = "raking";

        public $loadingtime = 6;

        public $rangePenalty = 0.33;
        public $fireControl = array(0, 4, 6); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 6)+60;   }
        public function setMinDamage(){     $this->minDamage = 66 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 120 - $this->dp;      }
    }

    class ParticleCutter extends Raking{

        public $trailColor = array(252, 252, 252);

        public $name = "hvyParticleCannon";
        public $displayName = "Heavy Particle Cannon";
        public $animation = "beam";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 30;
        public $animationWidth = 5;
        public $trailLength = 2400;
        public $damageType = "raking";
        
        // Set to make the weapon start already overloaded.
        public $overloadturns = 2;

        public $loadingtime = 2;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function isOverloadingOnTurn($turn = null){
            return true;
        }
        
        public function getDamage($fireOrder){ return Dice::d(10, 2)+12;   }
        public function setMinDamage(){     $this->minDamage = 14 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 32 - $this->dp;      }
    }

    class ParticleRepeater extends Particle{

        public $trailColor = array(252, 252, 252);

        public $name = "particleRepeater";
        public $displayName = "Particle Repeater";
        public $animation = "trail";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.40;
        public $projectilespeed = 40;
        public $animationWidth = 4;
        public $trailLength = 30;
        
        public $loadingtime = 1;
        public $boostable = true;
        public $boostEfficiency = 1;

        public $rangePenalty = 0.5;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals
        
        private $hitChanceMod = 0;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Standard";
            $this->normalload = $this->loadingtime;
        
            $this->setTimes();
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getLoadingTime(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 1 + floor($this->getBoostLevel(TacGamedata::$currentTurn)/2);
            }
            else{
                return $this->loadingtime;
            }
        }

        public function getTurnsloaded(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 1 + floor($this->getBoostLevel(TacGamedata::$currentTurn)/2);
            }
            else{
                return $this->turnsloaded;
            }
        }
        
        public function setTimes(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                $this->loadingtime = 1 + floor($this->getBoostLevel(TacGamedata::$currentTurn)/2);
                $this->turnsloaded = 1 + floor($this->getBoostLevel(TacGamedata::$currentTurn)/2);
                $this->normalload = 1 + floor($this->getBoostLevel(TacGamedata::$currentTurn)/2);
            }
        }
        
/*        protected function getPulses($turn)
        {
            switch($this->getBoostLevel($turn)){
                case 0:
                    return Dice::d(2);
                    break;
                case 1:
                    return (Dice::d(3)+1);
                    break;
                case 2:
                    return (Dice::d(3)+2);
                    break;
            }            
        }*/

        public function getIntercept($gamedata, $fireOrder){
            $this->intercept = $this->getInterceptRating($gamedata->turn);
            
            parent::getIntercept($gamedata, $fireOrder);
        }
        
        public function fire($gamedata, $fireOrder){
            $this->setTimes();

            $nrOfShots = $this->getMaxShots($gamedata->turn);
            
            for($count = 0; $count < $nrOfShots; $count++){
                setHitChanceMod($count+1);
                $fireOrder->rolled = 0;
                parent::fire($gamedata, $fireOrder);
                // stop as soon as a shot misses
                if($fireOrder->shotshit == 0){
                    break;
                }
            }
        }
        
        private function setHitChanceMod($shotNumber){
            switch($shotNumber){
                case 1:
                    $this->hitChanceMod = 0;
                    break;
                case 2:
                    $this->hitChanceMod = -1;
                    break;
                default:
                    $this->hitChanceMod = -1 - 2*floor($shotNumber-2);
                    break;
            }
        }
        
        protected function getWeaponHitChanceMod($turn){
            return $this->hitChanceMod;
        }

        private function getBoostLevel($turn){
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

        private function getMaxShots($turn){
            return 1 + $this->getBoostLevel($turn);
        }

        private function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;      }
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


        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;

            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }

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

