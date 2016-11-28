window.webglHexGridRenderer = (function(){

    var HEX_LENGTH = 128;
    var HEX_COUNT_WIDTH = 199;
    var HEX_COUNT_HEIGHT = 199;
    var HEX_LINE_COLOR = "rgba(255,255,255,255)";
    var HEX_LINE_WIDTH = 20;
    var HEX_CANVAS_SIZE = 1024;
    var HEX_OPACITY = 0.2;
    var HEX_MAX_OPACITY = 0.3;

    function webglHexGridRenderer (graphics){
        this.graphics = graphics;
        this.material = null;
        this.mesh = null;
        this.minZoom = 0;
        this.maxZoom = 0;
        this.hexSize = 128;
    }


    webglHexGridRenderer.prototype.renderHexGrid = function (scene, minZoom, maxZoom, hexLength) {
        this.minZoom = minZoom;
        this.maxZoom = maxZoom;
        HEX_LENGTH = hexLength;

        var width = getGeometryWidth();
        var height = getGeometryHeight();

        var texture = getTexture(renderHex(graphics), HEX_COUNT_WIDTH, HEX_COUNT_HEIGHT);
        var geometry = new THREE.PlaneGeometry(width, height, 1, 1);
        this.material = new THREE.MeshBasicMaterial({ map: texture, transparent: true, opacity: HEX_OPACITY });
        this.mesh = new THREE.Mesh(geometry, this.material);
        this.mesh.position.x += getHexB(HEX_LENGTH)/2;
        scene.add(this.mesh);

        drawGameSpace(scene);


        //debug(scene);
        //debug2(scene);
    };

    function drawGameSpace(scene) {
        var gamespace = gamedata.gamespace;

        if(!gamespace) {
            return
        }

        var width = parseInt(gamespace.substr(0, gamespace.indexOf("x")));
        var height = parseInt(gamespace.substr(gamespace.indexOf("x")+1));

        if(width == -1 || height == -1){
            return;
        }

        var lefttop = new hexagon.FVHex(-(width/2), (height - (height/2)));
        var righttop = new hexagon.FVHex((width-(width/2)), (height - (height/2)));
        var leftbottom = new hexagon.FVHex(-(width/2), -(height/2));
        //var rightbottom = new hexagon.FVHex((width-(width/2)), - (height/2));

        var lineWidth = 10;

        var gameWidth = window.coordinateConverter.fromHexToGame(righttop).x - window.coordinateConverter.fromHexToGame(lefttop).x;
        var gameHeight = window.coordinateConverter.fromHexToGame(lefttop).y - window.coordinateConverter.fromHexToGame(leftbottom).y;

        var sprite = new window.BoxSprite(
            {
                width: gameWidth + lineWidth,
                height: gameHeight + lineWidth
            },
            10,
            0,
            new THREE.Color(0,0,0),
            1
        );
        scene.add(sprite.mesh);
    }

    function debug(scene){
        for (var i = 0; i < 15; i++ ){
            x = getHexWidth(HEX_LENGTH)/2* i;
            y = (getHexHeight(HEX_LENGTH) - getHexA(HEX_LENGTH)) * i;
            scene.add(buildHexagonGeomatry(x,y, HEX_LENGTH));
        }
    }

    function debug2(scene){
        for (var i = 0; i < 21; i++ ){
            x = getHexWidth(HEX_LENGTH) * i;
            scene.add(buildHexagonGeomatry(x,0, HEX_LENGTH));
        }
    }

    webglHexGridRenderer.prototype.onZoom = function (zoom) {

        if (zoom > 1) {
            this.material.opacity = (HEX_MAX_OPACITY - HEX_OPACITY) * ((zoom-1) / (this.maxZoom-1)) + HEX_OPACITY;
        } else {
            this.material.opacity = zoom * HEX_OPACITY;
        }
    };

    function buildHexagonGeomatry(x, y, l) {

        var material = new THREE.LineBasicMaterial({
            color: 0x0000ff,
            linewidth: 30
        });
        var geometry = new THREE.Geometry();

        getCenteredHexagonPoints(x, y, l).forEach(function(point){
            geometry.vertices.push(
                new THREE.Vector3(point.x, point.y, 0 )
            );
        });


        return new THREE.Line( geometry, material );

    }

    function getCenteredHexagonPoints(x, y, l) {
        var a = getHexA(l);
        var b = getHexB(l);

        x = x - b;
        y = y - a*2;

        return [
            {x:x, y:y+a+l},
            {x:x, y:y+a},
            {x:x+b, y:y},
            {x:x+(2*b), y:y+a},
            {x:x+(2*b), y:y+a+l},
            {x:x+b, y:y+(2*l)},
            {x:x, y:y+a+l}
        ];
    }

    function getGeometryWidth() {
        return getHexWidth(HEX_LENGTH) * (HEX_COUNT_WIDTH + 0.5)
    }

    function getGeometryHeight() {
        var amount = Math.floor(HEX_COUNT_HEIGHT / 2);
        var height = amount * (getHexHeight(HEX_LENGTH) + HEX_LENGTH);

        if (HEX_COUNT_HEIGHT % 2 !== 0) {
            height += getHexHeight(HEX_LENGTH);
        }

        return height;
    }

    function getHexHeight(hexSide) {
        return hexSide + 2* getHexA(hexSide);;
    }

    function getHexWidth(hexSide) {
        return getHexB(hexSide) * 2;
    }

    function getTextureHeight(hexSide) {
        return Math.floor(hexSide + getHexHeight(hexSide));
    }

    function getHexA(l) {
        return l * Math.sin(30/180*Math.PI);
    }

    function getHexB(l) {
        return l * Math.cos(30/180*Math.PI);
    }

    function renderHex(graphics) {
        var scale = {x: 1, y: 1};
        var width = HEX_CANVAS_SIZE;
        var hl = width / 4 / Math.cos(30/180*Math.PI);
        var a = getHexA(hl);
        var b = getHexB(hl);
        var x = b;

        var height = getTextureHeight(hl);
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
        return window.AbstractCanvas.create(width, height, debug);
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


