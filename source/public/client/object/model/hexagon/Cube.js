import Offset from "./Offset";

const PRECISION = 4;

class Cube {
  constructor(x, y, z) {
    if (
      x instanceof Cube ||
      (x.x !== undefined && x.y !== undefined && x.z !== undefined)
    ) {
      const cube = x;
      this.x = this._formatNumber(cube.x);
      this.y = this._formatNumber(cube.y);
      this.z = this._formatNumber(cube.z);
    } else {
      this.x = this._formatNumber(x);
      this.y = this._formatNumber(y);
      this.z = this._formatNumber(z);
    }

    this.neighbours = [
      { x: 1, y: -1, z: 0 },
      { x: 1, y: 0, z: -1 },
      { x: 0, y: 1, z: -1 },
      { x: -1, y: 1, z: 0 },
      { x: -1, y: 0, z: 1 },
      { x: 0, y: -1, z: 1 }
    ];

    this._validate();
  }

  round() {
    if (this.x % 1 === 0 && this.y % 1 === 0 && this.z % 1 === 0) {
      return this;
    }

    var rx = Math.round(this.x);
    var ry = Math.round(this.y);
    var rz = Math.round(this.z);

    var x_diff = Math.abs(rx - this.x);
    var y_diff = Math.abs(ry - this.y);
    var z_diff = Math.abs(rz - this.z);

    if (x_diff > y_diff && x_diff > z_diff) {
      rx = -ry - rz;
    } else if (y_diff > z_diff) {
      ry = -rx - rz;
    } else {
      rz = -rx - ry;
    }

    return new Cube(rx, ry, rz);
  }

  _validate() {
    if (Math.abs(this.x + this.y + this.z) > 0.001) {
      throw new Error(
        "Invalid Cube coordinates: (" +
          this.x +
          ", " +
          this.y +
          ", " +
          this.z +
          ")"
      );
    }
  }

  getNeighbours() {
    var neighbours = [];

    this.neighbours.forEach(function(neighbour) {
      neighbours.push(this.add(neighbour));
    }, this);

    return neighbours;
  }

  moveToDirection(direction) {
    return this.add(this.neighbours[direction]);
  }

  add(cube) {
    return new Cube(this.x + cube.x, this.y + cube.y, this.z + cube.z);
  }

  subtract(cube) {
    return new Cube(this.x - cube.x, this.y - cube.y, this.z - cube.z);
  }

  scale(scale) {
    return new Cube(this.x * scale, this.y * scale, this.z * scale);
  }

  distanceTo(cube) {
    return Math.max(
      Math.abs(this.x - cube.x),
      Math.abs(this.y - cube.y),
      Math.abs(this.z - cube.z)
    );
  }

  equals(cube) {
    return this.x === cube.x && this.y === cube.y && this.z === cube.z;
  }

  getFacing(neighbour) {
    var index = -1;

    var delta = neighbour.subtract(this);

    this.neighbours.some(function(hex, i) {
      if (delta.equals(hex)) {
        index = i;
        return true;
      }
    });

    return index;
  }

  toOffset() {
    var q = this.x + (this.z + (this.z & 1)) / 2;
    var r = this.z;

    return new Offset(q, r); //EVEN_R
  }

  /*
        Cube.prototype.toOffset = function()
        {
            var q = this.x + (this.z - (this.z & 1)) / 2;
            var r = this.z;
    
            return new hexagon.Offset(q, r); //ODD_R
        };
    */
  toString() {
    return "(" + this.x + "," + this.y + "," + this.z + ")";
  }

  _formatNumber(number) {
    return parseFloat(number.toFixed(PRECISION));
  }
}

if (typeof window.hexagon === "undefined") window.hexagon = {};
window.hexagon.Cube = Cube;

export default window.hexagon.Cube;
