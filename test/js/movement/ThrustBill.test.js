import ThrustBill from "../../../source/public/client/renderer/object/handler/movement/ThrustBill.js";
import MovementOrder from "../../../source/public/client/renderer/object/handler/movement/MovementOrder.js";
require("../../../source/public/client/model/hexagon/Offset.js");
require("../../../source/public/client/model/hexagon/Cube.js");
require("../../../source/public/client/mathlib.js");

const getThruster = (direction = 0, output = 3) => ({
  direction,
  output,
  criticals: []
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
  expect(bill.totalThrustRequired).toBe(3);
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
  expect(bill.totalThrustRequired).toBe(21);
});
