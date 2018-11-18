"use strict";

window.ShipFacingSprite = (function() {
  let texture = null;
  const TEXTURE_SIZE = 256;

  function ShipFacingSprite(size, z, opacity) {
    webglSprite.call(this, null, size, z);
    this.setOpacity(opacity);

    createTexture(this.facing);
    this.uniforms.texture.value = texture;
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

  function createTexture(facing) {
    if (texture) {
      return;
    }

    var canvas = window.AbstractCanvas.create(TEXTURE_SIZE, TEXTURE_SIZE);
    var context = canvas.getContext("2d");
    context.strokeStyle = "rgba(0,0,255,1.0)";
    context.fillStyle = "rgba(0,0,255,0.5)";

    window.graphics.drawArrow(
      context,
      TEXTURE_SIZE / 2,
      TEXTURE_SIZE / 2,
      0,
      TEXTURE_SIZE / 2,
      3
    );

    texture = new THREE.Texture(canvas);
    texture.needsUpdate = true;
  }

  ShipFacingSprite.prototype = Object.create(webglSprite.prototype);

  return ShipFacingSprite;
})();
