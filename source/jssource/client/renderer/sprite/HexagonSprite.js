window.HexagonSprite = (function(){

    function HexagonSprite(z, scale){

        if (!scale) {
            scale = 1;
        }

        if (!z ) {
            z =0;
        }


        webglSprite.call(
            this,
            null,
            {
                width: window.HexagonMath.getTextureWidth() * scale,
                height: window.HexagonMath.getTextureHeight() * scale
            },
            z
        );
    }

    HexagonSprite.prototype = Object.create(webglSprite.prototype);

    return HexagonSprite;
})();
