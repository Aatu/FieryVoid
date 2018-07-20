'use strict';

window.webglSprite = function () {

    const imageBitmapLoader = new THREE.ImageBitmapLoader();
    const geometries = {};
    let loadedTextures = {};

    const baseMaterial = new THREE.ShaderMaterial({
        vertexShader: document.getElementById('spriteVertexShader').innerHTML,
        fragmentShader: document.getElementById('spriteFragmentShader').innerHTML,
        transparent: true,
        depthWrite: false
    });

    function Sprite(image, size, z) {
        this.z = z || 0;
        this.mesh = null;
        this.size = size;
        this.uniforms = {
            texture: { type: 't', value: new THREE.DataTexture(null, 0, 0) },
            overlayAlpha: { type: 'f', value: 0.0 },
            overlayColor: { type: 'v3', value: new THREE.Color(0, 0, 0) },
            opacity: { type: 'f', value: 1.0
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

    function create(size, image) {
        const geometry = geometries['' + size.width + '-' + size.height] || (function () {
            const geometry = new THREE.PlaneGeometry(size.width, size.height, 1, 1);
            geometries['' + size.width + '-' + size.height] = geometry;
            return geometry;
        }());

        //var attributes = {};

        if (typeof image == "string") {
            if (!loadedTextures[image]) {
                loadedTextures[image] = new Promise((resolve, reject) => {
                    imageBitmapLoader.load(
                        image,
                        imageBitmap => {
                            setTimeout(() => {
                                const texture = new THREE.CanvasTexture(imageBitmap);
                                texture.minFilter = THREE.LinearMipMapNearestFilter; //THREE.NearestFilter;
                                //tex.magFilter = THREE.NearestFilter;
                                //THREE.NearestFilter, THREE.NearestMipMapNearestFilter, THREE.NearestMipMapLinearFilter, THREE.LinearFilter, and THREE.LinearMipMapNearestFilter

                                resolve(texture);
                            }, 0);
                        },
                        undefined,
                        reject
                    );
                })
            }
            loadedTextures[image].then(texture => {
                setTimeout(() => {
                    this.uniforms.texture.value = texture;
                }, 0);
            });
        }

        this.material = baseMaterial.clone();
        this.material.uniforms = this.uniforms;

        this.material.depthTest = true;

        var mesh = new THREE.Mesh(geometry, this.material);

        mesh.position.z = this.z;
        return mesh;
    }

    return Sprite;
}();