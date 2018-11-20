import { RequiredThrust, ThrustAssignment } from ".";

class ThrustBill {
  constructor(ship, thrustAvailable, movement) {
    this.ship = ship;
    this.movement = movement;
    this.thrusters = ship.systems
      .filter(system => system.thruster)
      .map(thruster => new ThrustAssignment(thruster));

    this.buildRequiredThrust(movement);

    this.cost = 0;
    this.thrustAvailable = thrustAvailable;
    this.totalThrustRequired = this.getTotalThrustRequired(movement);
    this.directionsRequired = this.getRequiredThrustDirections(movement);
  }

  getRequiredThrustDirections(movement) {
    const result = movement.reduce(
      (accumulator, move) => move.requiredThrust.accumulate(accumulator),
      {}
    );

    result[0] = result[0] || 0;
    result[1] = result[1] || 0;
    result[2] = result[2] || 0;
    result[3] = result[3] || 0;
    result[4] = result[4] || 0;
    result[5] = result[5] || 0;

    return result;
  }

  getTotalThrustRequired(movement) {
    const totalRequired = this.getRequiredThrustDirections(movement);
    return (
      totalRequired[0] +
      totalRequired[1] +
      totalRequired[2] +
      totalRequired[3] +
      totalRequired[4] +
      totalRequired[5]
    );
  }

  getMonoThrustersForDirection(
    direction,
    amount,
    allowOverthrust = false,
    damageLevel = 0
  ) {
    return getThrustersForDirection(
      direction,
      amount,
      allowOverthrust,
      damageLevel
    ).filter(thruster => thruster.isMono(direction));
  }

  thrusterhasCapacity(
    direction,
    amount,
    allowOverthrust,
    damageLevel,
    thruster
  ) {
    const result = this.thrusters.getCost(direction, amount, damageLevel);
    if (result === null) {
      return false;
    }

    const { capacity, overCapacity, extraCost, costMultiplier } = this.result;

    return capacity > 0 || (allowOverthrust && overCapacity > 0);
  }

  sortThrusters(direction, amount, allowOverthrust, damageLevel, a, b) {
    const { capacity: capacityA, overCapacity: overCapacityA } = a.getCost(
      direction,
      amount,
      damageLevel
    );
    const { capacity: capacityB, overCapacity: overCapacityB } = b.getCost(
      direction,
      amount,
      damageLevel
    );

    if (allowOverthrust) {
      return capacityA + overCapacityA > capacityB + overCapacityB;
    }

    return capacityA > capacityB;
  }

  thrusterCanAffordedToBeUsed(
    direction,
    amount,
    allowOverthrust,
    damageLevel,
    thruster
  ) {
    const result = this.thrusters.getCost(direction, amount, damageLevel);
    if (result === null) {
      return false;
    }

    let wouldChannel = 0;

    //need and can overthrust
    if (allowOverthrust && capacity < amount) {
      //overthrust is not enough
      if (amount - capacity > overCapacity) {
        wouldChannel = capacity + overCapacity;
      } else if (amount > capacity) {
        //overthrust is enough
        wouldChannel = amount;
      }
    } else if (capacity < amount) {
      //can't overthrust, not enough capacity
      wouldChannel = capacity;
    } else if (capacity > amount) {
      //capacity is enough;
      wouldChannel = amount;
    }

    if (wouldChannel * costMultiplier + extraCost > this.thrustAvailable) {
      return false;
    }

    const { capacity, overCapacity, extraCost, costMultiplier } = this.result;
  }

  getThrustersForDirection(
    direction,
    amount,
    allowOverthrust = false,
    damageLevel = 0
  ) {
    this.thrusters
      .filter(thruster =>
        this.thrusterhasCapacity.bind(
          this,
          direction,
          amount,
          allowOverthrust,
          damageLevel
        )
      )
      .filter(thruster =>
        this.thrusterCanAffordedToBeUsed.bind(
          this,
          direction,
          amount,
          allowOverthrust,
          damageLevel
        )
      )
      .sort(
        this.sortThrusters.bind(
          this,
          direction,
          amount,
          allowOverthrust,
          damageLevel
        )
      );
  }

  errorIfOverBudget() {
    if (this.totalThrustRequired > this.thrustAvailable) {
      throw new Error("over budget");
    }
  }

  pay() {
    try {
      this.errorIfOverBudget();

      //assign thrust first to mono direction thrusters
      this.assignUndamagedMono();

      //overthrust here

      //if paid with overthrust, try to move trust to damaged thrusters
      //if not enough even with overthrust, try to assign to damaged thrusters and after that move overthrust to damaged

      //move thrust to lvl 1 damage mono

      //move thrust to lvl2 damage mono

      //move thrust to lvl3 damage mono

      //if still not satisfied, not possible

      return true;
    } catch (e) {
      if (e.message === "over budget") {
        return false;
      }

      throw e;
    }
  }

  assignUndamagedMono() {
    Object.keys(this.directionsRequired).forEach(assignUndamagedMonoDirection);
  }

  assignUndamagedMonoDirection(direction) {
    const required = this.directionsRequired[direction];
    const thrusters = this.getMonoThrustersForDirection(
      direction,
      required,
      false,
      0
    );

    this.useThrusters(direction, required, thrusters, false, 0);
  }

  assignDamagedMono(damageLevel) {
    Object.keys(this.directionsRequired).forEach(
      assignUndamagedMonoDirection.bind(this, damageLevel)
    );
  }

  assignDamagedMonoDirection(damageLevel, direction) {
    const required = this.directionsRequired[direction];
    const thrusters = this.getMonoThrustersForDirection(
      direction,
      required,
      false,
      damageLevel
    );

    this.useThrusters(direction, required, thrusters, false, 0);
  }

  useThrusters(
    direction,
    required,
    thrusters,
    allowOverthrust = false,
    damageLevel = 0
  ) {
    thrusters.forEach(thruster => {
      if (required <= 0) {
        return;
      }

      const { channeled, overthrusted, extraCost } = thruster.channel(
        direction,
        required,
        allowOverthrust
      );

      this.directionsRequired[direction] -= channeled;
      this.directionsRequired[direction] -= overthrusted;
      this.totalThrustRequired += extraCost;

      required -= channeled;
      required -= overthrusted;

      this.errorIfOverBudget();
    });
  }

  buildRequiredThrust(movement) {
    movement.forEach(
      move => (move.requiredThrust = new RequiredThrust(this.ship, move))
    );
  }
}

export default ThrustBill;
