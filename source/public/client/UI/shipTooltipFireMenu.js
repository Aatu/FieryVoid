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
        // Mirrors HangarOps::isFlowEnabled — gated to safeGameID 3730 + local
        // dev games (id <= 0). Stage 9 removes this gate.
        var SAFE_GAME_ID = 3730;
        var gid = parseInt(gamedata.gameid || 0, 10);
        return gid <= 0 || gid >= SAFE_GAME_ID;
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

            if (!hangarAcceptsCategory(sys.hangarType, category)) return;

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
        return hangars;
    }

    // Mirrors HangarOps::hangarAcceptsCategory (PHP) — combat-fighter size
    // hierarchy plus shuttle/BP compatibility. Keep in sync with the server
    // helper so the eligibility gate matches end-of-turn validation.
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
};