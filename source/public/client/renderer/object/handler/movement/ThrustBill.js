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
      totalRequired[5]
    );
  }

  getCurrentThrustRequired() {
      return (
        this.directionsRequired[0] +
        this.directionsRequired[1] +
        this.directionsRequired[2] +
        this.directionsRequired[3] +
        this.directionsRequired[4] +
        this.directionsRequired[5]
      );
  }

  isPaid() {
    return this.getCurrentThrustRequired() === 0;
  }

  getUndamagedThrusters(direction) {
    return this.thrusters.filter(
      thruster => {
        return thruster.getDamageLevel() === 0 && thruster.isDirection(direction)
      }
    );
  }

  errorIfOverBudget() {
    if (this.cost > this.thrustAvailable) {
      throw new Error("over budget");
    }
  }

  pay() {
    try {
      if (this.getTotalThrustRequired() > this.thrustAvailable) {
        throw new Error("over budget");
      }

      //use norma
      if (
        this.process(direction => this.getUndamagedThrusters(direction), false)
      ) {
        return true;
      }

     
        this.process(direction => this.getUndamagedThrusters(direction), true)
      
      //assign thrust first to mono direction thrusters

      //overthrust here

      //if paid with overthrust, try to move trust to damaged thrusters
      //if not enough even with overthrust, try to assign to damaged thrusters and after that move overthrust to damaged

      //move thrust to lvl 1 damage mono

      //move thrust to lvl2 damage mono

      //move thrust to lvl3 damage mono

      //if still not satisfied, not possible
      return this.isPaid();
    } catch (e) {
      if (e.message === "over budget") {
        return false;
      }

      throw e;
    }
  }

  process(thrusterProvider, overChannel = false) {
    Object.keys(this.directionsRequired).forEach(direction => {
        const required = this.directionsRequired[direction];
        direction = parseInt(direction, 10);
        
        if (required === 0) {
            return;
        }

      const thrusters = thrusterProvider(direction);
      this.useThrusters(
        direction,
        required,
        thrusters,
        overChannel
      );
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


      const { channeled, overChanneled, cost } = thruster.channel(
        required,
        allowOverChannel
      );

      this.directionsRequired[direction] -= channeled;
      this.directionsRequired[direction] -= overChanneled;
      this.cost += cost;

      required -= channeled;
      required -= overChanneled;

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
