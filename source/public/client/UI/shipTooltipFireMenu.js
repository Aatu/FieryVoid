"use strict";

window.ShipTooltipFireMenu = function () {

    function ShipTooltipFireMenu(selectedShip, targetedShip, turn) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
		var movement = shipManager.movement.getLastCommitedMove(targetedShip);
        this.hexagon = new hexagon.Offset(movement.position);
    }

    ShipTooltipFireMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipFireMenu.buttons = [
		{ className: "targetWeapons", condition: [isEnemy, hasWeaponsSelected], action: targetWeapons, info: "Target Weapons" },
        { className: "targetWeaponsHex", condition: [hasHexWeaponsSelected], action: targetHexagon, info: "Target Hex" },
        { className: "targetSuppWeapons", condition: [isFriendly, hasWeaponsSelected, FFWeaponSelected, notSelf], action: targetWeapons, info: "Target Support Weapons" },//30 June 2024 - DK - Added for Ally targeting.
        { className: "removeMultiOrder", condition: [isEnemy, hasWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firing Order" },
        { className: "launchFighters", condition: [isMine, isFiringPhase, hasLaunchableHangar, isLaunchEnabledGame, carrierNotPivotingOrRolling], action: openHangarLaunch, info: "Launch Fighters" },
        { className: "recoverFlights", condition: [isMine, isFiringPhase, hasReceivableFlights, isLaunchEnabledGame, carrierNotPivotingOrRolling], action: openHangarRecover, info: "Recover Flights" },
        //"Enter Hangar" is reused for LCVs: isDockableUnit accepts a flight OR an
        //LCV, and openHangarDock routes an LCV through the LCV-rail dock dialog.
        { className: "dockFlight", condition: [isMine, isFiringPhase, isDockableUnit, isLaunchEnabledGame, hasEligibleCarrierInHex], action: openHangarDock, info: "Enter Hangar" }
        //{ className: "targetSuppWeapons", condition: [isFriendly, hasWeaponsSelected, notSelf], action: targetWeapons, info: "Target support weapons" },//30 June 2024 - DK - Added for Ally targeting.
        //{ className: "removeMultiOrder", condition: [hasWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firing Order" }
	];

    ShipTooltipFireMenu.prototype.getAllButtons = function () {
        return ShipTooltipFireMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

    // Hangar boxes a single craft occupies. A unitSize<1 craft (Vorlon Assault
    // Fighter et al.) needs more than one box each (e.g. unitSize 0.5 → 2 boxes);
    // every other craft is one box. Mirrors HangarOps::boxesPerCraftForClass /
    // boxesPerCraftForEntry (PHP) so the in-game dock/recover capacity matches
    // what the server will accept.
    // Exposed on window because the dock/recover helpers below
    // (window.findEligibleCarriersForDock / ...ForRecover and their nested
    // collectReceivingHangars*) live OUTSIDE this IIFE and would otherwise not
    // see this closure-private function. The local var keeps in-closure callers
    // working unchanged.
    window.hangarBoxesPerCraftFromUnitSize = function (unitSize) {
        var u = (unitSize != null) ? parseFloat(unitSize) : 1;
        if (u > 0 && u < 1) return Math.ceil(1 / u);   // superheavy: >1 box/craft
        if (u > 1) return 1 / u;                        // ultralight: fractional box/craft (Zorth 0.5)
        return 1;
    };
    var hangarBoxesPerCraftFromUnitSize = window.hangarBoxesPerCraftFromUnitSize;
    // Also exposed on window for the same reason as
    // hangarBoxesPerCraftFromUnitSize above: the dock/recover helpers outside
    // this IIFE call these.
    window.hangarBoxesPerCraftForEntry = function (entry) {
        if (entry && entry.boxesPerCraft) {
            var b = parseFloat(entry.boxesPerCraft);   // float: fractional (0.5) round-trips
            return b > 0 ? b : 1;
        }
        return hangarBoxesPerCraftFromUnitSize(entry ? entry.unitSize : 1);
    };
    var hangarBoxesPerCraftForEntry = window.hangarBoxesPerCraftForEntry;
    // Boxes consumed by a queued dock/deploy order of $count craft of the
    // flight referenced by the order, looked up for its unitSize.
    window.hangarBoxesForQueuedCraft = function (flightId, count) {
        var n = parseInt(count || 0, 10);
        if (n <= 0) return 0;
        var f = (flightId != null) ? gamedata.getShip(flightId) : null;
        var bpc = f ? hangarBoxesPerCraftFromUnitSize(f.unitSize) : 1;
        return n * bpc;
    };
    var hangarBoxesForQueuedCraft = window.hangarBoxesForQueuedCraft;

    function targetWeapons() {
        weaponManager.targetShip(this.selectedShip, this.targetedShip);
    }	
	
    function targetHexagon() {
        weaponManager.targetHex(this.selectedShip, this.hexagon );
    }

	function hasSplitWeaponFiringOrder() {
	    return gamedata.selectedSystems.some(function (system) {
	        return system instanceof Weapon && system.canSplitShots && weaponManager.hasTargetedThisShip(this.targetedShip, system);
	    }.bind(this)); // Bind `this` to the callback
	}
	
	function removeFiringOrderMulti(){
	    // Loop through selected systems and check for systems that have canSplitShots set to true
	    gamedata.selectedSystems.forEach(function(system) {
	        if (system.canSplitShots) {
	            // Call weaponManager.removeFiringOrderMulti for each system that meets the condition
	            weaponManager.removeFiringOrderMulti(this.selectedShip, system, this.targetedShip, true);
	        }
	    }, this); // Make sure to bind `this` so that `this.selectedShip` is correct
	}
	
    function isEnemy() {
        //return this.selectedShip && !gamedata.isMyShip(this.targetedShip);
		//actually enemy is anyone not on players' TEAM, not every other player:
		return this.selectedShip && !gamedata.isMyOrTeamOneShip(this.targetedShip);
    }

    function isFriendly() {//30 June 2024 - DK - Added for Ally targeting.
        return gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function notSelf() {//30 June 2024 - DK - Added for Ally targeting.
        return this.selectedShip !== this.targetedShip;
    }

    function hasWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === false;
            return system instanceof Weapon && system.hextarget !== true;
        });
    }
	
    function hasHexWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === false;
            return system instanceof Weapon && system.hextarget === true;
        });
    }

    function FFWeaponSelected() {
        return gamedata.selectedSystems.some(system => {
            return system.canTargetAllies === true || system.canTargetAll === true ||  (gamedata.rules && gamedata.rules.friendlyFire  === 1);
        });
    }

    // === Hangar Operations Stage 4: launch button conditions + action ===

    function isMine() {
        return gamedata.isMyShip(this.targetedShip);
    }

    // Hangar Operations are a Firing-Phase action only. Pre-Firing (gamephase 5)
    // shares much of the fire-menu plumbing, so gate explicitly on gamephase 3.
    function isFiringPhase() {
        return gamedata.gamephase == 3;
    }

    function isLaunchEnabledGame() {
        // Mirrors HangarOps::isFlowEnabled. The safeGameID gate was removed
        // in Stage 9 — kept as a hook so a future per-game feature flag
        // (e.g. in gamedata) can re-gate without touching the call sites.
        return true;
    }

    function hasLaunchableHangar() {
        var ship = this.targetedShip;
        if (!ship || !ship.systems) return false;
        for (var i in ship.systems) {
            var sys = ship.systems[i];
            //Stage 16: a catapult (name "catapult") is launchable too. Fighter
            //Rails (name "fighterRail") launch like an ordinary hangar — they
            //have an output budget and respect their own damage, so they fall
            //through the isCat branches into the normal budget gate below.
            var isCat = !!(sys && (sys.isCatapult || sys.name === 'catapult'));
            if (!sys || (sys.name !== 'hangar' && sys.name !== 'fighterRail' && !isCat)) continue;
            //Stage S (S-f): a ShadowHangar (integrated-fighter bay) never offers the
            //ordinary "Launch Fighters" button — its fighters leave ONLY via the
            //Fighter Bomb weapon (a normal fireable weapon). Landing is unaffected.
            if (sys.isShadowHangar) continue;
            if (!Array.isArray(sys.hangarUsage) || sys.hangarUsage.length === 0) continue;
            //Stage 16.5: a cannotLaunch wreck (fighter destroyed landing on a
            //damaged catapult) occupies the bay but can never relaunch — it
            //doesn't count as launchable craft.
            var hasLaunchableCraft = sys.hangarUsage.some(function (e) { return e && !e.cannotLaunch; });
            if (!hasLaunchableCraft) continue;
            //A catapult launches its loaded fighter regardless of output budget
            //or damage (no shared launch+land budget), so skip the budget gate.
            if (isCat) return true;
            var output = parseInt(sys.output || 0, 10);
            var used = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
            if (used >= output) continue;
            return true;
        }
        //LCV rails: the "Launch Fighters" button also surfaces when a rail holds
        //an LCV and still has launch budget this turn.
        return window.hasLaunchableLCVRail(ship);
    }

    function carrierNotPivotingOrRolling() {
        var ship = this.targetedShip;
        if (!ship) return false;
        if (shipManager && shipManager.movement) {
            if (shipManager.movement.isRolling(ship)) return false;
            if (shipManager.movement.isPivoting && shipManager.movement.isPivoting(ship) !== "no") return false;
        }
        return true;
    }

    function openHangarLaunch() {
        var carrier = this.targetedShip;
        //hangarLaunch now appends an LCV launch section (appendLcvLaunchSection),
        //so a carrier with both fighters and LCVs shows them together. Only when
        //there is NO launchable fighter hangar (hangarLaunch would early-return on
        //an empty hangar list) do we open the standalone LCV launch dialog.
        if (!window.hasLaunchableFighterHangar(carrier) && window.hasLaunchableLCVRail(carrier)) {
            if (window.confirm && typeof window.confirm.lcvLaunch === 'function') {
                window.confirm.lcvLaunch(carrier);
            }
            return;
        }
        if (window.confirm && typeof window.confirm.hangarLaunch === 'function') {
            window.confirm.hangarLaunch(carrier);
        }
    }

    // === Hangar Operations Stage 5: dock button conditions + action ===

    // An LCV is a full ship (not a flight) that docks onto an LCV rail. The
    // "Enter Hangar" (dockFlight) button is reused for it — see isDockableUnit.
    function isLCVUnit(ship) {
        return !!(ship && !ship.flight && String(ship.hangarRequired || '').toLowerCase() === 'lcvs');
    }

    // The dock button surfaces for a fighter flight OR an LCV. The downstream
    // gate (hasEligibleCarrierInHex) and action (openHangarDock) branch on which.
    function isDockableUnit() {
        var ship = this.targetedShip;
        return !!(ship && (ship.flight || isLCVUnit(ship)));
    }

    // Looks for at least one friendly carrier in the same hex with a hangar
    // (LCV rail for an LCV) that can receive this unit. Doesn't enumerate exact
    // splits; the dialog does the precise math.
    function hasEligibleCarrierInHex() {
        var unit = this.targetedShip;
        if (!unit) return false;
        if (isLCVUnit(unit)) {
            //Keep the button available when a dock is already queued for this LCV,
            //so the player can re-open the dialog and un-declare it from the LCV
            //side even if every rail is now claimed (no "free" rail remains).
            if (window.findEligibleLCVRailsForDock(unit).length > 0) return true;
            return !!window.lcvQueuedDockRail(unit);
        }
        if (!unit.flight) return false;
        var carriers = findEligibleCarriersForDock(unit);
        return carriers.length > 0;
    }

    function openHangarDock() {
        var unit = this.targetedShip;
        if (isLCVUnit(unit)) {
            if (window.confirm && typeof window.confirm.lcvDock === 'function') {
                window.confirm.lcvDock(unit);
            }
            return;
        }
        if (window.confirm && typeof window.confirm.hangarDock === 'function') {
            window.confirm.hangarDock(unit);
        }
    }

    // === Carrier-side bulk recover: mirror of the Deployment-Phase dock dialog. ===
    //
    // The condition surfaces when the tooltip target is a friendly non-flight
    // ship with at least one functional hangar AND at least one same-hex
    // eligible flight that fits in one of its hangars. Eligibility otherwise
    // matches the per-flight Dock path (same-hex/heading/speed-within-thrust).
    function hasReceivableFlights() {
        var ship = this.targetedShip;
        if (!ship || ship.flight) return false;
        if (!Array.isArray(ship.systems)) return false;
        var flights = findEligibleFlightsForDocking(ship);
        if (flights.length > 0) return true;
        //LCV rails: the same "Recover Flights" button also surfaces when this
        //carrier has a free LCV rail and an eligible LCV sharing its hex.
        return window.findEligibleLCVsForRecover(ship).length > 0;
    }

    function openHangarRecover() {
        var carrier = this.targetedShip;
        //hangarRecover now renders BOTH fighter flights AND eligible LCVs in one
        //container (the LCV section is appended by appendLcvRecoverSection), so a
        //carrier with both shows them together. When ONLY LCVs are receivable
        //(no fighter bays / no eligible flights), hangarRecover still opens and
        //its empty-flights branch shows just the LCV section.
        if (window.confirm && typeof window.confirm.hangarRecover === 'function') {
            window.confirm.hangarRecover(carrier);
        }
    }

    return ShipTooltipFireMenu;
}();

// Shared helper used by both the tooltip menu (for the eligibility gate) and
// the confirm dialog (for the carrier picker). Returns an array of:
//   { ship, hangars: [{hangar, capacity}, ...], totalCapacity }
// for every friendly carrier in the same hex as $flight that can receive at
// least one craft. Capacity is min(free boxes, output budget) per hangar.
window.findEligibleCarriersForDock = function (flight) {
    var out = [];
    if (!flight || !flight.flight) return out;
    if (shipManager.isDestroyed(flight)) return out;

    var flightMove = shipManager.movement.getLastCommitedMove(flight);
    if (!flightMove) return out;
    var fPos = flightMove.position;
    var fHeading = parseInt(flightMove.heading, 10);
    var fSpeed = parseInt(flightMove.speed, 10);

    // Per-fighter thrust budget — best available approximation of B5W "thrust"
    // for the speed-delta check. flight.freethrust is set in ship file
    // constructors and serialized via stripForJson on FighterFlight.
    var thrust = parseInt(flight.freethrust || 0, 10);

    var category = categoryForFlight(flight);

    for (var key in gamedata.ships) {
        var ship = gamedata.ships[key];
        if (!ship || ship.id === flight.id) continue;
        if (shipManager.isDestroyed(ship)) continue;
        if (!gamedata.isMyorMyTeamShip(ship)) continue;
        if (ship.flight) continue;                       //flights can't carry flights
        if (!Array.isArray(ship.systems)) continue;

        var sMove = shipManager.movement.getLastCommitedMove(ship);
        if (!sMove) continue;
        if (!fPos || !sMove.position) continue;
        if (sMove.position.q !== fPos.q || sMove.position.r !== fPos.r) continue;
        if (Math.abs(parseInt(sMove.speed, 10) - fSpeed) > thrust) continue;
        if (shipManager.movement.isRolling(ship)) continue;
        if (shipManager.movement.isPivoting && shipManager.movement.isPivoting(ship) !== "no") continue;

        // Heading is gated per-hangar inside collectReceivingHangars: ordinary
        // hangars require the flight heading to match the carrier heading;
        // catapults (Stage 16) require a rear approach (flight heading == carrier
        // facing), so the carrier-level heading filter is no longer applied here.
        var hangarsOnShip = collectReceivingHangars(ship, category, sMove);
        if (hangarsOnShip.length === 0) continue;

        var total = 0;
        hangarsOnShip.forEach(function (h) { total += h.capacity; });
        if (total <= 0) continue;
        out.push({ ship: ship, hangars: hangarsOnShip, totalCapacity: total });
    }

    return out;

    // Mirrors HangarOps::trueSizeOf (PHP): an explicit hangarRequired wins;
    // generic 'fighters'/'normal' falls back to jinkinglimit-based classification
    // (the same buckets checkChoices() in gamelobby.js uses for fleet validation).
    function categoryForFlight(f) {
        var req = String(f.hangarRequired || '').trim();
        var lower = req.toLowerCase();
        if (lower === '' || lower === 'fighters' || lower === 'normal') {
            var jink = parseInt(f.jinkinglimit || 0, 10);
            if (jink >= 99) return 'ultralight';
            if (jink >= 10) return 'light';
            if (jink >= 8)  return 'medium';
            if (jink >= 6)  return 'heavy';
            return 'medium';
        }
        return req;
    }

    function collectReceivingHangars(ship, category, carrierMove) {
        var hangars = [];
        var flightId = parseInt(flight.id, 10);
        var bpcFlight = window.hangarBoxesPerCraftFromUnitSize(flight.unitSize);   //boxes per craft for THIS flight
        var carrierHeading = carrierMove ? parseInt(carrierMove.heading, 10) : null;
        var carrierFacing  = carrierMove ? parseInt(carrierMove.facing, 10)  : null;
        ship.systems.forEach(function (sys) {
            // Stage 16: a catapult (name "catapult") recovers its fighter from the
            // REAR, holds exactly one craft, ignores its own damage and has no
            // launch+land budget.
            var isCat = !!(sys && (sys.isCatapult || sys.name === 'catapult'));
            if (!sys || (sys.name !== 'hangar' && sys.name !== 'fighterRail' && !isCat))return;
            if (!isCat && shipManager.systems.isDestroyed(ship, sys)) return;

            if (!hangarAcceptsCategory(sys.hangarType, category, ship)) return;

            // Heading gate (per-hangar): ordinary hangar → flight heading must
            // match the carrier heading; catapult → rear approach (flight heading
            // == carrier facing).
            if (carrierMove !== null && carrierMove !== undefined) {
                var requiredHeading = isCat ? carrierFacing : carrierHeading;
                if (fHeading !== requiredHeading) return;
            }

            // Effective free boxes. Catapult capacity is a flat 1 regardless of
            // box count / damage; ordinary hangars use maxhealth - net damage.
            var effective;
            if (isCat) {
                effective = 1;
            } else {
                var netDamage = 0;
                if (Array.isArray(sys.damage)) {
                    sys.damage.forEach(function (d) {
                        netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
                    });
                }
                effective = Math.max(0, parseInt(sys.maxhealth, 10) - netDamage);
            }
            // Occupied boxes. unitSize<1 craft consume >1 box each, unitSize>1
            // ultralights consume a FRACTIONAL box each (ordinary hangars only);
            // a catapult counts craft 1:1 (single-fighter rail). Sum the box cost
            // fractionally and round the TOTAL up once below — so 24 Zorth (12.0)
            // and two separate half-box docks pack correctly rather than each
            // reserving a whole box.
            var usedBoxes = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) {
                    var perCraft = isCat ? 1 : window.hangarBoxesPerCraftForEntry(e);
                    usedBoxes += parseInt(e.flightSize || 1, 10) * perCraft;
                });
            }

            // Ordinary hangars share a launch+land output budget; catapults don't.
            var budget;
            if (isCat) {
                budget = 0;   //set after free is known
            } else {
                var output = parseInt(sys.output || 0, 10);
                var spent = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
                budget = Math.max(0, output - spent);
            }

            // Add queued allocations from OTHER flights/launches on this hangar
            // (shared output budget + physical free boxes) to the box total. Skip
            // this flight's OWN dock orders so re-editing/cancelling sees the full
            // capacity it had before queuing — the dialog re-applies the queued
            // amount as a pre-fill on the splitter input instead. The output budget
            // is in CRAFT; physical free space is in BOXES, so a queued ultralight
            // dock consumes its (fractional) per-craft box cost.
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === flightId) return;   //own queue is reclaimable
                    var n = parseInt(o.count || 0, 10);
                    usedBoxes += (isCat ? n : window.hangarBoxesForQueuedCraft(o.flightId, n));
                    if (!isCat) budget = Math.max(0, budget - n);
                });
            }
            // Free WHOLE boxes = capacity − total used rounded UP (catapult: free craft slots).
            var free = Math.max(0, effective - Math.ceil(usedBoxes));
            if (isCat) budget = free;
            if (!isCat && Array.isArray(sys.pendingLaunchOrders)) {
                sys.pendingLaunchOrders.forEach(function (o) {
                    budget = Math.max(0, budget - parseInt(o.size || 0, 10));
                });
            }

            // Capacity is returned in CRAFT (the splitter/totals are craft): a
            // catapult holds its free craft slots; an ordinary hangar fits
            // floor(free boxes / boxes-per-craft) of THIS flight, capped by budget.
            var capacity = isCat ? free : Math.min(Math.floor(free / bpcFlight), budget);
            if (capacity > 0) hangars.push({ hangar: sys, capacity: capacity });
        });

        // Stage 10.6.2: clamp aggregate capacity to the carrier's remaining
        // customFighter cap for this flight's customFtrName. Cap is shared
        // across all hangars on the carrier — walk in order and truncate each
        // entry until the running total hits the cap.
        var customName = String(flight.customFtrName || '');
        if (customName !== '') {
            var cap = customFighterRemainingFor(ship, customName, flightId);
            if (cap <= 0) return [];
            var running = 0;
            var clamped = [];
            for (var i = 0; i < hangars.length; i++) {
                if (running >= cap) break;
                var take = Math.min(hangars[i].capacity, cap - running);
                if (take <= 0) continue;
                clamped.push({ hangar: hangars[i].hangar, capacity: take });
                running += take;
            }
            return clamped;
        }
        return hangars;
    }

    // Mirrors HangarOps::customFighterRemaining (PHP). Per-CARRIER count of
    // remaining custom-named slots (e.g. Thunderbolt, Rutarian). Returns
    // Infinity when $name === '' (no gate); 0 when the carrier doesn't
    // declare $customFighter[name]; declared - used otherwise. Pending dock
    // orders from OTHER flights count against the cap; THIS flight's own
    // pending orders are reclaimable (mirrors physical-capacity reclaim).
    function customFighterRemainingFor(carrier, name, ownFlightId) {
        if (!name) return Infinity;
        if (!carrier.customFighter || !carrier.customFighter[name]) return 0;
        var declared = parseInt(carrier.customFighter[name], 10);
        var used = 0;
        carrier.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) {
                    if (e.customFtrName !== name) return;
                    used += parseInt(e.flightSize || 1, 10);
                });
            }
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === ownFlightId) return;
                    var f = gamedata.getShip(o.flightId);
                    if (!f || String(f.customFtrName || '') !== name) return;
                    used += parseInt(o.count || 0, 10);
                });
            }
        });
        return Math.max(0, declared - used);
    }

    // Mirrors HangarOps::hangarAcceptsCategory (PHP) — combat-fighter size
    // hierarchy plus shuttle/BP compatibility. Keep in sync with the server
    // helper so the eligibility gate matches end-of-turn validation. Universal
    // 'fighters'/'normal' slots derive their permissions from the ship's
    // $fighters declaration when ship is provided (handles multi-category
    // ships like Decurion / Falenna).
    function hangarAcceptsCategory(hangarType, category, ship) {
        var hType = String(hangarType || '').toLowerCase().trim();
        var cat   = String(category   || '').toLowerCase().trim();
        if (hType === '' || cat === '') return false;
        var rank = { ultralight: 1, light: 2, medium: 3, heavy: 4 };

        if (hType === cat) return true;
        if (rank[hType] && rank[cat]) return rank[cat] <= rank[hType];
        if ((cat === 'shuttles' || cat === 'minesweeping shuttles') && rank[hType]) return true;

        //Breaching Pods: dedicated BP slot (exact-match above), Assault Shuttle
        //slot, or ANY combat fighter slot (heavy/medium/light/ultralight).
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
            var declared = lowerKeys(ship.fighters);
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

    function lowerKeys(obj) {
        var out = {};
        for (var k in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, k)) {
                out[String(k).toLowerCase()] = obj[k];
            }
        }
        return out;
    }
};

// Carrier-perspective inverse of findEligibleCarriersForDock: given a carrier,
// return every friendly fighter flight in the same hex that matches heading
// and speed-within-thrust AND whose full active flight fits in at least one
// of the carrier's hangars (size hierarchy + shared launch+land budget).
//
// Returns an array of:
//   { flight, hangars: [{hangar, capacity}, ...] }
// for each eligible flight. Capacity treats THIS flight's own queued docks on
// this carrier as reclaimable — same as findEligibleCarriersForDock — so a
// re-edit sees the full capacity it had before queuing.
//
// Used by:
//   - shipTooltipFireMenu's hasReceivableFlights condition (boolean gate)
//   - confirm.hangarRecover (full row data with hangar dropdown options)
window.findEligibleFlightsForDocking = function (carrier) {
    var out = [];
    if (!carrier || !Array.isArray(carrier.systems)) return out;
    if (carrier.flight) return out;
    if (shipManager.isDestroyed(carrier)) return out;
    if (shipManager.movement.isRolling(carrier)) return out;
    if (shipManager.movement.isPivoting && shipManager.movement.isPivoting(carrier) !== "no") return out;

    var carrierMove = shipManager.movement.getLastCommitedMove(carrier);
    if (!carrierMove || !carrierMove.position) return out;
    var cPos = carrierMove.position;
    var cHeading = parseInt(carrierMove.heading, 10);
    var cSpeed = parseInt(carrierMove.speed, 10);

    for (var key in gamedata.ships) {
        var flight = gamedata.ships[key];
        if (!flight || !flight.flight) continue;
        if (flight.id === carrier.id) continue;
        if (shipManager.isDestroyed(flight)) continue;
        if (!gamedata.isMyorMyTeamShip(flight)) continue;

        var fMove = shipManager.movement.getLastCommitedMove(flight);
        if (!fMove || !fMove.position) continue;
        if (fMove.position.q !== cPos.q || fMove.position.r !== cPos.r) continue;

        var thrust = parseInt(flight.freethrust || 0, 10);
        if (Math.abs(parseInt(fMove.speed, 10) - cSpeed) > thrust) continue;

        // Heading is gated per-hangar inside collectReceivingHangarsForRecover:
        // ordinary hangars require flight heading == carrier heading; catapults
        // (Stage 16) require a rear approach (flight heading == carrier facing).
        // hangars = bays that each hold the WHOLE flight (single-bay dock). Its
        // .combinedFit property is true when the carrier's combined free space
        // holds the flight even if no single bay does (rails are a pooled
        // resource — a 9-flight spreads across a 6-box + 3-box rail); the dialog
        // then auto-distributes across bays.
        var hangars = collectReceivingHangarsForRecover(carrier, flight);
        if (hangars.length === 0 && !hangars.combinedFit) continue;

        out.push({ flight: flight, hangars: hangars, combinedFit: hangars.combinedFit });
    }
    return out;

    // Same shape as collectReceivingHangars in findEligibleCarriersForDock,
    // but returns only hangars whose capacity holds the FULL active flight —
    // the bulk-recover dialog doesn't split a flight across hangars (splitter
    // remains on the per-flight Dock dialog).
    function collectReceivingHangarsForRecover(ship, flight) {
        var hangars = [];          //bays that each hold the WHOLE flight
        hangars.combinedFit = false;
        var combinedCraft = 0;     //running sum of every eligible bay's craft capacity
        var flightId = parseInt(flight.id, 10);
        var category = categoryForFlightRecover(flight);
        var size = countActiveInFlight(flight);
        if (size <= 0) return hangars;
        var bpcFlight = window.hangarBoxesPerCraftFromUnitSize(flight.unitSize);   //boxes per craft for THIS flight

        // Stage 10.6.2: bulk recover only ever docks a FULL flight into a
        // single hangar — if the carrier's customFighter cap can't hold the
        // whole flight, the flight isn't eligible at all.
        var customName = String(flight.customFtrName || '');
        if (customName !== '') {
            var cap = customFighterRemainingForRecover(ship, customName, flightId);
            if (cap < size) return hangars;
        }

        // Flight heading for the per-hangar rear-approach gate (catapults).
        var rMove = shipManager.movement.getLastCommitedMove(flight);
        var rFlightHeading = rMove ? parseInt(rMove.heading, 10) : null;
        var rCarrierHeading = carrierMove ? parseInt(carrierMove.heading, 10) : null;
        var rCarrierFacing  = carrierMove ? parseInt(carrierMove.facing, 10)  : null;

        ship.systems.forEach(function (sys) {
            // Stage 16: a catapult recovers from the REAR, holds one craft,
            // ignores its own damage and has no launch+land budget.
            var isCat = !!(sys && (sys.isCatapult || sys.name === 'catapult'));
            if (!sys || (sys.name !== 'hangar' && sys.name !== 'fighterRail' && !isCat))return;
            if (!isCat && shipManager.systems.isDestroyed(ship, sys)) return;
            if (!hangarAcceptsCategoryRecover(sys.hangarType, category, ship)) return;

            // Per-hangar heading gate.
            if (rFlightHeading !== null) {
                var requiredHeading = isCat ? rCarrierFacing : rCarrierHeading;
                if (rFlightHeading !== requiredHeading) return;
            }

            var effective;
            if (isCat) {
                effective = 1;
            } else {
                var netDamage = 0;
                if (Array.isArray(sys.damage)) {
                    sys.damage.forEach(function (d) {
                        netDamage += Math.max(0, parseInt(d.damage || 0, 10) - parseInt(d.armour || 0, 10));
                    });
                }
                effective = Math.max(0, parseInt(sys.maxhealth, 10) - netDamage);
            }
            // Occupied boxes. unitSize<1 craft consume >1 box each, unitSize>1
            // ultralights consume a FRACTIONAL box each (ordinary hangars only);
            // a catapult counts craft 1:1. Sum fractionally, round the TOTAL up once.
            var usedBoxes = 0;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) {
                    var perCraft = isCat ? 1 : window.hangarBoxesPerCraftForEntry(e);
                    usedBoxes += parseInt(e.flightSize || 1, 10) * perCraft;
                });
            }

            var budget;
            if (isCat) {
                budget = 0;   //set after free is known
            } else {
                var output = parseInt(sys.output || 0, 10);
                var spent  = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
                budget = Math.max(0, output - spent);
            }

            // OTHER flights' queued docks consume both free boxes and the shared
            // launch+land budget. THIS flight's own queue is reclaimable so the
            // row's hangar dropdown can re-pick it. Budget is in CRAFT; free space
            // is in BOXES (queued ultralight docks cost a fractional box each).
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === flightId) return;
                    var n = parseInt(o.count || 0, 10);
                    usedBoxes += (isCat ? n : window.hangarBoxesForQueuedCraft(o.flightId, n));
                    if (!isCat) budget = Math.max(0, budget - n);
                });
            }
            // Free WHOLE boxes = capacity − total used rounded UP (catapult: free craft slots).
            var free = Math.max(0, effective - Math.ceil(usedBoxes));
            if (isCat) budget = free;
            // Queued launch orders consume budget only (no physical boxes since
            // the craft are leaving).
            if (!isCat && Array.isArray(sys.pendingLaunchOrders)) {
                sys.pendingLaunchOrders.forEach(function (o) {
                    budget = Math.max(0, budget - parseInt(o.size || 0, 10));
                });
            }
            // Capacity is in CRAFT: a catapult's free craft slots, else floor(free
            // boxes / boxes-per-craft) capped by the craft output budget.
            var capacity = isCat ? free : Math.min(Math.floor(free / bpcFlight), budget);
            if (capacity <= 0) return;
            combinedCraft += capacity;                                  //counts toward combined-pool fit
            if (capacity >= size) hangars.push({ hangar: sys, capacity: capacity });   //single-bay dock
        });
        //Combined-pool fit: the carrier's rails/bays together hold the flight even
        //when no single bay does (the dialog then auto-distributes across bays).
        hangars.combinedFit = (combinedCraft >= size);
        return hangars;
    }

    // Same shape as the per-flight Dock helper of the same name — duplicated
    // here because the two closures don't share scope and we want each
    // file's helper to be self-contained for readability.
    function customFighterRemainingForRecover(carrier, name, ownFlightId) {
        if (!name) return Infinity;
        if (!carrier.customFighter || !carrier.customFighter[name]) return 0;
        var declared = parseInt(carrier.customFighter[name], 10);
        var used = 0;
        carrier.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (Array.isArray(sys.hangarUsage)) {
                sys.hangarUsage.forEach(function (e) {
                    if (e.customFtrName !== name) return;
                    used += parseInt(e.flightSize || 1, 10);
                });
            }
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === ownFlightId) return;
                    var f = gamedata.getShip(o.flightId);
                    if (!f || String(f.customFtrName || '') !== name) return;
                    used += parseInt(o.count || 0, 10);
                });
            }
        });
        return Math.max(0, declared - used);
    }

    function countActiveInFlight(flight) {
        if (!Array.isArray(flight.systems)) return 0;
        var n = 0;
        flight.systems.forEach(function (ftr) {
            if (!shipManager.systems.isDestroyed(flight, ftr)) n++;
        });
        return n;
    }

    function categoryForFlightRecover(f) {
        var req = String(f.hangarRequired || '').trim();
        var lower = req.toLowerCase();
        if (lower === '' || lower === 'fighters' || lower === 'normal') {
            var jink = parseInt(f.jinkinglimit || 0, 10);
            if (jink >= 99) return 'ultralight';
            if (jink >= 10) return 'light';
            if (jink >= 8)  return 'medium';
            if (jink >= 6)  return 'heavy';
            return 'medium';
        }
        return req;
    }

    function hangarAcceptsCategoryRecover(hangarType, category, ship) {
        var hType = String(hangarType || '').toLowerCase().trim();
        var cat   = String(category   || '').toLowerCase().trim();
        if (hType === '' || cat === '') return false;
        var rank = { ultralight: 1, light: 2, medium: 3, heavy: 4 };

        if (hType === cat) return true;
        if (rank[hType] && rank[cat]) return rank[cat] <= rank[hType];
        if ((cat === 'shuttles' || cat === 'minesweeping shuttles') && rank[hType]) return true;

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
            var declared = lowerKeysR(ship.fighters);
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

    function lowerKeysR(obj) {
        var out = {};
        for (var k in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, k)) {
                out[String(k).toLowerCase()] = obj[k];
            }
        }
        return out;
    }
};

// ===================================================================== //
// LCV Rails (DockingCollar) — whole-ship dock/launch client helpers.     //
// An LCV is a full ship (ship.hangarRequired === 'LCVs'), not a flight.  //
// These mirror the server-side HangarOps::canLCVDock / collectLCVRails    //
// gates so the fire-menu buttons + confirm dialogs surface only when a    //
// dock/launch is actually legal.                                         //
// ===================================================================== //

// True if a system is an LCV rail (DockingCollar).
window.isLCVRailSystem = function (sys) {
    return !!(sys && (sys.isLCVRail || sys.name === 'dockingCollar'));
};

// Is this rail free (not destroyed, no LCV docked, launch+land budget left)?
window.lcvRailFree = function (carrier, rail) {
    if (!window.isLCVRailSystem(rail)) return false;
    if (shipManager.systems.isDestroyed(carrier, rail)) return false;
    if (rail.lcvDocked && rail.lcvDocked.shipId) return false;
    // Pending dock order this turn already claims the rail.
    if (Array.isArray(rail.pendingLcvDockOrders) && rail.pendingLcvDockOrders.length > 0) return false;
    var out  = parseInt(rail.output || 0, 10);
    var used = parseInt(rail.launchedThisTurn || 0, 10) + parseInt(rail.landedThisTurn || 0, 10);
    return used < out;
};

// Is this rail occupied by an LCV and still able to launch it this turn?
// Only a COMMITTED occupant (server-sent lcvDocked link, set at end-of-turn dock
// resolution / reload) is launchable — an LCV merely ORDERED to dock this turn is
// not docked yet (you can't dock and launch in the same turn; mirrors the server
// canLCVLaunch gate which reads lcvDocked, never the pending dock order).
window.lcvRailLaunchable = function (carrier, rail) {
    if (!window.isLCVRailSystem(rail)) return false;
    if (shipManager.systems.isDestroyed(carrier, rail)) return false;
    if (!(rail.lcvDocked && rail.lcvDocked.shipId)) return false;
    // A pending dock order this turn (re-dock of a just-launched rail, or a
    // mid-dialog order) is not yet a real occupant — don't offer to launch it.
    if (Array.isArray(rail.pendingLcvDockOrders) && rail.pendingLcvDockOrders.length > 0) return false;
    // A pending launch already empties it.
    if (Array.isArray(rail.pendingLcvLaunchOrders) && rail.pendingLcvLaunchOrders.length > 0) return false;
    var out  = parseInt(rail.output || 0, 10);
    var used = parseInt(rail.launchedThisTurn || 0, 10) + parseInt(rail.landedThisTurn || 0, 10);
    return used < out;
};

// Does $carrier have any LCV rail that could launch an LCV this turn?
window.hasLaunchableLCVRail = function (carrier) {
    if (!carrier || !Array.isArray(carrier.systems)) return false;
    return carrier.systems.some(function (s) { return window.lcvRailLaunchable(carrier, s); });
};

// Does $carrier have any launchable FIGHTER hangar (ordinary/catapult/rail)?
// Used by openHangarLaunch to decide between the fighter and LCV launch dialogs.
window.hasLaunchableFighterHangar = function (carrier) {
    if (!carrier || !Array.isArray(carrier.systems)) return false;
    return carrier.systems.some(function (sys) {
        var isCat = !!(sys && (sys.isCatapult || sys.name === 'catapult'));
        if (!sys || (sys.name !== 'hangar' && sys.name !== 'fighterRail' && !isCat)) return false;
        //Stage S (S-f): ShadowHangars launch only via the Fighter Bomb weapon, not
        //the ordinary launch dialog — exclude them here too (parallels hasLaunchableHangar).
        if (sys.isShadowHangar) return false;
        if (!Array.isArray(sys.hangarUsage) || sys.hangarUsage.length === 0) return false;
        if (!sys.hangarUsage.some(function (e) { return e && !e.cannotLaunch; })) return false;
        if (isCat) return true;
        var out  = parseInt(sys.output || 0, 10);
        var used = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
        return used < out;
    });
};

// The LCV's remaining engine thrust this turn (the dock-gate "1 thrust unspent"
// value sent to the server). Delegates to the authoritative movement helper.
window.lcvRemainingThrust = function (lcv) {
    return Math.max(0, parseInt(shipManager.movement.getRemainingEngineThrust(lcv), 10) || 0);
};

// Carriers in $lcv's hex with a free LCV rail, satisfying the dock conditions
// (carrier stationary, same hex, matching heading, LCV has >=1 thrust unspent).
// Returns [{ ship, rails: [rail,...] }, ...]. Mirrors HangarOps::canLCVDock.
window.findEligibleLCVRailsForDock = function (lcv) {
    var out = [];
    if (!lcv || lcv.flight) return out;
    if (String(lcv.hangarRequired || '').toLowerCase() !== 'lcvs') return out;
    if (shipManager.isDestroyed(lcv)) return out;

    var lcvMove = shipManager.movement.getLastCommitedMove(lcv);
    if (!lcvMove || !lcvMove.position) return out;
    if (window.lcvRemainingThrust(lcv) < 1) return out;   //no thrust to dock

    var lPos = lcvMove.position;
    var lHeading = parseInt(lcvMove.heading, 10);

    for (var key in gamedata.ships) {
        var ship = gamedata.ships[key];
        if (!ship || ship.id === lcv.id) continue;
        if (ship.flight) continue;
        if (shipManager.isDestroyed(ship)) continue;
        if (!gamedata.isMyorMyTeamShip(ship)) continue;
        if (!Array.isArray(ship.systems)) continue;

        var sMove = shipManager.movement.getLastCommitedMove(ship);
        if (!sMove || !sMove.position) continue;
        if (parseInt(sMove.speed, 10) !== 0) continue;                    //carrier stationary
        if (sMove.position.q !== lPos.q || sMove.position.r !== lPos.r) continue;  //same hex
        if (parseInt(sMove.heading, 10) !== lHeading) continue;          //matching heading

        var rails = ship.systems.filter(function (s) { return window.lcvRailFree(ship, s); });
        if (rails.length === 0) continue;
        out.push({ ship: ship, rails: rails });
    }
    return out;
};

// LCVs in $carrier's hex eligible to dock onto one of its free rails (the
// carrier-side mirror of findEligibleLCVRailsForDock). Returns [lcv, ...].
window.findEligibleLCVsForRecover = function (carrier) {
    var out = [];
    if (!carrier || carrier.flight) return out;
    if (!Array.isArray(carrier.systems)) return out;
    if (shipManager.isDestroyed(carrier)) return out;

    var freeRails = carrier.systems.filter(function (s) { return window.lcvRailFree(carrier, s); });
    if (freeRails.length === 0) return out;

    var cMove = shipManager.movement.getLastCommitedMove(carrier);
    if (!cMove || !cMove.position) return out;
    if (parseInt(cMove.speed, 10) !== 0) return out;     //carrier must be stationary
    var cPos = cMove.position;
    var cHeading = parseInt(cMove.heading, 10);

    for (var key in gamedata.ships) {
        var lcv = gamedata.ships[key];
        if (!lcv || lcv.id === carrier.id) continue;
        if (lcv.flight) continue;
        if (String(lcv.hangarRequired || '').toLowerCase() !== 'lcvs') continue;
        if (shipManager.isDestroyed(lcv)) continue;
        if (!gamedata.isMyorMyTeamShip(lcv)) continue;

        var lMove = shipManager.movement.getLastCommitedMove(lcv);
        if (!lMove || !lMove.position) continue;
        if (lMove.position.q !== cPos.q || lMove.position.r !== cPos.r) continue;
        if (parseInt(lMove.heading, 10) !== cHeading) continue;
        if (window.lcvRemainingThrust(lcv) < 1) continue;
        out.push(lcv);
    }
    return out;
};

// The rail (and its carrier) that already has a queued dock order for $lcv this
// turn, or null. Used so the dock/recover dialogs can pre-check the box and
// un-declare an existing order. Walks every owned ship's LCV rails.
window.lcvQueuedDockRail = function (lcv) {
    if (!lcv) return null;
    var lcvId = parseInt(lcv.id, 10);
    for (var key in gamedata.ships) {
        var ship = gamedata.ships[key];
        if (!ship || !Array.isArray(ship.systems)) continue;
        for (var i = 0; i < ship.systems.length; i++) {
            var sys = ship.systems[i];
            if (!window.isLCVRailSystem(sys)) continue;
            if (!Array.isArray(sys.pendingLcvDockOrders)) continue;
            if (sys.pendingLcvDockOrders.some(function (o) { return parseInt(o.shipId, 10) === lcvId; })) {
                return { carrier: ship, rail: sys };
            }
        }
    }
    return null;
};

// Remove any queued dock order for $lcv from every LCV rail (so unchecking the
// dock box, or switching carriers, clears the stale order). Marks each touched
// rail dirty so an emptied list is still submitted (cancel path).
window.clearQueuedLcvDock = function (lcv) {
    if (!lcv) return;
    var lcvId = parseInt(lcv.id, 10);
    for (var key in gamedata.ships) {
        var ship = gamedata.ships[key];
        if (!ship || !Array.isArray(ship.systems)) continue;
        ship.systems.forEach(function (sys) {
            if (!window.isLCVRailSystem(sys)) return;
            if (!Array.isArray(sys.pendingLcvDockOrders)) return;
            var had = sys.pendingLcvDockOrders.some(function (o) { return parseInt(o.shipId, 10) === lcvId; });
            if (!had) return;
            sys.pendingLcvDockOrders = sys.pendingLcvDockOrders.filter(function (o) { return parseInt(o.shipId, 10) !== lcvId; });
            sys.pendingLcvDockOrdersDirty = true;
            if (typeof sys.refreshHangarTooltip === 'function') sys.refreshHangarTooltip();
        });
    }
};

// Human-readable label for an LCV rail, keyed by its ship LOCATION rather than a
// flat index — e.g. "LCV Rail Forward 1", "LCV Rail Port". Mirrors the fighter
// hangar location-labelling in confirm.js (Main/Front/Aft/Port/Stbd numbered
// within a location group), but using the LCV terms the Deliverer et al. expect
// (Forward/Aft/Port/Starboard) and the "LCV Rail <Location> N" shape. The trailing
// N appears only when >1 rail shares the same location group. Used by every LCV
// dock/launch/recover dialog so a rail reads the same everywhere.
window.lcvRailLabel = function (carrier, rail) {
    if (!carrier || !Array.isArray(carrier.systems) || !rail) return 'LCV Rail';

    //Collapse split-arc port/stbd codes (31/32 → 3, 41/42 → 4) so both halves of
    //a side count as one location group (matches the fighter helper).
    var groupOf = function (l) {
        l = parseInt(l, 10);
        if (l === 31 || l === 32) return 3;
        if (l === 41 || l === 42) return 4;
        return l;
    };
    var locName = function (l) {
        switch (groupOf(l)) {
            case 0:  return 'Main';
            case 1:  return 'Forward';
            case 2:  return 'Aft';
            case 3:  return 'Port';
            case 4:  return 'Starboard';
            default: return '';
        }
    };

    var loc = locName(rail.location);
    //Rails sharing this rail's location group, in encounter (construction) order.
    var siblings = carrier.systems.filter(function (s) {
        return window.isLCVRailSystem(s) && groupOf(s.location) === groupOf(rail.location);
    });

    var base = loc ? ('LCV Rail ' + loc) : 'LCV Rail';
    if (siblings.length <= 1) return base;
    var idx = siblings.indexOf(rail);
    return base + ' ' + (idx + 1);
};