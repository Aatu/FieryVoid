"use strict";

window.TextSprite = function () {

    const TEXTURE_SIZE = 256;

    function TextSprite(text, color, z, args) {
        if (!args) {
            args = {};
        }

        this.z = z || 0;

        const size = args.size || TEXTURE_SIZE

        this.color = color || "rgba(255,255,255,1)"

        this.fontSize = args.fontSize || '32px'
        this.font = args.font || 'Arial Black'

        var canvas = window.AbstractCanvas.create(size, size);
        var context = canvas.getContext("2d");
        context.save();
        context.fillStyle = this.color;
        context.font =  this.fontSize + " " + this.font;
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
    
        context.shadowColor = "rgba(0,0,0,1)";
    
        context.shadowBlur = 8;
        context.fillText(text, Math.round(size/2),  Math.round(size/2));
        context.restore();

        var geometry = new THREE.PlaneGeometry(size, size, 1, 1);
        
        var texture = new THREE.Texture(canvas);
        texture.needsUpdate = true;

        this.material = new THREE.MeshBasicMaterial({
            map: texture,
            transparent: true
        })

        this.mesh = new THREE.Mesh(geometry, this.material);
    }

    
    TextSprite.prototype.setScale = function (width, height) {
        this.mesh.scale.set(width, height, 1);
    };

    TextSprite.prototype.hide = function () {
        this.mesh.visible = false;
        return this;
    };

    TextSprite.prototype.show = function () {
        this.mesh.visible = true;
        return this;
    };

    TextSprite.prototype.setOpacity = function (opacity) {
        this.material.opacity = opacity;
    };

    TextSprite.prototype.setPosition = function (pos) {
        this.mesh.position.x = pos.x;
        this.mesh.position.y = pos.y;
        this.mesh.position.z = this.z;
        return this;
    };

    TextSprite.prototype.destroy = function () {
        this.mesh.material.dispose();
    };

    return TextSprite;
}();