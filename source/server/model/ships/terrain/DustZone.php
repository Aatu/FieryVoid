<?php
/* The DUST ZONE's unit (DustZoneRule). The dust itself is the rule's hexes - the whole map, or one
   clump - and this unit exists only to own the DustCollision orders RammingAttack makes in Pre-Firing,
   because a fire order needs a shooter and a weapon. One per game, made by BuyingGamePhase::process
   while the rule is set.

   NOBODY SEES IT. It stands off the map (BuyingGamePhase::advance), so no hex rule - jump exits,
   vortices, deployment - ever finds terrain in a real hex, and the client counts it as gone
   (shipManager.isHiddenUnit): no icon, no selection, no target, no fleet-list row. The combat log still
   names it as the source of the damage.

   Its damage is the tabletop's, once per turn after movement (RammingAttack::createDustZoneOrders), not
   the Dust Field's once per hex entered. variantOf NONE keeps it out of the lobby's Terrain list. */
class DustZone extends DustField {

    function __construct($id, $userid, $name, $slot) {
        parent::__construct($id, $userid, $name, $slot);
        $this->phpclass = "DustZone";
        $this->shipClass = "Dust Zone";
        $this->variantOf = 'NONE';
        $this->notes = "Dust covers the map, or part of it.";
        $this->notes .= "<br>After movement, every unit that moved through dust takes damage once:";
        $this->notes .= "<br>Ships: speed / 2, to the Structure of the side that led for most of the move (Primary if none).";
        $this->notes .= "<br>Fighters: (speed - 10) / 3, to every craft.";
        $this->notes .= "<br>Standard damage. Armor facing the direction of motion applies, shields do not.";
    }

    //Where it stands: well off any map, so it is never in a hex anything can enter or aim at.
    public static function getParkingHex() {
        return new OffsetCoordinate(0, -500);
    }
}
?>
