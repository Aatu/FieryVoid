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
      5: [],
      6: []
    };

    switch (move.type) {
      case movementTypes.SPEED:
        this.requireSpeed(ship, move);
        break;
      case movementTypes.PIVOT:
        this.requirePivot(ship);
        break;
      case movementTypes.ROLL:
        this.requireRoll(ship);
        break;
      case movementTypes.EVADE:
        this.requireEvade(ship, move);
        break;
      default:
    }
  }

  serialize() {
    const fullfilments = {
      0: [],
      1: [],
      2: [],
      3: [],
      4: [],
      5: [],
      6: []
    };

    Object.keys(this.fullfilments).forEach(direction => {
      const entryArray = this.fullfilments[direction].map(fulfilment => {
        return {
          amount: fulfilment.amount,
          thrusterId: fulfilment.thruster.id
        };
      });

      fullfilments[direction] = entryArray;
    });

    return {
      requirements: this.requirements,
      fullfilments
    };
  }

  getTotalAmountRequired() {
    return Object.keys(this.requirements).reduce((total, direction) => {
      const required = this.requirements[direction] || 0;
      return total + required;
    }, 0);
  }

  getRequirement(direction) {
    if (!this.requirements[direction]) {
      return 0;
    }

    return this.requirements[direction] - this.getFulfilledAmount(direction);
  }

  isFulfilled() {
    return Object.keys(this.requirements).every(
      direction => this.getRequirement(direction) === 0
    );
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

  getFulfilments() {
    return Object.keys(this.fullfilments)
      .map(key => this.fullfilments[key])
      .filter(fulfillment => fulfillment.length > 0);
  }

  requireRoll(ship) {
    this.requirements[6] = ship.rollcost;
  }

  requireEvade(ship, move) {
    this.requirements[6] = ship.evasioncost * move.value;
  }

  requirePivot(ship) {
    this.requirements[6] = ship.pivotcost;
  }

  requireSpeed(ship, move) {
    const facing = move.facing;
    const direction = move.value;
    const actualDirection = window.mathlib.addToHexFacing(
      window.mathlib.addToHexFacing(direction, -facing),
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
