class ThrustAssignment {
  constructor(thruster) {
    this.thruster = thruster;

    this.directions = [].concat(thurster.direction);
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

  isMono(direction) {
    return this.directions.length === 1 && this.isDirection(direction);
  }

  isDirection(direction) {
    return this.directions.includes(direction);
  }

  getCost(direction, amount, damageLevel = 0) {
    if (!this.isDirection(direction)) {
      return null;
    }

    if (
      damageLevel === 0 &&
      ((this.firstIgnored && this.channeled === 0) || this.halfEfficiency)
    ) {
      return null;
    }

    if (damageLevel === 1 && this.halfEfficiency) {
      return null;
    }

    let extraCost = 0;

    if (this.firstIgnored && this.channeled === 0) {
      extraCost++;
    }

    if (this.halfEfficiency) {
      extraCost += amount;
    }

    const result = {
      capacity: this.capacity - this.channeled,
      overCapacity: 0,
      extraCost: this.firstIgnored && this.channeled === 0 ? 1 : 0,
      costMultiplier: this.halfEfficiency ? 1 : 0
    };

    if (!this.damaged) {
      result.overCapacity = this.capacity * 2 - this.channeled;

      if (result.overCapacity > this.capacity) {
        result.overCapacity = this.capacity;
      }
    }

    if (result.overCapacity < 0) {
      result.overCapacity = 0;
    }

    if (result.capacity < 0) {
      result.capacity = 0;
    }

    return result;
  }

  channel(direction, amount, overthrust = false) {
    if (!this.isDirection(direction)) {
      throw new Error("Trying to channel wrong direction");
    }

    const {
      capacity,
      overCapacity,
      extraCost,
      costMultiplier
    } = this.getForDirection(direction, amount, 2);

    const result = {
      channeled: 0,
      overthrusted: 0,
      extraCost: 0
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
        result.overthrusted = amount;
        amount = 0;
      } else {
        result.overthrusted = overCapacity;
      }
    }

    result.extraCost =
      (result.channeled + result.overthrusted) * costMultiplier + extraCost;

    this.channeled += result.channeled + result.overthrusted;
    return result;
  }
}

export default ThrustAssignment;
