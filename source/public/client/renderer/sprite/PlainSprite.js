"use strict";

window.PlainSprite = function () {

    function PlainSprite(size, z, color, opacity, avail) {
        this.z = z || 0;
        this.size = size;

        // Create the colored plane mesh exactly as before
        this.mesh = createPlane.call(this, size, color, opacity);

        // If avail is provided, create and add a text sprite on top
        if (typeof avail === 'number' && avail > 1) {
            this.addTextSprite(avail);
        }
    }

    PlainSprite.prototype = Object.create(webglSprite.prototype);

    function createPlane(size, color, opacity) {
        var mesh = new THREE.Mesh(
            new THREE.PlaneGeometry(size.width, size.height, 1, 1),
            new THREE.MeshBasicMaterial({ color: color, transparent: true, opacity: opacity })
        );
        mesh.position.z = this.z;
        return mesh;
    }

PlainSprite.prototype.addTextSprite = function(avail) {
    var canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 128;
    var ctx = canvas.getContext('2d');

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = 'white';
    ctx.font = 'bold 48px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText("Turn " + avail, canvas.width / 2, canvas.height / 2);

    var texture = new THREE.CanvasTexture(canvas);
    texture.minFilter = THREE.LinearFilter;
    texture.magFilter = THREE.LinearFilter;
    texture.needsUpdate = true;

    var material = new THREE.SpriteMaterial({
        map: texture,
        transparent: true,
        opacity: 0.15,
        depthTest: false,
        depthWrite: false
    });

    var sprite = new THREE.Sprite(material);

    const zoneAspect = this.size.width / this.size.height;
    const textAspect = canvas.width / canvas.height; // 2

    const maxWidth = this.size.width * 0.8;
    const maxHeight = this.size.height * 0.8;

    let scaleX, scaleY;
    if (zoneAspect > textAspect) {
      // Limit by height
      scaleY = maxHeight;
      scaleX = scaleY * textAspect;
    } else {
      // Limit by width
      scaleX = maxWidth;
      scaleY = scaleX / textAspect;
    }

    sprite.scale.set(scaleX, scaleY, 1);
    sprite.position.set(0, 0, this.z - 0.01);

    this.mesh.add(sprite);
};

    return PlainSprite;
}();