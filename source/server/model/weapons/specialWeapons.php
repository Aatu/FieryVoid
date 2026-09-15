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
			if($ship instanceof Mine) return true;
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
			if (($damage+$armour)>0){//ensure that some damage was actually dealt - if entire rake is blocked by defensive system, then it has no effect						 
				$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true; //in effect immediately, affecting further damage in the same turn!
				$system->setCritical($crit); //$system->criticals[] =  $crit;			
			}
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
				if ($target instanceof Mine) continue;			
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
	private $damageMod = 0;
	
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
			  $ship = $this->getUnit();
			  if($ship instanceof Mine){
				$this->data["Special"] = "This weapons automatically affects all units (friend or foe) in area of effect.";  
				$this->data["Special"] .= "<br>It cannot be fired manually."; 
				$this->data["Special"] .= "<br>Ignores armor, but cannot damage ship structure.";  
				$this->data["Special"] .= "<br>Deals 1-6 damage with a range of 4 hexes.";  
				$this->data["Special"] .= "<br>If multiple Spark Fields overlap, only strongest will attack."; 			
			  }else{
				$this->data["Special"] = "This weapons automatically affects all units (friend or foe) in area of effect.";  
				$this->data["Special"] .= "<br>It cannot be fired manually."; 
				$this->data["Special"] .= "<br>Ignores armor, but cannot damage ship structure.";  
				$this->data["Special"] .= "<br>Base damage is 1d6+1, range 2 hexes.";  
				$this->data["Special"] .= "<br>Can be boosted, for +2 AoE and -1 damage per level."; 
				$this->data["Special"] .= "<br>If multiple Spark Fields overlap, only strongest will attack."; 		
				$this->data["Special"] .= "<br>With CUSTOM Spark Curtain enhancement acts as anti-Ballistic shield (reducing hit chance only, by 2+boost)."; 
			  }
		}	//endof function setSystemDataWindow
	
	
	
	public function getAoE($turn){
		$boostlevel = $this->getBoostLevel($turn);
		$aoe = $this->baseOutput +(2*$boostlevel);
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
	

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $baseOutput = 2, $damageMod = 0, $boostable = true)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 2;
		}
		//Some settings can be different from Mine version.
		$this->baseOutput = $baseOutput; 
		$this->damageMod -= $damageMod; 	
		$this->boostable = $boostable;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		SparkFieldHandler::addSparkField($this);//so all Spark Fields are accessible together, and firing orders can be uniformly created
	}
	
	// ignore armor; advanced armor halves effect (due to weapon being Electromagnetic)
	public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
		if ($system->advancedArmor){
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
		$damageRolled = Dice::d(6, 1)+1 + $this->damageMod; //damageMod is for Mine version, which is set at 1d6 and can't be boosted.
		$boostlevel = $this->getBoostLevel($fireOrder->turn);
		$damageRolled -= $boostlevel; //-1 per level of boost
		$damageRolled = max(0,$damageRolled); //cannot do less than 0	
		return $damageRolled;   
	}
        public function setMinDamage(){    
		$this->minDamage = 2 + $this->damageMod ;	      		
	}
        public function setMaxDamage(){   
		$this->maxDamage = 7 + $this->damageMod ;	    
	}	

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->noProjectile = $this->noProjectile;
		$strippedSystem->baseOutput = $this->baseOutput;																	
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
		      $this->data["Special"] .= "<br>If you allocate multiple Surge Cannons in higher mode of fire at the same target, they will be combined."; 
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
		if($shooter->isTerrain() && !$shooter->isDestroyed() && $shooter->Enormous){ //Only Enormous terrain (asteroids/moons) collides; bases like shipyards/jumpgates do not.
			$relevantShips = array();

			//Make a list of relevant ships e.g. this ship and enemy fighters in the game.
			foreach($gamedata->ships as $ship){
				if($ship->isDestroyed()) continue; //Ignore destroyed ships
				if($ship->isTerrain()) continue;	//Don't add other terrain.
				if($ship instanceof spawnMeteoroid || $ship instanceof spawnDustField) continue; // GTS_Triad
				if($ship->getTurnDeployed($gamedata) > $gamedata->turn)	continue; //Ship not deployed yet.		
				if($this->isPhasedThroughTerrain($ship, $shooter, $gamedata)) continue; //Half-phased Shadow ships slip straight through terrain, provided they don't stop inside it.
					//if ($ship instanceof FighterFlight && $shooter->Huge == 0) continue; //Not doing fighters except for very large terrain, change if and when skindancing introduced.	
				$relevantShips[] = $ship;			
			}

			$terrrainPosition = $shooter->getHexPos();
			$collisiontargets = $this->checkForCollisions($relevantShips,  $gamedata, $terrrainPosition, $shooter);

			foreach($collisiontargets as $targetid=>$location){
				$target = $gamedata->getShipById($targetid);
                if($targetid == $shooter->id) continue; // GTS_Triad - prevent terrain firing at itself
				
				$type = "TerrainCollision"; //Moving through asteroids hex, d10 * speed damage.
				if($shooter->Huge > 0 ) $type = "TerrainCrash"; //Larger Terrain, like Moons.  Full ramming damage.
                if(isset($shooter->terrainCollisionType)) $type = $shooter->terrainCollisionType; // GTS_Triad
				$targetMovement = $target->getLastTurnMovement($gamedata->turn+1);
				if (!$targetMovement) continue;

				if ($target instanceof FighterFlight && $type === "TerrainCrash") {
					$first = true; // Flag to track the first entry
				
					foreach ($target->systems as $fighter) {                          
						$newFireOrder = new FireOrder(
							-1, "prefiring", $shooter->id, $target->id,
							$this->id, $fighter->id, $gamedata->turn, 1,
							0, 0, 1, 0, 0,
							$targetMovement->position->q, $targetMovement->position->r, $type, -1
						);
//						$newFireOrder->chosenLocation = $location;                
$newFireOrder->chosenLocation = $location ?: 1;
$newFireOrder->notes = "loc:" . ($location ?: 1);
				
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
//					$newFireOrder->chosenLocation = $location;									
$newFireOrder->chosenLocation = $location ?: 1;
$newFireOrder->notes = "loc:" . ($location ?: 1);
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
			if (isset($shooter->attached[$targetID])) continue; // Already attached to this Enormous unit, do not ram!
			if($shooter->hasSpecialAbility("Attaches") && !$shooter instanceof Terrain) continue; //ignore pods/grapple ships for now as we assume they are attaching.						
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
				if (!$movementThisTurn) continue;
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
				if (!$movementThisTurn) continue;
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
		//if($target->hasSpecialAbility("Attaches") && !$shooter instanceof Terrain) return 'Aborted';; //Assume that attachable ships are trying to attach so treat as aborted skindance, but not to Terrain.		
		
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


	//Every hex a Terrain unit occupies: its centre hex, plus the extra hexes given by an irregular hexOffsets shape or by a circular Huge radius.
	//Public static so other weapons that need a terrain unit's whole footprint can reuse it (PlanetCrackerBeam sweeps it) - it never touched $this.
	public static function getTerrainOccupiedHexes($terrain){
		$terrainPosition = $terrain->getHexPos();
		$occupiedHexes = array($terrainPosition); //Centre hex is always occupied.

		if (property_exists($terrain, 'hexOffsets') && !empty($terrain->hexOffsets)) {
			// 1. Irregular Shape defined by hexOffsets - hexOffsets lists only the EXTRA hexes
			$move = $terrain->getLastMovement();
			$facing = $move ? $move->facing : 0;
			foreach ($terrain->hexOffsets as $offset) {
				// Use accurate pixel-based rotation to get absolute hex position
				$occupiedHexes[] = Mathlib::getRotatedHex($terrainPosition, $offset, $facing);
			}
		} else if ($terrain->Huge > 0) {
			// 2. Standard Circular Shape defined by Huge radius
			foreach (Mathlib::getNeighbouringHexes($terrainPosition, $terrain->Huge) as $n) {
				$occupiedHexes[] = new OffsetCoordinate($n['q'], $n['r']);
			}
		}

		return $occupiedHexes;
	}//endof getTerrainOccupiedHexes()


	//Shadow Association ships that half-phase during their movement pass harmlessly through terrain - but only if they don't END that movement inside it.
	private function isPhasedThroughTerrain($ship, $terrain, $gamedata){
		if($ship->faction !== 'Shadow Association') return false;
		if(!Movement::isHalfPhased($ship, $gamedata->turn)) return false;

		$finalMove = $ship->getLastTurnMovement($gamedata->turn+1); //last move of THIS turn, ie. where the ship came to rest
		if(!$finalMove) return false;

		foreach(self::getTerrainOccupiedHexes($terrain) as $hex){
			if($hex->q == $finalMove->position->q && $hex->r == $finalMove->position->r) return false; //Stopped in the terrain - phasing does not save it.
		}

		return true;
	}//endof isPhasedThroughTerrain()


	/* Did this movement row take the unit into a NEW hex?

	   move/slipleft/slipright are the only plotted types that change hex, so for a unit
	   flying under its own power the type IS the test. An ATTACHED unit has no such rows:
	   MovementGamePhase copies its host's entire plot onto it under the single type
	   'attached' (specialWeapons is not alone in tripping over this - the mine detectors do
	   too), so a breaching pod or grapple ship was carried through an asteroid field
	   completely unharmed. For those rows compare positions instead, which is exactly
	   equivalent, and keep reading the ATTACHED unit's own rows rather than its host's: the
	   hexes are identical either way, but the mirrored facing carries the pod's attachment
	   offset and that is what decides which of the POD's sections the collision strikes.

	   The host is not double-damaged by this: it earns its own collision order from the same
	   loop, and Firing::fire refuses to spill a TerrainCollision/TerrainCrash order from a
	   pod onto its host precisely so the two do not stack. */
	private static function isHexTransit($move, $previousPosition){
		if ($move->type == "move" || $move->type == "slipleft" || $move->type == "slipright") return true;
		if ($move->type != "attached") return false;
		if ($previousPosition === null) return true;

		return ($move->position->q != $previousPosition->q || $move->position->r != $previousPosition->r);
	}//endof isHexTransit()


	private function checkForCollisions($relevantShips, $gamedata, $terrainPosition, $thisShip){
	    $collisiontargets = array(); // Initialize array for fighters to be fired at.
		//$thisShip = $this->getUnit();
		
		if ($thisShip->Huge > 0 || (property_exists($thisShip, 'hexOffsets') && !empty($thisShip->hexOffsets))) {  //Terrain occupies more than just 1 hex!  Need to check all of its hexes.
			// Terrain logic: Build list of ALL occupied hexes first.
			$occupiedHexes = self::getTerrainOccupiedHexes($thisShip);

			foreach ($relevantShips as $ship) {  
				$startMove = $ship->getLastTurnMovement($gamedata->turn);
				if (!$startMove) continue;
				$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.
				$previousFacing = $startMove->getFacingAngle();
		
				foreach ($ship->movement as $shipMove) {
					if ($shipMove->turn == $gamedata->turn) {
						if (self::isHexTransit($shipMove, $previousPosition)) {

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
				if (!$startMove) continue;
				$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.			 
				$previousFacing = $startMove->getFacingAngle();			

				foreach ($ship->movement as $shipMove) {
					if ($shipMove->turn == $gamedata->turn) {
			
						// Only interested in moves where ship enters a NEW hex!
						if (self::isHexTransit($shipMove, $previousPosition)) {
							// Check if the position matches the asteroids, e.g. zero distance.
							if ($terrainPosition->q == $shipMove->position->q && $terrainPosition->r == $shipMove->position->r) {
								$relativeBearing = $this->getTempBearing($previousPosition, $terrainPosition, $ship, $previousFacing);
								$location = $this->getCollisionLocation($relativeBearing, $ship);
								$collisiontargets[$ship->id] = $location; // Add to array to be targeted.
							}
						}

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
			if($fireOrder->damageclass == "TerrainCollision" || $fireOrder->damageclass == "TerrainCrash" || $fireOrder->damageclass == "MeteoroidCollision" || $fireOrder->damageclass == "DustCollision" || $fireOrder->damageclass == "WaveformCollision" || $fireOrder->damageclass == "SingularityCollision"){ // GTS_Triad
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;
			//Skip parent as auto-hit, but also stop us overwriting chosenLocation which has already been set.
		}else{
			parent::calculateHitBase($gamedata, $fireOrder);			
		}
	}

	private function getRamHitLocation($ship, $gamedata, $shipPosition){
				if($ship->getSpeed() == 0) return 1; //Just return front location as standard if Ship is not moving.
				// Now check other movements in the turn.
				$startMove = $ship->getLastTurnMovement($gamedata->turn);	//initialise as last move in previous turn, in case first move takes ship in asteroid.
				if (!$startMove) return 1;
				$previousPosition = $startMove->position; //This will change as we go through movements, but need to initialise as where the ship starts this turn.
				$previousFacing = $startMove->getFacingAngle();
				$location = 0;

				foreach ($ship->movement as $shipMove) {
					if ($shipMove->turn == $gamedata->turn) {

						// Only interested in moves where ship enters a NEW hex!
						if (self::isHexTransit($shipMove, $previousPosition)) {
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


	//For TerrainCrash return damage: compute hit location on the TERRAIN (not the colliding ship) based on bearing from terrain centre to the ship's incoming hex, in the terrain's frame of reference.
	private function getTerrainReturnHitLocation($terrain, $ship, $gamedata){
		$terrainPosition = $terrain->getHexPos();
		$terrainMove = $terrain->getLastMovement();
		$terrainFacing = $terrainMove ? $terrainMove->getFacingAngle() : 0;

		//Build list of occupied hexes (mirrors checkForCollisions logic).
		$occupiedHexes = [];
		if (property_exists($terrain, 'hexOffsets') && !empty($terrain->hexOffsets)) {
			$occupiedHexes[] = $terrainPosition; // Center hex is always occupied; hexOffsets lists only the EXTRA hexes
			foreach ($terrain->hexOffsets as $offset) {
				$occupiedHexes[] = Mathlib::getRotatedHex($terrainPosition, $offset, $terrainFacing);
			}
		} else {
			$occupiedHexes[] = $terrainPosition;
			if ($terrain->Huge > 0) {
				foreach (Mathlib::getNeighbouringHexes($terrainPosition, $terrain->Huge) as $n) {
					$occupiedHexes[] = new OffsetCoordinate($n['q'], $n['r']);
				}
			}
		}

		$startMove = $ship->getLastTurnMovement($gamedata->turn);
		if (!$startMove) return 1;
		$previousPosition = $startMove->position;

		foreach ($ship->movement as $shipMove) {
			if ($shipMove->turn != $gamedata->turn) continue;
			if (self::isHexTransit($shipMove, $previousPosition)) {
				foreach ($occupiedHexes as $hex) {
					if ($hex->q == $shipMove->position->q && $hex->r == $shipMove->position->r) {
						//Bearing FROM terrain centre TO ship's prior hex, relative to terrain's facing.
						$relativeBearing = $this->getTempBearing($terrainPosition, $previousPosition, $terrain, $terrainFacing);
						return $this->getCollisionLocation($relativeBearing, $terrain);
					}
				}
			}
			$previousPosition = $shipMove->position;
		}

		return 1; //Safe default matching MediumShip front.
	}//endof getTerrainReturnHitLocation()


//	public function fire($gamedata, $fireOrder){
		// If hit, firing unit itself suffers damage, too (based on ramming factor of target)!
//		$this->gamedata = $gamedata;			
	public function fire($gamedata, $fireOrder){
		// Restore chosenLocation from notes if lost between requests
		if (empty($fireOrder->chosenLocation) && !empty($fireOrder->notes)) {
			if (preg_match('/loc:(\d+)/', $fireOrder->notes, $m)) {
				$fireOrder->chosenLocation = (int)$m[1];
			}
		}
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

if($fireOrder->damageclass == 'WaveformCollision' && $this->getDamage($fireOrder) <= 0) return; // GTS
// Skip duplicate dust fire orders silently - only first hex counts
if ($fireOrder->damageclass == 'DustCollision') {
    if (isset(spawnDustField::$dustDamagedThisTurn[$fireOrder->targetid]) && 
        spawnDustField::$dustDamagedThisTurn[$fireOrder->targetid] == $gamedata->turn) {
        $fireOrder->shotshit = 0;
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

			//The chosenLocation set in beforePreFiringOrderResolution is the colliding ship's location and was used by parent::fire() to damage the ship correctly.
			//For return damage, we now switch chosenLocation to refer to the unit taking it (this->unit).
			if($fireOrder->damageclass == 'TerrainCrash') {
				//Return damage lands on the terrain itself, not the colliding ship - recompute against terrain's own location chart.
				$collider = $gamedata->getShipById($fireOrder->targetid);
				if($collider) {
					$fireOrder->chosenLocation = $this->getTerrainReturnHitLocation($target, $collider, $gamedata);
				}
			} else if($fireOrder->damageclass != 'TerrainCollision' && $fireOrder->damageclass != 'MeteoroidCollision' && $fireOrder->damageclass != 'DustCollision') { // GTS_Triad
				$fireOrder->chosenLocation = $this->getRamHitLocation($target, $gamedata, $targetPos);
			}
			//TerrainCollision (asteroids): return damage stays 0 via damageModRolled, so chosenLocation is not used meaningfully.
//	if($fireOrder->damageclass == 'MeteoroidCollision' || $fireOrder->damageclass == 'DustCollision' || $fireOrder->damageclass == 'WaveformCollision') return; // GTS_Triad
if($fireOrder->damageclass == 'MeteoroidCollision' || $fireOrder->damageclass == 'DustCollision' || $fireOrder->damageclass == 'WaveformCollision' || $fireOrder->damageclass == 'SingularityCollision') return; // GTS_Triad

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
		//Debug::log("fireOrder->chosenLocation4 " . $fireOrder->chosenLocation);		
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
			
			
        }else if($fireOrder->damageclass == 'MeteoroidCollision'){ // GTS
            $targetMove = $target->getLastMovement(); // GTS
            $targetSpeed = $targetMove ? $targetMove->speed : 1; // GTS
            $modifier = 0; // GTS
            if($targetSpeed <= 4) $modifier = -1; // GTS
            if($targetSpeed > 12) $modifier = 1; // GTS
            $hits = spawnMeteoroid::rollMeteorChart($target->shipSizeClass, ($target instanceof FighterFlight), $modifier); // GTS
            if($hits <= 0) return 0; // GTS
            $damage = 0; // GTS
            for($h = 0; $h < $hits; $h++) $damage += spawnMeteoroid::getMeteorDamage($targetSpeed); // GTS
            if(empty($target->advancedArmor)) $damage *= 2; // GTS - double damage for non-advanced armor
            return $damage; // GTS

		}else if($fireOrder->damageclass == 'DustCollision'){ // GTS
            if(isset(spawnDustField::$dustDamagedThisTurn[$target->id]) && spawnDustField::$dustDamagedThisTurn[$target->id] == $gd->turn) return 0; // GTS
            spawnDustField::$dustDamagedThisTurn[$target->id] = $gd->turn; // GTS
            $targetMove = $target->getLastMovement(); // GTS
            $targetSpeed = $targetMove ? $targetMove->speed : 0; // GTS
            $damage = spawnDustField::getDustDamage($targetSpeed); // GTS
            if(empty($target->advancedArmor)) $damage *= 2; // GTS - double damage for non-advanced armor
            return $damage; // GTS



		}else if($fireOrder->damageclass == 'WaveformCollision'){ // GTS_Triad
        $targetMove = $target->getLastMovement(); // GTS
        $targetSpeed = $targetMove ? $targetMove->speed : 0; // GTS
        if($targetSpeed <= 0) return 0; // GTS
        $damage = SpatialCutter::getWaveformDamage($target); // GTS
        if(empty($target->advancedArmor)) $damage *= 2; // GTS
        return $damage; // GTS

		}else if($fireOrder->damageclass == 'SingularityCollision'){ // GTS_Triad
            return 0; // GTS_Triad — damage handled in SingularityRammingAttack::beforeDamage
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
$fireOrder->updated = true;
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
		if($system->advancedArmor) return;
		if($ship instanceof Mine) return;		
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





/* ============================ THE VORTEX DISRUPTOR ==========================================
 *
 * A Shadow weapon built for one job: to fire into a jump point and tear it apart, killing whatever
 * is passing through it at that moment. Until 2026-08-29 there were no jump points in FV, so the
 * class rolled to hit an arbitrary hex and printed a sentence; Jump Points Phase 1-3 gave the game
 * real ones, and this is the weapon connected to them (user ruling 2026-08-29).
 *
 * WHAT IT DOES, in the order it happens (all of it inside FireGamePhase::advance):
 *
 *   1. fire() rolls to hit the HEX. Base 24, less 1 per hex of range, converted to d100. EW is
 *      irrelevant and there is no target ship - a player may perfectly well shoot at an empty hex,
 *      and the rules say so explicitly. A MISS has no effect whatsoever.
 *   2. On a hit, the hex is searched for a jump point - either colour, and FORMING counts as well
 *      as open (see getDisruptableVortexInHex). Nothing there: no effect, and the log says so.
 *   3. The doorway is marked collapsing (JumpEngine::disruptVortex). It then closes at the end of
 *      THIS turn whatever else was keeping it open - a Maintain declaration, a gate's programmed
 *      multi-turn hold - because JumpEngine::getVortexClosureReason answers for the disruption
 *      ahead of every other branch.
 *   4. Whatever was in transit through it is destroyed outright. No damage roll, no allocation:
 *        - a YELLOW ENTRANCE killed the units that flew into it during THIS turn's Movement phase
 *          (they are already off the board with a HyperspaceJump damage entry - see
 *          getDeparturesThrough for how "left the battle" is turned back into "died");
 *        - a BLUE EXIT killed the reinforcements riding it, who are still in hyperspace waiting to
 *          come through on a later turn.
 *   5. "ANCIENT JUMP DRIVES CANNOT BE AFFECTED BY VORTEX DISRUPTORS" (user ruling 2026-09-11). A
 *      doorway an Ancient special jump drive holds (JumpEngine::$ancientJump) does not collapse, and
 *      an Ancient unit inside any other doorway is spared without a roll - see isImmuneToDisruption.
 *      The Vorlons and The System, which keep ordinary vortex-opening engines, are the exception.
 *   6. Those two roll to slip through the collapsing rift first - see rollAncientEscape.
 *
 * ⚠️ ALL OF THIS LIVES IN fire(), NOT IN beforeFiringOrderResolution(). The hook runs from
 * Firing::prepareFiring, which calls it BEFORE calculateHitBase - so at that point the order has
 * neither a to-hit number nor a roll, and both are needed here: the shot has to hit before anything
 * happens, and the MARGIN by which it hit is an input to the ancient-drive escape roll. fire() is
 * also where the effect is idempotent for free (Firing::fire returns early on
 * `$fire->rolled > 0`), which is the same self-persisting-effect pattern ShadowFighterBomb uses
 * below. The one ordering this DOES depend on is that Firing::fireWeapons runs before
 * JumpEngine::closeExpiredVortices in FireGamePhase::advance, which it does, a few lines apart.
 *
 * ⚠️ HALF-PHASING STILL SWITCHES THE WEAPON OFF (hit chance 0 in calculateHitBase). Since
 * 2026-08-29 the client refuses the targeting gesture outright rather than letting the player spend
 * a shot on a certain miss - weaponManager.targetHex.
 * ===========================================================================================
 */
class VortexDisruptor extends Weapon{
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

	/* THE DAMAGECLASS THE COLLAPSE KILLS WITH, and it is load-bearing rather than a label.
	   A unit that flew into a jump point this turn is ALREADY off the board carrying a
	   'HyperspaceJump' damage entry, and both "did it leave or did it die?" tests -
	   JumpEngine::hasJumped and BaseShip::hasHyperspaceJumpDamage - work by SUBTRACTING the
	   HyperspaceJump entries and asking whether what remains is enough to have killed it. So the
	   one thing this string must not be is 'HyperspaceJump': anything else flips both tests, the
	   client mirror (shipManager.hasJumpedNotDestroyed) and the combat value with them.
	   ⚠️ it must also stay OUT of Firing::isHyperspaceLogOrder - that is a FIRE ORDER filter,
	   and the disruptor's order is a real shot that must keep being gathered and resolved. */
	const COLLAPSE_DAMAGECLASS = 'VortexCollapse';

	/* THE AGE AT WHICH A DRIVE CAN OUTRUN THE COLLAPSE. "factionAge 3+ ships use a slightly
	   advanced form of jump engine, developed in response to the vortex disruptor" - the same
	   threshold JumpEngine::openVortex already uses to halve an Ancient's jump-failure chance.
	   ⚠️ Since 2026-09-11 almost every Ancient is spared outright before this is asked
	   (isImmuneToDisruption), so the escape roll is only ever reached by the factions below. */
	const ANCIENT_FACTION_AGE = 3;

	/* THE ANCIENT FACTIONS A VORTEX DISRUPTOR STILL REACHES (user ruling 2026-09-11): the only
	   factionAge 3+ factions whose engines still open B5 jump points. Every other Ancient is immune -
	   "Ancient jump drives cannot be affected by vortex disruptors". Matched against $unit->faction. */
	const DISRUPTABLE_ANCIENT_FACTIONS = array('Vorlon Empire', 'The System');


	//in pickup play it's essentially a power source - and Shadows don't have all that much use for extra power. Very low repair priority,although maybe above Hangars ;)
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired


	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Fired into a jump point - either an open ENTRANCE (yellow, units leaving) or an EXIT (blue, reinforcements arriving), including one that is still forming.";
		$this->data["Special"] .= "<br>On a hit collapses the jump point at end of turn, destroying anything transitting the jump point at the time.";
		$this->data["Special"] .= "<br>Ancient jump drives are unaffected: a jump point one holds cannot be collapsed, and Ancient units (Shadows, Kirishiac, Mindriders, Torvalus, Triad, Thirdspace, Walkers) inside any jump point are spared.";
		$this->data["Special"] .= "<br>Vorlon and System hulls may escape: roll 1d100 against the shot's to-hit margin plus the distance*5 ship travelled this turn, escaping on equal or higher.";
		$this->data["Special"] .= "<br>A miss has no effect at all and cannot fired while half-phased.";
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
			$fireOrder->shotshit++;
			$this->disruptJumpPoint($gamedata, $fireOrder, $shooter);
		}else{ //MISS!
			$fireOrder->pubnotes .= " MISSED! ";
		}
	} //endof function fire


	/* ⭐ THE WHOLE EFFECT, ONCE THE SHOT HAS HIT. Everything here is written into
	 * $fireOrder->pubnotes rather than into a log order of its own: the disruptor's order is an
	 * ordinary fire order the combat log already renders, and calculateHitBase has already set
	 * ->updated, so the notes ride back to tac_fireorder on FireGamePhase::advance's
	 * updateFireOrders call. */
	protected function disruptJumpPoint($gamedata, $fireOrder, $shooter)
	{
		$vortex = self::getDisruptableVortexInHex($gamedata, new OffsetCoordinate($fireOrder->x, $fireOrder->y));

		if (!$vortex){
			//An expected outcome, not an error: the shot is aimed at a HEX and the shooter may have
			//guessed wrong, or the jump point may have closed before the Firing phase came round.
			$fireOrder->pubnotes .= " HIT - but there was no jump point in the target hex, so nothing happened. ";
			return;
		}

		$isExit = ($vortex instanceof SpawnJumpPointExit);
		$engine = JumpEngine::getHoldingEngine($vortex, $gamedata);

		/* ⭐ "ANCIENT JUMP DRIVES CANNOT BE AFFECTED BY VORTEX DISRUPTORS" (user ruling 2026-09-11). A
		   doorway an Ancient drive holds does not collapse, and nothing riding it is touched. Asked
		   before the "already collapsing" test, because it never can be.
		   ⚠️ A PHASE-IN doorway - the only kind an Ancient drive opens, being a legacy drive - is never
		   drawn for anybody, so the log must not confirm that one is there: it reads exactly as a shot
		   into an empty hex. A VISIBLE one (opened before its drive became Ancient, in a game that spans
		   the deploy) says why nothing happened. */
		if ($engine && $engine->isAncientJump()){
			$fireOrder->pubnotes .= ($vortex instanceof SpawnJumpPointPhaseIn)
				? " HIT - but there was no jump point in the target hex, so nothing happened. "
				: " HIT - but the jump point is held by an Ancient jump drive, which a Vortex Disruptor cannot affect. ";
			return;
		}

		//A SECOND SHOT INTO THE SAME COLLAPSING DOORWAY disrupts nothing further, and more
		//importantly must not kill the same units twice - so it reports and stops. Same-turn only:
		//once the closure is recorded the jump point fails getDisruptableVortexInHex's window on
		//every later turn.
		if ($engine && $engine->isVortexDisrupted()){
			$fireOrder->pubnotes .= " HIT - the jump point was already collapsing. ";
			return;
		}

		if ($engine){
			$engine->disruptVortex($vortex->id);
			$fireOrder->pubnotes .= " HIT - the jump point " . ($isExit ? "exit" : "entrance")
				. " collapses and will be gone at the end of this turn.";
		}else{
			/* AN ORPHANED DOORWAY - its 'Vortex' note never arrived, or its holder's row has gone,
			   so no engine owns its lifecycle and closeExpiredVortices will never reach it. The
			   rift is still real and whatever is inside still dies, but do not PROMISE a closure
			   that no sweep is going to carry out: a log line the game then contradicts is worse
			   than one that says less. (getVortexClosureReason's 'vortex unit is gone' branch is
			   the same situation seen from the other end.) */
			$fireOrder->pubnotes .= " HIT - the jump point " . ($isExit ? "exit" : "entrance")
				. " is torn open.";
		}

		$victims = $isExit
			? self::getArrivalsThrough($vortex, $gamedata)
			: self::getDeparturesThrough($vortex, $gamedata);

		if (empty($victims)){
			$fireOrder->pubnotes .= " Nothing was passing through it. ";
			return;
		}

		$killed  = array();
		$escaped = array();
		$spared  = array();

		foreach ($victims as $group){
			//THE GROUP'S FATE IS THE HOST'S. An attached pod mirrors its host's movement and was
			//carried into hyperspace by it (Movement::resolveJumpOuts), so it cannot roll for
			//itself - it goes wherever the hull it is bolted to goes, whatever its own faction age.
			$host = $group[0];

			//"Ancient jump drives cannot be affected by vortex disruptors" - spared outright, with no
			//roll and no die drawn. See isImmuneToDisruption.
			if (self::isImmuneToDisruption($host)){
				$spared[] = $host->name;
				continue;
			}

			$distance = $isExit
				? 0   //still in hyperspace - it moved no distance to reach the doorway
				: self::getApproachDistance($host, $vortex->getHexPos(), $gamedata);

			if (self::rollAncientEscape($host, $fireOrder, $distance)){
				$escaped[] = $host->name;
				continue;
			}

			foreach ($group as $unit){
				$this->destroyInCollapse($unit, $gamedata, $shooter);
				$killed[] = $unit->name;
			}
		}

		if ($isExit){
			/* ⚠️ COUNTS, NEVER NAMES, ON THE EXIT SIDE. pubnotes is PUBLIC - it is the combat-log
			   sentence every player reads - and a wave that is still in hyperspace is concealed
			   down to a count and a point total by TacGamedata::hideHyperspaceReinforcements
			   (REINFORCEMENTS_PLAN.md §3.6: never classes, never names). These units never get an
			   arrival turn, so they are never declassified; printing what the disruptor killed
			   would hand the shooter the enemy's whole reserve order of battle as a reward for one
			   lucky shot. The count is already public per slot, so it discloses nothing new.
			   ⭐ The DAMAGE ROWS take care of themselves: combatLog walks gamedata.ships matching
			   fireorderid, and a masked unit is not in the payload at all, so an enemy sees the
			   shot with no victims under it while the owner sees exactly which of their ships died.
			   A DEPARTURE is the opposite case and is named below - those hulls were on the board
			   in front of everybody. */
			if (!empty($killed)){
				$fireOrder->pubnotes .= " " . count($killed)
					. (count($killed) == 1 ? " reinforcement was" : " reinforcements were")
					. " destroyed in hyperspace as it tore apart.";
			}
			if (!empty($escaped)){
				$fireOrder->pubnotes .= " " . count($escaped) . " slipped clear before it closed.";
			}
			if (!empty($spared)){
				$fireOrder->pubnotes .= " " . count($spared) . " unaffected - Ancient jump drive" . (count($spared) == 1 ? "." : "s.");
			}
		}else{
			if (!empty($killed))  $fireOrder->pubnotes .= " Destroyed in the rift: " . implode(", ", $killed) . ".";
			if (!empty($escaped)) $fireOrder->pubnotes .= " Slipped through before it closed: " . implode(", ", $escaped) . ".";
			if (!empty($spared))  $fireOrder->pubnotes .= " Unaffected (Ancient jump drive): " . implode(", ", $spared) . ".";
		}
		$fireOrder->pubnotes .= " ";
	}


	/* ⭐ THE JUMP POINT THIS SHOT CAN DISRUPT AT $pos, or null.
	 *
	 * ⚠️ DELIBERATELY WIDER THAN Movement::getOpenVortexInHex, IN BOTH DIRECTIONS, and the
	 * two must not be merged:
	 *
	 *   BOTH COLOURS. That reader is entrance-only because an exit is not a doorway OUT
	 *   (REINFORCEMENTS_PLAN.md section 2.6). This weapon shoots at whatever is there.
	 *
	 *   FORMING COUNTS. That reader refuses a vortex that has not formed yet because nothing can
	 *   fly into one. Here it is the opposite: "a jump point that is forming" is an explicit target
	 *   in the rule, and for a BLUE exit the forming turn is the ONLY turn on which the
	 *   reinforcements behind it are still in hyperspace to be killed. $spawned is openTurn + 1, so
	 *   `spawned <= turn + 1` is "open, or forming right now" and nothing beyond that - a doorway
	 *   declared for a later turn does not exist yet.
	 *
	 * The CLOSED half of the window is that reader's verbatim: removedTurn is the first turn the
	 * jump point is gone, so a vortex stays shootable for the whole of the turn it closes on. */
	public static function getDisruptableVortexInHex($gamedata, OffsetCoordinate $pos)
	{
		foreach ($gamedata->ships as $unit){
			if (!($unit instanceof SpawnJumpPoint)) continue;              //both colours - Exit extends this
			if ((int)$unit->spawned > (int)$gamedata->turn + 1) continue;  //not even forming yet
			if ($unit->removed && $unit->removedTurn !== null
				&& $gamedata->turn >= $unit->removedTurn) continue;        //already gone
			if (!$unit->getLastMovement()) continue;                       //no deploy row: no hex to compare
			if ($unit->getHexPos()->equals($pos)) return $unit;
		}

		return null;
	}


	/* ⭐ THE UNITS THAT FLEW INTO THIS YELLOW ENTRANCE DURING THIS TURN'S MOVEMENT PHASE, as groups of
	 * [host, ...units carried out with it].
	 *
	 * They are ALREADY GONE by the time the Firing phase runs - Movement::resolveJumpOuts removed
	 * them at the end of Movement - so this searches wreckage rather than the board: the unit has a
	 * 'jumpout' movement row naming this vortex, and it actually left (isDestroyed +
	 * hasJumpedToHyperspace, which is what tells a successful departure from one the server
	 * REFUSED, whose ship is still sitting on the map).
	 *
	 * ⚠️ AN ATTACHED POD HAS NO JUMP-OUT ORDER OF ITS OWN. Its movement rows are all type
	 * 'attached' and mirror the host's, so resolveJumpOuts takes it out under the host's order and
	 * it can only be found through $hasAttached - the same reasoning, and the same loop, as there. */
	protected static function getDeparturesThrough($vortex, $gamedata)
	{
		$groups = array();

		foreach ($gamedata->ships as $unit){
			if ($unit->isTerrain() || $unit->mine) continue;
			if (!self::hasLeftThroughVortex($unit, $vortex, $gamedata)) continue;

			$group = array($unit);

			if (!empty($unit->hasAttached)){
				foreach (array_keys($unit->hasAttached) as $attachedId){
					$attached = $gamedata->getShipById((int)$attachedId);
					if (!$attached) continue;
					if (!$attached->isDestroyed($gamedata->turn) || !$attached->hasJumpedToHyperspace()) continue;
					$group[] = $attached;
				}
			}

			$groups[] = $group;
		}

		return $groups;
	}

	/* Did $unit leave the battle through THIS jump point on THIS turn? */
	protected static function hasLeftThroughVortex($unit, $vortex, $gamedata)
	{
		$order = Movement::getJumpOutOrder($unit->movement, $gamedata->turn);
		if (!$order) return false;
		if ((int)$order->value !== (int)$vortex->id) return false;

		//The order alone is not proof: resolveJumpOuts re-validates the stored path and refuses one
		//that does not enter the vortex through its mouth. A refused unit never left.
		//An explicit turn, not the bare isDestroyed(): the no-argument form falls back to
		//TacGamedata::$currentTurn, and the two are only the same thing when the static happens to
		//have been set. We have the turn right here.
		return ($unit->isDestroyed($gamedata->turn) && $unit->hasJumpedToHyperspace());
	}


	/* ⭐ THE REINFORCEMENTS RIDING THIS BLUE EXIT, as one-unit groups.
	 *
	 * These have never been on the board: they are waiting in hyperspace with a berth naming the
	 * unit (or the gate) that opened the doorway, and they would have come out of it in the
	 * Deployment phase of a later turn. Collapsing it kills them there.
	 *
	 * ⚠️ THE JOIN IS arrivalVia -> vortexHolderId, and a NULL arrivalVia MEANS "ITS OWN
	 * DOORWAY" rather than "unassigned" - JumpEngine::getArrivalVortex documents why, and the opener
	 * always comes through the exit it opened (section 2.2). Reading null as unassigned here would
	 * spare the one unit that is unquestionably inside the jump point.
	 *
	 * ⚠️ TIMING - THIS RUNS BEFORE JumpEngine::stampExitManifests, so the wave is still
	 * isReinforcement() (arrivalTurn null) and has not been given its arrival turn yet. That is the
	 * right side of the boundary in both directions: a unit that ALREADY came through on an earlier
	 * turn has an arrivalTurn, fails isReinforcement(), and is correctly left alone - it is standing
	 * on the board, not inside the jump point. A gate's second and later waves are caught here on
	 * each turn of its hold, which is what the rule wants.
	 *
	 * ⚠️ alwaysDeploysTurnOne() - a gate or base that wrongly carries the reinforcement flag
	 * (a game bought before BuyingGamePhase refused it) is on the board and is not riding anything.
	 * Same guard, same reason, as stampArrivingReinforcements. */
	protected static function getArrivalsThrough($vortex, $gamedata)
	{
		if ($vortex->vortexHolderId === null) return array();

		$openerId = (int)$vortex->vortexHolderId;
		$groups   = array();

		foreach ($gamedata->ships as $unit){
			if (!$unit->isReinforcement()) continue;
			if ($unit->alwaysDeploysTurnOne()) continue;
			if ($unit->isDestroyed($gamedata->turn)) continue;

			$via = ($unit->arrivalVia === null) ? (int)$unit->id : (int)$unit->arrivalVia;
			if ($via !== $openerId) continue;

			$groups[] = array($unit);
		}

		return $groups;
	}


	/* ⭐ IS $unit OUT OF THE DISRUPTOR'S REACH ALTOGETHER? (user ruling 2026-09-11.)
	 *
	 * "Ancient jump drives cannot be affected by vortex disruptors" - and the Vorlons and The System,
	 * whose engines still open B5 jump points, are the only factionAge 3+ factions it still reaches.
	 * Either test is enough:
	 *   1. the unit CARRIES an Ancient special jump drive (JumpEngine::$ancientJump), destroyed or not -
	 *      found through getUnitJumpEngines, so a Mapmaker flight's is found one level down;
	 *   2. it is an Ancient of any faction but those two - which is what covers the drive-less units
	 *      of the Ancient-drive factions (a Torvalus Stiletto, a Triad Imp, a Kirishiac Warrior)
	 *      caught riding somebody else's doorway.
	 * Everything else goes on to rollAncientEscape exactly as before, which answers false at once for
	 * anything under factionAge 3. */
	protected static function isImmuneToDisruption($unit)
	{
		if (!$unit) return false;

		foreach (JumpEngine::getUnitJumpEngines($unit) as $engine){
			if ($engine->isAncientJump()) return true;
		}

		if ((int)$unit->factionAge < self::ANCIENT_FACTION_AGE) return false;
		return !in_array($unit->faction, self::DISRUPTABLE_ANCIENT_FACTIONS, true);
	}


	/* ⭐⭐ THE ANCIENT-DRIVE ESCAPE ROLL (user ruling 2026-08-29). True when $unit slips through before
	 * the rift closes and is spared.
	 *
	 * "factionAge 3+ ships, which use a slightly advanced form of jump engine developed in response
	 * to the vortex disruptor, have a chance to slip through the jump point before it collapses.
	 * Determine the difference between the attack die roll to hit the jump point and the required
	 * to-hit value, and add to this the distance the advanced race ship moved to reach the jump
	 * point on this turn. Now roll 1d20: equal to or greater than that total and the ship escapes."
	 *
	 * So a CLEANER hit is harder to survive, and so is a longer run-up: both terms make the total
	 * bigger and the d20 harder to make. A total of 1 or less is a certain escape, 21 or more is
	 * certain death.
	 *
	 * ⚠️ THE MARGIN IS MEASURED IN d20s, NOT IN THE d100 THE WEAPON ACTUALLY ROLLS. FV
	 * converts the B5W to-hit number to percent (needed = (24 - range) * 5) and rolls d100, so both
	 * sides are divided back down before they are subtracted - ceil() on the roll and round() on the
	 * target, which is the exact inverse of that conversion (a d100 of 1-5 IS a d20 of 1).
	 * Subtracting raw d100 values would produce margins around 60 and make every escape impossible.
	 *
	 * ⚠️ DISTANCE IS ZERO FOR AN ARRIVING REINFORCEMENT, and that is a real answer rather than
	 * a missing one: it is in hyperspace, it has no hex, and it moved no distance to reach the
	 * doorway. Its escape rides on the shot's margin alone. */
	protected static function rollAncientEscape($unit, $fireOrder, $distance)
	{
		if (!$unit || (int)$unit->factionAge < self::ANCIENT_FACTION_AGE) return false;

		$neededD100 = (int)round(((int)$fireOrder->needed)); //50
		$rolledD100 = (int)ceil(((int)$fireOrder->rolled)); //30
		$total     = ($neededD100 - $rolledD100) + ((int)$distance*5);

		return (Dice::d(100) >= $total);
	}

	/* HOW FAR $unit TRAVELLED THIS TURN TO REACH $vortexPos - "between start point and jump point,
	 * not just speed value", so it is measured from where the unit stood when the turn began, in
	 * hexes, ignoring the shape of the path.
	 *
	 * ⚠️ DBManager::getMovesForShips fetches turn 1, turn N-1, turn N and the deploy/start
	 * rows and nothing else, so the last row BEFORE this turn is always present for a unit that has
	 * been on the board - and the fallback (this turn's first row) covers one that arrived during
	 * it. */
	protected static function getApproachDistance($unit, OffsetCoordinate $vortexPos, $gamedata)
	{
		if (!$unit || !is_array($unit->movement) || empty($unit->movement)) return 0;

		$turn  = (int)$gamedata->turn;
		$start = null;

		foreach ($unit->movement as $move){
			if ((int)$move->turn < $turn){ $start = $move; continue; }
			if ((int)$move->turn > $turn) continue;
			if ($start === null) $start = $move;   //nothing earlier: the unit arrived this turn
		}

		if (!$start || !$start->position) return 0;

		//(int) because CubeCoordinate::distanceTo returns a FLOAT (its cube conversion divides), and
		//this number is about to be added to a d20 target. An int is what the rule means.
		return (int)(new OffsetCoordinate($start->position))->distanceTo($vortexPos);
	}


	/* ⭐ KILL ONE UNIT IN THE COLLAPSE. Movement::applyJumpOut's damage half, with two differences
	 * that are the entire point of the method:
	 *
	 *   1. THE DAMAGECLASS IS NOT 'HyperspaceJump' (see COLLAPSE_DAMAGECLASS). That is what turns a
	 *      unit the game has already recorded as having LEFT into one it records as DEAD - both
	 *      tests subtract the jump entries and ask whether the rest was fatal.
	 *   2. THE DAMAGE IS maxhealth, NOT getRemainingHealth(). It LOOKS wrong on a structure that is
	 *      already at zero, and it is the load-bearing half of point 1: a departing unit's
	 *      HyperspaceJump entry has already taken its remaining health, so a second entry sized on
	 *      what is left would be worth nothing at all and the ship would still read as having
	 *      escaped. maxhealth is the smallest value that is certainly fatal whatever else has
	 *      happened to the hull, including nothing at all (an untouched reinforcement).
	 *
	 * A FLIGHT HAS NO PRIMARY STRUCTURE - the same three-anchor problem applyJumpOut documents - so
	 * it is killed craft by craft, which is what FighterFlight::isDestroyed reads.
	 *
	 * No allocation, no criticals, no overkill: this is not a hit, it is the fabric of space coming
	 * apart. shooterid/weaponid are stamped so DBManager::submitDamages can find the disruptor's own
	 * fire order and hang the rows off it in the combat log (fireorderid is not known yet here).
	 * ⚠️ damage pubnotes are interpolated into SQL unescaped by submitDamages - keep the text
	 * free of apostrophes. */
	protected function destroyInCollapse($unit, $gamedata, $shooter)
	{
		if ($unit instanceof FighterFlight){
			foreach ($unit->systems as $craft){
				if ($craft->isDestroyed($gamedata->turn)) continue;
				$this->addCollapseDamage($unit, $craft, $gamedata, $shooter);
			}
			return;
		}

		$primaryStruct = $unit->getStructureSystem(0);
		if (!$primaryStruct) return;

		$this->addCollapseDamage($unit, $primaryStruct, $gamedata, $shooter);
	}

	protected function addCollapseDamage($unit, $system, $gamedata, $shooter)
	{
		$damageEntry = new DamageEntry(
			-1, $unit->id, -1, $gamedata->turn,
			$system->id, (int)$system->maxhealth, 0, 0, -1, true, false,
			"Destroyed by a collapsing jump point.", self::COLLAPSE_DAMAGECLASS
		);
		$damageEntry->updated = true;
		if ($shooter){ //so submitDamages can find the fire order this belongs to
			$damageEntry->shooterid = $shooter->id;
			$damageEntry->weaponid  = $this->id;
		}
		$system->damage[] = $damageEntry;
	}


	public function getDamage($fireOrder){       return 0; /*no actual damage, just disruption of vortex which is narrative only*/  }
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

}//endof class VortexDisruptor


/* Stage S (S-f): Shadow Fighter Bomb (B5W Kitchen Sink "Fighter Bomb").
 *
 * An integrated-fighter carrier (ShadowHangar) cannot launch fighters from its
 * bay the ordinary way; instead it BOMBS them out as a weapon — a burst of its
 * held integrated fighters appears at a chosen hex, ready to fight from there.
 *
 * Structurally this is a Weapon (NOT a Hangar — Weapon/Hangar are sibling
 * ShipSystem subclasses, and the Bomb is genuinely weapon-shaped: an arc, a
 * range, fire-selectable in the Firing Phase, targets a hex). It mirrors
 * BallisticMineLauncher: a Weapon that SPAWNS units at a target hex during the
 * Fire step (beforeFiringOrderResolution), here ShadowMediumFighterFlights
 * instead of mines, drawn from the ship's ShadowHangar held pool.
 *
 * Behaviour (locked with user 2026-06-11):
 *  - bursts the ENTIRE held pool (output is the bay's capacity, e.g. 6) as ONE
 *    flight at the target hex — HangarOps::performBombLaunch caps it to what's
 *    actually held;
 *  - PURE HEX, no miss roll / no deviation (fighters always appear at the picked
 *    hex);
 *  - shares the ShadowHangar's pool (the bomb IS the bay's only launch path), so
 *    an empty bay (fighters lost on a previous turn) means the bomb cannot fire;
 *  - zero damage — the "effect" is the spawned flight (carrier heading/speed,
 *    integrated structure coupling registered; can't act until next turn via the
 *    deploy spawn). NO initiative penalty (no LaunchedThisTurn -50 / HangarOperations
 *    -20) — unlike an ordinary launch (user 2026-06-11). Docking keeps its penalty.
 *
 * The spawn happens in fire() (Firing::fireWeapons → Firing::fire on the live
 * Fire-Phase advance). fire() is guarded by `if ($fire->rolled > 0) return;`, so
 * setting $fireOrder->rolled in fire() makes the burst idempotent on replay —
 * the same self-persisting-effect pattern VortexDisruptor uses (it too is a
 * non-ballistic hex weapon whose whole effect lives in fire(), unlike the
 * BALLISTIC mine launcher which must spawn in beforeFiringOrderResolution
 * because ballistics resolve before the fire step). The burst is performed once
 * and persisted (ship row + 'Fighter Bomb launched' note); replay reads it back.
 */
class ShadowFighterBomb extends Weapon{
	public $name = "ShadowFighterBomb";
	public $displayName = "Fighter Bomb";
	public $iconPath = "FighterBomb.png"; //placeholder shadow-weapon icon until art exists

	// Auto-discovered by game.php to preload the flight blueprint into staticShips
	// so the client has its full definition when the bomb spawns a flight mid-game.
	public $spawnableClasses = array('ShadowMediumFighterFlight');

	public $damageType = "Standard"; //irrelevant — bomb does no damage
	public $weaponClass = "Ballistic";
	public $hextarget = true;        //targets a hex, not a unit
	public $hidetarget = false;
	public $ballistic = false;       //resolved as a hex-fire effect, not a ballistic shot
	public $uninterceptable = true;
	public $doNotIntercept = true;
	public $priority = 1;
	public $factionAge = 3;          //Ancient

	public $range = 10;              //≤10 hexes (B5W example for the test hull)
	public $loadingtime = 1;         //ready every turn (bay-limited, not charge-limited)

	public $animation = "ball";
	public $animationColor = array(160, 60, 200);
	public $animationExplosionScale = 0.5;
	public $animationExplosionType = "AoE";

	public $firingModes = array(
		1 => "Fighter Bomb"
	);

	//Self-repair: a hangar-adjacent system, low priority (matches VortexDisruptor).
	public $repairPriority = 2;

	//Stage S (multi-bay): which ShadowHangar this bomb serves. A carrier with SEVERAL
	//arc-keyed bays (shadowRegenBaseBomb) gives each its own bomb; the bomb launches/drains
	//ONLY the bay whose ShadowHangar->bombGroupIndex equals this. null = single-bay hull
	//(shadowCruiserBomb) — performBombLaunch falls back to the primary ShadowHangar.
	public $bombHangarIndex = null;

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $bombHangarIndex = null)
	{
		if ( $maxhealth == 0 ) $maxhealth = 4;
		if ( $powerReq == 0 ) $powerReq = 0; //no power draw — it throws fighters, not energy
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
		if ($bombHangarIndex !== null) $this->bombHangarIndex = (int)$bombHangarIndex;
	}

	//Round-trip the per-bay link so the client can size each bomb's pool to its own bay.
	public function stripForJson() {
		$strippedSystem = parent::stripForJson();
		if (isset($this->bombHangarIndex)) $strippedSystem->bombHangarIndex = (int)$this->bombHangarIndex;
		return $strippedSystem;
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"]  = "Used to launch integrated fighters at a target hex.";
		$this->data["Special"] .= "<br>Targets a hex within range; fighters deploy there at the carrier's heading and speed next turn.";
        $this->data["Special"] .= "<br>Technical system, cannot be damaged or destroyed.";			
		$this->data["Special"] .= "<br>Cannot be used when ship is Half-Phased.";
	}

	public function getDamage($fireOrder){ return 0; }
	public function setMinDamage(){ $this->minDamage = 0; }
	public function setMaxDamage(){ $this->maxDamage = 0; }

	/* Hex order has no target ship, so the base calculateHitBase would fall into
	 * its null-target "auto-miss + ERROR pubnote" branch. Override (like Vortex
	 * Disruptor) to mark it an automatic success — the bomb never misses; whether
	 * fighters actually launch is decided in fire() by the held-pool check. */
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$fireOrder->needed = 100;   //always "hits" (d100); range/arc enforced client-side
		$fireOrder->updated = true;
	}

	/* The whole effect: burst the carrier's held integrated fighters out at the
	 * ordered hex. No hit roll, no damage. Setting $fireOrder->rolled trips the
	 * `if ($fire->rolled > 0) return;` guard in Firing::fire so a replay scrub
	 * never re-spawns (the persisted ship row + note ARE the record). Mirrors
	 * VortexDisruptor's self-persisting, roll-once-on-live-advance fire(). */
	public function fire($gamedata, $fireOrder)
	{
		$this->changeFiringMode($fireOrder->firingMode);

		//A hex-target order may arrive bound to a ship (targetid != -1) if the
		//player clicked a unit; resolve it to that unit's hex (same correction the
		//mine launcher + Vortex Disruptor apply).
		if ($fireOrder->targetid != -1){
			$targetship = $gamedata->getShipById($fireOrder->targetid);
			if ($targetship){
				$tp = $targetship->getHexPos();
				$fireOrder->x = $tp->q;
				$fireOrder->y = $tp->r;
			}
			$fireOrder->targetid = -1;
			$fireOrder->calledid = -1;
		}

		$fireOrder->rolled = 1;   //trip the no-re-fire guard; bomb never "misses"
		$fireOrder->updated = true;

		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		if (!$shooter) return;

		//A carrier that HALF-PHASED this turn is partly shifted into hyperspace and
		//cannot form/expel its integrated fighters (B5W): the Fighter Bomb does not
		//launch. rolled is already set above so the guarded fire() never re-runs on
		//replay; we simply spawn nothing and note why. The client mirrors this in
		//weaponManager.queueShadowFighterBombOrder.
		if (Movement::isHalfPhased($shooter, $gamedata->turn)){
			$fireOrder->pubnotes .= " Fighter Bomb: carrier half-phased — integrated fighters cannot launch.";
			return;
		}

		//$fireOrder->shots carries the player's chosen launch count (set by the
		//client hex-target count picker, clamped there to the held pool). 0/absent
		//falls back to the whole pool inside performBombLaunch.
		$count = (int)$fireOrder->shots;

		$spawnPos = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
		//Multi-bay: launch ONLY from the bay this bomb serves (null ⇒ primary bay).
		$flightId = HangarOps::performBombLaunch($shooter, $spawnPos, $gamedata, $count, null, $this->bombHangarIndex);

		if ($flightId){
			$fireOrder->shotshit = 1;
			//Terse pubnote: in a manual split the combat log groups N same-hex bomb
			//orders into ONE line and concatenates their pubnotes, so keep each short
			//(the grouped line already reports the total shot count + target hex).
			$fireOrder->pubnotes .= " Fighter Bomb launched.";
		} else {
			$fireOrder->pubnotes .= " Fighter Bomb: no integrated fighters available to launch.";
		}
	} //endof function fire
}//endof class ShadowFighterBomb




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
	public $presetSubordinates = null; //pre-registered by a further weapon that yielded primary role to this (closest) weapon
	
	
	    public function setSystemDataWindow($turn){
		      parent::setSystemDataWindow($turn);
		      $this->data["Special"] = "Concentrators allocated in the same combined modes, at same section of the same target automatically merge into one shot.";
		      $this->data["Special"] .= "<br>Combining ships must all be within 1 hex of each other; multiple Concentrators on the same ship can combine.";
		      $this->data["Special"] .= "<br>Each additional weapon adds +2 Fire Control and +1d10 damage (max +10 / +5d10).";
		      $this->data["Special"] .= "<br>Combined shot fires from the closest ship's range; doubled range penalty if any ship lacks lock-on.";
		      $this->data["Special"] .= "<br>If too few partners are eligible, the shot fires in the highest mode actually achievable.";
	    }
	
		
	
	public function fire($gamedata, $fireOrder){
	    if ($this->isCombined) $fireOrder->shots = 0; //no actual shots from weapon that's firing as part of combined shot!
	    parent::fire($gamedata, $fireOrder);
	} //endof function fire	
	
	
	//if fired in higher mode - combine with other weapons that are so fired!
	//if already combining - do not fire at all (eg. set hit chance at 0, make self completely uninterceptable and number of shots at 0)
	public function calculateHitBase($gamedata, $fireOrder){
		$this->alreadyConsidered = true;
		if ($this->testRun){ //calculate nominal, skipping concentration issues - for subordinate weapon to calculate average hit chance
			parent::calculateHitBase($gamedata, $fireOrder);
			return;
		}
		if ($this->isCombined){  //this weapon is being used as subordinate combination weapon!
			$notes = "technical fire order - weapon combined into another shot";
			$fireOrder->chosenLocation = 0;
			$fireOrder->needed = 0;
			$fireOrder->shots = 0;
			$fireOrder->notes = $notes;
			$fireOrder->updated = true;
			$this->changeFiringMode($fireOrder->firingMode);
			return;
		}

		$subordinateData = array(); //per accepted subordinate: ['order','ship','nominalNeeded','dist']
		$subordinateOrders = array();
		$subordinateOrdersNo = 0;
		$firingShip = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);

		if (($fireOrder->firingMode > 1) && ($firingShip !== null) && ($target !== null)){

			if ($this->presetSubordinates !== null) {
				//Pre-designated primary: a further weapon that processed first yielded the primary role to us.
				$subordinateOrders = $this->presetSubordinates;
				$this->presetSubordinates = null;
				$subordinateOrdersNo = count($subordinateOrders);
				if ($subordinateOrdersNo < ($fireOrder->firingMode - 1)){
					$fireOrder->firingMode = $subordinateOrdersNo + 1;
				}
			} else {
				//B5W rule: combining ships must all be within 1 hex of each other and hit the same side of the target.
				$primarySide = $target->getHitSection($firingShip, $gamedata->turn);
				$shipsNearby = $gamedata->getShipsInDistance($firingShip, 1);

				//Collect candidate orders matching target/mode/section, sorted by distance to target so the closest get priority.
				//Note: the firing ship itself is intentionally included - multiple Concentrators on the same ship can combine.
				//The alreadyConsidered flag prevents the primary weapon from matching itself.
				$candidates = array();
				foreach($shipsNearby as $otherShip) {
					$allOrders = $otherShip->getAllFireOrders($gamedata->turn);
					foreach($allOrders as $subOrder) {
						if ($subOrder->type !== 'normal') continue;
						if ($subOrder->targetid != $fireOrder->targetid) continue;
						if ($subOrder->firingMode != $fireOrder->firingMode) continue;
						$subWeapon = $otherShip->getSystemById($subOrder->weaponid);
						if (!($subWeapon instanceof ParticleConcentrator)) continue;
						if ($subWeapon->alreadyConsidered) continue;
						$candidateSide = $target->getHitSection($otherShip, $gamedata->turn);
						if ($candidateSide !== $primarySide) continue;
						$candidates[] = array(
							'order' => $subOrder,
							'ship' => $otherShip,
							'distToTarget' => $otherShip->getHexPos()->distanceTo($target->getHexPos()),
						);
					}
				}
				usort($candidates, function($a, $b) {
					return $a['distToTarget'] - $b['distToTarget'];
				});

				//Greedy clique fill - each accepted ship must be within 1 hex of every already-accepted ship.
				$acceptedShips = array($firingShip);
				foreach($candidates as $cand){
					if ($subordinateOrdersNo >= ($fireOrder->firingMode - 1)) break;
					$candPos = $cand['ship']->getHexPos();
					$fits = true;
					foreach($acceptedShips as $acc){
						if ($candPos->distanceTo($acc->getHexPos()) > 1){
							$fits = false;
							break;
						}
					}
					if (!$fits) continue;
					$acceptedShips[] = $cand['ship'];
					$subordinateOrders[] = $cand['order'];
					$subordinateOrdersNo++;
				}

				//If not enough weapons could be combined, downgrade firing mode to what was actually achievable.
				if ($subordinateOrdersNo < ($fireOrder->firingMode - 1)){
					$fireOrder->firingMode = $subordinateOrdersNo + 1;
				}

				//Yield to closest: if a selected subordinate is closer to the target than we are, promote it to primary.
				//We become a subordinate, pre-register the list on the closer weapon, and return.
				if ($subordinateOrdersNo > 0) {
					$primaryDist = $firingShip->getHexPos()->distanceTo($target->getHexPos());
					$closestDist = $primaryDist;
					$closestIndex = null;
					foreach ($subordinateOrders as $idx => $subOrder) {
						$subShip = $gamedata->getShipById($subOrder->shooterid);
						$d = $subShip->getHexPos()->distanceTo($target->getHexPos());
						if ($d < $closestDist) { $closestDist = $d; $closestIndex = $idx; }
					}
					if ($closestIndex !== null) {
						$truePrimaryOrder = $subordinateOrders[$closestIndex];
						$truePrimaryShip  = $gamedata->getShipById($truePrimaryOrder->shooterid);
						$truePrimaryWeapon = $truePrimaryShip->getSystemById($truePrimaryOrder->weaponid);
						// Build subordinate list for the true primary: swap promoted slot with self's order.
						$newSubList = $subordinateOrders;
						$newSubList[$closestIndex] = $fireOrder;
						$truePrimaryWeapon->presetSubordinates = array_values($newSubList);
						// Lock all pre-registered subordinates (including self) so none claims primary.
						foreach ($newSubList as $lockedOrder) {
							$lockedShip   = $gamedata->getShipById($lockedOrder->shooterid);
							$lockedWeapon = $lockedShip->getSystemById($lockedOrder->weaponid);
							$lockedWeapon->isCombined = true;
							$lockedWeapon->alreadyConsidered = true;
							$lockedWeapon->doNotIntercept = true;
						}
						// Mark own fire order as technical subordinate.
						$this->isCombined = true;
						$fireOrder->chosenLocation = 0;
						$fireOrder->needed = 0;
						$fireOrder->shots = 0;
						$fireOrder->notes = "Technical fire order - weapon combined into another shot. ";
						$fireOrder->updated = true;
						$this->changeFiringMode($fireOrder->firingMode);
						return;
					}
				}
			}

			$shipsWithPubnotes = array($firingShip->id);

			//Mark subordinates as combined, capture nominal hit chance and distance, then null their fire orders.
			foreach($subordinateOrders as $subOrder){
				$otherShip = $gamedata->getShipById($subOrder->shooterid);
				$subWeapon = $otherShip->getSystemById($subOrder->weaponid);
				$subWeapon->isCombined = true;
				$subWeapon->alreadyConsidered = true;
				$subWeapon->doNotIntercept = true;
				
				if (!in_array($otherShip->id, $shipsWithPubnotes)) {
					$subOrder->pubnotes .= ' Shots were combined into a stronger attack.';
					$shipsWithPubnotes[] = $otherShip->id;
				}

				$subOrder->firingMode = $fireOrder->firingMode;

				$subWeapon->testRun = true;
				$subWeapon->calculateHitBase($gamedata, $subOrder);
				$nominalNeeded = $subOrder->needed;
				$subWeapon->testRun = false;

				$subordinateData[] = array(
					'ship' => $otherShip,
					'nominalNeeded' => $nominalNeeded,
					'dist' => $otherShip->getHexPos()->distanceTo($target->getHexPos()),
				);

				$subOrder->needed = 0;
				$subOrder->notes = "Technical fire order - weapon combined into another shot. ";
			}
		}

		parent::calculateHitBase($gamedata, $fireOrder);

		if ($fireOrder->firingMode > 1 && $target !== null){
			//Normalize each ship's nominal hit chance to the closest combining ship's range,
			//then average. If any combining ship lacks lock-on, double the range penalty.
			$primaryDist = $firingShip->getHexPos()->distanceTo($target->getHexPos());
			$closestDist = $primaryDist;
			foreach($subordinateData as $sd){
				if ($sd['dist'] < $closestDist) $closestDist = $sd['dist'];
			}

			$anyWithoutLockOn = ($firingShip->getOEW($target, $gamedata->turn) <= 0);
			if (!$anyWithoutLockOn){
				foreach($subordinateData as $sd){
					if ($sd['ship']->getOEW($target, $gamedata->turn) <= 0){
						$anyWithoutLockOn = true;
						break;
					}
				}
			}

			$combinedChance = $fireOrder->needed + ($primaryDist - $closestDist) * $this->rangePenalty;
			foreach($subordinateData as $sd){
				$combinedChance += $sd['nominalNeeded'] + ($sd['dist'] - $closestDist) * $this->rangePenalty;
			}
			$fireOrder->needed = round($combinedChance / $fireOrder->firingMode);

			if ($anyWithoutLockOn){
				$fireOrder->needed -= $closestDist * $this->rangePenalty;
				$fireOrder->notes .= 'At least one combining ship lacks lock-on; range penalty doubled. ';
			}
			$fireOrder->notes .= 'Combined shot from ' . $fireOrder->firingMode . ' Particle Concentrators (closest-range hit chance, averaged EW). ';
			$fireOrder->pubnotes .= ' Shots were combined into a stronger attack.';
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
	//the constructor rewrites displayName per instance ('Lightning Gun A'..'D' - the pairing letter),
	//so a hit-chart entry naming the mount directly matched nothing. VorlonLightningCannon keeps its
	//plain displayName, which is why the Dreadnought's "31:Lightning Cannon" works and the Strike
	//Cruiser's "31:Lightning Gun" did not. Same fix as the Kirishiac Orbital: a stable chart alias.
	//The 'Lightning Gun' TAG added in the constructor is unaffected - TAG: entries keep working.
	public $hitChartName = "Lightning Gun";

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
				if ($target instanceof Mine) continue;					
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
    public $fireControlArray = array( 1=>array(4, 3, 3), 2=>array(2, 4, 4), 3=>array(null, 4, 6), 4=>array(4, 3, 3), 5=>array(2, 4, 4));

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


/*
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

     	
		public function getDamage($fireOrder){       return 0;  }
		public function setMinDamage(){     $this->minDamage = 0 ;      }
		public function setMaxDamage(){     $this->maxDamage = 0 ;      }
	
}//endof class ProximityLaserLauncher
*/
/*
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
*/

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
		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition = 10){		
 			//$this->pairing = $pairing;
			//$this->displayName = 'Proximity Laser ' . $pairing . ''; 
			$this->startArcArray[] = $startArc; 
			$this->endArcArray[] = $endArc;		
			$this->ammunition = $ammunition;				
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

    
        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;
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
			if (isset($ship->attached[$thisShip->id])) continue; // Already attached to this unit, do not fire!			
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
		$this->data["Special"] .= '<br>Attacks are generated after movement but before the Firing Phase.';	
		$this->data["Special"] .= '<br>Cannot be manually targeted.';													
	}	

    public function getDamage($fireOrder){        return 8;   }
    public function setMinDamage(){     $this->minDamage = 8 ;      }
    public function setMaxDamage(){     $this->maxDamage = 8 ;      }
	
} //endof class PulsarMine



class Marines extends Weapon implements SpecialAbility{
	public $name = "Marines";
	public $displayName = "Marines";
	public $iconPath = "Marines.png";
	public $animation = "trail";
	public $animationColor = array(50, 50, 50);
	public $animationWidth = 0.2;
  	public $specialAbilities = array("Attaches");
	public $useOEW = false; 
	public $range = 0.1;
	public $ammunition = 2; //limited number of Marine contingents.

	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table, should never happen.

	public $calledShotMod = 0; //instead of usual -8
	
	public $loadingtime = 1;
	public $rangePenalty = 0;
	public $fireControl = array(null, 0, 0);
	
	public $noOverkill = true;
	public $priority = 3;
	
	public $uninterceptable = true; 
	public $doNotIntercept = true;	
		

	public $damageType = "Special";
	public $damageTypeArray = array(1=> "Special", 2=> "Standard", 3=> "Special");
	public $weaponClass = "Boarding";
	public $firingModes = array(
		1 => "Capture Ship",
		2 => "Sabotage",
		3 => "Rescue"
	);		

	public $eliteMarines = false;
	public $isBoardingAction = true;//For front end to recalculate hit chance.	

	public static $boardedThisTurn = array();//Static variable to keep track of Marine actions on current turn (to prevent too many on a ship).
	public static $clawBoardedThisTurn = array();//Grappling Claw deliveries, kept OUT of the pod cap - see recordClawBoarding().

	function __construct($startArc, $endArc, $damagebonus, $elite){
		parent::__construct(0, 1, 0, $startArc, $endArc);
		$this->eliteMarines = $elite;
	}    
	
	public function getSpecialAbilityValue($args){
		return $this->specialAbilityValue;
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Marine Units"] = $this->ammunition;    
		$this->data["Special"] = "<br>If on same hex as an enemy ship, can attempt to attach to target and board vessel.";	
		$this->data["Special"] .= "<br>Select from Firing Modes to attempt three 'Missions':";  		
		$this->data["Special"] .= "<br> - Capture Ship: Marines attempt to overcome defenders on enemy ship and disable it."; 
		$this->data["Special"] .= "<br> - Sabotage: Can target a specific system (via called shot)."; 
		$this->data["Special"] .= "<br> - Rescue: Scenarios only, Marines will board enemy ship and attempt to rescue a target."; 						 
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


	/* Stage 17 ext: marine reload while docked. Driven by HangarOps::serviceDockedFlights
	 * (same per-turn tick driving Weapon::whileDocked for matter weapons and
	 * FighterMissileRack::whileDocked for fighter missiles). Each restocked
	 * marine draws 10 pts from the carrier's MAR_CONT pool via
	 * HangarOps::drawMarineReload.
	 *
	 * Rate: 1 marine per FIGHTER per turn, even if a fighter mounts multiple
	 * Marines weapons (rare). The first Marines weapon encountered on the
	 * fighter walks every Marines sibling, picks the most-empty below cap,
	 * restocks one, then stamps $fighter->marinesReloadedTurn so remaining
	 * siblings' whileDocked calls early-return.
	 *
	 * Cap: class default ammunition (2) + flight's EXT_MAR enhancement bonus.
	 * Persistence: tac_ammo row via Manager::updateAmmoInfo (same path fire()
	 * uses). Saved value EXCLUDES the EXT_MAR bonus because the bonus is
	 * re-added on load via setEnhancementsFlight's EXT_MAR processing — matches
	 * fire()'s saving convention.
	 */
	public function whileDocked($flight, $carrier, $hangar, $gamedata){
		if (!($flight instanceof FighterFlight)) return;
		if ($this->isDestroyed($gamedata->turn)) return;
		//getUnit() returns the FighterFlight on a Marines weapon (per
		//FighterFlight::addSystem); resolve parent Fighter via the flight's
		//system-id lookup so the sibling walk and per-fighter gate work.
		$fighter = $flight->getFighterBySystem($this->id);
		if (!$fighter) return;

		//Per-fighter rate gate. Transient — fresh Fighter objects each
		//turn-load have this null, so the gate resets naturally.
		if (!empty($fighter->marinesReloadedTurn)
			&& (int)$fighter->marinesReloadedTurn >= (int)$gamedata->turn) return;

		//Cap: class default (2) + flight's EXT_MAR bonus. EXT_MAR is a
		//flight-level enhancement that adds to every Marines weapon on the
		//flight; cap is the post-enhancement starting load.
		$bonus = 0;
		if (isset($flight->enhancementOptions) && is_array($flight->enhancementOptions)){
			foreach ($flight->enhancementOptions as $enh){
				if (($enh[0] ?? '') === 'EXT_MAR' && (int)($enh[2] ?? 0) > 0){
					$bonus += (int)$enh[2];
				}
			}
		}
		$baseCap = 2;   //Marines class $ammunition default
		$cap = $baseCap + $bonus;

		//Build candidate list across every Marines sibling on this fighter
		//(not just $this) — pick the most-empty across the whole loadout.
		$candidates = array();
		foreach ($fighter->systems as $sib){
			if (!($sib instanceof Marines)) continue;
			if ($sib->isDestroyed($gamedata->turn)) continue;
			if ((int)$sib->ammunition >= $cap) continue;
			$candidates[] = array(
				'weapon'  => $sib,
				'missing' => $cap - (int)$sib->ammunition,
			);
		}
		if (empty($candidates)) return;

		//Most-missing first (prioritise emptiest).
		usort($candidates, function($a, $b){
			return $b['missing'] - $a['missing'];
		});

		//Pool is denominated in MARINES (1 unit = 1 marine), not CP. The 10 CP
		//price is paid up-front when buying the MAR_CONT enhancement in the
		//lobby; each in-game restock draws 1 marine from the count.
		$cost = 1;
		foreach ($candidates as $cand){
			if (!HangarOps::drawMarineReload($carrier, $cost)) return;   //pool exhausted
			$cand['weapon']->ammunition = min($cap, (int)$cand['weapon']->ammunition + 1);
			//Save with EXT_MAR bonus SUBTRACTED — bonus is re-added on load
			//by setEnhancementsFlight's EXT_MAR case (mirrors fire() at line ~7728).
			Manager::updateAmmoInfo(
				$flight->id,
				$cand['weapon']->id,
				$gamedata->id,
				$cand['weapon']->firingMode,
				$cand['weapon']->ammunition - $bonus,
				$gamedata->turn
			);
			$fighter->marinesReloadedTurn = (int)$gamedata->turn;
			return;   //1 marine per fighter per turn
		}
	}


	/* ---------------------------------------------------------------------------
	 * BOARDING ATTACHMENT - shared section helpers.
	 *
	 * Both Marines (breaching pods) and GrapplingClaw gate attachment through
	 * isAttachBlocked() below, and the Trek Transporter reuses the counting helpers.
	 * They live on Marines because Marines::$boardedThisTurn / getAttachedPodCount /
	 * recordBoarding were already the shared home, so no new autoload entry is needed.
	 *
	 * Limits are per STRUCTURE SECTION, derived from the hull itself rather than from a
	 * shipSizeClass table. getStructureSystem() falls back to Primary (0) for any location
	 * with no Structure of its own, so an MCV/LCV/OSAT collapses locations 1 and 2 onto 0,
	 * a Drazi Stormfalcon simply has no section 2, and a six-section starbase gets six
	 * sections for free - no table to maintain, and hulls no size class can express come
	 * out right. Primary needs no special case either: getHitSection() already returns 0
	 * once the facing structure was destroyed as of turn-1, so section 0 only becomes
	 * reachable when the rules say it should.
	 * --------------------------------------------------------------------------- */

	//Section key for an attachment: the location of the Structure that actually covers it.
	//Null only if a hull has no Primary Structure at all, which should never happen.
	public static function resolveAttachSection($target, $location){
		$struct = $target->getStructureSystem((int)$location);
		return ($struct === null) ? 0 : (int)$struct->location;
	}

	//Slots this unit consumes on its section: live pods for a flight (every breaching-pod
	//flight in the game is maxFlightSize 2), or the whole section for a claw ship.
	public static function getAttachFootprint($unit){
		if (!($unit instanceof FighterFlight)) return 1;

		$live = 0;
		foreach ($unit->systems as $pod){
			if (!$pod->isDestroyed()) $live++;
		}
		return $live;
	}

	//An LCV or OSAT supports a single attached craft, full stop - pod flight or claw ship.
	public static function isSingleAttachHull($target){
		return ($target->hangarRequired == 'LCVs' || $target instanceof OSAT);
	}

	/* Hull-wide ceiling on attached pods, applied ON TOP of the two-per-section rule. The
	   per-section rule alone would let a many-sectioned hull hold far more than its class
	   ever should: a Vree saucer carries SIX outer Structure blocks plus Primary, which is
	   seven sections and would otherwise be 14 pods.

	   Enormous units and bases are exempt from the class table and take 12. They are all
	   shipSizeClass 3, so the old `shipSizeClass > 3 => 12` branch never fired - size class 4
	   was never implemented - and they silently landed in the capital branch. Gating on the
	   Enormous/base flags is what that branch was reaching for. */
	public static function getHullPodCap($target){
		if ($target->base || $target->Enormous) return 12;
		if ($target->shipSizeClass >= 3) return 8;   //capital
		if ($target->shipSizeClass == 2) return 4;   //HCV
		return 2;                                    //medium ship and smaller
	}

	//Opposite-ends pairing for the capital claw rule. Only 1<->2 and 3<->4 exist on a
	//non-base capital hull, and Primary (0) is never a valid partner. Bases and Enormous
	//units skip this test entirely (isAttachBlocked branch C) precisely because their
	//locations - 1/41/42/2/32/31 on a six-section starbase - never match these pairs.
	public static function isOppositeSection($a, $b){
		$a = (int)$a;
		$b = (int)$b;
		if ($a == 0 || $b == 0) return false;
		if ($a == 1) return ($b == 2);
		if ($a == 2) return ($b == 1);
		if ($a == 3) return ($b == 4);
		if ($a == 4) return ($b == 3);
		return false;
	}

	/* How many pods the PRIMARY section (0) can hold.
	 *
	 * On a hull with exterior Structures, pods reach Primary ONLY through a breach:
	 * getHitSection redirects a hit to 0 exactly when the structure facing the shooter was
	 * destroyed as of turn-1. So Primary's capacity is two per BREACHED exterior section -
	 * a capital that has lost both sides can hold four on Primary, two arriving through
	 * each hole - rather than a flat two. Zero while the hull is intact, which is correct:
	 * with nothing breached there is no route to Primary in the first place.
	 *
	 * A hull with NO exterior structures (MCV, LCV, OSAT) keeps the flat two. For those,
	 * section 0 IS the hull - getStructureSystem falls back to Primary for every location -
	 * and nothing has to be destroyed to reach it.
	 */
	public static function getPrimaryPodCap($target, $turn){
		$exterior = 0;
		$breached = 0;

		foreach ($target->systems as $system){
			if (!($system instanceof Structure)) continue;
			if ((int)$system->location === 0) continue;   //Primary itself is not a way in

			$exterior++;
			//turn-1 mirrors getHitSection, which only routes to Primary once the facing
			//structure was destroyed as of the PREVIOUS turn.
			if ($system->isDestroyed($turn - 1)) $breached++;
		}

		if ($exterior === 0) return 2;   //Primary is the hull's only section

		return 2 * $breached;
	}

	//Distinct Structure locations on the hull, Primary included - how many sections a
	//boarding unit can attach to. Same walk the section caps use, so a derived total can
	//never exceed what could physically attach.
	public static function countAttachSections($target){
		$sections = array();
		foreach ($target->systems as $system){
			if ($system instanceof Structure) $sections[(int)$system->location] = true;
		}
		if (empty($sections)) return 1; //should never happen - every hull has a Primary
		return count($sections);
	}

	/* Occupancy of every section of $target, as
	 *   'sections' => sectionKey => array('pods'=>int, 'claws'=>int, 'units'=>int)
	 * plus the hull-wide totals 'podTotal', 'clawTotal', 'unitTotal' and 'clawSections' (the
	 * section keys currently holding a claw, in attachment order).
	 *
	 * Counts units ALREADY attached plus attach attempts approved earlier in this same
	 * resolution pass - see getPendingAttachments for why the second group is essential.
	 *
	 * $skipId excludes the unit being tested, so re-checking an already-attached unit
	 * never counts itself. Destroyed attached units are skipped: a dead claw ship must not
	 * hold a section forever, and a wiped-out flight must not keep an LCV's only slot.
	 */
	public static function getSectionOccupancy($target, $gamedata, $skipId = -1){
		$occ = array(
			'sections'     => array(),
			'podTotal'     => 0,
			'clawTotal'    => 0,
			'unitTotal'    => 0,
			'clawSections' => array(),
		);

		$counted = array(); //unit ids already in the tally, so a pending order never doubles one

		foreach ($target->hasAttached as $attachedId => $location){
			if ($attachedId == $skipId) continue;

			$unit = $gamedata->getShipById($attachedId);
			if (!$unit) continue;
			if ($unit->isDestroyed()) continue;

			$counted[$unit->id] = true;
			Marines::addSectionOccupant($occ, $target, $unit, $location);
		}

		foreach (Marines::getPendingAttachments($target, $gamedata, $skipId, $counted) as $pending){
			Marines::addSectionOccupant($occ, $target, $pending['unit'], $pending['location']);
		}

		return $occ;
	}

	//Folds one occupant into the tally built by getSectionOccupancy. $location is the raw
	//attachment location; the section key is resolved here.
	private static function addSectionOccupant(&$occ, $target, $unit, $location){
		$section = Marines::resolveAttachSection($target, $location);
		if (!isset($occ['sections'][$section])){
			$occ['sections'][$section] = array('pods' => 0, 'claws' => 0, 'units' => 0);
		}

		$occ['unitTotal']++;
		$occ['sections'][$section]['units']++;

		if ($unit instanceof FighterFlight){
			$pods = Marines::getAttachFootprint($unit);
			$occ['sections'][$section]['pods'] += $pods;
			$occ['podTotal'] += $pods;
		}else{
			$occ['sections'][$section]['claws']++;
			$occ['clawTotal']++;
			$occ['clawSections'][] = $section;
		}
	}

	/* Attach attempts on $target that have been APPROVED in this resolution pass but are
	 * not in hasAttached yet, as unitId => array('unit'=>ship, 'location'=>rawLocation).
	 *
	 * WHY THIS EXISTS. Firing::prepareFiring calculates the hit chance of EVERY fire order
	 * before Firing::fireWeapons resolves any of them, and hasAttached is only written at
	 * resolution time (onDamagedSystem). So an occupancy tally built from hasAttached alone
	 * shows every boarding order in a turn the same pre-turn state and they ALL pass the
	 * section limits - which is exactly how four pod flights ended up stacked two-deep on
	 * one Primus section and one Demos section in game 4300, filling those hulls' totals
	 * and locking their remaining sections out.
	 *
	 * An order counts as approved once calculateHitBase has processed it and left a
	 * non-zero 'needed' (every refusal sets it to 0), which is precisely the set walked
	 * ahead of this one by prepareFiring - so sections go first-come-first-served in fire
	 * order. 'updated' is what makes that test safe: it is false on every order loaded from
	 * the database and true only once calculateHitBase has run on it in THIS pass.
	 *
	 * The reservation is optimistic - a pod that later misses its attach roll still held
	 * the slot for this turn. That is the better trade: the roll is not known until a whole
	 * phase later, and refusing at resolution instead would spend the marine contingent
	 * before telling the player there was no room.
	 *
	 * A unit contributes at most ONE entry no matter how many boarding orders it files - a
	 * 2-pod flight files one per pod and a claw ship one per Grappling Claw mount, while
	 * hasAttached is keyed on the unit and getAttachFootprint already covers the whole unit.
	 */
	public static function getPendingAttachments($target, $gamedata, $skipId, $counted = array()){
		$pending = array();

		foreach ($gamedata->ships as $ship){
			if ($ship->id == $skipId) continue;
			if ($ship->id == $target->id) continue;
			if (isset($counted[$ship->id])) continue;   //already attached, counted from hasAttached
			if ($ship->isDestroyed()) continue;        //its order will never resolve
			//No hasSpecialAbility("Attaches") pre-filter here on purpose. That flag is built at
			//onConstructed and drops out when the attach system is destroyed or offline, so it
			//answers "can this unit still attach" rather than "does it hold an approved order" -
			//the per-order tests below answer the question we actually need.

			foreach ($ship->getAllFireOrders($gamedata->turn) as $fire){
				if ($fire->targetid != $target->id) continue;
				if (!$fire->updated) continue;             //not processed yet in this pass
				if ($fire->needed <= 0) continue;          //processed and refused
				if ($fire->chosenLocation === null) continue;

				//Marines and GrapplingClaw are the only weapons whose successful order
				//writes hasAttached. Tested by class rather than by the isBoardingAction
				//flag, which is a front-end display hint - the Trek Transporter delivers
				//marines under it without ever attaching.
				$weapon = $ship->getSystemById($fire->weaponid);
				if (!($weapon instanceof Marines) && !($weapon instanceof GrapplingClaw)) continue;

				$pending[$ship->id] = array('unit' => $ship, 'location' => $fire->chosenLocation);
				break;   //one reservation per unit
			}
		}

		return $pending;
	}

	//Empty occupancy record, so callers never have to isset() a section that nothing holds.
	public static function getSectionSlot($occ, $section){
		if (isset($occ['sections'][$section])) return $occ['sections'][$section];
		return array('pods' => 0, 'claws' => 0, 'units' => 0);
	}

	/* The one gate both Marines and GrapplingClaw call. Returns true to REFUSE the
	 * attachment, filling $reason with the text to show in pubnotes.
	 *
	 * $location is the raw chosenLocation - resolution to a section key happens here and
	 * ONLY here. hasAttached keeps storing the raw location, because the client positions
	 * the pod model from it and ShipInfo labels it ("Port-Forward" etc.), so there is no
	 * note-format change and no migration for games in flight.
	 */
	public static function isAttachBlocked($target, $gamedata, $shooter, $location, &$reason){
		$reason  = '';
		$section = Marines::resolveAttachSection($target, $location);
		$occ     = Marines::getSectionOccupancy($target, $gamedata, $shooter->id);
		$here    = Marines::getSectionSlot($occ, $section);
		$isClaw  = !($shooter instanceof FighterFlight);

		//A. LCV / OSAT - one attached craft on the whole hull, pod flight or claw.
		//A whole 2-pod flight still fits: the data model cannot record a partial
		//attachment, so the rule is capped as one UNIT here and one MISSION in
		//checkMissionAmount, which nets out to the one-pod-boards intent.
		if (Marines::isSingleAttachHull($target)){
			if ($occ['unitTotal'] >= 1){
				$reason = "This unit can only support a single attached craft.";
				return true;
			}
			return false;
		}

		//B. A claw ship holds its section exclusively - pods may never join it.
		//Deliberately asymmetric: nothing stops a claw attaching to a section that
		//already holds pods, which is today's behaviour.
		if ($here['claws'] >= 1){
			$reason = "A Grappling Claw already holds this section.";
			return true;
		}

		if ($isClaw){
			//C. Whole-hull claw caps. Bases and Enormous units have NO hull-wide cap -
			//one claw per section (rule B) is their only limit. That also retires the
			//opposite-ends test on hulls whose locations could never satisfy it.
			if (!$target->base && !$target->Enormous){
				if ($target->shipSizeClass <= 2){ //medium ship or HCV
					if ($occ['clawTotal'] >= 1){
						$reason = "Only one vessel may grapple a medium ship or HCV.";
						return true;
					}
				}else{ //capital
					if ($occ['clawTotal'] >= 2){
						$reason = "A capital ship can be grappled by two vessels only.";
						return true;
					}
					if ($occ['clawTotal'] == 1
						&& !Marines::isOppositeSection($occ['clawSections'][0], $section)){
						$reason = "Grappling vessels must attach to opposite ends.";
						return true;
					}
				}
			}
			//D. One claw per section is already covered by rule B above.
			return false;
		}

		$footprint = Marines::getAttachFootprint($shooter);

		//E. Pods: two per section, counting live pods across every attached flight.
		//Strict counting means a 1-pod flight blocks a full 2-pod flight from joining
		//its section even though a slot looks free - partial attachment cannot be
		//recorded, and never exceeding 2 pods on a section is the rules-correct outcome.
		//Primary is the exception: its capacity is two per BREACHED exterior section, so a
		//capital that has lost both sides takes four there. See getPrimaryPodCap.
		$sectionCap = ($section === 0)
			? Marines::getPrimaryPodCap($target, $gamedata->turn)
			: 2;

		if ($here['pods'] + $footprint > $sectionCap){
			$reason = ($sectionCap == 0)
				? "Breaching Pods can only reach the Primary section through a destroyed structure section."
				: "No room for more Breaching Pods on this section.";
			return true;
		}

		//F. Hull-wide ceiling by size class, on top of the per-section rule - a hull with
		//many sections must still never hold more pods than its class allows.
		if ($occ['podTotal'] + $footprint > Marines::getHullPodCap($target)){
			$reason = "This ship cannot support any more Breaching Pods.";
			return true;
		}

		return false;
	}

	//Marine missions this hull can absorb in one turn. Two per section, so a Stormfalcon
	//cannot allow 8 missions when only 6 pods can physically attach - then clamped to the
	//same hull-wide class cap, so it cannot exceed what can attach in the other direction
	//either. LCV/OSAT take one.
	public static function getMissionCap($target){
		if (Marines::isSingleAttachHull($target)) return 1;

		return min(2 * Marines::countAttachSections($target), Marines::getHullPodCap($target));
	}


	//Per-section limits now live in Marines::isAttachBlocked, which both this and
	//GrapplingClaw::checkAttachmentLimits call. Writes the specific refusal into pubnotes
	//so the player is told WHICH rule stopped the attachment rather than a generic string.
	private function checkAttachedAmount($target, $gamedata, $fireOrder){
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		if (!$shooter) return false;

		$reason = '';
		if (!Marines::isAttachBlocked($target, $gamedata, $shooter, $fireOrder->chosenLocation, $reason)){
			return false;
		}

		$fireOrder->pubnotes .= "<br>" . $reason;
		return true;
	}//endof checkAttachedAmount()

	public static function getAttachedPodCount($target, $gamedata, $skipId = -1){
		$noOfPods = 0;//Initialise	

		foreach ($target->hasAttached as $podID => $location){
            if ($podID == $skipId) continue;
			$podUnit = $gamedata->getShipById($podID);
			if($podUnit instanceof FighterFlight){ //Breaching Pods, not Grappling Ship
				foreach($podUnit->systems as $pod){
					if(!$pod->isDestroyed()) $noOfPods++; //Attached to target ship and not destroyed.
				}
			}	
		}

		return $noOfPods;
	}

	public static function getNewMissionsThisTurn($target){
		$noOfMissions = 0;//Initialise

		foreach (Marines::$boardedThisTurn as $boardedId) {//Check static variable for how many marines missions have boarded THIS turn.
	        if ($boardedId == $target->id) {
	            $noOfMissions++;
	        }
	    }

		return $noOfMissions;
	}

	//Checks during Delivery whether too many pods are trying to deliver marines to target.
	//Derived from the hull's section count (2 per section) rather than a size-class table,
	//so it can never allow more missions than pods can physically attach - a Stormfalcon
	//has 3 exterior sections, so 8 was always wrong for it.
	private function checkMissionAmount($target, $gamedata, $fireOrder){
		$noOfMissions = Marines::getNewMissionsThisTurn($target);

		return ($noOfMissions >= Marines::getMissionCap($target));
	}//endof checkMissionAmount()


	public function calculateHitBase($gamedata, $fireOrder)
	{
		//Needs it's own custom routine for hit chance.
		$shooter = $gamedata->getShipById($fireOrder->shooterid);	
		$target = $gamedata->getShipById($fireOrder->targetid);	

        $hitLoc = null;
        if (isset($target->hasAttached[$shooter->id])) {
            $hitLoc = $target->hasAttached[$shooter->id];
        } else {
			//getAttachSection, not getHitSection - a boarder takes the section its entry hex
			//edge points at, deterministically. See BaseShip::getAttachSection.
            $hitLoc = $target->getAttachSection($shooter, $fireOrder->turn);
        }
        $fireOrder->chosenLocation = $hitLoc;
		
        if($target instanceof Mine || $target instanceof Terrain){
			$fireOrder->pubnotes .= "<br> Breaching pods cannot attach to this kind of target.";			
			$fireOrder->needed = 0;
			$fireOrder->updated = true;
            return;
		}		

		if($target->advancedArmor) {//Cannot attach to Ancients.  Might be impossible if Front End chance is also made 0%
			$fireOrder->pubnotes .= "<br> Breaching pods cannot attach to Advanced Armour.";
			$fireOrder->needed = 0;
			$fireOrder->updated = true;						
			return; 
		}

		//An already-attached flight stays attached - resolve the auto-hit BEFORE the section
		//checks, or a claw that later grabbed the same section would refuse the pods that
		//were there first and silently detach them.
        if (isset($target->hasAttached[$shooter->id])) {
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
			//$fireOrder->pubnotes .= "<br> Pod already attached, automatic hit."; //Remove to declutter Combat Log
			return;
		}

		//check if this section can still take these pods.
		if($this->checkAttachedAmount($target, $gamedata, $fireOrder)){//If it returns true, the section is full.  Cancel attempt early..
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
		
		if($targetSpeed > $shooterSpeed){//Target is moving faster, roll to attach.
			$baseHitChance = 100;//Start with automatic hit.
			$speedChance = 	$finalSpeedDifference *10;//Each point of speed difference is 10% chance to miss.
			$finalHitChance = $baseHitChance - $speedChance;//Adjust hitchance.
			$fireOrder->needed = $finalHitChance;//Update fireOrder.		
			$fireOrder->updated = true;	
			return;
		}else{
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
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

	/* A Grappling Claw's marine delivery. Tracked SEPARATELY from $boardedThisTurn, so it does
	 * not eat the target's breaching-pod mission allowance.
	 *
	 * Ruling (2026-08-13): grappling vessels do not count against the pod cap. They used to
	 * share it, and since a Claweagle mounts TWO claws a single grappling frigate consumed half
	 * of an HCV's entire per-turn allowance (getMissionCap = 4 on a Demos) - so pods that had
	 * legitimately attached to the still-free aft section were refused delivery with "Too many
	 * Breaching Pods trying to deliver marines". Seen in game 4300 turn 1, orders 497280/497281,
	 * where one claw on the bow plus one pod flight used up all four missions.
	 *
	 * Nothing caps this tally: how many claw missions can land is already bounded by the
	 * attachment limits (one grappling vessel on a medium ship or HCV, two on a capital, one per
	 * section on a base) and by each claw's own ammunition.
	 */
	public static function recordClawBoarding($targetId) {
	    Marines::$clawBoardedThisTurn[] = $targetId;
	}



	private function getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder){
		$rollMod = 0;
		if($this->eliteMarines) $rollMod -= 1; //Elite Marines board more easily.

		if($target->faction == "Narn Regime" || $target->faction == "Gaim Intelligence" || $target->faction == "Escalation Wars Sshel'ath Alliance")	$rollMod += 1; //Certain factions defend harder! 

		if($shooter->faction == "Llort")  $rollMod -= 1; //Llort should get bonus to Rescue and Capture, but making them elite feels incorrect.  Have instead made it easier for their marines to board. 	
		if($shooter->faction == "Escalation Wars Sshel'ath Alliance")  $rollMod -= 1; //Sshel'ath physiology makes it easier for their marines to board. 	
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
				
		//check if there are too many pods attached already on target ship.
		if($this->checkMissionAmount($target, $gamedata, $fireOrder)){//If it returns true, there are too many attaching pods.							
			$this->ammunition++;//Marines weren't eliminated.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);	
			$fireOrder->pubnotes .= "<br>Too many Breaching Pods trying to deliver marines, boarding attempt cancelled.";		
			return;
		}	
			
		//Can proceed with boarding actions, roll to see if Marines are delivered.		
		$rollMod = $this->getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder);		
		$deliveryRoll = max(0, Dice::d(10) + $rollMod);		

		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.		
		foreach($cnc->criticals as $critDisabled){
			if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) $deliveryRoll = 1;//Ship captured, auto success.		
		}		

		if (!isset($target->hasAttached[$shooter->id])) {
			$target->hasAttached[$shooter->id] = $fireOrder->chosenLocation;
			$shooter->attached[$target->id] = $fireOrder->chosenLocation;
			$facingOffset = Movement::getAttachFacingOffsetFromBearing($target, $shooter);
			if ($facingOffset !== null) {
				$target->hasAttachedFacing[$shooter->id] = $facingOffset;
				$shooter->attachedFacing[$target->id] = $facingOffset;
			}
			if ($cnc) {
				$noteValue = $shooter->id . "=>" . $fireOrder->chosenLocation;
				if ($facingOffset !== null) $noteValue .= ":" . $facingOffset;
				$cnc->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$target->id,$cnc->id,"Attached","Attached",$noteValue);
			}
		}

		if($deliveryRoll <= 5){ //successful delivery, continue with applying critical effects.



			switch($this->firingMode){

				case 1://Capture

					$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt to capture enemy ship next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new CaptureShipElite(-1, $ship->id, $cnc->id, 'CaptureShipElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}else{//Not Elite Marines					
									$crit = new CaptureShip(-1, $ship->id, $cnc->id, 'CaptureShip', $gamedata->turn+1);  //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
								}							    		
			            }				
				
					break;

				case 2://Sabotage

					if($fireOrder->calledid != -1 && !($system instanceof Structure) && $system->location != 0){//Is a called shot, and not somehow attacking structure, place crit on system.
							$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt to sabotage " . $system->displayName ." system next turn.";
						if($this->eliteMarines){//Are Marines Elite?
							$crit = new SabotageElite(-1, $ship->id, $system->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
							$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}else{//Not Elite Marines			
							$crit = new Sabotage(-1, $ship->id, $system->id, 'Sabotage', $gamedata->turn+1); //Takes effect next turn.
							$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
						}	
					}else{ //Has targeted ship generally, not a specific system (or somehow retargeted to structure).  Apply crit to CnC.
						$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt sabotage operations on enemy ship next turn.";								
							if($cnc){
									if($this->eliteMarines){//Are Marines Elite?
										$crit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
										$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
									}else{//Not Elite Marines					
										$crit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
										$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.	
									}							    		
				            }				
					}	
					
					break;				
				
				case 3://Rescue

					$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt their rescue mission next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new RescueMissionElite(-1, $ship->id, $cnc->id, 'RescueMissionElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note marines have boarded this turn
								}else{//Not Elite Marines					
									$crit = new RescueMission(-1, $ship->id, $cnc->id, 'RescueMission', $gamedata->turn+1);  //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
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
			$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit failed to board enemy ship, but returned safely to their pod.";
			Marines::recordBoarding($fireOrder->targetid);//Add id entry to static variable to note pod attached this turn.							
			return;	
		}else{//Roll result is 9 or over
			$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit was eliminated whilst trying to board the enemy ship.";
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



class GrapplingClaw extends Weapon implements SpecialAbility{
	public $name = "GrapplingClaw";
	public $displayName = "Grappling Claw";
	public $iconPath = "grapplingClaw.png";
	public $animation = "trail";
	public $animationColor = array(50, 50, 50);
	public $animationWidth = 0.2;
  	public $specialAbilities = array("Attaches");  
	public $useOEW = false; 
	public $range = 0.1;

	public $noPrimaryHits = true; //cannot hit PRIMARY from outer table, should never happen.

	public $calledShotMod = 0; //instead of usual -8
	
	public $loadingtime = 1;
	public $rangePenalty = 0;
	
	public $noOverkill = true;
	public $priority = 2;
	
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
	public $hostShipId = -1; //Tracks if claw is attached to a ship.	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition, $elite)
	{
		if ( $maxhealth == 0 ) $maxhealth = 5;
        if ( $powerReq == 0 ) $powerReq = 0;      						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $ammunition, $elite); //Parent routines take care of the rest
		$this->ammunition = $ammunition;			
		$this->eliteMarines = $elite;	       
	}

	public function getSpecialAbilityValue($args){
		return $this->specialAbilityValue;
	}

	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		$this->data["Marine Units"] = $this->ammunition;    		   
		$this->data["Special"] = "<br>If on same hex as an enemy ship, and in arc, this weapon attempt to attach to a target in arc and deliver Marines.";	
		$this->data["Special"] .= "<br>Select from Firing Modes to attempt three 'Missions':";  		
		$this->data["Special"] .= "<br> - Capture Ship: Marines attempt to overcome defenders on enemy ship and disable it."; 
		$this->data["Special"] .= "<br> - Sabotage: Can target a specific system (via called shot)."; 
		$this->data["Special"] .= "<br> - Rescue: Scenarios only, Marines will board enemy ship and attempt to rescue a target."; 
		$this->data["Special"] .= "<br>NOTE - You cannot attach to ships which rolled higher initiative than you this turn, even if you are in the same Initiative bracket using Simultaneous Movement rules."; 
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

        if($target instanceof Mine || $target instanceof Terrain){
			$fireOrder->pubnotes .= "<br> Grappling Claws cannot attach to this kind of target.";
			$fireOrder->needed = 0;
			$fireOrder->updated = true;
            return;
		}

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

		//An attached claw keeps the section it already holds and stays attached automatically,
		//exactly as Marines does. Without this it re-rolled its attach chance every turn and
		//could "miss" while still being attached, and the section was re-rolled with it.
		//Guarded BEFORE the limit check so a claw is never blocked by its own occupancy.
        if (isset($target->hasAttached[$shooter->id])) {
			$fireOrder->chosenLocation = $target->hasAttached[$shooter->id];
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
			return;
		}

		//getAttachSection, not getHitSection - see BaseShip::getAttachSection.
        $fireOrder->chosenLocation = $target->getAttachSection($shooter, $fireOrder->turn);

        if($this->checkAttachmentLimits($target, $gamedata, $fireOrder)){
			$fireOrder->needed = 0;
			$fireOrder->updated = true;
			return;
		}

		if($finalSpeedDifference > 0){//D20 roll needs to be over speed difference.
			$baseHitChance = 100;//Start with automatic hit.
			$speedChance = 	$finalSpeedDifference *5;//Each point of speed difference is 5% chance to miss.
			$finalHitChance = $baseHitChance - $speedChance;//Adjust hitchance.
			if($target->Enormous) $finalHitChance += 10; //You can't attach to Enormous Units without auto-ramming, but at least you get a bonus :)
			$fireOrder->needed = $finalHitChance;//Update fireOrder.		
			$fireOrder->updated = true;	
			return;
		}else{
			$fireOrder->needed = 100;
			$fireOrder->updated = true;
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

	//Shares Marines::isAttachBlocked with the breaching pods - the size-class caps and the
	//opposite-ends test moved into its branch C rather than being deleted. Two behaviour
	//changes fall out of that: claw sections are resolved through the hull's Structures
	//(so an MCV's locations 1 and 2 are correctly the same section), and bases/Enormous
	//units now get one claw per section instead of an opposite-ends test that could never
	//match their locations and therefore accepted any two sections.
	private function checkAttachmentLimits($target, $gamedata, $fireOrder){
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		if (!$shooter) return false;

		$reason = '';
		if (!Marines::isAttachBlocked($target, $gamedata, $shooter, $fireOrder->chosenLocation, $reason)){
			return false;
		}

		$fireOrder->pubnotes .= "<br>" . $reason;
		return true;
	}//endof checkAttachmentLimits()



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
		
		//check the section can still take this claw. checkAttachmentLimits has already
		//written the specific reason into pubnotes.
		if($this->checkAttachmentLimits($target, $gamedata, $fireOrder)){
			$this->ammunition++;//Marines weren't eliminated.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			$fireOrder->pubnotes .= "<br>Boarding attempt cancelled.";
			return;
		}
			
		//Can proceed with boarding actions, roll to see if Marines are delivered.		
		$rollMod = $this->getDeliveryRollMod($shooter, $target, $gamedata, $fireOrder);		
		$deliveryRoll = max(0, Dice::d(10) + $rollMod);		

		$cnc = $ship->getSystemByName("CnC");//$this should be CnC, but just in case.		
		foreach($cnc->criticals as $critDisabled){
			if($critDisabled->phpclass == "ShipDisabled"  && $critDisabled->turn <= $gamedata->turn) $deliveryRoll = 1;//Ship captured, auto success.		
		}		

			if (!isset($target->hasAttached[$shooter->id])) {
				$target->hasAttached[$shooter->id] = $fireOrder->chosenLocation;
				$shooter->attached[$target->id] = $fireOrder->chosenLocation;
				$facingOffset = Movement::getAttachFacingOffsetFromBearing($target, $shooter);
				if ($facingOffset !== null) {
					$target->hasAttachedFacing[$shooter->id] = $facingOffset;
					$shooter->attachedFacing[$target->id] = $facingOffset;
				}
				if ($cnc) {
					$noteValue = $shooter->id . "=>" . $fireOrder->chosenLocation;
					if ($facingOffset !== null) $noteValue .= ":" . $facingOffset;
					$cnc->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$target->id,$cnc->id,"Attached","Attached",$noteValue);
				}
			}
		$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,$gamedata->phase,$shooter->id,$this->id,"ClawAttached","ClawAttached",$target->id);
		$this->hostShipId = $target->id;

		if($deliveryRoll <= 5){ //successful delivery, continue with applying critical effects.						

			switch($this->firingMode){
								
				case 1://Capture

					$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt to capture enemy ship next turn.";			
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new CaptureShipElite(-1, $ship->id, $cnc->id, 'CaptureShipElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
								}else{//Not Elite Marines
									$crit = new CaptureShip(-1, $ship->id, $cnc->id, 'CaptureShip', $gamedata->turn+1);  //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
								}
			            }

					break;

				case 2://Sabotage

					if($fireOrder->calledid != -1 && !($system instanceof Structure) && $system->location != 0){//Is a called shot and not structure, place crit on system.
							$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt to sabotage " . $system->displayName ." system next turn.";
						if($this->eliteMarines){//Are Marines Elite?
							$crit = new SabotageElite(-1, $ship->id, $system->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
							$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
						}else{//Not Elite Marines
							$crit = new Sabotage(-1, $ship->id, $system->id, 'Sabotage', $gamedata->turn+1); //Takes effect next turn.
							$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
							$crit->updated = true;
							$system->criticals[] =  $crit;
							Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
						}
					}else{ //Has targeted ship generally, not a specific system.  Apply crit to CnC.
						$fireOrder->pubnotes .= "<br>Roll(Mod): $deliveryRoll($rollMod) - A marine unit will attempt sabotage operations on enemy ship next turn.";
							if($cnc){
									if($this->eliteMarines){//Are Marines Elite?
										$crit = new SabotageElite(-1, $ship->id, $cnc->id, 'SabotageElite', $gamedata->turn+1); //Takes effect next turn.
										$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
									}else{//Not Elite Marines
										$crit = new Sabotage(-1, $ship->id, $cnc->id, 'Sabotage', $gamedata->turn+1);  //Takes effect next turn.
										$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
										$crit->updated = true;
										$cnc->criticals[] =  $crit;
										Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
									}
				            }
					}

					break;

				case 3://Rescue

					$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit will attempt their rescue mission next turn.";
						if($cnc){
								if($this->eliteMarines){//Are Marines Elite?
									$crit = new RescueMissionElite(-1, $ship->id, $cnc->id, 'RescueMissionElite', $gamedata->turn+1); //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
								}else{//Not Elite Marines
									$crit = new RescueMission(-1, $ship->id, $cnc->id, 'RescueMission', $gamedata->turn+1);  //Takes effect next turn.
									$crit->param = array('id' => $shooter->id, 'userid' => $shooter->userid, 'team' => $shooter->team);
									$crit->updated = true;
									$cnc->criticals[] =  $crit;
									Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.
								}
			            }
				
					break;			
				
			}
		}elseif($deliveryRoll >= 6 && $deliveryRoll <=8){//Unsuccessful delivery
			$this->ammunition++;//Marines weren't eliminated, they just weren't delivered.  Give ammunition back to weapon.
			Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
			$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit was beaten back by defenders but managed to return safely to their pod.";
			Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.							
			return;	
		}else{//Roll result is 9 or over
			$fireOrder->pubnotes .= "<br>Rolled: $deliveryRoll - A marine unit was eliminated by defenders whilst trying to board the enemy ship.";
			Marines::recordClawBoarding($fireOrder->targetid);//Claw missions do NOT count against the pod cap.								
			return;
		}			
	}//endof onDamagedSystem() 		
	

	public function criticalPhaseEffects($ship, $gamedata){	
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.

		foreach ($this->damage as $damage ) if(($damage->turn == $gamedata->turn) && ($damage->destroyed)){ 
			$this->exchangeMarines($ship, $gamedata);
		}
			
	}		
	
	public function exchangeMarines($ship, $gamedata){	
		foreach ($this->damage as $damage ) {
			if(($damage->turn == $gamedata->turn) && ($damage->destroyed)){ 
				$currAmmo = $this->ammunition;//How many marines were left when weapon was destroyed.
				
				if($currAmmo > 0) {
					foreach($ship->systems as $claw){
						if($claw->name == "GrapplingClaw" && $claw->id != $this->id){
							if(!$claw->isDestroyed()){

								$claw->ammunition += $currAmmo; //Add remaining marines to first claw we find, if any.
								$this->ammunition = 0; //Set number of Marines in destroyed Claw to 0.
								Manager::updateAmmoInfo($ship->id, $claw->id, $gamedata->id, $claw->firingMode, $claw->ammunition, $gamedata->turn);
								Manager::updateAmmoInfo($ship->id, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
								break;							
							}	
						}
					}		
				}
			}
		}
	}	

	public function getDamage($fireOrder){ //Damage is handled in criticalPhaseEffects()
		return 0;
	}

	public function setMinDamage(){     $this->minDamage = 0;      }
	public function setMaxDamage(){     $this->maxDamage = 0;      }

	private function sortNotes() {
		usort($this->individualNotes, function($a, $b) {
			// Compare by turn first
			if ($a->turn == $b->turn) {
				// If turns are equal, compare by phase
				return ($a->phase < $b->phase) ? -1 : 1;
			}
			return ($a->turn < $b->turn) ? -1 : 1;
		});
	}

	public function onIndividualNotesLoaded($gamedata)
	{
		//Sort notes by turn, and then phase so latest detection note is always last.
		$this->sortNotes();

		foreach ($this->individualNotes as $currNote) {
			if ($currNote->notekey == "ClawAttached") {
				$this->hostShipId = $currNote->notevalue;
			}
			if ($currNote->notekey == "ClawDetached") {
				$this->hostShipId = -1;
			}			
		}
	}

	public function stripForJson() {
			$strippedSystem = parent::stripForJson();    
			$strippedSystem->ammunition = $this->ammunition;			
			$strippedSystem->isBoardingAction = $this->isBoardingAction;
			$strippedSystem->hostShipId = $this->hostShipId;
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
	protected $hideFireOrdersFromEnemies = true; //psychic activation - invisible to enemies until it resolves (no launch hex/icon)

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
			  if ($ship instanceof Mine) continue;				  	  
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
			if ($ship instanceof Mine) continue;							
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


class PlanetCrackerBeam extends Weapon{
	public $name = "PlanetCrackerBeam";
    public $displayName = "Planet-Cracker Beam";
    public $iconPath = "PlanetCrackerBeam.png";    
	
    public $range = 4;
    public $firingMode = 1;
    public $priorityAF = 1;
    public $loadingtime = 1000;
	//public $hextarget = true;
    public $useOEW = false;
	public $noLockPenalty = false;
    
    public $doNotIntercept = true;
    public $uninterceptable = true;
   	public $ignoreJinking = true;//weapon ignores jinking completely.
	protected $hideFireOrdersFromEnemies = true; //psychic activation - invisible to enemies until it resolves (no launch hex/icon)

    public $rangePenalty = 0;
    public $fireControl = array(null, null, null); // fighters, <mediums, <capitals

	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!   
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	public $firingModes = array(1=> "Planet Cracker"); 

    public $animation = "laser";
    public $animationExplosionScale = 15;   
	public $animationColor = array(216, 192, 42); //yellow
	//public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.  		

	protected $autoHit = true;//To show 100% hit chance in front end.
   	//protected $noTargetHexIcon = true; //For Front End Hex icon display.
	
	public $autoFireOnly = true; //this weapon cannot manually fire by player at a target, just activated	
	
    protected $possibleCriticals = array();	
	protected $shootsStraight = true; //Denotes for Front End to use Line Arcs, not circles.
	protected $specialArcs = true;	//Denotes for Front End to redirect to weapon specific function to get arcs.			
	public $repairPriority = 8;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    public $ignoresLoS = true;	
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 110;
		if ( $powerReq == 0 ) $powerReq = 0;
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
						
		  //Have the beam originate from the firing ship's own hex.
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
		  $relevantHexes = $this->getBeamHexes($thisShip); //the four hexes in a straight line directly in front of the Planet Killer

		  //Make a list of relevant units - EVERY unit standing in the beam, friend or foe (this thing does not discriminate).
		  foreach($allShips as $ship){
			  if($ship === $thisShip) continue; //never itself
			  if($ship->isDestroyed()) continue;
			  if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore targets that are not deployed yet!
			  if (!$this->isUnitInHexes($ship, $relevantHexes)) continue; //only units standing in the swept hexes - a multi-hex Terrain unit counts if ANY of its hexes is swept
			  $relevantShips[] = $ship;
		  }

		  foreach($relevantShips as $target){

			//Now target all units with a new fireOrder.
			//A fighter flight is a single unit holding many fighters, and one order can only ever kill one of them
			//(FighterFlight::getHitSystem allocates one hit to one fighter) - so declare one CALLED order per living
			//fighter and the whole flight goes, which is what "destroys any unit in these hexes" has to mean for a flight.
			if($target instanceof FighterFlight){
				foreach($target->systems as $fighter){
					if($fighter == null) continue;
					if($fighter->isDestroyed()) continue;
					$this->prepareFiringOrder($thisShip, $target, $fighter->id, $gamedata, $hasFireOrder);
				}
			}else{
				$this->prepareFiringOrder($thisShip, $target, -1, $gamedata, $hasFireOrder);
			}

		  }

	  } //endof beforeFiringOrderResolution


	/*The hexes the beam sweeps: a straight line of $this->range hexes directly ahead of the ship, starting one hex
	  out (the firing ship's own hex is never swept). Sized off $this->range so the server sweep and the client's
	  yellow overlay - which sizes itself off the same number - cannot drift apart.*/
	private function getBeamHexes($thisShip){
		$hexes = array();
		$origin = $thisShip->getHexPos();
		$facing = $thisShip->getFacingAngle(); //absolute compass bearing, always a multiple of 60

		for($i = 1; $i <= $this->range; $i++){
			$hexes[] = Mathlib::moveInDirection($origin, $facing, $i);
		}

		return $hexes;
	}//endof getBeamHexes()


	/*Every hex a unit occupies - one hex for an ordinary ship, the whole footprint for a Terrain unit (irregular
	  hexOffsets shape, or circular Huge radius). That is what lets a large moon be caught when the beam clips any
	  part of its area rather than only its centre hex.*/
	private function getUnitOccupiedHexes($unit){
		if ($unit->Huge > 0 || (property_exists($unit, 'hexOffsets') && !empty($unit->hexOffsets))) {
			return RammingAttack::getTerrainOccupiedHexes($unit);
		}

		return array($unit->getHexPos());
	}//endof getUnitOccupiedHexes()


	//True when any hex the unit occupies is one of the swept hexes.
	private function isUnitInHexes($unit, $hexes){
		foreach($this->getUnitOccupiedHexes($unit) as $unitHex){
			foreach($hexes as $hex){
				if($hex->q == $unitHex->q && $hex->r == $unitHex->r) return true;
			}
		}

		return false;
	}//endof isUnitInHexes()


	/*One damage-dealing order per victim. $calledId names a particular fighter of a flight (-1 for everything else).

	  Persisted IMMEDIATELY rather than left at id -1 with addToDB: damage records copy $fireOrder->id at the moment
	  damage is dealt, and DBManager::submitDamages back-fills a -1 by querying "same gameid+turn+shooterid+weaponid,
	  targetid = target OR -1, shotshit > 0" and taking the FIRST row, with no ORDER BY. This weapon always has
	  several hitting orders under one weapon id (the activation marker plus one per victim), so without a real id
	  every one of them risks having its damage logged against a sibling.*/
	private function prepareFiringOrder($shooter, $target, $calledId, $gamedata, $originalFireOrder){

		$newFireOrder = new FireOrder(
			-1, "normal", $shooter->id, $target->id,
			$this->id, $calledId, $gamedata->turn, $this->firingMode,
			100, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
			$originalFireOrder->x, $originalFireOrder->y, $this->weaponClass, -1 //X, Y, damageclass, resolutionorder
		);
		$newFireOrder->pubnotes = " Caught in the Planet-Cracker Beam.";

		$newFireOrder->addToDB = true;
		$newId = Manager::insertSingleFiringOrder($gamedata, $newFireOrder);
		if ($newId) {
			$newFireOrder->id = (int)$newId;
		}
		$newFireOrder->addToDB = false; //already in the DB - stops FireGamePhase inserting a duplicate
		$newFireOrder->updated = true;  //but DO write back the rolled/notes/shotshit set during resolution

		$this->fireOrders[] = $newFireOrder;

	}//endof function prepareFiringOrder

	public function calculateHitBase($gamedata, $fireOrder)
		{
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
		}              

    public function fire($gamedata, $fireOrder)
    {
		//The activation order itself carries no target - it only marks the sweep in the combat log.
		//The per-victim orders built in beforeFiringOrderResolution are what actually deal the damage.
		if ($fireOrder->targetid == -1) {
			$fireOrder->needed = 100;
			$fireOrder->rolled = 0;
			$fireOrder->shotshit = 0;
			$fireOrder->pubnotes .= "<br>Planet-Cracker Beam sweeps the four hexes ahead.";
			return;
		}

		parent::fire($gamedata, $fireOrder); //auto-hits on needed = 100, then allocates getDamage() as normal
	}
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Special"] = 'Fire this weapon by clicking on icon and selecting Fire during the Firing phase.';
		$this->data["Special"] .= '<br>Automatically destroys any unit in the four hexes directly in front of ship.';		
	}	

    public function getDamage($fireOrder){        return 10000;   }
    public function setMinDamage(){     $this->minDamage = 10000 ;      }
    public function setMaxDamage(){     $this->maxDamage = 10000 ;      }
    
    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->shootsStraight = $this->shootsStraight;
        $strippedSystem->specialArcs = $this->specialArcs;
        $strippedSystem->autoHit = $this->autoHit;															                                        
        return $strippedSystem;
	} 
	
} //endof class PlanetCrackerBeam



class NeutronBurst extends Weapon {
    public $name        = "NeutronBurst";
    public $displayName = "Neutron Burst";
    public $iconPath    = "NeutronBlaster.png";
    public $animation      = "laser";
    public $animationColor = array(180, 255, 180);
    public $damageType  = "Raking";
    public $weaponClass = "Electromagnetic";
    public $firingModes = array(1 => "Raking");
    public $rangePenalty = 0.5;   // -5% per 2 hexes
    public $loadingtime  = 1;
    public $fireControl  = array(2, 5, 5);
    public $uninterceptable = true;

	public $factionAge = 4;//Ancient weapon, which sometimes has consequences!

    // Tracks system+order pairs already processed for Shadow vessels
    public $shadowEffectsApplied = array();
    // Tracks turns on which structure power loss has already been applied
    public $structurePowerLossApplied = array();

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $this->animationExplosionScale = $this->dynamicScale(0, 2);
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        $this->data["Special"]  = "Electromagnetic class, Raking mode. Primordial technology — unaffected by advanced armor or EM resistance.";
        $this->data["Special"] .= "<br>Structure hits: -2 power output for one turn (once per firing).";
        $this->data["Special"] .= "<br>Capacitor hits: drains 2 stored power (no critical roll).";
        $this->data["Special"] .= "<br>Weapon hits: system deactivated for one turn, requires manual reactivation. Power from deactivated system is lost for one turn.";
        $this->data["Special"] .= "<br>Capacitor-using vessels: hitting a weapon also drains the capacitor by that weapon's minimum firing cost.";
        $this->data["Special"] .= "<br>Non-weapon, non-powered system hits: +5 to critical roll (forced).";
        $this->data["Special"] .= "<br>Fighter hits: forced dropout. Superheavy fighters: standard dropout roll.";
        $this->data["Special"] .= "<br>All effects require at least 1 point of damage to trigger.";
        $this->data["Special"] .= "<br>Shadow Association vessels: all effects apply even if damage is fully absorbed.";
    }

    public function getDamage($fireOrder)  { return Dice::d(10, 4) + 8; }
    public function setMinDamage()         { $this->minDamage = 12; }
    public function setMaxDamage()         { $this->maxDamage = 48; }

    private function isShadow($ship) {
        return isset($ship->faction) && $ship->faction === "Shadow Association";
    }

    // -------------------------------------------------------------------------
    // Helper: get minimum capacitor firing cost for a given weapon system
    // -------------------------------------------------------------------------
    private function getMinCapacitorCost($system) {
        // Weapons with powerRequiredArray: minimum is mode 1, power element
        if (isset($system->powerRequiredArray[1][1])) {
            return $system->powerRequiredArray[1][1];
        }
        // Discharge weapons: minimum cost from their single-shot drain
        if ($system instanceof VorlonDischargeCannon) return 5;
        if ($system instanceof VorlonDischargePulsar)  return 4;
        if ($system instanceof VorlonDischargeGun)     return 2;
        // Standard weapon with explicit powerReq
        if ($system->powerReq > 0) return $system->powerReq;
        return 0;
    }

    // -------------------------------------------------------------------------
    // Core effects — applied per damaged system
    // -------------------------------------------------------------------------
    private function applyNeutronEffects($ship, $system, $damage, $gamedata, $fireOrder) {

        // Effects require at least 1 point of damage
        if ($damage < 1) return;

        // --- Fighter hit ---
        if ($system instanceof Fighter) {
            if (!$ship->superheavy) {
                $crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
                $crit->updated = true;
                $crit->inEffect = true;
                $system->setCritical($crit);
                $fireOrder->pubnotes .= " DROPOUT! ";
            } else {
                $crits = array();
                $crits = $system->testCritical($ship, $gamedata, $crits);
                foreach ($crits as $crit) {
                    $crit->updated = true;
                }
            }
            return;
        }

        // --- Structure hit: -2 power for one turn (once per firing) ---
        if ($system instanceof Structure) {
            if (!in_array($gamedata->turn, $this->structurePowerLossApplied)) {
                $this->structurePowerLossApplied[] = $gamedata->turn;
                $capacitor = $ship->getSystemByName("PowerCapacitor");
                if ($capacitor) {
                    // Capacitor vessel: drain 2 from stored charge
                    $capacitor->doDrawPower(2);
                    $fireOrder->pubnotes .= " [Neutron Burst: -2 capacitor charge (structure)] ";
                } else {
                    // Reactor vessel: apply two one-turn OutputReduced1 criticals
                    $reactor = $ship->getSystemByName("Reactor");
                    if ($reactor) {
                        $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn + 1);
                        $crit->updated = true;
                        $reactor->setCritical($crit);
                        $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn + 1);
                        $crit->updated = true;
                        $reactor->setCritical($crit);
                        $fireOrder->pubnotes .= " [Neutron Burst: -2 power output next turn] ";
                    }
                }
            }
            $system->forceCriticalRoll = true;
            $system->critRollMod += 5;
            return;
        }

        // --- Capacitor hit: drain 2 stored power, no critical roll ---
        if ($system instanceof PowerCapacitor) {
            $system->doDrawPower(2);
            $fireOrder->pubnotes .= " [Neutron Burst: -2 capacitor charge] ";
            return;
        }

        // --- Weapon hit: deactivate for one turn, power is lost ---
        if ($system instanceof Weapon) {
            if (!$system->isDestroyed()) {
                $system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
                $fireOrder->pubnotes .= " [Neutron Burst: weapon deactivated] ";
                $capacitor = $ship->getSystemByName("PowerCapacitor");
                if ($capacitor) {
                    // Capacitor vessel: drain powerReq (power loss) + minimum firing cost
                    $powerLoss = $system->powerReq; // 0 for Vorlon weapons, non-zero for others
                    $minCost   = $this->getMinCapacitorCost($system);
                    $totalDrain = $powerLoss + $minCost;
                    if ($totalDrain > 0) {
                        $capacitor->doDrawPower($totalDrain);
                        $fireOrder->pubnotes .= " [Neutron Burst: -{$totalDrain} capacitor charge] ";
                    }
                } else {
                    // Reactor vessel: apply OutputReduced1 per point of powerReq
                    if ($system->powerReq > 0) {
                        $reactor = $ship->getSystemByName("Reactor");
                        if ($reactor) {
                            for ($i = 0; $i < $system->powerReq; $i++) {
                                $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn + 1);
                                $crit->updated = true;
                                $reactor->setCritical($crit);
                            }
                        }
                    }
                }
            }
            // No forced critical roll — weapons roll normally
            return;
        }

        // --- Non-weapon, non-powered system hit: forced +5 critical roll ---
        // Covers engines, thrusters, sensors, C&C, etc.
        if ($system->powerReq > 0 || (!empty($system->canOffLine))) {
            $system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
            $fireOrder->pubnotes .= " [Neutron Burst: system deactivated] ";
            if ($system->powerReq > 0) {
                $reactor = $ship->getSystemByName("Reactor");
                if ($reactor) {
                    for ($i = 0; $i < $system->powerReq; $i++) {
                        $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn + 1);
                        $crit->updated = true;
                        $reactor->setCritical($crit);
                    }
                }
            }
            return;
        }

        // Truly non-powered system (structure excluded above): +5 forced crit
        $system->forceCriticalRoll = true;
        $system->critRollMod += 5;

    } // end applyNeutronEffects

    // -------------------------------------------------------------------------
    // Shadow exception: apply effects before absorption so they always fire.
    // -------------------------------------------------------------------------
    public function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder) {
        if ($this->isShadow($ship)) {
            $key = $system->id . '_' . $fireOrder->id;
            if (!in_array($key, $this->shadowEffectsApplied)) {
                $this->shadowEffectsApplied[] = $key;
                $this->applyNeutronEffects($ship, $system, $damage, $gamedata, $fireOrder);
            }
        }
        return parent::beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
    }

    // -------------------------------------------------------------------------
    // Normal hits: apply effects when damage lands.
    // Shadow vessels: already handled in beforeDamagedSystem, skip here.
    // -------------------------------------------------------------------------
    public function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder) {
        if (!$this->isShadow($ship)) {
            $this->applyNeutronEffects($ship, $system, $damage, $gamedata, $fireOrder);
        }
        parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
    }

} // end class NeutronBurst







// =============================================================================
// SingularityMine — Primordial Gravitic proximity weapon
// =============================================================================

class SingularityMine extends AoE {

    public $name        = "SingularityMine";
    public $displayName = "Singularity Mine";
    public $iconPath    = "SingularityMine.png";

    public $animation              = "ball";
    public $animationColor         = array(120, 0, 180);
    public $animationExplosionScale = 2;
    public $animationExplosionType  = "AoE";
    public $explosionColor          = array(120, 0, 180);

    public $weaponClass = "Gravitic";
    public $damageType  = "Flash";
    public $flashDamage = true;

    public $ballistic    = true;
    public $hextarget    = true;
    public $hidetarget   = true;
    public $priority     = 1;
    public $factionAge   = 4;
//    public $preFires     = true;

    public $range        = 120;
    public $loadingtime  = 3;
    public $rangePenalty = 0;
    public $uninterceptable = true;
    public $doNotIntercept  = true;

    public $spawnableClasses = array('spawnSingularity');

    public $firingModes = array(
        1 => "Clockwise",
        2 => "Anti-Clockwise"
    );

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        if ($maxhealth == 0) $maxhealth = 28;
        if ($powerReq  == 0) $powerReq  = 16;
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        $this->data["Special"]  = "Primordial Gravitic weapon. Ballistic hex-targeted mine.";
        $this->data["Special"] .= "<br>On detonation: all enemy units within 10 hexes take gravitic Flash damage = Ramming Factor / (5 x Range). Minimum range 1.";
        $this->data["Special"] .= "<br>Units at range 0 take damage as range 1 then immediately roll on the Singularity entry table.";
        $this->data["Special"] .= "<br>A singularity forms in the target hex (blocks LOS). Select Clockwise or Anti-Clockwise spin.";
        $this->data["Special"] .= "<br>Turn N+1: all units within 50 hexes affected by gravitational movement. Turn N+2: 25 hex range. Turn N+3: dissipates.";
        $this->data["Special"] .= "<br>Two singularities with opposing spin directions cancel each other out.";
    }

    public function getDamage($fireOrder)  { return 0; }
    public function setMinDamage()         { $this->minDamage = 0; }
    public function setMaxDamage()         { $this->maxDamage = 0; }

    // -------------------------------------------------------------------------
    // fire
    // -------------------------------------------------------------------------
    public function fire($gamedata, $fireOrder) {
        $this->changeFiringMode($fireOrder->firingMode);
        $shooter  = $gamedata->getShipById($fireOrder->shooterid);
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $posLaunch = $movement->position;

        if ($fireOrder->targetid != -1) {
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            $movement   = $targetship->getLastTurnMovement($fireOrder->turn);
            $fireOrder->x = $movement->position->q;
            $fireOrder->y = $movement->position->r;
            $fireOrder->targetid = -1;
        }

        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);
        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled;

        if ($rolled > $fireOrder->needed) {
            $fireOrder->pubnotes .= "Mine dissipates without effect. ";
            return;
        }

        $fireOrder->shotshit++;

        // Scatter
        if ($rolled > 75) {
            $maxdis    = $posLaunch->distanceTo($target);
            $dis       = min(Dice::d(6), floor($maxdis));
            $direction = Dice::d(6) - 1;
            $target    = $target->moveToDirection($direction, $dis);
            $fireOrder->pubnotes .= " Deviation from " . $fireOrder->x . ' ' . $fireOrder->y;
            $fireOrder->x = $target->q;
            $fireOrder->y = $target->r;
            $fireOrder->pubnotes .= " to " . $fireOrder->x . ' ' . $fireOrder->y . '. ';
            $fireOrder->pubnotes .= "Mine deviates $dis hexes. ";
        }

        $spinDirection = ($fireOrder->firingMode == 1) ? "CW" : "CCW";
        $spinLabel     = ($spinDirection == "CW") ? "Clockwise" : "Anti-Clockwise";
        $fireOrder->pubnotes .= "<br>Singularity forms! Spin: $spinLabel. ";

        // --- Multi-mine cancellation check ---
        foreach ($gamedata->ships as $ship) {
            if (!($ship instanceof spawnSingularity)) continue;
            if ($ship->isDestroyed()) continue;
            $existingSpin = spawnSingularity::decodeSpinFromName($ship->name);
            if ($existingSpin !== null && $existingSpin !== $spinDirection) {
                $structure = $ship->getSystemByName("Structure");
                if ($structure && !$structure->isDestroyed()) {
                    $damageEntry = new DamageEntry(
                        -1, $ship->id, $gamedata->id, $gamedata->turn, $structure->id,
                        $structure->maxhealth, 0, 0, -1, true, false,
                        "Opposing singularities cancel each other out", "Standard"
                    );
                    $damageEntry->updated = true;
                    $structure->damage[] = $damageEntry;
                }
                $fireOrder->pubnotes .= "<br>Opposing singularity detected — both singularities cancel each other out!";
                return;
            }
        }

        // --- Radial damage: enemy units within 10 hexes ---
        $processedIds = array();
        for ($r = 0; $r <= 10; $r++) {
            $shipsAtRange = $gamedata->getShipsInDistance($target, $r);
            foreach ($shipsAtRange as $id => $targetShip) {
                if (isset($processedIds[$id])) continue;
                $processedIds[$id] = true;

                if ($targetShip->isDestroyed()) continue;
                if ($targetShip->mine) continue;
                if ($targetShip->isTerrain()) continue;
                if ($targetShip->slot == $shooter->slot) continue;

                $effectiveRange = max(1, $r);
                $rammingFactor  = $targetShip->getRammingFactor();
                $damage         = floor($rammingFactor / (5 * $effectiveRange));
                if ($damage <= 0) continue;

                if ($targetShip instanceof FighterFlight) {
                    foreach ($targetShip->systems as $fighter) {
                        if ($fighter == null || $fighter->isDestroyed()) continue;
                        $this->doDamage($targetShip, $shooter, $fighter, $damage, $fireOrder, $target, $gamedata, false);
                    }
                } else {
                    $tmpLocation = $targetShip->getHitSectionPos(Mathlib::hexCoToPixel($target), $fireOrder->turn);
                    $system = $targetShip->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
                    $this->doDamage($targetShip, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
                }

                $fireOrder->pubnotes .= "<br>" . $targetShip->name . " takes $damage gravitic Flash damage (range $effectiveRange).";

                if ($r == 0) {
                    SingularityRammingAttack::applyEntryTable($targetShip, $shooter, $fireOrder, $target, $gamedata);
                }
            }
        }

        // --- Spawn singularity terrain ---
        $this->spawnSingularityTerrain($gamedata, $fireOrder, $shooter, $target, $spinDirection);

        $fireOrder->rolled = max(1, $fireOrder->rolled);
        $fireOrder->updated = true;
    }

    private function spawnSingularityTerrain($gamedata, $fireOrder, $shooter, $targetHex, $spinDirection) {
        $name = "SG" . $gamedata->turn . $spinDirection;

        $singularity = new spawnSingularity($gamedata->id, -5, $name, $shooter->slot);

        $shipid = Manager::insertSingleShip($gamedata, $singularity, -5);
        $singularity->id = $shipid;

        $deployMove = new MovementOrder(
            null, "deploy",
            new OffsetCoordinate($targetHex->q, $targetHex->r),
            0, 0, 0, 0, 0, false, $gamedata->turn, 0, 0
        );
        Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);

        SystemData::initSystemData($gamedata->turn, $gamedata->id);
        foreach ($singularity->systems as $system) {
            $system->setInitialSystemData($singularity);
        }
        Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

        $spinLabel = ($spinDirection == "CW") ? "Clockwise" : "Anti-Clockwise";
        $fireOrder->pubnotes .= "<br>Singularity ($spinLabel) formed at " . $targetHex->q . ' ' . $targetHex->r . '.';
    }
}


// =============================================================================
// SingularityRammingAttack — replaces RammingAttack on spawnSingularity
//
// Overrides beforeDamage to apply the Singularity entry table using the
// ResonanceGenerator pattern: each hit is applied separately by setting
// chosenLocation, then calling $this->damage().
// getDamage returns 0 — all damage handled in beforeDamage.
// =============================================================================

class SingularityRammingAttack extends RammingAttack {

    public $name        = "SingularityRammingAttack";
    public $displayName = "Singularity";
    public $damageType  = "Standard";
    public $weaponClass = "Gravitic";
    public $factionAge  = 4;

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

public function getDamage($fireOrder) {
    if ($fireOrder->damageclass === 'SingularityCollision') {
        return 1; // non-zero so Weapon::fire() calls beforeDamage
    }
    return 0;
}

public function calculateHitBase($gamedata, $fireOrder) {
    if ($fireOrder->damageclass === 'SingularityCollision') {
        $fireOrder->needed  = 100;
        $fireOrder->rolled  = 1;
        $fireOrder->shotshit = 1;
        $fireOrder->shots   = 1;
        return;
    }
    parent::calculateHitBase($gamedata, $fireOrder);
}

    // -------------------------------------------------------------------------
    // beforeDamage: called before damage is applied.
    // Rolls entry table and applies hits separately per section.
    // Mirrors ResonanceGenerator::beforeDamage pattern.
    // -------------------------------------------------------------------------
    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata) {
        if ($fireOrder->damageclass !== 'SingularityCollision') {
            parent::beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
            return;
        }

        if ($target->isDestroyed()) return;

        self::applyEntryTable($target, $shooter, $fireOrder, $shooter->getHexPos(), $gamedata, $this);
    }

    // -------------------------------------------------------------------------
    // applyEntryTable: static so it can be called from SingularityMine::fire()
    // for range-0 detonation hits.
    // -------------------------------------------------------------------------
    public static function applyEntryTable($target, $shooter, $fireOrder, $singularityHex, $gamedata, $weapon = null) {
        $lastMove = $target->getLastMovement();
        $speed    = $lastMove ? $lastMove->speed : 0;

        // Edge damage by ship type
        if ($target instanceof FighterFlight) {
            $damage = floor($speed * 0.5);
        } else if ($target->Enormous) {
            $damage = $speed * 10;
        } else {
            $structureCount = count($target->getSystemsByName("Structure", false));
            if ($structureCount >= 4) {
                $damage = $speed * 6;      // Capital
            } else if ($structureCount == 3) {
                $damage = $speed * 4;      // Heavy combat vessel
            } else {
                $damage = $speed * 2;      // LCV/MCV
            }
        }

        $modifier = 0;
        if ($target->Enormous) $modifier += 2;
        if (!empty($target->agile)) $modifier -= 1;

        $roll = Dice::d(20) + $modifier;
        $modStr = ($modifier != 0) ? " (modifier: " . ($modifier > 0 ? "+$modifier" : $modifier) . ")" : "";
        $fireOrder->pubnotes .= " Singularity entry roll: $roll$modStr. ";

        // Get the singularity terrain as the effective shooter for damage calls
        // If no weapon passed (called from SingularityMine), find the singularity's weapon
        if ($weapon === null) {
            foreach ($gamedata->ships as $ship) {
                if (!($ship instanceof spawnSingularity)) continue;
                $w = $ship->getSystemByName("SingularityRammingAttack");
                if ($w) { $weapon = $w; break; }
            }
        }
        if ($weapon === null) return; // no weapon found, can't apply damage

        if ($roll <= 4) {
            // Ride the edge — standard damage to facing section
            $fireOrder->pubnotes .= "Rides the edge! $damage standard damage to facing section. ";
            $fireOrder->chosenLocation = $fireOrder->chosenLocation ?? 1;
            $weapon->damage($target, $shooter, $fireOrder, $gamedata, $damage, false);

        } else if ($roll <= 8) {
            // Flung — edge damage to facing section + primary hit + movement
            $throwDis  = Dice::d(6) + 1;
            $throwDir  = Dice::d(6) - 1;
            $newPos    = $singularityHex->moveToDirection($throwDir, $throwDis);
            $lastMove  = $target->getLastMovement();
            $newFacing = Dice::d(6) - 1;
            $throwMove = new MovementOrder(
                null, "prefire",
                new OffsetCoordinate($newPos->q, $newPos->r),
                0, 0, $speed, $throwDir, $newFacing,
                false, $gamedata->turn, $fireOrder->id, 0
            );
            Manager::insertSingleMovement($gamedata->id, $target->id, $throwMove);
            $target->setMovement($throwMove);

            // Edge damage to facing section
            $savedLocation = $fireOrder->chosenLocation;
            $fireOrder->pubnotes .= "Flung outward $throwDis hexes! $damage standard damage to facing section. ";
            $weapon->damage($target, $shooter, $fireOrder, $gamedata, $damage, false);

            // Primary hit — change chosenLocation to primary
            $fireOrder->chosenLocation = 0;
            $fireOrder->pubnotes .= "$damage Primary hit. Involuntary pivot. ";
            $weapon->damage($target, $shooter, $fireOrder, $gamedata, $damage, true);
            $fireOrder->chosenLocation = $savedLocation;

        } else if ($roll <= 15) {
            // Expelled — edge damage then destroy
            $fireOrder->pubnotes .= "Expelled into hyperspace! $damage standard damage. Unit expelled from battle. ";
            $fireOrder->chosenLocation = $fireOrder->chosenLocation ?? 1;
            $weapon->damage($target, $shooter, $fireOrder, $gamedata, $damage, false);
            $primaryStruct = $target->getStructureSystem(0);
            if ($primaryStruct && !$primaryStruct->isDestroyed()) {
                $remaining = $primaryStruct->getRemainingHealth();
                $destroyEntry = new DamageEntry(
                    -1, $target->id, $gamedata->id, $gamedata->turn,
                    $primaryStruct->id, $remaining, 0, 0,
                    $fireOrder->id, true, false,
                    "Singularity — unit expelled into hyperspace", "Standard"
                );
                $destroyEntry->updated = true;
                $primaryStruct->damage[] = $destroyEntry;
            }

        } else {
            // Destroyed outright
            $fireOrder->pubnotes .= "Destroyed by the singularity! No possibility of survival. ";
            $primaryStruct = $target->getStructureSystem(0);
            if ($primaryStruct && !$primaryStruct->isDestroyed()) {
                $remaining = $primaryStruct->getRemainingHealth();
                $destroyEntry = new DamageEntry(
                    -1, $target->id, $gamedata->id, $gamedata->turn,
                    $primaryStruct->id, $remaining, 0, 0,
                    $fireOrder->id, true, false,
                    "Singularity — unit destroyed", "Standard"
                );
                $destroyEntry->updated = true;
                $primaryStruct->damage[] = $destroyEntry;
            }
        }
    }
}


// =============================================================================
// SingularityCore — system on spawnSingularity that handles per-turn effects
// =============================================================================

class SingularityCore extends ShipSystem {

    public $name        = "SingularityCore";
    public $displayName = "Singularity Core";
    public $iconPath    = "SingularityIcon.png";

    private static $processedThisTurn = array();

    function __construct($armour, $maxhealth, $powerReq, $startArc = 0) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc);
    }

    public function getSpawnTurn() {
        $ship = $this->getUnit();
        if (!$ship) return 0;
        return spawnSingularity::decodeSpawnTurnFromName($ship->name);
    }

    public function getSpinDirection() {
        $ship = $this->getUnit();
        if (!$ship) return "CW";
        return spawnSingularity::decodeSpinFromName($ship->name) ?? "CW";
    }

public function generateIndividualNotes($gameData, $dbManager) {
    $singularity = $this->getUnit();
    if (!$singularity || $singularity->isDestroyed()) return;

    $spawnTurn  = $this->getSpawnTurn();
    if ($spawnTurn <= 0) return;

    $turnsAlive = $gameData->turn - $spawnTurn;

    if ($gameData->phase === 4) {
        if ($turnsAlive >= 2) {
            $structure = $singularity->getSystemByName("Structure");
            if ($structure && !$structure->isDestroyed()) {
                $damageEntry = new DamageEntry(
                    -1, $singularity->id, $gameData->id, $gameData->turn, $structure->id,
                    $structure->maxhealth, 0, 0, -1, true, false,
                    "Singularity dissipates", "Standard"
                );
                $damageEntry->updated = true;
                $structure->damage[] = $damageEntry;
            }
        }
    }

    if ($gameData->phase !== 4) return;

    $key = $singularity->id . '_' . $gameData->turn . '_' . $gameData->phase;
    if (isset(self::$processedThisTurn[$key])) return;
    self::$processedThisTurn[$key] = true;

    if ($turnsAlive <= 0 || $turnsAlive >= 3) return;

    $maxRange   = ($turnsAlive == 1) ? 50 : 25;
    $innerRange = ($turnsAlive == 1) ? 10 : 5;
    $singularityHex = $singularity->getHexPos();

    // Apply gravitational movement to all ships in range
    foreach ($gameData->ships as $ship) {
        if ($ship->isDestroyed()) continue;
        if ($ship->isTerrain()) continue;
        if ($ship->mine) continue;
        if ($ship->getTurnDeployed($gameData) > $gameData->turn) continue;

        $shipHex  = $ship->getHexPos();
        $distance = $singularityHex->distanceTo($shipHex);

        if ($distance > $maxRange) continue;
        if ($distance == 0) continue;

        $pullHexes = ($distance <= $innerRange) ? 2 : 1;

        $this->applySpiralShift($ship, $singularityHex, $shipHex, $gameData);

        for ($i = 0; $i < $pullHexes; $i++) {
            $currentHex = $ship->getHexPos();
            $this->applyRadialPull($ship, $singularityHex, $currentHex, $gameData);
        }
    }

    // Detect ships that will be pulled into singularity hex and create collision fire orders
    // These are saved to DB now (phase 4) so the prefiring phase picks them up
    $rammingSystem = $singularity->getSystemByName("SingularityRammingAttack");
    if ($rammingSystem) {
        foreach ($gameData->ships as $ship) {
            if ($ship->isDestroyed()) continue;
            if ($ship->isTerrain()) continue;
            if ($ship->mine) continue;
            if ($ship->id === $singularity->id) continue;

            $shipHex  = $ship->getHexPos();
            $distance = $singularityHex->distanceTo($shipHex);

            if ($distance > $innerRange) continue;
            $pullHexes = 2;
            if ($distance > $pullHexes) continue;

            // Check not already created for this ship this turn
            $alreadyCreated = false;
            foreach ($rammingSystem->fireOrders as $fo) {
                if ($fo->targetid == $ship->id && $fo->turn == $gameData->turn &&
                    $fo->damageclass == 'SingularityCollision') {
                    $alreadyCreated = true;
                    break;
                }
            }
            if ($alreadyCreated) continue;

            $fireOrder = new FireOrder(
                -1, "prefiring", $singularity->id, $ship->id,
                $rammingSystem->id, -1, $gameData->turn, 1,
                100, 100, 1, 1, 0, 0, 0, 'SingularityCollision', -1
            );
$fireOrder->pubnotes = "Unit pulled into singularity! ";
SingularityRammingAttack::applyEntryTable(
    $ship, $singularity, $fireOrder, $singularityHex, $gameData, $rammingSystem
);
$fireOrder->id = (int)$dbManager->submitSingleFireorder($gameData->id, $fireOrder);
$rammingSystem->fireOrders[] = $fireOrder;
        }
    }
}

    private function applySpiralShift($ship, $singularityHex, $shipHex, $gameData) {
        $currentDistance = $singularityHex->distanceTo($shipHex);
        $dirToShip = $this->getHexDirection($singularityHex, $shipHex);
        $rotation  = ($this->getSpinDirection() === "CW") ? 1 : -1;

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $spiralDir     = ($dirToShip + ($rotation * $attempt) + 6) % 6;
            $candidateHex  = $shipHex->moveToDirection($spiralDir, 1);
            $candidateDist = $singularityHex->distanceTo($candidateHex);
            if ($candidateDist <= $currentDistance) {
                $lastMove   = $ship->getLastMovement();
                $spiralMove = new MovementOrder(
                    null, "prefire",
                    new OffsetCoordinate($candidateHex->q, $candidateHex->r),
                    0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing,
                    false, $gameData->turn, 0, 0
                );
                Manager::insertSingleMovement($gameData->id, $ship->id, $spiralMove);
                $ship->setMovement($spiralMove);
                return;
            }
        }
    }

	private function applyRadialPull($ship, $singularityHex, $shipHex, $gameData) {
		$distance = $singularityHex->distanceTo($shipHex);
		if ($distance == 0) return;

		// Normal pull — move one hex closer
		$dirToSingularity = $this->getHexDirection($shipHex, $singularityHex);
		$pullHex = $shipHex->moveToDirection($dirToSingularity, 1);
		$lastMove = $ship->getLastMovement();
		$pullMove = new MovementOrder(
			null, "prefire",
			new OffsetCoordinate($pullHex->q, $pullHex->r),
			0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing,
			false, $gameData->turn, 0, 0
		);
		Manager::insertSingleMovement($gameData->id, $ship->id, $pullMove);
		$ship->setMovement($pullMove);
	}

    private function getHexDirection($fromHex, $toHex) {
        $bestDir  = 0;
        $bestDist = PHP_INT_MAX;
        for ($dir = 0; $dir < 6; $dir++) {
            $adjacent = $fromHex->moveToDirection($dir, 1);
            $dist     = $adjacent->distanceTo($toHex);
            if ($dist < $bestDist) {
                $bestDist = $dist;
                $bestDir  = $dir;
            }
        }
        return $bestDir;
    }
}


// =============================================================================
// spawnSingularity — persistent terrain
// =============================================================================

class spawnSingularity extends Terrain {

    public $terrainCollisionType = 'SingularityCollision';
    public $Enormous    = true;
    public $mine        = true;
    public $weaponClass = "Gravitic";

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);

        $this->pointCost     = 0;
        $this->faction       = "Terrain";
        $this->factionAge    = 4;
        $this->phpclass      = "spawnSingularity";
        $this->imagePath     = "img/ships/Singularity.png";
        $this->canvasSize    = 200;
        $this->shipClass     = "Singularity";
        $this->Enormous      = true;
        $this->mine          = true;
        $this->iniativebonus = -200;
        $this->isd           = 0;
        $this->notes         = "Blocks line of sight.";
        $this->notes        .= "<br>Units entering this hex roll d20 on the Singularity entry table.";
        $this->notes        .= "<br>All units within range are subject to gravitational movement effects each turn.";
        $this->occurence     = "common";

        $this->base        = true;
        $this->smallBase   = true;
        $this->nonRotating = true;

        $this->forwardDefense = 20;
        $this->sideDefense    = 20;

        $this->turncost      = 0;
        $this->turndelaycost = 0;
        $this->accelcost     = 0;
        $this->rollcost      = 0;
        $this->pivotcost     = 0;

        Enhancements::nonstandardEnhancementSet($this, 'Terrain');
        $this->addPrimarySystem(new SingularityRammingAttack(0, 1, 0, 0, 360));
        $this->addPrimarySystem(new SingularityCore(0, 1, 0, 0));
        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(8, 300));

        $this->hitChart = array(
            0 => array(20 => "Structure"),
            1 => array(20 => "Primary"),
            2 => array(20 => "Primary"),
        );
    }

    public static function decodeSpawnTurnFromName($name) {
        if (preg_match('/^SG(\d+)(CW|CCW)$/', $name, $m)) {
            return (int)$m[1];
        }
        return 0;
    }

    public static function decodeSpinFromName($name) {
        if (preg_match('/^SG(\d+)(CW|CCW)$/', $name, $m)) {
            return $m[2];
        }
        return null;
    }

    public function stripForJson() {
        $stripped = parent::stripForJson();
        $stripped->spawnTurn     = self::decodeSpawnTurnFromName($this->name);
        $stripped->spinDirection = self::decodeSpinFromName($this->name);
        return $stripped;
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

    public $animation = "laser";
    public $animationColor = array(100, 0, 200);
    public $animationExplosionScale = 0.4;

    public $firingModes = array(1 => "Spatial Cutter");

	public $factionAge = 4; //Primordial

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        $this->data["Special"] = "Gravitic class weapon. Raking 15. Requires lock-on to fire.";
        $this->data["Special"] .= "<br>Uninterceptable. Can intercept uninterceptable weapons.";
        $this->data["Special"] .= "<br>On hit: creates a hyperspace waveform along the line of fire.";
        $this->data["Special"] .= "<br>Waveform is active for one turn. Units entering a waveform hex take speed x size factor damage per hex.";
        $this->data["Special"] .= "<br>Units without advanced armor take double waveform damage.";
    }

    public function getDamage($fireOrder) {
        return Dice::d(10, 8) + 20;
    }

    public function setMinDamage() { $this->minDamage = 28; }
    public function setMaxDamage() { $this->maxDamage = 100; }

    public function calculateHitBase($gamedata, $fireOrder) {
        $firingShip = $gamedata->getShipById($fireOrder->shooterid);
        $target     = $gamedata->getShipById($fireOrder->targetid);

        // Require lock-on — if no OEW on target, auto-miss with note
        if ($target && $firingShip->getOEW($target, $gamedata->turn) <= 0) {
            $fireOrder->needed   = 0;
            $fireOrder->shotshit = 0;
            $fireOrder->pubnotes .= "No lock-on — Spatial Cutter cannot fire. ";
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
                // spawnTurn = current turn so:
                // Turn N:   waveform exists but no collision damage (units already in position)
                // Turn N+1: units moving through take WaveformCollision damage
                // Turn N+1 end: generateIndividualNotes silently removes waveforms (no animation)
                $lineHexes = self::getHexLine($shooter->getHexPos(), $target->getHexPos());
                foreach ($lineHexes as $hex) {
                    $this->spawnWaveformAtHex($gamedata, $shooter, $hex);
                }
            }
        }
    }

    // Runs at the END of the fire phase (phase 4) on turn N+1 — after all
    // firing, criticals, and animations are resolved. Removes expired waveforms
    // silently (no DamageEntry = no explosion animation in the replay).
    // This ensures waveforms are gone before movement on turn N+2.
	public function generateIndividualNotes($gameData, $dbManager) {
		switch ($gameData->phase) {
			case 4:
				$ship = $this->getUnit();
				if ($this->isDestroyed() || !$ship || $ship->isDestroyed()) break;

				foreach ($gameData->ships as $waveShip) {
					if (!($waveShip instanceof spawnHyperspaceWaveform)) continue;
					$waveShip->loadSpawnTurn();
					if ($waveShip->spawnTurn > 0 && $gameData->turn >= $waveShip->spawnTurn + 1) {
						$structure = $waveShip->getSystemByName("Structure");
						if ($structure && !$structure->isDestroyed()) {
							$damageEntry = new DamageEntry(
								-1, $waveShip->id, $gameData->id, $gameData->turn, $structure->id,
								$structure->maxhealth, 0, 0, -1, true, false,
								"Waveform dissipated", "Standard"
							);
							$damageEntry->updated = true;
							$structure->damage[] = $damageEntry;
						}
					}
				}
				break;
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

    /* Moved to HexZone::line() - the Walkers' EDF targeting corridor and Sensor Charge
       Transceiver need the same tracer and neither is a spatial cutter. Kept as a
       delegating alias because getHexLine() reads better at the call site below. */
    public static function getHexLine(OffsetCoordinate $start, OffsetCoordinate $end) {
        /*
		$startCube = self::offsetToCube($start);
        $endCube   = self::offsetToCube($end);

        $dx = $endCube[0] - $startCube[0];
        $dy = $endCube[1] - $startCube[1];
        $dz = $endCube[2] - $startCube[2];

        $steps = max(abs($dx), abs($dy), abs($dz));
        if ($steps > 50) $steps = 50;

        $hexes = array();
        for ($i = 0; $i <= $steps; $i++) {
            $t       = $steps == 0 ? 0 : $i / $steps;
            $cx      = $startCube[0] + $dx * $t;
            $cy      = $startCube[1] + $dy * $t;
            $cz      = $startCube[2] + $dz * $t;
            $hexes[] = self::cubeToOffset(self::cubeRound($cx, $cy, $cz));
        }

        return $hexes;
    }

	private static function offsetToCube(OffsetCoordinate $hex) {
		$x = $hex->q - ($hex->r + ($hex->r & 1)) / 2;
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
		$q = $cube[0] + ($cube[2] + ($cube[2] & 1)) / 2;
		$r = $cube[2];
		return new OffsetCoordinate($q, $r);
	*/        
		return HexZone::line($start, $end);
    }

    public static function getWaveformDamage($ship) {
        $move  = $ship->getLastMovement();
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
                case 0: $factor = 1.5; break; // LCV
                case 1: $factor = 2.0; break; // Medium
                case 2: $factor = 4.0; break; // Heavy Combat Vessel
                case 3: $factor = 6.0; break; // Capital Ship
                default: $factor = 10.0; break;
            }
        }

        return (int)ceil($speed * $factor);
    }
}

class spawnHyperspaceWaveform extends Terrain {

    public $terrainCollisionType = 'WaveformCollision';
    public $Enormous  = true;
    public $spawnTurn = 0;
	public $updated   = false;

	public $weaponClass = "Gravitic";

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost  = 0;
        $this->faction    = "Terrain";
        $this->factionAge = 1;
        $this->phpclass   = "spawnHyperspaceWaveform";
        $this->imagePath  = "img/ships/hyperspaceWaveform.png";
        $this->canvasSize = 200;
        $this->shipClass  = "Hyperspace Waveform";
        $this->Enormous   = true;
        $this->iniativebonus = -200;
        $this->isd        = 0;
        $this->notes      = "Hyperspace waveform — active for one turn only.";
        $this->notes     .= "<br>Units entering this hex take speed x size factor damage per hex.";
        $this->notes     .= "<br>Units without advanced armor take double damage.";
        $this->occurence  = "common";

        $this->base        = true;
        $this->smallBase   = true;
        $this->nonRotating = true;
        $this->forwardDefense = 20;
        $this->sideDefense    = 20;

        $this->turncost      = 0;
        $this->turndelaycost = 0;
        $this->accelcost     = 0;
        $this->rollcost      = 0;
        $this->pivotcost     = 0;

        Enhancements::nonstandardEnhancementSet($this, 'Terrain');
        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(8, 300));
//To make this gravitic damage for adaptive armor
foreach ($this->systems as $system) {
    if ($system instanceof RammingAttack) {
        $system->weaponClass = "Gravitic";
    }
}

        $this->hitChart = array(
            0 => array(20 => "Structure"),
        );
    }

    public function loadSpawnTurn() {
        if (strpos($this->name, 'HW') === 0) {
            $this->spawnTurn = (int)substr($this->name, 2);
        }
    }
}



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



class HyperplasmaStream extends Plasma{
	public $name = "HyperplasmaStream";
	public $displayName = "Hyperplasma Stream";
	public $iconPath = "HyperplasmaStream.png";
	
	public $animation = "laser";
	public $priority = 2; //early, due to armor reduction effect

	public $factionAge = 4;//Primordial
		        
	public $raking = 20;
	public $loadingtime = 2;
	public $rangeDamagePenalty = 0.5;	
	public $rangePenalty = 0.33;
	public $fireControl = array(0, 2, 5); // fighters, <=mediums, <=capitals 
	
	public $damageType = "Raking"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Plasma"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

		public $firingModes = array(
			1 => "Raking"
		);
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		if ( $maxhealth == 0 ) $maxhealth = 12;
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
	    $this->data["Special"] .= "Reduces armor of systems hit by 4.";	
	    $this->data["Special"] .= "<br>Does not ignore already pierced armor (eg. every rake needs to pierce armor anew, even to the same location).";
	}
	
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		if (($damage+$armour)>0){
			for ($i = 0; $i < 4; $i++) {
				$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn);
				$crit->updated = true;
				$crit->inEffect = true;
				$system->setCritical($crit);
			}
		}
	}
	
	protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
		parent::doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
		$fireOrder->armorIgnored = array(); //clear armorIgnored array - next rake should be met with full armor value!
	}
	
	public function getDamage($fireOrder){        return Dice::d(10,8)+16;   }
	public function setMinDamage(){     $this->minDamage = 24 ;      }
	public function setMaxDamage(){     $this->maxDamage = 96 ;      }

}//endof class hyperplasmaStream




/* ================================================================================================
 * WALKERS OF SIGMA-957 - Lightning Array family        WALKERS_OF_SIGMA_PLAN.md sections 3.1 / 3.2
 * ================================================================================================
 *
 * STATS ARE FROM THE CONTROL SHEET (user, 2026-09-03). Damage, fire control, range penalty, power,
 * health, pool sizes and intercept ratings are real. Still carrying a value nobody has confirmed:
 * $damageType ("Standard" - switch to Raking if the sheet says so), $priority and $loadingtime.
 * Every number lives in a table or a property at the top of its class and no method below hard-codes
 * one, so re-statting stays a table edit.
 *
 * WHAT A LIGHTNING ARRAY IS
 * Four independent discharges per turn. The player may spend them as up to four separate shots at
 * up to four different targets, or FUSE any number of them into one heavier shot - "combined
 * fire" - which hits harder but is harder to aim AND carries a different range penalty. All THREE
 * of those move together, off three tables keyed by the fused count: $combinedDamageArray,
 * $combinedFireControlArray and $combinedRangePenaltyArray. The rules' "the decision to use combined fire is
 * announced before any shots are taken" needs no machinery: in FV every fire order is declared
 * before beforeFiringOrderResolution runs.
 *
 * DECLARING A COMBINED SHOT - AND HOW THE COUNT REACHES THE SERVER
 * There is NO allocation dialog. These are ordinary split-shot weapons, exactly like the Vorlon
 * Discharge Gun: one gun IS one discharge, and one click declares one. In Combined Fire mode a
 * SECOND click on the same target does not declare a second shot - it fuses another discharge into
 * the shot already standing against that target, and the fire control, range penalty and damage all
 * move to that row of the tables. Four clicks on one ship therefore produce ONE 4-discharge shot;
 * four clicks on four ships produce four single ones.
 *
 * The count rides in FireOrder->shots and nowhere else - the same field every other split-shot
 * weapon uses for its shot count. It is a whitelisted FireOrder constructor argument (Manager.php),
 * so it survives the POST rebuild on both the ship and the fighter branch, and it needs no token in
 * ->notes: ->notes stays the plain "Split" that the ballistic-icon and INCOMING-list machinery
 * expects to see on a split-shot order (BallisticIconContainer keys re-creation on it).
 *
 * beforeFiringOrderResolution RE-CLAMPS the total against the real pool - client input is never
 * trusted - stashes the per-order count and sets ->shots back to 1.
 *
 * TWO FIRING MODES, and only ever two - the Wide Beam refit is a TOGGLE, not a third mode.
 *   1 "Combined Fire" (default) - repeat clicks on one target fuse into a single heavier shot.
 *   2 "Single Shots"            - every click is a separate one-discharge shot, never fused. Useful
 *     against fighters, where four small shots beat one big one. A single-shot order is EXCLUDED
 *     from combining entirely: beforeFiringOrderResolution forces its count to 1 and ignores
 *     ->shots, so a stale or hand-edited client cannot smuggle a fused shot through the cheap mode.
 * ⚠️ Ask isSingleShotMode($mode) rather than comparing against MODE_SINGLE. It is one line either
 * way today; it exists so that the wide beam - which is orthogonal to the grouping choice - can
 * never be confused with it, and so a future third GROUPING mode has one place to be added.
 * ⚠️ Read $order->firingMode, never $this->firingMode, inside beforeFiringOrderResolution:
 * prepareFiring only calls changeFiringMode AFTER it, so the weapon's own mode is not yet the
 * order's mode at that point (the Slicer's class comment records the same trap).
 *
 * INTERCEPTION - AND WHY $guns IS REWRITTEN EVERY TURN
 * These arrays DO intercept, and that makes the engine's gun accounting wrong unless it is
 * corrected, because the engine counts ORDERS while a Lightning Array spends DISCHARGES. One
 * combined shot of four is a single fire order that empties the whole pool; left alone,
 * Firing::isValidInterceptor and Firing::automateIntercept would both read three discharges as
 * still available.
 *
 * Both of those sites do their arithmetic against $this->guns, so beforeFiringOrderResolution sets
 *     guns = pool - dischargesSpentOffensively + numberOfOffensiveOrders
 * which makes BOTH come out right (derivation in the method). The client mirrors the identical
 * formula in initializationUpdate, so its manual-intercept cap agrees with the server's.
 *
 * A 'selfIntercept' order is a PERMISSION MARKER, not a shot: it cancels out of the formula and
 * costs no discharge. It is only needed at all when max(loadingtime, normalload) > 1, which is true
 * of the Medium (normalload 2) and not of the full Array - see Firing::isValidInterceptor.
 * A manual 'intercept' order DOES cost one discharge, and the formula charges it.
 *
 * WIDE BEAM - A PER-TURN TOGGLE, NOT A FIRING MODE (WALKERS_OF_SIGMA_PLAN.md 3.3)
 * The Wide-Beam refit (SYS_WBLA / SYS_WBMLA in Enhancements.php, 300 / 200 points, one per array)
 * sets $wideBeamFitted on the array it is bought for, via enableWideBeam(). A fitted array then
 * shows a "Wide Beam" / "Normal Beam" toggle in its SystemActivation box during the Fire phase,
 * and ARMING it applies to every shot that array fires this turn, in EITHER firing mode.
 *
 * ⭐⭐ IT IS A TOGGLE BECAUSE IT IS AN INDEPENDENT CHOICE. The rules apply the -2 per die "in all
 * modes", i.e. whatever the shot's discharge count - spreading the beam and grouping the discharges
 * are orthogonal. Modelling it as extra firing modes (which this weapon did until 2026-09-06) means
 * enumerating the product, and a four-entry selector is what the player pays for that. The toggle
 * costs one boolean instead, and the rules' own framing - "the lightning array MAY BE CONFIGURED to
 * fire a wide beam", one declaration for the array for the turn - is exactly a toggle.
 *
 * An ARMED array, in either firing mode:
 *   - rolls every damage die at -2, with a floor of 1 PER DIE - so it must be rolled one die at a
 *     time, and the flat +N on the row is untouched because it is not a die;
 *   - scores flash collateral at 50% of the damage dealt instead of 25%, and the field suppression
 *     that silences every other flash weapon inside an Energy Draining Field does NOT apply -
 *     inside a field it simply scores the ordinary 25%;
 *   - goes into a ONE TURN COOLDOWN if it actually fired: calculateLoading() zeroes the turn-advance
 *     reload, so the array is not loaded for the following turn and cannot fire OR intercept with it
 *     (both intercept gates in firing.php test getTurnsloaded() against getLoadingTime()).
 *
 * HOW THE TOGGLE REACHES THE SERVER, and why it is not simpler than this:
 *   1. the player clicks in the Fire phase; the client sets ->active and posts [1] (or [0]) in the
 *      system's individualNotesTransfer;
 *   2. Manager::parseShips calls doIndividualNotesTransfer() on EVERY post in EVERY phase, which
 *      stashes it in $wideBeamRequested on the POST-SIDE ship;
 *   3. FireGamePhase::process calls saveFirePhaseDeclaration() - a narrow hook, see there - which
 *      writes it to tac_individual_notes;
 *   4. the advance re-loads gamedata from the database, and onIndividualNotesLoaded() sets
 *      $wideBeamArmed on the REAL array before prepareFiring runs.
 * ⚠️⚠️ Step 3 CANNOT be gated on $wideBeamFitted: a POST-side ship is rebuilt without enhancements
 * (arch_post_side_ship_reconstruction), so the refit is invisible there and the array would never
 * write its note. The refit is checked at READ time instead, on the real ship, in isWideBeamShot().
 *
 * ⚠️ TIMING DEVIATION, ACCEPTED (plan D6, re-confirmed by the user 2026-09-06): the rules configure
 * a wide beam during Prepare Weapons (Initial Orders); this toggle is in the Fire phase, alongside
 * declaring the shots. The cooldown is what keeps it a decision rather than a free upgrade.
 *
 * WHAT THESE WEAPONS DELIBERATELY DO NOT DO
 * - They are NOT uninterceptable: the rules make the Lightning Array explicitly susceptible to
 *   interception, so $uninterceptable stays false (the Weapon default).
 */
class LightningArray extends Weapon {

    public $name        = "LightningArray";
    public $displayName = "Lightning Array";
    public $iconPath    = "LightningArray.png";

    public $animation      = "bolt";
    public $animationColor = array(140, 210, 255); //pale electric blue

    public $damageType  = "Flash"; 
    public $weaponClass = "Electromagnetic"; //all Walker weaponry is Electromagnetic
    public $factionAge  = 3;                 //Ancient - matters to several to-hit and EDF rules

    /* Mode ids are referenced from the client (special.js) too - keep the two in step. The wide
       beam is NOT one of these: it is an orthogonal per-turn toggle, see the class comment. */
    const MODE_COMBINED = 1;
    const MODE_SINGLE   = 2;
    public $firingModes = array(1 => "Combined", 2 => "Single");

    /* -2 on every damage die while armed, floored at 1 per die. Named because three separate
       places read them: the roll, the tooltip damage span and the client mirror in special.js. */
    const WIDEBEAM_DIE_PENALTY = 2;
    const WIDEBEAM_DIE_FLOOR   = 1;

    /* The note key the Fire-phase toggle is persisted under. ⚠️ notekey_human is varchar(40) -
       anything longer is silently truncated by MySQL. */
    const WIDEBEAM_NOTEKEY = 'wideBeam';

    /* THE REFIT. Set once at construction by enableWideBeam(), from the stored SYS_WBLA / SYS_WBMLA
       purchase. Published per instance (stripForJson) because it is what tells the client to offer
       the toggle at all, and because a blueprint value cannot carry a per-mount purchase. */
    public $wideBeamFitted = false;

    /* THE PER-TURN DECLARATION, rebuilt from the individual notes on every gamedata load. Published
       as ->active, which is the field the generic SystemActivation box reads. */
    public $wideBeamArmed = false;

    /* What the CLIENT just asked for, on a POST-side ship only: 1, 0 or null for "said nothing".
       Transient - never persisted, never serialised, and meaningless on a loaded ship. */
    protected $wideBeamRequested = null;

    public $loadingtime  = 1;                //unconfirmed
    public $priority     = 6;                //unconfirmed
    /* ⚠️ THESE TWO MUST EQUAL ROW 1 of the combined tables below. They are the weapon's profile for
       a SINGLE discharge, and they are what the "Fire control" and "Range penalty" tooltip lines
       report. The combined tables override them per shot, so a mismatch does not change what gets
       rolled - it just makes the ship window quote numbers the weapon never uses, which is worse
       than a visible bug. $rangePenalty is also the fallback whenever no fused count is live. */
    public $rangePenalty = 0.33;             // -1 per 3 hexes = combinedRangePenaltyArray[1]
    public $fireControl  = array(8, 6, 4);   // fighters, <=mediums, <=capitals = combinedFireControlArray[1]
    public $intercept    = 5;                //one discharge per engagement; see the class comment

    /* Split-shot plumbing. $canSplitShots stays true in BOTH firing modes and at every pool size:
       it is what routes a click through doMultipleFireOrders (client) so one click declares one
       discharge, and it is also what the manual-interception UI reads. $maxVariableShots is the
       client's own ceiling on separate shots; the authoritative one is getDischargePool() below,
       which the server re-clamps against.
       ⚠️ $guns here is only the UNFIRED value, used before anything is declared and by the fleet
       builder. beforeFiringOrderResolution REWRITES it every turn for the intercept accounting -
       see the class comment - so do not read this literal anywhere that matters. */
    public $canSplitShots     = true;
    public $guns              = 4;           //one per discharge, before anything is spent
    public $maxVariableShots  = 4;
    /* Shots may be declared in BOTH modes in the same turn: fuse a couple of combined shots, switch
       to Single Shots and pepper a flight with the rest, switch back. Without this the firing-mode
       selector locks the moment the first order is declared (weaponManager.onModeClicked and
       SystemInfoButtons.canChangeFiringMode both gate on it), which would make the two modes an
       either/or choice for the whole turn rather than a per-shot one.
       ⚠️ It is protected on Weapon and NOT published by the base stripForJson - the override below
       has to pass it, or the client never sees it and the lock stays on. Nothing on the server reads
       it: Firing::prepareFiring already switches the weapon per ORDER before resolving each shot,
       which is why beforeFiringOrderResolution must read $order->firingMode (see the class comment). */
    protected $multiModeSplit = true;
    /* Both of these tell the CLIENT to ask this weapon for its own numbers instead of using the
       generic formulae - the server always calls its own overrides regardless. Combined fire moves
       BOTH the fire control and the range penalty, so both hooks are needed or the player's
       predicted hit chance disagrees with the dice. */
    public $specialHitChanceCalculation = true;
    public $specialRangeCalculation     = true;

    /* Discharges available per turn. LightningArray's is flat; MediumLightningArray
       overrides getDischargePool() to grow it with charge time. */
    protected $dischargePool = 4;

    /* DAMAGE TABLE, keyed by the number of discharges fused into one shot.
       'dice' d10s plus a flat 'add'. Row n MUST exist for every n from 1 to the pool size. */
    protected $combinedDamageArray = array(
        1 => array('dice' => 5, 'add' => 20),   
        2 => array('dice' => 10, 'add' => 20),   
        3 => array('dice' => 15, 'add' => 20),  
        4 => array('dice' => 20, 'add' => 20),  
    );

    /* FIRE CONTROL TABLE, same shape as $fireControl (fighters, <=mediums, <=capitals), keyed by
       fused discharge count. The two ends pull in OPPOSITE directions: fusing makes the shot much
       harder to land on a fighter (8 -> 2) and easier on a capital (4 -> 6), which is what makes the
       Single Shots mode worth having. Row 1 IS $fireControl - it is applied as a delta against it,
       so keep the two in step or the tooltip lies about the single-discharge profile. */
    protected $combinedFireControlArray = array(
        1 => array( 8, 6, 4),
        2 => array( 6, 6, 5),
        3 => array( 4, 6, 6),
        4 => array( 2, 6, 6),
    );

    /* RANGE PENALTY TABLE, keyed by fused discharge count - per-hex penalties, the same units as
       $rangePenalty. Fusing IMPROVES the reach (a tighter bolt carries further), which is the
       opposite of what the fighter column of the fire-control table does. Row 1 IS $rangePenalty. */
    protected $combinedRangePenaltyArray = array(
        1 => 0.33,  // -1 per 3 hexes
        2 => 0.25,  // -1 per 4 hexes
        3 => 0.25,  // -1 per 4 hexes
        4 => 0.2,   // -1 per 5 hexes
    );

    /* fire order id => discharges fused into it. Rebuilt from scratch every
       beforeFiringOrderResolution; never persisted. */
    protected $combinedCount = array();

    /* The order currently being resolved, published for the duration of ONE calculateHitBase call.
       calculateRangePenalty() is handed nothing but a distance - and the parent calls it up to three
       times per shot (the base penalty, then the no-lock and jammer variants) - so the fused count
       has to reach it out of band. Always cleared in a finally; never persisted, never serialised. */
    protected $activeCombinedCount = null;

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        if ($maxhealth == 0) $maxhealth = 40;
        if ($powerReq  == 0) $powerReq  = 16;
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    /* Total discharges this weapon may spend this turn. The single authority - the client's $guns /
       $maxVariableShots are only hints, and beforeFiringOrderResolution clamps against THIS.
       ⚠️ Deliberately NOT gated on the array being loaded: the tooltip, setMaxDamage and the
       fleet builder all read it on a weapon that is mid-reload and must still describe the mount.
       The "may it fire at all right now" question is asked once, in beforeFiringOrderResolution. */
    public function getDischargePool(){
        return $this->dischargePool;
    }

    /* ---------------------------------------------------------------- Wide Beam (plan 3.3) */

    /* Called by the SYS_WBLA / SYS_WBMLA applier in Enhancements.php, once per construction, on the
       ONE array the refit was bought for. It buys the CAPABILITY; arming it is a per-turn decision.
       ⚠️ A plain instance property, so two Lightning Arrays on one hull are refitted independently -
       which is what the rules ask for ("the player pays to enhance each one separately"). */
    public function enableWideBeam(){
        $this->wideBeamFitted = true;
    }

    /* Was this array refitted? */
    public function hasWideBeam(){
        return $this->wideBeamFitted;
    }

    /* Is this array firing a wide beam THIS TURN? Both halves are required: the refit (blueprint
       purchase, applied at construction) and the arm (this turn's toggle, replayed from the notes).
       ⚠️ Takes no fire order, on purpose. The rules configure the ARRAY, not the shot - one
       declaration covers every discharge it fires this turn, in either firing mode - so there is
       nothing per-order to read and no way for two shots in one turn to disagree. */
    public function isWideBeamShot(){
        return $this->wideBeamFitted && $this->wideBeamArmed;
    }

    /* Does this firing mode never fuse? One line today, because there is one such mode. It exists
       so that the grouping question is asked by NAME everywhere - the wide beam is a separate,
       orthogonal choice, and the two were briefly modelled as four firing modes (2026-09-06) with
       exactly the confusion that invites. A future third grouping mode adds itself here. */
    public static function isSingleShotMode($mode){
        return ((int)$mode) === self::MODE_SINGLE;
    }

    /* ---------------------------------------------- the toggle's round trip (class comment, 3) */

    /* Step 2. Called by Manager::parseShips on every POST, in every phase, as soon as the POST-side
       system exists. All it does is remember what was asked; the write is step 3.
       ⚠️ NOT gated on $wideBeamFitted - a POST-side ship is rebuilt WITHOUT enhancements
       (arch_post_side_ship_reconstruction), so the refit reads false here on a fitted array and
       gating would silently drop every declaration. The refit is re-checked at read time instead. */
    public function doIndividualNotesTransfer(){
        if (is_array($this->individualNotesTransfer) && isset($this->individualNotesTransfer[0])) {
            $this->wideBeamRequested = ((int)$this->individualNotesTransfer[0] === 1) ? 1 : 0;
        }
        $this->individualNotesTransfer = array();
    }

    /* Step 3. Called from FireGamePhase::process for the submitting player's own ships only - see
       the hook on ShipSystem for why this is a narrow hook rather than the generic
       generateIndividualNotes sweep the other phases run.
       Writes BOTH states, so that re-committing after un-arming is not stuck on a stale 1;
       onIndividualNotesLoaded takes the LAST note of the turn. Nothing is written at all unless the
       client sent a toggle state, so a game with no fitted array never touches the table. */
    public function saveFirePhaseDeclaration($gamedata, $dbManager){
        if ($this->wideBeamRequested === null) return;

        $ship = $this->getUnit();
        if (!$ship) return;

        $this->individualNotes[] = new IndividualNote(
            -1, TacGamedata::$currentGameID, $gamedata->turn, $gamedata->phase,
            $ship->id, $this->id,
            self::WIDEBEAM_NOTEKEY,
            'Wide beam declaration',      //notekey_human is varchar(40) - keep it short
            $this->wideBeamRequested
        );
        $this->wideBeamRequested = null;
    }

    /* Step 4. Rebuilds this turn's declaration on every gamedata load, before prepareFiring runs.
       ⚠️ getIndividualNotesForGame loads `turn <= current`, so the turn test is what makes this a
       PER-TURN declaration rather than a permanent one - without it, arming once would arm the
       array for the rest of the game.
       ⚠️ Highest id wins, not last in the array: the query orders by turn and phase only, so two
       notes written in the same phase (arm, then re-commit un-armed) come back in an order MySQL
       does not promise. Ids are auto-increment, so they are the write order. */
    public function onIndividualNotesLoaded($gamedata){
        $bestId = -1;
        foreach ($this->individualNotes as $currNote){
            if ($currNote->notekey !== self::WIDEBEAM_NOTEKEY) continue;
            if ($currNote->turn != $gamedata->turn) continue;
            if ((int)$currNote->id < $bestId) continue;
            $bestId = (int)$currNote->id;
            $this->wideBeamArmed = ((int)$currNote->notevalue === 1);
        }
        parent::onIndividualNotesLoaded($gamedata);   //clears the array, as every other reader does
    }

    /* Did this array fire a wide beam on $turn? Used only at the turn advance, where the fire
       orders in memory ARE that turn's (DBManager::getFireOrdersForShips loads one turn, and
       $gamedata->turn has already been stepped past it by then) - which is also why the cooldown
       cannot be re-derived during the following turn's Fire phase and has to live in the persisted
       loading state instead.
       ⚠️ OFFENSIVE orders only. Interception is a single discharge and never a wide beam
       (getInterceptOrderMode), so defending must not cost the array the following turn as well. */
    protected function firedWideBeamOnTurn($turn){
        if (!$this->isWideBeamShot()) return false;
        foreach ($this->fireOrders as $order){
            if ($order->type != "normal") continue;
            if ($order->weaponid != $this->id) continue;
            if ($order->turn != $turn) continue;
            return true;
        }
        return false;
    }

    /* THE COOLDOWN. "The operation of a lightning array in this mode is stressful on its systems,
       and therefore requires a 1 turn cooldown period."
     *
     * The turn advance (phase -1) is the one write that decides what the array has next turn, and
     * the parent's version of it hands an ordinary loading-1 weapon its charge straight back:
     * fired on turn T -> loading 0 -> +1 -> 1 -> loaded again on T+1. Zeroing that ONE write is the
     * entire cooldown. On T+1 the array holds 0/1 and is not loaded, so:
     *   - the client greys it out (weaponManager.isLoaded tests turnsloaded AND overloadturns, and
     *     the phase-2 write leaves overloadturns at 0 for a non-overloading weapon);
     *   - Firing::isValidInterceptor and validateManualIntercept both refuse it, so a wide beam
     *     costs the array its DEFENCE for the following turn too, which is what "stressful on its
     *     systems" should mean;
     *   - beforeFiringOrderResolution gives it a pool of 0, so a tampered POST buys nothing.
     * Advancing T+1 -> T+2 finds no shot on T+1, so the ordinary +1 puts it back at 1/1 (and the
     * Medium back at 1/2, part-charged, which is where it starts a scenario anyway).
     *
     * ⚠️ NOT done by raising $loadingtime for a turn. That number is a BLUEPRINT field riding the
     * per-class static bundle rather than the poll payload, so the client would have gone on
     * reading 1 and shown a loaded array - the Stage 7 trap. $turnsloaded IS published per
     * instance by Weapon::stripForJson, so this way the cooldown is visible for free. */
    public function calculateLoading(TacGamedata $gamedata){
        $loading = parent::calculateLoading($gamedata);
        if (!$loading) return $loading;
        if ($gamedata->phase != -1) return $loading;                    //only the turn-advance write reloads
        if (!$this->firedWideBeamOnTurn($gamedata->turn - 1)) return $loading;

        return new WeaponLoading(
            0,                          //<- no charge regained this turn: THE cooldown
            $loading->extrashots,
            $loading->loadedammo,
            0,                          //<- and no overload credit either, or the client reads it as loaded
            $loading->loadingtime,
            $loading->firingmode
        );
    }

    /* Is the array able to shoot at all right now? The same test both intercept gates in firing.php
       make, reused here because nothing on the server stops an UNLOADED weapon from resolving an
       offensive order - the client's isLoaded is the only gate on that path, and a cooldown that
       only the client enforced would not be a cooldown. */
    protected function isReadyToFire(){
        return $this->getTurnsloaded() >= $this->getLoadingTime();
    }

    /* Highest fused count the damage/FC tables actually describe. Guards against a control sheet
       whose pool is raised without the tables being extended: a shot is never resolved against a
       row that does not exist. */
    protected function getMaxTabledCount(){
        $keys = array_keys($this->combinedDamageArray);
        return empty($keys) ? 1 : max($keys);
    }

    public function beforeFiringOrderResolution($gamedata){
        $this->combinedCount = array();

        /* An array in its Wide Beam cooldown - or offline, or otherwise mid-reload - has nothing to
           spend. Every order below then clamps to 0 discharges and does 0 damage, which is this
           weapon's established loud failure (a 0-damage line in the log rather than a silently
           dropped order). The client refuses to declare in the first place; this is the half that
           cannot be edited out of a POST. */
        $pool   = $this->isReadyToFire() ? $this->getDischargePool() : 0;
        $left   = $pool;
        $maxRow = $this->getMaxTabledCount();

        $offensiveOrders     = 0; //O - number of "normal" orders
        $dischargesSpent     = 0; //D - discharges those orders consumed between them

        foreach ($this->fireOrders as $order){
            if ($order->type != "normal") continue;
            $offensiveOrders++;

            /* ⚠️ NO WIDE-BEAM CHECK HERE, and that is the point of the toggle: the wide beam is a
               property of the ARRAY this turn, not of the order, so there is no per-order claim to
               validate or demote. An array that was never refitted answers false to
               isWideBeamShot() whatever its notes say, so a hand-written note buys nothing.

               A SINGLE SHOTS order is exactly one discharge, whatever the order claims. Reading
               $order->firingMode and not $this->firingMode is load-bearing: prepareFiring calls
               changeFiringMode only AFTER this method, so the weapon's own mode is still last
               turn's here. Ignoring ->shots rather than clamping it is deliberate - it means a
               stale or hand-edited client cannot declare a fused shot in the cheap mode. */
            if (self::isSingleShotMode($order->firingMode)) {
                $count = 1;
            } else {
                //->shots IS the fused count, exactly as it is the shot count on every other
                //split-shot weapon. It is a whitelisted FireOrder constructor argument
                //(Manager.php), so it survives the POST rebuild on both the ship and the fighter
                //branch. An order that carried nothing (a hand-built payload, or a client that
                //predates this weapon) is treated as a single discharge, the smallest thing it
                //could have meant.
                $count = (int)$order->shots;
                if ($count < 1) $count = 1;
            }

            //Re-clamp: against the rows the tables describe, then against what is actually left in
            //the pool. A shot the pool cannot pay for gets 0 and does nothing - the same thing the
            //Slicer does with an over-allocated volley. It stays visible in the combat log at 0
            //damage rather than vanishing, which is the loud failure mode.
            if ($count > $maxRow) $count = $maxRow;
            if ($count > $left)   $count = $left;
            if ($count < 0)       $count = 0;
            $left -= $count;
            $dischargesSpent += $count;

            $this->combinedCount[$order->id] = $count;
            $order->shots = 1; //one shot; the fused count now lives in $this->combinedCount
        }

        $this->guns = $this->getGunsForInterceptAccounting($pool, $dischargesSpent, $offensiveOrders);
    }

    /* What $this->guns has to be for the engine's ORDER-counting intercept arithmetic to land on
       this weapon's real DISCHARGE count. Both consumers are in firing.php:
     *
     *   Firing::isValidInterceptor   refuses when  (allOrders - selfIntercepts) >= guns
     *                                          i.e. when  O + M >= guns
     *   Firing::automateIntercept    grants        guns - allOrders + selfIntercepts
     *                                          i.e.       guns - O - M
     *
     * with O = offensive orders, M = manual 'intercept' orders, S = selfIntercept markers.
     * We want both to key off the discharges actually left, R = pool - D - M (a manual intercept
     * costs one discharge; a marker is consent and costs nothing). Setting
     *
     *     guns = pool - D + O
     *
     * gives  automateIntercept:  pool - D + O - O - M  =  pool - D - M  =  R          ✔
     * and    isValidInterceptor: O + M >= pool - D + O  ⟺  M >= pool - D  ⟺  R <= 0   ✔
     *
     * S cancels out of both, which is why a marker is free. Nothing declared gives guns = pool, so
     * an idle array can intercept with every discharge it has.
     *
     * ⚠️ This is the Slicer's $guns-padding problem in a different currency, and it has the same
     * sharp edge: a manual 'intercept' order appears in BOTH terms of automateIntercept's
     * expression. Here it is charged exactly once, via M in the derivation above - the Slicer's
     * game-4306 bug was letting it cancel itself out and spending the same allowance twice. */
    protected function getGunsForInterceptAccounting($pool, $dischargesSpent, $offensiveOrders){
        return max(0, $pool - $dischargesSpent) + $offensiveOrders;
    }

    /* Discharges fused into this order. 0 when beforeFiringOrderResolution clamped it away, or when
       it never ran for this order - deliberately NOT 1, so a plumbing failure shows up as a
       0-damage shot in the log instead of quietly granting a free discharge. */
    protected function getCombinedCount($fireOrder){
        return isset($this->combinedCount[$fireOrder->id]) ? $this->combinedCount[$fireOrder->id] : 0;
    }

    public function getDamage($fireOrder){
        $n = $this->getCombinedCount($fireOrder);
        if ($n < 1 || !isset($this->combinedDamageArray[$n])) return 0;
        $row = $this->combinedDamageArray[$n];

        /* ⚠️ WIDE BEAM IS ROLLED ONE DIE AT A TIME. The floor is per DIE, not on the total, so
           Dice::d(10, $n) - which returns a sum - cannot express it: five dice each floored at 1
           can never total less than 5, while a floor applied to their sum would allow 1. The flat
           +N on the row is not a die and is not touched. */
        if ($this->isWideBeamShot()) {
            $total = 0;
            for ($i = 0; $i < $row['dice']; $i++) {
                $total += max(self::WIDEBEAM_DIE_FLOOR, Dice::d(10) - self::WIDEBEAM_DIE_PENALTY);
            }
            return $total + $row['add'];
        }

        return Dice::d(10, $row['dice']) + $row['add'];
    }

    /* WIDE BEAM COLLATERAL, both halves of the rule, and they pull in opposite directions.
     *
     *   "Collateral flash damage is scored as normal when the target is inside an energy draining
     *    field (i.e. 25% on any other targets in the same hex). If the target is not in an energy
     *    draining field, collateral flash damage is scored at an amount of 50%."
     *
     * The general rule (weapon.php, plan 2.1) is that a flash weapon striking a unit INSIDE a field
     * scores no collateral at all. A wide beam is the exception the rules carve out: inside a field
     * it drops back to the ordinary 25% instead of to nothing, and outside one it doubles to 50%.
     * ⚠️ 50% is computed from the damage, not by doubling the 25% figure - see the parent hook. */
    protected function edfSuppressesCollateral($target, $fireOrder, $gamedata){
        if ($this->isWideBeamShot()) return false;
        return parent::edfSuppressesCollateral($target, $fireOrder, $gamedata);
    }

    /* The one genuinely surprising outcome gets a log line: everything else in the game scores NO
       collateral against a target standing in a draining field, so a wide beam splashing there
       looks like a bug unless the log says otherwise. The 50% case outside a field needs no note -
       nothing about it contradicts what the player already expects of a flash weapon.
       ⚠️ Written before the parent runs, and unconditionally, exactly as the suppression note it
       replaces is: neither knows yet whether anything is actually sharing the hex. */
    public function doCollateralDamage($target, $shooter, $fireOrder, $gamedata, $flashDamageAmount){
        if ($this->isWideBeamShot()
            && TacGamedata::$edfPresent && $gamedata->isHexInEdfField($target->getHexPos())) {
            $fireOrder->pubnotes .= "<br>Wide beam deals Flash damage through the Energy Draining Field -"
                                 . " 25% collateral damage is scored. ";
        }
        parent::doCollateralDamage($target, $shooter, $fireOrder, $gamedata, $flashDamageAmount);
    }

    protected function getFlashCollateralAmount($damage, $target, $fireOrder, $gamedata){
        if (!$this->isWideBeamShot()) {
            return parent::getFlashCollateralAmount($damage, $target, $fireOrder, $gamedata);
        }
        //Same hex test the suppression uses - ANY field hex, no own-fleet exemption: this is the
        //field dampening an explosion, which is a property of the hex.
        $inField = TacGamedata::$edfPresent && $gamedata->isHexInEdfField($target->getHexPos());
        return (int)round($damage / ($inField ? 4 : 2), 0, PHP_ROUND_HALF_UP);
    }

    /* Combined fire is harder to aim. Applied as a DELTA on top of whatever the parent worked out,
       rather than by swapping $this->fireControl around the parent call: fireControl is read in
       several places during that call, and a temporarily-mutated copy is a per-instance mutation of
       a property the blueprint shares. The delta is in d100 units because $fireOrder->needed is;
       the client mirrors it in d20 units in calculateSpecialHitChanceMod. */
    public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder){
        /* Publish this shot's fused count for the duration of the parent call so
           calculateRangePenalty() below can see it. try/finally rather than a plain clear: if the
           parent throws, a count left standing would silently apply to the NEXT shot this weapon
           resolves, which is the kind of bug that only shows up as a wrong hit chance months later. */
        $this->activeCombinedCount = $this->getCombinedCount($fireOrder);
        try {
            parent::calculateHitBase($gamedata, $fireOrder);
        } finally {
            $this->activeCombinedCount = null;
        }

        //needed <= 0 is the parent's auto-miss marker (out of range, null target, ...). Adding a
        //delta to it would silently un-miss the shot.
        if ($fireOrder->needed <= 0) return;

        $delta = $this->getCombinedFireControlMod($gamedata, $fireOrder);
        if ($delta !== 0) {
            $fireOrder->needed += $delta * 5; //d20 table -> d100 roll
            $fireOrder->notes  .= " Combined fire x" . $this->getCombinedCount($fireOrder)
                               . ": " . ($delta > 0 ? "+" : "") . $delta . " FC.";
        }

        /* Wide Beam changes no hit chance at all - it is the same bolt spread wider - so this is
           purely the log saying why the damage came out low. Written here rather than in getDamage
           so that a MISS still records what was fired, and so a withdrawn shot records nothing. */
        if ($this->isWideBeamShot()) {
            $fireOrder->notes .= " Wide beam: -" . self::WIDEBEAM_DIE_PENALTY . " per damage die"
                              . " (min " . self::WIDEBEAM_DIE_FLOOR . ").";
        }
    }

    /* The combined-fire fire-control change for this order, in d20 units, as a delta against the
       weapon's normal fire control for that target class. Shared with the client, which computes
       the identical number from an identical table. */
    protected function getCombinedFireControlMod(TacGamedata $gamedata, FireOrder $fireOrder){
        $n = $this->getCombinedCount($fireOrder);
        if ($n < 1 || !isset($this->combinedFireControlArray[$n])) return 0;

        $target = $gamedata->getShipById($fireOrder->targetid);
        if ($target === null) return 0;

        $fcIndex = $target->getFireControlIndex();
        if (!isset($this->combinedFireControlArray[$n][$fcIndex])) return 0;
        if (!isset($this->fireControl[$fcIndex])) return 0;

        return $this->combinedFireControlArray[$n][$fcIndex] - $this->fireControl[$fcIndex];
    }

    /* Combined fire also changes the RANGE penalty, so this is a real override of the parent's
       formula rather than another delta on ->needed. That matters: the parent derives the no-lock
       and jammer modifiers from the range penalty - and in the doubleRangeIfNoLock branch calls this
       method a second and third time at modified distances - so overriding here keeps all three
       consistent, where a flat delta on ->needed would have moved the base penalty and left its two
       derivatives computing off the single-discharge value.

       Falls back to the plain $rangePenalty whenever there is no active count: that covers a shot
       resolved outside calculateHitBase and any future caller that has no fire order to hand. */
    public function calculateRangePenalty($distance){
        return $this->getCombinedRangePenalty() * $distance;
    }

    /* Per-hex range penalty for the shot currently being resolved. */
    protected function getCombinedRangePenalty(){
        $n = $this->activeCombinedCount;
        if ($n !== null && isset($this->combinedRangePenaltyArray[$n])) {
            return $this->combinedRangePenaltyArray[$n];
        }
        return $this->rangePenalty;
    }

    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        } else {
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= "Fires up to " . $this->getDischargePool()
                               . " separate shots per turn, at the same or different targets.";
        $this->data["Special"] .= "<br>Combined Fire mode: Every shot on the same target adds"
                               . " fuses together into one heavier hit.";
        $this->data["Special"] .= "<br>Single Shots mode: Every shot is a separate one-discharge"
                               . " shot.";
        $this->data["Special"] .= $this->getWideBeamTooltip();
        $this->data["Special"] .= $this->getCombinedFireTooltip();
        $this->data["Special"] .= "<br>Each shot not fired will instead intercept one incoming shot.";
    }

    /* The Wide Beam block, present only on a refitted array. ⚠️ It reaches the client because this
       class's stripForJson() republishes $this->data per instance - $data is otherwise baked into
       the per-class static blueprint, so an enhanced mount would quote the hull as designed. */
    protected function getWideBeamTooltip(){
        if (!$this->hasWideBeam()) return "";
        return "<br>Wide Beam (enhancement): toggled on this array for the whole turn, in EITHER"
             . " firing mode. While armed, every damage die is rolled at -"
             . self::WIDEBEAM_DIE_PENALTY . " (minimum " . self::WIDEBEAM_DIE_FLOOR . " per die)."
             . "<br> - Collateral flash damage is 50% instead of 25%, or the usual 25% when the"
             . " target stands inside an Energy Draining Field (where a flash weapon normally"
             . " scores none at all)."
             . "<br> - Firing a wide beam puts this array into a one turn cooldown: it cannot fire"
             . " or intercept on the following turn."
             . ($this->wideBeamArmed ? "<br> - ARMED THIS TURN." : "");
    }

    /* The combined-fire table as tooltip rows, generated from the tables themselves so a re-stat
       can never leave the tooltip describing the old numbers. */
    protected function getCombinedFireTooltip(){
        $out  = "";
        $pool = min($this->getDischargePool(), $this->getMaxTabledCount());
        for ($n = 1; $n <= $pool; $n++){
            if (!isset($this->combinedDamageArray[$n])) continue;
            $row  = $this->combinedDamageArray[$n];
            $out .= "<br> - " . $n . " discharge" . ($n > 1 ? "s" : "") . ": "
                 . $row['dice'] . "d10+" . $row['add'];
            if (isset($this->combinedFireControlArray[$n])) {
                $out .= " (FC " . implode("/", $this->combinedFireControlArray[$n]) . ")";
            }
            if (isset($this->combinedRangePenaltyArray[$n]) && $this->combinedRangePenaltyArray[$n] > 0) {
                //Same units the generic "Range penalty" tooltip line uses: penalty x5 per hex.
                $out .= " (range -" . number_format($this->combinedRangePenaltyArray[$n] * 5, 2) . "/hex)";
            }
        }
        return $out;
    }

    /* Tooltip damage spans one discharge at its worst to the whole pool at its best, which is what
       the weapon can actually produce in a turn.
       ⚠️ These two are called by Weapon::setSystemDataWindow inside a loop that walks $firingModes
       and calls changeFiringMode() before each pair, filling minDamageArray/maxDamageArray. So
       $this->firingMode IS the mode being described here - the one place in this class where
       reading it rather than an order is correct - and adding mode 3 to $firingModes is all it
       takes for the ship window to quote the wide beam's own span. */
    public function setMinDamage(){
        $row = isset($this->combinedDamageArray[1]) ? $this->combinedDamageArray[1] : array('dice' => 0, 'add' => 0);
        //Unchanged by Wide Beam: a d10 already rolls a 1 at worst, and the floor is 1 as well.
        $this->minDamage = $row['dice'] + $row['add'];
    }

    public function setMaxDamage(){
        $n   = min($this->getDischargePool(), $this->getMaxTabledCount());
        $row = isset($this->combinedDamageArray[$n]) ? $this->combinedDamageArray[$n] : array('dice' => 0, 'add' => 0);
        $this->maxDamage = ($row['dice'] * $this->getDieCeiling()) + $row['add'];
    }

    /* Highest a single damage die can roll - 10, or 8 while the array is armed for a wide beam. The
       floor never binds at the top of the range, so the penalty is the whole difference. Applies to
       BOTH firing modes, which is what makes the arm and the mode independent in the ship window
       exactly as they are in the dice. */
    protected function getDieCeiling(){
        if (!$this->isWideBeamShot()) return 10;
        return max(self::WIDEBEAM_DIE_FLOOR, 10 - self::WIDEBEAM_DIE_PENALTY);
    }

    /* The pool, the damage span and the tooltip all move with charge time on the Medium variant, so
       they have to be re-published per instance or the client keeps showing the blueprint values -
       the same reason LaserAccelerator overrides this. Everything named here is a value the client
       re-reads from the live system, so ShipCompactor stripping it at its default is harmless. */
    public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data           = $this->data;
        $strippedSystem->minDamage      = $this->minDamage;
        $strippedSystem->minDamageArray = $this->minDamageArray;
        $strippedSystem->maxDamage      = $this->maxDamage;
        $strippedSystem->maxDamageArray = $this->maxDamageArray;
        //$multiModeSplit is protected on Weapon and the base stripForJson does not publish it -
        //see the property comment. Without this line the client locks the firing-mode selector as
        //soon as one shot is declared.
        $strippedSystem->multiModeSplit = $this->multiModeSplit;

        /* THE WIDE BEAM TOGGLE, both halves.
           - wideBeamFitted is the REFIT, and the registry's `serialise` list would publish it for
             the enhanced mount anyway; naming it here as well costs nothing and keeps the two
             halves of one feature together.
           - active is THIS TURN's arm. Published under that name because the generic
             <SystemActivation> box reads system.active - the same thing ChameleonSensors does. */
        if ($this->wideBeamFitted) {
            $strippedSystem->wideBeamFitted = true;
            $strippedSystem->active         = $this->wideBeamArmed;
        }
        return $strippedSystem;
    }

}//endof class LightningArray


/* Accelerator variant: it charges over several turns and its discharge pool GROWS with the charge.
 * One turn of charge is one discharge, so "may only split its shots once it has charged for two or
 * more turns" falls out of the pool rather than needing a rule of its own - at one turn there is
 * simply nothing to split.
 *
 * It does NOT begin the game charged (getStartLoading below). That is the whole of that rule:
 * onConstructed writes whatever getStartLoading returns straight into tac_systemdata.
 */
class MediumLightningArray extends LightningArray {

    public $name        = "MediumLightningArray";
    public $displayName = "Medium Lightning Array";
    public $iconPath    = "LightningArrayMed.png"; //case-sensitive on live - see LightningArray

    public $loadingtime  = 1;               //one turn per discharge
    public $normalload   = 2;               
    public $priority     = 6;               
    //As on the parent, these two are row 1 of the tables below - the single-discharge profile.
    public $rangePenalty = 0.33;            // = combinedRangePenaltyArray[1]
    public $fireControl  = array(6, 4, 2);  // = combinedFireControlArray[1]

    /* ⚠️ One gun per TURN LOADED, not per $normalload: getDischargePool() below is the authority and
       both halves recompute $guns from it (server in beforeFiringOrderResolution, client in
       initializationUpdate). This literal is only what an unloaded array reports before either has
       run - the fleet builder shows the full pool, because getDischargePool() returns $normalload
       in the lobby phase. */
    public $guns             = 1;
    public $maxVariableShots = 2;
	public $intercept = 4;

    /* Lighter than the full Array at every charge level. Only TWO rows - its pool never exceeds 2. */
    protected $combinedDamageArray = array(
        1 => array('dice' => 4, 'add' => 12),   //  3 - 21
        2 => array('dice' => 8, 'add' => 12),   //  7 - 34
    );

    //Row 1 IS $fireControl, as on the parent.
    protected $combinedFireControlArray = array(
        1 => array( 6, 4, 2),
        2 => array( 4, 5, 5),
    );

    /* Per-hex, matching $rangePenalty's units. Row 1 IS $rangePenalty. */
    protected $combinedRangePenaltyArray = array(
        1 => 0.33,  // -1 per 3 hexes
        2 => 0.25,  // -1 per 4 hexes
    );

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        if ($maxhealth == 0) $maxhealth = 24;
        if ($powerReq  == 0) $powerReq  = 12;
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    /* One discharge per turn charged, capped at the charge ceiling. Everything else - the split-shot
       ceiling, the combined-fire ceiling, the tooltip - is derived from this one number. */
    public function getDischargePool(){
        $pool = (int)$this->turnsloaded;
        if ($pool > $this->normalload) $pool = $this->normalload;
        if ($pool < 1) $pool = 1; //a weapon ready to fire at all has at least one discharge
        return $pool;
    }

    /* "Does not begin the scenario fully charged" - it begins it with ONE turn of charge, i.e. one
       discharge, which is what the control sheet's 1/2 means. Weapon::getFullStartLoading seeds the
       first slot with getNormalLoad() (2 here, fully charged); seeding 1 instead is the entire
       change, and onConstructed writes it straight into tac_systemdata.
       ⚠️ NOT 0: turnsloaded 0 is below getLoadingTime(), so the array would be UNABLE TO FIRE AT ALL
       on turn 1 and would read "0/2" in the ship window (user report, game 4329). Everything else is
       copied from the parent so overloading and firing-mode seeding keep behaving identically.
       ⚠️ AND THE WANDERER IS EXEMPT (user ruling 2026-09-09, plan 3.10e): $seedsBelowFullCharge is
       what tells Weapon::getStartLoadingForShip that this seed is a restriction it may lift for a
       hull in Weapon::FULLY_CHARGED_HULL_CLASSES. This method itself never learns which hull it is on
       - it has no ship to ask - so it must keep answering the restricted value. */
    protected $seedsBelowFullCharge = true;

    public function getStartLoading(){
        $overloadTurns = $this->overloadturns;
        if ($overloadTurns === 0 && $this->overloadable) $overloadTurns = 1;

        return new WeaponLoading(
            1,                       //<- one turn charged (1/2), rather than getNormalLoad()'s full 2
            $this->overloadshots,
            0,
            $overloadTurns,
            $this->getLoadingTime(),
            $this->firingMode
        );
    }

    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
        $this->data["Special"] .= "<br>Accelerator: gains one discharge per turn charged, up to "
                               . $this->normalload . ". Cannot combine its discharges until it has"
                               . " charged for at least two turns.";
        $this->data["Special"] .= "<br>Begins the scenario part-charged, with one discharge ready.";
        //Its effective loading time is max(loadingtime, normalload) = normalload > 1, so unlike the
        //full Array it will NOT be auto-assigned to interception without the player's consent -
        //Firing::isValidInterceptor demands a selfIntercept marker first.
        $this->data["Special"] .= "<br>Must be explicitly committed to interception to defend.";
    }

}//endof class MediumLightningArray

/* SENSOR CHARGE TRANSCEIVER - WALKERS_OF_SIGMA_PLAN.md 3.9 (Stage 9), decision D2.
 *
 * "This system is used by advanced races to chart irregularities in the fabric of space... The
 *  transceiver launches a precisely modulated sensor charge encased in a force field that steers
 *  the charge through the field of interest to gather data. The pulse is then returned to the
 *  transceiver for analysis. The main disadvantage is that the charges need to have a complete
 *  path from and to a transceiver to be of any use."
 *
 * WHAT MAKES THIS WEAPON UNLIKE EVERY OTHER ONE IN THE TREE
 * It does not shoot at a target. The player plots a COURSE - a chain of straight legs from the
 * firing ship to a friendly ship that also carries a transceiver - and the charge damages whatever
 * enemy units it passes through on the way. So the declaration and the shots are two different
 * things, and most of this class is the bridge between them:
 *
 *   1. THE CLIENT (special.js) declares one hex fire order PER WAYPOINT, each carrying a
 *      "SCT|w:<n>" token in ->notes. That is the split-shot channel the Slicer's class comment
 *      describes, used here to carry a path rather than an allocation. None of those orders is a
 *      shot.
 *   2. beforeFiringOrderResolution() re-walks the path from scratch (client input is never
 *      trusted), DETACHES every waypoint order from $this->fireOrders, and puts back one real
 *      damage order per enemy-occupied hex plus one informational order for the combat log.
 *   3. Firing::prepareFiring then rolls those the ordinary way.
 *
 * ⚠️ THE WAYPOINT ORDERS ARE ALREADY IN THE DATABASE by the time step 2 runs - FireGamePhase::
 * process persisted them at the POST - so detaching them is an in-memory act only. That is
 * deliberate, and it is what makes the course re-derivable: see getChargeOutcome(), which the turn
 * advance calls a second time, on a DIFFERENT gamedata object, to decide the reload.
 *
 * THE PATH MODEL (D2, and the one simplification in this class worth arguing about)
 * The rules move the charge "in a manner similar to a fighter... speed 16 with 16 thrust and a 1/4
 * turn cost", i.e. 16 hexes and 4 manoeuvres, "with a manoeuvre counting as a turn or slide". A
 * full fighter plotter would be a second movement engine; instead each click is a WAYPOINT, and a
 * leg between two waypoints must run along one of the six hex axes. Changing axis at a waypoint
 * costs mathlib::getHexTurnCost() manoeuvres - 1 for 60 degrees, 2 for 120, 3 for a reversal -
 * which is what the same course would cost a fighter, and a one-hex leg at 60 degrees is a slide
 * priced at a slide's own cost. The first leg is free: the charge is launched, not turned.
 *
 * THE BOOST ("every additional 2 points of power applied as a boost produces either 1 additional
 * hex of range or adds the capability for an extra manoeuvre")
 * $boostEfficiency = 2 is those two points; one boost level buys ONE point, spendable on either.
 * ⭐ THE SPLIT IS NOT DECLARED - it is inferred at resolution from the course actually plotted
 * (hexes overspent plus manoeuvres overspent must fit the boost bought). That is a convenience,
 * not a power increase: the totals are identical either way, and it saves an Initial Orders
 * allocation dialog for a choice the player cannot make properly until they can see the board. The
 * BOOST ITSELF is still bought in Initial Orders, which is what the rules' "must be configured for
 * firing" is asking for.
 *
 * THE RECEIVING TRANSCEIVER PAYS FOR A SHORT COURSE
 * "For every 2 full hexes of possible range remaining on a received charge, the SCT must take a
 * point of damage, rolling critical hits as normal." Remaining is measured against the BASE range
 * (16), never the boosted one: a player who buys boost and spends it on manoeuvres has not
 * lengthened the charge, and one who spends it on hexes has by definition used those hexes. The
 * damage lands as an ordinary DamageEntry on the receiving system, so Criticals::setCriticals rolls
 * its critical in the same pass as every other system damaged this turn (isDamagedOnTurn).
 *
 * THE DUAL RECHARGE
 * "The rate of fire is 1 per 3 turns. This refers to the weapon's ability to fire a RECOVERED
 * charge. If a charge is not recovered (if it is intercepted, for example), then the recharge rate
 * is increased to 1 per turn." So a course that ends nowhere is not purely a loss - the transceiver
 * simply modulates a fresh charge, which is quick. calculateLoading() below is the whole of it.
 * ⚠️ INTERCEPTION IS NOT MODELLED AS AN EVENT OF ITS OWN. FV only intercepts ballistics, and this
 * weapon declares and resolves inside the Fire phase; "not recovered" is the general case that the
 * rules give interception as one example of, and it is the case implemented here.
 *
 * WHAT THIS CLASS DELIBERATELY DOES NOT DO
 * - The charge does not get its own hit section: a shot resolves on the FIRING ship's bearing, like
 *   any other direct-fire weapon, rather than on the bearing of the leg it arrived along. Doing it
 *   properly means a ballistic-style hit LOCATION and a ballistic-style defence PROFILE, and the
 *   two have to move together or the profile and the section describe different shots.
 * - It does not test line of sight along the course. A steered charge is not a beam, and the
 *   engine's only LoS test is a straight line from shooter to target hex, which a course is not.
 */
class SensorChargeTransceiver extends Weapon {

    public $name        = "SensorChargeTransceiver";
    public $displayName = "Sensor Charge Transceiver";
    //⚠️ Case-sensitive on the live Linux box (arch_dockingcollar_icon_case).
    public $iconPath    = "SensorChargeTransceiver.png";

    public $animation      = "bolt";
    public $animationColor = array(120, 255, 210); //pale sensor green

    public $weaponClass = "Electromagnetic"; //all Walker weaponry is Electromagnetic
    public $factionAge  = 3;                 //Ancient - matters to several to-hit and EDF rules

    public $damageType  = "Standard";
    /* "Damage is scored as a single standard-mode volley with overkill not transferring to
       structure (similar to a matter weapon volley)." */
    public $noOverkill  = true;

    public $loadingtime = 3;                 //"1 per 3 turns" - calculateLoading has the other rate
    public $priority    = 6;
    public $fireControl = array(1, 2, 3);    //fighters, <=mediums, <=capitals
    /* "Range penalty: None." Implemented as a real override of calculateRangePenalty() rather than
       a delta on ->needed, because the parent derives the no-lock and jammer modifiers from it. */
    public $rangePenalty = 0;
    /* 0 means UNLIMITED to weaponManager.targetHex and to isInDistanceRange. The charge's reach is
       its own hex budget, which has nothing to do with how far a target sits from the ship. */
    public $range        = 0;

    public $hextarget     = true;
    public $canSplitShots = true;            //D2: routes each click through doMultipleHexFireOrders
    /* The aim point is a COURSE, and a course drawn on the enemy's map a phase early would hand
       them every hex the charge is about to cross. TacGamedata::hideSystemFireOrders blanks x/y on
       a hidetarget order for anyone not on the shooter's team, for the turn it is declared on. */
    public $hidetarget    = true;
    /* And its ->notes with it. A waypoint token can name the unit the player picked out of a
       shared hex (TARGET_TOKEN below), and that hex is on the very course hidetarget has just
       blanked - so the two masks have to move together. See Weapon::$hideNotesFromEnemies. */
    protected $hideNotesFromEnemies = true;
    /* Nothing to shoot down: FV's interception machinery is for ballistics in flight, and this
       charge is declared and resolved inside one Fire phase. The rules' "a successful interception
       results in the loss of the charge" is folded into the not-recovered branch - class comment. */
    public $uninterceptable = true;
    public $doNotIntercept  = true;
    public $intercept       = 0;

    public $guns         = 1;
    public $defaultShots = 1;
    public $maxVariableShots = 5;            //client hint: the manoeuvres plus the free first leg
    public $firingModes  = array(1 => "Sensor Charge");
    public $hideFiringModeSelector = true;   //one mode; the selector would be a dead control

    /* THE BOOST. $boostEfficiency is the EXTRA power one level costs - the trap recorded against
       the Energy Draining Field, where a declared 0 made the boost free. The rules price a level at
       2 points and give it either a hex or a manoeuvre. $maxBoostLevel caps it at 4 (8 power on top
       of the base 5); the rules state no ceiling, so this is the tunable number. */
    public $boostable     = true;
    public $maxBoostLevel = 4;
    public $boostEfficiency = self::BOOST_POWER_PER_LEVEL;

    /* THE CHARGE'S OWN MOVEMENT ALLOWANCE. Named because four things read them: the resolver, the
       tooltip, the client mirror in special.js, and the plan.
       ⚠️ MIRROR PAIR with SensorChargeTransceiver.prototype.chargeRange / chargeManoeuvres. */
    const CHARGE_RANGE      = 16;    //"a charge defaults to a range of 16 hexes"
    const CHARGE_MANOEUVRES = 4;     //"and the ability to make 4 manoeuvres"
    const BOOST_POWER_PER_LEVEL = 2; //"every additional 2 points of power applied as a boost"
    const SELFDAMAGE_PER_HEXES  = 2; //"for every 2 full hexes of possible range remaining"

    /* The waypoint token the client encodes into ->notes. The index is what orders the course:
       fire orders come back from the database in an order MySQL does not promise, and a path read
       out of sequence is a different path. */
    const WAYPOINT_TOKEN = 'SCT|w:';

    /* The OPTIONAL second half of that token: "|t:<shipid>", the unit the player chose to hit in
       that waypoint's hex ("providing that it is sent against only one target per hex" gives the
       player the choice between units sharing one). A waypoint that continues on the SAME bearing
       costs no manoeuvre, so dropping one on a crowded hex is a free way to say which - which is
       why the choice rides the waypoint rather than needing a per-hex gesture of its own.
       ⚠️ ADVISORY, NEVER AUTHORITATIVE: the named unit still has to pass every eligibility test in
       pickTargetInHex, and anything that does not falls silently back to the automatic pick. So a
       stale or hostile POST can change WHICH legal enemy in that hex is hit and nothing else.
       ⚠️ MIRROR PAIR with SensorChargeTransceiver.TARGET_TOKEN in special.js. */
    const TARGET_TOKEN = '|t:';

    /* damageclass on the informational order that reports the transfer. ⚠️ It is also the key
       weaponManager.doShortLogText's shortLogTypes matches on, which is what makes the combat log
       print the pubnotes ALONE rather than behind "firing 1x ... at ...". Because of that, the
       pubnotes must name the ships itself, as bare shiplink spans. */
    const LOG_DAMAGECLASS = 'SensorCharge';

    /* 6d10 (rules). A flat volley - the charge either reaches a hex or it does not, and nothing
       about the course changes what it is carrying when it gets there. */
    protected $damageDice = 6;

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        if ($maxhealth == 0) $maxhealth = 8;   //"Health 8"
        if ($powerReq  == 0) $powerReq  = 5;   //"Power 5"
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    /* ------------------------------------------------------------------ the charge's budget --- */

    /* Boost levels bought for $turn. The same loop every boostable system in the tree writes for
       itself (Engine, Shield Generator, Energy Draining Field); type 2 is a boost allocation. */
    protected function getChargeBoost($turn){
        $boost = 0;
        foreach ($this->power as $entry){
            if ($entry->turn != $turn) continue;
            if ($entry->type == 2) $boost += $entry->amount;
        }
        return (int)$boost;
    }

    /* Is this transceiver able to send a charge at all right now? The same test both intercept
       gates in firing.php make, reused here because NOTHING on the server refuses an offensive
       order from an unloaded weapon - the client's isLoaded is the only gate on that path, and a
       three-turn recharge that only the client enforced would not be a recharge. */
    protected function isReadyToFire(){
        return $this->getTurnsloaded() >= $this->getLoadingTime();
    }

    /* Can this transceiver RECEIVE a charge? Deliberately weaker than isReadyToFire(): receiving is
       not firing, so a transceiver still recharging - or one that has already sent its own charge
       this turn - is a perfectly good destination. Only a wrecked one is not. */
    public function canReceiveCharge($turn){
        return !$this->isDestroyed($turn);
    }

    /* ------------------------------------------------------------------ the resolver ---------- */

    /* Waypoint orders belonging to this weapon on $turn, in the order the player clicked them.
       ⚠️ Sorted by the token index, not by array position: DBManager hands fire orders back in
       whatever order the query produced, and the legs of a course are not commutative. */
    protected function getWaypointOrders($turn){
        $found = array();
        foreach ($this->fireOrders as $order){
            if ($order->weaponid != $this->id) continue;
            if ($order->turn != $turn) continue;
            if ($order->type != "normal") continue;
            $index = self::readWaypointIndex($order);
            if ($index === null) continue;
            $found[] = array('index' => $index, 'order' => $order);
        }

        usort($found, function($a, $b){
            if ($a['index'] == $b['index']) return 0;
            return ($a['index'] < $b['index']) ? -1 : 1;
        });

        $orders = array();
        foreach ($found as $entry) $orders[] = $entry['order'];
        return $orders;
    }

    /* The waypoint's position in the course, or null when the order carries no token at all - so it
       is not a waypoint (a hand-built payload, or a client that predates this weapon). */
    public static function readWaypointIndex($order){
        if (empty($order->notes)) return null;
        if (!preg_match('/SCT\|w:(\d+)/', $order->notes, $matches)) return null;
        return (int)$matches[1];
    }

    /* The unit the player named for this waypoint's hex, or null when they named none.
       ⚠️ Deliberately a SEPARATE match rather than one regex with an optional group: a waypoint
       with no choice is the ordinary case, and readWaypointIndex must keep answering for the
       courses declared before this token existed. */
    public static function readWaypointTarget($order){
        if (empty($order->notes)) return null;
        if (!preg_match('/SCT\|w:\d+\|t:(\d+)/', $order->notes, $matches)) return null;
        return (int)$matches[1];
    }

    /* ⭐⭐ THE SINGLE AUTHORITY ON WHAT A DECLARED COURSE DOES, and it is called from TWO places, on
     * two different gamedata objects:
     *
     *   - beforeFiringOrderResolution(), during the Fire phase advance, to build the shots;
     *   - calculateLoading(), at the turn advance, to decide whether the reload is 1 turn or 3.
     *
     * ⚠️ THE SECOND CALLER IS WHY THIS RE-DERIVES EVERYTHING AND PERSISTS NOTHING. The turn advance
     * sweep runs on the gamedata object Manager::advanceGameState loaded BEFORE the Fire phase
     * resolved, so an individual note written during that resolution is in the database but not on
     * this object. Re-walking the same waypoints against the same positions gives the same answer
     * with no storage at all, and replays identically. (The waypoint orders themselves ARE on both
     * objects: the client's POST persisted them, and both loads read the same turn's fire orders.)
     *
     * Returns null when there is nothing to resolve. Otherwise:
     *   accepted   - the waypoint orders that fitted the budget, in flight order
     *   rejected   - the ones that did not (a tampered or stale POST; the client never sends these)
     *   hexes      - every hex the charge enters, origin EXCLUDED, in flight order, deduplicated
     *   hexesUsed / manoeuvresUsed  - what the course actually spent
     *   remaining  - unused BASE range, which is what the receiving transceiver pays for
     *   receiver / receiverSystem   - the friendly ship and transceiver the charge reached, or null
     *   ready      - was this weapon loaded at all
     */
    public function getChargeOutcome($gamedata, $turn){
        $orders = $this->getWaypointOrders($turn);
        if (empty($orders)) return null;

        $shooter = $this->getUnit();
        if (!$shooter) return null;

        $outcome = array(
            'accepted' => array(), 'rejected' => array(), 'hexes' => array(),
            'hexesUsed' => 0, 'manoeuvresUsed' => 0, 'remaining' => self::CHARGE_RANGE,
            'receiver' => null, 'receiverSystem' => null, 'ready' => $this->isReadyToFire(),
            //hexKey => shipid, from the accepted waypoints that named one. See TARGET_TOKEN.
            'targetChoices' => array(),
        );

        $boost       = $this->getChargeBoost($turn);
        $previous    = $shooter->getHexPos();
        $lastBearing = null;
        $seen        = array(self::hexKey($previous) => true);

        foreach ($orders as $order){
            $to = new OffsetCoordinate((int)$order->x, (int)$order->y);

            //⚠️ CAST. OffsetCoordinate::distanceTo works in cube coordinates and answers a FLOAT,
            //which would otherwise make hexesUsed and remaining floats too - and 'remaining' is
            //divided to produce the receiver's damage, which has to be a whole number of points.
            $legLength = (int)round(mathlib::getDistanceHex($previous, $to));
            $bearing   = mathlib::getHexDirection($previous, $to);

            //A leg has to go somewhere, and it has to go somewhere STRAIGHT.
            $legValid = ($legLength >= 1) && ($bearing !== null);
            $turnCost = 0;

            /* And the FIRST one has to go somewhere the mount can point. Truncating at leg 1
               rejects the whole course, which is right: a charge that cannot be launched has not
               been launched. ⚠️ Only the first - $lastBearing is still null exactly then. */
            if ($legValid && $lastBearing === null && !$this->isLaunchBearingOnArc($shooter, $bearing)){
                $legValid = false;
            }

            if ($legValid){
                $turnCost = ($lastBearing === null)
                          ? 0                                                //the launch is free
                          : mathlib::getHexTurnCost($lastBearing, $bearing);

                $hexes      = $outcome['hexesUsed'] + $legLength;
                $manoeuvres = $outcome['manoeuvresUsed'] + $turnCost;

                /* THE DEFERRED BOOST ALLOCATION, in one expression. Overspending on hexes and
                   overspending on manoeuvres both come out of the same pool of boost levels, so the
                   player never has to say in advance which of the two they bought. */
                $overspend = max(0, $hexes - self::CHARGE_RANGE)
                           + max(0, $manoeuvres - self::CHARGE_MANOEUVRES);
                $legValid  = ($overspend <= $boost);
            }

            /* ⚠️ TRUNCATE, do not skip. The legs of a course are a chain: dropping one in the middle
               would silently teleport the charge across the gap. The first leg the budget cannot pay
               for ends the course, and everything after it is rejected with it. */
            if (!$legValid || !empty($outcome['rejected'])){
                $outcome['rejected'][] = $order;
                continue;
            }

            $walk = $previous;
            for ($step = 0; $step < $legLength; $step++){
                $walk = mathlib::moveInDirection($walk, $bearing, 1);
                $key  = self::hexKey($walk);
                if (isset($seen[$key])) continue;   //a course may cross itself; a hex is hit once
                $seen[$key] = true;
                $outcome['hexes'][] = $walk;
            }

            $outcome['hexesUsed']      += $legLength;
            $outcome['manoeuvresUsed'] += $turnCost;
            $outcome['accepted'][]      = $order;

            //Only an ACCEPTED waypoint's choice counts: a rejected leg is never flown, so its hex
            //is not on the course at all and a preference recorded for it would name a unit the
            //charge never reaches.
            $chosen = self::readWaypointTarget($order);
            if ($chosen !== null) $outcome['targetChoices'][self::hexKey($to)] = $chosen;
            $previous    = $to;
            $lastBearing = $bearing;
        }

        //Unused BASE range - the class comment says why the boosted range is not the yardstick.
        $outcome['remaining'] = max(0, self::CHARGE_RANGE - $outcome['hexesUsed']);

        /* "The charge must end its movement in the same hex as another ship with a SCT" - and
           "(or possibly returning to the originator)", so the firing ship's own hex counts. */
        if (!empty($outcome['accepted'])){
            $receiver = self::findReceiver($gamedata, $shooter, $previous, $turn);
            if ($receiver !== null){
                $outcome['receiver']       = $receiver['ship'];
                $outcome['receiverSystem'] = $receiver['system'];
            }
        }

        return $outcome;
    }

    /* ⭐⭐ THE MOUNT AIMS THE LAUNCH, NOT THE COURSE (user ruling 2026-09-06). The Scribe's
       transceiver is a 300..60 mount, and until now that arc did nothing here at all - the plan
       had it as 0..360 on the reasoning that "the charge steers, so the section is for damage, not
       coverage". The arc is real, and what it bounds is the direction the charge LEAVES ON: the
       first leg must run along one of the hex lines the mount covers, and after that the charge
       steers wherever its manoeuvres will take it.

       ⚠️ Asked through the ship's OWN getBearingOnPos, about the hex one step along the launch
       bearing, rather than by comparing degrees here. That is the same call every other arc test
       on the server makes, so this inherits the facing arithmetic and the ROLL mirror for free -
       and it never has to assume that a hex direction and a compass heading are the same number.
       ⚠️ MIRROR PAIR with SensorChargeTransceiver.prototype.isLaunchBearingOnArc in special.js,
       which asks weaponManager.isPosOnWeaponArc the identical question about the identical hex. */
    protected function isLaunchBearingOnArc($shooter, $bearing){
        //A mount with no arc at all, or a full circle (isInArc's own start == end case).
        if (!is_int($this->startArc) || !is_int($this->endArc)) return true;

        $oneStep  = mathlib::moveInDirection($shooter->getHexPos(), $bearing, 1);
        $relative = $shooter->getBearingOnPos($oneStep);

        return mathlib::isInArc($relative, $this->startArc, $this->endArc);
    }

    /* The friendly ship standing on $hex with a transceiver able to take the charge, or null.
       Same TEAM rather than same player: "any other ship in the fleet" is a fleet question, and FV
       treats a team as one fleet everywhere else a friendly is asked for. */
    protected static function findReceiver($gamedata, $shooter, OffsetCoordinate $hex, $turn){
        $wanted = self::hexKey($hex);

        foreach ($gamedata->ships as $ship){
            if ($ship->team != $shooter->team) continue;
            if ($ship->isDestroyed()) continue;
            if (empty($ship->movement)) continue;                       //never placed - no position
            if ($ship->getTurnDeployed($gamedata) > $turn) continue;    //not on the board yet
            if (self::hexKey($ship->getHexPos()) !== $wanted) continue;

            foreach ($ship->systems as $system){
                if (!($system instanceof SensorChargeTransceiver)) continue;
                if (!$system->canReceiveCharge($turn)) continue;
                return array('ship' => $ship, 'system' => $system);
            }
        }

        return null;
    }

    protected static function hexKey($hex){
        return ((int)$hex->q) . ',' . ((int)$hex->r);
    }

    /* ------------------------------------------------------------------ resolution ------------ */

    public function beforeFiringOrderResolution($gamedata){
        $outcome = $this->getChargeOutcome($gamedata, $gamedata->turn);
        if ($outcome === null) return;

        $shooter = $this->getUnit();

        /* STEP 1 - every waypoint order comes off the weapon, accepted or not. They are a
           DECLARATION, not shots: left in place, Firing::prepareFiring would hand each one to
           calculateHitBase with targetid -1 and stamp "ERROR: Null target shot attempted in normal
           fire routines" into the player's own combat log. Their database rows are untouched. */
        $waypoints = array_merge($outcome['accepted'], $outcome['rejected']);
        $keep = array();
        foreach ($this->fireOrders as $order){
            if (!in_array($order, $waypoints, true)) $keep[] = $order;
        }
        $this->fireOrders = $keep;

        if (!$outcome['ready']){
            //A tampered or stale POST from a transceiver that is still recharging. Said out loud
            //rather than silently dropped: an order vanishing with no explanation is the failure
            //mode this avoids.
            $this->logChargeEvent($gamedata, $shooter, $shooter,
                "'s <b>Sensor Charge Transceiver</b> is still modulating and could not send a charge.");
            return;
        }

        if (empty($outcome['accepted'])) return;   //nothing survived validation - nothing happened

        /* STEP 2 - did the charge come home? "If it does not, then the charge is lost, and it is
           unable to do any damage during the turn." No shots at all, whatever it flew over. */
        if ($outcome['receiver'] === null){
            $this->logChargeEvent($gamedata, $shooter, $shooter,
                "'s sensor charge ran its course of " . (int)$outcome['hexesUsed']
                . " hexes with no transceiver to receive it. <b>The charge is lost</b> and does no"
                . " damage; a fresh charge is modulated in one turn instead of three.");
            return;
        }

        /* STEP 3 - one shot per enemy-occupied hex on the course, "providing that it is sent
           against only one target per hex". Built before the receiver's self-damage so that the
           informational row sits after the shots it describes. */
        $shots = 0;
        foreach ($outcome['hexes'] as $hex){
            $key    = self::hexKey($hex);
            $chosen = isset($outcome['targetChoices'][$key]) ? $outcome['targetChoices'][$key] : null;
            $target = self::pickTargetInHex($gamedata, $shooter, $hex, $gamedata->turn, $chosen);
            if ($target === null) continue;
            $this->createChargeShot($gamedata, $shooter, $target, $hex);
            $shots++;
        }

        /* STEP 4 - "For every 2 full hexes of possible range remaining on a received charge, the
           SCT must take a point of damage, rolling critical hits as normal." */
        $selfDamage = (int)floor($outcome['remaining'] / self::SELFDAMAGE_PER_HEXES);
        $logOrder   = $this->logChargeEvent($gamedata, $shooter, $outcome['receiver'],
            "'s sensor charge ran " . (int)$outcome['hexesUsed'] . " hexes through "
            . $shots . " enemy " . ($shots == 1 ? "hex" : "hexes")
            . " and was received by " . self::shipLink($outcome['receiver']) . ".");

        if ($selfDamage > 0){
            $this->applyReceiverDamage($gamedata, $shooter, $outcome, $selfDamage, $logOrder);
        }
    }

    /* One enemy unit standing on $hex, or null. The rules make hitting optional ("the SCT MAY
       attempt to hit it") and that half is still resolved here - declining a free hit is never a
       decision.

       ⭐ THE CHOICE BETWEEN UNITS SHARING A HEX IS THE PLAYER'S when they asked for it, and the
       automatic pick when they did not (user ruling 2026-09-06). $preferredId is whatever a
       waypoint on this hex named, and it wins ONLY by passing the same eligibility loop every
       other candidate does - so an id that is friendly, dead, terrain, not yet deployed or simply
       somewhere else is not an error, it just leaves the automatic pick in charge.

       That automatic pick is unchanged and stays deterministic and replay-stable: capitals before
       small craft, bigger before smaller, then lowest id. */
    protected static function pickTargetInHex($gamedata, $shooter, OffsetCoordinate $hex, $turn, $preferredId = null){
        $wanted = self::hexKey($hex);
        $best   = null;

        foreach ($gamedata->ships as $ship){
            if ($ship->team == $shooter->team) continue;
            if ($ship instanceof Terrain) continue;
            if ($ship->isDestroyed()) continue;
            if (empty($ship->movement)) continue;
            if ($ship->getTurnDeployed($gamedata) > $turn) continue;
            if (self::hexKey($ship->getHexPos()) !== $wanted) continue;

            //Named by the player, and legal - nothing else in the hex is even considered.
            if ($preferredId !== null && (int)$ship->id === (int)$preferredId) return $ship;

            if ($best === null || self::isBetterTarget($ship, $best)) $best = $ship;
        }

        return $best;
    }

    protected static function isBetterTarget($candidate, $incumbent){
        $candidateSmall = ($candidate instanceof FighterFlight) || $candidate->mine;
        $incumbentSmall = ($incumbent instanceof FighterFlight) || $incumbent->mine;
        if ($candidateSmall != $incumbentSmall) return !$candidateSmall;

        if ($candidate->shipSizeClass != $incumbent->shipSizeClass){
            return $candidate->shipSizeClass > $incumbent->shipSizeClass;
        }

        return $candidate->id < $incumbent->id;   //the tie-break that makes a replay identical
    }

    /* A real shot. ⚠️ INSERTED IMMEDIATELY to claim a database id: several of these exist per turn
       on one weapon, and DamageEntry copies $fireOrder->id at the moment damage is dealt. Left at
       -1 they would all share it, and DBManager::submitDamages' back-fill (same shooter + weapon +
       turn, first row, no ORDER BY) would file every hit against whichever row it found first. */
    protected function createChargeShot($gamedata, $shooter, $target, OffsetCoordinate $hex){
        $order = new FireOrder(
            -1, "normal", $shooter->id, $target->id,
            $this->id, -1, $gamedata->turn, $this->firingMode,
            0, 0, 1, 0, 0,                       //needed, rolled, shots, shotshit, intercepted
            $hex->q, $hex->r, strtolower($this->weaponClass), -1
        );
        $order->pubnotes = " Sensor charge passes through this hex.";
        $order->addToDB  = true;

        $newId = Manager::insertSingleFiringOrder($gamedata, $order);
        if ($newId) $order->id = (int)$newId;
        $order->addToDB = false;
        $order->updated = true;

        $this->fireOrders[] = $order;
        return $order;
    }

    /* The combat log row for the transfer itself. HOSTED ON THIS WEAPON, not on RammingAttack: the
       client resolves fire.weaponid against the SHOOTER and demands a Weapon, and both are true of
       the transceiver that sent the charge.
       ⚠️ $rolled MUST be non-zero or the printed log drops the row without a word
       (weaponManager.isResolvedFireOrder is literally `Number(fire.rolled) > 0`), while $shots and
       $shotshit MUST stay 0 so the row can never steal a real shot's damage in submitDamages'
       back-fill. A non-zero rolled is ALSO what stops Firing::fire from passing this to fire(). */
    protected function logChargeEvent($gamedata, $shooter, $subject, $text){
        $order = new FireOrder(
            -1, "normal", $shooter->id, $subject->id,
            $this->id, -1, $gamedata->turn, $this->firingMode,
            100, 1, 0, 0, 0,                     //needed, rolled=1 marker, no shots
            0, 0, self::LOG_DAMAGECLASS, 10000
        );
        /* ⚠️ THE SHOOTER IS ALREADY NAMED - DO NOT NAME IT AGAIN (user report 2026-09-06: the
           name appeared twice, once white and once in team colour). combatLog.js opens EVERY entry
           with `FIRE: <shiplink shooter>`, and a short-log entry (doShortLogText) then appends the
           pubnotes to that same line with no separator - so a pubnotes beginning with its own
           shipLink printed the name twice running. Every $text handed to this method therefore
           starts with "'s", and the rendered line reads "FIRE: Scribe #2's sensor charge ran...".
           ⚠️ Other ships named MID-sentence still need a bare shipLink span, and the receiver's
           does - log colours are per-VIEWER (howto_create_fire_orders). It is only the LEADING
           one, the shooter's, that the header has already supplied. */
        $order->pubnotes = $text;
        $order->addToDB  = true;

        $newId = Manager::insertSingleFiringOrder($gamedata, $order);
        if ($newId) $order->id = (int)$newId;
        $order->addToDB = false;

        $this->fireOrders[] = $order;
        return $order;
    }

    /* "the SCT must take a point of damage, rolling critical hits as normal". The critical is NOT
       rolled here: this runs inside the Fire phase advance, and Criticals::setCriticals' first pass
       tests every system that isDamagedOnTurn() a moment later. */
    protected function applyReceiverDamage($gamedata, $shooter, $outcome, $points, $logOrder){
        $system = $outcome['receiverSystem'];
        $ship   = $outcome['receiver'];

        $remainingHealth = $system->getRemainingHealth();
        $destroyed       = ($points >= $remainingHealth);
        $dealt           = min($points, $remainingHealth);

        $entry = new DamageEntry(
            -1, $ship->id, -1, $gamedata->turn, $system->id,
            $dealt, 0, 0, (int)$logOrder->id, $destroyed, false, "", self::LOG_DAMAGECLASS
        );
        $entry->updated = true;
        //Named anyway, even though fireorderid is already real: submitDamages only falls back to
        //these when it is not, and a future change that loses the id should not lose the shooter.
        $entry->shooterid = $shooter->id;
        $entry->weaponid  = $this->id;
        $system->damage[] = $entry;

        $logOrder->pubnotes .= " " . (int)$outcome['remaining'] . " hexes of range went unused, so"
            . " the receiving transceiver absorbs " . $dealt . " point"
            . ($dealt == 1 ? "" : "s") . " of damage"
            . ($destroyed ? " and is destroyed." : ".");
    }

    /* A ship name in the combat log is drawn in the READER's team colours and the server has no
       idea who is reading - so it goes out with no colour of its own, and
       combatLog.colourShipLinksInNotes() fills it in per viewer on the way to the DOM. */
    protected static function shipLink($ship){
        return '<span class="shiplink" data-id="' . (int)$ship->id . '">' . $ship->name . '</span>';
    }

    /* ------------------------------------------------------------------ to hit and damage ----- */

    public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder){
        //The informational row is not a shot, and must keep the needed/rolled it was built with.
        if ($fireOrder->damageclass === self::LOG_DAMAGECLASS) return;
        parent::calculateHitBase($gamedata, $fireOrder);
    }

    /* "All EW and fire control modifiers apply (although there is no range penalty)... There is no
       degradation on the chance to hit." A real override rather than a delta on ->needed: the
       parent derives the no-lock and the jammer modifiers from this same method, and in the
       doubleRangeIfNoLock branch calls it again at two modified distances. */
    public function calculateRangePenalty($distance){
        return 0;
    }

    public function getDamage($fireOrder){
        return Dice::d(10, $this->damageDice);
    }

    public function setMinDamage(){
        $this->minDamage = $this->damageDice;
    }

    public function setMaxDamage(){
        $this->maxDamage = $this->damageDice * 10;
    }

    /* ------------------------------------------------------------------ the dual recharge ----- */

    /* "The rate of fire is 1 per 3 turns. This refers to the weapon's ability to fire a RECOVERED
     * charge. If a charge is not recovered... then the recharge rate is increased to 1 per turn."
     *
     * The turn advance (phase -1) is the one write that decides what this weapon has next turn, and
     * the parent's version of it hands a weapon that fired last turn a single point of charge
     * (0 -> +1 -> 1/3). Filling it instead is the whole of the fast rate.
     *
     * ⚠️ $gamedata->turn has ALREADY been stepped past the turn that fired by the time this runs,
     * which is why the outcome is asked about turn - 1. The fire orders loaded on this object are
     * that turn's, exactly as the Wide-Beam cooldown relies on.
     *
     * ⚠️ NOT done by lowering $loadingtime for a turn. That is a BLUEPRINT field riding the
     * per-class static bundle rather than the poll payload, so the client would go on reading 3 -
     * the Stage 7 trap. $turnsloaded IS published per instance by Weapon::stripForJson, so filling
     * it is visible for free. */
    public function calculateLoading(TacGamedata $gamedata){
        /* ⚠️⚠️ ASKED BEFORE THE PARENT RUNS, and that ordering is load-bearing.
           parent::calculateLoading's phase -1 branch calls setLoading(), which writes
           $turnsloaded back to 0 for a weapon that fired - and isReadyToFire() inside
           getChargeOutcome() reads exactly that field. Asked afterwards, every charge looks as
           though it was sent by an unloaded transceiver, the fast rate never fires, and the only
           symptom is a recharge that is silently always three turns. */
        $outcome = ($gamedata->phase == -1)
                 ? $this->getChargeOutcome($gamedata, $gamedata->turn - 1)
                 : null;

        $loading = parent::calculateLoading($gamedata);
        if (!$loading) return $loading;
        if ($gamedata->phase != -1) return $loading;

        if ($outcome === null) return $loading;             //declared nothing last turn
        if (!$outcome['ready']) return $loading;            //was not loaded, so it never fired
        if (empty($outcome['accepted'])) return $loading;
        if ($outcome['receiver'] !== null) return $loading; //recovered: the slow rate stands

        return new WeaponLoading(
            $this->getLoadingTime(),   //<- a fresh charge is ready NEXT turn: THE fast rate
            $loading->extrashots,
            $loading->loadedammo,
            $loading->overloading,
            $loading->loadingtime,
            $loading->firingmode
        );
    }

    /* ------------------------------------------------------------------ display --------------- */

    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        } else {
            $this->data["Special"] .= '<br>';
        }

        $boost = $this->getChargeBoost($turn);

        $this->data["Special"] .= "Plots a course for a sensor charge, and damages every"
            . " enemy unit it passes through - one target per hex with no"
            . " range penalty.";
        $this->data["Special"] .= "<br>The charge flies in straight legs: " . self::CHARGE_RANGE
            . " hexes and " . self::CHARGE_MANOEUVRES . " manoeuvres, a manoeuvre being each 60"
            . "&deg; turn taken.";
        $this->data["Special"] .= "<br>Every " . self::BOOST_POWER_PER_LEVEL . " points of boost"
            . " power buy one more hex OR one more manoeuvre, spent as the course needs them."
            . ($boost > 0 ? " <b>Boosted this turn: +" . $boost . ".</b>" : "");
        $this->data["Special"] .= "<br>The course MUST end at a friendly ship with a"
            . " Sensor Charge Transceiver or the charge is lost and does no"
            . " damage.";
        $this->data["Special"] .= "<br>The receiving transceiver takes 1 point of damage, rolling"
            . " criticals as normal, for every " . self::SELFDAMAGE_PER_HEXES . " full hexes of"
            . " range the charge did not use.";
        $this->data["Special"] .= "<br>Recharges in " . $this->loadingtime . " turns after a"
            . " recovered charge, but in 1 turn when a charge is lost.";
    }

    /* The tooltip quotes this turn's boost, so it is per instance and cannot ride the per-class
       static blueprint. Everything else here is re-read from the live system, so ShipCompactor
       stripping a value at its default is harmless.

       ⚠️ THE OTHER TWO ARE DISPLAY-ONLY AND THE CLIENT CANNOT DERIVE EITHER (user report
       2026-09-06: "SCT is also not showing any arcs on normal hover like it should as a weapon").
       ShipIcon.showWeaponArc sizes every arc through getWeaponReachInHexes(), which reads
       ->range and ->rangePenalty - and this weapon carries BOTH at 0, meaning "no range limit"
       and "no range penalty". That works out to a reach of ZERO hexes, and nothing was drawn at
       all. $arcDisplayRange is the reach to draw instead: the charge's own BASE range, which is
       the honest answer to "how far does this thing reach" even though it has nothing to do with
       how far a target sits from the ship. Base rather than boosted, because a hover arc is a
       property of the mount and the boost is bought per turn.

       $shootsStraight then picks showStraightArcs' STAR OF HEX LINES over a smooth wedge, which
       is exactly the set of hexes a charge flying along hex axes can launch onto - and it is
       clipped to the mount's arc, so a 300..60 transceiver draws its three arms and no more.
       Both are protected on Weapon and reach the client only by being named here. */
    public function stripForJson(){
        $strippedSystem = parent::stripForJson();

        $strippedSystem->data            = $this->data;
        $strippedSystem->shootsStraight  = $this->shootsStraight;
        $strippedSystem->arcDisplayRange = self::CHARGE_RANGE;

        return $strippedSystem;
    }

}//endof class SensorChargeTransceiver

    class LightChromaticPulsar extends LinkedWeapon{
        public $name = "LightChromaticPulsar";
        public $displayName = "Light Chromatic Pulsar"; //it's not 'paired' in any way, except being usually mounted twin linked - like most fighter weapons...
        public $animation = "bolt";
    	public $animationColor = array(140, 210, 255); //pale electric blue

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;
		public $priority = 4; //correct for d6+2 and lighter

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
        
        public $damageType = "Standard";
        public $weaponClass = "Electromagnetic";

        /* D16 (WALKERS_OF_SIGMA_PLAN.md 3.13, Stage 14) - THE FLIGHT-WIDE EXCLUSIVITY GROUP. If any
           craft in the flight fires its Medium Lightning Array, no craft may fire its pulsar this
           turn, and the reverse. The whole rule lives in MedLightningArrayFtr::planFlightVolley -
           this class carries the group name so the sweep can find it, and the two members below so
           it can be told it lost. See that class for why the existing per-CRAFT $exclusive pair
           cannot express a flight-wide rule. */
        public $flightExclusiveGroup = 'MapmakerPrimary';

        /* Orders MedLightningArrayFtr cancelled, id => reason. Written during
           beforeFiringOrderResolution and read a moment later in calculateHitBase, both in the same
           request on the same object graph - exactly how NeutronBlaster's $isCombined marker travels.
           WARNING: protected, so an empty array never rides the static blueprint as JSON []. */
        protected $flightExclusionCancelled = array();

        public function cancelForFlightExclusion($fireOrder, $reason){
            $this->flightExclusionCancelled[$fireOrder->id] = $reason;
        }

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            if (!isset($this->data["Special"])) {
                $this->data["Special"] = '';
            } else {
                $this->data["Special"] .= '<br>';
            }
            $this->data["Special"] .= "The flight cannot fire its Light Chromatic Pulsars and its"
                                   . " Medium Lightning Arrays in the same turn.";
        }

        /* The losing half of D16. Marked technical rather than silently dropped: the player gets a
           line saying why the shot did not happen, and letting the parent run would compute a fresh
           ->needed and un-refuse it. */
        public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder){
            if (isset($this->flightExclusionCancelled[$fireOrder->id])){
                MedLightningArrayFtr::markTechnical(
                    $this, $fireOrder, $this->flightExclusionCancelled[$fireOrder->id]);
                return;
            }
            parent::calculateHitBase($gamedata, $fireOrder);
        }

        public function getDamage($fireOrder){        return Dice::d(6, 2);   }
        public function setMinDamage(){     $this->minDamage = 2;      }
        public function setMaxDamage(){     $this->maxDamage = 12;      }
    } //endof LightChromaticPulsar

/* MEDIUM LIGHTNING ARRAY, FIGHTER MOUNT - WALKERS_OF_SIGMA_PLAN.md 3.13 (Stage 14), D15/D16/D24/D25/D26.
 *
 * The Mapmaker Sensor Probes' heavy weapon. Three or six probes fire as ONE gun - the flight
 * concentrates its arrays into a single bolt, and a lone probe's array does nothing at all.
 *
 * "Uses EW for lock-on. Effected by DEW. Does not use Flight-Level combat or Offensive Bonus.
 *  Cannot fire MLA and LCP in same turn. May fire in combined mode on first turn."
 *
 * THE CONTROL SHEET (D24) - read from the sheet, nothing inferred:
 *
 *                     3-Probe group      6-Probe group
 *   Guns              1 per 3 craft      1 per 6 craft
 *   Damage            4d10+12 (16-52)    8d10+12 (20-92)
 *   Range penalty     -1 per 3 hexes     -1 per 4 hexes
 *   Fire control      +2 / +4 / +6       +5 / +5 / +4
 *   Class             Electromagnetic    Electromagnetic
 *   Mode              Flash              Flash
 *   Rate of fire      1 per 4 turns      1 per 4 turns
 *
 * WARNING: THE FLAT +12 DOES NOT DOUBLE. 8d10+12 is not two lots of 4d10+12, so both profiles are
 * written out in $damageProfile and neither is derived from the other - the same finding Stage 8
 * recorded about the Wide Beam's 50% collateral.
 *
 * WARNING: THE 6-GROUP IS NOT A STRICT UPGRADE. Its fire control is BETTER against fighters (5 vs 2)
 * and WORSE against capitals (4 vs 6), so the modes are a real choice. Any "bigger is better"
 * shortcut in a mode hint, here or on the client, would be wrong.
 *
 * WARNING: THIS DOES NOT EXTEND LightningArray, deliberately. MediumLightningArray does (above), and
 * inheriting that branch would hand a fighter weapon two things it must not have: an entry in the
 * SYS_WBLA / SYS_WBMLA refit registry a fighter cannot buy from, and
 * LightningArray::edfSuppressesCollateral(), which is the Wide Beam's exemption from the field rule
 * below. If that is ever revisited, every `instanceof LightningArray` in the tree has to be re-read.
 *
 * FLASH COLLATERAL INSIDE AN ENERGY DRAINING FIELD COSTS THIS CLASS NO CODE AT ALL (D26).
 * "Flash damage always loses its collateral damage (friend or foe) in Energy Draining Fields, unless
 * the Lightning Array is boosted by the Wide Beam enhancement" - and that is exactly what Stage 4
 * built: Weapon::edfSuppressesCollateral() defaults to true and TacGamedata::isHexInEdfField() is
 * deliberately team-blind, because a field dampening an explosion is a property of the HEX. Wide
 * Beam is a ship refit Mapmakers cannot buy, so this weapon simply inherits the default and is
 * silenced in every field on the board, its own fleet's included.
 *
 * IT BEGINS THE GAME CHARGED, and that is the DEFAULT rather than an override:
 * Weapon::getStartLoading() returns a full charge. "May fire in combined mode on first turn" is
 * therefore the absence of the "does not begin the game fully charged" override, not the presence
 * of anything.
 *
 * LOCK-ON IS THE FLIGHT'S EW ON SHIP RULES, NOT ITS OFFENSIVE BONUS (plan 3.13c, corrected
 * 2026-09-11 from the rulebook text). One flag, $useFlightEW, built in Stage 12; this weapon is its
 * only consumer. The flight's OEW is added as a ship's would be, the target's DEW/BDEW/SDEW apply as
 * the ordinary to-hit penalty they are against a ship, and no OEW doubles the range penalty as usual.
 * "Does not use Flight-Level combat or Offensive Bonus" is ONE exclusion - flight-level combat is not
 * an FV concept (Q15). The Light Chromatic Pulsar on the same craft keeps offensive bonus PLUS the
 * flight's OEW less the target's DEW (minimum 0), and otherwise ignores defensive EW.
 *
 * HOW A GROUP IS FORMED (D15). Within ONE flight, this turn's `normal` orders on this weapon are
 * bucketed by target, called system and firing mode; each bucket forms floor(n / required) complete
 * groups and every leftover order goes technical. So a flight of six may fire two 3-groups (at the
 * same or at different targets) or one 6-group; four or five declaring in mode 1 fire one 3-group
 * and waste the rest, which is the ruling exactly.
 *
 * The shape is NeutronBlaster's (per-mode stat arrays plus a "not enough partners, mark technical"
 * branch) reaching for partners the way HyperplasmaMatrix does (walk the flight's craft, elect a
 * primary, fully nullify the rest). Two things are NOT copied from those precedents:
 *
 *   WARNING: "Damaged craft cannot contribute" is NOT isDestroyed(). HyperplasmaMatrix's
 *      getAliveFighterCount counts everything not destroyed; this weapon needs the craft at FULL
 *      health. Copying the precedent's test is the obvious mistake and it is silent - a battered
 *      flight would simply keep firing at full strength.
 *
 *   WARNING: NOTHING ON THE SERVER REFUSES AN OFFENSIVE ORDER FROM AN UNLOADED WEAPON (Stage 8's
 *      finding), and a 4-turn reload makes that expensive. An order from an array that is not
 *      charged goes technical here, which is the half a POST cannot edit out.
 *
 * $guns AND THE INTERCEPTION ENGINE (trap 11). One order carries the whole 3- or 6-craft discharge,
 * and $guns stays at the default 1 - the combined shot is ONE order on ONE mount, so
 * Firing::automateIntercept's `guns - orders` arithmetic already lands on the right number and there
 * is nothing to pad. The Slicer's game-4306 bug was padding that had to skip manual 'intercept'
 * orders; this weapon has no padding at all, and $intercept is 0, so it never presents itself as an
 * interceptor either.
 *
 * NO SELF-IMMUNITY, deliberately. HyperplasmaMatrix exempts its own flight from its splash at range
 * 0 because that is a rule of that weapon; the control sheet grants this one nothing of the kind, so
 * a Mapmaker flight sharing a hex with its target takes the ordinary 25% collateral like anyone else
 * - or none at all, if the hex is inside an Energy Draining Field.
 */
class MedLightningArrayFtr extends Weapon {

    public $name        = "MedLightningArrayFtr";
    public $displayName = "Medium Lightning Array";
    public $iconPath    = "LightningArrayMed.png";

    public $animation      = "bolt";
    public $animationColor = array(140, 210, 255); //pale electric blue, as the ship-mounted arrays

    public $damageType  = "Flash";
    public $weaponClass = "Electromagnetic"; //all Walker weaponry is Electromagnetic
    public $factionAge  = 3;                 //Ancient - matters to several to-hit and EDF rules

    /* Mode ids are referenced from the client (special.js) too - keep the two in step. */
    const MODE_THREE = 1;
    const MODE_SIX   = 2;

    public $firingMode  = 1;
    public $firingModes = array(1 => "3-Probes", 2 => "6-Probes");

    /* Craft that must declare together, at the same target and in the same mode, to form one shot
       (D15). WARNING: a const rather than a public static - a public static on a weapon class is read
       back as an instance property by MissileRack::stripForJson's reflection walk and takes every
       missile ship's payload with it (Stage 11). A const is neither iterated nor serialised. */
    const CRAFT_REQUIRED = array(1 => 3, 2 => 6);

    public $loadingtime = 4;  //D24: rate of fire 1 per 4 turns. The brief's "2 turns" was a slip (Q14).
    /* Matches the ship-mounted LightningArray family, whose own value is marked unconfirmed. It only
       orders simultaneous shots against each other, so a re-stat costs nothing but a re-record. */
    public $priority    = 6;
    public $intercept   = 0;  //the sheet gives this mount no intercept rating: it never intercepts

    /* WARNING: THESE TWO MUST EQUAL THE MODE_THREE ROW of the arrays below. They are what the generic
       "Fire control" and "Range penalty" tooltip lines report before a mode is picked, and
       changeFiringMode() overwrites them from the arrays on every mode change (both sheets). */
    public $rangePenalty      = 0.33;                            // -1 per 3 hexes
    public $rangePenaltyArray = array(1 => 0.33, 2 => 0.25);     // -1 per 3 hexes / per 4 hexes
    public $fireControl       = array(2, 4, 6);                  // fighters, <=mediums, <=capitals
    public $fireControlArray  = array(1 => array(2, 4, 6), 2 => array(5, 5, 4));

    /* Plan 3.13c. Flight EW INSTEAD of the offensive bonus, on ship rules: the target's defensive
       EW is not waived and no OEW means a no-lock penalty. Read in the FighterFlight branch of
       Weapon::calculateHitBase, mirrored on the client by weaponManager.computeBaseDefenceBreakdown
       (the waiver) and weaponManager.computeOEW (the lock). */
    public $useFlightEW = true;

    /* D16 - THE FLIGHT-WIDE EXCLUSIVITY GROUP. Weapons sharing this string on one FLIGHT may not both
       be fired in one turn: if any craft fires the array, no craft may fire its Light Chromatic
       Pulsar, and the reverse. WARNING: FLIGHT-WIDE, NOT PER CRAFT, which is why the existing
       weaponManager.checkConflictingFireOrder / $exclusive pair cannot express it - that one asks the
       CRAFT (getFighterBySystem) and would happily let probe #2 fire the pulsar while probe #1 fired
       the array. Declared on the two Mapmaker weapons only, never on Weapon, so every read is a
       truthy test and no other mount in the game grows a key. */
    public $flightExclusiveGroup = 'MapmakerPrimary';

    /* THE TWO DAMAGE PROFILES, keyed by firing mode. WARNING: written out independently - the flat
       +12 is the same in both rows and does NOT double with the dice. */
    protected $damageProfile = array(
        1 => array('dice' => 4, 'add' => 12),   //4d10+12,  16-52
        2 => array('dice' => 8, 'add' => 12),   //8d10+12,  20-92
    );

    /* What beforeFiringOrderResolution decided about each of THIS mount's orders, keyed by order id.
       Either one of the two ROLE_ constants or a human-readable refusal. Rebuilt from scratch every
       turn, never persisted, never serialised. */
    protected $orderRoles = array();
    const ROLE_PRIMARY     = '=primary';
    const ROLE_SUBORDINATE = '=subordinate';

    function __construct($startArc, $endArc){
        parent::__construct(0, 1, 0, $startArc, $endArc);
    }

    /* Craft needed to form one shot in $mode. Falls back to the 3-probe requirement rather than to 1,
       so an unknown mode can never be made to fire a group of one. */
    public static function craftRequired($mode){
        $mode = (int)$mode;
        $table = self::CRAFT_REQUIRED;
        return isset($table[$mode]) ? $table[$mode] : $table[self::MODE_THREE];
    }

    /* May this craft contribute to a group? WARNING: FULL HEALTH, not "not destroyed" - see the class
       comment. A craft carrying so much as one point of damage is out. */
    public static function isCraftUndamaged($craft){
        if (!$craft) return false;
        if ($craft->isDestroyed()) return false;
        return $craft->getRemainingHealth() >= $craft->maxhealth;
    }

    /* ---------------------------------------------------------------- damage */

    protected function getProfile($mode){
        $mode = (int)$mode;
        return isset($this->damageProfile[$mode])
             ? $this->damageProfile[$mode]
             : $this->damageProfile[self::MODE_THREE];
    }

    /* WARNING: READS THE ORDER'S MODE, NOT THE WEAPON'S. Firing::fireWeapons does NOT call
       changeFiringMode before fire() - only prepareFiring does, once per order - so by the time the
       dice are rolled $this->firingMode is whatever the LAST hit-chance pass left behind. One order
       per mount makes that harmless today; reading the order makes it harmless for good. */
    public function getDamage($fireOrder){
        $row = $this->getProfile($fireOrder ? $fireOrder->firingMode : $this->firingMode);
        return Dice::d(10, $row['dice']) + $row['add'];
    }

    /* WARNING: these two are called by Weapon::setSystemDataWindow inside a loop that walks
       $firingModes and calls changeFiringMode() before each pair, filling
       minDamageArray/maxDamageArray. So $this->firingMode IS the mode being described here - the one
       place in this class where reading it rather than an order is correct. */
    public function setMinDamage(){
        $row = $this->getProfile($this->firingMode);
        $this->minDamage = $row['dice'] + $row['add'];
    }

    public function setMaxDamage(){
        $row = $this->getProfile($this->firingMode);
        $this->maxDamage = ($row['dice'] * 10) + $row['add'];
    }

    /* ---------------------------------------------------------------- the flight's volley */

    /* Is this mount able to shoot at all right now? The same test both intercept gates in firing.php
       make, reused here because nothing on the server stops an UNLOADED weapon from resolving an
       offensive order - the client's isLoaded is the only gate on that path (Stage 8's finding), and
       a 4-turn reload that only the client enforced would not be a reload. */
    protected function isReadyToFire(){
        return $this->getTurnsloaded() >= $this->getLoadingTime();
    }

    /* Sort key that puts the EARLIEST-DECLARED order first. Fire order ids are the tac_fireorder
       auto-increment, so a smaller id was inserted first; a server-made order carries -1 and has no
       identity at all, and anything non-numeric sorts last rather than first. */
    protected static function orderSortKey($order){
        return is_numeric($order->id) ? (int)$order->id : PHP_INT_MAX;
    }

    /* Fully nullified: no log line, no animation, no missed-shot display. The exact quadruple
       HyperplasmaMatrix uses, and calculateHitBase / fire below short-circuit on it. */
    protected static function nullifyOrder($order){
        $order->shots    = 0;
        $order->shotshit = 0;
        $order->needed   = 0;
        $order->rolled   = 100;
        $order->pubnotes = "";
        $order->updated  = true;
    }

    /* Technical: the shot is not fired, but it stays in the log saying WHY - a wasted order on a
       4-turn weapon is worth a line. NeutronBlaster's shape, and public because the Light Chromatic
       Pulsar's own calculateHitBase uses it when the exclusivity contest goes the other way. */
    public static function markTechnical($weapon, $order, $reason){
        $order->chosenLocation = 0;
        $order->needed         = 0;
        $order->shots          = 0;
        $order->notes          = "technical fire order - " . $reason;
        $order->updated        = true;
        $weapon->doNotIntercept = true;
    }

    /* THE WHOLE FLIGHT'S VOLLEY, decided from scratch and identically by every array in the flight.
     * Returns orderId => ROLE_PRIMARY | ROLE_SUBORDINATE | refusal string, covering every array order
     * in the flight - this mount then applies only its own rows.
     *
     * Every instance recomputing the same plan is the HyperplasmaMatrix pattern, and it is what makes
     * the result independent of the order the hook happens to run in. */
    protected function planFlightVolley($flight, $gamedata){
        $plan        = array();
        $arrayOrders = array();  //every array order in the flight, with its mount and craft
        $otherKind   = array();  //the flight's OTHER exclusivity-group orders (the pulsars)
        $arrayFirst  = null;
        $otherFirst  = null;

        foreach ($flight->systems as $craft){
            if (!is_object($craft) || empty($craft->systems)) continue;
            $undamaged = self::isCraftUndamaged($craft);

            foreach ($craft->systems as $sys){
                if (empty($sys->flightExclusiveGroup)) continue;
                if ($sys->flightExclusiveGroup !== $this->flightExclusiveGroup) continue;
                $isArray = ($sys instanceof MedLightningArrayFtr);

                foreach ($sys->fireOrders as $order){
                    if ($order->type != 'normal' || $order->turn != $gamedata->turn) continue;
                    $key = self::orderSortKey($order);

                    if ($isArray){
                        if ($arrayFirst === null || $key < $arrayFirst) $arrayFirst = $key;
                        $arrayOrders[] = array(
                            'order' => $order, 'weapon' => $sys, 'craft' => $craft,
                            'key' => $key, 'undamaged' => $undamaged,
                        );
                    } else {
                        if ($otherFirst === null || $key < $otherFirst) $otherFirst = $key;
                        $otherKind[] = array('order' => $order, 'weapon' => $sys);
                    }
                }
            }
        }

        if (empty($arrayOrders)) return $plan;

        /* D16, THE EXCLUSIVITY CONTEST. The kind declared LATER loses the whole turn. The client
           refuses the second kind before the click lands, so this is only ever reached by a stale or
           hand-edited POST - but it has to answer deterministically when it is, and "whichever was
           declared first" is the least surprising answer to give a player. */
        $arrayLoses = false;
        if ($otherFirst !== null && $arrayFirst !== null){
            if ($otherFirst < $arrayFirst){
                $arrayLoses = true;
            } else {
                foreach ($otherKind as $entry){
                    if (method_exists($entry['weapon'], 'cancelForFlightExclusion')){
                        $entry['weapon']->cancelForFlightExclusion(
                            $entry['order'],
                            "flight is firing its Medium Lightning Arrays this turn"
                        );
                    }
                }
            }
        }

        //Deterministic: lowest order id first, ties broken by mount id, so the plan cannot depend on
        //the order gamedata happened to hand back the craft.
        usort($arrayOrders, function($a, $b){
            if ($a['key'] !== $b['key']) return ($a['key'] < $b['key']) ? -1 : 1;
            if ($a['weapon']->id == $b['weapon']->id) return 0;
            return ($a['weapon']->id < $b['weapon']->id) ? -1 : 1;
        });

        //Bucket the eligible orders by what they are actually shooting at, in what mode. Everything
        //refused for a reason of its own never reaches a bucket.
        $buckets = array();
        foreach ($arrayOrders as $entry){
            $order = $entry['order'];

            if ($arrayLoses){
                $plan[$order->id] = "flight is firing its Light Chromatic Pulsars this turn";
                continue;
            }
            if (!$entry['undamaged']){
                $plan[$order->id] = "a damaged probe cannot contribute to a combined array";
                continue;
            }
            if (!$entry['weapon']->isReadyToFire()){
                $plan[$order->id] = "array not charged";
                continue;
            }

            $bucketKey = $order->targetid . '|' . $order->calledid . '|' . (int)$order->firingMode;
            if (!isset($buckets[$bucketKey])) $buckets[$bucketKey] = array();
            $buckets[$bucketKey][] = $entry;
        }

        foreach ($buckets as $bucket){
            $mode   = (int)$bucket[0]['order']->firingMode;
            $label  = isset($this->firingModes[$mode]) ? $this->firingModes[$mode] : "combined array";
            $needed = self::craftRequired($mode);
            $count  = count($bucket);
            $full   = (int)floor($count / $needed) * $needed;

            for ($i = 0; $i < $count; $i++){
                $order = $bucket[$i]['order'];
                if ($i >= $full){
                    $plan[$order->id] = "only " . ($count - $full) . " of the " . $needed
                                      . " probes a " . $label . " needs declared this shot";
                    continue;
                }
                //One primary per complete group; the rest of the group is silent.
                $plan[$order->id] = (($i % $needed) === 0) ? self::ROLE_PRIMARY : self::ROLE_SUBORDINATE;
            }
        }

        return $plan;
    }

    public function beforeFiringOrderResolution($gamedata){
        $this->orderRoles = array();

        $flight = $this->getUnit();
        /* WARNING: A FLIGHT'S SYSTEMS ARE CRAFT and this weapon lives one level further down, so
           there is no meaningful volley without the flight. A Mapmaker array mounted on a hull would
           simply never combine - which is the safe failure, and no hull mounts one. */
        if (!($flight instanceof FighterFlight)) return;

        $plan = $this->planFlightVolley($flight, $gamedata);

        foreach ($this->fireOrders as $order){
            if ($order->type != 'normal' || $order->turn != $gamedata->turn) continue;

            $role = isset($plan[$order->id]) ? $plan[$order->id] : "order not resolved";
            $this->orderRoles[$order->id] = $role;

            if ($role === self::ROLE_PRIMARY){
                $order->shots = 1; //one shot carrying the whole group; the profile comes from the mode
                continue;
            }
            if ($role === self::ROLE_SUBORDINATE){
                self::nullifyOrder($order);
                $this->doNotIntercept = true;
                continue;
            }
            self::markTechnical($this, $order, $role);
        }
    }

    /* Was this order fully nullified as a subordinate? The exact quadruple nullifyOrder writes -
       matched rather than re-read from $orderRoles so a second pass cannot resurrect a shot the first
       pass silenced. */
    protected static function isNullified($fireOrder){
        return $fireOrder->shots == 0 && $fireOrder->shotshit == 0
            && $fireOrder->needed == 0 && $fireOrder->rolled == 100;
    }

    public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder){
        if (self::isNullified($fireOrder)){
            $fireOrder->pubnotes  = "";
            $fireOrder->updated   = true;
            $this->doNotIntercept = true;
            return;
        }
        //A technical order has already had its reason written and its shot taken away; letting the
        //parent run would compute a fresh ->needed and un-refuse it.
        if (isset($this->orderRoles[$fireOrder->id])
            && $this->orderRoles[$fireOrder->id] !== self::ROLE_PRIMARY){
            return;
        }

        parent::calculateHitBase($gamedata, $fireOrder);

        if ($fireOrder->needed <= 0) return; //the parent's auto-miss marker; do not annotate it

        $needed = self::craftRequired($fireOrder->firingMode);
        $row    = $this->getProfile($fireOrder->firingMode);
        $fireOrder->pubnotes .= " [" . $needed . " probes combined, "
                             . $row['dice'] . "d10+" . $row['add'] . "]";
    }

    public function fire($gamedata, $fireOrder){
        if (self::isNullified($fireOrder)) return;
        parent::fire($gamedata, $fireOrder);
    }

    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        } else {
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= "Fires at Flight level: 3 or 6 probes fire their arrays"
                               . " into one bolt, a lone array cannot fire at all.";
        foreach ($this->firingModes as $mode => $label){
            $row = $this->getProfile($mode);
            $fc  = isset($this->fireControlArray[$mode]) ? $this->fireControlArray[$mode] : $this->fireControl;
            $rp  = isset($this->rangePenaltyArray[$mode]) ? $this->rangePenaltyArray[$mode] : $this->rangePenalty;
            $this->data["Special"] .= "<br> - " . $label . ": " . self::craftRequired($mode)
                                   . " probes, " . $row['dice'] . "d10+" . $row['add']
                                   . " (FC " . implode("/", $fc) . ")"
                                   . " (range -" . number_format($rp * 5, 2) . "/hex)";
        }
        $this->data["Special"] .= "<br>Every contributing probe must declare at the SAME target in the"
                               . " SAME mode.";
        $this->data["Special"] .= "<br>A probe that has taken ANY damage cannot contribute.";
        $this->data["Special"] .= "<br>The flight cannot fire its Medium Lightning Arrays and its"
                               . " Light Chromatic Pulsars in the same turn.";
        $this->data["Special"] .= "<br>Locks on with the flight's own EW instead of its offensive"
                               . " bonus.";
    }

}//endof class MedLightningArrayFtr

?>
