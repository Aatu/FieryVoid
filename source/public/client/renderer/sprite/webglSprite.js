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

    /* DESTROYED-SECTION STAMPS (see setDamageStamps). MAX_DAMAGE_STAMPS must match the shader's
       #define in shaders.php. The two textures are the original Fiery Void damage art, which the old
       canvas damageDrawer.js (removed in f05215cbc) stamped onto hull images: damagemask.png is the
       ragged hole, damage.png the scorch round it - both pure black, all the information in alpha.
       Loaded plain PNG (appendVersion, not getSmartImagePath): there is no fallback here if a WebP
       twin were missing, and the pair is only 40KB. */
    const MAX_DAMAGE_STAMPS = 8;
    let damageTextures = null;

    function getDamageTextures() {
        if (damageTextures) return damageTextures;
        const loader = new THREE.TextureLoader();
        const load = path => {
            const texture = loader.load(window.AssetManager.appendVersion(path), () => {
                if (window.webglScene && window.webglScene.requestRender) window.webglScene.requestRender();
            });
            //no mipmaps: the shader samples these inside a loop, where derivatives are undefined
            texture.generateMipmaps = false;
            texture.minFilter = THREE.LinearFilter;
            texture.magFilter = THREE.LinearFilter;
            return texture;
        };
        damageTextures = { mask: load('./img/damagemask.png'), scorch: load('./img/damage.png') };
        return damageTextures;
    }

    function Sprite(image, size, z) {
        this.z = z || 0;
        this.mesh = null;
        this.size = size;
        this.texturePromise = null; //set by create() for an image path - see whenTextureReady
        this.damageMaterial = null; //built on the first setDamageStamps with anything to stamp
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
        if (this.damageMaterial && this.damageMaterial !== this.mesh.material) this.damageMaterial.dispose();
    };

    /* Calls back with the loaded THREE texture - at once-ish if it is already in, otherwise when it
       lands. Never for a sprite built from anything but an image path, and never if the load failed. */
    Sprite.prototype.whenTextureReady = function (callback) {
        if (this.texturePromise) this.texturePromise.then(callback, function () { });
    };

    /* Cut destroyed sections out of the hull art (ShipIcon.setLostSections decides where).
       stamps: [{ u, v, m00, m01, m10, m11 }] - a UV centre and the matrix taking a UV offset from it
       into the stamp's -1..1 square. Empty or null puts the sprite back on the shared undamaged
       material, which matters in a replay that is rewound past the shot that took the section.

       The damaged material is a SEPARATE program (the SECTION_DAMAGE define), not a uniform branch in
       the common one: only a hull that has actually lost a section ever compiles or runs the stamp
       loop, so the hundreds of other sprites - arrows, markers, every intact ship - are untouched.
       It shares this.uniforms with the base material, so opacity and the zoom overlay keep working
       whichever of the two is on the mesh. */
    Sprite.prototype.setDamageStamps = function (stamps, scorchStrength) {
        if (!stamps || stamps.length === 0) {
            if (this.mesh.material !== this.material) this.mesh.material = this.material;
            return;
        }

        if (!this.damageMaterial) {
            const textures = getDamageTextures();
            this.uniforms.damageMask = { value: textures.mask };
            this.uniforms.damageScorch = { value: textures.scorch };
            //Flat arrays, four floats per vec4: THREE uploads a numeric array for a vec4[] as it stands,
            //and Vector4 is not in the tree-shaken shim (client/lib/three-global-shim.src.js).
            this.uniforms.damageStampA = { value: new Float32Array(MAX_DAMAGE_STAMPS * 4) };
            this.uniforms.damageStampB = { value: new Float32Array(MAX_DAMAGE_STAMPS * 4) };
            this.uniforms.damageStampCount = { value: 0 };
            this.uniforms.damageScorchStrength = { value: 0 };

            this.damageMaterial = baseMaterial.clone();
            this.damageMaterial.defines = { SECTION_DAMAGE: '' };
            this.damageMaterial.uniforms = this.uniforms;
            this.damageMaterial.depthTest = true;
        }

        const count = Math.min(stamps.length, MAX_DAMAGE_STAMPS);
        const a = this.uniforms.damageStampA.value;
        const b = this.uniforms.damageStampB.value;
        for (let i = 0; i < count; i++) {
            const s = stamps[i];
            a.set([s.u, s.v, s.m00, s.m01], i * 4);
            b.set([s.m10, s.m11, 0, 0], i * 4);
        }
        this.uniforms.damageStampCount.value = count;
        this.uniforms.damageScorchStrength.value = scorchStrength;

        if (this.mesh.material !== this.damageMaterial) this.mesh.material = this.damageMaterial;
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
                    // Defer the kickoff out of the synchronous icon-build path.
                    // Calling processQueue() inline here fires imageBitmapLoader.load()
                    // for the first MAX_CONCURRENT unique images *during* create(),
                    // which blocked first paint (~13ms locally / ~75ms at 132 ships
                    // per ?perf profiling — it was ~45% of all create() time).
                    // A macrotask lets the whole icon-build loop finish first; the
                    // task is already queued, so loads still start a tick later via
                    // the shared window.textureQueue. processQueue drains that shared
                    // queue, so a single deferred pump picks up every ship's task
                    // enqueued in the same synchronous burst.
                    setTimeout(processQueue, 0);
                });
            }

            this.texturePromise = loadedTextures[image];

            loadedTextures[image].then(texture => {
                setTimeout(() => {
                    this.uniforms.spriteTexture.value = texture;
                    // Textures resolve asynchronously, long after the render-loop
                    // budget from page load may have drained. Without a kick the
                    // newly-textured sprite wouldn't paint until the next input
                    // (idle render-loop gating in webglScene). Safe-guarded: the
                    // scene may not exist yet for sprites built before init.
                    if (window.webglScene && window.webglScene.requestRender) {
                        window.webglScene.requestRender();
                    }
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