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
    
    //public $possibleCriticals = array(16=>"PartialBurnout", 23=>"SevereBurnout");
    
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
            Debug::log("return 1");
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
    
    //public $possibleCriticals = array(16=>"StrReduced", 20=>"EffReduced", 25=>array("StrReduced", "EffReduced"));

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor);
        
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
    }
    
    public function onConstructed($ship, $turn, $phase){
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
        
        if ($this->checkIsFighterUnderShield($target, $shooter))
            return 0;
        
        return $this->output;
    }
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn){
        if ($this->checkIsFighterUnderShield($target, $shooter))
            return 0;
        
        return $this->output;
    }
}


class Reactor extends ShipSystem{

    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    public $outputType = "Power";
    
    public $possibleCriticals = array(11=>"OutputReduced2", 15=>"OutputReduced4", 19=>"OutputReduced6", 27=>"OutputReduced8", 100=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        
        
    }
}

class Engine extends ShipSystem{

    public $name = "engine";
    public $displayName = "Engine";
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    
    public $possibleCriticals = array(15=>"OutputReduced2", 21=>"OutputReduced4", 27=>"ForcedOfflineOneTurn");
    
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
    
    public $possibleCriticals = array(15=>"OutputReduced2", 19=>"OutputReduced4", 23=>"OutputReduced6", 27=>"OutputReduced8");
        
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

class Thruster extends ShipSystem{

    public $name = "thruster";
    public $displayName = "Thruster";
    public $direction;
    public $thrustused;
    public $thrustwasted = 0;
    
    public $possibleCriticals = array(15=>"FirstThrustIgnored", 20=>"HalfEfficiency", 25=>array("FirstThrustIgnored","HalfEfficiency"));
    
    
    public $criticalDescriptions = array(
        
    
    );
    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
    
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

