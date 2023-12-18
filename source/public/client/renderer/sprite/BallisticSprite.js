"use strict";

window.BallisticSprite = function () {

    var TEXTURE_SIZE = 512;
    var TEXTURE_LAUNCH = null;
    var TEXTURE_HEX = null;
    var TEXTURE_SHIP = null;
    var TEXTURE_HEX_DIRECT_BLUE = null;
    var TEXTURE_HEX_DIRECT_GREEN = null;
    var TEXTURE_HEX_DIRECT_YELLOW = null;         

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
        } else if (type == "hexDirectBlue") {
            return TEXTURE_HEX_DIRECT_BLUE;
        } else if (type == "hexDirectGreen") {
            return TEXTURE_HEX_DIRECT_GREEN;
        }else if (type == "hexDirectYellow") {
            return TEXTURE_HEX_DIRECT_YELLOW;
        }else {
            return TEXTURE_SHIP;
        }
    }

    function createTextures() {
        TEXTURE_LAUNCH = createTexture('launch');
        TEXTURE_HEX = createTexture('hex');
        TEXTURE_SHIP = createTexture('ship');
        TEXTURE_HEX_DIRECT_BLUE = createTexture('hexDirectBlue');
        TEXTURE_HEX_DIRECT_GREEN = createTexture('hexDirectGreen');
        TEXTURE_HEX_DIRECT_YELLOW = createTexture('hexDirectYellow');                  
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
        } else if (type == "hexDirectBlue") {//direct fire target hex - light blue
            return "rgba(0,184,230,0.50)";
        } else if (type == "hexDirectGreen") {//direct fire target hex - light green
            return "rgba(0, 204, 0,0.50)";
        }else if (type == "hexDirectYellow") {//direct fire target hex - light yellow
            return "rgba(255, 255, 0,0.50)";
        }else {//...something else...
            return "rgba(144,185,208,0.80)";
        }
    }

    function getFillColorByType(type) {

        if (type == "launch") {
            return "rgba(250,110,5,0.15)";//launch hex - orange
        } else if (type == "hex") {//ballistic target hex - red
            return "rgba(230,20,10,0.15)";
        } else if (type == "hexDirectBlue") {//direct fire target hex - light blue
            return "rgba(0,184,230,0.15)";
        } else if (type == "hexDirectGreen") {//direct fire target hex - light green
            return "rgba(0, 204, 0,0.15)";
        }else if (type == "hexDirectYellow") {//direct fire target hex - light yellow
            return "rgba(255, 255, 0,0.15)";
        }else {//...something else...
            return "rgba(144,185,208,0.30)";
        }
    }

    return BallisticSprite;
}();