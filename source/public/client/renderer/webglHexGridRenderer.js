window.webglHexGridRenderer = (function(){

    var HEX_LENGTH = 128;
    var HEX_COUNT_WIDTH = 19;
    var HEX_COUNT_HEIGHT = 19;
    var HEX_LINE_COLOR = "rgba(255,255,255,255)";
    var HEX_LINE_WIDTH = 50;
    var HEX_OPACITY = 0.2;
    var HEX_MAX_OPACITY = 0.6;

    function webglHexGridRenderer (graphics){
        this.graphics = graphics;
        this.material = null;
        this.mesh = null;
    }


    webglHexGridRenderer.prototype.renderHexGrid = function (scene) {

        var width = HEX_LENGTH * HEX_COUNT_WIDTH;
        var height = getGeometryHeight(width);

        var texture = getTexture(renderHex(graphics), HEX_COUNT_WIDTH, HEX_COUNT_HEIGHT);
        var geometry = new THREE.PlaneGeometry(width, height, 1, 1);
        this.material = new THREE.MeshBasicMaterial({ map: texture, transparent: true, opacity: HEX_OPACITY });
        this.mesh = new THREE.Mesh(geometry, this.material);
        this.mesh.position.x += Math.ceil(getHexA(HEX_LENGTH)/2);
        scene.add(this.mesh);
    };

    webglHexGridRenderer.prototype.onZoom = function (zoom) {

        if (zoom > 1) {
            this.material.opacity = HEX_OPACITY;
            return;
        }

        var opacity = zoom * HEX_OPACITY;

        if (opacity > HEX_MAX_OPACITY)
            opacity = HEX_MAX_OPACITY;

        this.material.opacity = opacity;
    };

    function getGeometryHeight(width) {
        return getHeight(width) / HEX_COUNT_WIDTH * HEX_COUNT_HEIGHT;
    }

    function getHeight(width) {
        return Math.ceil(width / 4 / 0.8660254 * 3);
    }

    function getWidth(height) {
        return height / 3 * 4 * 0.8660254;
    }

    function getHexA(l) {
        return l * 0.5;
    }

    function getHexB(l) {
        return l * Math.cos(30/180*Math.PI);
    }

    function renderHex(graphics) {
        var scale = {x: 1, y: 1};
        var width = 1024;
        var hl = width / 4 / 0.8660254;
        var a = getHexA(hl);
        var b = getHexB(hl);
        var x = b;


        var height = getHeight(width);
        console.log(height, width - height);

        var canvas = createCanvas(width, height, false);
        var context = canvas.getContext("2d", {antialias: true});
        context.fillStyle   = HEX_LINE_COLOR;
        context.strokeStyle = HEX_LINE_COLOR;
        context.lineWidth = HEX_LINE_WIDTH;

        //context.setTransform(1, 0, 0, 1, 0, 0);
        context.scale(scale.x, scale.y);

        // @todo
        //context.translate(0, -147.8);

        graphics.drawCenteredHexagon(context, x, 0, hl, true, true, true);
        graphics.drawCenteredHexagon(context, x*3, 0, hl, true, true, true);
        graphics.drawCenteredHexagon(context, x*2, hl+a, hl, true, true, true);
        graphics.drawCenteredHexagon(context, x, hl*3, hl, true, true, true);
        graphics.drawCenteredHexagon(context, x*3, hl*3, hl, true, true, true);

        var canvas2 = createCanvas(width, width, false);

        canvas2.getContext('2d', {antialias: true}).drawImage(canvas, 0, 0, width, height, 0, 0, width, width);

        return canvas2;
    }

    function createCanvas(width, height, debug){
        var canvas = $('<canvas width="'+width+'" height="'+height+'"></canvas>').css({
            position: 'absolute',
            top: '100px',
            right: '100px',
            border: '1px solid red',
            zIndex: 1000,
            backgroundColor: 'black'//'transparent'
            //,display: 'none'
        });
        if (debug) {
            $(document.body).append(canvas);
        }

        canvas = canvas[0];
        return canvas;
    }

    function getTexture(canvas, gridWidth, gridHeight){
        var texture = new THREE.Texture(canvas);
        texture.needsUpdate = true;
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(
            getHorizontalRepeat(gridWidth),
            getVerticalRepeat(gridHeight)
        );

        texture.offset.set(
            0,
            0.16666
        );

        return texture;
    }

    function getHorizontalRepeat(hexagons)
    {
        return 3/4 + (hexagons - 1) * 2/4;
    }

    function getVerticalRepeat(hexagons)
    {
        return 4/6 + (hexagons - 1) * 3/6;
    }

    return webglHexGridRenderer;
})();


