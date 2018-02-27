window.LineSprite = (function(){

    function LineSprite(start, end, lineWidth, z, color, opacity, args)
    {
        if (! args) {
           args = {}; 
        }
        
        this.z = z || 0;
        this.mesh = null;
        this.start = start;
        this.end = end;

        this.color = color;
        this.opacity = opacity;

        this.geometry = new THREE.PlaneGeometry( 1, 1, 1, 1 );
        this.material = new THREE.MeshBasicMaterial(
            {
                color: this.color,
                transparent: true,
                opacity: this.opacity,
                map: args.texture || null,
                blending: args.blending || null
            }
        );

        this.mesh = new THREE.Mesh(
            this.geometry,
            this.material
        );

        this.setStartAndEnd(start, end);

        this.setLineWidth(lineWidth);

    }

    LineSprite.prototype.setStartAndEnd = function (start, end) {

        var width = mathlib.distance(start, end);
        var position = mathlib.getPointBetween(start, end, 0.5, true);
        this.setPosition(position);
        this.mesh.scale.x = width;
        this.mesh.rotation.z = -mathlib.degreeToRadian(mathlib.getCompassHeadingOfPoint(start, end));

    };

    LineSprite.prototype.multiplyOpacity = function(m) {
        this.material.opacity = this.opacity * m;
    };

    LineSprite.prototype.setLineWidth = function (lineWidth) {
        this.mesh.scale.y = lineWidth;
    };

    LineSprite.prototype.hide = function()
    {
        this.mesh.visible = false;
        return this;
    };

    LineSprite.prototype.show = function()
    {
        this.mesh.visible = true;
        return this;
    };

    LineSprite.prototype.setPosition = function(pos)
    {
        this.mesh.position.x = pos.x;
        this.mesh.position.y = pos.y;
        return this;
    };

    LineSprite.prototype.destroy = function() {
        this.mesh.material.dispose();
    };

    LineSprite.prototype.setFacing = function(facing) {
        this.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };


    return LineSprite;
})();
