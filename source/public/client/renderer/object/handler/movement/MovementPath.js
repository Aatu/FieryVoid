class MovementPath {
  constructor(ship, movementService, scene) {
    this.ship = ship;
    this.movementService = movementService;
    this.scene = scene;

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

    const end = this.movementService.getPreviousTurnLastMove(this.ship);
    const move = this.movementService.getMostRecentMove(this.ship);
    const target = this.movementService.getCurrentMovementVector(this.ship);

    const line = createMovementLine(end.position, end.position.add(end.target), 0.2);
    this.scene.add(line.mesh);
    this.objects.push(line);

    const line2 = createMovementLine(end.position.add(end.target), end.position.add(target));
    this.scene.add(line2.mesh);
    this.objects.push(line2);

    const facing = createMovementFacing(move.facing, end.position.add(target));
    this.scene.add(facing.mesh);
    this.objects.push(facing);
  }
}

const createMovementLine = (position, target, opacity = 0.5) => {
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
    new THREE.Color(0, 0, 1),
    opacity
  );
};

const createMovementFacing = (facing, target) => {
  const size = window.coordinateConverter.getHexDistance() * 1.5;
  const facingSprite = new window.ShipFacingSprite(
    { width: size, height: size },
    0.01,
    0.8,
    facing
  );
  facingSprite.setPosition(
    window.coordinateConverter.fromHexToGame(target)
  );
  facingSprite.setFacing(mathlib.hexFacingToAngle(facing));

  return facingSprite;
};

export { createMovementLine };

export default MovementPath;
