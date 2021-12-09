<?php

class Jammer extends ShipSystem implements SpecialAbility{    
    public $name = "jammer";
    public $displayName = "Jammer";
    public $specialAbilities = array("Jammer");
    public $primary = true;
	
	//Jammer is very important, being the primary defensive system!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    public $possibleCriticals = array(16=>"PartialBurnout", 23=>"SevereBurnout");
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    //args for Jammer ability are array("shooter", "target")
    public function getSpecialAbilityValue($args)
    {
        if (!isset($args["shooter"]) || !isset($args["target"]))
            throw new InvalidArgumentException("Missing arguments for Jammer getSpecialAbilityValue");
        
        $shooter = $args["shooter"];
        $target = $args["target"];
        
        if ($shooter->faction === $target->faction) return 0; //same-faction units ignore Jammer
		
        if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip)) 
            throw new InvalidArgumentException("Wrong argument type for Jammer getSpecialAbilityValue");
        		
		$jammerValue = $this->getOutput();
		
		if ($jammerValue > 0){ //else no point
			//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
			if ($shooter->hasSpecialAbility("AdvancedSensors")) {
				$jammerValue = 0; //negated
			} else if ($shooter->hasSpecialAbility("ImprovedSensors")) {
				$jammerValue = $jammerValue * 0.5; //halved
			}
		} else {
			$jammerValue = 0; //never negative
		}
			
        return $jammerValue;
    }

    public function setSystemDataWindow($turn){
        $this->data["Special"] = "Denies a hostile OEW-lock versus this ship.";
        $this->data["Special"] .= "<br>Doesn't work ws own faction (eg. Minbari Jammer won't work against hostile Minbari).";
		$this->data["Special"] .= "<br>Enabling/Disabling Jammer will affect enemy missile launches on NEXT turn!";	     
    }
} //endof Jammer

class Stealth extends ShipSystem implements SpecialAbility{    
    public $name = "stealth";
    public $displayName = "Stealth systems";
    public $specialAbilities = array("Jammer");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
            $this->data["Special"] = "Jammer ability if targeted from over 5 hexes away.";
        }
    
    //args for Jammer ability are array("shooter", "target")
    public function getSpecialAbilityValue($args)
    {
        //Debug::log("calling stealth getSpecialAbilityValue");
        if (!isset($args["shooter"]) || !isset($args["target"]))
            throw new InvalidArgumentException("Missing arguments for Stealth getSpecialAbilityValue");
        
        $shooter = $args["shooter"];
        $target = $args["target"];
        
        if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip))
            throw new InvalidArgumentException("Wrong argument type for Stealth getSpecialAbilityValue");
		
        $jammerValue = 0; 
        if (mathlib::getDistanceHex($shooter, $target) > 5) //kicks in!
        {
			$jammerValue = 1; 
			//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
			if ($shooter->hasSpecialAbility("AdvancedSensors")) {
				$jammerValue = 0; //negated
			} else if ($shooter->hasSpecialAbility("ImprovedSensors")) {
				$jammerValue = $jammerValue * 0.5; //halved
			}
        }
        return $jammerValue;        
    }
} //endof Stealth

class Fighterimprsensors extends ShipSystem implements SpecialAbility{    
    public $name = "fighterimprsensors";
    public $displayName = "Improved Sensors";
    public $iconPath = "scannerTechnical.png";
    public $specialAbilities = array("ImprovedSensors");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
            $this->data["Special"] = "Halves effectiveness of enemy Jammer (not that of advanced races)."; //not that of advanced races
        }
    
    public function getSpecialAbilityValue($args)
    {     
        return 1; //Improved Sensors just are       
    }     
} //endof Improved Sensors

class Fighteradvsensors extends ShipSystem implements SpecialAbility{    
    public $name = "Fighteradvsensors";
    public $displayName = "Advanced Sensors";
    public $iconPath = "scannerTechnical.png";
    public $specialAbilities = array("AdvancedSensors");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
		$this->data["Special"] = "Ignores enemy Jammer."; //not that of advanced races
		//$this->data["Special"] .= "<br>Ignores enemy BDEW and SDEW."; //not that of advanced races (skipped as fighters ignore it anyway
		$this->data["Special"] .= "<br>Ignores any defensive systems lowering enemy profile (shields, EWeb...)."; //not that of advanced races
		$this->data["Special"] .= "<br>All of the above work as usual if operated by advanced races."; 
	}
    
    public function getSpecialAbilityValue($args)
    {     
        return 1; //Improved Sensors just are       
    }     
} //endof Advanced Sensors

interface SpecialAbility{
    public function getSpecialAbilityValue($args);
}

interface DefensiveSystem{
    public function getDefensiveType();
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon);
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon);
}

class Shield extends ShipSystem implements DefensiveSystem{
    public $name = "shield";
    public $displayName = "Shield";
    public $startArc = 0;
    public $endArc = 0;
    
    //defensive system
    public $defensiveSystem = true;
    public $tohitPenalty = 0;
    public $damagePenalty = 0;
    public $rangePenalty = 0;
    public $range = 5;
	
	public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
    
    public $possibleCriticals = array(
            16=>"OutputReduced1",
            20=>"DamageReductionRemoved",
            25=>array("OutputReduced1", "DamageReductionRemoved"));

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor);
        
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
    }
    
    public function setSystemDataWindow($turn){
		$damageReduction = $this->output;
		$profileReduction = $this->output *5;
		$this->data["Special"] = "Reduces damage done by incoming shots by $damageReduction."; 
		$this->data["Special"] .= "<br>Reduces hit chance of incoming shots by $profileReduction."; //not that of advanced races
		$this->data["Special"] .= "<br>Typical ship shields are ignored by fighter direct fire at range 0 (fighters flying under shields)."; 
	}
	
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = $this->getOutput();
		$this->damagePenalty = $this->getOutput();
    }
    
    private function checkIsFighterUnderShield($target, $shooter, $weapon){
	if(!($shooter instanceof FighterFlight)) return false; //only fighters may fly under shield!
	if($weapon && $weapon->ballistic) return false; //fighter missiles may NOT fly under shield
        $dis = mathlib::getDistanceOfShipInHex($target, $shooter);
        if ( $dis == 0 ){ //If shooter are fighers and range is 0, they are under the shield
            return true;
        }
        return false;
    }
    
    public function getDefensiveType()
    {
        return "Shield";
    }
    
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon))
            return 0;

        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
    }
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn())
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon))
            return 0;
        
        if ($this->hasCritical('DamageReductionRemoved'))
            return 0;
        
        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
    }
}

class EMShield extends Shield implements DefensiveSystem{
    public $name = "eMShield";
    public $displayName = "EM Shield";
    public $iconPath = "shield.png";
    public $canOffLine = true; //usually You don't want to, but Burst Beam can force it...

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
    }
}

class GraviticShield extends Shield implements DefensiveSystem{
    public $name = "graviticShield";
    public $displayName = "Gravitic Shield";
    public $iconPath = "shield.png";
    public $canOffLine = true;

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc);
    }
}

class ShieldGenerator extends ShipSystem{
	//Shield Generator repair priority is above average!
	public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    public $name = "shieldGenerator";
    public $displayName = "Shield Generator";
    public $primary = true;    
    public $boostable = true;

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->boostEfficiency = $powerReq;
    }    
}

class Reactor extends ShipSystem{
    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    public $fixedPower = false; //important for MagGrav reactors, but defined here!
    public $outputType = "power";
	
	//Reactor is very important, being the ship heart!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    public $boostable = true; //for reactor overload feature!
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;
    
    public $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        //27=>array("OutputReduced10", "ForcedOfflineOneTurn"));
		27=>array("OutputReduced10", "ContainmentBreach")
	);
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );        
    }

    
    public function addCritical($shipid, $phpclass, $gamedata) {
        if(strcmp($phpclass, "ForcedOfflineOneTurn") == 0){
            // This is the reactor. If it takes a ForcedOffLineForOneTurn,
            // propagate this crit to all systems that can be shut down.
            $ship = $gamedata->getShipById($shipid);
            if (!$ship instanceof StarBase){                
                foreach($ship->systems as $system){
                    if(($system->powerReq > 0) || $system instanceof Weapon){
                        $system->addCritical($shipid, $phpclass, $gamedata);
                    }
                }
            }
            else {
                foreach($ship->systems as $system){
                    if ($system->location == $this->location){
                        if(($system->powerReq > 0) || $system instanceof Weapon){
                            $system->addCritical($shipid, $phpclass, $gamedata);
                        }       
                    }
                }
            }
        }

        parent::addCritical($shipid, $phpclass, $gamedata);
    }
	

	//in case of containment breach - roll whether reactor explodes
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if ($this->isDestroyed()) return; //no point if Reactor is actually destroyed already
		if (!$this->hasCritical("ContainmentBreach")) return; //no Containment Breach, everything is fine
			
		$explodeRoll = Dice::d(100);
		$chance = $this->getTotalDamage();
			
		//try to make actual attack to show in log - use Ramming Attack system!	- even if there is no explosion			
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder=null;
		if($rammingSystem){ //actually exists! - it should on every ship!				
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				$chance, $explodeRoll, 1, 1, 0,
				0,0,'HalfPhase',10000
			);
			$newFireOrder->pubnotes = "Containment Breach - reactor explosion! Chance $chance %, roll $explodeRoll.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}
			
		if ($explodeRoll <= $chance) { //actual explosion
			//destroy self		
			$remaining = $this->getRemainingHealth();
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $remaining, 0, 0, -1, true, false, "", "ContainmentBreach");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
				$damageEntry->shooterid = $ship->id; //additional field
				$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
		}
    } //endof function criticalPhaseEffects
	
    public function isOverloading($turn){
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                return true;
            }
        }
        return false;
    }
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);     
        $this->data["Special"] = "Can be set to overload, self-destroying ship after Firing phase.";	     
    }
} //endof Reactor



class MagGravReactor extends Reactor{
/*Mag-Gravitic Reactor, as used by Ipsha (Militaries of the League 2);
	provides fixed power regardless of systems;
	techical implementation: count as Power minus power required by all systems enabled
*/	
	public $possibleCriticals = array( //different set of criticals than standard Reactor
		13=>"FieldFluctuations",
		17=>array("FieldFluctuations", "FieldFluctuations"),
		21=>array("FieldFluctuations", "FieldFluctuations", "FieldFluctuations"),
		29=>array("FieldFluctuations", "FieldFluctuations", "FieldFluctuations", "ForcedOfflineOneTurn")
	);
	
	function __construct($armour, $maxhealth, $powerReq, $output ){
		parent::__construct($armour, $maxhealth, $powerReq, $output );    
		$this->fixedPower = true;
	}
	
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] .= "<br>Mag-Gravitic Reactor: provides fixed total power, regardless of destroyed systems.";
	}	
	
}//endof MagGravReactor		


class MagGravReactorTechnical extends MagGravReactor{
/*Mag-Gravitic Reactor, but displayed in grey - as a technical system that cannot be damaged (Vorlons use it)
*/		
    public $iconPath = "reactorTechnical.png";
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] = "Mag-Gravitic Reactor: provides fixed total power, regardless of destroyed systems.";
		$this->data["Special"] .= "<br>This system is here for technical purposes only. Cannot be damaged in any way.";
	}		
}//endof MagGravReactor		

//warning: needs external code to function properly. Intended for starbases only.
/* let's disable it - all use changed to SubReactorUniversal!
class SubReactor extends ShipSystem{	
	//SubReactor is very important, though not as much as primary reactor itself!
	public $repairPriority = 8;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    public $name = "reactor";
    public $displayName = "Reactor";
    public $outputType = "power";
    public $primary = false;
    
    public $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        27=>"OutputReducedOneTurn"
    );
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] = "Secondary reactor: destruction will only destroy a section, not entire ship.";
	}	
	
    public function isOverloading($turn){
        return false;
    }	
}
*/



/*SubReactorUniversal - Sub-Reactor that can be fitted on any ship.
On destruction: will destroy the section it's fitted on.
On damage: will roll critical with half the effect of usual reactor and add that critical to primary reactor.
Official on damage: roll critical normally, it will only affect systems on the same section, maximum effect -10 (after cumulation)
*/
class SubReactorUniversal extends ShipSystem{
	public $name = "SubReactorUniversal";
    public $displayName = "Sub Reactor";
    public $iconPath = "reactor.png";
    public $primary = true; //well, it's intended to be fitted on outer sections, but treated as core system
	
	//SubReactor is very important, though not as much as primary reactor itself!
	public $repairPriority = 8;
    	
    public $possibleCriticals = array(
        11=>"OutputReduced1", 
        14=>"OutputReduced2",
        17=>"OutputReduced3",
        21=>"OutputReduced4" //lower but also smoother
    );
		
	/*main reactor criticals for comparision
    public $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        27=>array("OutputReduced10", "ForcedOfflineOneTurn"));
	*/
	
	function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0 ); 
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Critials roughly half as high as on main reactor; marked on main reactor.';
		$this->data["Special"] .= '<br>On destruction entire section will be destroyed (but not entire ship).';
    }
	
	//destroy section if destroyed
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if (!$this->isDamagedOnTurn($gamedata->turn)) return; 
		if (!$this->isDestroyed()) return;		
	
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!				
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				100, 100, 1, 1, 0,
				0,0,'Reactor',10000
			);
			$newFireOrder->pubnotes = "Sub Reactor destroyed - section is immolated.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$ownStruct = $ship->getStructureSystem($this->location);
		if($ownStruct){			
            $remaining = $ownStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $ownStruct->id, $remaining, 0, 0, -1, true, false, "", "Reactor");
            $damageEntry->updated = true;
            $ownStruct->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $ship->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }	
    } //endof function criticalPhaseEffects	
	
	
	//critical - add to primary reactor instead!
    public function addCritical($shipid, $phpclass, $gamedata) {
		//find main reactor
		$ship = $gamedata->getShipById($shipid);
		$mainReactor = $ship->getSystemByName("Reactor");
		if($mainReactor){
			$mainReactor->addCritical($shipid, $phpclass, $gamedata);
		}
		//do NOT call parent, as tis system will NOT actually suffer the crit!
        //parent::addCritical($shipid, $phpclass, $gamedata);
    }
}//endof class SubReactorUniversal


class Engine extends ShipSystem{
    public $name = "engine";
    public $displayName = "Engine";
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    public $outputType = "thrust";
	
	//Engine  is fairly important, being a core system!
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    public $possibleCriticals = array(
        15=>"OutputReduced2",
        21=>"OutputReduced4",
        28=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $powerReq, $output, $boostEfficiency, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->thrustused = (int)$thrustused;
        $this->boostEfficiency = (int)$boostEfficiency;
    }
    
}

class Scanner extends ShipSystem implements SpecialAbility{ //on its own Scanner does not implement anything in particular, but classes ovverriding it do!
    public $name = "scanner";
    public $displayName = "Scanner";
    public $primary = true;
    public $boostable = true;
    public $outputType = "EW";
	//Scanner  is fairly important, being a core system!
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    public $specialAbilityValue = false; //changed by modifications marking Improved/Advanced Sensors!
    
    public $possibleCriticals = array(
        15=>"OutputReduced1",
        19=>"OutputReduced2",
        23=>"OutputReduced3",
        27=>"OutputReduced4");
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->boostEfficiency = "output+1";
    }
    
    public function getOutput(){
        $output = parent::getOutput();
        if ($output === 0)
            return 0;
        
        foreach ($this->power as $power){
            if ($power->turn == TacGamedata::$currentTurn && $power->type == 2){
                $output += $power->amount;
            }        
        }        
        return $output;        
    }    
	
	/*functions adding Advanced/Improved Sensors trait*/
	public function markImproved(){		
		$this->specialAbilities[] = "ImprovedSensors";	
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Improved Sensors - halve Jammer effectiveness (to hit penalty and launch range penalty)(not that of advanced races).'; //not that of advanced races
	}
	public function markAdvanced(){		
    	$this->specialAbilities[] = "AdvancedSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->boostEfficiency = 14; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Advanced Sensors - ignores Jammer, flat 14 boost cost.';//not that of advanced races
		$this->data["Special"] .= "<br>Ignores enemy BDEW and SDEW."; //not that of advanced races
		$this->data["Special"] .= "<br>Ignores any defensive systems lowering enemy profile (shields, EWeb...)."; //not that of advanced races
		$this->data["Special"] .= "<br>All of the above work as usual if operated by advanced races."; 
	}	
	/*note: StarWarsSensors mark in itself doesn't do anything beyond being recognizable for ship description function
		all actual effects are contained in attribute changes
	*/
	public function markStarWars(){		
    	$this->specialAbilities[] = "StarWarsSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->maxBoostLevel = 2;
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Star Wars Sensors - boostability limited to +2.';
	}
	
	public function markAntiquated(){		
    	$this->specialAbilities[] = "AntiquatedSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
		$this->maxBoostLevel = 0;
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Antiquated Sensors cannot be boosted.';
	}

    public function markHyach(){
        $this->specialAbilities[] = "HyachSensors";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Damage sustained by Hyach sensors is halved for purposes of critical rolls.';
    }

	public function testCritical($ship, $gamedata, $crits, $add=0){ 
		$hasHyachSensors = $ship->hasSpecialAbility("HyachSensors");
		$damageBonus = 0;
		if( $hasHyachSensors){
			$damageBonus = -round($this->getTotalDamage() /2); //half of current damage, rounded
		}
		$this->critRollMod += $damageBonus; //apply bonus
		$critsReturn = parent::testCritical($ship, $gamedata, $crits); //add appropriate critical(s)
		$this->critRollMod -= $damageBonus; //unapply bonus
		return $critsReturn; //return new set of critical damage
	}
	

	/*note: LCV Sensors are (or will be) checked at committing Initial Orders, in front end. All but 2 EW points need to be OEW. 
	This is Sensor trait rather than being strictly tied to hull size - while no larger units have it, of LCVs themselves only Young ones have it more or less universally.
	More advanced factions usually do not, and for custom factions it's up to their creator.
	*/
	public function markLCV(){		
    		$this->specialAbilities[] = "LCVSensors";
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'LCV Sensors - up to 2 EW points may be allocated freely. All surplus can be allocated ONLY as OEW.';
	}	

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}
	
} //endof Scanner

class ElintScanner extends Scanner implements SpecialAbility{
    public $name = "elintScanner";
    public $displayName = "ELINT Scanner";
    public $specialAbilities = array("ELINT");
    public $iconPath = "elintArray.png";

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
    
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$boostability = $this->maxBoostLevel;		
	if (!isset($this->data["Special"])) {
		$this->data["Special"] = '';
	}else{
		$this->data["Special"] .= '<br>';
	}
        $this->data["Special"] .= "Allows additional Sensor operations:";
        $this->data["Special"] .= "<br> - SOEW: indicated friendly ship gets half of ElInt ships' OEW bonus. Target must be within 30 hexes of Scout at the moment of firing, supported ships at both declaration and firing.";		     
        $this->data["Special"] .= "<br> - SDEW: boosts target's DEW (by 1 for 2 points allocated). Range 30 hexes (at both declaration and firing).";		     
        $this->data["Special"] .= "<br> - Blanket Protection: all friendly units within 20 hexes (incl. fighters) get +1 DEW per 4 points allocated. Cannot combine with other ElInt activities.";		     
        $this->data["Special"] .= "<br> - Disruption: Reduces target enemy ships' OEW and CCEW by 1 per 3 points allocated (split evenly between targets, CCEW being counted as one target; cannot bring OEW on a target below 0). Range 30 hexes (at both declaration and firing).";	
    }
	/*
	public function markImproved(){	parent::markImproved();   }
	public function markAdvanced(){	parent::markImproved();	}
	*/

    public function markHyachELINT(){
        $this->specialAbilities[] = "HyachELINTSensors";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Damage sustained by Hyach sensors is halved for purposes of critical rolls.';
    }

	public function testCritical($ship, $gamedata, $crits, $add=0){ 
		$hasHyachELINTSensors = $ship->getSpecialAbilityValue("HyachELINTSensors");
		$damageBonus = 0;
		if( $hasHyachELINTSensors){
			$damageBonus = -round($this->getTotalDamage() /2); //half of current damage, rounded
		}
		$this->critRollMod += $damageBonus; //apply bonus
		$critsReturn = parent::testCritical($ship, $gamedata, $crits); //add appropriate critical(s)
		$this->critRollMod -= $damageBonus; //unapply bonus
		return $critsReturn; //return new set of critical damage
	}

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }

}


/*SW Scanners have boostability reduced to +2*/
class SWScanner extends Scanner {
    public $name = "SWScanner";
    public $iconPath = "scanner.png";
	
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
	$this->markStarWars();
    }
	
	/* moved to markStarWars function!
    public $maxBoostLevel = 2;

     public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$boostability = $this->maxBoostLevel;		
	if (!isset($this->data["Special"])) {
		$this->data["Special"] = '';
	}else{
		$this->data["Special"] .= '<br>';
	}
	$this->data["Special"] .= "Boostability limited to +".$boostability.".";	     
    }
    */
} //end of SWScanner

/*Antiquated Scanners cannot be boosted*/
class AntiquatedScanner extends Scanner {
    public $name = "AntiquatedScanner";
    public $iconPath = "scanner.png";
	
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
	$this->markAntiquated();
    }
	
} //end of AntiquatedScanner



class CnC extends ShipSystem{
    public $name = "cnC";
    public $displayName = "C&C";
    public $primary = true;
	
	//C&C  is VERY important, although not as much as the reactor!
	public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    public $possibleCriticals = array(
    	//1=>"SensorsDisrupted", //not implemented! so I take it out 
		1=>"CommunicationsDisrupted",   //this instead of SensorsDisrupted
		9=>"CommunicationsDisrupted", 
		12=>"PenaltyToHit", 
		15=>"RestrictedEW", 
		18=>array("ReducedIniativeOneTurn","ReducedIniative"), 
		21=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative"), 
		24=>array("RestrictedEW","ReducedIniative","ShipDisabledOneTurn")
    );
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
} //endof class CnC

/*Protected CnC - as compensation for ships lacking two C&Cs, these systems get different (lighter) critical table 
*/
class ProtectedCnC extends CnC{
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'This unit should have two separate C&Cs. As this is not possible in FV, critical chart is changed instead.';
	}
	
	public $possibleCriticals = array(
		8=>"CommunicationsDisrupted", 
		16=>"PenaltyToHit", 
		20=>"RestrictedEW", 
		24=>array("ReducedIniativeOneTurn","ReducedIniative"), 
		32=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative"), 
		40=>array("RestrictedEW","ReducedIniative","ShipDisabledOneTurn")
    );
	
}//endof class ProtectedCnC
	


class CargoBay extends ShipSystem{
    public $name = "cargoBay";
    public $displayName = "Cargo Bay";
    
	//Cargo Bay is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }
}


class Quarters extends ShipSystem{
    public $name = "Quarters";
    public $displayName = "Quarters";
    
	//Quarters is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }
}




class Thruster extends ShipSystem{
    public $name = "thruster";
    public $displayName = "Thruster";
    public $direction;
    public $thrustused;
    public $thrustwasted = 0;
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?	
    
    public $possibleCriticals = array(15=>"FirstThrustIgnored", 20=>"HalfEfficiency", 25=>array("FirstThrustIgnored","HalfEfficiency"));
    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
        //arc depends on direction!
		switch($this->direction){
			case 1: //retro
				$this->startArc = 330;
				$this->endArc = 30;
				break;
			case 2: //main
				$this->startArc = 150;
				$this->endArc = 210;
				break;	
			case 3://port
				$this->startArc = 210;
				$this->endArc = 330;
				break;
			case 4://Stbd
				$this->startArc = 30;
				$this->endArc = 150;
				break;
		}
    }
} //endof Thruster


class InvulnerableThruster extends Thruster{
	/*sometimes thruster is techically necessary, despite the fact that it shouldn't be there (eg. on LCVs)*/
	/*this thruster will be almost impossible to damage :) (it should be out of hit table, too!)*/
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
	public $isTargetable = false; //cannot be targeted ever!
	
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
	    parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused );
		//use "technical" (grey) images instead of regular system (blue) ones
		switch($this->direction){
			case 1: //retro
				$this->iconPath = "thruster1Technical.png";
				break;
			case 2: //main
				$this->iconPath = "thruster2Technical.png";
				break;	
			case 3://Port
				$this->iconPath = "thruster3Technical.png";
				break;
			case 4://Stbd
				$this->iconPath = "thruster4Technical.png";
				break;
		}
    }
	
    public function getArmourInvulnerable($target, $shooter, $dmgClass, $pos=null){ //this thruster should be invulnerable to anything...
		$activeAA = 99;
		return $activeAA;
    }
    
    public function testCritical($ship, $gamedata, $crits, $add = 0){ //this thruster won't suffer criticals ;)
	    return $crits;
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'This system is here for technical purposes only. Cannot be damaged in any way, and has unlimited thrust allowance.';
	}

	
} //endof InvulnerableThruster



class GraviticThruster extends Thruster{    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused);
    }
    
    public $firstCriticalIgnored = false; //not needed any more
    
    
    public function addCritical($shipid, $phpclass, $gamedata)
    {	    
	    //new approach: does GravThrusterCritIgnored exist? if yes, go ahead. If not, ignore critical and add GravThrusterCritIgnored instead.
	    //should affect only HalfEfficiency crit!
	    if ($phpclass == 'HalfEfficiency') { //such crit can be ignored - should it?!
		    $alreadyIgnored = false;
		    foreach($this->criticals as $preexisting){
			    if ($preexisting instanceof GravThrusterCritIgnored){
				$alreadyIgnored = true;    
			    }
		    }

		    if (!$alreadyIgnored ) {//nothing was negated yet		
			$crit = new GravThrusterCritIgnored(-1, $shipid, $this->id, 'GravThrusterCritIgnored', $gamedata->turn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
			return $crit;
		    }
	    }
	  
            
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
        return $crit;
    }
}


class MagGraviticThruster extends Thruster{ 
	public $possibleCriticals = array(20=>"HalfEfficiency");
	
	//Mag-Grav Thrusters are considerd Gravitic, complete with first crit ignored effect:
	public function addCritical($shipid, $phpclass, $gamedata)
    {
	    //does GravThrusterCritIgnored exist? if yes, go ahead. If not, ignore critical and add GravThrusterCritIgnored instead.
	    //should affect only HalfEfficiency crit!
	    if ($phpclass == 'HalfEfficiency') { //such crit can be ignored - should it?!
		    $alreadyIgnored = false;
		    foreach($this->criticals as $preexisting){
			    if ($preexisting instanceof GravThrusterCritIgnored){
				$alreadyIgnored = true;    
			    }
		    }

		    if (!$alreadyIgnored ) {//nothing was negated yet		
			$crit = new GravThrusterCritIgnored(-1, $shipid, $this->id, 'GravThrusterCritIgnored', $gamedata->turn);
			$crit->updated = true;
			$this->criticals[] =  $crit;
			return $crit;
		    }
	    }
            
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
        return $crit;
    }
}

class Hangar extends ShipSystem{

    public $name = "hangar";
    public $displayName = "Hangar";
    public $squadrons = Array();
    public $primary = true;
    
	//Hangar is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $output = null){
		if($output === null){ //if output is not explicitly indicated, assume it to be 6 per every full 6 boxes! (that's the usual combat craft capacity)
			$output = floor($maxhealth/6)*6;
		}
        parent::__construct($armour, $maxhealth, 0, $output ); 
    }
}


class Catapult extends ShipSystem{
    public $name = "catapult";
    public $displayName = "Catapult";
    public $squadrons = Array();
    public $primary = true;
    
	//Catapult is not impotant at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $output = 6){
        parent::__construct($armour, $maxhealth, 0, $output );
 
    }
}


class JumpEngine extends ShipSystem{
    public $name = "jumpEngine";
    public $displayName = "Jump Engine";
    public $delay = 0;
    public $primary = true;
    
	//JumpEngine tactically  is not important at all!
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $powerReq, $delay){
        parent::__construct($armour, $maxhealth, $powerReq, 0);
    
        $this->delay = $delay;
    }
	
     public function setSystemDataWindow($turn){
        $this->data["Special"] = "SHOULD NOT be shut down for power (unless damaged >50% or in desperate circumstances).";
	parent::setSystemDataWindow($turn);     
    }
}


class Structure extends ShipSystem{
    public $name = "structure";
    public $displayName = "Structure";
	private $isIndestructible = false;
    
	//Structure is last to be repaired, except purely cosmetic systems like Hanngars  
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour, $maxhealth, $isIndestructible = false){
        parent::__construct($armour, $maxhealth, 0, 0);
		$this->isIndestructible = $isIndestructible;
    }

	
	//Vree need Structure that doesn't fall off even if it's destroyed - here it is!
	//it will get destroyed all right (possibly multiple times in a batlle), but will still be there afterwards
	//Will be destroyed if all such Structures are reduced to 0 (and then all of them will get destroyed !)
	//upon destruction - delete destruction marker
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if($this->isIndestructible){
			foreach ($this->damage as $damage ) if(($damage->turn == $gamedata->turn) && ($damage->destroyed)){ 
				//check all others - if all of them are reduced to 0 - mark them destroyed as well; if not, delete destroyed marker!
				$structures = $ship->getSystemsByName('Structure', true);
				$allDestroyed = true;
				foreach($structures as $struct){
					if(($struct->isIndestructible) && ($struct->getRemainingHealth() > 0)) {
						$allDestroyed = false;
						break;
					}
				}
				if($allDestroyed){//actually do mark them so!
					foreach($structures as $struct) if( $struct->isIndestructible && (!$struct->isDestroyed())){
						$damageEntry = new DamageEntry(-1, $damage->shipid, -1, $damage->turn, $struct->id, 0, 0, 0, $damage->fireorderid, true, false, "Structure falls off", $damage->damageclass, $damage->shooterid, $damage->weaponid);
						$damageEntry->updated = true;
						$struct->damage[] = $damageEntry;
					}
				}else{//unmark this one
					$damage->destroyed = false;
				}
			}
		}
    } //endof function criticalPhaseEffects	
	
} //endof Structure	

	
/*custom system - Drakh Raider Controller*/
class DrakhRaiderController extends ShipSystem {
    public static $controllerList = array();
    public $name = "drakhRaiderController";
    public $displayName = "Raider Controller";
    public $iconPath = "hkControlNode.png";
    public $boostable = true;
    public $maxBoostLevel = 2;
	
    public static function addController($controller){
	    DrakhRaiderController::$controllerList[] = $controller; //add controller to list
    }
	

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        $this->boostEfficiency = $powerReq;
	    DrakhRaiderController::addController($this);
    }    
	
	
    public static function getIniBonus($unit){ //get current Initiative bonus; current = actual as of last turn
	    $iniBonus = 0;
	    $turn = TacGamedata::$currentTurn-1;
	    $turn = max(1,$turn);
	    //strongest system applies
	    foreach(DrakhRaiderController::$controllerList as $controller){
		$controllerShip = $controller->getUnit();
		if($unit->userid == $controllerShip->userid){ //only for the same player...
			if ( ($controller->isDestroyed($turn))
			     || ($controller->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect
	    		$iniBonus = max($controller->getOutputOnTurn($turn),$iniBonus); 
		}
	    }
	    $iniBonus = $iniBonus * 5; //d20->d100
	    $iniBonus = max(0,$iniBonus); 
	    return $iniBonus;
    }
	
    public function getOutputOnTurn($turn){
        $output = parent::getOutput();
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                $output += $power->amount;
            }    
        }
        return $output;
    }

	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);     
        $this->data["Special"] = "Gives indicated Initiative bonus to all friendly Raiders and Heavy Raiders.";	     
        $this->data["Special"] .= "<br>Only strongest bonus applies.";	     	     
        $this->data["Special"] .= "<br>Any changes are effective on NEXT TURN.";	
    }
} //end of DrakhRaiderController
	

/*Orieni Hunter-Killer Control Node
every 1 point of output of such systems allows for controlling 1 flight (here: 6 HKs)
if not enough nodes are active, HKs suffer many penalties
here penalties will be proportional (instead of, say, one flight controlled and one not, there will be 2 hal-controlled flights)
Also, instead of multitude of different penalties, there will be just Initiative penalty - but a big one.
Also, by rules HK link is vulnerable to ElInt activities - which is not modelled here.
*/
class HkControlNode extends ShipSystem{
    public $name = "hkControlNode";
    public $displayName = "HK Control Node";
    public $primary = true;
    private static $fullIniPenalty = -50; //-10, times 5 d20->d100
	
    public static $alreadyCleared = false;	
	public static $nodeList = array(); //array of nodes in game
	public static $hkList = array(); // array of HK flights in game
    
    public $possibleCriticals = array( //simplified from B5Wars!
        15=>"OutputReduced1",
        21=>"OutputReduced2",
    );	

    function __construct($armour, $maxhealth, $powerReq, $output){
        parent::__construct($armour, $maxhealth, $powerReq, $output ); 
	    HkControlNode::$nodeList[] = $this;
    }
	
	
	
	/*to be called by every HK flight after creation*/
    public static function addHKFlight($HKflight){
	    HkControlNode::$hkList[] = $HKflight;
    }
	
	//inactive entries (from other gamedata) might have slipped by... clear them out!
	public static function clearLists($gamedata){
		HkControlNode::$alreadyCleared = true;
		$tmpArray = array();
		foreach(HkControlNode::$nodeList as $curr){
			$shp = $curr->getUnit();
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($shp);
			if ($belongs){
				$tmpArray[] = $curr;
			}			
		}
		HkControlNode::$nodeList = $tmpArray;
		$tmpArray = array();
		foreach(HkControlNode::$hkList as $curr){
			//is this unit defined in current gamedata? (particular instance!)
			$belongs = $gamedata->shipBelongs($curr);
			if ($belongs){
				$tmpArray[] = $curr;
			}			
		}
		HkControlNode::$hkList = $tmpArray;
	}//endof function clearLists
	
	/*how big percentage of uncontrolled penalty will be assigned (multiplier)*/
	public static function getUncontrolledMod($playerID,$gamedata){
		$turn = TacGamedata::$currentTurn-1; //Ini based on Controllers from PREVIOUS turn!
		$turn = max(1,$turn);	
		$totalNodeOutput = 0; //output of all active HK control nodes!
		$totalHKs = 0; //number of all Hunter-Killer craft in operation!		
		
		if(!HkControlNode::$alreadyCleared) HkControlNode::clearLists($gamedata); //in case some inactive entries slipped in
		
		foreach(HkControlNode::$nodeList as $currNode){
			if ( ($currNode->isDestroyed($turn))
			     || ($currNode->isOfflineOnTurn($turn))
			    ){ continue; }//if controller system is destroyed or offline, no effect (or rather - was last turn)			
			$shp = $currNode->getUnit();
			if ($shp->userid == $playerID) $totalNodeOutput +=  $currNode->getOutput();			
		}
		$totalNodeOutput = $totalNodeOutput*6;//translate to number of controled craft - 6 per standard-sized flight
		
		foreach(HkControlNode::$hkList as $hkFlight){
			if ($hkFlight->userid == $playerID) {
				$totalHKs += $hkFlight->countActiveCraft($turn);
			}
		}
		
		$howPartial = 1;
		if ($totalHKs > 0){ //should be! but just in case
			$howPartial = 1-($totalNodeOutput / $totalHKs); //coverage of 100% means no penalty, no covewrage means 100% penalty
			$howPartial = max(0, $howPartial); //can't exercise more than 100% control ;)
		}
		
		return $howPartial;
	}
	
	
	/*Initiative modifier for hunter-killers (penalty for being uncontrolled)
		originally -3, but other penalties were there too (and 1-strong flight was still a flight) - so I increase full penalty significantly!
	*/
	public static function getIniMod($playerID,$gamedata){
		$howPartial = HkControlNode::getUncontrolledMod($playerID,$gamedata);
		$iniModifier = HkControlNode::$fullIniPenalty*$howPartial;
		    
		if($gamedata->turn<=2){ //HKs should start in hangars; instead, they will get additional Ini penalty on turn 1 and 2
			$iniModifier+=HkControlNode::$fullIniPenalty;
		}		
		
		$iniModifier = floor($iniModifier);
		return $iniModifier;
	}//endof function getIniMod
		
     public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$this->data["Special"] = "Controls up to 6 Hunter-Killer craft per point of output.";	     
	$this->data["Special"] .= "<br>If there are not enough nodes to control all deployed Hunter-Killers,<br>their Initiative will be reduced by up to " . HkControlNode::$fullIniPenalty . " due to (semi-)autonomous operation.";	     	     
	$this->data["Special"] .= "<br>On turns 1 and 2, there will be additional Ini penalty on top of that, as HKs orient themselves.";	  	     
	$this->data["Special"] .= "<br>Any Initiative changes are effective on NEXT TURN.";
    }	    
		    
} //endof class HkControlNode


/* Connection Strut, as present on units too large for their designers tech level
	in FV damage is reflected on Structure in Critical phase (not immediately), which means:
	 - incoming fire will affect less damaged Structure (rather than potentially spill over to PRIMARY Structure)
	 - Strut damage will be reflected on PRIMARY if appropriate structure is gone
	 - Strut should have the same armor as section itself (so reflection is accurate)
	 - damage will be attributed to ORIGINAL fire order - potentially creating strange order of events in log
	 - damage in the log will include damage on Connection Strut itself (so effectively a third of it will be non-damage ;) )
	 - any effect that trigger on hitting Structure will NOT work on Strut (like Burst Beam's power drain)
	 - any armor-affecting effects (Plasma Stream...) will work separately on Struct and Structure itself, leading to further discrepancies
*/
class ConnectionStrut extends ShipSystem{
    public $name = "connectionStrut";
    public $displayName = "Connection Strut";
    public $iconPath = "connectionStrut.png";
    
	//Connection Strut cannot be repaired!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    function __construct($armour){
        parent::__construct($armour, 999, 0, 0);    
    }
	
    public function setSystemDataWindow($turn){
        $this->data["Special"] = "This is not a system - rather a weak point in ships' Structure.";
        $this->data["Special"] .= " It has no Structure of its own (in FV - has infinite structure ;) ).";
        $this->data["Special"] .= "<br>Any damage scored on Connection Strut will be scored DOUBLE on appropriate Structure.";
        $this->data["Special"] .= "<br>It will appear in log as coming from appropriate hit, despite actually being marked in Critical phase (which may lead to strange order of events).";
		parent::setSystemDataWindow($turn);    
	}		


    public function testCritical($ship, $gamedata, $crits, $add = 0){ //reflect any damage taken this turn on appropriate Structure!
        foreach ($this->damage as $damage){
            if ($damage->turn == $gamedata->turn || $damage->turn == -1){
                if ($damage->damage > $damage->armour){
                    $dmgTaken = $damage->damage - $damage->armour;
					$dmgTaken = $dmgTaken *2;//double damage DONE, not raw damage coming!
					//reflect on appropriate Structure, and failing that on PRIMARY
					$trgtStructure = $this->structureSystem;
					$healthRem = $trgtStructure->getRemainingHealth();
					$toDeal = min($dmgTaken, $healthRem);
					$destroyed = false;
					if ($toDeal == $healthRem){
						$destroyed = true;
					}
					if ($toDeal > 0){						
						$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $trgtStructure->id, $toDeal, 0, 0, $damage->fireorderid, $destroyed, false, "Connection Strut!", $damage->damageclass);
						$damageEntry->updated = true;
						$trgtStructure->damage[] = $damageEntry;
					}
					$toDeal = $dmgTaken - $healthRem;
					if ($toDeal > 0){ //any remaining damage - score on PRIMARY Structure
						$primary = $ship->getStructureSystem(0);
						$healthRem = $primary->getRemainingHealth();
						$toDeal = min($toDeal, $healthRem);
						$destroyed= false;
						if ($toDeal == $healthRem){
							$destroyed = true;
						}
						if ($toDeal > 0){
							$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primary->id, $toDeal, 0, 0, $damage->fireorderid, $destroyed, false, "Connection Strut!", $damage->damageclass);
							$damageEntry->updated = true;
							$primary->damage[] = $damageEntry;
						}
					}
		
				}
            }
        }
        
        return $crits; //unmodified - this system suffers no criticals
    } //endof function testCritical	
	
}//endof class ConnectionStrut


/*this system contains entirety of Adaptive Armor management*/
class AdaptiveArmorController extends ShipSystem{
    public $name = "adaptiveArmorController";
    public $displayName = "Adaptive Armor Controller";
    public $primary = true; 
	public $isPrimaryTargetable = false;
	public $isTargetable = false; //cannot be targeted ever!
    public $iconPath = "adaptiveArmorController.png";
	
	public $AAtotal = 0;
	public $AAtotal_used = 0;
	public $AApertype = 0;
	public $AApreallocated = 0;
	public $AApreallocated_used = 0;
	public $currClass = '';//for front end
	
	public $allocatedAA = array(); //AA points allocated for given damage type
	public $availableAA = array(); //AA points available for allocation for given damage type
	public $currchangedAA = array(); //AA points allocated in front end
	
	
    
    public $possibleCriticals = array(); //no available criticals - in fact, this system is a technicality and should never be hit
    
	/*as this is a technical system, armor/health/power are always pre-set
		settings defined by ship creator are: maxiumum total AA points, maximum AA points per weapon type, AA points pre-allocated		
		NOTE: this system should be asigned to adaptiveArmorController attribute (in addition to regular placement) to work properly!
	*/
    function __construct( $AAtotal, $AApertype, $AApreallocated  ){ //technical object, does not need typical system attributes (armor, structure...)
        parent::__construct( 0, 1, 0, $AAtotal ); //$armour, $maxhealth, $powerReq, $output
		$this->AAtotal = $AAtotal;
		$this->AApertype = $AApertype;
		$this->AApreallocated = $AApreallocated;
		if (TacGamedata::$currentTurn > 1){
			$this->AApreallocated_used = $AApreallocated; //for further turns player cannot allocate "pre-battle" points any more
		}
		
    }

	//marks damage class(-es) of weapon as existing in $allocatedAA and $availableAA tables
	private function markWpnDmgClass(Weapon $weapon){
		$weaponClassArray = array();
		foreach($weapon->weaponClassArray as $weaponClass){
			$weaponClassArray[] = $weaponClass;
		}
		$weaponClassArray[] = $weapon->weaponClass;
		foreach($weaponClassArray as $weaponClass){
			//check if already defined, if not - add to both tables
			if (!isset($this->allocatedAA[$weaponClass])){
				$this->availableAA[$weaponClass] = 0;
				$this->allocatedAA[$weaponClass] = 0;
			}
		}
	}

	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Deployment phase: check existing (enemy!) weapons and weapon types, note them for allocation
	 - Initial phase: check setting changes made by user, convert to notes
	 - Firing phase: check damage suffered by ship, convert to notes if it increases allowance of particular weapon type
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case -1: //deployment phase 
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise two copies of initial data are written
						foreach($gameData->ships as $enemyUnit) if($enemyUnit->team != $ship->team){
							foreach($enemyUnit->systems as $sys){
								if($sys instanceOf Fighter){
										foreach($sys->systems as $wpn) if($wpn instanceOf Weapon){
											$this->markWpnDmgClass($wpn);
										}
								}else if($sys instanceOf Weapon){
										$this->markWpnDmgClass($sys);
								}							
							}				
						}
						//AND PREPARE APPROPRIATE NOTES!						
						//	'available;dmgType' public $availableAA = array(); //AA points available for allocation for given damage type
						//	'set;dmgType' public $allocatedAA = array(); //AA points allocated for given damage type
						foreach($this->availableAA as $weaponClass=>$ptsAvailable){
							//	'available;dmgType' public $availableAA = array(); //AA points available for allocation for given damage type
							//	'set;dmgType' public $allocatedAA = array(); //AA points allocated for given damage type
							if (isset($this->allocatedAA[$weaponClass])){
								$ptsSet = $this->allocatedAA[$weaponClass];
							} else $ptsSet = 0;
							$notekey = 'available;' . $weaponClass;
							$noteHuman = 'Adaptive Armor available';
							$noteValue = $ptsAvailable;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
							$notekey = 'set;' . $weaponClass;
							$noteHuman = 'Adaptive Armor set';
							$noteValue = $ptsSet;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					}
				break;
								
				case 1: //Initial phase
					//data returned as currchangedAA table, with ONLY information about what was changed )one entry means +1)
					//in turn 1 increase availability as well (this goes from pre-allocated pool), in further turns do not!
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);
							}
						}
						$this->onIndividualNotesLoaded($gameData);		
					
						foreach($this->currchangedAA as $weaponClass){
							$this->allocatedAA[$weaponClass]++;
							if ($gameData->turn==1) $this->availableAA[$weaponClass]++;
						}

						foreach($this->availableAA as $weaponClass=>$ptsAvailable){
							if (isset($this->allocatedAA[$weaponClass])){
								$ptsSet = $this->allocatedAA[$weaponClass];
							} else $ptsSet = 0;
							if ($gameData->turn==1){ //availability is changed here in turn 1 only
								$notekey = 'available;' . $weaponClass;
								$noteHuman = 'Adaptive Armor available';
								$noteValue = $ptsAvailable;
								$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
							}
							$notekey = 'set;' . $weaponClass;
							$noteHuman = 'Adaptive Armor set';
							$noteValue = $ptsSet;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					}
					
				break;
				
				case 4: //firing phase
					//after re-reading ship control sheets: AA should be at flight level unless fighter in question is SuperHeavy
					if((!($ship instanceOf FighterFlight)) || (!$ship->superheavy)){ 
						foreach($ship->systems as $system) foreach($system->damage as $dmg) if($dmg->turn==$gameData->turn){//damage suffered this turn
							if($dmg->damage > $dmg->armour){ //actual damage was caused!
								$weaponClass = $dmg->damageclass;
								if(!isset($this->availableAA[$weaponClass])){ //this type of damage wasn't encountered yet!
									$this->availableAA[$weaponClass] = 0;
									$this->allocatedAA[$weaponClass] = 0;
								}
								if($this->availableAA[$weaponClass] < $this->AApertype){ //maximum not yet unlocked!
									$this->availableAA[$weaponClass]+=1;
								}
							}
						}
					}else{ //for SUPERHEAVY fighter flight - only damage of a particular fighter counts!
						$relevantFtr = $ship->getFighterBySystem($this->id);
						foreach($relevantFtr->damage as $dmg) if($dmg->turn==$gameData->turn){//damage suffered this turn
							if($dmg->damage > $dmg->armour){ //actual damage was caused!
								$weaponClass = $dmg->damageclass;
								if(!isset($this->availableAA[$weaponClass])){ //this type of damage wasn't encountered yet!
									$this->availableAA[$weaponClass] = 0;
									$this->allocatedAA[$weaponClass] = 0;
								}
								if($this->availableAA[$weaponClass] < $this->AApertype){ //maximum not yet unlocked!
									$this->availableAA[$weaponClass]+=1;
								}
							}
						}
					}
					//AND PREPARE APPROPRIATE NOTES!
					//	'preallocatedUsed' public $AApreallocated_used = 0;	
					//$notekey = 'preallocatedUsed';
					//$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,'Adaptive Armor: preallocated points used',$this->AApreallocated_used);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					foreach($this->availableAA as $weaponClass=>$ptsAvailable){
						//	'available;dmgType' public $availableAA = array(); //AA points available for allocation for given damage type
						//	'set;dmgType' public $allocatedAA = array(); //AA points allocated for given damage type
						if (isset($this->allocatedAA[$weaponClass])){
							$ptsSet = $this->allocatedAA[$weaponClass];
						} else $ptsSet = 0;
						$notekey = 'available;' . $weaponClass;
						$noteHuman = 'Adaptive Armor available';
						$noteValue = $ptsAvailable;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						$notekey = 'set;' . $weaponClass;
						$noteHuman = 'Adaptive Armor set';
						$noteValue = $ptsSet;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	 - fill $allocation table
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting - so enact all changes as is
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($explodedKey[0]){
				/* always 0 for starters and max for further turns - no need to save or read this information
				case 'preallocatedUsed': //total number of preallocatable points that were actually preallocated
					$this->AApreallocated_used = $currNote->notevalue;
					break;
				*/
				case 'available': //total number of points available for assignment for a given damage type
					$this->availableAA[$explodedKey[1]] = $currNote->notevalue;
					break;
				case 'set': //total number of points assigned for a given damage type
					$this->allocatedAA[$explodedKey[1]] = $currNote->notevalue;
					break;				
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
		//calculate $this->AAtotal_used, too!
		$this->AAtotal_used = 0;
		foreach($this->allocatedAA as $dmgType=>$countAllocated){
			$this->AAtotal_used += $countAllocated;
		}
	} //endof function onIndividualNotesLoaded
	
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn); 
		$this->data["Adaptive Armor"] =  $this->AAtotal_used . '/' . $this->AAtotal;
		$this->data[" - per weapon type"] =  $this->AApertype;
		$this->data[" - preassigned"] =  $this->AApreallocated_used . '/' . $this->AApreallocated;
		foreach($this->allocatedAA as $dmgType=>$AAallocated){
			$AAavailable = $this->availableAA[$dmgType];
			$this->data[' - '.$dmgType] =  $AAallocated . '/' . $AAavailable;
		}
        $this->data["Special"] = "This system is responsible for Adaptive Armor settings management.";	   
        $this->data["Special"] .= "<br>You may assign AA points in Initial phase.";
        $this->data["Special"] .= "<br>Pre-set AA points may be used in turn 1 only.";
        $this->data["Special"] .= "<br>AA points set in previous turns cannot be unassigned.";
        $this->data["Special"] .= "<br>AA points are unlocked individually down to superheavy fighters - lighter craft unlock AA points as whole flights. Assignment is always individual.";
    }
	
	/*always redefine $this->data for AA controller! A lot of variable information goes there...*/
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        $strippedSystem->allocatedAA = $this->allocatedAA;
        $strippedSystem->availableAA = $this->availableAA;
        $strippedSystem->currchangedAA = $this->currchangedAA;
        $strippedSystem->AAtotal_used = $this->AAtotal_used;
        $strippedSystem->AApreallocated_used = $this->AApreallocated_used;
		
        return $strippedSystem;
    }
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer))	$this->currchangedAA = $this->individualNotesTransfer; //else there's nothing relevant there
		$this->individualNotesTransfer = array(); //empty, just in case
	}		
	
	//returns protection allocated for a given dmg class
	public function getArmourValue($weaponClass){
		$armour = 0;
		if (isset($this->allocatedAA[$weaponClass])) $armour = $this->allocatedAA[$weaponClass];
		return $armour;
	}
							
} //endof AdaptiveArmorController



class DiffuserTendril extends ShipSystem{
    public $name = "DiffuserTendril";
    public $displayName = "Diffuser Tendril";
    public $primary = true;
	public $isPrimaryTargetable = false; //shouldn't be targetable at all, in fact!
	public $isTargetable = false; //cannot be targeted ever!
    public $iconPath = "EnergyDiffuserTendril.png";
    
	//Diffuser Tendrils cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    
    function __construct($maxhealth, $side = 'R'){ //everything is done in the diffuser, Tendrils basically just are! - L/R suggests whether to use left or right graphics
		$this->iconPath = 'EnergyDiffuserTendril' . $side;
		if($maxhealth < 15){ //small
			$this->iconPath .= "1";
		}
		else if($maxhealth < 30){//medium
			$this->iconPath .= "2";
		} else{//large!
			$this->iconPath .= "3";
		}
		$this->iconPath .= ".png";
		parent::__construct(0, $maxhealth, 0, 0);
		
		$this->output=$maxhealth;//output is displayed anyway, make it show something useful...
	}
	

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Used to store absorbed energy from hits.<br>It is here for visual (and technical) purposes only. It's part of Energy Diffuser system.";
	}	
	
	public function getRemainingCapacity(){
		return $this->getRemainingHealth();
	}
	
	public function getUsedCapacity(){
		return $this->getTotalDamage();
	}
	
	public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
		$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $value, 0, 0, -1, false, false, "Absorb/Dissipate!", "Tendril");
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;
	}
}//endof class DiffuserTendril


//fighter systems don't get damaged - so fighter tendrils need to store damage by way of notes
class DiffuserTendrilFtr extends DiffuserTendril{
    public $name = "DiffuserTendrilFtr";
    public $displayName = "Diffuser Tendril";
	
	private $usedCapacityTotal=0;
	private $thisTurnEntries=array();
    
	//Diffuser Tendrils cannot be repaired at all!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired

    
	public function setSystemDataWindow($turn){
		//add information about damage stored - ships do have visual reminder about it, but fighters do not!
		parent::setSystemDataWindow($turn); 
		$freeCapacity = $this->maxhealth - $this->getUsedCapacity();
		//$this->data["Capacity"] = $this->getUsedCapacity() . '/' . $this->maxhealth;
		$this->data["Capacity available/max"] = $freeCapacity . '/' . $this->maxhealth;
	}	
	
	/*always redefine $this->data due to current capacity info*/
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;		
        return $strippedSystem;
    }
	
	
	public function getRemainingCapacity(){
		return $this->maxhealth - $this->usedCapacityTotal;
	}
	
	public function getUsedCapacity(){
		return $this->usedCapacityTotal;
	}
	
	public function absorbDamage($ship,$gamedata,$value){ //or dissipate, with negative value
		$this->usedCapacityTotal += $value; //running count
		$this->thisTurnEntries[] = $value; //mark for database
	}
	
	
	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Firing phase: add information on stored/dissipated energy (every entry separately)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case 4: //firing phase
					foreach($this->thisTurnEntries as $tte){					
						$notekey = 'absorb';
						$noteHuman = 'Tendril absorbed or dissipated';
						$noteValue = $tte;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	here:
	 - fill $usedCapacityTotal value
	*/
	public function onIndividualNotesLoaded($gamedata){
		$this->usedCapacityTotal = 0;
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting 
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($currNote->notekey){
				case 'absorb': //absorbtion or dissipation of energy
					$this->usedCapacityTotal += $currNote->notevalue;
					break;		
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded

}//endof class DiffuserTendrilFtr



/*Shadow damage absorbtion system; remember to add Tendrils - largest first!*/
class EnergyDiffuser extends ShipSystem{
    public $name = "EnergyDiffuser";
    public $displayName = "Energy Diffuser";
    public $iconPath = "EnergyDiffuser.png";
    public $primary = true;
	public $isPrimaryTargetable = false; //like other core systems
	public $tendrils = array();//tendrils belonging to this Diffuser
	
    
	//EnergyDiffuser has very high repair priority, being the core defensive system!
	public $repairPriority = 9;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    public $possibleCriticals = array(
		11=>"TendrilDestroyed",
		16=>array("TendrilDestroyed", "OutputReduced1"),
		20=>array("TendrilDestroyed", "OutputReduced2", "TendrilCapacityReduced"),
		25=>array("TendrilDestroyed", "TendrilDestroyed", "OutputReduced3", "TendrilCapacityReduced", "TendrilCapacityReduced")
	);
/* below list of critical effects:	
11-15: No effect to the diffuser. However, one of the
attached segments is destroyed (player’s choice). Mark
an X in its box to indicate this. The pilot suffers “pain” (see
10.18.10) on the next turn equal to the segment’s absorption
capacity (treated as damage, even though no damage points
are actually marked off anywhere in the ship).
16-19: Lose a segment as described under 11-15, and
reduce the diffuser’s discharge rate by 1.
20-24: Lose a segment, reduce the discharge rating by
2 and lower the absorption ratings of all remaining segments
by 2.
25+: Lose two segments, reduce the discharge rating by
3 and lower the absorption ratings of all remaining segments
by 4.
*/
	
	
	function __construct($armour, $maxhealth, $dissipation, $startArc, $endArc){
        // dissipation is handled as output.
        parent::__construct($armour, $maxhealth, 0, $dissipation);
        
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
		
		if ($this->getUnit() instanceOf FighterFlight){ //for fighters - no criticals of course ;)
			$this->possibleCriticals = array();
		}
    }
	
	function addTendril($tendril){
		if($tendril) $this->tendrils[] = $tendril;
	}

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Absorbs energy from hits as long as there is storage capacity available (Diffuser Tendrils).";
		$this->data["Special"] .= "<br>Tries not to absorb if protected system would have been destroyed anyway without overkilling (eg. very strong Piercing or Matter fire hitting small systems).";
		$this->data["Special"] .= "<br>Dissipates energy from Tendrils in Critical phase.";
	}	
	
	//effects that happen in Critical phase (after criticals are rolled) - dissipation and actual loss of tendrils due to critical received
	public function criticalPhaseEffects($ship, $gamedata){
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		$ship = $this->getUnit();
		$pilot = $ship->getSystemByName("CnC");
		
		//1. if THIS TURN TendrilDestroyed critical was added - mark last tendril destroyed
		foreach ($this->criticals as $crit) if(($crit instanceof TendrilDestroyed) && ($crit->turn==$gamedata->turn)) {
			$lastTendril = null;
			foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
				$lastTendril = $tendril;
			}
			if($lastTendril){ ///no need to redefine for fighter - there criticals just won't happen
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $lastTendril->id, 0, 0, 0, -1, true, false, "DestroyTendril!", "Destruction");
				$damageEntry->updated = true;
				$lastTendril->damage[] = $damageEntry;
				
				//add pain to pilot, too!				
				if($pilot){
					$onePainPer = 10; //1 point of pain per how many damage points?
					if ($ship->factionAge > 3) { //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
						$onePainPer = 20;//slow-grown Primordial ships are more resistant to pain		
					}
					$painSuffered = ceil( $lastTendril->maxhealth/$onePainPer ); //let's round that up...
					for($i=1;$i<=$painSuffered;$i++){
						$crit = new ShadowPilotPain(-1, $ship->id, $pilot->id, 'ShadowPilotPain', $gamedata->turn+1, $gamedata->turn+1);
						$crit->updated = true;
						$pilot->criticals[] =  $crit;
					}
				}
				
			}
		}
		
		//2. dissipate stored energy (in order of tendrils - largest are first, and usually freeing up largest ones is best)
		$dissipationAvailable = $this->getOutput();
		foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){				
			$toDissipate = min($dissipationAvailable, $tendril->getUsedCapacity());
			if($toDissipate > 0){
				$dissipationAvailable -= $toDissipate;
				//dissipation == undamage
				$tendril->absorbDamage($ship,$gamedata,-$toDissipate);
			}
		}
		
		
	} //endof function criticalPhaseEffects
	
	
	//function estimating how good this Diffuser is at stopping damage;
	//in case of diffuser, its effectiveness equals largest shot it can stop, with tiebreaker equal to remaining total capacity
	//this is for recognizing it as system capable of affecting damage resolution and choosing best one if multiple Diffusers can protect
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false) {
		$remainingCapacity = 0;
		$totalCapacity = 0;
		$largestCapacity = 0;
				
		//check capacity reduction due to criticals...
		$reduction = $this->hasCritical('TendrilCapacityReduced')*2;

		foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
			$totalCapacity += $tendril->maxhealth;
			$tendrilCapacity = $tendril->getRemainingCapacity()-$reduction;
			if($tendrilCapacity>0){
				$remainingCapacity += $tendrilCapacity;
				$largestCapacity = max($tendrilCapacity,$largestCapacity);
			}
		}		
		
		//tiebreaker: less filled (proportionally), to try and split load if possible
		$protectionValue = $largestCapacity;
		$protectionValue = min($largestCapacity,$expectedDmg);//being able to protect over expected damage is irrelevant - while ratio of being filled is!
		if($totalCapacity > 0) $protectionValue += $remainingCapacity/$totalCapacity;
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		$damageToAbsorb=$effectiveDamage-$effectiveArmor;
		$damageAbsorbed=0;
		
		if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
		
		$mostSuitableAbsorbtion=0;
		$mostSuitableTendril=null;		
		//check capacity reduction due to criticals...
		$reduction = $this->hasCritical('TendrilCapacityReduced')*2;

		foreach($this->tendrils as $tendril) if(!$tendril->isDestroyed()){
			$tendrilCapacity = $tendril->getRemainingCapacity() - $reduction;
			if($tendrilCapacity>0){ //else it's useless ATM
				if ($mostSuitableAbsorbtion < $damageToAbsorb){ //not found a tendril able to accept entire damage yet, looking for something larger
					if ($tendrilCapacity >= $mostSuitableAbsorbtion) { //new one is more suitable (all other things equal later tendril is more suitable)
						$mostSuitableAbsorbtion = $tendrilCapacity;
						$mostSuitableTendril = $tendril;
					}
				}else{ //appropriate tendril already found, looking for something better suited - eg. smaller but still able to accept entire damage
					if (($tendrilCapacity <= $mostSuitableAbsorbtion) && ($tendrilCapacity >= $damageToAbsorb)) { //new one is more suitable (all other things equal later tendril is more suitable)
						$mostSuitableAbsorbtion = $tendrilCapacity;
						$mostSuitableTendril = $tendril;
					}
				}
			}
		}	
		
		$noOverkill = (!$weapon->doOverkill) && ($weapon->noOverkill || ($weapon->damageType == 'Piercing'));
		if($noOverkill){//shot is incapable of overkilling - reducing it would not matter if it doesn't prevent destruction of system hit
			$remainingHealth = $systemProtected->getRemainingHealth();
			if ($remainingHealth+$mostSuitableAbsorbtion <= $damageToAbsorb) return $returnValues; //any absorbtion would be futile and just fill tendril
		}
		
		if($mostSuitableAbsorbtion>0){ //appropriate tendril found!
			$damageAbsorbed=min($damageToAbsorb,$mostSuitableAbsorbtion);
			$returnValues['dmg']=$effectiveDamage-$damageAbsorbed;			
			$mostSuitableTendril->absorbDamage($target,$gamedata,$damageAbsorbed);
		}
		
		return $returnValues;
	}
} //endof EnergyDiffuser






//self repair system - implemented as weapon for simplicity; does repair damage caused in current turn, too (but only earlier crits)
//othrwise it's close to the original
class SelfRepair extends ShipSystem{
	public $name = "SelfRepair";
	public $displayName = "Self Repair";
	public $iconPath = "SelfRepair.png";
    public $primary = true;
	
		
	public $output = 0;
	public $maxRepairPoints=0;//maximum points that can be repaired during battle
	public $usedRepairPoints=0;//repair points already used
	public $usedThisTurn=0;
	public $priorityChanges = array();//priority overrides - in format systemID->priority; 0 don't repair, 20 priority repair, -1 cancel override :)
	public $currentlyDisplayedSystem = -1; //for front end only
      
	
	//SelfRepair itself is most important to be repaired - as it's the condition of further repairs being effected!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
 	public $possibleCriticals = array( 
            19=>"OutputHalved"
	);

	function __construct($armour, $maxhealth, $output)
	{
		//power requirement is 0, health is always defined by constructor, as is output - but they cannot be <1!
		if ( $maxhealth <1 ) $maxhealth = 1;
		if ( $output <1 ) $output = 1; //base output cannot be <1
		parent::__construct($armour, $maxhealth, 0, 0, 0);
		$this->output = $output; //after parent - weapon has no output and passes 0 to system creation
		$this->maxRepairPoints = $maxhealth*10;
	}
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		//some effects should originally work for current turn, but it won't work with FV handling of ballistics. Moving everything to next turn.
		//it's Ion (not EM) weapon with no special remarks regarding advanced races and system - so works normally on AdvArmor/Ancients etc
		$this->data["Repair points (used/max)"] = $this->usedRepairPoints . "/" . $this->maxRepairPoints;
		$this->data["Special"] = "At end of turn phase automatically repairs damage to vessel. Cannot repair destroyed structure blocks.";      
		//$this->data["Special"] .= "<br>Priority: first fix criticals, then damaged systems, finally restore destroyed systems.";  
		$this->data["Special"] .= "<br>Priority: first fix criticals, then revive destroyed systems, finally restore boxes to damaged systems.";  
		$this->data["Special"] .= "<br>Core (and other particularly important) systems are repaired first, then weapons, then other systems.";
		$this->data["Special"] .= "<br>Will not fix criticals and damage caused in current turn.";
		$this->data["Special"] .= "<br>Player may modify repair priorities - click self repair system in Initial phase and cycle through damaged systems.";
	}

	
	/* sorts system for repair priority*/
    public static function sortSystemsByRepairPriority($a, $b){
		/* being destroyed modifies priority instead
		$aDestroyed = $a->isDestroyed();
		$bDestroyed = $b->isDestroyed();
		//destroyed systems are fixed first!
		if(($aDestroyed == true) && ($bDestroyed == false)){
			return -1;
		}else if(($aDestroyed == false) && ($bDestroyed == true)){
			return 1;
		}
		*/
		//priority, then size (smaller first, as easier to repair), then ID!
		if($a->repairPriority!==$b->repairPriority){ 
            return $b->repairPriority - $a->repairPriority; //higher priority first!
        }else if($a->maxhealth!==$b->maxhealth){ 
            return $a->maxhealth - $b->maxhealth; //smaller first!
        }else return $a->id - $b->id;
    } //endof function sortSystemsByRepairPriority
	/* sorts critical hits for repair priority*/
    public static function sortCriticalsByRepairPriority($a, $b){
		//priority, then size (smaller first, as easier to repair), then ID!
		if($a->repairPriority!==$b->repairPriority){ 
            return $b->repairPriority - $a->repairPriority; //higher priority first!
        }else if($a->repairCost!==$b->repairCost){ ///costlier first!
            return $b->repairCost - $a->repairCost; //costlier first!
        }else return $a->id - $b->id;
    } //endof function sortSystemsByRepairPriority
	
	
	
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		//how many points are available?
		$availableRepairPoints = $this->maxRepairPoints - $this->usedRepairPoints;
		$availableRepairPoints = min($availableRepairPoints,$this->getOutput()); //no more than remaining points, no more than actual system repair capability	
		
		//sort all systems by priority
		$ship=$this->getUnit();
		$systemList = array();
		foreach($ship->systems as $system){			
			if ( $system->maxhealth <= $system->getRemainingHealth( ) ) continue; //skip undamaged systems...
			//priority overrides...
			if(array_key_exists($system->id, $this->priorityChanges) && ($this->priorityChanges[$system->id]>=0)){
				$system->repairPriority = $this->priorityChanges[$system->id];
			}			
			//skip systems attached to destroyed structure blocks...
			if($system->repairPriority<1) continue;//skip systems that cannot be repaired
			if(!($system instanceOf Structure)){ //non-Structure system - cannot repair if attached to destroyed Structure block
				$strBlock = $ship->getStructureSystem($system->location);
				if($strBlock->isDestroyed($gamedata->turn)) continue;
			}else{ //Structure block - cannot repair if destroyed
				if($system->isDestroyed($gamedata->turn)) continue; //cannot repair destroyed Structure
			}
			
			//destroyed systems get first priority
			if( ($system->repairPriority <=10) //only systems whose priority wasn't modified yet
				&& ($system->isDestroyed($gamedata->turn))
			){
				$system->repairPriority += 10;
			}
			
			$systemList[] = $system;			
		}
		usort($systemList, "self::sortSystemsByRepairPriority");
		

		
		//repair criticals (on non-destroyed systems only; also, skip criticals generated this turn!)
		$critList = array();
		foreach ($ship->systems as $systemToRepair){//crit fixing may be necessary even on technically undamaged systems	
			if ($availableRepairPoints<1) break;//cannot repair anything
			if ($systemToRepair->repairPriority<1) continue;//skip systems that cannot be repaired
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair criticals on destroyed system...
			
			foreach($systemToRepair->criticals as $critDmg) {
				if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
				if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn
				if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
				if($critDmg->repairPriority<10) $critDmg->repairPriority += $systemToRepair->repairPriority; //modify priority by priority of system critical is on! 
				$critList[] = $critDmg;				
			}		
		}	
		$noOfCrits = count($critList);
		if($noOfCrits>0){
			usort($critList, "self::sortCriticalsByRepairPriority");
			foreach ($critList as $critDmg){ //repairable criticals of current system
				if ($critDmg->repairCost <= $availableRepairPoints){//execute repair!
					$critDmg->turnend = $gamedata->turn;//actual repair ;)
					$critDmg->forceModify = true; //actually save the repair...
					$critDmg->updated = true; //actually save the repair cd!...
					$availableRepairPoints -= $critDmg->repairCost;
					$this->usedThisTurn += $critDmg->repairCost;
				}
			}
		}		

		
		
		//repair damaged/destroyed systems, possibly undestroying them in the process (cannot repair destroyed Structure and systems attached to it - but this is taken care at the stage of preparing list of repairable systems)
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			$causedThisTurn = $systemToRepair->damageReceivedOnTurn($gamedata->turn);
			$toBeRepaired = $currentDamage-$causedThisTurn;
			if($toBeRepaired > 0){ //do repair!
				$toBeFixed = min($toBeRepaired, $availableRepairPoints);
				$undestroy = false;
				if ($toBeFixed>=$currentDamage){ //full health restored!
					$undestroy=true;
				}
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, $undestroy, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
			
			
		/*rehearsed above
		//repair criticals (on non-destroyed systems only; also, skip criticals generated this turn!)
		//foreach ($systemList as $systemToRepair){
		foreach ($ship->systems as $systemToRepair){//crit fixing may be necessary even on technically undamaged systems	
			if ($availableRepairPoints<1) break;//cannot repair anything
			if ($systemToRepair->repairPriority<1) continue;//skip systems that cannot be repaired
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair criticals on destroyed system...
			$critList = array();
			foreach($systemToRepair->criticals as $critDmg) {
				if($critDmg->repairPriority<1) continue;//if critical cannot be repaired
				if($critDmg->turn >= $gamedata->turn) continue;//don't repair criticals caused in current (or future!) turn
				if ($critDmg->oneturn || ($critDmg->turnend > 0)) continue;//temporary criticals (or those already repaired) also cannot be repaired
				$critList[] = $critDmg;				
			}			
			$noOfCrits = count($critList);
			if($noOfCrits>0){
				usort($critList, "self::sortCriticalsByRepairPriority");
				foreach ($critList as $critDmg){ //repairable criticals of current system
					if ($critDmg->repairCost <= $availableRepairPoints){//execute repair!
						$critDmg->turnend = $gamedata->turn;//actual repair ;)
						$critDmg->forceModify = true; //actually save the repair...
						$critDmg->updated = true; //actually save the repair cd!...
						$availableRepairPoints -= $critDmg->repairCost;
						$this->usedThisTurn += $critDmg->repairCost;
					}
				}
			}
		}	
		*/	
			
		/*old version - separate loops for destroyed and damaged systems
		//repair destroyed systems, possibly undestroying them in the process (cannot repair destroyed Structure)
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			if ($systemToRepair instanceOf Structure) continue; //cannot repair destroyed Structure
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			if($currentDamage > 0){ //do repair!
				$toBeFixed = min($currentDamage, $availableRepairPoints);
				$undestroy = false;
				if ($toBeFixed==$currentDamage){ //full health restored!
					$undestroy=true;
				}
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, $undestroy, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
		
		
		//repair damaged systems
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			//structure is okay - at apprpriate priority - as revivable systems were revived already
			//if ($systemToRepair instanceOf Structure) continue; //let's repair destroyed systems first, then go for damaged Structure
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//don't repair damage on destroyed system... yet!
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			if($currentDamage > 0){ //do repair!
				$toBeFixed = min($currentDamage, $availableRepairPoints);
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, false, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
		*/

		/* separate block not necessary when repair happens AFTER reviving
		//repair damaged Structure
		foreach ($systemList as $systemToRepair){
			if ($availableRepairPoints<1) continue;//cannot repair anything any longer
			if (!($systemToRepair instanceOf Structure)) continue; //now it's Structure exclusively
			if ($systemToRepair->isDestroyed($gamedata->turn)) continue;//cannot repair destroyed Structure
			$currentDamage = $systemToRepair->maxhealth - $systemToRepair->getRemainingHealth( );
			if($currentDamage > 0){ //do repair!
				$toBeFixed = min($currentDamage, $availableRepairPoints);
				//actual healing entry
				$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $systemToRepair->id, -$toBeFixed, 0, 0, -1, false, false, 'SelfRepair', 'SelfRepair');
				$damageEntry->updated = true;
				$systemToRepair->damage[] = $damageEntry;
				//meark repair points used
				$availableRepairPoints -= $toBeFixed;
				$this->usedThisTurn += $toBeFixed;
			}
		}	
		*/
		
    } //endof function criticalPhaseEffects
	
	

	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Firing phase: add repair points used to notes (current entry, not total)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case 1: //Initial phase - set new priority overrides! (and ONLY new, don't bother with preexisting ones)
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						/*set new overrides, just received from front end - so no need to load old ones at this point*/
						foreach($this->priorityChanges as $systemID=>$priorityValue){							
							$notekey = 'override';
							$noteHuman = 'Repair priority override';
							$noteValue = $systemID . ';' . $priorityValue;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue	
						}
					}
				
				case 4: //firing phase
					if($this->usedThisTurn>0){ //self-repair was actually used this turn!
						$notekey = 'used';
						$noteHuman = 'Self-repair used';
						$noteValue = $this->usedThisTurn;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
		}
	} //endof function generateIndividualNotes
	
	/*act on notes just loaded - to be redefined by systems as necessary
	here:
	 - fill $usedRepairPoints value
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting 
			$explodedKey = explode ( ';' , $currNote->notekey ) ;//split into array: [area;value] where area denotes action, value - damage type (typically) 
			switch($currNote->notekey){
				case 'used': //self-repair points used in a given turn
					$this->usedRepairPoints += $currNote->notevalue;
					break;		
				case 'override': //priority override for systems
					$explodedOverride = explode ( ';' , $currNote->notevalue ) ;//split into array: [systemID;overriding Priority] 
					if ($explodedOverride[1] >= 0){
						$this->priorityChanges[$explodedOverride[0]] = $explodedOverride[1];
					}else{
						unset($this->priorityChanges[$explodedOverride[0]]);
					}
					break;
			}
		}
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded

	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;		
        $strippedSystem->output = $this->getOutput();		
        $strippedSystem->priorityChanges = $this->priorityChanges;	
        return $strippedSystem;
    }
	
	/* data transferred from front end, if any - priority overrides!*/	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in currchangedAA
		if(is_array($this->individualNotesTransfer)){
			foreach($this->individualNotesTransfer as $noteReceived){
				$explodedOverride = explode ( ';' , $noteReceived ) ;//split into array: [systemID;overriding Priority] 
				$this->priorityChanges[$explodedOverride[0]] = $explodedOverride[1]; //here do add ALL values, -1 including - it's necessary so it's later saved to database
			}
		}
		$this->individualNotesTransfer = array(); //empty, just in case
	}		

}//endof class SelfRepair



//BioThruster - it's NOT seen as thruster by game; used to calculate output of BioDrive engine 
class BioThruster extends ShipSystem{
	public $iconPath = "thrusterOmni.png";
    public $name = "BioThruster";
    public $displayName = "BioThruster";
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
	//BioThrusters are fairly important!
	public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    	    
    public $possibleCriticals = array(15=>"OutputReduced1", 24=>array("OutputReduced1","OutputReduced1"));//different than original
    
    function __construct($armour, $maxhealth, $output ){
        parent::__construct($armour, $maxhealth, 0, $output );
		//always omnidirectional, but this need to be set AFTER default constructor
		$this->startArc = 0;
		$this->endArc = 360; 
    }
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);  
		$this->data["Special"] = "BioThruster - basically an omnidirectional thruster.";      
		$this->data["Special"] .= "<br>For technical reasons in FV BioThrusters output is summed up in BioDrive and then channeled by regular (invulnerable) thrusters.";  
	}	
} //endof class BioThruster



//BioDrive - basically an engine with rating calculated from ships' BioThrusters
//technical system, should never get hit.
//remember to plug BioThrusters to the BioDrive at design stage!
class BioDrive extends Engine{
	public $iconPath = "engineTechnical.png";
    public $name = "engine";
    public $displayName = "Engine";
    public $primary = true;
    public $isPrimaryTargetable = false;
    public $boostable = false;//cannot boost BioDrive!
    public $outputType = "thrust";
	
	private $bioThrusters = array();
	
    
    public $possibleCriticals = array( ); //technical system, should never get damaged
    
    function __construct(){
        parent::__construct(0, 1, 0, 0, 0 ); //($armour, $maxhealth, $powerReq, $output, $boostEfficiency
    }
    
	function addThruster($thruster){
		if($thruster) $this->bioThrusters[] = $thruster;
	}
	
	
	public function setSystemDataWindow($turn){
		$this->output = $this->getOutput();	
		parent::setSystemDataWindow($turn); 	
		$this->output = $this->getOutput();	
		$this->data["Efficiency"] = $this->boostEfficiency;
		$this->data["Special"] = "BioDrive - basically an Engine with basic output calculated from BioThruster outputs.";      
		$this->data["Special"] .= "<br>Will never be damaged.";  
		$this->data["Special"] .= "<br>Cannot but extra thrust."; //rules say BioThrusters CAN buy extra thrust, with rating provided on SCS... But rating on SCS is N/A...
	}
	
	
    public function getOutput(){
        $output = 0;
		//count thrust from BioThrusters
		foreach($this->bioThrusters as $thruster){
			$output += $thruster->getOutput();
		}
		if ($output === 0) return 0; //cannot buy extra thrust if there are no working thrusters!
	
		//reduce by pain
		$ship=$this->getUnit();
		if($ship){
			$pilot = $ship->getSystemByName("CnC");
			if($pilot){
				$painLevel = $pilot->hasCritical('ShadowPilotPain',TacGamedata::$currentTurn);
				$output -= $painLevel;
			}
		}
		
		//add boost, if any
        foreach ($this->power as $power){
            if ($power->turn == TacGamedata::$currentTurn && $power->type == 2){
                $output += $power->amount;
            }        
        }        
		
        return $output;        
    } //endof function getOutput
	
	
	public function stripForJson(){
		//$this->output = $this->getOutput();	
        $strippedSystem = parent::stripForJson();
        $strippedSystem->output = $this->getOutput();	
		$strippedSystem->data = $this->data;	
        return $strippedSystem;
    }
	
}//endof class BioDrive


/*Shadow Pilot - replaces C&C
 - irrepairable!
 - no regular criticals
 - feels pain due to damage suffered by ship (temporary) and own wounds (permanent)
*/
class ShadowPilot extends CnC{
    public $name = "cnC";
    public $displayName = "C&C";
    public $primary = true;
	public $iconPath = "ShadowPilot.png";
	
	//irrepairable!
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    public $possibleCriticals = array(
    );
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
	
	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
/*deliberately skip inherited description		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
*/		
		$this->data["Special"] = 'Ship pilot. Cannot be repaired. Damage to ship results in temporary pain, damage to pilot results in permanent pain.';
		$this->data["Special"] .= '<br>Pain reduces Initiative, Thrust and accuracy of fire.';
	}
	
	
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if($this->isDestroyed()) return; //destroyed system does not work... but other critical phase effects may work even if destroyed!
		
		$damageSufferedThisTurn = 0;
		$damageToSelfThisTurn = 0;
		$onePainPer = 10; //1 point of pain per how many damage points?
		if ($ship->factionAge > 3) { //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
			$onePainPer = 20;//slow-grown Primordial ships are more resistant to pain		
		}
		
		//get all damage suffered THIS TURN - except tendrils. Ignore healing, damage dealing is painful even if it's mended afterwards!
		//ignore tendrils, that's not true damage!
		foreach ($ship->systems as $system) if (!($system instanceOf DiffuserTendril)){
			foreach ($system->damage as $dmg) if ( ($dmg->turn == $gamedata->turn) && ($dmg->damage > $dmg->armour)){
				$damageSufferedThisTurn += $dmg->damage - $dmg->armour;
				if($system->id == $this->id){
					$damageToSelfThisTurn += $dmg->damage - $dmg->armour;
				}
			}
		}
		
		//let's start pain in NEXT turn; criticals in FV usually start in CURRENT turn formally, but that causes readability to suffer during replays (originally there were no replays ;) )
		//pain can be caused by tendrils being broken - this is handled in EnergyDiffuser class, and rounded up (but doesn't sum with actual damage below)
		
		//temporary pain
		$painSuffered = round( $damageSufferedThisTurn/$onePainPer );
		for($i=1;$i<=$painSuffered;$i++){
			$crit = new ShadowPilotPain(-1, $ship->id, $this->id, 'ShadowPilotPain', $gamedata->turn+1, $gamedata->turn+1);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		}
		
		//permanent pain
		for($i=1;$i<=$damageToSelfThisTurn;$i++){
			$crit = new ShadowPilotPain(-1, $ship->id, $this->id, 'ShadowPilotPain', $gamedata->turn+1);
			$crit->updated = true;
			$this->criticals[] =  $crit;
		}
		
    } //endof function criticalPhaseEffects	
		
} //endof class ShadowPilot


/*Phasing Drive - essentially a jump engine that destroys ship if damaged while half-phasing*/
class PhasingDrive extends JumpEngine{
    public $displayName = "Phasing Drive";
    
	//JumpEngine enables half phasing, so I'm torn about priority... I'll increase to 2 over Jump Engine's 1
	public $repairPriority = 2;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'If damaged while half-phasing - entire ship is destroyed.';
    }
	
	//destroy ship if damaged while half-phaseing
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if (!Movement::isHalfPhased($ship, $gamedata->turn)) return;
		if (!$this->isDamagedOnTurn($gamedata->turn)) return; 
		
	
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!				
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				100, 100, 1, 1, 0,
				0,0,'HalfPhase',10000
			);
			$newFireOrder->pubnotes = "Phasing drive damaged during half-phasing - ship destroyed.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$primaryStruct = $ship->getStructureSystem(0);
		if($primaryStruct){			
            $remaining = $primaryStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primaryStruct->id, $remaining, 0, 0, -1, true, false, "", "HalfPhase");
            $damageEntry->updated = true;
            $primaryStruct->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $ship->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }	
    } //endof function criticalPhaseEffects	
}//endof class PhasingDrive







/*Gaim damage absorbtion system
Cannot be hit directly in any way, except when absorbing damage. May protect any system on the same section (plus structure it's fitted on, even if it's primary structure - important for MCVs) 
as Bulkhead's activation is automatic, it will kick in when:
 - would prevent system destruction
 - system is Structure and would be destroyed without Bulkhead (even if it doesn't prevent destruction)
 - related Structure is under 34% (it's "use it or lose it" time)
*/
class Bulkhead extends ShipSystem{
    public $name = "Bulkhead";
    public $displayName = "Bulkhead";
    public $iconPath = "bulkhead.png";
	public $isTargetable = false; //cannot be targeted by called shots
	
	public $repairPriority = 1;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    public $possibleCriticals = array( ); //no critical effect applicable	
	
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }

	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);     
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Absorbs damage from hits on the same section - activation is automatic.";
		$this->data["Special"] .= "<br>Will kick in when it can prevent system destruction or when sections' structural integrity falls too low.";
	}	
	
     public function getOutput(){ //output = remaining health - just for visual purposes
        $output = $this->getRemainingHealth();     
        return $output;        
    }    
	
	
	//function estimating how good this Bulkhead is at stopping damage;
	public function doesProtectFromDamage($expectedDmg, $systemProtected = null, $damageWasDealt = false) {
		//first do check whether this system can be protected! (same location or appropriate structure location)
		if ($systemProtected) {
			//is it on the same section?
			if ( ($this->location != $systemProtected->location) //different location...
			    && ($this->structureSystem !== $systemProtected ) //and this isn't appropriate structure either!
			 ) return 0;
		}else { //no particular system indicated = cannot protect
			return 0;	
		}
		//now check whether it _should_ protect...
		$targetHealth = 1;
		if($systemProtected){
			$targetHealth = $systemProtected->getRemainingHealth();
		}
		$ownHealth = $this->getRemainingHealth();
		$structureHealthFraction = $this->structureSystem->getRemainingHealth() / $this->structureSystem->maxhealth;
		$protectionValue = 0;
		if ( ($targetHealth <= $expectedDmg) && ($targetHealth + $ownHealth > $expectedDmg) ){
			$protectionValue = $targetHealth + $ownHealth - $expectedDmg; //I cannot prioritize smaller Bulkhead if it'd do the job, but at least I can avoid prioritizing larger one
		} else if ( (($targetHealth - $expectedDmg) <= 12) && ($this->structureSystem == $systemProtected)) { //Structure is hit - and is expected to fall to or below 12 points after hit, do protect
			$protectionValue = $ownHealth;
		} else if ($structureHealthFraction < 0.34) { //structure health is low, do protect for fear of not using the bulkhead at all 
			$protectionValue = $ownHealth;
		}
		return $protectionValue;
	}
	//actual protection
	public function doProtect($gamedata, $fireOrder, $target, $shooter, $weapon, $systemProtected, $effectiveDamage,$effectiveArmor){ //hook for actual effect of protection - return modified values of damage and armor that should be used in further calculations
		$returnValues=array('dmg'=>$effectiveDamage, 'armor'=>$effectiveArmor);
		$damageToAbsorb=$effectiveDamage-$effectiveArmor;
		$damageAbsorbed=0;
		
		if($damageToAbsorb<=0) return $returnValues; //nothing to absorb
		
		$ownHealth = $this->getRemainingHealth();
		$damageAbsorbed = min($damageToAbsorb,$ownHealth); 
				
		
		$noOverkill = (!$weapon->doOverkill) && ($weapon->noOverkill || ($weapon->damageType == 'Piercing'));
		if($noOverkill){//shot is incapable of overkilling - reducing it would not matter if it doesn't prevent destruction of system hit
			$remainingHealth = $systemProtected->getRemainingHealth();
			if ($remainingHealth+$damageAbsorbed <= $damageToAbsorb) return $returnValues; //any absorbtion would be futile and just destroy the bulkhead uselessly
		}
		
		if($damageAbsorbed>0){ //can absorb something!
			$returnValues['dmg']=$effectiveDamage-$damageAbsorbed;
			$bulkheadDestroyed = false;
			if ($damageAbsorbed >=$ownHealth) $bulkheadDestroyed = true;
			//mark damage (possibly destruction) on bulkhead itself
			$damageEntry = new DamageEntry(-1, $target->id, -1, $gamedata->turn, $this->id, $damageAbsorbed, 0, 0, -1, $bulkheadDestroyed, false, "Absorb!", "Bulkhead");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;
		}
		
		return $returnValues;
	}
	
	
	public function stripForJson(){
		//$this->output = $this->getOutput();	
        $strippedSystem = parent::stripForJson();
        $strippedSystem->output = $this->getOutput();
        return $strippedSystem;
    }
} //endof Bulkhead








/*Vorlon energy generating/storing system
it should replace Reactor, in FV I think it would be better when Reactor just coordinates with Capacitor!
actual power shenanigans are almost entirely in front end!
*/
class PowerCapacitor extends ShipSystem{ 
    public $name = "powerCapacitor";
    public $displayName = "Power Capacitor";
    public $primary = true; 
	public $isPrimaryTargetable = false;
    public $iconPath = "PowerCapacitor.png";
	
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	//power held
	public $powerCurr = 0;
	public $capacityBonus = 0; //additional capacity - potentially set by enhancements
	public $powerReceivedFromFrontEnd = 0; //communication variable	
	public $powerReceivedFromBackEnd = 0; //communication variable
	public $nominalOutput = 0;//output to be used in front end display!
	
	//petals opening - done as boost of Capacitor!
    public $boostable = false; //changed to True if a given ship has Petals! 
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;
	
/*
	1-17: No effect.
18-22: -1 to recharge rate.
23-27: -2 to recharge rate and the
capacitor loses one half (drop fractions) of
the energy it is currently holding.
28+: -4 to recharge rate and the
capacitor is completely emptied.
*/    
    public $possibleCriticals = array(
		18=>"OutputReduced1",
		23=>array("OutputReduced2","ChargeHalve"), //multiple instances of OutputReduced - should scale fine with self-repair, rather than higher repair cost
		28=>array("OutputReduced2", "OutputReduced2","ChargeEmpty")//multiple instances of OutputReduced - should scale fine with self-repair, rather than higher repair cost
	); 
    

    function __construct( $armour, $maxhealth, $powerReq, $output, $hasPetals = true  ){ //technical object, does not need typical system attributes (armor, structure...)
        parent::__construct( $armour, $maxhealth, $powerReq, $output ); //$armour, $maxhealth, $powerReq, $output	
		$this->boostable = $hasPetals;
    }
	
	public function getMaxCapacity(){ //maximum capacity = health remaining + bonus (bonus only if there is no damage!)
		$capacity = $this->getRemainingHealth();
		$capacity += $this->capacityBonus ;
		return $capacity;
	}
	
	
	public function setPowerHeld($newValue){ //cut off by maximum capacity
		//$this->powerCurr = min($newValue, $this->getMaxCapacity() ); //cutting off at this point interacts badly with enhancements... moving to FRONT END!
		$this->powerCurr = $newValue;
	}
	
	


	/* this method generates additional non-standard informaction in the form of individual system notes
	in this case: 
	 - Deployment phase: fill to full
	 - Initial phase: may be changed in front end (boosting Capacitor and/or systems)
	 - Firing phase: may be changed in FRONT END (firing costs power!) - actually belay that, only BACK END will know whether firing actually happened!
	 - Firing phase: may be changed in BACK END as well (intercepting costs power! - intercept-capable weapons will have appropriate checks in place to see they don't overextax the capacitor)
	 Save always current stored power, not the changes that led to this value.
	 
	 CHANGES COMPARED TO OFFICIAL VERSION:
	  - recharge occurs in Initial phase (official - just before movement, which makes power not usable in Initial phase)
	  - opening petals reduces armor of all systems by 2 (official - armor is reduced on PRIMARY only, but all profiles are increased by 1)
	  - cannot icrease recharge rate in any other way (official - can shut down everything (weapons, shields) to increase by 100%)
	*/
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
				case -1: //deployment phase 
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise two copies of initial data are written
						$this->setPowerHeld($this->getMaxCapacity());
						//AND PREPARE APPROPRIATE NOTES!		
						$notekey = 'powerStored';
						$noteHuman = 'Power Capacitor - stored power';
						$noteValue = $this->powerCurr;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
								
				case 1: //Initial phase
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						/*in this case - result from front end completely replaces current value, so it's NOT necessary to read old notes!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);
							}
						}
						$this->onIndividualNotesLoaded($gameData);
						*/
						$this->setPowerHeld($this->powerReceivedFromFrontEnd); 
						//AND PREPARE APPROPRIATE NOTES!		
						$notekey = 'powerStored';
						$noteHuman = 'Power Capacitor - stored power';
						$noteValue = $this->powerCurr;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					}
					break;
				
				case 4: //firing phase
					//take what front end reports, and add what back end calculated (basically weapons fire cost)
					//$this->setPowerHeld($this->powerReceivedFromFrontEnd + $this->powerReceivedFromBackEnd); 
					//or perhaps disregard what front end says - in this phase it's cost of firing... and this is better calculated by back end (firing _declaration_ doesn't equal actual firing, especially for Ligntning Cannons!
					$this->setPowerHeld($this->powerCurr - $this->powerReceivedFromBackEnd); 
					//apply critical eefects: halve charge/empty charge
					if($this->hasCritical("ChargeEmpty")){
						$this->setPowerHeld(0); 
					}else if ($this->hasCritical("ChargeHalve")){
						$this->setPowerHeld(floor($this->powerCurr/2)); 
					}
					//AND PREPARE APPROPRIATE NOTES!		
					$notekey = 'powerStored';
					$noteHuman = 'Power Capacitor - stored power';
					$noteValue = $this->powerCurr ;
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
					break;
		}
	} //endof function generateIndividualNotes
	
	public function canDrawPower($powerNeeded){
		if(($this->powerCurr - $this->powerReceivedFromBackEnd) >= $powerNeeded){
			return true; //drawing such power is possible
		}else{
			return false; //cannot draw so much power!
		}
	}
	
	//it should not happen, but technically it's possible to actually draw more power than Capacitor holds...
	public function doDrawPower($powerDrawn){
		$this->powerReceivedFromBackEnd += $powerDrawn;
	}
	
	/*act on notes just loaded - to be redefined by systems as necessary
	 - set power held
	*/
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //assume ASCENDING sorting - so enact all changes as is
			switch($currNote->notekey){
				case 'powerStored': //power that should be stored at this moment
					$this->setPowerHeld($currNote->notevalue);
					break;			
			}
		}
	} //endof function onIndividualNotesLoaded
	
	
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn); 
		$this->powerMax = $this->getMaxCapacity(); //do cut off overflow here as well!
		$this->powerCurr =min($this->powerCurr, $this->powerMax);
		$this->data["Power stored/max"] =  $this->powerCurr . '/' . $this->powerMax;
		$this->data["Power regeneration"] =  'Initial phase only';
        $this->data["Special"] = "This system is responsible for generating and storing power (Reactor is nearby for technical purposes).";	   
		if ($this->boostable){
			$this->data["Special"] .= "<br>You may boost this system (open petals) to increase recharge rate by 50% - at the cost of treating all armor values as 2 points lower.";
		}
		$this->data["Special"] .= "<br>Destroying Capacitor disables (but does not destroy) the ship.";
    }
	
	public function beforeFiringOrderResolution($gamedata){ //actually mark armor reduced temporary critical if Petals are open
		$boostlevel = $this->getBoostLevel($gamedata->turn);
		if ($boostlevel <1) return; //not boosted - no crit!
		$ship = $this->unit;
		foreach($ship->systems as $system){		
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->criticals[] =  $crit;
			$crit = new ArmorReduced(-1, $ship->id, $system->id, "ArmorReduced", $gamedata->turn, $gamedata->turn);
			$crit->updated = true;
			$crit->inEffect = true;
			$system->criticals[] =  $crit;
		}
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
	
    public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
        $strippedSystem->powerCurr = max($this->powerCurr,0); //power less than 0 would block the game in case of this system
	    $strippedSystem->powerMax = $this->getMaxCapacity();
		$strippedSystem->nominalOutput = $this->output;
		//$strippedSystem->powerReceivedFromFrontEnd = $this->powerReceivedFromFrontEnd;
		$strippedSystem->individualNotesTransfer = $this->individualNotesTransfer;
        return $strippedSystem;
    }
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for it in powerReceivedFromFrontEnd
		//in this case it should be just one entry, power remaining
		if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $powerLeft)  $this->powerReceivedFromFrontEnd = $powerLeft;
		$this->individualNotesTransfer = array(); //empty, just in case
	}		
	
	
	//upon destruction (ship should be completely disabled) go for:
	// - add Power reduction critical to Reactor (so ship goes out of control) 
	// - add SelfRepair output reduction critical (so the damage isn't just repaired in a few turns ;) ).
	public function criticalPhaseEffects($ship, $gamedata)
    { 
		if (!$this->isDamagedOnTurn($gamedata->turn)) return; 
		if (!$this->isDestroyed()) return;		
		
		$reactor = $ship->getSystemByName("Reactor"); //by class name
		if($reactor){
			$reactor->addCritical($ship->id, "OutputReduced4", $gamedata);
			$reactor->addCritical($ship->id, "OutputReduced4", $gamedata);
			$reactor->addCritical($ship->id, "OutputReduced4", $gamedata);
		}
		
		$selfRepairList = $ship->getSystemsByName("Self Repair", true);//by readable name
		foreach($selfRepairList as $selfRepair){
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
			$selfRepair->addCritical($ship->id, "OutputReduced4", $gamedata);
		}
    } //endof function criticalPhaseEffects	
							
} //endof PowerCapacitor

class StructureTechnical extends ShipSystem{
    public $name = "StructureTechnical";
    public $displayName = "Structure Technical";
    public $iconPath = "StructureTechnical.png";    
    
	//Cannot be repaired
	public $repairPriority = 0;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
 
     public function getArmourInvulnerable($target, $shooter, $dmgClass, $pos=null){ //this thruster should be invulnerable to anything...
		$activeAA = 99;
		return $activeAA;
    }
    
    public function setSystemDataWindow($turn){
	parent::setSystemDataWindow($turn);     
	$this->data["Special"] = "This system is here for technical purposes only. Cannot be damaged in any way.";
	}  
	
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
	public $isTargetable = false; //cannot be targeted ever!
	
   function __construct($armour, $maxhealth, $powerReq, $output){
	    parent::__construct(0, 1, 0, 0); //$armour, $maxhealth, $powerReq, $output
		}
      
}//endof VreeStructurePlaceholder	




?>
