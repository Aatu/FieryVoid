import {
  MovementOrder,
  movementTypes,
  ThrustBill,
  OverChannelResolver
} from ".";

class MovementResolver {
  constructor(ship, movementService, turn) {
    this.ship = ship;
    this.movementService = movementService;
    this.turn = turn;
  }

  billAndPay(bill, commit = true) {
    if (bill.pay()) {
      const newMovement = bill.getMoves();

      const initialOverChannel = new OverChannelResolver(
        this.movementService.getThrusters(this.ship),
        this.movementService.getThisTurnMovement(this.ship)
      ).getAmountOverChanneled();

      const newOverChannel = new OverChannelResolver(
        this.movementService.getThrusters(this.ship),
        newMovement
      ).getAmountOverChanneled();

      /*
      console.log(
        "overChannel",
        initialOverChannel,
        newOverChannel,
        newOverChannel - initialOverChannel
      );
      */

      if (commit) {
        this.movementService.replaceTurnMovement(this.ship, newMovement);
        this.movementService.shipMovementChanged(this.ship);
      }
      return {
        result: true,
        overChannel: newOverChannel - initialOverChannel > 0
      };
    } else if (commit) {
      throw new Error(
        "Tried to commit move that was not legal. Check legality first!"
      );
    } else {
      return false;
    }
  }

  canRoll() {
    return this.roll(false);
  }

  roll(commit = true) {
    let rollMove = this.movementService.getRollMove(this.ship);

    const endMove = this.movementService.getLastEndMove(this.ship);

    let movements = this.movementService.getThisTurnMovement(this.ship);

    if (rollMove) {
      movements = movements.filter(move => move !== rollMove);
    } else {
      rollMove = new MovementOrder(
        null,
        movementTypes.ROLL,
        endMove.position,
        new hexagon.Offset(0, 0),
        endMove.facing,
        endMove.rolled,
        this.turn,
        endMove.rolled ? false : true
      );

      const playerAdded = movements.filter(move => move.isPlayerAdded());
      const nonPlayerAdded = movements.filter(move => !move.isPlayerAdded());

      movements = [...nonPlayerAdded, rollMove, ...playerAdded];
    }

    const bill = new ThrustBill(
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      movements
    );

    return this.billAndPay(bill, commit);
  }

  canEvade(step) {
    return this.evade(step, false);
  }

  evade(step, commit = true) {
    let evadeMove = this.movementService.getEvadeMove(this.ship);

    const endMove = this.movementService.getLastEndMove(this.ship);

    if (evadeMove) {
      evadeMove = evadeMove.clone();

      if (
        evadeMove.value + step >
        this.movementService.getMaximumEvasion(this.ship)
      ) {
        return false;
      }

      if (evadeMove.value + step < 0) {
        return false;
      }
      evadeMove.value += step;
    } else {
      if (step < 0) {
        return false;
      }

      evadeMove = new MovementOrder(
        null,
        movementTypes.EVADE,
        endMove.position,
        new hexagon.Offset(0, 0),
        endMove.facing,
        endMove.rolled,
        this.turn,
        1
      );
    }

    const playerAdded = this.movementService
      .getThisTurnMovement(this.ship)
      .filter(
        move => move.isPlayerAdded() && !move.isRoll() && !move.isEvade()
      );
    const nonPlayerAdded = this.movementService
      .getThisTurnMovement(this.ship)
      .filter(move => !move.isPlayerAdded() || move.isRoll());

    const movements =
      evadeMove.value === 0
        ? [...nonPlayerAdded, ...playerAdded]
        : [...nonPlayerAdded, evadeMove, ...playerAdded];

    const bill = new ThrustBill(
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      movements
    );

    return this.billAndPay(bill, commit);
  }

  canPivot(pivotDirection) {
    return this.pivot(pivotDirection, false);
  }

  pivot(pivotDirection, commit = true) {
    const lastMove = this.movementService.getMostRecentMove(this.ship);
    const pivotMove = new MovementOrder(
      null,
      movementTypes.PIVOT,
      lastMove.position,
      new hexagon.Offset(0, 0),
      mathlib.addToHexFacing(lastMove.facing, pivotDirection),
      lastMove.rolled,
      this.turn,
      pivotDirection
    );

    const movements = this.movementService.getThisTurnMovement(this.ship);

    if (lastMove.isPivot() && lastMove.value !== pivotDirection) {
      movements.pop();
    } else {
      movements.push(pivotMove);
    }

    const bill = new ThrustBill(
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      movements
    );

    return this.billAndPay(bill, commit);
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
      lastMove.rolled,
      this.turn,
      direction
    );

    let movements = this.movementService.getThisTurnMovement(this.ship);
    const opposite = this.getOpposite(movements, thrustMove);
    if (opposite) {
      movements = movements.filter(move => move !== opposite);
    } else {
      movements.push(thrustMove);
    }

    const bill = new ThrustBill(
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      movements
    );

    return this.billAndPay(bill, commit);
  }

  canCancel() {
    return this.movementService
      .getThisTurnMovement(this.ship)
      .some(move => move.isCancellable());
  }

  cancel() {
    const toCancel = this.ship.movement[this.ship.movement.length - 1];

    if (!toCancel || !toCancel.isCancellable()) {
      return;
    }

    this.removeMove(toCancel);

    const bill = new ThrustBill(
      this.ship,
      this.movementService.getTotalProducedThrust(this.ship),
      this.movementService.getThisTurnMovement(this.ship)
    );

    return this.billAndPay(bill, true);
  }

  canRevert() {
    return this.movementService
      .getThisTurnMovement(this.ship)
      .some(move => move.isPlayerAdded());
  }

  revert() {
    this.movementService
      .getThisTurnMovement(this.ship)
      .filter(move => move.isCancellable() || move.isEvade())
      .forEach(this.removeMove.bind(this));

    this.movementService.shipMovementChanged(this.ship);
  }

  getOpposite(movements, move) {
    return movements.find(other => other.isOpposite(move));
  }

  removeMove(move) {
    this.ship.movement = this.ship.movement.filter(other => other !== move);
  }
}

export default MovementResolver;
