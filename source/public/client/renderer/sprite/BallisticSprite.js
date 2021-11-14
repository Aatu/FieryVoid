"use strict";

window.BallisticSprite = function () {

    var TEXTURE_SIZE = 512;
    var TEXTURE_LAUNCH = null;
    var TEXTURE_HEX = null;
    var TEXTURE_SHIP = null;
    var TEXTURE_HEX_DIRECT = null;

    function BallisticSprite(position, type) {
        HexagonSprite.call(this, -2);

        if (!TEXTURE_LAUNCH) {
            createTextures();
        }

        this.uniforms.texture.value = chooseTexture(type);

        this.setPosition(position);
    }

    BallisticSprite.prototype = Object.create(HexagonSprite.prototype);

    function chooseTexture(type) {
        if (type == "launch") {
            return TEXTURE_LAUNCH;
        } else if (type == "hex") {
            return TEXTURE_HEX;
        } else if (type == "hexDirect") {
            return TEXTURE_HEX_DIRECT;
        } else {
            return TEXTURE_SHIP;
        }
    }

    function createTextures() {
        TEXTURE_LAUNCH = createTexture('launch');
        TEXTURE_HEX = createTexture('hex');
        TEXTURE_SHIP = createTexture('ship');
        TEXTURE_HEX_DIRECT = createTexture('hexDirect'); 
    }

    function createTexture(type) {

        var canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);

        var tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;
        return tex;
    }

    function getStrokeColorByType(type) {

        if (type == "launch") {//launch hex - orange
            return "rgba(250,110,5,0.50)"; 
        } else if (type == "hex") {//ballistic target hex - red
            return "rgba(230,20,10,0.50)";
        } else if (type == "hexDirect") {//direct fire target hex - light blue
            return "rgba(25,200,250,0.50)";
        } else {//...something else...
            return "rgba(144,185,208,0.80)";
        }
    }

    function getFillColorByType(type) {

        if (type == "launch") {
            return "rgba(250,110,5,0.15)";//launch hex - orange
        } else if (type == "hex") {//ballistic target hex - red
            return "rgba(230,20,10,0.15)";
        } else if (type == "hexDirect") {//direct fire target hex - light blue
            return "rgba(25,200,250,0.50)";
        } else {//...something else...
            return "rgba(144,185,208,0.30)";
        }
    }

    return BallisticSprite;
}();