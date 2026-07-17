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
        if (hangars.length > 0) return true;

        //LCV Rails: a carrier with ONLY LCV rails (no fighter hangar) still needs
        //the dialog when it holds a deploy-docked LCV, so the player can release it
        //without a page refresh (Issue 1). Surface the button on that case too.
        return shipHasDeployDockedLcv(ship);
    }

    // True if any LCV rail on $ship currently holds a pending deploy-dock LCV.
    function shipHasDeployDockedLcv(ship) {
        if (!ship || !Array.isArray(ship.systems)) return false;
        return ship.systems.some(function (sys) {
            return isLCVRailSys(sys)
                && Array.isArray(sys.pendingLcvDeployStartOrders)
                && sys.pendingLcvDeployStartOrders.length > 0;
        });
    }

    // Shared pure helpers — the single client mirror of the HangarOps PHP gates.
    // Bodies live in hangarShared.js (loaded before this file in game.php);
    // aliased at script-evaluation time so the rest of this file reads unchanged.
    // (Stage 16: isDockHangar treats a Catapult as a dock-capable hangar with a
    // flat capacity of 1; Fighter Rails use ordinary box-count capacity.)
    var HS = window.HangarShared;
    var isDockHangar              = HS.isDockHangar;
    var effectiveHangarBoxes      = HS.effectiveHangarBoxes;
    var entryBoxesInHangar        = HS.entryBoxesInHangar;
    var craftBoxesInHangar        = HS.craftBoxesInHangar;
    var categoryForFlight         = HS.categoryForFlight;
    var hangarAcceptsFighterClass = HS.hangarAcceptsFighterClass;
    var bayReservesFighterClass   = HS.bayReservesFighterClass;
    var bayReservesFlight         = HS.bayReservesFlight;
    var hangarAcceptsCategory     = HS.hangarAcceptsCategory;
    var isCustomCombatCategory    = HS.isCustomCombatCategory;

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

        // Box-aware: unitSize<1 craft consume >1 box each; unitSize>1 ultralights
        // consume a fractional box each (catapults excepted). Sum committed + queued
        // box cost fractionally and round the TOTAL up so the free count is whole
        // boxes (mirrors HangarOps::occupiedBoxes).
        var used = 0;
        if (Array.isArray(hangar.hangarUsage)) {
            hangar.hangarUsage.forEach(function (e) { used += entryBoxesInHangar(hangar, e); });
        }

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
                used += craftBoxesInHangar(hangar, slice, f.unitSize);
            });
        }

        return Math.max(0, effective - Math.ceil(used));
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
    // (Named for the dialog's API contract — confirm.js calls this to build the
    // deploy-dock row list.)
    function findPendingFlightsForCarrier(carrier) {
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

    // Greedy auto-distribution of $flight across $carrier's usable hangars/rails,
    // biggest free first. A flight larger than any single bay (e.g. a 9-fighter
    // flight onto a StrikeCarrier whose rails are 6+3+3+3+3+2) spreads across
    // several bays — each gets a {flightId, count} order so the server splits the
    // flight into fragments (mirrors the Firing-Phase dock splitter). Returns the
    // list of hangars used (with their allocated counts), or [] if the carrier's
    // COMBINED free capacity can't hold the whole flight.
    //
    // Box-aware: free is in boxes; a unitSize<1 craft costs >1 box each and a
    // unitSize>1 ultralight costs a FRACTIONAL box each (0.5), so the per-bay craft
    // capacity is floor(free / perCraftBoxes) — which is 2 craft per box for Zorth.
    // $reclaimFlightId (optional): re-planning an already-queued flight — its own
    // existing reservations are reclaimed so the plan sees true free capacity.
    function distributeFlightAcrossHangars(carrier, flight, reclaimFlightId) {
        var cat = categoryForFlight(flight);
        var perCraft = craftBoxesInHangar(null, 1, flight.unitSize); //boxes per single craft (catapult-agnostic here; rails/hangars are non-catapult)
        if (perCraft <= 0) perCraft = 1;   //don't clamp to >=1: ultralights are 0.5 box/craft
        var remaining = parseInt(flight.flightSize, 10) || 1;

        //Per-carrier custom combat category cap (e.g. Hunter-Killers). The bay pools
        //HK with light fighters by boxes, but ship.fighters caps HK count — so a
        //deploy-dock can't place more HKs than the carrier's declared HK capacity
        //minus what's already docked/queued. Whole-flight docks: if the cap can't
        //hold the whole flight, it isn't dockable at all (returns [] below).
        var catCap = categoryCapRemainingFor(carrier, cat, parseInt(flight.id, 10));
        if (catCap < remaining) return [];   //carrier can't hold this whole flight within its category cap

        //Stage 10.6.2: per-carrier customFighter cap (Thunderbolt, Rutarian, Ok-chn).
        //eligibleHangarsForFlight already gates this, but THIS packer is the actual
        //queue path for every deploy-dock button (same lesson as the HK cap above:
        //a dock constraint must live in the shared packer, not just the dialog
        //dropdown) — without it a Rutarian could deploy-dock into any Primus whose
        //bay fits it by size, and the server would then reject the commit.
        var customName = String(flight.customFtrName || '');
        if (customName !== '') {
            var ftrCap = customFighterRemainingFor(carrier, customName, parseInt(flight.id, 10));
            if (ftrCap < remaining) return [];   //carrier isn't outfitted for this custom fighter
        }

        var hangars = collectUsableHangars(carrier, reclaimFlightId).filter(function (h) {
            //Stage S: integrated-fighter bays accept only their own integrated
            //fighters (see eligibleHangarsForFlight — same gate).
            if (h.hangar.isShadowHangar && String(flight.phpclass) !== 'ShadowMediumFighterFlight') return false;
            return hangarAcceptsCategory(h.hangar.hangarType, cat, carrier) &&
                   hangarAcceptsFighterClass(h.hangar, flight);   //per-bay class allow-list
        });
        //Fill order: bays SPECIFICALLY reserved for this flight first — by phpclass
        //allow-list OR exact hangarType match (bayReservesFlight) — so a Reska fills
        //the Suom's Reska-only bay and an ultralight fills the Qoricc's dedicated
        //'ultralight' bay before spilling into the universal primary; THEN biggest
        //free first (fewest bays — prefer the 6-box rail before the 3-box ones).
        //Ties keep encounter order.
        hangars.sort(function (a, b) {
            var ra = bayReservesFlight(a.hangar, flight) ? 1 : 0;
            var rb = bayReservesFlight(b.hangar, flight) ? 1 : 0;
            if (ra !== rb) return rb - ra;        //reserved bays first
            return b.free - a.free;
        });

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

        // Per-carrier custom combat category cap (e.g. Hunter-Killers). The 37-box
        // bay pools HK with light fighters, but ship.fighters caps HK count — once
        // the declared HK slots are filled, further HK flights aren't dockable here.
        var catCap = categoryCapRemainingFor(carrier, category, flightId);
        if (catCap < size) return [];

        var out = [];
        carrier.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (shipManager.systems.isDestroyed(carrier, sys)) return;
            //Stage S: an integrated-fighter bay (ShadowHangar keeps name 'hangar')
            //accepts ONLY its own integrated fighters — a foreign flight deploy-
            //docked there could never launch (integrated bays launch only via the
            //Fighter Bomb). Mirrors the firing-phase dock gate.
            if (sys.isShadowHangar && String(flight.phpclass) !== 'ShadowMediumFighterFlight') return;
            if (!hangarAcceptsCategory(sys.hangarType, category, carrier)) return;
            if (!hangarAcceptsFighterClass(sys, flight)) return;   //per-bay class allow-list

            //Free boxes with THIS flight's own queued reservations reclaimed so
            //re-edit doesn't think the hangar is full. (Catapult → 1 slot.)
            //hangarFreeBoxes charges other flights' multi-bay orders at their
            //per-bay `count` slice — the old inline copy charged full flightSize
            //per bay, over-reserving sibling bays after an auto-distribute.
            var free = hangarFreeBoxes(sys, flightId);
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
                    //Multi-bay orders carry a per-bay count; single-bay orders omit it
                    //(the whole flight rides one bay). Use count when present so a flight
                    //spread across sibling bays isn't counted once per bay — same fix
                    //categoryCapRemainingFor already carries.
                    used += parseInt(o.count || f.flightSize || 1, 10);
                });
            }
        });
        return Math.max(0, declared - used);
    }

    // Mirrors HangarOps::categoryCapRemaining (PHP). Per-CARRIER cap for a custom
    // combat category (Hunter-Killers): ship.fighters[category] minus what's docked
    // (stored entries stamped hangarType=category) and pending across all bays.
    // Infinity for shared (size/shuttle) categories — no per-category gate there.
    function categoryCapRemainingFor(carrier, category, ownFlightId) {
        if (!isCustomCombatCategory(category)) return Infinity;
        var cat = String(category).toLowerCase().trim();
        var declared = 0;
        if (carrier.fighters) {
            for (var k in carrier.fighters) {
                if (String(k).toLowerCase().trim() === cat) { declared = parseInt(carrier.fighters[k], 10) || 0; break; }
            }
        }
        if (declared <= 0) return 0;
        var used = 0;
        carrier.systems.forEach(function (sys) {
            if (!sys || !isDockHangar(sys)) return;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) {
                    if (String(e.hangarType || '').toLowerCase().trim() !== cat) return;
                    used += parseInt(e.flightSize || 1, 10);
                });
            }
            if (Array.isArray(sys.pendingDeployStartOrders)) {
                sys.pendingDeployStartOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === ownFlightId) return;
                    var f = gamedata.getShip(o.flightId);
                    if (!f || categoryForFlight(f).toLowerCase().trim() !== cat) return;
                    //Multi-bay orders carry a per-bay count; single-bay orders omit it
                    //(the whole flight rides one bay). Use count when present so a flight
                    //spread across sibling bays isn't counted once per bay.
                    used += parseInt(o.count || f.flightSize || 1, 10);
                });
            }
        });
        return Math.max(0, declared - used);
    }

    // === LCV Rails (DockingCollar) — deployment-phase dock ===
    //
    // LCVs can't share a deploy hex with other ships, so they can't be placed on
    // the board next to a carrier and then dock. Instead they deploy-dock directly
    // onto a free LCV rail. State model parallels the fighter deploy-dock:
    //   rail.pendingLcvDeployStartOrders : [{shipId}]   client-side queue
    //   lcv.pendingLcvDeployDock         : {carrierId, railId} | undefined
    // On commit, DockingCollar.doIndividualNotesTransfer ships the queue as
    // {lcvDeployStarts:[{shipId}]}; the server marks the LCV removed + writes the
    // rail's lcvDocked note (HangarOps::processLcvDeployStartTransfer).

    function isLCVRailSys(sys) {
        return !!(sys && (sys.isLCVRail || sys.name === 'dockingCollar'));
    }

    // A rail is free for a DEPLOY dock if it isn't destroyed, holds no docked LCV,
    // and has no pending deploy-start order already (one LCV per rail). The Firing-
    // phase pendingLcvDockOrders aren't relevant in Deployment (different turn).
    function lcvRailFreeForDeploy(carrier, rail) {
        if (!isLCVRailSys(rail)) return false;
        if (shipManager.systems.isDestroyed(carrier, rail)) return false;
        if (rail.lcvDocked && rail.lcvDocked.shipId) return false;
        if (Array.isArray(rail.pendingLcvDeployStartOrders) && rail.pendingLcvDeployStartOrders.length > 0) return false;
        return true;
    }

    // Free LCV rails on $carrier available for a deploy dock (excluding $reclaimLcvId's
    // own queued rail so a re-open/re-route sees its own reservation as free).
    function freeLcvDeployRails(carrier, reclaimLcvId) {
        if (!carrier || !Array.isArray(carrier.systems)) return [];
        return carrier.systems.filter(function (s) {
            if (!isLCVRailSys(s)) return false;
            if (shipManager.systems.isDestroyed(carrier, s)) return false;
            if (s.lcvDocked && s.lcvDocked.shipId) return false;
            if (Array.isArray(s.pendingLcvDeployStartOrders)) {
                var blocked = s.pendingLcvDeployStartOrders.some(function (o) {
                    return parseInt(o.shipId, 10) !== parseInt(reclaimLcvId, 10);
                });
                if (blocked) return false;
            }
            return true;
        });
    }

    // True if $carrier can accept $lcv as a deploy-start dock: $carrier is one of
    // my ships deploying THIS turn (LCVs deploy-dock onto same-turn carriers, like
    // fighters) and has a free LCV rail. $lcv must be an LCV unit.
    function carrierAcceptsLcvDeployDock(carrier, lcv) {
        if (!carrier || !lcv) return false;
        if (!gamedata.isMyShip(carrier)) return false;
        if (carrier.flight) return false;
        if (String(lcv.hangarRequired || '').toLowerCase() !== 'lcvs') return false;
        if (gamedata.isTerrain && gamedata.isTerrain(carrier.shipSizeClass, carrier.userid)) return false;
        if (shipManager.getTurnDeployed(carrier) !== gamedata.turn) return false;
        return freeLcvDeployRails(carrier, lcv.id).length > 0;
    }

    // Spare LCV-rail capacity on $carrier for the deploy DOCK-TO button label.
    function freeLcvDeployRailCount(carrier, reclaimLcvId) {
        return freeLcvDeployRails(carrier, reclaimLcvId).length;
    }

    // Queue $lcv to deploy-dock onto a free LCV rail of $carrier. $targetRail
    // (optional) docks onto that SPECIFIC rail when it's free (the per-rail
    // picker); omitted/occupied falls back to the first free rail. Sets the rail's
    // pendingLcvDeployStartOrders + the LCV's pendingLcvDeployDock marker.
    // Returns the chosen rail, or null.
    function queueLcvDeployDock(carrier, lcv, targetRail) {
        if (!carrier || !lcv) return null;
        if (lcv.pendingLcvDeployDock) unqueueLcvDeployDock(lcv);   //re-route: release old reservation
        var rails = freeLcvDeployRails(carrier, lcv.id);
        if (rails.length === 0) return null;
        var rail = null;
        if (targetRail) {
            //Honour the explicit rail choice only if it's actually among this
            //carrier's free rails (defends against a stale picker selection).
            rail = rails.filter(function (r) { return r.id === targetRail.id; })[0] || null;
        }
        if (!rail) rail = rails[0];
        if (!Array.isArray(rail.pendingLcvDeployStartOrders)) rail.pendingLcvDeployStartOrders = [];
        rail.pendingLcvDeployStartOrders = [{ shipId: parseInt(lcv.id, 10) }];
        rail.pendingLcvDeployStartOrdersDirty = true;
        lcv.pendingLcvDeployDock = { carrierId: parseInt(carrier.id, 10), railId: parseInt(rail.id, 10) };
        if (typeof rail.refreshHangarTooltip === 'function') rail.refreshHangarTooltip();
        return rail;
    }

    // Remove $lcv from any rail's pendingLcvDeployStartOrders and clear its marker.
    // Idempotent. Snaps the released LCV to the host carrier's CURRENT hex so it
    // reappears co-located with the carrier (LCVs are now allowed to share the
    // carrier's hex — see DeploymentPhaseStrategy same-hex exemption); the player
    // can then move it off or re-dock. Mirrors unqueueDeployStartDock's re-deploy.
    function unqueueLcvDeployDock(lcv) {
        if (!lcv) return;
        var lcvId = parseInt(lcv.id, 10);

        //Capture the host carrier BEFORE clearing the marker — it tells us where
        //to re-place the LCV.
        var carrier = null;
        if (lcv.pendingLcvDeployDock && lcv.pendingLcvDeployDock.carrierId != null) {
            carrier = gamedata.getShip(lcv.pendingLcvDeployDock.carrierId);
        }

        for (var key in gamedata.ships) {
            var s = gamedata.ships[key];
            if (!s || !Array.isArray(s.systems)) continue;
            s.systems.forEach(function (sys) {
                if (!isLCVRailSys(sys) || !Array.isArray(sys.pendingLcvDeployStartOrders)) return;
                var before = sys.pendingLcvDeployStartOrders.length;
                sys.pendingLcvDeployStartOrders = sys.pendingLcvDeployStartOrders.filter(function (o) {
                    return parseInt(o.shipId, 10) !== lcvId;
                });
                if (sys.pendingLcvDeployStartOrders.length !== before) {
                    sys.pendingLcvDeployStartOrdersDirty = true;
                    if (typeof sys.refreshHangarTooltip === 'function') sys.refreshHangarTooltip();
                }
            });
        }
        delete lcv.pendingLcvDeployDock;

        //Re-deploy the LCV at the carrier's current hex so its icon reappears there
        //(same-hex placement is allowed for LCVs). shipManager.movement.deploy is
        //idempotent: it updates the existing deploy entry or creates one.
        if (carrier && typeof shipManager.movement !== 'undefined'
            && typeof shipManager.movement.deploy === 'function') {
            try {
                var carrierPos = shipManager.getShipPosition(carrier);
                if (carrierPos) shipManager.movement.deploy(lcv, carrierPos);
            } catch (e) {
                //fail-soft: a missing position shouldn't break the un-queue
            }
        }
    }

    return {
        shipHasOpenableDockDialog:    shipHasOpenableDockDialog,
        carrierAcceptsLcvDeployDock:  carrierAcceptsLcvDeployDock,
        freeLcvDeployRailCount:       freeLcvDeployRailCount,
        freeLcvDeployRails:           freeLcvDeployRails,
        queueLcvDeployDock:           queueLcvDeployDock,
        unqueueLcvDeployDock:         unqueueLcvDeployDock,
        findPendingFlightsForCarrier: findPendingFlightsForCarrier,
        eligibleHangarsForFlight:     eligibleHangarsForFlight,
        queueDeployStartDock:         queueDeployStartDock,
        autoQueueDockOnCarrier:       autoQueueDockOnCarrier,
        unqueueDeployStartDock:       unqueueDeployStartDock,
        hangarFreeBoxes:              hangarFreeBoxes,
        distributeFlightAcrossHangars: distributeFlightAcrossHangars
    };
})();

// Walk every hangar-family system in the game and refresh its tooltip data.
// Shared step of refreshDeploymentUIForDeployStart + refreshFiringHangarTooltips.
// Sledgehammer-but-cheap: each refresh is just a couple of array walks over
// already-in-memory state. Refreshing all ships keeps the callers phase-agnostic
// and avoids the dialogs having to enumerate which carriers they touched.
// (isDockHangar lives inside the IIFE scope above and is NOT visible here, so
// the name check is inlined — Stage 16: a catapult is a dock hangar too; Fighter
// Rails and LCV rails likewise have refreshable hangar tooltips.)
window.refreshAllHangarTooltips = function () {
    try {
        for (var key in gamedata.ships) {
            var s = gamedata.ships[key];
            if (!s || !Array.isArray(s.systems)) continue;
            s.systems.forEach(function (sys) {
                if (sys && (sys.name === 'hangar' || sys.name === 'catapult' || sys.name === 'fighterRail' || sys.name === 'dockingCollar') && typeof sys.refreshHangarTooltip === 'function') {
                    sys.refreshHangarTooltip();
                }
            });
        }
    } catch (e) {
        //fail-soft: tooltip refresh is cosmetic
    }
};

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
    window.refreshAllHangarTooltips();
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
    window.refreshAllHangarTooltips();
};
