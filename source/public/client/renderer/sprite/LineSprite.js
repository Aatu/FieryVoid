"use strict";

window.LineSprite = function () {

    function LineSprite(start, end, lineWidth, color, opacity, args) {
        if (!args) {
            args = {};
        }

        this.mesh = null;
        this.start = start;
        this.end = end;
        this.lineWidth = lineWidth || 10;

        this.color = color;
        this.opacity = opacity;

        this.material = new THREE.MeshBasicMaterial({
            color: this.color,
            transparent: true,
            opacity: this.opacity,
            map: args.texture || null,
            blending: args.blending || null,
            depthWrite: false
        });

        this.geometry = createGeometry(start, end, lineWidth);
        this.mesh = new THREE.Mesh(this.geometry, this.material);
    }

    const createGeometry = (start, end, lineWidth) => {
        const geometry = new THREE.Geometry();

        const lineAngle = mathlib.getCompassHeadingOfPoint({x: start.x, y: start.z}, {x: end.x, y: end.z})
        const startA = offsetPoint(start, lineAngle, -90, lineWidth) 
        const startB = offsetPoint(start, lineAngle, 90, lineWidth) 
        const endA = offsetPoint(end, lineAngle, -90, lineWidth)
        const endB = offsetPoint(end, lineAngle, 90, lineWidth)

        geometry.vertices.push(
            new THREE.Vector3( startA.x, startA.y, startA.z ),
            new THREE.Vector3( startB.x, startB.y, startB.z  ),
            new THREE.Vector3( endA.x, endA.y, endA.z ),
            new THREE.Vector3( endB.x, endB.y, endB.z )
        );

        geometry.faces.push( new THREE.Face3( 0, 1, 2, ) );
        geometry.faces.push( new THREE.Face3( 3, 2, 1 ) );
        
        geometry.faceVertexUvs[ 0 ].push( [
            new THREE.Vector2( 0, 0 ),
            new THREE.Vector2( 0, 1 ),
            new THREE.Vector2( 1, 0 ),
        ]);

        geometry.faceVertexUvs[ 0 ].push( [
            new THREE.Vector2( 1, 1 ),
            new THREE.Vector2( 1, 0 ),
            new THREE.Vector2( 0, 1 ),
        ]);

        geometry.computeBoundingSphere();
        geometry.dynamic = true;

        return geometry;
    }

    const offsetPoint = (point, lineAngle, angle, lineWidth) => {
        const offset = mathlib.getPointInDirection(lineWidth / 2, mathlib.addToDirection(lineAngle, angle), point.x, point.z, true)
        return {
            x: offset.x,
            y: point.y,
            z: offset.y
        }
    }

    LineSprite.prototype.update = function (start, end, lineWidth) {
        if (! lineWidth) {
            lineWidth = this.lineWidth;
        }
        this.lineWidth = lineWidth;
        
        const lineAngle = mathlib.getCompassHeadingOfPoint({x: start.x, y: start.z}, {x: end.x, y: end.z})
        const startA = offsetPoint(start, lineAngle, 90, lineWidth) 
        const startB = offsetPoint(start, lineAngle, -90, lineWidth) 
        const endA = offsetPoint(end, lineAngle, 90, lineWidth)
        const endB = offsetPoint(end, lineAngle, -90, lineWidth)

        this.geometry.vertices[0] = new THREE.Vector3( startA.x, startA.y, startA.z );
        this.geometry.vertices[1] = new THREE.Vector3( startB.x, startB.y, startB.z );
        this.geometry.vertices[2] = new THREE.Vector3( endA.x, endA.y, endA.z );
        this.geometry.vertices[3] = new THREE.Vector3( endB.x, endB.y, endB.z );
    };

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