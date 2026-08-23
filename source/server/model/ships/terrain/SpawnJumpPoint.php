<?php
/* JUMP_POINTS_PLAN.md STAGE 3 - THE VORTEX UNIT.
 *
 * A jump vortex projected into a hex by a ship's Jump Engine. It is spawned mid-game by
 * JumpEngine::spawnDeclaredVortices at the end of InitialOrdersGamePhase::advance, never bought,
 * and it is the thing units will fly into to leave the battle (Stage 4).
 *
 * WHY TERRAIN. shipSizeClass 5 (inherited from Terrain) makes isTerrain() true, and that single
 * fact buys almost everything this unit needs for free:
 *   - isMyShip / isMyorMyTeamShip return false outside Deployment, so NO player can select it,
 *     click it, plot it or give it orders. That is exactly the Phase 1 behaviour wanted (a Phase 2
 *     fixed jump gate WILL need to be clickable, which is the structural blocker recorded in the
 *     plan's section 7 - do not "solve" it here by loosening the terrain gate).
 *   - MovementGamePhase, setNextActiveShip and the mine detectors all skip terrain.
 *   - fleetList.js skips terrain, so it never appears in a fleet roster or a points total.
 * Two escape hatches close behind it: pointCost 0 means it contributes nothing to fleet value even
 * if some future total forgets to filter terrain, and canPreOrder stays false (BaseShip's default,
 * NOT Mine's override) so DeploymentPhaseStrategy will not offer it for re-placement in a later
 * Deployment phase - which a delayed-reinforcement game does have, on turns after the first.
 *
 * ⚠️ Enormous = FALSE, deliberately, copying jumpgateNew. Enormous terrain AUTO-RAMS everything
 * that flies through it (RammingAttack::beforePreFiringOrderResolution gates the whole collision
 * sweep on $shooter->Enormous), and a vortex that shredded any ship trying to use it would be the
 * exact opposite of the point. It also keeps the vortex out of gamedata->blockedHexes, which holds
 * Enormous units only - hence weaponManager.getVortexHexBlock sweeping gamedata.ships instead, so
 * that the "no second vortex in this hex" rule still sees it.
 *
 * FACING lives in the deploy MovementOrder, not on this class: free persistence, free rendering
 * (ShipIcon rotates the ship sprite to the movement facing) and free replay. The icon is drawn
 * with its mouth pointing EAST because facing 0 is east - plan section 2.2, do not re-derive it.
 *
 * variantOf 'NONE' hides it from the lobby ship list - the standard FV hiding mechanism; never do
 * this with an autoload exclude.
 *
 * ⚠️ THE FILENAME MUST MATCH THE CLASS NAME (bar case). ShipLoader::getShipClassnamesStatic
 * enumerates ship classes by stripping ".php" off every file under model/ships/ and calling
 * class_exists() on the result, so a file named anything else is SILENTLY skipped by the static
 * generator and by every lobby path built on it - no error, just a ship that does not exist.
 * (JUMP_POINTS_PLAN.md called this file jumpPointSpawn.php; that is why it does not.)
 */
class SpawnJumpPoint extends Terrain{

    /* ALWAYS-ON FACING ARROW (user request 2026-08-21). ShipIcon builds no direction sprites for
       Terrain - terrain does not move, so a heading arrow would be meaningless - but a vortex's
       facing IS its rule, and reading it off the icon art alone proved too subtle. Any ship class
       that sets $facingArrow gets that image drawn over its icon by ShipIcon, PERMANENTLY visible
       (not hover-gated the way a ship's prow/heading arrows are) and rotated with the unit.

       DECLARED as a property, not assigned in the constructor: PHP 8.2 deprecates dynamic
       properties and Manager's error handler rethrows every diagnostic, E_DEPRECATED included.

       Declared HERE and never on BaseShip: a base-class default would write "facingArrow":null into
       every entry of every static blueprint, and absent already reads as falsy on the client (same
       reasoning as JumpEngine::$hideFiringModeSelector - plan section 8).

       Same asset the Stage 2b facing control and the Forming marker use, so the arrow looks
       identical at all three points of a vortex's life: previewing, forming, open. */
    public $facingArrow = "img/directionOfVortex.png";

    /* JUMP_POINTS_PLAN.md STAGE 5 - the id of the SHIP whose Jump Engine holds this vortex open.

       Not persisted in tac_ship and not set by the constructor: JumpEngine::restoreVortexState
       stamps it from the 'Vortex' note's shipid on every load, which is the same note the open and
       close turns come from, so the link cannot drift from the state it describes.

       WHY THE CLIENT NEEDS IT. Maintaining is declared by targeting the vortex's OWN hex with the
       Jump Engine, so weaponManager.targetHex has to tell 'this is my jump point, keep it open'
       from 'this hex is blocked by terrain' - and userid alone will not do it: a player may have
       several ships with jump engines and only the holder may maintain.

       No masking question: the declaration's launch line is drawn from the shooter to the hex from
       phase 2 onward, so who opened a vortex has been public knowledge since Stage 2. */
    public $vortexHolderId = null;

    /* Emitted only when set, so a vortex that has somehow lost its note is byte-identical to
       before. The static blueprint carries vortexHolderId: null (the generator serialises public
       properties) and model/ship.js applies the blueprint FIRST, so the live value always wins. */
    public function stripForJson(){
        $strippedShip = parent::stripForJson();
        if ($this->vortexHolderId !== null) $strippedShip->vortexHolderId = (int)$this->vortexHolderId;
        return $strippedShip;
    }

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 0; //never bought, and must not disturb any fleet value it slips past
        $this->faction = "Terrain";
        $this->phpclass = "SpawnJumpPoint";
        $this->imagePath = "img/ships/JumpPointEntrance.png";
        $this->canvasSize = 200;
        $this->shipClass = "Jump Vortex";
        $this->variantOf = 'NONE'; //hidden from the lobby - see the class comment
        $this->Enormous = false;   //MUST stay false - see the class comment
        $this->iniativebonus = -200; //no voluntary movement anyway
        $this->isd = 0;
        $this->notes = "A jump vortex into hyperspace.";
        $this->notes .= "<br>Units entering this hex through the hex side the vortex faces may jump out of the battle.";

        $this->base = true;
        $this->smallBase = true;
        $this->nonRotating = true;  //completely immobile, doesn't even rotate

        $this->forwardDefense = 20;
        $this->sideDefense = 20;

        $this->turncost = 99;
        $this->turndelaycost = 99;
        $this->accelcost = 99;
        $this->rollcost = 99;
        $this->pivotcost = 99;

        //Block all enhancements for Terrain units when bought
        Enhancements::nonstandardEnhancementSet($this, 'Terrain');

        //OSATCnC for the same reason every other terrain file carries one ("Required for some
        //checks" - asteroidSNew): a unit with no C&C at all trips isDisabled() and friends.
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));

        //INDESTRUCTIBLE primary Structure. Nothing can shoot the vortex away: Structure's
        //isIndestructible flag clears the 'destroyed' marker in criticalPhaseEffects every turn, so
        //BaseShip::isDestroyed (which asks only about primary Structure) never returns true. The
        //vortex leaves the board by $removed / $removedTurn when it closes - Stage 5 - not by damage.
        $this->addPrimarySystem(new Structure(0, 1, true));

        //d20 hit chart - everything resolves onto the indestructible Structure, so a shot at the
        //vortex is a wasted shot rather than an error.
        $this->hitChart = array(
                0=> array(
                        20 => "Structure",
                ),
                1=> array(
                        20 => "Primary",
                ),
                2=> array(
                        20 => "Primary",
                ),
        );
    }
}
?>
