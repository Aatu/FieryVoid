window.coordinateConverter = (function(){

    function coordinateConverter(){
        this.hexlenght = 0;
        this.width = 0;
        this.height = 0;

        this.cameraPosition = {x:0, y:0};
        this.zoom = 1;
    }

    coordinateConverter.prototype.init = function (width, height) {
        this.hexlenght = window.Config.HEX_SIZE;
        this.width = width;
        this.height = height;
    };

    coordinateConverter.prototype.onResize = function (width, height) {
        this.width = width;
        this.height = height;
    };

    coordinateConverter.prototype.onCameraMoved = function (cameraPosition) {
        this.cameraPosition = cameraPosition;
    };

    coordinateConverter.prototype.onZoom = function (zoom) {
        this.zoom = zoom;
    };

    coordinateConverter.prototype.fromHexToViewport = function(hex)
    {
        return this.fromGameToViewPort(this.fromHexToGame(hex));
    };

    coordinateConverter.prototype.getHexHeightViewport = function()
    {
        return window.HexagonMath.getHexHeight() / this.zoom;
    };

    coordinateConverter.prototype.getHexDistance = function()
    {
        var hex1 = new hexagon.Offset(0, 0);
        var hex2 = hex1.getNeighbourAtDirection(0);

        return mathlib.distance(this.fromHexToGame(hex1), this.fromHexToGame(hex2));
    };

    coordinateConverter.prototype.fromGameToHex = function(gameCoordinates)
    {
        var q = (1/3 * Math.sqrt(3) * gameCoordinates.x - 1/3 * gameCoordinates.y) / this.hexlenght;
        var r = 2/3 * gameCoordinates.y / this.hexlenght;

        var x = q;
        var z = r;
        var y = -x - z;

        return new hexagon.Cube(x, y, z).round().toOffset(); //.toFVHex();
    };

    coordinateConverter.prototype.fromHexToGame = function(offsetHex)
    {
        if (offsetHex instanceof hexagon.Cube) {
            offsetHex = offsetHex.toOffset();
        }

        if (!(offsetHex instanceof hexagon.Offset) && typeof offsetHex == "object") {
            offsetHex = new hexagon.Offset(offsetHex.q, offsetHex.r);
        }

        var x = this.hexlenght * Math.sqrt(3) * (offsetHex.q - 0.5 * (offsetHex.r&1));
        var y = this.hexlenght * 3/2 * offsetHex.r;

        return {x: x, y: y};
    };

    coordinateConverter.prototype.fromViewPortToGame = function(pos)
    {
        var cameraPos = this.cameraPosition;
        var zoom = this.zoom;
        var windowDimensions = {width:this.width, height: this.height};

        var positionFromCenterOfScreen = {x: pos.x - windowDimensions.width/2, y: windowDimensions.height/2 - pos.y };
        var withZoom = {x: positionFromCenterOfScreen.x * zoom, y: positionFromCenterOfScreen.y * zoom};

        var positionFromCamera = {x:withZoom.x + cameraPos.x, y:withZoom.y + cameraPos.y};
        return positionFromCamera;
    };

    coordinateConverter.prototype.fromGameToViewPort = function(pos)
    {
        var cameraPos = this.cameraPosition;
        var zoom = this.zoom;
        var windowDimensions = {width:this.width, height: this.height};

        var positionFromCamera = {x: pos.x - cameraPos.x, y:pos.y - cameraPos.y};
        var withZoom = {x: positionFromCamera.x / zoom, y: positionFromCamera.y / zoom};
        var positionFromCenterOfScreen = {x: withZoom.x + windowDimensions.width/2, y: windowDimensions.height/2 - withZoom.y};

        return positionFromCenterOfScreen;
    };

    return new coordinateConverter();
})();
