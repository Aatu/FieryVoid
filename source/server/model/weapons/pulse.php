<?php

class Pulse extends Weapon{
        
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        public $grouping = 20;
        public $maxpulses = 6;
        public $priority = 5;
	public $damageType = 'Pulse'; //indicates that this weapon does damage in Pulse mode
    	public $weaponClass = "Particle"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	protected $useDie = 5; //die used for base number of hits
	protected $fixedBonusPulses=0;//for weapons doing dX+Y pulse
        public $firingModes = array( 1 => "Pulse"); //just a convenient name for firing mode
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
	    $this->data["Special"] = 'Pulse mode: D'.$this->useDie;
		if($this->fixedBonusPulses > 0){
			$this->data["Special"] .= '+'.$this->fixedBonusPulses;
		}
		$this->data["Special"] .= ', +1/'. $this->grouping."%, max. ".$this->maxpulses." pulses";
            $this->defaultShots = $this->maxpulses;            
            parent::setSystemDataWindow($turn);
        }
        
        protected function getPulses($turn)
        {
            return Dice::d($this->useDie) + $this->fixedBonusPulses;
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
	
	//Pulse weapon usually have fixed damage, so... non-fixed damage weapons would have to override as usual
        public function setMinDamage(){      $this->minDamage = $this->getDamage(null);      }
        public function setMaxDamage(){      $this->maxDamage = $this->getDamage(null);      }

	
} //endof class Pulse



class EnergyPulsar extends Pulse{
        public $name = "energyPulsar";
        public $displayName = "Energy Pulsar";
        public $animation = "trail";
        public $animationWidth = 5;
        public $projectilespeed = 13;
        public $animationExplosionScale = 0.30;
        public $rof = 2;
        public $trailLength = 12;
        public $grouping = 25;
        public $maxpulses = 3;
        public $priority = 4;
	protected $useDie = 2; //die used for base number of hits	

        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 3); // fighters, <mediums, <capitals 
        
        public $intercept = 1;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


        public function setSystemDataWindow($turn){
            
            parent::setSystemDataWindow($turn);
            //$this->data["Pulses"] = 'D 2';
        }

        
        public function getDamage($fireOrder){        return 10;   }
        //public function setMinDamage(){     $this->minDamage = 10 ;      }
        //public function setMaxDamage(){     $this->maxDamage = 10 ;      }

}
    


class ScatterPulsar extends Pulse{

        public $name = "scatterPulsar";
        public $displayName = "Scatter Pulsar";
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 13;
        public $animationExplosionScale = 0.10;
        public $rof = 3;
        public $grouping = 25;
        public $maxpulses = 6;
        
        public $loadingtime = 1;
        public $intercept = 2;
        public $priority = 3;
        
        public $rangePenalty = 2;
        public $fireControl = array(3, 2, 1); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 6;   }
    }
    



class QuadPulsar extends Pulse{

        public $name = "quadPulsar";
        public $displayName = "Quad Pulsar";
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 6;
        public $projectilespeed = 20;
        public $animationExplosionScale = 0.25;
        public $rof = 3;
        public $grouping = 25;
        public $maxpulses = 4;
        public $priority = 6;
	protected $useDie = 3; //die used for base number of hits	
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 3, 3); // fighters, <mediums, <capitals 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);
            //$this->data["Pulses"] = 'D 3';            
        }

	
        public function getDamage($fireOrder){        return 14;   }
    }
    
    
    class LightPulse extends Pulse{

        public $name = "lightPulse";
        public $displayName = "Light Pulse Cannon";
        public $animation = "trail";
        public $animationWidth = 3;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $trailLength = 10;
        
        public $loadingtime = 1;
        public $priority = 3;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals 
        
        public $intercept = 2;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 8;   }

    }
    
    class MediumPulse extends Pulse{

        public $name = "mediumPulse";
        public $displayName = "Medium Pulse Cannon";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 15;
        public $animationExplosionScale = 0.17;
        public $rof = 2;
        
        public $loadingtime = 2;
        public $priority = 4;
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 4); // fighters, <mediums, <capitals 
        
        public $intercept = 2;
        
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 10;   }
    }
    

    class HeavyPulse extends Pulse{

        public $name = "heavyPulse";
        public $displayName = "Heavy Pulse Cannon";
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 20;
        public $animationExplosionScale = 0.20;
        public $rof = 2;
        public $priority = 6;
        public $intercept = 1;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-1, 3, 4); // fighters, <mediums, <capitals 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 15;   }
    }
    
    
    class GatlingPulseCannon extends Weapon{  //this is NOT a Pulse weapon at all...

        public $name = "gatlingPulseCannon";
        public $displayName = "Gatling Pulse Cannon";
        public $animation = "beam";
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $trailLength = 10;
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        
        public $intercept = 2;
        
        public $loadingtime = 1;
	public $priority = 5;//this has firepower of a heavy weapon
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
        public $damageType = "Standard";
        public $weaponClass = "Particle"; 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6,2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 18 /*- $this->dp*/;      }

    }


    class MolecularPulsar extends Pulse
    {
        public $name = "molecularPulsar";
        public $displayName = "Molecular Pulsar";
        public $iconPath = "mediumPulse.png";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 25;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        public $rof = 2;
        public $maxpulses = 7;
        public $grouping = 15;
        public $priority = 4;
        public $intercept = 2;

        public $loadingtime = 1;
    	public $normalload = 2;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

	    public $damageType = "Pulse"; 
	    public $weaponClass = "Molecular"; 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        public function setSystemData($data, $subsystem){
	    parent::setSystemData($data, $subsystem);
            if ($this->turnsloaded == 1)
            {
                $this->maxpulses = 3;
            }
            else
            {
                $this->maxpulses = 7;
            }
		
	}
        
        public function setSystemDataWindow($turn){
                parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Pulse mode fully armed: D 5 +1/15 %, max. 7 pulses';
		$this->data["Special"] .= 'Pulse mode 1 turn: D 3 pulses';
        }
        

        protected function getPulses($turn)
        {
            if ($this->turnsloaded == 1)
            {
                return Dice::d(3);
            }
            else
            {
                return Dice::d(5);
            }
        } 
	    
        protected function getExtraPulses($needed, $rolled)
        {
            if ($this->turnsloaded == 1)
            {
                return 0;
            }
            else
            {
                return parent::getExtraPulses($needed, $rolled);
            }
        }

        public function getDamage($fireOrder){ return 10; }
    }



    class PairedLightBoltCannon extends LinkedWeapon{
        public $name = "pairedLightBoltCannon";
        public $displayName = "Light Bolt Cannon";
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

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus /*- $this->dp*/;      }
    }



    class PointPulsar extends Weapon //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
        public $name = "pointPulsar";
        public $displayName = "Point Pulsar";
        public $iconPath = "pointPulsar.png";
        public $animation = "trail";
        public $trailLength = 13;
        public $animationWidth = 4;
        public $projectilespeed = 25;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        public $guns = 3; //always 3, completely separate (not Pulse!) shots
        public $maxpulses = 3;
        public $grouping = 0;
        public $loadingtime = 2;
        public $normalload = 2;
	    
        public $priority = 2; //due to sniping bonus
        
        public $calledShotMod = -4; //instead of usual -8
	    
        public $intercept = 2; //should be 3, but then intercept should be like a Pulse weapon - just once... call this a compromise!
	    
        public $rangePenalty = 0.5;
        public $fireControl = array(-4, 3, 5); // fighters, <mediums, <capitals
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Particle"; 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    
        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);		
		$this->data["Special"] = "Called shot penalty halved";
        }
        
        public function getDamage($fireOrder){
            return 10 /*- $this->dp*/;
        }
 
        public function setMinDamage()
        {
            $this->minDamage = 10 /*- $this->dp*/;
        }
        public function setMaxDamage()
        {
            $this->maxDamage = 10 /*- $this->dp*/;
        }
    }


/* new Scatter Pulsar - it was initially made as Pulse weapon, only later brought to correctness
	number of shots is rolled after firing declaration (eg. after declaring offensive fire but before assigning interceptions)
*/
    class ScatterGun extends Weapon //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
	public $name = "scatterGun";
        public $displayName = "Scattergun";
        public $iconPath = "scatterGun.png";
        public $animation = "trail";
        public $trailLength = 13;
        public $animationWidth = 4;
        public $projectilespeed = 20;
        public $animationExplosionScale = 0.15;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
	public $guns = 1; //multiplied to d6 at firing
	     
        public $loadingtime = 1;
        public $normalload = 1;	    
        public $priority = 3; //very light weapon
        	    
        public $intercept = 2; //as it should be, but here they CAN combine vs same shot!
	    
	public $rangePenalty = 2;
        public $fireControl = array(5, 2, 0); // fighters, <mediums, <capitals
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Particle"; 
	    
	    //temporary private variables
	    private $multiplied = false;
	    private $alreadyIntercepted = array();
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 8;
		if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    
        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);		
		$this->data["Special"] = "Fires d6 separate shots (actual number rolled at firing resolution).";
		$this->data["Special"] .= "<br>When fired defensively, a single Scattergun cannot engage the same incoming shot twice (even ballistic one).";
        }
	    
	//if fired offensively - make d6 attacks (copies of 1 existing); 
	//if defensively - make weapon have d6 GUNS (would be temporary, but enough to assign multiple shots for interception)
	public function beforeFiringOrderResolution($gamedata){
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;
		$noOfShots = Dice::d(6,1); //actual number of shots for this turn

		foreach($this->fireOrders as $fire){
			if(($fire->type =='normal') && ($fire->turn == $gamedata->turn)) $offensiveShot = $fire;
		}
		if($offensiveShot!==null){ //offensive fire declared, multiply!
			while($noOfShots > 1){ //first shot is already declared!
				$multipliedFireOrder = new FireOrder( -1, $offensiveShot->type, $offensiveShot->shooterid, $offensiveShot->targetid,
					$offensiveShot->weaponid, $offensiveShot->calledid, $offensiveShot->turn, $offensiveShot->firingMode,
					0, 0, 1, 0, 0, null, null
				);
				$multipliedFireOrder->addToDB = true;
				$this->fireOrders[] = $multipliedFireOrder;
				$noOfShots--;	      
			}
		}else{//offensive fire NOT declared, multiply guns for interception!
			$this->guns = $noOfShots; //d6 intercept shots
		}
	} //endof function beforeFiringOrderResolution
        
	    /*return 0 if given fire order was already intercepted by this weapon - this should prevent such assignment*/
	public function getInterceptionMod($gamedata, $intercepted)
	{
		$wasIntercepted = false;
		$interceptMod = 0;
		foreach($this->alreadyIntercepted as $alreadyAssignedAgainst){
			if ($alreadyAssignedAgainst->id == $intercepted->id){ //this fire order was already intercepted by this weapon, this Scattergun cannot do so again
				$wasIntercepted = true;
				break;//foreach
			}
		}
		if(!$wasIntercepted) $interceptMod = parent::getInterceptionMod($gamedata, $intercepted);
		return $interceptMod;
	}//endof  getInterceptionMod
        
	//on weapon being ordered to intercept - note which shot (fireorder, actually) was intercepted!
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		parent::fireDefensively($gamedata, $interceptedWeapon);
		$this->alreadyIntercepted[] = $interceptedWeapon;
	}	    
	    
        public function getDamage($fireOrder){
            return Dice::d(6,2)+1; //2d6+1
        }
 
        public function setMinDamage()
        {
            $this->minDamage = 3;
        }
        public function setMaxDamage()
        {
            $this->maxDamage = 13 ;
        }
    }



    class BlastCannonFamily extends Pulse{
	/*core for all Blast Cannon family weapons*/
        public $animation = "trail";
	public $trailColor = array(140, 140, 140);
        public $animationColor = array(140, 140, 140);
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;
        public $rof = 3; //used for threat estimation at interception
	public $intercept = 1;
	    
        public $priority = 3;	    
	public $grouping = 25; //+1/5
        public $maxpulses = 4;
	protected $useDie = 3; //die used for base number of hits
	    
	public $noOverkill = true;//Matter weapons do not overkill
    	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	         
	public function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }
	    

    } //end of class BlastCannonFamily


    class LtBlastCannon extends BlastCannonFamily{
	/* Belt Alliance Light Blast Cannon - Matter Pulse weapon*/
        public $name = "LtBlastCannon";
        public $displayName = "Light Blast Cannon";
	    public $iconPath = 'LightBlastCannon.png';
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;

        public $priority = 3;
	    
        public $grouping = 25; //+1/5
        public $maxpulses = 4;
	protected $useDie = 3; //die used for base number of hits
        
        public $loadingtime = 1;
        
        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(0, 1, 2); // fighters, <mediums, <capitals 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 4;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 1;
		}		
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function getDamage($fireOrder){        return 3;   }
    }

    class MedBlastCannon extends BlastCannonFamily{
	/* Belt Alliance Medium Blast Cannon - Matter Pulse weapon*/
        public $name = "MedBlastCannon";
        public $displayName = "Medium Blast Cannon";
	    public $iconPath = 'MediumBlastCannon.png';
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;

        public $priority = 4;
	    
        public $grouping = 25; //+1/5
        public $maxpulses = 5;
	protected $useDie = 5; //die used for base number of hits
        
        public $loadingtime = 2;
        
        public $rangePenalty = 0.5; //-1/2hex
        public $fireControl = array(0, 2, 3); // fighters, <mediums, <capitals 
        
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 5;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 2;
		}		
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function getDamage($fireOrder){        return 5;   }
    }

    class HvyBlastCannon extends BlastCannonFamily{
	/* Belt Alliance Heavy Blast Cannon - Matter Pulse weapon*/
        public $name = "HvyBlastCannon";
        public $displayName = "Heavy Blast Cannon";
	    public $iconPath = 'HeavyBlastCannon.png';
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.15;

        public $priority = 5;
	    
        public $grouping = 25; //+1/5
        public $maxpulses = 6;
	protected $useDie = 6; //die used for base number of hits
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33; //-1/3hex
        public $fireControl = array(0, 3, 4); // fighters, <mediums, <capitals 
        
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
	    
        public function getDamage($fireOrder){        return 8;   }
    }


?>
