import Animation from "./Animation";

class ShipMovementAnimation extends Animation {
  constructor(
    shipIcon,
    movementService,
    coordinateConverter,
    time = 0,
    continious = false
  ) {
    super();

    this.shipIcon = shipIcon;
    this.ship = shipIcon.ship;
    this.movementService = movementService;
    this.coordinateConverter = coordinateConverter;
    this.continious = continious;
    this.time = time;

    this.doneCallback = null;

    if (!this.movementService) {
      throw new Error("movement service undefined");
    }

    this.duration = 5000;
    this.time = this.time;

    this.positionCurve = this.buildPositionCurve();
    this.rotations = this.buildRotations();

    this.easeInOut = new THREE.CubicBezierCurve(
      new THREE.Vector2(0, 0),
      new THREE.Vector2(0.75, 0),
      new THREE.Vector2(0.25, 1),
      new THREE.Vector2(1, 1)
    );

    /*
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
    this.callStartCallback(total);

    const { position, facing } = !this.done
      ? this.getPositionAndFacingAtTime(total)
      : this.getPositionAndFacingAtTime(this.time + this.duration);

    this.shipIcon.setPosition(position);
    this.shipIcon.setFacing(-facing);

    this.callDoneCallback(total);

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
    } else if (totalDone < 0) {
      totalDone = 0;
    }

    return {
      position: this.positionCurve.getPoint(totalDone),
      facing: this.getFacing(time)
    };
  }

  getFacing(time) {
    if (time < this.time) {
      return this.rotations[0].start;
    }

    if (time > this.time + this.duration) {
      return this.rotations[this.rotations.length - 1].end;
    }

    let rotation = this.rotations.find(
      rotation =>
        time >= rotation.startTime + this.time &&
        time < rotation.endTime + this.time
    );

    if (!rotation) {
      rotation = this.rotations[this.rotations.length - 1];
    }

    let totalDone =
      rotation.duration === 0
        ? 1
        : (time - (rotation.startTime + this.time)) / rotation.duration;

    if (totalDone > 1) {
      totalDone = 1;
    }

    return mathlib.addToDirection(
      rotation.start,
      rotation.amount * this.easeInOut.getPoint(totalDone).y
    );
  }

  buildRotations() {
    let pivots = this.buildPivotList();

    let startTime = 0;

    pivots.forEach(pivot => {
      pivot.startTime = startTime;
      startTime = pivot.endTime;
    });

    if (pivots.length === 0) {
      const facing = mathlib.hexFacingToAngle(
        this.movementService.getLastTurnEndMove(this.ship).facing
      );

      pivots = [
        {
          amount: 0,
          start: facing,
          end: facing,
          startTime: 0,
          endTime: this.duration
        }
      ];
    }

    pivots[pivots.length - 1].endTime = this.duration;

    pivots.forEach(pivot => {
      pivot.duration = pivot.endTime - pivot.startTime;
    });

    console.log(pivots);

    return pivots;
  }

  buildPivotList() {
    let startTime = 0;
    let facings = [];
    const lastTurnEndMove = this.movementService.getLastTurnEndMove(this.ship);
    let lastFacing = lastTurnEndMove.facing;

    const pivots = [];

    let pivotStarted = false;

    const totalMovementLength = this.movementService
      .getThisTurnMovement(this.ship)
      .filter(move => move.isPivot() || move.isSpeed()).length;

    const moveStep = this.duration / totalMovementLength;
    const pivotStep = moveStep > 1000 ? 1000 : moveStep;

    let moves = this.movementService
      .getThisTurnMovement(this.ship)
      .filter(move => move.isPivot() || move.isSpeed())
      .map(move => {
        if (move.isPivot()) {
          return move.facing;
        } else if (move.isSpeed()) {
          return null;
        }
      });

    moves.push(null);

    moves.forEach(pivot => {
      if (pivot === null && pivotStarted) {
        let direction = facings[0] - lastFacing;
        if (direction === -5) {
          direction = 1;
        } else if (direction === 5) {
          direction = -1;
        }

        const start = mathlib.hexFacingToAngle(lastFacing);
        const end = mathlib.hexFacingToAngle(facings[facings.length - 1]);
        pivots.push({
          start,
          end,
          amount:
            mathlib.getDistanceBetweenDirections(start, end, direction) *
            direction,
          endTime: startTime * pivotStep
        });

        lastFacing = facings[facings.length - 1];
        facings = [];
        pivotStarted = false;
      } else if (pivot && !pivotStarted) {
        pivotStarted = true;
        facings.push(pivot);
      } else if (pivot) {
        facings.push(pivot);
      }

      startTime++;
    });

    /*
    if (pivotStarted) {
      pivots.push({
        facings: facings,
        startTime: startTime
      });
    }
    */

    return pivots;
  }

  buildPositionCurve() {
    const end = this.movementService.getLastEndMove(this.ship);
    const start = this.movementService.getLastTurnEndMove(this.ship) || end;

    if (!end || end === start) {
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

    return new THREE.CubicBezierCurve3(
      new THREE.Vector3(point1.x, point1.y, point1.z),
      new THREE.Vector3(control1.x, control1.y, control1.z),
      new THREE.Vector3(control2.x, control2.y, control2.z),
      new THREE.Vector3(point2.x, point2.y, point2.z)
    );
  }
}

window.ShipMovementAnimation = ShipMovementAnimation;

export default ShipMovementAnimation;
