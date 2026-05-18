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
        { className: "launchFighters", condition: [isMine, hasLaunchableHangar, isLaunchEnabledGame, carrierNotPivotingOrRolling], action: openHangarLaunch, info: "Launch Fighters" },
        { className: "recoverFlights", condition: [isMine, hasReceivableFlights, isLaunchEnabledGame, carrierNotPivotingOrRolling], action: openHangarRecover, info: "Recover Flights" },
        { className: "dockFlight", condition: [isMine, isFighterFlight, isLaunchEnabledGame, hasEligibleCarrierInHex], action: openHangarDock, info: "Enter Hangar" }
        //{ className: "targetSuppWeapons", condition: [isFriendly, hasWeaponsSelected, notSelf], action: targetWeapons, info: "Target support weapons" },//30 June 2024 - DK - Added for Ally targeting.
        //{ className: "removeMultiOrder", condition: [hasWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firing Order" }
	];

    ShipTooltipFireMenu.prototype.getAllButtons = function () {
        return ShipTooltipFireMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

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
            if (!sys || sys.name !== 'hangar') continue;
            if (!Array.isArray(sys.hangarUsage) || sys.hangarUsage.length === 0) continue;
            var output = parseInt(sys.output || 0, 10);
            var used = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
            if (used >= output) continue;
            return true;
        }
        return false;
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
        if (window.confirm && typeof window.confirm.hangarLaunch === 'function') {
            window.confirm.hangarLaunch(this.targetedShip);
        }
    }

    // === Hangar Operations Stage 5: dock button conditions + action ===

    function isFighterFlight() {
        var ship = this.targetedShip;
        return !!(ship && ship.flight);
    }

    // Looks for at least one friendly carrier in the same hex with a hangar
    // that can receive (some of) this flight. Doesn't enumerate exact splits;
    // the dialog does the precise math.
    function hasEligibleCarrierInHex() {
        var flight = this.targetedShip;
        if (!flight || !flight.flight) return false;
        var carriers = findEligibleCarriersForDock(flight);
        return carriers.length > 0;
    }

    function openHangarDock() {
        if (window.confirm && typeof window.confirm.hangarDock === 'function') {
            window.confirm.hangarDock(this.targetedShip);
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
        return flights.length > 0;
    }

    function openHangarRecover() {
        if (window.confirm && typeof window.confirm.hangarRecover === 'function') {
            window.confirm.hangarRecover(this.targetedShip);
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
        if (parseInt(sMove.heading, 10) !== fHeading) continue;
        if (Math.abs(parseInt(sMove.speed, 10) - fSpeed) > thrust) continue;
        if (shipManager.movement.isRolling(ship)) continue;
        if (shipManager.movement.isPivoting && shipManager.movement.isPivoting(ship) !== "no") continue;

        var hangarsOnShip = collectReceivingHangars(ship, category);
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

    function collectReceivingHangars(ship, category) {
        var hangars = [];
        var flightId = parseInt(flight.id, 10);
        ship.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (shipManager.systems.isDestroyed(ship, sys)) return;

            if (!hangarAcceptsCategory(sys.hangarType, category, ship)) return;

            // Effective free boxes: maxhealth - net damage - usage.
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
            var free = Math.max(0, effective - used);

            var output = parseInt(sys.output || 0, 10);
            var spent = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
            var budget = Math.max(0, output - spent);

            // Subtract queued allocations from OTHER flights/launches on this
            // hangar (shared output budget + physical free boxes). Skip this
            // flight's OWN dock orders so re-editing/cancelling sees the full
            // capacity it had before queuing — the dialog re-applies the
            // queued amount as a pre-fill on the splitter input instead.
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === flightId) return;   //own queue is reclaimable
                    var n = parseInt(o.count || 0, 10);
                    free   = Math.max(0, free - n);
                    budget = Math.max(0, budget - n);
                });
            }
            if (Array.isArray(sys.pendingLaunchOrders)) {
                sys.pendingLaunchOrders.forEach(function (o) {
                    budget = Math.max(0, budget - parseInt(o.size || 0, 10));
                });
            }

            var capacity = Math.min(free, budget);
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
        if (parseInt(fMove.heading, 10) !== cHeading) continue;

        var thrust = parseInt(flight.freethrust || 0, 10);
        if (Math.abs(parseInt(fMove.speed, 10) - cSpeed) > thrust) continue;

        var hangars = collectReceivingHangarsForRecover(carrier, flight);
        if (hangars.length === 0) continue;

        out.push({ flight: flight, hangars: hangars });
    }
    return out;

    // Same shape as collectReceivingHangars in findEligibleCarriersForDock,
    // but returns only hangars whose capacity holds the FULL active flight —
    // the bulk-recover dialog doesn't split a flight across hangars (splitter
    // remains on the per-flight Dock dialog).
    function collectReceivingHangarsForRecover(ship, flight) {
        var hangars = [];
        var flightId = parseInt(flight.id, 10);
        var category = categoryForFlightRecover(flight);
        var size = countActiveInFlight(flight);
        if (size <= 0) return hangars;

        // Stage 10.6.2: bulk recover only ever docks a FULL flight into a
        // single hangar — if the carrier's customFighter cap can't hold the
        // whole flight, the flight isn't eligible at all.
        var customName = String(flight.customFtrName || '');
        if (customName !== '') {
            var cap = customFighterRemainingForRecover(ship, customName, flightId);
            if (cap < size) return hangars;
        }

        ship.systems.forEach(function (sys) {
            if (!sys || sys.name !== 'hangar') return;
            if (shipManager.systems.isDestroyed(ship, sys)) return;
            if (!hangarAcceptsCategoryRecover(sys.hangarType, category, ship)) return;

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
            var free = Math.max(0, effective - used);

            var output = parseInt(sys.output || 0, 10);
            var spent  = parseInt(sys.launchedThisTurn || 0, 10) + parseInt(sys.landedThisTurn || 0, 10);
            var budget = Math.max(0, output - spent);

            // OTHER flights' queued docks consume both free boxes and the
            // shared launch+land budget. THIS flight's own queue is
            // reclaimable so the row's hangar dropdown can re-pick it.
            if (Array.isArray(sys.pendingDockOrders)) {
                sys.pendingDockOrders.forEach(function (o) {
                    if (parseInt(o.flightId, 10) === flightId) return;
                    var n = parseInt(o.count || 0, 10);
                    free   = Math.max(0, free - n);
                    budget = Math.max(0, budget - n);
                });
            }
            // Queued launch orders consume budget only (no physical boxes since
            // the craft are leaving).
            if (Array.isArray(sys.pendingLaunchOrders)) {
                sys.pendingLaunchOrders.forEach(function (o) {
                    budget = Math.max(0, budget - parseInt(o.size || 0, 10));
                });
            }
            var capacity = Math.min(free, budget);
            if (capacity >= size) hangars.push({ hangar: sys, capacity: capacity });
        });
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