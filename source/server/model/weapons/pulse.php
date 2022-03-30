<?php

class Pulse extends Weapon{        
        public $grouping = 20;
        public $maxpulses = 6;
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
	    /*
        public $trailLength = 13;
        public $animationWidth = 4;
        public $projectilespeed = 16;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
	*/
        public $guns = 3; //always 3, completely separate (not Pulse!) shots
        public $maxpulses = 3;
        public $grouping = 0;
        public $loadingtime = 2;
        public $normalload = 2;
	    
        public $priority = 5; //medium standard shots - if called shots are used, they should be prioritized independently of base weapon priority
        
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
    }



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



?>
