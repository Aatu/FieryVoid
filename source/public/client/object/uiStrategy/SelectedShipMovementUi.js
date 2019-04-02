import UiStrategy from "./UiStrategy";

class SelectedShipMovementUi extends UiStrategy {
  deactivated() {
    this.uiManager.hideMovementUi();
    this.ship = null;
  }

  async setShipSelected({ ship }) {
    this.ship = ship;
    await this.shipIconContainer.getByShip(ship).getLoadedPromise();

    if (this.ship !== ship) {
      // ship was changed when waiting
      return;
    }

    console.log("SHOW 1");
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

    console.log("SHOW 2");
    this.uiManager.showMovementUi({
      ship,
      movementService: this.movementService
    });

    reposition(this.ship, this.shipIconContainer, this.uiManager);
  }
}

const reposition = (ship, shipIconContainer, uiManager) => {
  if (!ship) {
    console.log("no ship");
    return;
  }

  uiManager.repositionMovementUi(
    window.coordinateConverter.fromGameToViewPort(
      shipIconContainer.getByShip(ship).getPosition()
    )
  );
};

export default SelectedShipMovementUi;
