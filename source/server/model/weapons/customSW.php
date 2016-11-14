<?php
/*custom weapons - from StarWars universe (to keep them separate)*/

/*static class to handle accumulating Ion damage*/
class SWIonHandler{
	private static $accumulatedIonDmg = array();
	private static $power = 1.5; //effect magnitude from hit: damage ^power
	private static $free = 2; //this much damage doesn't cause anything
	private static $threshold = 8; //this much damage (after $free) causes power shortage
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
		$effect = pow($baseDmg,$power);
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

/*base class for StarWars Ion weapons*/
class SWIon extends Weapon{	
	/*compared to SW Lasers: a bit better range, but poor FC and damage (and possible RoF as well)*/
    public $name = "swion";
    public $priority = 10; //Ions usually fire last, to take advantage of induced criticals
 
    public $damageType = "Standard"; //most if not all SWIon weapons will be Standard mode
    public $weaponClass = "SWIon"; //weapon class
	  
    //public $systemKiller = true; //let's not go overhead - do NOT use $systemKiller...

	
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


/*
	StarWars Ray Shield: does not affect profile
	protects vs all weapoon classes except Matter, Ballistic and SW Ion
	doubly effective vs Raking weapons (to simulate longer burst)
*/
class SWRayShield extends Shield implements DefensiveSystem{
    public $name = "swrayshield";
    public $displayName = "Ray Shield";
    public $iconPath = "shield.png";
	
 	public $possibleCriticals = array( //different than usual B5Wars shield
            16=>"OutputReduced1",
            23=>array("OutputReduced1", "OutputReduced1")
	);
	
    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
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
        $output = $this->output;
	//Ballistic, Matter, SWIon - passes through!
	if($weapon->weaponClass == 'Ballistic' || $weapon->weaponClass == 'Matter' || $weapon->weaponClass == 'SWIon' ) $output = 0;
        $output -= $this->outputMod;
	//Raking - double effect!
	if($weapon->damageType == 'Raking') $output = 2*$output;
	$output=max(0,$output); //no less than 0!
        return $output;
    }
	
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);
	$this->data["<font color='red'>Remark</font>"] = "Strength ".$this->output.".";      
	$this->data["<font color='red'>Remark</font>"] .= "<br>Does not decrease profile."; 
	$this->data["<font color='red'>Remark</font>"] .= "<br>Does not protect from Ballistic, Matter and StarWars Ion damage."; 
    }
	   
} //endof class SWRayShield



class SWFighterLaser extends LinkedWeapon{
    /*StarWars fighter weapon - a Particle weapon!*/
    public $name = "SWFighterLaser";
    public $displayName = "Fighter Laser";
    public $iconPath = "starwars/swFighter4.png";
	
    public $animation = "trail";
    public $projectilespeed = 13;
    public $animationExplosionScale = 0.15;
    public $animationWidth = 3;
    public $trailLength = 20;
    public $animationColor =  array(245, 75, 95);
    public $trailColor = array(245, 75, 95);
	
    public $priority = 4;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $firingModes = array( 1 => "Standard");  
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
 
    public $damageType = "Standard"; //actual mode of dealing damage (standard, flash, raking...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Particle"; //weapon class - overrides $this->data["Weapon type"] if set!

	private $damagebonus = 0;
	

    public $exclusive = false;   
   
    
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
/*
		$this->damagebonusArray[1] = $damagebonus;
		$this->damagebonusArray[2] = $damagebonus; //first gun provides basic damage bonus in linked mode
		if($nrOfShots>1)$this->damagebonusArray[2] += 2; //+2 for second linked weapon
		if($nrOfShots>2)$this->damagebonusArray[2] += ($nrOfShots - 2); //+1 for each additional linked weapon
*/
		$this->shots = $nrOfShots;
//		$this->shotsArray = array(1=>$nrOfShots, 2=>1);
		
		$this->defaultShots = $nrOfShots;
		$this->intercept = $nrOfShots;

		//appropriate icon (number of barrels)...
		if($nrOfShots<5) $this->iconPath = "starwars/swFighter".$nrOfShots.".png";
		
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    
	
/* sadly fighter weapons can't change modes... but shipborne ones can, so this remains as an example!
	public function changeFiringMode($newMode){ //change parameters with mode change - those not changed by standard
		//to display in GUI, shipSystem.js changeFiringMode function also needs to be redefined
		parent::changeFiringMode($newMode);
		$i = $newMode;
		if(isset($this->damagebonusArray[$i])) $this->damagebonus = $this->damagebonusArray[$i];
	}
*/	
    
	public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus ;      }
	public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus ;      }

} //end of class SWFighterLaser




class SWFighterIon extends SWIon{
    /*StarWars fighter Ion weapon*/
    public $name = "SWFighterIon";
    public $displayName = "Fighter Ion Cannon";
	public $iconPath = "starwars/swFighterIon1.png";	  
	  
    public $animation = "trail";
    public $projectilespeed = 13;
    public $animationExplosionScale = 0.15;
    public $animationWidth = 3;
    public $trailLength = 20;
    public $animationColor =  array( 100, 100, 245);
    public $trailColor = array( 100, 100, 245);
	

    public $loadingtime = 1;
    public $rangePenalty = 1.5; //poor FC, but good range compared to Lasers! Perhaps lower rate of fire, too - but that would not be noticeable on fighter weapons (maybe in damage)
    public $firingModes = array( 1 => "Standard");  
    public $fireControl = array(-3, -1, -1); // fighters, <mediums, <capitals

    public $exclusive = false; //can be always overridden in particular fighter!
    public $isLinked = true; //indicates that this is linked weapon
    private $damagebonus = 0;     	
	
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->shots = $nrOfShots;
		$this->defaultShots = $nrOfShots;
		$this->intercept = 0;

		parent::__construct(0, 1, 0, $startArc, $endArc);
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
    public $priority = 4;
	public $damageType = 'Standard'; 
    	public $weaponClass = "Ballistic"; 
	
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct($startArc, $endArc, $fireControl);
    }
	
    public function getDamage($fireOrder=null){        return $this->damage;   }
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }              
}//end of SWFtrProtonTorpedo








?>
