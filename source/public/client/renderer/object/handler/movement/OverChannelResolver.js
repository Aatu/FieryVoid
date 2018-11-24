class OverChannelResolver {
  constructor(thrusters, movement) {
    this.thrusters = thrusters.map(thruster => ({
      channeled: 0,
      limit: thruster.output,
      thruster: thruster
    }));

    this.movement = movement;
  }

  getAmountOverChanneled() {
    this.movement
      .filter(move => move.requiredThrust)
      .forEach(move =>
        move.requiredThrust.getFulfilments().forEach(fulfilment => {
          this.track(fulfilment);
        })
      );

    return this.thrusters.reduce(
      (total, thruster) => total + this.getThrusterOverChannel(thruster),
      0
    );
  }

  track(fulfilments) {
    fulfilments.forEach(fulfilment => {
      const thruster = this.thrusters.find(
        thruster => thruster.thruster === fulfilment.thruster
      );

      thruster.channeled += fulfilment.amount;
    });
  }

  getThrusterOverChannel(thruster) {
    if (thruster.channeled > thruster.limit) {
      return thruster.channeled - thruster.limit;
    }

    return 0;
  }
}

export default OverChannelResolver;
