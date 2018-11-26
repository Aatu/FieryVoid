import Animation from "./Animation";

class ShipMovementAnimationNew extends Animation {
  constructor(
    shipIcon,
    movementService,
    coordinateConverter,
    continious = false
  ) {
    super();

    this.shipIcon = shipIcon;
    this.ship = shipIcon.ship;
    this.movementService = movementService;
    this.coordinateConverter = coordinateConverter;
    this.continious = continious;

    this.duration = 5000;
    this.time = 0;

    this.positionCurve = this.buildPositionCurve();

    /*
    this.turnCurve = new THREE.CubicBezierCurve(
      new THREE.Vector2(0, 0),
      new THREE.Vector2(0.75, 0),
      new THREE.Vector2(0, 0.75),
      new THREE.Vector2(1, 1)
    );

    this.turnCurve = new THREE.CubicBezierCurve(
      new THREE.Vector2(0, 0),
      new THREE.Vector2(0.25, 0.25),
      new THREE.Vector2(0.75, 0.75),
      new THREE.Vector2(1, 1)
    );

    */

    /*
    this.hexAnimations.forEach(function (animation) {
        animation.debugCurve = drawRoute(animation.curve);
    });
    */

    this.endPause = 0;

    Animation.call(this);
  }

  update(gameData) {}

  stop() {
    super.stop();
  }

  cleanUp() {}

  render(now, total, last, delta, zoom, back, paused) {
    const { position, facing } = this.getPositionAndFacingAtTime(total);

    this.shipIcon.setPosition(position);
    //this.shipIcon.setFacing(-positionAndFacing.facing);

    /*
    if (
      total > this.time &&
      total < this.time + this.duration + this.endPause &&
      !paused
    ) {
      window.webglScene.moveCameraTo(positionAndFacing.position);
    }
    */
  }

  getPositionAndFacingAtTime(time) {
    let totalDone = (time - this.time) / this.duration;

    if (totalDone > 1) {
      totalDone = 1;
    }

    return {
      position: this.positionCurve.getPoint(totalDone),
      facing: 0
    };
  }

  buildPositionCurve() {
    const start = this.movementService.getLastTurnEndMove(this.ship);
    const end = this.movementService.getLastEndMove(this.ship);

    if (!end || end === start) {
      console.log("Should not have end!");
      const position = this.coordinateConverter.fromHexToGame(start.position);
      return new THREE.CubicBezierCurve3(
        new THREE.Vector3(position.x, position.y, position.z),
        new THREE.Vector3(position.x, position.y, position.z),
        new THREE.Vector3(position.x, position.y, position.z),
        new THREE.Vector3(position.x, position.y, position.z)
      );
    }

    const point1 = this.coordinateConverter.fromHexToGame(start.position);

    const control1 = this.coordinateConverter.fromHexToGame(
      start.position.add(start.target.scale(0.5))
    );

    const control2 = this.coordinateConverter.fromHexToGame(
      this.continious
        ? end.position.subtract(end.target.scale(0.5))
        : end.position
    );

    const point2 = this.coordinateConverter.fromHexToGame(end.position);

    console.log(point1, control1, control2, point2);

    return new THREE.CubicBezierCurve3(
      new THREE.Vector3(point1.x, point1.y, point1.z),
      new THREE.Vector3(control1.x, control1.y, control1.z),
      new THREE.Vector3(control2.x, control2.y, control2.z),
      new THREE.Vector3(point2.x, point2.y, point2.z)
    );
  }
}

window.ShipMovementAnimationNew = ShipMovementAnimationNew;

export default ShipMovementAnimationNew;
