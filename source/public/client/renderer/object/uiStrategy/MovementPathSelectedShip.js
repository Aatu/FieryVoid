import UiStrategy from "./UiStrategy";

class MovementPathSelectedShip extends UiStrategy {
  deactivated() {
    this.shipIconContainer.hideAllMovementPaths();
  }

  setShipSelected({ ship }) {
    this.shipIconContainer.showMovementPath(ship);
    this.ship = ship;
  }

  shipDeselected({ ship }) {
    this.shipIconContainer.hideAllMovementPaths();
  }

  shipsMouseOut({ ships }) {
    if (this.ship) {
      this.shipIconContainer.showMovementPath(this.ship);
    }
  }

  shipMovementChanged({ ship }) {
    if (this.ship === ship) {
      this.shipIconContainer.showMovementPath(ship);
    }
  }
}

export default MovementPathSelectedShip;
