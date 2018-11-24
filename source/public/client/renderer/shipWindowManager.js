"use strict";

window.ShipWindowManager = (function() {
  function ShipWindowManager(uiManager, movementService) {
    this.uiManager = uiManager;
    this.movementService = movementService;
    this.ships = [];
  }

  ShipWindowManager.prototype.open = function(ship) {
    this.ships = this.ships.filter(function(otherShip) {
      return otherShip.team != ship.team;
    });

    if (!this.ships.includes(ship)) {
      this.ships.push(ship);
    }

    this.uiManager.renderShipWindows({
      ships: this.ships,
      movementService: this.movementService
    });
  };

  ShipWindowManager.prototype.close = function(ship) {
    this.ships = this.ships.filter(function(openShip) {
      return openShip !== ship;
    });

    this.uiManager.renderShipWindows({
      ships: this.ships,
      movementService: this.movementService
    });
  };

  ShipWindowManager.prototype.closeAll = function(ship) {
    this.ships = [];

    this.uiManager.renderShipWindows({
      ships: this.ships,
      movementService: this.movementService
    });
  };

  ShipWindowManager.prototype.update = function() {
    this.uiManager.renderShipWindows({
      ships: this.ships,
      movementService: this.movementService
    });
  };

  return ShipWindowManager;
})();
