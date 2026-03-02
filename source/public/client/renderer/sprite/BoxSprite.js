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

    function createSide(positions, indices, p1, p2, p3, p4, i) {
        positions.push(p1.x, p1.y, p1.z);
        positions.push(p2.x, p2.y, p2.z);
        positions.push(p3.x, p3.y, p3.z);
        positions.push(p4.x, p4.y, p4.z);

        var a = i * 4;

        indices.push(a + 2, a + 1, a);
        indices.push(a + 2, a + 3, a + 1);

        return ++i;
    }

    function create(size, lineWidth) {
        var width = size.width;
        var height = size.height;

        var geometry = new THREE.BufferGeometry();
        var positions = [];
        var indices = [];
        var i = 0;

        i = createSide(positions, indices, new THREE.Vector3(-width / 2, height / 2, 0), new THREE.Vector3(-width / 2 + lineWidth, height / 2, 0), new THREE.Vector3(-width / 2, -height / 2, 0), new THREE.Vector3(-width / 2 + lineWidth, -height / 2, 0), i);

        i = createSide(positions, indices, new THREE.Vector3(width / 2, -height / 2, 0), new THREE.Vector3(width / 2 - lineWidth, -height / 2, 0), new THREE.Vector3(width / 2, height / 2, 0), new THREE.Vector3(width / 2 - lineWidth, height / 2, 0), i);

        i = createSide(positions, indices, new THREE.Vector3(-width / 2, -height / 2, 0), new THREE.Vector3(-width / 2, -height / 2 + lineWidth, 0), new THREE.Vector3(width / 2, -height / 2, 0), new THREE.Vector3(width / 2, -height / 2 + lineWidth, 0), i);

        i = createSide(positions, indices, new THREE.Vector3(width / 2, height / 2, 0), new THREE.Vector3(width / 2, height / 2 - lineWidth, 0), new THREE.Vector3(-width / 2, height / 2, 0), new THREE.Vector3(-width / 2, height / 2 - lineWidth, 0), i);

        geometry.setAttribute('position', new THREE.BufferAttribute(new Float32Array(positions), 3));
        geometry.setIndex(indices);
        geometry.computeBoundingSphere();

        this.material = new THREE.MeshBasicMaterial({ color: this.color, transparent: true, opacity: this.opacity });

        var mesh = new THREE.Mesh(geometry, this.material);

        mesh.position.z = this.z;
        return mesh;
    }

    return BoxSprite;
}();