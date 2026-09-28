<?php
class DustField extends Terrain {
    public $isDustField = true;
    public $terrainCollisionType = 'DustCollision';

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 1;
        $this->faction = "Terrain";
        $this->factionAge = 1;
        $this->phpclass = "DustField";
        $this->imagePath = "img/ships/dust.png";
        $this->canvasSize = 200;
        $this->shipClass = "Dust Field";
        $this->Enormous = false;
        $this->iniativebonus = -200;
        $this->isd = 0;
        $this->notes = "Units entering this hex take damage.";
        $this->notes .= "<br>Ships: speed / 2, to the Structure of the side entering the hex (Primary if none).";
        $this->notes .= "<br>Fighters: (speed - 10) / 3, to every craft.";
        $this->notes .= "<br>Standard damage. Armor applies, shields do not.";
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
        $this->addPrimarySystem(new Structure(8, 300));
        $this->hitChart = array(
            0 => array(20 => "Structure"),
        );
    }

    //Per hex entered, fractions dropped - see RammingAttack's DustCollision handling for where it lands.
    public static function getDustDamage($speed, $isFlight = false) {
        if ($isFlight) return max(0, floor(($speed - 10) / 3));
        return max(0, floor($speed / 2));
    }
}
?>
