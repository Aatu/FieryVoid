<?php

class Jammer extends ShipSystem implements SpecialAbility{    
    public $name = "jammer";
    public $displayName = "Jammer";
    public $specialAbilities = array("Jammer");
    public $primary = true;
	
	//Jammer is very important, being the primary defensive system!
	public $repairPriority = 10;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    protected $possibleCriticals = array(16=>"PartialBurnout", 23=>"SevereBurnout");
    
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
        parent::setSystemDataWindow($turn);
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
    
    protected $possibleCriticals = array(
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
        parent::setSystemDataWindow($turn);
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

class Reactor extends ShipSystem implements SpecialAbility {
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
    
    protected $possibleCriticals = array(
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
		if(!isset($this->data["Special"])){
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
        $this->data["Special"] .= "Can be set to overload, self-destroying ship after Firing phase.";	     
    }
	
    public function markPowerFlux(){
        $this->specialAbilities[] = "ReactorFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= '<br>Power fluctuations. Each turn, the reactor rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }

	public function effectCriticals(){
		parent::effectCriticals();

	if (TacGamedata::$currentPhase <= 1) { 
		//account for Plasma Batteries present (if any)
		foreach ($this->unit->systems as $system)
			if ($system instanceof PlasmaBattery){
			$this->outputMod += $system->getOutput(); //outputMod is SUBTRACTED from base output, hence going for negative value here
			}
		}
	}	
	
	public function criticalPhaseEffects($ship, $gamedata) {		
		//as effects are getting complicate - call them separately
		$this->executeContainmentBreach($ship, $gamedata);	
		$this->executeReactorFlux($ship, $gamedata);	
		$this->destroyShipOnDestruction($ship, $gamedata); //destroy ship on Reactor destruction
	}//endof function criticalPhaseEffects

	public function executeReactorFlux($ship, $gamedata) {		
		if ($this->isDestroyed()) return; //no point if Reactor is actually destroyed already
		$hasPowerFlux = $ship->hasSpecialAbility("ReactorFlux");
		if ($hasPowerFlux) {
			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage
			if($roll >= 11 && $roll < 15){ // Output reduced by 2 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=15 && $roll < 19) { // Output reduced by 4 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced4(-1, $this->unit->id, $this->id, "OutputReduced4", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=19 && $roll < 27) { // Output reduced by 8 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced8(-1, $this->unit->id, $this->id, "OutputReduced8", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=27) { // Output reduced by 10 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced10(-1, $this->unit->id, $this->id, "OutputReduced10", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			}			
		}	
	}	//endof function executeReactorFlux
	
	//in case of containment breach - roll whether reactor explodes
	public function executeContainmentBreach($ship, $gamedata)
    { 
		if ($this->isDestroyed()) return; //no point if Reactor is actually destroyed already
		if (!$this->hasCritical("ContainmentBreach")) return; //no Containment Breach, everything is fine
			
		$explodeRoll = Dice::d(100);
		$chance = $this->getTotalDamage();
			
		//try to make actual attack to show in log - use Ramming Attack system!	- even if there is no explosion			
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		$newFireOrder=null;
		if($rammingSystem){ //actually exists! - it should on every ship!
			$shotsHit = 0;
			if ($explodeRoll <= $chance) { //actual explosion
				$shotsHit = 1;
			}
			$newFireOrder = new FireOrder(
				-1, "normal", $ship->id, $ship->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				$chance, $explodeRoll, 1, $shotsHit, 0,
				0,0,'ContainmentBreach',10000
			);
			$newFireOrder->pubnotes = "Containment Breach - reactor explosion. Chance $chance %, roll $explodeRoll.";
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
    } //endof function executeContainmentBreach
	
	

	//destroy PRIMARY section if Reactor is destroyed
	public function destroyShipOnDestruction($ship, $gamedata)
    {
		if (!$this->isDestroyed()) return; //Reactor is not destroyed, no need to act
		if ($this->unit->isDestroyed()) return; //entire ship is already destroyed, no need to act
	
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $ship->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!	
			//check whether firing order by RammingAttack vs own ship already exists!
			$newFireOrder = null;
			$fOrders = $rammingSystem->getFireOrders($gamedata->turn);
			foreach($fOrders as $fOrderAct){
				if($fOrderAct->targetid = $ship->id)
				{
					$newFireOrder = $fOrderAct;
					break; //foreach
				}
			}		
			if($newFireOrder){ //already exists, add to it!
				$newFireOrder->pubnotes .= "<br>";
			}else {//need actual new order!
				$newFireOrder = new FireOrder(
					-1, "normal", $ship->id, $ship->id,
					$rammingSystem->id, -1, $gamedata->turn, 1, 
					100, 100, 1, 1, 0,
					0,0,'Reactor',10000
				);
				$newFireOrder->addToDB = true;
				$rammingSystem->fireOrders[] = $newFireOrder;
			}
			$newFireOrder->pubnotes .= "Reactor destroyed - entire ship is immolated.";
		}else{
			$newFireOrder=null;
		}

		//destroy primary structure
		$primaryStruct = $this->unit->getStructureSystem(0);
		if($primaryStruct){			
            $remaining = $primaryStruct->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primaryStruct->id, $remaining, 0, 0, -1, true, false, "", "Reactor");
            $damageEntry->updated = true;
            $primaryStruct->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $ship->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }	
    } //endof function destroyShipOnDestruction
	
	
} //endof Reactor



class MagGravReactor extends Reactor{
/*Mag-Gravitic Reactor, as used by Ipsha (Militaries of the League 2);
	provides fixed power regardless of systems;
	techical implementation: count as Power minus power required by all systems enabled
*/	
	protected $possibleCriticals = array( //different set of criticals than standard Reactor
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

class AdvancedSingularityDrive extends Reactor{
/*Advanced version of Mag-Gravitic Reactor, used by custom Thirdspace faction;
	provides fixed power regardless of systems;
	techical implementation: count as Power minus power required by all systems enabled
*/	
    public $iconPath = "AdvancedSingularityDrive.png";
    
	protected $possibleCriticals = array( //different set of criticals than standard Reactor
		20=>"FieldFluctuations",
		25=>array("FieldFluctuations", "FieldFluctuations"),
		30=>array("FieldFluctuations", "FieldFluctuations", "FieldFluctuations")
	);
	
	function __construct($armour, $maxhealth, $powerReq, $output ){
		parent::__construct($armour, $maxhealth, $powerReq, $output );    
		$this->fixedPower = true;
	}
	
	public function setSystemDataWindow($turn){
		$this->data["Output"] = $this->output;
		parent::setSystemDataWindow($turn);     
		$this->data["Special"] .= "<br>Advanced Mag-Gravitic Reactor: provides fixed total power, regardless of destroyed systems.";
	}	
	
}//endof AdvancedSingularityDrive		


//warning: needs external code to function properly. Intended for starbases only.
/* let's disable it - all use changed to SubReactorUniversal!
class SubReactor extends ShipSystem{	
	//SubReactor is very important, though not as much as primary reactor itself!
	public $repairPriority = 8;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
	
    public $name = "reactor";
    public $displayName = "Reactor";
    public $outputType = "power";
    public $primary = false;
    
    protected $possibleCriticals = array(
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
    	
    protected $possibleCriticals = array(
        11=>"OutputReduced1", 
        14=>"OutputReduced2",
        17=>"OutputReduced3",
        21=>"OutputReduced4" //lower but also smoother
    );
		
	/*main reactor criticals for comparision
    protected $possibleCriticals = array(
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


class Engine extends ShipSystem implements SpecialAbility {
    public $name = "engine";
    public $displayName = "Engine";
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    public $outputType = "thrust";
	
	//Engine  is fairly important, being a core system!
	public $repairPriority = 7;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    
    
    protected $possibleCriticals = array(
	//official: 15-20 -2, 21-27 either all ahead full or shutdown, 28+ both
        15=>"OutputReduced2",
        21=>"ForcedOfflineOneTurn",
        28=>array("ForcedOfflineOneTurn", "OutputReduced2")
	/*old crits
        15=>"OutputReduced2",
        21=>"OutputReduced4",
        28=>"ForcedOfflineOneTurn"
		*/
		);
    
    function __construct($armour, $maxhealth, $powerReq, $output, $boostEfficiency, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->thrustused = (int)$thrustused;
        $this->boostEfficiency = (int)$boostEfficiency;
    }
	
    public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);   
		$this->data["Own thrust"] = $this->output;
    }
	
    public function markEngineFlux(){
        $this->specialAbilities[] = "EngineFlux";
        $this->specialAbilityValue = true; //so it is actually recognized as special ability!
        if (!isset($this->data["Special"])) {
            $this->data["Special"] = '';
        }else{
            $this->data["Special"] .= '<br>';
        }
        $this->data["Special"] .= 'Engine fluctuations. Each turn, the engine rolls for a critical, with a +5% penalty. Any effects last only 1 turn.';
    }

    public function getSpecialAbilityValue($args)
    {
        return $this->specialAbilityValue;
    }

	public function criticalPhaseEffects($ship, $gamedata) {
		
		$hasEngineFlux = $ship->hasSpecialAbility("EngineFlux");

		if ($hasEngineFlux) {

			$roll = Dice::d(20) + 1 + $this->getTotalDamage();  //There is a +1 penalty in addition to any damage

			if($roll >= 15 && $roll < 21){ // Output reduced by 2 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced2(-1, $this->unit->id, $this->id, "OutputReduced2", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			}elseif ($roll >=21){
				$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			}
/*this is based on old crit chart!
			elseif ($roll >=21 && $roll < 28) { // Output reduced by 4 for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new OutputReduced4(-1, $this->unit->id, $this->id, "OutputReduced4", $gamedata->turn, $finalTurn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
			} elseif ($roll >=28) { // Forced offline for one turn
				$finalTurn = $gamedata->turn + 1;
				$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
				$crit->updated = true;
				$crit->newCrit = true; // force save even if crit is not for current turn
				$this->criticals[] =  $crit;
            }
*/
			
		}
		
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
    
    protected $possibleCriticals = array(
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
	
	public function markThirdspace(){		
    	$this->specialAbilities[] = "AdvancedSensors";
		$this->specialAbilityValue = true; //so it is actually recognized as special ability!
    	$this->boostEfficiency = 14; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
    	$this->maxBoostLevel = 2; //Unlike Shadows/Vorlons Thirdspace ships have alot of spare power, so limit their max sensor boost for balance. 		
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Advanced Sensors - ignores Jammer.';//not that of advanced races
		$this->data["Special"] .= "<br>Ignores enemy BDEW and SDEW."; //not that of advanced races
		$this->data["Special"] .= "<br>Ignores any defensive systems lowering enemy profile (shields, EWeb...)."; //not that of advanced races
		$this->data["Special"] .= "<br>All of the above work as usual if operated by advanced races.";
		$this->data["Special"] .= "<br>Can only be boosted twice.";	 
	}	
		