<?php

class Pulse extends Weapon{        
        public $grouping = 20;
        public $maxpulses = 6;
        public $maxpulsesArray = array();
        public $rof = 4;
        public $priority = 5;
	public $damageType = 'Pulse'; //indicates that this weapon does damage in Pulse mode
    	public $weaponClass = "Particle"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	protected $useDie = 5; //die used for base number of hits
	protected $fixedBonusPulses=0;//for weapons doing dX+Y pulse
        public $firingModes = array( 1 => "Pulse"); //just a convenient name for firing mode
		
        public $animation = "bolt";
        public $animationColor = array(190, 75, 20);
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){     
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Pulse mode: D'.$this->useDie;
			if($this->fixedBonusPulses > 0){
				$this->data["Special"] .= '+'.$this->fixedBonusPulses;
			}
			$this->data["Special"] .= ', +1/'. $this->grouping."%, max. ".$this->maxpulses." pulses";
            $this->defaultShots = $this->maxpulses;       
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
	/*
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 5;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.30;
	*/
        public $rof = 2;
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
	/*
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 9;
        public $animationExplosionScale = 0.10;
        */
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
	/*
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 6;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.25;
        */
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
	    /*
        public $animation = "trail";
        public $animationWidth = 3;
        public $projectilespeed = 8;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $trailLength = 10;
*/
	    
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
	    /*
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.17;
        public $rof = 2;
        */
	    
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
	    /*
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.20;
        public $rof = 2;
	*/
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
	    /*
        public $animation = "beam";
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $trailLength = 10;
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        */
	    public $rof = 1;
        public $intercept = 2;
        
        public $loadingtime = 1;
	public $priority = 6;//VERY large fighter weapon
        
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
	    
        public $animationColor =  array(175, 225, 175); //closer to Neutron Laser than Particle-based Pulse family
	    /*
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        public $rof = 2;
	*/
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
		    $this->rof = 2;
            }
            else
            {
                $this->maxpulses = 7;
		    $this->rof = 4;
            }		
		}
        
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Fully armed: d5 +1/15 %, max. 7 pulses';
			$this->data["Special"] .= '<br>1 turn: d3 pulses, no volley count bonus';
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
	    /*
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
	*/
		public $priority = 3;

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

            if ($damagebonus >= 3) $this->priority++; //heavier varieties fire later in the queue
            if ($damagebonus >= 5) $this->priority++;
            if ($damagebonus >= 7) $this->priority++;

            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "pairedParticleGun3.png";
            }
			
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
	    
        public $animation = "bolt";	    
        public $animationColor = array(190, 75, 20);

        public $guns = 3; //always 3, completely separate (not Pulse!) shots
        public $maxpulses = 3;
        public $grouping = 0;
        public $loadingtime = 2;
        public $normalload = 2;
		public $firingModes = array(
			1 => "Normal",
            2 => "Split"
		);
        public $priority = 5; //medium standard shots - if called shots are used, they should be prioritized independently of base weapon priority
        
        public $calledShotMod = -4; //instead of usual -8
	    
        public $intercept = 2; //should be 3, but then intercept should be like a Pulse weapon - just once... call this a compromise!
	    
        public $rangePenalty = 0.5;
        public $fireControl = array(-4, 3, 5); // fighters, <mediums, <capitals
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Particle"; 
        public $canSplitShots = false;        
        public $canSplitShotsArray = array(1 => false, 2 => true);
        public $specialHitChanceCalculation = true;
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    
        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);		
            $this->data["Special"] = "Fires three shots.";
            $this->data["Special"] .= "<br>Can use 'Split' Firing Mode to target shots separately, but all shots must be against the same unit.";		    
            $this->data["Special"] .= "<br>Called shot penalty halved agaisnt ship systems.";
            $this->data["Special"] .= "<br>No called shot penalty against fighters.";            
        }

	public function calculateHitBase($gamedata, $fireOrder){

		$target = $gamedata->getShipById($fireOrder->targetid);        
        if($fireOrder->calledid !== -1 && $target instanceof FighterFlight) $this->calledShotMod = 0; //If called shot on fighter, 0 called shot mod.

		parent::calculateHitBase($gamedata, $fireOrder);
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


/* new ScatterGun - it was initially made as Pulse weapon, only later brought to correctness
	number of shots is rolled after firing declaration (eg. after declaring offensive fire but before assigning interceptions)
*/
    class ScatterGun extends Weapon //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
	public $name = "scatterGun";
        public $displayName = "Scattergun";
        public $iconPath = "scatterGun.png";
	    	    
        public $animation = "bolt";	    
        public $animationColor = array(190, 75, 20);
	    /*
        public $animation = "trail";
        public $trailLength = 13;
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
	*/
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
    }//endof Scattergun


//Markab fighter weapon - d3 shots (here treated as a single Pulse shot, no grouping bonus)
class LightScattergun extends ScatterGun{
	public $name = "LightScattergun";
	public $displayName = "Light Scattergun";
	public  $iconPath = "scatterGun.png";
   
	public $animation = "bolt";
	public $animationColor = array(190, 75, 20);
	
	public $intercept = 2;	
	public $rangePenalty = 2; //-2/hex	
	public $priority = 4;

	//temporary private variables
	private $multiplied = false;
	private $alreadyIntercepted = array();
	
	function __construct($startArc, $endArc){//more than a single emplacement not supported!
		$this->defaultShots = 1;
        $this->shots = 1;							
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    
		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Fires d3 separate shots (actual number rolled at firing resolution).";
			$this->data["Special"] .= "<br>When fired defensively, a single Scattergun cannot engage the same incoming shot twice (even ballistic one).";
	}
    
	//if fired offensively - make d3 attacks (copies of 1 existing); 
	//if defensively - make weapon have d3 GUNS (would be temporary, but enough to assign multiple shots for interception)
	public function beforeFiringOrderResolution($gamedata){
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;
		$noOfShots = Dice::d(3,1); //actual number of shots for this turn

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
	
	public function getDamage($fireOrder){        return Dice::d(6,2);   }
	public function setMinDamage(){     $this->minDamage = 2 ;      }
	public function setMaxDamage(){     $this->maxDamage = 12 ;      }
	
} //end of class LightScattergun


    class BlastCannonFamily extends Pulse{
	/*core for all Blast Cannon family weapons*/
        public $animation = "bolt";
        public $animationColor = array(140, 140, 140);
	    /*
	public $trailColor = array(140, 140, 140);
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;
	*/
        public $rof = 3; //used for threat estimation at interception
	public $intercept = 1;
	    
        public $priority = 3;	    
	public $grouping = 25; //+1/5
        public $maxpulses = 4;
	protected $useDie = 3; //die used for base number of hits
	    
	public $noOverkill = true;//Matter weapons do not overkill
    	public $damageType = "Pulse"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	         //Matter ignores armor - now handled by standard routines

    } //end of class BlastCannonFamily


    class LtBlastCannon extends BlastCannonFamily{
	/* Belt Alliance Light Blast Cannon - Matter Pulse weapon*/
        public $name = "LtBlastCannon";
        public $displayName = "Light Blast Cannon";
	    public $iconPath = 'LightBlastCannon.png';
	    /*
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;
*/
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
	    /*
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;
*/
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
	    /*
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.15;
*/
	    
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
    } //endof class HvyBlastCannon
	
	

/*Torata weapon*/
class PulseAccelerator extends Pulse{
	public $name = "PulseAccelerator";
	public $displayName = "Pulse Accelerator";
	public $iconPath = "PulseAccelerator.png";
	
	/*
	public $animation = "bolt";
	public $trailLength = 18;
	public $animationWidth = 5;
	public $projectilespeed = 13;
	public $animationExplosionScale = 0.20;
	*/
	public $rof = 2;
	public $maxpulses = 4;
	public $grouping = 25;
	public $priority = 5; 

	public $loadingtime = 1;
	public $normalload = 3;

	public $rangePenalty = 0.33; //-1/3 hexes
	public $fireControl = array(1, 3, 4);

	public $damageType = "Pulse";
	public $weaponClass = "Particle";

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{ //maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 9;
		if ( $powerReq == 0 ) $powerReq = 4;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	
	protected function getPulses($turn){
		switch($this->turnsloaded){
			Case 0:
			case 1:
				return 1;
				break;
			case 2:
				return Dice::d(2,1);
				break;
			Case 3:
				return Dice::d(3,1);	
			}
	}
	
	public function setSystemData($data, $subsystem){
		parent::setSystemData($data, $subsystem);
		if ($this->turnsloaded == 1){
			$this->maxpulses = 2;
		}else if ($this->turnsloaded == 2){
			$this->maxpulses == 3;
		}else{		
			$this->maxpulses = 4;
		}
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Volley count bonus: +1/25% . Number of pulses varies with loading time:';
		$this->data["Special"] .= '<br>1 turn: 1 base hit, max. 2 hits';
		$this->data["Special"] .= '<br>2 turns: d2 base hits, max. 3 hits';		
		$this->data["Special"] .= '<br>3 turns: d3 base hits, max. 4 hits';			
	}

	public function getDamage($fireOrder){	 	return 12;	 }	
} //End of class PulseAccelerator


	/*Shadow Pulse weapon; ignores shields and shield-like systems (except EM Shields), except those operated by advanced races
		profile reduction interaction needs to be coded in .js, as well!
	*/
    class PhasingPulseCannon extends Pulse{
        public $name = "PhasingPulseCannon";
        public $displayName = "Phasing Pulse Cannon";
        //public $rof = 3;
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
		
        public $animation = "bolt";
        public $animationColor = array(50, 125, 210); //let's make it blue-ish...
		/*
        public $animation = "trail";
        public $trailLength = 15; //meaningless?...
        public $animationWidth = 4; //meaningless?...
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.2;
        public $trailColor = array(170, 170, 170); //meaningless?...
        public $animationColor = array(50, 125, 210); //let's make it blue-ish...
		*/
	    
        public $grouping = 15; //+1 hit per 3 below target number
        public $maxpulses = 6;
		public $damageType = 'Pulse'; //indicates that this weapon does damage in Pulse mode
    	public $weaponClass = "Molecular"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
		protected $useDie = 5; //die used for base number of hits
        
        public $loadingtime = 2;
        public $priority = 5;
        
        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(2, 4, 6); // fighters, <mediums, <capitals 
        
        public $intercept = 3;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 7;
			if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){ 
			return 13;   
		}
		
		
		//ignores shields, unless EM or on Ancient+ ship
		public function shieldInteractionDefense($target, $shooter, $pos, $turn, $shield, $mod) {
			$toReturn = min(0,$mod);//negative "shielding" is usually a techincal system of some kind (Vorlon Petals?), do NOT ignore it
			if ($target->factionAge>=3) $toReturn = $mod;
			if ($shield instanceOf EMShield) $toReturn = $mod;
			return $toReturn; 	
		}
		
		//ignores shields, unless EM or on Ancient+ ship
		public function shieldInteractionDamage($target, $shooter, $pos, $turn, $shield, $mod) {
			$toReturn = min(0,$mod);//negative "shielding" is usually a techincal system of some kind (Vorlon Petals?), do NOT ignore it
			if ($target->factionAge>=3) $toReturn = $mod;
			if ($shield instanceOf EMShield) $toReturn = $mod;
			return $toReturn; 		
		}	

        public function setSystemDataWindow($turn){    
            parent::setSystemDataWindow($turn);      
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
            $this->data["Special"] .= 'Ignores non-Ancient shields and shield-like systems (both profile and damage reduction)';
            $this->data["Special"] .= ', EXCEPT EM shields.';
        }
		
    }//endof class PhasingPulseCannon
	
	class PhasingPulseCannonH extends PhasingPulseCannon{
        public $name = "PhasingPulseCannonH";
        public $displayName = "Heavy Phasing Pulse Cannon";
        //public $rof = 3;
		
		/*
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 6;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.4;
        //public $trailColor = array(170, 170, 170);
        //public $animationColor = array(216, 216, 216);	
        */
	
        public $loadingtime = 3;
        public $priority = 6;
        
        public $rangePenalty = 0.5; //-1/2hexes
        public $fireControl = array(2, 4, 6); // fighters, <mediums, <capitals 
        
        public $intercept = 2;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 9;
			if ( $powerReq == 0 ) $powerReq = 5;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 18;   }
		
    }//endof class PhasingPulseCannonH


    class TriopticPulsar extends Pulse //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
        public $name = "TriopticPulsar";
        public $displayName = "Trioptic Pulsar";
        public $iconPath = "TriopticPulsar.png";
	    
        public $animation = "bolt";	    
	 	public $animationColor = array(204, 102, 0);

        public $maxpulses = 3;
        public $grouping = 0;
        public $loadingtime = 1;
        public $normalload = 1;
	    
        public $priority = 4;    
        public $intercept = 2; //should be 3, but then intercept should be like a Pulse weapon - just once... call this a compromise!
	    
        public $rangePenalty = 0.5;
        public $fireControl = array(4, 3, 2); // fighters, <mediums, <capitals
	    
	    public $damageType = "Pulse"; 
	    public $weaponClass = "Particle"; 
	    
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 3;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}
        
	    
        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);		
		$this->data["Special"] = "Always fires three pulses.";
        }

        protected function getPulses($turn)
        {
            return 3;
        }
	
        protected function getExtraPulses($needed, $rolled)
        {
            return 0;
        }

        
        public function getDamage($fireOrder){
            return Dice::d(10,2); 
        }
 
        public function setMinDamage()
        {
            $this->minDamage = 2;
        }
        public function setMaxDamage()
        {
            $this->maxDamage = 20;
        }
    }//endof class TriopticPulsar


class UltraPulseCannon extends Pulse{        
    public $name = "UltraPulseCannon";
    public $displayName = "Ultra Pulse Cannon";
    public $iconPath = "UltraPulseCannon.png";
    public $animation = "bolt";
    public $animationColor = array(204, 102, 0);
    
    public $grouping = 20;
    public $groupingArray = array(1=>20, 2=>15, 3=>10);    
    public $maxpulses = 6;
    public $maxpulsesArray = array(1=>6, 2=>9, 3=>12);	    
    public $rof = 4;
	public $rofArray = array(1=>4, 2=>5, 3=>6);      
    public $priority = 7;
    public $priorityArray = array(1=>7, 2=>6, 3=>4);    
	public $damageType = 'Pulse'; //indicates that this weapon does damage in Pulse mode
    public $weaponClass = "Particle"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	public $intercept = 6; //intercept rating -1
    public $loadingtime = 1;
    public $normalload = 1;
    public $rangePenalty = 0.25;    
    public $rangePenaltyArray = array(1=>0.25, 2=>0.33, 3=>0.5);   	      
    	
	protected $useDie = 3; //die used for base number of hits
	protected $useDieArray = array(1=>3, 2=>5, 3=>6);  	
	protected $fixedBonusPulses=0;//for weapons doing dX+Y pulse
	
    public $firingModes = array( 1 => "Heavy",
    							 2 => "Medium",
    							 3 => "Light"	
    							); //just a convenient name for firing mode
		

    public $fireControl = array(4, 6, 8); // fighters, <mediums, <capitals 
    public $fireControlArray = array( 1=>array(4, 6, 8), 2=>array(6, 6, 6), 3=>array(8, 6, 4));    
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 28;
		if ( $powerReq == 0 ) $powerReq = 12;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Pulse characterstics vary depending on Firing Mode:';
		$this->data["Special"] .= '<br> - HEAVY: 24 Damage, D3 Pulses, 20% grouping, Max. Pulses: 6';
		$this->data["Special"] .= '<br> - MEDIUM: 16 Damage, D5 Pulses, 15% grouping, Max. Pulses: 9';	
		$this->data["Special"] .= '<br> - LIGHT: 12 Damage, D6 Pulses, 10% grouping, Max. Pulses: 12';			
	}
        
    protected function getPulses($turn){
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
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return 24; //Heavy Pulse
				break;	
			case 2:
				return 16; //Medium Pulse
				break;
			case 3:
				return 12; //Light Pulse
				break;
			default:
				return 24;
				break;							
		}
	}
        public function setMinDamage(){ 
			switch($this->firingMode){
				case 1:
					$this->minDamage = 24; //Heavy Pulse
					break;
				case 2:
					$this->minDamage = 16; //Medium Pulse
					break;
				case 3:
					$this->minDamage = 12; //Light Pulse
					break;
				default:
					$this->maxDamage = 24; //Hvy Pulse
					break;													
			}
			$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}
	    
	    public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 24; //Hvy Pulse
					break;
				case 2:
					$this->maxDamage = 16; //Medium Pulse
					break;
				case 3:
					$this->maxDamage = 12; //Light Pulse
					break;
				default:
					$this->maxDamage = 24; //Hvy Pulse
					break;																		
			}
			$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}  
               
	
} //endof class UltraPulseCannon


class VolleyLaser extends Pulse{
        public $name = "VolleyLaser";
        public $displayName = "Volley Laser";
        public $animationColor = array(255, 255, 0);
        public $animation = "bolt"; 	
	
        public $uninterceptable = true;
        public $priority = 6;

        public $grouping = 15;
        public $maxpulses = 6;
        protected $useDie = 3; //die used for base number of hits
        public $loadingtime = 1;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(4, 5, 6); // fighters, <mediums, <capitals 
        public $intercept = 3;        
	    public $weaponClass = "Laser"; 
   
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		
        public function setSystemDataWindow($turn)
        {
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Uninterceptable.";      
		}
        
        
        public function getDamage($fireOrder){        return 15;   }

} //VolleyLaser

/* ================================================================================================
 * WALKERS OF SIGMA-957 - Chromatic Pulse Driver         WALKERS_OF_SIGMA_PLAN.md section 3.4
 * ================================================================================================
 *
 * ⚠️ THE REST OF THE WALKER ARSENAL LIVES IN specialWeapons.php / special.js (Lightning Array
 * family, sections 3.1/3.2). This one is HERE because it is a Pulse weapon and must extend Pulse -
 * and on the client that is a hard constraint, not a preference: game.php and gamelobby.php load
 * special.js BEFORE pulse.js, so a class in special.js could not do Object.create(Pulse.prototype)
 * at load time. Both halves therefore live in the pulse files, so the pairing stays symmetric.
 *
 * WHAT A CHROMATIC PULSE DRIVER IS
 * An ACCELERATOR pulse weapon with two firing modes.
 *
 *   1 "Pulse"    - an ordinary pulse attack whose profile grows with charge time. The control
 *                  sheet gives two rows, one per turn of charge, and $chargeProfile below IS that
 *                  sheet. No method in this class hard-codes a game number.
 *   2 "Scanning" - does NO damage, but the to-hit roll resolves normally. A hit on a unit that
 *                  carries any shield-type defensive system teaches the SCANNING FLEET one point
 *                  about that RACE's shields, and from the NEXT turn every ship on that team
 *                  shoots at shields that are one point weaker. See CpdScanRegistry.
 *
 * HOW THE ACCELERATOR HALF WORKS
 * loadingtime 1 / normalload 2: it may fire from one turn of charge, and holding a second turn
 * moves it to the heavier row. getChargeRow() is the single authority - getPulses(), rollPulses(),
 * getDamage() and the tooltip all read it, so a re-stat is a table edit. $this->maxpulses and
 * $this->useDie are ALSO kept in step (applyChargeProfile) because Weapon::fire reads
 * $this->maxpulses directly, twice, for ->shots and for the interception tally.
 * ⚠️ It does not begin the scenario fully charged - getStartLoading() seeds ONE turn, exactly as
 * MediumLightningArray does and for the same reason: 0 is below getLoadingTime(), so the weapon
 * would be unable to fire at all on turn 1 and would read "0/2" (user report, game 4329).
 *
 * HOW THE SCANNING HALF PERSISTS - AND THE FOUR TRAPS IT WALKS PAST
 *  1. The mode swap is $damageTypeArray, so a Scanning order resolves as a single 'Standard' shot
 *     rather than as a pulse volley. ⚠️ Firing::fireWeapons does NOT re-apply an order's firing
 *     mode before calling fire() - only prepareFiring does, and it does so for ALL orders before
 *     ANY resolve, leaving the weapon in whichever mode the LAST prepared order used. fire() below
 *     re-applies it, the same idiom AoE::fire uses.
 *  2. A scan is recorded in beforeDamage (the per-hit hook) into $pendingScans, and written to the
 *     database by generateIndividualNotes - which FireGamePhase::advance calls for every ship
 *     immediately after firing resolves. ⚠️ It branches on "do I have pending scans", NEVER on
 *     $gamedata->phase: advance() has already set the next phase by then (plan trap 3).
 *  3. notekey "CPDSCAN", notekey_human = the target's faction string. ⚠️ Both columns are
 *     varchar(40) and an overflow is a fatal that aborts the whole submission - hence substr().
 *  4. onIndividualNotesLoaded replays every note into the per-load static CpdScanRegistry, and
 *     counts ONLY notes whose turn is strictly LESS than the current one: the adaptation applies
 *     "starting in the next Adjust Ship Systems segment". The registry is reset per load in
 *     DBManager::getSystemDataForShips - one request loads gamedata more than once.
 *
 * "The same race" is the RAW $ship->faction string (plan D7). No faction families, no normalising.
 *
 * STATS: firing mode 1 is the control sheet (user, 2026-09-03) and is real. Grouping, range
 * penalty, fire control and intercept are identical on BOTH charge rows of that sheet, so they are
 * plain properties rather than table columns. If a later sheet makes the fire control or the range
 * penalty vary with charge they need the LightningArray's treatment - a DELTA on ->needed for fire
 * control, and a real calculateRangePenalty() override for range, never a delta for that one,
 * because the parent derives the no-lock and jammer modifiers from it. Still unconfirmed and
 * marked so in-file: $priority, the health/power defaults, the point cost and the icon.
 */
class ChromaticPulseDriver extends Pulse {

    public $name        = "ChromaticPulseDriver";
    public $displayName = "Chromatic Pulse Driver";
    //PLACEHOLDER icon - an existing accelerator pulsar, so nothing 404s. Drop a real
    //ChromaticPulseDriver.png into img/systemicons/ and change this one line.
    //⚠️ The filename is case-sensitive on live even though it is not on Windows.
    public $iconPath    = "ChromaticPulseDriver.png";

    public $animation      = "bolt";
    public $animationColor = array(200, 120, 255);        //chromatic violet
    //Scanning fires a visibly different, colder bolt - it is a sweep, not a shot.
    public $animationColorArray = array(
        1 => array(200, 120, 255),
        2 => array(120, 255, 220),
    );

    public $weaponClass = "Electromagnetic"; //all Walker weaponry is Electromagnetic
    public $factionAge  = 3;                 //Ancient

    /* Mode ids are referenced from the client (pulse.js) too - keep the two in step. */
    const MODE_PULSE    = 1;
    const MODE_SCANNING = 2;
    public $firingModes = array(1 => "Pulse", 2 => "Scanning");

    /* ⚠️ THE MODE SWAP THAT MATTERS. In Scanning mode the weapon must stop being a pulse weapon:
       Weapon::fire branches on $this->damageType == 'Pulse' to collapse the volley to one shot and
       to rewrite ->shots and ->intercepted with $this->maxpulses. 'Standard' gives one ordinary
       shot, which is what a scan is. changeFiringMode() applies this per order. */
    public $damageTypeArray = array(1 => "Pulse", 2 => "Standard");

    public $loadingtime = 1;                 //may fire from one turn of charge...
    public $normalload  = 2;                 //...but keeps charging to a heavier profile
    public $priority    = 5;                 //UNCONFIRMED - the inherited Pulse default

    /* Identical on both charge rows of the control sheet - read the class comment before moving
       any of these into $chargeProfile. */
    public $grouping     = 15;
    public $rangePenalty = 0.5;              // -1 per 2 hexes
    public $fireControl  = array(4, 4, 4);   // fighters, <=mediums, <=capitals
    public $intercept    = 1;

    /* THE CONTROL SHEET, keyed by turns charged. 'useDie' is the die rolled for the base number of
       pulses, 'maxpulses' the ceiling on them, 'damage' the (fixed) damage each pulse does.
       Row n must exist for every n from 1 to $normalload. */
    protected $chargeProfile = array(
        1 => array('useDie' => 3, 'maxpulses' => 4, 'damage' => 14),
        2 => array('useDie' => 5, 'maxpulses' => 8, 'damage' => 18),
    );

    /* target faction => scan points earned this turn, waiting to be written as IndividualNotes.
       Filled in beforeDamage, drained by generateIndividualNotes; never serialised. */
    protected $pendingScans = array();

    const NOTE_KEY = "CPDSCAN";              //notekey is varchar(40); this fits, a faction may not
    const SCAN_POINTS_PER_HIT = 1;           //one point of adaptation per scanning hit

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        if ($maxhealth == 0) $maxhealth = 15;  
        if ($powerReq  == 0) $powerReq  = 6;   
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $this->applyChargeProfile();            //blueprint values describe one turn of charge
    }

    /* -- The accelerator half ------------------------------------------------------------- */

    /* Turns of charge this weapon is resolving at, clamped into the rows the sheet describes.
       Never below 1: a weapon able to fire at all has at least the first row. */
    public function getChargeLevel(){
        $level = (int)$this->turnsloaded;
        $max   = max(array_keys($this->chargeProfile));
        if ($level > $this->normalload) $level = $this->normalload;
        if ($level > $max) $level = $max;
        if ($level < 1)    $level = 1;
        return $level;
    }

    protected function getChargeRow(){
        return $this->chargeProfile[$this->getChargeLevel()];
    }

    /* Keep the two PUBLIC pulse properties in step with the charge row. Everything in this class
       reads getChargeRow() directly, but Weapon::fire reads $this->maxpulses itself - twice - and
       Pulse::setSystemDataWindow prints $this->useDie, so both have to be real. */
    protected function applyChargeProfile(){
        $row = $this->getChargeRow();
        $this->maxpulses = $row['maxpulses'];
        $this->useDie    = $row['useDie'];
    }

    /* "Does not begin the scenario fully charged" - it begins with ONE turn of charge, i.e. the
       first row. ⚠️ NOT 0: turnsloaded 0 is below getLoadingTime(), so it could not fire at all on
       turn 1. Everything else is copied from the parent so overloading and firing-mode seeding
       keep behaving identically. */
    public function getStartLoading(){
        $overloadTurns = $this->overloadturns;
        if ($overloadTurns === 0 && $this->overloadable) $overloadTurns = 1;

        return new WeaponLoading(
            1,                       //<- one turn charged (1/2), rather than getNormalLoad()'s full 2
            $this->overloadshots,
            0,
            $overloadTurns,
            $this->getLoadingTime(),
            $this->firingMode
        );
    }

    protected function getPulses($turn){
        $row = $this->getChargeRow();
        return Dice::d($row['useDie']) + $this->fixedBonusPulses;
    }

    /* Same shape as Pulse::rollPulses, but clamped against the ROW's ceiling rather than against
       whatever $this->maxpulses happens to be holding - so the cap is right even if something
       resolves a shot before applyChargeProfile() has run. */
    public function rollPulses($turn, $needed, $rolled){
        $row     = $this->getChargeRow();
        $pulses  = $this->getPulses($turn);
        $pulses += $this->getExtraPulses($needed, $rolled);
        return min($pulses, $row['maxpulses']);
    }

    /* -- Modes ----------------------------------------------------------------------------- */

    /* An order's mode when there is one, the weapon's own when there is not. setMinDamage() and
       setMaxDamage() call getDamage(null) and are driven by the per-mode loop in
       Weapon::setSystemDataWindow, which is why the fallback exists. */
    protected function isScanning($fireOrder = null){
        if ($fireOrder !== null) return ((int)$fireOrder->firingMode === self::MODE_SCANNING);
        return ((int)$this->firingMode === self::MODE_SCANNING);
    }

    public function getDamage($fireOrder){
        if ($this->isScanning($fireOrder)) return 0;  //a scan carries no energy at all
        $row = $this->getChargeRow();
        return $row['damage'];
    }

    /* Fixed damage per pulse, so both ends are the same number - as on every other Pulse weapon. */
    public function setMinDamage(){ $this->minDamage = $this->getDamage(null); }
    public function setMaxDamage(){ $this->maxDamage = $this->getDamage(null); }

    /* ⚠️ changeFiringMode HERE, not only in calculateHitBase. Firing::fireWeapons re-sorts every
       order in the game by priority and calls fire() straight off that list; only prepareFiring
       applies an order's mode, and it does so for ALL orders before ANY of them resolve. Without
       this line a driver that prepared a Pulse order after a Scanning one would resolve the scan
       as a pulse volley.
       The ->shots clamp is the server-authoritative half of "a scan is one shot": in Standard
       damage mode Weapon::fire loops ->shots times, and that number arrives from the client. */
    public function fire($gamedata, $fireOrder){
        $this->changeFiringMode($fireOrder->firingMode);
        $this->applyChargeProfile();
        if ($this->isScanning($fireOrder)) $fireOrder->shots = 1;
        parent::fire($gamedata, $fireOrder);
    }

    /* -- Scanning -------------------------------------------------------------------------- */

    /* The per-hit hook. A Scanning hit does no damage at all - it never calls the parent, so
       nothing rolls a hit location, nothing touches armour and no DamageEntry is created. */
    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
        if (!$this->isScanning($fireOrder)) {
            parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
            return;
        }
        $this->recordScanHit($target, $fireOrder);
    }

    /* One point of adaptation against the TARGET HULL's faction string, if the target actually
       carries shields to learn about. ⚠️ Read faction off the hull, never off its ships directory
       name - the two do not always match, which is why ShipLoader::getFactionDirMap() exists. */
    protected function recordScanHit($target, $fireOrder){
        if ($target === null) return;

        if (!self::unitHasShieldSystem($target)) {
            $fireOrder->pubnotes .= " Scanning hit: no shielding to analyse.";
            return;
        }

        $faction = $target->faction;
        if ($faction === null || $faction === '') return;

        if (!isset($this->pendingScans[$faction])) $this->pendingScans[$faction] = 0;
        $this->pendingScans[$faction] += self::SCAN_POINTS_PER_HIT;

        $fireOrder->pubnotes .= "<br>Scanning hit: " . $faction . " shielding analysed (-"
                             . self::SCAN_POINTS_PER_HIT . " shield effectiveness from next turn).";
    }

    /* Does this unit carry anything the scan can learn from? "Any DefensiveSystem whose
       getDefensiveType() is a shield type" - which covers Shield, EM Shield, Gravitic Shield,
       Flare Shielding, Shading Field and the four Weapon-subclass shields (Shield Projector,
       Flare Generator, Plasma Web, Water Caster) without naming any of them.
       A FighterFlight keeps its defensive systems on the individual fighters, exactly as
       FighterFlight::getHitChanceMod has to allow for - hence the second loop. */
    public static function unitHasShieldSystem($target){
        if ($target === null || !isset($target->systems)) return false;

        foreach ($target->systems as $system){
            if (self::isShieldSystem($system)) return true;
            //FighterFlight: $systems holds Fighters, each with its own $systems
            if (isset($system->systems) && is_array($system->systems)){
                foreach ($system->systems as $subsystem){
                    if (self::isShieldSystem($subsystem)) return true;
                }
            }
        }
        return false;
    }

    /* Delegated so the marker, the scan and the arithmetic share ONE definition of "shield-ish"
       - see CpdScanRegistry::isShieldSystem(). */
    private static function isShieldSystem($system){
        return CpdScanRegistry::isShieldSystem($system);
    }

    /* Write this turn's scans to the database. FireGamePhase::advance calls this for every ship
       immediately after firing resolves, then saves in a second pass.
       ⚠️ Gated on "are there pending scans", NEVER on $gameData->phase: advance() has already set
       the next phase before its ship loop (plan trap 3). Everywhere else this method is called -
       Movement, generateAdditionalNotes - the list is empty and this is a no-op.
       ⚠️ notekey and notekey_human are varchar(40) and an overflow is a fatal that aborts the whole
       player submission (plan trap 4). Truncating collides with nothing that matters: the same
       truncated form is what onIndividualNotesLoaded uses as the registry key. */
    public function generateIndividualNotes($gameData, $dbManager){
        if (empty($this->pendingScans)) return;

        $ship = $this->getUnit();
        if ($ship === null) { $this->pendingScans = array(); return; }

        foreach ($this->pendingScans as $faction => $points){
            if ($points < 1) continue;
            $this->individualNotes[] = new IndividualNote(
                -1,
                $gameData->id,
                $gameData->turn,
                $gameData->phase,
                $ship->id,
                $this->id,
                self::NOTE_KEY,
                substr($faction, 0, 40),
                (int)$points
            );
        }
        $this->pendingScans = array();
    }

    /* Replay this driver's scan history into the per-load registry. Called once per ship per
       gamedata load by DBManager::getSystemDataForShips, which resets the registry immediately
       before the sweep - see CpdScanRegistry's class comment for why that reset is mandatory.
       ⚠️ turn STRICTLY LESS THAN the current one: a scan landing this turn takes effect in the next
       Adjust Ship Systems segment, not in the firing that produced it.
       A destroyed scanner keeps its notes and its team keeps the knowledge - the adaptation was
       learned by the fleet, not stored in the hull. */
    public function onIndividualNotesLoaded($gamedata){
        //Cheapest possible exit for a driver that has never scanned - which is most of them, and all
        //of them on turn 1. Before getUnit(), deliberately.
        if (empty($this->individualNotes)) return;

        $ship = $this->getUnit();
        $team = ($ship !== null) ? $ship->team : null;

        foreach ($this->individualNotes as $currNote){
            if ($currNote->notekey !== self::NOTE_KEY) continue;
            if ($currNote->turn >= $gamedata->turn) continue;   //takes effect NEXT turn
            CpdScanRegistry::record($team, $currNote->notekey_human, (int)$currNote->notevalue);
        }

        $this->individualNotes = array(); //reacted to; they serve no further purpose in memory
    }

    /* -- Display --------------------------------------------------------------------------- */

    public function setSystemDataWindow($turn){
        $this->applyChargeProfile();
        /* A scan is ONE shot. Set before the parent call so the per-mode loop inside
           Weapon::setSystemDataWindow picks it up, and republished in stripForJson so the live
           client sees it rather than the blueprint's. Without it the client would build a Scanning
           order carrying Pulse mode's shot count - harmless, because fire() clamps it, but it would
           read wrong on the way there. In Pulse mode the number is cosmetic anyway: Weapon::fire
           rewrites ->shots with $maxpulses, and the "Number of shots" tooltip line is suppressed
           for every Pulse instance. */
        $this->defaultShotsArray = array(self::MODE_PULSE    => $this->maxpulses,
                                         self::MODE_SCANNING => 1);
        parent::setSystemDataWindow($turn); //Pulse writes the "Pulse mode: Dx, +1/y%, max. z pulses" line

        $this->data["Special"] = "<br>Accelerator weapon. Begins the scenario with one turn of charge.";
        foreach ($this->chargeProfile as $level => $row){
            $this->data["Special"] .= "<br> - " . $level . " turn" . ($level > 1 ? "s" : "")
                                   . " charged: D" . $row['useDie'] . " pulses, max. "
                                   . $row['maxpulses'] . ", " . $row['damage'] . " damage each";
        }
        $this->data["Special"] .= "<br>SCANNING MODE: does no damage, but hitting on a shielded"
                               . " target analyses that race's shielding. From the NEXT turn every"
                               . " ship in the fleet treats that factions's shields as 1 point weaker"
                               . " per scan, to a minimum of 0.";
    }

    /* The pulse count, the damage and the tooltip all move with charge time, so they have to be
       re-published per instance or the client keeps showing the blueprint values - the same reason
       LaserAccelerator overrides this. */
    public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data           = $this->data;
        $strippedSystem->minDamage      = $this->minDamage;
        $strippedSystem->minDamageArray = $this->minDamageArray;
        $strippedSystem->maxDamage      = $this->maxDamage;
        $strippedSystem->maxDamageArray = $this->maxDamageArray;
        $strippedSystem->defaultShotsArray = $this->defaultShotsArray; //see setSystemDataWindow
        return $strippedSystem;
    }

} //endof class ChromaticPulseDriver



?>
