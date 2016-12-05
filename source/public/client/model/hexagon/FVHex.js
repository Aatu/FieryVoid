if (typeof window.hexagon === 'undefined')
    window.hexagon = {};

window.hexagon.FVHex = (function () {

    function FVHex(x, y) {
        if (typeof x == "object" && x.x != undefined && x.y != undefined) {
            this.x = x.x;
            this.y = x.y;
        }else {
            this.x = x;
            this.y = y;
        }
    }

    FVHex.prototype.distanceTo = function (fvHex) {
        return this.toCube().distanceTo(fvHex.toCube());
    };

    FVHex.prototype.getNeighbours = function () {
        return this.toOffset().getNeighbours().map(function(neighbor){
           return neighbor.toFVHex();
        });
    };

    FVHex.prototype.getNeighbourAtDirection = function (direction) {
        if (direction == 0){
            return this.add({x: 1, y: 0});
        }
        if (direction == 1){
            return this.add({x: 1, y: 1});
        }
        if (direction == 2){
            return this.add({x: 0, y: 1});
        }
        if (direction == 3){
            return this.add({x: -1, y: 0});
        }
        if (direction == 4){
            return this.add({x: 0, y: -1});
        }
        if (direction == 5){
            return this.add({x: 1, y: -1});
        }
    };

    FVHex.prototype.add = function (fvHex) {
        var x = this.x + fvHex.x;
        var y = this.y + fvHex.y;

        return new hexagon.FVHex(x, y);
    };

    FVHex.prototype.equals = function (fvHex) {
        return this.x === fvHex.x && this.y === fvHex.y;
    };

    FVHex.prototype.toOffset = function () {


        var r = this.y*-1;
        var q = this.x - r/2;


        /*
        if (this.y < 0) {
            q = Math.floor(q);
        } else {
            q = Math.ceil(q);
        }
        */

        if (r < 0) {
            q = Math.floor(q);
        } else {
            q = Math.ceil(q);
        }

        /*
        if (this.y % 2 !== 0 ){
            q += 1;
        }
        */
        // x = q + r / 2;
        // 2 * x = q * 2 + r
        // 2 * x - r = q * 2;
        // x - r/2 = q

        return new hexagon.Offset(q, r, hexagon.Offset.EVEN_R);
    };

    FVHex.prototype.toCube = function () {
        return  this.toOffset().toCube();
    };

    return FVHex;
})();
