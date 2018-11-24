class MovementPath {
  constructor(ship, movementService, scene) {
    this.ship = ship;
    this.movementService = movementService;
    this.scene = scene;

    this.color = new THREE.Color(132 / 255, 165 / 255, 206 / 255);

    this.objects = [];

    this.create();
  }

  remove() {
    this.objects.forEach(object3d => {
      this.scene.remove(object3d.mesh);
      object3d.destroy();
    });
  }

  create() {
    const deployMovement = this.movementService.getDeployMove(this.ship);

    if (!deployMovement) {
      return;
    }

    const end = this.movementService.getLastEndMove(this.ship);
    const move = this.movementService.getMostRecentMove(this.ship);
    const target = this.movementService.getCurrentMovementVector(this.ship);

    const startPosition = end.position;
    const middlePosition = end.position.add(end.target);
    const finalPosition = startPosition.add(target);

    const line = createMovementLine(
      startPosition,
      middlePosition,
      this.color,
      0.5
    );
    this.scene.add(line.mesh);
    this.objects.push(line);

    if (!middlePosition.equals(finalPosition)) {
      const middle = createMovementMiddleStep(middlePosition, this.color);
      this.scene.add(middle.mesh);
      this.objects.push(middle);
    }

    const line2 = createMovementLine(middlePosition, finalPosition, this.color);
    this.scene.add(line2.mesh);
    this.objects.push(line2);

    const facing = createMovementFacing(move.facing, finalPosition, this.color);
    this.scene.add(facing.mesh);
    this.objects.push(facing);
  }
}

const createMovementMiddleStep = (position, color) => {
  const size = window.coordinateConverter.getHexDistance() * 0.5;
  const circle = new window.ShipSelectedSprite(
    { width: size, height: size },
    0.01,
    1.6
  );
  circle.setPosition(window.coordinateConverter.fromHexToGame(position));
  circle.setOverlayColor(color);
  circle.setOverlayColorAlpha(1);
  return circle;
};

const createMovementLine = (position, target, color, opacity = 0.8) => {
  const start = window.coordinateConverter.fromHexToGame(position);
  const end = window.coordinateConverter.fromHexToGame(target);

  return new window.LineSprite(
    mathlib.getPointBetweenInDistance(
      start,
      end,
      window.coordinateConverter.getHexDistance() * 0.45,
      true
    ),
    mathlib.getPointBetweenInDistance(
      end,
      start,
      window.coordinateConverter.getHexDistance() * 0.45,
      true
    ),
    10,
    color,
    opacity
  );
};

const createMovementFacing = (facing, target, color) => {
  const size = window.coordinateConverter.getHexDistance() * 1.5;
  const facingSprite = new window.ShipFacingSprite(
    { width: size, height: size },
    0.01,
    1.6,
    facing
  );
  facingSprite.setPosition(window.coordinateConverter.fromHexToGame(target));
  facingSprite.setOverlayColor(color);
  facingSprite.setOverlayColorAlpha(1);
  facingSprite.setFacing(mathlib.hexFacingToAngle(facing));

  return facingSprite;
};

export { createMovementLine };

export default MovementPath;
