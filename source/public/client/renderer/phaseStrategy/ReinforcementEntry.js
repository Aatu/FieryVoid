'use strict';

/* REINFORCEMENTS_PLAN.md STAGE 4 - CALLING REINFORCEMENTS IN.
 *
 * The client half of a jump point ENTRANCE. A player with units waiting in hyperspace opens a
 * doorway for them: pick which of those units opens it, pick a hex, pick the facing, then say which
 * of the rest ride through it.
 *
 *     "Manage Reinforcements" (#iniGui)
 *         -> the opener menu ....... EVERY jump-capable unit still in hyperspace, each marked
 *             |                      with the entrance it is already holding, if any
 *             |-> "Withdraw Jump Point" ... a unit that already holds one; the menu STAYS OPEN
 *             |                              and re-renders, so re-placing it is the next click
 *             \-> "Choose Hex" ............ the map is armed; the next click is the entrance hex
 *                     -> UI.vortexFacing (BLUE) ... turn the doorway, confirm
 *                         -> manifest dialog ..... who rides through
 *                             -> the FireOrder exists, and ship.arrivalVia is stamped locally
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
       server: null means "no entrance has been assigned to it yet". Once the Stage 6 sweep stamps
       an arrival turn the unit stops being a reinforcement in this sense - it becomes an ordinary
       unit with a late deploy turn, and it must not be offered for a second ride. */
    function isMine(ship) {
        return !!ship && ship.reinforcement === true
            && (ship.arrivalTurn === null || ship.arrivalTurn === undefined)
            && gamedata.isMyShip(ship)
            && !shipManager.isDestroyed(ship);
    }

    function myHyperspaceUnits() {
        return gamedata.ships.filter(isMine);
    }

    /* Does this unit mount a Jump Engine that could OPEN a jump point?

       ⚠️ THE CLIENT MIRROR OF JumpEngine::getVortexDeclaration's legacy test, and it has to be the
       same three properties the lobby's gamedata.hasVortexJumpEngine reads, for the same reason:
       $legacyJump is PROTECTED server-side and never reaches a blueprint, so what markLegacy()
       actually flips is ballistic/hextarget (cleared, and ShipCompactor strips a false key outright
       so both read undefined) and range (zeroed). A live engine carries all three.

       ⚠️ isJumpGate is excluded here even though a gate's engine passes those three: signalling a
       gate for an ENTRANCE is Stage 8, and it is a different gesture with a different panel. */
    function canOpen(ship) {
        if (!ship || !ship.systems) return false;
        if (gamedata.isJumpGate(ship)) return false;

        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (!s || s.name !== 'jumpEngine') continue;
            if (shipManager.systems.isDestroyed(ship, s)) continue;
            if (!s.ballistic || !s.hextarget) continue;   //markLegacy() cleared both
            if (!(s.range > 0)) continue;                 //markLegacy() zeroed it
            return true;
        }

        return false;
    }

    function jumpEngineOf(ship) {
        if (!ship || !ship.systems) return null;

        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (s && s.name === 'jumpEngine' && s.ballistic && s.hextarget && s.range > 0) return s;
        }

        return null;
    }

    /* EVERY unit of mine in hyperspace that could open an entrance - INCLUDING the ones already
       holding a declaration this turn.

       ⭐ INCLUDING THEM IS THE WHOLE OF THE SINGLE MENU (user request 2026-08-28). This used to
       be `availableOpeners()`, which filtered declared units out, because the #iniGui button
       flipped wholesale into withdraw mode as soon as any declaration stood - so a second opener
       could never be reached, and a fleet with three jump-capable hulls could open exactly one
       doorway per turn. The list is the menu now, and the row says which state each unit is in.

       One entrance per unit is still the rule (the server's one-vortex-per-ship test); it is
       expressed by what the row OFFERS - withdraw rather than declare - instead of by hiding the
       unit, so nothing here can produce an order the server would reject. */
    function openerCandidates() {
        return myHyperspaceUnits().filter(canOpen);
    }

    /* ---------------------------------------------------------------- the declaration */

    /* THIS TURN'S entrance declaration on $ship, or null.

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
            if (fire.damageclass !== 'jumpentry') continue;
            return fire;
        }

        return null;
    }

    /* Every entrance this player has declared this turn, as {ship, order} pairs. */
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
       same breath: an entrance that no longer exists cannot be ridden through, and leaving
       arrivalVia pointing at a withdrawn opener would post a manifest for nothing. */
    function withdraw(ship) {
        var engine = jumpEngineOf(ship);
        if (!engine || !Array.isArray(engine.fireOrders)) return;

        for (var i = engine.fireOrders.length - 1; i >= 0; i--) {
            var fire = engine.fireOrders[i];
            if (!fire || fire.turn != gamedata.turn) continue;
            if (fire.damageclass !== 'jumpentry') continue;
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

    /* ---------------------------------------------------------------- the stranding check */

    /* Is this unit actually leaving hyperspace this turn? Being ON a manifest is not enough: the
       entrance it names has to still exist. withdraw() clears the manifest it opened, but a
       declaration can also be replaced (createEntranceOrder withdraws first), so the order is
       the authority and arrivalVia is only the pointer to it. */
    function ridingOut(ship) {
        if (ship.arrivalVia === null || ship.arrivalVia === undefined) return false;

        var opener = gamedata.getShip(ship.arrivalVia);
        return !!opener && !!declarationOn(opener);
    }

    /* SOMEBODY ELSE'S jump point that this unit is already booked to ride, or null.

       ⭐ THE TEST THE MENU GREYS A ROW ON (user request 2026-08-28). A unit that is riding
       through another ship's doorway is spoken for: opening a SECOND jump point with it would
       have it hold a drive open on one side of the map while arriving through somebody else's
       on the other. The declaration would be built, the manifest would still point at the first
       opener, and the two would disagree - so the menu refuses the choice rather than letting it
       be made and then unpicked.

       ⚠️ arrivalVia == its OWN id is not riding with anybody - that is what createEntranceOrder
       stamps on an opener, because a drive always comes through its own doorway. Without this
       line every opener would grey itself out the moment it declared, and Withdraw would be
       unreachable.

       Riding is undone by withdrawing the entrance that carries it (withdraw() clears its whole
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
       arrive as a passenger on somebody else's entrance. If the one unit that COULD open a
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

       ⚠️ IT DOES NOT KNOW ABOUT JUMP GATES, and it does not need to yet: canOpen() excludes them
       because signalling a gate for an ENTRANCE is Stage 8 and is not built, so today there is
       genuinely no other way in. If that lands, a gate this player could signal has to count as a
       remaining opener here or this will warn about fleets that are perfectly fine. */
    function strandedByCommit() {
        var waiting = myHyperspaceUnits();

        var leaving = waiting.filter(ridingOut);
        if (leaving.length === 0) return [];

        var left = waiting.filter(function (ship) { return !ridingOut(ship); });
        if (left.some(canOpen)) return [];

        return left;
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
        if (_openerId === null) return;
        _openerId = null;
        hideBanner();
        gamedata.drawIniGUI();
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

    /* Is this hex a legal place to open an entrance? Returns null when it is, or the reason when it
       is not (plan §2.2).

       ⭐ NO RANGE AND NO LINE OF SIGHT, and their absence is the rule rather than an omission:
       there is no ship on the board to measure either from.

       The obstruction test is weaponManager.getVortexHexBlock, the very same sweep an EXIT
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
       an entrance (§2.2 forbids terrain, gates, vortices and Enormous units, and nothing else), and
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

    /* Hand the hex to the shared facing control, in its ENTRANCE livery. The control is the same
       transaction UI.vortexFacing runs for an exit - nothing is committed until the tick - and the
       `entrance` flag is the whole of the difference: cyan instead of yellow, and the arrow drawn
       with the outward asset, because an entrance's facing is the doorway OUT rather than the mouth
       units cross inbound (§0). */
    function openFacingControl(opener, hex) {
        var engine = jumpEngineOf(opener);
        if (!engine) return;

        webglScene.customEvent('VortexFacingRequested', {
            ship: opener,
            weapon: engine,
            hexpos: hex,
            type: 'ballistic',
            entrance: true,
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

                createEntranceOrder(live, liveEngine, hex, facing);
            }
        });
    }

    /* BUILD THE DECLARATION. Stored exactly as an exit's is - a ballistic FireOrder on the unit's
       Jump Engine, x/y the hex, firingMode = facing + 1 - with damageclass 'jumpentry' as the
       discriminator, mirroring 'jumppoint' (§3.4). That one string is what routes it to
       Firing::getEntranceDeclarationBlock instead of the ship rules, and what keeps it out of
       JumpEngine::getVortexDeclaration's exit sweep.

       ⚠️ targetid IS -1 AND MUST STAY -1 (trap 10). Every ballistic-icon path reads targetid as
       "hang the marker on that unit", and the unit here is in hyperspace: the marker would be drawn
       at its slot's deployment-box centre, with a bright line running to the entrance hex from a
       ship that is not on the board.

       Re-checked at the tick rather than only at the click, exactly as createJumpPointOrder is: a
       server poll can land between the two and put something in the hex. */
    function createEntranceOrder(opener, engine, hex, facing) {
        var block = weaponManager.getVortexHexBlock(opener, engine, hex);
        if (block) {
            confirm.error(block);
            return;
        }

        withdraw(opener);   //one entrance per unit per turn; re-declaring replaces

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
            damageclass: 'jumpentry'
        });

        //The opener always rides its own entrance - it is the unit whose drive is holding the
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
       SECOND entrance: the #iniGui button flipped to withdraw mode the moment any declaration
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
       to still be there (see _openerId's note at the top of this file). */
    function manageReinforcements() {
        var candidates = openerCandidates();

        if (candidates.length === 0) {
            confirm.error("None of your reinforcements can open a jump point.");
            return;
        }

        /* One candidate with nothing declared is a dialog with a single row and one possible
           answer - straight to the map, exactly as the opener picker always did.
           ⚠️ NOT skipped when that one unit already holds a declaration. The only thing the menu
           could do for it is withdraw, and withdrawing a jump point on a bare button click with
           no confirmation is not something a player should be able to trip over. */
        if (candidates.length === 1 && !declarationOn(candidates[0])) {
            activate(candidates[0]);
            return;
        }

        var e = confirm.fleetDialogShell(
            "Manage Reinforcements",
            "Whose drive do you want to work with? A unit already holding a jump point open is "
            + "marked; one already booked to ride somebody else's is greyed out.",
            "", "Choose Hex");

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
           throw - but the label still has to say something sensible. */
        function syncLabel() {
            var checked = $("input[name='reinforcementOpener']:checked", e);
            var declared = checked.length > 0 && checked.attr("data-declared") === '1';
            $(".confirmok", e).attr("data-label", declared ? "Withdraw Jump Point" : "Choose Hex");
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
            if (!live || !isMine(live)) { e.remove(); return; }

            if (declarationOn(live)) {
                withdraw(live);
                render(live.id);   //stay open, on the same unit - see render's note
                return;
            }

            e.remove();
            activate(live);
        });

        $(".confirmcancel", e).on("click", function () { e.remove(); });

        render(null);
        e.appendTo("body").fadeIn(250);
    }

    /* One row per jump-capable unit in hyperspace, in whatever state it is in RIGHT NOW - which
       is why this re-reads gamedata on every call rather than closing over a list.

       $selectedId is the unit to leave checked if it is still selectable; null means "the first
       one that is".

       ⚠️ A GREYED ROW IS NEVER PRE-CHECKED. Its radio is disabled, so a player who wanted a
       different unit could not move the selection off it - the dialog would be stuck. */
    function openerRowsHtml(selectedId) {
        var rows = openerCandidates().map(function (ship) {
            return { ship: ship, order: declarationOn(ship), host: ridingWith(ship) };
        });

        var pickIndex = -1;
        for (var i = 0; i < rows.length; i++) {
            if (rows[i].host) continue;
            if (pickIndex === -1) pickIndex = i;
            if (selectedId !== null && selectedId !== undefined && rows[i].ship.id == selectedId) {
                pickIndex = i;
                break;
            }
        }

        return rows.map(function (row, i) {
            var ship = row.ship;
            var riders = row.order ? manifestOf(ship.id).length : 0;

            var detail = row.order
                ? 'hex ' + row.order.x + ',' + row.order.y + ' &mdash; ' + riders + ' unit'
                  + (riders === 1 ? '' : 's')
                : (row.host ? 'riding ' + row.host.name : ship.shipClass);

            return '<label class="reinforcementRow'
                + (row.order ? ' reinforcementRowOpen' : '')
                + (row.host ? ' reinforcementRowRiding' : '') + '">'
                + '<input type="radio" name="reinforcementOpener" value="' + ship.id + '"'
                + ' data-declared="' + (row.order ? '1' : '0') + '"'
                + (row.host ? ' disabled' : '')
                + (i === pickIndex ? ' checked' : '') + '>'
                + '<span class="reinforcementRowName">' + ship.name + '</span>'
                + (row.order ? '<span class="reinforcementRowTag">OPENING</span>' : '')
                + (row.host ? '<span class="reinforcementRowTag reinforcementRowTagRiding">RIDING</span>' : '')
                + '<span class="reinforcementRowClass">' + detail + '</span>'
                + '</label>';
        }).join('');
    }

    /* WHO RIDES THROUGH. Any number, including none but the opener, and including units with no
       jump engine of their own (§2.2) - which is the whole point: one drive brings a wave.

       Offered only for units that are NOT already assigned to a DIFFERENT entrance this turn. A
       unit can only ride one doorway, and silently moving it would undo a choice the player has
       already made on another opener. */
    function showManifestDialog(opener) {
        var riders = myHyperspaceUnits().filter(function (ship) {
            if (ship.id == opener.id) return false;
            return ship.arrivalVia === null || ship.arrivalVia === undefined || ship.arrivalVia == opener.id;
        });

        if (riders.length === 0) {
            confirm.warning("<b>" + opener.name + "</b> will open a jump point and arrive through it next turn.");
            gamedata.drawIniGUI();
            return;
        }

        var rows = riders.map(function (ship) {
            return '<label class="reinforcementRow">'
                + '<input type="checkbox" class="reinforcementRider" value="' + ship.id + '"'
                + (ship.arrivalVia == opener.id ? ' checked' : '') + '>'
                + '<span class="reinforcementRowName">' + ship.name + '</span>'
                + '<span class="reinforcementRowClass">' + ship.shipClass + '</span>'
                + '</label>';
        }).join('');

        var e = confirm.fleetDialogShell(
            "Jump Point Manifest",
            opener.name + " arrives through this jump point. Which others ride with it?",
            rows, "Confirm");

        //No cancel: the ORDER is already made by this point and the manifest is a separate choice.
        //Closing with nothing ticked is a legal answer (the opener comes through alone), so an
        //explicit Cancel would only be ambiguous about whether it undid the declaration too.
        $(".confirmcancel", e).remove();

        $(".confirmok", e).on("click", function () {
            var chosen = {};
            $(".reinforcementRider:checked", e).each(function () { chosen[$(this).val()] = true; });
            e.remove();

            riders.forEach(function (ship) {
                if (chosen[ship.id]) ship.arrivalVia = opener.id;
                else if (ship.arrivalVia == opener.id) ship.arrivalVia = null;
            });

            gamedata.drawIniGUI();
        });

        e.appendTo("body").fadeIn(250);
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
       out of declaring a second entrance with a second jump-capable hull - the one action a
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
        jumpEngineOf: jumpEngineOf,
        myHyperspaceUnits: myHyperspaceUnits,
        strandedByCommit: strandedByCommit
    };
})();
