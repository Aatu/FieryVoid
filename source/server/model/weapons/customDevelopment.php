<?php
/*file for weapons and systems in development*/


    class LaserArray extends Laser{
        public $name = "LaserArray";
        public $displayName = "Laser Array";
        public $animation = "bolt";
		public $iconPath = "quadArray.png";

        public $intercept = 2;

        public $loadingtime = 1;
        public $guns = 4;
        public $priority = 4;

        public $rangePenalty = 2;
        public $fireControl = array(5, 3, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 10;
		if ( $powerReq == 0 ) $powerReq = 7;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }

    } // end class LaserArray


/*
	The Satyra have specialized armor that affects only Laser and Electromagnetic weapons.
	The best way to simulate this is with a shield that only reacts to these classe.
	Since this is "armor", it cannot be flown under, boosted, or destroyed.
*/

class SatyraShield extends Shield implements DefensiveSystem{
    public $name = "SatyraShield";
    public $displayName = "Satyra Armor";
    public $iconPath = "shieldInvulnerable.png";
    public $boostable = false; //$this->boostEfficiency and $this->maxBoostLevel in __construct() 
    public $baseOutput = 0; //base output, before boost
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
	public $isTargetable = false; //cannot be targeted ever!
	
    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
	$this->baseOutput = $shieldFactor;
    }
	
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = 0;
		$this->damagePenalty = $this->getOutput();
    }
	
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){ //no defensive hit chance change
            return 0;
    }
    private function checkIsFighterUnderShield($target, $shooter, $weapon){ //no flying under SW shield
        return false;
    }
	
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
		$output = 0;
		//Affects only Antimatter, Laser, and Particle weapons
		if($weapon->weaponClass == 'Laser' || $weapon->weaponClass == 'Electromagnetic') $output = 2;
        return $output;
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//$this->output = $this->baseOutput + $this->getBoostLevel($turn); //handled in front end
		$this->data["Basic Strength"] = $this->baseOutput;    
		$this->data["Special"] = "Note: Satyra armor is resistent to lasers and electromagnetic weapons."; 
		$this->data["Special"] .= "<br>The 'shield' represents the extra two points of armor the Satyra."; 
		$this->data["Special"] .= "<br>have available aginst these weapon classes."; 
	}
	
} //endof class SatyraShield



class TestGun extends Weapon{
        public $trailColor = array(30, 170, 255);

        public $name = "TestGun";
        public $displayName = "Test Gun";
		public $iconPath = "tacLaser.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 0.25; //-1/4 hexes
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals
	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Laser"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Laser";
        }

        public function getDamage($fireOrder){ return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;      }
}// endof TestGun



class TestGun2 extends Weapon{
        public $trailColor = array(30, 170, 255);

        public $name = "TestGun2";
        public $displayName = "Test Gun 2";
		public $iconPath = "EmPulsar.png";
	    
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 10;
        public $loadingtime = 1;
        public $priority = 5;
        public $intercept = 2;

        public $rangePenalty = 0.25; //-1/4 hexes
        public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals

	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Electromagnetic";
        }

        public function getDamage($fireOrder){ return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;      }
}// endof TestGun2


class PlasmaSiegeCannon extends Weapon {

/*Heavy plasma projector and ranged fuser (must be speed zero) */
	public $name = "PlasmaSiegeCannon";
	public $displayName = "Plasma Siege Cannon";
	public $iconPath = "HeavyPlasmaProjector.png";
	
	public $animationArray = array(1=>'laser', 2=>'trail');
	public $animationColorArray = array(1=>array(75, 250, 90), 2=>array(75, 250, 90));

	//actual weapon data
	public $raking = 8; //only useful for Raking mode
	public $priorityArray = array(1=>7, 2=>2);
	public $loadingtimeArray = array(1=>4, 2=>4);  //mode 1 should be the one with longest loading time
	public $rangePenaltyArray = array(1=>0.33, 2=>0.25);
	public $rangeDamagePenaltyArray = array(1=>0.25, 2=>0.25);
	public $fireControlArray = array(1=>array(null, 2, 4), 2=>array(null, 3, 5));
	
	public $firingModes = array(1=>'Heavy Plasma Projector', 2=>'Siege Cannon');
	public $damageTypeArray = array(1=>'Raking', 2=>'Flash');
	public $weaponClassArray = array(1=>'Plasma', 2=>'Plasma');
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 11;
			if ( $powerReq == 0 ) $powerReq = 8;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Can fire as either Heavy Plasma Projector or Range Fuser (Siege).";
			$this->data["Special"] .= "<br>Damage reduced by 1 point per 4 hexes in either mode.";
			$this->data["Special"] .= "<br>Ignores half of armor.";
			$this->data["Special"] .= "<br>Must be speed 0 to fire in Siege Cannon mode.";
	}
	
	public function calculateHitBase($gamedata, $fireOrder){ //auto-miss if restrictions not met
		$this->changeFiringMode($fireOrder->firingMode);  //needs to be outside the switch routine
		switch($this->firingMode){
			case 1: //Heavy Plasma Projector, no restrictions
//				$canHit = true;
//				if($canHit){
					parent::calculateHitBase($gamedata, $fireOrder);
//				}
				break;
			case 2: //Siege Cannon, shooter speed 0 only
				$canHit = true;
				$pubnotes = '';
		
				$shooter = $gamedata->getShipById($fireOrder->shooterid);
		
				if($shooter->getSpeed()>0){ $canHit=false; $pubnotes.= ' Shooter speed >0. '; }
			
				if($canHit){
					parent::calculateHitBase($gamedata, $fireOrder);
				}else{ //accurate targeting with this weapon not possible!
					$fireOrder->needed = 0;
						$fireOrder->notes = 'FIRING SHIP NOT SPEED 0';
					$fireOrder->pubnotes .= $pubnotes;   
						$fireOrder->updated = true;
				}
				break;
		}
	}

    public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 5)+10; //Heavy Plasma Projector
				break;
			case 2:
				return Dice::d(10,6)+12; //Siege Cannon
				break;	
		}
	}
    public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 15; //Heavy Plasma Projector
				break;
			case 2:
				$this->minDamage = 18; //Ranged Fuser
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
    public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 60; //Heavy Plasma Projector
				break;
			case 2:
				$this->maxDamage = 72; //Ranged Fuser
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

}  //end class PlasmaSiegeCannon


    class ImpHeavyLaser extends Laser{
        public $name = "ImpHeavyLaser";
        public $displayName = "Improved Heavy Laser";
		public $iconPath = "heavyLaser.png";
        public $animation = "laser";
        public $animationColor = array(179, 45, 0);
        //public $animationExplosionScale = 0.5;
        //public $animationWidth = 4;
        //public $animationWidth2 = 0.2;

        public $loadingtime = 4;

        // Set to make the weapon start already overloaded.
        public $firingModes = array( 1 => "Sustained");
        public $alwaysoverloading = true;
        public $overloadturns = 2;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
        public $priority = 8;

        public $raking = 10;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 2, 3); // fighters, <mediums, <capitals 
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 9;
			if ( $powerReq == 0 ) $powerReq = 8;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);        
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
            $this->data["Special"] .= "This weapon is always in sustained mode.";
		}

        public function isOverloadingOnTurn($turn = null){
            return true;
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 4)+20;   }
        public function setMinDamage(){     $this->minDamage = 24 ;      }
        public function setMaxDamage(){     $this->maxDamage = 60 ;      }
        
        
    }



class DirectEMine extends Weapon{
        public $name = "DirectEMine";
        public $displayName = "Direct Energy Mine";
	    public $iconPath = "energyMine.png";

        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
        public $animationExplosionScale = 1;

        public $range = 50;

        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
		public $priority = 1; //Flash weapon
	    
//	public $firingMode = 'Called Shot'; //firing mode - just a name essentially
    	public $weaponClass = "Plasma"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
        public $damageType = "Flash"; 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
		}
        
        public function getDamage($fireOrder){
            return Dice::d(10, 3);
       }
    
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
		
}//endof DirectEMine


?>
