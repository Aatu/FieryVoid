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
        { className: "targetSuppWeapons", condition: [isFriendly, hasWeaponsSelected, hasSupportWeaponSelected, notSelf], action: targetWeapons, info: "Target support weapons" }//30 June 2024 - DK - Added for Ally targeting.	
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

    function isEnemy() {
        //return this.selectedShip && !gamedata.isMyShip(this.targetedShip);
		//actually enemy is anyone not on players' TEAM, not every other player:
		return this.selectedShip && !gamedata.isMyOrTeamOneShip(this.targetedShip);
    }

    function isFriendly() {//30 June 2024 - DK - Added for Ally targeting.
        return gamedata.isMyShip(this.targetedShip);
    }

    function notSelf() {//30 June 2024 - DK - Added for Ally targeting.
        return this.selectedShip !== this.targetedShip;
    }

    function hasWeaponsSelected() {
        return gamedata.selectedSystems.length > 0;
    }
	
    function hasHexWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === false;
            return system instanceof Weapon && system.hextarget === true;
        });
    }

	function hasSupportWeaponSelected() {//30 June 2024 - DK - Added for Ally targeting.
	    return gamedata.selectedSystems.some((system) => {
	        return system.canTargetAllies === true;
	    });
	}

    return ShipTooltipFireMenu;
}();