<?php
// Added by Jasper

    class Molecular extends Weapon{
        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
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
        public $animationWidth = 6;
        public $trailLength = 16;

        public $intercept = 2;
        public $priority = 6;


        public $loadingtime = 1;


        public $rangePenalty = 1;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+9;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 19 ;      }
        
    }


    class HeavyFusionCannon extends FusionCannon{
        public $name = "heavyFusionCannon";
        public $displayName = "Heavy Fusion Cannon";
        public $animation = "beam";

        public $animationExplosionScale = 0.30;
        public $projectilespeed = 10;
        public $animationWidth = 9;
        public $trailLength = 25;

        public $loadingtime = 2;
        public $intercept = 1;
        public $priority = 6;

        public $rangePenalty = 0.5;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        public function getDamage($fireOrder){        return Dice::d(10, 2)+14;   }
        public function setMinDamage(){     $this->minDamage = 16 ;      }
        public function setMaxDamage(){     $this->maxDamage = 34 ;      }
    }



    class LightFusionCannon extends LinkedWeapon{
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
        public $priority = 3;

        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 

        function __construct($startArc, $endArc, $damagebonus, $shots){
            $this->damagebonus = $damagebonus;
            $this->shots = $shots;
            $this->defaultShots = $shots;
            $this->intercept = $shots;

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;
            
            $ns = min(3,$shots); //no graphics for more than 3 weapons
            $this->iconPath = "lightfusionCannon$ns.png";
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

    }


    // mhhh... extended from Raking as that involves less code duplication
    class MolecularDisruptor extends Raking
    {
        public $name = "molecularDisruptor";
        public $displayName = "Molecular Disruptor";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $trailColor = array(30, 170, 255);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 25;
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        public $intercept = 0;
        public $loadingtime = 4;

        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );

        public $rangePenalty = 1;

        public $fireControlArray = array( 1=>array(-4, 2, 4), 2=>array(null, -2, 0) ); //Raking and Piercing mode, respectively - Piercing adds -4!
        //public $fireControl = $this->fireControlArray[1];  // fighters, <mediums, <capitals
        //private $damagebonus = 30;

        public $damageType = "Raking"; 
        public $damageTypeArray = array(1=>'Raking', 2=>'Piercing');
        public $weaponClass = "Molecular"; 
                
        
        private $alreadyReduced = false;
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
            $this->data["Special"] .= "Reduces armor of facing structure.";
        }

        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
            if(!$this->alreadyReduced){ 
                $struct = $target->getStructureSystem($location);
                if ($struct->advancedArmor) return; //advanced armor prevents effect 
                if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
                    $this->alreadyReduced = true; //do this only for first part of shot that actually connects
                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = false;
                    $struct->criticals[] = $crit;
                }
            }
        }       

        public function getDamage($fireOrder){        return Dice::d(10, 2) + 30;   }
        public function setMinDamage(){     $this->minDamage = 2+30;      }
        public function setMaxDamage(){     $this->maxDamage = 20+30;      }
    } //endof class MolecularDisruptor




    class DestabilizerBeam extends Molecular{
        public $trailColor = array(30, 170, 255);

        public $name = "destabilizerBeam";
        public $displayName = "Destabilizer Beam";
        public $animation = "laser";
        public $animationColor = array(100, 100, 255);
        public $animationWidth = 4.5;
        public $animationWidth2 = 0.3;
        public $priority = 2; 

        public $animationExplosionScale = 0.35;

        public $intercept = 0;
        public $loadingtime = 4;

        public $firingModes = array(
            1 => "Piercing"
        );

        public $rangePenalty = 0.33;
        public $fireControl = array(-5, 1, 6); // fighters, <mediums, <capitals
        
        public $damageType = "Piercing"; 
        public $weaponClass = "Molecular"; 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 6)+30;   }
        public function setMinDamage(){     $this->minDamage = 6+30;      }
        public function setMaxDamage(){     $this->maxDamage = 60+30;      }
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
            1 => "Flaying"
        );
        
        public $rangePenalty = 0.33;
        public $fireControl = array(null, 0, 4); // fighters, <mediums, <capitals
        private $alreadyFlayed = false; //to avoid doing this multiple times

        public function setSystemDataWindow($turn){
            $this->data["Special"] = "Reduces armor of facing section (structure and all systems).";
            parent::setSystemDataWindow($turn);
        }


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }

        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            //$location is guaranteed to be filled in this case!     
            if($this->alreadyFlayed) return;
            $this->alreadyFlayed = true; //avoid doing that multiple times
            foreach ($target->systems as $system){
                if ($system->advancedArmor) return;
                if ($target->shipSizeClass<=1 || $system->location === $location){ //MCVs and smaller ships are one huge section technically
                    $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = false;
                    $system->criticals[] = $crit;
                }
            }
        } //endof function doDamage
        
    } //endof class MolecularFlayer


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
        public $loadingtime = 3;
        public $raking = 6;
        public $addedDice;
        public $priority = 8;

        public $boostable = true;
        public $boostEfficiency = 4;
        public $maxBoostLevel = 4;

        public $firingModes = array(
            1 => "Raking"
        );

        public $rangePenalty = 0.33;
        public $fireControl = array(null, 4, 4); // fighters, <mediums, <capitals
        //private $damagebonus = 10;

        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 


        public function setSystemDataWindow($turn){
            $boost = $this->getExtraDicebyBoostlevel($turn);            
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
            //Raking(6) is already described in Raking class
            $this->data["Special"] .= 'Treats armor as if it was 1 point lower.';
            $this->data["Special"] .= '<br>Can be boosted for increased dmg output (+1d10 per 4 power added, up to 4 times).';
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

 
        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){ //standard part of armor - reduce by 1!
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour - 1;
            $armour = max(0,$armour);
            return $armour;
        }
            
            
        public function getDamage($fireOrder){
            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10, (5 + $add)) +10;
            return $dmg;
        }

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }


        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 5 + ($boost * 1) + 10;
        }   


        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 50 + ($boost * 10) + 10;
        }  
   }

        
        
    class LightMolecularDisruptor extends Raking{
        public $name = "molecularDisruptor";
        public $displayName = "Light Molecular Distruptor";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 10;
        public $animationWidth = 5;
        public $trailLength = 12;
        
        public $loadingtime = 3;
        public $raking = 10;
        public $exclusive = true;
        public $priority = 8;//fighter Raking weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(-4, 0, 3); // fighters, <mediums, <capitals 

        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 
        private $alreadyReduced = false;
        
        function __construct($startArc, $endArc, $damagebonus){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){      
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = 'Reduces armor on facing Structure if at least 3 fighters hit';
        }

        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
            if(!$this->alreadyReduced){ 
                $this->alreadyReduced = true; 
                if(LightMolecularDisrupterHandler::checkArmorReduction($target, $shooter)){ //static counting!
                    $struct = $target->getStructureSystem($location);
                    if ($struct->advancedArmor) return;
                    if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
                        $crit = new ArmorReduced(-1, $target->id, $system->id, "ArmorReduced", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = false;
                        $struct->criticals[] = $crit;
                    }
                }
            }
        }
        
        public function getDamage($fireOrder){        return Dice::d(2, 10)+15;   }
        public function setMinDamage(){   return  $this->minDamage = 17 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 35 ;      }
    }



    class LightMolecularDisrupterHandler{
        private static $hits = array();

        
        /* checks if armor reduction should occur (returns true if so)
            increases internal counters needed to do so
        */
        public static function checkArmorReduction($target, $shooter){ 
            $currentTurn = TacGamedata::$currentTurn;

            // Always clean-up first.
            foreach (LightMolecularDisrupterHandler::$hits as $hit){
                if($hit['turn'] != $currentTurn) unset($hit);
            }

            // add new hit to the array
            LightMolecularDisrupterHandler::$hits[] = array('turn'=>$currentTurn, 'shooter'=>$shooter->id ,'target'=>$target->id);

            // Check if this was number 3 for a certain target. If so, decrease armor of the structure
            $count = 0;

            foreach(LightMolecularDisrupterHandler::$hits as $hit){
                //    Debug::log("Checking array");
                if($hit['shooter'] == $shooter->id && $hit['target'] == $target->id){
                    $count++; //       Debug::log("Count is ".$count." for shooter id ".$shooter->id);
                }
            }

            if($count===3){ //   Debug::log("Count is 3 for shooter id ".$shooter->id." and target id ".$target->id);
                return true;
            }

            return false;
        }
    }




?>