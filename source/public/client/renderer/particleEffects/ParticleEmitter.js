window.ParticleEmitter = (function(){
    function ParticleEmitter(particles, settings)
    {
        this.particles = particles;

        var attributes = settings.attributes || {
            customVisible:	{ type: 'f',  value: [] },
            customAngle:	{ type: 'f',  value: [] },
            customSize:		{ type: 'f',  value: [] },
            customColor:	{ type: 'c',  value: [] },
            customOpacity:	{ type: 'f',  value: [] }
        };

        var uniforms = settings.uniforms || {
            zoomLevel: { type: 'f', value: settings.zoomLevel || 1 },
            texture:   { type: 't', value: settings.texture }
        };

        this.particleGeometry = new THREE.Geometry();
        this.particleGeometry.dynamic = true;
        this.particleMaterial = new THREE.ShaderMaterial(
            {
                uniforms: uniforms,
                attributes: attributes,
                vertexShader:   settings.vertexShader || this.particleVertexShader,
                fragmentShader: settings.fragmentShader || this.particleFragmentShader,
                transparent: true,
                alphaTest: 0.5, // if having transparency issues, try including: alphaTest: 0.5,
                blending: THREE.NormalBlending, depthTest: true
            });

        this.particleMesh = null;
    }


    ParticleEmitter.prototype.observeZoomLevelChange = function(dispatcher, callback)
    {
        if ( ! callback)
            callback = function(event){return event.zoom};

        dispatcher.attach('ZoomEvent', function(event) {
            var zoomLevel = callback(event);
            this.setZoomLevel(zoomLevel)
        }.bind(this));

        return this;
    }

    ParticleEmitter.prototype.setZoomLevel = function(zoomLevel)
    {
        this.particleMaterial.uniforms.zoomLevel.value = zoomLevel;
    };

    ParticleEmitter.prototype.getFreeParticle = function()
    {
        for (var i in this.particles)
        {
            var particle = this.particles[i];
            if ( ! particle.isActive())
                return particle;
        }
        return null;
    };

    ParticleEmitter.prototype.animate = function()
    {
        for (var i = 0; i < this.particles.length; i++)
        {
            var particle = this.particles[i];
            particle.animate(0.01);
            particle.updateMaterial(this.particleMaterial, i);
        }
        this.particleGeometry.verticesNeedUpdate = true;
    };

    ParticleEmitter.prototype.getObject3d = function()
    {
        if ( ! this.particleMesh)
        {
            for (var i = 0; i < this.particles.length; i++)
            {
                var particle = this.particles[i];

                this.particleGeometry.vertices[i] = particle.position;
                particle.updateMaterial(this.particleMaterial, i);
            }

            this.particleMesh = new THREE.ParticleSystem( this.particleGeometry, this.particleMaterial );
            this.particleMesh.dynamic = true;
            this.particleMesh.sortParticles = true;
        }

        return this.particleMesh;
    };

    ParticleEmitter.prototype.particleVertexShader =
        [
            "attribute vec3  customColor;",
            "attribute float customOpacity;",
            "attribute float customSize;",
            "attribute float customAngle;",
            "attribute float customVisible;",  // float used as boolean (0 = false, 1 = true)
            "uniform float zoomLevel;",
            "varying vec4  vColor;",
            "varying float vAngle;",
            "void main()",
            "{",
            "if ( customVisible > 0.5 )", 				// true
            "vColor = vec4( customColor, customOpacity );", //     set color associated to vertex; use later in fragment shader.
            "else",							// false
            "vColor = vec4(0.0, 0.0, 0.0, 0.0);", 		//     make particle invisible.

            "vAngle = customAngle;",

            "gl_PointSize = customSize * zoomLevel;",
            "gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );",
            "}"
        ].join("\n");

    ParticleEmitter.prototype.particleFragmentShader =
        [
            "uniform sampler2D texture;",
            "varying vec4 vColor;",
            "varying float vAngle;",
            "void main()",
            "{",
            "gl_FragColor = vColor;",

            "float c = cos(vAngle);",
            "float s = sin(vAngle);",
            "vec2 rotatedUV = vec2(c * (gl_PointCoord.x - 0.5) + s * (gl_PointCoord.y - 0.5) + 0.5,",
            "c * (gl_PointCoord.y - 0.5) - s * (gl_PointCoord.x - 0.5) + 0.5);",  // rotate UV coordinates to rotate texture
            "vec4 rotatedTexture = texture2D( texture,  rotatedUV );",
            "gl_FragColor = gl_FragColor * rotatedTexture;",    // sets an otherwise white particle texture to desired color
            "}"
        ].join("\n");

    return ParticleEmitter;
})();

