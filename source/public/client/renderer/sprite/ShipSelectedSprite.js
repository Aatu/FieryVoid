"use strict";

window.ShipSelectedSprite = (function() {
  let texture = null;
  const TEXTURE_SIZE = 256;

  function ShipSelectedSprite(size, z, opacity) {
    webglSprite.call(this, null, size, z);
    this.setOpacity(opacity);

    createTexture();
    this.uniforms.texture.value = texture;
  }

  function createTexture() {
    if (texture) {
      return;
    }

    var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
    var context = canvas.getContext("2d");
    context.strokeStyle = "rgba(78,220,25,1.0)";
    context.fillStyle = "rgba(78,220,25,0.5)";

    window.graphics.drawCircleAndFill(
      context,
      TEXTURE_SIZE / 2,
      TEXTURE_SIZE / 2,
      TEXTURE_SIZE * 0.3,
      4
    );

    texture = new THREE.Texture(canvas);
    texture.needsUpdate = true;
  }

  ShipSelectedSprite.prototype = Object.create(webglSprite.prototype);

  return ShipSelectedSprite;
})();
