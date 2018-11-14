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

if (typeof window.hexagon === "undefined") window.hexagon = {};

window.hexagon.Offset = (function() {
  function Offset(q, r) {
    if ((typeof q === "undefined" ? "undefined" : _typeof(q)) === "object") {
      var offset = q;
      this.q = offset.q;
      this.r = offset.r;
    } else {
      this.q = q;
      this.r = r;
    }
  }

  Offset.prototype.neighbours = [
    [
      { q: 1, r: 0 },
      { q: 1, r: -1 },
      { q: 0, r: -1 },
      { q: -1, r: 0 },
      { q: 0, r: 1 },
      { q: 1, r: 1 }
    ],
    [
      { q: 1, r: 0 },
      { q: 0, r: -1 },
      { q: -1, r: -1 },
      { q: -1, r: 0 },
      { q: -1, r: 1 },
      { q: 0, r: 1 }
    ]
  ];

  Offset.prototype.getNeighbours = function() {
    var neighbours = [];

    this.neighbours[this.r & 1].forEach(function(neighbour) {
      neighbours.push(this.add(neighbour));
    }, this);

    return neighbours;
  };

  Offset.prototype.add = function(offset) {
    return this.toCube()
      .add(offset.toCube())
      .toOffset();
  };

  Offset.prototype.equals = function(offset) {
    return this.q === offset.q && this.r === offset.r;
  };

  Offset.prototype.getNeighbourAtDirection = function(direction) {
    var neighbours = this.getNeighbours();

    return neighbours[direction];
  };

  Offset.prototype.distanceTo = function(target) {
    return this.toCube().distanceTo(target.toCube());
  };

  Offset.prototype.toCube = function() {
    var x = this.q - (this.r + (this.r & 1)) / 2;
    var z = this.r;
    var y = -x - z;

    /*
        var x, y, z;
        switch (this.layout) {
            case Offset.ODD_R:
                x = this.q - (this.r - (this.r & 1)) / 2;
                z = this.r;
                y = -x - z;
                break;
            case Offset.EVEN_R:
                x = this.q - (this.r + (this.r&1)) / 2;
                z = this.r;
                y = -x - z;
                break;
        }
        */

    return new hexagon.Cube(x, y, z).round();
  };

  return Offset;
})();
