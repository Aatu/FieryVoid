import ThrustBill from "../../../source/public/client/object/handler/movement/ThrustBill.js";
import MovementOrder from "../../../source/public/client/object/handler/movement/MovementOrder.js";
import { Offset, Cube } from "../../../source/public/client/object/model";
require("../../../source/public/client/mathlib.js");

window.shipManager = {
  criticals: {
    hasCritical: (thruster, name) => thruster.criticals.includes(name)
  }
};

const getThruster = (direction = 0, output = 3, criticals = []) => ({
  thruster: true,
  direction,
  output,
  criticals,
  isDestroyed: () => false
});

const ship = {
  accelcost: 3,
  systems: [
    getThruster(0, 3),
    getThruster([1, 2], 3),
    getThruster(3, 3),
    getThruster([4, 5], 3)
  ]
};

const getMovementOrder = (type = "speed", facing = 0, value = 0) =>
  new MovementOrder(
    null,
    type,
    new window.hexagon.Offset(0, 0),
    new window.hexagon.Offset(0, 0),
    facing,
    0,
    false,
    value,
    null,
    null
  );

test("simple speed move", () => {
  const moves = [getMovementOrder("speed", 0, 0)];
  const bill = new ThrustBill(ship, 10, moves);
  expect(bill.directionsRequired).toEqual({
    "0": 0,
    "3": 3,
    "1": 0,
    "2": 0,
    "4": 0,
    "5": 0,
    "6": 0
  });
});

test("multiple speed moves", () => {
  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3),
    getMovementOrder("speed", 0, 1),
    getMovementOrder("speed", 0, 2),
    getMovementOrder("speed", 0, 4),
    getMovementOrder("speed", 0, 4),
    getMovementOrder("speed", 0, 5)
  ];
  const bill = new ThrustBill(ship, 10, moves);
  expect(bill.directionsRequired).toEqual({
    "0": 3,
    "3": 3,
    "1": 6,
    "2": 3,
    "4": 3,
    "5": 3,
    "6": 0
  });
});

test("Returns false if it is clear that there is not enough thrust", () => {
  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3),
    getMovementOrder("speed", 0, 1),
    getMovementOrder("speed", 0, 2),
    getMovementOrder("speed", 0, 4),
    getMovementOrder("speed", 0, 4),
    getMovementOrder("speed", 0, 5)
  ];
  const bill = new ThrustBill(ship, 10, moves);
  expect(bill.pay()).toBe(false);
});

test("it uses thrusters properly", () => {
  const thrusters = [
    getThruster(0, 3),
    getThruster(0, 3, ["HalfEfficiency", "FirstThrustIgnored"])
  ];

  ship.systems = thrusters;

  const bill = new ThrustBill(ship, 10, []);
  bill.useThrusters(0, 5, bill.thrusters, false);
  expect(bill.thrusters[0].channeled).toBe(3);
  expect(bill.thrusters[1].channeled).toBe(2);
  expect(bill.cost).toBe(8);
});

test("it uses thrusters properly, with overthrust", () => {
  const thrusters = [
    getThruster(0, 3),
    getThruster(0, 3, ["HalfEfficiency", "FirstThrustIgnored"])
  ];

  ship.systems = thrusters;

  const bill = new ThrustBill(ship, 10, []);
  bill.useThrusters(0, 5, bill.thrusters, true);
  expect(bill.thrusters[0].channeled).toBe(5);
  expect(bill.thrusters[1].channeled).toBe(0);
  expect(bill.cost).toBe(5);
});

test("It manages to pay a simple manouver", () => {
  const thrusters = [getThruster(0, 3), getThruster(3, 3)];

  ship.systems = thrusters;

  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3)
  ];

  const bill = new ThrustBill(ship, 10, moves);
  expect(bill.pay()).toBe(true);
});

test("It manages to pay a simple manouver with overthrusting", () => {
  const thrusters = [getThruster(0, 3), getThruster(3, 3)];

  ship.systems = thrusters;

  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3)
  ];

  const bill = new ThrustBill(ship, 10, moves);
  expect(bill.pay()).toBe(true);
});

test("It will use damaged thrusters", () => {
  const thrusters = [
    getThruster(0, 3),
    getThruster(0, 3, ["FirstThrustIgnored"]),
    getThruster(3, 3)
  ];

  ship.systems = thrusters;

  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3),
    getMovementOrder("speed", 0, 3),
    getMovementOrder("speed", 0, 3)
  ];

  const bill = new ThrustBill(ship, 15, moves);
  const result = bill.pay();
  expect(result).toBe(true);
  expect(bill.cost).toBe(13);
});

test("It gives thrusters in proper order", () => {
  const thrusters = [
    getThruster(0, 3),
    getThruster(0, 3, ["FirstThrustIgnored"]),
    getThruster(0, 4)
  ];

  ship.systems = thrusters;

  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 0)
  ];

  const bill = new ThrustBill(ship, 10, moves);
  const sortedThrusters = bill.getAllUsableThrusters(0);

  expect(sortedThrusters.length).toBe(3);
  expect(sortedThrusters[0].capacity).toBe(4);
  expect(sortedThrusters[0].getDamageLevel()).toBe(0);
  expect(sortedThrusters[1].capacity).toBe(3);
  expect(sortedThrusters[1].getDamageLevel()).toBe(0);
  expect(sortedThrusters[2].capacity).toBe(3);
  expect(sortedThrusters[2].getDamageLevel()).toBe(1);
});

test("It will rather use damaged thrusters than overthrust, if possible", () => {
  const thrusters = [
    getThruster(0, 3),
    getThruster(0, 3, ["FirstThrustIgnored"]),
    getThruster(3, 3)
  ];

  ship.systems = thrusters;

  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3),
    getMovementOrder("speed", 0, 3)
  ];

  const bill = new ThrustBill(ship, 10, moves);
  const time = expect(bill.pay()).toBe(true);
  expect(bill.cost).toBe(10);

  expect(bill.thrusters[1].channeled).toBe(3);
  expect(bill.thrusters[0].channeled).toBe(3);
  expect(bill.thrusters[1].damaged).toBe(true);
});

test("No budget to reallocate all overthrust", () => {
  const thrusters = [
    getThruster(0, 3),
    getThruster(0, 3, ["HalfEfficiency"]),
    getThruster(3, 3)
  ];

  ship.systems = thrusters;

  const moves = [
    getMovementOrder("speed", 0, 0),
    getMovementOrder("speed", 0, 3),
    getMovementOrder("speed", 0, 3)
  ];

  const bill = new ThrustBill(ship, 10, moves);
  const time = expect(bill.pay()).toBe(true);
  expect(bill.cost).toBe(10);
  expect(bill.isOverChanneled()).toBe(true);

  expect(bill.thrusters[1].channeled).toBe(1);
  expect(bill.thrusters[0].channeled).toBe(5);
  expect(bill.thrusters[1].damaged).toBe(true);

  const newMoves = bill.getMoves();

  expectDirectionsEmptyForRequiredThrust([0, 1, 2, 4, 5], newMoves[0]);
  expectDirectionsEqualForRequiredThrust(3, newMoves[0], [3]);

  expectDirectionsEmptyForRequiredThrust([3, 1, 2, 4, 5], newMoves[1]);
  expectDirectionsEqualForRequiredThrust(0, newMoves[1], [3]);

  expectDirectionsEmptyForRequiredThrust([3, 1, 2, 4, 5], newMoves[2]);
  expectDirectionsEqualForRequiredThrust(0, newMoves[2], [2, 1]);
});

const expectDirectionsEmptyForRequiredThrust = (directions, move) => {
  directions.forEach(direction => {
    expect(move.requiredThrust.fullfilments[direction]).toEqual([]);
  });
};

const expectDirectionsEqualForRequiredThrust = (direction, move, equal) => {
  move.requiredThrust.fullfilments[direction].forEach((entry, i) =>
    expect(entry.amount).toEqual(equal[i])
  );
};
