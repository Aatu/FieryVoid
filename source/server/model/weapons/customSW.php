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
    public $firingModes = array( 1 => "Standard", 2=>"Linked");  
    public $fireControlModes = array(1=>array(0,0,0), 2=>array(-2,-1,-1));
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
 
    public $damageType = "standard"; //actual mode of dealing damage (standard, flash, raking...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Particle"; //weapon class - overrides $this->data["Weapon type"] if set!

	

    public $exclusive = false;   
   
    
	function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameMod = ''){
		if ($nameMod != '') $displayName.= ' ('.$nameMod.')';
		$this->damagebonus = $damagebonus;
		$this->damagebonusTab[1] = $damagebonus;
		$this->damagebonusTab[2] = $damagebonus; //first gun provides basic damage bonus in linked mode
		if($nrOfShots>1)$this->damagebonusTab[2]+ = 2; //+2 for second linked weapon
		if($nrOfShots>2)$this->damagebonusTab[2]+ = ($nrOfShots - 2); //+1 for each additional linked weapon
		
		$this->defaultShots = $nrOfShots;
		$this->shots = $nrOfShots;
		$this->intercept = $nrOfShots;

		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    
	

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//$this->data["Weapon type"] = "Particle";
		//$this->data["Damage type"] = "Standard";
		$this->data["<font color='red'>Remark</font>"] = "Capable of linked fire."; 
	}

    
	public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
	public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
	public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

} //end of SWFighterLaser




  class SWFighterIon extends LinkedWeapon{
    /*StarWars fighter Ion weapon*/
/*let's skip content for now, concentrate on debugging Laser......
    public $name = "swFighterIon";
    public $displayName = "Fighter Ion Cannon";
	public $iconPath = "starwars/fighterIon.png";	  
    public $animation = "trail";
    public $projectilespeed = 10;
    public $animationColor =  array(175, 225, 195);
    public $animationWidth = 10;
    public $trailColor = array(175, 205, 185);
    public $trailLength = 5;
    public $priority = 9;
    public $intercept = 0;
    public $loadingtime = 1;
    public $shots = 2;
    public $defaultShots = 2;
    public $rangePenalty = 2;
    public $firingModes = array( 1 => "Standard", 2=>"Linked");  
    public $fireControlModes = array(1=>array(-3, -1, -1), 2=>array(-5,-2,-2));    
    public $fireControl = array(-3, -1, -1); // fighters, <mediums, <capitals
	    
    private $defaultDmgBonus = 0;
    private $damagebonus = 0;
    public $exclusive = false;   
	  
    public $systemKiller = true;

    
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
      $this->data["Weapon type"] = "SW Ion";
      $this->data["Damage type"] = "Standard";
      $this->data["<font color='red'>Remark</font>"] = "Increased chance to hit systems.";      
      $this->data["<font color='red'>Remark</font>"] .= "<br>Increased chance of critical."; 
      $this->data["<font color='red'>Remark</font>"] .= "<br>Capable of linked fire."; 
    }
     
    function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameMod = ''){
      if ($nameMod != '') $displayName+= ' ('+$nameMod+')';
      $this->damagebonus = $damagebonus;
      $this->defaultDmgBonus = $damagebonus;
      $this->defaultShots = $nrOfShots;
      $this->shots = $nrOfShots;
      $this->intercept = 0;
          
      parent::__construct(0, 1, 0, $startArc, $endArc);
    }    
    
    public function changeFiringMode($newMode){ //set number of shots, FC and damage bonus according to mode
	      if(!($newMode>0)) return; ///this is not correct!
	      parent::changeFiringMode($newMode);
	      //changes: linked shot will be single shot at reduced FC and doubled damage bonus
	      $this->fireControl = $this->fireControlModes[$newMode];
			  if($newMode == 1){ //standard
		$this->shots = $this->defaultShots;
		$this->damagebonus = $this->defaultDmgBonus;
	      }else{ //fire all guns as one shot
		$this->shots = 1;
		$this->damagebonus = $this->defaultDmgBonus +2*($this->defaultShots-1);
	      }
      
	return;
    }
	  
	  
    
    public function getDamage($fireOrder){        return Dice::d(3)+$this->damagebonus;   }
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 3+$this->damagebonus - $this->dp;      }
        
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //make vulnerable to next critical
      if($system->isDestroyed()) return; //destroyed system - vulnerability to critical is irrelevant
      if($system instanceof Structure) return; //structure does not suffer critical hits anyway
            
      $crit = new NastierCrit(-1, $ship->id, $system->id, $gamedata->turn, $damage); //for ship system and fighter alike
      $system->criticals[] =  $crit;
    }
*/
  } //end of SWFighterIon
    









    class SWFighterLaserT extends LinkedWeapon{
        public $trailColor = array(30, 170, 255);
        public $name = "SWFighterLaserT";
        public $displayName = "Paired Particle guns";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;
        public $intercept;
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            $this->intercept = $nrOfShots;
            if($nrOfShots === 1){
                $this->iconPath = "particleGun.png";
            }
            if($nrOfShots === 3){
                $this->iconPath = "pairedParticleGun3.png";
            }
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        public function setSystemDataWindow($turn){
            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Standard";
            parent::setSystemDataWindow($turn);
        }
        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }
    }








?>
