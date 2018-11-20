"use strict";

var _typeof =
  typeof Symbol === "function" && typeof Symbol.iterator === "symbol"
    ? function(obj) {
        return typeof obj;
      }
    : function(obj) {
        return obj &&
          typeof Symbol === "function" &&
          obj.constructor === Symbol &&
          obj !== Symbol.prototype
          ? "symbol"
          : typeof obj;
      };

window.mathlib = {
  distance: function distance(x1, y1, x2, y2) {
    if (
      (typeof x1 === "undefined" ? "undefined" : _typeof(x1)) == "object" &&
      (typeof y1 === "undefined" ? "undefined" : _typeof(y1)) == "object"
    ) {
      x2 = y1.x;
      y2 = y1.y;
      y1 = x1.y;
      x1 = x1.x;
    }

    var a = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
    return a;
  },

  distance3d: function(pointA, pointB) {
    var dx = pointB.x - pointA.x;
    var dy = pointB.y - pointA.y;
    var dz = pointB.z - pointA.z;

    var dist = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2) + Math.pow(dz, 2));

    return dist;
  },

  getSeededRandomGenerator: function(seed) {
    function lcg(a) {
      return (a * 48271) % 2147483647;
    }
    seed = seed ? lcg(seed) : lcg(Math.random());
    return function() {
      return (seed = lcg(seed)) / 2147483648;
    };
  },

  arrayIsEmpty: function arrayIsEmpty(array) {
    for (var i in array) {
      return false;
    }

    return true;
  },

  addToDirection: function addToDirection(current, add) {
    add = add % 360;

    var ret = 0;
    if (current + add > 360) {
      ret = add - (360 - current);
    } else if (current + add < 0) {
      ret = 360 + (current + add);
    } else {
      ret = current + add;
    }

    return ret;
  },

  getPointBetween: function getPointBetween(start, end, percentage, noRound) {
    var x = start.x + percentage * (end.x - start.x);
    var y = start.y + percentage * (end.y - start.y);

    if (noRound) {
      return { x: x, y: y };
    }

    return { x: Math.floor(x), y: Math.floor(y) };
  },

  getPointBetweenInDistance: function getPointBetween(
    start,
    end,
    distance,
    noRound
  ) {
    const totalDistance = mathlib.distance3d(start, end);
    const percentage = distance / totalDistance;
    return mathlib.getPointBetween3d(start, end, percentage, noRound);
  },

  getPointBetween3d: function getPointBetween(start, end, percentage, noRound) {
    var x = start.x + percentage * (end.x - start.x);
    var y = start.y + percentage * (end.y - start.y);
    var z = start.z + percentage * (end.z - start.z);

    if (noRound) {
      return { x: x, y: y, z: z };
    }

    return { x: Math.floor(x), y: Math.floor(y), z: Math.floor(z) };
  },

  getDistanceBetweenShipsInHex: function getDistanceBetweenShipsInHex(s1, s2) {
    var start = shipManager.getShipPosition(s1);
    var end = shipManager.getShipPosition(s2);
    return start.distanceTo(end);
  },

  getClosestAngleBetween: function getAngleBetween(angle1, angle2) {
    const right = mathlib.getAngleBetween(angle1, angle2, true);
    const left = mathlib.getAngleBetween(angle1, angle2, false);
  },

  getAngleBetween: function getAngleBetween(angle1, angle2, right) {
    //console.log(angle1  + " " + angle2);
    var total;
    var difference;
    if (right) {
      if (angle1 > angle2) {
        difference = 360 - angle1 + angle2;
      } else {
        difference = angle2 - angle1;
      }
    } else {
      if (angle1 < angle2) {
        difference = (angle1 + (360 - angle2)) * -1;
      } else {
        difference = angle2 - angle1;
      }
    }

    return difference;
  },

  addToHexFacing: function addToHexFacing(facing, add) {
    if (facing + add > 5) {
      return window.mathlib.addToHexFacing(0, facing + add - 6);
    }

    if (facing + add < 0) {
      return window.mathlib.addToHexFacing(6, facing + add);
    }

    return facing + add;
  },

  getPointInDirection: function getPointInDirection(r, a, cx, cy, noRound) {
    a = -a;

    var x = cx + r * Math.cos((a * Math.PI) / 180);
    var y = cy + r * Math.sin((a * Math.PI) / 180);

    if (noRound) {
      return { x: x, y: y };
    }
    return { x: Math.round(x), y: Math.round(y) };
  },

  getArcLength: function getArcLength(start, end) {
    var a = 0;
    if (start > end) {
      a = 360 - start + end;
    } else {
      a = end - start;
    }

    return a;
  },

  isInArc: function isInArc(direction, start, end) {
    //direction: 300 start: 360 end: 240
    direction = Math.round(direction);

    if (start == end) return true;

    if ((direction == 0 && start == 360) || (direction == 0 && end == 360))
      return true;

    if (start > end) {
      return direction >= start || direction <= end;
    } else if (direction >= start && direction <= end) {
      return true;
    }

    return false;
  },

  radianToDegree: function radianToDegree(angle) {
    return angle * (180.0 / Math.PI);
  },

  degreeToRadian: function degreeToRadian(angle) {
    //radian * (180.0 / Math.PI) = degree
    return angle / (180.0 / Math.PI);
  },

  getCompassHeadingOfShip: function getCompassHeadingOfShip(observer, target) {
    var oPos = shipManager.getShipPosition(observer);
    var tPos = shipManager.getShipPosition(target);

    if (oPos.equals(tPos)) {
      if (shipManager.hasBetterInitive(observer, target)) {
        oPos = shipManager.movement.getPreviousLocation(observer);
      } else {
        tPos = shipManager.movement.getPreviousLocation(target);
      }
    }

    oPos = window.coordinateConverter.fromHexToGame(oPos);
    tPos = window.coordinateConverter.fromHexToGame(tPos);

    return mathlib.getCompassHeadingOfPoint(oPos, tPos);
  },

  getCompassHeadingOfPoint: function getCompassHeadingOfPoint(
    observer,
    target
  ) {
    if (observer instanceof hexagon.Offset) {
      observer = coordinateConverter.fromHexToGame(observer);
    }

    if (target instanceof hexagon.Offset) {
      target = coordinateConverter.fromHexToGame(target);
    }

    var heading = mathlib.radianToDegree(
      Math.atan2(target.y - observer.y, target.x - observer.x)
    );

    if (heading > 0) {
      heading = 360 - heading;
    } else {
      heading = Math.abs(heading);
    }

    return heading;
  },

  hexFacingToAngle: function hexFacingToAngle(d) {
    switch (d) {
      case 0:
        return 0;
      case 1:
        return 60;
      case 2:
        return 120;
      case 3:
        return 180;
      case 4:
        return 240;
      default:
        return 300;
    }
  }
};
