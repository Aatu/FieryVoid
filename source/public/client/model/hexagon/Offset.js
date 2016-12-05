if (typeof window.hexagon === 'undefined')
    window.hexagon = {};

window.hexagon.Offset = (function () {

    function Offset(q, r, layout) {
        if (typeof q === 'object') {
            var offset = q;
            this.q = offset.q;
            this.r = offset.r;
            this.layout = typeof q.layout === 'undefined' ? hexagon.Offset.ODD_R : q.layout;
        } else {
            this.q = q;
            this.r = r;
            this.layout = typeof layout === 'undefined' ? hexagon.Offset.ODD_R : layout;
        }
    }

    Offset.EVEN_R = 1;
    Offset.ODD_R = 2;

    Offset.prototype.neighbours = {};

    Offset.prototype.neighbours[
        Offset.EVEN_R] = [
        [
            {q: 1, r: 0}, {q: 1, r: -1}, {q: 0, r: -1},
            {q: -1, r: 0}, {q: 0, r: 1}, {q: 1, r: 1}
        ],
        [
            {q: 1, r: 0}, {q: 0, r: -1}, {q: -1, r: -1},
            {q: -1, r: 0}, {q: -1, r: 1}, {q: 0, r: 1}
        ]
    ];

    Offset.prototype.neighbours[
        Offset.ODD_R] = [
        [
            {q: 1, r: 0}, {q: 0, r: -1}, {q: -1, r: -1},
            {q: -1, r: 0}, {q: -1, r: 1}, {q: 0, r: 1}
        ],
        [
            {q: 1, r: 0}, {q: 1, r: -1}, {q: 0, r: -1},
            {q: -1, r: 0}, {q: 0, r: 1}, {q: 1, r: 1}
        ]
    ];


    Offset.prototype.getNeighbours = function () {
        var neighbours = [];

        this.neighbours[this.layout][this.r & 1].forEach(function (neighbour) {
            neighbours.push(this.add(neighbour));
        }, this);

        return neighbours;
    };

    Offset.prototype.add = function (offset) {
        var q = this.q + offset.q;
        var r = this.r + offset.r;

        return new hexagon.Offset(q, r, this.layout);
    };

    Offset.prototype.equals = function (offset) {
        return this.q === offset.q &&
            this.r === offset.r &&
            this.layout === offset.layout;
    };

    Offset.prototype.toCube = function () {

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
                y = -x - z
                break;
        }

        return new hexagon.Cube(x, y, z).round();
    };

    Offset.prototype.toFVHex = function () {

        var x = this.q + this.r / 2;

        if (this.r < 0) {
            x = Math.ceil(x);
        } else {
            x = Math.floor(x);
        }

        return new hexagon.FVHex(x, this.r * -1);
    };

    return Offset;
})();