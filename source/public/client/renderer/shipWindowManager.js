"use strict";

window.ShipWindowManager = (function () {

    function ShipWindowManager(uiManager) {
        this.uiManager = uiManager;
        this.ships = [];
    }

    ShipWindowManager.prototype.open = function (ship) {
        var myTeam = window.gamedata.getPlayerTeam();
        var isNewShipMyTeam = (ship.team === myTeam);

        this.ships = this.ships.filter(function (otherShip) {
            var isOtherShipMyTeam = (otherShip.team === myTeam);
            return isOtherShipMyTeam !== isNewShipMyTeam;
        })

        if (!this.ships.includes(ship)) {
            this.ships.push(ship);
        }

        this.uiManager.renderShipWindows({ ships: this.ships });
    }

    ShipWindowManager.prototype.close = function (ship) {
        this.ships = this.ships.filter(function (openShip) {
            return openShip !== ship;
        })

        this.uiManager.renderShipWindows({ ships: this.ships });
    }

    ShipWindowManager.prototype.closeAll = function (ship) {
        this.ships = []

        this.uiManager.renderShipWindows({ ships: this.ships });
    }

    ShipWindowManager.prototype.update = function () {
        this.uiManager.renderShipWindows({ ships: this.ships });
    }

    return ShipWindowManager;
})();