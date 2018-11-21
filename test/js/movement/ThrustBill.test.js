import ThrustBill from "../../../source/public/client/renderer/object/handler/movement/ThrustBill.js";
import MovementOrder from "../../../source/public/client/renderer/object/handler/movement/MovementOrder.js";
require("../../../source/public/client/model/hexagon/Offset.js");
require("../../../source/public/client/model/hexagon/Cube.js");
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
  criticals
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
    "5": 0
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
    "5": 3
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
  