"use strict";

window.canvasHexGridRenderer = function () {

    function canvasHexGridRenderer(hexgrid, gamedata, graphics, deployment) {
        this.hexgrid = hexgrid;
        this.gamedata = gamedata;
        this.graphics = graphics;
        this.deployment = deployment;
    }

    canvasHexGridRenderer.prototype.renderHexGridOnCanvas = function (canvasId) {
        var canvas = window.graphics.getCanvas(canvasId);

        canvas.save();
        graphics.clearCanvas(canvasId);

        if (this.gamedata.gamephase === -1) this.deployment.drawDeploymentAreas(canvas);

        canvas.fillStyle = this.hexgrid.hexlinecolor;
        canvas.strokeStyle = this.hexgrid.hexlinecolor;

        //fadeHexgrid(canvas, this.gamedata.zoom);

        canvas.lineWidth = this.hexgrid.hexlinewidth;

        drawHexGrid(canvas, this.graphics, this.gamedata, this.hexgrid.hexWidth, this.hexgrid.hexHeight, this.hexgrid.hexlenght);

        //offsetHexgrid(canvasId, gamedata.scrollOffset, this.hexgrid.hexWidth, this.hexgrid.hexHeight);

        canvas.restore();
    };

    /**
     * Possible optimization: instead of drawing hexgrid when scrolling, just offset the canvas element.
     * Stil wip though, would need to resize the canvas to be larger than game windows so that it can be offset
     */
    function offsetHexgrid(canvasId, scrollOffset, hexWidth, hexHeight) {
        jQuery('#' + canvasId).css({
            top: -(scrollOffset.y + hexHeight()) + "px",
            left: -(scrollOffset.x + hexWidth()) + "px"
        });

        console.log();
    };

    function drawHexGrid(canvas, graphics, gamedata, hexWidth, hexHeight, hexLength) {

        var hl = hexLength * gamedata.zoom;
        var a = hl * 0.5;
        var b = hl * 0.8660254; //0.86602540378443864676372317075294

        var horizontalCount = gamedata.gamewidth / hexgrid.hexWidth() + 2;
        var verticalCount = gamedata.gameheight / hexgrid.hexHeight() + 2;

        for (var v = 0; v <= verticalCount; v++) {
            for (var h = 0; h <= horizontalCount; h++) {

                var x, y;

                if ((v + gamedata.scroll.y) % 2 == 0) {
                    x = h * b * 2;
                } else {
                    x = h * b * 2 - b;
                }

                y = v * hl * 2 - a * v;

                x -= gamedata.scrollOffset.x + hexWidth();
                y -= gamedata.scrollOffset.y + hexHeight();

                if (v == 0 && h == 0) graphics.drawHexagon(canvas, x, y, hl, true, true, true);else if (v == 0 && h != 0) {
                    graphics.drawHexagon(canvas, x, y, hl, false, true, true);
                } else if ((v + gamedata.scroll.y) % 2 == 0 && h == 0) graphics.drawHexagon(canvas, x, y, hl, true, false, false);else if (v != 0 && (v + gamedata.scroll.y) % 2 != 0 && h == 0) {
                    graphics.drawHexagon(canvas, x, y, hl, true, true, false);
                } else {
                    graphics.drawHexagon(canvas, x, y, hl, false, false, false);
                }

                canvas.font = 'italic 12px sans-serif';
                canvas.textBaseline = 'top';
                canvas.fillText(h + gamedata.scroll["x"] + "," + (v + gamedata.scroll["y"]), x + b, y + hl);
            }
        }
    }

    function fadeHexgrid(canvas, zoom) {
        if (zoom <= 0.8) canvas.strokeStyle = "rgba(255,255,255,0.16)";

        if (zoom <= 0.7) canvas.strokeStyle = "rgba(255,255,255,0.13)";

        if (zoom <= 0.6) canvas.strokeStyle = "rgba(255,255,255,0.1)";

        if (zoom < 0.7) canvas.strokeStyle = "rgba(255,255,255,0.0)";
    }

    return canvasHexGridRenderer;
}();