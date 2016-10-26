<?php
/*custom weapons - from StarWars universe (to keep them separate)*/

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
		if($nrOfShots<5) $this->iconPath = "starwars/swFighter"+$nrOfShots+".png";
		
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

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//$this->data["Weapon type"] = "Particle";
		//$this->data["Damage type"] = "Standard";
	}

    
	public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
	public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

} //end of SWFighterLaser




class SWFighterIon extends LinkedWeapon{
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
	
    public $priority = 4;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $firingModes = array( 1 => "Standard");  
    public $fireControl = array(-2, -1, -1); // fighters, <mediums, <capitals
 
    public $damageType = "standard"; //actual mode of dealing damage (standard, flash, raking...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "SW Ion"; //weapon class - overrides $this->data["Weapon type"] if set!
	  
    public $systemKiller = true;

    
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
      $this->data["<font color='red'>Remark</font>"] = "Increased chance to hit systems.";      
      $this->data["<font color='red'>Remark</font>"] .= "<br>Increased chance of critical."; 
      $this->data["<font color='red'>Remark</font>"] .= "<br>Ignore half of armor."; 
    }
     
	
	
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->shots = $nrOfShots;
		$this->defaultShots = $nrOfShots;
		$this->intercept = 0;

		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    

    
	  
	protected function getSystemArmour($system, $gamedata, $fireOrder, $pos=null){ //ignore half of armor
		$armour = parent::getSystemArmour($system, $gamedata, $fireOrder, $pos);
		    if (is_numeric($armour)){
			$new = floor($armour /2);
			return $new;
		    }
		    else {
			return 0;
		    }
        }
	
	
	  
    
    public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

	
	
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //make vulnerable to next critical
      if($system->isDestroyed()) return; //destroyed system - vulnerability to critical is irrelevant
      if($system instanceof Structure) return; //structure does not suffer critical hits anyway
            
      $crit = new NastierCrit(-1, $ship->id, $system->id, $gamedata->turn, $damage); //for ship system and fighter alike
      $system->criticals[] =  $crit;
    }

} //end of SWFighterIon
    


/* FighterTorpedoLauncher will be used instead
class SWFtrProtonTorpedoLauncher extends FighterMissileRack //this is launcher, which needs separate ammo
{
	//proton torpedo launcher for fighters
    public $name = "SWFtrProtonTorpedo";
    public $missileClass = "Torpedo";
    public $displayName = "Fighter Proton Torpedo";
    public $iconPath = "lightIonTorpedo.png";
	
    public $firingModes = array( 1 => "FtrTorpedo" );


    public $loadingtime = 1;
    public $iconPath = "fighterTorpedo.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    protected $distanceRangeMod = 0;
    public $priority = 4;
    public $fireControl = array(-4, -1, 0); // fighters, <mediums, <capitals 
    
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        $Torp = new SWFtrProtonTorpedo($startArc, $endArc, $this->fireControl);
        $this->missileArray = array( 1 => $Torp );
        $this->maxAmount = $maxAmount;
    }
    
    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);
        $this->data["Weapon type"] = "Ballistic";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = $this->missileArray[$this->firingMode]->displayName;
        if($this->missileArray[$this->firingMode]->minDamage != $this->missileArray[$this->firingMode]->maxDamage){
            $this->data["Damage"] = "".$this->missileArray[$this->firingMode]->minDamage."-".$this->missileArray[$this->firingMode]->maxDamage;
        }else{
            $this->data["Damage"] = "".$this->missileArray[$this->firingMode]->minDamage;
        }
        $this->data["Range"] = $this->missileArray[$this->firingMode]->range;
    }
    
    public function getDistanceRange(){
        return $this->missileArray[$this->firingMode]->range;
    }
	
	
} //end of SWFtrProtonTorpedoLauncher
*/



class SWFtrProtonTorpedo extends MissileFB //this is AMMO; use FighterTorpedoLauncher with appropriate name/icon/ammo changes for weapon
{
    public $name = "SWFtrProtonTorpedo";
    public $missileClass = "FtrTorpedo";
    public $displayName = "Fighter Proton Torpedo";
    public $cost = 10;
    public $surCharge = 0;
    public $damage = 12;
    public $amount = 0;
    public $range = 15;
    public $hitChanceMod = 0;
    public $priority = 4;
    
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct($startArc, $endArc, $fireControl);
    }
	
    public function getDamage($fireOrder){        return $this->damage;   }
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }              
}//end of SWFtrProtonTorpedo








?>
