"use strict";

window.HexagonMath = function () {

    function HexagonMath() {}

    HexagonMath.prototype.getHexHeight = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return hexSize + 2 * this.getHexA(hexSize);
    };

    HexagonMath.prototype.getHexWidth = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return this.getHexB(hexSize) * 2;
    };

    HexagonMath.prototype.getHexRowHeight = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return hexSize + this.getHexA(hexSize);
    };

    HexagonMath.prototype.getTextureHeight = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return Math.floor(hexSize + this.getHexHeight(hexSize));
    };

    HexagonMath.prototype.getTextureWidth = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return this.getHexWidth(hexSize) * 2;
    };

    HexagonMath.prototype.getHexA = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return hexSize * Math.sin(30 / 180 * Math.PI);
    };

    HexagonMath.prototype.getHexB = function (hexSize) {
        if (!hexSize) {
            hexSize = window.Config.HEX_SIZE;
        }

        return hexSize * Math.cos(30 / 180 * Math.PI);
    };

    HexagonMath.prototype.getGridWidth = function (hexCountWidth) {
        return this.getHexWidth() * (hexCountWidth + 0.5);
    };

    HexagonMath.prototype.getGridHeight = function (hexCountHeight) {
        var amount = Math.floor(hexCountHeight / 2);
        var height = amount * (this.getHexHeight() + window.Config.HEX_SIZE);

        if (hexCountHeight % 2 !== 0) {
            height += this.getHexHeight();
        }

        return height;
    };

    return new HexagonMath();
}();