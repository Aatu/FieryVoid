import { movementTypes } from ".";

class RequiredThrust {
  constructor(ship, move) {
    this.requirements = {};
    this.fullfilments = {
      0: [],
      1: [],
      2: [],
      3: [],
      4: [],
      5: []
    };

    switch (move.type) {
      case movementTypes.SPEED:
        this.requireSpeed(ship, move);
        break;
      default:
    }
  }

  getRequirement(direction) {
    if (!this.requirements[direction]) {
      return 0;
    }

    return this.requirements[direction] - this.getFulfilledAmount(direction);
  }

  fulfill(direction, amount, thruster) {
    this.fullfilments[direction].push({ amount, thruster });
    if (this.requirements[direction] < this.getFulfilledAmount(direction)) {
      throw new Error("Fulfilled too much!");
    }
  }

  getFulfilledAmount(direction) {
    return this.fullfilments[direction].reduce(
      (total, entry) => total + entry.amount,
      0
    );
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
