
if ( typeof window.hexagon === 'undefined')
    window.hexagon = {};


window.hexagon.Cube = (function(){
  function Cube(x, y, z)
    {
        if (typeof x === 'object') {
            var cube = x;
            this.x = this._formatNumber(cube.x);
            this.y = this._formatNumber(cube.y);
            this.z = this._formatNumber(cube.z);
        } else {
            this.x = this._formatNumber(x);
            this.y = this._formatNumber(y);
            this.z = this._formatNumber(z);
        }

        this._validate();
    }

    Cube.PRECISION = 4;

    Cube.prototype.round = function()
    {
        if (this.x % 1 === 0 && this.y % 1 === 0 && this.z % 1 === 0) {
            return this;
        }

        let rx = Math.round(this.x);
        let ry = Math.round(this.y);
        let rz = Math.round(this.z);

        let x_diff = Math.abs(rx - this.x);
        let y_diff = Math.abs(ry - this.y);
        let z_diff = Math.abs(rz - this.z);

        if (x_diff > y_diff && x_diff > z_diff) {
            rx = -ry - rz;
        } else if (y_diff > z_diff) {
            ry = -rx - rz;
        } else {
            rz = -rx - ry;
        }

        return new hexagon.Cube(rx, ry, rz);
    };

    Cube.prototype._validate = function()
    {
        if (Math.abs(this.x + this.y + this.z) > 0.001) {
            throw new Error(
                "Invalid Cube coordinates: (" + this.x + ", " + this.y + ", " + this.z + ")"
            );
        }
    }

    Cube.prototype.neighbours = [
        { x:  1, y: -1, z: 0 }, { x:  1, y:  0, z: -1 }, { x:  0, y:  1, z: -1 },
        { x: -1, y:  1, z: 0 }, { x: -1, y:  0, z:  1 }, { x:  0, y: -1, z:  1 }
    ];

    Cube.prototype.getNeighbours = function()
    {
        var neighbours = [];

        this.neighbours.forEach(function(neighbour) {
            neighbours.push(this.add(neighbour));
        }, this);

        return neighbours;
    }

    Cube.prototype.moveToDirection = function(direction)
    {
        return this.add(this.neighbours[direction]);
    }

    Cube.prototype.add = function(cube)
    {
        return new hexagon.Cube(this.x + cube.x, this.y + cube.y, this.z + cube.z);
    }

    Cube.prototype.subtract = function(cube)
    {
        return new hexagon.Cube(this.x - cube.x, this.y - cube.y, this.z - cube.z);
    }

    Cube.prototype.scale = function(scale)
    {
        return new hexagon.Cube(this.x * scale, this.y * scale, this.z * scale);
    }

    Cube.prototype.distanceTo = function(cube)
    {
        return Math.max(
            Math.abs(this.x - cube.x),
            Math.abs(this.y - cube.y),
            Math.abs(this.z - cube.z)
        );
    }

    Cube.prototype.equals = function(cube)
    {
        return this.x === cube.x &&
               this.y === cube.y &&
               this.z === cube.z;
    }

    Cube.prototype.getFacing = function(neighbour) {
        var index = -1;

        var delta = neighbour.subtract(this);

        this.neighbours.some(function(hex, i) {
            if (delta.equals(hex)) {
                index = i;
                return true;
            }
        });

        return index;
    };

    Cube.prototype.toOffset = function()
    {
        var q = this.x + (this.z + (this.z & 1)) / 2;
        var r = this.z;

        var Offset = hexagon.Offset;

        return new Offset(q, r); //EVEN_R
    };

/*
    Cube.prototype.toOffset = function()
    {
        var q = this.x + (this.z - (this.z & 1)) / 2;
        var r = this.z;

        return new hexagon.Offset(q, r); //ODD_R
    };
*/
    Cube.prototype.toString = function()
    {
        return "(" + this.x + "," + this.y + "," + this.z + ")";
    }

    Cube.prototype._formatNumber = function(number)
    {
        return parseFloat(number.toFixed(hexagon.Cube.PRECISION));
    };

    return Cube;
})();