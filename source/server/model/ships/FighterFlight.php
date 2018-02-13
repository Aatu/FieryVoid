<?php
require_once("ShipClasses.php");

class FighterFlight extends BaseShip
{

    public $shipSizeClass = -1; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
    public $imagePath = "img/ships/null.png";
    public $iconPath, $shipClass;
    public $systems = array();
    public $agile = true;
    public $turncost;
    public $turndelaycost = 0;
    public $accelcost = 1;
    public $rollcost = 1;
    public $pivotcost = 1;
    public $currentturndelay = 0;
    public $iniative = "N/A";
    public $iniativebonus = 0;
    public $gravitic = false;
    public $phpclass;
    public $forwardDefense, $sideDefense;
    public $destroyed = false;
    public $pointCost = 0;
    public $faction = null;
    public $flight = true;
    public $hasNavigator = false;
    public $superheavy = false;
    public $flightSize = 1;
    protected $flightLeader = null;

    public $offensivebonus, $freethrust;
    public $jinkinglimit = 0;


    public $canvasSize = 200;

    public $fireOrders = array();

    //following values from DB
    public $id, $userid, $name, $campaignX, $campaignY;
    public $rolled = false;
    public $rolling = false;
    public $team;

    protected $dropOutBonus = 0;

    public $movement = array();

    function __construct($id, $userid, $name, $slot)
    {
        $this->id = (int)$id;
        $this->userid = (int)$userid;
        $this->name = $name;
        $this->slot = $slot;
    }

    private $autoid = 1;


    public function getInitiativebonus($gamedata)
    {
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);

        if ($this->hasNavigator) {
            $initiativeBonusRet += 5;
        }

        return $initiativeBonusRet;
    }

    public function getDropOutBonus()
    {
        return $this->dropOutBonus;
    }

    public function getSystemById($id)
    {
        foreach ($this->systems as $system) {
            if ($system->id == $id) {
                return $system;
            }
            foreach ($system->systems as $fs) {
                if ($fs->id == $id) {
                    return $fs;
                }
            }
        }

        return null;
    }


    /*returns a sample fighter, if one needs to review example of what's in flight*/
    public function getSampleFighter()
    {
        return $this->systems[1];
    }


    /*redefinition - as defensive systems will be on actual fighters*/
    /*assuming all fighters are equal, it's enough to get system from first fighter, whether it's alive or not!*/
    public function getDamageMod($shooter, $pos, $turn, $weapon)
    {
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        $fighter = $this->systems[1];
        foreach ($fighter->systems as $system) {
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveDamageMod($this, $shooter, $pos, $turn, $weapon);
            if (!isset($affectingSystems[$system->getDefensiveType()])
                || $affectingSystems[$system->getDefensiveType()] < $mod) {
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        return array_sum($affectingSystems);
    }

    /*redefinition - as defensive systems will be on actual fighters*/
    /*assuming all fighters are equal, it's enough to get system from first fighter, whether it's alive or not!*/
    public function getHitChanceMod($shooter, $pos, $turn, $weapon)
    {
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        $fighter = $this->systems[1];
        foreach ($fighter->systems as $system) {
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveHitChangeMod($this, $shooter, $pos, $turn, $weapon);
            if (!isset($affectingSystems[$system->getDefensiveType()]) //no system of this kind is taken into account yet, or it is but it's weaker
                || $affectingSystems[$system->getDefensiveType()] < $mod) {
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        return (-array_sum($affectingSystems));
    }

    /*redefinition; for fighter, don't check whether system is destroyed - it doesn't matter as long as entire flight isn't!*/
    /*also, fighter systems don't get disabled :)*/
    private function checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)
    {
        if (!($system instanceof DefensiveSystem)) return false; //this isn't a defensive system at all

        //if the system has arcs, check that the position is on arc
        if (is_int($system->startArc) && is_int($system->endArc)) {
            //get bearing on incoming fire...
            if ($weapon->ballistic) {
                $relativeBearing = $this->getBearingOnPos($pos);
            } else { //direct fire weapon - check from shooter...
                $relativeBearing = $this->getBearingOnUnit($shooter);
            }
            //if not on arc, continue!
            if (!mathlib::isInArc($relativeBearing, $system->startArc, $system->endArc)) {
                return false;
            }
        }

        return true;
    }//endof function checkIsValidAffectingSystem


    public function getSystemByName($name)
    {
        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $fs) {
                if ($fs->name == $name) return $fs;
            }
        }
    }

    public function getFighterBySystem($id)
    {
        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $fs) {
                if ($fs->id == $id) return $fighter;
            }
        }
    }

    protected function addSystem($fighter, $loc = null)
    {
        $fighter->setUnit($this);
        $fighter->id = $this->autoid;
        $fighter->location = sizeof($this->systems);

        $this->autoid++;
        $fighterSys = array();
        foreach ($fighter->systems as $system) {
            $system->setUnit($this);
            $system->id = $this->autoid;
            $this->autoid++;
            $fighterSys[$system->id] = $system;
        }
        $fighter->systems = $fighterSys;
        $this->systems[$fighter->id] = $fighter;
    } //endof function addSystem


    public function getPreviousCoPos()
    {
        $pos = $this->getCoPos();

        for ($i = sizeof($this->movement) - 1; $i >= 0; $i--) {
            $move = $this->movement[$i];
            $pPos = $move->getCoPos();

            if ($pPos["x"] != $pos["x"] || $pPos["y"] != $pos["y"])
                return $pPos;
        }

        return $pos;
    }

    public function getDEW($turn)
    {

        foreach ($this->EW as $EW) {
            if ($EW->type == "DEW" && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;

    }

    public function getOEW($target, $turn)
    {

        foreach ($this->EW as $EW) {
            if ($EW->type == "OEW" && $EW->targetid == $target->id && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;
    }

    public function getFacingAngle()
    {
        $movement = null;

        foreach ($this->movement as $move) {
            $movement = $move;
        }

        return $movement->getFacingAngle();
    }


    public function getLocations()
    {
        $locs = array();
        foreach ($this->systems as $fighter) {
            $exampleFtr = $fighter; //whether still alive or not; any fighter in flight will do, as they're all the same!
        }
        $health = $exampleFtr->maxhealth;

        $locs[] = array("loc" => 0, "min" => 330, "max" => 30, "profile" => $this->forwardDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[0]);
        $locs[] = array("loc" => 0, "min" => 30, "max" => 150, "profile" => $this->sideDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[3]);
        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $this->forwardDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[1]);
        $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $this->sideDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[2]);

        return $locs;
    }

    public function fillLocations($locs)
    { //for fighters, armour and health are already defined by getLocations
        return $locs;
    }


    public function getStructureSystem($location)
    {
        return null;
    }

    public function getFireControlIndex()
    {
        return 0;

    }

    public function isDestroyed($turn = false)
    {
        foreach ($this->systems as $system) {
            if (!$system->isDestroyed($turn) && !$system->isDisengaged($turn)) {
                return false;
            }

        }

        return true;
    }

    public function isPowerless()
    {
        return false;
    }


    public function getHitSystem($shooter, $fire, $weapon, $location = null)
    {
        $skipStandard = false;
        $systems = array();
        if ($fire->calledid != -1) {
            $system = $this->getSystemById($fire->calledid);
            if (!$system->isDestroyed()) { //called shot at particular fighter, which is still living
                $systems[] = $system;
                $skipStandard = true;
            }
        }

        if (!$skipStandard) {
            foreach ($this->systems as $system) {
                if (!$system->isDestroyed()) {
                    $systems[] = $system;
                }
            }
        }

        if (sizeof($systems) == 0) return null;

        return $systems[(Dice::d(sizeof($systems)) - 1)];
    }


    public function getAllFireOrders($turn = -1)
    {
        $orders = array();

        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $system) {
                $orders = array_merge($orders, $system->getFireOrders($turn));
                //$orders = array_merge($orders, $system->fireOrders); //old version
            }
        }

        return $orders;
    }


    /*always nothing to do for fighters*/
    public function setExpectedDamage($hitLoc, $hitChance, $weapon)
    {
        return;
    }

}

class SuperHeavyFighter extends FighterFlight
{
    public $superheavy = true;

    function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
}

?>
