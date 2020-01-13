<?php

class Jammer extends ShipSystem implements SpecialAbility{    
    public $name = "jammer";
    public $displayName = "Jammer";
    public $specialAbilities = array("Jammer");
    public $primary = true;
    
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
        
        if (! ($shooter instanceof BaseShip) || ! ($target instanceof BaseShip))
            throw new InvalidArgumentException("Wrong argument type for Jammer getSpecialAbilityValue");
        
        if ($shooter->team === $target->team)
            return 0;
        
        return $this->getOutput();
    }

     public function setSystemDataWindow($turn){
        $this->data["Special"] = "Denies a hostile OEW-lock versus this ship.";
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
            $this->data["DEFENSIVE BONUS:"] = "Jammer ability if targeted from over 5 hexas away.";
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
        
        if (Mathlib::getDistanceOfShipInHex($shooter, $target) > 5)
        {
            return 1;
        }            
        
        return 0;        
    }     
} //endof Stealth

class Fighterimprsensors extends ShipSystem implements SpecialAbility{    
    public $name = "fighterimprsensors";
    public $displayName = "Improved Sensors";
    public $iconPath = "scanner.png";
    public $specialAbilities = array("ImprovedSensors");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
            $this->data["Special"] = "Halves effectiveness of enemy Jammer.";
        }
    
    public function getSpecialAbilityValue($args)
    {     
        return 1; //Improved Sensors just are       
    }     
} //endof Improved Sensors

class Fighteradvsensors extends ShipSystem implements SpecialAbility{    
    public $name = "Fighteradvsensors";
    public $displayName = "Advanced Sensors";
    public $iconPath = "scanner.png";
    public $specialAbilities = array("AdvancedSensors");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
    
    public function setSystemDataWindow($turn){
            $this->data["Special"] = "Negates enemy Jammer.";
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
    
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
		$this->tohitPenalty = $this->getOutput();
		$this->damagePenalty = $this->getOutput();
    }
    
    private function checkIsFighterUnderShield($target, $shooter){
	if(!($shooter instanceof FighterFlight)) return false; //only fighters may fly under shield!
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
        
        if ($this->checkIsFighterUnderShield($target, $shooter))
            return 0;

        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
    }
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn())
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter))
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
	
    public $boostable = true; //for reactor overload feature!
    public $maxBoostLevel = 1;
    public $boostEfficiency = 0;
    
    public $possibleCriticals = array(
        11=>"OutputReduced2",
        15=>"OutputReduced4",
        19=>"OutputReduced8",
        27=>array("OutputReduced10", "ForcedOfflineOneTurn"));
    
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


class SubReactor extends ShipSystem{
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

class Engine extends ShipSystem{
    public $name = "engine";
    public $displayName = "Engine";
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    public $outputType = "thrust";
    
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

class Scanner extends ShipSystem{
    public $name = "scanner";
    public $displayName = "Scanner";
    public $primary = true;
    public $boostable = true;
    public $outputType = "EW";
    
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
			
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Improved Sensors - halve Jammer effectiveness (to hit penalty and launch range penalty).';
		
	}
	/*note: as there are no actual Advanced Sensors unit yet, this feature is not complete;
		at the moment Advanced Sensors do have flat boost cost and do ignore Jammer, but do NOT yet ignore SDEW/BDEW as they should
	*/
	public function markAdvanced(){		
    		$this->specialAbilities[] = "AdvancedSensors";
		$this->boostEfficiency = 14; //Advanced Sensors are rarely lower than 13, so flat 14 boost cost is advantageous to output+1!
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Advanced Sensors - negate Jammer, flat 14 boost cost.';
	}	
	/*note: StarWarsSensors mark in itself doesn't do anything beyond being recognizable for ship description function
		all actual effects are contained in attribute changes
	*/
	public function markStarWars(){		
    		$this->specialAbilities[] = "StarWarsSensors";
		$this->maxBoostLevel = 2;
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= 'Star Wars Sensors - boostability limited to +2.';
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
        $this->data["Special"] .= "<br> - SOEW: indicated friendly ship gets half of ElInt ships' OEW bonus. Target must be within 30 hexes of Scout at the moment of firing.";		     
        $this->data["Special"] .= "<br> - SDEW: boosts target's DEW (by 1 for 2 points allocated). Range 30 hexes";		     
        $this->data["Special"] .= "<br> - Blanket Protection: all friendly units within 20 hexes (incl. fighters) get +1 DEW per 4 points allocated. Cannot combine with other ElInt activities.";		     
        $this->data["Special"] .= "<br> - Disruption: Reduces target enemy ships' OEW and CCEW by 1 per 3 points allocated (split evenly between targets, CCEW being counted as one target; cannot bring OEW on a target below 0). ";	
    }
	/*
	public function markImproved(){	parent::markImproved();   }
	public function markAdvanced(){	parent::markImproved();	}
	*/
    public function getSpecialAbilityValue($args)
    {
        return true;
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


class CnC extends ShipSystem{
    public $name = "cnC";
    public $displayName = "C&C";
    public $primary = true;
    
    public $possibleCriticals = array(
    	//1=>"SensorsDisrupted", //not implemented! so I take it out for now
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


class CargoBay extends ShipSystem{
    public $name = "cargoBay";
    public $displayName = "Cargo Bay";
    
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
	
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
	    parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused );
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
    
    function __construct($armour, $maxhealth, $output = 6){
        parent::__construct($armour, $maxhealth, 0, $output ); 
    }
}


class Catapult extends ShipSystem{
    public $name = "catapult";
    public $displayName = "Catapult";
    public $squadrons = Array();
    public $primary = true;
    
    function __construct($armour, $maxhealth, $output = 6){
        parent::__construct($armour, $maxhealth, 0, $output );
 
    }
}


class JumpEngine extends ShipSystem{
    public $name = "jumpEngine";
    public $displayName = "Jump Engine";
    public $delay = 0;
    public $primary = true;
    
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
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
    }
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
						$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $trgtStructure->id, $toDeal, 0, 0, $damage->fireorderid, $destroyed, "Connection Strut!", $damage->damageclass);
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
							$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $primary->id, $toDeal, 0, 0, $damage->fireorderid, $destroyed, "Connection Strut!", $damage->damageclass);
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
    public $iconPath = "adaptiveArmorController.png";
	
	public $AAtotal = 0;
	public $AAtotal_used = 0;
	public $AApertype = 0;
	public $AApreallocated = 0;
	public $AApreallocated_used = 0;
	
	public $allocatedAA = array(); //AA points allocated for given damage type
	public $availableAA = array(); //AA points available for allocation for given damage type
	
	
    
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
    public function generateIndividualNotes($gameData){
		$ship = $this->getUnit();
		switch($gameData->phase){
				case 0: //deployment phase
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
				break;
			
				case 3: //firing phase
					foreach($ship->systems as $system) foreach($system->damage as $dmg) if($dmg->updated == true)/*if($dmg->turn==$gameData->turn)*/{//damage suffered this turn
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
					//AND PREPARE APPROPRIATE NOTES!
					//	'preallocatedUsed' public $AApreallocated_used = 0;	
					$notekey = 'preallocatedUsed';
					$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,'Adaptive Armor: preallocated points used',$this->AApreallocated_used);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
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
						/* AA is not SET here, so no need to mark it!
						$notekey = 'set' . $dmgType;
						$noteHuman = 'Adaptive Armor set (for dmg type)';
						$noteValue = $ptsSet;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						*/
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
				case 'preallocatedUsed': //total number of preallocatable points that were actually preallocated
					$this->AApreallocated_used = $currNote->notevalue;
					break;
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
			$this->data[$dmgType] =  $AAallocated . '/' . $AAavailable;
		}
        $this->data["Special"] = "This system is responsible for Adaptive Armor settings management.";	   
        $this->data["Special"] .= "<br>You may assign AA points in Initial phase.";
        $this->data["Special"] .= "<br>Pre-set AA points may be used in turn 1 only.";
        $this->data["Special"] .= "<br>AA points set in previous turns cannot be unassigned.";
    }
	
	/*always redefine $this->data for AA controller! A lot of variable information goes there...*/
	public function stripForJson(){
        $strippedSystem = parent::stripForJson();
        $strippedSystem->data = $this->data;
		
        return $strippedSystem;
    }
	
} //endof AdaptiveArmorController

?>
