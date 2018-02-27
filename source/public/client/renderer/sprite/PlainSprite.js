"use strict";

window.PlainSprite = function () {

    function PlainSprite(size, z, color, opacity) {
        this.z = z || 0;
        this.size = size;
        this.mesh = create.call(this, size, color, opacity);
    }

    PlainSprite.prototype = Object.create(webglSprite.prototype);

    function create(size, color, opacity) {
        var mesh = new THREE.Mesh(new THREE.PlaneGeometry(size.width, size.height, 1, 1), new THREE.MeshBasicMaterial({ color: color, transparent: true, opacity: opacity }));

        mesh.position.z = this.z;
        return mesh;
    }

    return PlainSprite;
}();