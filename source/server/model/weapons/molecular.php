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
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+15;   }
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
    }//endof class LightMolecularDisrupterHandler



	/*fighter-sized Polarity Cannon - primary weapon of Shadow fighters*/
    class FtrPolarityCannon extends LinkedWeapon{
        public $name = "FtrPolarityCannon";
		public $iconPath = "ftrPolarityCannon.png";
        public $displayName = "Polarity Cannon";
        public $animation = "trail";
        public $animationColor = array(45, 180, 255);
        public $trailColor = array(45, 180, 255);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

        public $intercept = 0; //it seems that this weapon cannot intercept!
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
            /* ATM no special graphics for multi-barrel weapons, as they do to exist
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
        public $animationWidth = 4.5;
        public $animationWidth2 = 0.8;
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
        
		
		public $firingModes = array(1 =>'Raking', 2=>'3Split', 3=>'6Split');
		
        public $loadingtime = 1;
		public $normalload = 3;
		
        public $rangePenalty = 0.33;//-1/3 hexes
        public $fireControl = array(2, 4, 6); // fighters, <=mediums, <=capitals 
        public $fireControlArray = array(1=>array(2, 4, 6), 2=>array(1, 3, 5), 3=>array(0, 2, 4) );
        public $gunsArray = array(1=>1, 2=>3, 3=>6 );

		public $priority = 7;//heavy Raking weapon - with armor-ignoring 
		public $uninterceptable = true;

		public $raking = 10;
        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 
		
		
		//Slicers are usually THE weapons of SHadow ships - hence higher repair priority
		public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){			
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 10;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		/*Slicers ignore armor...*/
		public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){
			return 0;
        }
	
	    
		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Uninterceptable. Ignores armor.";  
			$this->data["Special"] .= "<br>Can fire accelerated for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 4d10+4"; 
			$this->data["Special"] .= "<br> - 2 turns: 6d10+6"; 
			$this->data["Special"] .= "<br> - 3 turns: 8d10+8"; 
			$this->data["Special"] .= "<br>Can split fire into 3 separate shots (at -1 FC) or 6 shots (-2 FC). Each shot will do appropriate portion of regular damage (rolled separately, rounded down).";  
		}
		
		
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
		

		public function getDamage($fireOrder){
			$dmg = 0;
            switch($this->turnsloaded){
                case 1:
                    $dmg = Dice::d(10,4)+4;
					break;
                case 2:
                    $dmg = Dice::d(10,6)+6;
					break;
				default: //3 turns
                    $dmg = Dice::d(10,8)+8;
					break;
            }
			$dmg = floor($dmg/$this->getDivider());
			return $dmg;
		}
        
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
			$this->minDamage = floor($this->minDamage/$this->getDivider()) ;   
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
			$this->maxDamage = floor($this->maxDamage/$this->getDivider()) ;   
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				
			return $strippedSystem;
		}

	}//endof class MolecularSlicerBeamL



	/*Molecular Slicer Beam - primary weapon of large Shadow ships. Regular variety.*/
    class MolecularSlicerBeamM extends MolecularSlicerBeamL{
		public $name = "MolecularSlicerBeamM";
        public $displayName = "Slicer Beam";
		public $iconPath = "MolecularSlicerBeamM.png";		   
		
		public $firingModes = array(1 =>'Raking', 2=>'3Split', 3=>'6Split', 4=>'9Split');
		
        public $rangePenalty = 0.33;//-1/3 hexes
        public $fireControl = array(4, 6, 8); // fighters, <=mediums, <=capitals 
        public $fireControlArray = array(1=>array(4, 6, 8), 2=>array(3, 5, 7), 3=>array(2, 4, 6), 4=>array(1, 3, 5) );
        public $gunsArray = array(1=>1, 2=>3, 3=>6, 4=>9 );

		public $priority = 7;//heavy Raking weapon - with armor-ignoring 

		public $raking = 15;
        public $damageType = "Raking"; 
        public $weaponClass = "Molecular"; 
		
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){			
            if ( $maxhealth == 0 ) $maxhealth = 15;
            if ( $powerReq == 0 ) $powerReq = 12;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
	
	    
		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Uninterceptable. Ignores armor. 15-point rakes.";  
			$this->data["Special"] .= "<br>Can fire accelerated for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 8d10+12"; 
			$this->data["Special"] .= "<br> - 2 turns: 12d10+24"; 
			$this->data["Special"] .= "<br> - 3 turns: 16d10+36"; 
			$this->data["Special"] .= "<br>Can split fire into 3 separate shots (at -1 FC), 6 shots (-2 FC) or 9 shots (-3 FC). Each shot will do appropriate portion of regular damage (rolled separately, rounded down).";  
		}
		
		
		public function getDivider(){ //by how much damage should be divided - depends on mode
			$divider = 1;
			switch($this->firingMode){
					case 2:
							$divider = 3;
							break;
					case 3:
							$divider = 6;
							break;
					case 4:
							$divider = 9;
							break;
					default:
							$divider = 1;
							break;
			}
			return $divider;
		}
		

		public function getDamage($fireOrder){
			$dmg = 0;
            switch($this->turnsloaded){
                case 1:
                    $dmg = Dice::d(10,8)+12;
					break;
                case 2:
                    $dmg = Dice::d(10,12)+24;
					break;
				default: //3 turns
                    $dmg = Dice::d(10,16)+36;
					break;
            }
			$dmg = floor($dmg/$this->getDivider());
			return $dmg;
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
			$this->minDamage = floor($this->minDamage/$this->getDivider()) ;   
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
			$this->maxDamage = floor($this->maxDamage/$this->getDivider()) ;   
		}
		
	}//endof class MolecularSlicerBeamM




	/*Molecular Slicer Beam - primary weapon of large Shadow ships. Heavy variety (found only on slow-grown primordial ships).*/
    class MolecularSlicerBeamH extends MolecularSlicerBeamL{
		public $name = "MolecularSlicerBeamH";
        public $displayName = "Heavy Slicer Beam";
		public $iconPath = "MolecularSlicerBeamH.png";		   
		
		public $firingModes = array(1=>'Piercing', 2 =>'Raking', 3=>'3Split', 4=>'6Split', 5=>'9Split');
		
        public $rangePenalty = 0.33;//-1/3 hexes
        public $fireControl = array(0, 2, 4); // fighters, <=mediums, <=capitals 
        public $fireControlArray = array(1=>array(0,2,4), 2=>array(4, 6, 8), 3=>array(3, 5, 7), 4=>array(2, 4, 6), 5=>array(1, 3, 5) );
        public $gunsArray = array(1=>1, 2=>1, 3=>3, 4=>6, 5=>9 );

		public $priority = 2;//primary mode being Piercing! otherwise heavy Raking weapon - with armor-ignoring 
		public $priorityArray = array(1=>2, 2=>7, 3=>7, 4=>7, 5=>7 );

		public $raking = 15;
        public $damageType = "Piercing"; 
        public $damageTypeArray = array(1=>"Piercing", 2=>"Raking", 3=>"Raking", 4=>"Raking", 5=>"Raking" );
        public $weaponClass = "Molecular"; 
		
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){			
            if ( $maxhealth == 0 ) $maxhealth = 18;
            if ( $powerReq == 0 ) $powerReq = 16;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		/*if charged for 3 turns and mode is Piercing - set doOverkill; otherwise unset it*/
		public function changeFiringMode($newMode){
			parent::changeFiringMode($newMode); 
			if (($this->turnsloaded >=3) && ($this->firingMode==1)){
				$this->doOverkill=true;
			}else{
				$this->doOverkill=false;
			}				
		}
	
	    
		public function setSystemDataWindow($turn){			
			parent::setSystemDataWindow($turn);   
			$this->data["Special"] = "Uninterceptable. Ignores armor. 15-point rakes.";  
			$this->data["Special"] = "<br>Full (3 turns arming) power can be unleashed only in Piercing mode, but it will become Piercing(Standard), with ability to overkill.";  
			$this->data["Special"] = "<br>Raking shot can still be used (at 2 turns damage output).";  
			$this->data["Special"] .= "<br>Can fire accelerated for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 8d10+12"; 
			$this->data["Special"] .= "<br> - 2 turns: 16d10+24"; 
			$this->data["Special"] .= "<br> - 3 turns: 24d10+36"; 
			$this->data["Special"] .= "<br>Can split Raking fire into 3 separate shots (at -1 FC), 6 shots (-2 FC) or 9 shots (-3 FC). Each shot will do appropriate portion of regular damage (rolled separately, rounded down).";  
		}
		
		
		public function getDivider(){ //by how much damage should be divided - depends on mode
			$divider = 1;
			switch($this->firingMode){
					case 3:
							$divider = 3;
							break;
					case 4:
							$divider = 6;
							break;
					case 5:
							$divider = 9;
							break;
					default:
							$divider = 1;
							break;
			}
			return $divider;
		}
		

		public function getDamage($fireOrder){
			$dmg = 0;
			$loadedtime = $this->turnsloaded;
			if($this->firingMode > 1){//cannot use 3-turns power for shots other than Piercing
				$loadedtime = min(2,$loadedtime);
			}
            switch($loadedtime){
                case 1:
                    $dmg = Dice::d(10,8)+12;
					break;
                case 2:
                    $dmg = Dice::d(10,16)+24;
					break;
				case 3: //if actual 3 turns - remember to set weapon to overkilling in Piercing mode!
				default: //3 turns
                    $dmg = Dice::d(10,24)+36;
					break;
            }
			$dmg = floor($dmg/$this->getDivider());
			return $dmg;
		}
        
        public function setMinDamage(){
            switch($this->turnsloaded){
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
			$this->minDamage = floor($this->minDamage/$this->getDivider()) ;   
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
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
			$this->maxDamage = floor($this->maxDamage/$this->getDivider()) ;   
		}
		
	}//endof class MolecularSlicerBeamH




    /*Shadow "export" light weapon*/
    class MultiphasedCutterL extends Weapon{        
        public $name = "MultiphasedCutterL";
        public $iconPath = "MultiphasedCutterL.png";
        public $displayName = "Light Multiphased Cutter";
        public $animation = "laser";
        public $animationColor = array(9, 0, 255); //animate as a dark blue beam, like from Shadow Omega light weapons fire in the show
        public $animationExplosionScale = 0.18;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
        public $priority = 6; //medium Standard, with average dmg of 13
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
        
        public $loadingtime = 1;
                
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
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
                
        public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }    
    }//endof class MultiphasedCutter


?>