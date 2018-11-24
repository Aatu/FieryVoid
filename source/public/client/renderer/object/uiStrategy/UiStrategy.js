class UiStrategy {
  "use strict";

  constructor() {
    this.selectedShip = null;
    this.movementService = null;
    this.shipIconContainer = null;
    this.gamedata = null;
    this.uiManager = null;
  }

  activate(movementService, shipIconContainer, gamedata, uiManager) {
    this.movementService = movementService;
    this.shipIconContainer = shipIconContainer;
    this.gamedata = gamedata;
    this.uiManager = uiManager;
    this.activated();
  }

  update(gamedata) {
    this.gamedata = gamedata;
    this.updated();
  }

  updated() {}

  deactivate() {
    this.deactivated();
  }

  activated() {}

  deactivated() {}

  //this is called when user selects a ship. Use this only if you want to do something when only the user selects ship
  shipSelected({ ship }) {
    this.selectedShip = ship;
  }

  shipDeselected({ ship }) {
    this.selectedShip = null;
  }

  //This is called when something is selected without user input. Always called after shipSelected
  setShipSelected({ ship }) {
    this.selectedShip = ship;
  }

  shipMouseOver({ ship }) {}

  shipsMouseOver({ ships }) {}

  shipsMouseOut({ ships }) {}

  shipMovementChanged(ship) {}

  onScroll() {}

  onZoom() {}

  render({ coordinateConverter, scene, zoom }) {}
}

export default UiStrategy;
