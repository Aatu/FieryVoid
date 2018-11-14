import { createMovementLine } from "./MovementPath";

class MovementPathDeployment {
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
    const firstMovement = this.movementService.getDeployMove(this.ship);

    if (!firstMovement) {
      return;
    }

    const lastMovement =
      this.moved && this.movementService.getMostRecentMove(this.ship);

    const line = createMovementLine(firstMovement);

    this.scene.add(line.mesh);
    this.objects.push(line);
  }
}

export default MovementPathDeployment;
