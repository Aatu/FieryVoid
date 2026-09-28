<?php
//A Dust Cloud of five hexes: its centre and four of the six around it. See DustCloudThreeHex.
class DustCloudFiveHex extends DustField {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 1;
        $this->phpclass = "DustCloudFiveHex";
        $this->imagePath = "img/ships/dustCloudFiveHex.png";
        $this->canvasSize = 520;
        $this->shipClass = "Dust Cloud (5 hexes)";
        $this->Huge = 1; //multi-hex, but not circular
        $this->hexOffsets = [
            ['q' => 1, 'r' => 1],  // NE hex
            ['q' => 0, 'r' => 1],  // NW hex
            ['q' => -1, 'r' => 0], // W hex
            ['q' => 0, 'r' => -1]  // SW hex
        ];
        $this->notes = "Occupies multiple hexes.";
        $this->notes .= "<br>Units take damage for EACH of its hexes they enter.";
        $this->notes .= "<br>Ships: speed / 2, to the Structure of the side entering the hex (Primary if none).";
        $this->notes .= "<br>Fighters: (speed - 10) / 3, to every craft.";
        $this->notes .= "<br>Standard damage. Armor applies, shields do not.";
    }
}
?>
