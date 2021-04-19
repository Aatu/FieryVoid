<?php

    class Matter extends Weapon
    {	    
        public $noOverkill = true;//Matter weapons do not overkill
    	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		//Matter ignores armor! - now this is hadled by default routines, $weaponClass= 'Matter' is enough

	/*no need due to noOverkill trait
        protected function getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata)
        {
            return null;
        }
	*/

        public function setSystemDataWindow($turn)
        {
            //$this->data["Weapon type"] = "Matter";
            //$this->data["Damage type"] = "Standard";
            parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Ignores armor, does not overkill.";
        }

        public $priority = 9;
    } //endof class Matter




    class MatterCannon extends Matter
    {
        public $name = "matterCannon";
        public $displayName = "Matter Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-2, 3, 3); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 2)+2;   }
        public function setMinDamage(){     $this->minDamage = 4 ;      }
        public function setMaxDamage(){     $this->maxDamage = 22 ;      }
    }
    

    class HeavyRailGun extends Matter
    {
        public $name = "heavyRailGun";
        public $displayName = "Heavy Railgun";
        public $animation = "trail";
        public $iconPath = "HeavyRailgun.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 20;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.30;

        public $loadingtime = 4;

        public $rangePenalty = 0.33;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 5)+7;   }
        public function setMinDamage(){     $this->minDamage = 12 ;      }
        public function setMaxDamage(){     $this->maxDamage = 57 ;      }    
    }
    

    class RailGun extends Matter
    {
        public $name = "railGun";
        public $displayName = "Railgun";
        public $animation = "trail";
        public $iconPath = "Railgun.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.25;
        
        public $loadingtime = 3;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-3, 2, 2); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){  return Dice::d(10, 3)+3;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 33 ;      }    
    }
    

    class LightRailGun extends Matter
    {
        public $name = "lightRailGun";
        public $displayName = "Light Railgun";
        public $animation = "trail";
        public $iconPath = "LightRailgun.png";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $priority = 6;
        
        public $loadingtime = 2;
		
        public $rangePenalty = 1.0;
        public $fireControl = array(3, 2, 0); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
    	public function getDamage($fireOrder){        return Dice::d(10, 1)+5;   }
        public function setMinDamage(){     $this->minDamage = 6 ;      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;      }    
    }
    


    class MassDriver extends Matter
    {
        public $name = "massDriver";
        public $displayName = "Mass Driver";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 10;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.90;
		public $noInterceptDegradation = true;
        //public $targetImmobile = true;
        
        public $loadingtime = 4;
		
        public $rangePenalty = 0.17;
        public $fireControl = array(null, null, 2); // fighters, <mediums, <capitals 

	    public function setSystemDataWindow($turn){ 
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Weapon misses automatically except vs speed 0 Enormous units. "     
			."<br>Weapon misses automatically if launching unit speed is > 0. "  
			."<br>Weapon always hits Structure. "
			."<br>Weapon can be intercepted without degradation (like ballistics). "; 
	}	    

	    
	public function calculateHitBase($gamedata, $fireOrder){ //auto-miss if restrictions not met
		$canHit = true;
		$pubnotes = '';
		
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
		
		if(!$target->Enormous){	$canHit=false; $pubnotes.= ' Target is not Enormous. '; }
		if($target->getSpeed()>0){ $canHit=false; $pubnotes.= ' Target speed >0. '; }
		if($shooter->getSpeed()>0){ $canHit=false; $pubnotes.= ' Shooter speed >0. '; }
			
		if($canHit){
			parent::calculateHitBase($gamedata, $fireOrder);
		}else{ //accurate targeting with this weapon not possible!
			$fireOrder->needed = 0;
        		$fireOrder->notes = 'ACCURATE FIRING CRITERIA NOT MET';
			$fireOrder->pubnotes .= $pubnotes;   
        		$fireOrder->updated = true;
		}
	}
	    
	    
	public function damage($target, $shooter, $fireOrder, $gamedata, $damage, $forcePrimary = false){ //always hit Structure...
		if ($target->isDestroyed()) return;
		$tmpLocation = $fireOrder->chosenLocation;	

		$system = $target->getStructureSystem($tmpLocation);
		if ($system == null || $system->isDestroyed()) $system = $target->getStructureSystem(0);//facing Structure nonexistent, go to PRIMARY
		if ($system == null || $system->isDestroyed()) return; //PRIMARY Structure nonexistent also
		$this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, $tmpLocation);
	}	    
	    	    
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 8)+ 60;   }
        public function setMinDamage(){     $this->minDamage = 68 ;      }
        public function setMaxDamage(){     $this->maxDamage = 140 ;      }
    } //end of Mass Driver


    class GaussCannon extends MatterCannon
    {
        public $name = "gaussCannon";
        public $displayName = "Gauss Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 28;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $trailLength = 8;
        
        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(-3, 1, 2); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 1)+10;   }
        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 20 ;      }
    }


    class HeavyGaussCannon extends GaussCannon{
        public $name = "heavyGaussCannon";
        public $displayName = "Heavy Gauss Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.30;
        public $trailLength = 12;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.66;
        public $fireControl = array(-2, 2, 3); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 3)+10;   }
        public function setMinDamage(){     $this->minDamage = 13;      }
        public function setMaxDamage(){     $this->maxDamage = 40 ;      }
	}


    class RapidGatling extends Matter{
        public $name = "rapidGatling";
        public $displayName = "Rapid Gatling Railgun";
        public $animation = "trail";
        public $trailColor = array(225, 255, 150);
        public $animationColor = array(225, 225, 150);
        public $projectilespeed = 16;
        public $animationWidth = 2;
        public $trailLength = 40;
        public $animationExplosionScale = 0.15;
        public $guns = 2;
        public $intercept = 1;
        public $loadingtime = 1;
        public $ballisticIntercept = true;
        public $priority = 6;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, 2, 0); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
            $this->data["Special"] .= "Can intercept ballistic weapons only.";
        }
	    
        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }
    

    class OrieniGatlingRG extends RapidGatling{
    	/*RapidGatling's predecessor*/ 
        public $displayName = "Gatling Railgun";
    	public $guns = 1;
    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
    }

    
	/*Orieni fighter weapon*/
    class PairedGatlingGun extends LinkedWeapon{
        public $name = "pairedGatlingGun";
        public $displayName = "Paired Gatling Guns";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;
	    
        
        public $loadingtime = 1;

        public $intercept = 2;
        public $ballisticIntercept = true;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
	    
	    public $noOverkill = true;
	    
	    public $priority = 4;//equivalent of d6+4, due to Matter properties 
		
	    
	    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  
	 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();
    
            $strippedSystem->ammunition = $this->ammunition;
           
            return $strippedSystem;
        }
        
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, does not overkill.";
            $this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
        }


        public function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }



        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }


       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function getDamage($fireOrder){
            $dmg = Dice::d(6, 1);
            return $dmg;
        }
        public function setMinDamage(){     $this->minDamage = 1;      }
        public function setMaxDamage(){     $this->maxDamage = 6;      }

    }


   class MatterGun extends PairedGatlingGun{
	   /*Belt Alliance fighter weapon, with limited ammo - poorer cousin of Orieni fighter weapon*/
        public $name = "MatterGun";
        public $displayName = "Matter Gun";  
	    public $iconPath = 'pairedGatlingGun.png';
	   
		public $priority = 3; //equivalent of d6+2, due to Matter properties
	  
        public function getDamage($fireOrder){ //d6-1, minimum 1
            $dmg = Dice::d(6, 1) -1;
			$dmg = max(1,$dmg);
            return $dmg;
        }
		public function setMinDamage(){     $this->minDamage = 1;      }
        public function setMaxDamage(){     $this->maxDamage = 5;      }
    }


   class FlakCannon extends Weapon{ 
/*Dual mode weapon based off the EA Laser-Pulse Array code. This is an attempt to allow
the Grome Flak Cannon to operate more as it did in the table top verion. The previous attempt 
worked fine offensively. Defensively, this had friendly intercept and intercepted lasers, 
but it only intercepted a single shot; not all fire from a single unit. After feedback from
the Fiery Void community, I aim giving the Flak Cannon the option to manually target a unit
in order to provide intercept against all shots from that unit. I need the Laser-Pulse Array
format so that the offensive mode against fighters is available. Lastly, the friendly intercept
function will remain. If the Flak Cannon is not fired offensively and is not fired defensively
manually, it will follow the automatic intercept routines. This will only be against single 
weapons, but the Flak Cannon will still intercept lasers in this mode and can still intercept
for friendly units.*/

/*This will be set up to function as follows:
1. Default mode will be manual intercept to gain full intercept against all shots from a single unit.
2. Option to switch to offensive, anti-fighter mode.
3. If nothing is done, it will follow, automated intercept routines.*/


        public $name = "FlakCannon";
        public $displayName = "Flak Cannon";
	    public  $iconPath = "FlakCannon.png";

		public $animationArray = array(1=>'trail', 2=>'trail');
        public $animationColorArray = array(1=>array(255, 250, 230), 2=>array(255, 250, 230));
        public $animationWidthArray = array(1=>2, 2=>4);
		public $trailColor = array(30, 170, 255);
        public $trailLength = 5;
        public $projectilespeed = 10;
        public $animationExplosionScaleArray = array(1=>0.6, 2=>0.6);//not used for Laser animation?...

	//actual weapons data
        public $priorityArray = array(1=>1, 2=>1);
		public $uninterceptableArray = array(1=>true, 2=>false);

        public $intercept = 3;
        public $freeintercept = true; //can intercept fire directed at different unit
        public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
        public $loadingtime = 1;

//        public $output = 3;
		public $canInterceptUninterceptable = true;

		public $range = 100; //let's put maximum range here, but generous one
	
        public $loadingtimeArray = array(1=>1, 2=>1); //mode 1 should be the one with longest loading time
        public $rangePenaltyArray = array(1=>0, 2=>2);
        public $fireControlArray = array( 1=>array(50, 50, 50), 2=>array(4, null, null) ); // fighters, <mediums, <capitals 
	
		public $firingModes = array(1=>'Intercept', 2=>'Anti-fighter');
		public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    	public $weaponClassArray = array(1=>'Matter', 2=>'Matter'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Can intercept lasers.";
			$this->data["Special"] .= "<br>May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes. Friendly intercept only engages individual shots.";
			$this->data["Special"] .= "<br>If manually targeted in Intercept (I) mode, will intercept all fire from targeted ship, with usual intercept degredation, at the Flak Cannon-firing ship.";
			$this->data["Special"] .= "<br>Offensiver, Anti-fighter mode (A) targets fighters only, but as a matter weapon doing damage in flash mode.";
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
					$posLaunch = $movement->position;//at moment of launch!!!		
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

    }	//endof class FlakCannon






	/*Grome fighter weapon*/
    class SlugCannon extends LinkedWeapon{
        public $name = "SlugCannon";
        public $displayName = "Slug Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;
	    public $iconPath = 'pairedGatlingGun.png';
        
        public $loadingtime = 1;

        public $intercept = 2;
//        public $ballisticIntercept = true;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
	    
	    public $noOverkill = true;
	    
	    public $priority = 3;//equivalent of d6+4, due to Matter properties 
		
	    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();
    
            $strippedSystem->ammunition = $this->ammunition;
           
            return $strippedSystem;
        }
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ignores armor, does not overkill.";
//            $this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
        }

        public function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }
    
        public function getDamage($fireOrder){        return 3;   }
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 3;      }

    }  //end SlugCannon
	
?>
