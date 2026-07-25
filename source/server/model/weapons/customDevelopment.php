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
    public $iconPath = "satyraShieldTechnical.png";
    public $boostable = false; //$this->boostEfficiency and $this->maxBoostLevel in __construct() 
    public $baseOutput = 0; //base output, before boost
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
	public $isTargetable = false; //cannot be targeted ever!
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value

	
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
		$this->data["Special"] = "Satyra armor is resistent to lasers and electromagnetic weapons."; 
		$this->data["Special"] .= "<br>This represents the extra two points of armor the Satyra"; 
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


/*
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
*/


    class DirectEMine extends Torpedo{

        public $name = "DirectEMine";
        public $displayName = "Direct Energy Mine";
	    public $iconPath = "energyMine.png";

        public $range = 50;
        public $loadingtime = 2;
        
        public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
        
        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
		
        public $priority = 1;

    	public $weaponClass = "Plasma"; 
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
    
    }//endof class DirectEMine



class AncientMatterGun extends Matter{
    	public $name = "AncientMatterGun";
        public $displayName = "Ancient Matter Gun";
		public $iconPath = "HeavyRailgun.png";

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    	public $rangeDamagePenalty = 0;
        public $damageType = "Standard"; 
	        
        public $loadingtime = 1;
			
        public $rangePenalty = 0.50;
        public $fireControl = array(5, 5, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 10 /*- $this->dp*/;      }

} //end of class AncientMatterGun


class AncientPlasmaGun extends Plasma{
    	public $name = "AncientPlasmaGun";
        public $displayName = "Ancient Plasma Gun";
		public $iconPath = "MegaPlasma.png";

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    	public $rangeDamagePenalty = 0;
        public $damageType = "Standard"; 
	        
        public $loadingtime = 1;
			
        public $rangePenalty = 0.50;
        public $fireControl = array(5, 5, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 10 /*- $this->dp*/;      }

} //end of class AncientMatterGun



class AncientParticleGun extends Particle{
    	public $name = "AncientParticleGun";
        public $displayName = "Ancient Particle Gun";
		public $iconPath = "particleBlaster.png";

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    	public $rangeDamagePenalty = 0;
        public $damageType = "Standard"; 
	        
        public $loadingtime = 1;
			
        public $rangePenalty = 0.50;
        public $fireControl = array(5, 5, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 10 /*- $this->dp*/;      }

} //end of class AncientMatterGun


class AncientParticleCannon extends Particle{
    	public $name = "AncientParticleCannon";
        public $displayName = "Ancient Particle Cannon";
		public $iconPath = "particleCannon.png";

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
		public $animation = "laser"; //originally Laser, but Bolt seems more appropriate

    	public $rangeDamagePenalty = 0;
        public $damageType = "Raking"; 
	        
        public $loadingtime = 1;
			
        public $rangePenalty = 0.50;
        public $fireControl = array(5, 5, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return 30;   }
        public function setMinDamage(){     $this->minDamage = 30 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 30 /*- $this->dp*/;      }

} //end of class AncientMatterGun



class AncientAntimatter extends Weapon{
    	public $name = "AncientAntimatter";
        public $displayName = "Ancient Antimatter";
		public $iconPath = "antimatterConverter.png";

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    	public $rangeDamagePenalty = 0;
		public $weaponClass = "Antimatter";
        public $damageType = "Flash"; 
	        
        public $loadingtime = 1;
			
        public $rangePenalty = 0.50;
        public $fireControl = array(5, 5, 5); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 10;
            if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return 40;   }
        public function setMinDamage(){     $this->minDamage = 40 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 40 /*- $this->dp*/;      }

} //end of class AncientMatterGun



    class AncientIonTorpedo extends Torpedo{
    
        public $name = "AncientIonTorpedo";
        public $displayName = "Ancient Ion Torpedo";
		public $iconPath = "ionTorpedo.png";

        public $range = 50;
        public $loadingtime = 1;
        
        public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
        
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
		/*
        public $trailColor = array(141, 240, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
		
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
		
        public $priority = 6;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 10 ;/*- $this->dp;*/      }
    
    }//endof class IonTorpedo



class AncientBurstBeam extends Weapon{
	public $name = "AncientBurstBeam";
	public $displayName = "Ancient Burst Beam";
	public $iconPath = "burstBeam.png";
	
	public $animation = "bolt"; //originally Laser, but Bolt seems more appropriate
	public $animationColor = array(158, 240, 255);
	public $animationExplosionScale = 0.30;
	/*
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	*/
	public $noOverkill = true;
	public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
		        
	public $loadingtime = 1;
	public $priority = 10; //as antiship weapon, going last
	public $priorityAFArray = array(1=>2); //as antifighter weapon, going very early
			
	public $rangePenalty = 2;
	public $fireControl = array(4, 2, 2); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	      
		$this->data["Special"] .= "Effect depends on system hit:";    
		$this->data["Special"] .= "<br> - Structure: Reactor output reduced by 1."; 
		$this->data["Special"] .= "<br> - Powered system: forced shutdown next turn."; 
		$this->data["Special"] .= "<br> - Other system: critical roll forced (at +4)."; 
		$this->data["Special"] .= "<br> - Fighter: immediate dropout (excluding superheavy)."; 
		$this->data["Special"] .= "<br>Automatically hits EM shield if interposed.";
		$this->data["Special"] .= "<br>Does not affect units protected by Advanced Armor.";  	
	}	
	
	//Burst Beams ignore armor; advanced armor halves effect (due to weapon being Electromagnetic)
	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
		if (WeaponEM::isTargetEMResistant($target,$system)){
			$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
			$returnArmour = floor($returnArmour/2);
			return $returnArmour;
		}else{
			return 0;
		}
	}
	
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){ //if target is protected by EM shield, that shield is hit automatically
		if($target instanceof FighterFlight){ //for fighters - regular allocation
			parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
			return;
		}
		
		//first - find bearing from target to firing ship (needed to determine whether shield interacts with incoming shot)
		$relativeBearing = $target->getBearingOnUnit($shooter);
		//are there any active EM shields affecting shot?
		$affectingShields = array();
		foreach($target->systems as $shield){
			if( ($shield instanceOf EMShield)  //this is an actual shield!
				&& (!$shield->isDestroyed()) //not destroyed
				&& (!$shield->isOfflineOnTurn($gamedata->turn)) //powered up
			   	&& (mathlib::isInArc($relativeBearing, $shield->startArc, $shield->endArc)) //actually in arc to affect
			) {
				$affectingShields[] = $shield;
			}
		}
		$countShields = count($affectingShields);
		if($countShields > 0){ //hit shield if active in arc and not destroyed (proceed to onDamagedSystem directly)
			//choose randomly from relevant shields
			$chosenID = Dice::d($countShields,1)-1; //array elements numeration starts at 0
			$shield = $affectingShields[$chosenID];			
			$this->onDamagedSystem($target, $shield, 0, 0, $gamedata, $fireOrder);
		} else { //otherwise hit normally (parent beforeDamage) (...for 0 damage...) , actual effect handled in onDamagedSystem 
			parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
			return;
		}
	}//endof function beforeDamage
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		
		if (!WeaponEM::isTargetEMResistant($ship,$system)){ //no effect at all vs Advanced Armor
			if ($system instanceof Fighter && !($ship->superheavy)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->setCritical($crit); //$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
				$crit->updated = true;
				$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
			}else if ($system->powerReq > 0 || $system->canOffLine ){
				$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			} else { //force critical roll at +4
				$system->forceCriticalRoll = true;
				$system->critRollMod += 4;
			}
		}
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);        
	}		
		
	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class BurstBeam




    class AncientMolecularDisruptor extends Raking
    {
        public $name = "AncientMolecularDisruptor";
        public $displayName = "Ancient Molecular Disruptor";
		public $iconPath = "molecularDisruptor.png";

        public $animation = "laser"; //it's Raking weapon after all
        public $animationColor = array(30, 170, 255);
	    /*
        public $trailColor = array(30, 170, 255);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 25;
	*/
        public $priority = 7;
        public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

        public $intercept = 0;
        public $loadingtime = 1;

        public $firingModes = array(
            1 => "Raking",
            2 => "Piercing"
        );

        public $rangePenalty = 1;

        public $fireControlArray = array( 1=>array(-4, 2, 4), 2=>array(null, -2, 0) ); //Raking and Piercing mode, respectively - Piercing adds -4!
        //public $fireControl = $this->fireControlArray[1];  // fighters, <mediums, <capitals
        //private $damagebonus = 30;

        public $damageType = "Raking"; 
        public $damageTypeArray = array(1=>'Raking', 2=>'Piercing');
        public $weaponClass = "Molecular"; 
                        
        private $alreadyReduced = false;
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
            $this->data["Special"] .= "Reduces armor of facing structure.";
        }

        protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
            parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
			if ($system->advancedArmor) return; //advanced armor prevents effect 
//			if ($system->hardAdvancedArmor) return; //hardened advanced armor prevents effect - GTS
            if(!$this->alreadyReduced){ 
                //$struct = $target->getStructureSystem($location); //this caused problems if first rake penetrated!
				$sectionFacing = $target->getHitSection($shooter, $fireOrder->turn);
				$struct = $target->getStructureSystem($sectionFacing); 
                if ($struct->advancedArmor) return; //advanced armor prevents effect 
//                if ($struct->hardAdvancedArmor) return; //advanced armor prevents effect 
                if(!$struct->isDestroyed($fireOrder->turn-1)){ //last turn Structure was still there...
                    $this->alreadyReduced = true; //do this only for first part of shot that actually connects
                    $crit = new ArmorReduced(-1, $target->id, $struct->id, "ArmorReduced", $gamedata->turn);
                    $crit->updated = true;
                    $crit->inEffect = false;
                    $struct->criticals[] = $crit;
                }
            }
        }       

        public function getDamage($fireOrder){        return 20;   }
        public function setMinDamage(){     $this->minDamage = 20;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    } //endof class MolecularDisruptor



class AncientShockCannon extends Weapon{
        public $name = "AncientShockCannon";
        public $displayName = "Ancient Shock Cannon";
		public $iconPath = "shockCannon.png";
	
        public $animation = "bolt"; //originally Laser, but Bolt seems more appropriate
        public $animationColor = array(175, 225, 175);
        public $animationExplosionScale = 0.35; //will be rescaled automatically, too
	/*
        public $trailColor = array(175, 225, 175);
        public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        public $animationExplosionScale = 0.15;
        public $trailLength = 30;
	*/
		public $priority = 4; //as antiship weapon, going early - actual damage is only to systems, and with armor ignoring it's worth dealing - but also as armor ignoring should let actual very light weapons go first
		public $priorityAFArray = array(1=>2); //as antifighter weapon, going very early - instant dropout

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

        public $loadingtime = 1;

        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 3); // fighters, <=mediums, <=capitals

		public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		$this->animationExplosionScale = $this->dynamicScale(0,2);//scale weapon using double damage output - due to additional effects it seems appropriate
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn); 
				if (!isset($this->data["Special"])) {
					$this->data["Special"] = '';
				}else{
					$this->data["Special"] .= '<br>';
				}	    
		      $this->data["Special"] .= "Ignores armor. Forces dropout on fighters.";  
		      $this->data["Special"] .= "<br>Structure hits reduce power output by 1 per 4 dmg rolled (but do no actual damage).";  
        }

        //ignore armor; advanced armor halves effect (due to this weapon being Electromagnetic)
        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
			if (WeaponEM::isTargetEMResistant($target,$system)){
				$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
				$returnArmour = floor($returnArmour/2);
				return $returnArmour;
			}else{
				return 0;
			}
		}

		public function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			$dmgToReturn = $damage;
			if ($system instanceof Structure){
				$dmgToReturn = 0; //no Structure damage
				if (!WeaponEM::isTargetEMResistant($ship,$system)){ //advanced armor prevents non-damaging EM effects
					$reactor = $ship->getSystemByName("Reactor");
					$outputMod = -floor($damage/4);
					//modifying how the critical is applied - Marcin Sawicki 06.06.2023
					while($outputMod<=-4){
						$crit = new OutputReduced4(-1, $ship->id, $reactor->id, "OutputReduced4", $gamedata->turn);
						$crit->updated = true;
						$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
						$outputMod += 4;
					}
					while($outputMod<=-3){
						$crit = new OutputReduced3(-1, $ship->id, $reactor->id, "OutputReduced3", $gamedata->turn);
						$crit->updated = true;
						$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
						$outputMod += 3;
					}
					while($outputMod<=-2){
						$crit = new OutputReduced2(-1, $ship->id, $reactor->id, "OutputReduced2", $gamedata->turn);
						$crit->updated = true;
						$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
						$outputMod += 2;
					}
					while($outputMod<=-1){
						$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
						$crit->updated = true;
						$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
						$outputMod += 1;
					}					
					/* original version
					if($outputMod < 0){
						$crit = new OutputReduced(-1, $ship->id, $reactor->id, "OutputReduced", $gamedata->turn, $outputMod);
						$crit->updated = true;
						$reactor->criticals[] =  $crit;
					}
					*/
				}
			}
			return $dmgToReturn;
		}

        public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
			//effects on Structure hits already handled by beforeDamagedSystem
			if (!WeaponEM::isTargetEMResistant($ship,$system)){ //advanced armor prevents non-damaging EM effects
				$crit = null;
				if ($system instanceof Fighter && !($ship->superheavy)){
					$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
					$crit->updated = true;
					$crit->inEffect = true;
					$system->setCritical($crit); //$system->criticals[] =  $crit;
					$fireOrder->pubnotes .= " DROPOUT! ";
				}
			}
            parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
	}//endof function onDamagedSystem

	public function getDamage($fireOrder){        return 10;   }
	public function setMinDamage(){     $this->minDamage = 10 /*- $this->dp*/;      }
	public function setMaxDamage(){     $this->maxDamage = 10 /*- $this->dp*/;      }
}//endof class ShockCannon


class AncientPlasmaArc extends PlasmaStream {

	public $name = "AncientPlasmaArc";
	public $displayName = "Ancient Plasma Arc";
    public $iconPath = "EWPlasmaArc.png";
	
	public $animation = "laser";
	public $priority = 1; //early, due to armor reduction effect
    public $loadingtime = 1;
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    public $priorityArray = array(1=>7, 2=>2); //Piercing shots go early, to do damage while sections aren't detroyed yet!
		        
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 5;
		if ( $powerReq == 0 ) $powerReq = 4;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
        
	public function getDamage($fireOrder){        return 15;   }
	public function setMinDamage(){     $this->minDamage = 15 ;      }
	public function setMaxDamage(){     $this->maxDamage = 15 ;      }
	
}




    class AncientParticleCutter extends Raking{
        public $name = "AncientParticleCutter";
        public $displayName = "Ancient Particle Cutter";
		public $iconPath = "particleCutter.png";
		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
	    
		public $animation = "laser";
        public $animationColor = array(255, 153, 102);
	    /*
        public $trailColor = array(255, 153, 102);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 3;
        public $animationWidth2 = 0.3;
	    */
        public $firingModes = array( 1 => "Sustained");
        
        public $damageType = "Raking"; 
        public $weaponClass = "Particle";
        
        // Set to make the weapon start already overloaded.
        public $alwaysoverloading = true;
        public $overloadturns = 2;
        public $extraoverloadshots = 2;
        public $overloadshots = 2;
        public $loadingtime = 2;
        public $priority = 8;

        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

        private $sustainedTarget = array(); //To track for next turn which ship was fired at in Sustained Mode and whether it was hit.
        private $sustainedSystemsHit = array(); //For tracking systems that were hit and how much armour they should be reduced by following turn if hit again. 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){			
            parent::setSystemDataWindow($turn);        
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
            $this->data["Special"] .= 'This weapon is always in sustained mode.';
            $this->data["Special"] .= '<br>As a Sustained weapon, if the first shot hits then the next turns shot will hit automatically.';
            $this->data["Special"] .= '<br>Subsequent Sustained shots ignore any armour/shields that have applied to previous shots.';                                                               
		}                            


        public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder) {
            if (
                $this->isOverloadingOnTurn($gamedata->turn) &&
                isset($this->sustainedTarget[$fireOrder->targetid]) &&
                $this->sustainedTarget[$fireOrder->targetid] == 1
            ) {
                $fireOrder->needed = 100; // Auto-hit!
                $fireOrder->updated = true;
                $this->uninterceptable = true;
                $this->doNotIntercept = true;
                $fireOrder->pubnotes .= " Sustained shot automatically hits.";
        
                return;
            }
        
            parent::calculateHitBase($gamedata, $fireOrder); // Default routine if not an auto-hit.
        }
 

        public function generateIndividualNotes($gameData, $dbManager) {
            switch($gameData->phase) {
                case 4: // Post-Firing phase
                    $firingOrders = $this->getFireOrders($gameData->turn); // Get fire orders for this turn
                    if (!$firingOrders) {
                        break; // No fire orders, nothing to process
                    }

                    $ship = $this->getUnit(); // Ensure ship is defined before use

                    if($this->isDestroyed() || $ship->isDestroyed()) break;                    
        
                    foreach ($firingOrders as $firingOrder) { //Should only be 1.
                        $didShotHit = $firingOrder->shotshit; //1 or 0 depending on hit or miss.
                        $targetid = $firingOrder->targetid;

                        // Check for sustained mode condition
                        if ($this->isOverloadingOnTurn($gameData->turn) && $this->loadingtime <= $this->overloadturns) {
                            if (($this->overloadshots - 1) > 0) { // Ensure not the last sustained shot
                                $notekey = 'targetinfo';
                                $noteHuman = 'ID of Target fired at';
                                $notevalue = $targetid . ';' . $didShotHit;
                                $this->individualNotes[] = new IndividualNote(
                                    -1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
                                    $ship->id, $this->id, $notekey, $noteHuman, $notevalue
                                );
                            }
                        
         
                            if ($didShotHit == 0) {
                                continue; // Shot missed, no need to track damage
                            }
        
                            // Process damage to target systems
                            $target = $gameData->getShipById($targetid);
                            if (!$target || !is_array($target->systems) || empty($target->systems)) {
                                continue; // Ensure valid target and systems exist
                            }

                            foreach ($target->systems as $system) {
                                $systemDamageThisTurn = 0;
                                $notes = 0; // Tracks how much armor should be ignored next turn
        
                                foreach ($system->damage as $damage) {
                                
                                    if ($damage->turn == $gameData->turn){  // Only consider this turn’s damage
                                    
                                        if ($damage->shooterid == $ship->id && $damage->weaponid == $this->id) {

                                            $systemDamageThisTurn += $damage->damage; // Accumulate total damage dealt this turn
                                        }
                                    }
                                }
                
                                if ($systemDamageThisTurn > 0) { // Ensure damage was dealt
                                    if ($systemDamageThisTurn >= $system->armour) {
                                        $notes = $system->armour; // All armor used up
                                    } else {
                                        $notes = $systemDamageThisTurn; // Partial armor penetration
                                    }
            
                                    // Create note(s) for armor ignored next turn
                                    while ($notes > 0) {
                                        $notekey = 'systeminfo';
                                        $noteHuman = 'ID of System fired at';
                                        $notevalue = $system->id;
                                        $this->individualNotes[] = new IndividualNote(
                                            -1, TacGamedata::$currentGameID, $gameData->turn, $gameData->phase,
                                            $ship->id, $this->id, $notekey, $noteHuman, $notevalue
                                        );
                                        $notes--;
                                    }
                                }
                            }    
                        }
                    }
                    break;
            }
        } // end of function generateIndividualNotes


        public function onIndividualNotesLoaded($gamedata)
        {
            // Process rearrangements made by player					
            foreach ($this->individualNotes as $currNote) {
                if ($currNote->turn == $gamedata->turn - 1) { // Only interested in last turn’s notes               
                    if ($currNote->notekey == 'targetinfo') {
                        if (strpos($currNote->notevalue, ';') === false) {
                            continue; // Skip notes with invalid format
                        }
        
                        $explodedValue = explode(';', $currNote->notevalue);
                        if (count($explodedValue) === 2) { // Ensure correct format
                            $targetId = $explodedValue[0];
                            $didHit = $explodedValue[1];
        
                            $this->sustainedTarget[$targetId] = $didHit; // Store target ID and hit status
                        }
                    }
            
                    // Process armor reductions
                    if ($currNote->notekey == 'systeminfo') {
                        $this->sustainedSystemsHit[] = $currNote->notevalue; // Store system ID
                    }    
                }
            }				

            //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
            $this->individualNotes = array();
                   
        }//endof onIndividualNotesLoaded               

        //Called from core firing routines to check if any armour should be bypassed by a sustained shot.
        public function getsustainedSystemsHit()
        {
            if(!empty($this->sustainedSystemsHit) && is_array($this->sustainedSystemsHit)){
                return $this->sustainedSystemsHit; 
            } else{
                return null;
            }
        }    

        // Sustained shots only apply shield damage reduction once.
        public function shieldInteractionDamage($target, $shooter, $pos, $turn, $shield, $mod) {
            $toReturn = max(0, $mod);
         
            // Ensure sustainedTarget is set and not an empty array before checking its keys
            if (!empty($this->sustainedTarget) && is_array($this->sustainedTarget) && array_key_exists($target->id, $this->sustainedTarget)) {
                $toReturn = 0;
            }
               
            return $toReturn;
        }

        public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->sustainedTarget = $this->sustainedTarget;	//Needed for front end hit calculation                      			
			return $strippedSystem;
		}    

        public function isOverloadingOnTurn($turn = null){
            return true;
        }  

        public function getDamage($fireOrder){ return 30 ;  }
        public function setMinDamage(){     $this->minDamage = 30 ;      }
        public function setMaxDamage(){     $this->maxDamage = 30 ;      }

    }//endof AncientParticleCutter


/* The System primary weapon */
class NeutronBlaster extends Weapon{
	public $name = "NeutronBlaster";
	public $displayName = "Neutron Blaster";
	public $iconPath = "NeutronBlaster.png";
	
	public $animation = "laser";
	public $animationColor = array(98, 127, 82);
 
    public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
 
  	public $gunsArray = array(1=>2, 2=>1, 3=>1); // mode 1: fires twice per blaster (uncombined); modes 2/3: single combined shot
 
	//technical variables for combined shot
	public $isCombined = false;
	public $alreadyConsidered = false;
	
	public $loadingtime = 1;
	
	public $uninterceptable = true; //Neutron Blaster is uninterceptable
	public $intercept = 3; //intercept rating -3
	public $modeLetters = 1;
	public $modeLettersArray = array(
		1 => 1,
		2 => 1,
		3 => 1,
	);
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "1-Blaster",
		2 => "2-Blasters",
		3 => "3-Blasters",
	);
	
	public $priority = 6; 
	public $priorityArray = array(1=>4, 2=>7, 3=>8); 
    public $rangePenalty = 0.5; 
	public $rangePenaltyArray = array(1=>0.5, 2=>0.33, 3=>0.25);
	public $fireControl = array(6, 1, 1); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(6, 3, 3), 2=>array(3,5,5), 3=>array(null,6,6)); 
 
	//number of blasters required to fire per mode (mode 2 needs 2 combined, mode 3 needs 3 combined)
	public $blastersRequiredArray = array( 1=>1, 2=>2, 3=>3 );
 
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array( 1=>"Standard", 2=>"Raking", 3=>"Raking");
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	public $weaponClassArray = array(1=>'Electromagnetic', 2=>'Electromagnetic', 3=>'Electromagnetic');
    public $canSplitShots = false; //Allows Firing Mode 1 to split shots.
    public $canSplitShotsArray = array(1=>true, 2=>false, 3=>false );          
	
	//rake size array
	public $raking = 15;//more in higher modes
	public $rakingArray = array( 1=>15, 2=>15, 3=>20 );
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc )
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 15;
		}
		if ( $powerReq == 0 ){
			$powerReq = 8;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
 
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    		
		$this->data["Special"] .= "Uninterceptable. Capable of multiple modes of fire. Higher modes require combining multiple blasters on the same target.";   
		$this->data["Special"] .= "<br>Firing modes available (Number of blasters per shot/damage output (and mode)/range penalty):";  
		$this->data["Special"] .= "<br> - 1 Blaster: 1d10+10 Standard - 2 shots, -2.5/hex"; 
		$this->data["Special"] .= "<br> - 2 Blasters: 4d10+20 Raking(15), -1.65/hex";
		$this->data["Special"] .= "<br> - 3 Blasters: 5d10+30 Raking(20), -1.25/hex"; 
		$this->data["Special"] .= "<br>If weapon is mis-declared (shot is declared but not enough blasters are allocated in appropriate mode) shot will automatically miss."; 
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";		
	}
 
	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+10; // fired individually, twice per blaster
			case 2:
				return Dice::d(10, 4)+20; // 1 shot from 2 combined blasters
			case 3:	
				return Dice::d(10, 5)+30; // 1 shot from 3 combined blasters
			default: //should never go here
				return Dice::d(10, 1)+10;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 11; 
				break;
			case 2:
				$this->minDamage = 24; 
				break;
			case 3:
				$this->minDamage = 35; 
				break;
			default: //should never go here
				$this->minDamage = 11;
				break;
		}
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 20; 
				break;
			case 2:
				$this->maxDamage = 60; 
				break;
			case 3:
				$this->maxDamage = 80; 
				break;
			default: //should never go here
				$this->maxDamage = 20;
				break;
		}
	}
	
	//hit chance calculation is standard - no power drain (unlike the weapon this was based on)
	//if already combining - do not fire at all (set hit chance at 0, uninterceptable, zero shots)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->changeFiringMode($fireOrder->firingMode);
		$doCalculate = true;
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0;
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->doNotIntercept = true;
			return;
		}
		
		$blastersNeeded = $this->blastersRequiredArray[$fireOrder->firingMode]; 
 
		if ($blastersNeeded < 2){ //nothing extra is needed, do fire!
			$doCalculate = true;
		} else {//additional blasters needed!
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Neutron Blaster?...
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if ($subWeapon instanceof NeutronBlaster){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($blastersNeeded-1)) break;//enough subordinate weapons found! - exit loop
			}						
			if ($subordinateOrdersNo == ($blastersNeeded-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}				
				$doCalculate = true;
			}else{//not enough weapons to combine in this mode - mark technical and don't fire
				$notes = "technical fire order - weapon mis-declared";
				$fireOrder->chosenLocation = 0;
				$fireOrder->needed = 0;
				$fireOrder->shots = 0;
				$fireOrder->notes = $notes;
				$fireOrder->updated = true;
				$this->doNotIntercept = true;
				$doCalculate = false;
			}
		}
		
		if($doCalculate){
			parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation, no power drain
		}
	}//endof function calculateHitBase
 
}//endof class NeutronBlaster




class NeutronBlasterFtr extends Weapon{ 

		public $name = "NeutronBlasterFtr";
		public $displayName = "Light Neutron Blaster";
		public $iconPath = "NeutronBlaster.png";
	
		//visual display 
		public $animationArray = array(1=>'laser', 2=>'laser', 3=>'laser');
		public $animationColorArray = array(1=>array(98, 127, 82), 2=>array(98, 127, 82), 3=>array(98, 127, 82));
	
		public $factionAge = 3; //Ancient
	
		//actual weapons data
		public $priorityArray = array(1=>7, 2=>5, 3=>3);
		public $uninterceptableArray = array(1=>true, 2=>true, 3=>true);
		public $defaultShotsArray = array(1=>1, 2=>1, 3=>2); 
	
		public $loadingtimeArray = array(1=>3, 2=>2, 3=>1); //mode 1 should be the one with longest loading time
		public $rangePenaltyArray = array(1=>1, 2=>1.5, 3=>2);
		public $fireControlArray = array( 1=>array(null, 0, 0), 2=>array(-2, 0, 0), 3=>array(0, 0, 0) ); // fighters, <mediums, <capitals 
	
		public $firingModes = array(1=>'Heavy', 2=>'Medium', 3=>'Rapid');
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard', 3=>'Standard'); //indicates that this weapon does damage in Pulse mode
		public $weaponClassArray = array(1=>'Electromagnetic', 2=>'Electromagnetic', 3=>'Electromagnetic'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
		public $intercept = 2; //technically only Pulse Cannon can intercept, but entire weapon is fired anyway - so it affects visuals only, and mode 1 should be the one with interception for technical reasons
	
        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
        public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = 'Can fire in three modes depending on the turns charged. ';
			$this->data["Special"] .= "<br>Rapid Fire: 1 turn, 2 shots of 1d6+6 damage, -2 per hex";  
			$this->data["Special"] .= "<br>Medium Charge: 2 turns, 1 shot of 2d6+9 damage, -3 per 2 hexes";  
			$this->data["Special"] .= "<br>Heavy Charge: 3 turns, 1 shot of 4d6+12 damage, -1 per hex";  
        }
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(6, 4)+12; //Heavy Charge
				break;
			case 2:
				return Dice::d(6, 2)+9; //Medium Charge
				break;
			case 3:
				return Dice::d(6, 1)+6; //Rapid Fire
				break;
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 16; //Heavy Charge
				break;
			case 2:
				$this->minDamage = 11; //Medium charge
				break;	
			case 3:
				$this->minDamage = 7; //Rapid Fire
				break;
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 36; //Heavy Charge
				break;
			case 2:
				$this->maxDamage = 21; //Medium charge
				break;	
			case 3:
				$this->maxDamage = 12; //Rapid Fire
				break;
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
} //endof class NeutronBlasterFtr



    class FusionBomb extends Torpedo{
        public $name = "FusionBomb";
        public $displayName = "Fusion Bomb";
        public $iconPath = "EWNuclearTorpedo.png";
        public $range = 10;
        public $distanceRange = 20;
        public $loadingtime = 3;

		public $factionAge = 3; //Ancient
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        public $fireControl = array(null, 2, 3); // fighters, <mediums, <capitals 
        
        public $trailColor = array(206, 32, 41);
        public $animation = "trail";
        public $animationColor = array(206, 32, 41);
        public $animationExplosionScale = 0.7;
        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 1; //Flash! should strike first (?)
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
		}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 4) + 20;   }
        public function setMinDamage(){     $this->minDamage = 24;      }
        public function setMaxDamage(){     $this->maxDamage = 60;      }
    
    }//endof class FusionBomb


    class SeekerTorp extends Torpedo{
        public $name = "SeekerTorp";
        public $displayName = "Seeker Torpedo";
        public $iconPath = "TrekPhotonicTorpedo.png";
        public $range = 50;
        public $distanceRange = 65;
        public $loadingtime = 2;

		public $factionAge = 3; //Ancient
        
        public $weaponClass = "Ballistic"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Standard"; 
        
        public $fireControl = array(null, 2, 3); // fighters, <mediums, <capitals 
        
        public $trailColor = array(98, 127, 82);
        public $animation = "ball";
        public $animationColor = array(98, 127, 82);
        public $animationExplosionScale = 0.7;
        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
        public $priority = 4; 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}

		}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 2);   }
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 20;      }
    
    }//endof class Seeker





class PlasmaDriver extends Pulse{
        public $name = "PlasmaDriver";
        public $displayName = "Plasma Driver";
		public $iconPath = "PlasmaDriver.png";

        public $animation = "bolt";
        public $animationColor = array(75, 250, 90);

        public $grouping = 15;
        public $maxpulses = 5;
        public $priority = 6;
		protected $useDie = 3; //die used for base number of hits	
        
        public $loadingtime = 1;
        public $intercept = 2;
        
        public $rangePenalty = 0.5;
    	public $rangeDamagePenalty = 0.5;
        public $fireControl = array(6, 4, 3); // fighters, <mediums, <capitals 

	    public $damageType = "Pulse"; 
	    public $weaponClass = "Plasma"; 
        
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc )
		{
			//maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ){
				$maxhealth = 6;
			}
			if ( $powerReq == 0 ){
				$powerReq = 6;
			}
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);   
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}	    		
				$this->data["Special"] .= "Does less damage over distance (0.5 per hex).";   
		$this->data["Special"] .= "<br>Ignores half of armor.";  
		}

        public function getDamage($fireOrder){        return 22;   }
		
    }  // end of class PlasmaDriver









?>
