"use strict";

window.webglHexGridRenderer = function () {

    var HEX_COUNT_WIDTH = 199;
    var HEX_COUNT_HEIGHT = 199;
    var HEX_LINE_COLOR = "rgba(255,255,255,255)";
    var HEX_FILL_COLOR = "rgba(0,0,0,0)";
    var HEX_LINE_WIDTH = 20;
    var HEX_CANVAS_SIZE = 1024;
    var HEX_OPACITY = 0.2;
    var HEX_MAX_OPACITY = 0.3;

    function webglHexGridRenderer(graphics) {
        this.graphics = graphics;
        this.material = null;
        this.mesh = null;
        this.minZoom = 0;
        this.maxZoom = 0;
        this.hexSize = 128;
    }

    webglHexGridRenderer.prototype.renderHexGrid = function (scene, minZoom, maxZoom) {
        this.minZoom = minZoom;
        this.maxZoom = maxZoom;

        var width = getGeometryWidth();
        var height = getGeometryHeight();

        var texture = window.HexagonTexture.getHexGridTexture(HEX_CANVAS_SIZE, HEX_COUNT_WIDTH, HEX_COUNT_HEIGHT, HEX_LINE_COLOR, HEX_FILL_COLOR, HEX_LINE_WIDTH);

        var geometry = new THREE.PlaneGeometry(width, height, 1, 1);
        this.material = new THREE.MeshBasicMaterial({ map: texture, transparent: true, opacity: HEX_OPACITY, depthWrite: false});
        this.mesh = new THREE.Mesh(geometry, this.material);
        this.mesh.position.x += window.HexagonMath.getHexB() / 2;
        scene.add(this.mesh);

        drawGameSpace(scene);
    };

    function drawGameSpace(scene) {
        var gamespace = gamedata.gamespace;

        if (!gamespace) {
            return;
        }

        var width = parseInt(gamespace.substr(0, gamespace.indexOf("x")));
        var height = parseInt(gamespace.substr(gamespace.indexOf("x") + 1));

        if (width == -1 || height == -1) {
            return;
        }

        var size = {
            width: window.HexagonMath.getHexWidth() * width,
            height: window.HexagonMath.getHexRowHeight() * height
        };

        var position = {
            x: width % 2 == 0 ? -window.HexagonMath.getHexWidth() / 2 : 0,
            y: 0
        };

        var sprite = new window.BoxSprite(size, 10, 0, new THREE.Color(1, 1, 1), 0.5);

        sprite.setPosition(position);
        scene.add(sprite.mesh);
    }

    function debug(scene) {
        var amount = 15;

        for (var i = 1; i <= 15; i++) {
            x = window.HexagonMath.getHexWidth() / 2 * i;
            y = (window.HexagonMath.getHexHeight() - window.HexagonMath.getHexA()) * i;
            scene.add(buildHexagonGeomatry(x, y, window.Config.HEX_SIZE));
        }

        for (var i = -1; i >= amount * -1; i--) {
            x = window.HexagonMath.getHexWidth() / 2 * i;
            y = (window.HexagonMath.getHexHeight() - window.HexagonMath.getHexA()) * i;
            scene.add(buildHexagonGeomatry(x, y, window.Config.HEX_SIZE));
        }
    }

    function debug2(scene) {
        var amount = 20;
        for (var i = 1; i <= amount; i++) {
            x = window.HexagonMath.getHexWidth() * i;
            scene.add(buildHexagonGeomatry(x, 0, window.Config.HEX_SIZE));
        }

        for (var i = -1; i >= amount * -1; i--) {
            x = window.HexagonMath.getHexWidth() * i;
            scene.add(buildHexagonGeomatry(x, 0, window.Config.HEX_SIZE));
        }
    }

    webglHexGridRenderer.prototype.onZoom = function (zoom) {

        if (zoom > 1) {
            //this.material.opacity = (HEX_MAX_OPACITY - HEX_OPACITY) * ((zoom - 1) / (this.maxZoom - 1)) + HEX_OPACITY;
        } else {
            //this.material.opacity = zoom * HEX_OPACITY;
        }
    };

    function buildHexagonGeomatry(x, y, l) {

        var material = new THREE.LineBasicMaterial({
            color: 0x0000ff,
            linewidth: 30
        });
        var geometry = new THREE.Geometry();

        getCenteredHexagonPoints(x, y, l).forEach(function (point) {
            geometry.vertices.push(new THREE.Vector3(point.x, point.y, 0));
        });

        return new THREE.Line(geometry, material);
    }

    function getCenteredHexagonPoints(x, y, l) {
        var a = window.HexagonMath.getHexA(l);
        var b = window.HexagonMath.getHexB(l);

        x = x - b;
        y = y - a * 2;

        return [{ x: x, y: y + a + l }, { x: x, y: y + a }, { x: x + b, y: y }, { x: x + 2 * b, y: y + a }, { x: x + 2 * b, y: y + a + l }, { x: x + b, y: y + 2 * l }, { x: x, y: y + a + l }];
    }

    function getGeometryWidth() {
        return window.HexagonMath.getGridWidth(HEX_COUNT_WIDTH);
    }

    function getGeometryHeight() {
        return window.HexagonMath.getGridHeight(HEX_COUNT_HEIGHT);
    }

    return webglHexGridRenderer;
}();