<?php
class MeteorSwarm extends Terrain {
    public $isMeteoroid = true;
    public $terrainCollisionType = 'MeteoroidCollision';

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 2;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "MeteorSwarm";
        $this->imagePath = "img/ships/meteorSwarm.png";
        $this->canvasSize = 200;
        $this->shipClass = "Meteoroid Swarm";
        $this->Enormous = false;
        $this->iniativebonus = -200;
        $this->isd = 0;
        $this->notes = "Units entering this hex roll to detemine number of Meteroid hits.";
        $this->notes .= "<br>Meteoroid damage = 1d10 + target speed.";
        $this->notes .= "<br>Deals Standard damage.";
        $this->notes .= "<br>Meteor Defence: weapons declared in Initial Orders, and covering the direction of motion, reduce each meteor's damage by their intercept rating.";
        $this->occurence = "common";
        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;
        $this->forwardDefense = 20;
        $this->sideDefense = 20;
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;

        Enhancements::nonstandardEnhancementSet($this, 'Terrain');

        $this->addPrimarySystem(new OSATCnC(10, 1, 0, 0));
        $this->addPrimarySystem(new Structure(5, 200));


        $this->hitChart = array(
            0 => array(20 => "Structure"),
        );
    }

    public static function rollMeteorChart($shipSizeClass, $isFlight, $modifier = 0) {
        $roll = Dice::d(20) + $modifier;
        $roll = max(1, min(20, $roll));
        $chart = array(
            12 => array(0, 0, 0, 0, 0),
            14 => array(0, 0, 0, 0, 1),
            15 => array(0, 0, 0, 1, 1),
            16 => array(0, 0, 1, 1, 1),
            17 => array(0, 1, 1, 1, 2),
            18 => array(0, 1, 1, 2, 2),
            19 => array(1, 1, 2, 2, 2),
            20 => array(1, 2, 2, 2, 3),
        );
        if ($isFlight) { $col = 0; }
        else {
            switch ($shipSizeClass) {
                case 1: $col = 1; break;
                case 2: $col = 2; break;
                case 3: $col = 3; break;
                default: $col = ($shipSizeClass >= 4) ? 4 : 1;
            }
        }
        $hits = 0;
        foreach ($chart as $threshold => $row) {
            if ($roll <= $threshold) { $hits = $row[$col]; break; }
        }
        if ($roll >= 20) $hits = $chart[20][$col];
        return $hits;
    }
        
    public static function getMeteorDamage($speed) {
        return Dice::d(10) + $speed;
    }
}    
?>
