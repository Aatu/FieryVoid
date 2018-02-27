window.ShipEWSprite = (function(){

    var TEXTURE_SIZE = 256;

    function ShipEWSprite(size, z){

        this.DEW = 0;
        this.CCEW = 0;
        webglSprite.call(this, null, size, z);
    }

    ShipEWSprite.prototype = Object.create(webglSprite.prototype);

    ShipEWSprite.prototype.update = function(DEW, CCEW) {

        if (this.DEW === DEW && this.CCEW === CCEW) {
            return;
        }

        this.DEW = DEW;
        this.CCEW = CCEW;

        var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
        var context = canvas.getContext("2d");
        drawDEW(context, DEW);
        drawCCEW(context, DEW, CCEW);

        var texture = new THREE.Texture(canvas);
        texture.needsUpdate = true;
        this.uniforms.texture.value = texture;
    };

    function drawDEW(context, DEW){
        if (! DEW) {
            return;
        }

        var a = 0.40;

        if (DEW < 3) {
            a = 0.70;
        }

        context.strokeStyle = "rgba(144,185,208,0)";
        context.fillStyle = "rgba(144,185,208,"+a+")";

        var r1 = getDEWStart();
        var r2 = getDEWStart() + DEW;

        graphics.drawFilledCircle(context, TEXTURE_SIZE/2, TEXTURE_SIZE/2, r1, r2);
    }

    function getDEWStart() {
        return Math.ceil(TEXTURE_SIZE*0.31);
    }

    function getCCEWStart(DEW) {

        return Math.ceil(TEXTURE_SIZE*0.32) + DEW ;
    }

    function drawCCEW(context, DEW, CCEW){
        if (! CCEW){
            return;
        }

        var a = 0.70;

        if (CCEW < 3) {
            a = 0.90;
        }

        context.strokeStyle = "rgba(20,80,128,0)";
        context.fillStyle = "rgba(20,80,128,"+a+")";

        var r1 = getCCEWStart(DEW);
        var r2 = getCCEWStart(DEW) + CCEW;

        graphics.drawFilledCircle(context, TEXTURE_SIZE/2, TEXTURE_SIZE/2, r1, r2);
    }

    return ShipEWSprite;
})();
