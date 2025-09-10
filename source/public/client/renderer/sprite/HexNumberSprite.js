"use strict";

window.HexNumberSprite = function () {
    var TEXTURE_SIZE = 512;
    var opacity = 0.85;	

    function HexNumberSprite(position, type, text = "", textColour = "#ffffff", textSize = 30) {
        HexagonSprite.call(this, -3);

		 if (text) {
            // If there is custom text (like Thoughtwave, Ion Field, etc.), create a custom texture
            this.uniforms.texture.value = createTextureWithText(type, text, textColour, textSize, opacity);
        }

        this.setPosition(position);
    }


    HexNumberSprite.prototype = Object.create(HexagonSprite.prototype);
/*
function createTextureWithText(type, text, textColour, textSize) {

    var canvas = HexagonTexture.renderNumberGrid(TEXTURE_SIZE, 10);
    var ctx = canvas.getContext('2d');
    
    // Set initial font size and style
    var fontSize = textSize;
    ctx.globalAlpha = opacity;  // Set opacity for the text
    ctx.font = `bold ${fontSize}px Arial`;
    ctx.fillStyle = textColour;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";

    // Measure the text width
    var maxWidth = TEXTURE_SIZE * 0.4;  // Max width for text (40% of texture size)
    var textWidth = ctx.measureText(text).width;

    // Adjust font size if text is too wide
    while (textWidth > maxWidth && fontSize > 10) {
        fontSize -= 5;  // Reduce font size in steps
        ctx.font = `bold ${fontSize}px Arial`;
        textWidth = ctx.measureText(text).width;
    }

    // Draw the text centered in the middle of the texture
    ctx.fillText(text, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2);

    // Create and return a texture from the canvas
    var tex = new THREE.Texture(canvas);
    tex.needsUpdate = true;

    return tex;
}
*/
    return HexNumberSprite;
}();