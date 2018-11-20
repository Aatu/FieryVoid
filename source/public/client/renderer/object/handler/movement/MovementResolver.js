import { MovementOrder, movementTypes, ThrustBill } from ".";

class MovementResolver {
  constructor(ship, movementService) {
    this.ship = ship;
    this.movementService = movementService;
  }

  thrust(direction) {
    console.log("Thrustage", direction);

    ship.movement;
    const lastMove = this.movementService.getMostRecentMove(this.ship);

    const thrustMove = new MovementOrder(
      null,
      movementTypes.SPEED,
      lastMove.position,
      lastMove.target.moveToDirection(direction),
      lastMove.facing,
      lastMove.turn,
      direction
    );

    const bill = new ThrustBill(
      ship,
      this.movementService.getTotalProducedThrust(ship)
    ).pay([...this.movementService.cloneThisTurnMovement(ship), thrustMove]);
  }
}

export default MovementResolver;
