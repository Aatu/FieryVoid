"use strict";

window.BallisticSprite = function () {
    var TEXTURE_SIZE = 512;
    var TEXTURE_HEX_ORANGE = null;
    var TEXTURE_HEX_RED = null;
    var TEXTURE_SHIP = null;
    var TEXTURE_HEX_BLUE = null;
    var TEXTURE_HEX_GREEN = null;
    var TEXTURE_HEX_YELLOW = null;
    var TEXTURE_HEX_PURPLE = null;         
    var TEXTURE_HEX_GREEN_EXCLAMATION = null; // New texture

    function BallisticSprite(position, type, text = "") {
        HexagonSprite.call(this, -2);

        if (!TEXTURE_HEX_ORANGE) {
            createTextures(text);
        }

        this.uniforms.texture.value = chooseTexture(type);

        this.setPosition(position);
    }

    BallisticSprite.prototype = Object.create(HexagonSprite.prototype);

    function chooseTexture(type) {
        if (type == "hexOrange") {
            return TEXTURE_HEX_ORANGE;
        } else if (type == "hexRed") {
            return TEXTURE_HEX_RED;
        } else if (type == "hexBlue") {
            return TEXTURE_HEX_BLUE;
        } else if (type == "hexGreen") {
            return TEXTURE_HEX_GREEN;
        } else if (type == "hexYellow") {
            return TEXTURE_HEX_YELLOW;
        }  else if (type == "hexPurple") {
            return TEXTURE_HEX_PURPLE;
        } else if (type == "hexGreenExclamation") { // New case
            return TEXTURE_HEX_GREEN_EXCLAMATION;
        } else {
            return TEXTURE_SHIP;
        }
    }

    function createTextures(text) {
        TEXTURE_HEX_ORANGE = createTexture('hexOrange');
        TEXTURE_HEX_RED = createTexture('hexRed');
        TEXTURE_SHIP = createTexture('ship');
        TEXTURE_HEX_BLUE = createTexture('hexBlue');
        TEXTURE_HEX_GREEN = createTextureWithText('hexGreen', text, "#aaaa00");
        TEXTURE_HEX_YELLOW = createTexture('hexYellow');
        TEXTURE_HEX_PURPLE = createTextureWithText('hexPurple', text, "#aaaa00");           
        TEXTURE_HEX_GREEN_EXCLAMATION = createTextureWithText('hexGreen', "!", "#aaaa00");
    }

    function createTexture(type) {
        var canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);

        var tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;
        return tex;
    }

    //New function to create the texture with text inside - DK 09.24
    function createTextureWithText(type, text, textColour) {
        var canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);
        var ctx = canvas.getContext('2d');
        
	    // Set initial font size to the maximum you expect
	    var fontSize = 130;
	    ctx.font = `bold ${fontSize}px Arial`;
	    ctx.fillStyle = textColour;
	    ctx.textAlign = "center";
	    ctx.textBaseline = "middle";

	    // Measure the text width and reduce the font size until it fits
	    var maxWidth = TEXTURE_SIZE * 0.5; // Adjust max width based on hexagon size (80% of the hexagon width)
	    var textWidth = ctx.measureText(text).width;

	    while (textWidth > maxWidth && fontSize > 10) { // Stop reducing at a minimum font size of 10
	        fontSize -= 5;
	        ctx.font = `bold ${fontSize}px Arial`;
	        textWidth = ctx.measureText(text).width;
	    }        
        
        ctx.fillText(text, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2);

        var tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;
        return tex;
    }

    function getStrokeColorByType(type) {
        if (type == "hexOrange") {
            return "rgba(250,110,5,0.50)"; 
        } else if (type == "hexRed") {
            return "rgba(230,20,10,0.50)";
        } else if (type == "hexBlue") {
            return "rgba(0,184,230,0.50)";
        } else if (type == "hexGreen" || type == "hexGreenExclamation") { // Combine cases
            return "rgba(0, 204, 0,0.50)";
        } else if (type == "hexYellow") {
            return "rgba(255, 255, 0,0.50)";
        } else if (type == "hexPurple") {
            return "rgba(127, 0, 255,0.50)";
        } else {
            return "rgba(144,185,208,0.80)";
        }
    }

    function getFillColorByType(type) {
        if (type == "hexOrange") {
            return "rgba(250,110,5,0.15)";
        } else if (type == "hexRed") {
            return "rgba(230,20,10,0.15)";
        } else if (type == "hexBlue") {
            return "rgba(0,184,230,0.15)";
        } else if (type == "hexGreen" || type == "hexGreenExclamation") { // Combine cases
            return "rgba(0, 204, 0,0.15)";
        } else if (type == "hexYellow") {
            return "rgba(255, 255, 0,0.15)";
        } else if (type == "hexPurple") {
            return "rgba(127, 0, 255,0.15)";
        } else {
            return "rgba(144,185,208,0.30)";
        }
    }

    return BallisticSprite;
}();