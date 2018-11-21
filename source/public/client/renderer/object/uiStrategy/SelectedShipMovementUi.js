import UiStrategy from "./UiStrategy";

class SelectedShipMovementUi extends UiStrategy {
  deactivated() {
    this.uiManager.hideMovementUi();
    this.ship = null;
  }

  setShipSelected({ ship }) {
    this.ship = ship;
    this.uiManager.showMovementUi({
      ship,
      movementService: this.movementService
    });

    reposition(this.ship, this.shipIconContainer, this.uiManager);
  }

  shipDeselected({ ship }) {
    this.uiManager.hideMovementUi();
    this.ship = null;
  }

  onScroll() {
    reposition(this.ship, this.shipIconContainer, this.uiManager);
  }

  onZoom() {
    reposition(this.ship, this.shipIconContainer, this.uiManager);
  }

  shipMovementChanged({ ship }) {
    if (this.ship !== ship) {
      return;
    }

    this.uiManager.showMovementUi({
      ship,
      movementService: this.movementService
    });

    reposition(this.ship, this.shipIconContainer, this.uiManager);
  }
}

const reposition = (ship, shipIconContainer, uiManager) => {
  if (!ship) {
    return;
  }

  uiManager.repositionMovementUi(
    window.coordinateConverter.fromGameToViewPort(
      shipIconContainer.getByShip(ship).getPosition()
    )
  );
};

export default SelectedShipMovementUi;
