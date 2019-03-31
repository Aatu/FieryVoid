"use strict";

window.InitialPhaseStrategy = (function() {
  function InitialPhaseStrategy(coordinateConverter) {
    PhaseStrategy.call(this, coordinateConverter);

    this.strategies = [
      new uiStrategy.MovementPathMouseOver(),
      new uiStrategy.MovementPathSelectedShip(),
      new uiStrategy.HighlightSelectedShip()
    ];
  }

  InitialPhaseStrategy.prototype = Object.create(
    window.PhaseStrategy.prototype
  );

  InitialPhaseStrategy.prototype.update = function(gamedata) {
    PhaseStrategy.prototype.update.call(this, gamedata);
    if (this.selectedShip) {
      this.ewIconContainer.showForShip(this.selectedShip);
    }
  };

  InitialPhaseStrategy.prototype.activate = function(
    shipIcons,
    ewIconContainer,
    ballisticIconContainer,
    gamedata,
    webglScene,
    shipWindowManager,
    movementService
  ) {
    shipManager.power.repeatLastTurnPower();
    this.changeAnimationStrategy(
      new window.IdleAnimationStrategy(
        shipIcons,
        gamedata.turn,
        movementService,
        this.coordinateConverter
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

    infowindow.informPhase(5000, function() {});
    this.selectFirstOwnShipOrActiveShip();
    gamedata.showCommitButton();
    gamedata.showSurrenderButton();

    this.setPhaseHeader("INITIAL ORDERS");
    return this;
  };

  InitialPhaseStrategy.prototype.deactivate = function() {
    PhaseStrategy.prototype.deactivate.call(this, true);

    gamedata.hideSurrenderButton();
    return this;
  };

  InitialPhaseStrategy.prototype.onHexClicked = function(payload) {
    if (!this.selectedShip) {
      return;
    }

    var ballistics = gamedata.selectedSystems.filter(function(system) {
      return system.ballistic;
    });

    if (ballistics.length > 0) {
      weaponManager.targetHex(this.selectedShip, payload.hex);
    }
  };

  InitialPhaseStrategy.prototype.selectShip = function(ship, payload) {
    var position = this.coordinateConverter.fromGameToHex(
      this.shipIconContainer.getByShip(ship).getPosition()
    );

    if (
      this.selectedShip &&
      shipManager.isElint(this.selectedShip) &&
      ship !== this.selectedShip
    ) {
      var menu = new ShipTooltipInitialOrdersMenu(
        this.selectedShip,
        ship,
        this.gamedata.turn,
        position
      );
      menu.addButton(
        "selectShip",
        function() {
          return this.selectedShip !== ship;
        },
        function() {
          PhaseStrategy.prototype.setSelectedShip.call(this, ship);
          this.showShipEW(this.selectedShip);
        }.bind(this),
        "Select ship"
      );
    } else if (gamedata.isMyShip(ship)) {
      PhaseStrategy.prototype.setSelectedShip.call(this, ship);
      var menu = new ShipTooltipInitialOrdersMenu(
        this.selectedShip,
        ship,
        this.gamedata.turn,
        position
      );
      this.showShipEW(this.selectedShip);
    }

    this.showShipTooltip(ship, payload, menu, false);
  };

  InitialPhaseStrategy.prototype.deselectShip = function(ship) {
    PhaseStrategy.prototype.deselectShip.call(this, ship);
    this.hideShipEW(ship);
  };

  InitialPhaseStrategy.prototype.onMouseOutShips = function(ships) {
    PhaseStrategy.prototype.onMouseOutShips.call(this, ships);
    if (this.selectedShip) {
      this.showShipEW(this.selectedShip);
    }
  };

  InitialPhaseStrategy.prototype.targetShip = function(ship, payload) {
    //TODO: Targeting ship with ballistic weapons
    //TODO: Targeting ship with support EW (defensive or offensive)
    var position = this.coordinateConverter.fromGameToHex(
      this.shipIconContainer.getByShip(ship).getPosition()
    );
    var menu = new ShipTooltipInitialOrdersMenu(
      this.selectedShip,
      ship,
      this.gamedata.turn,
      position
    );
    this.showShipTooltip(ship, payload, menu, false);
  };

  InitialPhaseStrategy.prototype.createReplayUI = function(gamedata) {
    if (gamedata.turn === 1) {
      return;
    }

    this.replayUI = new ReplayUI().activate();
  };

  InitialPhaseStrategy.prototype.onWeaponSelected = function(payload) {
    var ship = payload.ship;
    var weapon = payload.weapon;

    if (this.selectedShip !== ship) {
      this.setSelectedShip(ship);
    }

    PhaseStrategy.prototype.onSystemDataChanged.call(this, { ship: ship });
  };

  return InitialPhaseStrategy;
})();
