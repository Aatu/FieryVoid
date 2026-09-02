'use strict';

/* REINFORCEMENTS_PLAN.md STAGE 4 - CALLING REINFORCEMENTS IN.
 *
 * The client half of a jump point EXIT. A player with units waiting in hyperspace opens a
 * doorway for them: pick which of those units opens it, pick a hex, pick the facing, then say which
 * of the rest ride through it.
 *
 *     "Manage Reinforcements" (#iniGui)
 *         -> the doorway menu ...... EVERY jump-capable unit still in hyperspace, plus every JUMP
 *             |                      GATE holding a doorway in for me, each marked with what it
 *             |                      is already holding
 *             |-> "Withdraw Jump Point" ... a unit that already holds one; the menu STAYS OPEN
 *             |                              and re-renders, so re-placing it is the next click
 *             |-> "Jump Manifest" ........ the SECOND button, on any row that already holds a
 *             |       \-> manifest dialog     doorway - re-open the passenger list without taking
 *             |                              the jump point back first (2026-09-02). The manifest
 *             |                              has its own way back HERE, so the two are a loop
 *             |-> "Select Reinforcements" . a GATE whose jump point is already open (Stage 8)
 *             |       \-> manifest dialog ..... who rides through, this turn
 *             |-> "Withdraw Gate Signal" .. a gate this client claimed for arrival this turn
 *             \-> "Choose Hex" ............ the map is armed; the next click is the exit hex
 *                     -> UI.vortexFacing (BLUE) ... turn the doorway, confirm
 *                         -> manifest dialog ..... who rides through
 *                             -> the FireOrder exists, and ship.arrivalVia is stamped locally
 *
 * ⭐ STAGE 8 - A JUMP GATE IS THE SECOND KIND OF DOORWAY, and it enters this file by a different
 * door of its own: the gate is CLICKED on the map, in Initial Orders, and its tooltip offers
 * "Signal Gate for Arrival" (shipTooltipInitialOrdersMenu -> UI.gateSignal in its blue livery ->
 * weaponManager.createGateSignalOrder), which then calls straight into showGateManifest() below.
 * So the manifest is named the same way whichever doorway is being used, and the menu above is the
 * place a doorway that is ALREADY open is picked up again on a later turn. See the gate block
 * further down for why almost none of the opener machinery applies to one.
 *
 * ⭐⭐ THIS IS NOT A TARGETING GESTURE AND IT DELIBERATELY DOES NOT GO THROUGH targetHex
 * (plan §3.4). weaponManager.targetHex measures range from the shooter's hex and runs
 * mathlib.isLoSBlocked from it - and the opener HAS NO HEX. It is in hyperspace; the only movement
 * row it owns is the 'start' one every ship is given at its slot's deployment-box centre, which is
 * not a place it is. Every test in that pipeline is either the wrong question or an outright wrong
 * answer here, so this is its own short path - exactly the relationship UI.gateSignal has to
 * UI.vortexFacing. There is NO range test and NO line-of-sight test, by rule (§2.2).
 *
 * ⚠️ NOTHING IS COMMITTED UNTIL THE FACING IS CONFIRMED. Cancelling at any earlier step, clicking
 * away, or leaving the phase leaves no order, no arrivalVia and nothing to clean up - the same
 * transaction discipline UI.vortexFacing documents at the top of itself.
 *
 * ⚠️ window.UI is CREATED by shipMovement.js, so game.php must load this file after it - see the
 * defensive guards on every UI.vortexFacing call below.
 */
window.ReinforcementEntry = (function () {

    /* THE OPENER THE PLAYER PICKED, BY ID. Null means the mode is off.

       ⚠️ AN ID AND NEVER A SHIP OBJECT. gamedata.setShipsFromJson REPLACES every entry of
       gamedata.ships with a fresh `new Ship(...)` on every poll that carries ship data, so a held
       reference goes stale silently - and the failure is invisible rather than loud: the order
       would be pushed onto the fireOrders array of an object no longer in gamedata.ships, so it
       would never reach the POST and the declaration would simply not happen. The mode is armed
       across exactly the window in which a poll lands (the player is looking at the map, choosing
       a hex), so this is the normal case and not a corner one. Resolve fresh at every use. */
    var _openerId = null;
    var _banner = null;

    function openerShip() {
        return _openerId === null ? null : gamedata.getShip(_openerId);
    }

    /* ---------------------------------------------------------------- the eligible units */

    /* Is this MY unit, bought as a reinforcement and still waiting in hyperspace?

       arrivalTurn is the whole test, and it is the same one BaseShip::isReinforcement makes on the
       server: null means "no exit has been assigned to it yet". Once the Stage 6 sweep stamps
       an arrival turn the unit stops being a reinforcement in this sense - it becomes an ordinary
       unit with a late deploy turn, and it must not be offered for a second ride. */
    function isMine(ship) {
        return !!ship && ship.reinforcement === true
            && (ship.arrivalTurn === null || ship.arrivalTurn === undefined)
            && gamedata.isMyShip(ship)
            && !shipManager.isDestroyed(ship);
    }

    /* ⭐ STAGE 9 - THE RULE GATE IS HERE, at the head of the one list the whole module is built on
       (user request 2026-08-29). isOffered, canSignalJumpGateForArrival, strandedByCommit and every
       dialog reach hyperspace through this function, so one test switches the feature off wholesale
       for a game that does not have it - instead of a filter over every ship on every UI refresh
       for the whole battle. Nothing can be in hyperspace without the rule anyway: BuyingGamePhase
       refuses the flag outright. */
    function myHyperspaceUnits() {
        if (!gamedata.reinforcementsAllowed()) return [];

        return gamedata.ships.filter(isMine);
    }

    /* Does this unit mount a Jump Engine that could bring its group OUT of hyperspace?

       ⭐⭐ ANY JUMP ENGINE, LEGACY INCLUDED (Stage 9, user ruling 2026-08-29). Up to Stage 8 this
       was the three-property legacy test - $legacyJump is PROTECTED server-side and never reaches a
       blueprint, so the readable trace of markLegacy() is ballistic/hextarget cleared (ShipCompactor
       strips a false key outright, so both read undefined) and range zeroed. That test is now wrong
       in this direction: a Shadow hull phases IN, and the server's arrival list
       (Firing::getExitDeclarationBlock) has no range, line-of-sight, offline or charge rule for a
       legacy engine to fail. Its whole difference lives at spawn time, where the doorway becomes a
       SpawnJumpPointPhaseIn and is never drawn.

       ⚠️ THE EXIT READER STILL REFUSES A LEGACY DRIVE, and that asymmetry is the feature, not an
       inconsistency: such a hull may come back, and may still never open a way OUT. If you are
       here because "a legacy drive should not open a jump point", the rule you want is
       JumpEngine::getVortexDeclaration's, and it is intact.

       ⚠️ isJumpGate IS EXCLUDED HERE AND MUST STAY EXCLUDED. A gate is a doorway too from Stage 8,
       but it is not an OPENER in this sense: nobody's drive is holding it, it is not in hyperspace,
       it is signalled rather than aimed, and it belongs to nobody. gateCandidates() below is its
       list; this one is "whose drive can I spend?". Merging them would put a gate in front of the
       hex picker. */
    function canOpen(ship) {
        if (!ship || !ship.systems) return false;
        if (gamedata.isJumpGate(ship)) return false;

        return !!arrivalEngineOf(ship, true);
    }

    /* THE ENGINE canOpen FOUND, so the two can never disagree about which system it was.
       $checkDestroyed is what separates them: canOpen is offering a gesture and must not offer one
       through a wrecked drive, while every later resolution re-reads the engine off a fresh ship
       object and only wants the same system back. (Pre-battle damage can destroy a Jump Engine
       before turn 1, so "destroyed" is reachable on a unit that has never been shot at.) */
    function arrivalEngineOf(ship, checkDestroyed) {
        if (!ship || !ship.systems) return null;

        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (!s || s.name !== 'jumpEngine') continue;
            if (checkDestroyed && shipManager.systems.isDestroyed(ship, s)) continue;
            return s;
        }

        return null;
    }

    function jumpEngineOf(ship) {
        return arrivalEngineOf(ship, false);
    }

    /* EVERY unit of mine in hyperspace that could open an exit - INCLUDING the ones already
       holding a declaration this turn.

       ⭐ INCLUDING THEM IS THE WHOLE OF THE SINGLE MENU (user request 2026-08-28). This used to
       be `availableOpeners()`, which filtered declared units out, because the #iniGui button
       flipped wholesale into withdraw mode as soon as any declaration stood - so a second opener
       could never be reached, and a fleet with three jump-capable hulls could open exactly one
       doorway per turn. The list is the menu now, and the row says which state each unit is in.

       One exit per unit is still the rule (the server's one-vortex-per-ship test); it is
       expressed by what the row OFFERS - withdraw rather than declare - instead of by hiding the
       unit, so nothing here can produce an order the server would reject. */
    function openerCandidates() {
        return myHyperspaceUnits().filter(canOpen);
    }

    /* ---------------------------------------------------------------- the declaration */

    /* THIS TURN'S exit declaration on $ship, or null.

       ⚠️ SCOPED TO THIS TURN, never a bare fireOrders check, for the same reason
       weaponManager.getGateSignalOrder is: an engine accumulates every order it has ever made and
       the ones from earlier turns are history. While Initial Orders are open the only current-turn
       order that can be here is one THIS client just made - TacGamedata::hideSystemFireOrders
       strips every phase-1 ballistic order from every payload, its author's included. */
    function declarationOn(ship) {
        var engine = jumpEngineOf(ship);
        if (!engine || !Array.isArray(engine.fireOrders)) return null;

        for (var i = 0; i < engine.fireOrders.length; i++) {
            var fire = engine.fireOrders[i];
            if (!fire || fire.turn != gamedata.turn) continue;
            if (fire.damageclass !== 'jumpexit') continue;
            return fire;
        }

        return null;
    }

    /* Every exit this player has declared this turn, as {ship, order} pairs. */
    function declarations() {
        var out = [];
        myHyperspaceUnits().forEach(function (ship) {
            var order = declarationOn(ship);
            if (order) out.push({ ship: ship, order: order });
        });
        return out;
    }

    /* Withdraw one. Turn-scoped, exactly as removeGateSignalOrder is - removeFiringOrder would take
       every order the engine has ever carried with it. The manifest goes back to unassigned in the
       same breath: an exit that no longer exists cannot be ridden through, and leaving
       arrivalVia pointing at a withdrawn opener would post a manifest for nothing. */
    function withdraw(ship) {
        var engine = jumpEngineOf(ship);
        if (!engine || !Array.isArray(engine.fireOrders)) return;

        for (var i = engine.fireOrders.length - 1; i >= 0; i--) {
            var fire = engine.fireOrders[i];
            if (!fire || fire.turn != gamedata.turn) continue;
            if (fire.damageclass !== 'jumpexit') continue;
            engine.fireOrders.splice(i, 1);
        }

        clearManifest(ship.id);

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: engine });
        webglScene.customEvent('HexTargeted', { shooter: ship, hexagon: null });
        gamedata.drawIniGUI();
    }

    /* ---------------------------------------------------------------- the manifest */

    /* arrivalVia names the OPENER, never the vortex: the vortex does not exist yet (it is created
       two phases later, at the end of this turn) and for a gate it may never be created at all if
       the claim is lost. Keying on the opener makes the refund automatic - a manifest that never
       gets a vortex is simply never stamped with an arrival turn (plan §3.1). */
    function clearManifest(openerId) {
        myHyperspaceUnits().forEach(function (ship) {
            if (ship.arrivalVia == openerId) ship.arrivalVia = null;
        });
    }

    function manifestOf(openerId) {
        return myHyperspaceUnits().filter(function (ship) {
            return ship.arrivalVia == openerId;
        });
    }

    /* WHO COULD BE PUT ON THIS DOORWAY'S MANIFEST RIGHT NOW: everything of mine still in
       hyperspace that is not already spoken for by a DIFFERENT doorway, plus the ones already
       riding this one. A unit can only ride one jump point, and silently moving it would undo a
       choice the player has already made on another opener.

       Split out of showManifestDialog (user request 2026-09-02) because the Jump Manifest button
       has to ask the same question BEFORE it offers itself: a doorway with nobody left to offer
       would put a button on screen whose only possible outcome is a notice. One rule, two callers,
       so the button and the dialog can never disagree about whether there is anything to choose. */
    function manifestRiders(opener) {
        return myHyperspaceUnits().filter(function (ship) {
            if (ship.id == opener.id) return false;
            return ship.arrivalVia === null || ship.arrivalVia === undefined || ship.arrivalVia == opener.id;
        });
    }

    /* ---------------------------------------------------------------- the stranding check */

    /* Is this unit actually leaving hyperspace this turn? Being ON a manifest is not enough: the
       exit it names has to still exist. withdraw() clears the manifest it opened, but a
       declaration can also be replaced (createExitOrder withdraws first), so the order is
       the authority and arrivalVia is only the pointer to it. */
    function ridingOut(ship) {
        if (ship.arrivalVia === null || ship.arrivalVia === undefined) return false;

        var opener = gamedata.getShip(ship.arrivalVia);
        if (!opener) return false;

        /* STAGE 8 - A GATE BERTH IS HONOURED BY THE GATE, NOT BY A DECLARATION. There is nothing on
           a gate that looks like declarationOn(): the doorway may have been opened turns ago, by
           somebody else entirely, and the only thing this turn's claim proves is that one is about
           to form. gateDoorway() answers both cases and is the single place that rule lives. */
        if (gamedata.isJumpGate(opener)) return !!gateDoorway(opener);

        return !!declarationOn(opener);
    }

    /* SOMEBODY ELSE'S jump point that this unit is already booked to ride, or null.

       ⭐ THE TEST THE MENU GREYS A ROW ON (user request 2026-08-28). A unit that is riding
       through another ship's doorway is spoken for: opening a SECOND jump point with it would
       have it hold a drive open on one side of the map while arriving through somebody else's
       on the other. The declaration would be built, the manifest would still point at the first
       opener, and the two would disagree - so the menu refuses the choice rather than letting it
       be made and then unpicked.

       ⚠️ arrivalVia == its OWN id is not riding with anybody - that is what createExitOrder
       stamps on an opener, because a drive always comes through its own doorway. Without this
       line every opener would grey itself out the moment it declared, and Withdraw would be
       unreachable.

       Riding is undone by withdrawing the exit that carries it (withdraw() clears its whole
       manifest), which is why nothing has to un-grey a row by hand: the next render simply finds
       no standing declaration to ride. */
    function ridingWith(ship) {
        if (!ridingOut(ship)) return null;
        if (ship.arrivalVia == ship.id) return null;

        return gamedata.getShip(ship.arrivalVia);
    }

    /* ⭐ WHICH OF MY REINFORCEMENTS WOULD BE STRANDED IN HYPERSPACE FOR GOOD if Initial Orders
       were committed as they stand (user report 2026-08-28). Rendered by gamedata.onCommitClicked
       in the phase-1 confirm; empty means there is nothing to say.

       THE TRAP THIS EXISTS FOR: a reinforcement with no jump drive of its own can only ever
       arrive as a passenger on somebody else's exit. If the one unit that COULD open a
       doorway declares one, leaves the passengers off its manifest and jumps in alone, then from
       next turn there is nobody left in hyperspace able to open anything - and those units sit
       there for the rest of the battle, paid for and unusable. Nothing said so, and by the time
       it was visible the order could not be taken back. This is the last moment it can be.

       THE TEST IS DELIBERATELY NARROW, so it never cries wolf:
         - nothing is departing this turn ..... silence. No declaration, nothing changes, and the
                                               player can still call everybody in on a later turn.
         - somebody able to open the NEXT doorway is staying behind ... silence. The units left
                                               over can still be called; keeping a drive in
                                               reserve is a legitimate plan, not a mistake.
         - otherwise ......................... the units left over are named.

       ⭐ STAGE 8 - A JUMP GATE ON THE MAP COUNTS AS A REMAINING WAY IN, and the plan wrote this
       down as the change Stage 8 would have to make here. Without it the warning fires on fleets
       that are perfectly fine, which is the one failure mode a nag like this must not have: a gate
       can be signalled by anybody, on any later turn, for a doorway in, so as long as one is
       standing nothing is stranded for good.

       ⚠️ AND THE GATE TEST IS DELIBERATELY LOOSE - "a live gate exists", not "I could signal it
       right now". Charge, signal range and a rival claim all move between turns, and the question
       this method asks is about the WHOLE REST OF THE BATTLE rather than about this turn. Loose
       makes it silent where it might have cried wolf, which is the right direction to err in. */
    function strandedByCommit() {
        var waiting = myHyperspaceUnits();

        var leaving = waiting.filter(ridingOut);
        if (leaving.length === 0) return [];

        var left = waiting.filter(function (ship) { return !ridingOut(ship); });
        if (left.some(canOpen)) return [];
        if (anyLiveGate()) return [];

        return left;
    }

    /* ---------------------------------------------------------------- jump gates (Stage 8) */

    /* ⭐⭐ A FIXED GATE IS THE OTHER KIND OF DOORWAY, AND ALMOST NOTHING ABOVE APPLIES TO IT.
     *
     * A reinforcement's exit is opened by its own drive, from hyperspace, at a hex it aims, and
     * it is one-shot. A gate's is opened by SIGNALLING a unit nobody owns; it forms on the gate's own
     * hex on the gate's own fixed facing, which cannot be aimed; and it stands for the programmed
     * hold - one to four turns - with a fresh wave allowed on each of them (plan section 0).
     *
     * So a gate is never an "opener candidate": it never reaches the hex picker or the facing
     * control, because there is nothing to pick or turn. It appears in the menu as a doorway that
     * either already exists or is about to, and the only thing the player does with it there is name
     * a manifest - which is why its button reads Select Reinforcements (user request 2026-08-28).
     *
     * ⚠️ EVERY ONE OF THESE TAKES A LIVE GATE OBJECT AND RE-READS IT. gamedata.setShipsFromJson
     * replaces every entry of gamedata.ships on each poll that carries ship data (trap 17), and the
     * menu is open across exactly that window - so nothing here may be cached, and the menu's click
     * handler re-resolves the gate through gamedata.getShip before acting on it. */

    function anyLiveGate() {
        for (var i in gamedata.ships) {
            var unit = gamedata.ships[i];
            if (!gamedata.isJumpGate(unit)) continue;
            if (shipManager.isDestroyed(unit)) continue;
            return true;
        }

        return false;
    }

    /* THE OPEN EXIT VORTEX THIS GATE IS HOLDING, or null.

       ⚠️ isJumpVortexExit, NEVER isJumpVortex. A gate holding an ordinary yellow ENTRANCE is not a
       doorway in - the two are one-way in opposite directions (section 2.6) - and an entrance is exactly
       what an ENEMY winning the claim contest looks like. Widening this would offer the player a
       manifest for a jump point their units can never come out of. */
    function gateExitOn(gate) {
        if (!gate || !gamedata.isJumpGate(gate)) return null;
        return shipManager.movement.getExitHeldBy(gate.id);
    }

    /* THIS TURN'S ARRIVAL CLAIM ON THIS GATE, or null. The claim is a ballistic order on the GATE's
       Jump Engine with damageclass 'gateexit'; weaponManager owns the turn-scoping and the
       firing-mode range, so this only has to add the flavour test.

       ⚠️ IT CANNOT TELL WHOSE CLAIM IT IS, AND IT DOES NOT HAVE TO. TacGamedata::hideSystemFireOrders
       strips every phase-1 ballistic order from every payload, its author's included, so while
       Initial Orders are open the only current-turn order that can be sitting here is one THIS
       client just made. (targetid names the claimant, but it is masked for every viewer it does not
       belong to - trusting it would be trusting a field that is deliberately a lie.) */
    function gateClaimOn(gate) {
        var fire = weaponManager.getGateSignalOrder(gate);
        return (fire && fire.damageclass === 'gateexit') ? fire : null;
    }

    /* ⭐ WILL A MANIFEST NAMED NOW ACTUALLY COME THROUGH THIS GATE? The one piece of arithmetic in
       the gate half, and the reason the menu can say "closes this turn" rather than quietly taking a
       manifest that will never be honoured.

       A wave named in Initial Orders of turn T arrives in the DEPLOYMENT PHASE OF T+1 - Deployment
       is the first phase of a turn - so what matters is whether the doorway is still there THEN. On
       the last turn of a gate's hold it is not.

       ⚠️ THE NUMBERS ARE THE SYSTEM ICON'S, NOT NEW PAYLOAD. JumpEngine::stripForJson has sent
       vortexTurnsOpen / vortexMaxTurns since Phase 2 to draw the "2/4 turns open" counter, and on a
       GATE vortexMaxTurns IS the programmed hold (it is MAX_VORTEX_TURNS only on a ship's). The age
       reaches the hold on the final open turn, so "age < hold" is exactly "one more turn to give".
       Absent - a gate with no vortex at all - parses to NaN and answers false, which is right. */
    function gateTakesAWave(gate) {
        var engine = gamedata.getGateJumpEngine(gate);
        if (!engine) return false;

        var age  = parseInt(engine.vortexTurnsOpen, 10);
        var hold = parseInt(engine.vortexMaxTurns, 10);
        if (isNaN(age) || isNaN(hold)) return false;

        return age < hold;
    }

    /* Is this gate a doorway a berth can be booked on RIGHT NOW - either because it already holds
       one that survives into next turn, or because a claim made this turn is about to open one?
       The two are mutually exclusive in practice (a gate holding a vortex has no charge left to
       signal with) but both are asked, because the rule is "there will be a doorway", not "how". */
    function gateDoorway(gate) {
        if (gateClaimOn(gate)) return true;
        return !!gateExitOn(gate) && gateTakesAWave(gate);
    }

    /* EVERY GATE WORTH SHOWING IN THE MENU. A gate is listed when it is holding a doorway in, or
       when this client has just claimed one on it - and a doorway on its LAST turn is listed too,
       greyed, so the player can see WHY they cannot use it rather than wondering where it went. */
    function gateCandidates() {
        return gamedata.ships.filter(function (unit) {
            if (!gamedata.isJumpGate(unit)) return false;
            if (shipManager.isDestroyed(unit)) return false;

            return !!gateExitOn(unit) || !!gateClaimOn(unit);
        });
    }

    /* ---------------------------------------------------------------- the hex-click mode */

    function isActive() {
        return _openerId !== null;
    }

    function activate(opener) {
        deactivate();
        if (!opener) return;

        _openerId = opener.id;
        showBanner();
        gamedata.drawIniGUI();
    }

    function deactivate() {
        //Every teardown path goes through here - the menu's Choose Hex, the button's Cancel, the
        //phase strategy dropping the mode - so this is where the map stops pointing at a row
        //nobody is looking at any more. ABOVE the early return, which guards only the banner half.
        highlight(null);

        if (_openerId === null) return;
        _openerId = null;
        hideBanner();
        gamedata.drawIniGUI();
    }

    /* ⭐ THE DECLARED EXIT THE MENU IS CURRENTLY POINTING AT, BY OPENER ID (user request
       2026-08-28). Null - the value at every moment the menu is not open - means "point at
       nothing", and every marker draws in its ordinary blue.

       WHY IT EXISTS: a fleet with three drives puts three identical blue "Jump Point Forming"
       markers on the map, and a row reading `hex 4,-2 — 3 units` cannot tell you WHICH of them
       belongs to the unit you are about to withdraw. Selecting a declared row now names its own
       marker on the map - white, with the opener's name written into the hex - so the answer is
       a glance rather than an arithmetic.

       ⚠️ AN ID AND NEVER A SHIP OBJECT, for the reason _openerId carries at the top of this file:
       every poll replaces every entry of gamedata.ships.

       ⚠️ AN INT. The value arrives from `$(...).val()`, which is a STRING, and it is compared with
       `===` here (to skip a pointless rebuild) and against `order.shooterid` in the renderer. One
       normalisation, at the door. */
    var _highlightId = null;

    function getHighlightedOpener() {
        return _highlightId;
    }

    /* Point the map at one unit's declared exit, or - for null, an unknown id, or a unit with
       no declaration of its own - at nothing. The marker itself is drawn by
       BallisticIconContainer.generateExitHexes, which asks getHighlightedOpener() as it goes.

       ⚠️ THE EVENT CARRIES NO SHOOTER, deliberately. Firing 'HexTargeted' is how this asks for the
       ballistic icons to be rebuilt (PhaseStrategy.onHexTargeted, the same event withdraw() uses),
       but that handler then compares `payload.shooter` with the SELECTED ship - and selectedShip
       is null when nothing is selected, so a `shooter: null` would rebuild the weapon list for a
       ship that does not exist. An absent property matches neither a null nor a ship.
       webglScene.customEvent calls requestRender() itself, so the idle-gated render loop wakes
       without a second call ([[arch_render_loop_idle_gating]]). */
    function highlight(shipId) {
        var id = null;

        if (shipId !== null && shipId !== undefined) {
            var parsed = parseInt(shipId, 10);
            var ship = isNaN(parsed) ? null : gamedata.getShip(parsed);
            if (ship && declarationOn(ship)) id = parsed;
        }

        if (_highlightId === id) return;   //the change handler fires on every click on a row

        _highlightId = id;
        webglScene.customEvent('HexTargeted', { hexagon: null });
    }

    /* The one instruction the mode needs, as a plain anchored strip rather than anything on the
       map: the hex has not been chosen yet, so there is nowhere on the map to anchor to.
       Built and torn down here rather than living in game.php, because it exists only while the
       mode is armed and an empty div in the markup would be one more thing to keep hidden. */
    function showBanner() {
        hideBanner();

        var opener = openerShip();
        if (!opener) return;

        _banner = document.createElement('div');
        _banner.id = 'reinforcementEntryBanner';
        _banner.innerHTML = '<b>' + opener.name + '</b> is opening a jump point &mdash; '
            + 'click the hex it should form in.<span class="reinforcementEntryCancel">Cancel</span>';
        document.getElementById('pagecontainer').appendChild(_banner);

        _banner.querySelector('.reinforcementEntryCancel').addEventListener('click', function (e) {
            e.stopPropagation();
            deactivate();
        });
    }

    function hideBanner() {
        if (!_banner) return;
        if (_banner.parentNode) _banner.parentNode.removeChild(_banner);
        _banner = null;
    }

    /* Is this hex a legal place to open an exit? Returns null when it is, or the reason when it
       is not (plan §2.2).

       ⭐ NO RANGE AND NO LINE OF SIGHT, and their absence is the rule rather than an omission:
       there is no ship on the board to measure either from.

       The obstruction test is weaponManager.getVortexHexBlock, the very same sweep an ENTRANCE
       declaration uses - it reads whole terrain footprints (hexOffsets and Huge radii alike) and
       catches gates and existing vortices for free, because both are Terrain. It takes ship and
       weapon arguments it does not read, so the opener and its engine are passed through honestly
       rather than faked. */
    function hexBlock(hex) {
        if (!onMap(hex)) return "A jump point cannot be opened off the map.";

        var opener = openerShip();
        return weaponManager.getVortexHexBlock(opener, jumpEngineOf(opener), hex);
    }

    /* Is this hex inside the playing area? The server re-derives the same bounds from
       $gamedata->gamespace, which is a "WIDTHxHEIGHT" string with -1x-1 meaning unlimited - and
       unlimited is exactly what BuyingGamePhase::getGamespace substitutes 60x40 for, so an
       "unlimited" map is not actually unbounded and this must not treat it as though it were.

       Offset coordinates are centred on 0,0 and the game space is stated as a full width and
       height, so the legal band is half of each either side. */
    function onMap(hex) {
        var width = 60, height = 40;

        var match = (gamedata.gamespace || '').match(/^(-?\d+)x(-?\d+)$/);
        if (match) {
            var w = parseInt(match[1], 10);
            var h = parseInt(match[2], 10);
            if (w > 0) width = w;
            if (h > 0) height = h;
        }

        return Math.abs(hex.q) <= Math.floor(width / 2) && Math.abs(hex.r) <= Math.floor(height / 2);
    }

    /* Called from InitialPhaseStrategy.onClickEvent BEFORE the click reaches the ordinary
       ship/hex dispatch. Returns true when the click was consumed.

       ⚠️ INTERCEPTED AT onClickEvent, NOT AT onHexClicked. onHexClicked is only reached when the
       click landed on no icon at all - but a hex holding a SHIP is a perfectly legal place to open
       an exit (§2.2 forbids terrain, gates, vortices and Enormous units, and nothing else), and
       an arriving wave standing on top of somebody is the normal case. Waiting for onHexClicked
       would silently refuse every hex with a unit in it. */
    function onMapClick(payload) {
        if (!isActive()) return false;

        //Right-click cancels, matching every other bespoke mode on this map.
        if (payload.button !== 0 && payload.button !== undefined) {
            deactivate();
            return true;
        }

        var hex = payload.hex;
        var block = hexBlock(hex);
        if (block) {
            confirm.error(block);
            return true;   //consumed: the mode stays armed so the player can just click elsewhere
        }

        //Resolved AFTER the legality check, so a poll that landed mid-decision cannot leave a
        //stale object behind. If the unit has gone entirely, the mode simply closes.
        var opener = openerShip();
        deactivate();
        if (opener) openFacingControl(opener, hex);
        return true;
    }

    /* ---------------------------------------------------------------- the facing, then the order */

    /* Hand the hex to the shared facing control, in its EXIT livery. The control is the same
       transaction UI.vortexFacing runs for an entrance - nothing is committed until the tick - and the
       `exit` flag is the whole of the difference: cyan instead of yellow, and the arrow drawn
       with the outward asset, because an exit's facing is the doorway OUT rather than the mouth
       units cross inbound (§0). */
    function openFacingControl(opener, hex) {
        var engine = jumpEngineOf(opener);
        if (!engine) return;

        webglScene.customEvent('VortexFacingRequested', {
            ship: opener,
            weapon: engine,
            hexpos: hex,
            type: 'ballistic',
            exit: true,
            facing: 0,
            /* ⚠️ RE-RESOLVED AT THE TICK, not captured. The facing control is open for as long as
               the player takes to turn the doorway, and a poll in that window replaces every ship
               object - so pushing the order onto the captured `engine` would push it onto a
               discarded copy and lose the declaration without a word. The id is stable; the
               objects are not. */
            onConfirm: function (facing) {
                var live = gamedata.getShip(opener.id);
                var liveEngine = jumpEngineOf(live);
                if (!live || !liveEngine) return;

                createExitOrder(live, liveEngine, hex, facing);
            }
        });
    }

    /* BUILD THE DECLARATION. Stored exactly as an entrance's is - a ballistic FireOrder on the unit's
       Jump Engine, x/y the hex, firingMode = facing + 1 - with damageclass 'jumpexit' as the
       discriminator, mirroring 'jumppoint' (§3.4). That one string is what routes it to
       Firing::getExitDeclarationBlock instead of the ship rules, and what keeps it out of
       JumpEngine::getVortexDeclaration's entrance sweep.

       ⚠️ targetid IS -1 AND MUST STAY -1 (trap 10). Every ballistic-icon path reads targetid as
       "hang the marker on that unit", and the unit here is in hyperspace: the marker would be drawn
       at its slot's deployment-box centre, with a bright line running to the exit hex from a
       ship that is not on the board.

       Re-checked at the tick rather than only at the click, exactly as createJumpPointOrder is: a
       server poll can land between the two and put something in the hex. */
    function createExitOrder(opener, engine, hex, facing) {
        var block = weaponManager.getVortexHexBlock(opener, engine, hex);
        if (block) {
            confirm.error(block);
            return;
        }

        withdraw(opener);   //one exit per unit per turn; re-declaring replaces

        engine.fireOrders.push({
            id: opener.id + "_" + engine.id + "_" + (engine.fireOrders.length + 1),
            type: 'ballistic',
            shooterid: opener.id,
            targetid: -1,
            weaponid: engine.id,
            calledid: -1,
            turn: gamedata.turn,
            firingMode: facing + 1,
            shots: engine.defaultShots,
            x: hex.q,
            y: hex.r,
            damageclass: 'jumpexit'
        });

        //The opener always rides its own exit - it is the unit whose drive is holding the
        //doorway open, and leaving it behind would strand the drive on the far side.
        opener.arrivalVia = opener.id;

        webglScene.customEvent('SystemDataChanged', { ship: opener, system: engine });
        webglScene.customEvent('HexTargeted', { shooter: opener, hexagon: hex });

        showManifestDialog(opener);
    }

    /* ---------------------------------------------------------------- dialogs */

    /* THE ONE MENU FOR EVERY JUMP-CAPABLE UNIT IN HYPERSPACE (user request 2026-08-28).

       Replaces the old pair of dialogs - "Call Reinforcements" (openers with no declaration) and
       "Withdraw Jump Point" (openers with one) - which between them made it impossible to open a
       SECOND exit: the #iniGui button flipped to withdraw mode the moment any declaration
       stood, and the declare path could not be reached again until it was taken back. One list,
       both states, and the row the player picks decides which action is on offer.

       ⭐ THE PRIMARY BUTTON'S LABEL FOLLOWS THE SELECTION, because the two actions are opposites
       and nothing else in the dialog can say which one is armed. A unit with no declaration goes
       to the hex picker ("Choose Hex"); a unit already holding one is withdrawn ("Withdraw Jump
       Point"). The label is re-synced on every radio change AND once up front, because the first
       row is pre-checked and may already be a declared unit.

       ⚠️ data-declared is written into the row and read back rather than re-derived at click
       time - but the CLICK still re-derives it from live gamedata. The attribute is for the
       label only; a poll can land while the dialog is open and a declaration cannot be trusted
       to still be there (see _openerId's note at the top of this file). data-manifest, which
       decides whether the third button is offered, is read and re-derived exactly the same way.

       ⭐ THE THIRD BUTTON, JUMP MANIFEST (user request 2026-09-02), is how a passenger list is
       changed without withdrawing the doorway it belongs to. See the block that builds it. */
    function manageReinforcements() {
        var candidates = openerCandidates();
        var gates      = gateCandidates();

        if (candidates.length === 0 && gates.length === 0) {
            confirm.error("None of your reinforcements can open a jump point, and no jump gate is "
                + "holding one open for you.");
            return;
        }

        /* One candidate with nothing declared is a dialog with a single row and one possible
           answer - straight to the map, exactly as the opener picker always did.
           ⚠️ NOT skipped when that one unit already holds a declaration. The only thing the menu
           could do for it is withdraw, and withdrawing a jump point on a bare button click with
           no confirmation is not something a player should be able to trip over.
           ⚠️ AND NOT WHEN A GATE IS LISTED (Stage 8). With a doorway standing there is a second
           real answer, so the shortcut would be choosing for the player - and it would choose the
           harder-to-reverse of the two. */
        if (gates.length === 0 && candidates.length === 1 && !declarationOn(candidates[0])) {
            activate(candidates[0]);
            return;
        }

        var e = confirm.fleetDialogShell(
            "Manage Reinforcements",
            "Which doorway do you want to work with? A unit already holding a jump point open is "
            + "marked, as is a jump gate holding one for you; a unit already booked to ride "
            + "somebody else's is greyed out.",
            "", "Choose Hex");

        //Wider than the rest of the .fleetDialog family - see .reinforcementDialog in tactical.css.
        e.addClass("reinforcementDialog");

        /* ⭐⭐ THE THIRD BUTTON (user request 2026-09-02): CHANGE A MANIFEST WITHOUT UNDOING THE
           DOORWAY IT BELONGS TO.

           Until now a manifest was named exactly once, on the way out of the transaction that made
           the doorway - createExitOrder ends with showManifestDialog, and so does
           createGateSignalOrder - and there was no way back to it. Changing one's mind about a
           single passenger therefore meant withdrawing the jump point, re-picking the hex,
           re-turning the facing and naming the whole list again: four gestures to undo one tick.
           The manifest is a separate choice from the declaration - the dialog says so by having no
           Cancel - so it gets its own way back in.

           BETWEEN THE ACTION BUTTON AND CANCEL, which is where it belongs in both senses: it is
           secondary to the row's own action, and it is not a way out of the dialog. It takes the
           neutral .confirmalt paint rather than the accented .confirmok one for the same reason -
           see the button-row block in confirm.css.

           ⚠️ SHOWN ONLY WHEN THE SELECTED ROW ALREADY HOLDS A DOORWAY, and never on a row whose
           primary action is the manifest already. openerRowsHtml decides that per row and writes
           data-manifest; syncLabel below only moves the answer onto the button. */
        $('<div class="confirmalt" data-label="Jump Manifest"></div>').insertAfter($(".confirmok", e));

        /* THE LIST IS RE-RENDERED IN PLACE, NEVER REBUILT. Withdrawing used to close the whole
           window (user request 2026-08-28), which made "move my jump point" three gestures -
           withdraw, reopen the menu, find the unit again - and hid the one thing the withdrawal
           had just changed: the passengers it freed. Now the row loses its OPENING mark, the
           button reverts to Choose Hex, any unit the withdrawal un-booked stops being grey, and
           the same unit is still selected, so re-placing is the very next click. */
        function render(keepSelectedId) {
            $(".fleetDialogBody", e).html(openerRowsHtml(keepSelectedId));
            syncLabel();
        }

        /* ⚠️ TOLERATES NOTHING BEING CHECKED. Every row can be disabled in principle (each one
           riding somebody else's doorway), and .attr() on an empty set is undefined, not a
           throw - but the label still has to say something sensible.

           ⭐ STAGE 8 REPLACED data-declared WITH data-action, AND IT IS NOT A RENAME. There were two
           states and two labels; there are now four - Choose Hex, Withdraw Jump Point, Select
           Reinforcements and Withdraw Gate Signal - and a boolean cannot carry four. The row that
           knows which state it is in writes the label it wants, and this only has to move it onto
           the button. openerRowsHtml is the single place those four strings are decided. */
        function syncLabel() {
            var checked = $("input[name='reinforcementOpener']:checked", e);
            var action  = checked.length > 0 ? checked.attr("data-action") : null;

            $(".confirmok", e).attr("data-label", action || "Choose Hex");

            /* AND THE MAP FOLLOWS THE SELECTION (user request 2026-08-28). A row with a jump point
               of its own gets its marker named on the map; a row with none clears the highlight
               rather than pointing somewhere arbitrary. Every re-render calls this, so a withdrawal
               drops the highlight in the same breath as it drops the OPENING tag.

               ⚠️ A GATE ROW NEVER HIGHLIGHTS, and that is not an omission. highlight() drives
               BallisticIconContainer.generateExitHexes, which draws the blue Forming markers of
               'jumpexit' declarations - a gate's claim is not one of those, and the gate itself is
               a permanent, named, plainly visible unit sitting on its own hex. The highlight exists
               to tell three IDENTICAL blue markers apart; a gate has no twin to be confused with.
               highlight() ignores an id with no exit declaration on it, so passing the gate's
               would be harmless - it is skipped explicitly so the reason is on the record. */
            var isGateRow = checked.length > 0 && checked.attr("data-gate") === '1';
            highlight((!isGateRow && action === "Withdraw Jump Point") ? checked.val() : null);

            /* AND THE THIRD BUTTON FOLLOWS THE SELECTION TOO. Every re-render calls this, so
               withdrawing a jump point takes the Jump Manifest button away in the same breath as it
               takes the OPENING tag - there is no doorway left to name a manifest for. */
            $(".confirmalt", e).toggle(checked.length > 0 && checked.attr("data-manifest") === '1');
        }

        //DELEGATED on the dialog root and bound ONCE: render() replaces every row, so a handler
        //bound to the inputs themselves would die with the markup it was attached to.
        e.on("change", "input[name='reinforcementOpener']", syncLabel);

        $(".confirmok", e).on("click", function () {
            var id = $("input[name='reinforcementOpener']:checked", e).val();
            if (id === undefined) return;   //every row greyed; nothing to act on

            //Re-resolved from gamedata rather than from the captured row, and the declaration
            //re-read off the live object: both can have been replaced by a poll while the dialog
            //was open, and acting on a discarded copy would silently do nothing at all.
            var live = gamedata.getShip(id);
            if (!live) { highlight(null); e.remove(); return; }

            /* ⭐ THE GATE BRANCH (Stage 8), TAKEN FIRST AND RETURNING. A gate fails isMine() by
               construction - it is terrain, it is nobody's, and it is not in hyperspace - so it has
               to be recognised before that test rather than after it.

               Its two actions are the mirror of the ship's, one step along: a doorway that already
               stands is NAMED (the manifest dialog), and a claim made this turn is WITHDRAWN. The
               state is re-read here rather than trusted from the row for the same reason the ship
               branch re-reads its declaration - a poll can land while the dialog is open, and the
               gate's vortex can open, close or be lost to a rival claim between render and click. */
            if (gamedata.isJumpGate(live)) {
                if (gateExitOn(live) && gateTakesAWave(live)) {
                    highlight(null);
                    e.remove();
                    showManifestDialog(live);
                    return;
                }

                if (gateClaimOn(live)) {
                    //removeGateSignalOrder clears the berths that were riding it - see the note
                    //there. Stay open on the same row, exactly as a ship withdrawal does.
                    weaponManager.removeGateSignalOrder(live);
                    render(live.id);
                    return;
                }

                //Neither any more: a poll changed the gate under the dialog. Re-render rather than
                //acting on a state that no longer exists; the row will now say what is true.
                render(live.id);
                return;
            }

            if (!isMine(live)) { highlight(null); e.remove(); return; }

            if (declarationOn(live)) {
                withdraw(live);
                render(live.id);   //stay open, on the same unit - see render's note
                return;
            }

            e.remove();
            activate(live);
        });

        /* ⭐ THE JUMP MANIFEST BUTTON. Straight to the same dialog every other path opens, on the
           doorway the selected row is holding - a ship's own declaration or a gate's claim.

           IT CLOSES THE MENU, exactly as the gate's own 'Select Reinforcements' does a few lines
           up: the manifest is a full dialog of its own, and stacking the two would leave the player
           looking at a list they cannot reach underneath the one on top of it.

           ⚠️ THE DOORWAY IS RE-DERIVED FROM LIVE GAMEDATA, never trusted from data-manifest. The
           attribute decided whether to OFFER the button; a poll can land between the render and the
           click and take the declaration, the claim or the whole unit away (see _openerId at the top
           of this file). If it has gone, re-render rather than opening a manifest for a doorway that
           no longer exists - the row will then say what is true.

           gateDoorway() rather than gateClaimOn() for a gate, deliberately looser than the test that
           offered the button: if the claim has resolved into a standing exit while the dialog stood,
           the manifest is still exactly the right thing to open. */
        $(".confirmalt", e).on("click", function () {
            var id = $("input[name='reinforcementOpener']:checked", e).val();
            if (id === undefined) return;

            var live = gamedata.getShip(id);
            if (!live) { highlight(null); e.remove(); return; }

            var doorway = gamedata.isJumpGate(live) ? !!gateDoorway(live) : !!declarationOn(live);
            if (!doorway) { render(live.id); return; }

            highlight(null);
            e.remove();
            showManifestDialog(live);
        });

        //highlight(null) here and not only in deactivate(): closing the menu with Cancel arms
        //nothing, so deactivate() is never reached on this path.
        $(".confirmcancel", e).on("click", function () { highlight(null); e.remove(); });

        render(null);
        e.appendTo("body").fadeIn(250);
    }

    /* THE WHOLE WAVE, IN TWO GROUPS (user request 2026-08-28). Every unit of mine still in
       hyperspace appears, in whatever state it is in RIGHT NOW - which is why this re-reads
       gamedata on every call rather than closing over a list.

         JUMP-CAPABLE units come first and are the only selectable rows: they are what the dialog
         is FOR, since only a drive can open a doorway.

         PASSENGERS - a reinforcement with no jump engine of its own - follow underneath, greyed
         and unselectable. They used to be invisible here, which made the menu quietly misreport
         the fleet: a player looking at two jump-capable hulls had no way to see the four fighters
         waiting behind them, and the stranding warning at commit time was the first mention they
         ever got. They are listed for AWARENESS, not for choice - a passenger cannot open
         anything, so it can never be the answer to "whose drive do you want to work with?".

       $selectedId is the unit to leave checked if it is still selectable; null means "the first
       one that is".

       ⚠️ A GREYED ROW IS NEVER PRE-CHECKED. Its radio is disabled, so a player who wanted a
       different unit could not move the selection off it - the dialog would be stuck. And
       pickIndex indexes the JUMP-CAPABLE rows alone: the passenger group is appended after the
       selection has already been decided, so it cannot shift it. */
    function openerRowsHtml(selectedId) {
        /* ⭐ ONE LIST, TWO KINDS OF DOORWAY (Stage 8). A row is now described by four things rather
           than three, and every difference between a gate and a hyperspace drive is carried in
           them - the rendering below has no second branch and no second markup:

             action ... the label the primary button takes when this row is selected. Four values,
                        decided HERE and nowhere else (see syncLabel).
             tag ...... the badge, or null.
             detail ... the right-hand line.
             blocked .. the radio is disabled and the row is greyed.

           GATES COME FIRST because a standing doorway is the cheaper thing to use: it costs no
           drive, no charge and no hex, and it is usually what the player opened the menu for. */
        var rows = gateCandidates().map(gateRow).concat(openerCandidates().map(openerRow));

        var pickIndex = -1;
        for (var i = 0; i < rows.length; i++) {
            if (rows[i].blocked) continue;
            if (pickIndex === -1) pickIndex = i;
            if (selectedId !== null && selectedId !== undefined && rows[i].ship.id == selectedId) {
                pickIndex = i;
                break;
            }
        }

        var html = rows.map(function (row, i) {
            var ship = row.ship;

            return '<label class="reinforcementRow'
                + (row.open ? ' reinforcementRowOpen' : '')
                + (row.blocked ? ' reinforcementRowRiding' : '') + '">'
                + '<input type="radio" name="reinforcementOpener" value="' + ship.id + '"'
                + ' data-action="' + row.action + '"'
                + ' data-gate="' + (row.gate ? '1' : '0') + '"'
                + ' data-manifest="' + (row.manifest ? '1' : '0') + '"'
                + (row.blocked ? ' disabled' : '')
                + (i === pickIndex ? ' checked' : '') + '>'
                + '<span class="reinforcementRowMain">'
                + '<span class="reinforcementRowName">' + ship.name + '</span>'
                + (row.tag
                    ? '<span class="reinforcementRowTag' + (row.tagRiding ? ' reinforcementRowTagRiding' : '')
                      + '">' + row.tag + '</span>'
                    : '')
                + '</span>'
                + '<span class="reinforcementRowClass">' + row.detail + '</span>'
                + '</label>';
        }).join('');

        return html + passengerRowsHtml();
    }

    /* A HYPERSPACE DRIVE'S ROW - the original three states, unchanged in every visible respect. */
    function openerRow(ship) {
        var order  = declarationOn(ship);
        var host   = ridingWith(ship);
        var riders = order ? manifestOf(ship.id).length : 0;

        return {
            ship:      ship,
            gate:      false,
            open:      !!order,
            blocked:   !!host,
            //A standing declaration is a manifest that can be re-opened - but only while there is
            //somebody left to put on it. See manifestRiders and the Jump Manifest button.
            manifest:  !!order && manifestRiders(ship).length > 0,
            action:    order ? 'Withdraw Jump Point' : 'Choose Hex',
            tag:       order ? 'OPENING' : (host ? 'RIDING' : null),
            tagRiding: !!host && !order,
            detail:    order
                ? 'hex ' + order.x + ',' + order.y + ' &mdash; ' + riders + ' unit' + (riders === 1 ? '' : 's')
                : (host ? 'riding ' + host.name : ship.shipClass)
        };
    }

    /* ⭐⭐ A JUMP GATE'S ROW (user request 2026-08-28): "listed as a Jump Drive ship, but instead of
       Choose Hex the same button says Select Reinforcements and brings up the Jump Point Manifest."

       THREE STATES, and the third is the one worth having:

         OPEN, with a turn still to give ... SELECT REINFORCEMENTS. The doorway is standing; naming a
             manifest is the only thing left to do with it, and it is the whole of that request.
         SIGNALLED this turn ............... WITHDRAW GATE SIGNAL. The claim is made and the manifest
             is named; the doorway forms at the end of this turn. Withdrawing is the mirror of a
             ship's, and it takes the berths with it (weaponManager.removeGateSignalOrder).
         OPEN, but CLOSING at the end of this turn ... greyed, and it SAYS SO. A wave named now
             arrives in next turn's Deployment phase, by which time this doorway is gone - so the
             manifest would be taken, the berths written, and the units would silently not come. The
             row is listed rather than hidden precisely so the player can see why.

       ⚠️ "SIGNALLED", not "OPENING". The two badges are different words for different facts and the
       menu should not pretend otherwise: a drive is holding a doorway of its own open, while a gate
       has been ASKED to open one and may yet lose the contest to a nearer enemy claim (section 2.4).
       Both share the OPENING row tint, which is right - each is a doorway this player is counting
       on. */
    function gateRow(gate) {
        var claim  = gateClaimOn(gate);
        var riders = manifestOf(gate.id).length;
        var riderText = riders + ' unit' + (riders === 1 ? '' : 's');

        if (claim) {
            return {
                ship: gate, gate: true, open: true, blocked: false,
                //The claim is made and the manifest was named on the way out of the Signal panel;
                //this is how it is re-opened without taking the claim back first.
                manifest: manifestRiders(gate).length > 0,
                action: 'Withdraw Gate Signal',
                tag: 'SIGNALLED', tagRiding: false,
                detail: 'signalled &mdash; ' + riderText
            };
        }

        var takesAWave = gateTakesAWave(gate);

        return {
            ship: gate, gate: true, open: takesAWave, blocked: !takesAWave,
            //⚠️ FALSE EVEN THOUGH THIS ROW HAS A DOORWAY, and deliberately: the PRIMARY button on
            //this row already IS the manifest ('Select Reinforcements'), so a second button beside
            //it saying the same thing in different words would only be noise.
            manifest: false,
            action: 'Select Reinforcements',
            tag: takesAWave ? 'OPEN' : null, tagRiding: false,
            detail: takesAWave ? ('jump point open &mdash; ' + riderText) : 'jump point closes this turn'
        };
    }

    /* THE SECOND GROUP: everything of mine in hyperspace with no drive of its own, under a heading
       that divides the two - the dialog never opens with an empty first group
       (manageReinforcements refuses outright), so the heading can never end up labelling the whole
       list. No passengers, no heading and no change to the list this menu has always shown.

       ⭐ A DIFFERENT RADIO NAME, not merely `disabled`. The OK handler reads
       `input[name='reinforcementOpener']:checked`, and a passenger must never be able to answer
       that question whatever a browser does with a disabled control - so these rows are not in
       that group at all. They keep a radio rather than dropping it because the row is a flex line
       that measures from the control: without one, every name here would hang a control-width to
       the left of the names above them.

       Each says which state it is in, in the same words the group above uses: riding somebody's
       doorway, or nothing yet. */
    function passengerRowsHtml() {
        var passengers = myHyperspaceUnits().filter(function (ship) { return !canOpen(ship); });
        if (passengers.length === 0) return '';

        var rows = passengers.map(function (ship) {
            var host = ridingWith(ship);

            return '<label class="reinforcementRow reinforcementRowPassenger">'
                + '<input type="radio" name="reinforcementPassenger" value="' + ship.id + '" disabled>'
                + '<span class="reinforcementRowMain">'
                + '<span class="reinforcementRowName">' + ship.name + '</span>'
                + (host ? '<span class="reinforcementRowTag reinforcementRowTagRiding">RIDING</span>' : '')
                + '</span>'
                + '<span class="reinforcementRowClass">'
                + (host ? 'riding ' + host.name : ship.shipClass) + '</span>'
                + '</label>';
        }).join('');

        return '<div class="reinforcementRowHeading">No jump drive &mdash; they arrive as passengers</div>'
            + rows;
    }

    /* WHO RIDES THROUGH. Any number, including none but the opener, and including units with no
       jump engine of their own (§2.2) - which is the whole point: one drive brings a wave.

       Offered only for units that are NOT already assigned to a DIFFERENT exit this turn. A
       unit can only ride one doorway, and silently moving it would undo a choice the player has
       already made on another opener. */
    function showManifestDialog(opener) {
        /* ⭐ STAGE 8 - THE SAME DIALOG SERVES A GATE, and the only differences are words. A gate is
           not itself arriving (it is a fixture on the board, not a unit in hyperspace), so nothing
           is pre-booked and every hyperspace unit is on offer; a drive always rides its own doorway
           and is therefore never in its own list. The filter below already expresses both - a gate
           is never in myHyperspaceUnits(), so the self-exclusion simply never matches one - which is
           why this is a wording branch and not a structural one. */
        var isGateDoor = gamedata.isJumpGate(opener);

        var riders = manifestRiders(opener);

        /* ⭐ THREE WAYS THE LIST COMES BACK EMPTY, and they are three different facts (the third is
           new with the 2026-09-02 ruling below, which lets a gate be signalled for arrival by a
           player with nothing of their own waiting - so "they are all riding something else" is no
           longer the only reason a gate can have nobody to offer). A drive is never in this case in
           the same sense: it always rides its own doorway, so the message is about IT. */
        if (riders.length === 0) {
            confirm.warning(!isGateDoor
                ? "<b>" + opener.name + "</b> will open a jump point and arrive through it next turn."
                : (myHyperspaceUnits().length === 0
                    ? "<b>" + opener.name + "</b> will open a jump point, but you have nothing in "
                      + "hyperspace to bring through it."
                    : "<b>" + opener.name + "</b> is holding a jump point open, but everything you "
                      + "have in hyperspace is already riding another one."));
            gamedata.drawIniGUI();
            return;
        }

        var rows = riders.map(function (ship) {
            return '<label class="reinforcementRow">'
                + '<input type="checkbox" class="reinforcementRider" value="' + ship.id + '"'
                + (ship.arrivalVia == opener.id ? ' checked' : '') + '>'
                + '<span class="reinforcementRowMain">'
                + '<span class="reinforcementRowName">' + ship.name + '</span>'
                + '</span>'
                + '<span class="reinforcementRowClass">' + ship.shipClass + '</span>'
                + '</label>';
        }).join('');

        var e = confirm.fleetDialogShell(
            "Jump Point Manifest",
            isGateDoor
                ? opener.name + " holds a jump point open. Which of your reinforcements ride "
                  + "through it? They arrive next turn, and the gate can bring another wave on "
                  + "every turn it holds the doorway."
                : opener.name + " arrives through this jump point. Which others ride with it?",
            rows, "Confirm");

        //Same width as the menu it was opened from - see .reinforcementDialog in tactical.css.
        e.addClass("reinforcementDialog");

        //No cancel: the ORDER is already made by this point and the manifest is a separate choice.
        //Closing with nothing ticked is a legal answer (the opener comes through alone), so an
        //explicit Cancel would only be ambiguous about whether it undid the declaration too.
        $(".confirmcancel", e).remove();

        //THE TICK LIST, WRITTEN. Both buttons run it - see the Back button for why neither of them
        //discards. Split out only so the two cannot drift apart.
        function applyManifest() {
            var chosen = {};
            $(".reinforcementRider:checked", e).each(function () { chosen[$(this).val()] = true; });
            e.remove();

            riders.forEach(function (ship) {
                if (chosen[ship.id]) ship.arrivalVia = opener.id;
                else if (ship.arrivalVia == opener.id) ship.arrivalVia = null;
            });

            gamedata.drawIniGUI();
        }

        $(".confirmok", e).on("click", applyManifest);

        /* ⭐ BACK TO MANAGE REINFORCEMENTS (user request 2026-09-02), beside Confirm, and it closes
           the loop the Jump Manifest button opened: a fleet with two drives and a gate is three
           doorways to name, and every one of them used to end by dropping the player back onto the
           map to find the menu again.

           ⚠️ IT COMMITS THE TICKS, IT DOES NOT DISCARD THEM, and that is not a compromise - this
           dialog has no discarding half at all (the note above says why its Cancel was removed).
           "Back" here means "and now show me the menu", exactly as Confirm means "and now let me get
           on"; a Back that silently threw the ticks away would be the ambiguity that Cancel was
           deleted to avoid, wearing a different word.

           OFFERED ON EVERY PATH, including the two that were not reached from the menu - a fresh
           declaration (createExitOrder) and the gate Signal panel (createGateSignalOrder). "Name the
           wave, then set up the next doorway" is the same workflow whichever door was just opened,
           and manageReinforcements() always has at least the row this manifest belongs to, so it
           can never land on the empty-handed error. */
        $('<div class="confirmalt" data-label="Back to Manage Reinforcements"></div>')
            .insertAfter($(".confirmok", e))
            .on("click", function () {
                applyManifest();
                manageReinforcements();
            });

        e.appendTo("body").fadeIn(250);
    }

    /* ⭐⭐ THE MANIFEST, STRAIGHT OFF THE SIGNAL BUTTON (user request 2026-08-28): "when a player
       signals the jump gate to open an exit from Hyperspace and clicks Signal Gate, this should
       open the same Jump Point Manifest window."

       Signalling a gate for arrival is only half a gesture. The claim opens the doorway and the
       manifest says who walks through it, and everywhere else in this feature the two are named in
       one breath - createExitOrder ends with exactly this call. Without it a player would signal
       the gate, close the panel, then have to find Manage Reinforcements and pick the gate out of
       the list to finish what they started; and a gate signalled with nobody on its manifest brings
       nothing through, silently.

       ⚠️ RE-RESOLVED THROUGH gamedata.getShip, not used as passed. weaponManager calls this at the
       end of createGateSignalOrder, which is itself the tail of a transaction the player has been
       sitting in front of (open the panel, set the duration, press Signal) - a poll can and does
       land inside that window and replaces every entry of gamedata.ships (trap 17). The dialog then
       writes arrivalVia onto whatever objects IT reads, which are live ones.

       Called only from weaponManager, but public because the two files have no other relationship. */
    function showGateManifest(gate) {
        var live = gate ? gamedata.getShip(gate.id) : null;
        if (!live || !gamedata.isJumpGate(live)) return;

        showManifestDialog(live);
    }

    /* Drop every berth booked on this gate. The gate twin of what withdraw() does for a ship
       exit, called by weaponManager.removeGateSignalOrder when a claim is taken back - a
       booking for a doorway that will now never form would be posted for nothing, and the server
       would silently refuse it (persistManifest would not find the gate among the openers).

       ⚠️ THE CALLER DECIDES WHETHER TO CALL THIS, not this. Cancelling this turn's claim on a gate
       that is ALREADY holding a doorway from an earlier turn takes nothing away, and clearing the
       berths there would cancel a wave the player never asked to cancel. That test lives at the call
       site, where the gate object is in hand. */
    function clearGateManifest(gate) {
        if (!gate) return;

        clearManifest(gate.id);
        gamedata.drawIniGUI();
    }

    /* ---------------------------------------------------------------- the #iniGui button */

    /* Should the panel offer the button at all? Initial Orders only - that is the phase a
       declaration is made in and the only one it means anything in - and only while this player
       actually has something waiting in hyperspace. */
    function isOffered() {
        if (gamedata.gamephase !== 1) return false;
        if (gamedata.waiting) return false;
        return myHyperspaceUnits().length > 0;
    }

    /* The button's own state machine, in one place so the label and the click agree:
         armed  - the map is waiting for a hex; clicking cancels
         idle   - open the menu, which is where declare and withdraw both live now

       ⚠️ THE "HAS AN ORDER" STATE IS GONE ON PURPOSE (user request 2026-08-28). The button used
       to become "Withdraw Jump Point" as soon as any declaration stood, which locked the player
       out of declaring a second exit with a second jump-capable hull - the one action a
       multi-drive reinforcement group most wants. Withdrawing is now a row in the menu. */
    function buttonLabel() {
        if (isActive()) return 'Cancel Jump Point';
        return 'Manage Reinforcements';
    }

    function onButtonClicked() {
        if (isActive()) { deactivate(); return; }
        manageReinforcements();
    }

    return {
        isOffered: isOffered,
        isActive: isActive,
        buttonLabel: buttonLabel,
        onButtonClicked: onButtonClicked,
        onMapClick: onMapClick,
        deactivate: deactivate,
        declarations: declarations,
        manifestOf: manifestOf,
        canOpen: canOpen,
        getHighlightedOpener: getHighlightedOpener,
        jumpEngineOf: jumpEngineOf,
        myHyperspaceUnits: myHyperspaceUnits,
        strandedByCommit: strandedByCommit,
        //STAGE 8 - the two the gate half needs. showGateManifest is weaponManager's, straight off
        //the Signal button; clearGateManifest is its withdrawal twin. (myHyperspaceUnits above used
        //to answer gamedata.canSignalJumpGateForArrival; that rule was dropped on 2026-09-02, so it
        //is exported now only as the module's public "what is still waiting" reader.)
        showGateManifest: showGateManifest,
        clearGateManifest: clearGateManifest
    };
})();
