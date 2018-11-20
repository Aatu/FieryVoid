class ThrustAssignment {
  constructor(thruster) {
    this.thruster = thruster;

    this.directions = [].concat(thruster.direction);
    this.paid = 0;
    this.channeled = 0;
    this.capacity = thruster.output;

    this.firstIgnored = window.shipManager.criticals.hasCritical(
      thruster,
      "FirstThrustIgnored"
    );

    this.halfEfficiency = window.shipManager.criticals.hasCritical(
      thruster,
      "HalfEfficiency"
    );

    this.damaged = this.firstIgnored || this.halfEfficiency;
  }

  isDirection(direction) {
    return this.directions.includes(direction);
  }

  canOverthrust() {
    return !this.damaged && this.channeled < this.capacity * 2;
  }

  getOverthrust() {
    let overThrust = this.channeled - this.capacity;
    if (overThrust < 0) {
      overThrust = 0;
    }

    return overThrust;
  }

  getDamageLevel() {
    if (this.firstIgnored && !this.halfEfficiency && this.channeled === 0) {
      return 1;
    } else if (
      this.halfEfficiency &&
      (!this.firstIgnored || this.channeled > 0)
    ) {
      return 2;
    } else if (
      this.halfEfficiency &&
      this.firstIgnored &&
      this.channeled === 0
    ) {
      return 3;
    } else {
      return 0;
    }
  }

  getThrustCapacity() {
    const result = {
      capacity: this.capacity - this.channeled,
      overCapacity: 0,
      extraCost: this.firstIgnored && this.channeled === 0 ? 1 : 0,
      costMultiplier: this.halfEfficiency ? 2 : 1
    };

    if (!this.damaged) {
      if (this.channeled <= this.capacity) {
        result.overCapacity = this.capacity;
      } else {
        result.overCapacity = this.capacity - (this.channeled - this.capacity);
      }
    }

    if (result.capacity < 0) {
      result.capacity = 0;
    }

    return result;
  }

  overChannel(amount) {
    return this.channel(amount, true);
  }

  channel(amount, overthrust = false) {
    const {
      capacity,
      overCapacity,
      extraCost,
      costMultiplier
    } = this.getThrustCapacity();

    const result = {
      channeled: 0,
      overChanneled: 0,
      cost: 0
    };

    if (capacity >= amount) {
      result.channeled = amount;
      amount = 0;
    } else {
      result.channeled = capacity;
      amount -= capacity;
    }

    if (amount > 0 && overthrust) {
      if (overCapacity >= amount) {
        result.overChanneled = amount;
        amount = 0;
      } else {
        result.overChanneled = overCapacity;
      }
    }

    result.cost =
      (result.channeled + result.overChanneled) * costMultiplier + extraCost;

    this.channeled += result.channeled + result.overChanneled;
    return result;
  }

  undoChannel(amount) {
    if (this.channeled - amount < 0) {
      throw new Error("Can not undo channel more than channeled");
    }

    this.channeled = this.channeled - amount;

    let extraRefund = 0;

    if (this.channeled === 0 && this.firstIgnored) {
      extraRefund = 1;
    }

    if (this.halfEfficiency) {
      return { refund: amount * 2 + extraRefund };
    } else {
      return { refund: amount + extraRefund };
    }
  }
}

export default ThrustAssignment;
