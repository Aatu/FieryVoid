"use strict";

window.MovementPhaseStrategy = (function() {
  function MovementPhaseStrategy(coordinateConverter) {
    PhaseStrategy.call(this, coordinateConverter);

    this.strategies = [
      new uiStrategy.MovementPathMouseOver(),
      new uiStrategy.MovementPathSelectedShip(),
      new uiStrategy.HighlightSelectedShip(),
      new uiStrategy.SelectedShipMovementUi()
    ];
  }

  MovementPhaseStrategy.prototype = Object.create(
    window.PhaseStrategy.prototype
  );

  MovementPhaseStrategy.prototype.update = function(gamedata) {
    doForcedMovementForActiveShip();
    PhaseStrategy.prototype.update.call(this, gamedata);
    this.selectActiveShip();
  };

  MovementPhaseStrategy.prototype.activate = function(
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

    doForcedMovementForActiveShip();
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
    this.selectActiveShip();

    this.setPhaseHeader("MOVEMENT ORDERS", this.selectedShip.name);

    this.showAppropriateHighlight();
    this.showAppropriateEW();

    gamedata.ships
      .filter(window.SimultaneousMovementRule.isNotYetMovedShip)
      .forEach(function(ship) {
        var icon = this.shipIconContainer.getByShip(ship);
        icon.setNotMoved(true);
      }, this);

    gamedata.showCommitButton();
    return this;
  };

  MovementPhaseStrategy.prototype.deactivate = function() {
    PhaseStrategy.prototype.deactivate.call(this, true);

    gamedata.ships.forEach(function(ship) {
      var icon = this.shipIconContainer.getByShip(ship);
      icon.showSideSprite(false);
      icon.setNotMoved(false);
    }, this);

    return this;
  };

  MovementPhaseStrategy.prototype.onShipRightClicked = function(ship) {
    this.shipWindowManager.open(ship);
  };

  MovementPhaseStrategy.prototype.onHexClicked = function(payload) {};

  MovementPhaseStrategy.prototype.selectShip = function(ship, payload) {
    if (gamedata.getMyActiveShips().includes(ship)) {
      this.setSelectedShip(ship);
    }

    var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
    this.showShipTooltip(ship, payload, menu, false);
  };

  MovementPhaseStrategy.prototype.setSelectedShip = function(ship) {
    PhaseStrategy.prototype.setSelectedShip.call(this, ship);
  };

  MovementPhaseStrategy.prototype.targetShip = function(ship, payload) {
    var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
    this.showShipTooltip(ship, payload, menu, false);
  };

  MovementPhaseStrategy.prototype.showShipTooltip = function(
    ships,
    payload,
    menu,
    hide,
    ballisticsMenu
  ) {
    ships = [].concat(ships);

    PhaseStrategy.prototype.showShipTooltip.call(
      this,
      ships,
      payload,
      menu,
      hide,
      ballisticsMenu
    );
  };

  function doForcedMovementForActiveShip() {
    gamedata.getMyActiveShips().forEach(function(ship) {
      shipManager.movement.doForcedPivot(ship);

      if (ship.base) {
        shipManager.movement.doRotate(ship);

        //TODO: Test if this autocommit thing works
        gamedata.autoCommitOnMovement(ship);
      }
    });
  }

  MovementPhaseStrategy.prototype.onShipMovementChanged = function(payload) {
    PhaseStrategy.prototype.onShipMovementChanged.call(this, payload);

    this.onClickCallbacks = this.onClickCallbacks.filter(function(callback) {
      return callback();
    });

    this.gamedata.drawIniGUI();
  };

  MovementPhaseStrategy.prototype.showAppropriateEW = function() {
    this.shipIconContainer.getArray().forEach(icon => {
      icon.hideEW();
      icon.hideBDEW();
    });

    this.ewIconContainer.hide();
  };

  MovementPhaseStrategy.prototype.showAppropriateHighlight = function() {
    PhaseStrategy.prototype.showAppropriateHighlight.call(this);
    this.highlightUnmovedShips();
  };

  MovementPhaseStrategy.prototype.selectActiveShip = function() {
    var ship = gamedata
      .getMyActiveShips()
      .filter(function(ship) {
        return (
          !shipManager.movement.isMovementReady(ship) &&
          !shipManager.isDestroyed(ship)
        );
      })
      .pop();

    if (ship) {
      this.setSelectedShip(ship);
    } else {
      this.setSelectedShip(gamedata.getMyActiveShips().pop());
    }
  };

  MovementPhaseStrategy.prototype.highlightUnmovedShips = function() {
    //TODO: reimplement in ui strategy
  };

  return MovementPhaseStrategy;
})();
