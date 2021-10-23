<?php
/*file for Star Trek universe weapons*/


/*output is not recharge time (like for B5 warp drives), but rather impulse rating (eg. how much thrust is derived from this system...)*/
class TrekWarpDrive extends JumpEngine{
    //public $name = "TrekWarpDrive";
    public $displayName = "Nacelle";
    public $iconPath = "WarpDrive.png";
    public $primary = true;
    
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    public $possibleCriticals = array( //reduced output reduces available thrust
        12=>"OutputReduced1",
        24=>"OutputReduced2"
	);
	
    function __construct($armour, $maxhealth, $powerReq, $output){
        parent::__construct($armour, $maxhealth, $powerReq, $output);   
			$this->output = $output; //as JumpEngine doesn't show output really...
    }
	
     public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
        $this->data["Special"] = "Nacelle is an FTL Drive, as well as element of sublight drive system. Provides thrust in amount equal to system rating."; 
    }
}


//ImpulseDrive - basically an engine with rating calculated from ships' WarpDrive

//remember to plug WarpDrives to the ImpulseDrive at design stage!
class TrekImpulseDrive extends Engine{
	//public $iconPath = "engineTechnical.png";
	public $iconPath = "engine.png";
    public $name = "engine";
    public $displayName = "Engine";
    public $primary = true;
    public $isPrimaryTargetable = false;
    //public $boostable = false;//CAN boost!
    public $outputType = "thrust";
	public $baseOutput = 0;
	
	private $warpDrives = array();
	
	
    public $possibleCriticals = array( //as actual output is minima, so should be crits!	     
        16=>"OutputReduced1",
        22=>"OutputReduced2",
        28=>"ForcedOfflineOneTurn"
	/*original Engine crits:    
        15=>"OutputReduced2",
        21=>"OutputReduced4",
        28=>"ForcedOfflineOneTurn"
	*/
    );
	
    
    function __construct($armour, $maxhealth, $powerReq, $output, $boostEfficiency){
        parent::__construct($armour, $maxhealth, $powerReq, $output, $boostEfficiency ); //($armour, $maxhealth, $powerReq, $output, $boostEfficiency
		$this->baseOutput = $output;
    }
    
	function addThruster($thruster){
		if($thruster) $this->warpDrives[] = $thruster;
	}
	
	
	public function setSystemDataWindow($turn){
		//$this->output = $this->getOutput();	
		parent::setSystemDataWindow($turn); 	
		$this->output = $this->getOutput();	
		$this->data["Efficiency"] = $this->boostEfficiency;
		$this->data["Special"] = "Impulse Drive - basically an Engine with basic output calculated from Nacelle outputs in addition to its own.";  
	}
	
	
    public function getOutput(){ 
		$this->output = $this->baseOutput;//reset base value!
		$output = max(0,parent::getOutput());//criticals cannot bring base output below 0
		//count thrust from Warp Drives
		foreach($this->warpDrives as $thruster){
			$output += max(0,$thruster->getOutput());//cannot provide negative output!
		}			
        return $output;        
    } //endof function getOutput
	
	
	public function stripForJson(){
		//$this->output = $this->getOutput();	
        $strippedSystem = parent::stripForJson();
        $strippedSystem->output = $this->getOutput();	
		//$strippedSystem->data = $this->data;	
        return $strippedSystem;
    }
	
}//endof class TrekImpulseDrive




class TrekLtPhaseCannon extends Raking{
		public $name = "TrekLtPhaseCannon";
        public $displayName = "Light Phase Cannon";
        public $iconPath = "TrekLightPhaseCannon.png";
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
		public $animationExplosionScale = 0.25;
		public $animationWidth = 3;
		public $animationWidth2 = 0.2;

        public $raking = 6;
        
        public $intercept = 2;
		public $priority = 6; //light Raking		
		
        public $loadingtime = 1;
		
        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 3);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Raking");

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 4;
			if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Does damage in raking mode (6)";
	}
	
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

}//end of class Trek Light Phase Cannon


/*super-heavy fighter weapon*/
    class TrekFtrPhaseCannon extends Weapon{
        public $name = "TrekFtrPhaseCannon";
        public $displayName = "Light Phase Cannon";
        public $iconPath = "TrekLightPhaseCannon.png";
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
        public $animationWidth = 3;
        public $animationWidth2 = 0.2;

        public $loadingtime = 1;
        public $raking = 6;
//        public $exclusive = true;
        public $intercept = 2;
        public $priority = 8; //Raking weapon
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
 
		public $damageType = 'Raking'; 
		public $weaponClass = "Particle"; 
	    
        function __construct($startArc, $endArc, $damagebonus){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
				$this->data["Special"] = "Does damage in raking mode (6)";
		}
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){   return  $this->minDamage = 5 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 14 ;      }
		
    }  //end of class Trek Fighter Light Phase Cannon


class TrekPhaseCannon extends Raking{
		public $name = "TrekPhaseCannon";
        public $displayName = "Phase Cannon";
        public $iconPath = "TrekPhaseCannon.png";
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
		public $animationExplosionScale = 0.25;
		public $animationWidth = 4;
		public $animationWidth2 = 0.3;

        public $raking = 8;
        
        public $intercept = 2;
		public $priority = 8; //light Raking		
		
        public $loadingtime = 1;
		public $normalload = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 2, 2);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Raking");

	 	public function getInterceptRating($turn){
			return 2;
		}

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
			$this->data["Special"] .= "Can fire accelerated ROF for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 1d10+3"; 
			$this->data["Special"] .= "<br> - 2 turns: 2d10+12"; 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1:
                	return Dice::d(10)+3;
			    	break;
            	default:
                	return Dice::d(10,2)+12;
			    	break;
        	}
		}

 		public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 4 ;
                    break;
                default:
                    $this->minDamage = 14 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 13 ;
                    break;
                default:
                    $this->maxDamage = 32 ;  
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
			return $strippedSystem;
		}

}//end of class Trek Phase Cannon



class TrekHvyPhaseCannon extends Raking{
		public $name = "TrekHvyPhaseCannon";
        public $displayName = "Heavy Phase Cannon";
        public $iconPath = "TrekHeavyPhaseCannon.png";
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
		public $animationExplosionScale = 0.25;
		public $animationWidth = 5;
		public $animationWidth2 = 0.4;

        public $raking = 10;
        
        public $intercept = 1;
		public $priority = 8; 		
		
        public $loadingtime = 2;
		public $normalload = 3;
		
        public $rangePenalty = 0.33;
        public $fireControl = array(0, 2, 3);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Raking");

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 8;
			if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
			$this->data["Special"] .= "Can fire accelerated ROF for less damage:";  
			$this->data["Special"] .= "<br> - 2 turns: 2d10+8"; 
			$this->data["Special"] .= "<br> - 3 turns: 3d10+12"; 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1:
                	return Dice::d(10,2)+8;
			    	break;
            	default:
                	return Dice::d(10,3)+12;
			    	break;
        	}
		}

 		public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 10 ;
                    break;
                default:
                    $this->minDamage = 28 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 15 ;
                    break;
                default:
                    $this->maxDamage = 42 ;  
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
			return $strippedSystem;
		}

}//end of class Trek Heavy Phase Cannon




class TrekPhaser extends Raking{
		public $name = "TrekPhaser";
        public $displayName = "Phaser";
        public $iconPath = "mediumLaser.png"; //Laser icon - just so it's clear it needs to be changed!
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
		public $animationExplosionScale = 0.3;
		public $animationWidth = 4;
		public $animationWidth2 = 0.3;

        public $raking = 10;
        
        public $intercept = 2;
		public $priority = 8; //light Raking		
		
        public $loadingtime = 1;
		public $normalload = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(3, 3, 3);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Raking");

	 	public function getInterceptRating($turn){
			return 2;
		}

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 7;
			if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
			$this->data["Special"] .= "Can fire accelerated ROF for less damage:";  
			$this->data["Special"] .= "<br> - 1 turn: 1d10+4"; 
			$this->data["Special"] .= "<br> - 2 turns: 2d10+14"; 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1:
                	return Dice::d(10)+4;
			    	break;
            	default:
                	return Dice::d(10,2)+14;
			    	break;
        	}
		}

 		public function setMinDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->minDamage = 5 ;
                    break;
                default:
                    $this->minDamage = 14 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 14 ;
                    break;
                default:
                    $this->maxDamage = 34 ;  
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
			return $strippedSystem;
		}

}//end of class TrekPhaser




class TrekPhaserLance extends Raking{
		public $name = "TrekPhaserLance";
        public $displayName = "Phaser Lance";
        public $iconPath = "heavyLaser.png"; //Laser icon - just so it's clear it needs to be changed!
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
		public $animationExplosionScale = 0.3;
		public $animationWidth = 4;
		public $animationWidth2 = 0.3;

        public $raking = 10;
        
        public $intercept = 2;
		public $priority = 8; //light Raking		
		
        public $loadingtime = 2;
		
        public $rangePenaltyArray = array(1=>0.33, 2=>0.5);
        public $fireControlArray = array( 1=>array(0, 4, 4), 2=>array(3, 3, 3) ); 
	

        public $damageType = "Raking";
		public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); 
		public $weaponClass = "Particle";
		public $weaponClassArray = array(1=>'Particle', 2=>'Particle');
		public $firingModes = array( 1 => "Lance", 2=> "Dual Phasers");
		public $firingMode = 1;
		
		

	 	public function getInterceptRating($turn){
			return 2;
		}

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 10;
			if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Can fire as either:";  
			$this->data["Special"] .= "<br> - Phaser Lance: single shot, improved range, damage and antiship FC"; 
			$this->data["Special"] .= "<br> - Dual Phasers: two regular Phaser shots."; 
			$this->data["Special"] .= "<br>Cannot fire accelerated."; 
		}
	
		public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(10, 3)+14; //Phaser Lance
					break;
				case 2:
					return Dice::d(10, 2)+14; //Phaser
					break;	
			}
		}

 		public function setMinDamage(){
			switch($this->firingMode){
				case 1:
					$this->minDamage = 17; //Phaser Lance
					break;
				case 2:
					$this->minDamage = 16; //Phaser
					break;	
			}
		}
             
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 44; //Phaser Lance
					break;
				case 2:
					$this->maxDamage = 34; //Phaser
					break;	
			}
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			/* no need to override - no Accelerator option
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				
			*/
			return $strippedSystem;
		}

}//end of class TrekPhaserLance



    class TrekPlasmaBurst extends Plasma{

        public $name = "TrekPlasmaBurst";
        public $displayName = "Plasma Burst";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
        public $trailColor = array(75, 250, 90);
        public $animationWidth = 2;
        public $animationExplosionScale = 0.15;
    	public $trailLength = 10;
    	public $rangeDamagePenalty = 1;

        public $intercept = 0;
        public $loadingtime = 1;
        public $addedDice;
        public $priority = 6;

        public $boostable = true;
        public $boostEfficiency = 1;
        public $maxBoostLevel = 1;

        public $firingModes = array(
            1 => "Standard"
        );

        public $rangePenalty = 1;
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        //private $damagebonus = 10;

        public $damageType = "Standard"; 
        public $weaponClass = "Plasma"; 

        public function setSystemDataWindow($turn){
            $boost = $this->getExtraDicebyBoostlevel($turn);            
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
            $this->data["Special"] .= '<br>Double power boosts damage from 2d6 to 4d6. Forces a critical roll at a +10 penalty.';
            $this->data["Boostlevel"] = $boost;
        }

        private function getExtraDicebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 2;
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

        public function getDamage($fireOrder){
            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(6, (2 + $add)) +0;
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
            $this->minDamage = 2 + ($boost * 1) + 0;
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 12 + ($boost * 6) + 0;
        }  

        public function fire($gamedata, $fireOrder){
		$currBoostlevel = $this->getBoostLevel($gamedata->turn);
            parent::fire($gamedata, $fireOrder);
		
            // If fully boosted: test for possible crit.
            if($currBoostlevel === $this->maxBoostLevel){
				$this->criticalRollMod = 10;
            	$this->forceCriticalRoll = true;
            }
        }
		
    }  //end of class TrekPlasmaBurst



class TrekSpatialTorp extends Torpedo{
        public $name = "TrekSpatialTorp";
        public $displayName = "Spatial Torpedo";
		    public $iconPath = "EWRocketLauncher.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 12;
		public $distanceRange = 18;
		
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 4; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn); 
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+2;   
		}

        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
		
}//endof TrekSpatialTorp



class TrekPhotonicTorp extends Torpedo{
        public $name = "TrekPhotonicTorp";
        public $displayName = "Photonic Torpedo";
		    public $iconPath = "TrekPhotonicTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 15;
		public $distanceRange = 25;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 4; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+6;   
		}

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 18;      }
		
}//endof TrekPhotonicTorp




class TrekPhotonTorp extends Torpedo{
        public $name = "TrekPhotonTorp";
        public $displayName = "Photon Torpedo";
		    public $iconPath = "TrekPhotonicTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(11, 224, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 20;
		public $distanceRange = 30;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 4; //Standard weapon
	    
	public $firingMode = 'Ballistic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 3)+6;   
		}

        public function setMinDamage(){     $this->minDamage = 9;      }
        public function setMaxDamage(){     $this->maxDamage = 24;      }
		
}//endof TrekPhotonTorp



class HvyPlasmaProjector extends Raking{

	public $name = "HvyPlasmaProjector";
	public $displayName = "Heavy Plasma Projector";
	public $iconPath = "HeavyPlasmaProjector.png";
	public $animation = "laser";
	public $animationColor = array(75, 250, 90);
    public $animationWidth = 5;
    public $animationWidth2 = 0.3;
	public $priority = 7;

	public $rangeDamagePenalty = 0.25;
	public $loadingtime = 4;
	public $raking = 8;
	public $rangePenalty = 0.33;
	public $fireControl = array(null, 2, 4);

	public $damageType = "Raking";
	public $weaponClass = "Plasma";	
	public $firingModes = array(1 => "Raking");

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 11;
			if ( $powerReq == 0 ) $powerReq = 8;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Damage reduced by 1 points per 4 hexes.";
			$this->data["Special"] .= "<br>Does damage in raking mode (8)";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
    public function getDamage($fireOrder){        return Dice::d(10,5)+10;   }
	public function setMinDamage(){     $this->minDamage = 15;      }
	public function setMaxDamage(){     $this->maxDamage = 60;      }

}// End of class HvyPlasmaProjector


class LtPlasmaProjector extends Raking{

	public $name = "LtPlasmaProjector";
	public $displayName = "Light Plasma Projector";
	public $iconPath = "LightPlasmaProjector.png";
	public $animation = "laser";
	public $animationColor = array(75, 250, 90);
    public $animationWidth = 3;
    public $animationWidth2 = 0.1;
	public $priority = 5;

	public $rangeDamagePenalty = 0.5;
	public $loadingtime = 2;
	public $raking = 8;
	public $rangePenalty = 1;
	public $fireControl = array(1, 2, 2);

	public $damageType = "Raking";
	public $weaponClass = "Plasma";	
	public $firingModes = array(1 => "Raking");

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 3;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Damage reduced by 1 points per 2 hexes.";
			$this->data["Special"] .= "<br>Does damage in raking mode (8)";
			$this->data["Special"] .= "<br>Ignores half of armor.";
	}
			
    public function getDamage($fireOrder){        return Dice::d(10,2)+5;   }
	public function setMinDamage(){     $this->minDamage = 7;      }
	public function setMaxDamage(){     $this->maxDamage = 25;      }

}// End of class LtPlasmaProjector



/* Star Trek shield projection
 note this is NOT a shield as far as FV recognizes it!
*/
//class TrekShieldProjection extends ShipSystem{
class TrekShieldProjection extends Shield implements DefensiveSystem { //defensive values of zero, but still formally there to display arcs!
    public $name = "TrekShieldProjection";
    public $displayName = "Shield Projection";
    public $primary = true;
	public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
	public $isTargetable = false; //cannot be targeted ever!
    public $iconPath = "TrekShieldProjectionF.png"; //overridden anyway - to indicate proper direction
    
	public $possibleCriticals = array(); //no criticals possible
	
	//Shield Projections cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	private $projectorList = array();
	
    
    function __construct($armor, $maxhealth, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$this->iconPath = 'TrekShieldProjection' . $side . '.png';
		//parent::__construct($armor, $maxhealth, 0, $rating);
		parent::__construct($armor, $maxhealth, 0, $rating, $startArc, $endArc);
		/*
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
		*/
		
		$this->output=$rating;//output is displayed anyway, make it show something useful... in this case - number of points absorbed per hit
	}
	
	
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
            return 0;
    }
	public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){ //no shield-like damage reduction
		return 0;
	}
    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under shield
        return false;
    }
	

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
/*		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		*/
		$this->data["Special"] = "Defensive system absorbing damage from hits before projectile touches actual hull.";
		$this->data["Special"] .= "<br>Can absorb up to " .$this->output ." damage points per hit. ";
		$this->data["Special"] .= ", including " . $this->armour . " without reducing capacity for further absorption.";
		$this->data["Special"] .= "<br>Will absorb more from Raking mode hits.";
		$this->data["Special"] .= "<br>System's health represents damage capacity. If it is reduced to zero system will cease to function.";
		$this->data["Special"] .= "<br>Will not fall on its own unless its structure block is destroyed.";
		$this->data["Special"] .= "<br>This is NOT a shield as far as any shield-related interactions go.";
	}	
	
	public function getRemainingCapacity(){
		return $this->getRemainingHealth();
	}
	
	public function getUsedCapacity(){
		return $this->getTotalDamage();
	}
	
	public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
		$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Regenerate!", "TrekShieldProjection");
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;
	}
	
	
	//function estimating how good this system is at stopping damage;
	//in case of shield projection, its effectiveness equals largest shot it can stop, with tiebreaker equal to remaining capacity
	//this is for recognizing it as system capable of affecting damage resolution and choosing best one if multiple Diffusers can protect
	public function doesReduceImpactDamage($expectedDmg) {
		$remainingCapacity = $this->getRemainingCapacity();
		$protectionValue = 0;
		if($remainingCapacity>0){
			$protectionValue = min($remainingCapacity+$this->armour,$this->output);
		}
		return $protectionValue;
	}
	
	//actual protection - should return modified $effectiveDamage value
	public function doReduceImpactDamage($gamedata, $fireOrder, $target, $shooter, $weapon, $effectiveDamage){ 
		$returnValue = $effectiveDamage;
		$remainingCapacity = $this->getRemainingCapacity();
		$absorbedDamage = 0;
		
		$remainingCapacity = $this->getRemainingCapacity();
		if($remainingCapacity>0) { //else projection does not protect
			$reduction = 0;
			//first, armor takes part
			$reduction = min($this->armour, $returnValue);
			$returnValue += -$reduction;
			//next, actual absorbtion
			$reduction = min($this->output - $this->armour, $remainingCapacity, $returnValue ); //no more than output (modified by already accounted for armor); no more than remaining capacity; no more than damage incoming
			$returnValue += -$reduction;
			$absorbedDamage += $reduction;
			$remainingCapacity -= $reduction;
			//for Raking hit: repeat steps above for every expected FULL rake beyond first - but at half the value of parameters! (do not round, this will be done last)
			if ($weapon->damageType == 'Raking'){
				$fullRakes = floor($effectiveDamage/($weapon->raking))-1;
				$fullRakes = max(0,$fullRakes);
				while($fullRakes>0){
					$fullRakes--;
					//first, armor takes part
					$reduction = min($this->armour/2, $returnValue);
					$returnValue += -$reduction;
					//next, actual absorbtion
					$reduction = min(($this->output - $this->armour)/2, $remainingCapacity, $returnValue ); //no more than output (modified by already accounted for armor); no more than remaining capacity; no more than damage incoming
					$returnValue += -$reduction;
					$absorbedDamage += $reduction;
					$remainingCapacity -= $reduction;
					/*
					$reduction = 5; //no more than output (modified by already accounted for armor); no more than remaining capacity; no more than damage incoming
					$returnValue += -$reduction;
					$absorbedDamage += $reduction;
					$remainingCapacity -= $reduction;
					*/
					if($remainingCapacity<=0) $fullRakes = 0; //do not continue after shield is brought down to 0
				}
				//round damage UP and absorbed values DOWN
				$returnValue = ceil($returnValue);
				$absorbedDamage = floor($absorbedDamage);
			}
			if($absorbedDamage>0){ //mark!
				$this->absorbDamage($target,$gamedata,$absorbedDamage);
			}
		}
		
		return $returnValue;
	}		
	
    
	function addProjector($projector){
		if($projector) $this->projectorList[] = $projector;
	}
	
	//effects that happen in Critical phase (after criticals are rolled) - replenishment from active projectors 
	public function criticalPhaseEffects($ship, $gamedata){
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		$activeProjectors = 0;
		$projectorOutput = 0;
		$toReplenish = 0;
		
		foreach($this->projectorList as $projector){
			if ( ($projector->isDestroyed($gamedata->turn))
			     || ($projector->isOfflineOnTurn($gamedata->turn))
			) continue;
			$activeProjectors++;
			$projectorOutput += $projector->getOutputOnTurn($gamedata->turn);
		}
		/*after all - shield will NOT fall!
		if($activeProjectors <= 0){ //no active projectors - shield is falling!
			$toReplenish = -$this->getRemainingCapacity();	
			*/
		if($activeProjectors > 0){ //active projectors present - reinforce shield!
			$toReplenish = min($projectorOutput,$this->getUsedCapacity());		
		}
		
		if($toReplenish != 0){ //something changes!
			$this->absorbDamage($ship,$gamedata,-$toReplenish);
		}
	} //endof function criticalPhaseEffects
	
}//endof class TrekShieldProjection


/* Star Trek shield projector
 reinforces shield projection (and prevents it from falling)
 actual reinforcing (and falling) is done from Projection's own end, Projector just is (needs to be plugged into appropriate projection at design stage
*/
//class TrekShieldProjector extends ShipSystem{
class TrekShieldProjector  extends Shield implements DefensiveSystem { //defensive values of zero, but still formally there to display arcs!
    public $name = "TrekShieldProjector";
    public $displayName = "Shield Projector";
	public $isPrimaryTargetable = true; //projector can be targeted even on PRIMARY, like a weapon!
    public $iconPath = "TrekShieldProjectorF.png"; //overridden anyway - to indicate proper direction
    public $boostable = true; //$this->boostEfficiency and $this->maxBoostLevel in __construct()  
	public $boostEfficiency = 1; //flat boost cost of 1 Power per 1 additional point of shielding regained
    public $baseOutput = 0; //base output, before boost
    
	
    public $possibleCriticals = array(
            16=>"OutputReduced1",
            25=>"OutputReduced2" );
	
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    
    function __construct($armor, $maxhealth, $power, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$this->iconPath = 'TrekShieldProjector' . $side . '.png';
		//parent::__construct($armor, $maxhealth, $power, $rating);
		parent::__construct($armor, $maxhealth, $power, $rating, $startArc, $endArc);
        //$this->startArc = (int)$startArc;
        //$this->endArc = (int)$endArc;
		$this->baseOutput = $rating;
		$this->maxBoostLevel = $rating; //maximum double effect		
	}
	
	
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
            return 0;
    }
	public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){ //no shield-like damage reduction
		return 0;
	}
    private function checkIsFighterUnderShield($target, $shooter){ //no flying under shield
        return false;
    }

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn); 
		$this->data["Special"] = "Shield projector - replenishes appropriate projection by its rating at end of turn.";
		$this->data["Special"] .= "<br>Can be boosted.";
	}	
	
    public function getOutputOnTurn($turn){
        $output = $this->getOutput() + $this->getBoostLevel($turn);
        return $output;
    }
	
	
	private function getBoostLevel($turn){
		$boostLevel = 0;
		foreach ($this->power as $i){
				if ($i->turn != $turn) continue;
				if ($i->type == 2){
						$boostLevel += $i->amount;
				}
		}
		return $boostLevel;
	}
	

} //endof class TrekShieldProjector


?>
