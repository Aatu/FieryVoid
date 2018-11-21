import ThrustAssignment from "../../../source/public/client/renderer/object/handler/movement/ThrustAssignment";
require("../../../source/public/client/model/hexagon/Offset.js");
require("../../../source/public/client/model/hexagon/Cube.js");
require("../../../source/public/client/mathlib.js");

const getThruster = (direction = 0, output = 3, criticals = []) => ({
  direction,
  output,
  criticals
});

window.shipManager = {
  criticals: {
    hasCritical: (thruster, name) => thruster.criticals.includes(name)
  }
};

test("isDirection", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));
  expect(thrustAssignment.isDirection(0)).toBe(true);
  expect(thrustAssignment.isDirection(3)).toBe(false);
});

test("getDamageLevel", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));
  expect(thrustAssignment.getDamageLevel()).toBe(0);

  const thrustAssignment2 = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored"])
  );

  expect(thrustAssignment2.getDamageLevel()).toBe(1);
  thrustAssignment2.channel(1);
  expect(thrustAssignment2.getDamageLevel()).toBe(0);

  const thrustAssignment3 = new ThrustAssignment(
    getThruster(0, 6, ["HalfEfficiency"])
  );
  expect(thrustAssignment3.getDamageLevel()).toBe(2);

  const thrustAssignment4 = new ThrustAssignment(
    getThruster(0, 6, ["HalfEfficiency", "FirstThrustIgnored"])
  );
  expect(thrustAssignment4.getDamageLevel()).toBe(3);
  thrustAssignment4.channel(1);
  expect(thrustAssignment4.getDamageLevel()).toBe(2);
});

test("getCost, no damage, no overthrust", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));
  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(6);
  expect(overCapacity).toBe(6);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("channel wihtout overthrusting", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));

  const { channeled, overChanneled, cost } = thrustAssignment.channel(3);

  expect(channeled).toBe(3);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(3);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(3);
  expect(overCapacity).toBe(6);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("overchannel a lot", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));

  const { channeled, overChanneled, cost } = thrustAssignment.overChannel(9);

  expect(channeled).toBe(6);
  expect(overChanneled).toBe(3);
  expect(cost).toBe(9);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(0);
  expect(overCapacity).toBe(3);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("channel more than capacity", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));

  const { channeled, overChanneled, cost } = thrustAssignment.channel(9);

  expect(channeled).toBe(6);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(6);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(0);
  expect(overCapacity).toBe(6);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("over channel more than capacity", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));

  const { channeled, overChanneled, cost } = thrustAssignment.overChannel(19);

  expect(channeled).toBe(6);
  expect(overChanneled).toBe(6);
  expect(cost).toBe(12);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(0);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("capacity with damaged thruster correct", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored"])
  );

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(6);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(1);
  expect(costMultiplier).toBe(1);
});

test("channel with first thrust ignored thruster", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored"])
  );

  const { channeled, overChanneled, cost } = thrustAssignment.channel(2);

  expect(channeled).toBe(2);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(3);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(4);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("second channel with first thrust ignored causes no extra cost", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored"])
  );

  thrustAssignment.channel(2);
  const { channeled, overChanneled, cost } = thrustAssignment.channel(2);

  expect(channeled).toBe(2);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(2);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(2);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("half efficiency doubles the cost", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["HalfEfficiency"])
  );

  const { channeled, overChanneled, cost } = thrustAssignment.channel(4);

  expect(channeled).toBe(4);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(8);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(2);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(2);
});

test("half efficiency and first thrust ignores combine properly", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["HalfEfficiency", "FirstThrustIgnored"])
  );

  const { channeled, overChanneled, cost } = thrustAssignment.channel(4);

  expect(channeled).toBe(4);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(9);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(2);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(2);
});

test("half efficiency and first thrust ignored counts first only once", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["HalfEfficiency", "FirstThrustIgnored"])
  );

  thrustAssignment.channel(2);
  const { channeled, overChanneled, cost } = thrustAssignment.channel(4);

  expect(channeled).toBe(4);
  expect(overChanneled).toBe(0);
  expect(cost).toBe(8);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(0);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(2);
});

test("undoing channel works and returns refund", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));

  thrustAssignment.channel(2);
  const { refund } = thrustAssignment.undoChannel(2);

  expect(refund).toBe(2);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(6);
  expect(overCapacity).toBe(6);
  expect(extraCost).toBe(0);
  expect(costMultiplier).toBe(1);
});

test("undoing throws if trying to undo more than channeled", () => {
  const thrustAssignment = new ThrustAssignment(getThruster(0, 6));

  thrustAssignment.channel(2);
  expect(() => thrustAssignment.undoChannel(4)).toThrow(
    "Can not undo channel more than channeled"
  );
});

test("undoing channel refunds first thrust ignored", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored"])
  );

  thrustAssignment.channel(2);
  const { refund } = thrustAssignment.undoChannel(1);

  expect(refund).toBe(1);

  const { refund: refund2 } = thrustAssignment.undoChannel(1);

  expect(refund2).toBe(2);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(6);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(1);
  expect(costMultiplier).toBe(1);
});

test("undoing channel refunds first thrust ignored with damage level 3", () => {
  const thrustAssignment = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored", "HalfEfficiency"])
  );

  const { cost } = thrustAssignment.channel(2);
  expect(cost).toBe(5);
  const { refund } = thrustAssignment.undoChannel(1);

  expect(refund).toBe(2);

  const { refund: refund2 } = thrustAssignment.undoChannel(1);

  expect(refund2).toBe(3);

  const {
    capacity,
    overCapacity,
    extraCost,
    costMultiplier
  } = thrustAssignment.getThrustCapacity();

  expect(capacity).toBe(6);
  expect(overCapacity).toBe(0);
  expect(extraCost).toBe(1);
  expect(costMultiplier).toBe(2);
});

test("Channel cost should be right", () => {
  const thrustAssignment1 = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored"])
  );
  const thrustAssignment2 = new ThrustAssignment(
    getThruster(0, 6, ["HalfEfficiency"])
  );
  const thrustAssignment3 = new ThrustAssignment(
    getThruster(0, 6, ["FirstThrustIgnored", "HalfEfficiency"])
  );

  expect(thrustAssignment1.channel(2).cost).toBe(3);
  expect(thrustAssignment1.channel(2).cost).toBe(2);
  expect(thrustAssignment2.channel(2).cost).toBe(4);
  expect(thrustAssignment2.channel(2).cost).toBe(4);
  expect(thrustAssignment3.channel(2).cost).toBe(5);
  expect(thrustAssignment3.channel(2).cost).toBe(4);
});
