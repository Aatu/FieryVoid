"use strict";

window.BoxSprite = function () {

    function BoxSprite(size, lineWidth, z, color, opacity) {
        this.z = z || 0;
        this.mesh = null;
        this.size = { width: size.width + lineWidth, height: size.height + lineWidth };

        this.color = color;
        this.opacity = opacity;

        this.mesh = create.call(this, this.size, lineWidth);
    }

    BoxSprite.prototype = Object.create(webglSprite.prototype);

    function createSide(geometry, p1, p2, p3, p4, i) {
        geometry.vertices.push(p1, p2, p3, p4);

        var a = i * 4;

        geometry.faces.push(new THREE.Face3(a + 2, a + 1, a));
        geometry.faces.push(new THREE.Face3(a + 2, a + 3, a + 1));

        return ++i;
    }

    function create(size, lineWidth) {
        var width = size.width;
        var height = size.height;

        var geometry = new THREE.Geometry();
        var i = 0;

        i = createSide(geometry, new THREE.Vector3(-width / 2, height / 2, 0), new THREE.Vector3(-width / 2 + lineWidth, height / 2, 0), new THREE.Vector3(-width / 2, -height / 2, 0), new THREE.Vector3(-width / 2 + lineWidth, -height / 2, 0), i);

        i = createSide(geometry, new THREE.Vector3(width / 2, -height / 2, 0), new THREE.Vector3(width / 2 - lineWidth, -height / 2, 0), new THREE.Vector3(width / 2, height / 2, 0), new THREE.Vector3(width / 2 - lineWidth, height / 2, 0), i);

        i = createSide(geometry, new THREE.Vector3(-width / 2, -height / 2, 0), new THREE.Vector3(-width / 2, -height / 2 + lineWidth, 0), new THREE.Vector3(width / 2, -height / 2, 0), new THREE.Vector3(width / 2, -height / 2 + lineWidth, 0), i);

        i = createSide(geometry, new THREE.Vector3(width / 2, height / 2, 0), new THREE.Vector3(width / 2, height / 2 - lineWidth, 0), new THREE.Vector3(-width / 2, height / 2, 0), new THREE.Vector3(-width / 2, height / 2 - lineWidth, 0), i);

        geometry.computeBoundingSphere();

        this.material = new THREE.MeshBasicMaterial({ color: this.color, transparent: true, opacity: this.opacity });

        var mesh = new THREE.Mesh(geometry, this.material);

        mesh.position.z = this.z;
        return mesh;
    }

    return BoxSprite;
}();