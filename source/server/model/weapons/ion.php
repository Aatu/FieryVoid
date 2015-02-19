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

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 1;
    public $fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals 
    public $exclusive = true;

    function __construct($startArc, $endArc){
        parent::__construct(0, 1, 0, $startArc, $endArc);

    }

    public function getDamage($fireOrder){        return Dice::d(6, 3);  }
    public function setMinDamage(){     $this->minDamage = 3 - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 18 - $this->dp;      }

}

class IonCannon extends Raking{

    public $trailColor = array(30, 170, 255);

    public $name = "ionCannon";
    public $displayName = "Ion Cannon";
    public $animation = "trail";
    public $animationColor = array(127, 0, 255);
    public $animationExplosionScale = 0.25;
    public $projectilespeed = 10;
    public $animationWidth = 4;
    public $trailLength = 24;
    public $intercept = 1;
    public $priority = 4;

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.25;
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

    public function setSystemDataWindow($turn){

        $this->data["Weapon type"] = "Ion";
        $this->data["Damage type"] = "Raking";

        parent::setSystemDataWindow($turn);
    }

    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function getDamage($fireOrder){        return Dice::d(10, 2)+10;  }
    public function setMinDamage(){     $this->minDamage = 12 - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 30 - $this->dp;      }
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
    public $priority = 4;

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.25;
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
    
    public function setSystemDataWindow($turn){

        $this->data["Weapon type"] = "Ion";
        $this->data["Damage type"] = "Raking";

        parent::setSystemDataWindow($turn);
    }
    
    public function getDamage($fireOrder){        return Dice::d(10, 2)+15;  }
    public function setMinDamage(){     $this->minDamage = 17 - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 35 - $this->dp;      }
}


