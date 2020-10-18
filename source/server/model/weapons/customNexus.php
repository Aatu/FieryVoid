<?php
/*file for Nexus universe weapons*/


class NexusKineticBoxLauncher extends Weapon{
        public $name = "nexusKineticBoxLauncher";
        public $displayName = "Kinetic Box Launcher";
		    public $iconPath = "NexusKineticBoxLauncher.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = false; //missile
        public $ballistic = true; //missile
        public $range = 15;
        public $distanceRange = 20;
        public $ammunition = 4; //limited number of shots
	    
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals; INCLUDES BOTH LAUNCHER AND MISSILE DATA!
	    
	public $noOverkill = true; //Matter weapon
	public $priority = 9; //Matter weapon
	    
	public $firingMode = 'Kinetic'; //firing mode - just a name essentially
	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Ballistic"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 0;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, no overkill (Ballistic+Matter weapon).";
            $this->data["Ammunition"] = $this->ammunition;
        }
        
        public function getDamage($fireOrder){
            $dmg = 8;
            return $dmg;
       }
        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function setMinDamage(){     $this->minDamage = 8;      }
        public function setMaxDamage(){     $this->maxDamage = 8;      }
}//endof NexusKineticBoxLauncher


/*Chaff Launcher
intercepts all weapon fire (directed at self) from HEX (including uninterceptable weapons).
Done as: kind of offensive mode - player needs to pick hex to fire at. Animated as kind of EMine. 
All appropriate fire orders will get an interception set up before other intercepts are declared.
If weapon is left to its own devices it will simply provide a single interception (...if game allows non-1-per-turn weapon to be intercepting in the first place!)
*/
class NexusChaffLauncher extends Weapon{
        public $name = "nexusChaffLauncher";
        public $displayName = "Chaff Launcher";
		public $iconPath = "NexusChaffLauncher.png";
	
        public $trailColor = array(192,192,192);
        public $animation = "ball";
        public $animationColor = array(192,192,192);
        public $animationExplosionScale = 0.5;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(235,235,235);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;

        public $ballistic = false;
        public $hextarget = false; //for technical reasons this proved hard to do
        public $hidetarget = false;
        public $priority = 1; //to show effect quickly
        public $uninterceptable = true; //just so nothing tries to actually intercept this weapon
        public $doNotIntercept = true; //do not intercept this weapon, period
		public $canInterceptUninterceptable = true; //able to intercept shots that are normally uninterceptable, eg. Lasers
	
        public $useOEW = false; //not important, really	    
        
        public $loadingtime = 2; // 1/2 turns
		public $range = 100; //let's put maximum range here, but generous one
        public $rangePenalty = 0;
        public $fireControl = array(100, 100, 100); // fighters, <mediums, <capitals; just so the weapon is targetable
		public $intercept = 2; //intercept rating -2	    
	    
		public $firingMode = 'Intercept'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
	 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 2;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Fired at hex (although You technically have to pick an unit). Will apply interception to all fire from target hex to Chaff-protected ship.";
            $this->data["Special"] .= "<br>Will affect uninterceptable weapons.";
        }
        
	//hit chance always 100 - so it always hits and is correctly animated
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$fireOrder->needed = 100; //auto hit!
		$fireOrder->updated = true;
		
		//while we're at it - we may add appropriate interception orders!		
		$targetShip = $gamedata->getShipById($fireOrder->targetid);
		
		$shipsInRange = $gamedata->getShipsInDistance($targetShip); //all units on target hex
		foreach ($shipsInRange as $affectedShip) {
			$allOrders = $affectedShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->shooterid) ){ //something is firing at protected unit - and is affected!
					//uninterceptable are affected all right, just those that outright cannot be intercepted - like ramming or mass driver - will not be affected
					$subWeapon = $affectedShip->getSystemById($subOrder->weaponid);
					if( $subWeapon->doNotIntercept != true ){
						//apply interception! Note that this weapon is technically not marked as firing defensively - it is marked as firing offensively though! (already)
						//like firing.php addToInterceptionTotal
						$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);
        				$subOrder->numInterceptors++;
					}
				}
			}
		}
		
		//retarget at hex - this will affect how the weapon is animated/displayed in firing log!
		    //insert correct target coordinates: CURRENT target position
		    $pos = $targetShip->getHexPos();
		    $fireOrder->x = $pos->q;
		    $fireOrder->y = $pos->r;
		    $fireOrder->targetid = -1; //correct the error

	}//endof function calculateHitBase
	   
	
	public function fire($gamedata, $fireOrder)
	{ //sadly here it really has to be completely redefined... or at least I see no option to avoid this
		$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		/** @var MovementOrder $movement */
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = $movement->position;//at moment of launch!!!		
		//$this->calculateHit($gamedata, $fireOrder); //already calculated!
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled; ///and auto-hit ;)
		$fireOrder->shotshit++;
		$fireOrder->pubnotes .= "Interception applied to all weapons at target hex that are firing at Chaff-launching ship. "; //just information for player, actual applying was done in calculateHitBase method

		$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	} //endof function fire
	
	
        public function getDamage($fireOrder){
            return 0; //this weapon does no damage, in case it actually hits something!
        }
        public function setMinDamage(){     $this->minDamage = 0;      }
        public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof NexusChaffLauncher


/*custom extension of standard Particle Projector line*/
    class NexusParticleProjectorLight extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleProjectorLight";
        public $displayName = "Light Particle Projector";
	public $iconPath = "NexusParticleProjectorLight.png";
	    
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 10;
        public $animationWidth = 3;
        public $trailLength = 10;

        public $intercept = 2;
        public $loadingtime = 1;
        public $priority = 3;

        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 1;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(6, 1)+2;   }
        public function setMinDamage(){     $this->minDamage = 3 ;      }
        public function setMaxDamage(){     $this->maxDamage = 8 ;      }
    }


/*custom extension of standard Particle Projector line*/
    class NexusParticleProjectorHeavy extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleProjectorHeavy";
        public $displayName = "Heavy Particle Projector";
		public $iconPath = "NexusParticleProjectorHeavy.png";
	
		public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.35;
        public $projectilespeed = 17;
        public $animationWidth = 5;
        public $trailLength = 30;

        public $intercept = 1;
        public $loadingtime = 3;
        public $priority = 6;

        public $rangePenalty = 0.5; //-1/2hexes
        public $fireControl = array(0, 2, 3); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 4;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+8;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 28 ;      }
    }

	class NexusParticleProjectorFtr extends Particle{
		/*fighter-mounted version of medium Particle Projector*/
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleProjectorFtr";
        public $displayName = "Particle Projector";
        public $animation = "beam";
        public $animationColor = array(205, 200, 200);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 15;
        public $animationWidth = 4;
        public $trailLength = 20;
		public $iconPath = "particleProjector.png";

        public $intercept = 2;
        public $loadingtime = 2;
        public $priority = 5;

        public $rangePenalty = 1;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 1)+4;   }
        public function setMinDamage(){     $this->minDamage = 5 ;      }
        public function setMaxDamage(){     $this->maxDamage = 14 ;      }
    }//endof NexusParticleProjectorFtr


    class NexusParticleAgitator extends Particle{
        public $trailColor = array(30, 170, 255);

        public $name = "nexusParticleAgitator";
        public $displayName = "Particle Agitator";
		public $iconPath = "NexusParticleAgitator.png";
	    
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 4;
        public $trailLength = 10;

        public $intercept = 1;
        public $loadingtime = 2;
        public $priority = 6; //heavy Standard

        public $rangePenalty = 1; //-1/hex
        public $fireControl = array(0, 2, 2); // fighters, <mediums, <capitals

		public $firingMode = 'Standard'; //firing mode - just a name essentially
		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Particle"; //not important really
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 8;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){ return Dice::d(10, 2)+6;   }
        public function setMinDamage(){     $this->minDamage = 9 ;      }
        public function setMaxDamage(){     $this->maxDamage = 36 ;      }
    }
	
	
	

    class NexusInterceptorArray extends Particle{ 
        public $trailColor = array(30, 170, 255);

        public $name = "NexusInterceptorArray";
        public $displayName = "Interceptor Array";
        public $animation = "beam";
        public $animationColor = array(255, 250, 230);
        public $animationExplosionScale = 0.1;
        public $projectilespeed = 10;
        public $animationWidth = 3;
        public $trailLength = 8;
	    public  $iconPath = "NexusInterceptorArray.png";

        public $intercept = 3;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 1;


        public $rangePenalty = 2; //-2/hex
        public $fireControl = array(4, 2, 0); // fighters, <mediums, <capitals
        public $priority = 4; //light Standard 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 3 hexes.";
		}
		
		public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			//target must be within 3 hexes
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 3) return false;
			
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

        public function getDamage($fireOrder){        return Dice::d(3,2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }	//endof class NexusInterceptorArray


?>
