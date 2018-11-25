"use strict";

window.PhaseStrategy = (function() {
  function PhaseStrategy(coordinateConverter) {
    this.inactive = true;
    this.gamedata = null;
    this.shipIconContainer = null;
    this.ewIconContainer = null;
    this.ballisticIconContainer = null;
    this.movementService = null;
    this.shipWindowManager = null;
    this.coordinateConverter = coordinateConverter;
    this.currentlyMouseOveredIds = null;

    this.onMouseOutCallbacks = [];
    this.onZoomCallbacks = [
      this.repositionTooltip.bind(this),
      this.repositionSelectFromShips.bind(this)
    ];
    this.onScrollCallbacks = [
      this.repositionTooltip.bind(this),
      this.repositionSelectFromShips.bind(this)
    ];
    this.onClickCallbacks = [this.hideSystemInfo.bind(this, true)];

    this.selectedShip = null;
    this.targetedShip = null;
    this.animationStrategy = null;
    this.replayUI = null;

    this.shipTooltip = null;
    this.selectFromShips = null;
    this.movementUI = null;

    this.onDoneCallback = null;

    this.systemInfoState = null;

    this.uiManager = new window.UIManager($("body")[0]);
    this.strategies = [];
  }

  PhaseStrategy.prototype.updateStrategies = function() {
    this.strategies.forEach(strategy => strategy.update(this.gamedata));
  };

  PhaseStrategy.prototype.activateStrategies = function() {
    this.strategies.forEach(strategy =>
      strategy.activate(
        this.movementService,
        this.shipIconContainer,
        this.gamedata,
        this.uiManager
      )
    );
  };

  PhaseStrategy.prototype.callStrategies = function(functionName, payload) {
    this.strategies.forEach(strategy => strategy[functionName](payload));
  };

  PhaseStrategy.prototype.onOpenShipWindowFor = function(payload) {
    this.shipWindowManager.open(payload.ship);
  };

  PhaseStrategy.prototype.onCloseShipWindow = function(payload) {
    this.shipWindowManager.close(payload.ship);
  };

  PhaseStrategy.prototype.onCloseSystemInfo = function() {
    this.hideSystemInfo(true);
  };

  PhaseStrategy.prototype.hideSystemInfo = function(force) {
    if (!this.systemInfoState) {
      return true;
    }

    if (!this.systemInfoState.menu || force) {
      this.uiManager.hideSystemInfo();
      this.systemInfoState = null;
    }

    this.shipIconContainer.getArray().forEach(function(icon) {
      icon.hideWeaponArcs();
    });

    return true;
  };

  PhaseStrategy.prototype.showSystemInfo = function(
    ship,
    system,
    element,
    menu
  ) {
    if (this.systemInfoState && this.systemInfoState.menu && !menu) {
      return;
    }

    var boundingBox = element.getBoundingClientRect
      ? element.getBoundingClientRect()
      : element.get(0).getBoundingClientRect();

    if (menu) {
      if (!this.uiManager.canShowSystemInfoMenu(ship, system)) {
        return;
      }
      this.uiManager.showSystemInfoMenu({
        ship: ship,
        selectedShip: this.selectedShip,
        system: system,
        boundingBox: boundingBox
      });
    } else {
      this.uiManager.showSystemInfo({
        ship: ship,
        selectedShip: this.selectedShip,
        system: system,
        boundingBox: boundingBox
      });
    }
    this.systemInfoState = { menu: menu, element: element, system: system };
  };

  PhaseStrategy.prototype.consumeGamedata = function() {
    this.shipIconContainer.consumeGamedata(this.gamedata);
    this.movementService.update(this.gamedata, this);
    this.animationStrategy.update(this.gamedata);
    this.ewIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
    this.ballisticIconContainer.consumeGamedata(
      this.gamedata,
      this.shipIconContainer
    );

    this.updateStrategies();
  };

  PhaseStrategy.prototype.render = function(coordinateConverter, scene, zoom) {
    this.animationStrategy.render(coordinateConverter, scene, zoom);
    this.callStrategies("render", { coordinateConverter, scene, zoom });
  };

  PhaseStrategy.prototype.update = function(gamedata) {
    this.gamedata = gamedata;
    this.consumeGamedata();
    this.ewIconContainer.hide();
    this.ballisticIconContainer.show();
  };

  PhaseStrategy.prototype.activate = function(
    shipIcons,
    ewIconContainer,
    ballisticIconContainer,
    gamedata,
    webglScene,
    shipWindowManager,
    movementService
  ) {
    this.shipIconContainer = shipIcons;
    this.ewIconContainer = ewIconContainer;
    this.ballisticIconContainer = ballisticIconContainer;
    this.movementService = movementService;
    this.gamedata = gamedata;
    this.inactive = false;
    this.consumeGamedata();
    this.ballisticIconContainer.show();
    this.shipWindowManager = shipWindowManager;
    this.createReplayUI(gamedata);
    this.showAppropriateHighlight();
    this.showAppropriateEW();
    this.activateStrategies();
    return this;
  };

  PhaseStrategy.prototype.deactivate = function() {
    this.inactive = true;
    this.animationStrategy.deactivate();
    this.replayUI && this.replayUI.deactivate();

    if (this.ballisticIconContainer) {
      this.ballisticIconContainer.hide();
    }

    if (this.ewIconContainer) {
      this.ewIconContainer.hide();
    }

    if (this.shipTooltip) {
      this.shipTooltip.destroy();
    }

    if (this.selectedShip) {
      this.deselectShip(this.selectedShip);
    }

    this.currentlyMouseOveredIds = null;

    this.uiManager.hideWeaponList();
    this.hideSystemInfo(true);
    this.shipWindowManager.closeAll();

    this.shipIconContainer.getArray(icon => {
      icon.showSideSprite(false);
      icon.setHighlighted(false);
    });

    this.callStrategies("deactivate");

    return this;
  };

  PhaseStrategy.prototype.onEvent = function(name, payload) {
    var target = this["on" + name];
    if (target && typeof target == "function") {
      target.call(this, payload);
    }
  };

  PhaseStrategy.prototype.onScrollToShip = function(payload) {
    var icon = this.shipIconContainer.getById(payload.shipId);
    window.webglScene.moveCameraTo(icon.getPosition());
  };

  PhaseStrategy.prototype.onScrollEvent = function(payload) {
    this.onScrollCallbacks = this.onScrollCallbacks.filter(function(callback) {
      return callback(payload);
    });

    this.callStrategies("onScroll", payload);
  };

  PhaseStrategy.prototype.onZoomEvent = function(payload) {
    this.onZoomCallbacks = this.onZoomCallbacks.filter(function(callback) {
      return callback(payload);
    });

    this.callStrategies("onZoom", payload);
  };

  PhaseStrategy.prototype.onClickEvent = function(payload) {
    var icons = getInterestingStuffInPosition.call(
      this,
      payload,
      this.gamedata.turn
    );

    this.onClickCallbacks = this.onClickCallbacks.filter(function(callback) {
      return callback();
    });

    if (icons.length > 1) {
      this.onShipsClicked(
        icons.map(function(icon) {
          return this.gamedata.getShip(icon.shipId);
        }, this),
        payload
      );
    } else if (icons.length === 1) {
      if (payload.button !== 0 && payload.button !== undefined) {
        this.onShipRightClicked(
          this.gamedata.getShip(icons[0].shipId),
          payload
        );
      } else {
        this.onShipClicked(this.gamedata.getShip(icons[0].shipId), payload);
      }
    } else {
      this.onHexClicked(payload);
    }
  };

  PhaseStrategy.prototype.onHexClicked = function(payload) {};

  PhaseStrategy.prototype.onShipsClicked = function(ships, payload) {
    this.showSelectFromShips(ships, payload);
  };

  PhaseStrategy.prototype.onShipRightClicked = function(ship) {
    if (this.gamedata.isMyShip(ship)) {
      this.setSelectedShip(ship);
    }
    this.shipWindowManager.open(ship);
  };

  PhaseStrategy.prototype.onShipClicked = function(ship, payload) {
    if (this.gamedata.isMyShip(ship)) {
      this.selectShip(ship, payload);
    } else {
      this.targetShip(ship, payload);
    }
  };

  PhaseStrategy.prototype.selectShip = function(ship, payload) {
    this.setSelectedShip(ship);
    this.showAppropriateHighlight();
    this.showAppropriateEW();
    var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
    this.showShipTooltip(ship, payload, menu, false);
    this.callStrategies("shipSelected", { ship, payload });
  };

  PhaseStrategy.prototype.setSelectedShip = function(ship) {
    if (this.selectedShip) {
      this.deselectShip(this.selectedShip);
    }

    this.selectedShip = ship;
    this.showAppropriateEW();

    if (this.shipTooltip) {
      this.shipTooltip.update(ship, this.selectedShip);
    }

    this.uiManager.showWeaponList({
      ship: ship,
      gamePhase: gamedata.gamephase,
      movementService: this.movementService
    });

    this.callStrategies("setShipSelected", { ship });
  };

  PhaseStrategy.prototype.deselectShip = function(ship) {
    gamedata.selectedSystems.slice(0).forEach(function(selected) {
      weaponManager.unSelectWeapon(this.selectedShip, selected);
    }, this);

    this.selectedShip = null;
    this.uiManager.hideWeaponList();
    this.callStrategies("shipDeselected", { ship });
  };

  PhaseStrategy.prototype.targetShip = function(ship, payload) {
    var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
    this.showShipTooltip(ship, payload, menu, false);
  };

  /*
    PhaseStrategy.prototype.targetShip = function (ship) {
        if (this.targetedShip) {
            this.untargetShip(this.targetedShip);
        }
        this.targetedShip = ship;
        this.shipIconContainer.getById(ship.id).setSelected(true);
    };

    PhaseStrategy.prototype.untargetShip = function (ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);
        this.targetedShip = null;
    };
    */

  PhaseStrategy.prototype.onMouseMoveEvent = function(payload) {
    var icons = getInterestingStuffInPosition.call(
      this,
      payload,
      this.gamedata.turn
    );

    function doMouseOut() {
      if (this.currentlyMouseOveredIds) {
        this.currentlyMouseOveredIds = null;
      }

      this.onMouseOutCallbacks = this.onMouseOutCallbacks.filter(function(
        callback
      ) {
        callback();
        return false;
      });

      this.onMouseOutShips(gamedata.ships, payload);
    }

    if (icons.length === 0 && this.currentlyMouseOveredIds !== null) {
      doMouseOut.call(this);
      return;
    } else if (icons.length === 0) {
      return;
    }

    var mouseOverIds = icons.reduce(function(value, icon) {
      return value + icon.shipId;
    }, "");

    if (mouseOverIds === this.currentlyMouseOveredIds) {
      return;
    }

    doMouseOut.call(this);

    this.currentlyMouseOveredIds = mouseOverIds;

    var ships = icons.map(function(icon) {
      return this.gamedata.getShip(icon.shipId);
    }, this);
    if (ships.length > 1) {
      this.onMouseOverShips(ships, payload);
    } else {
      this.onMouseOverShip(ships[0], payload);
    }
  };

  PhaseStrategy.prototype.onMouseOutShips = function(ships, payload) {
    this.showAppropriateHighlight();
    this.showAppropriateEW();

    this.callStrategies("shipsMouseOut", { ships, payload });
  };

  PhaseStrategy.prototype.onMouseOverShips = function(ships, payload) {
    if (this.shipTooltip && this.shipTooltip.isForAnyOf(ships)) {
      return;
    }

    if (this.shipTooltip && this.shipTooltip.menu) {
      return;
    }

    this.showShipTooltip(ships, payload, null, true);

    this.callStrategies("shipsMouseOver", { ships, payload });
  };

  PhaseStrategy.prototype.onMouseOverShip = function(ship, payload) {
    this.showAppropriateHighlight();
    this.showAppropriateEW();

    var icon = this.shipIconContainer.getById(ship.id);
    if (!this.shipTooltip || !this.shipTooltip.menu) {
      this.showShipTooltip(ship, payload, null, true);
    }

    if (
      this.shipTooltip &&
      this.shipTooltip.ships.includes(ship) &&
      this.shipTooltip.ships.length === 1
    ) {
      this.shipTooltip.update(ship, this.selectedShip);
    }

    this.showShipEW(ship);
    icon.showSideSprite(true);
    icon.showBDEW();
    icon.setHighlighted(true);

    this.callStrategies("shipMouseOver", { ship, payload });
  };

  PhaseStrategy.prototype.showShipEW = function(ship) {
    this.shipIconContainer.getByShip(ship).showEW();
    this.ewIconContainer.showForShip(ship);
  };

  PhaseStrategy.prototype.hideShipEW = function(ship) {
    this.shipIconContainer.getByShip(ship).hideEW();
    this.ewIconContainer.hide();
  };

  PhaseStrategy.prototype.showShipTooltip = function(
    ships,
    payload,
    menu,
    hide,
    ballisticsMenu
  ) {
    if (this.shipTooltip) {
      this.hideShipTooltip(this.shipTooltip);
    }

    ships = [].concat(ships);

    var position = payload.hex;
    if (ships.length === 1) {
      position = this.shipIconContainer.getByShip(ships[0]).getPosition();
    }

    if (!ballisticsMenu) {
      ballisticsMenu = new ShipTooltipBallisticsMenu(
        this.shipIconContainer,
        this.gamedata.turn,
        false
      );
    }

    var shipTooltip = new window.ShipTooltip(
      this.selectedShip,
      ships,
      position,
      shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip),
      menu,
      payload.hex,
      ballisticsMenu
    );

    this.shipTooltip = shipTooltip;
    this.onClickCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));

    if (hide) {
      this.onMouseOutCallbacks.push(
        this.hideShipTooltip.bind(this, shipTooltip)
      );
    }
  };

  PhaseStrategy.prototype.showSelectFromShips = function(ships, payload) {
    var selectFromShips = new window.SelectFromShips(
      this.selectedShip,
      ships,
      payload,
      this
    );
    this.selectFromShips = selectFromShips;
    this.onClickCallbacks.push(
      this.hideSelectFromShips.bind(this, selectFromShips)
    );
  };

  PhaseStrategy.prototype.hideShipTooltip = function(shipTooltip) {
    if (this.shipTooltip && this.shipTooltip === shipTooltip) {
      this.shipTooltip.destroy();
      this.shipTooltip = null;
    }
  };

  PhaseStrategy.prototype.hideSelectFromShips = function(selectFromShips) {
    if (this.selectFromShips && this.selectFromShips === selectFromShips) {
      this.selectFromShips.destroy();
      this.selectFromShips = null;
    }
  };

  PhaseStrategy.prototype.repositionSelectFromShips = function() {
    if (this.selectFromShips) {
      this.selectFromShips.reposition();
    }

    return true;
  };

  PhaseStrategy.prototype.repositionTooltip = function() {
    if (this.shipTooltip) {
      this.shipTooltip.reposition();
    }

    return true;
  };

  PhaseStrategy.prototype.selectFirstOwnShipOrActiveShip = function() {
    var ship = gamedata.getFirstFriendlyShip();
    //TODO: what about active ship?
    if (ship) {
      this.setSelectedShip(ship);
    }
  };

  PhaseStrategy.prototype.done = function() {
    if (this.onDoneCallback) {
      this.onDoneCallback();
    }
  };

  PhaseStrategy.prototype.onSystemMouseOver = function(payload) {
    var ship = payload.ship;
    var system = payload.system;
    var element = payload.element;
    //systemInfo.showSystemInfo(element, weapon, ship, this.selectedShip);

    this.showSystemInfo(ship, system, element, false);

    this.shipIconContainer.getArray().forEach(function(icon) {
      icon.hideWeaponArcs();
    });

    if (system instanceof Ship) {
      return;
    }

    var icon = this.shipIconContainer.getByShip(ship);
    icon.showWeaponArc(ship, system);
  };

  PhaseStrategy.prototype.onSystemMouseOut = function() {
    this.shipIconContainer.getArray().forEach(function(icon) {
      icon.hideWeaponArcs();
    });

    this.hideSystemInfo();
  };

  PhaseStrategy.prototype.createReplayUI = function(gamedata) {
    this.replayUI = new ReplayUI().activate();
  };

  PhaseStrategy.prototype.changeAnimationStrategy = function(
    newAnimationStartegy
  ) {
    this.animationStrategy && this.animationStrategy.deactivate();
    this.animationStrategy = newAnimationStartegy;
    this.animationStrategy.activate();
  };

  function getInterestingStuffInPosition(payload, turn) {
    var mouseovered = payload.entities.filter(function(entity) {
      var turnDestroyed = shipManager.getTurnDestroyed(entity.ship);
      return (
        entity instanceof window.ShipObject &&
        (turnDestroyed === null || turnDestroyed >= turn)
      );
    });

    if (mouseovered.length > 0) {
      return mouseovered;
    }

    return this.shipIconContainer
      .getIconsInProximity(payload)
      .filter(function(icon) {
        var turnDestroyed = shipManager.getTurnDestroyed(icon.ship);
        return turnDestroyed === null || turnDestroyed >= turn;
      });
  }

  PhaseStrategy.prototype.setPhaseHeader = function(name, shipName) {
    if (name === false) {
      jQuery("#phaseheader").hide();
      return;
    }

    if (!shipName) {
      shipName = "";
    }

    $("#phaseheader .turn.value").html("TURN: " + this.gamedata.turn + ",");
    $("#phaseheader .phase.value").html(name);
    $("#phaseheader .activeship.value").html(shipName);
    $("#phaseheader").show();
  };

  PhaseStrategy.prototype.onShipEwChanged = function(payload) {
    var ship = payload.ship;

    if (this.shipTooltip) {
      this.shipTooltip.update(ship, this.selectedShip);
    }

    this.shipIconContainer.getByShip(ship).consumeEW(ship);
    this.ewIconContainer.updateForShip(ship);
    this.shipWindowManager.update();
  };

  PhaseStrategy.prototype.onShipMovementChanged = function(payload) {
    var ship = payload.ship;
    this.shipIconContainer.getByShip(ship).consumeMovement(ship.movement);

    if (this.animationStrategy) {
      this.animationStrategy.shipMovementChanged(ship);
    }

    this.callStrategies("shipMovementChanged", { ship });
  };

  PhaseStrategy.prototype.onShowAllEW = function(payload) {
    showGlobalEW.call(this, gamedata.ships, payload);
  };

  PhaseStrategy.prototype.onShowFriendlyEW = function(payload) {
    showGlobalEW.call(
      this,
      gamedata.ships.filter(function(ship) {
        return gamedata.isMyOrTeamOneShip(ship);
      }),
      payload
    );
  };

  PhaseStrategy.prototype.onShowEnemyEW = function(payload) {
    showGlobalEW.call(
      this,
      gamedata.ships.filter(function(ship) {
        return !gamedata.isMyOrTeamOneShip(ship);
      }),
      payload
    );
  };

  PhaseStrategy.prototype.showAppropriateEW = function() {
    this.shipIconContainer.getArray().forEach(icon => {
      icon.hideEW();
      icon.hideBDEW();
    });

    this.ewIconContainer.hide();
    if (this.selectedShip) {
      this.showShipEW(this.selectedShip);
    }
  };

  PhaseStrategy.prototype.showAppropriateHighlight = function() {
    this.shipIconContainer.getArray().forEach(icon => {
      icon.showSideSprite(false);
      icon.setHighlighted(false);
    });
  };

  function showGlobalEW(ships, payload) {
    if (payload.up) {
      this.showAppropriateEW();
    } else {
      ships.forEach(function(ship) {
        var icon = this.shipIconContainer.getById(ship.id);
        this.ewIconContainer.showByShip(ship);
        icon.showEW();
        icon.showBDEW();
      }, this);
    }
  }

  PhaseStrategy.prototype.onSystemDataChanged = function(payload) {
    var ship = payload.ship;
    var system = payload.system;

    if (this.selectedShip === ship) {
      this.uiManager.showWeaponList({
        ship: ship,
        gamePhase: gamedata.gamephase,
        movementService: this.movementService
      });
    }

    if (this.systemInfoState) {
      this.showSystemInfo(
        ship,
        this.systemInfoState.system,
        this.systemInfoState.element,
        this.systemInfoState.menu
      );
    }

    if (system && system.ballistic) {
      this.ballisticIconContainer.consumeGamedata(
        this.gamedata,
        this.shipIconContainer
      );
    }

    this.shipWindowManager.update();
  };

  PhaseStrategy.prototype.onSystemClicked = function(payload) {
    var ship = payload.ship;
    var system = payload.system;
    var element = payload.element;

    this.showSystemInfo(ship, system, element, true);
    PhaseStrategy.prototype.onSystemDataChanged.call(this, {
      ship: ship,
      system: system
    });
  };

  PhaseStrategy.prototype.onHexTargeted = function(payload) {
    this.ballisticIconContainer.consumeGamedata(
      this.gamedata,
      this.shipIconContainer
    );
    this.shipWindowManager.update();

    if (this.selectedShip === payload.shooter) {
      this.uiManager.showWeaponList({
        ship: payload.shooter,
        gamePhase: gamedata.gamephase,
        movementService: this.movementService
      });
    }
  };

  PhaseStrategy.prototype.onShipTargeted = function(payload) {
    if (
      payload.weapons.some(function(weapon) {
        return weapon.ballistic;
      })
    ) {
      this.ballisticIconContainer.consumeGamedata(
        this.gamedata,
        this.shipIconContainer
      );
    }

    if (this.selectedShip === payload.shooter) {
      this.uiManager.showWeaponList({
        ship: payload.shooter,
        gamePhase: gamedata.gamephase,
        movementService: this.movementService
      });
    }

    if (
      this.shipTooltip &&
      this.shipTooltip.ships.includes(payload.target) &&
      this.shipTooltip.ships.length === 1
    ) {
      this.shipTooltip.update(payload.target, this.selectedShip);
    }

    this.shipWindowManager.update();
  };

  return PhaseStrategy;
})();
