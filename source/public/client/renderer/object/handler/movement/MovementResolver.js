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
    const lastMove = this.movementService.getMostRecentMove(this.ship);

    const thrustMove = new MovementOrder(
      null,
      movementTypes.SPEED,
      lastMove.position,
      new hexagon.Offset(0, 0).moveToDirection(direction),
      lastMove.facing,
      lastMove.turn,
      direction
    );

    const movements = this.movementService.getThisTurnMovement(this.ship);

    if (this.getOpposite(movements, thrustMove)) {
      if (commit) {
        this.removeOpposite(movements, thrustMove);
      }
      return true;
    }

    const bill = new ThrustBill(
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      [...movements, thrustMove]
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

  canCancel() {
    return this.movementService
      .getThisTurnMovement(this.ship)
      .some(move => move.isCancellable());
  }

  cancel() {
    const toCancel = this.movementService
      .getThisTurnMovement(this.ship)
      .reverse()
      .find(move => move.isCancellable());

    if (!toCancel) {
      return;
    }

    this.removeMove(toCancel);
    this.movementService.shipMovementChanged(this.ship);
  }

  revert() {
    this.movementService
      .getThisTurnMovement(this.ship)
      .filter(move => move.isCancellable())
      .forEach(this.removeMove.bind(this));

    this.movementService.shipMovementChanged(this.ship);
  }

  addMove(move) {
    this.ship.movement.push(move);
    this.movementService.shipMovementChanged(this.ship);
  }

  getOpposite(movements, move) {
    return movements.find(other => other.isOpposite(move));
  }

  removeOpposite(movements, move) {
    const opposite = this.getOpposite(movements, move);
    this.ship.movement = this.ship.movement.filter(other => other !== opposite);
    this.movementService.shipMovementChanged(this.ship);
  }

  removeMove(move) {
    this.ship.movement = this.ship.movement.filter(other => other !== move);
  }
}

export default MovementResolver;
