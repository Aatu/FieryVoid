'use strict';

window.webglSprite = function () {

    const imageBitmapLoader = new THREE.ImageBitmapLoader();
    const geometries = {};
    let loadedTextures = {};

    const baseMaterial = new THREE.ShaderMaterial({
        vertexShader: document.getElementById('spriteVertexShader').innerHTML,
        fragmentShader: document.getElementById('spriteFragmentShader').innerHTML,
        transparent: true
    });

    function Sprite(image, size, z) {
        this.z = z || 0;
        this.mesh = null;
        this.size = size;
        this.uniforms = {
            spriteTexture: { type: 't', value: new THREE.DataTexture(null, 0, 0) },
            overlayAlpha: { type: 'f', value: 0.0 },
            overlayColor: { type: 'v3', value: new THREE.Color(0, 0, 0) },
            opacity: {
                type: 'f', value: 1.0
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
            }
        };

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

                    // Simple global queue system
                    window.textureQueue = window.textureQueue || [];
                    window.activeTextureLoads = window.activeTextureLoads || 0;
                    const MAX_CONCURRENT = (window.Config && window.Config.MAX_CONCURRENT_IMAGES) ? window.Config.MAX_CONCURRENT_IMAGES : 10;

                    const processQueue = () => {
                        while (window.activeTextureLoads < MAX_CONCURRENT && window.textureQueue.length > 0) {
                            window.activeTextureLoads++;
                            const task = window.textureQueue.shift();
                            task();
                        }
                    };

                    const loadTask = () => {
                        const smartPath = window.AssetManager.getSmartImagePath(image);

                        const onImageSuccess = imageBitmap => {
                            setTimeout(() => {
                                const cleanCanvas = document.createElement('canvas');
                                cleanCanvas.width = imageBitmap.width;
                                cleanCanvas.height = imageBitmap.height;
                                const cx = cleanCanvas.getContext('2d', { willReadFrequently: true });
                                cx.drawImage(imageBitmap, 0, 0);
                                const imgData = cx.getImageData(0, 0, cleanCanvas.width, cleanCanvas.height);
                                const px = imgData.data;
                                for (let i = 0; i < px.length; i += 4) {
                                    if (px[i + 3] < 77) {
                                        px[i] = px[i + 1] = px[i + 2] = 0;
                                    }
                                }
                                cx.putImageData(imgData, 0, 0);
                                const texture = new THREE.CanvasTexture(cleanCanvas);
                                texture.colorSpace = THREE.SRGBColorSpace;
                                texture.generateMipmaps = true;
                                texture.minFilter = THREE.LinearMipmapLinearFilter;
                                texture.magFilter = THREE.LinearFilter;
                                resolve(texture);
                                window.activeTextureLoads--;
                                processQueue();
                            }, 0);
                        };

                        imageBitmapLoader.load(
                            smartPath,
                            onImageSuccess,
                            undefined,
                            (err) => {
                                if (smartPath !== image) {
                                    console.warn("WebP failed, falling back to PNG:", smartPath);
                                    imageBitmapLoader.load(
                                        image,
                                        onImageSuccess,
                                        undefined,
                                        (errFallback) => {
                                            console.error("Critical texture failure:", image, errFallback);
                                            reject(errFallback);
                                            window.activeTextureLoads--;
                                            processQueue();
                                        }
                                    );
                                } else {
                                    console.error("Error loading texture:", image, err);
                                    reject(err);
                                    window.activeTextureLoads--;
                                    processQueue();
                                }
                            }
                        );
                    };

                    window.textureQueue.push(loadTask);
                    processQueue();
                });
            }

            loadedTextures[image].then(texture => {
                setTimeout(() => {
                    this.uniforms.spriteTexture.value = texture;
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