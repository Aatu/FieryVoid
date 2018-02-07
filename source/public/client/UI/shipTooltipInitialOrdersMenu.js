window.ShipTooltipInitialOrdersMenu = (function(){

    function ShipTooltipInitialOrdersMenu(selectedShip, targetedShip, turn) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
    }

    ShipTooltipInitialOrdersMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipInitialOrdersMenu.buttons = [
        {className: "addCCEW",       condition: [isSelf, notFlight],                action: addCCEW, info: "Add CCEW"},
        {className: "removeCCEW",    condition: [isSelf, notFlight],                action: removeCCEW, info: "Remove CCEW"},
        {className: "addOEW",        condition: [isEnemy, notFlight],               action: addOEW, info: "Add OEW"},
        {className: "removeOEW",     condition: [isEnemy, notFlight],               action: removeOEW, info: "Remove OEW"},
        {className: "addDIST",       condition: [isEnemy, isElint, notFlight],      action: addOEW.bind(null, 'DIST'), info: "Add DIST"},
        {className: "removeDIST",    condition: [isEnemy, isElint, notFlight],      action: removeOEW.bind(null, 'DIST'), info: "Remove DIST"},
        {className: "addSOEW",       condition: [isEnemy, isElint, notFlight],      action: addOEW.bind(null, 'SOEW'), info: "Add SOEW"},
        {className: "removeSOEW",    condition: [isEnemy, isElint, notFlight],      action: removeOEW.bind(null, 'SOEW'), info: "Remove SOEW"},
        {className: "addSDEW",       condition: [isFriendly, isElint, notFlight],   action: addOEW.bind(null, 'SDEW'), info: "Add SDEW"},
        {className: "removeSDEW",    condition: [isFriendly, isElint, notFlight],   action: removeOEW.bind(null, 'SDEW'), info: "Remove SDEW"},
        {className: "addBDEW",       condition: [isSelf, isElint, notFlight],       action: addBDEW, info: "add BDEW"},
        {className: "removeBDEW",    condition: [isSelf, isElint, notFlight],       action: removeBDEW, info: "remove BDEW"},
        {className: "targetWeapons", condition: [isEnemy, hasWeaponsSelected],      action: targetWeapons, info: "Target selected weapons"},
    ];



    ShipTooltipInitialOrdersMenu.prototype.getAllButtons = function() {
        return ShipTooltipInitialOrdersMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };


    function targetWeapons(){
        weaponManager.targetShip(this.selectedShip, this.targetedShip);
    }

    function addCCEW() {
        var entry = ew.getEntryByTargetAndType(this.selectedShip, null, "CCEW", this.turn);

        if (! entry) {
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

        if (! entry) {
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

    function addOEW(type) {

        if (!type) {
            type = "OEW";
        }

        var entry = ew.getEntryByTargetAndType(this.selectedShip, this.targetedShip, type, this.turn);

        if (! entry) {
            ew.AssignOEW(this.selectedShip, this.targetedShip, type);
        } else {
            ew.assignEW(this.selectedShip, entry);
        }
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

    function isEnemy() {
        return this.selectedShip && !gamedata.isMyShip(this.targetedShip);
    }

    function isFriendly() {
        return gamedata.isMyShip(this.targetedShip);
    }

    function hasWeaponsSelected() {
        return gamedata.selectedSystems.length > 0;
    }

    function isElint() {
        return this.selectedShip && shipManager.isElint(this.selectedShip)
    }

    function notFlight() {
        return (!this.selectedShip || !this.selectedShip.flight) && (!this.targetedShip || !this.targetedShip.flight);
    }

    return ShipTooltipInitialOrdersMenu;
})();