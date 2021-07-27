<?php
/*file for Battlestar Galactica universe weapons*/

/*file for Battlestar Galactica universe weapons*/

//BSG Fighter Weapons


class BSGHypergun extends Pulse{

        public $name = "BSGHypergun";
        public $displayName = "Hypergun";
        public $iconPath = "starwars/swFighter4.png"; 		
		
        public $animation = "trail";
        public $animationColor = array(217, 11, 41);
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;

	protected $useDie = 1; //die used for base number of hits
//		public $rof = 3;
		public $grouping = 15;
		public $maxpulses = 5;
        
        public $loadingtime = 1;
        public $intercept = 2;
		public $ballisticIntercept = true;
        public $priority = 5; 
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        
//        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Matter";

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] .= "<br>Ignores armor, does not overkill.";
		}
		
        public function getDamage($fireOrder){ return 3; }
        public function setMinDamage(){ $this->minDamage = 3 ; }
        public function setMaxDamage(){ $this->maxDamage = 3 ; }		
		
    } // endof BSGHypergun	



class BSGLtKineticEnergyWeapon extends Pulse{

        public $name = "BSGLtKineticEnergyWeapon";
        public $displayName = "Light Kinetic Energy Weapon";
        public $iconPath = "starwars/swFighter2.png"; 		
		
        public $animation = "trail";
        public $animationColor = array(217, 11, 41);
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;

	protected $useDie = 1; //die used for base number of hits
//		public $rof = 3;
		public $grouping = 25;
		public $maxpulses = 2;
        
        public $loadingtime = 1;
        public $intercept = 2;
		public $ballisticIntercept = true;
        public $priority = 3; 
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        
//        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+1; }
        public function setMinDamage(){ $this->minDamage = 2 ; }
        public function setMaxDamage(){ $this->maxDamage = 7 ; }		
		
    } // endof BSGLtKineticEnergyWeapon	



class BSGKineticEnergyWeapon extends Pulse{

        public $name = "BSGKineticEnergyWeapon";
        public $displayName = "Kinetic Energy Weapon";
        public $iconPath = "starwars/swFighter4.png"; 		
		
        public $animation = "trail";
        public $animationColor = array(217, 11, 41);
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;

	protected $useDie = 1; //die used for base number of hits
//		public $rof = 3;
		public $grouping = 15;
		public $maxpulses = 4;
        
        public $loadingtime = 1;
        public $intercept = 2;
		public $ballisticIntercept = true;
        public $priority = 3; 
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        
//        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+3; }
        public function setMinDamage(){ $this->minDamage = 4 ; }
        public function setMaxDamage(){ $this->maxDamage = 9 ; }		
		
    } // endof BSGKineticEnergyWeapon	


class BSGHvyKineticEnergyWeapon extends Pulse{

        public $name = "BSGHvyKineticEnergyWeapon";
        public $displayName = "Heavy Kinetic Energy Weapon";
        public $iconPath = "gatlingPulseCannon.png"; 		
		
        public $animation = "trail";
        public $animationColor = array(217, 11, 41);
        public $trailLength = 6;
        public $animationWidth = 5;
        public $projectilespeed = 15;
        public $animationExplosionScale = 0.20;

	protected $useDie = 1; //die used for base number of hits
//		public $rof = 3;
		public $grouping = 20;
		public $maxpulses = 3;
        
        public $loadingtime = 2;
        public $priority = 4; 
        
        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        
//        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 2)+4; }
        public function setMinDamage(){ $this->minDamage = 6 ; }
        public function setMaxDamage(){ $this->maxDamage = 16 ; }		
		
    } // endof BSGHvyKineticEnergyWeapon	



 class BSGFlakBattery extends Weapon{ 

        public $name = "BSGFlakBattery";
        public $displayName = "Flak Battery";
	    public  $iconPath = "FlakCannon.png";

        public $trailColor = array(245, 245, 44);
        public $animation = "ball";
        public $animationColor = array(245, 245, 44);
        public $animationExplosionScale = 1; //covers 1/2 hex away from explosion center
        public $animationExplosionType = "AoE";
        public $explosionColor = array(141, 240, 255);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;

        public $range = 5;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $intercept = 3;
        public $loadingtime = 1;

		public $canInterceptUninterceptable = true;

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Defensive Flak: -15 to hit on arc with active Flak Battery.";
			$this->data["Special"] .= "<br>Can intercept lasers.";
			$this->data["Special"] .= "<br>May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes.";
		}

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(6, null, null); // fighters, <mediums, <capitals
        public $priority = 1; //Flash 

        public $damageType = "Flash"; 
        public $weaponClass = "Matter";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 2;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 5 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 5) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}
		
        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }	//endof class BSGFlakBattery





    class BSGMainBattery extends Pulse{
	/* Belt Alliance Medium Blast Cannon used as template. Stats are very similar.
	Primary difference is that this cannot target fighters, is larger, and uses
	more power.*/
        public $name = "BSGMainBattery";
        public $displayName = "Main Battery";
	    public $iconPath = 'NexusHeavyAssaultCannon.png';
		public $animation = "trail";
		public $animationColor = array(245, 245, 44);
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.15;
		public $rof = 3;

        public $priority = 6;
		public $grouping = 25; //+1/5
        public $maxpulses = 6;
		protected $useDie = 6; //die used for base number of hits
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33; //-1/3hex
        public $fireControl = array(null, 3, 4); // fighters, <mediums, <capitals 

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] .= "<br>Ignores armor, does not overkill.";
		}
		
		public $noOverkill = true; //Matter weapon
//		public $damageType = "Pulse";
		public $weaponClass = "Matter";
        
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 9;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 6;
		}		
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function getDamage($fireOrder){        return 8;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
		
    } //endof class BSGMainBattery



    class BSGMedBattery extends Pulse{
	/* Belt Alliance Medium Blast Cannon used as template. Stats are very similar.
	Primary difference is that this cannot target fighters, is larger, and uses
	more power.*/
        public $name = "BSGMedBattery";
        public $displayName = "Battery";
	    public $iconPath = 'NexusAssaultCannon.png';
		public $animation = "trail";
		public $animationColor = array(245, 245, 44);
        public $trailLength = 15;
        public $animationWidth = 3;
        public $projectilespeed = 12;
        public $animationExplosionScale = 0.10;
		public $rof = 3;

        public $priority = 6;
		public $grouping = 25; //+1/5
        public $maxpulses = 5;
		protected $useDie = 5; //die used for base number of hits
        
        public $loadingtime = 2;
        
        public $rangePenalty = 0.5; //-1/3hex
        public $fireControl = array(null, 2, 3); // fighters, <mediums, <capitals 

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] .= "<br>Ignores armor, does not overkill.";
		}
		
		public $noOverkill = true; //Matter weapon
//		public $damageType = "Pulse";
		public $weaponClass = "Matter";
        
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
		    $maxhealth = 7;
		}
		if ( $powerReq == 0 ){
		    $powerReq = 4;
		}		
                parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function getDamage($fireOrder){        return 5;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 5 ;      }
		
    } //endof class BSGMedBattery




 class FlakArray extends InterceptorMkI{ 
        public $trailColor = array(30, 170, 255);

        public $name = "FlakArray";
        public $displayName = "Flak Array";
        public $animation = "trail";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.1;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 8;
	    public  $iconPath = "FlakArray.png";

		public $guns = 2;
        public $intercept = 3;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 1;

        public $output = 3;
		public $canInterceptUninterceptable = true;

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(5, 4, 3); // fighters, <mediums, <capitals
        public $priority = 1; //Flash 

        public $damageType = "Flash"; 
        public $weaponClass = "Matter";

        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->tohitPenalty = $this->getOutput();
            $this->damagePenalty = 0;
     
        }
        
        public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
            if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
                return 0;

            $output = $this->output;
            $output -= $this->outputMod;
            return $output;
        }

        public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
            return 0;
        }
		
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Defensive Flak: -15 to hit on arc with active Flak Array.";
			$this->data["Special"] .= "<br>Can intercept lasers.";
			$this->data["Special"] .= "<br>May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes.";
		}

		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 5 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 5) return false;
			
			//both source and target of fire must be in arc
			//first check target
			$targetBearing = $interceptingShip->getBearingOnUnit($target);
			if (!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false;
			//check on source - launch hex for ballistics, current position for direct fire
			if ($firingWeapon->ballistic){
				$movement = $shooter->getLastTurnMovement($fireOrder->turn);
				$pos = mathlib::hexCoToPixel($movement->position); //launch hex
				$sourceBearing = $interceptingShip->getBearingOnPos($pos);				
			}else{ //direct fire
				$sourceBearing = $interceptingShip->getBearingOnUnit($shooter);
			}
			if (!mathlib::isInArc($sourceBearing, $this->startArc, $this->endArc)) return false;
						
			return true;
		}

        public function getDamage($fireOrder){        return Dice::d(10,1)+6;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
        public function setMaxDamage(){     $this->maxDamage = 16 ;      }
		
    }	//endof class FlakArray






class SensorSpearFtr extends Weapon{
    /*Modified Abbai weapon - does no damage, but limits target's Sensors next turn.
	Note, the range has been halved to -1/hex. */
    public $name = "SensorSpearFtr";
    public $displayName = "DRADIS Jammer";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 2;
    public $rangePenalty = 1; //-1/ hex
    public $intercept = 0;
    public $fireControl = array(0, 0, 0);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationColor2 = array(160, 160, 240);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 1){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;

            if($nrOfShots === 1){
                $this->iconPath = "sensorSpike.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors (-1d3) rating next turn";  
    }	

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor

		$effectSensors = Dice::d(3,1);//strength of effect: 1d3
		$fireOrder->pubnotes .= "<br> Sensors reduced by $effectSensors.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $firstFighter->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!
			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
	
} //end of class SensorSpearFtr






?>
