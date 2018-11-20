import { movementTypes } from ".";

class RequiredThrust {
  constructor(ship, move) {
    this.requirements = {};

    switch (move.type) {
      case movementTypes.SPEED:
        this.requireSpeed(ship, move);
        break;
      default:
    }
  }

  requireSpeed(ship, move) {
    const facing = move.facing;
    const direction = move.value;
    const actualDirection = window.mathlib.addToHexFacing(
      window.mathlib.addToHexFacing(direction, facing),
      3
    );

    this.requirements[actualDirection] = ship.accelcost;
  }

  accumulate(total) {
    Object.keys(this.requirements).forEach(direction => {
      total[direction] = total[direction]
        ? total[direction] + this.requirements[direction]
        : this.requirements[direction];
    });

    return total;
  }
}

export default RequiredThrust;
