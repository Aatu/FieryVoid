"use strict";

window.ShipTooltipFireMenu = function () {

    function ShipTooltipFireMenu(selectedShip, targetedShip, turn) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
		var movement = shipManager.movement.getLastCommitedMove(targetedShip);
        this.hexagon = new hexagon.Offset(movement.position);
    }

    ShipTooltipFireMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipFireMenu.buttons = [
		{ className: "targetWeapons", condition: [isEnemy, hasWeaponsSelected], action: targetWeapons, info: "Target selected weapons" },
        { className: "targetWeaponsHex", condition: [hasHexWeaponsSelected], action: targetHexagon, info: "Target selected weapons on hexagon" },
        { className: "targetSuppWeapons", condition: [isFriendly, hasWeaponsSelected, FFWeaponSelected, notSelf], action: targetWeapons, info: "Target support weapons" },//30 June 2024 - DK - Added for Ally targeting.
        { className: "removeMultiOrder", condition: [isEnemy, hasWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firing Order" },
        { className: "launchFighters", condition: [isMine, hasLaunchableHangar, isLaunchEnabledGame, carrierNotPivotingOrRolling], action: openHangarLaunch, info: "Launch fighters/shuttles from hangar (Stage 4 — gated to safeGameID)" }
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

    return ShipTooltipFireMenu;
}();