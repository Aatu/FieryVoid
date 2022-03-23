<?php

 class CustomERLightPBeam extends Particle{
        public $name = "CustomERLightPBeam";
        public $displayName = "Extended Range Light Particle Beam";
		public $iconPath = "lightParticleBeamShip.png";
        public $animation = "bolt";
        public $animationColor = array(255, 250, 230);
        
        public $intercept = 2;
        public $loadingtime = 1;      
        
        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }

} //endof CustomERLightPBeam


class CustomLightMatterCannon extends Matter {
    /*Light Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customLightMatterCannon";
        public $displayName = "Light Matter Cannon";
	public $iconPath = "customLightMatterCannon.png";
        public $animation = "bolt";
        public $animationColor = array(250, 250, 190);
        public $priority = 6;

        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(-1, 3, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 2;
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
} //customLightMatterCannon



class CustomLightMatterCannonF extends Matter {
    /*fighter version of Light Matter Cannon, as used on Ch'Lonas fighters*/
    /*NOT done as linked weapon!*/
    /*limited ammo*/
        public $name = "customLightMatterCannonF";
        public $displayName = "Light Matter Cannon";
	public $iconPath = "customLightMatterCannon.png";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $priority = 9; //Matter weapon
        
        public $loadingtime = 3;
        public $ammunition = 6; //limited ammo!
        public $exclusive = false; //this is not an exclusive weapon!
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
	public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Ammunition"] = $this->ammunition;
        }	
	
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
        public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }        
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){   return  $this->minDamage = 5 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 14 ;      }
}//CustomLightMatterCannonF



class CustomHeavyMatterCannon extends Matter{
    /*Heavy Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customHeavyMatterCannon";
        public $displayName = "Heavy Matter Cannon";
		public $iconPath = "customHeavyMatterCannon.png";   
        public $animation = "bolt";
        public $animationColor = array(250, 250, 190);
        public $priority = 9;   

        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 3, 4); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 10;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 3)+5;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 35 ;      }
} //CustomHeavyMatterCannon




class CustomMatterStream extends Matter {
    /*Matter Stream, as used on Ch'Lonas ships*/
        public $name = "customMatterStream";
        public $displayName = "Matter Stream";
	public $iconPath = "customMatterStream.png";
        public $animation = "laser";
        public $animationColor = array(60, 60, 45);
        public $animationExplosionScale = 0.45; //deliberate, make it larger than automatically calculated
        public $priority = 7; //Matter Raking mode, but with low damage per rake so before other Matter weapons - together with heavy Raking weapons (I assume this weapon will be relatively lightly mitigated by multiple systems)
	
        public $loadingtime = 3;
        
        public $rangePenalty = 0.66; //-2 per 3 hexes
		
        public $raking = 6;
	
        // Set to make the weapon start already overloaded.
        public $alwaysoverloading = true;
        public $overloadturns = 3;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
	
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals 

        public $firingModes = array( 1 => "Sustained");	
        public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
        		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Does ".$this->raking." damage per rake.";
		$this->data["Special"] .= "<br>This weapon is always in Sustained mode";
	    	$this->data["Special"] .= "<br>Ignores armor, does not overkill.";		
	}	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 7;
            if ( $powerReq == 0 ) $powerReq = 6;
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function isOverloadingOnTurn($turn = null){
            return true;
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
} //customMatterStream



class CustomGatlingMattergunLight extends Pulse{
    /*Light Gatling Mattergun, as used on Ch'Lonas ships*/
        public $name = "customGatlingMattergunLight";
        public $displayName = "Light Gatling Mattergun";
		public $iconPath = "customGatlingMattergunLight.png";
        public $animation = "trail";
        public $animationColor = array(225, 225, 150);
        public $priority = 3; //very light weapons; at this damage output there's no point worrying about overkill lost, and this weapon should be great system sweeper

        public $grouping = 25; //+1 per 5
        public $maxpulses = 4;
        protected $useDie = 3; //die used for base number of hits
        public $loadingtime = 1;
        
        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
		
        public $noOverkill = true;//Matter weapons do not overkill
        public $ballisticIntercept = true; //can intercept ballistic weapons only
        public $intercept = 2;
        
		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
   
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 2;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn)
        {
            parent::setSystemDataWindow($turn);
			$this->data["Special"] .= "<br>Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
        }        
        
        public function getDamage($fireOrder){        return Dice::d(6, 1);   }
        public function setMinDamage(){     $this->minDamage = 1 ;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;      }
} //CustomGatlingMattergunLight




class CustomGatlingMattergunMedium extends Pulse{
    /*Gatling Mattergun, as used on Ch'Lonas ships*/
        public $name = "customGatlingMattergunMedium";
        public $displayName = "Gatling Mattergun";
		public $iconPath = "customGatlingMattergunMedium.png";
        public $animation = "trail";
        public $animationColor = array(225, 225, 150);
        public $priority = 5; //medium STandard weapons; their low and random damage output per shot doesn't qualify them for Matter damage step

        public $grouping = 20; //+1 per 4
        public $maxpulses = 4;
        protected $useDie = 2; //die used for base number of hits
        protected $fixedBonusPulses = 1; //d2+1 hits
        public $loadingtime = 2;
        
        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
		
        public $noOverkill = true;//Matter weapons do not overkill
        public $ballisticIntercept = true; //can intercept ballistic weapons only
        public $intercept = 1;
        
		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
   
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn)
        {
            parent::setSystemDataWindow($turn);
			$this->data["Special"] .= "<br>Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 1);   }
        public function setMinDamage(){     $this->minDamage = 1 ;      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;      }
} //CustomGatlingMattergunMedium




class CustomGatlingMattergunHeavy extends Pulse{
    /*Heavy Gatling Mattergun, as used on Ch'Lonas ships*/
        public $name = "customGatlingMattergunHeavy";
        public $displayName = "Heavy Gatling Mattergun";
		public $iconPath = "customGatlingMattergunHeavy.png";
        public $animation = "trail";
        public $animationColor = array(225, 225, 150);
        public $priority = 9; //Matter weapons

        public $grouping = 20; //+1 per 4
        public $maxpulses = 4;
        protected $useDie = 2; //die used for base number of hits
        protected $fixedBonusPulses = 1; //d2+1 hits
        public $loadingtime = 3;
        
        public $rangePenalty = 0.5; //-1/2hexes
        public $fireControl = array(-2, 3, 4); // fighters, <mediums, <capitals 
		
        public $noOverkill = true;//Matter weapons do not overkill
        public $ballisticIntercept = true; //can intercept ballistic weapons only
        public $intercept = 1;
        
		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 
   
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 7;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn)
        {
            parent::setSystemDataWindow($turn);
			$this->data["Special"] .= "<br>Ignores armor, does not overkill.";
			$this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
        }		
        
        public function getDamage($fireOrder){        return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
} //CustomGatlingMattergunHeavy



class CustomPulsarLaser extends Pulse{
    /*Pulsar Laser, as used on Ch'Lonas ships*/
        public $name = "customPulsarLaser";
        public $displayName = "Pulsar Laser";
	public $animationColor = array(255, 58, 31);
        public $animation = "bolt"; 	
	
        public $uninterceptable = true;
        public $priority = 5;

        public $grouping = 25;
        public $maxpulses = 4;
        protected $useDie = 3; //die used for base number of hits
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 3, 3); // fighters, <mediums, <capitals 
        
	public $weaponClass = "Laser"; 
   
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        
        public function getDamage($fireOrder){        return 12;   }
} //customPulsarLaser


/* moved to official Lasers!
class CustomStrikeLaser extends Weapon{
    //Strike Laser, as used on Ch'Lonas ships
        public $name = "customStrikeLaser";
        public $displayName = "Strike Laser";
		
        //public $animation = "laser";
		public $animation = "bolt";//a bolt, not a beam
        public $animationColor = array(255, 30, 30);
        public $animationExplosionScale = 0.45;
		
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals
        public $priority = 6; //heavy Standard weapon    
        public $loadingtime = 3;
        public $rangePenalty = 0.5; //-1/2 hexes
		
        public $uninterceptable = true;
	    public $damageType = 'Standard'; 
    	public $weaponClass = "Laser"; 


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 5;
			}
			if ( $powerReq == 0 ){
				$powerReq = 4;
			}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8; }
        public function setMinDamage(){ $this->minDamage = 10 ; }
        public function setMaxDamage(){ $this->maxDamage = 28 ; }
}//CustomStrikeLaser
*/


class HLPA extends Weapon{ 
/*Heavy Laser-Pulse Array - let's try to create it using new mode change mechanism...*/	
        public $name = "hlpa";
        public $displayName = "Heavy Laser-Pulse Array";
	    public $iconPath = "hlpa.png";
	
	//visual display - will it be enough to ensure correct animations?...
	public $animationArray = array(1=>'laser', 2=>'bolt');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(190, 75, 20));
        //public $animationExplosionScale = 0.20;//not used for Laser animation?...
	
	
	//actual weapons data
        public $groupingArray = array(1=>0, 2=>20);
        public $maxpulses = 6; //only useful for Pulse mode
	public $raking = 10; //only useful for Raking mode
        public $priorityArray = array(1=>7, 2=>6);
	public $uninterceptableArray = array(1=>true, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>6); //for Pulse mode it should be equal to maxpulses
	
        public $loadingtimeArray = array(1=>4, 2=>3); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.5);
        public $fireControlArray = array( 1=>array(-4, 2, 3), 2=>array(-1,3,4) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Laser', 2=>'Pulse');
	public $damageTypeArray = array(1=>'Raking', 2=>'Pulse'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Laser', 2=>'Particle'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	public $intercept = 1; //technically only Pulse Cannon can intercept, but entire weapon is fired anyway - so it affects visuals only, and mode 1 should be the one with interception for technical reasons
 
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 10;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 6;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either Heavy Laser or Heavy Pulse Cannon. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 4)+20; //Heavy Laser
				break;
			case 2:
				return 15; //Heavy Pulse
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 24; //Heavy Laser
				break;
			case 2:
				$this->minDamage = 15; //Hvy Pulse
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 60; //Hvy Laser
				break;
			case 2:
				$this->maxDamage = 15; //Hvy Pulse
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(5);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
	
} //endof class HLPA




class MLPA extends Weapon{ 
/*Medium Laser-Pulse Array - let's try to create it using new mode change mechanism...*/	
        public $name = "mlpa";
        public $displayName = "Medium Laser-Pulse Array";
	    public $iconPath = "mlpa.png";
	
	//visual display - will it be enough to ensure correct animations?...
	public $animationArray = array(1=>'laser', 2=>'bolt');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(190, 75, 20));	
	
	//actual weapons data
        public $groupingArray = array(1=>0, 2=>20);
        public $maxpulses = 6; //only useful for Pulse mode
	public $raking = 10; //only useful for Raking mode
        public $priorityArray = array(1=>8, 2=>4);
	public $uninterceptableArray = array(1=>true, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>6); //for Pulse mode it should be equal to maxpulses
	
        public $loadingtimeArray = array(1=>3, 2=>2); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.5, 2=>1); //-1/2 hexes and -1/hex, for Laser and Pulse respectably
        public $fireControlArray = array( 1=>array(-3, 2, 3), 2=>array(1,3,4) ); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Laser', 2=>'Pulse');
	public $damageTypeArray = array(1=>'Raking', 2=>'Pulse'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Laser', 2=>'Particle'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	public $intercept = 2; //technically only Pulse Cannon can intercept, but entire weapon is fired anyway - so it affects visuals only, and mode 1 should be the one with interception for technical reasons
 
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 9;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 5;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire as either Medium Laser or Medium Pulse Cannon. ';
        }

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 3)+12; //Medium Laser
				break;
			case 2:
				return 10; //Medium Pulse
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 15; //Medium Laser
				break;
			case 2:
				$this->minDamage = 10; //Medium Pulse
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 42; //Medium Laser
				break;
			case 2:
				$this->maxDamage = 10; //Medium Pulse
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
	
	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(5);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
} //endof class MLPA





/***   DRAKH Weapons ***/
/*
	Drakh Absorbtion Shield: does not affect profile
	protects vs all weapoon classes
	doubly effective vs Raking weapons (to simulate longer burst)
*/
class AbsorbtionShield extends Shield implements DefensiveSystem{
    public $name = "absorbtionshield";
    public $displayName = "Absorption Shield"; //typo corection: 'Absorption' is correct rather than 'Absorbtion'!
    public $iconPath = "shield.png";
    public $boostable = true; //$this->boostEfficiency and $this->maxBoostLevel in __construct() 
    public $baseOutput = 0; //base output, before boost
	
	
 	public $possibleCriticals = array( //different than usual B5Wars shield
            16=>"OutputReduced1",
            23=>array("OutputReduced1", "OutputReduced1")
	);
	
    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
	$this->baseOutput = $shieldFactor;
	$this->boostEfficiency = $powerReq;
	$this->maxBoostLevel = min(2,$shieldFactor); //maximum of +2 effect, costs $powerReq each - but can't more than double shield!
    }
	
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = 0;
		$this->damagePenalty = $this->getOutput();
    }
	
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
            return 0;
    }
    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under absorbtion shield
        return false;
    }
	
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn()) return 0; //destroyed shield gives no protection
        $output = $this->output + $this->getBoostLevel($turn);
		$output += $this->outputMod; //outputMod itself is negative!
		if( ($weapon->damageType == 'Raking') && !($this->unit instanceof FighterFlight) ) $output = 2*$output;//Raking - double effect! Feb 2022 - but not vs fighters (due to rule "Raking vs fighters is resolved as Standard)
		$output=max(0,$output); //no less than 0!
        return $output;
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
		$this->data["Basic Strength"] = $this->baseOutput; 
		/*standard shield description is misleading!
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    
		*/
		$this->data["Special"] = "Reduces damage done by incoming shots (by shield rating), but does not decrease profile."; 
		$this->data["Special"] .= "<br>Cannot be flown under."; 
		if (!($this->unit instanceof FighterFlight)) $this->data["Special"] .= "<br>Doubly effective vs Raking weapons."; //Raking vs Fighters should be resolved as Standard
		$this->data["Special"] .= "<br>Can be boosted."; 
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
	
} //endof class  AbsorbtionShield



class customPhaseDisruptor extends Raking{
    /*Phase Disruptor for Drakh ships*/
        public $name = "customPhaseDisruptor";
        public $displayName = "Phase Disruptor";
	 public $iconPath = "PhaseDisruptor.png";
        public $animation = "laser";
        public $animationColor = array(50, 125, 210);
        public $uninterceptable = false;
        public $loadingtime = 2;
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 6); // fighters, <mediums, <capitals
        public $priority = 8;
	public $rakes = array();
	public $firingModes = array(1=>'Concentrated', 2=>'Split');
	public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); 
	public $gunsArray = array(1=>1,2=>3);
	
		public $factionAge = 2; //Middle-born
	
	public $damageType = 'Raking'; 
    	public $weaponClass = "Molecular"; 



        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 9;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 5;
		}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1: //damage for concentrated shot
				$this->rakes = array();
				$damage = 0;
				$rake = Dice::d(6, 3);
				$damage+=$rake;
				$this->rakes[] = $rake;
				$rake = Dice::d(6, 3);
				$damage+=$rake;
				$this->rakes[] = $rake;
				$rake = Dice::d(6, 3);
				$damage+=$rake;
				$this->rakes[] = $rake;
				return $damage; 
				break;
			case 2:
				$damage = Dice::d(6, 3); //damage for separate shot
				$this->rakes = array($damage);
				return $damage;
				break;	
		}
	}
	
	public function getRakeSize(){
		//variable rake size: first entry from $this->rakes (min of 3, in case of trouble - should not happen!)	
		$rakesize = array_shift($this->rakes);
		$rakesize = max(3,$rakesize); //just in case of trouble
		return $rakesize;		
	}
	
        public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; //concentrated
				break;
			case 2:
				$this->minDamage = 3; //split
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 18*3; //concentrated
				break;
			case 2:
				$this->maxDamage = 18; //split
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	$this->data["Special"] = 'In concentrate mode does 3 rakes, each 3d6 strong.';
    }
	
}//customPhaseDisruptor



class customLtPhaseDisruptorShip extends Raking{
    /*LightPhase Disruptor for Drakh ships*/
        public $name = "customLtPhaseDisruptorShip";
        public $displayName = "Light Phase Disruptor";
	 public $iconPath = "LtPhaseDisruptor.png";
        public $animation = "laser";
        public $animationColor = array(50, 125, 210);
        public $uninterceptable = false;
        public $loadingtime = 1;
        public $rangePenalty = 1;
        public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals
        public $priority = 8;
	public $rakes = array();
	public $guns = 1;
	
	public $firingModes = array(1=>'Concentrated', 2=>'Split');
	public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); 
	public $gunsArray = array(1=>1,2=>2);
	
    	public $weaponClass = "Molecular"; 

		public $factionAge = 2; //Middle-born


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 6;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 3;
		}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1: //damage for concentrated shot
				$this->rakes = array();
				$damage = 0;
				$rake = Dice::d(6, 3);
				$damage+=$rake;
				$this->rakes[] = $rake;
				$rake = Dice::d(6, 3);
				$damage+=$rake;
				$this->rakes[] = $rake;
				return $damage; 
				break;
			case 2:
				$damage = Dice::d(6, 3); //damage for separate shot
				$this->rakes = array($damage);
				return $damage;
				break;	
		}
	}
	
	public function getRakeSize(){
		//variable rake size: first entry from $this->rakes (min of 3, in case of trouble - should not happen!)	
		$rakesize = array_shift($this->rakes);
		$rakesize = max(3,$rakesize); //just in case of trouble
		return $rakesize;		
	}
	
        public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 6; //concentrated
				break;
			case 2:
				$this->minDamage = 3; //split
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 18*2; //concentrated
				break;
			case 2:
				$this->maxDamage = 18; //split
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	$this->data["Special"] = 'In concentrated mode does 2 rakes, each 3d6 strong.';
    }
	
}//customLtPhaseDisruptorShip




class customLtPolarityPulsar extends Pulse{
        public $name = "customLtPolarityPulsar";
        public $displayName = "Light Polarity Pulsar";
	public $iconPath = "LightPolarityPulsar.png";
        
        public $animationColor = array(255,140,0);
        public $animation = "trail";
		
	public $weaponClass = "Molecular"; 
	
        public $loadingtime = 1;
        public $priority = 4;
        public $rof = 2;
	        
        public $rangePenalty = 2;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals 
        
	public $grouping = 15; //+1 per 3
	public $maxpulses = 6;
	protected $useDie = 5; //die used for base number of hits;
        public $intercept = 2;
		
		public $factionAge = 2; //Middle-born
		
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 4;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 3;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 10;   }

    } //customLtPolarityPulsar


class customMedPolarityPulsar extends Pulse{
        public $name = "customMedPolarityPulsar";
        public $displayName = "Medium Polarity Pulsar";
	public $iconPath = "MediumPolarityPulsar.png";
        
        public $animationColor = array(255,140,0);
        public $animation = "trail";

		
        public $loadingtime = 2;
        public $priority = 5;
	public $weaponClass = "Molecular"; 
        public $rof = 2;
	        
        public $rangePenalty = 1; // -1 hex
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals 
        
	public $grouping = 15; //+1 per 3
	public $maxpulses = 5;
	protected $useDie = 4; //die used for base number of hits;
        public $intercept = 2;
	
		public $factionAge = 2; //Middle-born
		
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 6;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 4;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 12;   }

    } //customMedPolarityPulsar


class customHeavyPolarityPulsar extends Pulse{
        public $name = "customHeavyPolarityPulsar";
        public $displayName = "Heavy Polarity Pulsar";
	public $iconPath = "HeavyPolarityPulsar.png";
        
        public $animationColor = array(255,140,0);
        public $animation = "trail";
		
        public $loadingtime = 3;
        public $priority = 6;
	public $weaponClass = "Molecular"; 
        public $rof = 2;
	        
        public $rangePenalty = 0.5; //-1/2 hexes
        public $fireControl = array(0, 3, 5); // fighters, <mediums, <capitals 
        
	public $grouping = 15; //+1 per 3
	public $maxpulses = 5;
	protected $useDie = 3; //die used for base number of hits;
        public $intercept = 1;
	
		public $factionAge = 2; //Middle-born
			
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 9;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 6;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 18;   }

    } //customHeavyPolarityPulsar




class customMphasedBeamAcc extends Weapon{
	public $name = "customMphasedBeamAcc";
        public $displayName = "Multiphased Beam Accelerator";
	    public $iconPath = "MultiphasedBeamAccelerator.png";
	    
	public $animation = "laser";
        public $animationColor = array(225,130,0);
	
        public $raking = 10;
	    
	    public $damageType = 'Raking'; 
	public $weaponClass = "Molecular"; 
	
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!
        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );        
        public $damageTypeArray=array(1=>'Raking', 2=>'Piercing');
	    
        
        public $loadingtime = 1;  //can fire every turn, but achieves full charge after 3 turns
		public $normalload = 3;
		
        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(3, 4, 5); // fighters, <=mediums, <=capitals 
	    public $fireControlArray = array( 1=>array(3, 4, 5), 2=>array(null,0,1) ); //Raking and Piercing mode
		
		public $factionAge = 2; //Middle-born
		
	    
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		$this->data["Special"] = "Ignores half of armor.";  		
		$this->data["Special"] .= "<br>Can fire accelerated for less damage";  
		$this->data["Special"] .= "<br> - 1 turn: 2d10+2"; 
		$this->data["Special"] .= "<br> - 2 turns: 4d10+4"; 
		$this->data["Special"] .= "<br> - 3 turns (full): 8d10+8"; 
	}
	    
		
	public function getDamage($fireOrder){
            switch($this->turnsloaded){
                case 0: 
                case 1:
                    return Dice::d(10,2)+2;
                    break;
                case 2:
                    return Dice::d(10, 4)+4;
                    break;
                case 3:
		    return Dice::d(10,8)+8;
                    break;
                default:
                    return Dice::d(10,8)+8;
                    break;
            }
	}
        
        public function setMinDamage(){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    $this->minDamage = 4 ;
                    break;
                case 2:
                    $this->minDamage = 8 ;  
                    break;
                case 3:
                default:
                    $this->minDamage = 16 ;  
                    break;
            }
		 //$this->minDamage = 16 ;  
	}
                
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 0:
                case 1:
                    $this->maxDamage = 22 ;
                    break;
                case 2:
                    $this->maxDamage = 44 ;  
                    break;
                case 3:
                    $this->maxDamage = 88 ;  
                    break;
                default:
                    $this->maxDamage = 88 ;  
                    break;
            }
		//$this->maxDamage = 88 ;  
	}
	    
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 11;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 10;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
	    //Multiphased Beam ignores half armor!
		public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){
			$armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
			if (is_numeric($armour)){
				$toIgnore = ceil($armour /2);
				$new = $armour - $toIgnore;
				return $new;
			}
			else {
				return 0;
			}
		}
	    	    
		/*allow accelerated changes (damage output) to be displayed*/
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->minDamage = $this->minDamage;			
			return $strippedSystem;
		}
    }//endof class customMphasedBeamAcc




/*Drakh fighter weapon - basic mode fast-firing antifigher, alternate concentrated antiship!*/
    class customLtPhaseDisruptor extends Weapon{
        public $trailColor = array(255, 170, 10);
        public $name = "customLtPhaseDisruptor";
        public $displayName = "Light Phase Disruptor";
	   public  $iconPath = "LtPhaseDisruptor.png";
        public $animation = "trail";
        public $animationColor = array(255, 170, 10);
	    
        public $intercept = 1;
        public $loadingtime = 1;
        public $shots = 1;
	    public $guns = 3;
        public $defaultShots = 1;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	    public $priority = 4;
	    public $priorityArray = array(1=>4, 2=>6); //alternate mode is far stronger
        
        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 
		
		public $firingModes = array(1=>'Split', 2=>'Concentrated');
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard'); 
		public $gunsArray = array(1=>3, 2=>1);
        public $fireControlArray = array( 1=>array(0, 0, 0), 2=>array(-3,0,0) ); // fighters, <mediums, <capitals 
        public $rangePenaltyArray = array(1=>2, 2=>1.5); //-2/hex and -3/2 hexes
		
		public $factionAge = 2; //Middle-born
	            
        function __construct($startArc, $endArc){
			$this->isLinked = false; //shots are separate, not linked! 
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
	
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Primary mode of fire: 3 shots, 2d6 damage, -10/hex (antifighter)";
            $this->data["Special"] .= "<br> alternate: 1 shot, 3d6+2 damage, -7.5/hex (antiship)";
        }
	    
        public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(6, 2); //Antifighter
					break;
				case 2:
					return Dice::d(6, 3)+2; //Antiship
					break;
			}
		}
        public function setMinDamage(){ 
			switch($this->firingMode){
				case 1:
					$this->minDamage = 2; //Antifighter
					break;
				case 2:
					$this->minDamage = 5; //Antiship
					break;	
			}
			$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 12; //Antifighter
					break;
				case 2:
					$this->maxDamage = 20; //Antiship
					break;	
			}
			$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}
		
    } //endof class customLtPhaseDisruptor


/*Drakh fighter weapon - interception and dogfight oriented*/
    class customPhaseSweeper extends Weapon{
        public $trailColor = array(255, 170, 10);
        public $name = "customPhaseSweeper";
        public $displayName = "Phase Sweeper";
	   public  $iconPath = "LtPhaseDisruptor.png";
	    
        public $intercept = 2;
        public $loadingtime = 1;
        public $shots = 1;
	    public $guns = 4;
        public $defaultShots = 1;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	    public $priority = 4;
	    
	    
        public $animation = "trail";
        public $animationColor = array(255, 170, 10);
        
        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 
	    
		public $factionAge = 2; //Middle-born
        
        function __construct($startArc, $endArc){
	    $this->isLinked = false; //shots are separate, not linked! 
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
	
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            //$this->data["Special"] = "Shots are NOT linked"; //not needed
        }
	
	    
        public function getDamage($fireOrder){        return Dice::d(6,2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    } //endof class customPhaseSweeper




class LightScattergun extends Pulse{
    /*Markab fighter weapon - d3 shots (here treated as a single Pulse shot, no grouping bonus)*/
       public $shots = 2; //overridden by Pulse, but still used for estimation of threat for interception
	public  $iconPath = "scatterGun.png";
	
	//for Pulse mode
	public $grouping = 2500; //NO GROUPING BONUS
	public $maxpulses = 3;	
	protected $useDie = 3; //die used for base number of hits
 
	public $name = "LightScattergun";
	public $displayName = "Light Scattergun";
	
    public $damageType = "Pulse"; 
    public $weaponClass = "Particle";
   
	public $animation = "bolt";
	public $animationColor = array(190, 75, 20);
	
	public $intercept = 2;	
	public $rangePenalty = 2; //-2/hex	
	public $priority = 4;
	
	function __construct($startArc, $endArc){//more than a single emplacement not supported!
		$this->maxpulses = 3;
		$this->defaultShots = 2;	
						
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    
		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'does always d3 pulses, no grouping bonus';
	}
	
    
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn); //$this->useDie usually
		//$pulses+= $this->getExtraPulses($needed, $rolled); //no grouping bonus for this weapon
		return $pulses;
	}	
	
	public function getDamage($fireOrder){        return Dice::d(6,2);   }
	public function setMinDamage(){     $this->minDamage = 2 ;      }
	public function setMaxDamage(){     $this->maxDamage = 12 ;      }
} //end of class LightScattergun


class CustomBPALight extends Weapon{ 
/*Light Bolt-Pulse Array - custom weapon, combining Light Bolter and Pulse Cannon*/	
        public $name = "CustomBPALight";
        public $displayName = "Light Bolt-Pulse Array";
	    public $iconPath = "CustomBPALight.png";
	
	//visual display
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(255, 250, 230));
		
	//actual weapons data
        public $groupingArray = array(1=>0, 2=>20);
        public $maxpulses = 6; //only useful for Pulse mode
        public $priorityArray = array(1=>5, 2=>3);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>6); //for Pulse mode it should be equal to maxpulses
	
        public $loadingtimeArray = array(1=>1, 2=>1); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>1, 2=>2); //-1/hex and -1/2hex, for Bolt and Pulse respectably
        public $fireControlArray = array( 1=>array(4, 3, 3), 2=>array(4,3,3) ); // fighters, <mediums, <capitals ; this weapons uses Pulse FC for both weapons
	
	public $firingModes = array(1=>'Bolt', 2=>'Pulse');
	public $damageTypeArray = array(1=>'Standard', 2=>'Pulse'); 
    	public $weaponClassArray = array(1=>'Particle', 2=>'Particle'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	public $intercept = 2; 
 
	
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 6;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 2;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Can fire as either Light Bolter or Light Pulse Cannon. ';
    }

	
    public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return 12; //Light Bolter
				break;
			case 2:
				return 8; //Light Pulse
				break;	
		}
	}
    
	public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 12; //Light Bolter
				break;
			case 2:
				$this->minDamage = 8; //Light Pulse
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
    
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 12; //Light Bolter
				break;
			case 2:
				$this->maxDamage = 8; //Light Pulse
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(5);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
} //endof class CustomBPALight


class CustomBPAMedium extends Weapon{ 
/*Medium Bolt-Pulse Array - custom weapon, combining Medium Bolter and Pulse Cannon*/	
        public $name = "CustomBPAMedium";
        public $displayName = "Medium Bolt-Pulse Array";
	    public $iconPath = "CustomBPAMedium.png";
	
	//visual display
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(255, 250, 230));	
	
	//actual weapons data
        public $groupingArray = array(1=>0, 2=>20);
        public $maxpulses = 6; //only useful for Pulse mode
        public $priorityArray = array(1=>6, 2=>4);
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>6); //for Pulse mode it should be equal to maxpulses
	
        public $loadingtimeArray = array(1=>2, 2=>2); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.5, 2=>1); //-1/2 hexes and -1/hex, for Bolt and Pulse respectably
        public $fireControlArray = array( 1=>array(1, 3, 4), 2=>array(1,3,4) ); // fighters, <mediums, <capitals ; this weapons uses Pulse FC for both weapons
	
	public $firingModes = array(1=>'Bolt', 2=>'Pulse');
	public $damageTypeArray = array(1=>'Standard', 2=>'Pulse'); 
    	public $weaponClassArray = array(1=>'Particle', 2=>'Particle'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	public $intercept = 2; 
 
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 4;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		$this->data["Special"] = 'Can fire as either Medium Bolter or Medium Pulse Cannon. ';
		parent::setSystemDataWindow($turn);
	}

	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return 18; //Medium Bolter
				break;
			case 2:
				return 10; //Medium Pulse
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 18; //Medium Bolter
				break;
			case 2:
				$this->minDamage = 10; //Medium Pulse
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 18; //Medium Bolter
				break;
			case 2:
				$this->maxDamage = 10; //Medium Pulse
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
	
	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(5);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
} //endof class CustomBPAMedium



class CustomBPAHeavy extends Weapon{ 
/*Heavy Bolt-Pulse Array - custom weapon, combining Heavy Bolter and Pulse Cannon*/	
        public $name = "CustomBPAHeavy";
        public $displayName = "Heavy Bolt-Pulse Array";
	    public $iconPath = "CustomBPAHeavy.png";
	
	//visual display
	public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(255, 250, 230));	
	
	//actual weapons data
        public $groupingArray = array(1=>0, 2=>20);
        public $maxpulses = 6; //only useful for Pulse mode
        public $priorityArray = array(1=>6, 2=>6); //both weapons fall into Heavy Standard category
	public $uninterceptableArray = array(1=>false, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>6); //for Pulse mode it should be equal to maxpulses
	
        public $loadingtimeArray = array(1=>3, 2=>3); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.5); //-1/3 hexes and -1/2hexes, for Bolt and Pulse respectably
        public $fireControlArray = array( 1=>array(-1, 3, 4), 2=>array(-1,3,4) ); // fighters, <mediums, <capitals ; this weapons uses Pulse FC for both weapons
	
	public $firingModes = array(1=>'Bolt', 2=>'Pulse');
	public $damageTypeArray = array(1=>'Standard', 2=>'Pulse'); 
    	public $weaponClassArray = array(1=>'Particle', 2=>'Particle'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
	public $intercept = 1; 
 
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 10;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 6;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
	public function setSystemDataWindow($turn){
		$this->data["Special"] = 'Can fire as either Heavy Bolter or Heavy Pulse Cannon. ';
		parent::setSystemDataWindow($turn);
	}


	public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return 24; //Heavy Bolter
				break;
			case 2:
				return 15; //Heavy Pulse
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 24; //Heavy Bolter
				break;
			case 2:
				$this->minDamage = 15; //Heavy Pulse
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 24; //Heavy Bolter
				break;
			case 2:
				$this->maxDamage = 15; //Heavy Pulse
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(5);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
} //endof class CustomBPAHeavy



class CustomIndustrialGrappler extends Weapon {
    /*Industrial Grappler - fluff feature on mining units*/
        public $name = "customIndustrialGrappler";
        public $displayName = "Industrial Grappler";
	public $iconPath = "grapplingClaw.png";

        public $loadingtime = 1;        
        public $rangePenalty = 0; //no range penalty to speak about
        public $fireControl = array(null, null, null); // fighters, <mediums, <capitals //no in game effect!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 0;
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    
		$this->data["Special"] .= "Has no actual in-game effect. Used to attach unit to asteroid."; 
    }

        public function getDamage($fireOrder){        return 0;   }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }
} //CustomIndustrialGrappler


    class CustomMiningCutter extends Laser{
        public $name = "customMiningCutter";
        public $displayName = "Mining Cutter";
	public $iconPath = "miningCutter.png";
        public $animation = "laser";
        public $animationColor = array(255, 250, 230);
	    
        public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Particle"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
        		
        public $uninterceptable = false;
        
        public $loadingtime = 3;
        public $priority = 8; //light Raking weapon
        
        public $raking = 6;
        public $rangePenalty = 1; //-1 per hex
        public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+5;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 25 ;      }
    } //CustomMiningCutter


class CustomLightSoMissileRack extends MissileLauncher{
        public $name = "CustomLightSoMissileRack";
        public $displayName = "Light SO-Missile Rack";
		    public $iconPath = "missile1.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 45;
        public $ammunition = 12; //limited number of shots
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(5, 5, 5); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
		public $noOverkill = false; //Ballistic weapon
		public $priority = 6; //Ballistic weapon
	    
		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
             $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 12;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }

}//endof CustomLightSoMissileRack


class CustomLightSMissileRack extends Weapon{
        public $name = "CustomLightSMissileRack";
        public $displayName = "Light S-Missile Rack";
		    public $iconPath = "missile1.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 45;
        public $ammunition = 20; //limited number of shots	    
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(6, 6, 6); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
		public $noOverkill = false; //Ballistic weapon
		public $priority = 6; //Ballistic weapon
	    
		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
             $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 12;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }
}//endof CustomLightSMissileRack


//Grome special shell railguns
//Listed in Customs as these are official options for the Grome.
//However, I did not want these in Matter as they do not adhear the to proper shell limits.

class GromeLgtRailgun extends Weapon{ 
/*Multi-mode weapon based off the EA Laser-Pulse Array code. This fires as:
	Standard light railgun (S): Matter, 1d10+5, -1/hex, +0/+2/+3, 1/2 turns
	Flash shell (F): Plasma (Flash), 1d10+5, -1/hex, +0/+2/+3, 1/2 turns, cost 3
	Heavy shell (H): Matter, 1d10+10, -1/hex, +0/+2/+3, 1/2 turns, cost 6
	Scatter shell (P): Matter (Pulse), 1 shot, max 4, +1/5, -1/hex, -2/+0/+1, 1/2 turns, cost 2
	Note, the light railgun cannot use a long-range shell. */
        public $name = "GromeLgtRailgun";
        public $displayName = "Light Railgun";
	    public $iconPath = "LightRailgun.png";
	
	public $animation = "bolt";
	public $animationColor = array(250, 250, 190);
		//public $animationArray = array(1=>'trail', 2=>'trail', 3=>'trail', 4=>'trail');	
        //public $animationColorArray = array(1=>array(250, 250, 190), 2=>array(250, 250, 190), 3=>array(250, 250, 190), 4=>array(250, 250, 190));
	/*
        public $animationWidthArray = array(1=>4, 2=>4, 3=>4, 4=>4);
		public $trailColor = array(190, 75, 20); 
        public $trailLength = 3;//not used for Laser animation?...
        public $projectilespeed = 30;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.2, 2=>0.4, 3=>0.3, 4=>0.3);
	*/
	
		//actual weapons data
		public $groupingArray = array(1=>0, 2=>0, 3=>0, 4=>25);
		public $maxpulses = 4; //Pulse mode only
		public $priorityArray = array(1=>6, 2=>1, 3=>7, 4=>5);
		public $uninterceptableArray = array(1=>false, 2=>false, 3=>false, 4=>false);
		public $defaultShotsArray = array(1=>1, 2=>1, 3=>1, 4=>4); 
	
        public $loadingtimeArray = array(1=>2, 2=>2, 3=>2, 4=>2); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>1, 2=>1, 3=>1, 4=>1);
        public $fireControlArray = array(1=>array(3, 2, 0), 2=>array(3, 2, 0), 3=>array(3, 2, 0), 4=>array(1, 0, -2)); // fighters, <mediums, <capitals 
		public $noOverkillArray = array(1=>true, 2=>false, 3=>true, 4=>true);

		public $firingModes = array(1=>'Standard', 2=>'Flash', 3=>'Heavy', 4=>'Pulse');
		public $damageTypeArray = array(1=>'Standard', 2=>'Flash', 3=>'Standard', 4=>'Pulse'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Matter', 2=>'Plasma', 3=>'Matter', 4=>'Matter'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 6;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 3;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'The Light Railgun has four different shell options.';
			$this->data["Special"] .= '<br>Standard (S): 1d10+5 dmg, -1/hex, +15/+10/+0, 1 per 2 turns.';
			$this->data["Special"] .= '<br>Flash (F): 1d10+5 dmg, Plasma (Flash) - ignores half armor, -1/hex, +15/+10/+0, 1 per 2 turns. ';
			$this->data["Special"] .= '<br>Heavy (H): 1d10+10 dmg, -1/hex, +15/+10/+0, 1 per 2 turns.';
			$this->data["Special"] .= '<br>Pulse (P): 1d6+1 dmg, +1/5, max 4 shots, -1/hex, +5/+0/-10, 1 per 2 turns.';
        } 
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+5; //Standard shot
				break;
			case 2:
				return Dice::d(10, 1)+5; //Flash
				break;	
			case 3:
				return Dice::d(10, 1)+10; //Heavy
				break;
			case 4:
				return Dice::d(6, 1)+1; //Pulse
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 6; //Standard shot
				break;
			case 2:
				$this->minDamage = 6; //Flash
				break;	
			case 3:
				$this->minDamage = 11; //Heavy
				break;
			case 4:
				$this->minDamage = 2; //Pulse
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 15; //Standard shot
				break;
			case 2:
				$this->maxDamage = 15; //Flash
				break;	
			case 3:
				$this->maxDamage = 20; //Heavy
				break;
			case 4:
				$this->maxDamage = 7; //Pulse
				break;			}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return 1;
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}	
	
} //endof class GromeLgtRailgun


class GromeMedRailgun extends Weapon{ 
/*Multi-mode weapon based off the EA Laser-Pulse Array code. This fires as:
	Standard light railgun (S): Matter, 3d10+3, -1/2 hexes, +2/+2/-3, 1/3 turns
	Flash shell (F): Plasma (Flash), 3d10+3, -1/2 hexes, +2/+2/-3, 1/3 turns, cost 6
	Heavy shell (H): Matter, 3d10+13, -1/2 hexes, +2/+2/-3, 1/3 turns, cost 12
	Scatter shell (P): Matter (Pulse), 1d10+3 dmg, 1 shot, max 4, +1/5, -1/2 hexs, +0/+0/-5, 1/3 turns, cost 5
	Long-range shell (L): Matter, 1d10+5, -1/3 hexes, +2/+2/-3, 1/3 turns, cost 2 */
        public $name = "GromeMedRailgun";
        public $displayName = "Medium Railgun";
	    public $iconPath = "Railgun.png";
	
	public $animation = "bolt";
	public $animationColor = array(250, 250, 190);
		//public $animationArray = array(1=>'trail', 2=>'trail', 3=>'trail', 4=>'trail', 5=>'trail');
        //public $animationColorArray = array(1=>array(250, 250, 190), 2=>array(250, 250, 190), 3=>array(250, 250, 190), 4=>array(250, 250, 190), 5=>array(250, 250, 190));
        /*
	public $animationWidthArray = array(1=>5, 2=>5, 3=>5, 4=>5, 5=>5);
		public $trailColor = array(190, 75, 20); 
        public $trailLength = 3;//not used for Laser animation?...
        public $projectilespeed = 25;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.25, 2=>0.45, 3=>0.35, 4=>0.35, 5=>0.2);
	*/
	
		//actual weapons data
		public $groupingArray = array(1=>0, 2=>0, 3=>0, 4=>25, 5=>0);
		public $maxpulses = 4; //Pulse mode only
		public $priorityArray = array(1=>7, 2=>1, 3=>7, 4=>5, 5=>6);
		public $uninterceptableArray = array(1=>false, 2=>false, 3=>false, 4=>false, 5=>false);
		public $defaultShotsArray = array(1=>1, 2=>1, 3=>1, 4=>4, 5=>1); 
	
        public $loadingtimeArray = array(1=>3, 2=>3, 3=>3, 4=>3, 5=>3); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.5, 2=>0.5, 3=>0.5, 4=>0.5, 5=>0.33);
        public $fireControlArray = array(1=>array(-3, 2, 2), 2=>array(-3, 2, 2), 3=>array(-3, 2, 2), 4=>array(-5, 0, 0), 5=>array(-3, 2, 2)); // fighters, <mediums, <capitals 
		public $noOverkillArray = array(1=>true, 2=>false, 3=>true, 4=>true, 5=>true);
	
		public $firingModes = array(1=>'Standard', 2=>'Flash', 3=>'Heavy', 4=>'Pulse', 5=>'Long-range');
		public $damageTypeArray = array(1=>'Standard', 2=>'Flash', 3=>'Standard', 4=>'Pulse', 5=>'Standard'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Matter', 2=>'Plasma', 3=>'Matter', 4=>'Matter', 5=>'Matter'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 9;
			}
			if ( $powerReq == 0 ){
				$powerReq = 6;
			}
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'The Medium Railgun has five different shell options.';
			$this->data["Special"] .= '<br>Standard (S): 3d10+3 dmg, -1/2 hexes, -15/+10/+10, 1 per 3 turns.';
			$this->data["Special"] .= '<br>Flash (F): 3d10+3 dmg, Plasma (Flash) - ignores half armor, -1/2 hexes, -15/+10/+10, 1 per 3 turns. ';
			$this->data["Special"] .= '<br>Heavy (H): 3d10+13 dmg, -1/2 hexes, -15/+10/+10, 1 per 3 turns.';
			$this->data["Special"] .= '<br>Pulse (P): 1d10+3 dmg, +1/5, max 4 shots, -1/2 hexes, -25/+0/+0, 1 per 3 turns.';
			$this->data["Special"] .= '<br>Long-range (L): 1d10+5 dmg, -1/3 hexes, -15/+10/+10, 1 per 3 turns.';
        } 
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 3)+3; //Standard shot
				break;
			case 2:
				return Dice::d(10, 3)+3; //Flash
				break;	
			case 3:
				return Dice::d(10, 3)+13; //Heavy
				break;
			case 4:
				return Dice::d(10, 1)+3; //Pulse
				break;	
			case 5:
				return Dice::d(10, 1)+5; //Long-range
				break;			
		}
	}
	
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 6; //Standard shot
				break;
			case 2:
				$this->minDamage = 6; //Flash
				break;	
			case 3:
				$this->minDamage = 16; //Heavy
				break;
			case 4:
				$this->minDamage = 4; //Pulse
				break;	
			case 5:
				$this->minDamage = 6; //Long-range
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 33; //Standard shot
				break;
			case 2:
				$this->maxDamage = 33; //Flash
				break;	
			case 3:
				$this->maxDamage = 43; //Heavy
				break;
			case 4:
				$this->maxDamage = 13; //Pulse
				break;			
			case 5:
				$this->maxDamage = 15; //Long-range
				break;			
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return 1;
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}	
	
} //endof class GromeMedRailgun


class GromeHvyRailgun extends Weapon{ 
/*Multi-mode weapon based off the EA Laser-Pulse Array code. This fires as:
	Standard light railgun (S): Matter, 5d10+7, -1/3 hexes, +2/+2/-3, 1/4 turns
	Flash shell (F): Plasma (Flash), 5d10+7, -1/3 hexes, +2/+2/-3, 1/4 turns, cost 10
	Heavy shell (H): Matter, 5d10+22, -1/3 hexes, +2/+2/-3, 1/4 turns, cost 18
	Scatter shell (P): Matter (Pulse), 1d10+5 dmg, 1 shot, max 6, +1/5, -1/3 hexs, +0/+0/-5, 1/4 turns, cost 10
	Long-range shell (L): Matter, 3d10+3, -1/4 hexes, +2/+2/-3, 1/4 turns, cost 4 
	Ultra long-range shell (U): Matter, 1d10+5, -1/5 hexes, +2/+2/-3, 1/4 turns, cost 6*/
        public $name = "GromeHvyRailgun";
        public $displayName = "Heavy Railgun";
	    public $iconPath = "HeavyRailgun.png";
	
	public $animation = "bolt";
	public $animationColor = array(250, 250, 190);
	/*
		public $animationArray = array(1=>'trail', 2=>'trail', 3=>'trail', 4=>'trail', 5=>'trail', 6=>'trail');
        public $animationColorArray = array(1=>array(250, 250, 190), 2=>array(250, 250, 190), 3=>array(250, 250, 190), 4=>array(250, 250, 190), 5=>array(250, 250, 190), 6=>array(250, 250, 190));
	
        public $animationWidthArray = array(1=>6, 2=>6, 3=>6, 4=>6, 5=>6, 6=>6);
		public $trailColor = array(190, 75, 20); 
        public $trailLength = 3;//not used for Laser animation?...
        public $projectilespeed = 25;//not used for Laser animation?...
        public $animationExplosionScaleArray = array(1=>0.3, 2=>0.5, 3=>0.4, 4=>0.4, 5=>0.25, 6=>0.2);
	*/
	
		//actual weapons data
		public $groupingArray = array(1=>0, 2=>0, 3=>0, 4=>25, 5=>0, 6=>0);
		public $maxpulses = 6; //Pulse mode only
		public $priorityArray = array(1=>7, 2=>1, 3=>7, 4=>5, 5=>7, 6=>6);
		public $uninterceptableArray = array(1=>false, 2=>false, 3=>false, 4=>false, 5=>false, 6=>false);
		public $defaultShotsArray = array(1=>1, 2=>1, 3=>1, 4=>5, 5=>1, 6=>1); 
	
        public $loadingtimeArray = array(1=>4, 2=>4, 3=>4, 4=>4, 5=>4, 6=>4); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0.33, 2=>0.33, 3=>0.33, 4=>0.33, 5=>0.25, 6=>0.2);
        public $fireControlArray = array(1=>array(-3, 2, 2), 2=>array(-3, 2, 2), 3=>array(-3, 2, 2), 4=>array(-5, 0, 0), 5=>array(-3, 2, 2), 6=>array(-3, 2, 2)); // fighters, <mediums, <capitals 
		public $noOverkillArray = array(1=>true, 2=>false, 3=>true, 4=>true, 5=>true, 6=>true);
	
		public $firingModes = array(1=>'Standard', 2=>'Flash', 3=>'Heavy', 4=>'Pulse', 5=>'Long-range', 6=>'Ultra long-range');
		public $damageTypeArray = array(1=>'Standard', 2=>'Flash', 3=>'Standard', 4=>'Pulse', 5=>'Standard', 6=>'Standard'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Matter', 2=>'Plasma', 3=>'Matter', 4=>'Matter', 5=>'Matter', 6=>'Matter'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 12;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 9;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'The Heavy Railgun has six different shell options.';
			$this->data["Special"] .= '<br>Standard (S): 5d10+7 dmg, -1/3 hexes, -15/+10/+10, 1 per 4 turns.';
			$this->data["Special"] .= '<br>Flash (F): 5d10+7 dmg, Plasma (Flash) - ignores half armor, -1/3 hexes, -15/+10/+10, 1 per 4 turns. ';
			$this->data["Special"] .= '<br>Heavy (H): 5d10+22 dmg, -1/3 hexes, -15/+10/+10, 1 per 4 turns.';
			$this->data["Special"] .= '<br>Pulse (P): 1d10+4 dmg, +1/5, max 6 shots, -1/3 hexes, -25/+0/+0, 1 per 4 turns.';
			$this->data["Special"] .= '<br>Long-range (L): 3d10+3 dmg, -1/4 hexes, -15/+10/+10, 1 per 4 turns.';
			$this->data["Special"] .= '<br>Ultra long-range (U): 1d10+5 dmg, -1/5 hexes, -15/+10/+10, 1 per 4 turns.';
        } 
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 5)+7; //Standard shot
				break;
			case 2:
				return Dice::d(10, 5)+7; //Flash
				break;	
			case 3:
				return Dice::d(10, 5)+22; //Heavy
				break;
			case 4:
				return Dice::d(10, 1)+5; //Pulse
				break;	
			case 5:
				return Dice::d(10, 3)+3; //Long-range
				break;			
			case 6:
				return Dice::d(10, 1)+5; //Ultra long-range
				break;			
		}
	}
	
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 12; //Standard shot
				break;
			case 2:
				$this->minDamage = 12; //Flash
				break;	
			case 3:
				$this->minDamage = 27; //Heavy
				break;
			case 4:
				$this->minDamage = 5; //Pulse
				break;	
			case 5:
				$this->minDamage = 12; //Long-range
				break;	
			case 6:
				$this->minDamage = 6; //Ultra long-range
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 57; //Standard shot
				break;
			case 2:
				$this->maxDamage = 57; //Flash
				break;	
			case 3:
				$this->maxDamage = 72; //Heavy
				break;
			case 4:
				$this->maxDamage = 14; //Pulse
				break;			
			case 5:
				$this->maxDamage = 33; //Long-range
				break;			
			case 6:
				$this->maxDamage = 15; //Ultra long-range
				break;			
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return 1;
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}	
	
} //endof class GromeHvyRailgun




/*custom-made light version of official Particle Cutter*/
    class CustomLtParticleCutter extends Raking{
        public $name = "customLtParticleCutter";
        public $displayName = "Light Particle Cutter";
	    public $iconPath = "customLtParticleCutter.png";
		public $animation = "laser";
        public $animationColor = array(255, 153, 102);
	    
        public $firingModes = array( 1 => "Sustained");
        
        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
		public $raking = 8; //smaller rake size
        
        // Set to make the weapon start already overloaded.
        public $alwaysoverloading = true;
        public $overloadturns = 2;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
        public $loadingtime = 2;
        public $priority = 8; //light Raking weapon

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 6;
			}
			if ( $powerReq == 0 ){
				$powerReq = 2;
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
            $this->data["Special"] .= "This weapon is always in sustained mode.";
		}


        public function isOverloadingOnTurn($turn = null){
            return true;
        }
        
        public function getDamage($fireOrder){ return Dice::d(6, 8)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 24 ;      }
    } //endof class CustomLtParticleCutter


/*custom-made light version of official Particle Cutter - make it early*/
    class CustomEarlyLtParticleCutter extends CustomLtParticleCutter{
        public $name = "customEarlyLtParticleCutter";
        public $displayName = "Early Light Particle Cutter";
	    public $iconPath = "customLtParticleCutter.png";
		
		public $raking = 6; //smaller rake size
        
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){ return Dice::d(8, 2)+4;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    } //endof class CustomEarlyLtParticleCutter



?>
