<?php
/*custom weapons - from StarWars universe (to keep them separate)
*/





/*
	StarWars Ray Shield: does not affect profile
	protects vs all weapoon classes except Matter, Ballistic and SW Ion
	doubly effective vs Raking weapons (to simulate longer burst)
*/
class SWRayShield extends Shield implements DefensiveSystem{
    public $name = "swrayshield";
    public $displayName = "Ray Shield";
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
	//Ballistic, Matter, SWIon - passes through!
	if($weapon->weaponClass == 'Ballistic' || $weapon->weaponClass == 'Matter' || $weapon->weaponClass == 'SWIon' ) $output = 0;
        $output += $this->outputMod; //outputMod itself is negative!
	if($weapon->damageType == 'Raking') $output = 2*$output;//Raking - double effect!
	$output=max(0,$output); //no less than 0!
        return $output;
    }
	
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	//$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
	$this->data["Basic Strength"] = $this->baseOutput;      	    
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
	$this->data["Special"] .= "Does not decrease profile."; 
	$this->data["Special"] .= "<br>Cannot be flown under."; 
	$this->data["Special"] .= "<br>Does not protect from Ballistic, Matter and StarWars Ion damage."; 
	$this->data["Special"] .= "<br>Doubly effective vs Raking weapons."; 
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
	
} //endof class SWRayShield



class SWFtrBallisticLauncher extends FighterMissileRack //this is generic launcher, which needs separate ammo
{
    public $loadingtime = 1;
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    public $priority = 4;
    public $firingModes = array( 1 => "Spread");  
	
    
	public $damageType = 'Pulse'; 
    	public $weaponClass = "Ballistic"; 
	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table
	
	protected $useDie = 3; //die used for base number of hits
	
    
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	    $this->data["Special"] = 'Spread mode: -1..1 +1/'. $this->grouping."%, max. ".$this->maxpulses." missiles";
		$this->data["Special"] .= '<br>Minimum of 1 missile.';
		$this->data["Special"] .= '<br>Cannot penetrate to PRIMARY when hitting outer section.';
    }
	
    function __construct($maxAmount, $startArc, $endArc, $nrOfShots){
	//number of barrels influences FC, too! +1/2 vs fighters, +1/3 vs ships
	if($this->fireControl[0]!==null) $this->fireControl[0] += floor($nrOfShots/2);
	if($this->fireControl[1]!==null) $this->fireControl[1] += floor($nrOfShots/3);
	if($this->fireControl[2]!==null) $this->fireControl[2] += floor($nrOfShots/3);
	    
        parent::__construct($maxAmount, $startArc, $endArc);
	    
        $Torp = new SWFtrProtonTorpedo($startArc, $endArc,  $nrOfShots, $this->fireControl);
        $this->missileArray = array( 1 => $Torp );
        $this->maxAmount = $maxAmount;
	    //Pulse mode data
	$this->maxpulses = $nrOfShots;
	$this->defaultShots = $nrOfShots;
	//$this->intercept = $nrOfShots; //each weapon needs to calculate this by itself!
	$this->grouping = 35-5*$nrOfShots; //more launchers means better grouping! 
	$this->grouping = max(10,$this->grouping); //but no better than +1 per 10!
    }	
	
	
	//needed as this is not based on Pulse class
        protected function getPulses($turn)
        {
            return Dice::d($this->useDie);
        }
	
	//needed as this is not based on Pulse class
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	
    
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn); //$this->useDie usually
		$pulses -= 2;
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses); //no more than maxpulses
		$pulses=max($pulses,1); //no less than 1
		return $pulses;
	}
	
	
} /*end of SWFtrBallisticLauncher*/



class SWFtrMissile extends MissileFB //generic class; this is AMMO for SWFtrProtonTorpedoLauncher
{
    public $priority = 4;
	public $damageType = 'Pulse'; 
    	public $weaponClass = "Ballistic"; 
	
	
    function __construct($startArc, $endArc, $noOfLaunchers, $fireControl = null){
	//number of linked launchers affects price!
	$this->cost = $this->cost * $noOfLaunchers;
        parent::__construct($startArc, $endArc, $fireControl);
    }
	
    public function getDamage($fireOrder=null){        return $this->damage;   }
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }              
}//end of SWFtrMissile




class SWDirectWeapon extends Pulse{
    /*StarWars weapon - extension of Pulse mode!*/
    public $shots = 1;
    public $firingModes = array( 1 => "Burst");  
	
	//for Pulse mode
	//public $grouping = 25;
	//public $maxpulses = 1;	
	protected $useDie = 3; //die used for base number of hits
 
    public $damageType = "Pulse"; //and this should remain!
    public $weaponClass = "Particle"; //and may be easily overridden
   
	//animation for fighter laser - bigger guns need to change size and speed attributes :)
	public $animation = "beam";
        public $animationColor = array(225, 0, 0); //I aim ar bright red here... Blue for Ion, green for Blasters
	public $trailColor = array(225, 0, 0);
        public $animationExplosionScale = 0.1;
        public $projectilespeed = 11;
	public $animationWidth = 3;
	public $trailLength = 8;
	
	protected $damagebonus = 0;
	protected $damagebonusArray = array();
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $nrOfShots){
		$this->maxpulses = $nrOfShots;
		$this->defaultShots = $nrOfShots;
		//$this->intercept = $nrOfShots; //each weapon needs to calculate this by itself!
		$this->grouping = 35-5*$nrOfShots; //more guns means better grouping!
		$this->grouping = max(10,$this->grouping); //but no better than +1 per 10!
		
		//maxhealth and powerReq affected by number of barrels - received is for single -gun mount!
		//size: +25% for each additional gun (was 40% originally, but triple/quad mounts were a bit too large then)
		//power: +65% for each additional gun 
		$maxhealth += $maxhealth*0.25*($nrOfShots-1);
		$powerReq += $powerReq*0.65*($nrOfShots-1);
		$maxhealth = ceil($maxhealth);
		$powerReq = ceil($powerReq);
		
		
		//number of barrels influences FC, too! +1/2 vs fighters, +1/3 vs ships
		if($this->fireControl[0]!==null) $this->fireControl[0] += floor($nrOfShots/2);
		if($this->fireControl[1]!==null) $this->fireControl[1] += floor($nrOfShots/3);
		if($this->fireControl[2]!==null) $this->fireControl[2] += floor($nrOfShots/3);
				
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}    
	
	
        public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Burst mode: -1..1 +1/'. $this->grouping."%, max. ".$this->maxpulses." pulses";
			$this->data["Special"] .= '<br>Minimum of 1 pulse.';
			$this->data["Special"] .= '<br>Alternate firing mode: Salvo: single shot with increased damage but lowered FC (all weapons in battery fire together instead of sequentially).';
        }
	
    
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn); //$this->useDie usually
		$pulses -= 2;
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses); //no more than maxpulses
		$pulses=max($pulses,1); //no less than 1
		return $pulses;
	}
	
	/*derives concentrate salvo mode from data of weapon; damage functions must be prepared to consider it!
	  to be called in create function where needed
	  AFTER calling parent::__construct! (else variables like maxpulses may be not yet filled correctly
	*/
	public function addSalvoMode(){
		
		if($this->defaultShots <2) return; //no point
		$this->firingModes[2] = 'Salvo';
		$new = ceil($this->animationExplosionScale*1.25);
		$this->animationExplosionScaleArray = array(1=>$this->animationExplosionScale, 2=>$new);
		$new = ceil($this->animationWidth*1.25);
		$this->animationWidthArray = array(1=>$this->animationWidth, 2=>$new);
		$new = ceil($this->trailLength*1.25);
		$this->trailLengthArray = array(1=>$this->trailLength, 2=>$new);
		$this->maxpulsesArray = array(1=>$this->maxpulses, 2=>1);
		$this->defaultShotsArray = array(1=>$this->defaultShots, 2=>1);
		$new = min(10,$this->priority+1);//system doesn't accept more than 10 for now
		$this->priorityArray = array(1=>$this->priority, 2=>$new);
		$fc1 = $this->fireControl;
		$fc2 = array($fc1[0]-4, $fc1[1]-2, $fc1[2]-1); //FC of salvo mode: worse (much worse vs fighters)
		$this->fireControlArray = array(1=>$fc1, 2=>$fc2);
		//damage bonus: at least +1 per extra gun (+2 for second gun), but otherwise percentage based
		$damagebonusMin = 2;
		$damagebonusPerc = 20; //percentage: +20% for first gun, +10% for each one after that; counted from average damage
		if($this->defaultShots > 2) {
			$damagebonusMin += $this->defaultShots - 2;//+1 for each barrel after that
			$damagebonusPerc += 10*($this->defaultShots-2);//+15% for each barrel after that
		}
		$avgDmg = ($this->minDamage+$this->maxDamage) /2;
		$damagebonusEffect = $this->damagebonus + max($damagebonusMin,ceil($avgDmg*$damagebonusPerc/100));
		$this->damagebonusArray = array(1=>$this->damagebonus, 2=>$damagebonusEffect);
		
		foreach($this->firingModes as $i=>$modeName){ //recalculating min and max damage - taking into account new firing mode!	    
			$this->changeFiringMode($i);
			$this->setMinDamage(); $this->minDamageArray[$i] = $this->minDamage;
			$this->setMaxDamage(); $this->maxDamageArray[$i] = $this->maxDamage;	
			if (!isset($this->priorityAFArray[$i])){ //if AF priority for this mode is not set - do set it!
				$this->priorityAF = 0; //so setPriorityAF works correctly
				$this->setPriorityAF(); 
				$this->priorityAFArray[$i] = $this->priorityAF;
			}
		}
		$this->changeFiringMode(1); //reset mode to basic
		
	}
	
	//these will ned to be overridden for each particular weapon! (taking into accunt salvo mode if eligible)
	//public function getDamage($fireOrder){        return ??;   }
	//public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
	//public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }
	
	public function changeFiringMode($newMode){ //do modify elements not present in usual weapons
		parent::changeFiringMode($newMode);
		if(isset($this->damagebonusArray[$newMode])) $this->damagebonus = $this->damagebonusArray[$newMode];
	}

} //end of class SWDirectWeapon



class SWBallisticWeapon extends Torpedo{
    /*StarWars weapon - extension of Pulse mode!*/
    public $firingModes = array( 1 => "Spread");  
    public $priority = 6;
	
	public $iconPath = "starwars/photonTorpedo.png"; //to be changed!

    protected $useDie = 3; //die used for base number of hits
 
    public $damageType = "Pulse"; //and this should remain!
    public $weaponClass = "Ballistic"; //and may be easily overridden
	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table
   
	//animation for capital concussion missile - others need to change things
        public $trailColor = array(141, 240, 255);
        public $animation = "trail";
        public $animationColor = array(90, 170, 190);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 10;
        public $animationWidth = 5;
        public $trailLength = 12;	
		
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $nrOfShots){
		$this->maxpulses = $nrOfShots;
		$this->defaultShots = $nrOfShots;
		//$this->intercept = $nrOfShots; //each weapon needs to calculate this by itself!
		$this->grouping = 35-5*$nrOfShots; //more launchers means better grouping! let's give them better grouping than direct fire...
		$this->grouping = max(10,$this->grouping); //but no better than +1 per 8!
		
		//maxhealth and powerReq affected by number of barrels - received is for single -gun mount!
		//let size advance quicker than for energy weapons (on account of magazines), and power lower
		//size: +40% for each additional gun (was 55% originally, but this was reduced when direct fire weapons size was reduced)
		//power: +35% for each additional gun 
		$maxhealth += $maxhealth*0.40*($nrOfShots-1);
		$powerReq += $powerReq*0.35*($nrOfShots-1);
		$maxhealth = ceil($maxhealth);
		$powerReq = ceil($powerReq);
		//number of barrels influences FC, too! +1/2 vs fighters, +1/3 vs ships
		if($this->fireControl[0]!==null) $this->fireControl[0] += floor($nrOfShots/2);
		if($this->fireControl[1]!==null) $this->fireControl[1] += floor($nrOfShots/3);
		if($this->fireControl[2]!==null) $this->fireControl[2] += floor($nrOfShots/3);
				
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}    
	
	
        public function setSystemDataWindow($turn){
	    $this->data["Special"] = 'Spread mode: -1..1 +1/'. $this->grouping."%, max. ".$this->maxpulses." missiles";
		$this->data["Special"] .= '<br>Minimum of 1 missile.';
		$this->data["Special"] .= '<br>Cannot penetrate to PRIMARY when hitting outer section.';
            parent::setSystemDataWindow($turn);
        }
	
	
	//needed as this is not based on Pulse class
        protected function getPulses($turn)
        {
            return Dice::d($this->useDie);
        }
	
	//needed as this is not based on Pulse class
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	
    
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn); //$this->useDie usually
		$pulses -= 2;
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses); //no more than maxpulses
		$pulses=max($pulses,1); //no less than 1
		return $pulses;
	}

	
	//these will ned to be overridden for each particular weapon!
	//public function getDamage($fireOrder){        return ??;   }
	//public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
	//public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

} //end of class SWBallisticWeapon




/*base class for StarWars Ion weapons*/
/*compared to SW Lasers: a bit better range, but poor FC and damage (and possible RoF as well)*/
class SWIon extends SWDirectWeapon{
    public $name = "swion";
    public $priority = 10; //Ions usually fire last, to take advantage of induced criticals
 
    public $weaponClass = "SWIon"; //weapon class
	  
    //public $systemKiller = true; //let's not go overhead - do NOT use $systemKiller...

    //animation for fighter gun - bigger guns need to change size and speed attributes :)
    public $animation = "beam";
    public $projectilespeed = 9;
    public $animationExplosionScale = 0.15;
    public $animationWidth = 3;
    public $trailLength = 8;
    public $animationColor =  array( 80, 150, 250);
    public $trailColor = array( 80, 150, 250);
	

	
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
      $this->data["Special"] .= "Damage may cause power shortages.";      
      $this->data["Special"] .= "<br>Increased chance of critical on systems damaged."; 
    }
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //make vulnerable to next critical
	if ($system->advancedArmor) return;
      $dmg = $damage - $armour;
      if($dmg<=0) return; //no damage was actually done
      SWIonHandler::addDamage($ship, $system, $dmg);//possibly cause power shortage
      if($system->isDestroyed()) return; //destroyed system - vulnerability to critical is irrelevant
      if($system instanceof Structure) return; //structure does not suffer critical hits anyway

      while($dmg>0){
	      $dmg--;
	      $system->critRollMod++;
      }
    }

} //end of class SWIon



/*static class to handle accumulating Ion damage*/
class SWIonHandler{
	private static $accumulatedIonDmg = array();
	//private static $power = 1.4; //effect magnitude from hit: damage ^power
	private static $free = 3; //this much damage doesn't cause anything
	private static $threshold = 7; //this much damage (after $free) causes power shortage
	private static $turn = 0; //turn for which data is held

	
	public static function addDamage($targetUnit, $targetSystem, $dmgInflicted){
		if($dmgInflicted<1) return;//no point if no damage was actually done
		if($targetUnit instanceof FighterFlight) return;//no effect on fighters
		if ($targetUnit->isDestroyed()) return; //no point in doing anything
		$baseDmg = $dmgInflicted;//$baseDmg = $dmgInflicted+1; //boost light damage a bit (..or not)
		if(($targetSystem->displayName != 'Structure') && (!($targetSystem instanceof Reactor)) ){ //half damage counts 
			$baseDmg = ceil($dmgInflicted/2);
		}
		//effect is stronger than raw damage inflicted, and bigger hits do more damage: //after all, skip that - paper-frienfdliness
		$effect = $baseDmg;//$effect = pow($baseDmg,SWIonHandler::$power);
		$targetID = $targetUnit->id;
		$currentTurn = TacGamedata::$currentTurn;
		if(SWIonHandler::$turn != $currentTurn){
			SWIonHandler::$accumulatedIonDmg = array();//clear everything, it's not current data
			SWIonHandler::$turn = $currentTurn;
		}
		if(!isset(SWIonHandler::$accumulatedIonDmg[$targetID]))SWIonHandler::$accumulatedIonDmg[$targetID]=0;
		SWIonHandler::$accumulatedIonDmg[$targetID]+=$effect;
		$threshold = SWIonHandler::$free+SWIonHandler::$threshold;
		while(SWIonHandler::$accumulatedIonDmg[$targetID]>$threshold){
			SWIonHandler::$accumulatedIonDmg[$targetID] -= SWIonHandler::$threshold;
			//cause power shortage...
			$reactor = $targetUnit->getSystemByName("Reactor");
			if($reactor instanceof Reactor){ //just making sure!
				$crit = new OutputReduced1(-1, $targetID, $reactor->id, "OutputReduced1", $currentTurn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
			}
		}
	}
}//endof class SWIonHandlerHandler





class SWFighterLaser extends SWDirectWeapon{
    /*StarWars fighter weapon - a Particle weapon!*/
    public $name = "SWFighterLaser";
    public $displayName = "Fighter Laser";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	protected $damagebonus = 0;

   
    
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->intercept = $nrOfShots;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/swFighter".$nr.".png";
		
		if($damagebonus > 2) $this->priority++;
		if($damagebonus > 4) $this->priority++;		
		if($damagebonus > 6) $this->priority = 8;
		
		
		parent::__construct(0, 1, 0, $startArc, $endArc, $nrOfShots);
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

} //end of class SWFighterLaser




class SWFighterIon extends SWIon{
    /*StarWars fighter Ion weapon*/
    public $name = "SWFighterIon";
    public $displayName = "Fighter Ion Cannon";	  
	  

    public $loadingtime = 1;
    public $rangePenalty = 1.5; //poor FC, but good range compared to Lasers! Perhaps lower rate of fire, too - but that would not be noticeable on fighter weapons (maybe in damage)
    public $fireControl = array(-3, -1, -1); // fighters, <mediums, <capitals

    public $exclusive = false; //can be always overridden in particular fighter!
    public $isLinked = true; //indicates that this is linked weapon
    public $priority = 8;	
    protected $damagebonus = 0;     	
	
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->intercept = 0;
		
		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonFtr".$nr.".png";
		
		
		if($damagebonus > 2) $this->priority++;
		if($damagebonus > 4) $this->priority++;	

		parent::__construct(0, 1, 0, $startArc, $endArc, $nrOfShots);
		$this->addSalvoMode();
	}    

			  
    
    public function getDamage($fireOrder){        return Dice::d(4)+$this->damagebonus;   }
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
    public function setMaxDamage(){     $this->maxDamage = 4+$this->damagebonus ;      }
	
} //end of class SWFighterIon
    



class SWFtrProtonTorpedoLauncher extends SWFtrBallisticLauncher //this is launcher, which needs separate ammo
{
	//proton torpedo launcher for fighters
    public $name = "SWFtrProtonTorpedo";
    public $missileClass = "Torpedo";
    public $displayName = "Fighter Proton Torpedo";

    public $loadingtime = 1;
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    public $priority = 4;
    public $fireControl = array(-4, -1, 0); // fighters, <mediums, <capitals 
	
    function __construct($maxAmount, $startArc, $endArc, $noOfShots){
	//appropriate icon (number of barrels)...
	$nr = min(4, $noOfShots); //images are not unlimited
	$this->iconPath = "starwars/mjsLightProton".$nr.".png";
	    
        parent::__construct($maxAmount, $startArc, $endArc, $noOfShots);
        $Torp = new SWFtrProtonTorpedo($startArc, $endArc, $noOfShots, $this->fireControl);
        $this->missileArray = array( 1 => $Torp );
        $this->maxAmount = $maxAmount;
    }
	
} //end of SWFtrProtonTorpedoLauncher




class SWFtrProtonTorpedo extends SWFtrMissile //this is AMMO for SWFtrProtonTorpedoLauncher
{
    public $name = "SWFtrProtonTorpedo";
    public $missileClass = "FtrTorpedo";
    public $displayName = "Fighter Proton Torpedo";
    public $cost = 8;
    public $damage = 11;
    public $amount = 0;
    public $range = 8;
    public $distanceRange = 16;
    public $hitChanceMod = 0;
    public $priority = 2;
	public $damageType = 'Pulse'; 
    	public $weaponClass = "Ballistic"; 
	
    function __construct($startArc, $endArc, $noOfShots, $fireControl = null){
        parent::__construct($startArc, $endArc, $noOfShots, $fireControl);
    }
	
    public function getDamage($fireOrder=null){        return $this->damage;   }
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }              
}//end of SWFtrProtonTorpedo



class SWFtrConcMissileLauncher extends SWFtrBallisticLauncher //this is launcher, which needs separate ammo
{
    public $name = "SWFtrConcMissileLauncher";
    public $missileClass = "FtrMissile";
    public $displayName = "Fighter Concussion Missile";
    public $loadingtime = 1;
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    public $priority = 4;
    public $fireControl = array(2, 1, 0); // fighters, <mediums, <capitals 
	
    function __construct($maxAmount, $startArc, $endArc, $noOfShots){
	//appropriate icon (number of barrels)...
	$nr = min(4, $noOfShots); //images are not unlimited
	$this->iconPath = "starwars/mjsLightConcussion".$nr.".png";
	    
        parent::__construct($maxAmount, $startArc, $endArc, $noOfShots);
        $Torp = new SWFtrConcMissile($startArc, $endArc, $noOfShots, $this->fireControl);
        $this->missileArray = array( 1 => $Torp );
        $this->maxAmount = $maxAmount;
    }
	
} //end of SWFtrConcMissileLauncher
class SWFtrConcMissile extends SWFtrMissile //this is AMMO for SWFtrProtonTorpedoLauncher
{
    public $name = "SWFtrConcMissile";
    public $missileClass = "FtrMissile";
    public $displayName = "Fighter Concussion Missile";
    public $cost = 2;
    public $damage = 6;
    public $amount = 0;
    public $range = 6;
    public $distanceRange = 18;
    public $hitChanceMod = 0;
    public $priority = 2;
	public $damageType = 'Pulse'; 
    	public $weaponClass = "Ballistic"; 
	
    function __construct($startArc, $endArc, $noOfShots, $fireControl = null){
        parent::__construct($startArc, $endArc, $noOfShots, $fireControl);
    }
	
    public function getDamage($fireOrder=null){        return $this->damage;   }
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }              
}//end of SWFtrConcMissile






class SWLightLaser extends SWDirectWeapon{
    /*StarWars lightest ship-mounted laser - essentially fighter weapon on ship housing!
      optimized for antifighter action (and interception)
    */
    /*d6+2 damage as a single gun
      2 structure, 0.5 power usage as a single gun
    */
    public $name = "SWLightLaser";
    public $displayName = "Light Laser";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $fireControl = array(4, 2, 1); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = $nrOfShots;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/swFighter".$nr.".png";
		
		parent::__construct($armor, 2, 0.5, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(6)+2 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 3+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 8+$this->damagebonus ;      }

} //end of class SWLightLaser



class SWMediumLaser extends SWDirectWeapon{
    /*StarWars standard ship-mounted laser - an universal (if still very light) weapon
    */
    public $name = "SWMediumLaser";
    public $displayName = "Medium Laser";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 1.5; // 3 per 2 hexes
    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals
   
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
	public $animationWidth = 3;
	public $trailLength = 10;
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = floor($nrOfShots*0.9); //this gives distinctly worse interception than light laser

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsLaserMedium".$nr.".png";
		
		parent::__construct($armor, 3, 0.7, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(6)+4 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 5 +$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 10 +$this->damagebonus ;      }

} //end of class SWMediumLaser



class SWHeavyLaser extends SWDirectWeapon{
    /*StarWars heaviest ship-mounted laser - for heavier work turbolasers are preferred
    */
    public $name = "SWHeavyLaser";
    public $displayName = "Heavy Laser";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 1; 
    public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals
	
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 13;
	public $animationWidth = 4;
	public $trailLength = 12;
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = floor($nrOfShots*0.5); //this gives very poor interception, but still interception is possible

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsLaserHvy".$nr.".png";
		
		parent::__construct($armor, 4, 1.1, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(8)+4 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 5 +$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 12 +$this->damagebonus ;      }

} //end of class SWHeavyLaser




class SWMediumLaserAF extends SWDirectWeapon{
    /*StarWars anti-fighter medium laser - for Lancer mainly
    */
    public $name = "SWMediumLaserAF";
    public $displayName = "AF Medium Laser";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 1.5; // 3 per 2 hexes
    public $fireControl = array(6, 2, 0); // fighters, <mediums, <capitals
   
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
	public $animationWidth = 3;
	public $trailLength = 10;
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = floor($nrOfShots*1); //this gives distinctly worse interception than light laser

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsLaserMedium".$nr.".png";
		
		parent::__construct($armor, 3, 0.7, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(6)+4 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 5 +$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 10 +$this->damagebonus ;      }

} //end of class SWMediumLaser






class SWLightTLaser extends SWDirectWeapon{
    /*StarWars lightest turbolaser - roughly comparable to heavy laser, but more dependable vs ships and longer ranged than lasers (but doing less raw damage)
    */
    public $name = "SWLightTLaser";
    public $displayName = "Light Turbolaser";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 1;
    public $fireControl = array(-1, 2, 3); // fighters, <mediums, <capitals
	public $animationColor = array(245, 0, 0); //let's make it brighter than regular lasers :)
	public $trailColor = array(245, 0, 0);
   
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 15;
	public $animationWidth = 4;
	public $trailLength = 12;
	
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsTLaserLight".$nr.".png";
		
		parent::__construct($armor, 4, 1.1, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(6)+6 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 7+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 12+$this->damagebonus ;      }

} //end of class SWLightTLaser



class SWMediumTLaser extends SWDirectWeapon{
    /*StarWars standard turbolaser - relaively good-ranged antiship weapon
    */
    public $name = "SWMediumTLaser";
    public $displayName = "Medium Turbolaser";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 0.5;
    public $fireControl = array(-3, 1, 3); // fighters, <mediums, <capitals
	public $animationColor = array(245, 0, 0); //let's make it brighter than regular lasers :)
	public $trailColor = array(245, 0, 0);
   
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
	public $animationWidth = 5;
	public $trailLength = 14;
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsTLaserMedium".$nr.".png";
		
		parent::__construct($armor, 5, 2, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(8)+6 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 7+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 14+$this->damagebonus ;      }

} //end of class SWMediumTLaser




class SWHeavyTLaser extends SWDirectWeapon{
    /*StarWars heavy turbolaser - excellent if slow-firing andiship weapon
    */
    public $name = "SWHeavyTLaser";
    public $displayName = "Heavy Turbolaser";
	
    public $priority = 5;
    public $loadingtime = 3;
    public $rangePenalty = 0.33;
    public $fireControl = array(-6, 0, 3); // fighters, <mediums, <capitals
	public $animationColor = array(245, 0, 0); //let's make it brighter than regular lasers :)
	public $trailColor = array(245, 0, 0);
	
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 16;
	public $animationWidth = 6;
	public $trailLength = 18;
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsTLaserHvy".$nr.".png";
		
		parent::__construct($armor, 6, 4, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(6,2)+7 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 9+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 19+$this->damagebonus ;      }

} //end of class SWHeavyTLaser






class SWLightLaserE extends SWLightLaser{
    public $name = "SWLightLaserE";
    public $displayName = "Light Laser (Early)";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $fireControl = array(3, 2, 1); // fighters, <mediums, <capitals

	public function getDamage($fireOrder){ return  Dice::d(6)+1 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 2+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 7+$this->damagebonus ;      }
} //end of class SWLightLaserE
class SWMediumLaserE extends SWMediumLaser{
    /*StarWars standard ship-mounted laser - an universal (if still very light) weapon
    */
    public $name = "SWMediumLaserE";
    public $displayName = "Medium Laser (Early)";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 1.5; // 3 per 2 hexes
    public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
	
	public function getDamage($fireOrder){ return  Dice::d(8)+2 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 3 +$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 10 +$this->damagebonus ;      }
} //end of class SWMediumLaserE
class SWHeavyLaserE extends SWHeavyLaser{
    public $name = "SWHeavyLaserE";
    public $displayName = "Heavy Laser (Early)";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 1; 
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals

	public function getDamage($fireOrder){ return  Dice::d(8)+3 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 4 +$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 11 +$this->damagebonus ;      }
} //end of class SWHeavyLaserE
class SWLightTLaserE extends SWLightTLaser{
    public $name = "SWLightTLaserE";
    public $displayName = "Light Turbolaser (Early)";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 1;
    public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals
	
	public function getDamage($fireOrder){ return  Dice::d(6)+5 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 6+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 11+$this->damagebonus ;      }
} //end of class SWLightTLaserE
class SWMediumTLaserE extends SWMediumTLaser{
    public $name = "SWMediumTLaserE";
    public $displayName = "Medium Turbolaser (Early)";
	
    public $priority = 4;
    public $loadingtime = 3;
    public $rangePenalty = 0.6;
    public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals
    
	
	public function getDamage($fireOrder){ return  Dice::d(8)+6 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 7+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 14+$this->damagebonus ;      }
} //end of class SWMediumTLaser
class SWHeavyTLaserE extends SWHeavyTLaser{
    public $name = "SWHeavyTLaserE";
    public $displayName = "Heavy Turbolaser (Early)";
	
    public $priority = 5;
    public $loadingtime = 4;
    public $rangePenalty = 0.4;
    public $fireControl = array(-7, 0, 3); // fighters, <mediums, <capitals
   
	public function getDamage($fireOrder){ return  Dice::d(6,2)+7 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 9+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 19+$this->damagebonus ;      }
} //end of class SWHeavyTLaserE






																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											


class SWLightIon extends SWIon{
    /*StarWars lightest shipborne ion cannon - equivalent of appropriate TURBOLaser (less damage and FC and RoF, more range)
    */
    public $name = "SWLightIon";
    public $displayName = "Light Ion Cannon";
	
    public $priority = 10;
    public $loadingtime = 2;
    public $rangePenalty = 0.75; //-3/4 hexes
    public $fireControl = array(-4, 1, 2); // fighters, <mediums, <capitals
   
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
	public $animationWidth = 4;
	public $trailLength = 12;
	
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonLight".$nr.".png";
		
		parent::__construct($armor, 4, 1.1, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(4)+4 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 5+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 8+$this->damagebonus ;      }

} //end of class SWLightTLaser



class SWMediumIon extends SWIon{
    /*StarWars standard shipborne ion cannon - equivalent of appropriate TURBOLaser (less damage and FC and RoF, more range)
    */
    public $name = "SWMediumIon";
    public $displayName = "Medium Ion Cannon";
	
    public $priority = 10;
    public $loadingtime = 3;
    public $rangePenalty = 0.33; //-1/3 hexes
    public $fireControl = array(-6, 0, 2); // fighters, <mediums, <capitals
   
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 13;
	public $animationWidth = 5;
	public $trailLength = 14;
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonMedium".$nr.".png";
		
		parent::__construct($armor, 5, 2, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(5)+5 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 6+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 10+$this->damagebonus ;      }

} //end of class SWMediumTLaser




class SWHeavyIon extends SWIon{
    /*StarWars heaviest shipborne ion cannon - equivalent of appropriate TURBOLaser (less damage and FC and RoF, more range)
    */
    public $name = "SWHeavyIon";
    public $displayName = "Heavy Ion Cannon";
	
    public $priority = 10;
    public $loadingtime = 4;
    public $rangePenalty = 0.25; //-1/4 hexes!
    public $fireControl = array(null, -1, 1); // fighters, <mediums, <capitals
	
	
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 13;
	public $animationWidth = 6;
	public $trailLength = 18;
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsIonHvy".$nr.".png";
		
		parent::__construct($armor, 6, 4, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(4,2)+5 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 7+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 15+$this->damagebonus ;      }

} //end of class SWHeavyTLaser


/*shipborne concussion missile launcher - implemented as torpedo
   can use EW, no seeking head
*/
class SWCapitalConcussion extends SWBallisticWeapon{
        public $name = "SWCapitalConcussion";
        public $displayName = "Capital Concussion Missile";
        public $range = 16;
	public $distanceRange = 24;
        public $loadingtime = 3;
        
        public $fireControl = array(-6, -1, 0); // fighters, <mediums, <capitals 

	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsCapConcussion".$nr.".png";
		
		parent::__construct($armor, 6, 0.4, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
	}    
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;     }
    
}//endof class SWCapitalConcussion


/*shipborne proton torpedo launcher - implemented as torpedo
   can use EW, no seeking head
*/
class SWCapitalProton extends SWBallisticWeapon{
        public $name = "SWCapitalProton";
        public $displayName = "Capital Proton Torpedo";
        public $range = 20;
	public $distanceRange = 30;
        public $loadingtime = 3;
        
        public $fireControl = array(-8, 0, 1); // fighters, <mediums, <capitals 

        public $trailColor = array(171, 240, 255);
        public $animationColor = array(150, 190, 230);
        public $animationExplosionScale = 0.4;
        public $projectilespeed = 11;
        public $animationWidth = 6;
        public $trailLength = 12;
	
	function __construct($armor, $startArc, $endArc, $noOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		//appropriate icon (number of barrels)...
		$nr = min(4, $noOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsCapProton".$nr.".png";
		
		parent::__construct($armor, 7, 0.4, $startArc, $endArc, $noOfShots); //maxhealth and powerReq for single gun mount!
	}    
        
        public function getDamage($fireOrder){        return 18;   }
        public function setMinDamage(){     $this->minDamage = 18;      }
        public function setMaxDamage(){     $this->maxDamage = 18 ;     }
    
}//endof class SWCapitalProton


/*shipborne concussion missile launcher (fighter-sized) - implemented as torpedo
   can use EW, no seeking head
*/
class SWAntifighterConcussion extends SWBallisticWeapon{
        public $name = "SWAntifighterConcussion";
        public $displayName = "Antifighter Missile";
        public $range = 9; //better than fighter-launched version - assume shipborne sensors can hand-off to the missile at greater range
	public $distanceRange = 18;
        public $loadingtime = 3;
	public $priority = 3;
	
	//color etc from base ballistic class
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $trailLength = 7;	
        
        public $fireControl = array(2, 1, 0); // fighters, <mediums, <capitals 

	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		//appropriate icon (number of barrels)...
		$nr = min(4, $nrOfShots); //images are not unlimited
		$this->iconPath = "starwars/mjsLightConcussion".$nr.".png";
		
		parent::__construct($armor, 3, 0.1, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
	}    
        
        public function getDamage($fireOrder){        return 6;   }
        public function setMinDamage(){     $this->minDamage = 6;      }
        public function setMaxDamage(){     $this->maxDamage = 6 ;     }
    
}//endof class SWCapitalConcussion



class SWTractorBeam extends SWDirectWeapon{
    /*StarWars Tractor Beam 
    */
    /*weapon that does no damage, but limits targets' maneuvrability next turn ('target held by tractor beam')
    */
    public $name = "SWTractorBeam";
    public $displayName = "Tractor Beam";
	
    public $priority = 10; //let's fire last
    public $loadingtime = 2;
    public $rangePenalty = 1;
    public $intercept = 0;
    public $fireControl = array(null, 2, 4); // can't fire at fighters, incompatible with crit behavior!
   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(55, 55, 55);
        public $animationColor2 = array(100, 100, 100);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 15;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
      $this->data["Special"] .= "Does no damage, but holds target next turn";      
      $this->data["Special"] .= "<br>limiting its maneuvering options"; 
      $this->data["Special"] .= "<br>(-1 thrust and -20 Initiative next turn).";  
    }	
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;
		$this->iconPath = "tractorBeam.png";
		
		parent::__construct($armor, 6, 4, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //target is held critical on PRIMARY Structure!
	      /*
		$primaryStruct = $ship->getStructureSystem(0); //primary Structure is where the crit will reside - it has to be there! (weapon does not target fighters)
	      if($primaryStruct->isDestroyed()) return; //destroyed system - critical is irrelevant
		$crit = new swtargetheld(-1, $ship->id, $primaryStruct->id, $gamedata->turn); 
		$crit->updated = true;
                //$crit->inEffect = true;
	      $primaryStruct->criticals[] =  $crit;*/
		
		//to C&C, NOT Structure - on Structure it couldn't be shown to player
		$CnC = $ship->getSystemByName("CnC");
		if($CnC){
			$crit = new swtargetheld(-1, $ship->id, $CnC->id, 'swtargetheld', $gamedata->turn); 
			$crit->updated = true;
		      $CnC->criticals[] =  $crit;
		}
		

	}
	
	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class SWTractorBeam



?>
