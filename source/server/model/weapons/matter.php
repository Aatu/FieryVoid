<?php

    class Matter extends Weapon
    {	    
        public $noOverkill = true;//Matter weapons do not overkill
    	public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    	public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!  
        public $priority = 9;     //most Matter weapons are best fired late in the queue, when systems are already stripped by other weapons
	    	    
        public $animation = "bolt";
        public $animationColor = array(250, 250, 190);
	    
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
    } //endof class Matter



    class MatterCannon extends Matter
    {
        public $name = "matterCannon";
        public $displayName = "Matter Cannon";
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        */
	    
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
        public $iconPath = "HeavyRailgun.png";
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 20;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.30;
*/
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
        public $iconPath = "Railgun.png";
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.25;
        */
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
        public $iconPath = "LightRailgun.png";
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 30;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
	*/
	    
        public $priority = 5;    //small enough damage that it's worth firing earlier - together with regular Medium weapons, I think    
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
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 10;
        public $animationWidth = 6;
        public $animationExplosionScale = 0.90;
	*/
		public $noInterceptDegradation = true;
        public $targetsImmobile = true;
        
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
		$this->data["Special"] .= "Weapon misses automatically except vs Speed 0 Enormous units. "     
			."<br>Weapon also misses automatically if launching unit speed is > 0. "  
			."<br>If weapon hits target, it always damages structure."
			."<br>Weapon can be intercepted without degradation (like ballistics)."; 
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
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 28;
        public $animationWidth = 4;
        public $animationExplosionScale = 0.20;
        public $trailLength = 8;
        */
	    
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
	    /*
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
        public $projectilespeed = 25;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.30;
        public $trailLength = 12;
        */
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
        public $iconPath = "rapidGatling.png";
	    /*
        public $animation = "trail";
        public $trailColor = array(225, 255, 150);
        public $animationColor = array(225, 225, 150);
        public $projectilespeed = 16;
        public $animationWidth = 2;
        public $trailLength = 40;
        public $animationExplosionScale = 0.15;
	*/
        public $guns = 2;
        public $intercept = 1;
        public $loadingtime = 1;
        public $ballisticIntercept = true;
        public $priority = 5; //low damage, worth firing early!
        
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
	    /*
    	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2 ;      }
        public function setMaxDamage(){     $this->maxDamage = 12 ;      }
	*/
    }

    
	/*Orieni fighter weapon*/
    class PairedGatlingGun extends LinkedWeapon{
        public $name = "pairedGatlingGun";
        public $displayName = "Paired Gatling Guns";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
	    /*
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
	*/
	    
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;        
        public $loadingtime = 1;
	    
		/* multi-mode ammo use tests 	
        public $firingModes = array(
            1 => "First",
            2 => "Second"
        );
		*/
		
        public $intercept = 2;
        public $ballisticIntercept = true;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
	    
	    public $noOverkill = true;	    
	    public $priority = 4;//more or less equivalent of d6+4, due to Matter properties
	    
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

			$ship = $gamedata->getShipById($fireOrder->shooterid);
            $this->ammunition--;//Deduct round just fired!			
			//Now need to remove Enhancement bonuses from saved ammo count, as these will be re-added in onConstructed()			
			foreach ($ship->enhancementOptions as $enhancement) {
			    $enhID = $enhancement[0];
				$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
			        if ($enhID == 'EXT_AMMO') $this->ammunition -= $enhCount;     	
				}
			}		
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


//New Version of Grome Flak Cannon which is more Tabletop Accurate - DK - April 2024
class GromeFlakCannon extends Weapon{ 
    public $name = "GromeFlakCannon";
    public $displayName = "Flak Cannon";
	public  $iconPath = "FlakCannon.png";

	 public $animation = "bolt";
    public $animationColor = array(255, 250, 230);
    public $animationExplosionScale = 0.5;
        
    public $priorityArray = array(1=>1, 2=>1);
	public $uninterceptableArray = array(1=>false, 2=>true);
	public $doNotInterceptArray = array(1=>false, 2=>true);

    public $intercept = 3;
    public $freeintercept = true; //can intercept fire directed at different unit
    public $freeinterceptspecial = true; //has own custom routine for deciding whether third party interception is legal
    public $loadingtime = 1;
	public $canInterceptUninterceptable = true;

	public $rangeArray = array(1=>0, 2=>100); //let's put maximum range here, but generous one
	
    public $rangePenaltyArray = array(1=>2, 2=>0);
    public $fireControlArray = array( 1=>array(4, null, null), 2=>array(0, 0, 0)); // fighters, <mediums, <capitals 
	
	public $firingModes = array(1=>'Anti-Fighter', 2=>'Defensive');
	public $damageTypeArray = array(1=>'Flash', 2=>'Standard'); //indicates that this weapon does damage in Pulse mode
    public $weaponClassArray = array(1=>'Matter', 2=>'Matter'); //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
    
	protected $autoHit = false;//To show 100% hit chance in front end.    
	protected $autoHitArray = array(1=>false, 2=>true); //To show 100% hit chance in front end.   
	
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
			if ( $maxhealth == 0 ) $maxhealth = 4;
            if ( $powerReq == 0 ) $powerReq = 2;            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
			$this->data["Special"] = "Intercepts all shots from an enemy ship automatically if not fired.";
			$this->data["Special"] .= "<br>Will also automatically intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 5 hexes.";
			$this->data["Special"] .= "<br>Can intercept uninterceptable weapons at 50% effectiveness, but not when intercepting for other units.";			
			$this->data["Special"] .= "<br>Can also be MANUALLY targeted to intercept specific units in Defensive firing mode";
			$this->data["Special"] .= "<br>In this mode it will automatically hit and intercept all fire from targeted ship at the Flak Cannon-firing ship (except ballistics).";
	}


	public function calculateHitBase($gamedata, $fireOrder){
			$this->changeFiringMode($fireOrder->firingMode);  //needs to be outside the switch routine

			switch($this->firingMode){
				case 1:
		
				parent::calculateHitBase($gamedata, $fireOrder);
				break;
		
			case 2: 		
				//hit chance always 100 - so it always hits and is correctly animated
				$fireOrder->needed = 100; //auto hit!
				$fireOrder->updated = true;

				//while we're at it - we may add appropriate interception orders!	
				if ($fireOrder->targetid >= 0) {//actual target is chosen...				
				$targetShip = $gamedata->getShipById($fireOrder->targetid);
				$allOrders = $targetShip->getAllFireOrders($gamedata->turn);
				
					foreach($allOrders as $subOrder) {
						if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->shooterid) ){ //something is firing at protected unit - except ballistics!
							$subWeapon = $targetShip->getSystemById($subOrder->weaponid);
							if(!$subWeapon->doNotIntercept){//just those that outright cannot be intercepted - like ramming or mass driver - will not be affected
								$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);//50% effectiveness for lasers etc handled here.
								$subOrder->numInterceptors++;
							}
						}
					}		
				}
				break;
			}
	}//endof function calculateHitBase
  
   
	public function fire($gamedata, $fireOrder) { 

			switch($this->firingMode){
				case 1:
					parent::fire($gamedata, $fireOrder);
					break;
				
				case 2:
					$shooter = $gamedata->getShipById($fireOrder->shooterid);
					$movement = $shooter->getLastTurnMovement($fireOrder->turn);
					$posLaunch = $movement->position;//at moment of launch!!!		
					$rolled = Dice::d(100);
					$fireOrder->rolled = $rolled; ///and auto-hit ;)
					$fireOrder->shotshit++;
					$fireOrder->pubnotes .= "<br>Interception applied to all weapons on target unit that are firing at Flak Cannon-firing ship. ";

					$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
					TacGamedata::$lastFiringResolutionNo++;    //note for further shots
					$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!

					break;
			}
	} //endof function fire
			
	public function canFreeInterceptShot($gamedata, $fireOrder, $shooter, $target, $interceptingShip, $firingWeapon){
			$distance = mathlib::getDistanceHex($interceptingShip, $target);
			if ($distance > 5) return false;//target must be within 5 hexes			
			if ($firingWeapon->uninterceptable) return false; //Cannot free intercept lasers etc.  Only matters for first shot.
								
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
			
	}//endof function canFreeInterceptShot


	public function getInterceptionMod($gamedata, $fireOrder){
		
		$interceptMod = parent::getInterceptionMod($gamedata, $fireOrder);

        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $firingweapon = $shooter->getSystemById($fireOrder->weaponid);		

		if($firingweapon->uninterceptable) $interceptMod = $interceptMod/2;	//It is marked uninterceptable e.g. lasers 50 % effectiveness.	
		
		return $interceptMod;
	}
	
	
	//on weapon being ordered to intercept - note which shot (fireorder, actually) was intercepted and intercept others at same target!
	public function fireDefensively($gamedata, $intercepted) //Gamedata and a fireOrder passed.
	{		
        $shooter = $gamedata->getShipById($intercepted->shooterid);
		$ship = $this->getUnit();      					

		$allOrders = $shooter->getAllFireOrders($gamedata->turn);
		
			foreach($allOrders as $subOrder) {
				//Now intercept everything else fired by Shooter at protected unit - but not original shot!								
				if (($subOrder->targetid == $intercepted->targetid) && ($subOrder->id != $intercepted->id) ){ 
					$subWeapon = $shooter->getSystemById($subOrder->weaponid);
					if(!$subWeapon->doNotIntercept){//Can be intercepted e.g. not Ramming / Mass Driver.
							if(!$subWeapon->uninterceptable){//Not marked uninterceptable, no further checks.		
								$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);
								$subOrder->numInterceptors++;						
							}else{//It is marked uninterceptable e.g. lasers
								if($subOrder->targetid == $ship->id){ //Has shooter targeted ship with Flak Cannon e.g. Not free intercept!, if so intercept at 50% effectiveness.
									$subOrder->totalIntercept += $this->getInterceptionMod($gamedata, $subOrder);//50% effectiveness handled here.
									$subOrder->numInterceptors++;
								}
							}
					}					
				}
			}					
	}//endof function fireDefensively	

    public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+2; //Anti-fighter shot
				break;	

			case 2:
				return 0; //Manual intercept
				break;
		}
	}
	
    public function setMinDamage(){ 
		switch($this->firingMode){
			case 1:
				$this->minDamage = 3; //Anti-fighter shot
				break;	
			case 2:
				$this->minDamage = 0; //Manual intercept
				break;
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
	
    public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 12; //Anti-fighter shot
				break;	
			case 2:
				$this->maxDamage = 0; //Manual intercept
				break;
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->autoHit = $this->autoHit;
            $strippedSystem->autoHitArray = $this->autoHitArray;                                       
            return $strippedSystem;
		}

}	//endof class GromeFlakCannon


	/*Grome fighter weapon*/
class SlugCannon extends LinkedWeapon{
        public $name = "SlugCannon";
        public $displayName = "Slug Cannon";
        public $animation = "trail";
        public $animationColor = array(250, 250, 190);
	    /*
        public $projectilespeed = 24;
        public $animationWidth = 2;
        public $trailLength = 12;
        public $animationExplosionScale = 0.15;
	*/
        public $shots = 2;
        public $defaultShots = 2;
        public $ammunition = 6;
	    public $iconPath = 'pairedGatlingGun.png';
        
        public $loadingtime = 1;

        public $intercept = 2; //"fighter mounted railgun" - and these don't generally intercept... more advanced Orieni designs can intercept ballistics, so let's allow that here too, as guidelines aren't clear...
        public $ballisticIntercept = true;

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
            $this->data["Special"] .= "<br>Can intercept ballistic weapons only.";
            $this->data["Ammunition"] = $this->ammunition;
        }

	    /*no longer needed
        public function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){
            return 0; //Matter ignores armor!
        }
	*/

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

       public function fire($gamedata, $fireOrder){ //note ammo usage
        	//debug::log("fire function");
            parent::fire($gamedata, $fireOrder);

			$ship = $gamedata->getShipById($fireOrder->shooterid);
			$ammo = $this->ammunition;
            $ammo--;//Deduct round just fired!			
			//Now need to remove Enhancement bonuses from saved ammo count, as these will be re-added in onConstructed()			
			foreach ($ship->enhancementOptions as $enhancement) {
			    $enhID = $enhancement[0];
				$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
			        if ($enhID == 'EXT_AMMO') $ammo -= $enhCount;     	
				}
			}		
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $ammo, $gamedata->turn);
        }
    
        public function getDamage($fireOrder){        return 3;   }
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 3;      }

}  //end SlugCannon


// Sshel'ath fighter weapon
class SingleSlugCannon extends SlugCannon{
	    public $iconPath = 'GatlingGun.png';
		public $shots = 1;
		public $intercept = 1;
        public $defaultShots = 1;
} //endof class SingleSlugCannon




class UnreliableMatterCannon extends MatterCannon{

    public $name = "UnreliableMatterCannon";
    public $displayName = "Unreliable Matter Cannon";
    public $iconPath = "matterCannon.png";

	protected $misfire1;

    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] .= "<br>This Matter Cannon is prone to misfires."; 
		$this->data["Special"] .= "<br>10% chance of misfire and doing no damage."; 
	}
	
	public function getDamage($fireOrder){
		$misfire1 = Dice::d(10,1);
		if ($misfire1 == 1) {
			$fireOrder->pubnotes .= "<br> Weapon misfire! No damage.";
			return (Dice::d(10, 2)+2) * 0;
		}else{
			return Dice::d(10, 2)+2;
		}
	}
	
	public function setMinDamage(){		$this->minDamage = 4;	}
	public function setMaxDamage(){		$this->maxDamage = 22;	}
	
} //endof class UnreliableMatterCannon




class UltraMatterCannon extends Matter{
    /*Kirishiac Weapon*/
        public $name = "UltraMatterCannon";
        public $displayName = "Ultra Matter Cannon";
		public $iconPath = "UltraMatterCannon.png";   
        public $animation = "bolt";
        public $animationColor = array(250, 250, 190);
        public $priority = 9;   

		public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
        public $loadingtime = 1;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(0, 5, 5); // fighters, <mediums, <capitals 

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 13;
            }
            if ( $powerReq == 0 ){
                $powerReq = 7;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function getDamage($fireOrder){        return Dice::d(10, 5)+5;   }
        public function setMinDamage(){     $this->minDamage = 10 ;      }
        public function setMaxDamage(){     $this->maxDamage = 55 ;      }
} //UltraMatterCannon


    class SniperCannon extends Matter{
        public $name = "SniperCannon";
        public $displayName = "Sniper Cannon";        
	    public $iconPath = "SniperCannon.png";
		
        public $animation = "torpedo";
        public $animationColor = array(192, 192, 192);
        
        public $loadingtime = 4;

        public $priority = 2; //Piercing shots go early, to do damage while sections aren't detroyed yet!
        
        public $firingModes = array(
            1 => "Piercing"
        );
        public $damageType = 'Piercing';
        public $weaponClass = "Matter"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
                
        public $rangePenalty = 0.25; //-1/4 hexes
        public $fireControl = array(null, 4, 6); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 10;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){
			return Dice::d(10, 4)+30;   
		}
        public function setMinDamage(){     $this->minDamage = 34 ;      }
        public function setMaxDamage(){     $this->maxDamage = 70 ;      }
        
    } //endof class SniperCannon




	
?>
