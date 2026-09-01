"use strict";

jQuery(function () { });

//Stage 9: look up the per-flight pointCost of a stored craft's phpclass from
//window.staticShips so the carrier's fleet-list line can include the value
//of anonymous stash records (orphaned partial-launch records and the like).
//Returns 0 when the class isn't preloaded (e.g. a ship file forgot to add it
//to $spawnableClasses) so we degrade silently instead of crashing the row.
function pointCostForPhpclass(phpclass) {
    if (!phpclass || !window.staticShips) return 0;
    for (var faction in window.staticShips) {
        var bp = window.staticShips[faction] && window.staticShips[faction][phpclass];
        if (bp) return parseInt(bp.pointCost || 0, 10);
    }
    return 0;
}

//Stage 9: sum the pointCost of every anonymous hangarUsage entry on $ship.
//dockedFlightId entries are skipped — those craft are represented by their
//own (removed=true) flight ship row in the fleet list, so counting them
//here would double-credit. Shuttles auto-fill carriers and have pointCost=0,
//so they contribute nothing.
//
//Stage 21.5 (no-split): under the no-split model EVERY value-bearing docked
//flight is its own ship row (full dock links dockedFlightId to the source
//flight; partial dock/launch link to a "- Split" K-flight). The only anonymous
//(no-dockedFlightId) entries left are auto-fill shuttles (pointCost 0), so this
//helper now contributes 0 in practice. It is KEPT as a deliberate legacy/orphan
//safety net: a legacy DB (pre-no-split fragment docks) or a future orphan entry
//carrying real value is still credited to the carrier rather than silently
//dropped. Don't remove without auditing legacy-shape games (Stage 21.6).
//
//Stage 18: a destroyed-non-jumped carrier loses its stash to the wreck —
//don't credit the carrier for contents it no longer has. (Server-side,
//processCarrierDestructionEscapes clears hangarUsage post-roll AND now persists
//that clear — Stage 21.4 fix — so this is 0 by next load, but the guard still
//covers the in-request window between destruction and the next setCriticals
//sweep.) Jumped carriers keep their stash since the jumped-flight preservation
//path treats the whole carrier+contents as off-board-but-intact.
function dockedCraftStashValue(ship) {
    if (!Array.isArray(ship.systems)) return 0;
    if (shipManager.isDestroyed(ship) && !shipManager.hasJumpedNotDestroyed(ship)) return 0;
    var total = 0;
    for (var s = 0; s < ship.systems.length; s++) {
        var sys = ship.systems[s];
        if (!sys || !Array.isArray(sys.hangarUsage)) continue;
        //Stage S: held integrated fighters (ShadowHangar bays) are NOT credited here —
        //the carrier's enhValue already pays for the whole integrated complement, and
        //the launched ones are netted off separately (see integratedFighterCarrierAdjust).
        //Counting the held ones here too would double-credit the carrier.
        if (sys.isShadowHangar) continue;
        for (var u = 0; u < sys.hangarUsage.length; u++) {
            var entry = sys.hangarUsage[u];
            if (!entry || entry.dockedFlightId) continue;
            var per = pointCostForPhpclass(entry.phpclass);
            if (per <= 0) continue;
            var size = parseInt(entry.flightSize || 1, 10);
            total += per * size / 6;
        }
    }
    return Math.round(total);
}

//Stage S (fleet-value attribution): integrated Shadow fighters are PAID FOR by the
//carrier (the SHAD_FTRL enhValue covers the whole complement), but once LAUNCHED each
//is valued on its own flight row in the fleet list. To keep the fleet total honest and
//make the carrier's value move with its fighters (drop on launch, rise on dock), we net
//the LAUNCHED-OUT integrated fighters off the carrier's value here.
//
//  launchedOut = purchased (integratedFighterCount) - heldNow (anonymous ShadowHangar
//                hangarUsage entries still in the bay)
//  adjust      = launchedOut * perCraft   (subtracted from the carrier's value)
//
//No carrier<->flight linkage exists client-side, so we derive launchedOut from the gap
//between what was bought and what's still docked — exact because every integrated fighter
//is either held in a ShadowHangar bay or out as a flight row. Returns 0 for non-integrated
//ships. The carrier's own value (base + enhValue) keeps the HELD fighters' share; the
//launched flight rows (pointCost * craft/6 via activeFlightValue) carry the rest, so the
//fleet total is conserved and reabsorbed fighters silently fold back into the carrier.
function integratedFighterCarrierAdjust(ship) {
    var purchased = parseInt(ship.integratedFighterCount || 0, 10);
    if (purchased <= 0 || !Array.isArray(ship.systems)) return 0;
    var perCraft = parseInt(ship.integratedFighterPerCraft || 0, 10);
    if (perCraft <= 0) return 0;

    //Count the integrated fighters STILL HELD in the carrier's ShadowHangar bays.
    //We count regardless of destroyed/jumped state: integrated fighters that were
    //still aboard when the carrier died (or jumped) went down/away WITH THE SHIP —
    //they were paid for via the carrier's enhValue and their CP belongs on the
    //carrier's row, NOT netted off as "launched". The server preserves these held
    //entries through destruction (processCarrierDestructionEscapes exempts
    //ShadowHangars from the wreck wipe), so heldNow stays accurate and a destroyed
    //base with 3 fighters aboard reads 10,450 not 10,000.
    var heldNow = 0;
    for (var s = 0; s < ship.systems.length; s++) {
        var sys = ship.systems[s];
        if (!sys || !sys.isShadowHangar || !Array.isArray(sys.hangarUsage)) continue;
        for (var u = 0; u < sys.hangarUsage.length; u++) {
            var entry = sys.hangarUsage[u];
            if (!entry || entry.phpclass !== 'ShadowMediumFighterFlight') continue;
            if (entry.cannotLaunch) continue;   //wreck — no value
            heldNow += parseInt(entry.flightSize || 1, 10);
        }
    }

    var launchedOut = Math.max(0, purchased - heldNow);
    return launchedOut * perCraft;
}

//Stage S: true when an integrated Shadow flight (ShadowMediumFighterFlight) has fully
//reabsorbed into its carrier — i.e. it has NO craft still in space. A craft is "in space"
//if it's neither destroyed nor docked/disengaged/split-launched. (A CUT OFF craft IS still
//in space and flying, so a flight holding any cut-off fighter is NOT fully reabsorbed and
//keeps its row.) When every craft has docked, the flight's value has folded back into the
//carrier (integratedFighterCarrierAdjust credits the bay), so the row would only duplicate
//— and, since docked craft read as destroyed, it would mislabel as "Destroyed".
function isFullyReabsorbedIntegratedFlight(flight) {
    if (!flight || flight.flight !== true || !Array.isArray(flight.systems)) return false;
    //Integrated fighters only (ordinary flights keep their normal docked/destroyed rows).
    if (flight.phpclass !== 'ShadowMediumFighterFlight') return false;
    var anyInSpace = false;
    for (var i = 0; i < flight.systems.length; i++) {
        var ftr = flight.systems[i];
        if (!ftr || !ftr.fighter) continue;
        if (ftr.destroyed) continue;
        if (shipManager.criticals.hasCritical(ftr, "DockedFighter")) continue;
        if (shipManager.criticals.hasCritical(ftr, "DisengagedFighter")) continue;
        if (shipManager.criticals.hasCritical(ftr, "SplitLaunchedFighter")) continue;
        //Still flying (includes CUT OFF craft) — the flight stays on the board.
        anyInSpace = true;
        break;
    }
    return !anyInSpace;
}

//Hangar Ops Stage 21.7 (value follow-up): re-base the value of a flight that has
//craft which LEFT to their own fleet-list row — a partial DOCK (DockedFighter) or
//a partial LAUNCH from a docked remnant (SplitLaunchedFighter) — onto just the
//craft this row still holds. The flight keeps its full flightSize (replay/reload-
//safe — the departed craft's state lives on the "- Split" row and returns on
//relaunch), so the raw base (pointCost*flightSize/6 + enh) counts the departed
//craft, while the server combatValue (round(100*present/total)) discounts them in
//the denominator. The two compound and don't cancel (the CV multiply also scales
//the enh term, which shouldn't be discounted):
//  - in-space partial-DOCK remnant rendered 360*0.5 = 180 vs the 228 a clean
//    flight of 3 shows (game 4148);
//  - docked partial-LAUNCH remnant rendered 612*0.5 = 306 vs the 402 the launched
//    "- Split" flight correctly shows (game 4151).
//Recomputing base + CV over the retained roster makes each remnant read the same
//as an equivalent fresh flight of that size, and the two rows sum to the original.
//
//IMPORTANT — only DockedFighter / SplitLaunchedFighter re-base the row (those craft
//moved to ANOTHER row, so counting them here double-counts). DESTROYED and
//DisengagedFighter (the B5W combat-DROPOUT mechanic — a fighter that took too much
//damage and left the game) are "lost points": they STAY in this flight's paid
//roster (full base) and contribute 0 to combat value, exactly as a flight valued
//before Hangar Operations. So a flight of 6 that lost 2 in combat still shows
//300/450 (full base, reduced current), not 300/300.
//
//Returns null only for a non-flight / a flight with no Fighter subsystems, so those
//fall through to the default pointCost*flightSize/6 + server combatValue path. For
//any real flight it returns the precise {activeCraft, combatValue} the caller uses
//for BOTH base and current value:
//
//  - activeCraft = retained roster (full fighters minus departed) → the base count.
//  - combatValue = 100 * cvAccum / roster, computed as a FRACTION and NOT pre-rounded
//    to a whole percent. The caller rounds ONCE at the very end (base * cv/100). This
//    also removes a long-standing display artifact: the server's calculateCombatValue
//    rounds the percentage to an integer (e.g. 5/6 active -> round(83.333) = 83), and
//    multiplying that back gave round(348 * 83/100) = 289 instead of the true
//    348 * 5/6 = 290 (game 4150). Computing the fraction here and rounding once fixes
//    the off-by-one. The fraction matches FighterFlight::calculateCombatValue exactly
//    (active craft worth 1, >50%-damaged worth 0.75, destroyed/dropped-out worth 0) —
//    just without the intermediate integer rounding. (FighterFlight CV has none of the
//    structure/engine/sensor modifiers the ship CV does, so this is faithful.)
function activeFlightValue(ship) {
    if (!ship || ship.flight !== true || !Array.isArray(ship.systems)) return null;

    var totalCraft = 0;  //all fighters in this ship (full roster)
    var departed = 0;    //craft that LEFT to their own row (Docked / SplitLaunched)
    var roster = 0;      //craft still belonging to THIS row's value (totalCraft - departed)
    var cvAccum = 0;     //combat-value weight summed over the retained roster
    for (var i = 0; i < ship.systems.length; i++) {
        var fighter = ship.systems[i];
        if (!fighter || !fighter.fighter) continue;   //skip non-Fighter entries
        totalCraft++;

        //Departed to its own row — excluded from this row's base AND its CV.
        if (shipManager.criticals.hasCritical(fighter, "DockedFighter") ||
            shipManager.criticals.hasCritical(fighter, "SplitLaunchedFighter")) {
            departed++;
            continue;
        }

        //Stays in this flight's paid roster. Destroyed / combat-dropped-out craft
        //count toward base (full as-paid value) but add 0 combat value — lost points.
        roster++;
        if (fighter.destroyed ||
            shipManager.criticals.hasCritical(fighter, "DisengagedFighter")) {
            continue;   //0 CV weight
        }
        //Mirror FighterFlight::calculateCombatValue: >50% damage -> 3/4 value.
        var dmg = damageManager.getDamage(ship, fighter);
        if ((fighter.maxhealth - dmg) * 2 < fighter.maxhealth) {
            cvAccum += 0.75;
        } else {
            cvAccum += 1;
        }
    }

    //No Fighter subsystems (shouldn't happen for a real flight) — let the default
    //path handle it rather than divide by zero.
    if (roster <= 0) return null;

    //Unrounded fraction; caller rounds once after multiplying by base.
    var effectiveCV = 100 * (cvAccum / roster);
    return { activeCraft: roster, combatValue: effectiveCV };
}

window.fleetListManager = {

    initialized: false,
    refreshed: true,

    /* ── View state (LOG_PANEL_REDESIGN_PLAN.md Stage 3) ─────────────────────────
       Presentation only. Nothing here changes what is READ out of gamedata - the rows
       are all built, and these decide which of them are on screen and in what order. */
    teamFilter: 0,          //0 = all teams
    onMapOnly: false,
    sortKey: null,          //null = build order (initiative order within the slot)
    sortDir: 1,

    PREF_KEY: 'fv.fleetList.view',

    prepare: function prepare() { },

    displayFleetLists: function displayFleetLists() {
        if (!fleetListManager.initialized) {
            $("#fleetListBody").empty();
            const template = $("#logtemplates .fleetlistentry");

            var uniqueTeams = [];
            for (const i in gamedata.slots) {
                var team = parseInt(gamedata.slots[i].team, 10);
                if (team > 0 && !uniqueTeams.includes(team)) {
                    uniqueTeams.push(team);
                }
            }
            uniqueTeams.sort(function (a, b) { return a - b; });

            for (var t = 0; t < uniqueTeams.length; t++) {
                var currentTeam = uniqueTeams[t];
                for (const i in gamedata.slots) {
                    const slot = gamedata.slots[i];
                    if (parseInt(slot.team, 10) === currentTeam) {
                        fleetListManager.createFleetList(slot, template);
                    }
                }
            }

            //The same sweep the loop above already needed, reused for the control bar's
            //team picker rather than derived a second time.
            fleetListManager.buildTeamFilter(uniqueTeams);

            fleetListManager.initialized = true;
        } else if (!fleetListManager.refreshed) { //Just refresh whether orders committed or not.
            // Only update turnTaken text if refreshing
            for (const i in gamedata.slots) {
                const slot = gamedata.slots[i];
                fleetListManager.updateTurnTakenInFleetHeader(slot);
            }

            // Reset the flag
            fleetListManager.refreshed = true;
        }

        fleetListManager.updateFleetList();
    },

    /* ALWAYS DRAWN, at any team count (user, 2026-08-31). The plan hid it below three
       teams on the grounds that a 2-player game's picker has only two real options and is
       noise - but a control that comes and goes with the game is worse than one option too
       many, and filtering to one side of a duel is a perfectly ordinary thing to want.
       Only a game with NO teams at all (nothing to pick between) still hides it. */
    buildTeamFilter: function buildTeamFilter(uniqueTeams) {
        var sel = $("#fleetTeamFilter");
        if (!sel.length) return;

        if (!uniqueTeams.length) {
            $("#fleetTeamFilterWrap").hide();
            fleetListManager.teamFilter = 0;
            return;
        }

        //A remembered team from a DIFFERENT game may not exist in this one, which would
        //filter every fleet away with a picker that says "All teams".
        if (fleetListManager.teamFilter && uniqueTeams.indexOf(fleetListManager.teamFilter) === -1) {
            fleetListManager.teamFilter = 0;
        }

        var html = "<option value='0'>All teams</option>";
        for (var i = 0; i < uniqueTeams.length; i++) {
            html += "<option value='" + uniqueTeams[i] + "'>Team " + uniqueTeams[i] + "</option>";
        }
        sel.html(html).val(String(fleetListManager.teamFilter));
        $("#fleetTeamFilterWrap").show();
    },

    createFleetList: function createFleetList(slot, template) {
        var shipArray = new Array();

        // Clone the template and append to the INFO tab's scrolling body. It used to go
        // straight into #gameinfo, which put the fleets in the same box as the panel's
        // chrome; a body of its own is what lets the head and control bars stay put.
        var fleetlistentry = template.clone(true).appendTo("#fleetListBody");

        // CHANGED: Use a unique class based on slot ID instead of just playerid (to avoid DOM selector collisions)
        fleetlistentry.addClass("slot_" + slot.slot);
        fleetlistentry.attr("data-team", parseInt(slot.team, 10) || 0);

        var teamName = "TEAM " + slot.team;

        // Colour ONLY the "TEAM X" label to match the combat-log scheme:
        // observers (and 3+-team participants) get the absolute per-team palette;
        // 2-team participants get relative mine=green / ally=blue / enemy=red.
        // Player name + points keep their default colour for now.
        var headerColor = gamedata.getFleetHeaderColorRGB(slot);
        var headerColorStyle = "color:" + headerColor + ";";

        /* The 3px rail is the SECOND arm of the same two-arm allegiance gate as the label:
           both take their colour from getFleetHeaderColorRGB, so a 4-team game and a 2-team
           game agree about it. Never give this rail a CSS-class colour - that arm only ever
           fires for 2-team participants (arch_team_colour_logic).

           ⭐ ONE WRITE PAINTS THE WHOLE SLOT (user, 2026-08-31). This used to set
           border-left-color directly on .fleetheader, so the rail stopped at the header and
           the rows below it looked unattached to the fleet they belong to. It now sets a
           custom property on the BLOCK; logPanel.css hangs the header's rail, the column
           head's and every row's off that one value, and inheritance carries it to all of
           them - so rows built later in this function need no inline style of their own,
           and a row rebuilt mid-turn cannot lose the colour.

           setProperty, not .attr("style", ...): .attr would REPLACE the style attribute,
           and this element is the one the row templates are cloned out of. */
        fleetlistentry.get(0).style.setProperty("--fv-fleet-rail", headerColor);
        fleetlistentry.find(".fleetheader")
            .html(
                "<span class='headername' style='" + headerColorStyle + "'>" + teamName + "</span><span class='playername'>" + slot.playername + "</span>"
            );

        var mineGroups = {};

        // Build list of ships for this player
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;

            //Hangar Ops Stage 9: a docked flight whose fighters were all
            //disengaged (e.g. partial relaunch consumed its identity) carries
            //combatValue 0 and adds no information — skip it. Normal docked
            //flights (combatValue > 0) still render as "Docked" rows.
            if (ship.removed && ship.flight && (ship.combatValue === 0)) continue;

            //Stage S: an integrated Shadow flight that has fully REABSORBED (every craft
            //landed/docked, none still flying or cut off) is folded back into its carrier's
            //value — it must NOT render its own row (it would otherwise show as "Destroyed"
            //since all its fighters are docked-and-inactive but the source flight row may
            //not be flagged removed). Skip when no craft remain in space (alive or cut off).
            if (ship.flight && isFullyReabsorbedIntegratedFlight(ship)) continue;

            if (ship.userid == slot.playerid && ship.slot == slot.slot) {
                if (ship.mine) {
                    if (ship.spawned != -1) continue; // Exclude spawned mines

                    var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
                    var shipClass = ship.shipClass;
                    if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
                        shipClass = "Mine";
                    }

                    if (!mineGroups[shipClass]) {
                        mineGroups[shipClass] = [];
                    }

                    mineGroups[shipClass].push(ship);
                } else {
                    shipArray.push(ship);
                }
            }
        }

        var fleetlisttable = fleetlistentry.find(".fleetlist");

        // CHANGED: Only search for the template inside this fleetlistentry, not globally
        template = fleetlistentry.find(".fleetlistline");

        var fleetlistline = template.clone(true);

        // Remove original template line (so it doesn’t get duplicated)
        fleetlistentry.find(".fleetlistline").remove();

        /* Column heads are SORT CONTROLS now (data-sort), so they carry a class of their
           own to keep them out of the row sweeps that follow. Labels are shortened to fit
           the fixed grid tracks - "Current Value" was wider than the column it headed. */
        fleetlistline.addClass("fleetlisthead");
        fleetlistline.html("<span>"
            + "<span class='shipname header' data-sort='name'>Ship Name</span>"
            + "<span class='shipclass header' data-sort='class'>Class</span>"
            + "<span class='shiptype header' data-sort='type'>Type</span>"
            + "<span class='initiative header' data-sort='ini'>Ini</span>"
            + "<span class='value header' data-sort='value'>Value</span>"
            + "</span>");
        fleetlistline.appendTo(fleetlisttable);

        var totalBaseValue = 0;
        var totalCurrValue = 0;

        // Add each ship to the list
        for (var index in shipArray) {
            ship = shipArray[index];
            fleetlistline = template.clone(true);

            var shiptype = "unknown";
            switch (ship.shipSizeClass) {
                case -1:
                    shiptype = "Squadron";
                    break;
                case 1:
                    shiptype = "MCV";
                    break;
                case 2:
                    shiptype = "HCV";
                    break;
                case 3:
                    shiptype = "Capital";
                    break;
                default:
                    break;
            }

            var baseValue = ship.pointCost || 0;
            //Hangar Ops Stage 21.7: value a flight from its actual craft via
            //activeFlightValue — base on the retained roster (excluding craft that
            //split off to their own Docked/Split row) and an UNROUNDED combat-value
            //fraction. This both re-bases partial-dock/launch remnants and removes
            //the server CV's integer-percent rounding (the round happens once below,
            //fixing e.g. 289 -> 290 for a 5/6-active flight). Falls back to the
            //flightSize + server-combatValue path only for a non-flight or a flight
            //with no Fighter subsystems (activeVal null).
            var activeVal = activeFlightValue(ship);
            var effectiveCV = (ship.combatValue !== undefined ? ship.combatValue : 100);
            if (ship.flight === true) {
                if (activeVal) {
                    baseValue = (ship.pointCost || 0) * (activeVal.activeCraft / 6);
                    effectiveCV = activeVal.combatValue;   //unrounded fraction; rounded once below
                } else {
                    // Flights have cost calculated per 6 fighters
                    baseValue = (ship.pointCost || 0) * (ship.flightSize / 6);
                }
            }
            baseValue = Math.round(baseValue + (ship.pointCostEnh || 0) + (ship.pointCostEnh2 || 0));

            //Stage S: net LAUNCHED integrated fighters off this carrier BEFORE applying its
            //combat value — their worth now lives on their own flight rows (valued at their
            //own CV), so the carrier's combat damage must NOT scale them. The carrier keeps
            //only its HELD integrated fighters' share of enhValue; its value drops as they
            //launch and rises as they reabsorb on dock. Subtract from the BASE so the CV
            //multiply below applies only to the hull + retained complement.
            var intAdjust = integratedFighterCarrierAdjust(ship);
            if (intAdjust > 0) baseValue = Math.max(0, baseValue - intAdjust);

            var currValue = Math.round(baseValue * effectiveCV / 100);

            //Stage 9: carriers carry the point cost of any anonymous docked
            //craft (auto-filled shuttles are 0-cost; orphaned fighter records
            //from partial relaunches contribute). dockedFlightId records are
            //shown as separate "Docked" rows, so we deliberately skip them.
            //We add the same value to both baseValue and currValue — stash
            //craft take no damage in storage; hangar damage that evicts them
            //is reflected by the entry no longer being in hangarUsage.
            var stashValue = dockedCraftStashValue(ship);
            if (stashValue > 0) {
                baseValue += stashValue;
                currValue += stashValue;
            }

            totalBaseValue += baseValue;
            totalCurrValue += currValue;

            /* `unitrow` means "there is something here worth opening" - it is what carries
               the hover highlight and the right-click / long-press route to the ship
               window. The mine group and the enemy's reinforcement SUMMARY below
               deliberately do not get it: neither is a single unit with a ship object
               behind it. setRowState takes it back off destroyed and jumped rows, which
               are gone from the battle - but NOT off docked or hyperspace ones, which are
               yours and still coming back (user decisions, 2026-08-31). */
            var iniOrder = shipManager.getIniativeOrder(ship);
            fleetlistline.addClass("unitrow").attr("data-shipid", ship.id);
            fleetlistline.attr("data-sort-name", String(ship.name).toLowerCase());
            fleetlistline.attr("data-sort-class", String(ship.shipClass).toLowerCase());
            fleetlistline.attr("data-sort-type", shiptype.toLowerCase());
            fleetlistline.attr("data-sort-ini", parseFloat(iniOrder) || 0);
            fleetlistline.attr("data-sort-value", currValue);

            fleetlistline.html(
                "<span id='" + ship.id + "'>" +
                "<span class='shipname clickable' data-shipid='" + ship.id + "'>" + ship.name + "</span>" +
                "<span class='shipclass'>" + ship.shipClass + "</span>" +
                "<span class='shiptype'>" + shiptype + "</span>" +
                "<span class='initiative'>" + iniOrder + "</span>" +
                "<span class='value'>" + currValue + '/' + baseValue + "CP</span>" +
                //Discoverability for the right-click / long-press gesture, and on touch a
                //tap target for it in its own right - the same reasoning as the hex
                //picker's details button (arch_hex_stack_picker).
                "<span class='rowinfo' title='Open ship window' role='button'>&#9432;</span>" +
                "<span class='shipstatus'></span></span>"
            );

            fleetlistline.appendTo(fleetlisttable);
        }

        // Add grouped mines to the list
        for (var mineClass in mineGroups) {
            var mines = mineGroups[mineClass];
            var firstMine = mines[0];
            var bulkBuy = 0;

            fleetlistline = template.clone(true);
            var shiptype = "Mine";

            var combinedBaseValue = 0;
            var combinedCurrValue = 0;

            for (var m in mines) {
                var mine = mines[m];
                var mCount = mine.bulkBuy || 1;
                bulkBuy += mCount;
                var mBaseValue = Math.round(((mine.pointCost || 0) + (mine.pointCostEnh || 0) + (mine.pointCostEnh2 || 0)) * mCount);
                var mCurrValue = Math.round(mBaseValue * (mine.combatValue !== undefined ? mine.combatValue : 100) / 100);
                combinedBaseValue += mBaseValue;
                combinedCurrValue += mCurrValue;
            }

            var uniqueClassCount = Object.keys(mineGroups).length;
            var surchargeMultiplier = 1 + ((uniqueClassCount - 1) * 0.10);

            // Apply fleet-wide 100pt premium and class surcharges uniformly to the display values
            // To make it look right on a per-row basis, we take the raw mine group cost, 
            // add its proportional share of the 100pt premium, and multiply by surcharge.
            var rawTotalMineCost = 0;
            for (var mC in mineGroups) {
                for (var mm in mineGroups[mC]) {
                    var mmCount = mineGroups[mC][mm].bulkBuy || 1;
                    rawTotalMineCost += Math.round(((mineGroups[mC][mm].pointCost || 0) + (mineGroups[mC][mm].pointCostEnh || 0) + (mineGroups[mC][mm].pointCostEnh2 || 0)) * mmCount);
                }
            }

            var GroupProportion = (rawTotalMineCost > 0) ? (combinedBaseValue / rawTotalMineCost) : 0;
            var finalGroupBaseValue = Math.round((combinedBaseValue + (100 * GroupProportion)) * surchargeMultiplier);
            var finalGroupCurrValue = Math.round(finalGroupBaseValue * (firstMine.combatValue !== undefined ? firstMine.combatValue : 100) / 100);

            totalBaseValue += finalGroupBaseValue;
            totalCurrValue += finalGroupCurrValue;

            var displayName = mineClass + " (" + bulkBuy + ")";

            /* No `unitrow`: a mine group is a BULK row, not a single unit, so there is no
               ship object for the window to open. It is still ON THE MAP though, so the
               "On map only" filter must leave it alone - hence its own class. */
            fleetlistline.addClass("minerow");
            fleetlistline.attr("data-sort-name", displayName.toLowerCase());
            fleetlistline.attr("data-sort-class", String(mineClass).toLowerCase());
            fleetlistline.attr("data-sort-type", "mine");
            fleetlistline.attr("data-sort-ini", parseFloat(shipManager.getIniativeOrder(firstMine)) || 0);
            fleetlistline.attr("data-sort-value", combinedCurrValue);

            fleetlistline.html(
                "<span>" +
                "<span class='shipname' style='cursor:default;' title='Mines cannot be selected here'>" + displayName + "</span>" +
                "<span class='shipclass'>" + mineClass + "</span>" +
                "<span class='shiptype'>" + shiptype + "</span>" +
                "<span class='initiative'>" + shipManager.getIniativeOrder(firstMine) + "</span>" +
                "<span class='value'>" + combinedCurrValue + '/' + combinedBaseValue + "CP</span>" +
                "<span class='shipstatus'></span></span>"
            );

            fleetlistline.appendTo(fleetlisttable);
        }

        /* REINFORCEMENTS_PLAN.md §3.6 - THE ENEMY'S VIEW OF A FLEET STILL IN HYPERSPACE: one row
           carrying a count and a point total, and nothing else. Never classes, never names.

           These two numbers are non-zero ONLY in a masked payload: the sweep that fills them
           (TacGamedata::hideHyperspaceReinforcements) is the same one that deletes those units from
           $this->ships, so the owner and their team never reach this branch - they got the real
           rows, which the shipArray loop above has already listed and priced (it has no deploy-turn
           filter, by design).

           ⭐ THE POINTS GO INTO THE HEADER TOTAL, and that is the point of having them. The owner's
           own copy already counts these units, so leaving them out here would make the two players'
           headers disagree about the same fleet - which is itself a tell, and a louder one than the
           number. Added BEFORE the Chameleon adjustment below so the curr/base ratio it computes
           stays coherent.

           Current == base: a unit that has never been on the board has taken no damage. */
        var reinfCount = parseInt(slot.reinforcementCount, 10) || 0;
        var reinfPoints = parseInt(slot.reinforcementPoints, 10) || 0;
        if (reinfCount > 0) {
            fleetlistline = template.clone(true);
            //No .clickable, no id and no `unitrow`: there is no unit here to scroll to or to
            //open a window for - this is an AGGREGATE of the enemy's masked-out units. Same
            //reasoning as the mine group's un-clickable name above. It is off the board, so
            //the "On map only" filter DOES hide it (the .hyperspace class it already carries
            //is what that filter reads).
            fleetlistline.addClass("reinfrow");
            fleetlistline.attr("data-sort-name", "reinforcements");
            fleetlistline.attr("data-sort-class", "");
            fleetlistline.attr("data-sort-type", "unknown");
            fleetlistline.attr("data-sort-ini", 9999);
            fleetlistline.attr("data-sort-value", reinfPoints);
            fleetlistline.html(
                "<span>" +
                "<span class='shipname hyperspace' style='cursor:default;' title='Bought as reinforcements and still in hyperspace'>Reinforcements</span>" +
                "<span class='shipclass hyperspace'>" + reinfCount + (reinfCount === 1 ? " unit" : " units") + "</span>" +
                "<span class='shiptype hyperspace'>Unknown</span>" +
                "<span class='initiative hyperspace'>&mdash;</span>" +
                "<span class='value hyperspace'>" + reinfPoints + "CP</span>" +
                "<span class='shipstatus'></span></span>"
            );
            fleetlistline.appendTo(fleetlisttable);

            totalBaseValue += reinfPoints;
            totalCurrValue += reinfPoints;
        }

        //Chameleon Sensor Suite: a disguised ship is valued off the simulacrum blueprint this viewer
        //was served, so a simulacrum dearer than the real hull inflates the total above what the slot
        //could legally spend — a fleet costing more than its budget is impossible, not just odd, and
        //that is a free reveal. The server hands us the overstatement to take back off the header
        //(TacGamedata::setChameleonFleetValueAdjust); it is 0 on every slot of every ordinary game,
        //and 0 again for this fleet's owner, their allies, and everyone once the deception breaks.
        //
        //Rows are deliberately left showing the simulacrum's own cost — capping the ROW instead would
        //put "Octurion — 750CP" on screen against a catalogue cost every player can look up, which is
        //a plainer contradiction than a header that no longer sums.
        //
        //Current value is scaled rather than adjusted separately: at full health curr == base, so
        //discounting base alone would render 3600/3000. The fleet-wide ratio keeps the pair coherent
        //and needs no second field to stay pinned at 0.
        var fleetValueAdjust = parseFloat(slot.fleetValueAdjust) || 0;
        if (fleetValueAdjust > 0 && totalBaseValue > 0) {
            var valueRatio = totalCurrValue / totalBaseValue;
            totalBaseValue = Math.max(0, totalBaseValue - fleetValueAdjust);
            totalCurrValue = Math.round(totalBaseValue * valueRatio);
        }

        var deploys = "";
        if (slot.depavailable > gamedata.turn) {
            deploys = "<span class='fv-tag fv-tag--deploys'>Deploys T" + slot.depavailable + "</span>";
        }

        /* The header is a card head now (plan Stage 3): team label, player name, the CP
           total as a mono readout, and the state as .fv-tag chips rather than three
           different inline colours and a run of &nbsp;. Its 3px rail was set on the
           element itself above and survives this rebuild - only the innerHTML is replaced. */
        fleetlistentry.find(".fleetheader").html(
            "<span class='headername' style='" + headerColorStyle + "'>" + teamName + "</span>" +
            "<span class='playername'>" + slot.playername + "</span>" +
            "<span class='fleetcp'>" + totalCurrValue + " / " + totalBaseValue + " CP</span>" +
            "<span class='turnTaken'>" + fleetListManager.turnTakenHtml(slot) + "</span>" +
            deploys
        );

        // Add ship click handler
        $(".clickable", fleetlistentry).on("click", fleetListManager.doScrollToShip);
    },

    /* ONE source for the three commit-state chips, shared by the initial build and by
       updateTurnTakenInFleetHeader - they used to be the same switch and the same three
       strings written out twice, which is how the two spellings of the "Orders committed"
       green (`rgb(50, 205, 50);` vs `rgb(50, 205, 50)`) survived side by side. */
    turnTakenHtml: function turnTakenHtml(slot) {
        var phaseLabel = "Initial"
        switch (gamedata.gamephase) {
            case -1:
                phaseLabel = "Pre-Turn";
                break;
            case 2:
                phaseLabel = "Movement";
                break;
            case 5:
                phaseLabel = "Pre-Firing";
                break;
            case 3:
                phaseLabel = "Firing";
                break;
        }

        if (slot.surrendered !== null && slot.surrendered <= gamedata.turn) {
            //Surrender is checked first: a surrendered slot's commit state is history.
            return "<span class='fv-tag fv-tag--surrendered'>Surrendered T" + slot.surrendered + "</span>";
        }
        if (slot.waiting) {
            return "<span class='fv-tag fv-tag--committed'>Orders committed</span>";
        }
        return "<span class='fv-tag fv-tag--waiting'>Waiting for " + phaseLabel + " orders</span>";
    },

    updateTurnTakenInFleetHeader: function updateTurnTakenInFleetHeader(slot) {
        const container = $(".slot_" + slot.slot); // Target the correct fleet list block
        const header = container.find(".fleetheader .turnTaken");

        if (!header.length) return; // Just in case something went wrong

        header.html(fleetListManager.turnTakenHtml(slot));
    },

    updateFleetReadiness: function updateFleetReadiness(playerId) {

        for (const i in gamedata.slots) {
            const slot = gamedata.slots[i];
            if (slot.playerid === playerId) {
                slot.waiting = true; //Set this manually for front end to know, gamedata will not refect it yet with page refresh
                fleetListManager.refreshed = false;
                fleetListManager.displayFleetLists();
            }
        }

    },


    //`options.select` additionally makes the ship the selected ship once the camera
    //arrives (see PhaseStrategy.onScrollToShip). Off by default, so a fleet list row
    //keeps behaving as it always has - scroll only.
    doScrollToShip: function doScrollToShip(e, options) {
        e.stopPropagation();

        //A long press has already opened the window; swallow the click the gesture leaves
        //behind so the row does not ALSO scroll to the ship (arch_hex_stack_picker).
        if (fleetListManager.longPressFired) {
            fleetListManager.longPressFired = false;
            return;
        }

        var shipNameEntry = e.currentTarget;

        if (!shipNameEntry.classList.contains("clickable")) {
            return;
        }

        var shipId = shipNameEntry.dataset["shipid"];
        var ship = gamedata.getShip(shipId);

        //getShip returns null for an id no longer in gamedata.ships. A fleet list row is
        //rebuilt from gamedata.ships so it can't go stale, but a confirm dialog holds its
        //rendered ship names across polls - so this can be clicked after the fact.
        if (!ship) {
            return;
        }

        /* CLICK BEHAVIOUR FOLLOWS BOARD PRESENCE (plan Stage 3). A unit that is off the
           board has nothing for a scroll to find, so the click opens its window instead:
             - a DOCKED flight (Hangar Ops Stage 9.1 - the window is the only route to a
               bay's contents), and
             - a REINFORCEMENT still in hyperspace, which is yours, which you paid for,
               and which the window is the only way to look at before it arrives.
           Ship-window redesign Stage 2d (SHIPWINDOW_REDESIGN_PLAN.md §4.5) routes both
           through the OpenShipWindowFor event PhaseStrategy already handles.

           This sits ABOVE the shouldBeHidden guard, and has to: shouldBeHidden is a
           BOARD-PRESENCE test - it treats every removed flight as destroyed and every
           not-yet-deployed unit as hidden - so below the guard neither branch could ever
           be reached. openShipWindowFor applies the RIGHT test for a window, which is
           whether this viewer is entitled to the ship at all. */
        if (fleetListManager.isOffBoardButOurs(ship)) {
            fleetListManager.openShipWindowFor(ship);
            return;
        }

        if (shipManager.shouldBeHidden(ship)) { //Enemy, stealth equipped and undetected, or not deployed yet.
            return; //Do not scroll to Stealthed ships
        }

        window.webglScene.customEvent('ScrollToShip', {
            shipId: shipId,
            select: !!(options && options.select)
        });
    },

    /* "Off the board, but yours and still coming back" - the two states whose row opens a
       window on EITHER click, because scroll-to-ship has nothing to do for them. NOT the
       same question as shouldBeHidden, which also covers units that are gone for good. */
    isOffBoardButOurs: function isOffBoardButOurs(ship) {
        if (ship.removed && ship.flight) return true;   //docked flight
        if (ship.reinforcement && (ship.arrivalTurn === null || ship.arrivalTurn === undefined)
            && gamedata.isMyorMyTeamShip(ship)) {
            return true;                                 //still in hyperspace
        }
        return false;
    },

    /* ⭐ THE GUARD IS NOT A BARE shouldBeHidden, AND THAT IS THE WHOLE TRICK.

       shouldBeHidden (ships.js) answers "should this be drawn on the map?", not "may this
       viewer know about it?". It returns TRUE for a unit of your OWN that is merely not
       deployed yet, and (through its destroyed check) for every docked flight - i.e. for
       precisely the two cases a fleet-list row is being asked to open. A window reveals no
       board POSITION, which is the only thing that guard exists to protect.

       isMyorMyTeamShip is the exactly-right bypass rather than a judgement call: it is the
       byte-for-byte client twin of the server's own mask. TacGamedata::
       hideHyperspaceReinforcements keeps the real ship rows for
       `$ship->userid == $this->forPlayer || $ship->team == $playerTeam` and DELETES the
       ships from the payload for everyone else; gamedata.isMyorMyTeamShip tests the same
       two clauses. So if a hyperspace row exists at all, this viewer is already entitled
       to it, and the guard is left covering only the case masking does not - an ENEMY ship
       that is on the board but stealthed and undetected, where the row exists and the
       position must stay secret. That case still returns early, so nothing leaks.

       ⚠️ Slightly over-strict in one corner: in a FINISHED game the server discloses
       everything, so an enemy hyperspace row can exist post-mortem and this will still
       refuse it. Safe direction, and post-mortem disclosure is the server's business. */
    openShipWindowFor: function openShipWindowFor(ship) {
        if (!ship) return;
        if (!gamedata.isMyorMyTeamShip(ship) && shipManager.shouldBeHidden(ship)) return;
        //OpenShipWindowFor rather than onShipRightClicked: the latter also SELECTS the
        //ship, which a fleet-list row has no business doing.
        window.webglScene.customEvent('OpenShipWindowFor', { ship: ship });
    },

    //Hangar Ops: ids of docked flights whose carrier jumped to hyperspace. A
    //jumped carrier stays in gamedata.ships and keeps its hangarUsage (and the
    //dockedFlightId links) intact, so we map each jumped carrier's stored craft
    //back to the flight rows the fleet list renders. updateFleetList uses this
    //to show those flights as "Jumped" (orange) rather than "Docked" (blue) —
    //a docked flight has no jump engine of its own, so hasJumpedNotDestroyed
    //can't detect this on the flight directly.
    //
    //OWN TEAM ONLY, unavoidably: bay contents are masked out of an opponent's
    //payload (Hangar::stripForJson), so their hangarUsage arrives as an empty list
    //and this walk finds nothing. The server answers it for them instead, with the
    //flight's own jumpedWithCarrier flag (TacGamedata::markJumpedDockedFlights),
    //which updateFleetList ORs in below. Both are kept: the flag needs a round trip,
    //while this walk flips the owner's own rows the instant they commit the jump.
    getJumpedDockedFlightIds: function getJumpedDockedFlightIds() {
        var ids = {};
        for (var i in gamedata.ships) {
            var carrier = gamedata.ships[i];
            if (shipManager.isDestroyed(carrier)) {
                //OUT OF PLAY: the damage entries are the record and the pending order is history.
                //hasJumpedNotDestroyed only distinguishes "jumped" from "damage-killed" among ships
                //already out of play — on a healthy, in-play carrier it returns true purely because
                //it has a jump engine and little non-jump damage — which is why it is gated on
                //isDestroyed, the same pairing updateFleetList uses for a ship's own row.
                //⚠️ ASKED BEFORE THE ORDER, not after (Vortex Disruptor, 2026-08-29): a carrier
                //killed by a collapsing jump point still carries the jumpout order it flew in on,
                //so taking the order as proof first painted a wreck's flights Jumped for good. The
                //server twin of this ordering is TacGamedata::hasLeftThroughVortex.
                if (!shipManager.hasJumpedNotDestroyed(carrier)) continue;
            } else {
                /* STILL IN PLAY: a carrier that has COMMITTED a jump-out is on its way out but the
                   server has not removed it yet (that happens at the end of the Movement phase).
                   Take the order as proof on its own, so the flights flip to "Jumped" at the same
                   instant as the carrier's own row and its map sprite. */
                if (!shipManager.movement.hasCommittedJumpOut(carrier)) continue;
            }
            if (!Array.isArray(carrier.systems)) continue;
            for (var s = 0; s < carrier.systems.length; s++) {
                var sys = carrier.systems[s];
                if (!sys || !Array.isArray(sys.hangarUsage)) continue;
                for (var u = 0; u < sys.hangarUsage.length; u++) {
                    var entry = sys.hangarUsage[u];
                    if (entry && entry.dockedFlightId) ids[entry.dockedFlightId] = true;
                }
            }
        }
        return ids;
    },

    /* THE fleet list's row for a ship - never $("#" + ship.id) on its own.

       gamedata.drawIniGUI gives every Order of Battle <tr> the ship's RAW id too, and #iniGui is
       written before #gameinfo in game.php - so getElementById (which is what jQuery's #id fast
       path uses) hands back the Order of Battle row for any unit still listed there, and the paint
       silently lands on the wrong element: addClass colours an OoB row nobody styles, and the
       .initiative lookup inside it matches nothing at all, because an OoB row holds .iniOrder and
       .iniInfo instead.

       It only bites a unit that is still IN the Order of Battle when its row changes state, which
       is why it went unnoticed: drawIniGUI filters out anything isDestroyed, so docked flights and
       destroyed hulls - the only two states this ever painted before - had already dropped out and
       their ids really were unique. A jumped-out ship has NOT: the server does not remove it until
       the end of the Movement phase, so for that whole phase its own row was the one row that could
       not be painted, while its docked flights' rows changed correctly around it.

       Scoped to the fleet list container rather than renaming the ids: the OoB's own click handler
       reads this.id, and an attribute selector sidesteps the "#123" invalid-CSS-identifier
       question entirely. */
    fleetRow: function fleetRow(ship) {
        return $("#gameinfo").find("[id='" + ship.id + "']");
    },

    /* Paint one row's out-of-play state. The four states are MUTUALLY EXCLUSIVE and the rows are
       only rebuilt at the start of a turn (displayFleetLists rebuilds when fleetListManager.reset()
       has cleared `initialized`, which only initPhase does, in phase 1) - so a row that changes
       state mid-turn keeps whatever class it was given earlier unless the others are taken off it.
       They are the same specificity, so the CASCADE decided which colour won, and .docked is
       written after .jumped in tactical.css: a docked flight whose carrier jumped read "Jumped"
       in blue and only turned orange at the next turn's rebuild. Set the class, clear the
       others. */
    setRowState: function setRowState(ship, state, label) {
        var STATES = ["jumped", "docked", "destroyed", "hyperspace"];
        var row = fleetListManager.fleetRow(ship);
        for (var s = 0; s < STATES.length; s++) {
            if (STATES[s] !== state) row.removeClass(STATES[s]);
        }
        row.addClass(state);
        row.find(".initiative").html(label);

        /* ⭐ `unitrow` is taken away for DESTROYED and JUMPED only (user, 2026-08-31).
           The line is not on-the-board vs off it - it is GONE FROM THE BATTLE vs YOURS AND
           STILL COMING BACK:
             destroyed / jumped  -> inert. No highlight, no window; there is nothing to
                                    inspect in a wreck and nothing to plan around a unit
                                    that has left.
             docked              -> keeps it. Hangar Ops Stage 9.1 - the ship window is the
                                    ONLY route to a bay's contents.
             hyperspace          -> keeps it, for the owner and their team, who are the only
                                    viewers the server gives real rows to.
           This is the right home for it: setRowState already runs for exactly these four
           states and already strips the sibling state classes. It replaces the two
           scattered removeClass("clickable") calls that used to sit in updateFleetList. */
        var line = row.closest(".fleetlistline");
        if (state === "destroyed" || state === "jumped") {
            line.removeClass("unitrow");
            row.find(".shipname").removeClass("clickable");
        }
    },

    updateFleetList: function updateFleetList() {
        //Hangar Ops: collect the docked flights whose carrier jumped to
        //hyperspace once, before the row loop, so we can flag them below.
        var jumpedDockedFlightIds = fleetListManager.getJumpedDockedFlightIds();

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var name = ship.name;
            /* A COMMITTED jump-out is a departure the server has not resolved yet - it removes the
               unit at the end of the Movement phase - but the order is on the board and the map
               already shows the hex empty. Read it as Jumped from that moment, so the ship, its
               docked flights and its sprite all change together instead of the list lagging
               behind the rest of the turn (JUMP_POINTS_PLAN.md Stage 4). */
            var jumpingOut = shipManager.movement.hasCommittedJumpOut(ship);
            if (shipManager.isDestroyed(ship) || jumpingOut) {
                if (ship.removed) {
                    //Docked flight: same isDestroyed=true filtering, but not
                    //actually destroyed. Keep .clickable so the player can
                    //open the flight window (doScrollToShip opens the React
                    //ship window via OpenShipWindowFor for removed flights
                    //since they're not on the board).
                    //Two sources by design - see getJumpedDockedFlightIds: the local walk
                    //covers this flight's owner immediately, the server's jumpedWithCarrier
                    //covers every OTHER viewer, whose copy of the bay is masked empty.
                    if (jumpedDockedFlightIds[ship.id] || ship.jumpedWithCarrier) {
                        //Carrier jumped to hyperspace and took the flight with
                        //it: it kept its combat value but is no longer in play,
                        //so render it like a jumped ship (orange) not docked.
                        fleetListManager.setRowState(ship, "jumped", "Jumped");
                    } else {
                        fleetListManager.setRowState(ship, "docked", "Docked");
                    }
                    continue;
                }
                /* The removeClass("clickable") that used to sit here has moved into
                   setRowState, which is where the four out-of-play states are decided and
                   which now also strips `unitrow`. */
                /* ⚠️ ONCE THE UNIT IS OUT OF PLAY, THE DAMAGE DECIDES — NOT THE PENDING ORDER
                   (Vortex Disruptor, 2026-08-29). A ship killed by a collapsing jump point is
                   destroyed AND still carries the jumpout order it flew in on, so ORing the two
                   the way this used to would have labelled the wreck Jumped and, worse, kept its
                   combat value out of the enemy's score (fleetListManager's value walk at the top
                   of this file uses the same pairing). Before the removal resolves there is no
                   damage to read yet, so the committed order is the only signal and stands.
                   Same shape, same reason, as getJumpedDockedFlightIds above. */
                var jumpedOut = shipManager.isDestroyed(ship)
                    ? shipManager.hasJumpedNotDestroyed(ship)
                    : jumpingOut;
                if (jumpedOut) {
                    fleetListManager.setRowState(ship, "jumped", "Jumped");
                } else {
                    fleetListManager.setRowState(ship, "destroyed", "Destroyed");
                }
            } else if (ship.reinforcement && (ship.arrivalTurn === null || ship.arrivalTurn === undefined)) {
                /* REINFORCEMENTS_PLAN.md - a reinforcement still WAITING IN HYPERSPACE. Every other
                   list in the game drops it for free off getTurnDeployed's 999, but the fleet list
                   is deliberately the one that shows a fleet in FULL - that is how a late slot's
                   ships appear under a "[Deploys on Turn N]" header from turn 1 - so it needs
                   telling explicitly.
                   The row STAYS: this is the owner's own list, they paid for these units and need
                   to see what is still waiting (and its points are already in the fleet totals
                   above). It is an ENEMY's copy that must not show them at all, and that is done
                   a whole layer down by dropping the ship from the payload - so by the time a row
                   could be built there is nothing to build it from.

                   ⭐ IT KEEPS ITS CLICK, as of 2026-08-31 (user decision). The old
                   removeClass("clickable") here was argued from shouldBeHidden refusing to
                   SCROLL to a unit that is not on the board - true, but the row does not
                   have to scroll. These are units this player bought and is waiting on, and
                   the ship window is the only way to look at them before they arrive, so
                   doScrollToShip opens the window for them instead (isOffBoardButOurs). */
                fleetListManager.setRowState(ship, "hyperspace", "Hyperspace");
            }
        }

        //The state classes this loop paints are exactly what the "On map only" filter
        //reads, so the two can never disagree about what is on the board.
        fleetListManager.applyView();
    },

    /* ── VIEW: filters, sort and the head-bar readout (plan Stage 3) ──────────────
       Runs at the END of updateFleetList, which is the only place the four out-of-play
       state classes are painted - so the filter READS that state rather than re-deriving
       it, and the two cannot drift apart. */
    applyView: function applyView() {
        var body = $("#fleetListBody");
        if (!body.length) return;

        var team = fleetListManager.teamFilter;
        var onMap = fleetListManager.onMapOnly;
        var visibleUnits = 0;
        var visibleFleets = 0;

        body.children(".fleetlistentry").each(function () {
            var block = $(this);
            var blockTeam = parseInt(block.attr("data-team"), 10) || 0;

            if (team && blockTeam !== team) {
                block.hide();
                return;
            }

            var shown = 0;
            block.find(".fleetlistline").not(".fleetlisthead").each(function () {
                var line = $(this);
                var hide = false;
                if (onMap) {
                    /* OFF the board: destroyed, jumped, docked, and a reinforcement still
                       in hyperspace - whether it is a real row (the .hyperspace class) or
                       the enemy's aggregate summary row (.reinfrow).
                       ⚠️ MINES ARE ON THE MAP. The bulk mine row stays. */
                    var cells = line.children("span").first();
                    hide = line.hasClass("reinfrow")
                        || cells.hasClass("destroyed") || cells.hasClass("jumped")
                        || cells.hasClass("docked") || cells.hasClass("hyperspace");
                }
                line.toggle(!hide);
                if (!hide) shown++;
            });

            /* ⭐ THE FLEET BLOCK GOES TOO, not just its rows. A slot whose whole fleet is
               still in hyperspace would otherwise leave a header and a column head with
               nothing underneath them. */
            if (shown === 0 && onMap) {
                block.hide();
            } else {
                block.show();
                visibleFleets++;
                visibleUnits += shown;
            }
        });

        fleetListManager.applySort();

        /* An empty list is never left to explain itself. Two ways to get here: a filter
           that hid everything, or a payload with no ships at all - which is the ordinary
           state for a WAITING player, who is served no ship data at all
           (arch_gamedata_polling_cache). They read differently, so they say so. */
        var empty = body.find(".fleetListEmpty");
        if (visibleFleets === 0) {
            if (!empty.length) {
                empty = $("<div class='fleetListEmpty'></div>").appendTo(body);
            }
            empty.text(body.children(".fleetlistentry").length === 0
                ? "No fleet data yet."
                : (onMap || team)
                    ? "Nothing on the map matches the current filters."
                    : "No units in this game.").show();
        } else if (empty.length) {
            empty.hide();
        }

        //The readout recounts against the filter, so it always describes what is actually
        //on screen rather than what the payload happens to hold.
        if (window.botPanel && botPanel.setMeta) {
            botPanel.setMeta('info',
                visibleFleets + (visibleFleets === 1 ? ' FLEET' : ' FLEETS')
                + ' · ' + visibleUnits + (visibleUnits === 1 ? ' UNIT' : ' UNITS'));
        }
    },

    /* Column-head sorting. Reorders the existing rows rather than rebuilding them, so a
       row keeps its state classes, its handlers and its selection highlight. data-ord is
       stamped on first use and is what "no sort" restores - the build order, which is the
       slot's own initiative order. */
    applySort: function applySort() {
        var key = fleetListManager.sortKey;
        var dir = fleetListManager.sortDir;

        $("#fleetListBody .fleetlist").each(function () {
            var list = $(this);
            var rows = list.children(".fleetlistline").not(".fleetlisthead");
            if (rows.length < 2) return;

            var arr = rows.get();
            for (var i = 0; i < arr.length; i++) {
                if (arr[i].getAttribute("data-ord") === null) arr[i].setAttribute("data-ord", i);
            }

            var ord = function (el) { return parseInt(el.getAttribute("data-ord"), 10) || 0; };

            arr.sort(function (a, b) {
                if (!key) return ord(a) - ord(b);
                var r;
                if (key === "ini" || key === "value") {
                    r = (parseFloat(a.getAttribute("data-sort-" + key)) || 0)
                        - (parseFloat(b.getAttribute("data-sort-" + key)) || 0);
                } else {
                    var av = a.getAttribute("data-sort-" + key) || "";
                    var bv = b.getAttribute("data-sort-" + key) || "";
                    r = av < bv ? -1 : (av > bv ? 1 : 0);
                }
                if (r === 0) return ord(a) - ord(b);   //stable within equal keys
                return r * dir;
            });

            list.append(arr);
        });

        $("#fleetListBody .fleetlisthead .header").removeClass("sorted-asc sorted-desc");
        if (key) {
            $("#fleetListBody .fleetlisthead .header[data-sort='" + key + "']")
                .addClass(dir > 0 ? "sorted-asc" : "sorted-desc");
        }
    },

    /* Open the ship window for a row. The row carries the id; the entitlement question is
       openShipWindowFor's, not this one's. */
    openRowShipWindow: function openRowShipWindow(rowElement) {
        if (!rowElement) return;
        var id = rowElement.getAttribute("data-shipid");
        if (!id) return;
        fleetListManager.openShipWindowFor(gamedata.getShip(id));
    },

    /* Set by the long-press path so the click that follows it does not ALSO scroll to the
       ship. Cleared by whichever comes first - the click it suppresses, or the timeout,
       because Android fires contextmenu on its own and does not always follow with one. */
    longPressFired: false,

    markLongPressFired: function markLongPressFired() {
        fleetListManager.longPressFired = true;
        setTimeout(function () { fleetListManager.longPressFired = false; }, 700);
    },

    /* Right-click / long-press -> ship window, and the hover info affordance that makes the
       gesture discoverable (and gives touch an explicit tap target for it - the same
       reasoning as the hex picker's details button).

       ⚠️ TOUCH: an Android long press fires `contextmenu` BY ITSELF, so that path needs no
       timer - but iOS Safari does not, hence the pointerdown timer as a fallback, with
       longPressFired stopping the two routes from both firing (arch_hex_stack_picker). */
    initRowInteractions: function initRowInteractions() {
        var body = document.getElementById("fleetListBody");
        if (!body || body.getAttribute("data-fv-bound")) return;
        body.setAttribute("data-fv-bound", "1");

        var $body = $(body);

        $body.on("contextmenu", ".unitrow", function (e) {
            e.preventDefault();
            fleetListManager.markLongPressFired();
            fleetListManager.openRowShipWindow(this);
        });

        $body.on("click", ".rowinfo", function (e) {
            e.preventDefault();
            e.stopPropagation();
            fleetListManager.openRowShipWindow($(this).closest(".unitrow").get(0));
        });

        var timer = null;
        var startX = 0;
        var startY = 0;

        var cancel = function () {
            if (timer) { clearTimeout(timer); timer = null; }
        };

        $body.on("pointerdown", ".unitrow", function (e) {
            if (e.pointerType !== "touch") return;   //mouse has contextmenu
            var row = this;
            startX = e.clientX;
            startY = e.clientY;
            cancel();
            timer = setTimeout(function () {
                timer = null;
                fleetListManager.markLongPressFired();
                if (navigator.vibrate) { try { navigator.vibrate(30); } catch (ex) { } }
                fleetListManager.openRowShipWindow(row);
            }, 450);
        });

        $body.on("pointermove", ".unitrow", function (e) {
            if (!timer) return;
            if (Math.abs(e.clientX - startX) > 8 || Math.abs(e.clientY - startY) > 8) cancel();
        });

        $body.on("pointerup pointercancel pointerleave", ".unitrow", cancel);

        //Collapsible fleets. The header is the whole affordance - there is no separate
        //twisty to hit on a phone.
        $body.on("click", ".fleetheader", function () {
            $(this).closest(".fleetlistentry").toggleClass("collapsed");
        });

        //Sortable column heads: same column again reverses, a different column starts
        //ascending, and a third click on the same column drops back to build order.
        $body.on("click", ".fleetlisthead .header[data-sort]", function () {
            var key = String($(this).data("sort"));
            if (fleetListManager.sortKey !== key) {
                fleetListManager.sortKey = key;
                fleetListManager.sortDir = 1;
            } else if (fleetListManager.sortDir === 1) {
                fleetListManager.sortDir = -1;
            } else {
                fleetListManager.sortKey = null;
                fleetListManager.sortDir = 1;
            }
            fleetListManager.savePrefs();
            fleetListManager.applySort();
        });
    },

    /* Called by PhaseStrategy.setSelectedShip. Marks the selected unit's row, flashes it
       once, and brings it into view - but only while the INFO tab is actually on screen,
       so selecting a ship on the map cannot scroll a panel nobody is looking at. */
    revealShipRow: function revealShipRow(ship) {
        if (!ship) return;
        var body = $("#fleetListBody");
        if (!body.length) return;

        body.find(".fleetlistline.is-selected").removeClass("is-selected");

        var line = fleetListManager.fleetRow(ship).closest(".fleetlistline");
        if (!line.length) return;
        line.addClass("is-selected");

        //Restart the flash animation on a row that already carries the class.
        line.removeClass("rowflash");
        void line.get(0).offsetWidth;
        line.addClass("rowflash");

        if ($("#gameinfo").is(":visible") && line.get(0).scrollIntoView) {
            //"nearest" so an already-visible row does not jump, and so the scroll stays
            //inside #fleetListBody rather than moving the page.
            line.get(0).scrollIntoView({ block: "nearest" });
        }
    },

    loadPrefs: function loadPrefs() {
        try {
            var raw = window.localStorage.getItem(fleetListManager.PREF_KEY);
            if (!raw) return;
            var p = JSON.parse(raw);
            fleetListManager.teamFilter = parseInt(p.teamFilter, 10) || 0;
            fleetListManager.onMapOnly = !!p.onMapOnly;
            fleetListManager.sortKey = p.sortKey || null;
            fleetListManager.sortDir = p.sortDir === -1 ? -1 : 1;
        } catch (e) { /* defaults stand */ }
    },

    savePrefs: function savePrefs() {
        try {
            window.localStorage.setItem(fleetListManager.PREF_KEY, JSON.stringify({
                teamFilter: fleetListManager.teamFilter,
                onMapOnly: fleetListManager.onMapOnly,
                sortKey: fleetListManager.sortKey,
                sortDir: fleetListManager.sortDir
            }));
        } catch (e) { /* the choice still applies for this session */ }
    },

    reset: function reset() {
        fleetListManager.initialized = false;
    },

};

/* The INFO tab's control bar (plan Stage 3). Bound once; the rows themselves are
   delegated off #fleetListBody so a rebuild never has to re-bind anything. */
$(function () {
    fleetListManager.loadPrefs();
    fleetListManager.initRowInteractions();

    $("#fleetOnMapOnly").attr("aria-pressed", fleetListManager.onMapOnly ? "true" : "false")
        .on("click", function () {
            fleetListManager.onMapOnly = !fleetListManager.onMapOnly;
            $(this).attr("aria-pressed", fleetListManager.onMapOnly ? "true" : "false");
            fleetListManager.savePrefs();
            fleetListManager.applyView();
        });

    $("#fleetTeamFilter").on("change", function () {
        fleetListManager.teamFilter = parseInt(this.value, 10) || 0;
        fleetListManager.savePrefs();
        fleetListManager.applyView();
    });

    //Recount when the tab is opened: updateFleetList may have run while it was hidden.
    $("#gameinfo").on("onshow", function () { fleetListManager.applyView(); });
});

//Clickable ship names inside confirm/error dialogs (gamedata.onCommitClicked and
//gamedata.doCommit list ships by name; gamedata.shipNameSpan marks each one with
//.clickable + data-shipid, exactly like a fleet list row).
//
//Delegated from `document` rather than bound per dialog: confirm.js builds a fresh
//<div class="confirm"> for every prompt and wipes it with $(".confirm").remove(), so a
//direct binding would have to be re-applied on each dialog and in each of the ~40 places
//that build one. One delegated handler covers every dialog, present and future, and costs
//nothing when no dialog is open. doScrollToShip is reused unmodified - it reads only
//e.currentTarget's .clickable class and data-shipid (jQuery points currentTarget at the
//delegated match), so the confirm dialogs inherit its shouldBeHidden guard and its
//docked-flight branch for free.
$(document).on("click", ".confirm .ship-name.clickable", function (e) {
    var dialog = $(e.currentTarget).closest(".confirm");

    //Close the dialog BEFORE scrolling, not after: PhaseStrategy.setSelectedShip bails out
    //while $(".confirm") is on screen (it stops map clicks leaking through a modal), so the
    //select half of the jump would silently do nothing in the other order. Closing first
    //satisfies that guard rather than working around it.
    //
    //moveCameraTo centres the ship in the viewport, which is exactly where the dialog sits
    //(position:fixed, left/top 50%), so it had to go anyway.
    //
    //Dismiss through the dialog's OWN cancel control rather than removing the node: the
    //movement and firing commit prompts pass a cancelCallback that re-shows the pivot UI
    //(UI.shipMovement.show()), and a bare .remove() would strand that UI hidden.
    //Order matters here too - .confirmok on a commit prompt IS the commit.
    var closer = dialog.find(".confirmcancel, .confirmcanceloption").first();
    if (!closer.length) {
        //error/warning dialogs are acknowledge-only: they carry no cancel, and their ok
        //button is the dismiss (every commit-error callback in gamedata.js is a no-op).
        closer = dialog.find(".confirmok, .confirmokoption").first();
    }

    if (closer.length) {
        closer.trigger("click");
    } else {
        dialog.remove();
    }

    //Safe to read e.currentTarget after the dialog is gone - the span is detached, but its
    //dataset and classList are what doScrollToShip reads, and those survive detachment.
    fleetListManager.doScrollToShip(e, { select: true });
});