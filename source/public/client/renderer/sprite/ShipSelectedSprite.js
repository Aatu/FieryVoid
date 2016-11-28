window.ShipSelectedSprite = (function(){

    var TEXTURE_SIZE = 256;
    var TEXTURE_ALLY = null;
    var TEXTURE_ENEMY = null;
    var TEXTURE_ALLY_SELECTED = null;
    var TEXTURE_ENEMY_SELECTED = null;

    function ShipSelectedSprite(size, z, type, selected){

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
        }
    }

    function createTextures(){
        TEXTURE_ALLY =              createTexture('ally', false);
        TEXTURE_ALLY_SELECTED =     createTexture('ally', true);
        TEXTURE_ENEMY =             createTexture('enemy', false);
        TEXTURE_ENEMY_SELECTED =    createTexture('enemy', true);
    }

    function createTexture(type, selected) {
        var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
        var context = canvas.getContext("2d");
        getColorByType(context, type, selected);


        if (selected) {
            window.graphics.drawDottedCircle(context, TEXTURE_SIZE/2, TEXTURE_SIZE/2, TEXTURE_SIZE*0.25, TEXTURE_SIZE*0.30, 16, 0.3);
        } else {
            window.graphics.drawCircleAndFill(context, TEXTURE_SIZE/2, TEXTURE_SIZE/2, TEXTURE_SIZE*0.30, 1);
        }

        var tex = new THREE.Texture(canvas);
        tex.needsUpdate = true;

        return tex;
    }



    function getColorByType(context, type, selected) {

        var a = selected ? 0.1 : -0.4;

        if (type == "ally") {
            context.strokeStyle = "rgba(78,220,25,"+(0.70 + a)+")";
            context.fillStyle = "rgba(78,220,25,"+(0.60 + a)+")";
        } else if (type == "enemy") {
            context.strokeStyle = "rgba(229,87,38,"+(0.70 + a)+")";
            context.fillStyle = "rgba(229,87,38,"+(0.60 + a)+")";
        }else {
            context.strokeStyle = "rgba(144,185,208,0.80)";
            context.fillStyle = "rgba(255,255,255,0.18)";
        }

        if (!selected) {
            context.strokeStyle = "rgba(144,185,208,0.0)";
        }
    }

    ShipSelectedSprite.prototype = Object.create(webglSprite.prototype);

    return ShipSelectedSprite;
})();
