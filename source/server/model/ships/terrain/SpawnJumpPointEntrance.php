<?php
/* REINFORCEMENTS_PLAN.md STAGE 3 - THE ENTRANCE VORTEX UNIT.
 *
 * The mirror of SpawnJumpPoint: a jump vortex units come OUT of. It is opened by a reinforcement
 * still waiting in hyperspace (or, from Stage 8, by a fixed gate), it forms at the end of the turn
 * it was declared on, the manifest arrives through it in the Deployment phase of the next turn, and
 * a ship's entrance then closes.
 *
 * ⭐ THE CLASS IS THE DISCRIMINATOR. There is no note to parse and no flag to keep in step: the
 * class name reaches the client for free in ship.phpclass, and every server test is one instanceof.
 * That is why this is a subclass and not a boolean on SpawnJumpPoint - the plan's §3.4 records the
 * same reasoning for the declaration's damageclass.
 *
 * ⚠️ THE CONSTRUCTOR IS NOT OPTIONAL, and an empty subclass body is a silent bug. SpawnJumpPoint's
 * constructor sets $this->phpclass = "SpawnJumpPoint", and phpclass is the PERSISTED identity:
 * DBManager::submitShip writes the property (not get_class()), the reload does
 * `new $phpclass(...)`, and BaseShip::stripForJson sends it as the only route by which the client
 * learns what a unit is. Inherit the parent's value and the entrance is an entrance for exactly one
 * request, then reloads as an ordinary exit vortex that any ship may jump out through.
 *
 * ⚠️ THE FILENAME MUST MATCH THE CLASS NAME (bar case), for the same reason SpawnJumpPoint.php
 * carries that warning: ShipLoader::getShipClassnamesStatic enumerates ship classes by stripping
 * ".php" off every file under model/ships/ and calling class_exists() on the result, so a file named
 * anything else is SILENTLY skipped - no error, just a ship that does not exist. class_exists()
 * resolves through the GENERATED source/autoload.php, so a new file also needs
 * `scripts/fvbuild.ps1 -Autoload -Statics`, in that order.
 *
 * ONE-WAY, and it is enforced in four places, all of them cheap:
 *   Movement::getOpenVortexInHex   - an entrance is not a doorway out, so no Jump Out button
 *   Movement::getJumpOutVortex     - and a hand-forged jumpout order is refused
 *   Firing::getVortexDeclarationBlock - an entrance has no Maintain to declare
 *   JumpEngine::getVortexClosureReason - it is one-shot, and MUST close or trap 5 fires
 * The client mirror is shipManager.movement.isJumpVortexEntrance / isAnyJumpVortex - isJumpVortex
 * itself stays EXIT-ONLY, deliberately, because its callers do not agree on the verdict.
 */
class SpawnJumpPointEntrance extends SpawnJumpPoint{

    /* THE ARROW POINTS OUTWARD - the mirror of the exit's, and the mirror of its rule. On an exit
       the facing names the MOUTH a unit crosses inbound to leave; on an entrance it is the DOORWAY
       OUT, and an arriving unit is placed on that heading (plan §0).

       A NEW ASSET, not a rotation: img/directionOfVortex.png must not change, and rotating it would
       point the arrow at the wrong hex side rather than reversing it. The blue twin is that file
       mirrored WITHIN ITS OWN BOUNDING BOX and recoloured to #00b8e6 - so it occupies exactly the
       same 101x118 pixels of the same 512x512 canvas, which is what lets it share
       ShipIcon.FACING_ARROW_SCALE with the yellow one instead of needing a hand-synced twin of it.
       (Plan §3.7 anticipated three more constants; identical glyph geometry makes them unnecessary,
       and trap 7 is the reason not to add numbers that must be kept in step by eye.) */
    public $facingArrow = "img/directionOfVortexEntry.png";

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        //THE PERSISTED IDENTITY - see the ⚠️ in the class comment. Everything else here is
        //presentation; this line is the feature.
        $this->phpclass = "SpawnJumpPointEntrance";

        /* The name is a LABEL, not player data: spawnVortexUnit is the only thing that ever builds
           one of these and it passes a constant, exactly as it does for the exit. Overriding here
           rather than at the spawn site keeps the two ends of the round trip in agreement - the
           reload passes the stored name straight back in and this rewrites it to the same string.
           It is also what the client prints when a player tries to open their own vortex on top of
           this hex (weaponManager.getVortexHexBlock names the unit). */
        $this->name = "Jump Point Entrance";
        $this->shipClass = "Jump Vortex Entrance";

        /* ⚠️ THE TWO ASSET NAMES ARE THE OTHER WAY ROUND FROM WHAT YOU EXPECT, and both files
           predate this feature. They are the SAME cone art in two colours - mouth flaring east,
           because facing 0 is east - and it is the COLOUR that carries the meaning:
             img/ships/JumpPointEntrance.png  is YELLOW and is worn by SpawnJumpPoint, the EXIT
             img/ships/JumpPointExit.png      is BLUE   and is worn here, by the ENTRANCE
           Yellow = leaving, blue = arriving is FV's established pairing (§3.7 and the hexBlue
           marker). Do not "fix" the filenames; renaming an asset is a live cache-busting problem
           for no gain ([[arch_image_cache_busting]]). */
        $this->imagePath = "img/ships/JumpPointExit.png";

        $this->notes = "A jump vortex out of hyperspace.";
        $this->notes .= "<br>Reinforcements assigned to this jump point arrive here, on the heading the arrow shows.";
        $this->notes .= "<br>It cannot be jumped out of - that needs a vortex of the other kind.";
    }
}
?>
