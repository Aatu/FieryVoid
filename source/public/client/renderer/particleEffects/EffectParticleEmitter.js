window.EffectParticleEmitter = (function(){
    function EffectParticleEmitter(scene, particleCount, blending)
    {
        Animation.call(this);

        if ( ! blending )
            blending = THREE.NormalBlending;

        if (! particleCount) {
            particleCount = 100;
        }

        this.scene = scene;

        this.free = [];
        for ( var i = 0; i<particleCount; i++)
        {
            this.free.push(i);
        }

        this.effects = 0;

        var uniforms = {
            gameTime: {type: 'f', value: 0.0},
            zoomLevel: { type: 'f', value:  1.0 },
            texture:   { type: 't', value: new THREE.TextureLoader().load("img/effect/effectTextures1024.png")}
        };

        this.particleGeometry = new THREE.BufferGeometry();

        this.particleGeometry.addAttribute( 'position', new THREE.Float32BufferAttribute( new Float32Array(particleCount * 3), 3 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'size', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'sizeChange', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'color', new THREE.Float32BufferAttribute( new Float32Array(particleCount * 3), 3 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'opacity', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'fadeInTime', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'fadeInSpeed', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'fadeOutTime', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'fadeOutSpeed', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'activationGameTime', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'velocity', new THREE.Float32BufferAttribute( new Float32Array(particleCount * 3), 3 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'acceleration', new THREE.Float32BufferAttribute( new Float32Array(particleCount * 3), 3 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'textureNumber', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'angle', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );
        this.particleGeometry.addAttribute( 'angleChange', new THREE.Float32BufferAttribute( new Float32Array(particleCount), 1 ).setDynamic( true ) );

        this.particleGeometry.dynamic = true;


        this.particleGeometry.setDrawRange( 0, particleCount );

        this.particleMaterial = new THREE.ShaderMaterial(
            {
                uniforms: uniforms,
                vertexShader:   this.vertexShader,
                fragmentShader: this.fragmentShader,
                transparent: true,
                alphaTest: 0.5, // if having transparency issues, try including: alphaTest: 0.5,
                blending: blending, depthTest: true
            });

        /*
        THREE.NormalBlending = 0;
        THREE.AdditiveBlending = 1;
        THREE.SubtractiveBlending = 2;
        THREE.MultiplyBlending = 3;
        THREE.AdditiveAlphaBlending = 4;
        */

        this.flyParticle = new BaseParticle(this.particleMaterial, this.particleGeometry);

        while(particleCount--)
        {
            this.flyParticle.create(particleCount).setInitialValues();
        }

        this.mesh = new THREE.Points( this.particleGeometry, this.particleMaterial );
        this.mesh.position = new THREE.Vector3(0, 0, 10);

        this.needsUpdate = false;

        this.scene.add(this.mesh);
    }


    EffectParticleEmitter.prototype =  Object.create(Animation.prototype);

    EffectParticleEmitter.prototype.start = function () {
        this.active = true;
    };

    EffectParticleEmitter.prototype.stop = function () {
        this.active = false;
    };

    EffectParticleEmitter.prototype.reset = function () {

    };

    EffectParticleEmitter.prototype.cleanUp = function () {
        this.scene.remove(this.mesh);
    };

    EffectParticleEmitter.prototype.update = function (gameData) {

    };

    EffectParticleEmitter.prototype.render = function (now, total, last, delta) {
        this.particleMaterial.uniforms.gameTime.value = total;
        this.mesh.material.needsUpdate = true;
    };

    EffectParticleEmitter.prototype.done = function() {
        if (this.onDoneCallback) {
            this.onDoneCallback();
        }
    };

    EffectParticleEmitter.prototype.register = function()
    {
        this.effects++;
    };

    EffectParticleEmitter.prototype.unregister = function(effect)
    {
        this.freeParticles(effect.particles);
        this.effects--;
    };

    EffectParticleEmitter.prototype.getFreeParticle = function()
    {
        var i = this.free.pop();

        return this.flyParticle.create(i);
    };

    EffectParticleEmitter.prototype.freeParticles = function(particleIndices)
    {
        particleIndices.forEach(function(i){
            this.flyParticle.create(i).setInitialValues();
        }, this);
        this.free = this.free.concat(particleIndices);
    };


    EffectParticleEmitter.prototype.vertexShader =
        [
            "attribute vec3 color;",
            "attribute float opacity;",
            "attribute float fadeInTime;",
            "attribute float fadeInSpeed;",
            "attribute float fadeOutTime;",
            "attribute float fadeOutSpeed;",
            "attribute float size;",
            "attribute float sizeChange;",
            "attribute float angle;",
            "attribute float angleChange;",
            "attribute vec3 velocity;",
            "attribute vec3 acceleration;",
            "attribute float activationGameTime;",
            "attribute float textureNumber;",
            "uniform float zoomLevel;",
            "uniform float gameTime;",
            "varying vec4  vColor;",
            "varying float vAngle;",
            "varying float textureN;",
            "void main()",
            "{",

            "float currentOpacity = 0.0;",
            "if (fadeInSpeed == 0.0) currentOpacity = opacity;",

            "float elapsedTime = gameTime - activationGameTime;",


            "if (fadeInSpeed > 0.0 && gameTime > fadeInTime)",
            "{",
            "float fadeIn = (gameTime - fadeInTime) / fadeInSpeed;",
            "if (fadeIn > 1.0) fadeIn = 1.0;",
            "currentOpacity =  opacity * fadeIn;",
            "}",

            "if (fadeOutSpeed > 0.0 && gameTime > fadeOutTime)",
            "{",
            "float fadeOut = (gameTime - fadeOutTime) / fadeOutSpeed;",
            "if (fadeOut > 1.0) fadeOut = 1.0;",
            "currentOpacity =  currentOpacity *  (1.0 - fadeOut);",
            "}",

            "if ( currentOpacity > 0.0 && elapsedTime >= 0.0)", 				// true
            "{",
            "vColor = vec4( color, currentOpacity );", //     set color associated to vertex; use later in fragment shader.
            "} else {",							// false
            "vColor = vec4(0.0, 0.0, 0.0, 0.0);", 		//     make particle invisible.
            "}",

            "vAngle = angle + angleChange * elapsedTime;",
            "textureN = textureNumber;",

            "vec3 displacement = velocity * elapsedTime;",
            "vec3 accelerationDisplacement  = elapsedTime * elapsedTime * 0.5 * acceleration;",

            "vec3 modPos = position + displacement + accelerationDisplacement;",


            "gl_PointSize = clamp(size + (sizeChange * elapsedTime), 0.0, 1024.0) * zoomLevel;",
            "gl_Position = projectionMatrix * modelViewMatrix * vec4( modPos, 1.0 );",
            "}"
        ].join("\n");

//xRot = xCenter + cos(Angle) * (x - xCenter) - sin(Angle) * (y - yCenter)
//yRot = yCenter + sin(Angle) * (x - xCenter) + cos(Angle) * (y - yCenter)
    EffectParticleEmitter.prototype.fragmentShader =
        [
            "uniform sampler2D texture;",
            "varying vec4 vColor;",
            "varying float vAngle;",
            "varying float textureN;",
            "void main()",
            "{",
            "gl_FragColor = vColor;",
            "if (gl_FragColor.a == 0.0)",
            "return;",

            "float c = cos(vAngle);",
            "float s = sin(vAngle);",
            "float textureAmount = 8.0;",
            "vec2 tPos = vec2((mod(textureN, textureAmount) * (1.0 / textureAmount)), (floor(textureN / textureAmount) * (1.0 / textureAmount)));",
            //"vec2 tCen = vec2((0.5 / textureAmount) , 1.0 - (0.5 / textureAmount));",//(1.0 / textureAmount);",
            //"vec2 pos = vec2(",
            //	"gl_PointCoord.x / textureAmount,",
            //	"1.0 - (gl_PointCoord.y / textureAmount));",

            "vec2 pos = vec2(gl_PointCoord.x, gl_PointCoord.y);",
            "vec2 tCen = vec2(0.5, 0.5);",//(1.0 / textureAmount);",

            "vec2 rPos = vec2(",
            "tCen.x + c * (pos.x - tCen.x) - s * (pos.y - tCen.y),",
            "tCen.y + s * (pos.x - tCen.x) + c * (pos.y - tCen.y));",

            "rPos = clamp(rPos, 0.0, 1.0);",

            "vec2 finalPos = vec2(",
            "(rPos.x / textureAmount + tPos.x),",
            "1.0 - (rPos.y / textureAmount + tPos.y));",

            "vec4 rotatedTexture = texture2D( texture, finalPos);", //rotatedUV );",
            "gl_FragColor = gl_FragColor * rotatedTexture;",
            "}"
        ].join("\n");
    
    return EffectParticleEmitter;

})();




