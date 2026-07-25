"use strict";

window.ShipWindowManager = (function () {

    /*Which side of the screen a ship's window docks to (true = left). One window may
      be open per side; opening a ship on an occupied side replaces that window.

      - game.php / replay: own team left, enemy right (unchanged behaviour).
      - gamelobby.php (Stage 3, gamephase -2): store blueprints (userid 0) left,
        own-fleet ships right - the same split the legacy lobby window used.

      Exposed as ShipWindowManager.isLeftSide so the React ShipWindow can place its
      container on the same side the filter reserves (single source of truth).*/
    function isLeftSide(ship) {
        if (window.gamedata && window.gamedata.gamephase === -2) {
            return ship.userid == 0;
        }
        return ship.team === window.gamedata.getPlayerTeam();
    }

    function ShipWindowManager(uiManager) {
        this.uiManager = uiManager;
        this.ships = [];
    }

    ShipWindowManager.isLeftSide = isLeftSide;

    ShipWindowManager.prototype.open = function (ship) {
        var isNewShipLeft = isLeftSide(ship);

        this.ships = this.ships.filter(function (otherShip) {
            return isLeftSide(otherShip) !== isNewShipLeft;
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
