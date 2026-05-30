"use strict";

// Stage 7: deployment-phase hangar docking helpers.
//
// During Deployment Phase, a player can "park" a pending fighter flight inside
// a friendly carrier's hangar instead of placing it on the map. This file
// holds the shared lookups used by both DeploymentPhaseStrategy.js (tooltip
// button condition) and confirm.js (dock dialog).
//
// State model:
//   carrierHangar.pendingDeployStartOrders  : [{flightId}, ...]   client-side queue
//   flight.pendingDeployDock                : {carrierId, hangarId} | undefined
//
// The two are kept in sync — toggling a flight in the dialog mutates both,
// so the tooltip eligibility and validateAllDeployment skip-flight logic stay
// consistent. On commit, Hangar.doIndividualNotesTransfer ships the queue as
// {deployStarts: [...]} in the system note transfer payload.

window.DeploymentDock = (function () {

    // True if $ship is one of MY carriers (has at least one hangar) during
    // Deployment Phase AND the carrier itself is deploying THIS turn —
    // fighters arriving on turn N can only dock into ships also arriving on
    // turn N, so a previously-deployed carrier can never accept deploy-start
    // docks. (Stage 7 originally surfaced the dock button on every friendly
    // carrier to support cancelling previously-queued flights, but under the
    // new same-turn-only rule no legitimate queue can exist on a previously-
    // deployed carrier — those carriers are excluded entirely.)
    function shipHasOpenableDockDialog(ship) {
        if (!ship || !Array.isArray(ship.systems)) return false;
        if (!gamedata.isMyShip(ship)) return false;
        if (ship.flight) return false;                                   //flights can't carry flights
        if (gamedata.isTerrain && gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
        if (shipManager.getTurnDeployed(ship) !== gamedata.turn) return false;

        var hangars = collectAllHangars(ship);
        return hangars.length > 0;
    }

    // Stage 16: a Catapult is a dock-capable hangar (name "catapult"). Treat it
    // like a hangar everywhere deploy-dock iterates systems, but its capacity is
    // a flat 1 (it holds one fighter; extra boxes are HP only, and it operates
    // regardless of damage).
    function isDockHangar(sys) {
        //Fighter Rails (name "fighterRail") are dock-capable too; like an ordinary
        //hangar they use box-count capacity and respect their own damage (the
        //isCat branches in the box-cost helpers stay catapult-only).
        return !!(sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail'));
    }
    function effectiveHangarBoxes(hangar) {
        if (!hangar) return 0;
        if (hangar.isCatapult || hangar.name === 'catapult') return 1;
        var netDamage = 0;
        if (Array.isArray(hangar.damage)) {
            hangar.damage.forEach(function (d) {
                netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
            });
        }
        return Math.max(0, parseInt(hangar.maxhealth, 10) - netDamage);
    }

    // Hangar boxes a single craft occupies. A unitSize<1 craft (Vorlon Assault
    // Fighter et al.) needs more than one box each; every other craft is one box.
    // Mirrors HangarOps::boxesPerCraftForClass / boxesPerCraftForEntry (PHP).
    // Duplicated here (not shared with shipTooltipFireMenu) to avoid a load-order
    // dependency, same as the categoryForFlight / customFighterRemainingFor copies.
    function boxesPerCraftFromUnitSize(unitSize) {
        var u = (unitSize != null) ? parseFloat(unitSize) : 1;
        return (u > 0 && u < 1) ? Math.ceil(1 / u) : 1;
    }
    function boxesPerCraftForEntry(entry) {
        if (entry && entry.boxesPerCraft) {
            var b = parseInt(entry.boxesPerCraft, 10);
            return b >= 1 ? b : 1;
        }
        return boxesPerCraftFromUnitSize(entry ? entry.unitSize : 1);
    }
    // Boxes occupied by an entry/flight in $hangar — a catapult is a single-fighter
    // rail (counts craft 1:1); ordinary hangars charge the per-craft box cost.
    function entryBoxesInHangar(hangar, entry) {
        var n = parseInt(entry.flightSize || 1, 10);
        var isCat = !!(hangar && (hangar.isCatapult || hangar.name === 'catapult'));
        return isCat ? n : n * boxesPerCraftForEntry(entry);
    }
    function craftBoxesInHangar(hangar, count, unitSize) {
        var n = parseInt(count || 0, 10);
        var isCat = !!(hangar && (hangar.isCatapult || hangar.name === 'catapult'));
        return isCat ? n : n * boxesPerCraftFromUnitSize(unitSize);
    }

    // All non-destroyed Hangar systems on $ship, regardless of free capacity.
    // Used by shipHasOpenableDockDialog (Issue 7) so the button surfaces even
    // for fully-loaded hangars (the dialog still needs to expose docked-flight
    // un-checking on those).
    function collectAllHangars(ship) {
        var out = [];
        ship.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (shipManager.systems.isDestroyed(ship, sys)) return;
            out.push(sys);
        });
        return out;
    }

    // Friendly hangars on $ship that aren't destroyed and still have free
    // boxes (accounting for current usage AND already-queued deploy starts).
    // $reclaimFlightId (optional): that flight's own reservations are treated as
    // reclaimable (for re-planning an already-queued flight).
    function collectUsableHangars(ship, reclaimFlightId) {
        var out = [];
        ship.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (shipManager.systems.isDestroyed(ship, sys)) return;
            var free = hangarFreeBoxes(sys, reclaimFlightId);
            if (free > 0) out.push({ hangar: sys, free: free });
        });
        return out;
    }

    // Effective free boxes = effective max (maxhealth - net damage)
    //                      - already-stored usage
    //                      - flights queued for deploy-start dock here this session.
    // $reclaimFlightId (optional): treat that flight's OWN existing reservations
    // as reclaimable (not committed) so a re-edit/re-plan of an already-queued
    // flight sees the capacity it had before queuing — mirrors
    // eligibleHangarsForFlight's own-order reclaim.
    function hangarFreeBoxes(hangar, reclaimFlightId) {
        var effective = effectiveHangarBoxes(hangar);
        var reclaim = (reclaimFlightId != null) ? parseInt(reclaimFlightId, 10) : null;

        // Box-aware: unitSize<1 craft consume >1 box each (catapults excepted).
        var used = 0;
        if (Array.isArray(hangar.hangarUsage)) {
            hangar.hangarUsage.forEach(function (e) { used += entryBoxesInHangar(hangar, e); });
        }

        var queued = 0;
        if (Array.isArray(hangar.pendingDeployStartOrders)) {
            hangar.pendingDeployStartOrders.forEach(function (o) {
                if (reclaim != null && parseInt(o.flightId, 10) === reclaim) return;   //own queue is reclaimable
                var f = gamedata.getShip(o.flightId);
                if (!f) return;
                //Auto-distribute orders carry a per-bay `count` slice; legacy
                //single-bay orders reserve the whole flight. Use the slice so a
                //flight spread across bays doesn't over-reserve each one.
                var slice = (o.count != null && parseInt(o.count, 10) > 0)
                    ? parseInt(o.count, 10)
                    : parseInt(f.flightSize || 1, 10);
                queued += craftBoxesInHangar(hangar, slice, f.unitSize);
            });
        }

        return Math.max(0, effective - used - queued);
    }

    // Pending flights belonging to the same slot/player as $carrier that:
    //   - are FighterFlights
    //   - are deploying THIS turn (deployment is still in progress for them)
    //   - aren't already destroyed/removed
    //   - aren't already queued in a DIFFERENT hangar
    //   - are positioned in the same hex as the carrier (visual co-location is
    //     the player's signal that "these belong together"; pulling every same-
    //     slot flight regardless of position is too much noise on big fleets).
    //     Flights already queued for THIS carrier remain visible regardless of
    //     their old position so the player can amend/cancel the queue.
    function collectPendingFlightsForSlot(carrier) {
        var out = [];
        var carrierPos = safeShipPosition(carrier);
        for (var key in gamedata.ships) {
            var s = gamedata.ships[key];
            if (!s || !s.flight) continue;
            if (s.id === carrier.id) continue;
            if (parseInt(s.slot, 10) !== parseInt(carrier.slot, 10)) continue;
            if (parseInt(s.userid, 10) !== parseInt(carrier.userid, 10)) continue;
            if (shipManager.isDestroyed(s)) continue;
            if (s.removed) continue;
            //Only flights whose deployment IS this turn. Future reinforcements
            //(> turn) aren't yet available; past-deployed flights (< turn) are
            //already locked in on the board and can't be docked from here —
            //they go through the Firing-Phase dock path instead.
            if (shipManager.getTurnDeployed(s) !== gamedata.turn) continue;

            //If queued on a DIFFERENT carrier, don't show — player can re-edit there.
            if (s.pendingDeployDock && s.pendingDeployDock.carrierId !== carrier.id) continue;

            //Same-hex check: flight must visually share the carrier's hex (or be
            //already queued on this carrier — in which case its icon is hidden
            //but the queue entry should still be amendable).
            var alreadyQueuedHere = !!(s.pendingDeployDock && s.pendingDeployDock.carrierId === carrier.id);
            if (!alreadyQueuedHere) {
                var flightPos = safeShipPosition(s);
                if (!carrierPos || !flightPos) continue;
                if (carrierPos.q !== flightPos.q || carrierPos.r !== flightPos.r) continue;
            }

            out.push(s);
        }
        return out;
    }

    // shipManager.getShipPosition can throw if the ship has no movement entries.
    // BuyingGamePhase seeds every unit with one, but be defensive — a malformed
    // ship row shouldn't take out the whole dock-eligibility check.
    function safeShipPosition(ship) {
        try { return shipManager.getShipPosition(ship); }
        catch (e) { return null; }
    }

    // Returns the first hangar in $hangars that accepts $flight's size, or
    // null if none fit. $carrier passed so universal hangars use ship.$fighters.
    function firstFittingHangar(hangars, flight, carrier) {
        var cat = categoryForFlight(flight);
        var size = parseInt(flight.flightSize, 10) || 1;
        for (var i = 0; i < hangars.length; i++) {
            var h = hangars[i];
            if (!hangarAcceptsCategory(h.hangar.hangarType, cat, carrier)) continue;
            //h.free is in boxes; a unitSize<1 flight needs size × per-craft boxes.
            if (h.free >= craftBoxesInHangar(h.hangar, size, flight.unitSize)) return h.hangar;
        }
        return null;
    }

    // Mirrors HangarOps::trueSizeOf (PHP) — see shipTooltipFireMenu.js for the
    // canonical copy. Duplicated here so DeploymentDock doesn't depend on
    // shipTooltipFireMenu (which only loads for game.php, not for the inverse
    // load order).
    function categoryForFlight(flight) {
        var req = String(flight.hangarRequired || '').trim();
        var lower = req.toLowerCase();
        if (lower === '' || lower === 'fighters' || lower === 'normal') {
            var jink = parseInt(flight.jinkinglimit || 0, 10);
            if (jink >= 99) return 'ultralight';
            if (jink >= 10) return 'light';
            if (jink >= 8)  return 'medium';
            if (jink >= 6)  return 'heavy';
            return 'medium';
        }
        return req;
    }

    // Mirrors HangarOps::hangarAcceptsCategory (PHP). See shipTooltipFireMenu.js.
    // Universal 'fighters'/'normal' slots derive permissions from ship.$fighters
    // when ship is provided (handles multi-category carriers like Decurion).
    function hangarAcceptsCategory(hangarType, category, ship) {
        var hType = String(hangarType || '').toLowerCase().trim();
        var cat   = String(category   || '').toLowerCase().trim();
        if (hType === '' || cat === '') return false;
        var rank = { ultralight: 1, light: 2, medium: 3, heavy: 4 };

        if (hType === cat) return true;
        if (rank[hType] && rank[cat]) return rank[cat] <= rank[hType];
        if ((cat === 'shuttles' || cat === 'minesweeping shuttles') && rank[hType]) return true;

        //Breaching Pods: AS slot or ANY combat fighter slot.
        if (cat === 'breaching pods') {
            if (hType === 'assault shuttles') return true;
            if (rank[hType]) return true;
        }

        if (hType === 'fighters' || hType === 'normal') {
            if (cat === 'shuttles' || cat === 'minesweeping shuttles') return true;
            if (!ship || !ship.fighters) {
                if (rank[cat]) return true;
                return false;
            }
            var declared = {};
            for (var k in ship.fighters) {
                if (Object.prototype.hasOwnProperty.call(ship.fighters, k)) {
                    declared[String(k).toLowerCase()] = ship.fighters[k];
                }
            }
            if (rank[cat]) {
                if (declared['normal']) return true;
                var sizes = ['heavy', 'medium', 'light', 'ultralight'];
                for (var i = 0; i < sizes.length; i++) {
                    if (!declared[sizes[i]]) continue;
                    if (rank[cat] <= rank[sizes[i]]) return true;
                }
                return false;
            }
            if (cat === 'assault shuttles') return !!declared['assault shuttles'];
            if (cat === 'breaching pods') {
                if (declared['breaching pods']) return true;
                if (declared['assault shuttles']) return true;
                if (declared['normal']) return true;
                if (declared['heavy']) return true;
                if (declared['medium']) return true;
                if (declared['light']) return true;
                if (declared['ultralight']) return true;
                return false;
            }
            return false;
        }
        return false;
    }

    // Greedy auto-distribution of $flight across $carrier's usable hangars/rails,
    // biggest free first. A flight larger than any single bay (e.g. a 9-fighter
    // flight onto a StrikeCarrier whose rails are 6+3+3+3+3+2) spreads across
    // several bays — each gets a {flightId, count} order so the server splits the
    // flight into fragments (mirrors the Firing-Phase dock splitter). Returns the
    // list of hangars used (with their allocated counts), or [] if the carrier's
    // COMBINED free capacity can't hold the whole flight.
    //
    // Box-aware: free is in boxes; a unitSize<1 craft costs >1 box each, so the
    // per-bay craft capacity is floor(free / perCraftBoxes).
    // $reclaimFlightId (optional): re-planning an already-queued flight — its own
    // existing reservations are reclaimed so the plan sees true free capacity.
    function distributeFlightAcrossHangars(carrier, flight, reclaimFlightId) {
        var cat = categoryForFlight(flight);
        var perCraft = craftBoxesInHangar(null, 1, flight.unitSize); //boxes per single craft (catapult-agnostic here; rails/hangars are non-catapult)
        if (perCraft < 1) perCraft = 1;
        var remaining = parseInt(flight.flightSize, 10) || 1;

        var hangars = collectUsableHangars(carrier, reclaimFlightId).filter(function (h) {
            return hangarAcceptsCategory(h.hangar.hangarType, cat, carrier);
        });
        //Biggest free first so we use the fewest bays (and prefer the 6-box rail
        //before the 3-box ones). Ties keep encounter order.
        hangars.sort(function (a, b) { return b.free - a.free; });

        var plan = [];
        for (var i = 0; i < hangars.length && remaining > 0; i++) {
            var h = hangars[i];
            //Catapults count craft 1:1; rails/hangars charge per-craft boxes.
            var isCat = !!(h.hangar.isCatapult || h.hangar.name === 'catapult');
            var craftFit = isCat ? h.free : Math.floor(h.free / perCraft);
            if (craftFit <= 0) continue;
            var take = Math.min(remaining, craftFit);
            plan.push({ hangar: h.hangar, count: take });
            remaining -= take;
        }
        if (remaining > 0) return [];   //combined capacity insufficient
        return plan;
    }

    // Queue $flight for deployment-dock onto $carrier, auto-distributing across
    // its hangars/rails. Stamps $flight.pendingDeployDock so validateAllDeployment
    // skips it. Returns true on success, false if the carrier can't hold the
    // whole flight across all its bays.
    function queueDeployStartDock(carrier, flight) {
        if (!flight || !carrier) return false;
        if (flight.pendingDeployDock) return false;          //already queued somewhere
        var plan = distributeFlightAcrossHangars(carrier, flight);
        if (plan.length === 0) return false;

        var fid = parseInt(flight.id, 10);
        var single = (plan.length === 1);
        plan.forEach(function (slot) {
            if (!Array.isArray(slot.hangar.pendingDeployStartOrders)) slot.hangar.pendingDeployStartOrders = [];
            //Single-bay dock omits count (server treats as the whole flight) so the
            //common one-hangar case behaves exactly as before — no fragmenting.
            var order = { flightId: fid };
            if (!single) order.count = slot.count;
            slot.hangar.pendingDeployStartOrders.push(order);
            slot.hangar.pendingDeployStartOrdersDirty = true;
        });

        flight.pendingDeployDock = {
            carrierId: parseInt(carrier.id, 10),
            hangarId:  parseInt(plan[0].hangar.id, 10)   //informational only (first bay used)
        };
        return true;
    }

    // Remove $flight from any hangar's pendingDeployStartOrders and clear its
    // pendingDeployDock marker. Idempotent — safe to call on flights that
    // aren't currently queued.
    //
    // Issue 8: when un-queueing, snap the flight's deploy position to the host
    // carrier's CURRENT hex (the carrier may have moved during deployment), so
    // the un-docked flight reappears at the carrier's new location instead of
    // its stale pre-dock hex.
    function unqueueDeployStartDock(flight) {
        if (!flight) return;
        var flightId = parseInt(flight.id, 10);

        //Capture the host carrier BEFORE deleting pendingDeployDock — the
        //marker tells us where to re-deploy the flight.
        var carrier = null;
        if (flight.pendingDeployDock && flight.pendingDeployDock.carrierId != null) {
            carrier = gamedata.getShip(flight.pendingDeployDock.carrierId);
        }

        //Walk every ship's hangars; only one should match, but be defensive.
        for (var key in gamedata.ships) {
            var s = gamedata.ships[key];
            if (!s || !Array.isArray(s.systems)) continue;
            s.systems.forEach(function (sys) {
                if (!sys || !isDockHangar(sys)) return;
                if (!Array.isArray(sys.pendingDeployStartOrders)) return;
                var before = sys.pendingDeployStartOrders.length;
                sys.pendingDeployStartOrders = sys.pendingDeployStartOrders.filter(function (o) {
                    return parseInt(o.flightId, 10) !== flightId;
                });
                if (sys.pendingDeployStartOrders.length !== before) {
                    sys.pendingDeployStartOrdersDirty = true;
                }
            });
        }
        delete flight.pendingDeployDock;

        //Re-deploy the flight at the carrier's current hex so it reappears on
        //the map where the carrier is now. shipManager.movement.deploy is
        //idempotent: it either updates ship.deploymove.position or creates a
        //new deploy entry.
        if (carrier && typeof shipManager.movement !== 'undefined'
            && typeof shipManager.movement.deploy === 'function') {
            try {
                var carrierPos = shipManager.getShipPosition(carrier);
                if (carrierPos) shipManager.movement.deploy(flight, carrierPos);
            } catch (e) {
                //fail-soft: a missing position shouldn't break the un-queue
            }
        }
    }

    // Issue 6: auto-dock $flight into $carrier's first compatible hangar without
    // opening the multi-flight dialog. Used by the SelectFromShips DOCK button
    // when the player has explicitly indicated which carrier to send the flight
    // to. Returns the chosen hangar on success, or null if no hangar can hold
    // the flight (caller should keep the deploy option visible in that case).
    function autoQueueDockOnCarrier(carrier, flight) {
        if (!carrier || !flight) return null;
        if (flight.pendingDeployDock) {
            //Already queued somewhere — re-route by un-queueing first so the old
            //hangar releases its reservation, then re-queue here.
            unqueueDeployStartDock(flight);
        }

        //Auto-distribute across the carrier's bays (handles a flight larger than
        //any single rail by spreading it; queueDeployStartDock does the work and
        //sets pendingDeployDock). Returns the first bay used so callers that just
        //need a truthy "it docked" signal keep working.
        if (!queueDeployStartDock(carrier, flight)) return null;
        var hid = flight.pendingDeployDock ? flight.pendingDeployDock.hangarId : null;
        return (hid != null) ? gamedata.getShip(carrier.id).systems.find(function (s) { return s && s.id === hid; }) || null : null;
    }

    // Public wrapper used by confirm.js. Same as collectPendingFlightsForSlot
    // but named to match the dialog's expected API contract.
    function findPendingFlightsForCarrier(carrier) {
        return collectPendingFlightsForSlot(carrier);
    }

    // Returns {hangar, capacity} entries for every hangar on $carrier that
    // accepts $flight's size category AND has enough free boxes to hold the
    // full flight. Used by the dock dialog to render the per-flight hangar
    // dropdown.
    //
    // Free-box accounting treats $flight's own pendingDeployStartOrders entry
    // (if any) as reclaimable — so re-opening the dialog shows the queued
    // hangar without complaining about "no space."
    function eligibleHangarsForFlight(carrier, flight) {
        if (!carrier || !flight || !Array.isArray(carrier.systems)) return [];
        var category = categoryForFlight(flight);
        var size = parseInt(flight.flightSize, 10) || 1;
        var flightId = parseInt(flight.id, 10);

        // Stage 10.6.2: per-ship customFighter cap. Deploy-dock is always a
        // whole-flight commit, so cap < size → flight isn't dockable here.
        var customName = String(flight.customFtrName || '');
        if (customName !== '') {
            var cap = customFighterRemainingFor(carrier, customName, flightId);
            if (cap < size) return [];
        }

        var out = [];
        carrier.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (shipManager.systems.isDestroyed(carrier, sys)) return;
            if (!hangarAcceptsCategory(sys.hangarType, category, carrier)) return;

            //Compute free boxes — but reclaim THIS flight's own queued entry
            //so re-edit doesn't think the hangar is full. (Catapult → 1 slot.)
            var effective = effectiveHangarBoxes(sys);

            //Box-aware usage: unitSize<1 craft consume >1 box each (catapults
            //counted 1:1 via entryBoxesInHangar / craftBoxesInHangar).
            var used = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) { used += entryBoxesInHangar(sys, e); });
            }

            var queued = 0;
            if (Array.isArray(sys.pendingDeployStartOrders)) {
                sys.pendingDeployStartOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === flightId) return;          //own queue is reclaimable
                    var f = gamedata.getShip(o.flightId);
                    if (f) queued += craftBoxesInHangar(sys, f.flightSize, f.unitSize);
                });
            }

            var free = Math.max(0, effective - used - queued);
            if (free >= craftBoxesInHangar(sys, size, flight.unitSize)) out.push({ hangar: sys, capacity: free });
        });
        return out;
    }

    // Mirrors HangarOps::customFighterRemaining (PHP). Per-CARRIER count of
    // remaining custom-named slots. Same shape as the shipTooltipFireMenu
    // helpers — duplicated so DeploymentDock has no load-order dependency on
    // shipTooltipFireMenu (game.php-only).
    //
    // Deployment-phase uses pendingDeployStartOrders instead of pendingDockOrders.
    function customFighterRemainingFor(carrier, name, ownFlightId) {
        if (!name) return Infinity;
        if (!carrier.customFighter || !carrier.customFighter[name]) return 0;
        var declared = parseInt(carrier.customFighter[name], 10);
        var used = 0;
        carrier.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) {
                    if (e.customFtrName !== name) return;
                    used += parseInt(e.flightSize || 1, 10);
                });
            }
            if (Array.isArray(sys.pendingDeployStartOrders)) {
                sys.pendingDeployStartOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === ownFlightId) return;
                    var f = gamedata.getShip(o.flightId);
                    if (!f || String(f.customFtrName || '') !== name) return;
                    used += parseInt(f.flightSize || 1, 10);
                });
            }
        });
        return Math.max(0, declared - used);
    }

    return {
        shipHasOpenableDockDialog:    shipHasOpenableDockDialog,
        collectUsableHangars:         collectUsableHangars,
        collectAllHangars:            collectAllHangars,
        collectPendingFlightsForSlot: collectPendingFlightsForSlot,
        findPendingFlightsForCarrier: findPendingFlightsForCarrier,
        eligibleHangarsForFlight:     eligibleHangarsForFlight,
        firstFittingHangar:           firstFittingHangar,
        queueDeployStartDock:         queueDeployStartDock,
        autoQueueDockOnCarrier:       autoQueueDockOnCarrier,
        unqueueDeployStartDock:       unqueueDeployStartDock,
        hangarFreeBoxes:              hangarFreeBoxes,
        distributeFlightAcrossHangars: distributeFlightAcrossHangars
    };
})();

// Refresh after the dock dialog commits:
//   1. Re-check the deployment commit gate — a flight newly queued for dock no
//      longer needs a position; one un-queued does.
//   2. Force the icon container to re-evaluate visibility so docked flights
//      hide and un-docked flights reappear immediately (without this, the
//      IdleAnimationStrategy.update only fires on consumeGamedata events, so
//      icons stay stale until the next gamedata refresh).
//   3. Refresh each touched hangar's tooltip so the "Carrying" / "Stored Craft"
//      lines show the queued flights right away.
// Switch the currently-selected ship in the active phase strategy. Used after
// a successful dock so the player isn't left holding a now-invisible flight as
// the selectedShip — instead, focus moves to the carrier so the next action
// (move it, dock more flights, end deployment) reads naturally.
window.selectShipInDeploymentPhase = function (ship) {
    if (!ship) return;
    try {
        var ps = window.webglScene && window.webglScene.phaseDirector
            ? window.webglScene.phaseDirector.phaseStrategy
            : null;
        if (ps && typeof ps.setSelectedShip === 'function') {
            //setSelectedShip already calls deselectShip on the prior selectedShip
            //via the base PhaseStrategy implementation.
            ps.setSelectedShip(ship);
        }
    } catch (e) { /* fail-soft */ }
};

window.refreshDeploymentUIForDeployStart = function () {
    //Step 1: re-evaluate commit-button readiness via the existing global helper.
    if (typeof window.validateAllDeploymentGlobal === 'function'
        && typeof gamedata !== 'undefined'
        && typeof gamedata.showCommitButton === 'function') {
        try {
            //An empty array makes per-slot bounding-box checks fail-soft; that's
            //fine because flights newly queued for dock are SKIPPED in the
            //validator (see DeploymentPhaseStrategy.validateAllDeployment).
            if (window.validateAllDeploymentGlobal(gamedata, window._deploymentSprites || [])) {
                gamedata.showCommitButton();
            }
        } catch (e) {
            //fail-soft: a stale gamedata reference shouldn't break the dialog flow
        }
    }

    //Step 2: nudge the phase strategy to re-run consumeGamedata so the
    //IdleAnimationStrategy hides newly-docked flight icons (shouldBeHidden now
    //returns true) and shows un-docked ones again. Without this the icons
    //stay visually stale until the next server reload.
    try {
        var ps = window.webglScene && window.webglScene.phaseDirector
            ? window.webglScene.phaseDirector.phaseStrategy
            : null;
        if (ps && typeof ps.consumeGamedata === 'function') {
            ps.consumeGamedata();
        }
        //Issue 3: any currently-open systemInfo (e.g. the hangar tooltip)
        //is mounted with stale props because React class components don't
        //re-render on data mutation. Unmount it here so the next hover
        //re-mounts with the live system.data. Cheaper than rebuilding the
        //tree manually.
        if (ps && typeof ps.hideSystemInfo === 'function') {
            ps.hideSystemInfo(true);
        }
    } catch (e) {
        //fail-soft: an inactive renderer shouldn't break the dialog flow
    }

    //Step 3: refresh hangar tooltips so the carrier's system info shows the
    //newly-queued/cancelled flights in the Carrying / Stored Craft lines.
    try {
        for (var key in gamedata.ships) {
            var s = gamedata.ships[key];
            if (!s || !Array.isArray(s.systems)) continue;
            s.systems.forEach(function (sys) {
                //isDockHangar lives inside the IIFE scope above and is NOT visible
                //here, so inline the check (Stage 16: a catapult is a dock hangar too;
                //Fighter Rails likewise have a refreshable hangar tooltip).
                if (sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail') && typeof sys.refreshHangarTooltip === 'function') {
                    sys.refreshHangarTooltip();
                }
            });
        }
    } catch (e) {
        //fail-soft: tooltip refresh is cosmetic
    }
};

// Stage 10.2: sibling helper for the Firing-Phase dialogs (hangarLaunch,
// hangarDock, hangarRecover). Same shape as refreshDeploymentUIForDeployStart
// but skips the deployment-specific commit-gate re-check and the
// consumeGamedata nudge (flight icons don't hide/unhide on queued firing
// orders — orders only resolve at end of turn). Keeps the systemInfo
// unmount so an open hangar tooltip re-mounts on next hover with the
// projected pendingDockOrders / pendingLaunchOrders from refreshHangarTooltip.
window.refreshFiringHangarTooltips = function () {
    //Step 1: unmount any currently-open systemInfo so the next hover
    //re-mounts with the live (now-projected) system.data. Mirrors the
    //hideSystemInfo step in refreshDeploymentUIForDeployStart.
    try {
        var ps = window.webglScene && window.webglScene.phaseDirector
            ? window.webglScene.phaseDirector.phaseStrategy
            : null;
        if (ps && typeof ps.hideSystemInfo === 'function') {
            ps.hideSystemInfo(true);
        }
    } catch (e) {
        //fail-soft: an inactive renderer shouldn't break the dialog flow
    }

    //Step 2: walk every hangar in the game and refresh its tooltip data.
    //Sledgehammer-but-cheap: each refresh is just a couple of array walks
    //over already-in-memory state. Refreshing all ships keeps the helper
    //phase-agnostic and avoids the dialogs having to enumerate which
    //carriers they touched.
    try {
        for (var key in gamedata.ships) {
            var s = gamedata.ships[key];
            if (!s || !Array.isArray(s.systems)) continue;
            s.systems.forEach(function (sys) {
                //isDockHangar lives inside the IIFE scope above and is NOT visible
                //here, so inline the check (Stage 16: a catapult is a dock hangar too;
                //Fighter Rails likewise have a refreshable hangar tooltip).
                if (sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail') && typeof sys.refreshHangarTooltip === 'function') {
                    sys.refreshHangarTooltip();
                }
            });
        }
    } catch (e) {
        //fail-soft: tooltip refresh is cosmetic
    }
};
