<?php


class IonBolt extends Weapon{
    public $trailColor = array(30, 170, 255);

    public $name = "ionBolt";
    public $displayName = "Ion Bolt";
    public $animation = "bolt";
    public $animationColor = array(127, 0, 255);
	/*
    public $animationExplosionScale = 0.15;
    public $projectilespeed = 12;
    public $animationWidth = 3;
    public $trailLength = 20;
    */
	
    public $priority = 6; //very heavy fighter weapon... even if borderline!

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
    public $name = "ionCannon";
    public $displayName = "Ion Cannon";
    //public $animation = "trail";
    public $animation = "laser"; //more fitting for Raking, and faster at long range
    public $animationColor = array(127, 0, 255);
	/*
    public $animationExplosionScale = 0.25;
    public $animationWidth = 3;//4;
    public $animationWidth2 = 0.3;
    public $trailLength = 24;
    */
	
    public $priority = 8; //light Raking weapon
    
    public $intercept = 1;
    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.25;
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 
    
    public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ion"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        public $firingModes = array( 1 => "Raking");  
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function getDamage($fireOrder){        return Dice::d(10, 2)+10;  }
    public function setMinDamage(){     $this->minDamage = 12 ;      }
    public function setMaxDamage(){     $this->maxDamage = 30 ;      }
}


//CUSTOM weapon
class ImprovedIonCannon extends Raking{
    public $name = "improvedIonCannon";
    public $displayName = "Improved Ion Cannon";   
    public $animation = "laser"; //more fitting for Raking, and faster at long range
    public $animationColor = array(127, 0, 255);
	/*
    public $animationExplosionScale = 0.25;
    public $animationWidth = 3;//4;
    public $animationWidth2 = 0.3;
    */
	
    public $intercept = 1;
    public $priority = 8;

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 0.25;
    public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals 

    public $damageType = "Raking"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ion"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        public $firingModes = array( 1 => "Raking");
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
        
    public function getDamage($fireOrder){        return Dice::d(10, 2)+15;  }
    public function setMinDamage(){     $this->minDamage = 17 ;      }
    public function setMaxDamage(){     $this->maxDamage = 35 ;      }
}


    /*Cascor fighter weapon*/
    class Ionizer extends LinkedWeapon{
        public $name = "Ionizer";
        public $iconPath = "ionizer2.png";
        public $displayName = "Ionizer";        
        
        //public $animation = "laser"; //deals Standard damage, but that's due to beam having short duration - it's a laser beam in nature; 
	    //most such weapons do use Bolt animation, but her it serves to make them stand out!
		public $animation = "bolt"; //let's make it Bolt after all - more fitting with other fighter weapons
        public $animationColor = array(160, 0, 255);
	    /*
        public $animationExplosionScale = 0.1;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        */
	    
        public $priority = 3; //rather light weapons!
        public $intercept = 0; //Lasers cannot intercept!
        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        public $uninterceptable = true;//not-interceptable weapon - described as Ion+Laser originally
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        
        function __construct($startArc, $endArc, $nrOfShots = 2){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            //$this->intercept = $nrOfShots; //Lasers cannot intercept!
            if($nrOfShots === 1){
                $this->iconPath = "ionizer1.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "ionizer3.png";
            }
			
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            }	    
            $this->data["Special"] .= "Uninterceptable."; 
        }
        
        public function getDamage($fireOrder){        return Dice::d(3,2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
    }



    /*CUSTOM Cascor fighter antiship weapon*/
    class IonizerHvy extends LinkedWeapon{
        public $name = "IonizerHvy";
        public $iconPath = "ionizerHvy2.png";
        public $displayName = "Heavy Ionizer";        
        
        //public $animation = "laser"; //deals Standard damage, but that's due to beam having short duration - it's a laser beam in nature; 
	    //most such weapons do use Bolt animation, but her it serves to make them stand out!
		public $animation = "bolt"; //let's make it Bolt after all - more fitting with other fighter weapons
        public $animationColor = array(160, 0, 255);
	    /*
        public $animationExplosionScale = 0.1;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        */
	    
        public $priority = 5; //rather heavy weapons!
        public $intercept = 0; //Lasers cannot intercept!
        public $loadingtime = 2; //RoF 1/2turns
        public $shots = 2;
        public $defaultShots = 2;
        public $rangePenalty = 2;
        public $fireControl = array(-3, 0, 0); // fighters, <mediums, <capitals
		public $exclusive = true; //exclusive weapon
        public $uninterceptable = true;//not-interceptable weapon - described as Ion+Laser originally
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        
        function __construct($startArc, $endArc, $nrOfShots = 2){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            //$this->intercept = $nrOfShots; //Lasers cannot intercept!
            if($nrOfShots === 1){
                $this->iconPath = "ionizerHvy1.png";
            }
            if($nrOfShots >2){//no special icon for more than 3 linked weapons
                $this->iconPath = "ionizerHvy3.png";
            }
			
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            }	    
            $this->data["Special"] .= "Uninterceptable."; 
        }
        
        public function getDamage($fireOrder){        return Dice::d(3,3)+3;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }


    /*Cascor weapon*/
    class IonicLaser extends Laser{        
        public $name = "IonicLaser";
        public $iconPath = "ionicLaser.png";
        public $displayName = "Ionic Laser";
        public $animation = "laser";
        public $animationColor = array(160, 0, 255);
	    /*
        public $animationExplosionScale = 0.18;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
	*/
	
        public $priority = 8; //light Raking
        public $loadingtime = 2;
        public $raking = 10;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 3); // fighters, <mediums, <capitals 
        
        public $damageType = "Raking"; 
        public $weaponClass = "Ion"; 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            /* Laser class itself marks Uninterceptable
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            }	    
            $this->data["Special"] .= "Uninterceptable."; 
            */
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+8;   }
        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 38 ;      }    
    }


    /*Cascor weapon*/
    class DualIonBolter extends Weapon{
        public $name = "DualIonBolter";
        public $displayName = "Dual Ion Bolter";
        public $iconPath = "dualIonBolter.png";
        
        public $animation = "trail";
        public $animationColor = array(127, 0, 255);
	    /*
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 16;
        public $animationWidth = 3;
        public $trailLength = 3;
        */
	    
        public $priority = 3; //damage output 8 is very light
        public $loadingtime = 1;
        public $intercept = 2;
        public $rangePenalty = 1;
        public $guns = 2;
        
        public $damageType = "Standard"; 
        public $weaponClass = "Ion"; 
        
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 8;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
    }


?>
