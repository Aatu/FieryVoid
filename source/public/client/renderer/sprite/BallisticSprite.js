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
    
/* //Older methods that don't accommodate text/image or just accommodate text inside hex icons

	function BallisticSprite(position, type, text = "", textColour = "#aaaa00") {
	    HexagonSprite.call(this, -2);

	    if (!TEXTURE_HEX_ORANGE) {
	        createTextures(); // Initialize all textures once
	    }
	    
	    this.uniforms.texture.value = chooseTexture(type);
	    

	    this.setPosition(position);
	}


	function BallisticSprite(position, type, text = "", textColour = "#ffffff") {
	    HexagonSprite.call(this, -2);

	    // If there is custom text (like Thoughtwave, Ion Field, Plasma Web etc.), create a custom texture
	    if (text) {
	        // Ensure textColour defaults to "#aaaa00" if not provided
	        this.uniforms.texture.value = createTextureWithText(type, text, textColour || "#ffffff");
	    } else {
	        if (!TEXTURE_HEX_ORANGE) {
	            createTextures(); // Initialize all textures once
	        }
	        this.uniforms.texture.value = chooseTexture(type);
	    }

	    this.setPosition(position);
	}
*/	
 	//Alternative method if we want to add images to the hex icons in future. DK.
    function BallisticSprite(position, type, text = "", textColour = "#ffffff", imageSrc = null) {
        HexagonSprite.call(this, -2);

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
        TEXTURE_HEX_GREEN = createTexture('hexGreen');
        TEXTURE_HEX_YELLOW = createTexture('hexYellow');
        TEXTURE_HEX_PURPLE = createTexture('hexPurple');           
        TEXTURE_HEX_GREEN_EXCLAMATION = createTextureWithText('hexGreen', "!", "#aaaa00");
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
	        var imageSize = TEXTURE_SIZE * 0.28; // Scale image to 50% of the hex size
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

    //New function to create the texture with text inside - DK 09.24
    function createTextureWithText(type, text, textColour) {
        var canvas = HexagonTexture.renderHexGrid(TEXTURE_SIZE, getStrokeColorByType(type), getFillColorByType(type), 10);
        var ctx = canvas.getContext('2d');
        
	    // Set initial font size to the maximum you expect
	    var fontSize = 130;
		var initTextColour = textColour;
		var lightenedColour = lightenColor(initTextColour, 35); // Lighten by 40%
	    
	    ctx.font = `bold ${fontSize}px Arial`;
	    ctx.fillStyle = lightenedColour;
	    ctx.textAlign = "center";
	    ctx.textBaseline = "middle";

	    // Measure the text width and reduce the font size until it fits
	    var maxWidth = TEXTURE_SIZE * 0.4; // Adjust max width based on hexagon size (80% of the hexagon width)
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