<?php


class CustomLightMatterCannon extends Matter {
    /*Light Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customLightMatterCannon";
        public $displayName = "Light Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
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
        public $name = "customLightMatterCannonF";
        public $displayName = "Light Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 18;
        public $animationWidth = 2;
        public $animationExplosionScale = 0.10;
        public $priority = 8;
        
        public $loadingtime = 3;
        public $exclusive = false; //this is not an exclusive weapon!
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 1)+4;   }
        public function setMinDamage(){   return  $this->minDamage = 5 ;      }
        public function setMaxDamage(){   return  $this->maxDamage = 14 ;      }
}//CustomLightMatterCannonF



class CustomHeavyMatterCannon extends Matter{
    /*Heavy Matter Cannon, as used on Ch'Lonas ships*/
        public $name = "customHeavyMatterCannon";
        public $displayName = "Heavy Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.25;
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




class CustomPulsarLaser extends Pulse{
    /*Pulsar Laser, as used on Ch'Lonas ships*/
        public $name = "customPulsarLaser";
        public $displayName = "Pulsar Laser";
        public $animation = "laser";
        public $animationColor = array(130, 50, 200);
        public $animationWidth = 3;
        public $animationWidth2 = 0.5;
        public $uninterceptable = true;
        public $priority = 5;

        public $grouping = 25;
        public $maxpulses = 4;
        protected $useDie = 3; //die used for base number of hits
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 3, 3); // fighters, <mediums, <capitals 
        
   
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



class CustomStrikeLaser extends Weapon{
    /*Srike Laser, as used on Ch'Lonas ships*/
        public $name = "customStrikeLaser";
        public $displayName = "Strike Laser";
        public $animation = "laser";
        public $animationColor = array(130, 25, 200);
        public $animationWidth = 4;
        public $animationWidth2 = 0.5;
        public $uninterceptable = true;
        public $loadingtime = 3;
        public $rangePenalty = 0.5;
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals
        public $priority = 6;
    
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


class HLPA extends Weapon{ 
/*Heavy Laser-Pulse Array - let's try to create it using new mode change mechanism...*/	
        public $name = "hlpa";
        public $displayName = "Heavy Laser-Pulse Array";
	    public $iconPath = "hlpa.png";
	
	//visual display - will it be enough to ensure correct animations?...
	public $animationArray = array(1=>'laser', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(190, 75, 20));
        public $animationWidthArray = array(1=>4, 2=>5);
        public $animationWidth2 = 0.2; //not used for Trail animation?...
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 20;//not used for Laser animation?...
        public $projectilespeed = 20;//not used for Laser animation?...
        public $animationExplosionScale = 0.20;//not used for Laser animation?...
	
	
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
		$this->data["Special"] = 'Can fire as either Heavy Laser or Heavy Pulse Cannon. ';
		parent::setSystemDataWindow($turn);
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
	public $animationArray = array(1=>'laser', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 11, 11), 2=>array(190, 75, 20));
        public $animationWidthArray = array(1=>4, 2=>5);
        public $animationWidth2 = 0.2; //not used for Trail animation?...
	public $trailColor = array(190, 75, 20); //not used for Laser animation?...
        public $trailLength = 20;//not used for Laser animation?...
        public $projectilespeed = 20;//not used for Laser animation?...
        public $animationExplosionScale = 0.20;//not used for Laser animation?...
	
	
	//actual weapons data
        public $groupingArray = array(1=>0, 2=>20);
        public $maxpulses = 6; //only useful for Pulse mode
	public $raking = 10; //only useful for Raking mode
        public $priorityArray = array(1=>8, 2=>4);
	public $uninterceptableArray = array(1=>true, 2=>false);
	public $defaultShotsArray = array(1=>1, 2=>6); //for Pulse mode it should be equal to maxpulses
	
        public $loadingtimeArray = array(1=>3, 2=>2); //mode 1 should be the one with longest loading time
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
		    $maxhealth = 9;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 5;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
		$this->data["Special"] = 'Can fire as either Medium Laser or Medium Pulse Cannon. ';
		parent::setSystemDataWindow($turn);
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
    public $displayName = "Absorbtion Shield";
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
    private function checkIsFighterUnderShield($target, $shooter){ //no flying under SW shield
        return false;
    }
	
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn()) return 0; //destroyed shield gives no protection
        $output = $this->output + $this->getBoostLevel($turn);
	$output += $this->outputMod; //outputMod itself is negative!
	if($weapon->damageType == 'Raking') $output = 2*$output;//Raking - double effect!
	$output=max(0,$output); //no less than 0!
        return $output;
    }
	
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	//$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
	$this->data["Basic Strength"] = $this->baseOutput;      
	$this->data["<font color='red'>Remark</font>"] = "<br>Does not decrease profile.";  
	$this->data["<font color='red'>Remark</font>"] .= "<br>Doubly effective vs Raking weapons."; 
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
        public $animationWidth = 4;
        public $animationWidth2 = 0.5;
        public $uninterceptable = false;
        public $loadingtime = 2;
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 4, 6); // fighters, <mediums, <capitals
        public $priority = 6;
	public $rakes = array();
	public $firingModes = array(1=>'Concentrated', 2=>'Split');
	public $damageTypeArray = array(1=>'Raking', 2=>'Raking'); //indicates that this weapon does damage in Pulse mode
	public $gunsArray = array(1=>1,2=>3);
	
	    //public $damageType = 'Raking'; 
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



class customLtPolarityPulsar extends Pulse{
        public $name = "customLtPolarityPulsar";
        public $displayName = "Light Polarity Pulsar";
	public $weaponClass = "Molecular"; 
        public $animation = "trail";
        public $animationWidth = 3;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $trailLength = 10;
        
	public $iconPath = "LightPolarityPulsar.png";
        public $trailColor = array(255,140,0); //let's make Polarity Pulsars orange...
        public $animationColor = array(255,140,0);

		
        public $loadingtime = 1;
        public $priority = 4;
	        
        public $rangePenalty = 2;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals 
        
	public $grouping = 15; //+1 per 3
	public $maxpulses = 6;
	protected $useDie = 5; //die used for base number of hits;
        public $intercept = 2;
	
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
	public $weaponClass = "Molecular"; 
        public $animation = "trail";
        public $rof = 2;
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 15;
        public $animationExplosionScale = 0.17;
        
	public $iconPath = "MediumPolarityPulsar.png";
        public $trailColor = array(255,140,0); //let's make Polarity Pulsars orange...
        public $animationColor = array(255,140,0);

		
        public $loadingtime = 2;
        public $priority = 5;
	        
        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals 
        
	public $grouping = 15; //+1 per 3
	public $maxpulses = 5;
	protected $useDie = 4; //die used for base number of hits;
        public $intercept = 2;
	
	
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




    class customMphasedBeamAcc extends Weapon{
	public $name = "customMphasedBeamAcc";
        public $displayName = "Multiphased Beam Accelerator";
	public $animation = "laser";
        public $animationColor = array(225,130,0);
        public $animationWidth = 4;
        public $animationWidth2 = 0.2;
	    public $iconPath = "MultiphasedBeamAccelerator.png";
	    
        public $raking = 10;
	    
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
		
		
	public function getDamage($fireOrder){
            switch($this->turnsloaded){
                case 0: 
                case 1:
                    return Dice::d(10,2)+2;
                case 2:
                    return Dice::d(10, 4)+4;
                case 3:
                default:
                    return Dice::d(10,8)+8;
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
                default:
                    $this->maxDamage = 88 ;  
                    break;
            }
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
	protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
		$armour = parent::getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos);
		    if (is_numeric($armour)){
			$toIgnore = ceil($armour /2);
			$new = $armour - $toIgnore;
			return $new;
		    }
		    else {
			return 0;
		    }
        }	    
	    
        public function setSystemDataWindow($turn){
		$this->data["Special"] = 'Ignores 1/2 of armor. Can fire accelerated for less damage.';
		parent::setSystemDataWindow($turn);
        }
	    
    }//endof class customMphasedBeamAcc






    class customLtPhaseDisruptor extends LinkedWeapon{
        public $trailColor = array(255, 170, 10);
        public $name = "customLtPhaseDisruptor";
        public $displayName = "Light Phase Disruptor";
        public $animation = "trail";
        public $animationColor = array(255, 170, 10);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 13;
        public $animationWidth = 2;
        public $trailLength = 13;
        public $intercept;
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        
        public $damageType = "Standard"; 
        public $weaponClass = "Molecular"; 
	    
        
        function __construct($startArc, $endArc){
            $this->defaultShots = 2;
            $this->shots = 2;
            $this->intercept = 2;
            $this->iconPath = "PhaseDisruptor.png";
	    $this->isLinked = false; //shots are separate, not linked! 
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] .= "Shots are NOT linked";
        }
	    
        public function getDamage($fireOrder){        return Dice::d(6,2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    } //endof class customLtPhaseDisruptor



?>
