<?php
/* Dust Clouds: a Dust Field spread over several hexes - three, five or seven, the most being one hex and
   the six around it. Create Game's random Dust mixes them in as the random Asteroids mix in their 2- and
   3-hex units (BuyingGamePhase::addDustAndMeteors); CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §12.12.

   Everything else is the Dust Field's, inherited: its collision (DustCollision, speed / 2 to a unit
   entering one of its hexes), no line-of-sight block, no ram. Huge = 1 marks it as multi-hex for the
   sites that ask only that, as on the irregular asteroids; the hexes it stands on are its hexOffsets
   turned to its facing (RammingAttack::getTerrainOccupiedHexes - offsets win over the Huge disc). */
class DustCloudThreeHex extends DustField {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->pointCost = 3;
        $this->phpclass = "DustCloudThreeHex";
        $this->imagePath = "img/ships/dustCloudThreeHex.png";
        $this->canvasSize = 520; //a 260-unit sprite: the image is drawn round the centre hex, at facing 0
        $this->shipClass = "Dust Cloud (3 hexes)";
        $this->Huge = 1; //multi-hex, but not circular
        $this->hexOffsets = [
            ['q' => 0, 'r' => 1],  // NW hex
            ['q' => -1, 'r' => 0]  // W hex
        ];
        $this->notes = "Occupies multiple hexes.";
        $this->notes .= "<br>Units entering its hexes take damage.";
        $this->notes .= "<br>Deals target speed / 2 damage.";
    }
}
?>
