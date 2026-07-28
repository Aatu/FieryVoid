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

        var myTeam = window.gamedata.getPlayerTeam();

        /*VIEWER NOT IN THE GAME (2026-07-23 user report: "all windows open on the right and
          only one can be open at once"). getPlayerTeam() returns UNDEFINED whenever no slot
          matches the viewer - a spectator, an admin looking at someone else's game, or a
          replay whose thisplayer never got set (gamedata.js:1987 skips the assignment when
          gamedata.replay). Every ship then failed `ship.team === myTeam`, so every window
          docked RIGHT and one-window-per-side collapsed to a single window.
          Mirror gamedata.isMyOrTeamOneShip (gamedata.js): in game, own team is left; with no
          viewer team, team 1 is the left/first side. Keeping the two in step means the
          window docks on the side its team colour already implies.
          Loose == throughout (as getPlayerTeam's own slot test uses): team ids arrive as
          JSON and a string/int drift must never re-split the windows.*/
        if (myTeam === undefined || myTeam === null) {
            return ship.team == 1;
        }

        return ship.team == myTeam;
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
