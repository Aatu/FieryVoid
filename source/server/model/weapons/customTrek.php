<?php
/*file for Star Trek universe weapons*/


/*output is not recharge time (like for B5 warp drives), but rather impulse rating (eg. how much thrust is derived from this system...)*/
class TrekWarpDrive extends JumpEngine{
    //public $name = "TrekWarpDrive";
    public $displayName = "Nacelle";
    public $iconPath = "WarpDrive.png";
    public $primary = true;
    
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    protected $possibleCriticals = array( //reduced output reduces available thrust
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
	
	
    protected $possibleCriticals = array( //as actual output is minima, so should be crits!	     
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
	    	/*11.11.2024 - Marcin Sawicki - use basic output only, as front end accounts for criticals again!
		$output = max(0,parent::getOutput());//criticals cannot bring base output below 0
  		*/
	    	$output = $this->output; //corrected version 11.11.2024
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


class TrekPhaserBase extends Raking { //common Phaser things like color
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);	
}


class TrekLtPhaseCannon extends TrekPhaserBase{
		public $name = "TrekLtPhaseCannon";
        public $displayName = "Light Phase Cannon";
        public $iconPath = "TrekLightPhaseCannon.png";
        //public $animationExplosionScale = 0.2;

        public $raking = 6;
        
        public $intercept = 2;
		public $priority = 8; //light Raking		
		public $priorityAF = 7; //Standard
		
        public $loadingtime = 1;
		
        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 2);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Raking");

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 4;
			if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		/*
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Does damage in raking mode (6)";
	}
	*/
	
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

}//end of class Trek Light Phase Cannon




class TrekPhaseCannon extends TrekPhaserBase{
		public $name = "TrekPhaseCannon";
        public $displayName = "Phase Cannon";
        public $iconPath = "TrekPhaseCannon.png";
        //public $animationExplosionScale = 0.3;

        public $raking = 8;
        
        public $intercept = 2;
		public $priority = 8; //light Raking	
		public $priorityAF = 3; //as heavy Raking vs fighters!		
		
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



class TrekHvyPhaseCannon extends TrekPhaserBase{
		public $name = "TrekHvyPhaseCannon";
        public $displayName = "Heavy Phase Cannon";
        public $iconPath = "TrekHeavyPhaseCannon.png";
        //public $animationExplosionScale = 0.35;

        public $raking = 10;
        
        public $intercept = 1;
		public $priority = 7; //technically light Raking, but they're heaviest Raking guns that early Federation has - and stand out in the context of Federation fleet  	
		public $priorityAF = 3; //as heavy Raking vs fighters!		
		
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
                    $this->minDamage = 15 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                    $this->maxDamage = 28 ;
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




class TrekPhaser extends TrekPhaserBase{
		public $name = "TrekPhaser";
        public $displayName = "Phaser";
        public $iconPath = "TrekPhaserM.png"; 
        //public $animationExplosionScale = 0.3;

        public $raking = 10;
        
        public $intercept = 2;
		public $priority = 8; //light Raking	
		public $priorityAF = 3; //as heavy Raking vs fighters!		
		
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
                    $this->minDamage = 16 ;  
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




class TrekPhaserLance extends TrekPhaserBase{
		public $name = "TrekPhaserLance";
        public $displayName = "Phaser Lance";
        public $iconPath = "TrekPhaserLanceM.png"; 
        public $animation = "laser";
        //public $animationExplosionScale = 0.4;
	//public $animationExplosionScaleArray = array(1=>0.4, 2=>0.3); 

        public $raking = 10;
        
        public $intercept = 2;
		public $priority = 7; //technically light Raking, but borderline - and they're by far heaviest weapons that Federation has - hence 'heavy Raking' status
		public $priorityArray = array(1=>7, 2=>8);		
		public $priorityAF = 3; //both Lance and full Phaser are treated as heavy vs fighters
		
        public $loadingtime = 2;
    	public $gunsArray = array(1=>1, 2=>2); //one Lance, but two Beam shots!
		
        public $rangePenaltyArray = array(1=>0.33, 2=>0.5);
        public $fireControlArray = array( 1=>array(0, 4, 4), 2=>array(2, 3, 3) ); 
	

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
}//end of class TrekPhaserLance


class TrekLightPhaser extends TrekPhaserBase{
		public $name = "TrekLightPhaser";
        public $displayName = "Light Phaser";
        public $iconPath = "TrekPhaserL.png"; 
        //public $animationExplosionScale = 0.3;

        public $raking = 8;
        
        public $intercept = 2;
		public $priority = 8; //light Raking	
		public $priorityAF = 7; //...Standard vs fighters
		
        public $loadingtime = 1;
		
        public $rangePenalty = 1.0;
        public $fireControl = array(4, 3, 2);

        public $damageType = "Raking";
		public $weaponClass = "Particle";
		public $firingMode = "Raking";

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 4;
			if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }

}//end of class TrekLightPhaser


class TrekLightPhaserLance extends TrekPhaserBase{
		public $name = "TrekLightPhaserLance";
        public $displayName = "Light Phaser Lance";
        public $iconPath = "TrekPhaserLanceL.png";
        public $animation = "laser";
        //public $animationExplosionScale = 0.4;
	//public $animationExplosionScaleArray = array(1=>0.4, 2=>0.3); 

        public $raking = 8;
        
        public $intercept = 2;
		public $priority = 8; //light Raking		
    	public $gunsArray = array(1=>1, 2=>2); //one Lance, but two Beam shots!
		public $priorityAF = 3; //heavy Raking vs fighters!	
		public $priorityAFArray = array(1=>3, 2=>7); ///...but regular Light Phasers are effectively medium Standard vs fighters
		
        public $loadingtime = 1;
		
        public $rangePenaltyArray = array(1=>1.0, 2=>1.0);
        public $fireControlArray = array( 1=>array(3, 3, 3), 2=>array(4, 3, 2) ); 

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
			$this->data["Special"] .= "Can fire as either:";  
			$this->data["Special"] .= "<br> - Light Phaser Lance: single shot with improved damage"; 
			$this->data["Special"] .= "<br> - Dual Phasers: two regular Light Phaser shots."; 
		}
	
		public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(10, 2)+6; //Light Phaser Lance
					break;
				case 2:
					return Dice::d(10, 1)+6; //Light Phaser
					break;	
			}
		}

 		public function setMinDamage(){
			switch($this->firingMode){
				case 1:
					$this->minDamage = 8; //Light Phaser Lance
					break;
				case 2:
					$this->minDamage = 7; //Light Phaser
					break;	
			}
		}
             
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 26; //Light Phaser Lance
					break;
				case 2:
					$this->maxDamage = 16; //Light Phaser
					break;	
			}
		}
		
}//end of class TrekLightPhaserLance



    class TrekPlasmaBurst extends Plasma{

        public $name = "TrekPlasmaBurst";
        public $displayName = "Plasma Burst";
        public $animation = "trail";
        //public $animationColor = array(75, 250, 90);
        //public $animationExplosionScale = 0.15;
	    
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
            $this->data["Special"] .= 'Double power boosts damage from 2d6 to 4d6 and forces a critical roll at a +10 penalty.';
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
		
            // If fully boosted: force a critical roll (with hefty penalty)
            if($currBoostlevel === $this->maxBoostLevel){
				$this->critRollMod += 10;
            	$this->forceCriticalRoll = true;
            }
        }
		
    }  //end of class TrekPlasmaBurst



class TrekSpatialTorp extends Torpedo{
        public $name = "TrekSpatialTorp";
        public $displayName = "Spatial Torpedo";
		    public $iconPath = "EWRocketLauncher.png";
			
        public $animation = "bolt";
        public $animationColor = array(100, 100, 100); //color of projectile
        //public $animationExplosionScale = 0.2; //indicates projectile size

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 12;
		public $distanceRange = 18;
		
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 1); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 4; //light Standard weapon
	    
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
		
        public $animation = "torpedo";
        public $animationColor = array(255, 188, 0); //let's make it yellowish
        //public $animationExplosionScale = 0.2;
		

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
			
			public $animation = "torpedo";
        public $animationColor = array(255, 188, 0); //let's make it yellowish
        //public $animationExplosionScale = 0.35;

        public $useOEW = true; //torpedo
        public $ballistic = true; //missile
        public $range = 20;
		public $distanceRange = 30;
        
        public $loadingtime = 2; // 1 turn
        public $rangePenalty = 0;
        public $fireControl = array(1, 1, 2); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $priority = 5; //Standard Medium weapon; maybe even heavy...
	    
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
        //public $animationExplosionScale = 0.5; 
	
	public $priority = 7; //heavy Raking weapon

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
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Damage reduced by 1 point per 4 hexes.";
			//$this->data["Special"] .= "<br>Does damage in raking(8) mode ";
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
        //public $animationExplosionScale = 0.3;
	
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
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Damage reduced by 1 points per 2 hexes.";
			//$this->data["Special"] .= "<br>Does damage in raking(8) mode";
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
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value - shields are overloaded and regenerating all the time, do not represent permanent loss of combat ability
    
	protected $possibleCriticals = array(); //no criticals possible
	
	//Shield Projections cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

	private $projectorList = array();
	
    
    function __construct($armor, $maxhealth, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$this->iconPath = 'TrekShieldProjection' . $side . '.png';
		parent::__construct($armor, $maxhealth, 0, $rating, $startArc, $endArc);
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
		/*
		$this->data["Special"] = "Defensive system absorbing damage from hits before projectile touches actual hull.";
		$this->data["Special"] .= "<br>Can absorb up to " .$this->output ." damage points per hit, ";
		$this->data["Special"] .= "including " . $this->armour . " without reducing capacity for further absorption.";
		$this->data["Special"] .= "<br>Protects from every separate impact (eg. every rake!) separately.";
		$this->data["Special"] .= "<br>System's health represents damage capacity. If it is reduced to zero system will cease to function.";
		$this->data["Special"] .= "<br>Will not fall on its own unless its structure block is destroyed.";
		$this->data["Special"] .= "<br>This is NOT a shield as far as any shield-related interactions go.";
		*/
		$absorb = $this->output - $this->armour;
		$this->data["Special"] = "Defensive system which absorbs damage from incoming shots before they damage ship hull.";
		$this->data["Special"] .= "<br>Can absorb up to " . $absorb . " damage points per hit after projection armour is applied, ";
		$this->data["Special"] .= "<br>Protects from every separate impact (e.g. every rake!) separately.";
		$this->data["Special"] .= "<br>Projection armour cannot absorb more than half of any impact.";
		$this->data["Special"] .= "<br>System's structure represents damage capacity. If it is reduced to zero system will cease to function.";
		$this->data["Special"] .= "<br>Can't be destroyed unless associated structure block is also destroyed.";
		$this->data["Special"] .= "<br>This is NOT a shield as far as any shield-related interactions go.";
		
		$this->outputDisplay = $this->armour . '/' . $absorb . '/' . $this->getRemainingCapacity();//override on-icon display default
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
	
	
	
	//decision whether this system can protect from damage - value used only for choosing strongest shield to balance load.
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false) {
		if($damageWasDealt) return 0; //does not protect from overkill damage, just first impact
		
		$remainingCapacity = $this->getRemainingCapacity();
		$protectionValue = 0;
		if($remainingCapacity>0){
			$protectionValue = $remainingCapacity+$this->armour; //this is actually more than this system can protect from - but allows to balance load between systems in arc
		}
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		$damageToAbsorb=$effectiveDamage; //shield works BEFORE armor
		$damageAbsorbed=0;
		
		if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
		
		$remainingCapacity = $this->getRemainingCapacity();
		$absorbedDamage = 0;
		
		if($remainingCapacity>0) { //else projection does not protect
			$absorbedFreely = 0;
			//first, armor takes part
			$absorbedFreely = min($this->armour, (floor($damageToAbsorb/2)));
			$damageToAbsorb += -$absorbedFreely;
			//next, actual absorbtion
			$absorbedDamage = min($this->output - $this->armour, $remainingCapacity, $damageToAbsorb ); //no more than output (modified by already accounted for armor); no more than remaining capacity; no more than damage incoming
			$damageToAbsorb += -$absorbedDamage;
			if($absorbedDamage>0){ //mark!
				$this->absorbDamage($target,$gamedata,$absorbedDamage);
			}
			$returnValues['dmg'] = $damageToAbsorb;
			$returnValues['armor'] = min($damageToAbsorb, $returnValues['armor']);
		}
		
		return $returnValues;
	} //endof function doProtect
	    
	function addProjector($projector){
		if($projector) $this->projectorList[] = $projector;
	}
	
	//effects that happen in Critical phase (after criticals are rolled) - replenishment from active projectors 
	public function criticalPhaseEffects($ship, $gamedata){
		
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.
					
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
	
	public function stripForJson() {
        $strippedSystem = parent::stripForJson();

		$strippedSystem->outputDisplay = $this->outputDisplay;
		
        return $strippedSystem;
	}
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
    
	
    protected $possibleCriticals = array(
            16=>"OutputReduced1",
            25=>"OutputReduced2" );
	
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    
    function __construct($armor, $maxhealth, $power, $rating, $startArc, $endArc, $side = 'F'){ //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$this->iconPath = 'TrekShieldProjector' . $side . '.png';
		parent::__construct($armor, $maxhealth, $power, $rating, $startArc, $endArc);
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
		$this->data["Special"] = "Shield projector - replenishes structure of appropriate Shield Projection by Projector's rating at end of turn.";
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



//fighter systems don't get damaged - so fighter tendrils need to store damage by way of notes
class TrekShieldFtr extends ShipSystem{
    public $name = "TrekShieldFtr";
    public $displayName = "Shield Projection";
	public $iconPath = "TrekShieldProjectionF.png";
	
	private $recharge = 0; //recharge rate
	private $usedCapacityTotal=0;
	private $thisTurnEntries=array();
    
	//Diffuser Tendrils cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired



    function __construct($armor,$maxhealth, $rating, $recharge){ 
		parent::__construct($armor, $maxhealth, 0, 360);
		$this->recharge = $recharge;
		$this->output=$rating;//output is displayed anyway, make it show something useful...
		
	}

    
	public function setSystemDataWindow($turn){
		//add information about damage stored - ships do have visual reminder about it, but fighters do not!
		parent::setSystemDataWindow($turn); 
		
		$this->data["Capacity available/max"] = $this->getRemainingCapacity() . '/' . $this->maxhealth;
		$this->data["Armor"] = $this->armour;
		$this->data["Recharge"] = $this->recharge;
		
		/*
		$this->data["Special"] = "Defensive system absorbing damage from hits before projectile touches actual hull.";
		$this->data["Special"] .= "<br>Can absorb up to " .$this->output ." damage points per hit, ";
		$this->data["Special"] .= "including " . $this->armour . " without reducing capacity for further absorption.";
		$this->data["Special"] .= "<br>Regenerates at end of turn, after firing. Regeneration rate is doubled if fighter doesn't use its direct fire weapons.";
		*/
		
		$absorb = $this->output - $this->armour;
		$this->data["Special"] = "Defensive system which absorbs damage from incoming shots before they damage ship hull.";
		$this->data["Special"] .= "<br>Can absorb up to " . $absorb . " damage points per hit after projection armour is applied, ";
		$this->data["Special"] .= "<br>Projection armour cannot absorb more than half of any impact.";
		$this->data["Special"] .= "<br>If damage capacity it is reduced to zero system will cease to function.";
		$this->data["Special"] .= "<br>Recharges at end of turn, after firing. Recharge rate is doubled if fighter doesn't use its direct fire weapons.";
		$this->data["Special"] .= "<br>This is NOT a shield as far as any shield-related interactions go.";
		
		$this->outputDisplay = $this->armour . '/' . $absorb . '/' . $this->getRemainingCapacity();//override on-icon display default
	}	
	
	/*always redefine $this->data due to current capacity info*/
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;		
        return $strippedSystem;
    }
	
	
	public function getRemainingCapacity(){
		return $this->maxhealth - $this->usedCapacityTotal;
	}
	
	public function getUsedCapacity(){
		return $this->usedCapacityTotal;
	}
	
	public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
		$this->usedCapacityTotal += $value; //running count
		$this->thisTurnEntries[] = $value; //mark for database
	}



	//decision whether this system can protect from damage - value used only for choosing strongest shield to balance load.
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false) {
		if($damageWasDealt) return 0; //does not protect from overkill damage, just first impact
		
		$remainingCapacity = $this->getRemainingCapacity();
		$protectionValue = 0;
		if($remainingCapacity>0){
			$protectionValue = min($remainingCapacity,$this->output - $this->armour)+$this->armour ; //this is how much system can actually absorb
			$protectionValue += $remainingCapacity/100;//this is a fraction of point to represent remaining shield capacity and help balance load
		}
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		$damageToAbsorb=$effectiveDamage; //shield works BEFORE armor
		$damageAbsorbed=0;
		
		if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
		
		$remainingCapacity = $this->getRemainingCapacity();
		$absorbedDamage = 0;
		
		if($remainingCapacity>0) { //else projection does not protect
			$absorbedFreely = 0;
			//first, armor takes part
			$absorbedFreely = min($this->armour, (floor($damageToAbsorb/2)));
			$damageToAbsorb += -$absorbedFreely;
			//next, actual absorbtion
			$absorbedDamage = min($this->output - $this->armour, $remainingCapacity, $damageToAbsorb ); //no more than output (modified by already accounted for armor); no more than remaining capacity; no more than damage incoming
			$damageToAbsorb += -$absorbedDamage;
			if($absorbedDamage>0){ //mark!
				$this->absorbDamage($target,$gamedata,$absorbedDamage);
			}
			$returnValues['dmg'] = $damageToAbsorb;
			$returnValues['armor'] = min($damageToAbsorb, $returnValues['armor']);
		}
		
		return $returnValues;
	} //endof function doProtect



	//effects that happen in Critical phase (after criticals are rolled) - shield recharge
	public function criticalPhaseEffects($ship, $gamedata){
		
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.		
		
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		$flight = $this->getUnit(); 
		$fighter = $flight->getFighterBySystem($this->id);
		
		//recharge
		$needed = $this->getUsedCapacity();
		if($needed <=0) return; //no need to recharge anything
		$rechargeRate = $this->recharge;
		if($rechargeRate <=0) return;//some fighters might have shield that doesn't regenerate in combat 
		//if fighter didn't fire direct fire weapons this turn - double recharge rate!
		$fireOrders = $flight->getFighterFireOrders($fighter, $gamedata->turn);
		$didFire = false;
		foreach($fireOrders as $fireOrder) if ($fireOrder->type != 'ballistic'){ 
			$didFire = true;
		}
		if(!$didFire) $rechargeRate += $rechargeRate;
		$rechargeActual = min($needed, $rechargeRate);
		$this->absorbDamage($ship,$gamedata,-$rechargeActual);		
	} //endof function criticalPhaseEffects

	
	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Firing phase: add information on stored/dissipated energy (every entry separately)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case 4: //firing phase
					foreach($this->thisTurnEntries as $tte){					
						$notekey = 'absorb';
						$noteHuman = 'Trek Shield absorbed or dissipated';
						$noteValue = $tte;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	here:
	 - fill $usedCapacityTotal value
	*/
	public function onIndividualNotesLoaded($gamedata){
		$this->usedCapacityTotal = 0;
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting 
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($currNote->notekey){
				case 'absorb': //absorbtion or dissipation of energy
					$this->usedCapacityTotal += $currNote->notevalue;
					break;		
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded

}//endof class TrekShieldFtr



    class TrekFtrPhaser extends LinkedWeapon{
        public $name = "TrekFtrPhaser";
        public $displayName = "Phaser"; 
        public $animation = "bolt";
        public $animationColor = array(225, 0, 0);
        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
		public $priority = 3; //correct for d6+2 and lighter

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";         

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameBase = 'Phaser'){ //just so it can be easily renamed to eg. Phase Cannon
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		
			$this->displayName = $nameBase;

			if($damagebonus<=2){
				$this->displayName = 'Ultralight ' . $nameBase;
			}
            if ($damagebonus >= 3) {
				$this->priority++; //heavier varieties fire later in the queue	
				$this->displayName = 'Light ' . $nameBase;//...and go no further up - full-bore "Phaser" (or "Phase Cannon") is shipborne weapon, or superheavy fighter weapon)		
			}
            if ($damagebonus >= 5) {
				$this->priority++;
			}
            if ($damagebonus >= 7) $this->priority++;

            if($nrOfShots == 1){
                $this->iconPath = "TrekFtrPhaser1.png";
            }else if ($nrOfShots == 2){
                $this->iconPath = "TrekFtrPhaser2.png";
			}else{ //more than 2
                $this->iconPath = "TrekFtrPhaser3.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }
    } //endof TrekFtrPhaser



/*super-heavy fighter weapon*/
 class TrekFtrPhaseCannon extends Raking{
        public $name = "TrekFtrPhaseCannon";
        public $displayName = "Phaser"; 
        public $animation = "laser";
        public $animationColor = array(225, 0, 0);
        public $iconPath = "TrekLightPhaseCannon.png";

        public $loadingtime = 2;
        public $raking = 6;
        public $shots = 2;
        public $defaultShots = 2;
		public $priority = 8; //Raking weapon
        public $intercept = 2;

        public $rangePenalty = 1.5; //-3 per 2 hexes
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Particle";         

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $raking, $nameBase = 'Phaser'){ //just so it can be easily renamed to eg. Phase Cannon
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;		
            $this->raking = $raking;	
			$this->displayName = 'Fighter Heavy ' . $nameBase;


            if($nrOfShots == 1){
                $this->iconPath = "TrekLightPhaseCannon1.png";
			}else{ //more than 1
                $this->iconPath = "TrekLightPhaseCannon2.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6,2)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 2+$this->damagebonus ;      }
        public function setMaxDamage(){     $this->maxDamage = 12+$this->damagebonus ;      }
    } 
/*old version
    class TrekFtrPhaseCannon extends TrekPhaserBase{
        public $name = "TrekFtrPhaseCannon";
        public $displayName = "Phase Cannon";
        public $iconPath = "TrekLightPhaseCannon.png";
        //public $animationExplosionScale = 0.2;

        public $loadingtime = 2;
        public $raking = 6;
//        public $exclusive = true;
        public $intercept = 2;
        public $priority = 8; //Raking weapon
        
        //public $rangePenalty = 1;
		public $rangePenalty = 1.5; // -3 per 2 hexes
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
 
		public $damageType = 'Raking'; 
		public $weaponClass = "Particle";  
		public $firingModes = array( 1 => "Raking");
	    
        function __construct($startArc, $endArc, $damagebonus){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

	    
        public function getDamage($fireOrder){
			return Dice::d(6, 2)+4;
		}
        public function setMinDamage(){   return  $this->minDamage = 6 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 16 ;      }
		
    }  
*/	
//end of class Trek Fighter Light Phase Cannon




class TrekFtrPhotonTorpedo extends FighterMissileRack
{
    public $name = "TrekFtrPhotonTorpedo";
    public $displayName = "Light Photon Torpedo";
    public $loadingtime = 1;
    public $iconPath = "TrekPhotonicTorpedo.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    protected $distanceRangeMod = 0;
    public $priority = 5; //priority: strong fighter weapon (similar damage output to basic fighter missiles)


    public $animation = "torpedo";

    public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "PhotonTorpedo"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        
        $ammo = new TrekFtrPhotonTorpedoAmmo($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $ammo
        );
        
        $this->maxAmount = $maxAmount;
		$this->fireControl = array(0, 1, 2);
    }    
}
class TrekFtrPhotonTorpedoAmmo extends MissileFB
{
    public $name = "TrekFtrPhotonTorpedoAmmo";
    public $missileClass = "PhotonTorpedo";
    public $displayName = "Light Photon Torpedo";
    public $cost = 6;//definitely worse than basic fighter missile - but packing about the same puch (less reliable though, with much less distance range (fighters will often be able to evade, possibly agile MCVs as well - and with less FC)
    //public $damage = 10;
    public $amount = 0;
    public $range = 10;
    public $distanceRange = 18;
    public $hitChanceMod = 0;
    public $priority = 5;
    public $iconPath = "TrekPhotonicTorpedo.png";
	
	 //data below is overridden for some reason :(
	public $fireControl = array(0, 1, 2);	
    public $animation = "torpedo";
        public $animationColor = array(255, 188, 0); //same as Photon Torpedo line
    
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){ 
			return Dice::d(6, 2)+3;
	}
    public function setMinDamage(){     $this->minDamage = 5;      }
    public function setMaxDamage(){     $this->maxDamage = 15;      }        
}


?>
