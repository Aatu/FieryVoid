<?php
/*custom weapons - from StarWars universe (to keep them separate)*/

class SWFighterLaser extends LinkedWeapon{
    /*StarWars fighter weapon - a Particle weapon!*/
    public $name = "SWFighterLaser";
    public $displayName = "Fighter Laser";
    public $iconPath = "starwars/swFighter2.png";
	
    public $animation = "trail";
    public $projectilespeed = 13;
    public $animationExplosionScale = 0.15;
    public $animationWidth = 3;
    public $trailLength = 20;
    public $animationColor =  array(225, 175, 195);
    public $trailColor = array(205, 175, 185);
	
    public $priority = 4;
    public $loadingtime = 1;
    public $rangePenalty = 2;
    public $firingModes = array( 1 => "Standard");  
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
 
    public $damageType = "standard"; //actual mode of dealing damage (standard, flash, raking...) - overrides $this->data["Damage type"] if set!
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

		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    
	
/* sadly fighter weapons can't change modes... but shipborne ones can, so this remains as an example!
	public function changeFiringMode($newMode){ //change parameters with mode change - those not changed by standard
		//to display in GUI, shipSystem.js changeFiringMode function also needs to be redefined
		parent::changeFiringMode($newMode);
		$i = $newMode;
		if(isset($damagebonusArray[$i])) $this->damagebonus = $damagebonusArray[$i];
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
	public $iconPath = "starwars/fighterIon.png";	  
	  
    public $animation = "trail";
    public $projectilespeed = 13;
    public $animationExplosionScale = 0.15;
    public $animationWidth = 3;
    public $trailLength = 20;
    public $animationColor =  array( 175, 195, 225);
    public $trailColor = array( 175, 185, 205);
	
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
    }
     
	
	
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots){
		$this->damagebonus = $damagebonus;
		$this->shots = $nrOfShots;
		$this->defaultShots = $nrOfShots;
		$this->intercept = 0;

		parent::__construct(0, 1, 0, $startArc, $endArc);
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
    















?>
