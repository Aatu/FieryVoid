import UiStrategy from "./UiStrategy";

class SelectedShipMovementUi extends UiStrategy {
  deactivated() {
    this.uiManager.hideMovementUi();
  }

  setShipSelected({ ship }) {
    this.uiManager.showMovementUi({
      ship,
      movementService: this.movementService
    });
  }

  shipDeselected({ ship }) {
    this.uiManager.hideMovementUi();
  }

  onScroll() {
    this.uiManager.repositionMovementUi();
  }

  onZoom() {
    this.uiManager.repositionMovementUi();
  }
}

export default SelectedShipMovementUi;
