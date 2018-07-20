"use strict";

window.LineSprite = function () {

    function LineSprite(start, end, lineWidth, color, opacity, args) {
        if (!args) {
            args = {};
        }

        this.mesh = null;
        this.start = start;
        this.end = end;

        this.color = color;
        this.opacity = opacity;

        this.material = new THREE.MeshBasicMaterial({
            color: this.color,
            transparent: true,
            opacity: this.opacity,
            map: args.texture || null,
            blending: args.blending || null
        });

        this.geometry = new THREE.Geometry();


        this.geometry.vertices.push(
            new THREE.Vector3( -100,  100, 0 ),
            new THREE.Vector3( -100, -100, 0 ),
            new THREE.Vector3(  100, -100, 0 )
        );

        this.geometry.faces.push( new THREE.Face3( 0, 1, 2 ) );

        this.geometry.computeBoundingSphere();

        this.mesh = new THREE.Mesh(this.geometry, this.material);

    }

    LineSprite.prototype.multiplyOpacity = function (m) {
        this.material.opacity = this.opacity * m;
    };

    LineSprite.prototype.setLineWidth = function (lineWidth) {
        this.mesh.scale.y = lineWidth;
    };

    LineSprite.prototype.hide = function () {
        this.mesh.visible = false;
        return this;
    };

    LineSprite.prototype.show = function () {
        this.mesh.visible = true;
        return this;
    };

    LineSprite.prototype.setPosition = function (pos) {
        this.mesh.position.x = pos.x;
        this.mesh.position.y = pos.y;
        this.mesh.position.z = pos.z;
        return this;
    };

    LineSprite.prototype.destroy = function () {
        this.mesh.material.dispose();
    };

    LineSprite.prototype.setFacing = function (facing) {
        this.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };

    return LineSprite;
}();