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
        public $animation = "bolt";
        public $animationColor =  array(175, 225, 175);

        public $intercept = 2;
        public $priority = 6; //that's heavy hitting power


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
	    
	    /*
        public $animation = "beam";
        public $animationExplosionScale = 0.30;
        public $projectilespeed = 10;
        public $animationWidth = 9;
        public $trailLength = 25;
*/
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
        public $name = "lightfusionCannon";
        public $displayName = "Light Fusion Cannon";
        public $animation = "bolt";
	public $animationColor = array(175, 225, 175);//changing to the same as Fusion Cannon
	    /*
        public $animationColor = array(30, 170, 255);
        public $trailColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
*/
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
        public $animation = "laser"; //it's Raking weapon after all
        public $animationColor = array(30, 170, 255);
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 25;
	*/
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
			if ($system->advancedArmor) return; //advanced armor prevents effect 
            if(!$this->alreadyReduced){ 
                //$struct = $target->getStructureSystem($location); //this caused problems if first rake penetrated!
				$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
				$struct = $target->getStructureSystem($sectionFacing); 
                if ($struct->advancedArmor) return; //advanced armor prevents effect 
                if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
                    $this->alreadyReduced = true; //do this only for first part of shot that actually connects
                    $crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
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



class SuperHeavyMolecularDisruptor extends Raking
    {
        public $name = "superHeavyMolecularDisruptor";
        public $displayName = "Super Heavy Molecular Disruptor";
        public $animation = "laser"; //it's Raking weapon after all
        public $animationColor = array(30, 170, 255);
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 25;
	*/
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        public $intercept = 0;
        public $loadingtime = 6;

        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );

        public $rangePenalty = 0.5;

        public $fireControlArray = array( 1=>array(null, 1, 3), 2=>array(null, -3, -1) ); //Raking and Piercing mode, respectively - Piercing adds -4!

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
            $this->data["Special"] .= "Reduces armor of facing structure by 2 points.";
        }

		protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
		    parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
		    
		    if ($system->advancedArmor) return; // advanced armor prevents effect
		    
		    if(!$this->alreadyReduced){ 
		        //$struct = $target->getStructureSystem($location); //this caused problems if first rake penetrated!
		        $sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
		        $struct = $target->getStructureSystem($sectionFacing); 

		        if ($struct->advancedArmor) return; // advanced armor prevents effect 
		        
		        if(!$struct->isDestroyed($fireOrder->turn-1)){ // last turn Structure was still there...
		            $this->alreadyReduced = true; // do this only for the first part of the shot that actually connects
		            
		            $crit1 = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
		            $crit1->updated = true;
		            $crit1->inEffect = false;
		            $struct->criticals[] = $crit1;

		            // Add the crit twice since Super Heavy Distruptor reduces armour by 2.
		            $crit2 = clone $crit1;
		            $struct->criticals[] = $crit2;
		        }
		    }
		}    

        public function getDamage($fireOrder){        return Dice::d(10, 4) + 32;   }
        public function setMinDamage(){     $this->minDamage = 4+32;      }
        public function setMaxDamage(){     $this->maxDamage = 40+32;      }
	} //endof class SuperHeavyMolecularDisruptor


   class MolecularPenetrator extends Raking{
        public $name = "molecularPenetrator";
        public $displayName = "Molecular Penetrator";
        public $animation = "laser";
        public $animationColor = array(100, 100, 255);
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationWidth = 4.5;
        public $animationWidth2 = 0.3;
        public $animationExplosionScale = 0.35;
	*/
	    
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

        public $intercept = 0;
        public $loadingtime = 4;

        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );

        public $rangePenalty = 0.33;

        public $fireControlArray = array( 1=>array(null, 1, 4), 2=>array(null, -3, 0) ); //Raking and Piercing mode, respectively - Piercing adds -4!

        public $damageType = "Raking"; 
        public $damageTypeArray = array(1=>'Raking', 2=>'Piercing');
        public $weaponClass = "Molecular"; 

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
            $this->data["Special"] .= "Can fire in Raking or Piercing modes.";
        }

        public function getDamage($fireOrder){        return Dice::d(10, 4)+15;   }
        public function setMinDamage(){     $this->minDamage = 4+15;      }
        public function setMaxDamage(){     $this->maxDamage = 40+15;      }
    }//endof class MolecularPenetrator



    class DestabilizerBeam extends Molecular{
        public $name = "destabilizerBeam";
        public $displayName = "Destabilizer Beam";
        public $animation = "laser";
        public $animationColor = array(100, 100, 255);
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationWidth = 4.5;
        public $animationWidth2 = 0.3;
        public $animationExplosionScale = 0.35;
	*/
	    
        public $priority = 2; 
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
        public $animationExplosionScale = 0.7; //needed, as weapon does no damage
	    /*
        public $trailColor = array(0, 200, 200);
        public $animationExplosionScale = 0.50;
        public $projectilespeed = 15;
        public $animationWidth = 6;
        public $trailLength = 15;
	*/
        public $priority = 1;

        public $intercept = 0;
        public $loadingtime = 1;

        public $firingModes = array(
            1 => "Flaying"
        );
	    
        public $damageType = "Standard"; //simplest
        public $weaponClass = "Molecular"; 
        
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
                    $system->setCritical($crit); //$system->criticals[] = $crit;
                }
            }
        } //endof function doDamage
        
    } //endof class MolecularFlayer


    class FusionAgitator extends Raking{
        public $name = "fusionAgitator";
        public $displayName = "Fusion Agitator";
        public $animation = "laser";
        public $animationColor = array(0, 200, 200);
        public $animationExplosionScale = 0.35; //make it thin, despite high damage potential!
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationWidth = 2;
        public $animationWidth2 = 0.3;
        public $animationExplosionScale = 0.15;
*/
        public $intercept = 0;
        public $loadingtime = 3;
        public $raking = 6;
        public $addedDice;
        public $priority = 8; //damage dealing mode means it's not very effective as stripping systems, and should be fired late despite high damage potential

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
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
            //Raking(6) is already described in Raking class
            $this->data["Special"] .= 'Treats armor as if it was 1 point lower.';
            $this->data["Special"] .= '<br>Can be boosted for increased dmg output (+1d10 per 4 power added, up to 4 times).';
            $this->data["Boostlevel"] = $boost;
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
 
 	class EarlyFusionAgitator extends Raking{
 		public $name = "earlyFusionAgitator";
        public $displayName = "Early Fusion Agitator";
        public $animation = "laser";        
        public $intercept = 0;
        public $loadingtime = 3;
        public $raking = 6;
        public $priority = 7; //damage dealing mode means it's not very effective as stripping systems, and should be fired late despite high damage potential

        public $firingModes = array(
            1 => "Raking"
        );

        public $rangePenalty = 0.33;
        public $fireControl = array(null, 2, 4); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 

        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){ //standard part of armor - reduce by 1!
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour - 1;
            $armour = max(0,$armour);
            return $armour;
        }
                                
        public function getDamage($fireOrder){        return Dice::d(10, 4)+10;   }
        public function setMinDamage(){   return  $this->minDamage = 14 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 50 ;      }
         
   }//endof class EarlyFusionAgitator

  class FusionCutter extends Raking{ //Even earlier version of Fusion Agitator used by Yolu on OSATs
        public $name = "fusionCutter";
        public $displayName = "Fusion Cutter";
        public $animation = "laser";
        public $animationColor = array(0, 200, 200);
        public $animationExplosionScale = 0.35; //make it thin, despite high damage potential!
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationWidth = 2;
        public $animationWidth2 = 0.3;
        public $animationExplosionScale = 0.15;
*/
        public $intercept = 0;
        public $loadingtime = 3;
        public $raking = 6;
        public $priority = 7; //damage dealing mode means it's not very effective as stripping systems, and should be fired late despite high damage potential

        public $firingModes = array(
            1 => "Raking"
        );

        public $rangePenalty = 0.5;
        public $fireControl = array(null, 2, 3); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 
                        
        public function getDamage($fireOrder){        return Dice::d(10, 4)+8;   }
        public function setMinDamage(){   return  $this->minDamage = 12 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 48 ;      }
         
   }//endof	class FusionCutter
	      
        //Yolu heavy fighter weapon
    class LightMolecularDisruptor extends Raking{
        public $name = "molecularDisruptor";
        public $displayName = "Light Molecular Disruptor";
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
	    /*
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 10;
        public $animationWidth = 5;
        public $trailLength = 12;
        */
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
			if ($system->advancedArmor) return;
            if(!$this->alreadyReduced){ 
                $this->alreadyReduced = true; 
                if(LightMolecularDisrupterHandler::checkArmorReduction($target, $shooter)){ //static counting!
					$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
					$struct = $target->getStructureSystem($sectionFacing); 
                    //$struct = $target->getStructureSystem($location);
                    if ($struct->advancedArmor) return;
                    if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
                        $crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = false;
                        $struct->criticals[] = $crit;
                    }
                }
            }
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+15;   }
        public function setMinDamage(){   return  $this->minDamage = 17 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 35 ;      }
    }

        //Yolu heavy fighter weapon
    class LightMolecularDisruptorShip extends Raking{
        public $name = "molecularDisruptor";
        public $displayName = "Light Molecular Disruptor";
        public $animation = "laser";
        public $animationColor = array(30, 170, 255);
	    /*
        public $animationExplosionScale = 0.20;
        public $projectilespeed = 10;
        public $animationWidth = 5;
        public $trailLength = 12;
        */
        public $loadingtime = 3;
        public $raking = 10;
  //      public $exclusive = true;
        public $priority = 8;//fighter Raking weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(-4, 0, 3); // fighters, <mediums, <capitals 

        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 
        private $alreadyReduced = false;
        

        
        public function setSystemDataWindow($turn){      
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = 'Reduces armor on facing Structure if at least 3 shots hit';
        }

        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
			if ($system->advancedArmor) return;
            if(!$this->alreadyReduced){ 
                $this->alreadyReduced = true; 
                if(LightMolecularDisrupterHandler::checkArmorReduction($target, $shooter)){ //static counting!
					$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
					$struct = $target->getStructureSystem($sectionFacing); 
                    //$struct = $target->getStructureSystem($location);
                    if ($struct->advancedArmor) return;
                    if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
                        $crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
                        $crit->updated = true;
                        $crit->inEffect = false;
                        $struct->criticals[] = $crit;
                    }
                }
            }
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+15;   }
        public function setMinDamage(){   return  $this->minDamage = 17 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 35 ;      }
    }//end of LightMolecularDisruptorShip


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
    }//endof class LightMolecularDisrupterHandler



	/*fighter-sized Polarity Cannon - primary weapon of Shadow fighters*/
    class FtrPolarityCannon extends LinkedWeapon{
        public $name = "FtrPolarityCannon";
		public $iconPath = "ftrPolarityCannon.png";
        public $displayName = "Polarity Cannon";
        public $animation = "trail";
        public $animationColor = array(45, 180, 255);
	    /*
        public $trailColor = array(45, 180, 255);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
	*/
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

        public $intercept = 1; //weapon can (..probably...) intercept at default rules... probably intended as a single shot of -2/-3 - but in FV these shots are separate, so that's a lot of separate -1s
        public $loadingtime = 1;
		public $normalload = 2; //full power (one more shot) needs longer charge
		public $guns = 2;//3 after a turn of charging

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        public $priority = 5; //2d6+2 qualifies as "large" fighter weapon, I think! 

        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 

        function __construct($startArc, $endArc, $shots=1){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            //$this->intercept = $shots;
            /* ATM no special graphics for multi-barrel weapons, as they do not exist
            $ns = min(3,$shots); //no graphics for more than 3 weapons
            $this->iconPath = "lightfusionCannon$ns.png";
			*/
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            switch($this->turnsloaded){
                case 2:
                    $this->guns = 3;
                    break;
				default:
                    $this->guns = 2;
                    break;
            }            
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Can fire accelerated ROF for less shots:";  
			$this->data["Special"] .= "<br> - 1 turn: 2 shots"; 
			$this->data["Special"] .= "<br> - 2 turns: 3 shots"; 
			$this->data["Special"] .= "<br>REMINDER: as an Accelerator weapon, it will not be used for interception unless specifically ordered to do so!"; 
        }

        public function getDamage($fireOrder){        return Dice::d(6,2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;		
			$strippedSystem->guns = $this->guns;		
			return $strippedSystem;
		}

    }//endof class ftrPolarityCannon

        

	/*Molecular Slicer Beam - primary weapon of large Shadow ships. Light variety.*/
    class MolecularSlicerBeamL extends Raking{
		public $name = "MolecularSlicerBeamL";
        public $displayName = "Light Slicer Beam";
		public $iconPath = "MolecularSlicerBeamL.png";
		
        public $animation = "laser";
        public $animationColor = array(213, 0, 255); //thick, purple beam
    	public $animationExplosionScale = 0.2; //Manually set, as high-strength of Slicer makes split shots a little too large.

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
		public $firingModes = array(1 =>'Sweeping');
        public $loadingtime = 1;
		public $normalload = 3;
		
        public $rangePenalty = 0.33;//-1/3 hexes
        public $fireControl = array(2, 4, 6); // fighters, <=mediums, <=capitals 
		public $priority = 7;//heavy Raking weapon - with armor-ignoring 
		public $uninterceptable = true;
        public $intercept = 2;
		public $raking = 10;
        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 	
        
        //New variables to allow sweeping split shots.
        public $maxVariableShots = 8; //Default value, will be amended in front end anyway.
		public $canSplitShots = true; //Allows Firing Mode 1 to split shots.
		public $specialHitChanceCalculation	= true;	 //To update targeting tooltip in Front End               	

		//Slicers are usually THE weapons of Shadow ships - hence higher repair priority
		public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
        protected $overrideCallingRestrictions = true;
        private $damageDice = array();   
        private $maxDiceArray = array(1 => 4, 2 => 6, 3=> 8);
        private $uniqueIntercepts = array();		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){			
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 10;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	 		    
		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Uninterceptable. Ignores armor.";
			$this->data["Special"] .= "<br>May choose to split shots between multiple targets, allocating a number to d10 damage dice to each one.";
			$this->data["Special"] .= "<br>Each shot after the first attracts a cumulative -5% to hit modifier.";            
            $this->data["Special"] .= "<br>Can fire accelerated for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 4d10+4"; 
			$this->data["Special"] .= "<br> - 2 turns: 6d10+6"; 
			$this->data["Special"] .= "<br> - 3 turns: 8d10+8"; 
			$this->data["Special"] .= "<br>May spend 1d10 dice to gain -10 intercept, this is cumulative and suffers no degradation.";
			$this->data["Special"] .= "<br>Each self-intercept dice committed increases the number of shots Slicer may intercept, as well as the total interception amount.";                            
        }        

		/*Slicers ignore armor, except against hardened advanced armor*/
		public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){
		$tempArmor = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
			if ($system->hardAdvancedArmor) {
				return $tempArmor / 2;
			}else{
				return 0;
			}
        }

        public function beforeFiringOrderResolution($gamedata){ 
            $this->guns = 0;          
            $diceUsed = 0;
            $loadedDice = $this->maxDiceArray[$this->turnsloaded] ?? 4;            
            //$interceptsAllowed = 0;

            $maxDice = min($this->maxDiceArray[3], $loadedDice);              
            //Search fireOrders
            foreach ($this->fireOrders as $order) {
                //Add +1 gun for each order like this so that intercept algorithms allow it any selfIntercepts                
                $this->guns++;
            
                if($order->type == "normal"){ //Offensive shot.
                    $this->damageDice[$order->id] = $order->shots; //Dice number is passed from front end as $order->shots variable.
                    $diceUsed += $order->shots; //Dice number is passed from front end as $order->shots variable.                    
                    $order->shots = 1; //Set to a single shot after capturing dice used for this shot.
                }
            
                if($order->type == "selfIntercept"){ //Defensive shot.
                    $diceUsed += 1; //Add intercept orders  
                    //$interceptsAllowed += 1; //Add intercept orders                                      
                }                   
            } 

            if($diceUsed > 0){ //A shot was fired or Slicer was set to selfIntercept                            
                $spareDice = $maxDice - $diceUsed;
                while ($spareDice > 0){
                    $this->guns++; //Add a new gun to increase intercept shots by 1                                
                    $spareDice -= 1;                 
                } 
             
                /* //Removed because it was giving slicers a free intercept (e.g. without -5% hit chance), or it was adding -5% hitchance after firing orders committed.
                if($interceptsAllowed == 0){
                    //Offensive shot fired, but no selfINtercept declared.  Let's make one for the spare dice.
                    $ship = $this->getUnit();                      
                    $interceptFireOrder = new FireOrder( -1, "selfIntercept", $ship->id, $ship->id,
                        $this->id, -1, $gamedata->turn, 1,
                        0, 0, 1, 0, 0, null, null
                    );
                    $interceptFireOrder->addToDB = true;
                    $this->fireOrders[] = $interceptFireOrder;                                    
                } */                                      
            }   

        }

		public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder) {
            //If called shot on fighter, 0 called shot mod since Slicers can freely select which fighter to hit.		    
            $target = $gamedata->getShipById($fireOrder->targetid);        
            if($fireOrder->calledid !== -1 && $target instanceof FighterFlight) $this->calledShotMod = 0; 		    
            
            parent::calculateHitBase($gamedata, $fireOrder);
    
		    $hitmod = 0; // Initialize
		    // Find the position of THIS fireOrder in the array
		    $index = 0; // Initialize index

		    foreach ($this->fireOrders as $i => $splitShots) {
		        if ($splitShots->id === $fireOrder->id) {
		            $index = $i;
                    break;
		        }
            }    

		    // Use $index to adjust the hitmod or fireOrder->needed as necessary
		    // Example: Modify hitmod based on shot position                     
		    $hitmod = $index * 5; // Example logic (adjust as needed)             
		    $fireOrder->needed -= $hitmod; // Modify fireOrder->needed based on hitmod 		            
            
		}  

    public function getInterceptionMod($gamedata, $intercepted){
        //Slicers can freely combine their self-intercepts into a single strong intercept or multiple small ones. 
        //Therefore, two self-intercepts at -10 would always be -20 e.g. no degradation.        
        $allowedIntercepts = 0; //Counter for how many individual intercepts can be made

        foreach ($this->fireOrders as $order){ //Need to  extract normal types from list fo fireOrder objects.        
            if($order->type == "intercept"){ //A previous intercept
                if(!(in_array($order->targetid, $this->uniqueIntercepts, true))) { //An intercept order with a targetid we haven't saved yet.
                    $this->uniqueIntercepts[] = $order->targetid;
                }
            }else if($order->type == "selfIntercept"){
                $allowedIntercepts++;               
            }           
        }
        $noOfIntercepts = count($this->uniqueIntercepts);        

        if($noOfIntercepts <= $allowedIntercepts){ //Still headroom to intercept based on number of selfIntercept orders committed by player.
            return $this->intercept * 5;
        }else{
            return 0;
        }

    }//endof  getInterceptionMod

	public function getDamage($fireOrder) {
		$damDice = 0;        
        $nonDiceDam = 0;	
        $noOfShots = 0;

        //Count number of non-intercept orders    
        foreach ($this->fireOrders as $fOrder) {
            if($fOrder->type == "normal"){ //Only count offensive shots for this.
                $noOfShots++; //Add to shots total
                //Now retrieve saved details of dice used that we input in beforeFiringOrderResolution()
                foreach($this->damageDice as $id => $dice){
                    if($id == $fireOrder->id) $damDice = $dice;
                }
            }    
        }

	    // Determine base damage based on turns loaded          
        switch($this->turnsloaded){
            case 1:
                $nonDiceDam = 4;                    
                break;
            case 2:
                $nonDiceDam = 6;                     
                break;
            case 3: 
            default: //3 turns
                $nonDiceDam = 8;                     		
                break;
        } 
        
        $nonDiceDam = floor($nonDiceDam/$noOfShots); //Split non-dice damage equally between offensve shots this turn.          
        $finalDmg = Dice::d(10, $damDice) + $nonDiceDam;
    
        return $finalDmg;    		      
	}



		/*
		public function getDivider(){ //by how much damage should be divided - depends on mode
			$divider = 1;
			switch($this->firingMode){
					case 2:
							$divider = 3;
							break;
					case 3:
							$divider = 6;
							break;
					default:
							$divider = 1;
							break;
			}
			return $divider;
		}
		
		public function getDamage($fireOrder) {
			//$dmg = 0;
            $diceMod = 0;	
			$loadedtime = $this->turnsloaded;
            $noOfShots = 0;
  
            //Count number of non-intercept orders    
            foreach ($this->fireOrders as $fOrder) {
                if($fOrder->type === "selfIntercept"){
                    $diceMod++;
                }else if($fOrder->type !== "intercept"){
                    $noOfShots++; //Don't add autogenerated intercept shots.
                }    
            }
  
		    // Determine base damage based on turns loaded
            if($this->damageRoll <= 0){//Not rolled yet            
                switch($loadedtime){
                    case 1:
		                $dmg = Dice::d(10, 4 - $diceMod) + 4;   
                        $this->damageRoll = $dmg;                       
                        break;
                    case 2:
		                $dmg = Dice::d(10, 6 - $diceMod) + 6;
                        $this->damageRoll = $dmg;                          
                        break;
                    case 3: //if actual 3 turns - remember to set weapon to overkilling in Piercing mode!
                    default: //3 turns
		                $dmg = Dice::d(10, 8 - $diceMod) + 8;	
                        $this->damageRoll = $dmg;                          		
                        break;
                }
            }    

		    // Adjust damage based on firing mode
            $finalDmg = 0;           
		    if ($noOfShots > 1) {
		        $finalDmg = floor($this->damageRoll / $noOfShots);
		    }else{
                $finalDmg = $this->damageRoll;               
            }

		    return $finalDmg;    		    
			   
		}
*/                    

        public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 8 ;
                    break;
                case 2:
                    $this->minDamage = 12 ;  
                    break;
                default:
                    $this->minDamage = 16 ;  
                    break;
            }
 
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 44 ;
                    break;
                case 2:
                    $this->maxDamage = 66 ;  
                    break;
                default:
                    $this->maxDamage = 88 ;  
                    break;
            }
  
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;
			$strippedSystem->overrideCallingRestrictions = $this->overrideCallingRestrictions;	            				
			return $strippedSystem;
		}

	}//endof class MolecularSlicerBeamL
	

	/*Molecular Slicer Beam - primary weapon of large Shadow ships. Regular variety.*/
    class MolecularSlicerBeamM extends MolecularSlicerBeamL{
		public $name = "MolecularSlicerBeamM";
        public $displayName = "Slicer Beam";
		public $iconPath = "MolecularSlicerBeamM.png";		   
    	public $animationExplosionScale = 0.25; //Manually set, as high-strength of Slicer makes split shots a little too large.
		public $firingModes = array(1 =>'Sweeping');		
        public $rangePenalty = 0.33;//-1/3 hexes
        public $fireControl = array(4, 6, 8); // fighters, <=mediums, <=capitals 
		public $priority = 7;//heavy Raking weapon - with armor-ignoring 

		public $raking = 15;
        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 

        //New variables to allow sweeping split shots.
        public $maxVariableShots = 16; //Default value, will be amended in front end anyway.
		public $canSplitShots = true; //Allows Firing Mode 1 to split shots.
        public $canSplitShotsArray = array(1=>true);
		public $specialHitChanceCalculation	= true;	 //To update targeting tooltip in Front End 		
        private $damageDice = array();   		
        private $maxDiceArray = array(1 => 8, 2 => 12, 3=> 16);	
        private $uniqueIntercepts = array();	

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){			
            if ( $maxhealth == 0 ) $maxhealth = 15;
            if ( $powerReq == 0 ) $powerReq = 12;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }	
	    
		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Uninterceptable. Ignores armor. 15-point rakes.";  
			$this->data["Special"] .= "<br>May choose to split shots between multiple targets, allocating a number to d10 damage dice to each one.";
			$this->data["Special"] .= "<br>Each shot after the first attracts a cumulative -5% to hit modifier.";   
			$this->data["Special"] .= "<br>Can fire accelerated for less damage:"; 
            $this->data["Special"] .= "<br> - 1 turn: 8d10+12"; 
			$this->data["Special"] .= "<br> - 2 turns: 12d10+24"; 
			$this->data["Special"] .= "<br> - 3 turns: 16d10+36";
			$this->data["Special"] .= "<br>May spend 1d10 dice to gain -10 intercept, this is cumulative and suffers no degradation.";
			$this->data["Special"] .= "<br>Each self-intercept dice committed increases the number of shots Slicer may intercept, as well as the total interception amount.";               
        }

        public function beforeFiringOrderResolution($gamedata){ 
            $this->guns = 0;          
            $diceUsed = 0;
            $loadedDice = $this->maxDiceArray[$this->turnsloaded] ?? 8;            
            //$interceptsAllowed = 0;
            $maxDice = min($this->maxDiceArray[3], $loadedDice);               

            //Search fireOrders
            foreach ($this->fireOrders as $order) {
                //Add +1 gun for each order like this so that intercept algorithms allow it any selfIntercepts                
                $this->guns++;
            
                if($order->type == "normal"){ //Offensive shot.
                    $this->damageDice[$order->id] = $order->shots; //Dice number is passed from front end as $order->shots variable.
                    $diceUsed += $order->shots; //Dice number is passed from front end as $order->shots variable.                    
                    $order->shots = 1; //Set to a single shot after capturing dice used for this shot.
                }
            
                if($order->type == "selfIntercept"){ //Defensive shot.
                    $diceUsed += 1; //Add intercept orders  
                    //$interceptsAllowed += 1; //Add intercept orders                                      
                }                   
            } 

            if($diceUsed > 0){ //A shot was fired or Slicer was set to selfIntercept                            
                $spareDice = $maxDice - $diceUsed;
                while ($spareDice > 0){
                    $this->guns++; //Add a new gun to increase intercept shots by 1                                
                    $spareDice -= 1;                 
                } 
             
                /*
                if($interceptsAllowed == 0){
                //Offensive shot fired, but no selfINtercept declared.  Let's make one for the spare dice.
                    $ship = $this->getUnit();                      
                    $interceptFireOrder = new FireOrder( -1, "selfIntercept", $ship->id, $ship->id,
                        $this->id, -1, $gamedata->turn, 1,
                        0, 0, 1, 0, 0, null, null
                    );
                    $interceptFireOrder->addToDB = true;
                    $this->fireOrders[] = $interceptFireOrder;                                
                } 
                */                                          
            }    

        }

    public function getInterceptionMod($gamedata, $intercepted){
        //Slicers can freely combine their self-intercepts into a single strong intercept or multiple small ones. 
        //Therefore, two self-intercepts at -10 would always be -20 e.g. no degradation.        
        $allowedIntercepts = 0; //Counter for how many individual intercepts can be made

        foreach ($this->fireOrders as $order){ //Need to  extract normal types from list fo fireOrder objects.        
            if($order->type == "intercept"){ //A previous intercept
                if(!(in_array($order->targetid, $this->uniqueIntercepts, true))) { //An intercept order with a targetid we haven't saved yet.
                    $this->uniqueIntercepts[] = $order->targetid;
                }
            }else if($order->type == "selfIntercept"){
                $allowedIntercepts++;               
            }           
        }
        $noOfIntercepts = count($this->uniqueIntercepts);        

        if($noOfIntercepts <= $allowedIntercepts){ //Still headroom to intercept based on number of selfIntercept orders committed by player.
            return $this->intercept * 5;
        }else{
            return 0;
        }

    }//endof  getInterceptionMod

	public function getDamage($fireOrder) {
		$damDice = 0;        
        $nonDiceDam = 0;	
        $noOfShots = 0;

        //Count number of non-intercept orders    
        foreach ($this->fireOrders as $fOrder) {
            if($fOrder->type == "normal"){ //Only count offensive shots for this.
                $noOfShots++; //Add to shots total
                //Now retrieve saved details of dice used that we input in beforeFiringOrderResolution()
                foreach($this->damageDice as $id => $dice){
                    if($id == $fireOrder->id) $damDice = $dice;
                }
            }    
        }
    
	    // Determine base damage based on turns loaded          
        switch($this->turnsloaded){
            case 1:
                $nonDiceDam = 12;                    
                break;
            case 2:
                $nonDiceDam = 24;                     
                break;
            case 3: 
            default: //3 turns
                $nonDiceDam = 36;                     		
                break;
        }

        $nonDiceDam = floor($nonDiceDam/$noOfShots); //Split non-dice damage equally between offensve shots this turn.       
        $finalDmg = Dice::d(10, $damDice) + $nonDiceDam;
    
        return $finalDmg;    		      
	}

    
        public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 8+12 ;
                    break;
                case 2:
                    $this->minDamage = 12+24 ;  
                    break;
                default:
                    $this->minDamage = 16+36 ;  
                    break;
            }   
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 80+12 ;
                    break;
                case 2:
                    $this->maxDamage = 120+24 ;  
                    break;
                default:
                    $this->maxDamage = 160+36 ;  
                    break;
            }
		}
		
	}//endof class MolecularSlicerBeamM


    class MolecularSlicerBeamH extends MolecularSlicerBeamL{
		public $name = "MolecularSlicerBeamH";
        public $displayName = "Heavy Slicer Beam";
		public $iconPath = "MolecularSlicerBeamH.png";		   
    	public $animationExplosionScale = 0.3; //Manually set, as high-strength of Slicer makes split shots a little too large.		
        public $firingModes = array(1=>'Piercing', 2 =>'Raking');        
	    public $modeLetters = 1;	
        public $rangePenalty = 0.33;//-1/3 hexes
        public $fireControl = array(0, 2, 4); // fighters, <=mediums, <=capitals 
        public $fireControlArray = array(1=>array(0,2,4), 2=>array(4, 6, 8));
		public $priority = 2;//primary mode being Piercing! otherwise heavy Raking weapon - with armor-ignoring 
		public $priorityArray = array(1=>2, 2=>3);        

		public $raking = 15;
        public $damageType = "Piercing"; 
        public $weaponClass = "Molecular"; 
        public $startArcArray = array(); 
        public $endArcArray = array();        
        protected $splitArcs = false; //Used to tell Front End that weapon has 2 or more separate arcs, passed manually via stripForJson()
        
        //New variables to allow sweeping split shots.
        public $maxVariableShots = 24; //Default value, will be amended in front end anyway.
		public $canSplitShots = true; //Allows weapon to split shots.
        public $canSplitShotsArray = array(1=>true, 2=>true);        
	    protected $multiModeSplit = true; //Can split shots across different modes
		public $specialHitChanceCalculation	= true;	 //To update targeting tooltip in Front End
        private $damageDice = array();  
        private $maxDiceArray = array(1=> 8, 2=> 16, 3=> 24);         			
        private $uniqueIntercepts = array();	


		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $startArc2 = null, $endArc2 = null){           			
            if ( $maxhealth == 0 ) $maxhealth = 18;
            if ( $powerReq == 0 ) $powerReq = 16;     

            if($startArc2 !== null || $endArc2 !== null){      
                $this->startArcArray[0] = $startArc; //Set rear arcs manually
                $this->endArcArray[0] = $endArc; 
                $this->startArcArray[1] = $startArc2;
                $this->endArcArray[1] = $endArc2; 
                $this->splitArcs = true;                          
            }                                   
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		//if charged for 3 turns and mode is Piercing - set doOverkill; otherwise unset it
		public function changeFiringMode($newMode){
			parent::changeFiringMode($newMode); 
			if (($this->turnsloaded >=3) && $this->firingMode ==1){                
				$this->doOverkill=true;
			}else{
				$this->doOverkill=false;
			}				
		}
	    
		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);                 
			$this->data["Special"] = "Uninterceptable. Ignores armor. 15-point rakes.";
			$this->data["Special"] .= "<br>Full power (3 turns armed) can be fired ONLY in Piercing mode, but it will become Piercing(Standard), with ability to overkill.  Raking Mode can still be used (at 2 turns output)";           
			$this->data["Special"] .= "<br>May choose to split shots between multiple targets, allocating a number to d10 damage dice to each one.";
			$this->data["Special"] .= "<br>Each shot after the first attracts a cumulative -5% to hit modifier.";   			 
			$this->data["Special"] .= "<br>Can fire accelerated for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 8d10+12"; 
			$this->data["Special"] .= "<br> - 2 turns: 16d10+24"; 
			$this->data["Special"] .= "<br> - 3 turns: 24d10+36";
			$this->data["Special"] .= "<br>May spend 1d10 dice to gain -10 intercept, this is cumulative and suffers no degradation.";
			$this->data["Special"] .= "<br>Each self-intercept dice committed increases the number of shots Slicer may intercept, as well as the total interception amount.";             
        }

        public function beforeFiringOrderResolution($gamedata){ 
            $this->guns = 0;          
            $diceUsed = 0;
            $maxDice = 0;         
        
            $loadedDice = $this->maxDiceArray[$this->turnsloaded] ?? 8;

            if ($this->firingMode == 2) {                
                $maxDice = min($this->maxDiceArray[2], $loadedDice);
            } else {
                $maxDice = min($this->maxDiceArray[3], $loadedDice);
            }

            //Search fireOrders
            foreach ($this->fireOrders as $order) {
                //Add +1 gun for each order like this so that intercept algorithms allow it any selfIntercepts                
                $this->guns++;
            
                if($order->type == "normal"){ //Offensive shot.
                    $this->damageDice[$order->id] = $order->shots; //Dice number is passed from front end as $order->shots variable.
                    $diceUsed += $order->shots; //Dice number is passed from front end as $order->shots variable.                    
                    $order->shots = 1; //Set to a single shot after capturing dice used for this shot.
                }
            
                if($order->type == "selfIntercept"){ //Defensive shot.
                    $diceUsed += 1; //Add intercept orders                                      
                }                   
            } 

            if($diceUsed > 0){ //A shot was fired or Slicer was set to selfIntercept                            
                $spareDice = $maxDice - $diceUsed;
                while ($spareDice > 0){
                    $this->guns++; //Add a new gun to increase intercept shots by 1                                
                    $spareDice -= 1;                 
                }                                     
            }   
 
        }        

		public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder) {
            //If called shot on fighter, 0 called shot mod since Slicers can freely select which fighter to hit.		    
            $target = $gamedata->getShipById($fireOrder->targetid);        
            if($fireOrder->calledid !== -1 && $target instanceof FighterFlight) $this->calledShotMod = 0; 

		    Weapon::calculateHitBase($gamedata, $fireOrder); //Don't call direct parent, duplicates some hitmods
    
		    $hitmod = 0; // Initialize
		    // Find the position of THIS fireOrder in the array
		    $index = 0; // Initialize index
		    foreach ($this->fireOrders as $i => $splitShots) {
		        if ($splitShots->id === $fireOrder->id) {
		            $index = $i;
		            break; // Exit the loop
		        }
		    }
		    // Use $index to adjust the hitmod or fireOrder->needed as necessary
		    // Example: Modify hitmod based on shot position
		    $hitmod = $index * 5; // Example logic (adjust as needed) 
		    $fireOrder->needed -= $hitmod; // Modify fireOrder->needed based on hitmod 		     
            
            //If piercing mode used on less than full charge, should use Raking Fire Control, so add +20%
            //if($this->turnsloaded < 3 && ($fireOrder->firingMode == 1 || $fireOrder->firingMode == 3)){
            if($this->turnsloaded < 3 && $fireOrder->firingMode == 1){                
                $fireOrder->needed += 20; // Modify fireOrder->needed based on hitmod              
            }            
            
		}  

    public function getInterceptionMod($gamedata, $intercepted){
        //Slicers can freely combine their self-intercepts into a single strong intercept or multiple small ones. 
        //Therefore, two self-intercepts at -10 would always be -20 e.g. no degradation.        
        $allowedIntercepts = 0;

        foreach ($this->fireOrders as $order){ //Need to  extract normal types from list fo fireOrder objects.        
            if($order->type == "intercept"){ //A previous intercept
                if(!(in_array($order->targetid, $this->uniqueIntercepts, true))) { //An intercept order with a targetid we haven't saved yet.
                    $this->uniqueIntercepts[] = $order->targetid;
                }
            }else if($order->type == "selfIntercept"){
                $allowedIntercepts++;               
            }           
        }
        $noOfIntercepts = count($this->uniqueIntercepts);        

        if($noOfIntercepts <= $allowedIntercepts){ //Still headroom to intercept based on number of selfIntercept orders committed by player.
            return $this->intercept * 5;
        }else{
            return 0;
        }

    }//endof  getInterceptionMod   

	public function getDamage($fireOrder) {
		$damDice = 0;        
        $nonDiceDam = 0;	
        $noOfShots = 0;
        $loadedtime = $this->turnsloaded;

		if($this->firingMode == 2){//cannot use 3-turns power for shots other than Piercing            
			$loadedtime = min(2,$loadedtime);
		}

        //Count number of non-intercept orders    
        foreach ($this->fireOrders as $fOrder) {
            if($fOrder->type == "normal"){ //Only count offensive shots for this.
                $noOfShots++; //Add to shots total
                //Now retrieve saved details of dice used that we input in beforeFiringOrderResolution()
                foreach($this->damageDice as $id => $dice){
                    if($id == $fireOrder->id) $damDice = $dice;
                }
            }    
        }
     
	    // Determine base damage based on turns loaded          
        switch($loadedtime){
            case 1:
                $nonDiceDam = 12;                    
                break;
            case 2:
                $nonDiceDam = 24;                     
                break;
            case 3: 
            default: //3 turns
                $nonDiceDam = 36;                     		
                break;
        } 
           
        $nonDiceDam = floor($nonDiceDam/$noOfShots); //Split non-dice damage equally between offensve shots this turn.        
        $finalDmg = Dice::d(10, $damDice) + $nonDiceDam;
    
        return $finalDmg;    		      
	}


        public function setMinDamage(){
			$loadedtime = $this->turnsloaded;

			if($this->firingMode == 2){//cannot use 3-turns power for shots other than Piercing                
				$loadedtime = min(2,$loadedtime);
			}
            switch($loadedtime){
                case 1:
                    $this->minDamage = 8+12 ;
                    break;
                case 2:
                    $this->minDamage = 16+24 ;  
                    break;
                default:
                    $this->minDamage = 24+36 ;  
                    break;
            } 
		}
             
        public function setMaxDamage(){
			$loadedtime = $this->turnsloaded;

			if($this->firingMode == 2){//cannot use 3-turns power for shots other than Piercing                
				$loadedtime = min(2,$loadedtime);
			}
            switch($loadedtime){
                case 1:
                    $this->maxDamage = 80+12 ;
                    break;
                case 2:
                    $this->maxDamage = 160+24 ;  
                    break;
                default:
                    $this->maxDamage = 240+36 ;  
                    break;
            }
		}


    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->multiModeSplit = $this->multiModeSplit;
        $strippedSystem->splitArcs = $this->splitArcs;        						                                        
        return $strippedSystem;
	}	        
		
	}//endof class MolecularSlicerBeamH


    /*Shadow "export" light weapon*/
    class MultiphasedCutterL extends Weapon{        
        public $name = "MultiphasedCutterL";
        public $iconPath = "MultiphasedCutterL.png";
        public $displayName = "Light Multiphased Cutter";
        public $animation = "laser";
        public $animationColor = array(9, 0, 255); //animate as a dark blue beam, like from Shadow Omega light weapons fire in the show

        public $priority = 5; //medium Standard, with average dmg of 13
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
        
        public $loadingtime = 1;
        public $intercept = 2;
                
        public $rangePenalty = 0.5;//-1/2 hexes
        public $fireControl = array(6, 3, 3); // fighters, <mediums, <capitals 
        
        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
                
        public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }    
    }//endof class MultiphasedCutterL



    /*Shadow Destroyer defensive weapon - essentially triple mount of Light Multiphased Cutters (way more power efficient)*/
    class MultiphasedCutter extends MultiphasedCutterL {        
        public $name = "MultiphasedCutter";
        public $iconPath = "MultiphasedCutter.png";
        public $displayName = "Multiphased Cutter";
        
        public $guns = 3; 
        public $canSplitShots = false; //Allows Firing Mode 2 to split shots.
		public $canSplitShotsArray = array(1=>false, 2=>true );
        
        public $firingModes = array( 1 => "Normal", 2=> "Split");        
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Can use 'Split' Firing Mode to target different enemy units.";
}


    }//endof class MultiphasedCutter


 

?>
