<?php
//A Dust Cloud at its largest: one hex and the six around it - the Huge = 1 disc, so no hexOffsets.
//See DustCloudThreeHex.
class DustCloudSevenHex extends DustField {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 7;
        $this->phpclass = "DustCloudSevenHex";
        $this->imagePath = "img/ships/dustCloudSevenHex.png";
        $this->canvasSize = 520;
        $this->shipClass = "Dust Cloud (7 hexes)";
        $this->Huge = 1;
        $this->notes = "Occupies multiple hexes.";
        $this->notes .= "<br>Units entering its hexes take damage.";
        $this->notes .= "<br>Deals target speed / 2 damage.";
    }
}
?>
