import UiStrategy from "./UiStrategy";

class HighlightSelectedShip extends UiStrategy {
  constructor() {
    super();
    this.ship = null;
    this.lastAnimationTime = null;
    this.totalTime = 0;

    this.amplitude = 1;
    this.frequency = 300;
  }

  deactivated() {
    this.reset();
    this.ship = null;
  }

  setShipSelected({ ship }) {
    this.ship = ship;
  }

  shipDeselected({ ship }) {
    this.reset();
    this.ship = null;
  }

  render({ coordinateConverter, scene, zoom }) {
    const now = new Date().getTime();

    const delta =
      this.lastAnimationTime !== null ? now - this.lastAnimationTime : 0;

    this.totalTime += delta;

    this.lastAnimationTime = now;

    if (!this.ship) {
      return;
    }

    const opacity =
      this.amplitude * 0.5 * Math.sin(this.totalTime / this.frequency) +
      this.amplitude;

    this.shipIconContainer.getByShip(this.ship).setSideSpriteOpacity(opacity);
  }

  reset() {
    if (this.ship)
      this.shipIconContainer.getByShip(this.ship).setSideSpriteOpacity(1);
  }
}

export default HighlightSelectedShip;
