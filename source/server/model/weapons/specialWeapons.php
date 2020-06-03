<?php
/*
********************************
*all EM weapons should look for EMHardened trait, and treat is as they would AdvancedArmor.
* this is rough simplification of how this trait should affect them (see Ipsha for details, Militaries of the League 2)
********************************
*/

class WeaponEM  {	
	public static function isTargetEMResistant($ship,$system = null){ //returns true if target has either AdvancedArmor or EM Hardening (which, for simplicity, in FV is treated as AA would be for EM weapons)
		if($ship){
			if($ship->advancedArmor) return true;
			if($ship->EMHardened) return true;
		}else if ($system){
			if($system->advancedArmor) return true;
		}
		return false;
	}
}


class PlasmaStream extends Raking{
	public $name = "plasmaStream";
	public $displayName = "Plasma Stream";
	public $animation = "beam";
	public $animationColor = array(75, 250, 90);
	public $trailColor = array(75, 250, 90);
	public $projectilespeed = 20;
	public $animationWidth = 3;
	public $animationExplosionScale = 0.25;
	public $trailLength = 400;
	public $priority = 1;
		        
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
		
		
	public function getDamage($fireOrder){        return Dice::d(10,3)+4;   }
	public function setMinDamage(){     $this->minDamage = 7 ;/*- $this->dp;*/      }
	public function setMaxDamage(){     $this->maxDamage = 34 /*- $this->dp*/;      }
}//endof class PlasmaStream




class ShockCannon extends Weapon{
        public $name = "shockCannon";
        public $displayName = "Shock Cannon";
        public $animation = "laser";
        public $animationColor = array(175, 225, 175);
        public $trailColor = array(175, 225, 175);
        public $projectilespeed = 15;
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        public $animationExplosionScale = 0.15;
        public $trailLength = 30;
        public $priority = 3; //dropout effect on fighters

        public $loadingtime = 2;

        public $rangePenalty = 1;
        public $fireControl = array(3, 3, 3); // fighters, <=mediums, <=capitals

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
		      $this->data["Special"] .= "Ignores armor. Forces dropout on fighters.";  
		      $this->data["Special"] .= "<br>Structure hits reduce power output by 1 per 4 dmg rolled (but do no actual damage).";  
        }

        //ignore armor; advanced armor halves effect (due to this weapon being Electromagnetic)
        public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null){
			if (WeaponEM::isTargetEMResistant($ship,$system)){
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
					if($outputMod < 0){
						$crit = new OutputReduced(-1, $ship->id, $reactor->id, "OutputReduced", $gamedata->turn, $outputMod);
						$crit->updated = true;
						$reactor->criticals[] =  $crit;
					}
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
					$system->criticals[] =  $crit;
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
	public $animation = "laser";
	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	public $noOverkill = true;
		        
	public $loadingtime = 1;
	public $priority = 10; //as antiship weapon, going last
	public $priorityAFArray = array(1=>3); //as antifighter weapon, going early
			
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
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
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

        public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);

        public $animation = "trail";
        public $trailLength = 2;
        public $animationWidth = 4;
        public $projectilespeed = 17;
        public $animationExplosionScale = 0.05;
        public $rof = 5;
        public $grouping = 25;
        public $maxpulses = 6;
		        
	public $loadingtime = 2;
        public $priority = 9;
        
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
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}else if ($system instanceof Structure){
				$reactor = $ship->getSystemByName("Reactor");
				$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn);
				$crit->updated = true;
				$reactor->criticals[] =  $crit;
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

	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 12;
	public $animationWidth = 3;
	public $animationWidth2 = 0.4;
	public $animationExplosionScale = 0.20;
	public $trailLength = 40;

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
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}
		}
		else if ($system instanceof Structure){
			$reactor = $ship->getSystemByName("Reactor");
			$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced2", $gamedata->turn);
			$crit->updated = true;
			$reactor->criticals[] =  $crit;
		}
		else if ($system->powerReq > 0 || $system->canOffLine ){
			$crit = new ForcedOfflineForTurns (-1, $ship->id, $system->id, "ForcedOfflineForTurns", $gamedata->turn, $gamedata->turn+2);
			$crit->updated = true;
			$system->criticals[] = $crit;
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

	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 10;
	public $animationWidth = 4;
	public $animationWidth2 = 0.5;
	public $animationExplosionScale = 0.30;
	public $trailLength = 50;

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
				$system->criticals[] =  $crit;
				$fireOrder->pubnotes .= " DROPOUT! ";
			}
		}
		else if ($system instanceof Structure){
			$reactor = $ship->getSystemByName("Reactor");
			$crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced4", $gamedata->turn);
			$crit->updated = true;
			$reactor->criticals[] =  $crit;
		}
		else if ($system->powerReq > 0 || $system->canOffLine ){
			$crit = new ForcedOfflineForTurns (-1, $ship->id, $system->id, "ForcedOfflineForTurns", $gamedata->turn, $gamedata->turn+3);
			$crit->updated = true;
			$system->criticals[] = $crit;
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
	public $animation = "laser";
	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
	public $priority = 1;

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
			$system->criticals[] =  $crit;
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
	public $animation = "laser";
	public $animationColor = array(158, 240, 255);
	public $trailColor = array(158, 240, 255);
	public $projectilespeed = 15;
	public $animationWidth = 2;
	public $animationWidth2 = 0.2;
	public $animationExplosionScale = 0.10;
	public $trailLength = 30;
		        
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
		$this->data["Special"] = 'Forces dropout on fighters (except superheavy), turns off powered systems. ';
	}
		
	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
		$crit = null;
		if (WeaponEM::isTargetEMResistant($ship,$system)) return;
		if ($system instanceof Fighter && !($ship->superheavy)){
			$crit = new DisengagedFighter(-1, $ship->id, $system->id, "DisengagedFighter", $gamedata->turn);
			$crit->updated = true;
					$crit->inEffect = true;
			$system->criticals[] =  $crit;
			$fireOrder->pubnotes .= " DROPOUT! ";
				}else if ($system->powerReq > 0 || $system->canOffLine ){
			$system->addCritical($ship->id, "ForcedOfflineOneTurn", $gamedata);
		}
	}
	
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
	
    public $priority = 10; //let's fire last, order not all that important here!
    public $loadingtime = 3;
    public $rangePenalty = 0.5; //-1/2 hexes
    public $intercept = 0;
    public $fireControl = array(-1, 2, 3);
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
   
	   
	//let's animate this as a very wide beam...
	public $animation = "laser";
        public $animationColor = array(150, 150, 220);
        public $animationColor2 = array(170, 170, 250);
        public $animationExplosionScale = 0.45;
        public $animationWidth = 15;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    
	      $this->data["Special"] .= "Does no damage, but weakens target's Initiative (-1d6) and Sensors (-1d6) rating next turn";  
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
        public $animationColor2 = array(160, 160, 240);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
	      $this->data["Special"] = "Does no damage, but weakens target's Initiative (-1d6) rating next turn";  
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
			        	$CnC->criticals[] =  $crit;
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
        public $animationColor2 = array(160, 160, 240);
        public $animationExplosionScale = 0.25;
        public $animationWidth = 10;
        public $animationWidth2 = 0.5;
	
 	public $possibleCriticals = array( //no point in damage reduced crit
            14=>"ReducedRange"
	);
	
    public function setSystemDataWindow($turn){
	      parent::setSystemDataWindow($turn);    
	      $this->data["Special"] = "Does no damage, but weakens target's Sensors (-1d3) rating next turn";  
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
			        	$CnC->criticals[] =  $crit;
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
} //end of class SensorSpike




class EmBolter extends Weapon{
    /*EM Bolter - Ipsha weapon*/
        public $name = "EmBolter";
        public $displayName = "EM Bolter";
	public $iconPath = "EMBolter.png";
	
        public $animation = "trail";
        public $animationColor = array(100, 100, 250);
        public $projectilespeed = 14;
        public $animationWidth = 5;
        public $animationExplosionScale = 0.45;
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
		/*replaced by ForTurns crit above
		for($i = 1; $i<=$this->cooldown;$i++){		
			$trgtTurn = $gamedata->turn+$i-1;//start on current turn rather than next!
			$crit = new ForcedOfflineOneTurn(-1, $fireOrder->shooterid, $this->id, "ForcedOfflineOneTurn", $trgtTurn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}	
		*/
	} //endof function fire
	
	
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 10;
            }
            if ( $powerReq == 0 ){
                $powerReq = 9;
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
		usort(SparkFieldHandler::$sparkFields, "self::sortByBoost");	
	
		//table of units that are already targeted
		$alreadyTargeted = array();
		//create firing order for each weapon (target self)
		//for each weapon find possible targets and add them to weapons' target list
		//strongest weapons fire first, and only 1 field affects particular ship	
		foreach(SparkFieldHandler::$sparkFields as $field){			
			if ($field->isDestroyed($gamedata->turn-1)) continue; //destroyed field does not attack
			if ($field->isOfflineOnTurn($gamedata->turn)) continue; //disabled field does not attack
			$shooter = $field->getUnit();      
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
	public $trailColor = array(141, 240, 255);
        public $animation = "ball";
        public $animationColor = array(1, 1, 255);
        public $animationExplosionScale = 2;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(165, 165, 255);
        public $projectilespeed = 20;
        public $animationWidth = 1;
        public $trailLength = 1;
	
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
    	public $firingModes = array( 1 => "Field"); //just a convenient name for firing mode
	public $hextarget = true;
	
	protected $targetList = array(); //weapon will hit units on this list rather than target from firing order; filled by SparkFieldHandler!
	
	
 	public $possibleCriticals = array( //no point in range reduced crit; but reduced damage is really nasty for this weapon!
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
        	$notes = "this weapon simply causes damage, hit is automatic"; //replace usual note
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
} //endof class SparkField 







class SurgeCannon extends Raking{
    /*Surge Cannon - Ipsha weapon*/
	public $name = "SurgeCannon";
	public $displayName = "Surge Cannon";
	public $iconPath = "SurgeCannon.png";
	
	public $animation = "laser";
	public $animationColor = array(165, 165, 255);
	public $animationWidth = 2;
	public $animationWidthArray = array(1=>2, 2=>3, 3=>4, 4=>5, 5=>6);
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.1, 2=>0.2, 3=>0.3, 4=>0.5, 5=>0.6);

      
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
	public $animationWidth = 2;
	public $animationWidthArray = array(1=>2, 2=>3);
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.1, 2=>0.2);
      
	public $loadingtime = 1;
	public $intercept = 1; //intercept rating -1
        
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
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "+2 per rake to critical/dropout rolls on system(s) hit this turn.";  //original rule is more fancy
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
	public $animation = "trail";
	public $animationColor =  array(145, 145, 245);
	public $animationExplosionScale = 0.10;
	public $projectilespeed = 10;
	public $animationWidth = 2;
	public $trailLength = 10;
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
	public $animation = "trail";
	public $animationWidth = 3;
	public $projectilespeed = 10;
	public $animationExplosionScale = 0.15;
	public $trailLength = 10;

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
		$this->data["Special"] .= "<br>-1 per hit to crit rolls, -2 on dropout rolls.";
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
	 
        
        public function getDamage($fireOrder){        return 9;   }
    }//endof class EmPulsar



class ResonanceGenerator extends Weapon{
    /*Resonance Generator - Ipsha weapon*/
	public $name = "ResonanceGenerator";
	public $displayName = "Resonance Generator";
	public $iconPath = "ResonanceGenerator.png";
	
	public $animation = "laser"; //described as beam in nature, standard damage is resonance effect and not direct
	public $animationColor = array(125, 125, 230);
	public $animationWidth = 10;
	public $animationWidth2 = 0.4;
	public $animationExplosionScaleArray = array(1=>0.25);
      
	public $loadingtime = 1;
	
	public $rangePenalty = 1; //-1/hex
	public $fireControl = array(null, 2, 2); // fighters, <mediums, <capitals 
	
	public $intercept = 0;
	public $priority = 1;// as it attacks every section, should go first!
	
	public $noPrimaryHits = true; //outer section hit will NOT be able to roll PRIMARY result!
	
	private $cooldown = 2;
	
	public $damageType = "Standard"; //(first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $weaponClass = "Electromagnetic"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!

	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
	{
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
		if ( $maxhealth == 0 ){
			$maxhealth = 8;
		}
		if ( $powerReq == 0 ){
			$powerReq = 6;
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
	
	public $animation = "trail";
	public $animationColor =  array(165, 165, 255);
	public $projectilespeed = 14;
	public $animationWidth = 4;
	public $animationExplosionScale = 0.4;
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
	
	//animation irrelevant really (range 0), but needs to be fast!
	public $animation = "trail";
	public $animationColor =  array(1, 1, 1);
	public $projectilespeed = 24;
	public $animationWidth = 1;
	public $animationExplosionScale = 0.4;
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
	private $designedToRam = false;
	private $selfDestroy = 0; //will successful attack destroy the ramming ship? Additional damage modifier
	private $designDamage = 0;
	private $damageModRolled = 0;

	private $gamedata = null; //gamedata is needed in places normally unavailable - this variable will be filled before any calculations happen!
	
	 public $possibleCriticals = array(); //shouldn't be hit ever, but if it is, should not suffer any criticals
	
	
	//fill gamedata variable, which might otherwise be left out!
	public function beforeFiringOrderResolution($gamedata){
		$this->gamedata = $gamedata;
	}
	
	public function setSystemDataWindow($turn){
		$this->setMinDamage(); //just in case it's not set correctly in the beginning!
			$this->setMaxDamage();
		  parent::setSystemDataWindow($turn);  
		  $this->data["Special"] = "Ramming attack - if succcessful, ramming unit itself will take damage too (determined by targets' ramming factor).";  
		  if($this->designedToRam) {
			  $this->data["Special"] .= "<br>This unit is specifically designed for ramming and may do so in any scenario.";
		  }else{
			  $this->data["Special"] .= "<br>ALLOWED ONLY IN SPECIAL CIRCUMSTANCES, LIKE HOMEWORLD DEFENSE!";
		  }
		  $this->data["Special"] .= "<br>Profiles and EW do not matter for hit chance - but unit size and target speed does.";  
		  $this->data["Special"] .= "<br>	(it's generally easier to ram slow targets and targets larger than ramming units itself)";  
		  $this->data["Special"] .= "<br>	Hunter-Killers have speed penalty as well.";  
		  $this->data["Special"] .= "<br>Ramming damage is also influenced by conditions - moving head on with initiative slightly increases chance of high damage.";
		  $this->data["Special"] .= "<br>Ramming attacks will be done in ship firing phase (even attacks by fighters) and cannot be intercepted.";
	}	
	
	
	public function fire($gamedata, $fireOrder){
		// If hit, firing unit itself suffers damage, too (based on raming factor of target)!
		$this->gamedata = $gamedata;
		parent::fire($gamedata, $fireOrder);
		if($fireOrder->shotshit > 0){
			$pos = null;
			$shooter = $gamedata->getShipById($fireOrder->targetid);
			$target = $this->unit;
			$fireOrder->chosenLocation = 0;//to be redetermined!
			$damage = $this->getReturnDamage($fireOrder);
        		$damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
        		$damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);
			if($target instanceof FighterFlight){ //allocate exactly to firing fighter!
				$ftr = $target->getFighterBySystem($this->id);
				if ($ftr->isDestroyed()) return; //do not allocate to already destroyed fighter!!! it would cause the game to randomly choose another one, which would be incorrect
				$fireOrder->calledid = $ftr->id;
			}
			$this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
			$fireOrder->calledid = -1; //just in case!
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
	

	
	private function getRammingFactor(){
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
		//modifier: +1 if greater Ini than target, +1 if head on, +1 if target is head on also
		$modifier = 0;
		$shooter = $this->unit;
		$gd = $this->gamedata;
		$target = $gd->getShipById($fireOrder->targetid);
		if ($shooter->iniative > $target->iniative) $modifier++;
		$bearing = abs($shooter->getBearingOnUnit($target));
		if ($bearing < 10) $modifier++;//should be 0, but at rage 0 there may be a few degrees off...
		$bearing = abs($target->getBearingOnUnit($shooter));
		if ($bearing < 10) $modifier++;//should be 0, but at rage 0 there may be a few degrees off...
		
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
	}//endof function getDamage
        public function getReturnDamage($fireOrder){    //damage that ramming unit suffers itself - using same modifier as actual attack! (already set)   
		$gd = $this->gamedata;
		$target = $gd->getShipById($fireOrder->targetid);
		$shooter = $this->unit;
		$rfactor =  $target->getRammingFactor();
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
	public $animation = "trail";
	public $animationColor =  array(145, 145, 245);
	public $animationExplosionScale = 0.10;
	public $projectilespeed = 10;
	public $animationWidth = 2;
	public $trailLength = 10;
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
	
	public $animation = "beam";//behaves like a bolt, I think beam animation is fitting
	public $animationColor = array(150, 10, 10); //make it deep red...
	public $animationExplosionScale = 0.3;
	public $projectilespeed = 15;
	public $animationWidth = 8;
	public $trailLength = 20;
  
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
	}
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "Doesn't actually deal damage except as noted below. Automatically hits shields if interposed.";      
		$this->data["Special"] .= "<br>Effect depends on system hit:";    
		$this->data["Special"] .= "<br> - Structure: 10 boxes marked destroyed (regardless of armor)."; 
		$this->data["Special"] .= "<br> - Shield: system destroyed."; 
		$this->data["Special"] .= "<br>  -- Gravitic Shield reduces generator output by 1, too."; 
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
			$system->criticals[] =  $crit;
		} else if($system instanceOf Engine) { //Engine: output reduced by 2.
			$crit = new OutputReduced2(-1, $ship->id, $system->id, "OutputReduced2", $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = false;
			$system->criticals[] =  $crit;
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
	
	public $trailColor = array(30, 170, 255);
	public $animation = "ball";
	public $animationColor = array(30, 170, 255);
	public $animationExplosionScale = 2; //covers 2 hexes away from explosion center
	public $animationExplosionType = "AoE";
	public $explosionColor = array(30, 170, 255);
	public $projectilespeed = 12;
	public $animationWidth = 14;
	public $trailLength = 10;
	    
	public $firingModes = array(
		1 => "IonStorm"
	);
		
	private static $alreadyAffected = array(); //list of IDs of units already affected in this firing phase - to avoid multiplying effects on overlap
	
		
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		//some effects should originally work for current turn, but it won't work with FV handling of ballistics. Moving everything to next turn.
		//it's Ion (not EM) weapon with no special remarks regarding advanced races and system - so works normally on AdvArmor/Ancients etc
		$this->data["Special"] = "Every unit in affected area is subject to effects:";      
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
			$system->criticals[] =  $crit;
		}
		if($ship instanceOf FighterFlight){ //effects on fighters - applying to first fighter (already found), will affect entire flight
			$crit = new tmpsensordown(-1, $ship->id, $system->id, 'tmpsensordown', $gamedata->turn);  //-1 OB
			$crit->updated = true;
			$system->criticals[] =  $crit;
			for($i=1; $i<=3;$i++){ //-3 Initiative
				$crit = new tmpinidown(-1, $ship->id, $system->id, 'tmpinidown', $gamedata->turn);  
				$crit->updated = true;
				$system->criticals[] =  $crit;
			}
		}else{ //effects on ships
			$CnC = $ship->getSystemByName("CnC"); //temporary effects are applied to C&C 
			if($CnC){
				for($i=1; $i<=2;$i++){ //-2 Sensor rating
					$crit = new tmpsensordown(-1, $ship->id, $CnC->id, 'tmpsensordown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
				for($i=1; $i<=3;$i++){ //-3 Initiative
					$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
				}
				$powerLoss = min(2,$ship->shipSizeClass); //1 for LCVs and smaller, 2 for larger ships
				for($i=1; $i<=$powerLoss;$i++){ //-3 Initiative
					$crit = new tmppowerdown(-1, $ship->id, $CnC->id, 'tmppowerdown', $gamedata->turn); 
					$crit->updated = true;
			        	$CnC->criticals[] =  $crit;
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
	
	public $range = 23;//no point firing at further target with base 24 to hit!
	public $loadingtime = 3;
    public $rangePenalty = 1;//-1/hex
	
	public $trailColor = array(245, 90, 90);
	public $animation = "ball";
	public $animationColor = array(245, 90, 90);
	public $animationExplosionScale = 0.5; //single hex explosion
	public $animationExplosionType = "AoE";
	public $explosionColor = array(255, 0, 0);
	public $projectilespeed = 10;
	public $animationWidth = 14;
	public $trailLength = 10;
	    
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



?>
