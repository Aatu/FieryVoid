'use strict';

window.webglSprite = function () {

    let loadedTextures = {};
    
    var SHADER_VERTEX = null;
    var SHADER_FRAGMENT = null;

    function Sprite(image, size, z) {
        this.z = z || 0;
        this.mesh = null;
        this.size = size;
        this.uniforms = {
            texture: { type: 't', value: new THREE.DataTexture(null, 0, 0) },
            overlayAlpha: { type: 'f', value: 0.0 },
            overlayColor: { type: 'v3', value: new THREE.Color(0, 0, 0) },
            opacity: { type: 'f', value: 1.0
                //opacity:		{ type: 'f',	value: 1.0},
                //tileDimensions: { type: 'v2',	value: new THREE.Vector2(1, 1)},
                //damageLookup:	{ type: 't',	value: new THREE.DataTexture(null, 0, 0)},
                //damageLookup2:	{ type: 't',	value: new THREE.DataTexture(null, 0, 0)},
                //damageBrushes:  { type: 't', 	value: THREE.ImageUtils.loadTexture("/misc/damageBrushes.png")},
                //damageNormalMap:{ type: 't', 	value: THREE.ImageUtils.loadTexture("/misc/damageBrushes-normal.png")},
                //normalMap:		{ type: 't', 	value: new THREE.DataTexture(null, 0, 0)},
                //worldPosition:	{ type: 'v3',	value: new THREE.Vector3(0, 0, 0)},
                //scale:			{ type: 'v2',	value: new THREE.Vector2(1, 1)},
                //flatLight:		{ type: 'f',	value: 1.0}
            } };

        this.mesh = create.call(this, size, image);
    }

    Sprite.prototype.hide = function () {
        this.mesh.visible = false;
        return this;
    };

    Sprite.prototype.show = function () {
        this.mesh.visible = true;
        return this;
    };

    Sprite.prototype.setPosition = function (pos) {
        this.mesh.position.x = pos.x;
        this.mesh.position.y = pos.y;
        return this;
    };

    Sprite.prototype.setOpacity = function (opacity) {
        this.uniforms.opacity.value = opacity;
    };

    Sprite.prototype.setOverlayColorAlpha = function (alpha) {
        this.uniforms.overlayAlpha.value = alpha;
    };

    Sprite.prototype.setOverlayColor = function (color) {
        this.uniforms.overlayColor.value = color;
    };

    Sprite.prototype.setScale = function (width, height) {
        this.mesh.scale.set(width, height, 1);
    };

    Sprite.prototype.destroy = function () {
        this.mesh.material.dispose();
    };

    Sprite.prototype.setFacing = function (facing) {
        this.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };

    function getShaders() {
        if (!SHADER_VERTEX) SHADER_VERTEX = document.getElementById('spriteVertexShader').innerHTML;

        if (!SHADER_FRAGMENT) SHADER_FRAGMENT = document.getElementById('spriteFragmentShader').innerHTML;

        return { vertex: SHADER_VERTEX, fragment: SHADER_FRAGMENT };
    }

    function create(size, image) {
        var geometry = new THREE.PlaneGeometry(size.width, size.height, 1, 1);

        //var attributes = {};

        if (typeof image == "string") {
            var tex = loadedTextures[image] || new THREE.TextureLoader().load(image);

            if (!loadedTextures[image]) {
                loadedTextures[image] = tex;
            } 
            
            //tex.magFilter = THREE.NearestFilter;
            tex.minFilter = THREE.LinearMipMapNearestFilter; //THREE.NearestFilter;

            //THREE.NearestFilter, THREE.NearestMipMapNearestFilter, THREE.NearestMipMapLinearFilter, THREE.LinearFilter, and THREE.LinearMipMapNearestFilter

            this.uniforms.texture.value = tex;
        }

        var shaders = getShaders();

        this.material = new THREE.ShaderMaterial({
            uniforms: this.uniforms,
            //attributes: attributes,
            vertexShader: shaders.vertex,
            fragmentShader: shaders.fragment,
            transparent: true
        });

        this.material.depthTest = true;

        var mesh = new THREE.Mesh(geometry, this.material);

        mesh.position.z = this.z;
        return mesh;
    }

    return Sprite;
}();