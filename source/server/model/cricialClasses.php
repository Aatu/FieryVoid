<?php

class Critical{
    public $id, $shipid, $systemid, $phpclass, $turn,  $param;
    public $updated = false;
    public $outputMod = 0;
    public $outputModPercentage = 0; //if output is percentage-based rather than absolute
    public $description = "";
    public $oneturn = false; //technically superseded by $turnend, but there are many exiting references to $oneturn - I'm leaving it fully functional rather than overhaul
    public $inEffect = true;
    public $newCrit = false; //true forces database insert even out of current turn! (obsolete ATM, code checks presence of ID instead!
	//criticals remake
	public $forceModify = false; //true forces modification of critical already existing in the database
	public $turnend = 0; //last turn when critical is in efect; 0 = indefinitely
	public $repairCost = 1; //how many self repair points are needed to repair it;
	//official: all crits cost 1, except C&C cost 4; FV: except C&C and nastier Reactor crits, cost 2; no partial repairs!
	public $repairPriority = 4;//lower = higher priority, 0 means it's irrepairable
	
		
    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0, $param = null){
        $this->id = (int)$id;
        $this->shipid = (int)$shipid;
        $this->systemid = (int)$systemid;
        $this->phpclass = $phpclass;
        $this->turn = (int)$turn;
        $this->turnend = (int)$turnend;
        $this->setParam($param);
		if($this->oneturn) $this->turnend = $this->turn + 1; //set appropriate ending for "one turn" criticals, even if not called to explicitly
    }
        
        
    protected function setParam($param){
        $this->param = $param;
    }

    public function getDescription(){
        return $this->description;
    }
}
	
class DisengagedFighter extends Critical{
    public $description = "DISENGAGED";
	public $repairPriority = 0;//disengaged fighter will remain disengaged
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}
	
	

class OutputHalved extends Critical{
    public $description = "Output halved.";
    public $outputModPercentage = -50; //output modified by -50%
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}
	
class OutputReduced extends Critical{
    public $description = "Output reduced.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
    function setParam($param){
        parent::setParam($param);
    }
}

class OutputReducedOneTurn extends Critical{
    public $description = "Critical Shutdown.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        //$this->description = $this->description." ".($turn+1).".";
		if($turnend == 0) $turnend = $turn + 1;
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}

class OutputReduced1 extends Critical{
    public $description;
    public $outputMod = -1;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        $this->description = "Output altered by ".$this->outputMod;
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}
	
	
class OutputReduced2 extends OutputReduced1{
    public $outputMod = -2;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class OutputReduced3 extends OutputReduced1{
    public $outputMod = -3;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class OutputReduced4 extends OutputReduced1{
    public $outputMod = -4;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class OutputReduced6 extends OutputReduced1{
    public $outputMod = -6;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }

}
class OutputReduced8 extends OutputReduced1{
    public $outputMod = -8;
	public $repairCost = 2;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class OutputReduced10 extends OutputReduced1{
    public $outputMod = -10;
	public $repairCost = 2;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class PartialBurnout extends Critical{
    public $description = "Efficiency halved.";
    public $outputMod = -0.5;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class SevereBurnout extends Critical{
    public $description = "System non functional.";
    public $outputMod = -1;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class DamageReductionRemoved extends Critical{
    public $description = "Damage reduction disabled.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ForcedOfflineOneTurn extends Critical{
    public $description = "Forced offline.";
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ForcedOfflineForTurns extends Critical{
    public $description = "";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend){
        $this->description = "Forced offline.";
		parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class FirstThrustIgnored extends Critical{
    public $description = "First point of channeled thrust is ignored.";
    public $outputMod = -1;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}

class HalfEfficiency extends Critical{
    public $description = "Two points of thrust required to channel one through.";
    public $outputMod = 0;
    public $priority = 3; //nastiest of Thruster crits, so should be removed first!
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class OSATThrusterCrit extends Critical{
    public $description = "Can only turn once per turn.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class AmmoExplosion extends Critical{
    public $description = "Stored ammunition did explode.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class CommunicationsDisrupted extends Critical{
    public $description = "Communications disrupted. -5 initiative.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class PenaltyToHit extends Critical{
    public $description = "-1 penalty to hit for all weapons.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class RestrictedEW extends Critical{
    public $description = "-2 EW. May use no more than half of its EW offensively.";
	public $priority = 1; //probably the nastiest C&C crit, to be fixed at first priority
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedIniativeOneTurn extends Critical{
    public $description = "-10 iniative.";
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedIniative extends Critical{
    public $description = "-10 iniative.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ShipDisabledOneTurn extends Critical{
    public $description = "Ship disabled for ";
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedRange extends Critical{
    public $description = "Range penalty increased.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedDamage extends Critical{
    public $description = "Damage reduced.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ArmorReduced extends Critical{
    public $description = "Armor reduced.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class FieldFluctuations extends Critical{ /*reduced power output for MagGrav Reactor*/
    public $description = "Field Fluctuations (-10% Power).";
    public $outputModPercentage = -10; //output modified by -10%
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class GravThrusterCritIgnored extends Critical{ /*Gravitic Thruster - first critical is ignored*/
    public $description = "Critical damage negated.";
	public $priority = 6; //in itself does nothing, last priority to fix
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class swtargetheld extends Critical{ /*next turn target is being held by tractor beam!*/
    public $description = "Held by tractor beam! Reduced Initiative (-20/hit) and remaining thrust (-1/hit).";
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turnend );
    }
}



class tmpsensordown extends Critical{ /*next turn target Sensors/OB are down by 1, to a minimum of 0 - place on C&C or FIRST FIGHTER! (may be destroyed)!*/
    public $description = "-1 Sensors/OB.";
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class tmpinidown extends Critical{ /*next turn target Initiative is down by 1, to a minimum of 0 - place on C&C or FIRST FIGHTER! (may be destroyed)!*/
    public $description = "-5 Initiative."; //-1 in d20 system
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend =0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class tmppowerdown extends Critical{ /*next turn target Power is down by 1 - place on C&C (may be destroyed)!*/
    public $description = "-1 Power."; 
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}



class TendrilDestroyed extends Critical{
    public $description = "Tendril destroyed.";
	public $priority = 0; //cannot be fixed
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class TendrilCapacityReduced extends Critical{
    public $description = "Capacity of all tendrils reduced by 2.";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}
