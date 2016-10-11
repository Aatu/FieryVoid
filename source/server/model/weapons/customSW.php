<?php
/*custom weapons - from StarWars universe (to keep them separate)*/


  class SWFighterLaser extends LinkedWeapon{
    /*StarWars fighter weapon - a Particle weapon!*/
    public $name = "swFighterLaser";
    public $displayName = "Fighter Laser";
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
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
    private $damagebonus = 0;
    public $exclusive = false;    
    
    function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameMod = ''){
      if ($nameMod != '') $displayName+= ' ('+$nameMod+')';
      $this->damagebonus = $damagebonus;
      $this->defaultShots = $nrOfShots;
      $this->shots = $nrOfShots;
      $this->intercept = $nrOfShots;
          
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

  } //end of SWFighterLaser


  class SWFighterLaserAllLinked extends SWFighterLaser{
    /*StarWars fighter weapon - maximum power shot, all guns linked!*/
    function __construct($startArc, $endArc, $damagebonus){
      $name = "swFighterLaserAllLinked";
      $this->$animationColor =  array(235, 175, 200);
      $this->$animationWidth = 12;
      $this->$trailColor = array(215, 175, 190);
      $this->$trailLength = 6;
      public $fireControl = array(-3, -1, -1); // fully linked shot is less accurate!
      $this->exclusive = true;
      
      parent::__construct($startArc, $endArc, $damagebonus, 1, 'All Linked');
    }

  } //end of SWFighterLaserAllLinked



  class SWFighterIon extends LinkedWeapon{
    /*StarWars fighter Ion weapon*/
    public $name = "swFighterIon";
    public $displayName = "Fighter Ion Cannon";
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
    public $fireControl = array(-3, -1, -1); // fighters, <mediums, <capitals
    private $damagebonus = 0;
    public $exclusive = false;    
    
    function __construct($startArc, $endArc, $damagebonus, $nrOfShots, $nameMod = ''){
      if ($nameMod != '') $displayName+= ' ('+$nameMod+')';
      $this->damagebonus = $damagebonus;
      $this->defaultShots = $nrOfShots;
      $this->shots = $nrOfShots;
          
      parent::__construct(0, 1, 0, $startArc, $endArc);
    }    
    
    public function setSystemDataWindow($turn){
      $this->data["Weapon type"] = "SW Ion";
      $this->data["Damage type"] = "Standard";
      $this->data["<font color='red'>Remark</font>"] = "Increased chance to hit systems.";      
      $this->data["<font color='red'>Remark</font>"] = "Increased chance of critical."; 
      $this->data["<font color='red'>Remark</font>"] = "Ignores half of armor."; 
      parent::setSystemDataWindow($turn);
    }
     
    public function getDamage($fireOrder){        return Dice::d(3)+$this->damagebonus;   }
    public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 3+$this->damagebonus - $this->dp;      }
    
    protected function getSystemArmour($system, $gamedata, $fireOrder){
			$armour = parent::getSystemArmour($system, $gamedata, $fireOrder);
        if (is_numeric($armour)){
          $toIgnore = ceil($armour /2);
          $new = $armour - $toIgnore;
          return $new;
        }
        else {
          return 0;
        }
    }
    
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //make vulnerable to next critical
      if($system->isDestroyed()) return; //destroyed system - vulnerability to critical is irrelevant
      if($system instanceof Structure) return; //structure does not suffer critical hits anyway
            
      $crit = new NastierCrit(-1, $ship->id, $system->id, $gamedata->turn, $damage); //for ship system and fighter alike
      $system->criticals[] =  $crit;
    }

  } //end of SWFighterIon
    

  class SWFighterIonAllLinked extends SWFighterIon{
    /*StarWars fighter weapon - maximum power shot, all guns linked!*/
    function __construct($startArc, $endArc, $damagebonus){
      $name = "swFighterIonAllLinked";      
      $this->$animationColor =  array(175, 235, 200);
      $this->$animationWidth = 12;
      $this->$trailColor = array(175, 215, 190);
      $this->$trailLength = 6;
      public $fireControl = array(-5, -3, -2); // fully linked shot is less accurate!
      $this->exclusive = true;
      
      parent::__construct($startArc, $endArc, $damagebonus, 1, 'All Linked');
    }

  } //end of SWFighterIonAllLinked

















?>
