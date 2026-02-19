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
	public $repairPriority = 4;//0-9; lower = lower priority, 0 means it's irrepairable
    public $forInfo = false; // For crits that actually don't directly affect weapon, but inform player of other statuses
		
    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0, $forInfo = false, $param = null){
        $this->id = (int)$id;
        $this->shipid = (int)$shipid;
        $this->systemid = (int)$systemid;
        $this->phpclass = $phpclass;
        $this->turn = (int)$turn;
        $this->turnend = (int)$turnend;
        $this->forInfo = (bool)$forInfo; // Always store as a boolean        
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
    public $description = "Output halved";
    public $outputModPercentage = -50; //output modified by -50%
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}

class OutputHalvedOneTurn extends Critical{
    public $description = "Output halved";
    public $outputModPercentage = -50; //output modified by -50%
    public $oneturn = true;    
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}
	
class OutputReduced extends Critical{
    public $description = "Output reduced";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
    function setParam($param){
        parent::setParam($param);
    }
}

class OutputReducedOneTurn extends Critical{
    public $description = "Output reduced";
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
    public $description = "Efficiency halved";
    public $outputMod = -0.5;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class SevereBurnout extends Critical{
    public $description = "System non-functional";
    public $outputMod = -1;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class DamageReductionRemoved extends Critical{
    public $description = "Damage reduction disabled";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ForcedOfflineOneTurn extends Critical{
    public $description = "Forced offline";
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
    public $description = "First point of channeled thrust lost";
    public $outputMod = -1;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}

class FirstThrustIgnoredOneTurn extends Critical{
    public $description = "First point of channeled thrust lost";
    public $outputMod = -1;    
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend);
    }
}

class HalfEfficiency extends Critical{
    public $description = "Double thrust required";
    public $outputMod = 0;
    public $priority = 3; //nastiest of Thruster crits, so should be removed first!
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class OSATThrusterCrit extends Critical{
    public $description = "Can only turn once per turn";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class AmmoExplosion extends Critical{
    public $description = "Stored ammunition exploded";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class CommunicationsDisruptedOneTurn extends Critical{
    public $description = "Comms disrupted. -5 Initiative";
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class CommunicationsDisrupted extends Critical{
    public $description = "Comms disrupted. -5 Initiative";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class PenaltyToHitOneTurn extends Critical{
    public $description = "-1 penalty to hit for all weapons";
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class PenaltyToHit extends Critical{
    public $description = "-1 penalty to hit for all weapons";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class RestrictedEWOneTurn extends Critical{
    public $description = "-2 EW. Only up to half EW offensively";
    public $oneturn = true;
//	public $priority = 1; //probably the nastiest C&C crit, to be fixed at first priority
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class RestrictedEW extends Critical{
    public $description = "-2 EW. Only up to half EW offensively";
	public $priority = 1; //probably the nastiest C&C crit, to be fixed at first priority
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedIniativeOneTurn extends Critical{
    public $description = "-10 Initiative";
    public $oneturn = true;
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedIniative extends Critical{
    public $description = "-10 Initiative";
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

class ShipDisabled extends Critical{
    public $description = "Ship disabled";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}
//Reduces $rangePenalty
class ReducedRange extends Critical{
    public $description = "Range penalty increased";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ReducedDamage extends Critical{
    public $description = "Damage reduced";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

//Antimatter has special ReducedRange critical
class ReducedRangeAntimatter extends Critical{
    public $description = "Range penalty increased";//increase range by 3
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

//Reduces $range
class ReducedRangeValue extends Critical{
    public $description = "Offensive mode range reduced";//decrease range by 2
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
} 

//Antimatter has special ReducedDamage critical
class ReducedDamageAntimatter extends Critical{
    public $description = "Damage reduced"; //reduce X by 2 (not below 0)
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ArmorReduced extends Critical{
    public $description = "Armor reduced";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class FieldFluctuations extends Critical{ /*reduced power output for MagGrav Reactor*/
    public $description = "Field Fluctuations (-10% Power)";
    public $outputModPercentage = -10; //output modified by -10%
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class GravThrusterCritIgnored extends Critical{ /*Gravitic Thruster - first critical is ignored*/
    public $description = "Critical damage negated";
	public $priority = 6; //in itself does nothing, last priority to fix
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class swtargetheld extends Critical{ /*next turn target is being held by tractor beam!*/
    public $description = "Held by tractor beam! Reduced Initiative (-20/hit) and remaining thrust (-1/hit)";
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}



class tmpsensordown extends Critical{ /*next turn target Sensors/OB are down by 1, to a minimum of 0 - place on C&C or FIRST FIGHTER! (may be destroyed)!*/
    public $description = "-1 Sensors/OB";
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class tmpinidown extends Critical{ /*next turn target Initiative is down by 1, to a minimum of 0 - place on C&C or FIRST FIGHTER! (may be destroyed)!*/
    public $description = "-5 Initiative"; //-1 in d20 system
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend =0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class tmppowerdown extends Critical{ /*next turn target Power is down by 1 - place on C&C (may be destroyed)!*/
    public $description = "-1 Power"; 
    public $oneturn = true;		
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class tmphitreduction extends Critical{ /*immediate reduction in target's chance to hit for all fire*/
    public $description = "Chaff Missile Hit! -15% chance to hit to all fire, except ballistics";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}



class TendrilDestroyed extends Critical{
    public $description = "Tendril destroyed";
	public $repairPriority = 0; //cannot be fixed
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class TendrilCapacityReduced extends Critical{
    public $description = "Capacity of all tendrils reduced by 2";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ShadowPilotPain extends Critical{
    public $description = "Pilot feels pain"; //-1 penalty on weapons fire, has a -1 penalty to initiative and loses 1 point of free thrust
	public $repairPriority = 0; //cannot be fixed
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


class ChargeHalve extends Critical{ //charge halved - instant effect, to be received in appropriate crit-phase method; no lasting effect
    public $description = "Stored power is halved";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
		$turnend=$turn;//ALWAYS - immediate end of effect! 
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class ChargeEmpty extends Critical{ //charge emptied - instant effect, to be received in appropriate crit-phase method; no lasting effect
    public $description = "Stored power is lost";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
		$turnend=$turn;//ALWAYS - immediate end of effect! 
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

//for Reactor - it will check for explosion every turn (including turn the critical was added, as officially this is done at start of turn; in FV there is no option to voluntarily shut down the Reactor to prevent that)
//-10 Power that comes with the crit officially will be applied as a separate critical
class ContainmentBreach extends Critical{
     public $description = "Containment Breach";
	public $repairCost = 2;
	public $repairPriority = 9; //it's REALLY important to prevent reactor explosion :)
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class SensorLoss extends Critical{
	//Used by the Cylon Hybrid
    public $description = "Sensor loss: -3 EW";
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class LimpetBore extends Critical{
	//Used by the Limpet Bore Torpedo
    public $description = "Limpet Bore attached to system"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
		if($turnend == 0) $turnend = $turn + 4;    	
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class LimpetBoreTravelling extends Critical{
	//Used by the Limpet Bore Torpedo to mark when travelling to target system
    public $description = "A Limpet Bore is travelling to attack this system"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
            parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class MayOverheat extends Critical { 
    public $description = "May overheat";
    public $repairPriority = 0; // Can't repair.
    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0) {
        // Always pass $forInfo as true for this crit
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true);
    }
}

class Sabotage extends Critical{
	//Used by Breaching Pods to mark when Marines are trying to sabotage a system / wreck havoc.
    public $description = "Enemy marine unit is undertaking sabotage operations"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class SabotageElite extends Critical{
	//Used by Breaching Pods to mark when Marines are trying to sabotage a system / wreck havoc.
    public $description = "Elite marine unit is undertaking sabotage operations"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class ProfileIncreased extends Critical{
	//Used to increase ship profiles during firing, and make them easier to hit.
    public $description = "Ship defence profile increased";      
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}

class CaptureShip extends Critical{
	//Used by Breaching Pods to mark when Marines are conducting a Rescue mission for scenarios etc
    public $description = "Enemy marines are attempting to capture this ship"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class CaptureShipElite extends Critical{
	//Used by Breaching Pods to mark when Marines are conducting a Rescue mission for scenarios etc
    public $description = "Elite marines are attempting to capture this ship"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class RescueMission extends Critical{
	//Used by Breaching Pods to mark when Marines are conducting a Rescue mission for scenarios etc
    public $description = "An Marine unit is conducting a rescue mission"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class RescueMissionElite extends Critical{
	//Used by Breaching Pods to mark when Marines are conducting a Rescue mission for scenarios etc
    public $description = "An elite Marine unit is conducting a rescue mission"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class DefenderLost extends Critical{
	//To record when defending marines are killed.
    public $description = "Defender lost"; 
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend, true );
    }
} 

class EngineShorted extends Critical{
	//Serious engine critical to roll for whether to offline or force full acceleration.
    public $description = "Engine Shorted";
    public $oneturn = true;     
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
} 

class ControlsStuck extends Critical{
	//Serious engine critical to roll for whether to offline or force full acceleration.
    public $description = "Involuntary Acceleration";  
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
} 

class DamageSystem extends Critical{
	//Sometimes a critical effect is to actually cause damage to a system, the idea here is create a critical on that turn and then resolve the actual damage in CriticalPhaseEffects() on same turn.
    public $description = "Damage a system";  
	public $repairPriority = 0;//Can't repair.'       
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){  	
    parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turn, true );
    }
} 

class IncreasedRecharge1 extends Critical{
    //This one requires quite a bit of work within the weapon class to work properly, see Transverse Drive for an example.
    public $description = "Weapon recharge increased by one turn";
	public $repairPriority = 6;//0-9; lower = lower priority, 0 means it's irrepairable    
    function __construct($id, $shipid, $systemid, $phpclass, $turn, $turnend = 0){
		parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $turnend );
    }
}


?>
