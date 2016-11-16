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
        $output -= $this->outputMod;
	if($weapon->damageType == 'Raking') $output = 2*$output;//Raking - double effect!
	$output=max(0,$output); //no less than 0!
        return $output;
    }
	
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	//$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
	$this->data["Basic Strength"] = $this->baseOutput;      
	$this->data["<font color='red'>Remark</font>"] = "<br>Does not decrease profile."; 
	$this->data["<font color='red'>Remark</font>"] .= "<br>Does not protect from Ballistic, Matter and StarWars Ion damage."; 
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
	
} //endof class SWRayShield



class SWDirectWeapon extends Pulse{
    /*StarWars weapon - extension of Pulse mode!*/
    public $shots = 1;
    public $firingModes = array( 1 => "Burst");  
	
	//for Pulse mode
	//public $grouping = 25;
	//public $maxpulses = 1;	
	private $useDie = 3; //die used for base number of hits
 
    public $damageType = "Pulse"; //and this should remain!
    public $weaponClass = "Particle"; //and may be easily overridden
   
	//animation for fighter laser - bigger guns need to change size and speed attributes :)
	public $animation = "beam";
        public $animationColor = array(245, 75, 95);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 11;
	public $animationWidth = 3;
	public $trailLength = 8;
	
	protected $damagebonus = 0;
	protected $damagebonusArray = array();
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $nrOfShots){
		$this->maxpulses = $nrOfShots;
		$this->defaultShots = $nrOfShots;
		$this->intercept = $nrOfShots;
		$this->grouping = 35-5*$nrOfShots; //more guns means better grouping!
		$this->grouping = max(10,$this->grouping); //but no better than +1 per 10!
		
		//maxhealth and powerReq affected by number of barrels - received is for single -gun mount!
		//size: +40% for each additional gun
		//power: +65% for each additional gun 
		$maxhealth += $maxhealth*0.4*($nrOfShots-1);
		$powerReq += $powerReq*0.65*($nrOfShots-1);
		$maxhealth = ceil($maxhealth);
		$powerReq = ceil($powerReq);
				
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}    
	
	
        public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		 $this->data["Special"] = 'Burst mode: 0..2 +1/'. $this->grouping."%, max. ".$this->maxpulses." pulses";
		$this->data["Special"] .= '<br>Minimum of 1 pulse.';
        }
	
    
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn); //$this->useDie usually
		$pulses -= 1;
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
		$new = $this->priority+1;
		$this->priorityArray = array(1=>$this->priority, 2=>$new);
		$fc1 = $this->fireControl;
		$fc2 = array($fc1[0]-4, $fc1[1]-2, $fc1[2]-1); //FC of salvo mode: worse (much worse vs fighters)
		$this->fireControlArray = array(1=>$fc1, 2=>$fc2);
		//damage bonus: at least +1 per extra gun (+2 for second gun), but otherwise percentage based
		$damagebonusMin = 2;
		$damagebonusPerc = 120; //percentage: +20% for first gun, +10% for each one after that; counted from average damage
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
    public $animationColor =  array( 100, 100, 245);
    public $trailColor = array( 100, 100, 245);
	
	
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
      $this->data["<font color='red'>Remark</font>"] = "Damage may cause power shortages.";      
      $this->data["<font color='red'>Remark</font>"] .= "<br>Increased chance of critical on systems damaged."; 
    }
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //make vulnerable to next critical
      $dmg = $damage - $armour;
      if($dmg<=0) return; //no damage was actually done
      SWIonHandler::addDamage($ship, $system, $dmg);//possibly cause power shortage
      if($system->isDestroyed()) return; //destroyed system - vulnerability to critical is irrelevant
      if($system instanceof Structure) return; //structure does not suffer critical hits anyway

      $crit = new NastierCrit(-1, $ship->id, $system->id, $gamedata->turn, $dmg); //for ship system and fighter alike
      $system->criticals[] =  $crit;
    }

} //end of class SWIon



/*static class to handle accumulating Ion damage*/
class SWIonHandler{
	private static $accumulatedIonDmg = array();
	private static $power = 1.4; //effect magnitude from hit: damage ^power
	private static $free = 1; //this much damage doesn't cause anything
	private static $threshold = 6; //this much damage (after $free) causes power shortage
	private static $turn = 0; //turn for which data is held

	
	public static function addDamage($targetUnit, $targetSystem, $dmgInflicted){
		if($dmgInflicted<1) return;//no point if no damage was actually done
		if($targetUnit instanceof FighterFlight) return;//no effect on fighters
		if ($targetUnit->isDestroyed()) return; //no point in doing anything
		if(($targetSystem->displayName == 'Structure') || ($targetSystem instanceof Reactor) ){ //full damage counts
			$baseDmg = $dmgInflicted;
		}else{ //half damage counts 
			$baseDmg = ceil($dmgInflicted/2);
		}
		//effect is stronger than raw damage inflicted, and bigger hits do more damage:
		$effect = pow($baseDmg,SWIonHandler::$power);
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
    public $iconPath = "starwars/swFighter4.png";
	
    public $priority = 4;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

	protected $damagebonus = 0;

   
    
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->intercept = $nrOfShots;

		//appropriate icon (number of barrels)...
		if($nrOfShots<5) $this->iconPath = "starwars/swFighter".$nrOfShots.".png";
		
		parent::__construct(0, 1, 0, $startArc, $endArc, $nrOfShots);
	}    
	
	public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

} //end of class SWFighterLaser




class SWFighterIon extends SWIon{
    /*StarWars fighter Ion weapon*/
    public $name = "SWFighterIon";
    public $displayName = "Fighter Ion Cannon";
	public $iconPath = "starwars/swFighterIon1.png";	  
	  

	

    public $loadingtime = 1;
    public $rangePenalty = 1.5; //poor FC, but good range compared to Lasers! Perhaps lower rate of fire, too - but that would not be noticeable on fighter weapons (maybe in damage)
    public $fireControl = array(-3, -1, -1); // fighters, <mediums, <capitals

    public $exclusive = false; //can be always overridden in particular fighter!
    public $isLinked = true; //indicates that this is linked weapon
    protected $damagebonus = 0;     	
	
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->intercept = 0;

		parent::__construct(0, 1, 0, $startArc, $endArc, $nrOfShots);
	}    

			  
    
    public function getDamage($fireOrder){        return Dice::d(4)+$this->damagebonus;   }
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
    public function setMaxDamage(){     $this->maxDamage = 4+$this->damagebonus ;      }
	
} //end of class SWFighterIon
    



class SWFtrProtonTorpedoLauncher extends FighterMissileRack //this is launcher, which needs separate ammo; 2 shots per turn!
{
	//proton torpedo launcher for fighters
    public $name = "SWFtrProtonTorpedo";
    public $missileClass = "Torpedo";
    public $displayName = "Fighter Proton Torpedo";
    public $iconPath = "lightIonTorpedo.png";
    public $firingModes = array( 1 => "FtrTorpedo" );

    public $loadingtime = 1;
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    public $priority = 4;
    public $fireControl = array(-4, -1, 0); // fighters, <mediums, <capitals 
    
	public $damageType = 'Standard'; 
    	public $weaponClass = "Ballistic"; 
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        $Torp = new SWFtrProtonTorpedo($startArc, $endArc, $this->fireControl);
        $this->missileArray = array( 1 => $Torp );
        $this->maxAmount = $maxAmount;
    }
	
} //end of SWFtrProtonTorpedoLauncher




class SWFtrProtonTorpedo extends MissileFB //this is AMMO for SWFtrProtonTorpedoLauncher
{
    public $name = "SWFtrProtonTorpedo";
    public $missileClass = "FtrTorpedo";
    public $displayName = "Fighter Proton Torpedo";
    public $cost = 10;
    public $damage = 12;
    public $amount = 0;
    public $range = 15;
    public $distanceRange = 15;
    public $hitChanceMod = 0;
    public $priority = 2;
	public $damageType = 'Standard'; 
    	public $weaponClass = "Ballistic"; 
	
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct($startArc, $endArc, $fireControl);
    }
	
    public function getDamage($fireOrder=null){        return $this->damage;   }
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }              
}//end of SWFtrProtonTorpedo





class SWLightLaser extends SWDirectWeapon{
    /*StarWars lightest ship-mounted laser - essentially fighter weapon on ship housing!
      optimized for antifighter action (and interception)
    */
    /*d6+2 damage as a single gun
      2 structure, 0.5 power usage as a single gun
    */
    public $name = "SWLightLaser";
    public $displayName = "Light Laser";
    public $iconPath = "starwars/laserSmall4.png";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $fireControl = array(4, 2, 1); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = $nrOfShots;

		//appropriate icon (number of barrels)...
		if($nrOfShots<5) $this->iconPath = "starwars/laserSmall".$nrOfShots.".png";
		
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
    public $iconPath = "starwars/laserSmall4.png";
	
    public $priority = 3;
    public $loadingtime = 1;
    public $rangePenalty = 1.5; // 3 per 2 hexes
    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = floor($nrOfShots*0.9); //this gives distinctly worse interception than light laser

		//appropriate icon (number of barrels)...
		if($nrOfShots<5) $this->iconPath = "starwars/laserSmall".$nrOfShots.".png";
		
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
    public $iconPath = "starwars/laserSmall4.png";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 1; 
    public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = floor($nrOfShots*0.5); //this gives very poor interception, but still interception is possible

		//appropriate icon (number of barrels)...
		if($nrOfShots<5) $this->iconPath = "starwars/laserSmall".$nrOfShots.".png";
		
		parent::__construct($armor, 4, 1.1, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(8)+4 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 5 +$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 12 +$this->damagebonus ;      }

} //end of class SWHeavyLaser



class SWLightTLaser extends SWDirectWeapon{
    /*StarWars lightest turbolaser - roughly comparable to heavy laser, but more dependable vs ships and longer ranged than lasers (but doing less raw damage)
    */
    public $name = "SWLightTLaser";
    public $displayName = "Light Turbolaser";
    public $iconPath = "starwars/hvyTurbo.png";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 1;
    public $fireControl = array(-1, 2, 3); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		//if($nrOfShots<5) $this->iconPath = "starwars/laserSmall".$nrOfShots.".png";
		
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
    public $iconPath = "starwars/hvyTurbo.png";
	
    public $priority = 4;
    public $loadingtime = 2;
    public $rangePenalty = 0.5;
    public $fireControl = array(-3, 1, 3); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		//if($nrOfShots<5) $this->iconPath = "starwars/laserSmall".$nrOfShots.".png";
		
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
    public $iconPath = "starwars/hvyTurbo.png";
	
    public $priority = 5;
    public $loadingtime = 3;
    public $rangePenalty = 0.33;
    public $fireControl = array(-6, 0, 3); // fighters, <mediums, <capitals
   
    
	function __construct($armor, $startArc, $endArc, $nrOfShots){ //armor, arc and number of weapon in common housing: structure and power data are calculated!
		$this->intercept = 0;

		//appropriate icon (number of barrels)...
		//if($nrOfShots<5) $this->iconPath = "starwars/laserSmall".$nrOfShots.".png";
		
		parent::__construct($armor, 6, 4, $startArc, $endArc, $nrOfShots); //maxhealth and powerReq for single gun mount!
		$this->addSalvoMode();
	}    
	
	public function getDamage($fireOrder){ return  Dice::d(6,2)+7 +$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 9+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 19+$this->damagebonus ;      }

} //end of class SWHeavyTLaser


?>
