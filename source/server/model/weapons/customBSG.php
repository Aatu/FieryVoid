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

	protected $useDie = 2; //die used for base number of hits
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
		
        public function getDamage($fireOrder){ return Dice::d(6, 1)+2; }
        public function setMinDamage(){ $this->minDamage = 3 ; }
        public function setMaxDamage(){ $this->maxDamage = 8 ; }		
		
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

	protected $useDie = 2; //die used for base number of hits
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



class BSGMarineAssault extends Weapon{

        public $name = "BSGMarineAssault";
        public $displayName = "Marine Assault";
        public $iconPath = "BSGMarines.png"; 		
		
        public $animation = "trail";
        public $animationColor = array(217, 11, 41);
        public $trailLength = 5;
        public $animationWidth = 4;
        public $projectilespeed = 18;
        public $animationExplosionScale = 0.10;
		public $noOverkill = true; //this will let simplify entire Matter line enormously!
        public $uninterceptable = true;
		public $exclusive = true;

        public $loadingtime = 2;
        public $priority = 2; 

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Simulates marines attacking a system directly.";
			$this->data["Special"] .= "<br>Must be in same hex as target.";
			$this->data["Special"] .= "<br>Uninterceptable.";
			$this->data["Special"] .= "<br>No called shot penalty.";
			$this->data["Special"] .= "<br>Causes 3d6+2 matter damage to targetted system.";
		}
        
        public $rangePenalty = 0;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        public $range = 0;
        public $calledShotMod = 0; //instead of usual -8
        
        public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
        public $weaponClass = "Matter";

	function __construct($startArc, $endArc, $nrOfShots = 1){
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
		
        public function getDamage($fireOrder){ return Dice::d(6, 3)+2; }
        public function setMinDamage(){ $this->minDamage = 5 ; }
        public function setMaxDamage(){ $this->maxDamage = 20 ; }		
		
    } // endof BSGMarineAssault	



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
			$this->data["Special"] = "Can intercept lasers.";
			$this->data["Special"] .= "<br>May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes.";
		}

        public $rangePenalty = 1; //
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
		
        public function getDamage($fireOrder){        return Dice::d(6, 1)+6 ;   }
        public function setMinDamage(){     $this->minDamage = 7 ;      }
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




 class FlakArray2 extends InterceptorMkI{ 
        public $trailColor = array(30, 170, 255);

        public $name = "FlakArray2";
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
		
    }	//endof class FlakArray2





   class FlakArray extends Weapon{ 
/*Dual mode weapon based off the EA Laser-Pulse Array code to operate more as it did in 
the table top verion. This is based off the Grome Flak Cannon and could be considered an 
advanced version of it as it fires twice and can target ships. The Flak Array has the option 
to manually target a unit in order to provide intercept against all shots from that unit. 
Lastly, the friendly intercept function is available. If the Flak Array is not fired offensively 
and is not fired defensively manually, it will follow the automatic intercept routines. This 
will only be against single weapons, but the Flak Array will still intercept lasers in this 
mode and can still intercept for friendly units.*/

/*This will be set up to function as follows:
1. Default mode will be manual intercept to gain full intercept against all shots from a single unit.
2. Option to switch to offensive-mode. Unlike the Flak Cannon, this can target all units.
3. If nothing is done, it will follow automated, friendly intercept routines.*/

        public $name = "FlakArray";
        public $displayName = "Flak Array";
	    public  $iconPath = "FlakArray.png";

		public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 250, 230), 2=>array(255, 250, 230));
        public $animationWidthArray = array(1=>2, 2=>2);
		public $trailColor = array(30, 170, 255);
        public $trailLength = 5;
        public $projectilespeed = 10;
        public $animationExplosionScaleArray = array(1=>0.6, 2=>0.6);//not used for Laser animation?...

	//actual weapons data
        public $priorityArray = array(1=>1, 2=>1);
		public $uninterceptableArray = array(1=>true, 2=>false);
		public $doNotInterceptArray = array(1=>true, 2=>false);

		public $guns = 2;
        public $intercept = 3;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 1;

//        public $output = 3;
		public $canInterceptUninterceptable = true;

		public $range = 100; //let's put maximum range here, but generous one
	
        public $loadingtimeArray = array(1=>1, 2=>1); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0, 2=>2);
        public $fireControlArray = array( 1=>array(50, 50, 50), 2=>array(5, 4, 3) ); // fighters, <mediums, <capitals 
	
		public $firingModes = array(1=>'Intercept', 2=>'Offensive-mode');
		public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Matter', 2=>'Matter'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 6;
            if ( $powerReq == 0 ) $powerReq = 3;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Can intercept lasers.";
			$this->data["Special"] .= "<br>Fires twice.";
			$this->data["Special"] .= "<br>May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes. Friendly intercept only engages individual shots.";
			$this->data["Special"] .= "<br>If manually targeted in Intercept (I) mode, will intercept all fire from targeted ship (except ballistics), with usual intercept degredation, at the Flak Array-firing ship.";
			$this->data["Special"] .= "<br>Offensiver-mode (O) can engage any unit as a matter weapon doing damage in flash mode.";
			$this->data["Special"] .= "<br>Offensiver mode fire control is +25/+20/+15. Damage is 1d10+2 as Matter (Flash).";
		}

		//hit chance always 100 - so it always hits and is correctly animated
		public function calculateHitBase($gamedata, $fireOrder){

			$this->changeFiringMode($fireOrder->firingMode);  //needs to be outside the switch routine

			switch($this->firingMode){
				case 1:

				$fireOrder->needed = 100; //auto hit!
				$fireOrder->updated = true;
		
				//while we're at it - we may add appropriate interception orders!		
				$targetShip = $gamedata->getShipById($fireOrder->targetid);

//				$shipsInRange = $gamedata->getShipsInDistance($targetShip); //all units on target hex
//				foreach ($shipsInRange as $affectedShip) {
					$allOrders = $targetShip->getAllFireOrders($gamedata->turn);
//					$allOrders = $affectedShip->getAllFireOrders($gamedata->turn);
					foreach($allOrders as $subOrder) {
						if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->shooterid) ){ //something is firing at protected unit - and is affected!
							//uninterceptable are affected all right, just those that outright cannot be intercepted - like ramming or mass driver - will not be affected
							$subWeapon = $targetShip->getSystemById($subOrder->weaponid);
//							$subWeapon = $affectedShip->getSystemById($subOrder->weaponid);
							if( $subWeapon->doNotIntercept != true ){
								//apply interception! Note that this weapon is technically not marked as firing defensively - it is marked as firing offensively though! (already)
								//like firing.php addToInterceptionTotal
								$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);
								$subOrder->numInterceptors++;
							}
						}
					}	
//				}
		
				//retarget at hex - this will affect how the weapon is animated/displayed in firing log!
					//insert correct target coordinates: CURRENT target position
					$pos = $targetShip->getHexPos();
					$fireOrder->x = $pos->q;
					$fireOrder->y = $pos->r;
					$fireOrder->targetid = -1; //correct the error

				break;
		
			case 2:
		
				parent::calculateHitBase($gamedata, $fireOrder);
				break;
			}

		}//endof function calculateHitBase

	   
		public function fire($gamedata, $fireOrder) { 

			switch($this->firingMode){
				case 1:

//					$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
					$shooter = $gamedata->getShipById($fireOrder->shooterid);
					/** @var MovementOrder $movement */
					$movement = $shooter->getLastTurnMovement($fireOrder->turn);
	//GTS				$posLaunch = $movement->position;//at moment of launch!!!		
					//$this->calculateHit($gamedata, $fireOrder); //already calculated!
					$rolled = Dice::d(100);
					$fireOrder->rolled = $rolled; ///and auto-hit ;)
					$fireOrder->shotshit++;
					$fireOrder->pubnotes .= "Interception applied to all weapons on target unit that are firing at Flak Cannon-firing ship. "; //just information for player, actual applying was done in calculateHitBase method

					$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
					TacGamedata::$lastFiringResolutionNo++;    //note for further shots
					$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!

					break;
				
				case 2:
				
					parent::fire($gamedata, $fireOrder);
					break;
			}

		} //endof function fire
			
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

        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return 0; //Manual intercept
				break;
			case 2:
				return Dice::d(10, 1)+2; //Anti-fighter shot
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 0; //Manual intercept
				break;
			case 2:
				$this->minDamage = 3; //Anti-fighter shot
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 0; //Manual intercept
				break;
			case 2:
				$this->maxDamage = 12; //Anti-fighter shot
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

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




class SensorSpikeFtr extends Weapon{
    /*Modified Abbai weapon - does no damage, but limits target's Sensors next turn.
	Note, the range has been halved to -1/hex. */
    public $name = "SensorSpikeFtr";
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
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors (-1d6) rating next turn";  
    }	

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor

		$effectSensors = Dice::d(6,1);//strength of effect: 1d6
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
	
} //end of class SensorSpikeFtr




class CommJammerFtr extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Initiative  next turn
    */
    public $name = "CommJammerFtr";
    public $displayName = "Comm Jammer";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 3;
    public $rangePenalty = 1; //-1/hex
    public $intercept = 0;
    public $fireControl = array(0, 2, 2);
    public $exclusive = true;
	
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
                $this->iconPath = "commJammer.png";
            }

            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
	      $this->data["Special"] = "Does no damage, but weakens target's Initiative (-1d6) rating next turn.";  
    }	
    
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		
		$effectIni = Dice::d(6,1);//strength of effect: 1d6
		$effectIni5 = $effectIni * 5;
		$fireOrder->pubnotes .= "<br> Initiative reduced by $effectIni5.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $firstFighter->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
			}
		}else{ //ship - place effcet on C&C!
			$CnC = $ship->getSystemByName("CnC");
			if($CnC){
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
	
} //end of class CommJammerFtr





class BSGMedScattergun extends Pulse{
    /*Markab fighter weapon - d3 shots (here treated as a single Pulse shot, no grouping bonus)*/
       public $shots = 2;
	public  $iconPath = "scatterGun.png";
	
	//for Pulse mode
	public $grouping = 2500; //NO GROUPING BONUS
	public $maxpulses = 2;	
	protected $useDie = 3; //die used for base number of hits
 
    public $damageType = "Pulse"; 
    public $weaponClass = "Particle";
   
	//animation for fighter laser - bigger guns need to change size and speed attributes :)
	public $name = "BSGMedScattergun";
	public $displayName = "Medium Scattergun";
	public $animation = "trail";
	public $animationColor = array(30, 170, 255);
	public $animationExplosionScale = 0.10;
	public $projectilespeed = 12;
	public $animationWidth = 2;
	public $trailLength = 10;
	
	public $intercept = 2;
	
	public $rangePenalty = 2;
	
	public $priority = 4;

	
    
	function __construct($startArc, $endArc){//more than a single emplacement not supported!
		$this->maxpulses = 4;
		$this->defaultShots = 2;	
						
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}    
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'does always d4 pulses, no grouping bonus';
	}
	
    
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn); //$this->useDie usually
		//$pulses+= $this->getExtraPulses($needed, $rolled); //no grouping bonus for this weapon
		return $pulses;
	}	
	
	public function getDamage($fireOrder){        return Dice::d(6,2);   }
	public function setMinDamage(){     $this->minDamage = 2 ;      }
	public function setMaxDamage(){     $this->maxDamage = 12 ;      }
} //end of class LightScattergun




class FlakBlast extends Weapon{
        public $name = "FlakBlast";
        public $displayName = "Flak Blast";
		public $iconPath = "PlasmaWeb.png";
		
		public $range = 5;
		public $loadingtime = 1;
//		public $hextarget = true;
		
		public $flashDamage = true;
		public $priority = 1;
			
        public $animation = "ball";
        public $trailColor = array(30, 140, 60);
        public $animationColor = array(30, 140, 60);
        public $animationExplosionScale = 1;
		public $animationExplosionType = "AoE";
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;    

		public $firingMode = 'AoE'; //firing mode - just a name essentially
		public $damageType = "Flash"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Plasma"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)

        public $rangePenalty = 0; //none
        public $fireControl = array(50, null, null); // fighters, <mediums, <capitals




	public function calculateHitBase($gamedata, $fireOrder)
    {
        $fireOrder->needed = 100; //100% chance of hitting everything on target hex
        $fireOrder->updated = true;
    } 

	public function fire($gamedata, $fireOrder){
        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
        $shooter = $gamedata->getShipById($fireOrder->shooterid); //so we know which ship is firing, this is useful

		if ($fireOrder->targetid != -1) { //make weapon target hex rather than unit
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            //insert correct target coordinates: CURRENT  target position
            $position = $targetship->getCoPos(); 
            $fireOrder->x = $position["x"];
            $fireOrder->y = $position["y"];
            $fireOrder->targetid = -1; 
        }

		//roll to hit - we'll make a regular roll (irrelevant as hit is automatic, but we need to mark SOME number anyway):
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled;

		//deal damage!
        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $ships1 = $gamedata->getShipsInDistance($target); //all ships on target hex
        foreach ($ships1 as $targetShip) if ($targetShip instanceOf FighterFlight) {

            $this->AOEdamage($targetShip, $shooter, $fireOrder, $gamedata);
        }
    }
	
	//and now actual damage dealing - and we already know fighter is hit (fire()) doesn't pass anything else)
	//source hex will be taken from firing unit, damage will be individually rolled for each fighter hit
	 public function AOEdamage($target, $shooter, $fireOrder, $gamedata)
    {
        if ($target->isDestroyed()) return; //no point allocating
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
                }
         //roll (and modify as appropriate) damage for this particular fighter:
        $damage = $this->getDamage();
//        $damage = $this->getDamageMod($damage, $shooter, $target, null, $gamedata);
//        $damage -= $target->getDamageMod($shooter, null, $gamedata->turn, $this);

                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);

		}
	}

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
//    	public function getDamage($fireOrder){        return Dice::d(6, 1)+2;   }
    	public function getDamage($fireOrder){        return 12;   }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }
}//endof PlasmaBlast



?>
