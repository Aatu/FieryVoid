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
    // Deployment Phase. Stage 7 deliberately surfaces the dock button on every
    // friendly carrier so the player can also UN-DOCK previously-queued flights
    // even when no eligible same-hex flight exists. The dialog itself shows a
    // "no operations available" message when there's nothing to queue or
    // cancel.
    function shipHasOpenableDockDialog(ship) {
        if (!ship || !Array.isArray(ship.systems)) return false;
        if (!gamedata.isMyShip(ship)) return false;
        if (ship.flight) return false;                                   //flights can't carry flights
        if (gamedata.isTerrain && gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;

        var hangars = collectAllHangars(ship);
        return hangars.length > 0;
    }

    // All non-destroyed Hangar systems on $ship, regardless of free capacity.
    // Used by shipHasOpenableDockDialog (Issue 7) so the button surfaces even
    // for fully-loaded hangars (the dialog still needs to expose docked-flight
    // un-checking on those).
    function collectAllHangars(ship) {
        var out = [];
        ship.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (shipManager.systems.isDestroyed(ship, sys)) return;
            out.push(sys);
        });
        return out;
    }

    // Friendly hangars on $ship that aren't destroyed and still have free
    // boxes (accounting for current usage AND already-queued deploy starts).
    function collectUsableHangars(ship) {
        var out = [];
        ship.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (shipManager.systems.isDestroyed(ship, sys)) return;
            var free = hangarFreeBoxes(sys);
            if (free > 0) out.push({ hangar: sys, free: free });
        });
        return out;
    }

    // Effective free boxes = effective max (maxhealth - net damage)
    //                      - already-stored usage
    //                      - flights queued for deploy-start dock here this session.
    function hangarFreeBoxes(hangar) {
        var netDamage = 0;
        if (Array.isArray(hangar.damage)) {
            hangar.damage.forEach(function (d) {
                netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
            });
        }
        var effective = Math.max(0, parseInt(hangar.maxhealth, 10) - netDamage);

        var used = 0;
        if (Array.isArray(hangar.hangarUsage)) {
            hangar.hangarUsage.forEach(function (e) { used += parseInt(e.flightSize || 1, 10); });
        }

        var queued = 0;
        if (Array.isArray(hangar.pendingDeployStartOrders)) {
            hangar.pendingDeployStartOrders.forEach(function (o) {
                var f = gamedata.getShip(o.flightId);
                if (f) queued += parseInt(f.flightSize || 1, 10);
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
    // null if none fit.
    function firstFittingHangar(hangars, flight) {
        var cat = categoryForFlight(flight);
        var size = parseInt(flight.flightSize, 10) || 1;
        for (var i = 0; i < hangars.length; i++) {
            var h = hangars[i];
            if (!hangarAcceptsCategory(h.hangar.hangarType, cat)) continue;
            if (h.free >= size) return h.hangar;
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
    function hangarAcceptsCategory(hangarType, category) {
        var hType = String(hangarType || '').toLowerCase().trim();
        var cat   = String(category   || '').toLowerCase().trim();
        if (hType === '' || cat === '') return false;
        if (hType === 'fighters' || hType === 'normal') return true;
        if (hType === cat) return true;
        var rank = { ultralight: 1, light: 2, medium: 3, heavy: 4 };
        if (rank[hType] && rank[cat]) return rank[cat] <= rank[hType];
        if ((cat === 'shuttles' || cat === 'minesweeping shuttles') && rank[hType]) return true;
        if (hType === 'assault shuttles' && cat === 'breaching pods') return true;
        return false;
    }

    // Add $flight to $carrier's first-fitting hangar's pendingDeployStartOrders,
    // and stamp $flight.pendingDeployDock so validateAllDeployment skips it.
    // Returns true on success, false if no hangar can hold the flight.
    function queueDeployStartDock(carrier, flight) {
        if (!flight || !carrier) return false;
        if (flight.pendingDeployDock) return false;          //already queued somewhere
        var hangars = collectUsableHangars(carrier);
        var hangar = firstFittingHangar(hangars, flight);
        if (!hangar) return false;

        if (!Array.isArray(hangar.pendingDeployStartOrders)) hangar.pendingDeployStartOrders = [];
        hangar.pendingDeployStartOrders.push({ flightId: parseInt(flight.id, 10) });
        hangar.pendingDeployStartOrdersDirty = true;

        flight.pendingDeployDock = {
            carrierId: parseInt(carrier.id, 10),
            hangarId:  parseInt(hangar.id, 10)
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
                if (!sys || sys.name !== 'hangar') return;
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

        //eligibleHangarsForFlight already filters by category + free space and
        //treats THIS flight's own queue as reclaimable. Pick the first match.
        var eligible = eligibleHangarsForFlight(carrier, flight);
        if (eligible.length === 0) return null;
        var hangar = eligible[0].hangar;

        if (!Array.isArray(hangar.pendingDeployStartOrders)) hangar.pendingDeployStartOrders = [];
        hangar.pendingDeployStartOrders.push({ flightId: parseInt(flight.id, 10) });
        hangar.pendingDeployStartOrdersDirty = true;
        flight.pendingDeployDock = {
            carrierId: parseInt(carrier.id, 10),
            hangarId:  parseInt(hangar.id, 10)
        };
        return hangar;
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

        var out = [];
        carrier.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (shipManager.systems.isDestroyed(carrier, sys)) return;
            if (!hangarAcceptsCategory(sys.hangarType, category)) return;

            //Compute free boxes — but reclaim THIS flight's own queued entry
            //so re-edit doesn't think the hangar is full.
            var netDamage = 0;
            if (Array.isArray(sys.damage)) {
                sys.damage.forEach(function (d) {
                    netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
                });
            }
            var effective = Math.max(0, parseInt(sys.maxhealth, 10) - netDamage);

            var used = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) { used += parseInt(e.flightSize || 1, 10); });
            }

            var queued = 0;
            if (Array.isArray(sys.pendingDeployStartOrders)) {
                sys.pendingDeployStartOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === flightId) return;          //own queue is reclaimable
                    var f = gamedata.getShip(o.flightId);
                    if (f) queued += parseInt(f.flightSize || 1, 10);
                });
            }

            var free = Math.max(0, effective - used - queued);
            if (free >= size) out.push({ hangar: sys, capacity: free });
        });
        return out;
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
        hangarFreeBoxes:              hangarFreeBoxes
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
                if (sys && sys.name === 'hangar' && typeof sys.refreshHangarTooltip === 'function') {
                    sys.refreshHangarTooltip();
                }
            });
        }
    } catch (e) {
        //fail-soft: tooltip refresh is cosmetic
    }
};
