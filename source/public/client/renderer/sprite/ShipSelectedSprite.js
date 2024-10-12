"use strict";

window.ShipSelectedSprite = function () {

    var TEXTURE_SIZE = 256;
    var TEXTURE_ALLY = null;
    var TEXTURE_ENEMY = null;
    var TEXTURE_NEUTRAL = null;
    var TEXTURE_ALLY_SELECTED = null;
    var TEXTURE_ENEMY_SELECTED = null;

    function ShipSelectedSprite(size, z, type, selected) {

        this.DEW = 0;
        this.CCEW = 0;
        webglSprite.call(this, null, size, z);

        if (!TEXTURE_ALLY) {
            createTextures();
        }

        this.uniforms.texture.value = chooseTexture(type, selected);
    }

    function chooseTexture(type, selected) {
        if (type == "ally" && selected) {
            return TEXTURE_ALLY_SELECTED;
        } else if (type == "ally" && !selected) {
            return TEXTURE_ALLY;
        } else if (type == "enemy" && selected) {
            return TEXTURE_ENEMY_SELECTED;
        } else if (type == "enemy" && !selected) {
            return TEXTURE_ENEMY;
        } else {
            return TEXTURE_NEUTRAL;
        }
    }

    function createTextures() {
        TEXTURE_ALLY = createTexture('ally', false);
        TEXTURE_ALLY_SELECTED = createTexture('ally', true);
        TEXTURE_ENEMY = createTexture('enemy', false);
        TEXTURE_ENEMY_SELECTED = createTexture('enemy', true); //Actually use this somehow for enemies moving this turn.
        TEXTURE_NEUTRAL = createTexture('neutral', true);
    }

    function createTexture(type, selected) {
        var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
        var context = canvas.getContext("2d");
        getColorByType(context, type, selected);

        if (selected && type == 'ally') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.23, TEXTURE_SIZE * 0.30, 16, 0.3);
        } else if (selected && type == 'enemy') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.23, TEXTURE_SIZE * 0.30, 10, 0.20);      
		} else if (selected && type == 'neutral') {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.22, TEXTURE_SIZE * 0.26, 4, 0.15);      
		} else {
            window.graphics.drawCircleAndFill(context, TEXTURE_SIZE / 2, TEXTURE_SIZE / 2, TEXTURE_SIZE * 0.30, 4);
        }

        var tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;

        return tex;
    }

    function getColorByType(context, type, selected) {

        var a = -0.1;

        if (type == "ally" && selected) {
            context.strokeStyle = "rgba(78,220,25," + (0.60 + a) + ")";
            context.fillStyle = "rgba(78,220,25," + (0.40 + a) + ")";
        } else if (type == "ally") {
            context.strokeStyle = "rgba(78,220,25," + (0.50 + a) + ")";
            context.fillStyle = "rgba(78,220,25," + (0.30 + a) + ")";
        } else if (type == "enemy" && selected) {
            context.strokeStyle = "rgba(229,87,38," + (0.80 + a) + ")";
            context.fillStyle = "rgba(229,87,38," + (0.50 + a) + ")";
        } else if (type == "enemy") {
            context.strokeStyle = "rgba(229,87,38," + (0.70 + a) + ")";
            context.fillStyle = "rgba(229,87,38," + (0.30 + a) + ")";
        } else {
            context.strokeStyle = "rgba(255,194,102,0.45)";
            context.fillStyle = "rgba(255,194,102,0.25)";
        }
    }

    ShipSelectedSprite.prototype = Object.create(webglSprite.prototype);

    return ShipSelectedSprite;
}();