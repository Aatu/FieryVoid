<?php
/*file for Star Trek universe weapons*/


/*output is not recharge time (like for B5 warp drives), but rather impulse rating (eg. how much thrust is derived from this system...)*/
class TrekWarpDrive extends JumpEngine{
    //public $name = "TrekWarpDrive";
    public $displayName = "Warp Drive";
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
        $this->data["Special"] = "SHOULD NOT be shut down for power (unless damaged >50% or in desperate circumstances)."; 
        $this->data["Special"] .= "<br>Warp Drive provides impulse thrust as well - amount equals to system rating."; 
    }
}


//ImpulseDrive - basically an engine with rating calculated from ships' WarpDrive
//technical system, should never get hit.
//remember to plug WarpDrives to the ImpulseDrive at design stage!
class TrekImpulseDrive extends Engine{
	public $iconPath = "engineTechnical.png";
    public $name = "engine";
    public $displayName = "Engine";
    public $primary = true;
    public $isPrimaryTargetable = false;
    public $boostable = false;//cannot boost BioDrive!
    public $outputType = "thrust";
	
	private $warpDrives = array();
	
    
    public $possibleCriticals = array( ); //technical system, should never get damaged
    
    function __construct(){
        parent::__construct(0, 1, 0, 0, 0 ); //($armour, $maxhealth, $powerReq, $output, $boostEfficiency
    }
    
	function addThruster($thruster){
		if($thruster) $this->warpDrives[] = $thruster;
	}
	
	
	public function setSystemDataWindow($turn){
		//$this->output = $this->getOutput();	
		parent::setSystemDataWindow($turn); 	
		$this->output = $this->getOutput();	
		//$this->data["Efficiency"] = $this->boostEfficiency;
		$this->data["Special"] = "Impulse Drive - basically an Engine with basic output calculated from Warp Drive outputs.";      
		$this->data["Special"] .= "<br>Will never be damaged.";  
		$this->data["Special"] .= "<br>Cannot buy extra thrust.";    
		$this->data["Special"] .= "<br>If Warp Nacelle is shut down - Thrust effect will be visible only after committing orders.";   
	}
	
	
    public function getOutput(){
        $output = 0;
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
			if ($this->turnsloaded == 1)
			{
				return 2;
			}
			else
			{
				return 2;
			}
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
			$this->data["Special"] .= "<br> - 1 turn: 1d10+3, intercept -10"; 
			$this->data["Special"] .= "<br> - 2 turns: 2d10+12, intercept -10"; 
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




class TrekSpatialTorp extends Weapon{
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
        public $range = 15;
		public $guns = 1;
        
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
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+2;   
		}

        public function setMinDamage(){     $this->minDamage = 4;      }
        public function setMaxDamage(){     $this->maxDamage = 14;      }
		
}//endof TrekSpatialTorp



class TrekPhotonicTorp extends Weapon{
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
        public $range = 20;
		public $guns = 1;
        
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
			$this->data["Special"] = 'Benefits from offensive EW.';			
        }
        
        public function getDamage($fireOrder){ 
		
			return Dice::d(6, 2)+6;   
		}

        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 18;      }
		
}//endof TrekPhotonicTorp




?>
