<?php

class ElintArray extends ShipSystem implements SpecialAbility{
    public $name = "elintArray";
    public $displayName = "ELINT array";
    public $specialAbilities = array("ELINT");
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 0);
    }
    
    public function getSpecialAbilityValue($args)
    {
        return true;
    }
    
}

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
        $this->data["Ability:"] = "Denies a hostile OEW-lock versus this ship.";
    }

}

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
        Debug::log("calling stealth getSpecialAbilityValue");
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
     
}

interface SpecialAbility{
 
    public function getSpecialAbilityValue($args);
}

interface DefensiveSystem{

    public function getDefensiveType();
     
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn);
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn);
    
    
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
        $dis = mathlib::getDistanceOfShipInHex($target, $shooter);
            
        if ( $dis == 0 && ($shooter instanceof FighterFlight)){
            // If shooter are fighers and range is 0, they are under the shield
            return true;
        }
        return false;
    }
    
    public function getDefensiveType()
    {
        return "Shield";
    }
    
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn($turn))
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter))
            return 0;

        $output = $this->output;
        $output -= $this->outputMod;
        return $output;
    }
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn){
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn())
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter))
            return 0;
        
        if ($this->hasCritical('DamageReductionRemoved'))
            return 0;
        
        $output = $this->output;
        $output -= $this->outputMod;
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
        
        $this->boostEfficiency = $output;
    }    
}

class Reactor extends ShipSystem{

    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    public $outputType = "power";
    
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
            foreach($ship->systems as $system){
                if($system->powerReq > 0){
                    $system->addCritical($shipid, $phpclass, $gamedata);
                }
            }
        }

        parent::addCritical($shipid, $phpclass, $gamedata);
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
    
}

class ElintScanner extends Scanner implements SpecialAbility{
    public $name = "elintScanner";
    public $displayName = "ELINT Scanner";
    public $specialAbilities = array("ELINT");
    public $iconPath = "elintArray.png";

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }
    
    public function getSpecialAbilityValue($args)
    {
        return true;
    }
}

class CnC extends ShipSystem{

    public $name = "CnC";
    public $displayName = "C&C";
    public $primary = true;
    
    public $possibleCriticals = array(
    1=>"SensorsDisrupted", 
    9=>"CommunicationsDisrupted", 
    12=>"PenaltyToHit", 
    15=>"RestrictedEW",
    18=>array("ReducedIniativeOneTurn","ReducedIniative"), 
    21=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative"), 
    24=>array("RestrictedEW","ReducedIniative","ShipDisabledOneTurn"));
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    

    }
}

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
    
    public $possibleCriticals = array(15=>"FirstThrustIgnored", 20=>"HalfEfficiency", 25=>array("FirstThrustIgnored","HalfEfficiency"));
    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
    
    }
}

class GraviticThruster extends Thruster{
    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused);
    }
    
    public $firstCriticalIgnored = false;
    
    public function onAdvancingGamedata($ship)
    {
        SystemData::addDataForSystem(
            $this->id, 0, $ship->id,
            '"firstCriticalIgnored":{"1":"'.$this->firstCriticalIgnored.'"}');
        
        parent::onAdvancingGamedata($ship);
    }
    
    public function setSystemData($data, $subsystem)
    {
        $array = json_decode($data, true);
        if (!is_array($array))
            return;
        
        foreach ($array as $i=>$entry)
        {
            if ($i == "firstCriticalIgnored"){
                $this->firstCriticalIgnored = $entry[1];
            }
        }
        
        parent::setSystemData($data, $subsystem);
    }
    
    public function addCritical($shipid, $phpclass, $gamedata)
    {
        if (! $this->firstCriticalIgnored)
        {
            $this->firstCriticalIgnored = true;
            Debug::log("Gravitic thruster ignored first critical (shipid: $shipid systemid: $this->id");
            return null;
        }
        
        Debug::log("Gravitic thruster got critical (shipid: $shipid systemid: $this->id");
            
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
    public $displayName = "Jump engine";
    public $delay = 0;
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq, $delay){
        parent::__construct($armour, $maxhealth, $powerReq, 0);
    
        $this->delay = $delay;
    }
}


class Structure extends ShipSystem{

    public $name = "structure";
    public $displayName = "Structure";

    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
         
    
    }

}

