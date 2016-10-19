<?php
/*custom weapons - from StarWars universe (to keep them separate)*/


  class SWFighterLaser extends LinkedWeapon{
    /*StarWars fighter weapon - a Particle weapon!*/
    public $name = "swFighterLaser";
    public $displayName = "Fighter Laser";
    public $iconPath = "starwars/swFighter2.png";
    public $animation = "trail";
    public $projectilespeed = 12;
    public $animationColor =  array(225, 175, 195);
    public $animationWidth = 10;
    public $trailColor = array(205, 175, 185);
    public $trailLength = 5;
    public $priority = 4;
    public $intercept = 2;
    public $loadingtime = 1;
    public $shots = 2;
    public $defaultShots = 2;
    public $rangePenalty = 2;
    public $firingModes = array( 1 => "Standard", 2=>"Linked");  
    public $fireControlModes = array(1=>(0,0,0), 2->(-2,-1,-1));
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
 
    private $defaultDmgBonus = 0;
    private $damagebonus = 0;
    public $exclusive = false;    
    
    function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameMod = ''){
      if ($nameMod != '') $displayName+= ' ('+$nameMod+')';
      $this->damagebonus = $damagebonus;
      $this->defaultDmgBonus = $damageBonus;
      $this->defaultShots = $nrOfShots;
      $this->shots = $nrOfShots;
      $this->intercept = $nrOfShots;
          
      parent::__construct(0, 1, 0, $startArc, $endArc);
    }    
    
    public function changeFiringMode($newMode){ //set number of shots, FC and damage bonus according to mode
      if(!($newMode>0)) return; ///this is not correct!
      parent::changeFiringMode($newMode);
      //changes: linked shot will be single shot at reduced FC and doubled damage bonus
      $this->fireControl = $this->$fireControlModes[$newMode];
		  if($newMode == 1){ //standard
        $this->shots = $this->defaultShots;
        $this->damagebonus = $this->defaultDmgBonus;
      }else{ //fire all guns as one shot
        $this->shots = 1;
        $this->damagebonus = $this->defaultDmgBonus + 2*($this->defaultShots-1);
      }
      
		  return;
	  }
    
    public function setSystemDataWindow($turn){
      parent::setSystemDataWindow($turn);
      $this->data["Weapon type"] = "Particle";
      $this->data["Damage type"] = "Standard";
      $this->data["<font color='red'>Remark</font>"] = "Capable of linked fire."; 
    }
    
    public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus - $this->dp;      }

  } //end of SWFighterLaser


/*
  class SWFighterLaserAllLinked extends SWFighterLaser{
    function __construct($startArc, $endArc, $damagebonus){
      $name = "swFighterLaserAllLinked";
      $this->$animationColor =  array(235, 175, 200);
      $this->$animationWidth = 12;
      $this->$trailColor = array(215, 175, 190);
      $this->$trailLength = 6;
      public $fireControl = array(-2, -1, -1); // fully linked shot is less accurate!
      $this->exclusive = true;
      
      parent::__construct($startArc, $endArc, $damagebonus, 1, 'All Linked');
    }

  } //end of SWFighterLaserAllLinked
*/


  class SWFighterIon extends LinkedWeapon{
    /*StarWars fighter Ion weapon*/
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
    public $defaultDmgBonus = 0;
    public $rangePenalty = 2;
    public $firingModes = array( 1 => "Standard", 2=>"Linked");  
    public $fireControlModes = array(1=>(-3, -1, -1), 2->(-5,-2,-2));    
    public $fireControl = array(-3, -1, -1); // fighters, <mediums, <capitals
    private $damagebonus = 0;
    public $exclusive = false;   
    public $systemKiller = true;
    
    function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameMod = ''){
      if ($nameMod != '') $displayName+= ' ('+$nameMod+')';
      $this->damagebonus = $damagebonus;
      $this->defaultDmgBonus = $damagebonus;
      $this->defaultShots = $nrOfShots;
      $this->shots = $nrOfShots;      
          
      parent::__construct(0, 1, 0, $startArc, $endArc);
    }    
    
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
      $this->defaultDmgBonus = $damageBonus;
      $this->defaultShots = $nrOfShots;
      $this->shots = $nrOfShots;
      $this->intercept = $nrOfShots;
          
      parent::__construct(0, 1, 0, $startArc, $endArc);
    }    
    
    public function changeFiringMode($newMode){ //set number of shots, FC and damage bonus according to mode
	      if(!($newMode>0)) return; ///this is not correct!
	      parent::changeFiringMode($newMode);
	      //changes: linked shot will be single shot at reduced FC and doubled damage bonus
	      $this->fireControl = $this->$fireControlModes[$newMode];
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

  } //end of SWFighterIon
    


















?>
