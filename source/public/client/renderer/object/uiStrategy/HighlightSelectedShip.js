import UiStrategy from "./UiStrategy";

class HighlightSelectedShip extends UiStrategy {
  deactivated() {
    this.shipIconContainer.hideAllMovementPaths();
  }

  setShipSelected({ ship }) {
    this.shipIconContainer.getByShip(ship).setSelected(true);
  }

  shipDeselected({ ship }) {
    this.shipIconContainer.getByShip(ship).setSelected(false);
  }
}

export default HighlightSelectedShip;
