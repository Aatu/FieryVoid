"use strict";

window.ShipWindowManager = (function(){

    function ShipWindowManager(uiManager){
        this.uiManager = uiManager;
        this.ships = [];
    }

    ShipWindowManager.prototype.open = function (ship) {
        if (!this.ships.includes(ship)){
            this.ships.push(ship);
        }

        this.uiManager.renderShipWindows({ships: this.ships});
    }

    ShipWindowManager.prototype.close = function (ship) {
        this.ships = this.ships.filter(function(openShip) {
            return openShip !== ship;
        })

        this.uiManager.renderShipWindows({ships: this.ships});
    }

    ShipWindowManager.prototype.update = function () {
        this.uiManager.renderShipWindows({ships: this.ships});
    }

    return ShipWindowManager;
})();