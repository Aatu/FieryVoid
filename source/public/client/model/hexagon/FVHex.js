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

    FVHex.prototype.add = function (fvHex) {
        var x = this.x + fvHex.x;
        var y = this.y + fvHex.y;

        return new hexagon.FVHex(x, y);
    };

    FVHex.prototype.equals = function (fvHex) {
        return this.x === fvHex.x && this.y === fvHex.y;
    };

    FVHex.prototype.toOffset = function () {
        return new hexagon.Offset(this.x, this.y*-1, hexagon.Offset.EVEN_R);
    };

    FVHex.prototype.toCube = function () {
        return  new hexagon.Offset(x, y*-1, hexagon.Offset.EVEN_R).toCube();
    };

    return FVHex;
})();
