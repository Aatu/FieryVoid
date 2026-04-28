"use strict";

window.ShipTooltipInitialOrdersMenu = function () {

    function ShipTooltipInitialOrdersMenu(selectedShip, targetedShip, turn, hexagon) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
        this.hexagon = hexagon;
    }

    ShipTooltipInitialOrdersMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipInitialOrdersMenu.buttons = [
        { className: "addCCEW", condition: [isSelf, notFlight, notMine], action: addCCEW, info: "Add CCEW (right-click: max)", supportsMaxClick: true },
        { className: "removeCCEW", condition: [isSelf, notFlight, notMine], action: removeCCEW, info: "Remove CCEW (right-click: clear)", supportsMaxClick: true },
        { className: "addOEW", condition: [notSelf, isEnemyEW, sourceNotFlight], action: addOEW, info: "Add OEW (right-click: max)", supportsMaxClick: true },
        { className: "removeOEW", condition: [notSelf, isEnemyEW, sourceNotFlight], action: removeOEW, info: "Remove OEW (right-click: clear)", supportsMaxClick: true },
        { className: "addMDEW", condition: [isSelf, enemyMines], action: addMDEW, info: "Add Mine Detection (right-click: max)", supportsMaxClick: true },
        { className: "removeMDEW", condition: [isSelf, enemyMines], action: removeMDEW, info: "Remove Mine Detection (right-click: clear)", supportsMaxClick: true },
        { className: "addDIST", condition: [notSelf, isEnemyEW, isElint, notFlight, notMine, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck], action: getAddOEW('DIST'), info: "Add DIST (right-click: max)", supportsMaxClick: true },
        { className: "removeDIST", condition: [notSelf, isEnemyEW, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck, hasDIST], action: getRemoveOEW('DIST'), info: "Remove DIST (right-click: clear)", supportsMaxClick: true },
        //{ className: "addOEW", condition: [notSelf, sourceNotFlight], action: addOEW, info: "Add OEW" },
        //{ className: "removeOEW", condition: [notSelf, sourceNotFlight], action: removeOEW, info: "Remove OEW" },
        //{ className: "addDIST", condition: [notSelf, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck], action: getAddOEW('DIST'), info: "Add DIST" },
        //{ className: "removeDIST", condition: [notSelf, isElint, notFlight, isInElintDistance(30), doesNotHaveBDEW, advSensorsCheck, hasDIST], action: getRemoveOEW('DIST'), info: "Remove DIST" },
        { className: "addSOEW", condition: [isFriendly, isElint, notFlight, notMine, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SOEW'), info: "Add SOEW", supportsMaxClick: true },
        { className: "removeSOEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW, hasSOEW], action: getRemoveOEW('SOEW'), info: "Remove SOEW (right-click: clear)", supportsMaxClick: true },
        { className: "addSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW], action: getAddOEW('SDEW'), info: "Add SDEW (right-click: max)", supportsMaxClick: true },
        { className: "removeSDEW", condition: [isFriendly, isElint, notFlight, notSelf, isInElintDistance(30), doesNotHaveBDEW, hasSDEW], action: getRemoveOEW('SDEW'), info: "Remove SDEW (right-click: clear)", supportsMaxClick: true },
        { className: "addBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: addBDEW, info: "Add BDEW (right-click: max)", supportsMaxClick: true },
        { className: "removeBDEW", condition: [isSelf, isElint, notFlight, doesNotHaveOtherElintEWThanBDEW], action: removeBDEW, info: "Remove BDEW (right-click: clear)", supportsMaxClick: true },
        { className: "addDetectSEW", condition: [isSelf, isElint, notFlight, doesNotHaveBDEW, enemyStealth], action: addDetectSEW, info: "Add Detect Stealth (right-click: max)", supportsMaxClick: true },
        { className: "removeDetectSEW", condition: [isSelf, isElint, notFlight, doesNotHaveBDEW, enemyStealth], action: removeDetectSEW, info: "Remove Detect Stealth (right-click: clear)", supportsMaxClick: true },
        { className: "removeAllEW", condition: [isSelf, notFlight, notMine], action: removeAllEW, info: "Remove All EW" },
        { className: "targetWeapons", condition: [isEnemy, hasShipWeaponsSelected], action: targetWeapons, info: "Target selected weapons on ship" },
        { className: "targetWeaponsHex", condition: [hasHexWeaponsSelected], action: targetHexagon, info: "Target selected weapons on hexagon" },
        { className: "targetSuppWeapons", condition: [isFriendly, hasShipWeaponsSelected, FFWeaponSelected, notSelf], action: targetWeapons, info: "Target support weapons" },//30 June 2024 - DK - Added for Ally targeting.
        { className: "removeMultiOrder", condition: [hasShipWeaponsSelected, hasSplitWeaponFiringOrder], action: removeFiringOrderMulti, info: "Remove a Firing Order" }				        
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

    function removeFiringOrderMulti() {
        // Loop through selected systems and check for systems that have canSplitShots set to true
        gamedata.selectedSystems.forEach(function (system) {
            if (system.canSplitShots) {
                // Call weaponManager.removeFiringOrderMulti for each system that meets the condition
                weaponManager.removeFiringOrderMulti(this.selectedShip, system, this.targetedShip, true);
            }
        }, this); // Make sure to bind `this` so that `this.selectedShip` is correct
    }

    function FFWeaponSelected() {
        if (gamedata.rules && gamedata.rules.friendlyFire === 1) return true; //To let ballistics target

        return gamedata.selectedSystems.some(system => {
            return system.canTargetAllies === true || system.canTargetAll === true;
        });
    }

    function addSelfEW(ewType, isMaxClick) {
        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, null, ewType, this.turn);
            var before = ew.getEWLeft(this.selectedShip);
            if (!entry) {
                ew.assignEW(this.selectedShip, ewType);
            } else {
                ew.assignEW(this.selectedShip, entry);
            }
            if (!isMaxClick) return;
            if (ew.getEWLeft(this.selectedShip) >= before) return;
        } while (ew.getEWLeft(this.selectedShip) > 0);
    }

    function removeSelfEW(ewType, isMaxClick) {
        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, null, ewType, this.turn);
            if (!entry) return;
            ew.deassignEW(this.selectedShip, entry);
        } while (isMaxClick);
    }

    function addCCEW(isMaxClick) { addSelfEW.call(this, "CCEW", isMaxClick); }
    function removeCCEW(isMaxClick) { removeSelfEW.call(this, "CCEW", isMaxClick); }
    function addMDEW(isMaxClick) { addSelfEW.call(this, "Detect Mines", isMaxClick); }
    function removeMDEW(isMaxClick) { removeSelfEW.call(this, "Detect Mines", isMaxClick); }
    function addBDEW(isMaxClick) { addSelfEW.call(this, "BDEW", isMaxClick); }
    function removeBDEW(isMaxClick) { removeSelfEW.call(this, "BDEW", isMaxClick); }
    function addDetectSEW(isMaxClick) { addSelfEW.call(this, "Detect Stealth", isMaxClick); }
    function removeDetectSEW(isMaxClick) { removeSelfEW.call(this, "Detect Stealth", isMaxClick); }

    function removeAllEW() {
        ew.removeEW(this.selectedShip);
    }

    function getAddOEW(type) {
        return function (isMaxClick) {
            addOEW.call(this, type, isMaxClick);
        };
    }

    function addOEW(type, isMaxClick) {
        if (!type) {
            type = "OEW";
        }

        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);
            var before = ew.getEWLeft(this.selectedShip);
            if (!entry) {
                ew.AssignOEW(this.selectedShip, this.targetedShip, type);
            } else {
                ew.assignEW(this.selectedShip, entry);
            }
            if (!isMaxClick) return;
            if (ew.getEWLeft(this.selectedShip) >= before) return;
        } while (ew.getEWLeft(this.selectedShip) > 0);
    }

    function getRemoveOEW(type) {
        return function (isMaxClick) {
            removeOEW.call(this, type, isMaxClick);
        };
    }

    function removeOEW(type, isMaxClick) {
        if (!type) {
            type = "OEW";
        }

        do {
            var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);
            if (!entry) return;
            ew.deassignEW(this.selectedShip, entry);
        } while (isMaxClick);
    }

    function isSelf() {
        return this.selectedShip === this.targetedShip;
    }

    function notSelf() {
        return this.selectedShip !== this.targetedShip;
    }

    function isEnemy() {
        return this.selectedShip && !gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function isEnemyEW() {
        if (gamedata.rules && gamedata.rules.friendlyFire === 1) return true;
        if (shipManager.hasSpecialAbility(this.selectedShip, "alliedEW")) return true;

        var hexWeaponSelected = gamedata.selectedSystems.some(function (system) {
            return system instanceof Weapon && system.hextarget === true;
        });

        if (hexWeaponSelected) return true;

        return this.selectedShip && !gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function isFriendly() {
        return gamedata.isMyorMyTeamShip(this.targetedShip);
    }

    function isElint() {
        return this.selectedShip && shipManager.isElint(this.selectedShip);
    }

    function notFlight() {
        return (!this.selectedShip || !this.selectedShip.flight) && (!this.targetedShip || !this.targetedShip.flight);
    }

    function notMine() {
        return (!this.selectedShip || !this.selectedShip.mine) && (!this.targetedShip || !this.targetedShip.mine);
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

    function enemyStealth() {
        return gamedata.isStealthPresent;
    } 
    
    function enemyMines() {
        return gamedata.areMinesPresent;
    }     

    function doesNotHaveOtherElintEWThanBDEW() {
        return ew.getEWByType("SDEW", this.selectedShip) === 0 && ew.getEWByType("DIST", this.selectedShip) === 0 && ew.getEWByType("SOEW", this.selectedShip) === 0 && ew.getEWByType("Detect Stealth", this.selectedShip) === 0;
    }

    function hasOEW() { return ew.getEWByType("OEW", this.selectedShip, this.targetedShip) > 0; }
    function hasCCEW() { return ew.getEWByType("CCEW", this.selectedShip) > 0; }
    function hasSDEW() { return ew.getEWByType("SDEW", this.selectedShip, this.targetedShip) > 0; }
    function hasSOEW() { return ew.getEWByType("SOEW", this.selectedShip, this.targetedShip) > 0; }
    function hasBDEW() { return ew.getEWByType("BDEW", this.selectedShip) > 0; }
    function hasDIST() { return ew.getEWByType("DIST", this.selectedShip, this.targetedShip) > 0; }
    function hasDSEW() { return ew.getEWByType("Detect Stealth", this.selectedShip) > 0; }

    function advSensorsCheck() { /*check whether source ship has Advanced Sensors OR target ship does NOT have Advanced Sensors*/
        return (shipManager.hasSpecialAbility(this.selectedShip, "AdvancedSensors") || (!(shipManager.hasSpecialAbility(this.targetedShip, "AdvancedSensors"))))
    }

    return ShipTooltipInitialOrdersMenu;
}();
