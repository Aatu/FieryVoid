"use strict";

window.DeploymentIcon = function () {

    function DeploymentIcon(position, size, type, scene, avail, holes) {
        this.z = -2;
        this.mesh = null;
        this.size = size;
        this.color = getColorByType(type);
        this.opacity = 0.5;        
        if (type == "terrain" || type == "mine") {
            this.opacity = 0.2;
        }

        this.mesh = new THREE.Object3D();
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
        this.mesh.renderDepth = 10;

        var lineWidth = 10;

        if (holes && holes.length > 0) {
            // Use Stencil Buffer to flawlessly cut holes in the mesh (ignores Earcut topology limits)
            var stencilGroup = new THREE.Group();
            this.mesh.add(stencilGroup);

            holes.forEach(function(hole) {
                var hw = hole.size.width;
                var hh = hole.size.height;
                var hx = hole.position.x - position.x;
                var hy = hole.position.y - position.y;
                
                // 1. Stencil write mask
                var holeGeom = new THREE.PlaneGeometry(hw, hh);
                var holeMat = new THREE.MeshBasicMaterial({
                    colorWrite: false,
                    depthWrite: false,
                    stencilWrite: true,
                    stencilRef: 1,
                    stencilFunc: THREE.AlwaysStencilFunc,
                    stencilZPass: THREE.ReplaceStencilOp
                });
                var holeMesh = new THREE.Mesh(holeGeom, holeMat);
                holeMesh.position.set(hx, hy, 0);
                holeMesh.renderOrder = -1; // Render hole masks before the map
                stencilGroup.add(holeMesh);
            }.bind(this));

            // 3. Draw the main map, but only where stencil == 0 (outside the holes)
            var plainGeom = new THREE.PlaneGeometry(size.width + lineWidth, size.height + lineWidth);
            var plainMat = new THREE.MeshBasicMaterial({ 
                color: this.color, 
                transparent: true, 
                opacity: this.opacity * 0.5,
                stencilWrite: true,
                stencilRef: 0,
                stencilFunc: THREE.EqualStencilFunc
            });
            var plainMesh = new THREE.Mesh(plainGeom, plainMat);
            plainMesh.position.z = this.z;
            plainMesh.renderOrder = 0;
            this.mesh.add(plainMesh);
        } else {
            var borders = new window.BoxSprite(size, lineWidth, this.z, this.color, this.opacity);
            this.mesh.add(borders.mesh);

            var plain = new window.PlainSprite({ width: size.width + lineWidth, height: size.height + lineWidth }, this.z, this.color, this.opacity * 0.5, avail);
            this.mesh.add(plain.mesh);
        }

        scene.add(this.mesh);
        this.hide();
    }

    DeploymentIcon.prototype.hide = function () {
        this.mesh.visible = false;
    };

    DeploymentIcon.prototype.show = function () {
        this.mesh.visible = true;
    };

    function getColorByType(type) {
        if (type == "own") {
            return new THREE.Color(160 / 255, 250 / 255, 100 / 255).convertSRGBToLinear();
        } else if (type == "ally") {
            return new THREE.Color(100 / 255, 170 / 255, 250 / 255).convertSRGBToLinear();
        } else if (type == "terrain") {
            return new THREE.Color(255 / 255, 255 / 255, 255 / 255).convertSRGBToLinear();
        } else if (type == "mine") {
            return new THREE.Color(255 / 255, 165 / 255, 0).convertSRGBToLinear(); // Orange
        } else {
            return new THREE.Color(250 / 255, 100 / 255, 100 / 255).convertSRGBToLinear();
        }
    }    

    return DeploymentIcon;
}();