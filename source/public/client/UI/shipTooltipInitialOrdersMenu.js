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
        { className: "addOEW", condition: [isEnemy], action: addOEW, info: "Add OEW" }, 
        { className: "removeOEW", condition: [isEnemy], action: removeOEW, info: "Remove OEW" }, 
        { className: "addDIST", condition: [isEnemy, isElint, notFlight, isInElintDistance(50), doesNotHaveBDEW], action: getAddOEW('DIST'), info: "Add DIST" }, 
        { className: "removeDIST", condition: [isEnemy, isElint, notFlight, isInElintDistance(50), doesNotHaveBDEW], action: getRemoveOEW('DIST'), info: "Remove DIST" }, 
        { className: "addSOEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SOEW'), info: "Add SOEW" }, 
        { className: "removeSOEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getRemoveOEW('SOEW'), info: "Remove SOEW" }, 
        { className: "addSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SDEW'), info: "Add SDEW" }, 
        { className: "removeSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getRemoveOEW('SDEW'), info: "Remove SDEW" }, 
        { className: "addBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: addBDEW, info: "add BDEW" }, 
        { className: "removeBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: removeBDEW, info: "remove BDEW" }, 
        { className: "targetWeapons", condition: [isEnemy, hasShipWeaponsSelected], action: targetWeapons, info: "Target selected weapons on ship" }, 
        { className: "targetWeaponsHex", condition: [hasHexWeaponsSelected], action: targetHexagon, info: "Target selected weapons on hexagon" }
    ];
    /*lines replaced:    
        { className: "addOEW", condition: [isEnemy, notFlight], action: addOEW, info: "Add OEW" }, 
        { className: "removeOEW", condition: [isEnemy, notFlight], action: removeOEW, info: "Remove OEW" }, 
    */
    

    ShipTooltipInitialOrdersMenu.prototype.getAllButtons = function () {
        return ShipTooltipInitialOrdersMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

    function hasShipWeaponsSelected() {
        return gamedata.selectedSystems.some(function (system) {
            //return system instanceof Weapon && system.targetsShips === true;
            return system instanceof Weapon && system.hextarget !== true;
        });
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

    function isInElintDistance(distance) {
        return function () {
            return ew.checkInELINTDistance(this.selectedShip, this.targetedShip, distance);
        };
    }

    function doesNotHaveBDEW() {
        return ew.getEWByType("BDEW", this.selectedShip) === 0;
    }

    function doesNotHaveOtherElintEWThanBDEW() {
        return ew.getEWByType("SDEW", this.selectedShip) === 0 && ew.getEWByType("DIST", this.selectedShip) === 0 && ew.getEWByType("SOEW", this.selectedShip) === 0;
    }

    return ShipTooltipInitialOrdersMenu;
}();
