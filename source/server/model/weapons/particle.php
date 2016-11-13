<?php


    class Particle extends Weapon{
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public $priority = 6;

    }



    class StdParticleBeam extends Particle{ 

        public $trailColor = array(30, 170, 255);

        public $name = "stdParticleBeam";
        public $displayName = "Standard Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;


        public $rangePenalty = 1;
        public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals
        public $priority = 5;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }

    }

    class QuadParticleBeam extends StdParticleBeam {
        public $name = "quadParticleBeam";
        public $displayName = "Quad Particle Beam";
        public $guns = 4;
    }

    class ParticleBlaster extends Particle{

        public $trailColor = array(30, 170, 255);

        public $name = "particleBlaster";
        public $displayName = "Particle Blaster";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 5;
        public $trailLength = 10;
        public $priority = 6;

        public $loadingtime = 2;


        public $rangePenalty = 0.5;
        public $fireControl = array(0, 4, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }

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
        public $priority = 5;

        public $intercept = 2;
        public $loadingtime = 1;
        public $rangePenalty = 0.66;
        public $fireControl = array(5, 5, 5); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+8;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }

    }



    class TwinArray extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "twinArray";
        public $displayName = "Twin Array";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 2;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(6, 5, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

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
        public $priority = 6;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 26 ;      }

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

        public $intercept = 1;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 5); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+15;   }
        public function setMinDamage(){     $this->minDamage = 17 ;      }
        public function setMaxDamage(){     $this->maxDamage = 35 ;      }
    }



    class LightParticleCannon extends Raking{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleCannon";
        public $displayName = "Light Particle Cannon";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 13;
        public $animationWidth = 5;
        public $trailLength = 24;

        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 1;
        public $fireControl = array(0, 2, 4); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
           // $this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
    }
    


    class HvyParticleCannon extends Raking{

        public $trailColor = array(252, 252, 252);

        public $name = "hvyParticleCannon";
        public $displayName = "Heavy Particle Cannon";
        public $animation = "laser";
        public $animationColor = array(255, 230, 100);
        public $animationColor2 = array(255, 255, 255);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 5;
        public $priority = 7;

        public $loadingtime = 6;

        public $rangePenalty = 0.33;
        public $fireControl = array(0, 4, 6); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        public $firingModes = array( 1 => "Raking");
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 6)+60;   }
        public function setMinDamage(){     $this->minDamage = 66 ;      }
        public function setMaxDamage(){     $this->maxDamage = 120 ;      }
    }



    class ParticleCutter extends Raking{

        public $trailColor = array(252, 252, 252);

        public $name = "particleCutter";
        public $displayName = "Particle Cutter";
        public $animation = "beam";
        public $animationColor = array(252, 252, 252);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 30;
        public $animationWidth = 5;
        public $trailLength = 1500;
        public $firingModes = array( 1 => "Sustained");
        
        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        
        // Set to make the weapon start already overloaded.
        public $alwaysoverloading = true;
        public $overloadturns = 2;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Raking";
            $this->data["REMARK"] = "This weapon is always in sustained mode.";

            parent::setSystemDataWindow($turn);
        }

        public function isOverloadingOnTurn($turn = null){
            return true;
        }
        
        public function getDamage($fireOrder){ return Dice::d(10, 2)+12;   }
        public function setMinDamage(){     $this->minDamage = 14 ;      }
        public function setMaxDamage(){     $this->maxDamage = 32 ;      }
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
        public $priority = 5;

        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals
        
        private $hitChanceMod = 0;
        private $previousHit = true;
        
       
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";
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
        
//        public function getIntercept($gamedata, $fireOrder){
//            $this->intercept = $this->getInterceptRating($gamedata->turn);
//            
//            parent::getIntercept($gamedata, $fireOrder);
//        }
        
        
        public function fire($gamedata, $fireOrder){ //new, minimalistic redefinition, relying on  getShotHitChanceMod()
            $this->hitChanceMod = 0;
            $this->setTimes();
            $fireOrder->shots = $this->getMaxShots($gamedata->turn);
            parent::fire($gamedata, $fireOrder);
        }
        
        /*old version - full redefine of fire()
        public function fire($gamedata, $fireOrder){ 
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $target = $gamedata->getShipById($fireOrder->targetid);
            $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
            
            $this->setTimes();
            $fireOrder->shots = $this->getMaxShots($gamedata->turn);

            $pos = $shooter->getCoPos();

            for ($i=0;$i<$fireOrder->shots;$i++){
                $this->setHitChanceMod($i+1);
                $this->calculateHit($gamedata, $fireOrder);
                $intercept = $this->getIntercept($gamedata, $fireOrder);

                $needed = $fireOrder->needed - ($this->grouping*$i);
                $rolled = Dice::d(100);
                if ($rolled > $needed && $rolled <= $needed+($intercept*5)){
                    $fireOrder->intercepted += 1;
                }

                $fireOrder->notes .= " FIRING SHOT ". ($i+1) .": rolled: $rolled, needed: $needed\n";
                if ($rolled <= $needed){
                    $fireOrder->shotshit++;
                    $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
                }
                else{
                    // a repeater shot missed. No need to roll for the rest.
                    break;
                }
            }

            $fireOrder->rolled = 1;//Marks that fire order has been handled
        }
        */
        
        
        /*
        protected function setHitChanceMod($shotNumber){
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
        */
        
        
        
        
        /*if previous shot missed, next one misses automatically*/
        /*so if current mod is not equal to one of previous shot, then it's clearly a miss - return suitably high mod*/
        public function getShotHitChanceMod($shotInSequence){ 
            $prevExpectedChance = $this->getPrevShotHitChanceMod($shotInSequence-1);
            if($prevExpectedChance != $this->hitChanceMod){ //something missed in between
                $this->hitChanceMod = 10000; //clear miss!!!
            }else{
                $this->hitChanceMod = $this->getPrevShotHitChanceMod($shotInSequence);
            }
            return $this->hitChanceMod;
        }
        
        public function getPrevShotHitChanceMod($shotInSequence){ //just finds hit chance for a given shot - what it should be
            if($shotInSequence <=0) return 0;
            if($shotInSequence ==1) return 5;
            $mod= 5+10*($shotInSequence-2);
            return $mod;
        }
    
                
        
        protected function getWeaponHitChanceMod($turn){
            return $this->hitChanceMod;
        }

        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){ 
            //if target is fighter flight, ensure that the same fighter is hit every time!
            if($target instanceof FighterFlight){
                $fireOrder->linkedHit = $system;
            }            
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }
        
        
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

        protected function getMaxShots($turn){
            return 1 + $this->getBoostLevel($turn);
        }

        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    } //endof class ParticleRepeater
    


    
    class RepeaterGun extends ParticleRepeater{
        public $name = "repeaterGun";
        public $displayName = "Repeater Gun";
        public $animation = "trail";
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $trailLength = 25;
        
        public $boostEfficiency = 2;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        public $priority = 4;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        //appropriate redefinitions mostly done in ParticleRepeater class!
        
        public function getDamage($fireOrder){ return Dice::d(10)+3;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
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
        public $intercept;


        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;

        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }

            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }



    class SolarCannon extends Particle{
        public $name = "solarCannon";
        public $displayName = "Solar Cannon";
        public $animation = "beam";
        public $animationColor = array(0, 250, 0);
        public $animationExplosionScale = 0.45;
        public $projectilespeed = 15;
        public $animationWidth = 8;
        public $trailLength = 14;
        public $priority = 6;

        public $loadingtime = 3;

        public $rangePenalty = 0.5;
        public $fireControl = array(0, 3, 5); // fighters, <mediums, <capitals

        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        public $noOverkill = true; // The damage of a solar cannon does not overkill.

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            /*repeat damage on structure (ignoring armor); 
              system hit will have its armor reduced by 2
              for non-fighter targets
              */

            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
            if(!$target instanceof FighterFlight){
                $damageWasDealt=true; //if structure is already destroyed, no further overkill will happen
                $struct = $target->getStructureSystem($system->location);
                //reduce damage by armor of system hit - as it would be (was!) during actual damage-dealing procedure
                $damage = $damage - $this->getSystemArmourStandard($system, $gamedata, $fireOrder) - $this->getSystemArmourInvulnerable($system, $gamedata, $fireOrder);
                //reduce armor of system hit
                $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                $crit->updated = true;
                $crit->inEffect = false;
                if ( $system != null ){
                    $system->criticals[] = $crit;
                    $system->criticals[] = $crit;
                }
                //repeat damage on structure this system is mounted to; instead of ignoring armor, damage is increased by armor of struture
                //increase damage by armor of structure - to simulate armor-ignoring effect
                $damage = $damage + $this->getSystemArmourStandard($struct, $gamedata, $fireOrder) + $this->getSystemArmourInvulnerable($struct, $gamedata, $fireOrder);
                parent::doDamage($target, $shooter, $struct, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location); 
            }
        } //endof function doDamage
        
        
        /*old version, kept just in case
        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $location = null){

            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $location);

            // Lower armor on the system that was hit.
            $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
            $crit->updated = true;
            $crit->inEffect = false;

            if ( $system != null ){
                $system->criticals[] = $crit;
                $system->criticals[] = $crit;
            }
            
            // Get the target system to apply the melting damage
            $structTarget = null;

            if($system instanceof Structure){
                // The system that was hit is structure
                $structTarget = $system;
                if($structTarget->isDestroyed()){
                    return;
                }
            }
            else{
                // The system is not structure. Get the structure of this system.
                if ( $target instanceof MediumShip ){
                    $structTarget = $target->getStructureSystem(0);
                }
                else{
                    $structTarget = $target->getStructureSystem($system->location);
                }
            }

            // Make a new damage entry for the structure.
            $structArmour = 0; // the melt damage ignores armour
            $systemHealth = $structTarget->getRemainingHealth();
            $armour = $this->getSystemArmour($system, $gamedata, $fireOrder );
            $structDamage = $damage - $armour; // Only give the amount of damage that came through
            
            if($structDamage < 0){
                $structDamage = 0;
            }

            $destroyed = false;
            if ($damage >= $systemHealth){
                $destroyed = true;
                $structDamage = $systemHealth;
            }

            $damageEntry = new DamageEntry(-1, $target->id, -1, $gamedata->turn, $structTarget->id, $structDamage, $structArmour, 0, $fireOrder->id, $destroyed, "", $fireOrder->damageclass);
            $damageEntry->updated = true;
            $structTarget->damage[] = $damageEntry;
        }
        */
        
        public function getDamage($fireOrder){        return Dice::d(5)+12;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 17 ;      }

    }



    class LightParticleBlaster extends LinkedWeapon{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleBlaster";
        public $displayName = "Light Particle Blaster";
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
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(3)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 3+$this->damagebonus ;      }

    }



    class LightParticleBeam extends LinkedWeapon{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleBeam";
        public $displayName = "Light Particle Beam";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $priority = 4;

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;

        public $damageType = "Standard"; 
        public $weaponClass = "Particle"; 
        
        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }



    class HeavyBolter extends Particle{
        public $name = "heavyBolter";
        public $displayName = "Heavy Bolter";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.5;
        public $projectilespeed = 12;
        public $animationWidth = 6;
        public $trailLength = 6;
        public $priority = 6;

        public $loadingtime = 3;


        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 2, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 24;   }
        public function setMinDamage(){     $this->minDamage = 24 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }
    }
    


    class MediumBolter extends Particle{
        public $name = "mediumBolter";
        public $displayName = "Medium Bolter";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 14;
        public $animationWidth = 4;
        public $trailLength = 4;
        public $priority = 6;

        public $loadingtime = 2;

        public $intercept = 1;

        public $rangePenalty = 0.5;
        public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 18;   }
        public function setMinDamage(){     $this->minDamage = 18 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
    }
    
    class LightBolter extends Particle{

        public $name = "lightBolter";
        public $displayName = "Light Bolter";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        public $priority = 5;

        public $loadingtime = 1;

        public $intercept = 1;

        public $rangePenalty = 1;
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 12;   }
        public function setMinDamage(){     $this->minDamage = 12 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }
    
    class LightParticleBeamShip extends StdParticleBeam{

        public $trailColor = array(30, 170, 255);

        public $name = "lightParticleBeamShip";
        public $displayName = "Light Particle Beam";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.12;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 8;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }



    class ParticleProjector extends Particle{

        public $trailColor = array(30, 170, 255);

        public $name = "particleProjector";
        public $displayName = "Particle Projector";
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 20;

        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 4;

        public $rangePenalty = 1;
        public $fireControl = array(1, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }



?>

