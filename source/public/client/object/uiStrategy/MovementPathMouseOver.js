import UiStrategy from "./UiStrategy";

class MovementPathMouseOver extends UiStrategy {
  deactivated() {
    this.shipIconContainer.hideAllMovementPaths();
  }

  shipMouseOver({ ship }) {
    this.shipIconContainer.hideAllMovementPaths();
    this.shipIconContainer.showMovementPath(ship);
  }

  shipsMouseOver({ ships }) {}

  shipsMouseOut({ ships }) {
    this.shipIconContainer.hideAllMovementPaths();
  }
}

export default MovementPathMouseOver;
