<?php

class ElintArray extends ShipSystem{
    public $name = "elintArray";
    public $displayName = "ELINT array";
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 0);
    }
}

interface DefensiveSystem{

    public function getHitChangeMod($shooter, $pos, $turn);
    
    public function getDamageMod($shooter, $pos, $turn);
    
    
}

class Shield implements DefensiveSystem{
    public $name = "shield";
    public $displayName = "Shield";
    public $startArc = 0;
    public $endArc = 0;
    
    //defensive system
    public $defensiveType = "Shield";
    public $defensiveSystem = true;
    public $tohitPenalty = 0;
    public $damagePenalty = 0;
    
    public $possibleCriticals = array(16=>"StrReduced", 20=>"EffReduced", 25=>array("StrReduced", "EffReduced"));

    function __construct($armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc){
        // shieldfactor is handled as output.
        parent::__construct($armour, $maxhealth, $powerReq, $shieldFactor);
        
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
    }
    
    public function onConstructed($ship, $turn, $phase){
		$this->tohitPenalty = $this->output;
		$this->damagePenalty = $this->output;
     
    }
    
    private function checkIsFighterUnderShield($shooter){
        $dis = mathlib::getDistanceHex($this->getCoPos(), $shooter->getCoPos());
            
        if ( $dis == 0 && ($shooter instanceof FighterFlight)){
            // If shooter are fighers and range is 0, they are under the shield
            return true;
        }
        return false;
    }
    
    public function getHitChangeMod($shooter, $pos, $turn){
        
        if ($this->checkIsFighterUnderShield($shooter))
            return 0;
        
        return $this->output;
    }
    
    public function getDamageMod($shooter, $pos, $turn){
        if ($this->checkIsFighterUnderShield($shooter))
            return 0;
        
        return $this->output;
    }
}

class Jammer extends ShipSystem{
    
    public $name = "jammer";
    public $displayName = "Jammer";
    public $primary = true;
    
    public $possibleCriticals = array(16=>"PartialBurnout", 23=>"SevereBurnout");
    
    function __construct($armour, $maxhealth, $powerReq){
        parent::__construct($armour, $maxhealth, $powerReq, 1);
    }
     
}

class Reactor extends ShipSystem{

    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    
    public $possibleCriticals = array(11=>"OutputReduced2", 15=>"OutputReduced4", 19=>"OutputReduced6", 27=>"OutputReduced8", 100=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        
        
    }
}

class Engine extends ShipSystem{

    public $name = "engine";
    public $displayName = "Engine";
    public $engineEfficiency;
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    
    public $possibleCriticals = array(15=>"OutputReduced2", 21=>"OutputReduced4", 27=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $powerReq, $output, $engineEfficiency, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        $this->thrustused = (int)$thrustused;
        $this->engineEfficiency = (int)$engineEfficiency;
    }
    
}

class Scanner extends ShipSystem{

    public $name = "scanner";
    public $displayName = "Scanner";
    public $primary = true;
    public $boostable = true;
    
    public $possibleCriticals = array(15=>"OutputReduced2", 19=>"OutputReduced4", 23=>"OutputReduced6", 27=>"OutputReduced8");
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    }

    
    public function getScannerOutput($turn){

        if ($this->isOfflineOnTurn($turn))
            return 0;
            
        $output = $this->output;
    
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                $output += $power->amount;
            }
        
        }
        
        $output -= $this->outputMod;
        return $output;
        
    }
    
}

class ElintScanner extends Scanner{
    public $name = "elintScanner";
    public $displayName = "ELINT Scanner";

    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
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
?>


