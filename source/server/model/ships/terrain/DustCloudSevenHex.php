<?php
//A Dust Cloud at its largest: one hex and the six around it - the Huge = 1 disc, so no hexOffsets.
//See DustCloudThreeHex.
class DustCloudSevenHex extends DustField {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 1;
        $this->phpclass = "DustCloudSevenHex";
        $this->imagePath = "img/ships/dustCloudSevenHex.png";
        $this->canvasSize = 520;
        $this->shipClass = "Dust Cloud (7 hexes)";
        $this->Huge = 1;
        $this->notes = "Occupies multiple hexes.";
        $this->notes .= "<br>Units take damage for EACH of its hexes they enter.";
        $this->notes .= "<br>Ships: speed / 2, to the Structure of the side entering the hex (Primary if none).";
        $this->notes .= "<br>Fighters: (speed - 10) / 3, to every craft.";
        $this->notes .= "<br>Standard damage. Armor applies, shields do not.";
    }
}
?>
