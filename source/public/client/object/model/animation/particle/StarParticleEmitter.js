import Animation from "../Animation";
import StarParticle from "./StarParticle";

let SHADER_VERTEX = null;
let SHADER_FRAGMENT = null;

const texture = new THREE.TextureLoader().load(
  "img/effect/effectTextures1024.png"
);

const getShaders = () => {
  if (!SHADER_VERTEX)
    SHADER_VERTEX = document.getElementById("starVertexShader").innerHTML;

  if (!SHADER_FRAGMENT)
    SHADER_FRAGMENT = document.getElementById("starFragmentShader").innerHTML;

  return { vertex: SHADER_VERTEX, fragment: SHADER_FRAGMENT };
};

class StarParticleEmitter extends Animation {
  constructor(scene, particleCount, args) {
    super();

    if (!args) {
      args = {};
    }

    var blending = args.blending || THREE.AdditiveBlending;

    if (!particleCount) {
      particleCount = 1000;
    }

    this.scene = scene;

    this.free = [];
    for (var i = 0; i < particleCount; i++) {
      this.free.push(i);
    }

    this.effects = 0;

    var uniforms = {
      gameTime: { type: "f", value: 0.0 },
      texture: { type: "t", value: texture }
    };

    this.particleGeometry = new THREE.BufferGeometry();

    this.particleGeometry.addAttribute(
      "position",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount * 3),
        3
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "size",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "sizeChange",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "color",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount * 3),
        3
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "opacity",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "activationGameTime",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "textureNumber",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "angle",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "angleChange",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "parallaxFactor",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "sineFrequency",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );
    this.particleGeometry.addAttribute(
      "sineAmplitude",
      new THREE.Float32BufferAttribute(
        new Float32Array(particleCount),
        1
      ).setDynamic(true)
    );

    this.particleGeometry.dynamic = true;

    this.particleGeometry.setDrawRange(0, particleCount);

    var shaders = getShaders();

    this.particleMaterial = new THREE.ShaderMaterial({
      uniforms: uniforms,
      vertexShader: shaders.vertex,
      fragmentShader: shaders.fragment,
      transparent: true,
      blending: blending,
      depthWrite: false //Try removing this if problems with transparency
    });

    /*
        THREE.NormalBlending = 0;
        THREE.AdditiveBlending = 1;
        THREE.SubtractiveBlending = 2;
        THREE.MultiplyBlending = 3;
        THREE.AdditiveAlphaBlending = 4;
        */

    this.flyParticle = new StarParticle(
      this.particleMaterial,
      this.particleGeometry
    );

    while (particleCount--) {
      this.flyParticle.create(particleCount).setInitialValues();
    }

    this.mesh = new THREE.Points(this.particleGeometry, this.particleMaterial);
    this.mesh.frustumCulled = false;
    //this.mesh.matrixAutoUpdate = false;
    this.mesh.position.set(0, 0, args.z || -10);

    this.needsUpdate = false;

    this.scene.add(this.mesh);
  }

  start() {
    this.active = true;
  }

  stop() {
    this.active = false;
  }

  reset() {}

  cleanUp() {
    this.mesh.material.dispose();
    this.scene.remove(this.mesh);
  }

  update(gameData) {}

  render(now, total, last, delta, zoom) {
    this.particleMaterial.uniforms.gameTime.value = total;
    this.mesh.material.needsUpdate = true;
  }

  done() {
    if (this.onDoneCallback) {
      this.onDoneCallback();
    }
  }

  getParticle() {
    if (this.free.length === 0) {
      return false;
    }

    var i = this.free.pop();

    return this.flyParticle.create(i);
  }

  freeParticles(particleIndices) {
    particleIndices.forEach(function(i) {
      this.flyParticle.create(i).setInitialValues();
    }, this);
    this.free = this.free.concat(particleIndices);
  }
}

export default StarParticleEmitter;
