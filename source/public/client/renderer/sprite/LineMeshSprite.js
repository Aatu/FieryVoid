"use strict";

window.LineMeshSprite = function () {

    function LineMeshSprite(start, end, lineWidth, z, color, opacity) {
        this.z = z || 0;
        this.mesh = null;
        this.start = start;
        this.end = end;

        this.color = color;
        this.opacity = opacity;

        this.mesh = create.call(this, start, end, lineWidth);
    }

    LineMeshSprite.prototype = Object.create(webglSprite.prototype);

    LineMeshSprite.prototype.setStartAndEnd = function (start, end) {
        var width = mathlib.distance(start, end);
        this.mesh.scale.x = width;
        //this.setPosition(mathlib.getPointBetween(start, end, 0.5));
        //this.setFacing(mathlib.getCompassHeadingOfPoint(start, end));
    };

    LineMeshSprite.prototype.setLineWidth = function (lineWidth) {
        console.log("update lineWidth from", this.mesh.material.uniforms.lineWidth, "to", lineWidth);
        this.mesh.material.uniforms.lineWidth = { type: "f", value: lineWidth };
        //this.mesh.material.lineWidth = lineWidth;
        this.mesh.material.needsUpdate = true;
    };

    function create(start, end, lineWidth) {
        console.log("lineWidth", lineWidth);
        var geometry = new THREE.Geometry();
        geometry.vertices.push(new THREE.Vector3(start.x, start.y, 0));
        geometry.vertices.push(new THREE.Vector3(end.x, end.y, 0));
        var line = new MeshLine();
        line.setGeometry(geometry);

        var material = new MeshLineMaterial({
            color: this.color,
            transparent: true,
            opacity: this.opacity,
            lineWidth: 1,
            sizeAttenuation: true
        });

        return new THREE.Mesh(line.geometry, material); // this syntax could definitely be improved!
    }

    return LineMeshSprite;
}();