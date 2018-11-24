<?php

class BaseShipNoAft extends BaseShip
{

    public $draziCap = true;

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getLocations()
    {
        //debug::log("getLocations");
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 180, "profile" => $this->sideDefense);

        return $locs;
    }
}

class HeavyCombatVessel extends BaseShip
{
    public $shipSizeClass = 2;

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
    public function getLocations()
    {
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }

}

class HeavyCombatVesselLeftRight extends BaseShip
{

    public $draziHCV = true;
    public $shipSizeClass = 2;

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getLocations()
    {
        $locs = array();
        $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 3, "min" => 330, "max" => 360, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 360, "profile" => $this->forwardDefense);

        return $locs;
    }
}

class MediumShip extends BaseShip
{

    public $shipSizeClass = 1;

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getFireControlIndex()
    {
        return 1;
    }

    public function getLocations()
    {
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);

        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }

} //end of class MediumShip

class MediumShipLeftRight extends MediumShip
{

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getLocations()
    {
        $locs = array();

        $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 330, "max" => 360, "profile" => $this->forwardDefense);

        return $locs;
    }
}

class LightShip extends BaseShip
{ //is this used anywhere?...

    public $shipSizeClass = 0;

    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getFireControlIndex()
    {
        return 1;

    }

} //end of class LightShip

class OSAT extends MediumShip
{
    public $osat = true;
    public $canvasSize = 100;

    public function isDisabled()
    {
        return false;
    }

    public function getLocations()
    {
        $locs = array();

        $locs[] = array("loc" => 0, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 0, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }
}

class StarBase extends BaseShip
{
    public $base = true;
    public $Enormous = true;

    public function isDisabled()
    {
        if ($this->isPowerless()) {
            return true;
        }

        $cncs = $this->getControlSystems();

        if (sizeof($cncs) > 0) {
            $intact = sizeof($cncs);

            foreach ($cncs as $cnc) {
                if ($cnc->destroyed) {
                    $intact--;
                }
            }
            if ($intact == 0) {
                return true;
            }

            usort($cncs, function ($a, $b) {
                if ($a->getRemainingHealth() > $b->getRemainingHealth()) {
                    return 1;
                } else {
                    return -1;
                }

            });

            $CnC = $cncs[0];
        }

        if ($CnC->hasCritical("ShipDisabledOneTurn", TacGamedata::$currentTurn)) {
            debug::log("is effeictlvy PHP Disabled due to " . $CnC->id);
            return true;
        }

        return false;
    }

    public function getControlSystems()
    {
        $array = array();

        foreach ($this->systems as $system) {
            if ($system instanceof CnC) {
                $array[] = $system;

            }
        }

        return $array;
    }

    protected function addLeftFrontSystem($system)
    {
        $this->addSystem($system, 31);
    }
    protected function addLeftAftSystem($system)
    {
        $this->addSystem($system, 32);
    }
    protected function addRightFrontSystem($system)
    {
        $this->addSystem($system, 41);
    }
    protected function addRightAftSystem($system)
    {
        $this->addSystem($system, 42);
    }

    public function isDestroyed($turn = false)
    {
        foreach ($this->systems as $system) {
            if ($system instanceof Reactor && $system->location == 0 && $system->isDestroyed($turn)) {
                return true;
            }
            if ($system instanceof Structure && $system->location == 0 && $system->isDestroyed($turn)) {
                return true;
            }
        }
        return false;
    }

    public function getMainReactor()
    {
        foreach ($this->systems as $system) {
            if ($system instanceof Reactor && $system->location == 0) {
                return $system;
            }
        }
    }

    public function destroySection($reactor, $gamedata)
    {
        $locToDestroy = $reactor->location;
        $sysArray = array();

        debug::log("killing section: " . $locToDestroy);
        foreach ($this->systems as $system) {
            if ($system->location == $reactor->location) {
                if (!$system->destroyed) {
                    $sysArray[] = $system;
                }
            }
        }

        foreach ($sysArray as $system) {

            $remaining = $system->getRemainingHealth();
            $armour = $system->armour;
            $toDo = $remaining + $armour;

            $damageEntry = new DamageEntry(-1, $this->id, -1, $gamedata->turn, $system->id, $toDo, $armour, 0, -1, true, "", "plasma");
            $damageEntry->updated = true;

            $system->damage[] = $damageEntry;
        }
    }
}

class StarBaseSixSections extends StarBase
{

    /* no longer needed, keeping code just in case
    public function getPiercingLocations($shooter, $pos, $turn, $weapon){
    $location = $this->getHitSection($shooter, $turn, true);

    $locs = array();
    $finallocs = array();

    if ($location == 1 || $location == 2){
    $locs[] = 1;
    $locs[] = 0;
    $locs[] = 2;
    }
    else if ($location == 31 || $location == 42){
    $locs[] = 31;
    $locs[] = 0;
    $locs[] = 42;
    }
    else if ($location == 32 || $location == 41){
    $locs[] = 32;
    $locs[] = 0;
    $locs[] = 41;
    }

    foreach ($locs as $loc){
    $structure = $this->getStructureSystem($loc);
    if ($structure != null && !$structure->isDestroyed()){
    $finallocs[] = $loc;
    }
    }

    return $finallocs;

    }
     */

    public function getLocations()
    {
        //debug::log("getLocations");
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 300, "max" => 60, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 120, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 42, "min" => 60, "max" => 180, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 120, "max" => 240, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 180, "max" => 300, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 31, "min" => 240, "max" => 360, "profile" => $this->forwardDefense);

        return $locs;
    }
}

class StarBaseFiveSections extends StarBase
{
    /* no longer needed, keeping code just in case
    public function getPiercingLocations($shooter, $pos, $turn, $weapon){
    $location = $this->getHitSection($shooter, $turn, true);

    $locs = array();
    $finallocs = array();
    if ($location == 1 ){
    $locs[] = 1;
    $locs[] = 0;
    $locs[] = 41; //should be choice, let's go for '3 sections further'
    }
    else if ($location == 41){
    $locs[] = 41;
    $locs[] = 0;
    $locs[] = 31;
    }
    else if ($location == 42){
    $locs[] = 42;
    $locs[] = 0;
    $locs[] = 1;
    }
    else if ($location == 32){
    $locs[] = 32;
    $locs[] = 0;
    $locs[] = 41;
    }
    else if ($location == 31){
    $locs[] = 31;
    $locs[] = 0;
    $locs[] = 42;
    }

    foreach ($locs as $loc){
    $structure = $this->getStructureSystem($loc);
    if ($structure != null && !$structure->isDestroyed()){
    $finallocs[] = $loc;
    }
    }

    return $finallocs;

    }
     */

    public function getLocations()
    {
        //debug::log("getLocations");
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 330, "max" => 150, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 42, "min" => 30, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 90, "max" => 270, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 31, "min" => 150, "max" => 330, "profile" => $this->forwardDefense);

        return $locs;
    }
} //end of StarBaseFiveSections

class SmallStarBaseFourSections extends BaseShip
{ //just change arcs of sections...
    public function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);

        $this->base = true;
        $this->smallBase = true;

        $this->shipSizeClass = 3;
        $this->iniativebonus = -200; //no voluntary movement anyway
        $this->turncost = 0;
        $this->turndelaycost = 0;
    }

    public function getLocations()
    {
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 270, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 0, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 0, "max" => 180, "profile" => $this->forwardDefense);

        return $locs;
    }
} //end of SmallStarBaseFourSections
