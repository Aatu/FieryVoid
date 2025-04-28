"use strict";

window.BallisticLineSprite = function () {

function BallisticLineSprite(start, end, lineWidth, z, color, opacity, args) {
    if (!args) {
        args = {};
    }

    this.z = z || -3;
    this.mesh = new THREE.Object3D(); // Use Object3D to group arrows
    this.start = start;
    this.end = end;
    this.width = lineWidth;

    this.color = color;
    this.opacity = opacity;

    // Check for zero-length line
    if (start.x === end.x && start.y === end.y && start.z === end.z) {
//        console.warn("Attempted to create a BallisticLineSprite with zero-length. Skipping.");
        this.arrowCount = 0; // No arrows to create
        return;
    }

    this.arrowCount = 8; // Number of arrows in the series (adjustable)
    this.arrowSpacing = mathlib.distance(start, end) / this.arrowCount; // Space between arrows

    // Create the series of arrows
    for (let i = 0; i < this.arrowCount; i++) {
        let arrow = this.createArrow(start, end, i);
        if (arrow) {
            this.mesh.add(arrow); // Add the arrow to the group
        }
    }

    this.setLineWidth(lineWidth);
}

    // Create an individual arrow at a specific position along the line
    BallisticLineSprite.prototype.createArrow = function (start, end, index) {
        // Calculate the position of the arrow
        let position = mathlib.getPointBetween(start, end, index / this.arrowCount, true);

        // Create an ArrowHelper for the arrow
        let arrow = new THREE.ArrowHelper(
            new THREE.Vector3(end.x - start.x, end.y - start.y, 0).normalize(), // Direction vector
            new THREE.Vector3(position.x, position.y,-3), // Arrow position
            this.arrowSpacing, // Length of the arrow
            this.color, // Arrow color
            this.width*2, // Head length (adjustable for visual impact)
            this.width*1.8 // Head width (adjustable for visual impact)
        );

        //var arrowOpacity = this.opacity + 0.2;

        // Modify the arrow material's opacity
        arrow.line.material.transparent = true;
        arrow.line.material.opacity = this.opacity;
        arrow.children[1].material.transparent = true;     //Cause issues with other render if I try to make arrows transparent   
        arrow.children[1].material.opacity = this.opacity;
    
        
        return arrow;
    };

    BallisticLineSprite.prototype.setLineWidth = function (lineWidth) {
        // Optionally, you can scale the arrows here if needed
    };

    BallisticLineSprite.prototype.hide = function () {
        this.mesh.visible = false;
        return this;
    };

    BallisticLineSprite.prototype.show = function () {
        this.mesh.visible = true;
        return this;
    };

    BallisticLineSprite.prototype.setPosition = function (pos) {
        this.mesh.position.x = pos.x;
        this.mesh.position.y = pos.y;
        this.mesh.position.z = this.z;
        return this;
    };

    BallisticLineSprite.prototype.destroy = function () {
        // Dispose of each arrow's material
        this.mesh.children.forEach(arrow => {
            // Check if the arrow has a line (which is the actual 3D object rendered)
            if (arrow.line && arrow.line.material) {
                arrow.line.material.dispose(); // Dispose the material of the line
            }
        });
    };

    return BallisticLineSprite;
}();