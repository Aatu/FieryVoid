import { movementTypes } from ".";

class MovementOrder {
  constructor(
    id,
    type,
    position,
    target,
    facing,
    rolled,
    turn,
    value = 0,
    requiredThrust = null
  ) {
    if (!(position instanceof window.hexagon.Offset)) {
      throw new Error("MovementOrder requires position as offset hexagon");
    }

    this.id = id;
    this.type = type;
    this.position = position;
    this.target = target;
    this.facing = facing;
    this.rolled = rolled;
    this.turn = turn;
    this.value = value;
    this.requiredThrust = requiredThrust;
  }

  serialize() {
    return {
      id: this.id,
      type: this.type,
      position: this.position,
      target: this.target,
      facing: this.facing,
      rolled: this.rolled,
      turn: this.turn,
      value: this.value,
      requiredThrust: this.requiredThrust
        ? this.requiredThrust.serialize()
        : null
    };
  }

  isSpeed() {
    return this.type === movementTypes.SPEED;
  }

  isDeploy() {
    return this.type === movementTypes.DEPLOY;
  }

  isStart() {
    return this.type === movementTypes.START;
  }

  isEvade() {
    return this.type === movementTypes.EVADE;
  }

  isRoll() {
    return this.type === movementTypes.ROLL;
  }

  isEnd() {
    return this.type === movementTypes.END;
  }

  isPivot() {
    return this.type === movementTypes.PIVOT;
  }

  isCancellable() {
    return this.isSpeed() || this.isPivot();
  }

  isPlayerAdded() {
    return this.isSpeed() || this.isPivot() || this.isEvade() || this.isRoll();
  }

  clone() {
    return new MovementOrder(
      this.id,
      this.type,
      this.position,
      this.target,
      this.facing,
      this.rolled,
      this.turn,
      this.value,
      this.requiredThrust
    );
  }

  isOpposite(move) {
    switch (move.type) {
      case movementTypes.SPEED:
        return (
          this.isSpeed() && this.value === mathlib.addToHexFacing(move.value, 3)
        );
      default:
        return false;
    }
  }
}

window.MovementOrder = MovementOrder;
export default MovementOrder;
