<?php
/* REINFORCEMENTS_PLAN.md STAGE 9 - THE INVISIBLE DOORWAY, FOR A DRIVE THAT DOES NOT TEAR ONE OPEN.
 *
 * A Shadow hull does not open a B5 vortex. It fades out - "boost the drive in Initial Orders,
 * vanish at the end of the turn, leave nothing behind" (user ruling 2026-08-25, see
 * JumpEngine::markLegacy and PhasingDrive). Stage 9 gives that gesture its other half: such a hull
 * PHASES IN the same way, and no jump point terrain is created for it either (user ruling
 * 2026-08-29).
 *
 * ⭐⭐ IT IS STILL A UNIT, AND THAT IS THE WHOLE DESIGN. Every Stage 6/7/8 rule is anchored on the
 * vortex object - where the wave stands (JumpEngine::getArrivalVortex), which way it faces, when it
 * closes, whether an unplaced unit keeps its berth, whether a second doorway may clamp onto the
 * same hex. Deleting the object for one faction would have forked all of that in two. So the object
 * is created exactly as before and the CLIENT draws nothing for it: shipManager.shouldBeHidden
 * answers true for this phpclass, which takes the icon, the facing arrow, the hover/click sweep,
 * the hex ship-list and the replay animation with it in one move.
 *
 * WHAT THE PLAYER SEES INSTEAD is the blue ballistic hex the declaration already draws, relabelled
 * REINFORCEMENTS rather than "Jump Point Forming" - because for a phasing hull nothing is forming.
 * That marker is public exactly as the ordinary one is (§2.3's warning trade is unchanged); it is
 * the terrain that is gone, not the warning.
 *
 * ⚠️ A SUBCLASS AND NOT A FLAG, and the reason is persistence, not taste. A public property set at
 * spawn time does NOT survive the round trip: DBManager writes the columns, and the reload is
 * `new $phpclass(...)` with the stored name and slot - so a boolean would be true for exactly one
 * request and false for the rest of the game, silently. phpclass IS the persisted identity, which
 * is the same reasoning SpawnJumpPointExit's own constructor carries (§3.4).
 *
 * ⚠️ THREE REGISTRATIONS, NOT ONE (plan trap 16): source/autoload.php (or class_exists() is false
 * and the static generator skips this file SILENTLY), JumpEngine::$spawnableClasses (which is what
 * game.php's BlueprintCache actually reads for a unit that appears mid-game on a poll), and the
 * client mirror shipManager.movement.isJumpVortexExit - which is a phpclass STRING test, so a
 * subclass does not match it for free. Trap 23's shape exactly: a client predicate narrower than
 * its server twin shows up as a feature that works on one side and not the other.
 *
 * ⚠️ EVERY `instanceof SpawnJumpPointExit` ON THE SERVER MATCHES THIS TOO, and that is wanted:
 * one-way-ness, the closure rule, the arrival lookup and the Maintain refusal are all the same
 * rules for a phasing hull. Nothing here is a different KIND of doorway - it is the same doorway
 * with no picture.
 */
class SpawnJumpPointPhaseIn extends SpawnJumpPointExit{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        //THE PERSISTED IDENTITY - see the ⚠️ in the class comment. Everything else here is
        //presentation for the one place a hidden unit can still be read: the combat log.
        $this->phpclass = "SpawnJumpPointPhaseIn";

        $this->name = "Phase Point";
        $this->shipClass = "Phase Point";

        /* THE IMAGE AND THE ARROW ARE INHERITED AND NEVER DRAWN. Left in place rather than nulled:
           shouldBeHidden is the single suppression and it is a client rule, so anything that
           reaches this unit another way (a replay of a game recorded before that rule shipped, a
           future debug view) still gets a coherent object rather than a unit with no art. Nulling
           $facingArrow here would ALSO be read by ShipIcon as "this unit has no facing", which is
           false - the wave is placed on that heading. */

        $this->notes = "A phasing hull's arrival point.";
        $this->notes .= "<br>No vortex is torn open - the units simply fade into being on the heading shown.";
        $this->notes .= "<br>It cannot be jumped out of, and it is not visible on the map.";
    }
}
?>
