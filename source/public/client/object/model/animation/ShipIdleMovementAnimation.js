import Animation from "./Animation";

class ShipIdleMovementAnimation extends Animation {
  constructor(shipIcon, movementService, coordinateConverter) {
    super();

    this.shipIcon = shipIcon;
    this.ship = shipIcon.ship;
    this.movementService = movementService;
    this.coordinateConverter = coordinateConverter;

    this.duration = 0;

    this.position = this.getPosition();
    this.facing = this.getFacing();
  }

  update(gameData) {
    this.position = this.getPosition();
    this.facing = this.getFacing();
  }

  stop() {
    super.stop();
  }

  cleanUp() {}

  render(now, total, last, delta, zoom, back, paused) {
    this.shipIcon.setPosition(this.position);
    this.shipIcon.setFacing(-this.facing);
  }

  getPosition() {
    const end = this.movementService.getLastEndMove(this.ship);
    return this.coordinateConverter.fromHexToGame(end.position);
  }

  getFacing() {
    return mathlib.hexFacingToAngle(
      this.movementService.getLastEndMove(this.ship).facing
    );
  }
}

window.ShipIdleMovementAnimation = ShipIdleMovementAnimation;

export default ShipIdleMovementAnimation;
