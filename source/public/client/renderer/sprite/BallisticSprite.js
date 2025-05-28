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
    var TEXTURE_HEX_WHITE = null;
    var TEXTURE_HEX_CLEAR = null;             

    function BallisticSprite(position, type, text = "", textColour = "#ffffff", imageSrc = null) {
        HexagonSprite.call(this, -3);

        // If an image source is provided, create a texture with the image
        if (imageSrc) {
            this.uniforms.texture.value = createTextureWithImage(type, text, textColour, imageSrc);
        } else if (text) {
            // If there is custom text (like Thoughtwave, Ion Field, etc.), create a custom texture
            this.uniforms.texture.value = createTextureWithText(type, text, textColour || "#ffffff");
        } else {
            if (!TEXTURE_HEX_ORANGE) {
                createTextures(); // Initialize all textures once
            }
            this.uniforms.texture.value = chooseTexture(type);
        }

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
        } else if (type == "hexPurple") {
            return TEXTURE_HEX_PURPLE;
        } else if (type == "hexWhite") { 
            return TEXTURE_HEX_WHITE;
        } else if (type == "hexClear") {  // ✅ New hexClear case
            return TEXTURE_HEX_CLEAR;
        } else {
            return TEXTURE_SHIP;
        }
    }

    function createTextures() {
        TEXTURE_HEX_ORANGE = createTexture('hexOrange');
        TEXTURE_HEX_RED = createTexture('hexRed');
        TEXTURE_SHIP = createTexture('ship');
        TEXTURE_HEX_BLUE = createTexture('hexBlue');
        TEXTURE_HEX_GREEN = createTexture('hexGreen');
        TEXTURE_HEX_YELLOW = createTexture('hexYellow');
        TEXTURE_HEX_PURPLE = createTexture('hexPurple');
        TEXTURE_HEX_WHITE = createTexture('hexWhite');
        TEXTURE_HEX_CLEAR = createTexture('hexClear'); // ✅ New hexClear texture
    }

    function createTexture(type) {
        var canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);

        var tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;
        return tex;
    }

	function createTextureWithImage(type, text, textColour, imageSrc) {
	    // Create the initial hex grid texture with the colored background
	    var canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);
	    var ctx = canvas.getContext('2d');

	    // Create a temporary texture to return immediately
	    var tex = new THREE.Texture(canvas);
	    tex.needsUpdate = true;

	    // Load the image asynchronously
	    var image = new Image();
	    image.src = imageSrc;

	    image.onload = function () {
	        // Redraw the hex grid background (without clearing the canvas)
	        HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10, ctx);

	        // Scale and position the image in the center of the hex
	        var imageSize = TEXTURE_SIZE * 0.28; // Scale image to 28% of the hex size
	        var xPos = (TEXTURE_SIZE - imageSize) / 2;
	        var yPos = (TEXTURE_SIZE - imageSize) / 2;

	        ctx.drawImage(image, xPos, yPos, imageSize, imageSize);

	        // Optionally draw text after the image
	        if (text) {
	            var fontSize = 25;
				var initTextColour = textColour;
				var lightenedColour = lightenColor(initTextColour, 40); // Lighten by 40%            
	            ctx.font = `bold ${fontSize}px Arial`;
	            ctx.fillStyle = lightenedColour || "#ffffff";
	            ctx.textAlign = "center";
	            ctx.textBaseline = "middle";
	            ctx.fillText(text, TEXTURE_SIZE / 2, TEXTURE_SIZE / 1.55);
	        }

	        // Update the texture with the new canvas content
	        tex.needsUpdate = true;
	    };

	    // Return the placeholder texture, which will update once the image is loaded
	    return tex;
	}

    function createTextureWithText(type, text, textColour) {
        const canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);
        const ctx = canvas.getContext('2d');

        let fontSize = 40;
        const initTextColour = textColour;
        const lightenedColour = lightenColor(initTextColour, 35);
        ctx.fillStyle = lightenedColour;
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";


    const maxWidth = TEXTURE_SIZE * 0.45;

    let lines;
    do {
        ctx.font = `bold ${fontSize}px Arial`;
        lines = wrapText(ctx, text, maxWidth);
        fontSize -= 5;
    } while (lines.some(line => ctx.measureText(line).width > maxWidth) && fontSize > 10);

    ctx.font = `bold ${fontSize}px Arial`; // Ensure correct font used after loop

        const lineHeight = fontSize * 1.2;
        const totalHeight = lines.length * lineHeight;
        const startY = (TEXTURE_SIZE / 2) - (totalHeight / 2) + (lineHeight / 2);

        lines.forEach((line, index) => {
            const y = startY + index * lineHeight;
            ctx.fillText(line, TEXTURE_SIZE / 2, y);
        });

        const tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;
        return tex;
    }

    function wrapText(ctx, text, maxWidth) {
        const words = text.split(" ");
        const lines = [];
        let currentLine = words[0];

        for (let i = 1; i < words.length; i++) {
            const word = words[i];
            const width = ctx.measureText(currentLine + " " + word).width;
            if (width < maxWidth) {
                currentLine += " " + word;
            } else {
                lines.push(currentLine);
                currentLine = word;
            }
        }
        lines.push(currentLine);
        return lines;
    }

	function lightenColor(hex, percent) { //Need to lighten text so it stands out from hex clouring a little!
	    // Convert hex to RGB
	    var num = parseInt(hex.slice(1), 16),
	        r = (num >> 16) + Math.round(255 * percent / 100),
	        g = ((num >> 8) & 0x00FF) + Math.round(255 * percent / 100),
	        b = (num & 0x0000FF) + Math.round(255 * percent / 100);
	    
	    // Ensure values stay within bounds
	    r = r > 255 ? 255 : r;
	    g = g > 255 ? 255 : g;
	    b = b > 255 ? 255 : b;

	    // Convert back to hex
	    return `#${(r << 16 | g << 8 | b).toString(16).padStart(6, 0)}`;
	}


    function getStrokeColorByType(type) {
        if (type == "hexOrange") {
            return "rgba(250,110,5,0.50)"; 
        } else if (type == "hexRed") {
            return "rgba(230,20,10,0.50)";
        } else if (type == "hexBlue") {
            return "rgba(0,184,230,0.50)";
        } else if (type == "hexGreen") {
            return "rgba(0, 204, 0,0.50)";
        } else if (type == "hexYellow") {
            return "rgba(255, 255, 0,0.50)";
        } else if (type == "hexPurple") {
            return "rgba(127, 0, 255,0.50)";
        } else if (type == "hexWhite") { 
            return "rgba(255, 255, 255,0.40)";
        } else if (type == "hexClear") { // ✅ No stroke color
            return "rgba(0,0,0,0)";
        } else {
            return "rgba(144,185,208,0.80)";
        }
    }
    
    function getFillColorByType(type) {
        if (type == "hexOrange") {
            return "rgba(250,110,5,0.10)";
        } else if (type == "hexRed") {
            return "rgba(230,20,10,0.10)";
        } else if (type == "hexBlue") {
            return "rgba(0,184,230,0.10)";
        } else if (type == "hexGreen") {
            return "rgba(0, 204, 0,0.10)";
        } else if (type == "hexYellow") {
            return "rgba(255, 255, 0,0.10)";
        } else if (type == "hexPurple") {
            return "rgba(127, 0, 255,0.10)";
        } else if (type == "hexWhite") { 
            return "rgba(255, 255, 255,0.10)";
        } else if (type == "hexClear") { // ✅ No fill color
            return "rgba(0,0,0,0)";
        } else {
            return "rgba(144,185,208,0.30)";
        }
    }

    return BallisticSprite;
}();