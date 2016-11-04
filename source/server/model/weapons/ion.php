<?php


class IonBolt extends Weapon{
    public $trailColor = array(30, 170, 255);

    public $name = "ionBolt";
    public $displayName = "Ion Bolt";
    public $animation = "trail";
    public $animationColor = array(127, 0, 255);
    public $animationExplosionScale = 0.15;
    public $projectilespeed = 12;
    public $animationWidth = 3;
    public $trailLength = 20;
    public $priority = 8;

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 1;
    public $fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals 
    public $exclusive = true;
    
    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ion"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

    function __construct($startArc, $endArc){
        parent::__construct(0, 1, 0, $startArc, $endArc);

    }
    

    public function getDamage($fireOrder){        return Dice::d(6, 3);  }
    public function setMinDamage(){     $this->minDamage = 3 ;      }
    public function setMaxDamage(){     $this->maxDamage = 18 ;      }

}


class IonCannon extends Raking{
    public $trailColor = array(30, 170, 255);

    public $name = "ionCannon";
    public $displayName = "Ion Cannon";
    //public $animation = "trail";
    public $animation = "laser"; //more fitting for Raking, and faster at long range
    public $animationColor = array(127, 0, 255);
    public $animationExplosionScale = 0.25;
    public $projectilespeed = 10;
    public $animationWidth = 3;//4;
    public $animationWidth2 = 0.3;
    public $trailLength = 24;
    public $intercept = 1;
    public $priority = 8;
    
    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.25;
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 
    
    public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ion"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function getDamage($fireOrder){        return Dice::d(10, 2)+10;  }
    public function setMinDamage(){     $this->minDamage = 12 ;      }
    public function setMaxDamage(){     $this->maxDamage = 30 ;      }
}



class ImprovedIonCannon extends Raking{
    public $trailColor = array(30, 170, 255);

    public $name = "improvedIonCannon";
    public $displayName = "Imp. Ion Cannon";
    public $animation = "trail";
    public $animationColor = array(127, 0, 255);
    public $animationExplosionScale = 0.30;
    public $projectilespeed = 10;
    public $animationWidth = 5;
    public $trailLength = 24;
    public $intercept = 1;
    public $priority = 8;

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.25;
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

    public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ion"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
    
    
    public function getDamage($fireOrder){        return Dice::d(10, 2)+15;  }
    public function setMinDamage(){     $this->minDamage = 17 ;      }
    public function setMaxDamage(){     $this->maxDamage = 35 ;      }
}


?>
