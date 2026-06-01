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

//Hangar Ops Stage 21.7 (value follow-up): for a flight that is still in
//space but has some craft docked-out (partial dock) or disengaged, value it
//by its ACTIVE craft count rather than its persisted flightSize. The flight
//keeps flightSize at the full roster (replay/reload-safe — the docked craft's
//state lives on the "- Split" fragment and returns on relaunch), so the raw
//baseValue (pointCost*flightSize/6) over-counts the docked craft, while the
//server combatValue (round(100*active/total)) under-counts because it treats
//docked craft as destroyed in the DENOMINATOR. The two compound: a 3-of-6
//partial-dock remnant rendered 360*0.5 = 180 instead of the 228 a clean
//flight of 3 shows. This recomputes both base and CV over the active roster
//so the in-space remnant reads the same as an equivalent fresh flight.
//
//Returns null when no re-base is needed (not a flight, fully active, or no
//systems) so callers fall through to the existing flightSize/combatValue
//path unchanged for every ordinary flight.
function activeFlightValue(ship) {
    if (!ship || ship.flight !== true || !Array.isArray(ship.systems)) return null;
    //A removed flight (fully docked / jumped with its carrier) isn't in space —
    //it's rendered by the "Docked"/"Jumped" row path and valued elsewhere. Only
    //re-base flights actually on the board (a partial-dock remnant), or we'd zero
    //out a fully-docked flight's row (all its craft carry DockedFighter).
    if (ship.removed) return null;

    var total = 0;       //fighter craft in the roster (matches server craftTotal)
    var active = 0;      //craft still in space (not docked-out / disengaged / dead)
    var cvAccum = 0;     //combat-value weight summed over active craft
    for (var i = 0; i < ship.systems.length; i++) {
        var fighter = ship.systems[i];
        if (!fighter || !fighter.fighter) continue;   //skip non-Fighter entries
        total++;

        if (fighter.destroyed) continue;
        if (shipManager.criticals.hasCritical(fighter, "DockedFighter")) continue;
        if (shipManager.criticals.hasCritical(fighter, "DisengagedFighter")) continue;

        active++;
        //Mirror FighterFlight::calculateCombatValue: >50% damage -> 3/4 value.
        var dmg = damageManager.getDamage(ship, fighter);
        if ((fighter.maxhealth - dmg) * 2 < fighter.maxhealth) {
            cvAccum += 0.75;
        } else {
            cvAccum += 1;
        }
    }

    //Only re-base when craft are actually missing from the in-space roster.
    //A full (or empty) flight falls through to the unchanged default path.
    if (total <= 0 || active >= total) return null;

    var effectiveCV = (active > 0) ? Math.round(100 * (cvAccum / active)) : 0;
    return { activeCraft: active, combatValue: effectiveCV };
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
            //Hangar Ops Stage 21.7: a partial-dock remnant keeps the full
            //flightSize but only some craft are still in space — value it by its
            //active roster (and a CV recomputed over those craft) so it matches a
            //fresh flight of the same active size. Returns null for ordinary
            //flights, so the default flightSize/combatValue path is unchanged.
            var activeVal = activeFlightValue(ship);
            var effectiveCV = (ship.combatValue !== undefined ? ship.combatValue : 100);
            if (ship.flight === true) {
                if (activeVal) {
                    baseValue = (ship.pointCost || 0) * (activeVal.activeCraft / 6);
                    effectiveCV = activeVal.combatValue;
                } else {
                    // Flights have cost calculated per 6 fighters
                    baseValue = (ship.pointCost || 0) * (ship.flightSize / 6);
                }
            }
            baseValue = Math.round(baseValue + (ship.pointCostEnh || 0) + (ship.pointCostEnh2 || 0));
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