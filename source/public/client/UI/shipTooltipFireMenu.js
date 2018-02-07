window.ShipTooltipFireMenu = (function(){

    function ShipTooltipFireMenu(selectedShip, targetedShip, turn) {
        ShipTooltipMenu.call(this, selectedShip, targetedShip, turn);
    }

    ShipTooltipFireMenu.prototype = Object.create(ShipTooltipMenu.prototype);

    ShipTooltipFireMenu.buttons = [
        {className: "targetWeapons", condition: [isEnemy, hasWeaponsSelected],      action: targetWeapons, info: "Target selected weapons"},
    ];

    ShipTooltipFireMenu.prototype.getAllButtons = function() {
        return ShipTooltipFireMenu.buttons.concat(ShipTooltipMenu.prototype.getAllButtons.call(this));
    };

    function targetWeapons(){
        weaponManager.targetShip(this.selectedShip, this.targetedShip);
    }

    function isEnemy() {
        return this.selectedShip && !gamedata.isMyShip(this.targetedShip);
    }

    function hasWeaponsSelected() {
        return gamedata.selectedSystems.length > 0;
    }

    return ShipTooltipFireMenu;
})();