<?php
/*
********************************
*all EM weapons should look for EMHardened trait, and treat is as they would AdvancedArmor.
* this is rough simplification of how this trait should affect them (see Ipsha for details, Militaries of the League 2)
********************************
*/

class WeaponEM  {	
	public static function isTargetEMResistant($ship,$system = null){ //returns true if target has Advanced Armor or EM Hardening (which, for simplicity, in FV is treated as AA would be for EM weapons)
		if($ship){
			$EMHardened = $ship->getEMHardened();
			if($ship->advancedArmor) return true;
			if($EMHardened) return true;
		}else if ($system){
			if($system->advancedArmor) return true;
		}
		return false;
	}
}


class PlasmaStream extends Plasma{
	public $name = "plasmaStream";
	public $displayName = "Plasma Stream";
	
	public $animation = "laser";
	public $priority = 2; //early, due to armor reduction effect
		        
	public $raking = 5;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 1;	
	public $rangePenalty = 1;
	public $fireControl = array(-4, 2, 2); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Plasma"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

		public $firingModes = array(
			1 => "Raking"
		);
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 9;
		if ( $powerReq == 0 ) $powerReq = 7;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
        
	
	public function setSystemDataWindow($turn){		
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) { //Plasma class covers basic Plasma properties
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
	    $this->data["Special"] .= "Reduces armor of systems hit.";	
	    $this->data["Special"] .= "<br>Does not ignore already pierced armor (eg. every rake needs to pierce armor anew, even to the same location).";
	}
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (!$system->advancedArmor){//advanced armor prevents effect
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true; //in effect immediately, affecting further damage in the same turn!
			$system->setCritical($crit); //$system->criticals[] =  $crit;			
			//and previous turn crit - to be NOT saved, but so crit is recognized as
		}
	}
	
	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
		parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
		$fireOrder->armorIgnored = array(); //clear armorIgnored array - next rake should be met with full armor value!
	}
	
	public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
	public function setMinDamage(){     $this->minDamage = 7 ;      }
	public function setMaxDamage(){     $this->maxDamage = 34 ;      }
}//endof class PlasmaStream


class DualPlasmaStream extends PlasmaStream{
	public $name = "DualPlasmaStream";
	public $displayName = "Dual Plasma Stream";
	public $iconPath = "DualPlasmaStream.png"; 	
	
	//only properties differing form single Plasma Stream
	public $rangeDamagePenalty = 2;	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 10;
		if ( $powerReq == 0 ) $powerReq = 10;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	
		
	public function getDamage($fireOrder){        return Dice::d(10,6)+8;   }
	public function setMinDamage(){     $this->minDamage = 14;     }
	public function setMaxDamage(){     $this->maxDamage = 68;      }
	
}//endof class DualPlasmaStream




/* Plasma Streams reworked to be animated closer to regular Plasma!
class PlasmaStream extends Raking{
	public $name = "plasmaStream";
	public $displayName = "Plasma Stream";
	
	public $animation = "laser";
	public $animationColor = array(75, 250, 90);
	public $priority = 2; //early, due to armor reduction effect
		        
	public $raking = 5;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 1;	
	public $rangePenalty = 1;
	public $fireControl = array(-4, 2, 2); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Plasma"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

		public $firingModes = array(
			1 => "Raking"
		);
	
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
	    $this->data["Special"] .= "Damage reduced by 1 point per hex.";
	    $this->data["Special"] .= "<br>Reduces armor of systems hit.";	
	    $this->data["Special"] .= "<br>Ignores half of armor.";	 //now handled by standard routines
	    $this->data["Special"] .= "<br>Does not ignore already pierced armor (eg. every rake needs to pierce armor anew, even to the same location).";
	}
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (!$system->advancedArmor){//advanced armor prevents effect 
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true; //in effect immediately, affecting further damage in the same turn!
			$system->criticals[] =  $crit;			
			//and previous turn crit - to be NOT saved, but so crit is recognized as
		}
	}
	
	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
		parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
		$fireOrder->armorIgnored = array(); //clear armorIgnored array - next rake should be met with full armor value!
	}
	
	public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
	public function setMinDamage(){     $this->minDamage = 7 ;      }
	public function setMaxDamage(){     $this->maxDamage = 34 ;      }
}//endof class PlasmaStream


class DualPlasmaStream extends Raking{
	public $name = "DualPlasmaStream";
	public $displayName = "Dual Plasma Stream";
	public $iconPath = "DualPlasmaStream.png"; 	
	
	public $priority = 2;
		        
	public $raking = 5;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 2;	
	public $rangePenalty = 1;
	public $fireControl = array(-4, 2, 2);
	
	public $damageType = "Raking"; 
	public $weaponClass = "Plasma";

	public $firingModes = array(1 => "Raking");
	
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
	    $this->data["Special"] .= "Damage reduced by 2 points per hex.";
	    $this->data["Special"] .= "<br>Reduces armor of systems hit.";	
	    $this->data["Special"] .= "<br>Ignores half of armor.";
	}
		 
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (!$system->advancedArmor){//advanced armor prevents effect 
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
			$crit->updated = true;
			    $crit->inEffect = false;
			    $system->criticals[] =  $crit;
		}
	}
		
	public function getDamage($fireOrder){        return Dice::d(10,6)+8;   }
	public function setMinDamage(){     $this->minDamage = 14;     }
	public function setMaxDamage(){     $this->maxDamage = 68;      }
	
}//endof class DualPlasmaStream
*/


class ShockCannon extends Weapon{
        public $name = "shockCannon";
        public $displayName = "Shock Cannon";
	
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

	public function getDamage($fireOrder){        return Dice::d(10)+4;   }
	public function setMinDamage(){     $this->minDamage = 5 /*- $this->dp*/;      }
	public function setMaxDamage(){     $this->maxDamage = 14 /*- $this->dp*/;      }
}//endof class ShockCannon



class BurstBeam extends Weapon{
	public $name = "burstBeam";
	public $displayName = "Burst Beam";
	
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
	}		
		
	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class BurstBeam


	class DualBurstBeam extends BurstBeam{
		public $name = "dualBurstBeam";
		public $displayName = "Dual Burst Beam";
		public $guns = 2;
	}


class BurstPulseCannon extends Pulse {
	public $name = "burstPulseCannon";
        public $displayName = "Burst Pulse Cannon";

        public $animation = "bolt";
        public $animationColor = array(158, 240, 255);
        public $animationExplosionScale = 0.3; //does no damage, so needs scale indicator
	/*
	public $trailColor = array(158, 240, 255);
        public $animation = "trail";
        public $trailLength = 2;
        public $animationWidth = 4;
        public $projectilespeed = 17;
        public $animationExplosionScale = 0.05;
	*/
        public $rof = 4;
        public $grouping = 25;
        public $maxpulses = 6;
		        
	public $loadingtime = 2;
        public $priority = 9; //late due to dropout/disable effect
        
	    public $damageType = "Pulse"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
			
        public $rangePenalty = 0.5;
        public $fireControl = array(2, 3, 4); // fighters, <=mediums, <=capitals 


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
	
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){ //if target is protected by EM shield, that shield is hit automatically
		if($target instanceof FighterFlight){ //for fighters - regular effect
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
	}		
	
	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class BurstPulseCannon



class MediumBurstBeam extends BurstBeam{
	public $name = "mediumBurstBeam";
	public $displayName = "Medium Burst Beam";

	public $animationExplosionScale = 0.4;
	/*
	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 12;
	public $animationWidth = 3;
	public $animationWidth2 = 0.4;
	public $animationExplosionScale = 0.20;
	public $trailLength = 40;
*/
	public $loadingtime = 2;
	public $priority = 9;

	public $rangePenalty = 0.5;
	public $fireControl = array(0, 3, 4); // fighters, <=mediums, <=capitals 

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}

	    
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//specifically override inherited Special - it has a bit different (stronger) effect:
		      $this->data["Special"] = "Effect depends on system hit:";    
		      $this->data["Special"] .= "<br> - Structure: Reactor output reduced by 2."; 
		      $this->data["Special"] .= "<br> - Powered system: forced shutdown for 2 turns."; 
		      $this->data["Special"] .= "<br> - Other system: critical roll forced (at +6)."; 
		      $this->data["Special"] .= "<br> - Superheavy fighter: 1/6 chance of immediate dropout."; 
		      $this->data["Special"] .= "<br> - Other fighter: immediate dropout."; 
		      $this->data["Special"] .= "<br> - Any fighter: 1d6 to 3d6 damage (ignoring armor)."; 
		      $this->data["Special"] .= "<br>Automatically hits EM shield if interposed.";
		      $this->data["Special"] .= "<br>Does not affect units protected by Advanced Armor (other than fighter damage).";  	
	} //a lot of the above is handled by methods inherited from BurstBeam class


	public function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$dmgToReturn = $damage;
		if ($ship instanceof FighterFlight){ //dealing 1d6 to 3d6 damage
			$roll = Dice::d(6);
			$dmgDice = 1;
			if($roll<=3) $dmgDice+=2; //50% chance for additional 2 dice of damage
			$dmgToReturn += Dice::d(6,$dmgDice);
		}
		return $dmgToReturn;
	}
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;	

		if (WeaponEM::isTargetEMResistant($ship,$system)){ //no actual effect on AA-protected ship! - damage itself is already dealt (if any)
		    return;
	    }		

		if ($system instanceof Fighter){ //regular fighter drops out automatically, superheavy on a roll of 1 on d6
			$roll = Dice::d(6);
			if ((!$ship->superheavy) || ($roll < 2)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->setCritical($crit); //$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}
		}
		else if ($system instanceof Structure){
			$reactor = $ship->getSystemByName("Reactor");
			$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced2", $gamedata->turn);
			$crit->updated = true;
			$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
		}
		else if ($system->powerReq > 0 || $system->canOffLine ){
			$crit = new ForcedOfflineForTurns (-1, $ship->id, $system->id, "ForcedOfflineForTurns", $gamedata->turn, $gamedata->turn+2);
			$crit->updated = true;
			$system->setCritical($crit); //$system->criticals[] = $crit;
		}
		else {//force critical roll at +6
			$system->forceCriticalRoll = true;
			$system->critRollMod += 6;
		}
	}    
}//endof class MediumBurstBeam



class HeavyBurstBeam extends BurstBeam{
	public $name = "heavyBurstBeam";
	public $displayName = "Heavy Burst Beam";

	public $animationExplosionScale = 0.6;
	/*
	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 10;
	public $animationWidth = 4;
	public $animationWidth2 = 0.5;
	public $animationExplosionScale = 0.30;
	public $trailLength = 50;
*/
	public $loadingtime = 3;
	public $priority = 9;

	public $rangePenalty = 0.33;
	public $fireControl = array(2, 4, 5); // fighters, <=mediums, <=capitals 

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	    
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		//specifically override inherited Special - it has a bit different (stronger) effect:
		      $this->data["Special"] = "Effect depends on system hit:";    
		      $this->data["Special"] .= "<br> - Structure: Reactor output reduced by 4."; 
		      $this->data["Special"] .= "<br> - Powered system: forced shutdown for 3 turns."; 
		      $this->data["Special"] .= "<br> - Other system: critical roll forced (at +8)."; //no mention of it but it's a logical scale-up - I consider lack of appropriate fragment to be an omission 
		      $this->data["Special"] .= "<br> - Superheavy fighter: 1/3 chance of immediate dropout."; 
		      $this->data["Special"] .= "<br> - Other fighter: immediate dropout."; 
		      $this->data["Special"] .= "<br> - Any fighter: 5d6 damage (ignoring armor).";
		      $this->data["Special"] .= "<br>Automatically hits EM shield if interposed.";
		      $this->data["Special"] .= "<br>Effects other than direct damage do not affect units protected by Advanced Armor.";  	
	}//a lot of the above handled by methods inherited from BurstBeam	    
		
	public function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$dmgToReturn = $damage;
		if ($ship instanceof FighterFlight){ //dealing 5d6 damage
			$dmgToReturn += Dice::d(6,5);
		}
		return $dmgToReturn;
	}
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;	

		if (WeaponEM::isTargetEMResistant($ship,$system)){ //no actual effect on AA-protected ship! - damage itself is already dealt (if any)
		    return;
	    }		

		if ($system instanceof Fighter){ //regular fighter drops out automatically, superheavy on a roll of 1 on d3
			$roll = Dice::d(3);
			if ((!$ship->superheavy) || ($roll < 2)){
				$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->setCritical($crit); //$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}
		}
		else if ($system instanceof Structure){
			$reactor = $ship->getSystemByName("Reactor");
			$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced4", $gamedata->turn);
			$crit->updated = true;
			$reactor->setCritical($crit); //$reactor->criticals[] =  $crit;
		}
		else if ($system->powerReq > 0 || $system->canOffLine ){
			$crit = new ForcedOfflineForTurns (-1, $ship->id, $system->id, "ForcedOfflineForTurns", $gamedata->turn, $gamedata->turn+3);
			$crit->updated = true;
			$system->setCritical($crit); //$system->criticals[] = $crit;
		}
		else {//force critical roll at +8
			$system->forceCriticalRoll = true;
			$system->critRollMod += 8;
		}
	}    
	    
}//endof class HeavyBurstBeam

    
class TractorBeam extends ShipSystem{
	public $name = "tractorBeam";
	public $displayName = "Tractor Beam";
	
	public function setSystemDataWindow($turn){
		  parent::setSystemDataWindow($turn); 
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}	    
		  $this->data["Special"] .= "No in-game effect. Used to move or drag objects without physical contact.";  
	}	
  
	function __construct($armour, $maxhealth, $powerReq, $output ){
		parent::__construct($armour, $maxhealth, $powerReq, $output );
	}
}


class ElectroPulseGun extends Weapon{
	public $name = "electroPulseGun";
	public $displayName = "Electro-Pulse Gun";
	
	public $animation = "bolt"; //originally Laser, but Bolt seems more appropriate
	public $animationColor = array(158, 240, 255);
	public $animationExplosionScale = 0.25; //does no damage, but quite large animation seems appropriate ;)
	/*
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	*/
	public $priority = 1; //flat dropout

	public $loadingtime = 2;
	public $rangePenalty = 3;
	public $fireControl = array(3, null, null); // fighters, <=mediums, <=capitals
	public $calledShotMod = 0; //can call shot at no penalty! (eg. pick off undamaged fighter)

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
		$this->data["Special"] .= 'Forces dropout on fighters (except superheavy). Can pick particular fighter at no penalty.';
	}

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		// On a hit, make fighters drop out, but if this weapon had
		// a ReducedDamage crit, roll a d6 and substract 2 for each
		// ReducedDamage crit. If the result is less than 1, the hit
		// has no effect on the fighter.
		if (!WeaponEM::isTargetEMResistant($ship,$system)){
			$crit = null;
			$affect = Dice::d(6);

			foreach ($this->criticals as $crit){
			if ($crit instanceof ReducedDamage){
				$affect = $affect - 2;
			}
			}

			if ( ($system instanceof Fighter) && (!($ship->superheavy)) && ($affect > 0)){
			$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->setCritical($crit); //$system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
			}
		}
		
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
	}

	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class ElectroPulseGun



class StunBeam extends Weapon{
	public $name = "StunBeam";
	public $displayName = "Stun Beam";
	public  $iconPath = "stunBeam.png";
	
	public $animation = "bolt"; //originally laser, but bolt seems more appropriate
	public $animationColor = array(158, 240, 255);
	public $animationExplosionScale = 0.25;
	/*
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	*/
	
	public $loadingtime = 2;
	public $priority = 9; //as antiship weapon; as antifighter should go first...

	public $rangePenalty = 1;
	public $fireControl = array(0, 2, 4); // fighters, <=mediums, <=capitals 
		    
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
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
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Doesn't deal damage. Effect depends on system hit:.";      
		$this->data["Special"] .= "<br> - Weapon, Jammer or Jump Engine: System deactivated for one turn.";
		$this->data["Special"] .= "<br> - Thruster: Thruster cannot be used for one turn."; 		 
		$this->data["Special"] .= "<br> - C&C: -20 Initiative for one turn."; 
		$this->data["Special"] .= "<br> - Scanner: Output halved for one turn."; 
		$this->data["Special"] .= "<br> - Engine: Output halved for one turn.";
		$this->data["Special"] .= "<br> - No effect on Structure or any other type of system.";		
		$this->data["Special"] .= "<br>Forces dropout on fighters (except superheavy)."; 
		$this->data["Special"] .= "<br>Does not affect ships with advanced armor.";  		    
	}		
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;		
		if ($system->isDestroyed()) return; //no point allocating
		if ($ship->isDestroyed()) return; //no point allocating				
		if ($system->advancedArmor) { //no effect on Advanced Armor but Ipsha etc still get affected.
		$fireOrder->pubnotes .= "<br> Stun Beam has no effect on advanced armor.";				
		return; 	
		}
		if($system instanceOf Structure){ //No effect on Structure.
		$fireOrder->pubnotes .= "<br> Stun Beam impacted harmlessly on structure.";				
		return; 
		}else if ($system instanceOf Weapon || $system instanceOf JumpEngine || $system instanceOf Jammer){ //Deactivate for 1 turn.
			if ($system->powerReq > 0 || $system->canOffLine ){
			$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
			}
		}else if ($system instanceOf Thruster){ //Can't deactivate thurster, but can render it unusable for 1 turn using mulitple FirstThrustIgnored crits.
			$thrusterOutput = $system->getOutput();	
				for($i=1; $i<=$thrusterOutput;$i++){
					$crit = new FirstThrustIgnoredOneTurn(-1, $ship->id, $system->id, 'FirstThrustIgnoredOneTurn', $gamedata->turn); 
					$crit->updated = true;
			        $system->criticals[] =  $crit;
				}     			
		}else if($system instanceOf CnC) { // -20 Initiative, so just ReducedIniativeOneTurn twice.
			$system->addCritical($ship->id, "ReducedIniativeOneTurn", $gamedata);			
			$system->addCritical($ship->id, "ReducedIniativeOneTurn", $gamedata);
		}else if($system instanceOf Scanner || $system instanceOf Engine) { //Halve output for 1 turn.					
			$system->addCritical($ship->id, "OutputHalvedOneTurn", $gamedata);								
		}else if ($system instanceof Fighter && !($ship->superheavy)){ //Dropout unless super heavy fighter.
			$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->setCritical($crit); //$system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
				}else{ //No other types of systems are effected.
						$fireOrder->pubnotes .= "<br> Stun Beam has no effect.";				
						return; 
				}		
	}//end of onDamagedSystem

	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }
}//endof class StunBeam



class CommDisruptor extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Initiative and Sensors next turn
    */
    public $name = "CommDisruptor";
    public $displayName = "Comm Disruptor";
	public $iconPath = "commDIsruptor.png";
	
	//let's animate this as a very wide beam...
	public $animation = "bolt";
        public $animationColor = array(150, 150, 220);
        public $animationExplosionScale = 0.55;
	/*
        public $animationColor2 = array(170, 170, 250);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 15;
        public $animationWidth2 = 0.5;
	*/
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 3;
    public $rangePenalty = 0.5; //-1/2 hexes
    public $intercept = 0;
    public $fireControl = array(-1, 2, 3);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   	   
	
 	protected $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    
	      $this->data["Special"] .= "Does no damage, but weakens target's Initiative (-1d6) and Sensors/OB (-1d6) rating next turn";  
    }	
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		
		$effectIni = Dice::d(6,1);//strength of effect: 1d6
		$effectSensors = Dice::d(6,1);//strength of effect: 1d6
		$effectIni5 = $effectIni * 5;
		$fireOrder->pubnotes .= "<br> Initiative reduced by $effectIni5, Sensors by $effectSensors.";
		
		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effectSensors;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $firstFighter->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $firstFighter->id, 'tmpinidown', $gamedata->turn); 
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
} //end of class CommDisruptor



class CommJammer extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Initiative  next turn
    */
    public $name = "CommJammer";
    public $displayName = "Comm Jammer";
	public $iconPath = "commJammer.png";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 3;
    public $rangePenalty = 1; //-1/hex
    public $intercept = 0;
    public $fireControl = array(0, 2, 2);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   
	   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationExplosionScale = 0.35;
	/*
        public $animationColor2 = array(160, 160, 240);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;
	*/
	
 	protected $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
	      $this->data["Special"] = "Does no damage, but weakens target's Initiative (-1d6) rating next turn.";  
    }	
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 4;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
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
			        	$CnC->setCritical($crit); //$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class CommJammer


class ImpCommJammer extends CommJammer{
    /*Abbai weapon - does no damage, but limits target's Initiative and Sensors next turn
    */
    public $name = "ImpCommJammer";
    public $displayName = "Improved Comm Jammer";
	public $iconPath = "commJammer.png";
	
    public $rangePenalty = 0.5; //-1/2 hexes
} //end of class ImpCommJammer




class SensorSpear extends Weapon{
    /*Abbai weapon - does no damage, but limits target's Sensors next turn
    */
    public $name = "SensorSpear";
    public $displayName = "Sensor Spear";
	public $iconPath = "sensorSpike.png";
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 2;
    public $rangePenalty = 0.5; //-1/2 hexes
    public $intercept = 0;
    public $fireControl = array(-1, 1, 1);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   
	   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationExplosionScale = 0.35;
	/*
        public $animationColor2 = array(160, 160, 240);
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;
	*/
	
 	protected $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors/OB (-1d3) rating next turn.";  
    }	
    
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
	    //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
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
			        	$CnC->setCritical($crit); //$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class SensorSpear


class SensorSpike extends SensorSpear{
    /*Abbai weapon - does no damage, but limits target's Sensors next turn
    */
    public $name = "SensorSpike";
    public $displayName = "Sensor Spike";
	public $iconPath = "sensorSpike.png";
	

    public $fireControl = array(-1, 1, 2);
	
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);  
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors/OB (-1d6) rating next turn";  
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
			        	$CnC->setCritical($crit); //$CnC->criticals[] =  $crit;
				}
			}
		}
	} //endof function onDamagedSystem

	public function getDamage($fireOrder){ return  0;   }
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }
} //end of class SensorSpike




class EmBolter extends Weapon{
    /*EM Bolter - Ipsha weapon*/
        public $name = "EmBolter";
        public $displayName = "EM Bolter";
	public $iconPath = "EMBolter.png";
	
        public $animation = "bolt";
        public $animationColor = array(100, 100, 250);
	/*
        public $projectilespeed = 14;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.45;
        */
	public $priority = 6;
      
        public $loadingtime = 1;
        
        public $rangePenalty = 0.33; //-1/3 hexes
        public $fireControl = array(0, 3, 3); // fighters, <mediums, <capitals 
	
	
	    public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	private $cooldown = 2;
	
	private $alreadyResolved = false;
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Cooldown period: 2 turns.";  
		      $this->data["Special"] .= "<br>+1 to all critical rolls made by target this turn.";  
	    }	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no special effect on Advanced Armor
		if ($this->alreadyResolved) return; //effect already applied this turn
		
		$this->alreadyResolved = true;
		if (!($ship instanceof FighterFlight)){
			$ship->critRollMod++; //+1 to all critical rolls made by target this turn 
		}

	} //endof function onDamagedSystem
	
	
	public function fire($gamedata, $fireOrder){
		// If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
		parent::fire($gamedata, $fireOrder);
		
		$turnEndEffect = $gamedata->turn + $this->cooldown;
		$crit = new ForcedOfflineForTurns(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $turnEndEffect);
		$crit->updated = true;
		$this->criticals[] = $crit;
	} //endof function fire
		
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $aftFacing=false)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 10;
            }
            if ( $powerReq == 0 ){
                $powerReq = 9;
            }
			//switch to Aft-facing icons for weapons that are facing Aft-oriented!
			if($aftFacing){
				$this->iconPath = "EMBolterAft.png";
			}
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
        public function getDamage($fireOrder){        return 21;   }
        public function setMinDamage(){     $this->minDamage = 21 ;      }
        public function setMaxDamage(){     $this->maxDamage = 21 ;      }
} //endof class EmBolter



/*handles creation of firing orders for Spark Fields*/
class SparkFieldHandler{
	public $name = "sparkFieldHandler";
	private static $sparkFields = array();
	private static $firingDeclared = false;
	
	
	//should be called by every SparkField on creation!
	public static function addSparkField($weapon){
		SparkFieldHandler::$sparkFields[] = $weapon;		
	}
	
	//compares boost levels of fields
	//	lowest boost first (will potentially do more damage)
	//	owner irrelevant, as weapon will damage everything in range except firing unit itself
	public static function sortByBoost($fieldA, $fieldB){	    
		if ($fieldA->boostlevel < $fieldB->boostlevel){ //low boost level first
		    return -1;
		}else if ($fieldA->boostlevel > $fieldB->boostlevel){
		    return 1;
		}else{
		    return 0;
		}   
	} //endof function sortByBoost
	
	
	public static function createFiringOrders($gamedata){
		if (SparkFieldHandler::$firingDeclared) return; //already done!
		SparkFieldHandler::$firingDeclared = true;
		
		//apparently ships may be loaded multiple times... make sure fields in array belong to current gamedata!
		$tmpFields = array();
		foreach(SparkFieldHandler::$sparkFields as $field){
			$shooter = $field->getUnit();
			//if($field->isDestroyed($gamedata->turn-1)) continue; //destroyed weapons can be safely left out
			if($field->isDestroyed($gamedata->turn)) continue; //actually at this stage - CURRENT turn should be indicated!
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($shooter);
			if ($belongs){
				$tmpFields[] = $field;
			}			
		}
		SparkFieldHandler::$sparkFields = $tmpFields;
		
		
		//make sure boost level for all weapons is calculated
		foreach(SparkFieldHandler::$sparkFields as $field){
			$field->calculateBoostLevel($gamedata->turn);
		}
		
		//sort all fields by boost
		//usort(SparkFieldHandler::$sparkFields, "self::sortByBoost");
		usort(self::$sparkFields, [self::class, 'sortByBoost']);	
	
		//table of units that are already targeted
		$alreadyTargeted = array();
		//create firing order for each weapon (target self)
		//for each weapon find possible targets and add them to weapons' target list
		//strongest weapons fire first, and only 1 field affects particular ship	
		foreach(SparkFieldHandler::$sparkFields as $field){			
			if ($field->isDestroyed($gamedata->turn-1)) continue; //destroyed field does not attack
			if ($field->isOfflineOnTurn($gamedata->turn)) continue; //disabled field does not attack
			$shooter = $field->getUnit();
			$deployTurn = $shooter->getTurnDeployed($gamedata);		
			if($deployTurn > $gamedata->turn) continue;  //Ship not deployed yet, don't fire weapon!

			$targetPos = $shooter->getCoPos();
			$movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn+1);
			$fire = new FireOrder(-1, 'normal', $shooter->id, -1, $field->id, -1, $gamedata->turn, 
				1, 0, 0, 1, 0, 0, $movementThisTurn->position->q,  $movementThisTurn->position->r, $field->weaponClass
			);
			$fire->addToDB = true;
			$field->fireOrders[] = $fire;			
			$aoe = $field->getAoE($gamedata->turn);			
			$inAoE = $gamedata->getShipsInDistance($shooter, $aoe);
			foreach($inAoE as $targetID=>$target){		
				if ($shooter->id == $target->id) continue;//does not threaten self!
				if ($target->isDestroyed()) continue; //no point allocating	
				if ($target->isTerrain()) continue;				
				if ($target->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore targets that are not deployed yet!							
				if (in_array($target->id,$alreadyTargeted,true)) continue;//each target only once 
				//add to target list
				$alreadyTargeted[] = $target->id; //add to list of already targeted units
				$field->addTarget($target);
			}
		} //endof foreach SparkField
	}//endof function createFiringOrders
	
}//endof class SparkFieldHandler



class SparkField extends Weapon implements DefensiveSystem{
    /*Spark Field - Ipsha weapon
    	with custom enhancement (Spark Curtain) - anti-ballistic EWeb :)
    */
        public $name = "SparkField";
        public $displayName = "Spark Field";
		public $iconPath = "SparkField.png";
	
	//let's make animation more or less invisible, and effect very large
//		public $trailColor = array(0, 0, 0);
        public $animation = "ball";
		public $animationColor = array(165, 165, 255);
        public $animationExplosionScale = 2;
//      public $animationExplosionType = "AoE";
		public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.          
        //public $explosionColor = array(165, 165, 255);
        //public $projectilespeed = 20;
        //public $animationWidth = 1;
        //public $trailLength = 1;
	
	public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 4;
	
	public $output = 0;//affected by Spark Curtain
	public $baseOutput = 2;//base output WITH Spark Curtain
	public $defensiveType = "SparkCurtain"; //needs to be set to recognize as defensive system
      
        public $priority = 2; //should attack very early
	
        public $loadingtime = 1;
	public $autoFireOnly = true; //this weapon cannot be fired by player
	public $doNotIntercept = true; //this weapon is a field, "attacks" are just for technical reason
        
        public $rangePenalty = 0; //no range penalty, but range itself is limited
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals ; not relevant really!
	
	public $boostlevel = 0;
		
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
    	public $firingModes = array( 1 => "Spark Field"); //just a convenient name for firing mode
	public $hextarget = true;
	public $tohitPenalty = 0;
	public $damagePenalty = 0;
	
	protected $targetList = array(); //weapon will hit units on this list rather than target from firing order; filled by SparkFieldHandler!
	
	
 	protected $possibleCriticals = array( //no point in range reduced crit; but reduced damage is really nasty for this weapon!
            14=>"ReducedDamage"
	);
	
	
	
	public function addTarget($newTarget){
		$this->targetList[] = $newTarget;
	}

	
	    public function setSystemDataWindow($turn){
		    $boostlevel = $this->getBoostLevel($turn);
		    $this->minDamage = 2-$boostlevel;
		    $this->maxDamage = 7-$boostlevel;
		    $this->minDamage = max(0,$this->minDamage);
		    $this->animationExplosionScale = $this->getAoE($turn);
		    $this->range = $this->getAoE($turn);
		      parent::setSystemDataWindow($turn);  
		      //$this->data["AoE"] = $this->getAoE($turn);
		      $this->data["Special"] = "This weapons automatically affects all units (friend or foe) in area of effect.";  
		      $this->data["Special"] .= "<br>It should not be fired manually."; 
		      $this->data["Special"] .= "<br>Ignores armor, but cannot damage ship structure.";  
		      $this->data["Special"] .= "<br>Base damage is 1d6+1, range 2 hexes.";  
		      $this->data["Special"] .= "<br>Can be boosted, for +2 AoE and -1 damage per level."; 
		      $this->data["Special"] .= "<br>Multiple overlapping Spark Fields will only cause 1 (strongest) attack on a particular target."; 
		      $this->data["Special"] .= "<br>With CUSTOM Spark Curtain enhancement acts as anti-Ballistic shield (reducing hit chance only, by 2+boost)."; 
	    }	//endof function setSystemDataWindow
	
	
	
	public function getAoE($turn){
		$boostlevel = $this->getBoostLevel($turn);
		$aoe = 2+(2*$boostlevel);
		return $aoe;
	}
	
	
	public function calculateHitBase($gamedata, $fireOrder){
		//parent::calculateHitBase($gamedata, $fireOrder);
	        $fireOrder->updated = true;
		$fireOrder->chosenLocation = 0;//so it's recalculated later every time! - as location chosen here is completely incorrect for target 
		$fireOrder->needed = 100; //this weapon simply causes damage, hit is automatic
		$range = $this->getAoE($gamedata->turn);
		$fireOrder->pubnotes = "<br>Spark Field damages all units within " . $range . " hexes.";
	}
	
	public function fire($gamedata, $fireOrder){
		//parent::fire($gamedata, $fireOrder);
		//actually fire at units from target list - and fill fire order data appropriately
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$fireOrder->rolled = 1; //just to mark that there was a roll!
		$fireOrder->shotshit = 1; //always hit, technically
		
		//actual damage dealing...
		foreach($this->targetList as $target){
			$this->beforeDamage($target, $shooter, $fireOrder, null, $gamedata);			
		}
        	$notes = "This weapon simply causes damage, hit is automatic"; //replace usual note
		$fireOrder->notes = $notes;
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	}
	
	public function calculateBoostLevel($turn){
		$this->boostlevel = $this->getBoostLevel($turn);
	}
	
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }
            return $boostLevel;
        }	
	
	
	//find units in range (other than self), create attacks vs them
	public function beforeFiringOrderResolution($gamedata){
		$ship = $this->getUnit();
		$deployTurn = $ship->getTurnDeployed($gamedata);		
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!


		SparkFieldHandler::createFiringOrders($gamedata);		
	}
	
	
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		if (!($target instanceof FighterFlight)){ //ship - as usual
			$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
		}else{//fighter flight - separate hit on each fighter!
			foreach ($target->systems as $fighter){
				if ($fighter == null || $fighter->isDestroyed()){
				    continue;
				}
				$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
				$this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);
                    	}
		}
	}	


	public function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$dmgToReturn = $damage;
		if ($system instanceof Structure) $dmgToReturn = 0; //will not harm Structure!
		return $dmgToReturn;
	}
	

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 2;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		SparkFieldHandler::addSparkField($this);//so all Spark Fields are accessible together, and firing orders can be uniformly created
	}
	
	// ignore armor; advanced armor halves effect (due to weapon being Electromagnetic)
	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
		if (WeaponEM::isTargetEMResistant($target,$system)){
			$returnArmour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
			$returnArmour = floor($returnArmour/2);
			return $returnArmour;
		}else{
			return 0;
		}
	}
	

	public function onConstructed($ship, $turn, $phase){
		parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = $this->getOutput();
		$this->damagePenalty = 0;
	}
	public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
		if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn)) return 0;
	if(!$weapon->ballistic) return 0;//only ballistic weapons are affected!
	$output = $this->getOutput();
		return $output;
	}
	public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
		return 0; //does not reduce damage
	}
	public function getDefensiveType()
	{
		return "SparkCurtain";
	}    
	public function getOutput(){
		$output = 0;
		if($this->output == 0) return 0; //if base output is not enhanced this means there is no effect
		foreach ($this->power as $power){
		    if ($power->turn == TacGamedata::$currentTurn && $power->type == 2){
				$output += $power->amount;
		    }        
		}        
		$output = $output + $this->baseOutput; //strength = 2+boostlevel
		return $output;        
	}    
	
	
	public function getDamage($fireOrder){        
		$damageRolled = Dice::d(6, 1)+1;
		$boostlevel = $this->getBoostLevel($fireOrder->turn);
		$damageRolled -= $boostlevel; //-1 per level of boost
		$damageRolled = max(0,$damageRolled); //cannot do less than 0	
		return $damageRolled;   
	}
        public function setMinDamage(){    
		$this->minDamage = 2 ;	      		
	}
        public function setMaxDamage(){   
		$this->maxDamage = 7 ;	    
	}	

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->noProjectile = $this->noProjectile;															
		return $strippedSystem;
	} 
	
} //endof class SparkField 







class SurgeCannon extends Raking{
    /*Surge Cannon - Ipsha weapon*/
	public $name = "SurgeCannon";
	public $displayName = "Surge Cannon";
	public $iconPath = "SurgeCannon.png";
	
	public $animation = "laser";
	public $animationColor = array(165, 165, 255);
	/*
	public $animationWidth = 2;
	public $animationWidthArray = array(1=>2, 2=>3, 3=>4, 4=>5, 5=>6);
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.1, 2=>0.2, 3=>0.3, 4=>0.5, 5=>0.6);
*/
      
        public $loadingtime = 1;
	public $intercept = 2; //intercept rating -2
        
	
	
        public $priority = 8;
        public $priorityArray = array(1=>9, 2=>8, 3=>8, 4=>7, 5=>7); //weakest mode should go late, more powerful modes early (for Raking weapon)
	public $firingMode = 1;	
            public $firingModes = array(
                1 => "Single",
                2 => "2combined",
                3 => "3combined",
                4 => "4combined",
                5 => "5combined"
            );
        public $rangePenalty = 2; //-2 hex in single mode
            public $rangePenaltyArray = array( 1=>2, 2=>1, 3=>0.5, 4=>0.33, 5=>0.25 ); //Raking and Piercing mode
        public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals 
            public $fireControlArray = array( 1=>array(2, 2, 2), 2=>array(1,3,3), 3=>array(0,4,4), 4=>array(-2,4,4), 5=>array(-4,4,4) ); //Raking and Piercing mode
	
	
	
	    public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	public $isCombined = false; //is being combined with other weapon
	public $alreadyConsidered = false; //already considered - either being fired or combined
	
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Can combine multiple Surge Cannons into a single shot with increased range and damage (and cooldown):";  
		      $this->data["Special"] .= "<br> - 2 SC: 5-23 dmg, -5/hex"; 
		      $this->data["Special"] .= "<br> - 3 SC: 9-36 dmg, -2.5/hex"; 
		      $this->data["Special"] .= "<br> - 4 SC: 14-50 dmg, -1.65/hex"; 
		      $this->data["Special"] .= "<br> - 5 SC: 20-65 dmg, -1.25/hex"; 
		      $this->data["Special"] .= "<br>If You allocate multiple Surge Cannons in higher mode of fire at the same target, they will be combined."; 
		      $this->data["Special"] .= "<br>If not enough weapons are allocated to be combined, weapons will be fired in single mode instead.";  
		      $this->data["Special"] .= "<br>Cooldown period: 1 less than number of weapons combining.";  
		      $this->data["Special"] .= "<br>+2 per rake to critical/dropout rolls on system(s) hit this turn.";  //original rule is more fancy
	    }	
	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ 
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		//each rake causes +2 mod on critical roll for hit system! 
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=2; 
	} //endof function onDamagedSystem
	
	
	public function fire($gamedata, $fireOrder){
		// If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
	    if ($this->isCombined) $fireOrder->shots = 0; //no actual shots from weapon that's firing as part of combined shot!
		parent::fire($gamedata, $fireOrder);
		/*replaced by single crit ForTurns!
	    for($i = 1; $i<$this->firingMode;$i++){
			$trgtTurn = $gamedata->turn+$i-1;
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
	    }
		*/
		if ($this->firingMode > 1){
			$turnEndEffect = $gamedata->turn + $this->firingMode - 1;//2combined for 1 turn, 3combined for 2 turns...
			$crit = new ForcedOfflineForTurns(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $turnEndEffect);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] = $crit;
		}
	} //endof function fire
	
	
	
	//if fired in higher mode - combine with other weapons that are so fired!
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->changeFiringMode($fireOrder->firingMode);
			return;
		}
		if ($fireOrder->firingMode > 1){ //for single fire there's nothing special
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Surge Cannon?...
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if ($subWeapon instanceof SurgeCannon){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($fireOrder->firingMode-1)) break;//enough subordinate weapons found! - exit loop
			}						
			if ($subordinateOrdersNo == ($fireOrder->firingMode-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}
			}else{//not enough weapons to combine in this mode - set self to single fire
				$fireOrder->firingMode = 1;
			}
		}
		parent::calculateHitBase($gamedata, $fireOrder);
	}//endof function calculateHitBase
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $aftFacing = false)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
			//switch to Aft-facing icons for weapons that are facing Aft-oriented!
			if($aftFacing){
				$this->iconPath = "SurgeCannonAft.png";
			}
			
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
	
        public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+1; //1 SC
				break;
			case 2:
				return Dice::d(10, 2)+3; //2 SC
				break;
			case 3:
				return Dice::d(10, 3)+6; //3 SC
				break;
			case 4:
				return Dice::d(10, 4)+10; //4 SC
				break;
			case 5:
				return Dice::d(10, 5)+15; //5 SC
				break;
		}
	}
        public function setMinDamage(){    
		switch($this->firingMode){
			case 1:
				$this->minDamage = 2;
				break;
			case 2:
				$this->minDamage = 5;
				break;	
			case 3:
				$this->minDamage = 9;
				break;	
			case 4:
				$this->minDamage = 14;
				break;	
			case 5:
				$this->minDamage = 20;
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 11;
				break;
			case 2:
				$this->maxDamage = 23;
				break;	
			case 3:
				$this->maxDamage = 36;
				break;	
			case 4:
				$this->maxDamage = 50;
				break;	
			case 5:
				$this->maxDamage = 65;
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;  
	}
} //endof class SurgeCannon




class SurgeLaser extends Raking{
    /*Surge Laser - Streib weapon*/
	public $name = "SurgeLaser";
	public $displayName = "Surge Laser";
	public $iconPath = "SurgeCannon.png";
	
	public $animation = "laser";
	public $animationColor = array(165, 165, 255);
	/*
	public $animationWidth = 2;
	public $animationWidthArray = array(1=>2, 2=>3);
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.1, 2=>0.2);
      */
	
	public $loadingtime = 1;
	public $intercept = 1; //intercept rating -1
        public $uninterceptable = true;
        
	public $priority = 3; //technically it's Raking weapon, but so light it's essentially light Standard
	public $priorityArray = array(1=>3, 2=>8); //...but Combined shot is much nastier, light Raking all right
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "Rapid",
		2 => "Combined",
	);
	public $rangePenalty = 1; //-1 hex in single mode
	public $rangePenaltyArray = array( 1=>1, 2=>1); //-1/hex in both modes
	public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
	public $fireControlArray = array( 1=>array(4, 2, 2), 2=>array(2,2,4) ); 
	public $guns = 2;
	public $gunsArray = array(1=>2,2=>1);//basic 2 shots, combined 1 shot
		
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Laser"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Uninterceptable.<br>+2 per rake to critical/dropout rolls on system(s) hit this turn.";  //original rule is more fancy
		$this->data["Special"] .= "Basic firing mode is 2 shots with FC 20/10/10 (d10+2 dmg), combined 1 shot with FC 10/10/20 (2d10+3 dmg).";  //original rule is more fancy
	}	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ 
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		//each rake causes +2 mod on critical roll for hit system! 
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=2; 
	} //endof function onDamagedSystem
	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 6;
		}
		if ( $powerReq == 0 ){
			$powerReq = 3;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	
	
	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+2; //rapid fire
				break;
			case 2:
				return Dice::d(10, 2)+3; //combined fire
				break;
		}
	}
	public function setMinDamage(){    
		switch($this->firingMode){
			case 1:
				$this->minDamage = 2;
				break;
			case 2:
				$this->minDamage = 5;
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 12;
				break;
			case 2:
				$this->maxDamage = 23;
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;  
	}
} //endof class SurgeLaser




class LtSurgeBlaster extends LinkedWeapon{
   /*Ipsha fighter weapon*/
	public $trailColor = array(50, 50, 200);
	public $name = "LtSurgeBlaster";
	public $displayName = "Light Surge Blaster";
	public  $iconPath = "lightParticleBeam.png";
	
	public $animation = "bolt";
	public $animationColor =  array(145, 145, 245);
	/*
	public $animationExplosionScale = 0.10;
	public $projectilespeed = 10;
	public $animationWidth = 2;
	public $trailLength = 10;
	*/
	
	public $intercept = 2;
	public $loadingtime = 1;
	public $shots = 2;
	public $defaultShots = 2;
	public $rangePenalty = 2;
	public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	public $priority = 4; //average output ftr weapon

	public $damageType = "Standard"; 
	public $weaponClass = "Electromagnetic"; 
	
	
	function __construct($startArc, $endArc, $nrOfShots = 2){
		$this->defaultShots = $nrOfShots;
		$this->shots = $nrOfShots;
		$this->intercept = $nrOfShots;
		if($nrOfShots === 1){
			$this->iconPath = "lightParticleBeam1.png";
		}
		if($nrOfShots >2){//no special icon for more than 3 linked weapons
			$this->iconPath = "lightParticleBeam3.png";
		}
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "+1 to Crit/Dropout rolls per hit.";
	}
   
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ 
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		//each hit causes +1 mod on critical roll for hit system! 
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=1; 
	} //endof function onDamagedSystem
	
	public function getDamage($fireOrder){        return Dice::d(6,2)-1;   }
	public function setMinDamage(){     $this->minDamage = 1 ;      }
	public function setMaxDamage(){     $this->maxDamage = 11 ;      }
} //endof class LtSurgeBlaster



class EmPulsar extends Pulse{
	/*Ipsha weapon*/
	public $name = "EmPulsar";
	public $displayName = "EM Pulsar";
	public $iconPath = "EmPulsar.png";
	
	public $animationColor = array(100, 100, 255);
	public $animation = "bolt";
	/*
	public $animationWidth = 3;
	public $projectilespeed = 10;
	public $animationExplosionScale = 0.15;
	public $trailLength = 10;
*/
	
	public $loadingtime = 1;
	public $priority = 3;

	public $intercept = 2;
	public $rangePenalty = 1; //-1/hex
	public $grouping = 25; //+1 pulse hit per 1 below target number on d20
	public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 

	public $weaponClass = "Electromagnetic";
	 
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 6;
		}
		if ( $powerReq == 0 ){
			$powerReq = 3;
		}		
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	 
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] .= "<br>+1 per hit to crit rolls, +2 on dropout rolls.";
		$this->data["Special"] .= "<br>Cooldown period: 1 turn.";  
	}
	 
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);		
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		
		//+1 to crit roll, +2 to dropout roll
		$mod = 1;
		if ($ship instanceof FighterFlight) $mod++;		
		$system->critRollMod += $mod; 
	} //endof function onDamagedSystem
	
	public function fire($gamedata, $fireOrder){
		// If fired, this weapon needs 1 turn cooldown period (=forced shutdown)
		parent::fire($gamedata, $fireOrder);		
		$trgtTurn = $gamedata->turn;
		$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
		$crit->updated = true;
		$crit->newCrit = true; //force save even if crit is not for current turn
		$this->criticals[] =  $crit;	
	} //endof function fire
	 
	/* applying cooldown when firing defensively, too
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
			$trgtTurn = $gamedata->turn;
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;		
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
        
        public function getDamage($fireOrder){        return 9;   }
    }//endof class EmPulsar



class ResonanceGenerator extends Weapon{
    /*Resonance Generator - Ipsha weapon*/
	public $name = "ResonanceGenerator";
	public $displayName = "Resonance Generator";
	public $iconPath = "ResonanceGenerator.png";
	
	public $animation = "laser"; //described as beam in nature, standard damage is resonance effect and not direct
	public $animationColor = array(125, 125, 230);
	public $animationExplosionScale = 0.6; //make it look really large - while singular damage is low, it's repeated on every structure block - eg. all-encompassing
	/*
	public $animationWidth = 10;
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.25);
      */
	public $loadingtime = 1;
	
	public $rangePenalty = 1; //-1/hex
	public $fireControl = array(null, 2, 2); // fighters, <mediums, <capitals 
	
	public $intercept = 0;
	public $priority = 1;// as it attacks every section, should go first!
	
	public $noPrimaryHits = true; //outer section hit will NOT be able to roll PRIMARY result!
	
	private $cooldown = 2;
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $aftFacing=false)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 6;
		}
		//switch to Aft-facing icons for weapons that are facing Aft-oriented!
		if($aftFacing){
			$this->iconPath = "ResonanceGeneratorAft.png";
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Cooldown period: 2 turns.";  
		$this->data["Special"] .= "<br>Attacks all sections (so a capital ship will sufer 5 attacks, while MCV only 1).";  //MCV should suffer 2, but for technical reasons I opted for going for Section = Structure block		    
		$this->data["Special"] .= "<br>Ignores armor."; 
	}
	
	public function fire($gamedata, $fireOrder){
		// If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
		parent::fire($gamedata, $fireOrder);
		/*replaced by straight 2 turns cooldown
		for($i = 1; $i<=$this->cooldown;$i++){		
			$trgtTurn = $gamedata->turn+$i-1;//start on current turn rather than next!
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}
		*/		
		$turnEndEffect = $gamedata->turn+$this->cooldown;//2 turns
		$crit = new ForcedOfflineForTurns (-1, $fireOrder->shooterid, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $turnEndEffect);
		$crit->updated = true;
		$this->criticals[] = $crit;
	} //endof function fire	
	
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
	
	public function isTargetAmbiguous($gamedata, $fireOrder){//targat always ambiguous - just so enveloping weapon is not used to decide target section!
		return true;
	}
	
	/*attacks every not destroyed (as of NOW!) ship section*/
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		//fighters are untargetable, so we know it's a ship
		if ($target->isDestroyed()) return; //no point allocating
		$activeStructures = $target->getSystemsByName("Structure",false);//list of non-destroyed Structure blocks
		foreach($activeStructures as $struct){
			$fireOrder->chosenLocation = $struct->location;			
			$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage, true);//force PRIMARY location!
		}
	}//endof function beforeDamage
		
	public function getDamage($fireOrder){       return Dice::d(10,1);   }
	public function setMinDamage(){     $this->minDamage = 1 ;      }
	public function setMaxDamage(){     $this->maxDamage = 10 ;      }
} //endof class ResonanceGenerator



class SurgeBlaster extends Weapon{
    /*Surge Blaster - Ipsha weapon*/
	public $name = "SurgeBlaster";
	public $displayName = "Surge Blaster";
	public $iconPath = "SurgeBlaster.png";
	
	public $animation = "bolt";
	public $animationColor =  array(165, 165, 255);
	/*
	public $projectilespeed = 14;
	public $animationWidth = 4;
	public $animationExplosionScale = 0.4;
	*/
	public $priority = 6;
  
	public $loadingtime = 1;
	public $intercept = 1;
        
	public $rangePenalty = 0.5; //-1/2 hexes
	public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	private $cooldown = 1;
		
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	  		
		$this->data["Special"] .= "Cooldown period: " . $this->cooldown . " turns.";  
		$this->data["Special"] .= "<br>+4 to all critical/dropout rolls made by system hit this turn.";  
	}	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (WeaponEM::isTargetEMResistant($ship,$system)) return; //no effect on Advanced Armor
		$system->critRollMod+=4; //+4 to all critical/dropout rolls on system hit this turn
	} //endof function onDamagedSystem
	
	
	public function fire($gamedata, $fireOrder){
		// If fired, this weapon needs 2 turns cooldown period (=forced shutdown)
		parent::fire($gamedata, $fireOrder);
		/*replaced by straight 2 turn cooldown
		for($i = 1; $i<=$this->cooldown;$i++){		
			$trgtTurn = $gamedata->turn+$i-1;//start on current turn rather than next!
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}
		*/		
		$turnEndEffect = $gamedata->turn+$this->cooldown;//2 turns
		$crit = new ForcedOfflineForTurns (-1, $fireOrder->shooterid, $this->id, "ForcedOfflineForTurns", $gamedata->turn, $turnEndEffect);
		$crit->updated = true;
		$this->criticals[] = $crit;
	} //endof function fire
	
	
	/* applying cooldown when firing defensively, too
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		if ($this->firedDefensivelyAlready==0){ //in case of multiple interceptions during one turn - suffer backlash only once
			$trgtTurn = $gamedata->turn;
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;		
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
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
	
	public function getDamage($fireOrder){        return Dice::d(10,4);   }
	public function setMinDamage(){     $this->minDamage = 4 ;      }
	public function setMaxDamage(){     $this->maxDamage = 40 ;      }
} //endof class SurgeBlaster


class RammingAttack extends Weapon{
    /*option to ram target will be implemented as an actual weapon*/
	public $name = "RammingAttack";
	public $displayName = "Ramming Attack";
	public $iconPath = "RammingAttack.png";
	public $isTargetable = false; //cannot be targeted ever!
	protected $doCountForCombatValue = false; //don't count when estimating remaining combat value - this is an ability rather than actual system
	
	//animation irrelevant really (range 0), but needs to be fast!
	public $animation = "trail";
	public $animationColor =  array(1, 1, 1);
	public $animationExplosionScale = 0.1; //very small bolt; explosion itself is scaled by damage done anyway!
	public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one. 
	/*
	public $projectilespeed = 24;
	public $animationWidth = 1;
	public $animationExplosionScale = 0.4;
	*/
	public $priority = 1;
	
	public $doNotIntercept = true; //unit hurls itself at the enemy - this cannot be intercepted!
      
	public $loadingtime = 1;
	public $intercept = 0;
        
	public $rangePenalty = 0; //no range penalty... HKs will add it though!
	public $range = 0.1; //attacks units on same hex only; range = 0 is treated as unlimited
	
	public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
	public $raking = 10; //size of rake
	
	public $firingModes = array(
		1 => "Ramming"
	);	
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Ramming"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	public $isRammingAttack = true;	
	public $designedToRam = false;
	private $selfDestroy = 0; //will successful attack destroy the ramming ship? Additional damage modifier
	private $designDamage = 0;
	private $damageModRolled = 0;

	private $gamedata = null; //gamedata is needed in places normally unavailable - this variable will be filled before any calculations happen!
	
	 protected $possibleCriticals = array(); //shouldn't be hit ever, but if it is, should not suffer any criticals
	
	//preventing double ramming
	private $alreadyRammed = array();
	public function checkAlreadyRammed($targetID){
		foreach($this->alreadyRammed as $rammedID) if ($rammedID == $targetID) return true;
		return false;
	}
	public function setAlreadyRammed($targetID){
		$this->alreadyRammed[] = $targetID;
	}
	
	public function setSystemDataWindow($turn){
		$this->setMinDamage(); //just in case it's not set correctly in the beginning!
			$this->setMaxDamage();
		  parent::setSystemDataWindow($turn);  
		  $this->data["Special"] = "Ramming attack - if succcessful, ramming unit itself will take damage too (determined by targets' ramming factor).";  
		  if($this->designedToRam) {
			  $this->data["Special"] .= "<br>This unit is specifically designed for ramming and may do so in any scenario.";
		  }else{
			  $this->data["Special"] .= "<br>ONLY ALLOWED WHEN DESPERATE RULES APPLY, OR WITH OTHER PLAYER'S CONSENT";
		  }
		  $this->data["Special"] .= "<br>Profiles and EW do not matter for hit chance - but unit size and target speed does.";  
		  $this->data["Special"] .= "<br>	(it's generally easier to ram slow targets and targets larger than ramming units itself)";  
		  $this->data["Special"] .= "<br>	Hunter-Killers have speed penalty as well.";  
		  $this->data["Special"] .= "<br>Ramming damage is also influenced by conditions - moving head on with initiative slightly increases chance of high damage.";
		  $this->data["Special"] .= "<br>Ramming attacks will be done in ship firing phase (even attacks by fighters) and cannot be intercepted.";
		  $this->data["Special"] .= "<br>Ships (not fighters) on the same hex as Enormous unit will automatically declare ramming attack against it!";
		  $this->data["Special"] .= "<br>Immobile objects do have ramming attack for technical purposes, but won't use it offensively.";
	}	
	

	//Here we will check for collisions with Terrain in PRE-FIRING phase
	public function beforePreFiringOrderResolution($gamedata){
		$shooter = $this->getUnit();
		$deployTurn = $shooter->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't ram anything!			
	
		//First let's check if any units moved through this Terrain unit and create appropriate fireOrders.		
		if($shooter->isTerrain() && !$shooter->isDestroyed()){ //This userid denotes shooter unit is terrain e.g. Asteroids.
			$relevantShips = array();

			//Make a list of relevant ships e.g. this ship and enemy fighters in the game.
			foreach($gamedata->ships as $ship){
				if($ship->isDestroyed()) continue; //Ignore destroyed ships
				if($ship->isTerrain()) continue;	//Don't add other terrain.
				if($ship->getTurnDeployed($gamedata) > $gamedata->turn)	continue; //Ship not deployed yet.		
				//if ($ship instanceof FighterFlight && $shooter->Huge == 0) continue; //Not doing fighters except for very large terrain, change if and when skindancing introduced.	
				$relevantShips[] = $ship;			
			}

			$terrrainPosition = $shooter->getHexPos();
			$collisiontargets = $this->checkForCollisions($relevantShips,  $gamedata, $terrrainPosition, $shooter);

			foreach($collisiontargets as $targetid=>$location){
				$target = $gamedata->getShipById($targetid);
				$type = "TerrainCollision"; //Moving through asteroids hex, d10 * speed damage.
				if($shooter->Huge > 0 ) $type = "TerrainCrash"; //Larger Terrain, like Moons.  Full ramming damage.
				$targetMovement = $target->getLastTurnMovement($gamedata->turn+1);

				if ($target instanceof FighterFlight && $type === "TerrainCrash") {
					$first = true; // Flag to track the first entry
				
					foreach ($target->systems as $fighter) {                          
						$newFireOrder = new FireOrder(
							-1, "prefiring", $shooter->id, $target->id,
							$this->id, $fighter->id, $gamedata->turn, 1,
							0, 0, 1, 0, 0,
							$targetMovement->position->q, $targetMovement->position->r, $type, -1
						);
						$newFireOrder->chosenLocation = $location;                
				
						if ($first) {
							$newFireOrder->pubnotes = "<br>COLLISION! A fighter unit collided with terrain during its movement!";
							$first = false; // Set flag to false after first iteration
						}
				
						$newFireOrder->addToDB = true;
						$this->fireOrders[] = $newFireOrder;
					}       
				}else{						
					$newFireOrder = new FireOrder(
						-1, "prefiring", $shooter->id, $target->id,
						$this->id, -1, $gamedata->turn, 1,
						0, 0, 1, 0, 0,
						$targetMovement->position->q, $targetMovement->position->r, $type, -1
					);
					$newFireOrder->chosenLocation = $location;				
					$newFireOrder->pubnotes = "<br>COLLISION! Ship collided with terrain during its movement!";
					$newFireOrder->addToDB = true;
					$this->fireOrders[] = $newFireOrder;			
				}	
			}	
		}

		if($this->autoFireOnly) return;//ramming attack on some units (eg. immobile ones) is for technical purposes only!	
		$this->gamedata = $gamedata;//fill gamedata variable, which might otherwise be left out!
		
		//if($shooter instanceof FighterFlight) return; //skindancing added, so this line is removed.
		//Change condition above to create opportunity for skindancing roll instead of just skipping.

		if($shooter->isDestroyed()) return; //destroyed unit does not ram
		$targetList = $gamedata->getShipsInDistance($shooter, 0); //distance 0!



		$alreadyFiringAt = $this->getFireOrders($gamedata->turn);
		foreach($targetList as $targetID=>$target){
			if(!$target->Enormous) continue; //only auto-ram Enormous units
			if($target instanceof Terrain) continue; //Terrain Enormous units are handled as collisions now.		
			if($targetID == $shooter->id) continue; //do not ram self			
			if($target->isDestroyed()) continue; //destroyed unit does not ram... and neither is rammed			
			if($deployTurn > $gamedata->turn) continue;  //Ship not deployed yet, don't ram it!			
			if (isset($shooter->skinDancing[$targetID])) continue; //Already skin-dancing, or additional fighter after the first has succeeded/rammed.		

			$skinDancing = $this->getSkinDancingResult($shooter, $target, $gamedata); //Targeting FIXED call internal method
				
			if($skinDancing == 'Success'){
				$shooter->skinDancing[$targetID] = true;			
				continue; //Do not proceed with auto-ram when a successful skindance has occurred. Note target id in ship variable for notes later.
			}else if($skinDancing == 'Aborted') {
				$shooter->skinDancing[$targetID] = 'Aborted';				
				continue; //Not skin-dancing, but not ramming either.
			} else if ($skinDancing == 'Failed'){
				$shooter->skinDancing[$targetID] = 'Failed'; //Add false here, so we only hit one fighter.			
				$calledid = -1;
				if($shooter instanceof FighterFlight){
					//Find last undestroyed fighter
					foreach($shooter->systems as $fighter){
						if(!$fighter->isDestroyed()){
							$calledid = $fighter->id;
						}
					}			
				}
		
				//don’t repeat manual ramming order
				$alreadyDeclared = false;
				foreach ($alreadyFiringAt as $existingFiringOrder){
					if($existingFiringOrder->targetid == $targetID) $alreadyDeclared = true;
				}
				if($alreadyDeclared) continue;
				//unit on the same hex is Enormous, not self, not destroyed, has deployed and not being rammed by this unit already, either can't skin-dance or failed to – auto-ram it!
				$movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn+1);
				$fire = new FireOrder(-1, 'prefiring', $shooter->id, $targetID, $this->id, $calledid, $gamedata->turn,
					1, 0, 0, 1, 0, 0, $movementThisTurn->position->q,  $movementThisTurn->position->r, 'TerrainCrash'
				);
				$fire->addToDB = true;		
				$this->fireOrders[] = $fire;
			}else{ //'Invalid' returns effectively.  Caused all fighters to crash etc.
				//don’t repeat manual ramming order
				$alreadyDeclared = false;
				foreach ($alreadyFiringAt as $existingFiringOrder){
					if($existingFiringOrder->targetid == $targetID) $alreadyDeclared = true;
				}
				if($alreadyDeclared) continue;
				//unit on the same hex is Enormous, not self, not destroyed, has deployed and not being rammed by this unit already, either can't skin-dance or failed to – auto-ram it!
				$movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn+1);
				$fire = new FireOrder(-1, 'prefiring', $shooter->id, $targetID, $this->id, -1, $gamedata->turn,
					1, 0, 0, 1, 0, 0, $movementThisTurn->position->q,  $movementThisTurn->position->r, 'TerrainCrash'
				);
				$fire->addToDB = true;		
				$this->fireOrders[] = $fire;				
			}	
			
		}
	}	

    public function getSkinDancingResult($shooter, $target, $gamedata) {
		if($this->designedToRam) return 'Invalid'; //Do full automatic ramming for these units e.g. HKs		
		if(!empty($shooter->skinDancing)) return 'Success'; //Already skindancing.
		//Debug::log("Ship name " . $shooter->name);			
		//Debug::log("before size check " . $shooter->shipSizeClass);	
        //Ship type checks        
        if($shooter->shipSizeClass > 1 && !$shooter->isSkinDancer()) return 'Invalid'; //LCV or smaller (or special rules like Torvalus)
		//Debug::log("before agile check " . $shooter->agile);			
        if(!$shooter->agile) return 'Invalid'; //Must be agile
	
        $shipSpeed = $shooter->getSpeed();
		//Debug::log("before shooter speed check. Shooter Speed: " . $shipSpeed);			
        if($shipSpeed == 0) return 'Invalid'; //Speed 0 ships can't skindance.
		//Debug::log("before target speed check " . $shooter->id);	
        //Speed checks
        $targetSpeed = $target->getSpeed(); 
		//Debug::log("before target speed check. Target Speed: " . $targetSpeed);			       
        if($targetSpeed > 5) return 'Invalid';
        if($targetSpeed > 0){
			$shooterLastMove = $shooter->getLastMovement();
			$shooterHeading = $shooterLastMove->heading;
			//Debug::log("shooterHeading " . $shooterHeading);			
			$targetLastMove = $target->getLastMovement();
			$targetHeading = $targetLastMove->heading;	
			//Debug::log("targetHeading " . $targetHeading);			
			$oppositeHeading = ($targetHeading + 3) % 6;
			//Debug::log("oppositeHeading " . $oppositeHeading);			
			//Cannot skin-dance if target moving and ship not moving in same or opposite direction.	
			if($shooterHeading !== $targetHeading && $shooterHeading !== $oppositeHeading) return 'Invalid';	
        }        

		$roll = Dice::d(20); // Roll the dice
		
		//Debug::log("roll " . $roll);			
		$mod = 0;
		if($shipSpeed > 5) $mod += ceil(($shipSpeed - 5)/2);
		//Debug::log("speed mod " . $mod);			
		if(Movement::isPivoting($shooter, $gamedata->turn) || Movement::isPivoting($target, $gamedata->turn)) $mod += 5; //Shooter or target is pivoting
		//Debug::log("pivoting mod " . $mod);			
		if(Movement::isRolling($shooter, $gamedata->turn) || Movement::isRolling($target, $gamedata->turn)) $mod += 5; //Shooter or target is rolling
		//Debug::log("rolling mod " . $mod);
		$thrusterMod = 0;
		foreach($shooter->systems as $system){
			if($system->name == 'thruster' && $system->isDestroyed()) $thrusterMod += $system->output; 	
		}
		$mod += $thrusterMod;
		//Debug::log("thruster mod " . $thrusterMod);		
		$jinking = Movement::getJinking($shooter, $gamedata->turn);
		if($jinking > 0) $mod += $jinking * 3;
		//Debug::log("jinking mod " . $jinking * 3);	
		if($shooter instanceof FighterFlight && $shooter->hasNavigator) $mod -= 1;
		//Debug::log("Navigator mod " . $mod);	
		$modifiedRoll = $roll + $mod;
		//Debug::log("modifiedRoll " . $modifiedRoll);	
		if($modifiedRoll <= 15){
			return 'Success';
		} else if($modifiedRoll > 15 && $modifiedRoll < 21){
			return 'Aborted';
		} else{
			return 'Failed'; 	
		}

    }     

	public function generateIndividualNotes($gamedata, $dbManager){	
		//Create notes for successful Skindancers in Pre-Firing Phase advance()		
		//Check removed to ensure notes are generated whenever data is present - relies on getSkinDancingResult population.
		$ship = $this->getUnit();
		
		if(!empty($ship->skinDancing)){
			//Ensuring only one note is generated per flight (as each fighter has its own RammingAttack system)
            if($ship instanceof FighterFlight){
                $firstRamming = $ship->getSystemByName("RammingAttack");
                if($firstRamming && $firstRamming->id !== $this->id) return;
            }

			$noteValue = null;
			foreach ($ship->skinDancing as $targetID => $value) {
				if ($value === true) {
					$notekey   = 'skindancing';					
					$noteValue = $targetID;
					break;
				}else if($value == 'Aborted'){
					$notekey   = 'abortedSkindance';					
					$noteValue = $targetID;
					break;
				}else if($value == 'Failed'){
					$notekey   = 'failedSkindance';					
					$noteValue = $targetID;
					break;
				}
			}

			if ($noteValue !== null) {
				//$notekey   = 'skindancing';
				$noteHuman = 'Ship is skindancing';

				$this->individualNotes[] = new IndividualNote(
					-1,
					$gamedata->id,
					$gamedata->turn,
					$gamedata->phase,
					$ship->id,
					$this->id,
					$notekey,
					$noteHuman,
					$noteValue
				);
			}
		}

	}	


	public function onIndividualNotesLoaded($gamedata){
    	foreach ($this->individualNotes as $currNote) { // Assume ASCENDING sorting - so enact all changes as is
			if($currNote->turn == $gamedata->turn){

				if($currNote->notekey === 'skindancing'){
					$ship = $this->getUnit();
					$ship->skinDancing[$currNote->notevalue] = true;
				}else if($currNote->notekey === 'abortedSkindance'){
					$ship = $this->getUnit();
					$ship->skinDancing[$currNote->notevalue] = 'Aborted';					
				}else if($currNote->notekey === 'failedSkindance'){
					$ship = $this->getUnit();
					$ship->skinDancing[$currNote->notevalue] = 'Failed';					
				}
			}	
		}		
		$this->individualNotes = array();//delete notes, after reaction on their load they serve no further purpose
	}


	private function checkForCollisions($relevantShips, $gamedata, $terrainPosition, $thisShip){
	    $collisiontargets = array(); // Initialize array for fighters to be fired at.	
		//$thisShip = $this->getUnit();
		
		if ($thisShip->Huge > 0 || (property_exists($thisShip, 'hexOffsets') && !empty($thisShip->hexOffsets))) {  //Terrain occupies more than just 1 hex!  Need to check all of its hexes.
			// Terrain logic: Build list of ALL occupied hexes first.
			$occupiedHexes = [];

			if (property_exists($thisShip, 'hexOffsets') && !empty($thisShip->hexOffsets)) {
				// 1. Irregular Shape defined by hexOffsets
				$move = $thisShip->getLastMovement();
				$facing = $move ? $move->facing : 0;
				
				foreach ($thisShip->hexOffsets as $offset) {
					// Use accurate pixel-based rotation to get absolute hex position
					$occupiedHexes[] = Mathlib::getRotatedHex($terrainPosition, $offset, $facing);
				}
			} else {
				// 2. Standard Circular Shape defined by Huge radius
				$occupiedHexes[] = $terrainPosition; // Center is always occupied for circular
				if ($thisShip->Huge > 0) {
					$neighbors = Mathlib::getNeighbouringHexes($terrainPosition, $thisShip->Huge);
					foreach ($neighbors as $n) {
						$occupiedHexes[] = new OffsetCoordinate($n['q'], $n['r']);
					}
				}
			}
	
			foreach ($relevantShips as $ship) {  
				$startMove = $ship->getLastTurnMovement($gamedata->turn);
				$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.
				$previousFacing = $startMove->getFacingAngle();
		
				foreach ($ship->movement as $shipMove) {
					if ($shipMove->turn == $gamedata->turn) {
						if ($shipMove->type == "move" || $shipMove->type == "slipleft" || $shipMove->type == "slipright") {
							
							// Check if shipMove position matches ANY occupied hex
							$match = false;
							foreach ($occupiedHexes as $hex) {
								if ($hex->q == $shipMove->position->q && $hex->r == $shipMove->position->r) {
									$match = true;
									break;
								}
							}
		
							if ($match) {
								if (!isset($collisiontargets[$ship->id])) {
									$relativeBearing = $this->getTempBearing($previousPosition, $terrainPosition, $ship, $previousFacing);
									$location = $this->getCollisionLocation($relativeBearing, $ship);
									$collisiontargets[$ship->id] = $location; // Add to array to be targeted.
								}
							}
						}
		
						$previousPosition = $shipMove->position;
						$previousFacing = $shipMove->getFacingAngle();
					}
				}
			}
		}else{
			foreach ($relevantShips as $ship) { // Look through relevant ships' movements and take appropriate action.					
				// Now check other movements in the turn.
				$startMove = $ship->getLastTurnMovement($gamedata->turn);	//initialise as last move in previous turn, in case first move takes ship in asteroid.				
				$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.			 
				$previousFacing = $startMove->getFacingAngle();			

				foreach ($ship->movement as $shipMove) {
					if ($shipMove->turn == $gamedata->turn) {
			
						// Only interested in moves where ship enters a NEW hex!
						if ($shipMove->type == "move" || $shipMove->type == "slipleft" || $shipMove->type == "slipright") {					
							// Check if the position matches the asteroids, e.g. zero distance.
							if ($terrainPosition->q == $shipMove->position->q && $terrainPosition->r == $shipMove->position->r) {
								$relativeBearing = $this->getTempBearing($previousPosition, $terrainPosition, $ship, $previousFacing);
								$location = $this->getCollisionLocation($relativeBearing, $ship);
								$collisiontargets[$ship->id] = $location; // Add to array to be targeted.
							}
						}
			
						// If the movement type is "end", and ship on Asteroid coordinates, remove the ship from collision targets as auto-ram chance with Enormous units will do the work here.
						/*if ($shipMove->type == "end" && 
							isset($collisiontargets[$ship->id]) &&
							$terrainPosition->q == $shipMove->position->q &&
							$terrainPosition->r == $shipMove->position->r) {
							unset($collisiontargets[$ship->id]); // Remove from collision targets.
						}*/

						$previousPosition = $shipMove->position;
						$previousFacing = $shipMove->getFacingAngle();

					}
				}
			}			
		}

	    return $collisiontargets;		
		
	}//end of checkForCollisions()		


	private function getTempBearing($shipPosition, $asteroidPosition, $ship, $facing){
		$relativeBearing = 0;	
		$oPos = mathlib::hexCoToPixel($shipPosition);//Convert to pixel format		
		$tPos = mathlib::hexCoToPixel($asteroidPosition); //Convert to pixel format
				
		$compassHeading = mathlib::getCompassHeadingOfPoint($oPos, $tPos);//Get heading using pixel formats.
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$facing);//relative bearing, compass - current facing.
       
        if( Movement::isRolled($ship) ){ //if ship is rolled, mirror relative bearing.  Not really needed, since arcs don't actually change.  
            if( $relativeBearing !== 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }        

		return round($relativeBearing);//Round and return!
	}


	private function getCollisionLocation($relativeBearing, $target) {
		foreach ($target->getLocations() as $location) {
			$min = $location["min"];
			$max = $location["max"];
			
			// Normal range check
			if ($min < $max && $relativeBearing >= $min && $relativeBearing < $max) {
				return $location["loc"];
			}
			
			// Wrap-around range check (e.g., 330-30)
			if ($min > $max && ($relativeBearing >= $min || $relativeBearing < $max)) {
				return $location["loc"];
			}
		}
		
		return 0; // Should not happen but return default if so.
	} //endof getCollisionLocation()


	public function calculateHitBase($gamedata, $fireOrder)
	{
		if($fireOrder->damageclass == "TerrainCollision" || $fireOrder->damageclass == "TerrainCrash"){ //These attacks automatically hit.
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;
		}else{
			parent::calculateHitBase($gamedata, $fireOrder);			
		}
	}

	private function getRamHitLocation($ship, $gamedata, $shipPosition){	
				if($ship->getSpeed() == 0) return 1; //Just return front location as standard if Ship is not moving.			
				// Now check other movements in the turn.
				$startMove = $ship->getLastTurnMovement($gamedata->turn);	//initialise as last move in previous turn, in case first move takes ship in asteroid.				
				$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.			 
				$previousFacing = $startMove->getFacingAngle();	
				$location = 0;		

				foreach ($ship->movement as $shipMove) {
					if ($shipMove->turn == $gamedata->turn) {
			
						// Only interested in moves where ship enters a NEW hex!
						if ($shipMove->type == "move" || $shipMove->type == "slipleft" || $shipMove->type == "slipright") {					
							// Check if the position matches the asteroids, e.g. zero distance.
							if ($shipPosition->q == $shipMove->position->q && $shipPosition->r == $shipMove->position->r) {
								$relativeBearing = $this->getTempBearing($previousPosition, $shipPosition, $ship, $previousFacing);
								$location = $this->getCollisionLocation($relativeBearing, $ship);
								return $location; //Found the first one, just return.
							}
						}
						$previousPosition = $shipMove->position;
						$previousFacing = $shipMove->getFacingAngle();
					}
				}
								
				return $location;
	}//endof getRamHitLocation()


	public function fire($gamedata, $fireOrder){
		// If hit, firing unit itself suffers damage, too (based on ramming factor of target)!
		$this->gamedata = $gamedata;
		
		//preventing double hit on the same target!
		if($this->checkAlreadyRammed($fireOrder->targetid)){
			$target = $gamedata->getShipById($fireOrder->targetid);		
			if($fireOrder->damageclass != 'TerrainCrash' && !($target instanceof FighterFlight))	{//If a TerrainCrash on fighters, we won't several orders to go through, but only then.		
				$fireOrder->shotshit = 0;
				$fireOrder->needed = 0;
				$fireOrder->rolled = 100;
				$fireOrder->pubnotes .= "TECHNICAL MISS (this collision already happened!)\n";
				return;
			}	
		}
		
		parent::fire($gamedata, $fireOrder);

		if($fireOrder->shotshit > 0){
			$pos = null;
			//$shooter = $gamedata->getShipById($fireOrder->targetid);
			$shooter = $this->unit; //technically this unit after all
			$target = $this->unit;
			$targetPos = $target->getHexPos();

			$fireOrder->chosenLocation = $this->getRamHitLocation($target, $gamedata, $targetPos);
			$damage = $this->getReturnDamage($fireOrder);
        		$damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
        		$damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);
			if($target instanceof FighterFlight){ //allocate exactly to firing fighter if no calledid!
				if ($fireOrder->calledid != -1) {
					// Called ID already set (e.g. by beforePreFiringOrderResolution for skindancing), use it.
					// We might want to verify it's not destroyed, but the generator should have handled that.
				} else {
					$ftr = $target->getFighterBySystem($this->id);
					if ($ftr->isDestroyed()) return; //do not allocate to already destroyed fighter!!! it would cause the game to randomly choose another one, which would be incorrect
					$fireOrder->calledid = $ftr->id;
				}
			}

			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
			if($fireOrder->id < 0 && $fireOrder->damageclass != 'TerrainCollision'){ //for automatic firing orders return damage will not be correctly assigned; create a virtual firing order for this damage to be displayed unless is a collision during movement.
				//Auto-ramming will generally occur in PreFiring Phase now, since there's little movement after that.
				$fireType = "prefiring";
				if($gamedata->phase == 3) $fireType = "normal";

				$newFireOrder = new FireOrder(
					-1, $fireType, $shooter->id, $target->id,
					$this->id, -1, $gamedata->turn, 1, 
					100, 100, 1, 1, 0,
					0,0,'AutoRam',10000
				);				
				$newFireOrder->chosenLocation = $this->getRamHitLocation($target, $gamedata, $targetPos);								
				if(!$this->checkAlreadyRammed($fireOrder->targetid)) $newFireOrder->notes = " Automatic ramming - Enormous unit returns damage.";
				$newFireOrder->addToDB = true;
				$this->fireOrders[] = $newFireOrder;				
			}
			$fireOrder->calledid = -1; //just in case!
			$this->setAlreadyRammed($fireOrder->targetid); //prevent repeating			
		}
	} //endof function fire

	
	function __construct($armour, $startArc, $endArc, $designDamage = 0, $fcbonus = 0, $designedToRam = false, $selfDestroy = 0){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		$maxhealth = 1;
		$powerReq = 0;
		if ($fcbonus != 0){
			$this->fireControl = array($fcbonus, $fcbonus, $fcbonus);
		}
		if ($designDamage > 0){ //most units calculate ramming factor on the fly, but some are specifically designed to ram and carry explosives to do so effectively - they have fixed ramming factor
			$this->designDamage = 	$designDamage;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		$this->designedToRam = $designedToRam;
		$this->selfDestroy = $selfDestroy;
	}
	
	
	public function getRammingFactor(){
		$dmg = 0;
		if ($this->designDamage > 0){
			$dmg = 	$this->designDamage;
		}else{
			$rammingShip = $this->unit;
			if (isset($rammingShip)) {
				$dmg = $rammingShip->getRammingFactor();
			}
		}
		return $dmg;
	}
	
	public function getDamage($fireOrder){ 

		$shooter = $this->unit;
		$gd = $this->gamedata;
		$target = $gd->getShipById($fireOrder->targetid);

		//Ramming attacks from ships moving through asteroid hexes have their own calculation.
		if($fireOrder->damageclass == 'TerrainCollision'){		
			$damage = 0;
			$targetMove = $target->getLastMovement();
			$targetSpeed = $targetMove->speed;
			if($target->factionAge >= 3) $targetSpeed = max(0, ($targetSpeed -2)); //Ancients have -2 to speed for this roll.						
			$diceRoll = Dice::d(10,1);		
			$damage = $targetSpeed * $diceRoll;				
			return $damage;
		}else{
			//modifier: +1 if greater Ini than target, +1 if head on, +1 if target is head on also
			$modifier = 0;			
			if ($shooter->iniative > $target->iniative) $modifier++;
			$bearing = abs($shooter->getBearingOnUnit($target));
			if ($bearing < 10) $modifier++;//should be 0, but at range 0 there may be a few degrees off...
			$bearing = abs($target->getBearingOnUnit($shooter));
			if ($bearing < 10) $modifier++;//should be 0, but at range 0 there may be a few degrees off...
			
			//roll and consult table
			$rfactor = $this->getRammingFactor();
			$roll = Dice::d(20,1)+$modifier;
			$this->damageModRolled = 0.25; //baseline: 25% damage
			if ($roll >= 17){
				$this->damageModRolled = 1; //100%, perfect hit!
			}else if ($roll >= 13){
				$this->damageModRolled = 0.75;
			}else if ($roll >= 7){
				$this->damageModRolled = 0.5;
			}//if lower, stays 0.25
			$damage = ceil($this->damageModRolled * $rfactor);
			if ($fireOrder->notes != '') $fireOrder->notes .= "; ";
			$fireOrder->notes .= "; mod = " . $this->damageModRolled . " rammingfactor: $rfactor" ;
			if ((!($shooter instanceof FighterFlight)) && ($target instanceof FighterFlight)) $damage += 1000;  //fighter colliding with ship will always be destroyed
			$fireOrder->notes .= "mod = " . $this->damageModRolled . " rammingfactor: $rfactor damage: $damage" ;		
			return $damage;
		}				     
	}//endof function getDamage

	public function getReturnDamage($fireOrder){    //damage that ramming unit suffers itself - using same modifier as actual attack! (already set)   
		$gd = $this->gamedata;
		$target = $gd->getShipById($fireOrder->targetid);
		$shooter = $this->unit;
		
		$rfactor = 0;
		$rammingSystem = $target->getSystemByName("RammingAttack");
		if($rammingSystem){
			$rfactor =  $rammingSystem->getRammingFactor();
			$rammingSystem->setAlreadyRammed($fireOrder->shooterid); //prevent repeating
		}else{ //no ramming system present... 
			$rfactor =  $target->getRammingFactor();
		}
		$damage = ceil($this->damageModRolled * $rfactor);			
		if ((!($target instanceof FighterFlight)) && ($shooter instanceof FighterFlight)) $damage = 1000;  //fighter colliding with ship will always be destroyed
		$damage += $this->selfDestroy;//unit will suffer additional damage on a successful attack
		$fireOrder->notes .= "; return rammingfactor: $rfactor damage: $damage" ;
		return $damage;					     
	}
	
        public function setMinDamage(){     
		$this->minDamage = ceil($this->getRammingFactor()/4);				      
	}
        public function setMaxDamage(){     
		$this->maxDamage = $this->getRammingFactor();				      
	}	
} //endof class RammingAttack



/*LtEMWaveDisruptor - Streib fighter defensive weapon*/
class LtEMWaveDisruptor extends LinkedWeapon{
	public $trailColor = array(50, 50, 200);
	public $name = "LtEMWaveDisruptor";
	public $displayName = "Light EM Wave Disruptor";
	
	public $animation = "bolt";
	public $animationColor =  array(145, 145, 245);
	public $animationExplosionScale = 0.2;//relatively large, despite doing no damage... although it's irrelevant as there is no offensive mode
	/*
	public $animationExplosionScale = 0.10;
	public $projectilespeed = 10;
	public $animationWidth = 2;
	public $trailLength = 10;
	*/
	public $intercept = 3; //very good interception
	public $loadingtime = 1;
	public $shots = 2;
	public $defaultShots = 2;
	public $rangePenalty = 2;
	public $fireControl = array(null, null, null); // no offensive mode
	public $priority = 4; //irrelevant with no offensive mode
	public $iconPath = "emWaveDisruptor.png";
	
	public $damageType = "Standard"; 
	public $weaponClass = "Electromagnetic"; 
	
	
	function __construct($startArc, $endArc, $nrOfShots = 1){
		$this->defaultShots = $nrOfShots;
		$this->shots = $nrOfShots;
		$this->intercept = $nrOfShots *3;
		parent::__construct(0, 1, 0, $startArc, $endArc);
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "No offensive mode.";
	}
	
	public function getDamage($fireOrder){        return 0;   }
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }
} //endof class LtEMWaveDisruptor




class RadCannon extends Weapon{
    /*Radiation Cannon - Cascor weapon (with LOTS of specials; essentially it's all special, with no base damage effect whatsover*/
	public $name = "RadCannon";
	public $displayName = "Rad Cannon";
	public $iconPath = "RadCannon.png";
	
	public $animation = "bolt";//behaves like a bolt
	public $animationColor = array(150, 10, 10); //make it deep red...
	public $animationExplosionScale = 0.3; //will be recalculated anyway
	/*
	public $projectilespeed = 15;
	public $animationWidth = 8;
	public $trailLength = 20;
  */
	
	public $loadingtime = 2;
	public $noOverkill = true; //does not overkill
        
	public $rangePenalty = 0.5; //-1/2hexes
	public $fireControl = array(null, 2, 3); // fighters, <mediums, <capitals 
	
	public $intercept = 0;
	public $priority = 2;// should go first/very early due to ignoring actual durability of system hit
		
	public $firingModes = array(1=>'Irradiate'); //just a convenient name
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Ion"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
		
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 8;
		if ( $powerReq == 0 ) $powerReq = 6;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		
		$this->animationExplosionScale = $this->dynamicScale(24);//scale weapon using Heavy Bolter damage output - this seems appropriate (effect is lesser but, let's say, encompassing, deserves recognition)
	}
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Doesn't actually deal damage except as noted below. Automatically hits shields if interposed.";      
		$this->data["Special"] .= "<br>Effect depends on system hit:";    
		$this->data["Special"] .= "<br> - Structure: 10 boxes marked destroyed (regardless of armor)."; 
		$this->data["Special"] .= "<br> - Shield: system destroyed."; 
		$this->data["Special"] .= "<br> - Gravitic Shield reduces generator output by 1, too."; 
		$this->data["Special"] .= "<br> - Weapon, Thruster or Jump Engine: system destroyed."; 
		$this->data["Special"] .= "<br> - C&C: critical roll forced (at +2)."; 
		$this->data["Special"] .= "<br> - Scanner: output reduced by 1."; 
		$this->data["Special"] .= "<br> - Engine: output reduced by 2."; 
		//and disable a tendril on diffuser, but there's no diffuser in game to disable at the moment
		$this->data["Special"] .= "<br>No effect on any other system. Note that armor and shields do not affect above effects.";
		$this->data["Special"] .= "<br>Does not affect ships of advanced species (eg. Ancient-born or older).";  		    
	}	
	
	//don't care about armor whatsover - due to "marking damage boxes destroyed" rather than dealing true damage
	//due to that ignore Adaptive Armor as well effectively
	public function getSystemArmourComplete($target, $system, $gamedata, $fireOrder, $pos = null){
		return 0;
	}
	
	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		//fighters are untargetable, so we know it's a ship
		//hit shield if active in arc and not destroyed (proceed to onDamagedSystem directly) (use instanceof Shield to determine!)
		
		//no effect on advanced ships!
		if($target->factionAge > 2) return;
		
		//first - find bearing from target to firing ship (needed to determine whether shield interacts with incoming shot)
		$relativeBearing = $target->getBearingOnUnit($shooter);

		//are there any active shields affecting shot?
		$affectingShields = array();
		foreach($target->systems as $shield){
			if( ($shield instanceOf Shield)  //this is an actual shield!
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
		}
	}//endof function beforeDamage
	
	//weapon formally always does 0 damage; now apply appropriate effect depending on system hit!
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		if ($ship->isDestroyed()) return; //no point allocating
		if ($system->isDestroyed()) return; //no point allocating
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$shooterID = $shooter->id;
		$remHealth = $system->getRemainingHealth();
		
		if($system instanceOf Structure) { //Structure: mark 10 damage (but no more than Structure actually possesses!)
            $destroyed = false;
			$dmgToDo = min(10,$remHealth);			
			if($dmgToDo >= $remHealth) $destroyed = true;	
			if($dmgToDo > 0 ) {			
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $fireOrder->turn, $system->id, $dmgToDo, 0, 0, $fireOrder->id, $destroyed, false, "", $this->weaponClass, $shooterID, $this->id);
				$damageEntry->updated = true;
				$system->damage[] = $damageEntry;
			}
		} else if($system instanceOf Shield) { //Shield: destroy; if Gravitic Shield - find generator and apply -1 output 
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $fireOrder->turn, $system->id, $remHealth, 0, 0, $fireOrder->id, true, false, "", $this->weaponClass, $shooterID, $this->id);
			$damageEntry->updated = true;
			$system->damage[] = $damageEntry;
			if($system instanceOf GraviticShield){ //if Gravitic Shield - find generator and apply -1 output 
				foreach( $ship->systems as $generator){
					if( ($generator instanceOf ShieldGenerator)
					  && (!$generator->isDestroyed())
					){
						$crit = new OutputReduced1(-1, $ship->id, $generator->id, "OutputReduced1", $gamedata->turn);
						$crit->updated = true;
						$crit->inEffect = false;
						$generator->criticals[] =  $crit;
						break; //don't look for further Generators
					}
				}
			}
		} else if( ($system instanceOf Weapon)    //weapon, thruster, jump drive - destroy outright
			or ($system instanceOf Thruster)
			or ($system instanceOf JumpEngine)
		) {
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $fireOrder->turn, $system->id, $remHealth, 0, 0, $fireOrder->id, true, false, "", $this->weaponClass, $shooterID, $this->id);
			$damageEntry->updated = true;
			$system->damage[] = $damageEntry;
		} else if($system instanceOf CnC) { //C&C: critical roll forced (at +2).
			$system->forceCriticalRoll = true;
			$system->critRollMod += 2;
		} else if($system instanceOf Scanner) { //Scanner: output reduced by 1.
			$crit = new OutputReduced1(-1, $ship->id, $system->id, "OutputReduced1", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = false;
			$system->setCritical($crit); //$system->criticals[] =  $crit;
		} else if($system instanceOf Engine) { //Engine: output reduced by 2.
			$crit = new OutputReduced2(-1, $ship->id, $system->id, "OutputReduced2", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = false;
			$system->setCritical($crit); //$system->criticals[] =  $crit;
		} //other systems: no effect!			 
	}//endof function onDamagedSystem
			
	public function getDamage($fireOrder){       return 0; /*no actual damage, just various effects*/  }
	public function setMinDamage(){     $this->minDamage = 10 ; /*mark as 10 damage for display and interception purposes, it actually does as much on Structure...*/     }
	public function setMaxDamage(){     $this->maxDamage = 10 ;      }
} //endof class RadCannon

	

class IonFieldGenerator extends Weapon{
	/*Cascor weapon - area debuff, no direct damage
	I don't like the official icon (looks like triple Ion Bolter really...) so will create a different one, more suggestive of ballistic nature
	*/
	public $name = "IonFieldGenerator";
	public $displayName = "Ion Field Generator";
	public $iconPath = "ionFieldGenerator.png";
	
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Ion";
	public $hextarget = true;
	public $hidetarget = true;
	public $ballistic = true;
	public $uninterceptable = true;
	public $doNotIntercept = true; //just in case
	public $priority = 1;
	
	public $range = 35;
	public $loadingtime = 2;
	

	public $animation = "ball";
	public $animationColor = array(160, 0, 255);
	public $animationExplosionScale = 2; //covers 2 hexes away from explosion center

	/*useless
	public $explosionColor = array(30, 170, 255);
	public $animationExplosionType = "AoE";	
	public $projectilespeed = 12;
	public $animationWidth = 14;
	public $trailLength = 10;
	public $trailColor = array(160, 0, 255);	
	    */
	
	public $firingModes = array(
		1 => "Ion Storm"
	);
		
	private static $alreadyAffected = array(); //list of IDs of units already affected in this firing phase - to avoid multiplying effects on overlap
	
		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		//some effects should originally work for current turn, but it won't work with FV handling of ballistics. Moving everything to next turn.
		//it's Ion (not EM) weapon with no special remarks regarding advanced races and system - so works normally on AdvArmor/Ancients etc
		$this->data["Special"] = "Targets a hex and affects all units within 2 hexes of that location.";      
		$this->data["Special"] .= "<br> Every unit in affected area is subject to following effects:"; 		
		$this->data["Special"] .= "<br> - Roll one location, as per regular attack. If weapon is hit, it's forced to shut down."; //originally just charging cycle resets - but I opted for simpler (if stronger) effect. 
		$this->data["Special"] .= "<br> - -2 Sensor rating (ships) or -1 OB (fighters) for a turn.";    
		$this->data["Special"] .= "<br> - -15 Initiative for a turn."; 
		$this->data["Special"] .= "<br> - Lose 1 (MCVs/LCVs) or 2 (larger ships) points of power for a turn."; 
		$this->data["Special"] .= "<br>Does not affect bases, mines and OSATs. Overlapping Fields are not cumulative.";
	}	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 8;
		if ( $powerReq == 0 ) $powerReq = 4;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$fireOrder->needed = 100; //always true
		$fireOrder->updated = true;
	}
	
    public function fire($gamedata, $fireOrder)
    { //sadly here it really has to be completely redefined... or at least I see no option to avoid this
        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        /** @var MovementOrder $movement */
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $posLaunch = $movement->position;//at moment of launch!!!
        //sometimes player does manage to target ship after all..
        if ($fireOrder->targetid != -1) {
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            //insert correct target coordinates: last turns' target position
            $movement = $targetship->getLastTurnMovement($fireOrder->turn);
            $fireOrder->x = $movement->position->q;
            $fireOrder->y = $movement->position->r;
            $fireOrder->targetid = -1; //correct the error
        }
        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled; //...and hit, regardless of value rolled
		$fireOrder->pubnotes .= "Ion Storm created, nearby units are handicapped for one turn. ";
		$fireOrder->shotshit++;            
		//do affect ships in range...
		$ships1 = $gamedata->getShipsInDistance($target); //directly on target hex - important for direction of impact
		$affectedUnits = $gamedata->getShipsInDistance($target, 2);
		foreach ($affectedUnits as $targetShip) {	
			if (!$targetShip->isDestroyed()) { //no point allocating to destroyed ship
				//check for overlap - return if this unit was already affected
				foreach (IonFieldGenerator::$alreadyAffected as $affectedID){
					if ($affectedID == $targetShip->id) return;	
				}
				IonFieldGenerator::$alreadyAffected[] = $targetShip->id;//add new ID to affected list			
				
				if ( (!$targetShip->base) && (!$targetShip->osat) ) {//does not affect bases, OSATs and mines
					if (isset($ships1[$targetShip->id])) { //units on target hex! direction damage is coming from: launch hex
						$sourceHex = $posLaunch;
					} else { //other units in range! direction damage is coming from: impact hex
						$sourceHex = $target;
					}
					$this->AOEdamage($targetShip, $shooter, $fireOrder, $sourceHex, 0, $gamedata);
				}
			}
		}
        $fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
    } //endof function fire	
	

	public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata)
	{
		if ($target instanceOf FighterFlight) {
		    $firstFighter = $target->getSampleFighter(); //place effect on the first fighter, even if ti's already destroyed - entire flight will be affected!
		    $this->onDamagedSystem($target, $firstFighter, 0, 0, $gamedata, $fireOrder);//no actual damage, proceed to apply effects
		} else {
		    $tmpLocation = $target->getHitSectionPos(Mathlib::hexCoToPixel($sourceHex), $fireOrder->turn);
		    $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
		    $this->onDamagedSystem($target, $system, 0, 0, $gamedata, $fireOrder);//no actual damage, proceed to apply effects
		}
	}
	
	
	/*actual applying of effect*/ 
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		//not affecting units protected by Advanced Armor!
		if(WeaponEM::isTargetEMResistant($ship,$system)) return;
		//$shooter = $gamedata->getShipById($fireOrder->shooterid);
		//$shooterID = $shooter->id;
		if ($system instanceOf Weapon) {//weapon "hit" is forced to shut down for a turn - on top of regular mandatory effects
			$crit = new ForcedOfflineOneTurn(-1, $ship->id, $system->id, "ForcedOfflineOneTurn", $gamedata->turn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$system->setCritical($crit); //$system->criticals[] =  $crit;
		}
		if($ship instanceOf FighterFlight){ //effects on fighters - applying to first fighter (already found), will affect entire flight
			$crit = new tmpsensordown(-1, $ship->id, $system->id, 'tmpsensordown', $gamedata->turn);  //-1 OB
			$crit->updated = true;
			$system->setCritical($crit); //$system->criticals[] =  $crit;
			for($i=1; $i<=3;$i++){ //-3 Initiative
				$crit = new tmpinidown(-1, $ship->id, $system->id, 'tmpinidown', $gamedata->turn);  
				$crit->updated = true;
				$system->setCritical($crit); //$system->criticals[] =  $crit;
			}
		}else{ //effects on ships
			$CnC = $ship->getSystemByName("CnC"); //temporary effects are applied to C&C 
			if($CnC){
				for($i=1; $i<=2;$i++){ //-2 Sensor rating
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->setCritical($crit); //$CnC->criticals[] =  $crit;
				}
				for($i=1; $i<=3;$i++){ //-3 Initiative
					$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->setCritical($crit); //$CnC->criticals[] =  $crit;
				}
				$powerLoss = min(2,$ship->shipSizeClass); //1 for LCVs and smaller, 2 for larger ships
				for($i=1; $i<=$powerLoss;$i++){ //-3 Initiative
					$crit = new tmppowerdown(-1, $ship->id, $CnC->id, 'tmppowerdown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->setCritical($crit); //$CnC->criticals[] =  $crit;
				}
			}
		}			 
	}//endof function onDamagedSystem
	
	
        public function getDamage($fireOrder){       return 0; /*no actual damage, just various effects*/  }
        public function setMinDamage(){     $this->minDamage = 0 ;      }
        public function setMaxDamage(){     $this->maxDamage = 0 ;      }
	
}//endof class IonFieldGenerator





class VortexDisruptor extends Weapon{
	/*Shadow weapon - destabilizes target vortex (it will collapse, destroying any ships that are trying to use it).
		In FV there are no actual vortexes to be destabilized, but such action may happen in a scenario, being narrated. 
		Hence the weapon is rendered as hex-targeted - players will get information of hit or miss.
	*/
	public $name = "VortexDisruptor";
	public $displayName = "Vortex Disruptor";
	public $iconPath = "VortexDisruptor.png";
	
	public $damageType = "Standard"; //irrelevant, really
	public $weaponClass = "Ion";
	public $hextarget = true;
	public $hidetarget = false;
	public $ballistic = false;
	public $uninterceptable = true; //although I don't think a weapon exists that could intercept it...
	public $doNotIntercept = true; //although I don't think a weapon exists that could intercept it...
	public $priority = 1;
	public $factionAge = 3;//Ancient weapon, which sometimes has consequences!
	
	public $range = 23;//no point firing at further target with base 24 to hit!
	public $loadingtime = 3;
    public $rangePenalty = 1;//-1/hex
	
	public $animation = "ball";
	public $animationColor = array(245, 90, 90);
	public $animationExplosionScale = 0.5; //single hex explosion
	public $animationExplosionType = "AoE";
	/*useless
	public $trailColor = array(245, 90, 90);
	public $explosionColor = array(255, 0, 0);
	public $projectilespeed = 10;
	public $animationWidth = 14;
	public $trailLength = 10;
	    */
	
	public $firingModes = array(
		1 => "Disruption"
	);
		
			
	//in pickup play it's essentially a power source - and Shadows don't have all that much use for extra power. Very low repair priority,although maybe above Hangars ;)
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    		
		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Weapon that destabilizes hyperspace vortexes, therefore preventing escape (any ships entering destabilized vortex is destroyed).";      
		$this->data["Special"] .= "<br>There are no actual vortexes in game, but such action might be useful for a scenario - in such case, target weapon on a hex where (by scenario narration) vortex appears."; //originally just charging cycle resets - but I opted for simpler (if stronger) effect. 
		$this->data["Special"] .= "<br>Game will calculate whether disruption was successful (base chance is 120%, -5%/hex - EW is irrelevant) - but will NOT show it during targeting.";  
		$this->data["Special"] .= "<br>Being half-phased renders weapon ineffective (hit chance = 0)."; 
	}	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ) $maxhealth = 4;
		if ( $powerReq == 0 ) $powerReq = 8;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	}
	
	public function calculateHitBase($gamedata, $fireOrder)
	{
		//reduce by distance...
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$shooterHalfphased = Movement::isHalfPhased($shooter, $gamedata->turn);
		$firingPos = $shooter->getHexPos();
		if ($fireOrder->targetid != -1) { //for some reason ship was targeted!
			$targetship = $gamedata->getShipById($fireOrder->targetid);
			//insert correct target coordinates: target ships' position!
			$targetPos = $targetship->getHexPos();
			$fireOrder->x = $targetPos->q;
			$fireOrder->y = $targetPos->r;
			$fireOrder->targetid = -1; //correct the error
		}
		$targetPos = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
		$dis = mathlib::getDistanceHex($firingPos, $targetPos);
		$rangePenalty = $this->rangePenalty * $dis;
		if($shooterHalfphased){ //this prevents Disruptor from working 
			$fireOrder->needed = 0;
		}else{//calculate hit chance: 24 minus range penalty
			$fireOrder->needed = 24 - $rangePenalty;
			$fireOrder->needed = $fireOrder->needed *5; //convert to d100
		}
		$fireOrder->notes .=  "shooter: " . $firingPos->q . "," . $firingPos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty ";
		$fireOrder->updated = true;
	}
	
    public function fire($gamedata, $fireOrder)
    { //sadly here it really has to be completely redefined... or at least I see no option to avoid this
        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
        $shooter = $gamedata->getShipById($fireOrder->shooterid);        
        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled; 
		$fireOrder->pubnotes .= " chance " . $fireOrder->needed . "%,";
		if($rolled <= $fireOrder->needed){//HIT!
			$fireOrder->pubnotes .= " HIT - target vortex is disrupted, ships entering it are destroyed! ";
			$fireOrder->shotshit++;
		}else{ //MISS!
			$fireOrder->pubnotes .= " MISSED! ";
		}
	} //endof function fire	
	
	
	public function getDamage($fireOrder){       return 0; /*no actual damage, just disruption of vortex which is narrative only*/  }
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }
	
}//endof class VortexDisruptor




class ParticleConcentrator extends Raking{
    /*Particle Concentrator - Gaim weapon*/
	public $name = "ParticleConcentrator";
	public $displayName = "Particle Concentrator";
	public $iconPath = "ParticleConcentrator.png";
	
	public $animation = "laser";
        public $animationColor = array(255, 163, 26); //should be the same as Particle Cannon
	/*
	public $trailColor = array(30, 170, 255);	
	public $animationWidth = 4;
	public $animationWidthArray = array(1=>4, 2=>5, 3=>6, 4=>7, 5=>8, 6=>10);
	public $animationWidth2 = 0.3;
        public $animationExplosionScale = 0.25;
	public $animationExplosionScaleArray = array(1=>0.25, 2=>0.35, 3=>0.45, 4=>0.55, 5=>0.70, 6=>0.85);
      */
        public $loadingtime = 2;
	public $intercept = 1; //intercept rating -1     
	
        public $priority = 8;
        public $priorityArray = array(1=>8, 2=>7, 3=>7, 4=>7, 5=>7); //weakest mode is light Raking weapon, heavier ones are heavy raking weapons
	public $firingMode = 1;	
            public $firingModes = array(
                1 => "Single",
                2 => "2combined",
                3 => "3combined",
                4 => "4combined",
                5 => "5combined",
                6 => "6combined"
            );
        public $rangePenalty = 0.5; //-1/2 hexes - and this stays constant!
            //public $rangePenaltyArray = array( 1=>2, 2=>1, 3=>0.5, 4=>0.33, 5=>0.25 ); //Raking and Piercing mode
        public $fireControl = array(2, 4, 5); // fighters, <mediums, <capitals 
            public $fireControlArray = array( 1=>array(2, 4, 5), 2=>array(4, 6, 7), 3=>array(6, 8, 9), 4=>array(8, 10, 11), 5=>array(10, 12, 13), 6=>array(12, 14, 15) ); //+2 to hit per every additional combining weapon
	
	
	
	    public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Particle"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	public $isCombined = false; //is being combined with other weapon
	public $alreadyConsidered = false; //already considered - either being fired or combined
	public $testRun = false;//testRun = true means hit chance is calculated nominal skipping concentration issues - for subordinate weapon to calculate average hit chance
	
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Can combine multiple Particle Concentrator into one concentrated shot - for +2 Fire Control and +1d10 damage per additional weapon (up to 5 additional weapon can be added).";  
		      $this->data["Special"] .= "<br>If You allocate multiple Particle Concentrators in higher mode of fire at the same target, they will be combined."; 
		      $this->data["Special"] .= "<br>If not enough weapons are allocated to be combined, weapons will be fired in highest actually possible mode instead.";  
		      $this->data["Special"] .= "<br>Concentrators do not need to be on the same ship, but need to be on the same hex to combine."; //tabletop: within 1 hex  			  
		      $this->data["Special"] .= "<br>Hit chance will be average of all weapons combining.";//tabletop: use average EW, best range, worst criticals and no lock-on if ANY weapon lacks lock-on
	    }	
	
		
	
	public function fire($gamedata, $fireOrder){
	    if ($this->isCombined) $fireOrder->shots = 0; //no actual shots from weapon that's firing as part of combined shot!
	    parent::fire($gamedata, $fireOrder);
	} //endof function fire	
	
	
	//if fired in higher mode - combine with other weapons that are so fired!
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->alreadyConsidered = true;
		$combinedChance = 0;
		if ($this->testRun){ //calculate nominal, skipping concentration issues - for subordinate weapon to calculate average hit chance
			parent::calculateHitBase($gamedata, $fireOrder);
			return;
		}
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->changeFiringMode($fireOrder->firingMode);
			return;
		}
		if ($fireOrder->firingMode > 1){ //for single fire there's nothing special
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);	
			$srcPos = $firingShip->getCoPos();			
			$shipsSameHex = $gamedata->getShipsInDistance($firingShip, 0);//all ships on the same hex can potentially combine!
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			foreach($shipsSameHex as $otherShip) {			
				//look for firing orders from same hex at same target in same mode - and make sure it's same type of weapon
				$allOrders = $otherShip->getAllFireOrders($gamedata->turn);
				foreach($allOrders as $subOrder) {
					if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
						//order data fits - is weapon another Particle Concentrator?...
						$subWeapon = $otherShip->getSystemById($subOrder->weaponid);
						if ($subWeapon instanceof ParticleConcentrator){
							if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
								$subordinateOrdersNo++;
								$subordinateOrders[] = $subOrder;
							}
						}
					}
					if ($subordinateOrdersNo>=($fireOrder->firingMode-1)) break;//enough subordinate weapons found! - exit loop
				}
				if ($subordinateOrdersNo>=($fireOrder->firingMode-1)) break;//enough subordinate weapons found! - exit loop
			}
			//if not enough weapons to combine in desired mode - combine still, just in appropriately lesser mode
			if ($subordinateOrdersNo < ($fireOrder->firingMode-1)){ 
				$fireOrder->firingMode = $subordinateOrdersNo +1; //worst case it's single fire ;)
			}			
			//combining - set other combining weapons/fire orders to technical status! (and change their firing mode to the same as base weapon, also just in case ;)
			foreach($subordinateOrders as $subOrder){
				$otherShip = $gamedata->getShipById($subOrder->shooterid);	
				$subWeapon = $otherShip->getSystemById($subOrder->weaponid);
				$subWeapon->isCombined = true;
				$subWeapon->alreadyConsidered = true;
				$subWeapon->doNotIntercept = true;
				$subOrder->pubnotes .= 'Combined into another shot. ';
				$subOrder->firingMode = $fireOrder->firingMode;
				
				//get nominal hit chance...
				$subWeapon->testRun = true;
				$subWeapon->calculateHitBase($gamedata, $subOrder);
				$combinedChance += $subOrder->needed;
				//and now nullify  hit chance...
				$subWeapon->testRun = false;
				$notes = "Technical fire order - weapon combined into another shot. ";
				$subOrder->needed = 0;
				$subOrder->notes = $notes;
			}
		}
		parent::calculateHitBase($gamedata, $fireOrder);
		if($fireOrder->firingMode > 1){ //for concentrated shot - hit chance is average of hit chances of all weapons
			$combinedChance += $fireOrder->needed;
			$fireOrder->needed = round($combinedChance/$fireOrder->firingMode);
			$fireOrder->notes .= 'Modified as average of concentrating shots! ';
		}
	}//endof function calculateHitBase
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 7;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
	
	
    public function getDamage($fireOrder){
		return Dice::d(10, 1+$this->firingMode)+15; //2d10+15 +1d10 per every additional weapon
	}
	public function setMinDamage(){    
		$this->minDamage = 1+$this->firingMode+15;
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
	public function setMaxDamage(){
		$this->maxDamage = 10*(1+$this->firingMode)+15;
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;  
	}
} //endof class ParticleConcentrator





/* Vorlon secondary weapon */
class VorlonDischargeGun extends Weapon{
	public $name = "VorlonDischargeGun";
	public $displayName = "Discharge Gun";
	public $iconPath = "VorlonDischargeGun.png";
	
	public $animation = "bolt";
	public $animationColor = array(175, 255, 225);
	/*
	public $trailColor = array(175, 225, 175);
	public $projectilespeed = 15;
	public $animationWidth = 4;
	public $animationExplosionScale = 0.25;
	public $trailLength = 30;
	*/
	public $loadingtime = 1;
	public $normalload = 2;
		
	//public $canChangeShots = true; //No Longer needed after Split Shots added. 
	//public $shots = 4;
	//public $defaultShots = 4; //can fire up to 4 shots (if power is available); LET'S DECLARE ALL 4 BY DEFAULT - chance of player wanting full power is higher than conserving energy (if he has energy shortages he'll be stopped by EoT check anyway)
	//public $maxVariableShots = 4; //For front end to know how many shots weapon CAN fire where this can be changed after locking in.	
	public $intercept = 2; //intercept rating -2
	
	public $priority = 8; //light Raking weapon - even highest damaging mode falls into this category (borderline)
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "2 Power",
		2 => "4 Power",
		3 => "6 Power"
	);
	
    public $rangePenalty = 0.5; //-1/2 hexes
	public $fireControl = array(4, 3, 2); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(4, 3, 2), 2=>array(4, 3, 2), 3=>array(4, 3, 2) ); //same FC for every mode

	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
//	public $multiplied = false; //technical variable
	public $canSplitShots = true; //New method, let's just have shots treated as separate shots! - DK
	public $guns = 4;
	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 10;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
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
		$this->data["Special"] .= "Firing mode affects damage output (and power used):";  
		$this->data["Special"] .= "<br> - 2 power: 2d10+2"; 
		$this->data["Special"] .= "<br> - 4 power: 3d10+3"; 
		$this->data["Special"] .= "<br> - 6 power: 4d10+4"; 
		$this->data["Special"] .= "<br>Fires up to 4 times (costing power per shot), at same or different targets.";
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";
		$this->data["Special"] .= "<br>Interceping shots consume 2 power per shot (refunded if not used).";   	
	}
		
		
		

	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 2)+2; //2 Power
				break;
			case 2:
				return Dice::d(10, 3)+3; //4 Power
				break;
			case 3:
				return Dice::d(10, 4)+4; //6 Power
				break;
			default: //should never go here
				return Dice::d(10, 2)+2;
				break;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 4; //2 Power
				break;
			case 2:
				$this->minDamage = 6; //4 Power
				break;
			case 3:
				$this->minDamage = 8; //6 Power
				break;
			default: //should never go here
				$this->minDamage = 4;
				break;
		}
	}
             
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 22; //2 Power
				break;
			case 2:
				$this->maxDamage = 33; //4 Power
				break;
			case 3:
				$this->maxDamage = 44; //6 Power
				break;
			default: //should never go here
				$this->maxDamage = 22;
				break;
		}
	}
	
	
	//hit chance calculation is standard - but at this stage power used information is sent to Capacitor, too
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
	//		$powerNeeded = 2*$fireOrder->firingMode*$fireOrder->shots;
			$powerNeeded = 2*$fireOrder->firingMode;	//Each shot will have its own Firing Order now - DK
			$capacitor->doDrawPower($powerNeeded);
		}
		parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation
	}//endof function calculateHitBase

	/* drain power when firing defensively
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$capacitor->doDrawPower(2);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	/*can intercept anything only if Capacitor holds enough Power...*/
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$powerIsAvailable = false;
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			if($capacitor->canDrawPower(2)) $powerIsAvailable = true;
		}
		return $powerIsAvailable;
	}

	/*
	//if fired offensively - make as many attacks as firing order declares shots (and resent number of shots declared to 1 :) )
	//if defensively - make weapon have 4 GUNS (would be temporary, but enough to assign multiple shots for interception)
	public function beforeFiringOrderResolution($gamedata){
		//Previous method before split shots ability added.
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;

		foreach($this->fireOrders as $fire){
			if(($fire->type =='normal') && ($fire->turn == $gamedata->turn)) $offensiveShot = $fire;
		}
		if($offensiveShot!==null){ //offensive fire declared, multiply!
			$shotsDeclared = $fire->shots;
			$fire->shots = 1;
			while($shotsDeclared > 1){ //first attack is already declared!
				$multipliedFireOrder = new FireOrder( -1, $offensiveShot->type, $offensiveShot->shooterid, $offensiveShot->targetid,
					$offensiveShot->weaponid, $offensiveShot->calledid, $offensiveShot->turn, $offensiveShot->firingMode,
					0, 0, 1, 0, 0, null, null
				);
				$multipliedFireOrder->addToDB = true;
				$this->fireOrders[] = $multipliedFireOrder;
				$shotsDeclared--;	      
			}
		}else{//offensive fire NOT declared, multiply guns for interception!
			$this->guns = 4; //up to 4 intercept shots (if Power is available and weapon is declared eligible)
		}
		
	} //endof function beforeFiringOrderResolution
	*/
	
}//endof class VorlonDischargeGun




/* Vorlon secondary weapon */
class VorlonDischargePulsar extends Weapon{
	public $name = "VorlonDischargePulsar";
	public $displayName = "Discharge Pulsar";
	public $iconPath = "VorlonDischargePulsar.png";
	
	public $animation = "bolt";
	public $animationColor = array(175, 255, 225);
	
	public $loadingtime = 1;
	public $normalload = 2;

	protected $useDie = 3; //die used for base number of hits
		public $groupingArray = array(1=>15, 2=>15);
		public $maxpulses = 4;
        public $priorityArray = array(1=>5, 2=>5); 
	public $defaultShotsArray = array(1=>4, 2=>4); //for Pulse mode it should be equal to maxpulses
		
	public $intercept = 2; //intercept rating -2
	
	public $priority = 5; 
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "1xPower",
		2 => "2xPower",
	);
	
    public $rangePenalty = 0.5; //-1/2 hexes
	public $fireControl = array(5, 3, 2); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(5, 3, 2), 2=>array(5, 3, 2) ); //same FC for every mode

	public $damageType = "Pulse"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	public $multiplied = false; //technical variable
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 12;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
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
		$this->data["Special"] .= "<br>Firing mode affects damage output (and power used):";  
		$this->data["Special"] .= "<br> - 4 power: 12 1d3 times, max 4"; 
		$this->data["Special"] .= "<br> - 8 power: 18, 1d3 times, max 4"; 
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";		
	}
		
	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return 12; //4 Power
				break;
			case 2:
				return 18; //8 Power
				break;
			default: //should never go here
				return 12;
				break;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 12; //4 Power
				break;
			case 2:
				$this->minDamage = 18; //8 Power
				break;
			default: //should never go here
				$this->minDamage = 12;
				break;
		}
				$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
             
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 12; //4 Power
				break;
			case 2:
				$this->maxDamage = 18; //8 Power
				break;
			default: //should never go here
				$this->maxDamage = 12;
				break;
		}
				$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
	
	
	//hit chance calculation is standard - but at this stage power used information is sent to Capacitor, too
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$powerNeeded = 4*$fireOrder->firingMode; //*$fireOrder->shots;
			$capacitor->doDrawPower($powerNeeded);
		}
		parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation
	}//endof function calculateHitBase

	/* drain power when firing defensively
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$capacitor->doDrawPower(4);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	/*can intercept anything only if Capacitor holds enough Power...*/
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$powerIsAvailable = false;
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			if($capacitor->canDrawPower(4)) $powerIsAvailable = true;
		}
		return $powerIsAvailable;
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

	//necessary for Pulse mode
        protected function getPulses($turn)
        {
            return Dice::d(3);
        }
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }

	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}


}//endof class VorlonDischargePulsar






/* Vorlon primary weapon */
class VorlonLightningCannon extends Weapon{
	public $name = "VorlonLightningCannon";
	public $displayName = "Lightning Cannon";
	//public $iconPath = "VorlonDischargeGun.png";
	
	public $animation = "laser";
	public $animationColor = array(195, 235, 195);
	/*
	public $trailColor = array(175, 225, 175);
	public $projectilespeed = 15;
	public $animationWidth = 4;
	public $animationExplosionScale = 0.3;
	public $trailLength = 30;
	*/
	
	//technical variables for combined shot
	public $isCombined = false;
	public $alreadyConsidered = false;
	
	public $loadingtime = 1;
	public $normalload = 2;
	
	public $uninterceptable = true; //Lightning Cannon is uninterceptable
	public $intercept = 4; //intercept rating -4
	public $modeLetters = 1;
	public $modeLettersArray = array(
		1 => 1,
		2 => 1,
		3 => 1,
		4 => 1,
		5 => 2,
		6 => 2
	);
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "1-Prong",
		2 => "2-Prongs",
		3 => "3-Prongs",
		4 => "4-Prongs",
		5 => "3Piercing",
		6 => "4Piercing"
	);
	
	public $priority = 5; //medium Standard weapon - for single fire...
	public $priorityArray = array(1=>5, 2=>8, 3=>7, 4=>7, 5=>2, 6=>2); //single fire is Medium Standard, double Light Raking, 3/4 Heavy Raking, 5/6 Piercing)
    public $rangePenalty = 1; 
	public $rangePenaltyArray = array(1=>1, 2=>0.5, 3=>0.33, 4=>0.25, 5=>0.33, 6=>0.25);
	public $fireControl = array(8, 5, 5); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(8, 5, 5), 2=>array(4,5,5), 3=>array(0,5,5), 4=>array(null,5,5), 5=>array(null,1,1), 6=>array(null,1,1) ); // fighters, <mediums, <capitals ; Piercing shots incorporate Piercing shot penalty into FC
	
	//number of prongs/power required to fire - PER PRONG!
	public $powerRequiredArray = array( 1=>array(1,1), 2=>array(2,2), 3=>array(3,4), 4=>array(4,6), 5=>array(3,4), 6=>array(4,6) );

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array( 1=>"Standard", 2=>"Raking", 3=>"Raking", 4=>"Raking", 5=>"Piercing", 6=>"Piercing" );
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	//rake size array
	public $raking = 10;//more in higher modes
	public $rakingArray = array( 1=>10, 2=>10, 3=>15, 4=>20, 5=>15, 6=>20 );
	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $orientation ) //$orientation is 'L'eft or 'R'ight - regarding graphics
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 15;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
		}
		$this->iconPath = "VorlonLightningCannon".$orientation.".png";
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		$this->addTag('Lightning Cannon'); //needed to properly allocate hits on Vorlon ships, where most of these weapons are used
	}
	

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    		
		$this->data["Special"] .= "Uninterceptable. Capable of multiple modes of fire. Higher modes require combining multiple prongs on the same target.";   
		$this->data["Special"] .= "<br>Firing modes available (Number of prongs/power used per SHOT/damage output (and mode)/range penalty):";  
		$this->data["Special"] .= "<br> - 1 Prong: 1 Power, 1d10+8 Standard, -5/hex"; 
		$this->data["Special"] .= "<br> - 2 Prongs: 4 Power, 2d10+16 Raking(10), -2.5/hex";
		$this->data["Special"] .= "<br> - 3 Prongs: 12 Power, 4d10+32 Raking(15), -1.65/hex"; 
		$this->data["Special"] .= "<br> - 4 Prongs: 24 Power, 8d10+64 Raking(20), -1.25/hex"; 
		$this->data["Special"] .= "<br> - 3 Prongs Piercing: 12 Power, 4d10+32 Piercing, -1.65/hex"; 
		$this->data["Special"] .= "<br> - 4 Prongs Piercing: 24 Power, 8d10+64 Piercing, -1.25/hex"; 
		$this->data["Special"] .= "<br>If weapon is mis-declared (shot is declared but not enough prongs are allocated in appropriate mode) shot will automatically miss and Power will NOT be drained."; 
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";		
	}
		
		
		

	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 1)+8; 
				break;
			case 2:
				return Dice::d(10, 2)+16; 
				break;
			case 3:	
				return Dice::d(10, 4)+32; 
				break;
			case 4:
				return Dice::d(10, 8)+64; 
				break;
			case 5:
				return Dice::d(10, 4)+32; 
				break;
			case 6:
				return Dice::d(10, 8)+64; 
				break;
			default: //should never go here
				return Dice::d(10, 1)+8;
				break;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; 
				break;
			case 2:
				$this->minDamage = 18; 
				break;
			case 3:
				$this->minDamage = 36; 
				break;
			case 4:
				$this->minDamage = 72; 
				break;
			case 5:
				$this->minDamage = 36; 
				break;
			case 6:
				$this->minDamage = 72; 
				break;
			default: //should never go here
				$this->minDamage = 9;
				break;
		}
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 18; 
				break;
			case 2:
				$this->maxDamage = 36; 
				break;
			case 3:
				$this->maxDamage = 72; 
				break;
			case 4:
				$this->maxDamage = 144; 
				break;
			case 5:
				$this->maxDamage = 72; 
				break;
			case 6:
				$this->maxDamage = 144; 
				break;
			default: //should never go here
				$this->maxDamage = 18;
				break;
		}
	}
	
	
	//hit chance calculation is standard - but at this stage power used information is sent to Capacitor, too
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->changeFiringMode($fireOrder->firingMode);
		$doDrain = true;
		$doCalculate = true;
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->doNotIntercept = true;
			return;
		}
		
		$powerRequired = $this->powerRequiredArray[$fireOrder->firingMode];				
		$powerPerProng = $powerRequired[1];
		$prongsNeeded = $powerRequired[0] ; 
		if ($prongsNeeded < 2){ //nothing extra is needed, do fire!
			$doDrain = true;
			$doCalculate = true;
		} else {//additional prongs needed!
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Lightning Cannon?...
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if ($subWeapon instanceof VorlonLightningCannon){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($prongsNeeded-1)) break;//enough subordinate weapons found! - exit loop
			}						
			if ($subordinateOrdersNo == ($prongsNeeded-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}				
				$doDrain = true;
				$doCalculate = true;
			}else{//not enough weapons to combine in this mode - mark combined and effectively don't fire
				$notes = "technical fire order - weapon mis-declared";
				$fireOrder->chosenLocation = 0; //tylko techniczne i tak
				$fireOrder->needed = 0;
				$fireOrder->shots = 0;
				$fireOrder->notes = $notes;
				$fireOrder->updated = true;
				$this->doNotIntercept = true;
				$doDrain = false;
				$doCalculate = false;
			}
		}
		
		if($doDrain){
			$capacitor = $this->unit->getSystemByName("PowerCapacitor");
			if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
				$powerNeeded = $powerPerProng*$prongsNeeded;//drain for ALL combined prongs!
				$capacitor->doDrawPower($powerNeeded);
			}
		}
		if($doCalculate){
			parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation
		}
	}//endof function calculateHitBase


	/* drain power when firing defensively
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$capacitor->doDrawPower(1);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	/*can intercept anything only if Capacitor holds enough Power...*/
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$powerIsAvailable = false;
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			if($capacitor->canDrawPower(1)) $powerIsAvailable = true;
		}
		return $powerIsAvailable;
	}

}//endof class VorlonLightningCannon






/* Vorlon primordial primary weapon */
class VorlonLightningGun extends Weapon{
	public $name = "VorlonLightningGun";
	public $displayName = "Lightning Gun";
	
	public $animation = "laser";
	public $animationColor = array(195, 235, 195);
	
	//technical variables for combined shot
	public $isCombined = false;
	public $alreadyConsidered = false;

	private $pairing = null;
	private $mirror= null;
	
	public $loadingtime = 1;
	public $normalload = 2;
	
	public $uninterceptable = true; //Lightning Cannon is uninterceptable
	public $intercept = 4; //intercept rating -4
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "1Prong",
		2 => "2Prongs",
		3 => "3Prongs",
		4 => "4Prongs",
		5 => "P3Piercing",
		6 => "Q4Piercing"
	);
	
	public $priority = 5; //medium Standard weapon - for single fire...
	public $priorityArray = array(1=>5, 2=>8, 3=>7, 4=>7, 5=>2, 6=>2); //single fire is Medium Standard, double Light Raking, 3/4 Heavy Raking, 5/6 Piercing)
    public $rangePenalty = 1; 
	public $rangePenaltyArray = array(1=>1, 2=>0.5, 3=>0.33, 4=>0.33, 5=>0.33, 6=>0.33);
	public $fireControl = array(7, 4, 4); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(7, 4, 4), 2=>array(3,4,4), 3=>array(0,4,4), 4=>array(null,4,4), 5=>array(null,0,0), 6=>array(null,0,0) ); // fighters, <mediums, <capitals ; Piercing shots incorporate Piercing shot penalty into FC
	
	//number of prongs/power required to fire - PER PRONG!
	public $powerRequiredArray = array( 1=>array(1,1), 2=>array(2,1.5), 3=>array(3,3), 4=>array(4,4.5), 5=>array(3,3), 6=>array(4,4.5) );

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array( 1=>"Standard", 2=>"Raking", 3=>"Raking", 4=>"Raking", 5=>"Piercing", 6=>"Piercing" );
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	//rake size array
	public $raking = 10;//more in higher modes
	public $rakingArray = array( 1=>10, 2=>10, 3=>15, 4=>20, 5=>15, 6=>20 );
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $orientation, $pairing ) //$orientation is 'L'eft or 'R'ight - regarding graphics
	{
		$this->pairing = $pairing;
		$this->displayName = 'Lightning Gun ' . $pairing . ''; 				
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 11;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
		}
		$this->iconPath = "VorlonLightningGun".$orientation.".png";
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		$this->addTag('Lightning Gun'); //needed to properly allocate hits on Vorlon ships, where most of these weapons are used
	}

       function addMirror($mirror){ //Function used to assign the mirrored lightning guns on ship php file.
             $this->mirror = $mirror;
        }

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    		
		$this->data["Special"] .= "Uninterceptable. Capable of multiple modes of fire. Higher modes require combining multiple prongs on the same target."; 
		$this->data["Special"] .= "<br>Firing modes available (Number of prongs/power used per SHOT/damage output (and mode)/range penalty):";  
		$this->data["Special"] .= "<br> - 1 Prong: 1 Power, 1d5+8 Standard, -5/hex"; 
		$this->data["Special"] .= "<br> - 2 Prongs: 3 Power, 1d10+16 Raking(10), -2.5/hex";
		$this->data["Special"] .= "<br> - 3 Prongs: 9 Power, 2d10+32 Raking(15), -1.65/hex"; 
		$this->data["Special"] .= "<br> - 4 Prongs: 18 Power, 4d10+64 Raking(20), -1.65/hex"; 
		$this->data["Special"] .= "<br> - 3 Prongs Piercing: 9 Power, 2d10+32 Piercing, -1.65/hex"; 
		$this->data["Special"] .= "<br> - 4 Prongs Piercing: 18 Power, 4d10+64 Piercing, -1.65/hex"; 
		$this->data["Special"] .= "<br>If weapon is mis-declared (shot is declared but not enough prongs are allocated in appropriate mode) shot will automatically miss and Power will NOT be drained."; 
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";		
	}

	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(5, 1)+8; 
				break;
			case 2:
				return Dice::d(10, 1)+16; 
				break;
			case 3:	
				return Dice::d(10, 2)+32; 
				break;
			case 4:
				return Dice::d(10, 4)+64; 
				break;
			case 5:
				return Dice::d(10, 2)+32; 
				break;
			case 6:
				return Dice::d(10, 4)+64; 
				break;
			default: //should never go here
				return Dice::d(5, 1)+8;
				break;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; 
				break;
			case 2:
				$this->minDamage = 17; 
				break;
			case 3:
				$this->minDamage = 34; 
				break;
			case 4:
				$this->minDamage = 68; 
				break;
			case 5:
				$this->minDamage = 34; 
				break;
			case 6:
				$this->minDamage = 68; 
				break;
			default: //should never go here
				$this->minDamage = 9;
				break;
		}
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 13; 
				break;
			case 2:
				$this->maxDamage = 26; 
				break;
			case 3:
				$this->maxDamage = 52; 
				break;
			case 4:
				$this->maxDamage = 104; 
				break;
			case 5:
				$this->maxDamage = 52; 
				break;
			case 6:
				$this->maxDamage = 104; 
				break;
			default: //should never go here
				$this->maxDamage = 13;
				break;
		}
	}
	
	//hit chance calculation is standard - but at this stage power used information is sent to Capacitor, too
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->changeFiringMode($fireOrder->firingMode);
		$doDrain = true;
		$doCalculate = true;
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->doNotIntercept = true;
			return;
		}
		
		$powerRequired = $this->powerRequiredArray[$fireOrder->firingMode];				
		$powerPerProng = $powerRequired[1];
		$prongsNeeded = $powerRequired[0] ; 
		if ($prongsNeeded < 2){ //nothing extra is needed, do fire!
			$doDrain = true;
			$doCalculate = true;
		} else {//additional prongs needed!
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Lightning Cannon?...
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if (($subWeapon instanceof VorlonLightningGun) || ($subWeapon instanceof VorlonLightningGun2)){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($prongsNeeded-1)) break;//enough subordinate weapons found! - exit loop
			}						
			if ($subordinateOrdersNo == ($prongsNeeded-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}				
				$doDrain = true;
				$doCalculate = true;
			}else{//not enough weapons to combine in this mode - mark combined and effectively don't fire
				$notes = "technical fire order - weapon mis-declared";
				$fireOrder->chosenLocation = 0; //tylko techniczne i tak
				$fireOrder->needed = 0;
				$fireOrder->shots = 0;
				$fireOrder->notes = $notes;
				$fireOrder->updated = true;
				$this->doNotIntercept = true;
				$doDrain = false;
				$doCalculate = false;
			}
		}
		
		if($doDrain){
			$capacitor = $this->unit->getSystemByName("PowerCapacitor");
			if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
				$powerNeeded = $powerPerProng*$prongsNeeded;//drain for ALL combined prongs!
				$capacitor->doDrawPower($powerNeeded);
			}
		}
		if($doCalculate){
			parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation
		}
	}//endof function calculateHitBase


	/* drain power when firing defensively
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$capacitor->doDrawPower(1);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	/*can intercept anything only if Capacitor holds enough Power...*/
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$powerIsAvailable = false;
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			if($capacitor->canDrawPower(1)) $powerIsAvailable = true;
		}
		return $powerIsAvailable;
	}

		//If Lightning Gun is destroyed, destroy the paired lightning gun as well.
		public function criticalPhaseEffects($ship, $gamedata)
	    { 
		  	parent::criticalPhaseEffects($ship, $gamedata);//Some critical effects like Limpet Bore might destroy weapon in this phase!
	  	 	    
			//Need to check if a destroyed lightning gun has been repaired and then restore the mirrored lightning gun
			if(!$this->isDestroyed()){
				$mirror = $this->mirror;  // get the associated mirror lightning gun
				$gunHealth = $mirror->getRemainingHealth(); // If destroyed this is 0, otherwise it should be 1
				if($gunHealth<1){
					$mirrorDamage = $mirror->maxhealth;
					$toBeFixed = $mirrorDamage;
					$undestroy=true;
					//actual healing entry
					$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $mirror->id, -$toBeFixed, 0, 0, -1, false, $undestroy, 'SelfRepair', 'SelfRepair');
					$damageEntry->updated = true;
					$mirror->damage[] = $damageEntry;
				}
				return;//Lightning gun is not destroyed, all is well, or it was resurrected and brought the mirror gun back online.
			}

			if($this->isDestroyed()){ //Destroy the mirror gun if the main gun is destroyed
				$mirror = $this->mirror;
				$gunHealth = $mirror->getRemainingHealth();	//Just in case it's higher than 1 for some reason...						
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $mirror->id, $gunHealth, 0, 0, -1, true, false, "Lightning Gun Destroyed - Mirrored Gun removed");
				$damageEntry->updated = true;
				$this->damage[] = $damageEntry;								
			}				
	    } //endof function criticalPhaseEffects	

		//Now, apply the primary gun's critical to the matching mirror gun
		public function setCritical($critical, $turn=0){ //Critical already known and passed to this funciton
			$this->criticals[] = $critical; //Set original critical to Lightning Gun itself
			$mirror = $this->mirror; //Find the appropriate mirror gun
			//Get the other variables you need
			$criticalPhpClass = $critical->phpclass; //What type of crit was set on the original Lightning Gun
			//Now set the same type of critical for the mirro gun
			$mirrorCritical = new $criticalPhpClass(-1, $critical->shipid,$mirror->id, $criticalPhpClass, $critical->turn, $critical->turnend);
			$mirror->setCritical($mirrorCritical); //And set it
		}

    //Overwrite repairCrtical() function in main Lightning Gun system with this.
    public function repairCritical($critDmg, $turn){
            //repair critical on Lightning Gun as usual.
        $critDmg->turnend = $turn;//actual repair 😉
        $critDmg->forceModify = true; //actually save the repair...
        $critDmg->updated = true; //actually save the repair cd!...

        $mirrorGun = $this->mirror; //Get details of mirror Lighting Gun system

        foreach($mirrorGun->criticals as $mirrorCritical){ //Search through any criticals in mirror gun, we know there should be a matching critical.
            if($mirrorCritical->phpclass == $critDmg->phpclass && $critDmg->turn >= $turn){ //Make sure it's same type of critical, and is current.
                $mirrorCritical->turnend = $turn;//actual repair 😉
                $mirrorCritical->forceModify = true; //actually save the repair...
                   $mirrorCritical->updated = true; //actually save the repair cd!...
                break; //Matching critical repaired, break out of foreach loop and don't search for any more.
            }
        }
    }//endof repairCritical() 

}//endof class VorlonLightningGun







/* Vorlon primordial primary weapon */
class VorlonLightningGun2 extends Weapon{
	public $name = "VorlonLightningGun2";
	public $displayName = "Mirror Lightning Gun";
	
	public $animation = "laser";
	public $animationColor = array(195, 235, 195);
	
	//technical variables for combined shot
	public $isCombined = false;
	public $alreadyConsidered = false;
	
	public $loadingtime = 1;
	public $normalload = 2;
	
	public $uninterceptable = true; //Lightning Cannon is uninterceptable
	public $intercept = 4; //intercept rating -4

	public $repairPriority = 0; // As a mirrored system, this should never be repaired
	
		//Should never be targeted or counted for CV.	
		protected $doCountForCombatValue = false;
		public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
		public $isTargetable = false; //cannot be targeted ever!	

		private $pairing = null;	//Which lightning gun is it paired with?	
		private $mirror= null;
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "1Prong",
		2 => "2Prongs",
		3 => "3Prongs",
		4 => "4Prongs",
		5 => "P3Piercing",
		6 => "Q4Piercing"
	);
	
	public $priority = 5; //medium Standard weapon - for single fire...
	public $priorityArray = array(1=>5, 2=>8, 3=>7, 4=>7, 5=>2, 6=>2); //single fire is Medium Standard, double Light Raking, 3/4 Heavy Raking, 5/6 Piercing)
    public $rangePenalty = 1; 
	public $rangePenaltyArray = array(1=>1, 2=>0.5, 3=>0.33, 4=>0.33, 5=>0.33, 6=>0.33);
	public $fireControl = array(7, 4, 4); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(7, 4, 4), 2=>array(3,4,4), 3=>array(0,4,4), 4=>array(null,4,4), 5=>array(null,0,0), 6=>array(null,0,0) ); // fighters, <mediums, <capitals ; Piercing shots incorporate Piercing shot penalty into FC
	
	//number of prongs/power required to fire - PER PRONG!
	public $powerRequiredArray = array( 1=>array(1,1), 2=>array(2,1.5), 3=>array(3,3), 4=>array(4,4.5), 5=>array(3,3), 6=>array(4,4.5) );

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array( 1=>"Standard", 2=>"Raking", 3=>"Raking", 4=>"Raking", 5=>"Piercing", 6=>"Piercing" );
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	//rake size array
	public $raking = 10;//more in higher modes
	public $rakingArray = array( 1=>10, 2=>10, 3=>15, 4=>20, 5=>15, 6=>20 );
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $orientation, $pairing ) //$orientation is 'L'eft or 'R'ight - regarding graphics
	{
		$this->pairing = $pairing;
		$this->displayName = 'Mirror Lightning Gun ' . $pairing . '';
		//Nominal amount of health, should never be hit.
		if ( $maxhealth == 0 ){
			$maxhealth = 1;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
		}
		$this->iconPath = "VorlonLightningGunAlt".$orientation.".png";
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		$this->addTag('Mirror Lightning Gun'); //needed to properly allocate hits on Vorlon ships, where most of these weapons are used
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    		
		$this->data["Special"] .= "The mirror gun represents the Lightning Gun's ability to fire twice per turn.";
		$this->data["Special"] .= "<br>May combine with other mirror guns or regular Lightning Guns for heavier shots.";
		$this->data["Special"] .= "<br>Uninterceptable. Capable of multiple modes of fire. Higher modes require combining multiple prongs on the same target.";   
		$this->data["Special"] .= "<br>Firing modes available (Number of prongs/power used per SHOT/damage output (and mode)/range penalty):";  
		$this->data["Special"] .= "<br> - 1 Prong: 1 Power, 1d5+8 Standard, -5/hex"; 
		$this->data["Special"] .= "<br> - 2 Prongs: 3 Power, 1d10+16 Raking(10), -2.5/hex";
		$this->data["Special"] .= "<br> - 3 Prongs: 9 Power, 2d10+32 Raking(15), -1.65/hex"; 
		$this->data["Special"] .= "<br> - 4 Prongs: 18 Power, 4d10+64 Raking(20), -1.65/hex"; 
		$this->data["Special"] .= "<br> - 3 Prongs Piercing: 9 Power, 2d10+32 Piercing, -1.65/hex"; 
		$this->data["Special"] .= "<br> - 4 Prongs Piercing: 18 Power, 4d10+64 Piercing, -1.65/hex"; 
		$this->data["Special"] .= "<br>If weapon is mis-declared (shot is declared but not enough prongs are allocated in appropriate mode) shot will automatically miss and Power will NOT be drained."; 
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";		
	}

	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(5, 1)+8; 
				break;
			case 2:
				return Dice::d(10, 1)+16; 
				break;
			case 3:	
				return Dice::d(10, 2)+32; 
				break;
			case 4:
				return Dice::d(10, 4)+64; 
				break;
			case 5:
				return Dice::d(10, 2)+32; 
				break;
			case 6:
				return Dice::d(10, 4)+64; 
				break;
			default: //should never go here
				return Dice::d(5, 1)+8;
				break;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; 
				break;
			case 2:
				$this->minDamage = 17; 
				break;
			case 3:
				$this->minDamage = 34; 
				break;
			case 4:
				$this->minDamage = 68; 
				break;
			case 5:
				$this->minDamage = 34; 
				break;
			case 6:
				$this->minDamage = 68; 
				break;
			default: //should never go here
				$this->minDamage = 9;
				break;
		}
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 13; 
				break;
			case 2:
				$this->maxDamage = 26; 
				break;
			case 3:
				$this->maxDamage = 52; 
				break;
			case 4:
				$this->maxDamage = 104; 
				break;
			case 5:
				$this->maxDamage = 52; 
				break;
			case 6:
				$this->maxDamage = 104; 
				break;
			default: //should never go here
				$this->maxDamage = 13;
				break;
		}
	}
	
	//hit chance calculation is standard - but at this stage power used information is sent to Capacitor, too
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->changeFiringMode($fireOrder->firingMode);
		$doDrain = true;
		$doCalculate = true;
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->doNotIntercept = true;
			return;
		}
		
		$powerRequired = $this->powerRequiredArray[$fireOrder->firingMode];				
		$powerPerProng = $powerRequired[1];
		$prongsNeeded = $powerRequired[0] ; 
		if ($prongsNeeded < 2){ //nothing extra is needed, do fire!
			$doDrain = true;
			$doCalculate = true;
		} else {//additional prongs needed!
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Lightning Cannon?...
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if (($subWeapon instanceof VorlonLightningGun) || ($subWeapon instanceof VorlonLightningGun2)){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($prongsNeeded-1)) break;//enough subordinate weapons found! - exit loop
			}						
			if ($subordinateOrdersNo == ($prongsNeeded-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}				
				$doDrain = true;
				$doCalculate = true;
			}else{//not enough weapons to combine in this mode - mark combined and effectively don't fire
				$notes = "technical fire order - weapon mis-declared";
				$fireOrder->chosenLocation = 0; //tylko techniczne i tak
				$fireOrder->needed = 0;
				$fireOrder->shots = 0;
				$fireOrder->notes = $notes;
				$fireOrder->updated = true;
				$this->doNotIntercept = true;
				$doDrain = false;
				$doCalculate = false;
			}
		}
		
		if($doDrain){
			$capacitor = $this->unit->getSystemByName("PowerCapacitor");
			if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
				$powerNeeded = $powerPerProng*$prongsNeeded;//drain for ALL combined prongs!
				$capacitor->doDrawPower($powerNeeded);
			}
		}
		if($doCalculate){
			parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation
		}
	}//endof function calculateHitBase


	/* drain power when firing defensively
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$capacitor->doDrawPower(1);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	/*can intercept anything only if Capacitor holds enough Power...*/
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$powerIsAvailable = false;
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			if($capacitor->canDrawPower(1)) $powerIsAvailable = true;
		}
		return $powerIsAvailable;
	}

}//endof class VorlonLightningGun2

























/*Vorlon fighter weapon*/
    class VorlonLtDischargeGun extends Weapon{
        public $name = "VorlonLtDischargeGun";
        public $displayName = "Light Discharge Gun";
	    public $iconPath = "VorlonLtDischargeGun.png";
	    
        public $animation = "bolt";
		public $animationColor = array(175, 255, 225);
	    /*
		public $trailColor = array(175, 225, 175);
        public $animationExplosionScale = 0.15;
        public $projectilespeed = 10;
        public $animationWidth = 2;
        public $trailLength = 10;
	*/
        public $intercept = 1; //not very good ant intercepting things... I am going with default as nothing is marged on control card (except default allows merging multiple shots for interception purposes, too)
        public $loadingtime = 1;
        public $shots = 1;
	    public $guns = 2;
        public $defaultShots = 1;
        public $rangePenalty = 2; // -2/hex... for single fire
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
	    public $priority = 5;
	    public $priorityArray = array(1=>5, 2=>6); //alternate mode is stronger
        
        public $damageType = "Standard"; 
        public $weaponClass = "Electromagnetic"; 
		
		public $firingModes = array(1=>'Single', 2=>'Dual');
		public $damageTypeArray = array(1=>'Standard', 2=>'Standard'); 
		public $gunsArray = array(1=>2, 2=>1);
        public $rangePenaltyArray = array(1=>2, 2=>1.5); //-2/hex and -3/2 hexes
		
		public $factionAge = 3; //Ancient
	    
        
        function __construct($startArc, $endArc,$dual = false){
			$this->isLinked = false; //shots are separate, not linked! 
			if($dual){ //dual weapon is extending base weapon by adding third firing mode - combining ALL FOUR shots into one massive blast!
				$this->firingModes[3] = 'Quad';
				$this->damageTypeArray[3] = 'Standard'; 
				$this->gunsArray = array(1=>4, 2=>2, 3=>1); //lower modes get double allowance
				$this->rangePenaltyArray[3] = 1.5; // -3/2 hexes
				$this->iconPath = "VorlonLtDischargeGun2.png"; //alternate graphics showing off more powerful mount
			}
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
	
	
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "This weapon is capable of combining basic shots into smaller number of more powerful ones:";
            $this->data["Special"] .= "<br> Single shot: d6+6 damage, -10/hex";
            $this->data["Special"] .= "<br> Dual shot: 2d6+9 damage, -7.5/hex";
			if(isset ($this->firingModes[3])){
				$this->data["Special"] .= "<br> Quad shot: 4d6+9 damage, -7.5/hex";
			}
        }
	
	    
        public function getDamage($fireOrder){
			switch($this->firingMode){
				case 1:
					return Dice::d(6, 1)+6; 
					break;
				case 2:
					return Dice::d(6, 2)+9; 
					break;
				case 3:
					return Dice::d(6, 4)+9; 
					break;
			}
		}
        public function setMinDamage(){ 
			switch($this->firingMode){
				case 1:
					$this->minDamage = 7; 
					break;
				case 2:
					$this->minDamage = 11; 
					break;
				case 3:
					$this->minDamage = 13; 
					break;
			}
			$this->minDamageArray[$this->firingMode] = $this->minDamage;
		}
        public function setMaxDamage(){
			switch($this->firingMode){
				case 1:
					$this->maxDamage = 12; 
					break;
				case 2:
					$this->maxDamage = 21; 
					break;	
				case 3:
					$this->maxDamage = 33; 
					break;	
			}
			$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
		}
		
    } //endof class VorlonLtDischargeGun




/* Vorlon not-quite-secondary weapon */
class VorlonDischargeCannon extends Weapon{
	public $name = "VorlonDischargeCannon";
	public $displayName = "Discharge Cannon";
	public $iconPath = "VorlonDischargeCannon.png";
	
	public $animation = "bolt";
	public $animationColor = array(175, 255, 225);
	/*
	public $trailColor = array(175, 225, 175);
	public $projectilespeed = 15;
	public $animationWidth = 5;
	public $animationExplosionScale = 0.45;
	public $trailLength = 30;
	*/
	
	public $loadingtime = 1;
	public $normalload = 2;
		
	//public $canChangeShots = true;
	//public $shots = 4;
	//public $defaultShots = 4; //can fire up to 4 shots (if power is available); LET'S DECLARE ALL 4 BY DEFAULT - chance of player wanting full power is higher than conserving energy (if he has energy shortages he'll be stopped by EoT check anyway)
	//public $maxVariableShots = 4; //For front end to know how many shots weapon CAN fire where this can be changed after locking in.	
	public $intercept = 2; //intercept rating -2
	
	public $priority = 8; //light Raking weapon in lightest mode - heavier modes are definitely heavy Raking
	public $priorityArray = array(1=>8, 2=>7, 3=>7);
	
	public $firingMode = 1;	
	public $firingModes = array(
		1 => "1xPower",
		2 => "2xPower",
		3 => "3xPower"
	);
	
    public $rangePenalty = 0.5; //-1/2 hexes
	public $fireControl = array(4, 3, 2); // fighters, <=mediums, <=capitals 
	public $fireControlArray = array( 1=>array(4, 3, 2), 2=>array(3, 3, 3), 3=>array(2, 3, 5) ); 
	public $rakingArray = array(1=>10, 2=>15, 3=>15);

	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	//public $multiplied = false; //technical variable
	public $canSplitShots = true; //New method, let's just have shots treated as separate shots! - DK
	public $guns = 4;
	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 15;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
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
		$this->data["Special"] .= "<br>Firing mode affects damage output (and power used), and also fire control (e.g. higher modes are better against ships):";  
		$this->data["Special"] .= "<br> - 5 power: 4d10+5, Raking(10)"; 
		$this->data["Special"] .= "<br> - 10 power: 6d10+10, Raking(15)"; 
		$this->data["Special"] .= "<br> - 15 power: 8d10+15, Raking(15)"; 
		$this->data["Special"] .= "<br>Fires up to 4 times (costing power per shot), at same or different targets.";
		$this->data["Special"] .= "<br>You must explicitly order this weapon to intercept.";	
		$this->data["Special"] .= "<br>Interceping shots consume 5 power per shot (refunded if not used).";   	
	}
		
		
		

	public function getDamage($fireOrder){
		switch($this->firingMode){
			case 1:
				return Dice::d(10, 4)+5; //5 Power
				break;
			case 2:
				return Dice::d(10, 6)+10; //10 Power
				break;
			case 3:
				return Dice::d(10, 8)+15; //15 Power
				break;
			default: //should never go here
				return Dice::d(10, 4)+5;
				break;
		}
	}
        
	public function setMinDamage(){
		switch($this->firingMode){
			case 1:
				$this->minDamage = 9; //5 Power
				break;
			case 2:
				$this->minDamage = 16; //10 Power
				break;
			case 3:
				$this->minDamage = 23; //15 Power
				break;
			default: //should never go here
				$this->minDamage = 9;
				break;
		}
	}
             
	public function setMaxDamage(){
		switch($this->firingMode){
			case 1:
				$this->maxDamage = 45; //5 Power
				break;
			case 2:
				$this->maxDamage = 70; //10 Power
				break;
			case 3:
				$this->maxDamage = 95; //15 Power
				break;
			default: //should never go here
				$this->maxDamage = 45;
				break;
		}
	}
	
	
	//hit chance calculation is standard - but at this stage power used information is sent to Capacitor, too
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			//$powerNeeded = 5*$fireOrder->firingMode*$fireOrder->shots;
			$powerNeeded = 5*$fireOrder->firingMode;
			$capacitor->doDrawPower($powerNeeded);
		}
		parent::calculateHitBase($gamedata, $fireOrder); //standard hit chance calculation
	}//endof function calculateHitBase

	/* drain power when firing defensively
	*/
	public function fireDefensively($gamedata, $interceptedWeapon)
	{
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			$capacitor->doDrawPower(5);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}
	
	/*can intercept anything only if Capacitor holds enough Power...*/
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$powerIsAvailable = false;
		$capacitor = $this->unit->getSystemByName("PowerCapacitor");
		if($capacitor){ //else something is wrong - weapon is put on a ship without Power Capacitor!
			if($capacitor->canDrawPower(5)) $powerIsAvailable = true;
		}
		return $powerIsAvailable;
	}

	/*
	//if fired offensively - make as many attacks as firing order declares shots (and resent number of shots declared to 1 :) )
	//if defensively - make weapon have 4 GUNS (would be temporary, but enough to assign multiple shots for interception)
	public function beforeFiringOrderResolution($gamedata){
		if($this->multiplied==true) return;//shots of this weapon are already multiplied
		$this->multiplied = true;//shots WILL be multiplied in a moment, mark this
		//is offensive fire declared?...
		$offensiveShot = null;

		foreach($this->fireOrders as $fire){
			if(($fire->type =='normal') && ($fire->turn == $gamedata->turn)) $offensiveShot = $fire;
		}
		if($offensiveShot!==null){ //offensive fire declared, multiply!
			$shotsDeclared = $fire->shots;
			$fire->shots = 1;
			while($shotsDeclared > 1){ //first attack is already declared!
				$multipliedFireOrder = new FireOrder( -1, $offensiveShot->type, $offensiveShot->shooterid, $offensiveShot->targetid,
					$offensiveShot->weaponid, $offensiveShot->calledid, $offensiveShot->turn, $offensiveShot->firingMode,
					0, 0, 1, 0, 0, null, null
				);
				$multipliedFireOrder->addToDB = true;
				$this->fireOrders[] = $multipliedFireOrder;
				$shotsDeclared--;	      
			}
		}else{//offensive fire NOT declared, multiply guns for interception!
			$this->guns = 4; //up to 4 intercept shots (if Power is available and weapon is declared eligible)
		}
	} //endof function beforeFiringOrderResolution
	*/ 

}//endof class VorlonDischargeCannon




class PsychicField extends Weapon{ //Thirdspace weapons that operates similar to Spark Field.
    public $name = "PsychicField";
    public $displayName = "Psychic Field";
	public $iconPath = "PsychicField.png";
	
	//let's make animation more or less invisible, and effect very large
    public $animation = "ball";
    public $animationColor = array(128, 0, 0);
    public $animationExplosionScale = 5; //Default
	public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.      
	
	public $boostable = true;
    public $boostEfficiency = 0;
    public $maxBoostLevel = 3;
	
	public $output = 0;
      
    public $priority = 2; //should attack very early
	
    public $loadingtime = 1;
	public $autoFireOnly = true; //this weapon cannot be fired by player
	public $doNotIntercept = true; //this weapon is a field, "attacks" are just for technical reason

	public $range = 4;        
    public $rangePenalty = 0; //no range penalty, but range itself is limited
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals ; not relevant really!
	
	public $boostlevel = 0;
    public $canOffLine = true;	
	public $repairPriority = 3;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired	
		
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Psychic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
    public $firingModes = array( 1 => "Psychic Field"); //just a convenient name for firing mode
	public $hextarget = true;
	
    protected $ewBoosted = true;	
	
	protected $targetList = array(); //weapon will hit units on this list rather than target from firing order; filled by PsychicField handler!
	private $alreadyEngaged = array();
	
	
 	protected $possibleCriticals = array(
            16=>"ForcedOfflineOneTurn"
	);
	
	
	public function addTarget($newTarget){
		$this->targetList[] = $newTarget;
	}

	    public function setSystemDataWindow($turn){
		    $boostlevel = $this->getBoostLevel($turn);
		    $this->minDamage = 1+$boostlevel;
		    $this->maxDamage = 1+$boostlevel;
		    $this->minDamage = max(1,$this->minDamage);
		    $this->animationExplosionScale = $this->getAoE($turn);
		    $this->range = $this->range + $boostlevel;
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Automatically affects all enemy units in Range."; 
		      $this->data["Special"] .= "<br>Reduces Fighters' Initiative (by 5-15), and Hit Chances (5-10%) next turn.";  
		      $this->data["Special"] .= "<br>Reduces Ships' Hit Chances (5-10%) next turn if hits Structure, and critical rolls on other systems.";  		      
		      $this->data["Special"] .= "<br>Can be boosted 3 times using EW, adding +1 Range, +1 Damage and +5 to Initiative / Hit Chance penalties per boost."; 
		      $this->data["Special"] .= "<br>Multiple overlapping Psychic Fields will only cause the strongest to effect a particular target.";
		      $this->data["Special"] .= "<br>Does not affect friendly units.";
		      $this->data["Special"] .= "<br>Only 50% effective against Advanced Armor."; 		        		       
	    }	//endof function setSystemDataWindow
	
	
	
	public function getAoE($turn){
		$boostlevel = $this->getBoostLevel($turn);
		$aoe = $this->range + $boostlevel;
		return $aoe;
	}
	
	
	public function calculateHitBase($gamedata, $fireOrder){
	    $fireOrder->updated = true;
		$fireOrder->chosenLocation = 0;//so it's recalculated later every time! - as location chosen here is completely incorrect for target 
		$fireOrder->needed = 100; //hit is automatic
		$range = $this->getAoE($gamedata->turn);
		$fireOrder->pubnotes = "<br>Psychic Field effects all units within " . $range . " hexes.";		
	}
	
	public function fire($gamedata, $fireOrder){
		//actually fire at units from target list - and fill fire order data appropriately
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$fireOrder->rolled = 1; //just to mark that there was a roll!
		$fireOrder->shotshit = 1; //always hit, technically
		
		//actual damage dealing...
		foreach($this->targetList as $target){
			$this->beforeDamage($target, $shooter, $fireOrder, null, $gamedata);			
		}
        	$notes = "This weapon hits automatically"; //replace usual note
		$fireOrder->notes = $notes;
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	}

	protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
		
		if (!($target instanceof FighterFlight)){ //ship - as usual
			$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
			if ($target->faction == "Thirdspace") $damage = 0; //No effect on other Thirdspace ships.			
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
		}else{//fighter flight - separate hit on each fighter!
			foreach ($target->systems as $fighter){
				if ($fighter == null || $fighter->isDestroyed()){
				    continue;
				}
				$damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
				if ($target->team == $shooter->team) $damage = 0; //No effect on other Thirdspace ships.					
				$this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, null, $gamedata, false);
                }
		}
	}	


	public function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$dmgToReturn = $damage;
		if ($system instanceof Structure) $dmgToReturn = 0; //will not harm Structure!
		return $dmgToReturn;
	}
	
	public function calculateBoostLevel($turn){
		$this->boostlevel = $this->getBoostLevel($turn);
	}
	
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;
                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
			}

            return $boostLevel;     	
	}
	
	//find units in range (other than self), create attacks vs them
	public function beforeFiringOrderResolution($gamedata){
		$ship = $this->getUnit();
		$deployTurn = $ship->getTurnDeployed($gamedata);		
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

		PsychicFieldHandler::createFiringOrders($gamedata);		
	}

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!	
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		if ($ship->team == $shooter->team) return; //No effect on own team.
		if (isset($this->alreadyEngaged[$ship->id])) return; // Ignore flights that have already been had crits applied.
			
		$boostlevel = $this->getBoostLevel($gamedata->turn);
		
		$effectIni = Dice::d(3,1)+$boostlevel;//strength of effect: -5 to -15 base, up to -30 with boost. initiative.
		$effecttohit = Dice::d(2,1)+$boostlevel;//strength of effect: -5 to -10 base, up to -25 with boost.
		$effectCrit = $effectIni +2;
				
		//$fireOrder->pubnotes .= "<br> All enemies units have Initiative reduced and suffer a Hit Penalty next turn. Ships may also suffer a potential Critical.";
						
		if ($system->advancedArmor){		
			$effectIni = ceil($effectIni/2);  	//Other Ancients are somewhat resistant to pyschic attack from Thirdspace Aliens, 50% effect.	
			$effecttohit = ceil($effecttohit/2);
			$effectCrit = ceil($effectCrit/2);		
			}

		if ($ship instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!			
			$firstFighter = $ship->getSampleFighter();
			if($firstFighter){
				for($i=1; $i<=$effecttohit;$i++){
					$crit = new tmpsensordown(-1, $ship->id, $firstFighter->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				} 
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $firstFighter->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$firstFighter->criticals[] =  $crit;
				}
		    $this->alreadyEngaged[$ship->id] = true; // Mark engaged				
			}
		}else if ($system instanceof Structure){ //Give penalty to hit next turn if it hits structure.
			$CnC = $ship->getSystemByName("CnC");
				for($i=1; $i<=$effecttohit;$i++){
					$crit = new PenaltyToHitOneTurn(-1, $ship->id, $CnC->id, 'PenaltyToHitOneTurn', $gamedata->turn); 
					$crit->updated = true;
			        $CnC->criticals[] =  $crit;
				}
				for($i=1; $i<=$effectIni;$i++){
					$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        $CnC->criticals[] =  $crit;
					}    
		}else { //Force critical roll if it hits something other than structure.
			$CnC = $ship->getSystemByName("CnC");			
				for($i=1; $i<=$effecttohit;$i++){
					$crit = new PenaltyToHitOneTurn(-1, $ship->id, $CnC->id, 'PenaltyToHitOneTurn', $gamedata->turn); 
					$crit->updated = true;
			        $CnC->criticals[] =  $crit;
				}
				for($i=1; $i<=$effectIni;$i++){
						$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
						$crit->updated = true;
				        $CnC->criticals[] =  $crit;			
				}
				$system->forceCriticalRoll = true;
				$system->critRollMod += $effectCrit;	//Add 3-8 modifier depending on $effectIni roll and boost (halved for Ancients). 		
		}			
	} //endof function onDamagedSystem	
		
		
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		if ( $maxhealth == 0 ){
			$maxhealth = 20;
		}
		if ( $powerReq == 0 ){
			$powerReq = 0;
		}
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		PsychicFieldHandler::addPsychicField($this);//so all Psychic Fields are accessible together, and firing orders can be uniformly created
	}
	

	public function onConstructed($ship, $turn, $phase){
		parent::onConstructed($ship, $turn, $phase);
	}

	public function getDamage($fireOrder){        
		$fieldDamage = 1;
		$boostlevel = $this->getBoostLevel($fireOrder->turn);
		$fieldDamage += $boostlevel; //-1 per level of boost	
		return $fieldDamage;   
	}
	
	public function setMinDamage(){   $this->minDamage =  0 ;      }
	public function setMaxDamage(){   $this->maxDamage =  0 ;      }

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->ewBoosted = $this->ewBoosted;
		$strippedSystem->noProjectile = $this->noProjectile;
		$strippedSystem->range = $this->range;																	
		return $strippedSystem;
	} 

	
} //endof class PsychicField 


/*handles creation of firing orders for Psychic Fields*/
class PsychicFieldHandler{
	public $name = "PsychicFieldHandler";
	private static $psychicFields = array();
	private static $firingDeclared = false;
	
	
	//should be called by every SparkField on creation!
	public static function addPsychicField($weapon){
		PsychicFieldHandler::$psychicFields[] = $weapon;		
	}
	
	//compares boost levels of fields
	//	highest boost first
	//	owner irrelevant, as weapon will damage everything in range except firing unit itself
	public static function sortByBoost($fieldA, $fieldB){
	    if ($fieldA->boostlevel > $fieldB->boostlevel) { // High boost level first
	        return -1;
	    } else if ($fieldA->boostlevel < $fieldB->boostlevel) {
	        return 1;
	    } else {
	        return 0;
	    }
	} //endof function sortByBoost
	
	
	public static function createFiringOrders($gamedata){
		if (PsychicFieldHandler::$firingDeclared) return; //already done!
		PsychicFieldHandler::$firingDeclared = true;
		
		//apparently ships may be loaded multiple times... make sure fields in array belong to current gamedata!
		$tmpFields = array();
		foreach(PsychicFieldHandler::$psychicFields as $field){
			$shooter = $field->getUnit();
			//if($field->isDestroyed($gamedata->turn-1)) continue; //destroyed weapons can be safely left out
			if($field->isDestroyed($gamedata->turn)) continue; //actually at this stage - CURRENT turn should be indicated!
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($shooter);
			if ($belongs){
				$tmpFields[] = $field;
			}			
		}
		PsychicFieldHandler::$psychicFields = $tmpFields; 
		
		
		//make sure boost level for all weapons is calculated
		foreach(PsychicFieldHandler::$psychicFields as $field){
			$field->calculateBoostLevel($gamedata->turn);
		}
		
		//sort all fields by boost
		//usort(PsychicFieldHandler::$psychicFields, "self::sortByBoost");
		usort(self::$psychicFields, [self::class, 'sortByBoost']);
	
		//table of units that are already targeted
		$alreadyTargeted = array();
		//create firing order for each weapon (target self)
		//for each weapon find possible targets and add them to weapons' target list
		//strongest weapons fire first, and only 1 field affects particular ship	
		foreach(PsychicFieldHandler::$psychicFields as $field){			
			if ($field->isDestroyed($gamedata->turn-1)) continue; //destroyed field does not attack
			if ($field->isOfflineOnTurn($gamedata->turn)) continue; //disabled field does not attack
			$shooter = $field->getUnit();  
			$deployTurn = $shooter->getTurnDeployed($gamedata);		
			if($deployTurn > $gamedata->turn) continue;  //Ship not deployed yet, don't fire weapon!			
			
			$targetPos = $shooter->getCoPos();
			$movementThisTurn = $shooter->getLastTurnMovement($gamedata->turn+1);
			$fire = new FireOrder(-1, 'normal', $shooter->id, -1, $field->id, -1, $gamedata->turn, 
				1, 0, 0, 1, 0, 0, $movementThisTurn->position->q,  $movementThisTurn->position->r, $field->weaponClass
			);
			$fire->addToDB = true;
			$field->fireOrders[] = $fire;			
			$aoe = $field->getAoE($gamedata->turn);			
			$inAoE = $gamedata->getShipsInDistance($shooter, $aoe);
			foreach($inAoE as $targetID=>$target){		
				if ($shooter->id == $target->id) continue;//does not threaten self!
				if ($target->isDestroyed()) continue; //no point allocating	
				if ($target->team == $shooter->team) continue; //No effect on units in same team.
				if ($target->isTerrain()) continue;
				if ($target->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore targets that are not deployed yet!									
				if (in_array($target->id,$alreadyTargeted,true)) continue;//each target only once 
				//add to target list
				$alreadyTargeted[] = $target->id; //add to list of already targeted units
				$field->addTarget($target);
			}
		} //endof foreach PsychicField
	}//endof function createFiringOrders
	
}//endof class PsychicFieldHandler


   class HeavyPsionicLance extends Raking{
        public $name = "HeavyPsionicLance";
        public $displayName = "Heavy Psionic Lance";
        public $iconPath = "HeavyPsionicLance.png";        
        public $animation = "laser";
        public $animationColor = array(128, 0, 0);

        public $intercept = 0;
        public $loadingtime = 3;
        public $raking = 20;
        public $addedDice;
        public $priority = 7;

        public $boostable = true;
        public $boostEfficiency = 0;
        public $maxBoostLevel = 3;

        public $firingModes = array(
            1 => "Raking"
        );

        public $rangePenalty = 0.25;
        public $fireControl = array(null, 2, 5); // fighters, <mediums, <capitals
        //private $damagebonus = 10;

        public $damageType = "Raking"; 
        public $weaponClass = "Psychic"; 
        
        public $uninterceptable = true;
        
    	protected $ewBoosted = true;             
        
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired            


        public function setSystemDataWindow($turn){
            $boost = $this->getExtraDicebyBoostlevel($turn);            
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
            //Raking(20) is already described in Raking class           
            $this->data["Special"] .= "Uninterceptable.";  
            $this->data["Special"] .= '<br>Can be boosted with EW for an extra +2d10 +8 damage per point of EW used, up to three times.';
		    $this->data["Special"] .= "<br>This EW does not count towards your OEW lock on a target.";	            
		    $this->data["Special"] .= "<br>Has +1 modifier to critical hit rolls, and +2 to fighter dropout rolls.";               
            $this->data["Boostlevel"] = $boost;
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 16;
            }
            if ( $powerReq == 0 ){
                $powerReq = 10;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        private function getExtraDicebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 2;
                    break;
                case 2:
                    $add = 4;
                    break;
                case 3:
                    $add = 6;
                    break;

                default:
                    break;
            }
            return $add;
        }


         private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }
 
		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //Unlikely to matter at Raking 20, but keep it in for thematic reasons!
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);		
			if ($system->advancedArmor) return; //no effect on Advanced Armor but Ipsha etc still get affected.
			//+1 to crit roll, +2 to dropout roll
			$mod = 1;
			if ($ship instanceof FighterFlight) $mod++;		
			$system->critRollMod += $mod; 
		} //endof function onDamagedSystem	               
                        
        public function getDamage($fireOrder){
            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10, (6 + $add)) + ($add *4) + 60;
            return $dmg;
        }

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }

        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 66 + ($boost * 10);
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 120 + ($boost * 28);
        }  
        
	public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->ewBoosted = $this->ewBoosted;													
			return $strippedSystem;
		}     
        
   } //end of class HeavyPsionicLance
   

class PsionicLance extends Raking{
        public $name = "PsionicLance";
        public $displayName = "Psionic Lance";
        public $iconPath = "PsionicLance.png";         
        public $animation = "laser";
        public $animationColor = array(128, 0, 0);

        public $intercept = 0;
        public $loadingtime = 2;
        public $raking = 15;
        public $addedDice;
        public $priority = 8;

        public $boostable = true;
        public $boostEfficiency = 0;
        public $maxBoostLevel = 2;

        public $firingModes = array(
            1 => "Raking"
        );

        public $rangePenalty = 0.33;
        public $fireControl = array(-4, 4, 5); // fighters, <mediums, <capitals

        public $damageType = "Raking"; 
        public $weaponClass = "Psychic";
        
        public $uninterceptable = true;
        
    	protected $ewBoosted = true;          
       
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired                


        public function setSystemDataWindow($turn){
            $boost = $this->getExtraDicebyBoostlevel($turn);            
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            }else{
                $this->data["Special"] .= '<br>';
            } 
            //Raking(15) is already described in Raking class
            $this->data["Special"] .= "Uninterceptable.";              
            $this->data["Special"] .= '<br>Can be boosted with EW for an extra +2d10 damage per point of EW used, up to twice.';
		    $this->data["Special"] .= "<br>This EW does not count towards your OEW lock on a target.";		    
		    $this->data["Special"] .= "<br>Has +1 modifier to critical hit rolls, and +2 to fighter dropout rolls.";  	                
            $this->data["Boostlevel"] = $boost;
        }

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        private function getExtraDicebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 2;
                    break;
                case 2:
                    $add = 4;
                    break;

                default:
                    break;
            }
            return $add;
        }


         private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }

		protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
				parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);		
				if ($system->advancedArmor) return; //no effect on Advanced Armor		
				//+1 to crit roll, +2 to dropout roll
				$mod = 1;
				if ($ship instanceof FighterFlight) $mod++;		
				$system->critRollMod += $mod; 
			} //endof function onDamagedSystem	 
                       
        public function getDamage($fireOrder){
            $add = $this->getExtraDicebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10, (3 + $add)) +35;
            return $dmg;
        }

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }

        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 38 + ($boost * 2);
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 65 + ($boost * 20);
        } 
        
		public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->ewBoosted = $this->ewBoosted;													
			return $strippedSystem;
		}            
        
         
   }//end of Psionic Lance


class PsionicConcentrator extends Weapon{
	public $name = "PsionicConcentrator";
	public $displayName = "Psionic Concentrator";
	public $iconPath = "PsionicConcentrator.png";
	
	public $animation = "bolt";
    public $animationColor = array(128, 0, 0);

    public $loadingtime = 1;
	public $intercept = 2; //intercept rating -1     

    public $guns = 4;
    public $gunsArray = array(1=>4, 2=>2, 3=>1, 4=>4, 5=>2);	

    public $priority = 4;
    public $priorityArray = array(1=>4, 2=>5, 3=>7, 4=>4, 5=>5);

	public $firingMode = 1;	
            public $firingModes = array(
                1 => "Quad",
                2 => "Double",
                3 => "Single",
                4 => "4Split",
                5 => "2Split",				                
            );

    public $fireControl = array(6, 2, 2); // fighters, <mediums, <capitals 
    public $fireControlArray = array( 1=>array(6, 2, 2), 2=>array(1, 4, 5), 3=>array(null, 3, 7), 4=>array(6, 2, 2), 5=>array(1, 4, 5));

    public $rangePenalty = 0.5;
    public $rangePenaltyArray = array( 1=>0.5, 2=>1, 3=>2, 4=>0.5, 5=>1,);
            
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!   
	public $weaponClass = "Psychic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!    
	
	public $isCombined = false; //is being combined with other weapon
	public $alreadyConsidered = false; //already considered - either being fired or combined
	public $testRun = false;//testRun = true means hit chance is calculated nominal skipping concentration issues - for subordinate weapon to calculate average hit chance
	
	public $repairPriority = 4;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	public $canSplitShots = true; //Allows Firing Mode 1 to split shots.
	public $canSplitShotsArray = array(1=>false, 2=>false, 1=>false, 4=>true, 5=>true); 	

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 12;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }	
	
	public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Fires 4 shots by default, but can combine these into 1 or 2 powerful, shorter-ranged shot(s)";			      		      		      
		      $this->data["Special"] .= "<br>Any hits drain -1 Power from Younger Race ships for one turn.";
		      $this->data["Special"] .= "<br>Has +1 modifier to critical hit rolls, and +2 to fighter dropout rolls.";
			  $this->data["Special"] .= "<br>Can use '4Split' and '2Split' Firing Modes to target different enemy units.";		  
	    }	


    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata)
    {
		parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
 		//-1 power to ships for one turn.  
		if ($target->advancedArmor) return; //no effect on Advanced Armor
			 		     
		$reactor = $target->getSystemByName("Reactor");
		$mod = 1;//Easier to change later.
			if($reactor){
				for($i=1; $i<=$mod;$i++){
					$crit = new OutputReduced1(-1, $target->id, $reactor->id, 'OutputReduced1', $gamedata->turn+1, $gamedata->turn+1); 
					$crit->updated = true;
			        $reactor->criticals[] =  $crit;
				}    		
            }          
    }//endof beforeDamage

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);		
		if ($system->advancedArmor) return; //no effect on Advanced Armor		
		//+1 to crit roll, +2 to dropout roll, 
		$mod = 1;

		if ($ship instanceof FighterFlight) {
            $mod += 1;    		 
        }
        
        $system->critRollMod += $mod; 
	} //endof function onDamagedSystem	

	
    public function getDamage($fireOrder){
		switch($this->firingMode){
			
			case 1:
				return Dice::d(6, 2)+6;								
							
			case 2:
				return Dice::d(6, 3)+15;								
			
			case 3:
				return Dice::d(6, 5)+24;								
		}

	}
	
	public function setMinDamage(){    
		switch($this->firingMode){
			
			case 1:
				$this->minDamage = 8;		
			break;
							
			case 2:
				$this->minDamage = 18;		
			break;
			
			case 3:
				$this->minDamage = 29;				
			break;
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage; 		
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			
			case 1:
				$this->maxDamage = 18;		
			break;
							
			case 2:
				$this->maxDamage = 33;		
			break;
			
			case 3:
				$this->maxDamage = 54;				
			break;
		}

		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;  
	}
} //endof class PsionicConcentrator


//Lighter version of Psionic Concentrator used in ships with which can't field a full 4 shot system e.g. Attack Craft variants.
class PsionicConcentratorLight extends Weapon{
	public $name = "PsionicConcentratorLight";
	public $displayName = "Light Psionic Concentrator";
	public $iconPath = "PsionicConcentratorLight.png";
	
	public $animation = "bolt";
    public $animationColor = array(128, 0, 0);

    public $loadingtime = 1;
	public $intercept = 2; //intercept rating -1     
	

    public $priority = 4;
    public $priorityArray = array(1=>4, 2=>5);

	public $firingMode = 1;	
            public $firingModes = array(
                1 => "Single",
                2 => "Double"              
            );

    public $fireControl = array(7, 3, 2); // fighters, <mediums, <capitals 
    public $fireControlArray = array( 1=>array(6, 2, 2), 2=>array(1, 4, 5));	

    public $rangePenalty = 1;
    public $rangePenaltyArray = array( 1=>0.5, 2=>1);
            
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!   
	public $weaponClass = "Psychic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!    
	
	public $isCombined = false; //is being combined with other weapon
	public $alreadyConsidered = false; //already considered - either being fired or combined
	public $testRun = false;//testRun = true means hit chance is calculated nominal skipping concentration issues - for subordinate weapon to calculate average hit chance
	
	public $repairPriority = 4;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 1;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }	
	
	public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);  
		      $this->data["Special"] = "Can be fired individually, or two Concentrators can be combined for a more powerful attack with shorter range.";
		      $this->data["Special"] .= "<br>Allocate 1+ Concentrators in 'Double' Firing Mode at same target to combine them."; 
		      $this->data["Special"] .= "<br>If not enough Concentrators are allocated in 'Double' mode, the extra shot is fired in Single mode instead.";  		  
		      $this->data["Special"] .= "<br>Each hit causes -1 Power on non-Ancient ships with Reactors for one turn.";
		      $this->data["Special"] .= "<br>Has +1 modifier to critical hit rolls, and +2 to fighter dropout rolls.";
	    }	
	
		
	public function fire($gamedata, $fireOrder){
	    if ($this->isCombined) $fireOrder->shots = 0; //no actual shots from weapon that's firing as part of combined shot!
	    parent::fire($gamedata, $fireOrder);
	} //endof function fire	
	
	
	//if fired in higher mode - combine with other weapons that are so fired!
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
//echo "Value of firingMode0: " . $fireOrder->firingMode . "\n";			
		$this->alreadyConsidered = true;
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon! 
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0; //tylko techniczne i tak
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->changeFiringMode($fireOrder->firingMode);
			return;
		}
			
		if ($fireOrder->firingMode > 1){ //for single fire there's nothing special
			$firingShip = $gamedata->getShipById($fireOrder->shooterid);
			$subordinateOrders = array();
			$subordinateOrdersNo = 0;
	
			//look for firing orders from same ship at same target (and same called id as well) in same mode - and make sure it's same type of weapon
			$allOrders = $firingShip->getAllFireOrders($gamedata->turn);
			foreach($allOrders as $subOrder) {
				if (($subOrder->type == 'normal') && ($subOrder->targetid == $fireOrder->targetid) && ($subOrder->calledid == $fireOrder->calledid) && ($subOrder->firingMode == $fireOrder->firingMode) ){ 
					//order data fits - is weapon another Concentrator?
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					if ($subWeapon instanceof PsionicConcentratorLight){
						if (!$subWeapon->alreadyConsidered){ //ok, can be combined then!
							$subordinateOrdersNo++;
							$subordinateOrders[] = $subOrder;
						}
					}
				}
				if ($subordinateOrdersNo>=($fireOrder->firingMode-1)) break;//enough subordinate weapons found! - exit loop
			}
										
			if ($subordinateOrdersNo == ($fireOrder->firingMode-1)){ //combining - set other combining weapons/fire orders to technical status!
				foreach($subordinateOrders as $subOrder){
					$subWeapon = $firingShip->getSystemById($subOrder->weaponid);
					$subWeapon->isCombined = true;
					$subWeapon->alreadyConsidered = true;
					$subWeapon->doNotIntercept = true;
				}
			}else{//not enough weapons to combine in this mode - set self to single fire	
				$fireOrder->firingMode = 1;
			}
		}
		parent::calculateHitBase($gamedata, $fireOrder);
	}//endof function calculateHitBase


    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata)
    {
		parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
 		//-1 power to ships for one turn.  
		if ($target->advancedArmor) return; //no effect on Advanced Armor
			 		     
		$reactor = $target->getSystemByName("Reactor");
		$mod = 1;//Easier to change later.
			if($reactor){
				for($i=1; $i<=$mod;$i++){
					$crit = new OutputReduced1(-1, $target->id, $reactor->id, 'OutputReduced1', $gamedata->turn+1, $gamedata->turn+1); 
					$crit->updated = true;
			        $reactor->criticals[] =  $crit;
				}    		
            }          
    }//endof beforeDamage

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);		
		if ($system->advancedArmor) return; //no effect on Advanced Armor		
		//+1 to crit roll, +2 to dropout roll, 
		$mod = 1;

		if ($ship instanceof FighterFlight) {
            $mod += 1;    		 
        }
        
        $system->critRollMod += $mod; 
	} //endof function onDamagedSystem	

	
    public function getDamage($fireOrder){ 	
		switch($this->firingMode){
		
			case 1:
				return Dice::d(6, 2)+6;							
							
			case 2:
				return Dice::d(6, 3)+15;							
		}
	}
	
	public function setMinDamage(){    
		switch($this->firingMode){
			
			case 1:
				$this->minDamage = 8;		
			break;
							
			case 2:
				$this->minDamage = 18;		
			break;
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage; 		
	}
	
	public function setMaxDamage(){
		switch($this->firingMode){
			
			case 1:
				$this->maxDamage = 18;		
			break;
							
			case 2:
				$this->maxDamage = 33;		
			break;
		}

		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;  
	}
	
} //endof class PsionicConcentratorLight



//Kor-Lyan system, used to designate where attached Proximity Laser shots originates by targeting a hex and automatically hitting.
class ProximityLaserLauncher extends Weapon{

		public $name = "ProximityLaserLauncher";
		public $displayName = "Proximity Launcher";
		public $iconPath = "ProximityLaserLauncher.png";
		
		public $damageType = "Standard"; //irrelevant, really
		public $weaponClass = "Ballistic";
		public $hextarget = true;
		public $hidetarget = true;
		public $ballistic = true;
		public $uninterceptable = true; 
		public $doNotIntercept = true;
		public $priority = 1;
	        public $useOEW = false;	
		public $noLockPenalty = false;	        
		
		public $range = 30;
		public $loadingtime = 1; //same as attached laser	
		
		public $animation = "ball";
		public $animationColor = array(245, 90, 90);
		public $animationExplosionScale = 0.5; //single hex explosion
		public $animationExplosionType = "AoE";
		
		//Should never be targeted or counted for CV.	
		protected $doCountForCombatValue = false;
		public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
		public $isTargetable = false; //cannot be targeted ever!	

			private $pairing = null;	//Which targeter is it paired with?	
			
		public $firingModes = array(
			1 => "Proximity Launcher"
		);
			
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	 
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $pairing)
		{
			$this->pairing = $pairing;
			$this->displayName = 'Proximity Launcher ' . $pairing . ''; 				
			//Nominal amount of health, should never be hit.
			if ( $maxhealth == 0 ) $maxhealth = 1;
			if ( $powerReq == 0 ) $powerReq = 0;
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		}
		    		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);        
			$this->data["Special"] = "Proximity Launcher " . $this->pairing ."."; 
			$this->data["Special"] .= "<br>Use to select hex from where Proximity Laser " . $this->pairing ." will fire.";	 
			$this->data["Special"] .= "<br>IMPORTANT - No effect unless Proximity Laser " . $this->pairing ." targeted at the same time as launcher is fired.";		 			
		}	
		
		public function calculateHitBase($gamedata, $fireOrder)
		{
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;
		}
		
	    public function fire($gamedata, $fireOrder)
	    { 
	        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
	        $shooter = $gamedata->getShipById($fireOrder->shooterid);        
	        $rolled = Dice::d(100);
	        $fireOrder->rolled = $rolled; 
			//$fireOrder->pubnotes .= "Automatically hits."; 
			if($rolled <= $fireOrder->needed){//HIT!
				$fireOrder->shotshit++;			
			}else{ //MISS!  Should never happen.
				$fireOrder->pubnotes .= "DEBUG - MISSED! ";
			}
		} //endof function fire	

     	
		public function getDamage($fireOrder){       return 0; /*no actual damage*/  }
		public function setMinDamage(){     $this->minDamage = 0 ;      }
		public function setMaxDamage(){     $this->maxDamage = 0 ;      }
	
}//endof class ProximityLaserLauncher


   class ProximityLaser extends Weapon{        
        public $name = "ProximityLaser";
        public $displayName = "Proximity Laser";
		public $iconPath = "ProximityLaser.png";        
        
        public $animation = "laser";

        public $animationColor = array(179, 45, 0); //same as Heavy Laser

        public $priority = 8;

        public $useOEW = false;
		public $hidetarget = true;
		public $ballistic = true;
        public $ammunition = 10; //limited number of shots	
        public $uninterceptable = true;        	
        
        public $loadingtime = 3;
        public $raking = 10; 
		public $noLockPenalty = false;	        
              
        
        public $weaponClass = "Laser"; 
        public $damageType = "Raking";
        
	public $firingModes = array(
		1 => "Laser"
	);                 
        
        public $rangePenalty = 0.5; //-1 per 2 hexes from Launcher's target hex.
        public $fireControl = array(null, 3, 3); //No fire control per se, but gets automatic +3 points.
        
		private $launcher = null;   //Variable where paired launcher be assigned.
		private $pairing = null;	//Which launcher is it paired with?	    
		protected $hasSpecialLaunchHexCalculation = true; //Weapons like Proximity Laser use a separate launcher system to determine point of shot.         
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $pairing){
 			$this->pairing = $pairing;
			$this->displayName = 'Proximity Laser ' . $pairing . ''; 			
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 6;				        	       	
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);  
			$this->data["Special"] = "Paired with Proximity Launcher ". $this->pairing ."."; 
			$this->data["Special"] .= "<br>Use Proximity Launcher ". $this->pairing ." to target a hex, laser will fire from this location.";
			$this->data["Special"] .= "<br>Range Penalty is calculated from Launcher target, not from this ship.";
			$this->data["Special"] .= "<br>Does not need an EW lock, and does not benefit from OEW.";				
			$this->data["Special"] .= "<br>IMPORTANT - Automatically misses if Proximity Launcher ". $this->pairing ." not fired as well.";					
	        $this->data["Ammunition"] = $this->ammunition;		
		}	



		public function getFiringHex($gamedata, $fireOrder) {
			if($this->launcher){	//Check that Proximity Laser have a Launcher (it always should)

		    $launchPos = null; // Initialize $launchPos outside the loop
			$launcherFireOrders = $this->launcher->getFireOrders($gamedata->turn);
		    
            foreach($launcherFireOrders as $fireOrder){	       	
			            // Sometimes player might target ship after all...
						if ($fireOrder->targetid != -1) {
	                        $targetship = $gamedata->getShipById($fireOrder->targetid);
	                        $movement = $targetship->getLastTurnMovement($fireOrder->turn);
	                        $fireOrder->x = $movement->position->q;
	                        $fireOrder->y = $movement->position->r;
	                        $fireOrder->targetid = -1; // Correct the error
	                    }
	                    $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
	                    $launchPos = $target; 	            
			        break;				       
			        }
			}
			if($launchPos == null) $launchPos = parent::getFiringHex($gamedata, $fireOrder); //Go back to normal function if returning null for some reason.
				
		    return $launchPos;
		} //endof getFiringHex
		


       function addLauncher($launcher){ //Function used to assign launcher on ship php file.
             $this->launcher = $launcher;
        }


        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

		public function calculateHitBase($gamedata, $fireOrder)
		{
			$launcherFireOrder = $this->launcher->getFireOrders($gamedata->turn);
						
			if(empty($launcherFireOrder)){//Launcher hasn't fired, laser automatically misses.	
				$fireOrder->needed = 0; //auto-miss.
				$fireOrder->updated = true;
				$fireOrder->pubnotes .= "<br>A Proximity Launcher was not fired, it's laser shot automatically missed.";
				return;				
			}

			$target = $gamedata->getShipById($fireOrder->targetid);
			$pos = $this->getFiringHex($gamedata, $fireOrder);		
			$targetPos = $target->getHexPos();			
			$losBlocked = $this->isLoSBlocked($pos, $targetPos, $gamedata);

			if($losBlocked){//Laser does not have Line of Sight from it's firing position
				$fireOrder->needed = 0; //auto-miss.
				$fireOrder->updated = true;
				$fireOrder->pubnotes .= "<br>A Proximity Laser did not have line of sight from its firing position.";
				return;				
			}	

			parent::calculateHitBase($gamedata, $fireOrder);		

		}//endof calculateHitBase()

		
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }

		//If Proximity Laser is destroyed, destroy this paired launcher as well.
		public function criticalPhaseEffects($ship, $gamedata)
	    { 
		  	parent::criticalPhaseEffects($ship, $gamedata);//Some critical effects like Limpet Bore might destroy weapon in this phase!
	  	 	    
			if(!$this->isDestroyed()) return;//Laser is not destroyed, all is well.

			if($this->isDestroyed()){ //Or if destroyed, find launcher and destroy it too.
				$launcher = $this->launcher;
				$launcherHealth = $launcher->getRemainingHealth();	//Just in case it's higher than 1 for some reason...						
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $launcher->id, $launcherHealth, 0, 0, -1, true, false, "Proximity Laser Destroyed - Launcher removed");
				$damageEntry->updated = true;
				$this->damage[] = $damageEntry;								
			}				
	    } //endof function criticalPhaseEffects	
	    
	    
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;
            $strippedSystem->launcher = $this->launcher; 
            $strippedSystem->hasSpecialLaunchHexCalculation = $this->hasSpecialLaunchHexCalculation;                              
            return $strippedSystem;
        }

        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+8;   }
        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 38 ;      }    
    }


   class ProximityLaserNew extends Weapon{        
        public $name = "ProximityLaserNew";
        public $displayName = "Proximity Laser";
		public $iconPath = "ProximityLaser.png";        
        
        public $animation = "laser";

        public $animationColor = array(179, 45, 0); //same as Heavy Laser

        public $priority = 8;

        public $useOEW = false;
		public $hextarget = true; //Added
		public $hidetarget = true;
		public $ballistic = true;
        public $ammunition = 10; //limited number of shots	
        public $uninterceptable = true;        	
        
        public $loadingtime = 3;
        public $raking = 10; 
		public $noLockPenalty = false;	        
              
        
        public $weaponClass = "Laser"; 
        public $damageType = "Raking";
        
		public $firingModes = array(
			1 => "Proximity Laser"
		);                 
			
        public $rangePenalty = 0.5; //-1 per 2 hexes from Launcher's target hex.
        public $fireControl = array(null, 3, 3); //No fire control per se, but gets automatic +3 points.
        
		//private $launcher = null;   //Variable where paired launcher be assigned.
		//private $pairing = null;	//Which launcher is it paired with?	    
		protected $hasSpecialLaunchHexCalculation = true; //Weapons like Proximity Laser use a separate launcher system to determine point of shot.         
		public $canSplitShots = true; //Added 
		public $startArcArray = array(); 
		public $endArcArray = array();	
		public $range = 30; 
		
        //function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $pairing){
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){		
 			//$this->pairing = $pairing;
			//$this->displayName = 'Proximity Laser ' . $pairing . ''; 
			$this->startArcArray[] = $startArc; 
			$this->endArcArray[] = $endArc;						
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 6;				        	       	
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);  
			$this->data["Special"] = "First use Proximity Launcher to target the hex from where the laser will fire at an enemy target.";
			$this->data["Special"] .= "<br>Then target an enemy ship to lock the laser shot onto it.";			
			$this->data["Special"] .= "<br>Range Penalty is calculated from hex you targeted, not from this ship.";
			$this->data["Special"] .= "<br>Does not need an EW lock, and does not benefit from OEW.";				
			$this->data["Special"] .= "<br>NOTE - You still need line of sight between laser and target when it fires, otherwise it will automatically miss.";					
	        $this->data["Ammunition"] = $this->ammunition;		
		}	


		public function getFiringHex($gamedata, $fireOrder) {
			//if($this->launcher){	//Check that Proximity Laser have a Launcher (it always should)

		    $launchPos = null; // Initialize $launchPos outside the loop
			//$launcherFireOrders = $this->launcher->getFireOrders($gamedata->turn);
			
			if($fireOrder->damageclass == 'Targeter'){
				$launchPos = parent::getFiringHex($gamedata, $fireOrder); //Use normal method for hex targeted launcher.
			}else{				
				$allFireOrders = $this->getFireOrders($gamedata->turn);
				$launcherFireOrder = null; //First fire order is always hex target.	

				foreach($allFireOrders as $fireOrderCheck){
					if ($fireOrderCheck->damageclass == 'Targeter'){
						$launcherFireOrder = $fireOrderCheck;					
						break;						
					}				
				}	
			
				if($launcherFireOrder){				       	
						// Sometimes player might target ship after all...
						if ($launcherFireOrder->targetid != -1) {
							$targetship = $gamedata->getShipById($launcherFireOrder->targetid);
							$movement = $targetship->getLastTurnMovement($launcherFireOrder->turn);
							$launcherFireOrder->x = $movement->position->q;
							$launcherFireOrder->y = $movement->position->r;
							$launcherFireOrder->targetid = -1; // Correct the error
						}

					$target = new OffsetCoordinate($launcherFireOrder->x, $launcherFireOrder->y);
					$launchPos = $target; 	            
					//break;				       
				}

				//Check in case something went wrong, in which case use default to prevent error.	
				if($launchPos == null || $launcherFireOrder == null) $launchPos = parent::getFiringHex($gamedata, $fireOrder); //Go back to normal function if returning null for some reason.
			}

		    return $launchPos;
		} //endof getFiringHex
		

		/*
       function addLauncher($launcher){ //Function used to assign launcher on ship php file.
             $this->launcher = $launcher;
        }
		*/

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }

		public function calculateHitBase($gamedata, $fireOrder)
		{
			if($fireOrder->damageclass == 'Targeter'){
				$fireOrder->needed = 100; //always true
				$fireOrder->updated = true;
			}else{	

				$allFireOrders = $this->getFireOrders($gamedata->turn);
				$launcherFireOrder = $allFireOrders[0]; //First fire order is always hex target.
				//$launcherFireOrder = $this->launcher->getFireOrders($gamedata->turn);
							
				if(empty($launcherFireOrder)){//Launcher hasn't fired, laser automatically misses.	
					$fireOrder->needed = 0; //auto-miss.
					$fireOrder->updated = true;
					$fireOrder->pubnotes .= "<br>A Proximity Launcher was not fired, it's laser shot automatically missed.";
					return;				
				}
				

				$target = $gamedata->getShipById($fireOrder->targetid);
				$pos = $this->getFiringHex($gamedata, $fireOrder);		
				$targetPos = $target->getHexPos();			
				$losBlocked = $this->isLoSBlocked($pos, $targetPos, $gamedata);

				if($losBlocked){//Laser does not have Line of Sight from it's firing position
					$fireOrder->needed = 0; //auto-miss.
					$fireOrder->updated = true;
					$fireOrder->pubnotes .= "<br>A Proximity Laser did not have line of sight from its firing position.";
					return;				
				}

				parent::calculateHitBase($gamedata, $fireOrder);
			}		

		}//endof calculateHitBase()

		
       public function fire($gamedata, $fireOrder){ //note ammo usage
			if($fireOrder->damageclass == 'Targeter'){		
				return; //Don't roll targeting shots, to remove them from animations.
			}else{	
            	parent::fire($gamedata, $fireOrder);
            	$this->ammunition--;
            	Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			}	
		}

		/*//If Proximity Laser is destroyed, destroy this paired launcher as well. No longer required
		public function criticalPhaseEffects($ship, $gamedata)
	    { 
		  	parent::criticalPhaseEffects($ship, $gamedata);//Some critical effects like Limpet Bore might destroy weapon in this phase!
	  	 	    
			if(!$this->isDestroyed()) return;//Laser is not destroyed, all is well.
			
			if($this->isDestroyed()){ //Or if destroyed, find launcher and destroy it too.
				$launcher = $this->launcher;
				$launcherHealth = $launcher->getRemainingHealth();	//Just in case it's higher than 1 for some reason...						
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $launcher->id, $launcherHealth, 0, 0, -1, true, false, "Proximity Laser Destroyed - Launcher removed");
				$damageEntry->updated = true;
				$this->damage[] = $damageEntry;								
			}
						
	    } //endof function criticalPhaseEffects	
	    */
	    
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;
            //$strippedSystem->launcher = $this->launcher; 
            $strippedSystem->hasSpecialLaunchHexCalculation = $this->hasSpecialLaunchHexCalculation;                              
            return $strippedSystem;
        }

        
        public function getDamage($fireOrder){        return Dice::d(10, 3)+8;   }
        public function setMinDamage(){     $this->minDamage = 11 ;      }
        public function setMaxDamage(){     $this->maxDamage = 38 ;      }    
    }	

class GromeTargetingArray extends Weapon{
		public $name = "GromeTargetingArray";
		public $displayName = "Targeting Array";
		public $iconPath = "TargetingArray.png";

		public $damageType = "Raking"; //To prevent called shots"
		public $weaponClass = "Particle";

		public $uninterceptable = true; 
		public $doNotIntercept = true;
		public $priority = 1;
	    public $useOEW = false;	
		public $noLockPenalty = false;	        
		
		public $range = 15;
		public $loadingtime = 1;	
        public $fireControl = array(null, 0, 0); //No fire control per se, but gets automatic +3 points.		
		
		public $animation = "bolt";
		public $animationColor = array(250, 250, 250);
		
		public $output = 0;
		public $outputDisplay = ''; //if not empty - overrides default on-icon display text
		public $escortArray = false;//Can be marked during firing if Array can support nearby vessels.		
		public $animationExplosionScale = 0.4; //single hex explosion
		
		public $haphazardTargeting = false;//To mark if ship has Haphazard Targeting Systems
		private $malfunction = false;//To mark when an array malfunctions.			
		public $firingModes = array(
			1 => "Targeting"
		);
		protected $autoHit = true;//To show 100% hit chance in front end.
			
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	 
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output, $escort, $base)
		{				
			//Nominal amount of health, should never be hit.
			if ( $maxhealth == 0 ) $maxhealth = 6;
			if ( $powerReq == 0 ) $powerReq = 2;	
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output);
			$this->output = $output;
//			$this->outputDisplay = $this->output;
			if($escort){
				$this->escortArray = true;
			}
			if($base){
				$this->range += 30;
			}										
			TargetingArrayHandler::addTargetingArray($this);//so all Targeting Array are accessible together.			
		}

	    protected $possibleCriticals = array(
			16=>"OutputReduced1"
	    );

		public function getOutput()
		{
			return $this->output;			
		}

		public function markHaphazard()
		{
			$this->haphazardTargeting = true;			
		}
		    		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);      
			$this->data["Special"] = "Automatically hits, but scores no damage."; 
			$this->data["Special"] .= "<br>Adds a bonus to hit for all other weapons against selected target based on rating of Targeting Array e.g. A rating of 2 would equal +10% to hit chance.";
			$this->data["Special"] .= "<br>Multiple Targeting Arrays can combine, but the effect will degrade by 5% per subsequent array.";
			$this->data["Special"] .= "<br>Cannot target fighters.";			
			if ($this->escortArray){
				$this->data["Special"] .= "<br>Escort Array - Also provides targeting assistance to friendly ships within 5 hexes.";			
			}	
		}	


    public function beforeFiringOrderResolution($gamedata)
    {
		if($this->haphazardTargeting){//Firing with Haphazard Targeting Systems.
        		
			$ship = $this->getUnit();
			$arraysDeactivated = 0;//Initialise counter.
					//Less chance of a malfunction if 1 or more Targeting Arrays are unavailable. 
			        foreach ($ship->systems as $system) {
			            if ($system instanceof GromeTargetingArray) {
			                if ($system->isDestroyed($gamedata->turn) || $system->isOfflineOnTurn($gamedata->turn)) {
			                    $arraysDeactivated++;
			                }
			            }
			        }
		        				
					if($arraysDeactivated == 0){//No Targeting Array deactivated, 16.66% chance of malfunction.				
							$roll6 = Dice::d(6);								
								if($roll6 < 2){ 								
									$this->output = 0;
									$this->malfunction = true;														
								}
					}else if($arraysDeactivated == 1){//One Targeting Array deactivated, 12.5% chance of malfunction.			
							$roll8 = Dice::d(8);
								if($roll8 < 2){ 				
									$this->output = 0;
									$this->malfunction = true;													
								}
					}else{} //2 or more deactivated/destroyed = no effect.
			}
		}//endof beforeFiringOrderResolution 

		
		public function calculateHitBase($gamedata, $fireOrder)
		{
			if($this->malfunction){//If Haphazard and a malfunction has been rolled.
					$fireOrder->needed = 0;
					$fireOrder->updated = true;																	
					$fireOrder->pubnotes .= " <br>A Targeting Array malfunctions!";							
			}else{
			//Normal firing
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
			}					
		}//endof calculateHitBase
			
	        	
		public function getDamage($fireOrder){       return 0;   } //no actual damage
		public function setMinDamage(){     $this->minDamage = 0 ;      }
		public function setMaxDamage(){     $this->maxDamage = 0 ;      }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->autoHit = $this->autoHit;                         
            return $strippedSystem;
		}
		
}//endof class GromeTargetingArray



class AegisSensorPod extends Weapon implements SpecialAbility{
		public $name = "AegisSensorPod";
		public $displayName = "Aegis Sensor Pod";
		public $iconPath = "AegisSensorPod.png";
		
		public $damageType = "Raking"; //irrelevant, really
		public $weaponClass = "Particle";

    	public $specialAbilities = array("BonusOEW"); //Front end looks for this.	
		public $specialAbilityValue = true; //so it is actually recognized as special ability!

		public $uninterceptable = true; 
		public $doNotIntercept = true;
		public $priority = 1;
	    public $useOEW = false;	
		public $noLockPenalty = false;
		public $range = 10;		        
		
		public $loadingtime = 1;	
        public $fireControl = array(0, null, null); //No fire control per se, but gets automatic +3 points.		
		
		public $animation = "bolt";
		public $animationColor = array(250, 250, 250);
		
		public $output = 0;
		public $outputDisplay = ''; //if not empty - overrides default on-icon display text
		
		public $animationExplosionScale = 0.4; //single hex explosion
		
		protected $calledShotBonus = 2;//Some systems, like Aegis Sensor Pod are easier to hit with called shots.		
					
		public $firingModes = array(
			1 => "Targeting"
		);
		protected $autoHit = true;//To show 100% hit chance in front end.
			
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
	 
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output)
		{				
			if ( $maxhealth == 0 ) $maxhealth = 5;
			if ( $powerReq == 0 ) $powerReq = 2;	
			parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output);
			$this->output = $output;											
		}

	    protected $possibleCriticals = array(
			16=>array("OutputReduced3") //System only exists on Aegis Cruiser and always has Output of 3.  So this works to make it cease to function.
	    );

		public function getSpecialAbilityValue($args)
	    {
			return $this->specialAbilityValue;
		}
		    		
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);      
			$this->data["Special"] = "<br>Can only target fighters.";
			$this->data["Special"] .= "Automatically hits, but scores no damage."; 
			$this->data["Special"] .= "<br>Provides 3 CCEW against target fighter unit.";
			$this->data["Special"] .= "<br>This CCEW cannot be combined with any other EW produced by the aegis ship, including OEW, CCEW, or another Aegis pods.";
			$this->data["Special"] .= "<br>Called shots have +10% chance to hit this system (i.e. -30%, not -40%).";
			
		}	

		public function checkforCalledShotBonus(){
			return $this->calledShotBonus;
		}
		
		public function calculateHitBase($gamedata, $fireOrder)
		{
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;
			$fireOrder->pubnotes .= "<br>Aegis Pod provides at least 3 CCEW against this target."; 						
								
		}//endof calculateHitBase
			
	        	
		public function getDamage($fireOrder){       return 0;   } //no actual damage
		public function setMinDamage(){     $this->minDamage = 0 ;      }
		public function setMaxDamage(){     $this->maxDamage = 0 ;      }

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->autoHit = $this->autoHit;
            $strippedSystem->calledShotBonus = $this->calledShotBonus;                         
            return $strippedSystem;
		}
		
}//endof class AegisSensorPod


class TargetingArrayHandler{
	public $name = "TargetingArrayHandler";
	private static $targetingArrays = array();
	
	
	//should be called by every Targeting Array on creation!
	public static function addTargetingArray($weapon){
		TargetingArrayHandler::$targetingArrays[] = $weapon;		
	}


	//Checks if current Array is potentially valid to support nearby friendly ships.
	public static function targetingArraysExist(){
		
		if(isset(TargetingArrayHandler::$targetingArrays)) return true;

	    return false;
	}//endof function targetingArraysExist 
	
	
	//compares Output of applicable Targeting Arrays, sorts them, then deducts -1 from output for each subsequent Targeting Array.
	public static function sortByOutput($arraysOnTarget){
	    // Initialize the adjustedBonus array
	    $adjustedBonus = array();
	    // Initialize the rank counter
	    $rank = 1;

	    // Sort the array based on the 'output' value
	    usort($arraysOnTarget, function($a, $b) {
	        return $b['output'] <=> $a['output'];
	    });

	    // Iterate over each item in the sorted array
	    foreach ($arraysOnTarget as $item) {
	        // Get the output value from the current item
	        $output = $item['output'];
	        // Adjust the value based on the rank
	        $adjustedOutput = max(0, $output - ($rank - 1));
	        // Store the adjusted value using the original key
	        $adjustedBonus[] = $adjustedOutput;
	        // Increment the rank counter
	        $rank++;
	    }

	    // Calculate the total sum of adjusted outputs
	    $totalSum = array_sum($adjustedBonus);

	    return $totalSum;
	}//endof function sortByOutput 
	
	
	//Checks if current Array is potentially valid to support nearby friendly ships.
	public static function isValidEscort($tArray, $arrayUnit, $shooter){
		$distance =	mathlib::getDistanceHex($arrayUnit, $shooter);
		if ($distance <= 5 && $arrayUnit->team == $shooter->team && $tArray->escortArray) return true;//Within 5 hexes, same team and has Escort Array marker.
	    return false;
	}//endof function isValidEscort 

	
	//Called during calculateHitBase whenever a weapon is fired at a target and shooter has Targeting Arrays.	
	public static function getHitBonus($gamedata, $fireOrder, $shooter, $target){ 

		if($shooter instanceof FighterFlight) return 0; //Fighters can't benefit from Targeting Arrays!
				
		//apparently ships may be loaded multiple times... make sure Targeting Arrays in $targetingArrays belong to current gamedata!
		$tmpArrays = array();
		foreach(TargetingArrayHandler::$targetingArrays as $targetArray){
			$arrayShooter = $targetArray->getUnit();
			if($targetArray->isDestroyed($gamedata->turn)) continue; //actually at this stage - CURRENT turn should be indicated!
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($arrayShooter);
			if ($belongs){
				$tmpArrays[] = $targetArray;
			}			
		}
		TargetingArrayHandler::$targetingArrays = $tmpArrays;
		
		$hitMod = 0; //Initialise
		$arraysOnTarget = array();	//Initialise	
		
		foreach(TargetingArrayHandler::$targetingArrays as $tArray){ //Check each Targeting Array in game.
			if ($tArray->isDestroyed($gamedata->turn)) continue; //destroyed Targeting Array does not matter.
			if ($tArray->isOfflineOnTurn($gamedata->turn)) continue; //disabled Targeting Array does not matter.			

			$arrayUnit = $tArray->getUnit(); //Unit with Targeting Array.
			$validEscort = TargetingArrayHandler::isValidEscort($tArray, $arrayUnit, $shooter);
		
			if($arrayUnit->id != $fireOrder->shooterid && (!$validEscort)) continue; //Only interested in Targeting Arrays that belong to shooter, or valid Escort Array.			
		    $arrayFiringOrders = $tArray->getFireOrders($gamedata->turn); //Get fireorders for current Targeting Array.		    	
		    $arrayOrder = null;
		    
		        foreach ($arrayFiringOrders as $order) { //Find appropriate order.
		              if ($order->type == 'normal') { 
		                $arrayOrder = $order;
		                break; //no need to search further
		              }
				}   
				 						
        	if($arrayOrder==null) continue; //no fire order, end of work	

	        if ($arrayOrder->targetid == $fireOrder->targetid) { //Is the current shot against same ship hit by Targeting Array?
			//The Targeting Array is now either on the Shooter vessel, or an friendly Escort within 5 hexes, and has fired at Target	        	
	          		$output = $tArray->getOutput(); //Get Targeting Array Output.		          			          			          			          		
    				$arraysOnTarget[] = array( //Add both Array and Output to variable for sorting.
	       				 'tArray' => $tArray,
	        			 'output' => $output
    				);         
	        }
	        
		}		
		
		$hitMod += TargetingArrayHandler::sortByOutput($arraysOnTarget); //Use sort function to find total hit bonus.
		$arraysOnTarget = array();	//clear, just in case.						
		return $hitMod;		
	
	}//endof function getHitBonus  	

}//endof class TargetingArrayHandler


class PulsarMine extends Weapon{
	public $name = "PulsarMine";
    public $displayName = "Pulsar Mine";
    public $iconPath = "PulsarMine.png";    
	
    public $range = 2;
    public $firingMode = 1;
    public $priorityAF = 1;
    public $loadingtime = 1;
	public $autoFireOnly = true; //this weapon cannot be fired by player
	public $preFires = true;
    public $useOEW = false;
	public $noLockPenalty = false;
    public $calledShotMod = 0;
	public $weaponClass = "Particle";     
    
    public $doNotIntercept = true; 		    		          
    public $uninterceptable = true;
   	public $ignoreJinking = true;//weapon ignores jinking completely.
        
    public $rangePenalty = 0; 
    public $fireControl = array(4, null, null); // fighters, <mediums, <capitals 

	public $animation = "bolt";
	public $animationColor = array(245, 90, 90);
	public $animationExplosionScale = 0.15; //single hex explosion

	private $alreadyEngaged = array(); //units that were already engaged by this Pulsar Mine this turn	 

	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 6;
		if ( $powerReq == 0 ) $powerReq = 4;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	} 

/*
    public function beforeFiringOrderResolution($gamedata){
    	
    	if($this->isDestroyed($gamedata->turn)) return;//Pulsar Mine is destroyed
		if($this->isOfflineOnTurn($gamedata->turn)) return; //Pulsar Mine is offline

		$thisShip = $this->getUnit();
		$deployTurn = $thisShip->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

    	$allShips = $gamedata->ships;  
    	$relevantShips = array();

		//Make a list of relelvant ships e.g. this ship and enemy fighters in the game.
		foreach($allShips as $ship){
			if ($ship->isDestroyed()) continue;
			if (!$ship instanceof FighterFlight && ($ship->id != $thisShip->id)) continue; //Ignore ships EXCEPT this one!			
			if ($ship instanceof FighterFlight && $ship->team == $thisShip->team) continue;	//Ignore flights that are friendly.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore fighters that are not deployed yet!			
			$relevantShips[] = $ship;			
		}
	
		//Now check if any enemy fighters got in arc and range during their movement.
		$targetFighters = $this->checkForValidTargets($relevantShips, $thisShip, $gamedata);

    	//Now create up to 18 attacks using $targetFighters array.
    	$this->createFireOrders($targetFighters, $thisShip, $gamedata);		

    	
	} //endof beforeFiringOrderResolution
*/

    public function beforePreFiringOrderResolution($gamedata){
    	
    	if($this->isDestroyed($gamedata->turn)) return;//Pulsar Mine is destroyed
		if($this->isOfflineOnTurn($gamedata->turn)) return; //Pulsar Mine is offline

		$thisShip = $this->getUnit();
		$deployTurn = $thisShip->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

    	$allShips = $gamedata->ships;  
    	$relevantShips = array();

		//Make a list of relelvant ships e.g. this ship and enemy fighters in the game.
		foreach($allShips as $ship){
			if ($ship->isDestroyed()) continue;
			if (!$ship instanceof FighterFlight && ($ship->id != $thisShip->id)) continue; //Ignore ships EXCEPT this one!			
			if ($ship instanceof FighterFlight && $ship->team == $thisShip->team) continue;	//Ignore flights that are friendly.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore fighters that are not deployed yet!			
			$relevantShips[] = $ship;			
		}
	
		//Now check if any enemy fighters got in arc and range during their movement.
		$targetFighters = $this->checkForValidTargets($relevantShips, $thisShip, $gamedata);

    	//Now create up to 18 attacks using $targetFighters array.
    	$this->createFireOrders($targetFighters, $thisShip, $gamedata);		

    	
	} //endof beforeFiringOrderResolution

	private function getTempBearing($shipPosition, $targetPostion, $currFacing){
		$relativeBearing = null;
		
		$oPos = mathlib::hexCoToPixel($shipPosition);//Convert to pixel format		
		$tPos = mathlib::hexCoToPixel($targetPostion); //Convert to pixel format
		
		$compassHeading = mathlib::getCompassHeadingOfPoint($oPos, $tPos);//Get heading using pixel formats.
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$currFacing);//relative bearing, compass - current facing.
       
        $ship = $this->getUnit();
        if( Movement::isRolled($ship) ){ //if ship is rolled, mirror relative bearing.  Not really needed, since arcs don't actually change.  
            if( $relativeBearing <> 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }        
        							
		return round($relativeBearing);//Round and return!
	}
	

	private function checkTargetConditions($previousBearing, $shipPosition, $targetPostion, $shipfacing, $gamedata){
		
		$distance =	mathlib::getDistanceHex($shipPosition, $targetPostion);//Compare starting positions.						
		$targetBearing = 0;
		
		if($distance == 0){//Ship and target are on same hex.
			if($previousBearing != null) $targetBearing = $previousBearing;//Same hex returns wrong bearing, so use previous if there is one. If null will return 0.
		}else{//Distance not 0, get bearing normally.
			$targetBearing = $this->getTempBearing($shipPosition, $targetPostion, $shipfacing);					
		} 
		
	    if ($distance > 2) return false; //Not within 2 hexes, skip LoS check and return false.
		if(!mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)) return false; //Not in arc.

		$loSBlocked = $this->isLoSBlocked($shipPosition, $targetPostion, $gamedata); //Returns true is LoS blocked
		if($loSBlocked) return false; //LoS Blocked

		return true;
	}	


	private function checkForValidTargets($relevantShips, $thisShip, $gamedata){
    	$targetFighters = array();//Initialise array for fighters to be fired at.		

		$shipStartLoc = $thisShip->getLastTurnMovement($gamedata->turn);
		$shipPosition = $shipStartLoc->position;
		$shipfacing = $shipStartLoc->getFacingAngle();

		foreach($relevantShips as $unit){//Now look through relevant ships and take appropriate action.				
				
			if($unit->id == $thisShip->id){//We've found Pulsar Mine equipped ship in array, check and update position and facing.
							
				foreach($unit->movement as $shipMove){ //Update position and facing. Assume handled in order.			
					if($shipMove->turn == $gamedata->turn){//This turn.
						if($shipMove->type == "start") continue; //not a move
						if($shipMove->type == "speedchange") continue; //not a move							
					
						$newPosition = $shipMove->position;//Update position for distance check.
						$shipPosition = $newPosition;
						$newfacing = $shipMove->getFacingAngle();//Update facing for arc check.
						$shipfacing = $newfacing;
					}
				}
							
			}else{//Look through enemy fighter positions to see if they were ever in arc/range.  Only during in Fighter's' movement, not $thisShip!	

				//Check starting position first.
				$fighterStartLoc = $unit->getLastTurnMovement($gamedata->turn);
				$previousBearing = null;
								
			    //Check if Fighter can be attacked in its starting position	
				if($this->checkTargetConditions($previousBearing, $shipPosition, $fighterStartLoc->position, $shipfacing,$gamedata)){
					$targetFighters[] = $unit;//Add to array.
					continue;//Fire Orders will be created for this unit, move to next.
				}		
						    
			    //Now check other movements in turn.	
				foreach($unit->movement as $fighterMove){//When we find Pulsar Mine equipped ship, update it's position as it moves. Assume will be handle in order.
					if($fighterMove->turn == $gamedata->turn){
						if($fighterMove->type == "start" ||
						 $fighterMove->type == "speedchange" ||
						 $fighterMove->type == "turnleft" || 
						 $fighterMove->type == "turnright") continue; //Not interested in start during Deployment, speed changes or turns.

			    		if(!$this->checkTargetConditions($previousBearing, $shipPosition, $fighterMove->position, $shipfacing, $gamedata)) {
				    		$distance =	mathlib::getDistanceHex($shipPosition, $fighterMove->position);	//Compare positions at point of movement.	
					    	$targetBearing = $this->getTempBearing($shipPosition, $fighterMove->position, $shipfacing);//Get bearing at this point in movement.
							if($distance != 0) $previousBearing = $targetBearing;//Update previous bearing with latest unless distance still 0.			    			
			    			continue;//Doesn't meet targeting criteria.
						}
			    			
				    	$targetFighters[] = $unit; //Add to array to be targeted.
				    	break; //Fire Orders will be created, no sense in checking further $fighterMove(s).   		 		
					}
				}					
			}			
		}		

	return $targetFighters; 		
		
	}//end of checkForValidTargets


	private function createFireOrders($targetFighters, $thisShip, $gamedata){
		$attacksTotal = 18;
		$currentShotNumber = 0;

		foreach ($targetFighters as $target) {
		    if (isset($this->alreadyEngaged[$target->id])) continue; // Ignore flights that have already been engaged. Shouldn't happen.
		    $fighters = $target->systems;
		    
		    // Create an array of valid indices
		    $validIndices = [];
		    foreach ($fighters as $index => $fighter) {
		        if ($fighter !== null) {
		            $validIndices[] = $index;
		        }
		    }
		    
		    // Sort the valid indices in descending order
		    rsort($validIndices);

		    // Iterate over the valid indices backwards
		    foreach ($validIndices as $i) {
		        $fighter = $fighters[$i];
		        
		        // Check if the fighter is null or destroyed
		        if ($fighter == null || $fighter->isDestroyed()) {
		            continue;
		        }

		        // Create a new FireOrder
		        $newFireOrder = new FireOrder(
		            -1, "prefiring", $thisShip->id, $target->id,
		            $this->id, $fighter->id, $gamedata->turn, 1, 
		            0, 0, 1, 0, 0, // needed, rolled, shots, shotshit, intercepted
		            0, 0, $this->weaponClass, -1 // X, Y, damageclass, resolutionorder
		        );        

		        $newFireOrder->addToDB = true;
		        $this->fireOrders[] = $newFireOrder;
		        $currentShotNumber++;
		                                                        
		        if ($currentShotNumber >= $attacksTotal) break; // Break out of the loop if the required number of attacks is reached
		    }

		    $this->alreadyEngaged[$target->id] = true; // Mark engaged
		    if ($currentShotNumber >= $attacksTotal) break; // No sense looking at further target units if mines all used up.
		}
	}//End of createFireOrders()	

	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Automatically attacks up to 18 enemy fighters who were within 2 hexes of this ship during their Movement Phase (and were in arc)';
		$this->data["Special"] .= '<br>Attacks are generated during the Firing Phase as normal.';	
		$this->data["Special"] .= '<br>Cannot be manually targeted.';													
	}	

    public function getDamage($fireOrder){        return 8;   }
    public function setMinDamage(){     $this->minDamage = 8 ;      }
    public function setMaxDamage(){     $this->maxDamage = 8 ;      }
	
} //endof class PulsarMine



class Marines extends Weapon{
	public $name = "Marines";
	public $displayName = "Marines";
	public $iconPath = "Marines.png";
	public $animation = "trail";
	public $animationColor = array(50, 50, 50);
	public $animationWidth = 0.2;
  
	public $useOEW = false; 
	public $range = 0.1;
	public $ammunition = 2; //limited number of Marine contingents.

	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table, should never happen.

	public $calledShotMod = 0; //instead of usual -8
	
	public $loadingtime = 1;
	public $rangePenalty = 0;
	public $fireControl = array(null, 0, 0);
	
	public $noOverkill = true;
	public $priority = 9;
	
	public $uninterceptable = true; 
	public $doNotIntercept = true;	
		

	public $damageType = "Special";
	public $damageTypeArray = array(1=> "Special", 2=> "Standard", 3=> "Special");
	public $weaponClass = "Matter";
	public $firingModes = array(
		1 => "Capture Ship",
		2 => "Sabotage",
		3 => "Rescue"
	);		

	public $eliteMarines = false;
	public $isBoardingAction = true;//For front end to recalculate hit chance.	

	public static $boardedThisTurn = array();//Static variable to keep track of Marine actions on current turn (to prevent too many on a ship).			
		 
	function __construct($startArc, $endArc, $damagebonus, $elite){
		parent::__construct(0, 1, 0, $startArc, $endArc);
		$this->eliteMarines = $elite;
	}    
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);      
		$this->data["Special"] = "<br>If on same hex as an enemy ship, can attempt to board that vessel.";	
		$this->data["Special"] .= "<br>Marines may attempt three 'Missions' by selecting the appropriate Firing Mode.";  		 		
		$this->data["Special"] .= "<br> - Capture Ship: Marines can attempt to overcome defenders on enemy ship and disable it."; 
		$this->data["Special"] .= "<br> - Sabotage: Can be directed at a specific system (i.e. called shot) or for general sabotage operations on enemy ship.";
		$this->data["Special"] .= "<br> - Rescue: Scenarios only, Marines will board enemy ship and attempt to rescue target."; 						 
		$this->data["Special"] .= "<br>See 'Common Systems & Enhancements' file for full information on Boarding Actions.";  		                     
		if($this->eliteMarines){
			$this->data["Elite"] = "Yes";
		}else{
			$this->data["Elite"] = "No";			
		}		
		$this->data["Ammunition"] = $this->ammunition;	
	}
	

	public function setAmmo($firingMode, $amount){
		$this->ammunition = $amount;
	}

	public function calculateHitBase($gamedata, $fireOrder)
	{
		//Needs it's own custom routine for hit chance.
		$shooter = $gamedata->getShipById($fireOrder->shooterid);	
		$target = $gamedata->getShipById($fireOrder->targetid);			

		if($target->factionAge > 2) {//Cannot attach to Ancients.  Might be impossible if Front End chance is also made 0%
			$fireOrder->pubnotes .= "<br> Breaching pods cannot attach to Ancient ships.";
			$fireOrder->needed = 0;
			$fireOrder->updated = true;						
			return; 
		}
		
		//Now roll to see if the Breaching Pod attaches on this turn.
		$shooterMove = $shooter->getLastMovement();
		$shooterSpeed = $shooterMove->speed;		
		
		$targetMove = $target->getLastMovement();
		$targetSpeed = $targetMove->speed;
		$speedDifference = abs($targetSpeed - $shooterSpeed);//Calculate absolute difference in speed.
		if($shooter->faction == "Llort" || $shooter->faction == "ZNexus Sal-bez Coalition") $speedDifference -= 1;//Llort reduce speed difference by 1.
			
		$finalSpeedDifference = max(0, $speedDifference);//Llort bonus could make it -1...
		
		if($finalSpeedDifference > $shooter->freethrust){//Pod cannot compensate enough for speed difference with available thrust.
			$fireOrder->needed = 0;
			$fireOrder->updated = true;
			$fireOrder->pubnotes .= "<br> The speed difference to target is too great and pod is unable to attach.";					
			return; 
		}

        $hitLoc = null;
        $hitLoc = $target->getHitSection($shooter, $fireOrder->turn);
		
		if($targetSpeed > $shooterSpeed){//Target is moving faster, roll to attach.
			$baseHitChance = 100;//Start with automatic hit.
			$speedChance = 	$finalSpeedDifference *10;//Each point of speed difference is 10% chance to miss.
			$finalHitChance = $baseHitChance - $speedChance;//Adjust hitchance.
			$fireOrder->needed = $finalHitChance;//Update fireOrder.		
			$fireOrder->updated = true;	
			$fireOrder->chosenLocation = $hitLoc;//Need to mark this for successful shots to check if hitting Primary.									
			return;
		}else{
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
			$fireOrder->chosenLocation = $hitLoc;			
			return;
		}	
		
	}//endof calculateHitBase
	
   public function fire($gamedata, $fireOrder){ //note ammo usage
		parent::fire($gamedata, $fireOrder);

		if($fireOrder->rolled <= $fireOrder->needed){//Only reduce ammo if Marines successfully boarded enemy ship.

			$this->ammunition--;//Deduct Marine unit just used.			

			//Need to remove Enhancement bonuses from saved ammo count, as these will be re-added in onConstructed()
			$ship = $gamedata->getShipById($fireOrder->shooterid);
	
			foreach ($ship->enhancementOptions as $enhancement) {
			    $enhID = $enhancement[0];
				$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
			        if ($enhID == 'EXT_MAR') $this->ammunition -= $enhCount;       	
				}
			}	
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
		}
					
	}

	public static function recordBoarding($targetId) {
	    Marines::$boardedThisTurn[] = $targetId;
	}	
	
	private function checkMissionAmount($target, $gamedata, $fireOrder){	
		$tooMany = false;//Initialise
		$noOfPods = 0;//Initialise	

	    foreach (Marines::$boardedThisTurn as $boardedId) {//Check static variable for how many marines missions have boarded THIS turn.
	        if ($boardedId == $target->id) {
	            $noOfPods++;
	        }
	    }	
		
		//Different amount of marine missions possible depending on size of ships.
		if(	($target->shipSizeClass > 3 && $noOfPods >= 12) ||
			($target->shipSizeClass == 3 && $noOfPods >= 8) ||
		   	($target->shipSizeClass == 2 && $noOfPods >= 4) || 
		   	($target->shipSizeClass == 1 && $noOfPods >= 2) ||
		   	($target->shipSizeClass < 1 && $noOfPods > 1)) {
		 									
			$tooMany = true; //There are too many, change to false.
		}	

		return $tooMany;	
		
	}//endof checkMissionAmount()

	private function getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder){
		$rollMod = 0;
		if($this->eliteMarines) $rollMod -= 1; //Elite Marines board more easily.

		if($target->faction == "Narn Regime" || $target->faction == "Gaim Intelligence" )	$rollMod += 1; //Certain factions defend harder! 

		if($shooter->faction == "Llort")  $rollMod -= 1; //Llort should get bonus to Rescue and Capture, but making them elite feels incorrect.  Have instead made it easier for their marines to board. 	
		if($shooter->faction == "Yolu Confederation")  $rollMod -= 2; //Yolu have -2 to deliver marines.	
						
		$location = $fireOrder->chosenLocation ;
		if($location == 0 && (!$target instanceof OSAT)) $rollMod -= 1; //Easier to deliver marines to destroyed sections i.e direct to Primary section.	       

		foreach ($target->enhancementOptions as $enhancement) {//Defender quality can influence roll too.
		    $enhID = $enhancement[0];
			$enhCount = $enhancement[2];		        
			if($enhCount > 0) {		            
		        if ($enhID == 'ELITE_CREW') $rollMod += $enhCount;	//Elite Crews are better at defending.
		        if ($enhID == 'POOR_CREW') $rollMod -= $enhCount; //Poor Crews are worse.
		        if ($enhID == 'MARK_FERV') $rollMod += $enhCount; //Markab Fervor causes defenders to fight harder.		        	
			}
		}

        return $rollMod;
        		
	}//endof getDeliveryRollMod
	

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!

		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
			
		if ($system->advancedArmor) {//no effect on Advanced Armor for Younger Races equipped with this e.g. Shadow Omega.	
			$fireOrder->pubnotes .= "<br> Marines cannot attack systems with advanced armor.";				
			return; 	
		}

		//check if there are too many marines already on target ship.
		if($this->checkMissionAmount($target, $gamedata, $fireOrder)){//If it returns true, there are too many attaching pods.							
			$this->ammunition++;//Marines weren't eliminated.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);	
			$fireOrder->pubnotes .= "<br>Too many Breaching Pods/Grappling Claws attached on target, boarding attempt cancelled.";		
			return;
		}	
			
		//Can proceed with boarding actions, roll to see if Marines are delivered.		
		$rollMod = $this->getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder);		
		$deliveryRoll = max(0, Dice::d(10) + $rollMod);		

		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.		
		foreach($cnc->criticals as $critDisabled){
			if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) $deliveryRoll = 1;//Ship captured, auto success.		
		}		
		
		if($deliveryRoll <= 5){ //successful delivery, continue with applying critical effects.						
				
			switch($this->firingMode){
								
				case 1://Capture

					$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt to capture enemy ship next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new CaptureShipElite(-1, $ship->id, $cnc->id, 'CaptureShipElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}else{//Not Elite Marines					
									$crit = new CaptureShip(-1, $ship->id, $cnc->id, 'CaptureShip', $gamedata->turn+1);  //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }				
				
					break;

				case 2://Sabotage

					if($fireOrder->calledid != -1 && !($system instanceof Structure) && $system->location != 0){//Is a called shot, and not somehow attacking structure, place crit on system.
							$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt to sabotage " . $system->displayName ." system next turn.";
						if($this->eliteMarines){//Are Marines Elite?
							$crit = new SabotageElite(-1, $ship->id, $system->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}else{//Not Elite Marines			
							$crit = new Sabotage(-1, $ship->id, $system->id, 'Sabotage', $gamedata->turn+1); //Takes effect next turn.
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}	
					}else{ //Has targeted ship generally, not a specific system (or somehow retargeted to structure).  Apply crit to CnC.
						$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt sabotage operations on enemy ship next turn.";								
							if($cnc){
									if($this->eliteMarines){//Are Marines Elite?
										$crit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
									}else{//Not Elite Marines					
										$crit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
									}							    		
				            }				
					}	
					
					break;				
				
				case 3://Rescue

					$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt their rescue mission next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new RescueMissionElite(-1, $ship->id, $cnc->id, 'RescueMissionElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note marines have boarded this turn
								}else{//Not Elite Marines					
									$crit = new RescueMission(-1, $ship->id, $cnc->id, 'RescueMission', $gamedata->turn+1);  //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }	
				
					break;			
				
			}
		}elseif($deliveryRoll >= 6 && $deliveryRoll <=8){//Unsuccessful delivery
			$this->ammunition++;//Marines weren't eliminated, they just weren't delivered.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit failed to board enemy ship, but returned safely to their pod.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
			return;	
		}else{//Roll result is 9 or over
			$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit was eliminated whilst trying to board the enemy ship.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.								
			return;
		}			
	}//endof onDamagedSystem() 	
	
	
	public function getDamage($fireOrder){ //Damage is handled in criticalPhaseEffects()
		return 0;
	}


	public function setMinDamage(){     $this->minDamage = 0;      } //However, keep these values for intercept calculations.
	public function setMaxDamage(){     $this->maxDamage = 0;      }

	public function stripForJson() {
			$strippedSystem = parent::stripForJson();    
			$strippedSystem->ammunition = $this->ammunition;			
			$strippedSystem->isBoardingAction = $this->isBoardingAction;                          
			return $strippedSystem;
	}
	
}//endof Marines



class GrapplingClaw extends Weapon{
	public $name = "GrapplingClaw";
	public $displayName = "Grappling Claw";
	public $iconPath = "grapplingClaw.png";
	public $animation = "trail";
	public $animationColor = array(50, 50, 50);
	public $animationWidth = 0.2;
  
	public $useOEW = false; 
	public $range = 0.1;

	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table, should never happen.

	public $calledShotMod = 0; //instead of usual -8
	
	public $loadingtime = 1;
	public $rangePenalty = 0;
	
	public $noOverkill = true;
	public $priority = 9;
	
	public $uninterceptable = true; 
	public $doNotIntercept = true;			

	public $damageType = "Special";
	public $damageTypeArray = array(1=> "Special", 2=> "Standard", 3=> "Special");	
	public $weaponClass = "Matter";
	public $firingModes = array(
		1 => "Capture Ship",
		2 => "Sabotage",
		3 => "Rescue"
	);		

	public $eliteMarines = false;
	public $isBoardingAction = true;//For front end to recalculate hit chance.	
		
	public $ammunition = 0; //limited number of Marine contingents.
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition, $elite)
	{
		if ( $maxhealth == 0 ) $maxhealth = 5;
        if ( $powerReq == 0 ) $powerReq = 0;      						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition, $elite); //Parent routines take care of the rest
		$this->ammunition = $ammunition;			
		$this->eliteMarines = $elite;	       
	}

	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);      
		$this->data["Special"] = "<br>If on same hex as an enemy ship, and in arc, this weapon attempt to deliver Marines to that vessel.";	
		$this->data["Special"] .= "<br>Marines may attempt three 'Missions' by selecting the appropriate Firing Mode.";  		
		$this->data["Special"] .= "<br> - Capture Ship: Marines can attempt to overcome defenders on enemy ship and disable it."; 
		$this->data["Special"] .= "<br> - Sabotage: Can be directed at a specific system (i.e. called shot) or for general sabotage operations on enemy ship."; 
		$this->data["Special"] .= "<br> - Rescue: Scenarios only, Marines will board enemy ship and attempt to rescue target."; 
		$this->data["Special"] .= "<br>NOTE - You cannot Grapple ships which rolled higher initiative than you this turn, even if you are in the same Initiative bracket using Simultaneous Movement rules."; 
		$this->data["Special"] .= "<br>See 'Common Systems & Enhancements' file for full information on Boarding Actions.";  		                     
		if($this->eliteMarines){
			$this->data["Elite"] = "Yes";
		}else{
			$this->data["Elite"] = "No";			
		}
	    $this->data["Ammunition"] = $this->ammunition;					
	}


	public function calculateHitBase($gamedata, $fireOrder)
	{
		//Needs it's own custom routine for hit chance.
		$shooter = $gamedata->getShipById($fireOrder->shooterid);	
		$target = $gamedata->getShipById($fireOrder->targetid);			

		if($target->factionAge > 2) {//Cannot attach to Ancients.  Might be impossible if Front End chance is also made 0%
			$fireOrder->pubnotes .= "<br> Grappling Claws cannot attach to Ancient ships.";
			$fireOrder->needed = 0;
			$fireOrder->updated = true;	
			return; 
		}
		
        if($target->iniative > $shooter->iniative){//Should not happen, Front End will prevent.  But just in case.
			$fireOrder->pubnotes .= "<br> Grappling Claws cannot attach when you have lower Initiative than target.";
			$fireOrder->needed = 0;
			$fireOrder->updated = true;	
			return; 
		}  		
		
		//Now roll to see if the Grappling Claw attaches on this turn.
		$shooterMove = $shooter->getLastMovement();
		$shooterSpeed = $shooterMove->speed;		
		
		$targetMove = $target->getLastMovement();
		$targetSpeed = $targetMove->speed;
		$speedDifference = abs($targetSpeed - $shooterSpeed);//Calculate absolute difference in speed.
			
		$finalSpeedDifference = max(0, $speedDifference);

        $hitLoc = null;
        $hitLoc = $target->getHitSection($shooter, $fireOrder->turn);

		if($finalSpeedDifference > 0){//D20 roll needs to be over speed difference.
			$baseHitChance = 100;//Start with automatic hit.
			$speedChance = 	$finalSpeedDifference *5;//Each point of speed difference is 5% chance to miss.
			$finalHitChance = $baseHitChance - $speedChance;//Adjust hitchance.
			if($target->Enormous) $finalHitChance += 10; //You can't attach to Enormous Units without auto-ramming, but at least you get a bonus :)
			$fireOrder->needed = $finalHitChance;//Update fireOrder.		
			$fireOrder->updated = true;	
			$fireOrder->chosenLocation = $hitLoc;//Need to mark this for successful shots to check if hitting Primary.									
			return;
		}else{
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
			$fireOrder->chosenLocation = $hitLoc;							
			return;
		}	
		
	}//endof calculateHitBase

   public function fire($gamedata, $fireOrder){ //note ammo usage
		parent::fire($gamedata, $fireOrder);

		if($fireOrder->rolled <= $fireOrder->needed){//Only reduce ammo if Marines successfully boarded enemy ship.

			$this->ammunition--;//Deduct Marine unit just used.			

			//Need to remove Enhancement bonuses from saved ammo count, as these will be re-added in onConstructed()
			$ship = $gamedata->getShipById($fireOrder->shooterid);
	
			foreach ($ship->enhancementOptions as $enhancement) {
			    $enhID = $enhancement[0];
				$enhCount = $enhancement[2];		        
				if($enhCount > 0) {		            
			        if ($enhID == 'EXT_MRN') $this->ammunition -= $enhCount;       	
				}
			}	
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
		}
			
	}

	private function checkMissionAmount($target, $gamedata, $fireOrder){	
		$tooMany = false;//Initialise
		$noOfPods = 0;//Initialise	

	    foreach (Marines::$boardedThisTurn as $boardedId) {//Check static variable for how many marines missions have boarded THIS turn.
	        if ($boardedId == $target->id) {
	            $noOfPods++;
	        }
	    }	
		
		//Different amount of marine missions possible depending on size of ships.
		if(	($target->shipSizeClass > 3 && $noOfPods >= 12) ||
			($target->shipSizeClass == 3 && $noOfPods >= 8) ||
		   	($target->shipSizeClass == 2 && $noOfPods >= 4) || 
		   	($target->shipSizeClass == 1 && $noOfPods >= 2) ||
		   	($target->shipSizeClass < 1 && $noOfPods > 1)) {
		 									
			$tooMany = true; //There are too many, change to false.
		}	

		return $tooMany;	
		
	}//endof checkMissionAmount()



	private function getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder){
		$rollMod = 0;
		if($this->eliteMarines) $rollMod -= 1; //Elite Marines board more easily.
						
		$location = $fireOrder->chosenLocation ;
		if($location == 0 && (!$target instanceof OSAT)) $rollMod -= 1; //Easier to deliver marines to destroyed sections i.e direct to Primary section.	       

		foreach ($target->enhancementOptions as $enhancement) {//Defender quality can influence roll too.
		    $enhID = $enhancement[0];
			$enhCount = $enhancement[2];		        
			if($enhCount > 0) {		            
		        if ($enhID == 'ELITE_CREW') $rollMod += $enhCount;	//Elite Crews are better at defending.
		        if ($enhID == 'POOR_CREW') $rollMod -= $enhCount; //Poor Crews are worse.
		        if ($enhID == 'MARK_FERV') $rollMod += $enhCount; //Markab Fervor causes defenders to fight harder.		        	
			}
		}

        return $rollMod;
        		
	}//endof getDeliveryRollMod	
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!

		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
			
		if ($system->advancedArmor) {//no effect on Advanced Armor for Younger Races equipped with this e.g. Shadow Omega.	
			$fireOrder->pubnotes .= "<br> Marines cannot attack systems with advanced armor.";				
			return; 	
		}
		
		//check if there are too many marines already on target ship.
		if($this->checkMissionAmount($target, $gamedata, $fireOrder)){//If it returns true, there are too many attaching pods.							
			$this->ammunition++;//Marines weren't eliminated.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);	
			$fireOrder->pubnotes .= "<br>Too many Breaching Pods/Grappling Claws attached on target, boarding attempt cancelled.";		
			return;
		}	
			
		//Can proceed with boarding actions, roll to see if Marines are delivered.		
		$rollMod = $this->getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder);		
		$deliveryRoll = max(0, Dice::d(10) + $rollMod);		

		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.		
		foreach($cnc->criticals as $critDisabled){
			if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) $deliveryRoll = 1;//Ship captured, auto success.		
		}		
		
		if($deliveryRoll <= 5){ //successful delivery, continue with applying critical effects.						
				
			switch($this->firingMode){
								
				case 1://Capture

					$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt to capture enemy ship next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new CaptureShipElite(-1, $ship->id, $cnc->id, 'CaptureShipElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}else{//Not Elite Marines					
									$crit = new CaptureShip(-1, $ship->id, $cnc->id, 'CaptureShip', $gamedata->turn+1);  //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }				
				
					break;

				case 2://Sabotage

					if($fireOrder->calledid != -1 && !($system instanceof Structure) && $system->location != 0){//Is a called shot and not structure, place crit on system.
							$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt to sabotage " . $system->displayName ." system next turn.";
						if($this->eliteMarines){//Are Marines Elite?
							$crit = new SabotageElite(-1, $ship->id, $system->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}else{//Not Elite Marines			
							$crit = new Sabotage(-1, $ship->id, $system->id, 'Sabotage', $gamedata->turn+1); //Takes effect next turn.
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}	
					}else{ //Has targeted ship generally, not a specific system.  Apply crit to CnC.
						$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt sabotage operations on enemy ship next turn.";								
							if($cnc){
									if($this->eliteMarines){//Are Marines Elite?
										$crit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
									}else{//Not Elite Marines					
										$crit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
									}							    		
				            }				
					}	
					
					break;				
				
				case 3://Rescue

					$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt their rescue mission next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new RescueMissionElite(-1, $ship->id, $cnc->id, 'RescueMissionElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note marines have boarded this turn
								}else{//Not Elite Marines					
									$crit = new RescueMission(-1, $ship->id, $cnc->id, 'RescueMission', $gamedata->turn+1);  //Takes effect next turn.
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }	
				
					break;			
				
			}
		}elseif($deliveryRoll >= 6 && $deliveryRoll <=8){//Unsuccessful delivery
			$this->ammunition++;//Marines weren't eliminated, they just weren't delivered.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit was beaten back by defenders but managed to return safely to their pod.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
			return;	
		}else{//Roll result is 9 or over
			$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit was eliminated by defenders whilst trying to board the enemy ship.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.								
			return;
		}			
	}//endof onDamagedSystem() 		
	
	
	public function getDamage($fireOrder){ //Damage is handled in criticalPhaseEffects()
		return 0;
	}

	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }

	public function stripForJson() {
			$strippedSystem = parent::stripForJson();    
			$strippedSystem->ammunition = $this->ammunition;			
			$strippedSystem->isBoardingAction = $this->isBoardingAction;                          
			return $strippedSystem;
	}	
	
} //endof class GrapplingClaw



class SecondSight extends Weapon{
	public $name = "SecondSight";
    public $displayName = "Second Sight";
    public $iconPath = "SecondSight.png";    
	
    public $range = 100;
    public $firingMode = 1;
    public $priorityAF = 1;
    public $loadingtime = 2;
	public $hextarget = true;
    public $useOEW = false;
	public $noLockPenalty = false;
    
    public $doNotIntercept = true; 		    		          
    public $uninterceptable = true;
   	public $ignoreJinking = true;//weapon ignores jinking completely.
        
    public $rangePenalty = 0; 
    public $fireControl = array(null, null, null); // fighters, <mediums, <capitals 

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!   
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	public $firingModes = array(1=> "Second Sight"); 

    public $animation = "ball";
    public $animationExplosionScale = 15;   
	public $animationColor = array(204, 102, 0);
	public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.  		

	protected $autoHit = true;//To show 100% hit chance in front end.
   	//protected $noTargetHexIcon = true; //For Front End Hex icon display.
	
	public $autoFireOnly = true; //this weapon cannot manually fire by player at a target, just activated	
	
    protected $possibleCriticals = array();	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 16;
		if ( $powerReq == 0 ) $powerReq = 8;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
	} 


	public function beforeFiringOrderResolution($gamedata){


		$firingOrders = $this->getFireOrders($gamedata->turn);
		  
		$hasFireOrder = null;
				foreach ($firingOrders as $fireOrder) { 
					   if ($fireOrder->type == 'normal') { 
					  $hasFireOrder = $fireOrder;
					  break; //no need to search further
					  }
				  }    			
				  
		  if($hasFireOrder==null) return; //no appropriate fire order, end of work
  
		  $thisShip = $this->getUnit();  	
						
		  //Have Second Sight Wave originate from firinf ship's locations.	
		  $targetPos = $thisShip->getHexPos();
		  $hasFireOrder->x = $targetPos->q;
		  $hasFireOrder->y = $targetPos->r;
		  
		  //Correct any errors.
		  if ($hasFireOrder->targetid != -1) {
			  $hasFireOrder->targetid = -1; //correct the error
			  $hasFireOrder->calledid = -1; //just in case
		  }
		  
		  
		  $allShips = $gamedata->ships;
		  $relevantShips = array();
  
		  //Make a list of relevant ships e.g. all enemy ships.
		  foreach($allShips as $ship){
			  if($ship->isDestroyed()) continue;		
			  if ($ship->team == $thisShip->team) continue;	//Ignore friendlies.
			  if ($ship->isTerrain()) continue;			  
			  if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore targets that are not deployed yet!			  	
			  $relevantShips[] = $ship;			
		  }
	  
		  foreach($relevantShips as $target){
			  
			  $effectIni = Dice::d(6, 1)+2;
			  if ($target->advancedArmor) $effectIni = 2;
			  
			  if ($target instanceof FighterFlight){  //place effect on first fighter, even if it's already destroyed!
				  $firstFighter = $target->getSampleFighter();
				  if($firstFighter){
					  for($i=1; $i<=$effectIni;$i++){
						  $crit = new tmpinidown(-1, $target->id, $firstFighter->id, 'tmpinidown', $gamedata->turn); 
						  $crit->updated = true;
						  $firstFighter->criticals[] =  $crit;
					  }
				  }
			  }else{ //ship - place effcet on C&C!
				  $CnC = $target->getSystemByName("CnC");
				  if($CnC){
					  for($i=1; $i<=$effectIni;$i++){
						  $crit = new tmpinidown(-1, $target->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
						  $crit->updated = true;
						  $CnC->criticals[] =  $crit;
					  }
				  }
			  }			
  
		  }
		  
	  } //endof beforeFiringOrderResolution

	public function calculateHitBase($gamedata, $fireOrder)
		{
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
		}              

    public function fire($gamedata, $fireOrder)
    {
		//    $shooter = $gamedata->getShipById($fireOrder->shooterid);        
	        $rolled = Dice::d(100);
	        $fireOrder->rolled = $rolled; 
			$fireOrder->pubnotes .= "<br> Reduces Initiative of all enemy ships.";
			if($rolled <= $fireOrder->needed){//HIT!
				$fireOrder->shotshit++;		
			}else{ //MISS!  Should never happen.
				$fireOrder->pubnotes .= " MISSED! ";
			}
	}

    public function getFiringHex($gamedata, $fireOrder){
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
		$pos = $shooter->getHexPos();
		$launchPos = null;		
		
        if ($this->ballistic) {
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $launchPos = $movement->position;
        } else {
            $launchPos = $pos;
        }
       return $launchPos; 
	}//endof getFiringHex
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Fire this weapon by clicking Select during the Firing phase.';
		$this->data["Special"] .= '<br>Automatically reduces Initiative of ALL enemy units next turn.';		
		$this->data["Special"] .= '<br>Enemy ships suffer a D6+2 (e.g. -15 to -40) Initiative penalty next turn.';	
		$this->data["Special"] .= '<br>Ships equipped with Advanced Armor will only suffer -10 Initiatve penalty.';
		$this->data["Special"] .= '<br>Initiative penalties ARE cumulative with other Second Sight weapons.';	
	}	

    public function getDamage($fireOrder){        return 0;   }
    public function setMinDamage(){     $this->minDamage = 0 ;      }
    public function setMaxDamage(){     $this->maxDamage = 0 ;      }
    

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->autoHit = $this->autoHit;
		//$strippedSystem->noProjectile = $this->noProjectile; 
		//$strippedSystem->noTargetHexIcon = $this->noTargetHexIcon;		                                
        return $strippedSystem;
	}    
 
	
} //endof class SecondSight 


class ThoughtWave extends Plasma{
	public $name = "ThoughtWave";
    public $displayName = "Thought Wave";
    public $iconPath = "ThoughtWave.png";    

	public $ballistic = true;	
    public $range = 100;
    public $firingMode = 1;
    public $priority = 7;
    public $loadingtime = 3;
	public $hextarget = true;
	public $hidetarget  = true;	
    public $useOEW = false;
	public $noLockPenalty = false;
    
    public $doNotIntercept = true; 		    		          
    public $uninterceptable = true;
   	public $ignoreJinking = true;//weapon ignores jinking completely.
        
    public $rangePenalty = 0.33; 
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 

	public $damageType = "Flash"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!   
	public $weaponClass = "Plasma"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	public $rangeDamagePenalty = 1;	

    public $animation = "ball";
    public $animationExplosionScale = 2;   
	public $animationColor = array(188, 55, 130);
	public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.
	
	public $firingModes = array(1=> "Thought Wave"); 	
	public $autoFireOnly = true; //this weapon cannot manually fire by player at a target, just activated	

	public $output = 15;//Is actually used as the base hit chance, but can be modified by critical hits.	
	private $diceRollonTurn = null;	
   	//protected $noTargetHexIcon = true; //For Front End Hex icon display.	


    protected $possibleCriticals = array(
	    21=>array("OutputReduced1", "OutputReduced1", "OutputReduced1", "OutputReduced1", "OutputReduced1")
    );
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output){
		if ( $maxhealth == 0 ) $maxhealth = 9;
		if ( $powerReq == 0 ) $powerReq = 8;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output);
		$this->output = $output;
	}    
		

    public function beforeFiringOrderResolution($gamedata){

      $firingOrders = $this->getFireOrders($gamedata->turn);
    	
      $originalFireOrder = null;
              foreach ($firingOrders as $fireOrder) { 
              	   if ($fireOrder->type == 'ballistic') { 
                    $originalFireOrder = $fireOrder;
                    break; //no need to search further
                    }
				}    			
			
        if($originalFireOrder==null) return; //no appropriate fire order, end of work
	
    	$thisShip = $this->getUnit();  	
    				
		//Have Thought Wave originate from usual ballistic location.	
		$shipStartLoc = $thisShip->getLastTurnMovement($gamedata->turn);
		$startPosition = $shipStartLoc->position;
		$originalFireOrder->x = $startPosition->q;
		$originalFireOrder->y = $startPosition->r;
		
		//Correct any errors.
		if ($originalFireOrder->targetid != -1) {
			$originalFireOrder->targetid = -1; //correct the error
			$originalFireOrder->calledid = -1; //just in case
		}
	
		//find all units in target area, to declare firing orders vs them...    	
    	$allShips = $gamedata->ships;
    	$relevantShips = array();

		//Make a list of relelvant ships e.g. all enemy ships.
		foreach($allShips as $ship){
			if($ship->isDestroyed()) continue;		
			if ($ship->faction == "Mindriders") continue;//Mindriders not affected.
			if ($ship->isTerrain()) continue;				
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore targets that are not deployed yet!				
			$relevantShips[] = $ship;			
		}
	
		foreach($relevantShips as $target){
			$this->prepareFiringOrder($thisShip, $target, $gamedata, $originalFireOrder);							
		}
				 	
	} //endof beforeFiringOrderResolution


	private function prepareFiringOrder($shooter, $target, $gamedata, $originalFireOrder){

		$newFireOrder = new FireOrder(
			-1, "normal", $shooter->id, $target->id,
			$this->id, -1, $gamedata->turn, 1, 
			0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
			$originalFireOrder->x,$originalFireOrder->y,$this->weaponClass,-1 //X, Y, damageclass, resolutionorder
		);		
		$newFireOrder->addToDB = true;
		$this->fireOrders[] = $newFireOrder;
			
	}//endof function prepareFiringOrders		          

	public function calculateHitBase($gamedata, $fireOrder){
		
		if($fireOrder->targetid == -1) { //initial "targeting location" Thought Wave shot should not actually be resolved
			$fireOrder->needed = 0;	//just so no one tries to intercept it				
			$fireOrder->updated = true;
			$fireOrder->notes .= 'Thought Wave aiming shot, not resolved.';
			return;
		}

		//Direct shot - Thought Wave specific routine!
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
		$launchPos = $this->getFiringHex($gamedata, $fireOrder);
		$targetPos = $target->getHexPos();

        $rangePenalty = 0;
        $oew = 0;
		$dew = 0;
		$distanceForPenalty = mathlib::getDistanceHex($launchPos, $targetPos);

		$rangePenalty = $this->calculateRangePenalty($distanceForPenalty);//Straight rp, no need to consider Jammers (unless Torvalus added!)		
		$oew = $shooter->getOEW($target, $gamedata->turn);
        $dew = $target->getDEW($gamedata->turn);

		if($this->diceRollonTurn == null){//Roll once for entire turn.
			$rollD20 = Dice::d(20);
			$this->diceRollonTurn = $rollD20;						
		}
		
		$diceRoll = $this->diceRollonTurn;//Take d20 roll
		$initiativeBase = max(0, $target->iniative / 5); //Target initiative convert FROM a %.  Don't go into negative.
		$initiative = round($initiativeBase);		

        $hitLoc = null;
        $hitLoc = $target->getHitSectionPos(mathlib::hexCoToPixel($launchPos), $fireOrder->turn);

        $fireOrder->chosenLocation = $hitLoc;
        $output = $this->getOutput();
        $result = round($output - $rangePenalty + $oew - $dew - $initiative + $diceRoll);//basehit - rp + OEW - DEW - target ini + d20
        
		if($result > 0){ //Automatically hits if calculation result is above 0.
        	$fireOrder->needed = 100;
		}else{
			$fireOrder->needed = 0;			
		}
		$fireOrder->notes .= 'Thought Wave direct shot.';
		$fireOrder->pubnotes .= ' Hit Roll: ' . $result . '.';		        
        $fireOrder->updated = true;		
					
	}  //end of calculateHitBase() 


    public function fire($gamedata, $fireOrder){

		if($fireOrder->needed <= 0 && $fireOrder->targetid != -1){ //Don't resolve direct shots which don't hit.
			return;				
		}
		//Standard routines for everything!
		parent::fire($gamedata, $fireOrder);
	}


    protected function getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder)
    {
		$damage = 0;//Intialise.
		
		if($fireOrder->targetid != -1){//Direct fire shot.
			$diceRoll = Dice::d(6, 3); //3-18
            $defence = $target->getHitSectionProfilePos(mathlib::hexCoToPixel($pos));//Base profile.
        	$mod = $target->getHitChanceMod($shooter, $pos, $gamedata->turn, $this); //Shields/E-web etc affect profile!
			$fireOrder->pubnotes .= ' Damage Roll: ' . $diceRoll . '/18.';	
			$fireOrder->pubnotes .= ' Profile/Mod: ' . $defence . '/' . $mod . '. ';					
					        	           
            if($target->advancedArmor){//Divide by 5 for AA
				$damage = floor(floor($diceRoll/5) * ($defence + $mod)); //3d6 divide by 5, multiplied by defence profile. 
            }else{//Divide by 3 for everything else							
				$damage = floor(floor($diceRoll/3) * ($defence + $mod)); //3d6 divide by 3, multiplied by defence profile.
			}				
		}
		$fireOrder->pubnotes .= ' Damage before Mods: ' . $damage . '. ';
		
        $damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata); //E.g. plasma -damage for range          
        $damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);// e.g. enemy shields.

		$damageForLog = max(0,$damage);			
		$fireOrder->pubnotes .= '  Final Damage: ' . $damageForLog . '.';
					
        return $damage;
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Damage"] = 'Special';	
		$this->data["Special"] = 'Fire this weapon by clicking Select during  the Initial Orders phase.';
		$this->data["Special"] .= '<br><br>The Thought Wave will always originate from the starting location of the firing ship (as per usual with ballistic weapons).';
		$this->data["Special"] .= '<br>The Thought Wave will attempt to hit ALL non-Mindrider ships in the game in Firing Phase (even friendlies), using the following formula:';
		$this->data["Special"] .= '<br> - (15 + OEW + d20) - (Range Penalty + DEW - Target Initiative/3)';
		$this->data["Special"] .= '<br>If this formula returns a result above 0, the Thought Wave automatically hits, and deals (3D6/3) * (Profile/5) Flash damage (-' . $this->rangeDamagePenalty . ' per hex as per usual Plasma rules).';
		$this->data["Special"] .= '<br>Advanced Armor changes this formula to (3d6/5) * (Profile/5), and Shields etc affect profile as normal for this calculation.';			
		$this->data["Special"] .= '<br>Will only strike 1 fighter in a flight, but Flash damage may still affect other fighters.';
		$this->data["Special"] .= '<br>Note - Only successful attacks will appear in the Combat Log.';								
	}	

    public function getDamage($fireOrder){ return 0;   }//Initial shot doesn't actually do any damage.
    public function setMinDamage(){     $this->minDamage = 0 ;      }
    public function setMaxDamage(){     $this->maxDamage = 0 ;      }

	/*
 	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->noProjectile = $this->noProjectile;
		//$strippedSystem->noTargetHexIcon = $this->noTargetHexIcon;																			
		return $strippedSystem;
	}         
	*/
} //endof class ThoughtWave


?>
