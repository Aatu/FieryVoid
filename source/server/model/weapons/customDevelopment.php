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
 
  	public $gunsArray = array(1=>1, 2=>1, 3=>1); // mode 1: fires twice per blaster (uncombined); modes 2/3: single combined shot
 
	//technical variables for combined shot
	public $isCombined = false;
	public $alreadyConsidered = false;
	
	public $loadingtime = 1;
	public $normalload = 3; //lets $turnsloaded climb as high as 3, so Mode 3's 3-turn cooldown can be checked
	
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
	public $fireControlArray = array( 1=>array(6, 4, 2), 2=>array(4,4,4), 3=>array(null,6,6)); 
 
	//number of blasters required to fire per mode (mode 2 needs 2 combined, mode 3 needs 3 combined)
	public $blastersRequiredArray = array( 1=>1, 2=>2, 3=>3 );
 
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array( 1=>"Standard", 2=>"Raking", 3=>"Raking");
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	public $weaponClassArray = array(1=>'Electromagnetic', 2=>'Electromagnetic', 3=>'Electromagnetic');
    public $canSplitShots = false; //not a real "split" - only used to route Modes 2/3 through doMultipleFireOrders, where the combine readiness gate lives
    public $canSplitShotsArray = array(1=>false, 2=>true, 3=>true );          
	
	//rake size array
	public $raking = 15;//more in higher modes
	public $rakingArray = array( 1=>10, 2=>15, 3=>20 );
	
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
		$this->data["Special"] .= "<br> - 1 Blaster: 1d10+10 Raking (10) - 2 shots, -2.5/hex"; 
		$this->data["Special"] .= "<br> - 2 Blasters: 3d110+30 Raking(15), -1.65/hex";
		$this->data["Special"] .= "<br> - 3 Blasters: 7d10+70 Raking(20), -1.25/hex"; 
		$this->data["Special"] .= "<br>Mode 1 fires every turn. Mode 2 requires the 2 combining blasters be held unfired for 1 turn first (fires every other turn). Mode 3 requires the 3 combining blasters be held unfired for 2 turns first (fires every third turn).";
		$this->data["Special"] .= "<br>If weapon is mis-declared (shot is declared but not enough blasters are allocated in appropriate mode) shot will automatically miss."; 
		$this->data["Special"] .= "<br>If weapon is insufficient number of charged blasters for the selected mode, the blaster will not committ to firing."; 
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";		
	}
 
	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+10; // fired individually, twice per blaster
			case 2:
				return Dice::d(10, 3)+30; // 1 shot from 2 combined blasters
			case 3:	
				return Dice::d(10, 7)+70; // 1 shot from 3 combined blasters
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
				$this->minDamage = 33; 
				break;
			case 3:
				$this->minDamage = 77; 
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
				$this->maxDamage = 140; 
				break;
			default: //should never go here
				$this->maxDamage = 20;
				break;
		}
	}

	/* Combine readiness gate: this blaster and every prospective combining partner must all
	 * have turnsloaded >= blastersNeeded - i.e. the more blasters a mode needs, the longer
	 * they must sit unfired first (Mode 2 needs 2 turns' worth of charge, Mode 3 needs 3).
	 * Uses the engine's own $turnsloaded counter (the same property HypergravitonBlaster
	 * reads directly for its load-based damage bonus) rather than reconstructing firing
	 * history ourselves. */
	protected function readyToCombine($gamedata, $firingShip, $subordinateOrders, $blastersNeeded){
		if ($this->turnsloaded < $blastersNeeded) return false;
		foreach ($subordinateOrders as $subOrder){
			$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
			if ($subWeapon->turnsloaded < $blastersNeeded) return false;
		}
		return true;
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
			if ($subordinateOrdersNo == ($blastersNeeded-1)){ //enough blasters found - but they also need to have been held ready long enough
				$readyToCombine = $this->readyToCombine($gamedata, $firingShip, $subordinateOrders, $blastersNeeded);

				if ($readyToCombine){ //combining - set other combining weapons/fire orders to technical status!
					foreach($subordinateOrders as $subOrder){
						$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
						$subWeapon->isCombined = true;
						$subWeapon->alreadyConsidered = true;
						$subWeapon->doNotIntercept = true;
					}				
					$doCalculate = true;
				}else{//blasters found, but not all held ready long enough - mark technical and don't fire
					$notes = "technical fire order - weapon mis-declared (all combining blasters must be held unfired long enough)";
					$fireOrder->chosenLocation = 0;
					$fireOrder->needed = 0;
					$fireOrder->shots = 0;
					$fireOrder->notes = $notes;
					$fireOrder->updated = true;
					$this->doNotIntercept = true;
					$doCalculate = false;
				}
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


class NeutronBeam extends Laser{
		public $name = "NeutronBeam";
        public $displayName = "Neutron Beam";
        public $iconPath = "NeutronBlaster.png";
        public $animation = "laser";
        public $animationColor = array(98, 127, 82); 
        
        public $loadingtime = 2;
		public $normalload = 3;
		public $raking = 15;
		public $priority = 8; //heavy Raking	

		public $factionAge = 3; //Ancient
		
        public $rangePenalty = 0.33; //-1 per 3 hexes
        public $fireControl = array(0, 3, 6);

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){ //maxhealth and power reqirement are fixed; left option to override with hand-written values
			if ( $maxhealth == 0 ) $maxhealth = 14;
			if ( $powerReq == 0 ) $powerReq = 8;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	    public $weaponClass = "Electromagnetic"; 
	    
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn); 
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}	    		  
			$this->data["Special"] .= "Can fire at accelerated RoF for less damage:";  
			$this->data["Special"] .= "<br> - 1 per 2 turns: 5d10+5"; 
			$this->data["Special"] .= "<br> - 1 per 3 turns: 6d10+10"; 
		}
	
		public function getDamage($fireOrder){
        	switch($this->turnsloaded){
            	case 0:
            	case 1: 
            	case 2:
                	return Dice::d(10,5)+5;
					break;
			    default:
			    	return Dice::d(10,6)+10;
					break;			
        	}
		}

        public function setMinDamage(){
            switch($this->turnsloaded){
            	case 1:
            	case 2:
                    $this->minDamage = 10 ;
                    break;
                default:
                    $this->minDamage = 16 ;  
                    break;
            }
		}
             
        public function setMaxDamage(){
            switch($this->turnsloaded){
                case 1:
                case 2:
                    $this->maxDamage = 55 ;
                    break;
                default:
                    $this->maxDamage = 70 ;  
                    break;
            }
		}

		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;				
			return $strippedSystem;
		}

} // End of class NeutronBeam


class NeutronCannon extends Laser{
    	public $name = "NeutronCannon";
        public $displayName = "Neutron Cannon";
		public $iconPath = "NeutronCannon.png";
        public $animation = "laser";
        public $animationColor = array(98, 127, 82);
		public $raking = 15;
        public $priority = 8;		

		public $factionAge = 3; //Ancient

        public $loadingtime = 3;
			
        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 2, 4); // fighters, <=mediums, <=capitals 

	    public $weaponClass = "Electromagnetic"; 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 6;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return Dice::d(10, 7)+20;   }
        public function setMinDamage(){     $this->minDamage = 27 ;      }
        public function setMaxDamage(){     $this->maxDamage = 90 ;      }

}  // endof NeutronCannon



class PlasmaArray extends Plasma{
    	public $name = "PlasmaArray";
        public $displayName = "Plasma Array";
		public $iconPath = "EWHeavyPointPlasmaGun.png";
        public $animation = "trail";
        public $animationColor = array(75, 250, 90);
    	public $trailColor = array(75, 250, 90);
    	public $projectilespeed = 15;
        public $animationWidth = 5;
    	public $animationExplosionScale = 0.30;
    	public $trailLength = 20;
        public $priority = 5;		
    	public $rangeDamagePenalty = 0.5;
        public $guns = 2;

		public $factionAge = 3; //Ancient

        public $intercept = 1;
    		        
        public $loadingtime = 1;
			
        public $rangePenalty = 0.5;
        public $fireControl = array(6, 4, 3); // fighters, <=mediums, <=capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 8;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
    	public function getDamage($fireOrder){        return Dice::d(10, 1)+10;   }
        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }

}  // endof PlasmaArray



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

// GTS_Triad

class AsteroidSalvo extends AoE {

    public $name = "AsteroidSalvo";
    public $displayName = "Asteroid Salvo";
    public $iconPath = "AsteroidSalvo.png";

//    public $damageType = "Standard";
    public $weaponClass = "Matter";
    public $flashDamage = true;

    public $hextarget = true;
    public $hidetarget = true;
    public $ballistic = true;
    public $uninterceptable = true;

	public $factionAge = 4; //Primordial

    public $range = 50;
    public $loadingtime = 2;
    public $priority = 1;

    public $animation = "ball";
    public $animationColor = array(150, 100, 50);
    public $animationExplosionScale = 2;
    public $animationExplosionType = "AoE";
    public $explosionColor = array(150, 100, 50);

    //Classes that will be spawned on successful hit.
    public $spawnableClasses = array(
        'spawnAsteroidSalvo',
        'spawnMeteoroid',
        'spawnDustField',
    );

    public $firingModes = array(1 => "Asteroid Salvo");

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        if ($maxhealth == 0) $maxhealth = 30;
        if ($powerReq == 0) $powerReq = 10;
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        if (!isset($this->data["Special"])) $this->data["Special"] = '';
        else $this->data["Special"] .= '<br>';
        $this->data["Special"] .= "Ballistic hex-targeted weapon. Matter damage: 50 at target hex, 20 at range 1, 10 at range 2.";
        $this->data["Special"] .= "<br>25% chance to scatter up to d6 hexes. 40% chance of that scatter to dissipate.";
        $this->data["Special"] .= "<br>On clean hit: spawns an asteroid at target hex, meteoroid fields at range 1, dust fields at range 2 (from next turn onwards).";
        $this->data["Special"] .= "<br>Terrain stacking: asteroid > meteoroid > dust. Existing asteroid/meteoroid is not replaced.";
    }

    public function getDamage($fireOrder) { return 50; }
    public function setMinDamage() { $this->minDamage = 10; }
    public function setMaxDamage() { $this->maxDamage = 50; }

    public function fire($gamedata, $fireOrder) {
        $this->changeFiringMode($fireOrder->firingMode);
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $posLaunch = $movement->position;

        //Handle unit-targeted fire orders (correct to hex)
        if ($fireOrder->targetid != -1) {
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            $movement = $targetship->getLastTurnMovement($fireOrder->turn);
            $fireOrder->x = $movement->position->q;
            $fireOrder->y = $movement->position->r;
            $fireOrder->targetid = -1;
        }

        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);

        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled;

        if ($rolled > $fireOrder->needed) {
            $fireOrder->pubnotes .= "Charge dissipates. ";
            return;
        }

        $fireOrder->shotshit++;
        $scattered = false;

        if ($rolled > 75) { //scatter
            $maxdis = $posLaunch->distanceTo($target);
            $dis = Dice::d(6);
            $dis = min($dis, floor($maxdis));
            $direction = Dice::d(6) - 1;
            $target = $target->moveToDirection($direction, $dis);

            $fireOrder->pubnotes .= " Deviation from " . $fireOrder->x . ' ' . $fireOrder->y;
            $fireOrder->x = $target->q;
            $fireOrder->y = $target->r;
            $fireOrder->pubnotes .= " to " . $fireOrder->x . ' ' . $fireOrder->y . '. ';
            $fireOrder->pubnotes .= "Shot deviates $dis hexes. ";
            $scattered = true;
        }

        //Apply AoE matter damage: 50/20/10 at ranges 0/1/2
        $damageByRange = array(0 => 50, 1 => 20, 2 => 10);

        $ships0 = $gamedata->getShipsInDistance($target, 0);
        $ships1 = $gamedata->getShipsInDistance($target, 1);
        $ships2 = $gamedata->getShipsInDistance($target, 2);

        foreach ($ships2 as $targetShip) {
            if ($targetShip->isDestroyed()) continue;
            if ($targetShip->mine) continue;
            if ($targetShip->isTerrain()) continue;

            if (isset($ships0[$targetShip->id])) {
                $damage = $damageByRange[0];
            } else if (isset($ships1[$targetShip->id])) {
                $damage = $damageByRange[1];
            } else {
                $damage = $damageByRange[2];
            }

            $this->AOEdamage($targetShip, $shooter, $fireOrder, $target, $damage, $gamedata);
        }

        //Spawn terrain only on clean hit (no scatter)
        if (!$scattered) {
            $this->spawnAsteroidTerrain($gamedata, $fireOrder, $shooter, $target);
        } else {
            $fireOrder->pubnotes .= "Shot scattered - no asteroid formed. ";
        }

        $fireOrder->rolled = max(1, $fireOrder->rolled);
    }

    //Spawn asteroid at target hex, meteoroids at range 1, dust at range 2.
    //Terrain stacking rules apply.
    private function spawnAsteroidTerrain($gamedata, $fireOrder, $shooter, $targetHex) {
        //Spawn asteroid at direct hit hex
        $this->spawnTerrainAtHex($gamedata, $shooter, $targetHex, 'asteroid', $fireOrder);

        //Spawn meteoroids at range 1
        for ($dir = 0; $dir < 6; $dir++) {
            $hex = $targetHex->moveToDirection($dir, 1);
            $this->spawnTerrainAtHex($gamedata, $shooter, $hex, 'meteoroid', $fireOrder);
        }

        //Spawn dust at range 2
        $range2Hexes = array();
        for ($dir = 0; $dir < 6; $dir++) {
            $ring1 = $targetHex->moveToDirection($dir, 1);
            $range2Hexes[] = $ring1->moveToDirection($dir, 1);
            $range2Hexes[] = $ring1->moveToDirection(($dir + 1) % 6, 1);
        }
        //Deduplicate
        $seen = array();
        foreach ($range2Hexes as $hex) {
            $key = $hex->q . ',' . $hex->r;
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $this->spawnTerrainAtHex($gamedata, $shooter, $hex, 'dust', $fireOrder);
            }
        }

        $fireOrder->pubnotes .= "<br>Asteroid formed at " . $targetHex->q . ' ' . $targetHex->r . '. Meteoroid and dust fields spawned.';
    }

    //Spawn terrain at a specific hex, respecting stacking rules.
    //asteroid > meteoroid > dust (higher tier never replaced by lower)
    private function spawnTerrainAtHex($gamedata, $shooter, $hex, $type, $fireOrder) {
        //Check existing terrain
        $existingShips = $gamedata->getShipsInDistance($hex, 0);
        $hasAsteroid = false;
        $hasMeteoroid = false;
        $hasDust = false;

        foreach ($existingShips as $existing) {
            if (!$existing->isTerrain()) continue;
            if ($existing instanceof spawnAsteroidSalvo || $existing instanceof asteroidSNew ||
                $existing instanceof asteroidMNew || $existing instanceof asteroidLNew) {
                $hasAsteroid = true;
            } else if ($existing instanceof spawnMeteoroid) {
                $hasMeteoroid = true;
            } else if ($existing instanceof spawnDustField) {
                $hasDust = true;
            }
        }

        //Apply stacking rules
        if ($type == 'asteroid') {
            if ($hasAsteroid) return; //already has asteroid
            //Remove existing meteoroid/dust and replace with asteroid
            $terrain = new spawnAsteroidSalvo($gamedata->id, -5, "Asteroid", $shooter->slot);
        } else if ($type == 'meteoroid') {
            if ($hasAsteroid || $hasMeteoroid) return; //asteroid/meteoroid already present
            //Can replace dust with meteoroid
            $terrain = new spawnMeteoroid($gamedata->id, -5, "Meteoroid Field", $shooter->slot);
        } else if ($type == 'dust') {
            if ($hasAsteroid || $hasMeteoroid || $hasDust) return; //higher or equal tier present
            $terrain = new spawnDustField($gamedata->id, -5, "Dust Field", $shooter->slot);
        } else {
            return;
        }

        //Insert terrain into game using ballistic mine pattern
        $shipid = Manager::insertSingleShip($gamedata, $terrain, -5);
        $terrain->id = $shipid;

        //Create deployment movement at target hex
        $deployMove = new MovementOrder(
            null, "deploy",
            new OffsetCoordinate($hex->q, $hex->r),
            0, 0, 0, 0, 0, false, $gamedata->turn, 0, 0
        );
        Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);

        //Initialize system data
        SystemData::initSystemData($gamedata->turn, $gamedata->id);
        foreach ($terrain->systems as $system) {
            $system->setInitialSystemData($terrain);
        }
        Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

        //Save note so terrain is recognized in subsequent turns
        $note = new IndividualNote(
            -1, $gamedata->id,
            1, 1,
            $shooter->id, $this->id,
            $shipid,
            "Asteroid Salvo terrain spawned",
            $gamedata->turn
        );
        Manager::insertIndividualNote($note);
    }

    //Override AOEdamage to use Matter damage class
    public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata) {
        if ($target->isDestroyed()) return;
        if ($target->mine) return;
        $damage = $this->getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
        $damage -= $target->getDamageMod($shooter, $sourceHex, $gamedata->turn, $this);
        if ($target instanceof FighterFlight) {
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) continue;
                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, $sourceHex, $gamedata, false);
            }
        } else {
            $tmpLocation = $target->getHitSectionPos(Mathlib::hexCoToPixel($sourceHex), $fireOrder->turn);
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
        }
    }
}


class spawnAsteroidSalvo extends Terrain {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);

        $this->pointCost = 0;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "spawnAsteroidSalvo";
        $this->imagePath = "img/ships/AsteroidS1.png";
        $this->canvasSize = 200;
        $this->shipClass = "Asteroid (Salvo)";
        $this->Enormous = true;
        $this->iniativebonus = -200;
        $this->isd = 0;
        $this->notes = "Blocks line of sight";
        $this->notes .= "<br>Units entering this hex take collision damage";
        $this->occurence = "common";

        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;

        $this->forwardDefense = 20;
        $this->sideDefense = 20;

        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;

        Enhancements::nonstandardEnhancementSet($this, 'Terrain');

        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(8, 300));

        $this->hitChart = array(
            0 => array(20 => "Structure"),
            1 => array(20 => "Primary"),
            2 => array(20 => "Primary"),
        );
    }
} // end of class spawnAsteroidSalvo




class spawnMeteoroid extends Terrain {
    public $isMeteoroid = true;
    public $terrainCollisionType = 'MeteoroidCollision';

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 0;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "spawnMeteoroid";
        $this->imagePath = "img/ships/asteroidField3.png";
        $this->canvasSize = 200;
        $this->shipClass = "Meteoroid Field";
        $this->Enormous = true;
        $this->iniativebonus = -200;
        $this->isd = 0;
        $this->notes = "Units entering this hex roll d20 on the Meteoroid chart.";
        $this->notes .= "<br>Meteoroid hit damage = 1d6 + (unit speed / 2). Standard damage. Armor applies.";
        $this->occurence = "common";
        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;
        Enhancements::nonstandardEnhancementSet($this, 'Terrain');
        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(8, 300));
        $this->hitChart = array(
            0 => array(20 => "Structure"),
        );
    }

    public static function rollMeteorChart($shipSizeClass, $isFlight, $modifier = 0) {
        $roll = Dice::d(20) + $modifier;
        $roll = max(1, min(20, $roll));
        $chart = array(
            12 => array(0, 0, 0, 0, 0),
            14 => array(0, 0, 0, 0, 1),
            15 => array(0, 0, 0, 1, 1),
            16 => array(0, 0, 1, 1, 1),
            17 => array(0, 1, 1, 1, 2),
            18 => array(0, 1, 1, 2, 2),
            19 => array(1, 1, 2, 2, 2),
            20 => array(1, 2, 2, 2, 3),
        );
        if ($isFlight) { $col = 0; }
        else {
            switch ($shipSizeClass) {
                case 1: $col = 1; break;
                case 2: $col = 2; break;
                case 3: $col = 3; break;
                default: $col = ($shipSizeClass >= 4) ? 4 : 1;
            }
        }
        $hits = 0;
        foreach ($chart as $threshold => $row) {
            if ($roll <= $threshold) { $hits = $row[$col]; break; }
        }
        if ($roll >= 20) $hits = $chart[20][$col];
        return $hits;
    }

    public static function getMeteorDamage($speed) {
        return Dice::d(6) + floor($speed / 2);
    }
}



class spawnDustField extends Terrain {
    public $isDustField = true;
    public $terrainCollisionType = 'DustCollision';
    public static $dustDamagedThisTurn = array();

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 0;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "spawnDustField";
        $this->imagePath = "img/ships/dust.png";
        $this->canvasSize = 200;
        $this->shipClass = "Dust Field";
        $this->Enormous = true;
        $this->iniativebonus = -200;
        $this->isd = 0;
        $this->notes = "Units entering this hex take dust damage.";
        $this->notes .= "<br>Single damage roll per turn: speed / 2 (drop fractions).";
        $this->occurence = "common";
        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;
        Enhancements::nonstandardEnhancementSet($this, 'Terrain');
        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(8, 300));
        $this->hitChart = array(
            0 => array(20 => "Structure"),
        );
    }

    public static function getDustDamage($speed) {
        return floor($speed / 2);
    }
}




class SpatialCutter extends Weapon {

    public $name = "SpatialCutter";
    public $displayName = "Spatial Cutter";
    public $iconPath = "SpatialCutter.png";

    public $damageType = "Raking";
    public $weaponClass = "Gravitic";
    public $raking = 15;

    public $range = 12;
    public $rangePenalty = 0.25;
    public $fireControl = array(-2, 3, 7);
    public $intercept = 8;

    public $uninterceptable = true;
    public $canInterceptUninterceptable = true;

    public $loadingtime = 1;

    public $animation = "gravitic";
    public $animationColor = array(100, 0, 200);
    public $animationExplosionScale = 0.4;

    public $firingModes = array(1 => "Spatial Cutter");

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        $this->data["Special"] = "Gravitic class weapon. Raking 15. Requires lock-on to fire.";
        $this->data["Special"] .= "<br>Uninterceptable. Can intercept uninterceptable weapons.";
        $this->data["Special"] .= "<br>On hit: creates a hyperspace waveform along the line of fire.";
        $this->data["Special"] .= "<br>Waveform appears at start of next turn. Units entering a waveform hex take speed x size factor damage.";
        $this->data["Special"] .= "<br>Units without advanced armor take double waveform damage.";
    }

    public function getDamage($fireOrder) {
        return Dice::d(10, 8) + 20;
    }

    public function setMinDamage() { $this->minDamage = 28; }
    public function setMaxDamage() { $this->maxDamage = 100; }

    public function calculateHitBase($gamedata, $fireOrder) {
        $firingShip = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);

        if ($target && $firingShip->getOEW($target, $gamedata->turn) <= 0) {
            $fireOrder->needed = 0;
            $fireOrder->pubnotes .= "No lock-on - shot cannot be fired. ";
            return;
        }

        parent::calculateHitBase($gamedata, $fireOrder);
    }

    public function fire($gamedata, $fireOrder) {
        parent::fire($gamedata, $fireOrder);

        if ($fireOrder->shotshit > 0) {
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $target  = $gamedata->getShipById($fireOrder->targetid);

            if ($shooter && $target) {
                // Spawn waveform immediately along the line of fire.
                // spawnTurn is set to the current turn so:
                // - No collision damage on turn N (units already placed before firing)
                // - Collision damage fires on turn N+1 when units move through the hex
                // - beforePreFiringOrderResolution removes it on turn N+2
                $lineHexes = self::getHexLine($shooter->getHexPos(), $target->getHexPos());
                foreach ($lineHexes as $hex) {
                    $this->spawnWaveformAtHex($gamedata, $shooter, $hex);
                }
            }
        }
    }

    // Remove expired waveforms at the start of pre-firing on turn N+2.
    // Waveform spawns on turn N, is active on turn N+1, removed on turn N+2.
    public function beforePreFiringOrderResolution($gamedata) {
        foreach ($gamedata->ships as $waveShip) {
            if ($waveShip instanceof spawnHyperspaceWaveform) {
                $waveShip->loadSpawnTurn();
                if ($waveShip->spawnTurn > 0 && $gamedata->turn > $waveShip->spawnTurn + 1) {
                    $structure = $waveShip->getSystemByName("Structure");
                    if ($structure && !$structure->isDestroyed()) {
                        $damageEntry = new DamageEntry(
                            -1, $waveShip->id, $gamedata->id, $gamedata->turn, $structure->id,
                            $structure->maxhealth, 0, 0, -1, true, false,
                            "Waveform dissipated", "Standard"
                        );
                        $damageEntry->updated = true;
                        $structure->damage[] = $damageEntry;
                    }
                }
            }
        }
    }

    private function spawnWaveformAtHex($gamedata, $shooter, $hex) {
        $waveform = new spawnHyperspaceWaveform($gamedata->id, -5, "HW" . $gamedata->turn, $shooter->slot);
        $waveform->spawnTurn = $gamedata->turn;

        $shipid = Manager::insertSingleShip($gamedata, $waveform, -5);
        $waveform->id = $shipid;

        $deployMove = new MovementOrder(
            null, "deploy",
            new OffsetCoordinate($hex->q, $hex->r),
            0, 0, 0, 0, 0, false, $gamedata->turn, 0, 0
        );
        Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);

        SystemData::initSystemData($gamedata->turn, $gamedata->id);
        foreach ($waveform->systems as $system) {
            $system->setInitialSystemData($waveform);
        }
        Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

        unset($gamedata->ships[$shipid]);
    }

    public static function getHexLine(OffsetCoordinate $start, OffsetCoordinate $end) {
        $startCube = self::offsetToCube($start);
        $endCube = self::offsetToCube($end);

        $dx = $endCube[0] - $startCube[0];
        $dy = $endCube[1] - $startCube[1];
        $dz = $endCube[2] - $startCube[2];

        $steps = max(abs($dx), abs($dy), abs($dz));
        if ($steps > 50) $steps = 50;

        $hexes = array();
        for ($i = 0; $i <= $steps; $i++) {
            $t = $steps == 0 ? 0 : $i / $steps;
            $cx = $startCube[0] + $dx * $t;
            $cy = $startCube[1] + $dy * $t;
            $cz = $startCube[2] + $dz * $t;
            $hexes[] = self::cubeToOffset(self::cubeRound($cx, $cy, $cz));
        }

        return $hexes;
    }

    private static function offsetToCube(OffsetCoordinate $hex) {
        $x = $hex->q - ($hex->r - ($hex->r & 1)) / 2;
        $z = $hex->r;
        $y = -$x - $z;
        return array($x, $y, $z);
    }

    private static function cubeRound($x, $y, $z) {
        $rx = round($x);
        $ry = round($y);
        $rz = round($z);
        $dx = abs($rx - $x);
        $dy = abs($ry - $y);
        $dz = abs($rz - $z);
        if ($dx > $dy && $dx > $dz) {
            $rx = -$ry - $rz;
        } else if ($dy > $dz) {
            $ry = -$rx - $rz;
        } else {
            $rz = -$rx - $ry;
        }
        return array($rx, $ry, $rz);
    }

    private static function cubeToOffset($cube) {
        $q = $cube[0] + ($cube[2] - ($cube[2] & 1)) / 2;
        $r = $cube[2];
        return new OffsetCoordinate($q, $r);
    }

    public static function getWaveformDamage($ship) {
        $move = $ship->getLastMovement();
        $speed = $move ? $move->speed : 0;
        if ($speed <= 0) return 0;

        if ($ship instanceof FighterFlight) {
            if (!empty($ship->shuttle) || !empty($ship->superheavy)) {
                $factor = 1.0;
            } else {
                $factor = 0.75;
            }
        } else if (!empty($ship->Enormous)) {
            $factor = 10.0;
        } else {
            switch ($ship->shipSizeClass) {
                case 0: $factor = 1.5; break;
                case 1: $factor = 2.0; break;
                case 2: $factor = 4.0; break;
                case 3: $factor = 6.0; break;
                default: $factor = 10.0; break;
            }
        }

        return (int)ceil($speed * $factor);
    }
}

class spawnHyperspaceWaveform extends Terrain {

    public $terrainCollisionType = 'WaveformCollision';
    public $Enormous = true;
	public $spawnTurn = 0; 

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 0;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "spawnHyperspaceWaveform";
        $this->imagePath = "img/ships/hyperspaceWaveform.png";
        $this->canvasSize = 200;
        $this->shipClass = "Hyperspace Waveform";
        $this->Enormous = true;
        $this->iniativebonus = -200;
        $this->isd = 0;
        $this->notes = "Hyperspace waveform - lasts one turn only.";
        $this->notes .= "<br>Units entering this hex take speed x size factor damage per hex.";
        $this->notes .= "<br>Units without advanced armor take double damage.";
        $this->occurence = "common";

        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;
        $this->forwardDefense = 20;
        $this->sideDefense = 20;

        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;

        Enhancements::nonstandardEnhancementSet($this, 'Terrain');
        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(8, 300));

        $this->hitChart = array(
            0 => array(20 => "Structure"),
        );
    }

    public function loadSpawnTurn() {
        // Name is stored as "HWN" where N is the turn
        if (strpos($this->name, 'HW') === 0) {
            $this->spawnTurn = (int)substr($this->name, 2);
        }
    }
}








?>
