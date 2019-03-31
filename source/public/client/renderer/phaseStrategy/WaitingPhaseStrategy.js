"use strict";

window.WaitingPhaseStrategy = (function() {
  function WaitingPhaseStrategy(coordinateConverter, phaseState) {
    PhaseStrategy.call(this, coordinateConverter, phaseState);
    this.strategies = [new uiStrategy.MovementPathMouseOver("movement")];
  }

  WaitingPhaseStrategy.prototype = Object.create(
    window.PhaseStrategy.prototype
  );

  WaitingPhaseStrategy.prototype.activate = function(
    shipIcons,
    ewIconContainer,
    ballisticIconContainer,
    gamedata,
    webglScene,
    shipWindowManager,
    movementService
  ) {
    this.changeAnimationStrategy(
      new window.MovementAnimationStrategy(
        shipIcons,
        gamedata.turn,
        movementService,
        this.coordinateConverter,
        this.phaseState
      )
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

    gamedata.hideCommitButton();

    ajaxInterface.startPollingGamedata();

    this.setPhaseHeader("WAITING FOR TURN...");
    this.showAppropriateHighlight();
    this.showAppropriateEW();
    return this;
  };

  WaitingPhaseStrategy.prototype.deactivate = function() {
    PhaseStrategy.prototype.deactivate.call(this);
    ajaxInterface.stopPolling();
    return this;
  };

  WaitingPhaseStrategy.prototype.onHexClicked = function(payload) {};

  WaitingPhaseStrategy.prototype.selectShip = function(ship, payload) {
    var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
    this.showShipTooltip(ship, payload, menu, false);
  };

  WaitingPhaseStrategy.prototype.targetShip = function(ship, payload) {
    var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
    this.showShipTooltip(ship, payload, menu, false);
  };

  WaitingPhaseStrategy.prototype.showAppropriateEW = function() {
    this.shipIconContainer.getArray().forEach(icon => {
      icon.hideEW();
      icon.hideBDEW();
    });

    this.ewIconContainer.hide();
  };

  WaitingPhaseStrategy.prototype.showAppropriateHighlight = function() {
    this.shipIconContainer.getArray().forEach(icon => {
      icon.showSideSprite(false);
      icon.setHighlighted(false);
    });
  };

  return WaitingPhaseStrategy;
})();
