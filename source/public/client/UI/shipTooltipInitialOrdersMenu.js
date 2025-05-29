"use strict";

window.ShipTooltipInitialOrdersMenu = function () {

    function ShipTooltipInitialOrdersMenu(selectedShip, targetedShip, turn, hexagon) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
        this.hexagon = hexagon;
    }

    ShipTooltipInitialOrdersMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipInitialOrdersMenu.buttons = [
        { className: "addCCEW", condition: [isSelf, notFlight], action: addCCEW, info: "Add CCEW" }, 
        { className: "removeCCEW", condition: [isSelf, notFlight], action: removeCCEW, info: "Remove CCEW" }, 
        { className: "addOEW", condition: [isEnemy, sourceNotFlight], action: addOEW, info: "Add OEW" }, 
        { className: "removeOEW", condition: [isEnemy, sourceNotFlight], action: removeOEW, info: "Remove OEW" }, 
        { className: "addDIST", condition: [isEnemy, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck], action: getAddOEW('DIST'), info: "Add DIST" }, 
        { className: "removeDIST", condition: [isEnemy, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck, hasDIST], action: getRemoveOEW('DIST'), info: "Remove DIST" }, 
        { className: "addSOEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SOEW'), info: "Add SOEW" }, 
        { className: "removeSOEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW, hasSOEW], action: getRemoveOEW('SOEW'), info: "Remove SOEW" }, 
        { className: "addSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SDEW'), info: "Add SDEW" }, 
        { className: "removeSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW, hasSDEW], action: getRemoveOEW('SDEW'), info: "Remove SDEW" }, 
        { className: "addBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: addBDEW, info: "Add BDEW" }, 
        { className: "removeBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW, hasBDEW], action: removeBDEW, info: "Remove BDEW" },
        { className: "addDetectSEW", condition: [isSelf, isElint, notFlight, doesNotHaveBDEW], action: addDetectSEW, info: "Add Detect Stealth" }, 
        { className: "removeDetectSEW", condition: [isSelf, isElint, notFlight, doesNotHaveBDEW, hasDSEW], action: removeDetectSEW, info: "Remove Detect Stealth" },    
        { className: "removeAllEW", condition: [isSelf, notFlight], action: removeAllEW, info: "Remove All EW" }, 
        { className: "targetWeapons", condition: [isEnemy, hasShipWeaponsSelected], action: targetWeapons, info: "Target selected weapons on ship" }, 
        { className: "targetWeaponsHex", condition: [hasHexWeaponsSelected], action: targetHexagon, info: "Target selected weapons on hexagon" },
		{ className: "targetSuppWeapons", condition: [isFriendly, hasShipWeaponsSelected, hasSupportWeaponSelected, notSelf], action: targetWeapons, info: "Target support weapons" },//30 June 2024 - DK - Added for Ally targeting.
        { className: "removeMultiOrder", condition: [isEnemy, hasShipWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firig Order" } 				        
    ];
    

    ShipTooltipInitialOrdersMenu.prototype.getAllButtons = function () {
        return ShipTooltipInitialOrdersMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

    function hasShipWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === true;
            return system instanceof Weapon && system.hextarget !== true;
        });
    }

	function hasSplitWeaponFiringOrder() {
	    return gamedata.selectedSystems.some(function (system) {
	        return system instanceof Weapon && system.canSplitShots && weaponManager.hasTargetedThisShip(this.targetedShip, system);
	    }.bind(this)); // Bind `this` to the callback
	}

    function hasHexWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === false;
            return system instanceof Weapon && system.hextarget === true;
        });
    }

    function targetWeapons() {
        weaponManager.targetShip(this.selectedShip, this.targetedShip);
    }

    function targetHexagon() {
        weaponManager.targetHex(this.selectedShip, this.hexagon);
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

	function hasSupportWeaponSelected() {//30 June 2024 - DK - Added for Split targeting.	
	    return gamedata.selectedSystems.some((system) => {
	        return system.canTargetAllies === true;
	    });
	}

    function addCCEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "CCEW", this.turn);

        if (!entry) {
            ew.assignEW(this.selectedShip, "CCEW");
        } else {
            ew.assignEW(this.selectedShip, entry);
        }
    }

    function removeCCEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "CCEW", this.turn);
        if (!entry) return;
        ew.deassignEW(this.selectedShip, entry);
    }

    function addBDEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "BDEW", this.turn);

        if (!entry) {
            ew.assignEW(this.selectedShip, "BDEW");
        } else {
            ew.assignEW(this.selectedShip, entry);
        }
    }

    function removeBDEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "BDEW", this.turn);
        if (!entry) return;
        ew.deassignEW(this.selectedShip, entry);
    }

    function addDetectSEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "Detect Stealth", this.turn);

        if (!entry) {
            ew.assignEW(this.selectedShip, "Detect Stealth");
        } else {
            ew.assignEW(this.selectedShip, entry);
        }
    }

    function removeDetectSEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "Detect Stealth", this.turn);
        if (!entry) return;
        ew.deassignEW(this.selectedShip, entry);
    }    
    
    function removeAllEW() {
        ew.removeEW(this.selectedShip);
    }

    function getAddOEW(type) {
        return function () {
            addOEW.call(this, type);
        };
    }

    function addOEW(type) {

        if (!type) {
            type = "OEW";
        }

        var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);

        if (!entry) {
            ew.AssignOEW(this.selectedShip, this.targetedShip, type);
        } else {
            ew.assignEW(this.selectedShip, entry);
        }
    }

    function getRemoveOEW(type) {
        return function () {
            removeOEW.call(this, type);
        };
    }

    function removeOEW(type) {

        if (!type) {
            type = "OEW";
        }

        var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);
        if (!entry) return;
        ew.deassignEW(this.selectedShip, entry);
    }

    function isSelf() {
        return this.selectedShip === this.targetedShip;
    }

    function notSelf() {
        return this.selectedShip !== this.targetedShip;
    }

    function isEnemy() {
        return this.selectedShip && !gamedata.isMyShip(this.targetedShip);
    }

    function isFriendly() {
        return gamedata.isMyShip(this.targetedShip);
    }

    function isElint() {
        return this.selectedShip && shipManager.isElint(this.selectedShip);
    }

    function notFlight() {
        return (!this.selectedShip || !this.selectedShip.flight) && (!this.targetedShip || !this.targetedShip.flight);
    }
	
    function sourceNotFlight() {
        return (!this.selectedShip || !this.selectedShip.flight);
    }
	
    function targetNotFlight() {
        return (!this.targetedShip || !this.targetedShip.flight);
    }

    function isInElintDistance(distance) {
        return function () {
            return ew.checkInELINTDistance(this.selectedShip, this.targetedShip, distance);
        };
    }

    function doesNotHaveBDEW() {
        return ew.getEWByType("BDEW", this.selectedShip) === 0;
    }

    function doesNotHaveOtherElintEWThanBDEW() {
        return ew.getEWByType("SDEW", this.selectedShip) === 0 && ew.getEWByType("DIST", this.selectedShip) === 0 && ew.getEWByType("SOEW", this.selectedShip) === 0 && ew.getEWByType("Detect Stealth", this.selectedShip) === 0;
    }

    function hasOEW() { return ew.getEWByType("OEW", this.selectedShip) > 0; }
    function hasCCEW() { return ew.getEWByType("CCEW", this.selectedShip) > 0; }
    function hasSDEW() { return ew.getEWByType("SDEW", this.selectedShip) > 0; }
    function hasSOEW() { return ew.getEWByType("SOEW", this.selectedShip) > 0; }
    function hasBDEW() { return ew.getEWByType("BDEW", this.selectedShip) > 0; }
    function hasDIST() { return ew.getEWByType("DIST", this.selectedShip) > 0; }
    function hasDSEW() { return ew.getEWByType("Detect Stealth", this.selectedShip) > 0; }
		
    function advSensorsCheck() { /*check whether source ship has Advanced Sensors OR target ship does NOT have Advanced Sensors*/
	return ( shipManager.hasSpecialAbility(this.selectedShip, "AdvancedSensors") || (!(shipManager.hasSpecialAbility(this.targetedShip, "AdvancedSensors"))) ) 
    }

    return ShipTooltipInitialOrdersMenu;
}();
