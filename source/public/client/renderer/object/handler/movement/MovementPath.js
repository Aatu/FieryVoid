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

    const move = this.movementService.getMostRecentMove(this.ship);

    const line = createMovementLine(move);
    this.scene.add(line.mesh);
    this.objects.push(line);

    const facing = createMovementFacing(move);
    this.scene.add(facing.mesh);
    this.objects.push(facing);
  }
}

const createMovementLine = move => {
  const start = window.coordinateConverter.fromHexToGame(move.position);
  const end = window.coordinateConverter.fromHexToGame(
    move.position.add(move.target)
  );

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
    0.5
  );
};

const createMovementFacing = move => {
  const size = window.coordinateConverter.getHexDistance() * 1.5;
  const facing = new window.ShipFacingSprite(
    { width: size, height: size },
    0.01,
    0.8,
    move.facing
  );
  facing.setPosition(
    window.coordinateConverter.fromHexToGame(move.position.add(move.target))
  );
  facing.setFacing(mathlib.hexFacingToAngle(move.facing));

  return facing;
};

export { createMovementLine };

export default MovementPath;
