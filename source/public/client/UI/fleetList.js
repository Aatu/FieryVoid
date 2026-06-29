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

    prepare: function prepare() { },

    displayFleetLists: function displayFleetLists() {
        if (!fleetListManager.initialized) {
            $("#gameinfo .fleetlistentry").remove();
            const template = $("#logcontainer .fleetlistentry");

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

    createFleetList: function createFleetList(slot, template) {
        var shipArray = new Array();

        // Clone the template and append to gameinfo
        var fleetlistentry = template.clone(true).appendTo("#gameinfo");

        // CHANGED: Use a unique class based on slot ID instead of just playerid (to avoid DOM selector collisions)
        fleetlistentry.addClass("slot_" + slot.slot);

        var teamName = "TEAM " + slot.team;

        // Set the fleet list header
        fleetlistentry.find(".fleetheader").html(
            "<span class='headername'>" + teamName + " - </span><span class='playername'>" + slot.playername + "</span>"
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

        // Create and append the header row
        fleetlistline.html("<span><span class='shipname header'>Ship Name</span><span class='shipclass header'>Ship Class</span><span class='shiptype header'>Type</span><span class='initiative header'>Initiative</span><span class='value header'>Current Value</span></span>");
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

            fleetlistline.html(
                "<span id='" + ship.id + "'>" +
                "<span class='shipname clickable' data-shipid='" + ship.id + "'>" + ship.name + "</span>" +
                "<span class='shipclass'>" + ship.shipClass + "</span>" +
                "<span class='shiptype'>" + shiptype + "</span>" +
                "<span class='initiative'>" + shipManager.getIniativeOrder(ship) + "</span>" +
                "<span class='value'>" + currValue + '/' + baseValue + "CP</span>" +
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

        var turnTaken = "<span style='color:orange'>&nbsp;&nbsp;[Waiting for " + phaseLabel + " Orders]</span>";

        if (slot.surrendered !== null) {
            if (slot.surrendered <= gamedata.turn) { //Surrendered on this turn or before.
                turnTaken = "<span style='color:red'>&nbsp;&nbsp;[Surrendered on Turn " + slot.surrendered + "]</span>"; //Check surrendered first.
            }
        } else if (slot.waiting) {
            turnTaken = "<span style='color:green;'>&nbsp;&nbsp;[Orders committed]</span>";
        }

        var deploys = "";
        if (slot.depavailable > gamedata.turn) deploys = "<span style='color: #00b8e6'>[Deploys on Turn " + slot.depavailable + "]&nbsp;</span>";

        // Update fleet header with value totals
        fleetlistentry.find(".fleetheader").html(
            deploys + "<span class='headername'>" + teamName + " - </span>" +
            "<span class='playername'>" + slot.playername +
            ": " + totalCurrValue + " / " + totalBaseValue + " CP" +
            "<span class='turnTaken'>" + turnTaken + "</span>"
        );

        // Add ship click handler
        $(".clickable", fleetlistentry).on("click", fleetListManager.doScrollToShip);
    },


    updateTurnTakenInFleetHeader: function updateTurnTakenInFleetHeader(slot) {
        const container = $(".slot_" + slot.slot); // Target the correct fleet list block
        const header = container.find(".fleetheader .turnTaken");

        if (!header.length) return; // Just in case something went wrong

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

        var html = "<span style='color:orange'>&nbsp;&nbsp;[Waiting for " + phaseLabel + " Orders]</span>";

        if (slot.surrendered !== null && slot.surrendered <= gamedata.turn) {
            html = "<span style='color:red'>&nbsp;&nbsp;[Surrendered on Turn " + slot.surrendered + "]</span>";
        } else if (slot.waiting) {
            html = "<span style='color:green'>&nbsp;&nbsp;[Orders committed]</span>";
        }

        header.html(html);
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


    doScrollToShip: function doScrollToShip(e) {
        e.stopPropagation();
        var shipNameEntry = e.currentTarget;

        if (!shipNameEntry.classList.contains("clickable")) {
            return;
        }

        var shipId = shipNameEntry.dataset["shipid"];
        var ship = gamedata.getShip(shipId);

        if (shipManager.shouldBeHidden(ship)) { //Enemy, stealth equipped and undetected, or not deployed yet.
            return; //Do not scroll to Stealthed ships
        }

        //Hangar Ops Stage 9.1: a docked flight isn't on the board, so a
        //scroll-to-ship event has nothing to find. Open its status window
        //directly instead so the player can inspect the docked fighters
        //(DOCKED label in cyan over the faded icons).
        if (ship.removed && ship.flight) {
            if (typeof flightWindowManager !== 'undefined' && flightWindowManager.open) {
                flightWindowManager.open(ship);
            }
            return;
        }

        window.webglScene.customEvent('ScrollToShip', { shipId: shipId });
    },

    //Hangar Ops: ids of docked flights whose carrier jumped to hyperspace. A
    //jumped carrier stays in gamedata.ships and keeps its hangarUsage (and the
    //dockedFlightId links) intact, so we map each jumped carrier's stored craft
    //back to the flight rows the fleet list renders. updateFleetList uses this
    //to show those flights as "Jumped" (orange) rather than "Docked" (blue) —
    //a docked flight has no jump engine of its own, so hasJumpedNotDestroyed
    //can't detect this on the flight directly.
    getJumpedDockedFlightIds: function getJumpedDockedFlightIds() {
        var ids = {};
        for (var i in gamedata.ships) {
            var carrier = gamedata.ships[i];
            //hasJumpedNotDestroyed only distinguishes "jumped" from
            //"damage-killed" among ships already out of play: on a healthy,
            //in-play carrier it returns true purely because it has a jump
            //engine and little non-jump damage. Gate on isDestroyed first —
            //the same pairing updateFleetList uses for a ship's own row — so
            //we only flag flights whose carrier actually left via hyperspace.
            if (!shipManager.isDestroyed(carrier)) continue;
            if (!shipManager.hasJumpedNotDestroyed(carrier)) continue;
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

    updateFleetList: function updateFleetList() {
        //Hangar Ops: collect the docked flights whose carrier jumped to
        //hyperspace once, before the row loop, so we can flag them below.
        var jumpedDockedFlightIds = fleetListManager.getJumpedDockedFlightIds();

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var name = ship.name;
            if (shipManager.isDestroyed(ship)) {
                if (ship.removed) {
                    //Docked flight: same isDestroyed=true filtering, but not
                    //actually destroyed. Keep .clickable so the player can
                    //open the flight window (doScrollToShip routes removed
                    //flights to flightWindowManager.open directly since
                    //they're not on the board).
                    if (jumpedDockedFlightIds[ship.id]) {
                        //Carrier jumped to hyperspace and took the flight with
                        //it: it kept its combat value but is no longer in play,
                        //so render it like a jumped ship (orange) not docked.
                        $("#" + ship.id).addClass("jumped");
                        $("#" + ship.id + " .initiative").html("Jumped");
                    } else {
                        $("#" + ship.id).addClass("docked");
                        $("#" + ship.id + " .initiative").html("Docked");
                    }
                    continue;
                }
                // Remove action listener and make everything italic to indicate the
                // ship was destroyed.
                $("#" + ship.id + " .shipname").removeClass("clickable");
                if (shipManager.hasJumpedNotDestroyed(ship)) {
                    $("#" + ship.id).addClass("jumped");
                    $("#" + ship.id + " .initiative").html("Jumped");
                } else {
                    $("#" + ship.id).addClass("destroyed");
                    $("#" + ship.id + " .initiative").html("Destroyed");
                }
            }
        }
    },

    reset: function reset() {
        fleetListManager.initialized = false;
    },

};