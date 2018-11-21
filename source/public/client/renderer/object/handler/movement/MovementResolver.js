import { MovementOrder, movementTypes, ThrustBill } from ".";

class MovementResolver {
  constructor(ship, movementService) {
    this.ship = ship;
    this.movementService = movementService;
  }

  canThrust(direction) {
    return this.thrust(direction, false);
  }

  thrust(direction, commit = true) {
    console.log("Thrustage", direction, commit);

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
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      [...this.movementService.getThisTurnMovement(this.ship), thrustMove]
    );

    if (bill.pay()) {
      if (commit) {
        bill.commit();
        this.addMove(thrustMove);
      }
      return true;
    } else if (commit) {
      throw new Error(
        "Tried to commit move that was not legal. Check legality first!"
      );
    } else {
      return false;
    }
  }

  addMove(move) {
    this.ship.movement.push(move);
    this.movementService.shipMovementChanged(this.ship);
  }
}

export default MovementResolver;
