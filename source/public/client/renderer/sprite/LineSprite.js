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

        this.mesh = create.call(this, start, end, lineWidth, args);
    }

    LineSprite.prototype = Object.create(webglSprite.prototype);

    LineSprite.prototype.setStartAndEnd = function (start, end) {
        var width = mathlib.distance(start, end);
        var position = mathlib.getPointBetween(start, end, 0.5);
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
        this.mesh.scale.x = width;
        this.mesh.rotation.z = -mathlib.degreeToRadian(mathlib.getCompassHeadingOfPoint(start, end));
    };

    LineSprite.prototype.multiplyOpacity = function(m) {
        this.material.opacity = this.opacity * m;
    };

    LineSprite.prototype.setLineWidth = function (lineWidth) {
        this.mesh.scale.y = lineWidth;
    };

    function createSide(geometry, p1, p2, p3, p4) {
        geometry.vertices.push(
            p1, p2, p3, p4
        );


        geometry.faces.push( new THREE.Face3( 2, 1 , 0) );
        geometry.faces.push( new THREE.Face3( 2, 3 , 1) );
    }

    function create(start, end, lineWidth, args)
    {
        var width = mathlib.distance(start, end);

        var geometry = new THREE.PlaneGeometry( 1, 1, 1, 1 );
        this.material = new THREE.MeshBasicMaterial(
            {
                color: this.color,
                transparent: true,
                opacity: this.opacity,
                map: args.texture || undefined,
                blending: args.blending || undefined
            }
        );

        var mesh = new THREE.Mesh(
            geometry,
            this.material
        );

        mesh.rotation.z = -mathlib.degreeToRadian(mathlib.getCompassHeadingOfPoint(start, end));
        mesh.position.z = this.z;
        var position = mathlib.getPointBetween(start, end, 0.5, true);
        mesh.position.x = position.x;
        mesh.position.y = position.y;
        mesh.scale.x = width;
        mesh.scale.y = lineWidth;

        return mesh;
    }

    return LineSprite;
})();
