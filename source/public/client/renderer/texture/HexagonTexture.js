"use strict";

window.HexagonTexture = function () {

    function HexagonTexture(graphics) {
        this.graphics = graphics;
    }

    HexagonTexture.prototype.getHexGridTexture = function (canvasSize, gridWidth, gridHeight, lineColor, fillColor, lineWidth) {
        return this.getTexture(this.renderHexGrid(canvasSize, lineColor, fillColor, lineWidth, true), gridWidth, gridHeight);
    };

    HexagonTexture.prototype.getHexTexture = function (canvasSize, lineColor, fillColor, lineWidth) {
        return this.getTexture(this.renderHexGrid(canvasSize, lineColor, fillColor, lineWidth), 1, 1);
    };

    HexagonTexture.prototype.renderHexGrid = function (canvasSize, lineColor, fillColor, lineWidth, repeat) {
        var scale = { x: 1, y: 1 };
        var width = canvasSize;
        var hl = width / 4 / Math.cos(30 / 180 * Math.PI);
        var a = window.HexagonMath.getHexA(hl);
        var b = window.HexagonMath.getHexB(hl);
        var x = b;

        var height = window.HexagonMath.getTextureHeight(hl);

        var canvas = createCanvas(width, height, false);
        var context = canvas.getContext("2d", { antialias: true });
        context.fillStyle = lineColor;
        context.strokeStyle = lineColor;
        context.fillStyle = fillColor;
        context.lineWidth = lineWidth;

        context.scale(scale.x, scale.y);

        if (repeat) {
            this.graphics.drawCenteredHexagon(context, x, 0, hl, true, true, true);
            this.graphics.drawCenteredHexagon(context, x * 3, 0, hl, true, true, true);
        }

        this.graphics.drawCenteredHexagon(context, x * 2, hl + a, hl, true, true, true);

        if (repeat) {
            this.graphics.drawCenteredHexagon(context, x, hl * 3, hl, true, true, true);
            this.graphics.drawCenteredHexagon(context, x * 3, hl * 3, hl, true, true, true);
        }

        var canvas2 = createCanvas(width, width, false);

        canvas2.getContext('2d', { antialias: true }).drawImage(canvas, 0, 0, width, height, 0, 0, width, width);

        return canvas2;
    };

    function createCanvas(width, height, debug) {
        return window.AbstractCanvas.create(width, height, debug);
    }

    HexagonTexture.prototype.getTexture = function (canvas, gridWidth, gridHeight) {

        if (gridWidth == undefined) {
            gridWidth = 1;
        }

        if (gridHeight == undefined) {
            gridHeight = 1;
        }

        var texture = new THREE.Texture(canvas);
        texture.needsUpdate = true;
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(getHorizontalRepeat(gridWidth), getVerticalRepeat(gridHeight));

        texture.offset.set(0, 0.16666);

        return texture;
    };

    function getHorizontalRepeat(hexagons) {
        return 3 / 4 + (hexagons - 1) * 2 / 4;
    }

    function getVerticalRepeat(hexagons) {
        return 4 / 6 + (hexagons - 1) * 3 / 6;
    }

    return new HexagonTexture(window.graphics);
}();