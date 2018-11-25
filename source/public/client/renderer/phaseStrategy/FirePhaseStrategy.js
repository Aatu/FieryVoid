"use strict";

window.FirePhaseStrategy = (function() {
  function FirePhaseStrategy(coordinateConverter) {
    PhaseStrategy.call(this, coordinateConverter);
    this.animationStrategy = new window.IdleAnimationStrategy();

    this.deploymentSprites = [];
  }

  FirePhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

  FirePhaseStrategy.prototype.activate = function(
    shipIcons,
    ewIconContainer,
    ballisticIconContainer,
    gamedata,
    webglScene,
    shipWindowManager,
    movementService
  ) {
    this.changeAnimationStrategy(
      new window.IdleAnimationStrategy(shipIcons, gamedata.turn)
    );

    PhaseStrategy.prototype.activate.call(
      this,
      shipIcons,
      ewIconContainer,
      ballisticIconContainer,
      gamedata,
      webglScene,
      shipWindowManager,
      movementService
    );

    infowindow.informPhase(5000, null);
    this.selectFirstOwnShipOrActiveShip();

    gamedata.showCommitButton();

    this.setPhaseHeader("FIRE ORDERS");
    this.showAppropriateHighlight();
    this.showAppropriateEW();
    return this;
  };

  FirePhaseStrategy.prototype.deactivate = function() {
    PhaseStrategy.prototype.deactivate.call(this);
  };

  FirePhaseStrategy.prototype.onHexClicked = function(payload) {
    var hex = payload.hex;

    if (!this.selectedShip) {
      return;
    }
  };

  FirePhaseStrategy.prototype.selectShip = function(ship, payload) {
    this.setSelectedShip(ship);
    var menu = new ShipTooltipFireMenu(
      this.selectedShip,
      ship,
      this.gamedata.turn
    );
    var ballisticsMenu = new ShipTooltipBallisticsMenu(
      this.shipIconContainer,
      this.gamedata.turn,
      true,
      this.selectedShip
    );
    this.showShipTooltip(ship, payload, menu, false, ballisticsMenu);
  };

  FirePhaseStrategy.prototype.targetShip = function(ship, payload) {
    var menu = new ShipTooltipFireMenu(
      this.selectedShip,
      ship,
      this.gamedata.turn
    );
    this.showShipTooltip(ship, payload, menu, false);
  };

  FirePhaseStrategy.prototype.onWeaponSelected = function(payload) {
    var ship = payload.ship;
    var weapon = payload.weapon;

    if (this.selectedShip !== ship) {
      this.setSelectedShip(ship);
    }

    PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship });
  };

  FirePhaseStrategy.prototype.setSelectedShip = function(ship) {
    PhaseStrategy.prototype.setSelectedShip.call(this, ship);
  };

  FirePhaseStrategy.prototype.onMouseOutShips = function(ships, payload) {
    PhaseStrategy.prototype.onMouseOutShips.call(this, ships, payload);
  };

  FirePhaseStrategy.prototype.onSystemTargeted = function(payload) {
    var ship = payload.ship;
    var system = payload.system;

    if (
      gamedata.isEnemy(ship, this.selectedShip) &&
      gamedata.selectedSystems.length > 0 &&
      weaponManager.canCalledshot(ship, system, this.selectedShip)
    ) {
      weaponManager.targetShip(this.selectedShip, ship, system);
    }

    PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship });
  };

  return FirePhaseStrategy;
})();
