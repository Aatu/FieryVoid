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




class TrekDisruptorBase extends Weapon { //centralize armor interactions and color

		public $animationColor = array(142, 252, 13);
	
		public $reduceFacing = 0; //reduce armor of facing structure (compard with total damage done - Armor reduced if damage exceeds this variable)
		public $reduceSystem = 0; //reduce armor of system hit, except Structure (compared with immediate damage done)
		public $reduceStructure = 0; //reduce armor of Structure hit  (compared with immediate damage done)
		
		
}





class TrekLtPhaseCannon extends TrekPhaserBase{
		public $name = "TrekLtPhaseCannon";
        public $displayName = "Light Phase Cannon";
        public $iconPath = "TrekLightPhaseCannon.png";
        //public $animationExplosionScale = 0.2;

        public $raking = 6;
        
        public $intercept = 2;
		public $priority = 8; //light Raking		
		public $priorityAF = 8; //light Standard
		
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
		public $priority = 7; //heavy Raking - they are light Raking technically, but among Federation weapons they're heavier ones
		public $priorityAF = 6; //count as moderately strong vs fighters
		
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
		public $priority = 6; //technically light Raking, but they're heaviest Raking guns that early Federation has - and stand out in the context of Federation fleet  		
		public $priorityAF = 5; //count as strong vs fighters
		
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


//Pulse weapon based on Light Phase Cannon
class TrekPhasedPulseCannon extends Pulse{ //NOT TrekPhaserBase!
		public $name = "TrekPhasedPulseCannon";
        public $displayName = "Phased Pulse Cannon";
        public $iconPath = "TrekPulsePhaseCannon.png";
        //public $animationExplosionScale = 0.2;		
        public $animationColor = array(225, 0, 0);	
        
        public $intercept = 2;
		public $priority = 3; //very light Standard
		
        public $loadingtime = 1;
		
        public $rangePenalty = 1.5; //-3/2 hexes
        public $fireControl = array(3, 2, 2);

        public $damageType = "Pulse";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Pulse");

		//Pulse data:		
        public $grouping = 20; //+1 hit per 4 below target number
        public $maxpulses = 6;
		protected $useDie = 5; //die used for base number of hits
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 5;
			if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
			
        public function getDamage($fireOrder){        return 7;   }
}//end of class TrekPhasedPulseCannon

//Pulse weapon based on Light Phase Cannon
class TrekPhasedPulseAccelerator extends Pulse{ //NOT TrekPhaserBase!
		public $name = "TrekPhasedPulseAccelerator";
        public $displayName = "Phased Pulse Accelerator";
        public $iconPath = "TrekPulsePhaseAccel.png";
        //public $animationExplosionScale = 0.2;		
        public $animationColor = array(225, 0, 0);	
        
        public $intercept = 2;
		public $priority = 4; //light Standard
		public $priorityArray = array(1=>4,2=>3); //light to very light Standard
		
        public $loadingtime = 2;
        public $loadingtimeArray = array(1=>2,2=>1);
		
        public $rangePenalty = 0.66; //-2/3 hexes at full power, -3/2 hexes accelerated
        public $rangePenaltyArray = array(1=>0.66,2=>1.5);
        public $fireControl = array(2, 3, 3);

        public $damageType = "Pulse";
		public $weaponClass = "Particle";
		public $firingModes = array( 1 => "Full", 2 => "Light");

		//Pulse data:		
        public $grouping = 20; //+1 hit per 4 below target number
        public $maxpulses = 6;
		protected $useDie = 5; //die used for base number of hits
		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 7;
			if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
			
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
				$this->data["Special"] .= "Can fire as lighter weapon after 1 turn of charging.";
		}
	
	
        public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return 9; //full
					break;
				case 2:
					return 7; //light
					break;	
			}
		}
}//end of class TrekPhasedPulseAccelerator



class TrekPhaser extends TrekPhaserBase{
		public $name = "TrekPhaser";
        public $displayName = "Phaser";
        public $iconPath = "TrekPhaserM.png"; 
        //public $animationExplosionScale = 0.3;

        public $raking = 10;
        
        public $intercept = 2;
	    public $priority = 7; //heavy Raking - they are light Raking technically, but among Federation weapons they're heavier ones
		public $priorityAF = 6; //count as moderately strong vs fighters
		
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
		public $priorityArray = array(1=>6, 2=>7);		
		public $priorityAF = 3; //Lances are very heavy vs fighers, Phasers high end mediums
		public $priorityAFArray = array(1=>3, 2=>6);		
		
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
		public $priorityAF = 8; //...light Standard vs fighters
		
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
		public $priority = 7; //light Raking... but they still stand out among Federation weapons, at least in Lance mode	
		public $priorityArray = array(1=>7, 2=>8); 
    	public $gunsArray = array(1=>1, 2=>2); //one Lance, but two Beam shots!
		public $priorityAF = 7;
		public $priorityAFArray = array(1=>7, 2=>8); 
		
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
	public $isCloaked = false;
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
	
    public function setCritical($critical, $turn = 0){ //do nothing, shield projection should not receive any criticals
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
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false, $inflictingShots = 1, $isUnderShield = false) {
		$ship = $this->getUnit(); //GTS
		if($damageWasDealt || $isUnderShield) return 0; //does not protect from overkill damage, just first impact. Also does not protect from internal damage.
		if($ship->isCloaked) return 0; //shield is not active when cloaked    GTS
		
		$remainingCapacity = $this->getRemainingCapacity();
		$protectionValue = 0;
		if($remainingCapacity>0){
			$protectionValue = ($remainingCapacity / $inflictingShots) + $this->armour; //distribute capacity over shots
		}
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations

		$ship = $this->getUnit(); //GTS
		if($ship->isCloaked) return 0; //shield is not active when cloaked    GTS

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
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false, $inflictingShots = 1, $isUnderShield = false) {
		if($damageWasDealt || $isUnderShield) return 0; //does not protect from overkill damage, just first impact. Also does not protect from internal damage.
		
		$remainingCapacity = $this->getRemainingCapacity();
		$protectionValue = 0;
		if($remainingCapacity>0){
			$absorbable = min($remainingCapacity, ($this->output - $this->armour) * $inflictingShots);
			$protectionValue = ($absorbable / $inflictingShots) + $this->armour; //Average absorbable + constant armour
			$protectionValue += $remainingCapacity/100;//tie breaker matching original logic
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



/*base class for StarTrek SWIon weapons*/
/*based upon StarWars Ion weapons, but with a few differences:*/
/* - no salvo mode*/
/* - relatively high damage, BUT whatever penetrates armor is halved*/
/* - any damage penetration results in temporary power shortages*/
/* - permanent power shortages like SWIon weapons*/
class TrekIon extends Weapon{
    public $name = "trekion";
    public $priority = 10; //Ions usually fire last, to take advantage of induced criticals
 
    public $weaponClass = "SWIon"; //weapon class
    public $damageType = "Standard"; //and this should remain!
	public $noOverkill = true;
	
    public $animationColor =  array( 80, 150, 250); //scale is derived from damage
	
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
      $this->data["Special"] .= "Damage after armor is halved.";   
      $this->data["Special"] .= "<br>No overkill.";
      $this->data["Special"] .= "<br>Damage dealt is doubled for critical roll."; 
      $this->data["Special"] .= "<br>High damage may cause permanent power shortages."; 
    }
	
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //make vulnerable to next critical
		if ($system->advancedArmor) return;
//		if ($system->hardAdvancedArmor) return;  // Hardened Advanced Armor - GTS
		
      $dmg = $damage - $armour;
      if($dmg<=0) return; //no damage was actually done
      SWIonHandler::addDamage($ship, $system, $dmg);//possibly cause permanent power shortage
      if($system->isDestroyed()) return; //destroyed system - vulnerability to critical is irrelevant
      if($system instanceof Structure) return; //structure does not suffer critical hits anyway

      while($dmg>0){
	      $dmg--;
	      $system->critRollMod++;
      }
    }	
	
	
	//halve damage after armor!
    protected function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {
		$damageAfterArmor = $damage-$armour;
		if($damageAfterArmor<=1) return $damage; //up to 1 damage dealt - leave as is!
		//...otherwise: halve (rounding up) damage after Armor
		$damageAfterArmor = ceil($damageAfterArmor/2);
		return ($armour+$damageAfterArmor);
    }	
	
	
	//cause immediate but temporary power shortage
	protected function causeTemporaryShortage($ship,$currTurn, $turns, $count){ //target ship, current turn, how long critical should last, how many criticals should be applied
		if ($ship->advancedArmor) return;
//		if ($system->hardAdvancedArmor) return;  // Hardened Advanced Armor - GTS
		
		$reactor = $ship->getSystemByName("Reactor");
		$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $currTurn, $currTurn+$turns);
		$crit->updated = true;
		$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
		
		if ($count>1) $this->causeTemporaryShortage($ship,$currTurn,$turns, $count-1); 
	}

} //end of class TrekIon


class TrekFighterDisablerBase extends TrekIon{
    /*StarTrek fighter Ion weapon*/
    public $name = "TrekFighterDisablerBase";
	 
    public $exclusive = false; //can be always overridden in particular fighter!
    public $isLinked = true; //indicates that this is linked weapon
    public $priority = 10;	//always at the end of the queue, due to extra dropout effect
    protected $damagebonus = 0;
	
	
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->intercept = 0;
		
		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonFtr".$nr.".png";
		
		//appropriate number of linked weapons:		
		$this->defaultShots = $nrOfShots;
		$this->shots = $nrOfShots;
			
		parent::__construct(0, 1, 0, $startArc, $endArc, $nrOfShots);
	}    	  
    
    public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   } //d6+$damagebonus damage, like regular fighter weapons
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
    public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }
		
} //end of class TrekFighterDisablerBase



class TrekEarlyFighterDisabler extends TrekFighterDisablerBase{
    /*StarTrek fighter Ion weapon*/
    public $name = "TrekEarlyFighterDisabler";
    public $displayName = "Early Compact Disabler";	  
	  
    public $loadingtime = 2; //1/2 turns
    public $rangePenalty = 3; //-3/hex - very short range
    public $fireControl = array(-1, 0, 0); // fighters, <mediums, <capitals
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}    
		$this->data["Special"] .= "Causes temporary power shortages: 1 Power for d2 turns."; 
		$this->data["Special"] .= "<br>If no damage was dealt: 50% chance of 1 Power for 1 turn."; 
    }
	
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //call parent, cause temporary shortages
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		$length = 1;
		$amount = 1;
		if($damage>$armour){//if damage was actually dealt!
			$length = Dice::d(2);
		}else{ //armor was not pierced - 50% chance for single turn of drain
			$amount = Dice::d(2)-1;
		}
		if ($amount > 0) $this->causeTemporaryShortage($ship,$gamedata->turn,$length, $amount); //called method will check for Advanced Armor
    }

	
} //end of class TrekEarlyFighterDisabler

class TrekFighterDisabler extends TrekFighterDisablerBase{
    /*StarTrek fighter Ion weapon*/
    public $name = "TrekFighterDisabler";
    public $displayName = "Compact Disabler";	  
	  
    public $loadingtime = 2; //1/2 turns
    public $rangePenalty = 2; //-2/hex 
    public $fireControl = array(0, 1, 1); // fighters, <mediums, <capitals
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}    
		$this->data["Special"] .= "Causes temporary power shortages: 1 Power for d2 turns."; 
		$this->data["Special"] .= "<br>If no damage was dealt: 50% chance of 1 Power for 1 turn."; 
    }
	
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //call parent, cause temporary shortages
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		$length = 1;
		$amount = 1;
		if($damage>$armour){//if damage was actually dealt!
			$length = Dice::d(2);
		}else{ //armor was not pierced - 50% chance for single turn of drain
			$amount = Dice::d(2)-1;
		}
		if ($amount > 0) $this->causeTemporaryShortage($ship,$gamedata->turn,$length, $amount); //called method will check for Advanced Armor
    }
	
} //end of class TrekFighterDisabler


class TrekShipDisablerBase extends TrekIon{
    /*StarTrek shipborne Ion weapon*/
    public $name = "TrekShipDisablerBase";
	 
    public $priority = 10;	//always at the end of the queue, due to extra dropout/critical effect and reduced actual damage	
} //end of class TrekShipDisablerBase


class TrekLightDisabler extends TrekShipDisablerBase{
    //StarTrek lightest shipborne ion cannon 
    public $name = "TrekLightDisabler";
    public $displayName = "Light Disabler";
	
    public $loadingtime = 1; //1/turn
    public $rangePenalty = 2; //-2/hex
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals
   	
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonLight".$nr.".png";
		
		$guns = $nrOfShots;
		
		$powerReq = 2;
		$maxhealth = 5;
		//more for multiple mount		
		$maxhealth += $maxhealth*0.25*($nrOfShots-1);
		$powerReq += $powerReq*0.65*($nrOfShots-1);
		$maxhealth = ceil($maxhealth);
		$powerReq = ceil($powerReq);
		
		$this->guns = $nrOfShots;
		
		parent::__construct($armor, $maxhealth, $powerReq, $startArc, $endArc); 
	}    
		
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}    
		$this->data["Special"] .= "Causes temporary power shortages: 1 Power for d2 turns."; 
		$this->data["Special"] .= "If actual damage was done: +1 turn."; 
    }	
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //call parent, cause temporary shortages
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		$length = Dice::d(2);
		$amount = 1;
		if($damage>$armour){//if damage was actually dealt!
			$length += 1;
		}
		if ($amount > 0) $this->causeTemporaryShortage($ship,$gamedata->turn,$length, $amount); //called method will check for Advanced Armor
    }
	
	public function getDamage($fireOrder){ return  Dice::d(10)+2;   }
	public function setMinDamage(){     $this->minDamage = 3 ;      }
	public function setMaxDamage(){     $this->maxDamage = 12 ;      }

} //end of class TrekLightDisabler


class TrekMediumDisabler extends TrekShipDisablerBase{
    //StarTrek shipborne ion cannon 
    public $name = "TrekMediumDisabler";
    public $displayName = "Disabler";
	
    public $loadingtime = 2; //1/2 turns
    public $rangePenalty = 1; //-1/hex
    public $fireControl = array(1, 3, 3); // fighters, <mediums, <capitals
   	
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonMedium".$nr.".png";
		
		$guns = $nrOfShots;
		
		$powerReq = 3;
		$maxhealth = 7;
		//more for multiple mount		
		$maxhealth += $maxhealth*0.25*($nrOfShots-1);
		$powerReq += $powerReq*0.65*($nrOfShots-1);
		$maxhealth = ceil($maxhealth);
		$powerReq = ceil($powerReq);
		
		$this->guns = $nrOfShots;
		
		parent::__construct($armor, $maxhealth, $powerReq, $startArc, $endArc); 
	}    
		
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}    
		$this->data["Special"] .= "Causes temporary power shortages: d2 Power for d3 turns."; 
		$this->data["Special"] .= "If actual damage was done: +1 turn."; 
    }	
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //call parent, cause temporary shortages
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		$length = Dice::d(2);
		$amount = Dice::d(3);
		if($damage>$armour){//if damage was actually dealt!
			$length += 1;
		}
		if ($amount > 0) $this->causeTemporaryShortage($ship,$gamedata->turn,$length, $amount); //called method will check for Advanced Armor
    }
	
	public function getDamage($fireOrder){ return  Dice::d(10)+4;   }
	public function setMinDamage(){     $this->minDamage = 5 ;      }
	public function setMaxDamage(){     $this->maxDamage = 14 ;      }

} //end of class TrekMediumDisabler


//Trek Cloaking Device
class CloakingDevice extends ShipSystem implements SpecialAbility{    
	public $name = "CloakingDevice";
	public $displayName = "Cloaking Device";
	public $iconPath = "CloakingDevice.png";
	public $specialAbilities = array("Cloaking");
	public $primary = true;
	public $detected = true;
	public $detectedNew = array(); // New multi-team array logic
	protected $active = false; //To track in Front End whether system was ever activate this turn during Deployment/PreOrders.		
//	public $isCloaked = false;
		
	function __construct($armour, $maxhealth, $powerReq, $output){
		parent::__construct($armour, $maxhealth, $powerReq, $output);
	}	

	public function onConstructed($ship, $turn, $phase){
		parent::onConstructed($ship, $turn, $phase);
	}

	protected $possibleCriticals = array(
		16=>array("ForcedOfflineOneTurn")
	);

/*
	public function setSystemDataWindow($turn){							
		$this->data["Special"] = "Detection is tested at the start of the turn and before the firing phase.";
		$this->data["Special"] .= "<br>No weapons are functionl while the cloak is engaged.";
		$this->data["Special"] .= "<br>Bases and ELINT units detect cloaked units at 1.5 their EW rating.";
		$this->data["Special"] .= "<br>All other units use their sensor rating. Fighters get half their offensive bonus.";
		parent::setSystemDataWindow($turn);     
	}	
*/

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] = "Detection is tested at the start of the turn and before the firing phase.";
		$this->data["Special"] .= "<br>No weapons are functional while the cloak is engaged.";
		$this->data["Special"] .= "<br>No shields are functional while the cloak is engaged.";
		$this->data["Special"] .= "<br>Bases and ELINT units detect cloaked units at 1.5 their EW rating.";
		$this->data["Special"] .= "<br>All other units use their sensor rating. Fighters get half their offensive bonus.";
	}
			
	public function getSpecialAbilityValue($args){
		return $this->specialAbilityValue;
	}

	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer)){			
			foreach($this->individualNotesTransfer as $cloakingChange){			
				if($cloakingChange == 1){
					$this->active = true;
				}else{
					$this->active = false; //May start Deployment phase as true via notes
				}									
			}
		} 
		$this->individualNotesTransfer = array(); //empty, just in case
	}			

    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$this->doIndividualNotesTransfer();
		$ship = $this->getUnit();	
		
		switch($gameData->phase){
			
			case -1:
				if ($this->active) {
					$notekey = 'Cloaked';
					$noteHuman = 'Cloaked this turn';
					$noteValue = 1;
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				}else{
					$notekey = 'Decloaked';
					$noteHuman = 'Not cloaked this turn';
					$noteValue = 1;
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
				}	
			break;
		}	
	}			

		public function onIndividualNotesLoaded($gamedata){
			//Sort notes by turn, and then phase so latest detection note is always last.
			$ship = $this->getUnit();  //GTS
			$this->sortNotes();
			if (!is_array($this->detectedNew)) $this->detectedNew = array();

			foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
				switch($currNote->notekey){
					case 'detected': 
						$this->detected = true;
						if (strpos($currNote->notevalue, 'Team:') === 0) {
							$teamId = (int) substr($currNote->notevalue, 5);
							if (!in_array($teamId, $this->detectedNew)) {
								$this->detectedNew[] = $teamId;
							}
						}
					break;
					case 'undetected': 
						$this->detected = false;						
						if (strpos($currNote->notevalue, 'Team:') === 0) {
							$teamId = (int) substr($currNote->notevalue, 5);
							$this->detectedNew = array_values(array_diff($this->detectedNew, [$teamId]));
						}
					break;
					case 'Cloaked': 
						if($currNote->turn == $gamedata->turn || $gamedata->phase == -1 && $currNote->turn == $gamedata->turn-1){					
							$this->active = true;
						}	
						$ship->isCloaked = true;  //GTS
					break;	
					case 'Decloaked': 
						if($currNote->turn == $gamedata->turn || $gamedata->phase == -1 && $currNote->turn == $gamedata->turn-1){					
							$this->active = false;
						}		
						$ship->isCloaked = false;  //GTS
					break;																				
				}
			}

			//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
			$this->individualNotes = array();		
		} //endof function onIndividualNotesLoaded


		private function sortNotes() {
			usort($this->individualNotes, function($a, $b) {
				// Compare by turn first
				if ($a->turn == $b->turn) {
					// If turns are equal, compare by phase
					return ($a->phase < $b->phase) ? -1 : 1;
				}
				return ($a->turn < $b->turn) ? -1 : 1;
			});
		}


		public function checkStealthNextPhase($gamedata){
			$ship = $this->getUnit();
			if($gamedata->phase == 1){ 
				$noteHuman1 = 'D-detectedActive';
				$noteHuman2 = 'D-undetectedActive';						
				$noteHuman3 = 'D-NotActive';						
			}else{
				$noteHuman1 = '2-detectedActive';
				$noteHuman2 = '2-undetectedActive';						
				$noteHuman3 = '2-NotActive';						
			}

			// Get all enemy teams in the game
			$enemyTeams = array();
			foreach ($gamedata->slots as $slot) {
				$teamId = (int)$slot->team;
				if ($teamId != $ship->team && !in_array($teamId, $enemyTeams)) {
					$enemyTeams[] = $teamId;
				}
			}

			// If we're checking during DeploymentGamePhase->Advance (actually Phase 1 at this point).					
			if ($this->active) {
				$detectingTeams = $this->isDetected($ship, $gamedata);

				foreach ($enemyTeams as $teamId) {
					if (in_array($teamId, $detectingTeams)) {
						$notekey   = 'detected';
						$noteHuman = $noteHuman1;
						$noteValue = "Team:" . $teamId;							
					} else {
						$notekey   = 'undetected';
						$noteHuman = $noteHuman2;
						$noteValue = "Team:" . $teamId;							
					}

					$note = new IndividualNote(
							-1,
							$gamedata->id,
							$gamedata->turn,
							$gamedata->phase,
							$ship->id,
							$this->id,
							$notekey,
							$noteHuman,
							$noteValue
					);
					Manager::insertIndividualNote($note);	
				}
			} else {
				foreach ($enemyTeams as $teamId) {
					$notekey   = 'detected';
					$noteHuman = $noteHuman3; //Not cloaked or was cloaked and then turned off.
					$noteValue = "Team:" . $teamId;						

					$note = new IndividualNote(
							-1,
							$gamedata->id,
							$gamedata->turn,
							$gamedata->phase,
							$ship->id,
							$this->id,
							$notekey,
							$noteHuman,
							$noteValue
					);
					Manager::insertIndividualNote($note);	
				}
			}
		}


		private function isDetected($ship, $gameData) {

			// Check all enemy ships to see if any can detect this ship at end of turn
			//$blockedHexes = $gameData->getBlockedHexes(); //Just do this once outside loop
			$blockedHexes = $gameData->blockedHexes; //Just do this once outside loop			
			$pos = $ship->getHexPos(); //Just do this once outside loop		

			$detectedTeams = array();

			foreach ($gameData->ships as $otherShip) {
				// Skip friendly ships
				$teamId = (int)$otherShip->team;
				if($teamId == $ship->team) continue;
				if($otherShip->isTerrain()) continue; //Ignore Terrain
				if($otherShip->isDestroyed()) continue; //Ignore destroyed enemy ships.
				if(in_array($teamId, $detectedTeams)) continue;
		
				$totalDetection = 0;
		
				if (!$otherShip instanceof FighterFlight) {
					if($otherShip->isDisabled()) continue;
					// Not a fighter — use scanner systems
					foreach($otherShip->systems as $system){
						if($system instanceof Scanner){
							if(!$system->isDestroyed() && !$system->isOfflineOnTurn()) $totalDetection += $system->output;
						}
					}	
					// Apply detection multiplier based on ship type
					if ($otherShip->base) {
						$totalDetection = floor($totalDetection * 1.5);
					} elseif ($otherShip->hasSpecialAbility("ELINT")) {
						//$totalDetection *= 1;				
						$totalDetection = floor($totalDetection * 1.5);
//						$bonusDSEW = $otherShip->getEWByType("Detect Stealth", $gameData->turn);	
//						$totalDetection += $bonusDSEW;
		
					} else {
//						$totalDetection = floor($totalDetection/2); //Half sensor rating
						$totalDetection = floor($totalDetection); //Sensor rating
					}
				} else {
					// Fighter unit — use offensive bonus
					$totalDetection = ceil($otherShip->offensivebonus/2);// Half of OB.
				}
		
				// Get distance to the stealth ship and check line of sight
				$distance = mathlib::getDistanceHex($ship, $otherShip);
				$otherPos = $otherShip->getHexPos();          
				$noLoS = !empty($blockedHexes) && Mathlib::isLoSBlocked($pos, $otherPos, $blockedHexes);

				// If within detection range, and LoS not blocked the ship is detected
				if ($totalDetection >= $distance && !$noLoS) {  
					$detectedTeams[] = $teamId;
				}
			}

			return $detectedTeams; //Return all teams that can detect the ship.		
		}	


		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->detected = $this->detected;
			if (isset($this->detectedNew) && !empty($this->detectedNew)) {
				$strippedSystem->detectedNew = $this->detectedNew;
			}  					
			//$strippedSystem->detectedNew = is_array($this->detectedNew) ? $this->detectedNew : array();
			$strippedSystem->active = $this->active;				        
			return $strippedSystem;
		}

	} //endof CloakingDevice



class TrekEarlyDisruptor extends Pulse{
        public $name = "TrekEarlyDisruptor";
        public $displayName = "Early Disruptor";
	    public $iconPath = 'TrekEarlyDisruptor.png';

        public $animation = "bolt";
        public $animationColor = array(142, 252, 13);

		public $rof = 2;
        public $grouping = 20;
        public $maxpulses = 3;
 		protected $useDie = 2; //die used for base number of hits
       
        public $loadingtime = 2;
        public $intercept = 2;
        public $ballisticIntercept = true;
        public $priority = 5;

    	public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Molecular"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
        
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 6;
			}
			if ( $powerReq == 0 ){
				$powerReq = 4;
			}		
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1) + 6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 13 ;      }
		
    }  //end of class TrekEarlyDisruptor



class TrekLightDisruptorArray extends Weapon{
		public $name = "TrekLightDisruptorArray";
        public $displayName = "Light Disruptor Array";
        public $iconPath = "TrekDisruptorArray.png"; 
        public $animation = "laser";
        public $animationColor = array(142, 252, 13);

        public $raking = 8;
        
        public $intercept = 2;
		public $priority = 6; //technically light Raking, but borderline - and they're by far heaviest weapons that Federation has - hence 'heavy Raking' status
		public $priorityArray = array(1=>6, 2=>6);		
		public $priorityAF = 4; //both Lance and full Phaser are treated as heavy vs fighters
		
        public $loadingtime = 1;
    	public $gunsArray = array(1=>3, 2=>1); //three individual shots, can combine into one
		
        public $rangePenaltyArray = array(1=>1.33, 2=>1.33);
        public $fireControlArray = array( 1=>array(3, 2, 2), 2=>array(3, 2, 2) ); 

        public $damageType = "Raking";
		public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); 
		public $weaponClass = "Molecular";
		public $weaponClassArray = array(1=>'Molecular', 2=>'Molecular');
		public $firingModes = array( 1 => "Multiple", 2=> "Combined");
		public $firingMode = 1;

	 	public function getInterceptRating($turn){
			return 2;
		}

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
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
			$this->data["Special"] .= "Can fire as either:";  
			$this->data["Special"] .= "<br> - Multiple: 3 shots, 1d10+3 damage"; 
			$this->data["Special"] .= "<br> - Combined: 1 shot, 3d10+6 damage"; 
		}
	
		public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(10, 1)+3; //Multiple
					break;
				case 2:
					return Dice::d(10, 3)+6; //Combined
					break;	
			}
		}

 		public function setMinDamage(){
			switch($this->firingMode){
				case 1:
					$this->minDamage = 4; //Multiple
					break;
				case 2:
					$this->minDamage = 9; //Combined
					break;	
			}
		}
             
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 13; //Multiple
					break;
				case 2:
					$this->maxDamage = 36; //Combined
					break;	
			}
		}
		
}//end of class TrekLightDisruptorArray


class TrekLightDisruptor extends Pulse{
        public $name = "TrekLightDisruptor";
        public $displayName = "Light Disruptor";
	    public $iconPath = 'TrekLtDisruptor.png';

        public $animation = "bolt";
        public $animationColor = array(142, 252, 13);

		public $rof = 3;
        public $grouping = 20;
        public $maxpulses = 5;
 		protected $useDie = 3; //die used for base number of hits
       
        public $loadingtime = 1;
        public $intercept = 2;
		public $priority = 4; //light Raking	
		public $priorityAF = 5; //...Standard vs fighters

    	public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Molecular"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
        
        public $rangePenalty = 1.32;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
			if (!$system->advancedArmor){//advanced armor prevents effect
				if (!$ship instanceof FighterFlight) {
					if ($system instanceof Structure) return;        // don't apply to structure
					if (($damage-$armour) > 0) {
						$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
						$crit->updated = true;
						$crit->inEffect = true; //in effect immediately, affecting further damage in the same turn!
						$system->setCritical($crit); //$system->criticals[] =  $crit;
					}
				}
			}
		}

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
			$this->data["Special"] .= "-1 armor to every system hit as long as at least 1 damage scored on the system.";
            $this->data["Special"] .= '<br>Structure armor unaffected.';
        }
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 6;
			}
			if ( $powerReq == 0 ){
				$powerReq = 3;
			}		
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(6, 2) + 2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
		
    }  //end of class TrekLightDisruptor

class TrekMedDisruptor extends Pulse{
        public $name = "TrekMedDisruptor";
        public $displayName = "Medium Disruptor";
	    public $iconPath = 'TrekMedDisruptor.png';

        public $animation = "bolt";
        public $animationColor = array(142, 252, 13);

		public $postArmorTotal = 0;

		public $rof = 3;
        public $grouping = 20;
        public $maxpulses = 4;
 		protected $useDie = 3; //die used for base number of hits
       
        public $loadingtime = 2;

		public $reduceFacing = 12; //reduce armor of facing structure (compard with total damage done - Armor reduced if damage exceeds this variable)
		private $alreadyReduced = false;  //we only want the disruptor to reduce the structure armor once per shot

        public $ballisticIntercept = true;
        public $intercept = 2;
		public $priority = 5; //light Raking	
		public $priorityAF = 5; //...Standard vs fighters

    	public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Molecular"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
        
        public $rangePenalty = 0.66;
        public $fireControl = array(2, 3, 3); // fighters, <mediums, <capitals

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
			if ($system instanceof Structure) {  //Only checking for structure damage
				$this->postArmorTotal += max(0, $damage - $armour); //clamp this so that you do not remove damage scored if armor exceeds damage
//				$fireOrder->pubnotes .= "<br>Total Damage is: $this->postArmorTotal";
//				$fireOrder->pubnotes .= "<br>reduceFacing is: $this->reduceFacing";
			}
			if (!$system->advancedArmor){//advanced armor prevents effect
				if ($ship instanceof FighterFlight) return;  //does not affect fighters
				if ($system instanceof Structure) {
					//no need to do anything here
				}else{
					if (($damage-$armour) > 0) {
						$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
						$crit->updated = true;
						$crit->inEffect = true; //in effect immediately, affecting further damage in the same turn!
						$system->setCritical($crit); //$system->criticals[] =  $crit;	
					}
				}
				if(!$this->alreadyReduced){ //if enough damage gets through shields and armor, structure armor is reduced
					if ($this->postArmorTotal >= $this->reduceFacing) {
						$target = $ship;
						$shooter = $this->getUnit();
						$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
						$struct = $target->getStructureSystem($sectionFacing);
						if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
							$this->alreadyReduced = true; //do this only for first part of shot that actually connects
							$crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
							$crit->updated = true;
							$crit->inEffect = false;
							$struct->criticals[] = $crit;
						}					
					}
				}
			}
		}

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
			$this->data["Special"] .= "-1 armor to every system hit as long as at least one damage scored on the system.";
            $this->data["Special"] .= '<br>If 12 or more damage strike the facing structure, the facing structure armor loses 1 point.';
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 8;
			}
			if ( $powerReq == 0 ){
				$powerReq = 6;
			}		
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(6, 2) + 4;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;      }
		
    }  //end of class TrekMedDisruptor

class TrekDisruptorCannon extends TrekDisruptorBase {
		public $name = "TrekDisruptorCannon";
        public $displayName = "Disruptor Cannon";
        public $iconPath = "TrekDisruptorCannon.png";
        public $animation = "laser";
        public $animationColor = array(142, 252, 13);

        public $raking = 10;

		public $postArmorTotal = 0;
        
		public $priority = 7; //light Raking		
		public $priorityAF = 3; //Standard
		
        public $loadingtime = 2;

		public $reduceFacing = 12; //reduce armor of facing structure (compard with total damage done - Armor reduced if damage exceeds this variable)
		private $alreadyReduced = false;  //we only want the disruptor to reduce the structure armor once per shot
		
        public $rangePenalty = 0.66;
        public $fireControl = array(1, 2, 3);

        public $damageType = "Raking";
		public $weaponClass = "Molecular";
		public $firingModes = array( 1 => "Raking");

       public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
			$this->data["Special"] .= "If 12 or more damage breach shields and armor, the facing structure block loses 1 point of armor.";
        }

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 8;
			if ( $powerReq == 0 ) $powerReq = 6;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
			$this->postArmorTotal += max(0, $damage - $armour); //clamp this so that you do not remove damage scored if armor exceeds damage

//			$fireOrder->pubnotes .= "<br>Total Damage is: " . $this->postArmorTotal;
//			$fireOrder->pubnotes .= "<br>reduceFacing is: " . $this->reduceFacing;
//			$fireOrder->pubnotes .= "<br>already reduced outside is: " . $this->alreadyReduced;

			if($system->advancedArmor) return; //advanced armor prevents this
			if ($ship instanceof FighterFlight) return;  //does not affect fighters
			if(!$this->alreadyReduced){
				if ($this->postArmorTotal >= $this->reduceFacing) {

					$target = $ship;
					$shooter = $this->getUnit();
					$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
					$struct = $target->getStructureSystem($sectionFacing);
					if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
						$this->alreadyReduced = true; //do this only for first part of shot that actually connects
						$crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
						$crit->updated = true;
						$crit->inEffect = false;
						$struct->criticals[] = $crit;
					}					
				}
			}
		}

        public function getDamage($fireOrder){        return Dice::d(10, 3) + 10;   }
        public function setMinDamage(){     $this->minDamage = 13 ;      }
        public function setMaxDamage(){     $this->maxDamage = 40 ;      }

}//end of class TrekDisruptorCannon

    class TrekKlingonLauncher extends Torpedo{
        public $name = "TrekKlingonLauncher";
        public $displayName = "Klingon Launcher";
        public $iconPath = "TrekKlingonLauncher.png";
        public $range = 25;  
		public $distanceRange = 40;
        public $loadingtime = 2;
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!
        
        public $weaponClass = "Molecular"; 
        public $damageType = "Standard"; 
        
        public $fireControl = array(0, 2, 3); // fighters, <mediums, <capitals 
		public $rangePenalty = 0.5; //-1/2 hexes - BUT ONLY AFTER 10 HEXES
        
        public $animation = "torpedo";
        public $animationColor = array(142, 252, 13);

        public $priority = 5; //heavy Standard

		public $postArmorTotal = 0;

		public $reduceFacing = 12; //reduce armor of facing structure (compard with total damage done - Armor reduced if damage exceeds this variable)

		private $alreadyReduced = false;  //we only want the disruptor to reduce the structure armor once per shot

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 10;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
            	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "This weapon suffers range penalty (like direct fire weapons do), but only after first 10 hexes of distance.";
		$this->data["Special"] .= "<br>If 12 or more damage breach shields and armor, the facing structure block loses 1 point of armor.";
	}
        
		public function calculateRangePenalty($distance){
			$rangePenalty = 0;//base penalty
			$rangePenalty += $this->rangePenalty * max(0,$distance-10); //everything above 10 hexes receives range penalty
			return $rangePenalty;
		}

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
			$this->postArmorTotal += max(0, $damage - $armour); //clamp this so that you do not remove damage scored if armor exceeds damage

//			$fireOrder->pubnotes .= "<br>Total Damage is: " . $this->postArmorTotal;
//			$fireOrder->pubnotes .= "<br>reduceFacing is: " . $this->reduceFacing;
//			$fireOrder->pubnotes .= "<br>already reduced outside is: " . $this->alreadyReduced;

			if($system->advancedArmor) return; //advanced armor prevents this
			if ($ship instanceof FighterFlight) return;  //does not affect fighters
			if(!$this->alreadyReduced){
				if ($this->postArmorTotal >= $this->reduceFacing) {

					$target = $ship;
					$shooter = $this->getUnit();
					$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
					$struct = $target->getStructureSystem($sectionFacing);
					if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
						$this->alreadyReduced = true; //do this only for first part of shot that actually connects
						$crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
						$crit->updated = true;
						$crit->inEffect = false;
						$struct->criticals[] = $crit;
					}					
				}
			}
		}


        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+10;    }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class TrekKlingonLauncher






class CombatTransporter extends Weapon{
	public $name = "CombatTransporter";
	public $displayName = "Combat Transporter";
	public $iconPath = "TrekCombatTransporter.png";
	public $animation = "trail";
	public $animationColor = array(50, 50, 50);
	public $animationWidth = 0.2;

	public $useOEW = false; 
	public $range = 10;
  
	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table, should never happen.

	public $calledShotMod = 0; //instead of usual -8
	
	public $loadingtime = 1;
	public $rangePenalty = 1;

	public $noOverkill = true;
	public $priority = 9;
	
	public $uninterceptable = true; 
	public $doNotIntercept = true;			

	public $damageType = "Special";
	public $damageTypeArray = array(1=> "Special", 2=> "Standard", 3=> "Special");	
	public $weaponClass = "Matter";
	public $firingModes = array(
		1 => "Capture Ship",
		2 => "Sabotage",
		3 => "Rescue"
	);		

	public $eliteMarines = false;
	public $isBoardingAction = true;//For front end to recalculate hit chance.	
		
	public $ammunition = 0; //limited number of Marine contingents.

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition, $elite) 
		{
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition, $elite); //Parent routines take care of the rest
			$this->ammunition = $ammunition;			
			$this->eliteMarines = $elite;
        }
        
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);      
		$this->data["Special"] = "<br>If on same hex as an enemy ship, and in arc, this weapon attempt to deliver Marines to that vessel.";	
		$this->data["Special"] .= "<br>Marines may attempt three 'Missions' by selecting the appropriate Firing Mode.";  		
		$this->data["Special"] .= "<br> - Capture Ship: Marines can attempt to overcome defenders on enemy ship and disable it."; 
		$this->data["Special"] .= "<br> - Sabotage: Can be directed at a specific system (i.e. called shot) or for general sabotage operations on enemy ship."; 
		$this->data["Special"] .= "<br> - Rescue: Scenarios only, Marines will board enemy ship and attempt to rescue target."; 
		$this->data["Special"] .= "<br>See 'Common Systems & Enhancements' file for full information on Boarding Actions.";  		                     
		if($this->eliteMarines){
			$this->data["Elite"] = "Yes";
		}else{
			$this->data["Elite"] = "No";			
		}
	    $this->data["Ammunition"] = $this->ammunition;					
	}




       public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }




	public function calculateHitBase($gamedata, $fireOrder)
	{
		//Needs it's own custom routine for hit chance.
		$shooter = $gamedata->getShipById($fireOrder->shooterid);	
		$target = $gamedata->getShipById($fireOrder->targetid);			

		if($target->factionAge > 2) {//Cannot attach to Ancients.  Might be impossible if Front End chance is also made 0%
			$fireOrder->pubnotes .= "<br> Cannot transport to Ancient ships.";
			$fireOrder->needed = 0;
			$fireOrder->updated = true;						
			return; 
		}
		
		//Now roll to see if the Breaching Pod attaches on this turn.
//		$shooterMove = $shooter->getLastMovement();
//		$shooterSpeed = $shooterMove->speed;		
		
//		$targetMove = $target->getLastMovement();
//		$targetSpeed = $targetMove->speed;
//		$speedDifference = abs($targetSpeed - $shooterSpeed);//Calculate absolute difference in speed.
//		if($shooter->faction == "Llort" || $shooter->faction == "ZNexus Sal-bez Coalition") $speedDifference -= 1;//Llort reduce speed difference by 1.
			
//		$finalSpeedDifference = 0;
		
//		if($finalSpeedDifference > $shooter->freethrust){//Pod cannot compensate enough for speed difference with available thrust.
//			$fireOrder->needed = 0;
//			$fireOrder->updated = true;
//			$fireOrder->pubnotes .= "<br> The speed difference to target is too great and pod is unable to attach.";					
//			return; 
//		}

        $hitLoc = null;
        $hitLoc = $target->getHitSection($shooter, $fireOrder->turn);
		
//		if($targetSpeed > $shooterSpeed){//Target is moving faster, roll to attach.
			$baseHitChance = 100;//Start with automatic hit.
//			$speedChance = 	$finalSpeedDifference *10;//Each point of speed difference is 10% chance to miss.
//			$finalHitChance = $baseHitChance - $speedChance;//Adjust hitchance.
			$finalHitChance = $baseHitChance;
			$fireOrder->needed = $finalHitChance;//Update fireOrder.		
			$fireOrder->updated = true;	
			$fireOrder->chosenLocation = $hitLoc;//Need to mark this for successful shots to check if hitting Primary.									
//			return;
//		}else{
//			$fireOrder->needed = 100;
//			$fireOrder->updated = true;
//			$fireOrder->chosenLocation = $hitLoc;			
//			return;
//		}	
		
	}//endof calculateHitBase





       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);

			if($fireOrder->rolled <= $fireOrder->needed){//Only reduce ammo if Marines successfully boarded enemy ship.

				$this->ammunition--;//Deduct Marine unit just used.			

				//Need to remove Enhancement bonuses from saved ammo count, as these will be re-added in onConstructed()
				$ship = $gamedata->getShipById($fireOrder->shooterid);
	
				foreach ($ship->enhancementOptions as $enhancement) {
					$enhID = $enhancement[0];
					$enhCount = $enhancement[2];		        
					if($enhCount > 0) {		            
						if ($enhID == 'EXT_MAR') $this->ammunition -= $enhCount;       	
					}
				}	
				Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			}

        }

	public static function recordBoarding($targetId) {
	    Marines::$boardedThisTurn[] = $targetId;
	}	

	private function getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder){
		$rollMod = 0;
		if($this->eliteMarines) $rollMod -= 1; //Elite Marines board more easily.

		if($target->faction == "Narn Regime" || $target->faction == "Gaim Intelligence" )	$rollMod += 1; //Certain factions defend harder! 

		$location = $fireOrder->chosenLocation ;
		if($location == 0 && (!$target instanceof OSAT)) $rollMod -= 1; //Easier to deliver marines to destroyed sections i.e direct to Primary section.	       

		foreach ($target->enhancementOptions as $enhancement) {//Defender quality can influence roll too.
		    $enhID = $enhancement[0];
			$enhCount = $enhancement[2];		        
			if($enhCount > 0) {		            
		        if ($enhID == 'ELITE_CREW') $rollMod += $enhCount;	//Elite Crews are better at defending.
		        if ($enhID == 'POOR_CREW') $rollMod -= $enhCount; //Poor Crews are worse.
		        if ($enhID == 'MARK_FERV') $rollMod += $enhCount; //Markab Fervor causes defenders to fight harder.		        	
			}
		}

        return $rollMod;
        		
	}//endof getDeliveryRollMod
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!

		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
			
		if ($system->advancedArmor) {//no effect on Advanced Armor for Younger Races equipped with this e.g. Shadow Omega.	
			$fireOrder->pubnotes .= "<br> Marines cannot attack systems with advanced armor.";				
			return; 	
		}

		//Can proceed with boarding actions, roll to see if Marines are delivered.		
		$rollMod = $this->getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder);		
		$deliveryRoll = max(0, Dice::d(10) + $rollMod);		

		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.		
		foreach($cnc->criticals as $critDisabled){
			if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) $deliveryRoll = 1;//Ship captured, auto success.		
		}		
		
		if($deliveryRoll <= 5){ //successful delivery, continue with applying critical effects.						
				
			switch($this->firingMode){
								
				case 1://Capture

					$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt to capture enemy ship next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new CaptureShipElite(-1, $ship->id, $cnc->id, 'CaptureShipElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}else{//Not Elite Marines					
									$crit = new CaptureShip(-1, $ship->id, $cnc->id, 'CaptureShip', $gamedata->turn+1);  //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }				
				
					break;

				case 2://Sabotage

					if($fireOrder->calledid != -1 && !($system instanceof Structure) && $system->location != 0){//Is a called shot and not structure, place crit on system.
							$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt to sabotage " . $system->displayName ." system next turn.";
						if($this->eliteMarines){//Are Marines Elite?
							$crit = new SabotageElite(-1, $ship->id, $system->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}else{//Not Elite Marines			
							$crit = new Sabotage(-1, $ship->id, $system->id, 'Sabotage', $gamedata->turn+1); //Takes effect next turn.
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}	
					}else{ //Has targeted ship generally, not a specific system.  Apply crit to CnC.
						$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt sabotage operations on enemy ship next turn.";								
							if($cnc){
									if($this->eliteMarines){//Are Marines Elite?
										$crit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
									}else{//Not Elite Marines					
										$crit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
									}							    		
				            }				
					}	
					
					break;				
				
				case 3://Rescue

					$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt their rescue mission next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new RescueMissionElite(-1, $ship->id, $cnc->id, 'RescueMissionElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note marines have boarded this turn
								}else{//Not Elite Marines					
									$crit = new RescueMission(-1, $ship->id, $cnc->id, 'RescueMission', $gamedata->turn+1);  //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }	
				
					break;			
				
			}
		}elseif($deliveryRoll >= 6 && $deliveryRoll <=8){//Unsuccessful delivery
			$this->ammunition++;//Marines weren't eliminated, they just weren't delivered.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit was beaten back by defenders but managed to return safely to their pod.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
			return;	
		}else{//Roll result is 9 or over
			$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit was eliminated by defenders whilst trying to board the enemy ship.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.								
			return;
		}			
	}//endof onDamagedSystem() 		
	
	
	public function getDamage($fireOrder){ //Damage is handled in criticalPhaseEffects()
		return 0;
	}

	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }

	public function stripForJson() {
			$strippedSystem = parent::stripForJson();    
			$strippedSystem->ammunition = $this->ammunition;			
			$strippedSystem->isBoardingAction = $this->isBoardingAction;                          
			return $strippedSystem;
	}	
	
} //endof class CombatTransporter


//class MicroJumpSystem extends Weapon implements SpecialAbility, DefensiveSystem{
class MicroJumpSystem extends Weapon implements SpecialAbility{	
    public $name = "MicroJumpSystem";
    public $displayName = "Warp Drive";
	public $noProjectile = true;	
	public $specialAbilities = array("PreFiring");
	public $specialAbilityValue = true; //so it is actually recognized as special ability!  		
	
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Particle";
	public $hextarget = true;
	public $hidetarget = false;
	public $uninterceptable = true; //although I don't think a weapon exists that could intercept it...
	public $doNotIntercept = true; //although I don't think a weapon exists that could intercept it...
	public $priority = 1;
	
	public $range = 0;
	public $loadingtime = 4;
    public $rangePenalty = 0;
	
	public $animation = "blink";
	public $animationColor = array(255, 255, 0);
	public $animationExplosionScale = 0.3; //single hex explosion
	public $animationExplosionType = "AoE";
	
	public $firingModes = array(
		1 => "Warp Jump"
	);
	public $preFires = true;
	protected $shootsStraight = true; //Denotes for Front End to use Line Arcs, not circles.
	protected $specialArcs = true;	//Denotes for Front End to redirect to weapon specific function to get arcs.			
	public $repairPriority = 6;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	private $baseLoadingTime = 4; //Can be altered by a IncreasedRecharge1 critical, so we need to remember it's base value.
    public $ignoresLoS = false; //I assume we're not warping thru Terrain.

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $range, $loading){
		if ( $maxhealth == 0 ) $maxhealth = 9; //Set these as you like.
        if ( $powerReq == 0 ) $powerReq = 0;                           
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $this->startArc = $startArc;       	
        $this->endArc = $endArc;
        $this->range = $range; //Set range value passed from SCS
 		$this->outputDisplay = $this->range;
		$this->loadingtime = $loading; //Set loading values in SCS too so we can alter for more modern Trek ships		  
		$this->baseLoadingTime = $loading;       
    }

	public function onConstructed($ship, $turn, $phase){
		parent::onConstructed($ship, $turn, $phase);

		foreach($ship->systems as $system){
			if($system->displayName == "Nacelle"){
				if ( $system->maxhealth <= $system->getRemainingHealth() ) continue; //skip undamaged systems...
            	$currentDamage = $system->maxhealth - $system->getRemainingHealth();				
				if($currentDamage >= (ceil($system->maxhealth/2))){
					$this->range = max(0, $this->range - 2); //Reduce by 1 for each Nacelle that has taken more than 50% damage.
					$this->outputDisplay = max(0, $this->outputDisplay - 2);		
				} 
			}
		}			

	}

    protected $possibleCriticals = array(
            20 =>array("IncreasedRecharge1")
	);

	//Required to be overwritten in weapon for IncreasedRecharge1 crit to function. 
    public function getLoadingTime()
    {
		$loadingTime = $this->baseLoadingTime;
		$critPenalty = $this->hasCritical("IncreasedRecharge1");

		$combinedLoadingTime = $loadingTime + $critPenalty;
        return $combinedLoadingTime;
    }

	//Required to be overwritten in weapon for IncreasedRecharge1 crit to function. 
    public function setLoading($loading){
		if (!$loading)
			return;

		$this->overloadturns = $loading->overloading;
		$this->overloadshots = $loading->extrashots;

		$critsLastTurn = 0;

		$critPenalty = $this->hasCritical("IncreasedRecharge1");
		if($critPenalty > 0){		
			foreach($this->criticals as $crit){
				if ($crit->phpclass === "IncreasedRecharge1" && $crit->turn == TacGamedata::$currentTurn-1) {
					$critsLastTurn++;	
				}
			}
		}

		if(TacGamedata::$currentPhase == -1){ //Only adjust oading during Deployment loading of new turn.

			if($loading->loadingtime == $loading->loading){ //If Transverse Drive was fully charged, keep it fully charged.
				$this->turnsloaded = $loading->loading + $critsLastTurn;
			}else{
				$this->turnsloaded = $loading->loading;	//Else, it wasn't fully charged so keep number of turns loaded the same, just loadingtime ceiling increases.
			} 
		}else{
			$this->turnsloaded = $loading->loading;			
		}	

        $this->loadingtime = $loading->loadingtime;
        $this->firingMode = $loading->firingmode;
    }

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}
	/*
	public function getDefensiveType()	{
		return "Blink"; //Different category so it works in parallel with EM Shield effect of Shading Field
	}

	//I kept the ballistic hit change mod after a jump, seem appropriate but can be removed if Wolfgang doesn't want it
	public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon) {
		// Only applies to ballistic weapons
		if (!$weapon->ballistic) {
			return 0;
		}

		// Retrieve fire orders for this turn
		$firingOrders = $this->getFireOrders($turn);

		// Find the first normal fire order
		$hasFireOrder = null;
		foreach ($firingOrders as $fireOrder) {
			if ($fireOrder->type === 'prefiring') {
				$hasFireOrder = $fireOrder;
				break;
			}
		}

		// No transverse jump, return 0
		if ($hasFireOrder === null) {
			return 0;
		}

		if($hasFireOrder->shotshit == 1){ //Transverse jump was successful!
			// Extract the 'dis' value from fire order notes (example: "shooter: 2,-2 target: 2,0 dis: 2")
			$notes = $hasFireOrder->notes;
			$dis = 0; // default value

			if (preg_match('/dis:\s*(\d+)/', $notes, $matches)) {
				$dis = (int)$matches[1];
			}

			// Multiply distance by 2 to get the modifier, adjust as needed.
			$mod = $dis * 2;

			return $mod;
		}else{
			return 0;
		}	
	}

	public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
		return 0;
	}
	*/
	public function calculateHitBase($gamedata, $fireOrder)
	{
		//reduce by distance...
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$firingPos = $shooter->getHexPos();
		if ($fireOrder->targetid != -1) { //for some reason ship was targeted!
			$targetship = $gamedata->getShipById($fireOrder->targetid);
			//insert correct target coordinates: target ships' position!
			$targetPos = $targetship->getHexPos();
			$fireOrder->x = $targetPos->q;
			$fireOrder->y = $targetPos->r;
			$fireOrder->targetid = -1; //correct the error
		}
		$targetPos = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
		$dis = mathlib::getDistanceHex($firingPos, $targetPos);
		$fireOrder->needed = 100;
		$fireOrder->notes .=  "shooter: " . $firingPos->q . "," . $firingPos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis ";
		$fireOrder->updated = true;
	}
	
    public function fire($gamedata, $fireOrder){ 

        $ship = $gamedata->getShipById($fireOrder->shooterid); 
		$shipPos = $ship->getHexPos(); 		
		
        $rolled = Dice::d(20); //Roll d20 to decide what happens during jump
        //$rolled = 1; //For debugging

		$targetPos = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $dis = mathlib::getDistanceHex($shipPos, $targetPos); //How many hexes did player choose to jump. 

		//Then see what was rolled and create movement orders/crits accordingly.
		if($rolled <= 16){	//E.g. 1-16 successful

        	$fireOrder->rolled = $rolled; 
			$fireOrder->shotshit++; //Mark hit.

			$this->doWarpJump($gamedata,$targetPos, $ship, $dis);
			$fireOrder->pubnotes .= " Warp Drive activates succesfully and moves ship to new hex.";

		//I've removed the deviation logic for Mirco Jump atm, not sure if this is required.  This means there's just a 20% chance of failure. 			
		/*} else if($rolled >= 17 && $rolled <= 18){  //17 - Success but towards a different counterclockwise/clockwise hex facings.
			$newPos = null;

        	$fireOrder->rolled = $rolled; 

			$originalBearing = $ship->getBearingOnPos($targetPos); //0, 60, 120. 180, 240 or 300

			if($rolled == 17){
				$shipFacing = $ship->getFacingAngle();
				$relative = Mathlib::addToDirection($originalBearing, -60);
				$absoluteBearing = Mathlib::addToDirection($shipFacing, $relative);

				// Get actual position in new direction
				$newPos = Mathlib::moveInDirection($shipPos, $absoluteBearing, $dis);

			    $fireOrder->pubnotes .= " Transverse Drive activates succesfully, but the direction travelled is changed by 60 degrees counter-clockwise.";											
			}else{
				$shipFacing = $ship->getFacingAngle();
				$relative = Mathlib::addToDirection($originalBearing, 60); //Bearing from ship heading.
				$absoluteBearing = Mathlib::addToDirection($shipFacing, $relative); //Objective Bearing on map
				// Get actual position in new direction
				$newPos = Mathlib::moveInDirection($shipPos, $absoluteBearing, $dis);

			    $fireOrder->pubnotes .= " Transverse Drive activates succesfully, but the direction travelled is changed by 60 degrees clockwise.";												
			}
		
			$this->doWarpJump($gamedata,$newPos, $ship, $dis);
			
			//Update fireOrder details with new targetPos		
			$fireOrder->x = $newPos->q;
			$fireOrder->y = $newPos->r;
        	$fireOrder->updated = true;
			$fireOrder->shotshit++; //Mark hit.	

		*/} else if($rolled == 19){ //19 - Fail, test jump engine for explosion then return.
        	$fireOrder->rolled = $rolled; 

			$fireOrder->pubnotes .= " Warp jump attempt was unsuccesful!";						
			return;

		} else if($rolled >= 20){ //20- Fail with ForcedOfflineOneTurn Crit
        	$fireOrder->rolled = $rolled; 

			//Force cooldown as crit failue, if ship didn't blow up :)
			$crits = array(); 
			$crits = $this->testCritical($ship, $gamedata, $crits); //Need to force critical test outside normal routine

			$fireOrder->pubnotes .= " Warp Drive failed to jump and has rolled for potential critical effect.";					
			return;
		}	

	} //endof function fire	

	private function doWarpJump($gamedata, $targetPos, $ship, $distance){
	
		$lastMove = $ship->getLastMovement();
		
		//Create new movement orders to $targetPos.
        $transverseJump = new MovementOrder(null, "prefire", new OffsetCoordinate($targetPos->q, $targetPos->r), 0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing, false, $gamedata->turn, $distance, 0);

		//Add Tranverse movement order to database
		Manager::insertSingleMovement($gamedata->id, $ship->id, $transverseJump);		

		/*
		//No need to check for collision now, as there's no deviation and Warp Jump obeys Line of Sight at Targeting
		$crashableShips = array();
		$shipPoS = $targetPos;

		foreach($gamedata->ships as $otherShip){
			if($otherShip->isDestroyed()) continue; //Ignore destroyed ships
			if(!$otherShip->isTerrain() && !$otherShip->Enormous) continue;	//Don't add non-Terrain or non-Enormous units
			if($otherShip->getTurnDeployed($gamedata) > $gamedata->turn) continue; //Ship not deployed yet.	Shouldn't happen for Enormous units...
			$dist = mathlib::getDistance($transverseJump->getCoPos(), $otherShip->getCoPos());									
			if($dist <= 8) $crashableShips[] = $otherShip;	//You can only jump 3 hexes, and max terrain radiius is 3, but let's add a 2 hex buffer to future proof.		
		}

		foreach($crashableShips as $crashShip){		
			$crashShipPoS = $crashShip->getHexPos();			
			//$collision = $this->checkForCollisions($ship, $transverseJump,  $gamedata, $crashShipPoS, $crashShip);

			if(!empty($collision)){
				foreach($collision as $crashShipId=>$location){ //Should only be one					
					$crashShip = $gamedata->getShipById($crashShipId);
					$rammingAttack = $crashShip->getSystemByName("RammingAttack");										
					$fire = new FireOrder(-1, 'prefiring', $crashShip->id, $ship->id, $rammingAttack->id, -1, $gamedata->turn,
						1, 100, 100, 1, 0, 0, $shipPoS->q,  $shipPoS->r, 'TerrainCrash', -1
					);
					$fire->chosenLocation = $location;							
					$fire->addToDB = true;		
					$this->fireOrders[] = $fire;
					$rammingAttack->fire($gamedata, $fire);					
				}		
			}			

		}		
		*/		
	}

	/* //Not needed, no check for collision necessary
	private function checkForCollisions($ship, $transverseJump, $gamedata, $terrainPosition, $crashShip){
	    $collisiontargets = array(); // Initialize array
		
		if ($crashShip->Huge > 0) { //Terrain occupies more than just 1 hex!  Need to check all of its hexes.
			// Add code that calls a new function, and replicates check below for all hexes within radius.
			$radiusHexes = mathlib::getNeighbouringHexes($terrainPosition, $crashShip->Huge);
                
			$startMove = $ship->getLastTurnMovement($gamedata->turn);
			$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.
			$previousFacing = $startMove->getFacingAngle();
							
			// Check if shipMove position matches any position in $radiusHexes
			$match = array_filter($radiusHexes, function($hex) use ($transverseJump) {
				return $hex['q'] == $transverseJump->position->q && $hex['r'] == $transverseJump->position->r;
			});
		
			// Or if it matches the centre position directly
			if (
				!empty($match) || 
				($transverseJump->position->q == $terrainPosition->q && $transverseJump->position->r == $terrainPosition->r)
				) {
							// Prevent duplicate ship IDs
				if (!isset($collisiontargets[$crashShip->id])) {
					$relativeBearing = $this->getTempBearing($previousPosition, $terrainPosition, $ship, $previousFacing);
					$location = $this->getCollisionLocation($relativeBearing, $ship);
					$collisiontargets[$crashShip->id] = $location; // Add to array to be targeted.
				}
			}
		}else{					
			// Now check other movements in the turn.
			$startMove = $ship->getLastTurnMovement($gamedata->turn);	//initialise as last move in previous turn, in case first move takes ship in asteroid.				
			$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.			 
			$previousFacing = $startMove->getFacingAngle();			
				
					// Check if the position matches the asteroids, e.g. zero distance.
					if ($terrainPosition->q == $transverseJump->position->q && $terrainPosition->r == $transverseJump->position->r) {
						$relativeBearing = $this->getTempBearing($previousPosition, $terrainPosition, $ship, $previousFacing);
						$location = $this->getCollisionLocation($relativeBearing, $ship);
						$collisiontargets[$crashShip->id] = $location; // Add to array to be targeted.
					}
		}

	    return $collisiontargets;		
		
	}//end of checkForCollisions()		


	private function getTempBearing($shipPosition, $asteroidPosition, $ship, $facing){
		$relativeBearing = 0;	
		$oPos = mathlib::hexCoToPixel($shipPosition);//Convert to pixel format		
		$tPos = mathlib::hexCoToPixel($asteroidPosition); //Convert to pixel format
				
		$compassHeading = mathlib::getCompassHeadingOfPoint($oPos, $tPos);//Get heading using pixel formats.
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$facing);//relative bearing, compass - current facing.
       
        if( Movement::isRolled($ship) ){ //if ship is rolled, mirror relative bearing.  Not really needed, since arcs don't actually change.  
            if( $relativeBearing !== 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }        

		return round($relativeBearing);//Round and return!
	}


	private function getCollisionLocation($relativeBearing, $target) {
		foreach ($target->getLocations() as $location) {
			$min = $location["min"];
			$max = $location["max"];
			
			// Normal range check
			if ($min < $max && $relativeBearing >= $min && $relativeBearing < $max) {
				return $location["loc"];
			}
			
			// Wrap-around range check (e.g., 330-30)
			if ($min > $max && ($relativeBearing >= $min || $relativeBearing < $max)) {
				return $location["loc"];
			}
		}
		
		return 0; // Should not happen but return default if so.
	} //endof getCollisionLocation()
	*/

/*
	public function setSystemDataWindow($turn){							
		$this->data["Special"] = "The warp jump is activated before the firing phase to";
		$this->data["Special"] .= "<br>jump the ship forward in a straight line.";
		$this->data["Special"] .= "<br>Cannot jump through enormous units. 20% chance of failure.";
		$this->data["Special"] .= "<br>There is a 3 turn cooldown before another jump can be initiated.";
		parent::setSystemDataWindow($turn);     

	}	
*/

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] = "The warp jump is activated before the firing phase to";
		$this->data["Special"] .= "<br>jump the ship forward in a straight line.";
		$this->data["Special"] .= "<br>Cannot jump through enormous units. 20% chance of failure.";
		$this->data["Special"] .= "<br>There is a 3 turn cooldown before another jump can be initiated.";
	}

	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->shootsStraight = $this->shootsStraight;
        $strippedSystem->specialArcs = $this->specialArcs;
        $strippedSystem->loadingtime = $this->loadingtime;	//With certain crits this can change for this weapon!													                                        
        return $strippedSystem;
	}	


}	

?>
