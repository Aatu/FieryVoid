<?php

class Critical
{

    public $id, $shipid, $systemid, $phpclass, $tur, $param;
    public $updated = false;
    public $outputMod = 0;
    public $outputModPercentage = 0; //if output is percentage-based rather than absolute
    public $description = "";
    public $oneturn = false;
    public $inEffect = true;
    public $newCrit = false; //true forces database insert even out of current turn!

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $param = null)
    {
        $this->id = (int) $id;
        $this->shipid = (int) $shipid;
        $this->systemid = (int) $systemid;
        $this->phpclass = $phpclass;
        $this->turn = (int) $turn;
        $this->setParam($param);

    }

    protected function setParam($param)
    {
        $this->param = $param;
    }

    public function getDescription()
    {
        return $this->description;
    }
}

class DisengagedFighter extends Critical
{

    public $description = "DISENGAGED";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class OutputReduced extends Critical
{
    public $description = "Output reduced.";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

    public function setParam($param)
    {
        parent::setParam($param);
    }
}

class OutputReducedOneTurn extends Critical
{
    public $description = "Critical Shutdown effective in Turn ";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        $this->description = $this->description . " " . ($turn + 1) . ".";
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class OutputReduced1 extends Critical
{
    public $description;
    public $outputMod = -1;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        $this->description = "Output altered by " . $this->outputMod;
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class OutputReduced2 extends OutputReduced1
{

    public $outputMod = -2;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class OutputReduced3 extends OutputReduced1
{

    public $outputMod = -3;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class OutputReduced4 extends OutputReduced1
{

    public $outputMod = -4;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class OutputReduced6 extends OutputReduced1
{

    public $outputMod = -6;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}
class OutputReduced8 extends OutputReduced1
{

    public $outputMod = -8;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class OutputReduced10 extends OutputReduced1
{

    public $outputMod = -10;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class PartialBurnout extends Critical
{

    public $description = "Efficiency halved.";
    public $outputMod = -0.5;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class SevereBurnout extends Critical
{

    public $description = "System non functional";
    public $outputMod = -1;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }

}

class DamageReductionRemoved extends Critical
{

    public $description = "Damage reduction disabled";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ForcedOfflineOneTurn extends Critical
{

    public $description = "Forced offline for ";
    public $oneturn = true;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ForcedOfflineForTurns extends Critical
{

    public $description = "";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $time)
    {
        $this->$time = $time;
        $this->description = "Forced offline until end of turn " . ($turn + $time);
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $time);
    }
}

class FirstThrustIgnored extends Critical
{
    public $description = "First point of channeled thrust is ignored.";
    public $outputMod = 0;
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class HalfEfficiency extends Critical
{

    public $description = "Two points of thrust required to channel one through.";
    public $outputMod = 0;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class OSATThrusterCrit extends Critical
{
    public $description = "Can only turn once per turn.";
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class AmmoExplosion extends Critical
{
    public $description = "Stored ammunition did explode.";
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class SensorsDisrupted extends Critical
{
    public $description = "Sensors disrupted. Cannot change sensor settings. [TODO]";
    public $oneturn = true;
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class CommunicationsDisrupted extends Critical
{
    public $description = "Communications disrupted. -5 initiative.";
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class PenaltyToHit extends Critical
{
    public $description = "-1 penalty to hit for all weapons.";
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class RestrictedEW extends Critical
{
    public $description = "-2 EW. May use no more than half of its EW offensively.";
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ReducedIniativeOneTurn extends Critical
{
    public $description = "-10 iniative for  ";
    public $oneturn = true;
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ReducedIniative extends Critical
{
    public $description = "-10 iniative.";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ShipDisabledOneTurn extends Critical
{
    public $description = "Ship disabled for ";
    public $oneturn = true;
    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ReducedRange extends Critical
{
    public $description = "Range penalty increased.";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ReducedDamage extends Critical
{
    public $description = "Damage reduced.";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class ArmorReduced extends Critical
{
    public $description = "Armor reduced.";

    public function __construct($id, $shipid, $systemid, $phpclass, $turn)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn);
    }
}

class NastierCrit extends Critical
{ /*next critical (or dropout!) roll will be nastier*/
    public $description = "Vulnerable to criticals.";
    public $oneturn = true;
    //public $outputMod = 1; //can't use otputMod as it has effects regardless of a particular crit!

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $param = null)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $param);
    }
}

class FieldFluctuations extends Critical
{ /*reduced power output for MagGrav Reactor*/
    public $description = "Field Fluctuations (-10% Power).";
    public $outputModPercentage = -10; //output modified by -10%

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $param = null)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $param);
    }
}

class swtargetheld extends Critical
{ /*next turn target is being held by tractor beam!*/
    public $description = "Held by tractor beam! Reduced Initiative (-20/hit) and remaining thrust (-1/hit).";
    public $oneturn = true;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $param = null)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $param);
    }
}

class tmpsensordown extends Critical
{ /*next turn target Sensors/OB are down by 1, to a minimum of 0 - place on C&C or FIRST FIGHTER! (may be destroyed)!*/
    public $description = "-1 Sensors/OB.";
    public $oneturn = true;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $param = null)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $param);
    }
}

class tmpinidown extends Critical
{ /*next turn target Initiative is down by 1, to a minimum of 0 - place on C&C or FIRST FIGHTER! (may be destroyed)!*/
    public $description = "-5 Initiative."; //-1 in d20 system
    public $oneturn = true;

    public function __construct($id, $shipid, $systemid, $phpclass, $turn, $param = null)
    {
        parent::__construct($id, $shipid, $systemid, $phpclass, $turn, $param);
    }
}
