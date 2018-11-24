import { RequiredThrust, ThrustAssignment } from ".";

class ThrustBill {
  constructor(ship, thrustAvailable, movement) {
    this.ship = ship;
    this.movement = movement.map(move => move.clone());
    this.thrusters = ship.systems
      .filter(system => system.thruster)
      .filter(system => !system.isDestroyed())
      .map(thruster => new ThrustAssignment(thruster));

    this.buildRequiredThrust(this.movement);

    this.paid = null;

    this.cost = 0;
    this.thrustAvailable = thrustAvailable;
    this.directionsRequired = this.getRequiredThrustDirections();
  }

  getRequiredThrustDirections() {
    const result = this.movement.reduce(
      (accumulator, move) => move.requiredThrust.accumulate(accumulator),
      {}
    );

    result[0] = result[0] || 0;
    result[1] = result[1] || 0;
    result[2] = result[2] || 0;
    result[3] = result[3] || 0;
    result[4] = result[4] || 0;
    result[5] = result[5] || 0;
    result[6] = result[6] || 0;

    return result;
  }

  getTotalThrustRequired() {
    const totalRequired = this.getRequiredThrustDirections();
    return (
      totalRequired[0] +
      totalRequired[1] +
      totalRequired[2] +
      totalRequired[3] +
      totalRequired[4] +
      totalRequired[5] +
      totalRequired[6]
    );
  }

  getCurrentThrustRequired() {
    return (
      this.directionsRequired[0] +
      this.directionsRequired[1] +
      this.directionsRequired[2] +
      this.directionsRequired[3] +
      this.directionsRequired[4] +
      this.directionsRequired[5] +
      this.directionsRequired[6]
    );
  }

  isPaid() {
    return this.getCurrentThrustRequired() === 0;
  }

  getUndamagedThrusters(direction) {
    return this.thrusters
      .filter(thruster => {
        return (
          thruster.getDamageLevel() === 0 && thruster.isDirection(direction)
        );
      })
      .sort(this.sortThrusters);
  }

  getAllUsableThrusters(direction) {
    return this.thrusters
      .filter(thruster => {
        const { capacity, overCapacity } = thruster.getThrustCapacity();

        return (
          thruster.isDirection(direction) && (capacity > 0 || overCapacity > 0)
        );
      })
      .sort(this.sortThrusters);
  }

  getOverChannelers(direction) {
    return this.thrusters
      .filter(thruster => thruster.isDirection(direction))
      .filter(thruster => thruster.getOverChannel() > 0)
      .filter(thruster => !thruster.isDamaged())
      .sort(this.sortThrusters);
  }

  getNonOverChannelers(direction) {
    const overChannelers = this.getOverChannelers(direction);
    return this.getAllUsableThrusters(direction)
      .filter(thruster => thruster.canChannel())
      .filter(thruster => !overChannelers.includes(thruster));
  }

  sortThrusters(a, b) {
    const damageA = a.getDamageLevel();
    const damageB = b.getDamageLevel();

    if (damageA !== damageB) {
      if (damageA > damageB) {
        return 1;
      } else {
        return -1;
      }
    }

    if (a.firstIgnored && !b.firstIgnored) {
      return -1;
    } else if (b.firstIgnored && !a.firstIgnored) {
      return 1;
    }

    const {
      capacity: capacityA,
      overCapacity: overCapacityA
    } = a.getThrustCapacity();
    const {
      capacity: capacityB,
      overCapacity: overCapacityB
    } = b.getThrustCapacity();

    if (capacityA !== capacityB) {
      if (capacityA > capacityB) {
        return -1;
      } else {
        return 1;
      }
    }

    if (overCapacityA !== overCapacityB) {
      if (overCapacityA > overCapacityB) {
        return -1;
      } else {
        return 1;
      }
    }

    return 0;
  }

  isOverChanneled() {
    return this.thrusters.some(thruster => thruster.getOverChannel() > 0);
  }

  errorIfOverBudget() {
    if (this.isOverBudget()) {
      throw new Error("over budget");
    }
  }

  isOverBudget() {
    return this.cost > this.thrustAvailable;
  }

  pay() {
    if (this.paid !== null) {
      throw new Error("Thrust bill is already paid!");
    }

    try {
      if (this.getTotalThrustRequired() > this.thrustAvailable) {
        throw new Error("over budget");
      }

      if (
        this.process(direction => this.getUndamagedThrusters(direction), false) //do not overthrust
      ) {
        return true;
      }

      this.process(direction => this.getUndamagedThrusters(direction), true); //OVERTHRUST

      this.process(direction => this.getAllUsableThrusters(direction), true); //use damaged thrusters too

      this.reallocateOverChannelForAllDirections(); //try to move over channel from good thrusters to already damaged ones

      this.paid = this.isPaid();
      return this.paid;
    } catch (e) {
      if (e.message === "over budget") {
        this.paid = false;
        return this.paid;
      }

      throw e;
    }
  }

  reallocateOverChannelForAllDirections() {
    Object.keys(this.directionsRequired).forEach(direction => {
      direction = parseInt(direction, 10);

      this.reallocateOverChannel(direction);
    });
  }

  reallocateOverChannel(direction) {
    const overChannelers = this.getOverChannelers(direction);

    overChannelers.forEach(thruster =>
      this.reallocateSingleOverChannelThruster(
        thruster,
        direction,
        this.getNonOverChannelers(direction)
      )
    );
  }

  reallocateSingleOverChannelThruster(thruster, direction, otherThrusters) {
    if (otherThrusters.length === 0) {
      return;
    }

    otherThrusters.forEach(otherThruster => {
      while (thruster.getOverChannel() > 0) {
        const { capacity } = otherThruster.getThrustCapacity();

        if (capacity === 0) {
          return;
        }

        this.undoThrusterUse(thruster, direction, 1);

        this.useThruster(otherThruster, direction, 1);

        if (this.isOverBudget()) {
          this.undoThrusterUse(otherThruster, direction, 1);
          this.useThruster(thruster, direction, 1, true);
          return; //tried to, but best thruster was too expensive
        }
      }
    });
  }

  process(thrusterProvider, overChannel = false) {
    Object.keys(this.directionsRequired).forEach(direction => {
      const required = this.directionsRequired[direction];
      direction = parseInt(direction, 10);

      if (required === 0) {
        return;
      }

      const thrusters = thrusterProvider(direction);
      this.useThrusters(direction, required, thrusters, overChannel);
    });

    return this.isPaid();
  }

  useThrusters(direction, required, thrusters, allowOverChannel = false) {
    thrusters.forEach(thruster => {
      if (required <= 0) {
        return;
      }

      if (!thruster.isDirection(direction)) {
        throw new Error("Trying to use thruster to wrong direction");
      }

      required = this.useThruster(
        thruster,
        direction,
        required,
        allowOverChannel
      );

      this.errorIfOverBudget();
    });
  }

  useThruster(thruster, direction, amount, allowOverChannel = false) {
    const { channeled, overChanneled, cost } = thruster.channel(
      amount,
      allowOverChannel
    );

    this.directionsRequired[direction] -= channeled;
    this.directionsRequired[direction] -= overChanneled;

    this.cost += cost;

    amount -= channeled;
    amount -= overChanneled;

    return amount;
  }

  undoThrusterUse(thruster, direction, amount) {
    this.cost -= thruster.undoChannel(amount).refund;
    this.directionsRequired[direction] += amount;
  }

  buildRequiredThrust(movement) {
    movement.forEach(
      move => (move.requiredThrust = new RequiredThrust(this.ship, move))
    );
  }

  getMoves() {
    this.thrusters.forEach(thruster => {
      let channeled = thruster.channeled;
      this.movement.forEach(move => {
        thruster.directions.forEach(direction => {
          if (channeled === 0) {
            return;
          }

          const required = move.requiredThrust.getRequirement(direction);

          if (required === 0) {
            return;
          }

          if (required > channeled) {
            move.requiredThrust.fulfill(
              direction,
              channeled,
              thruster.thruster
            );
            channeled = 0;
          } else {
            move.requiredThrust.fulfill(direction, required, thruster.thruster);
            channeled -= required;
          }
        });
      });
    });

    if (!this.movement.every(move => move.requiredThrust.isFulfilled())) {
      throw new Error("Not all moves are fulfilled");
    }

    return this.movement;
  }
}

export default ThrustBill;
