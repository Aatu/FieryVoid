window.StarField = (function() {
  function StarField(scene) {
    this.starCount = 5000;
    this.emitterContainer = null;
    this.scene = scene;
    this.lastAnimationTime = null;
    this.totalAnimationTime = 0;
    this.zoomChanged = 0;

    this.getRandom = null;

    this.create();
  }

  StarField.prototype.create = function() {
    //this.scene.background = new THREE.Color(10 / 255, 10 / 255, 30 / 255);

    this.cleanUp();

    this.emitterContainer = new ParticleEmitterContainer(
      this.scene,
      this.starCount,
      StarParticleEmitter
    );

    //this.webglScene.scene.background = new THREE.Color(10/255, 10/255, 30/255);
    var width = 3000; //this.webglScene.width * 1.5;
    var height = 2000; // this.webglScene.height * 1.5;

    this.getRandom = mathlib.getSeededRandomGenerator(gamedata.gameid);

    //var stars = Math.floor(this.starCount * (width / 4000));
    var stars = this.starCount;
    while (stars--) {
      createStar.call(this, width, height);

      if (this.getRandom() > 0.98) {
        createShiningStar.call(this, width, height);
      }
    }

    var gas = Math.floor(this.getRandom() * 5) + 8;

    /*
        while(gas--){
            createGasCloud.call(this, width, height)
        }
        */

    this.emitterContainer.start();
    this.lastAnimationTime = new Date().getTime();
    this.totalAnimationTime = 0;
    this.zoomChanged = 1;
    return this;
  };

  StarField.prototype.cleanUp = function() {
    if (this.emitterContainer) {
      this.emitterContainer.cleanUp();
      this.emitterContainer = null;
    }
  };

  StarField.prototype.render = function() {
    if (!this.emitterContainer) {
      this.create();
    }

    var deltaTime = new Date().getTime() - this.lastAnimationTime;
    this.totalAnimationTime += deltaTime;
    this.emitterContainer.render(
      0,
      this.totalAnimationTime,
      0,
      0,
      this.zoomChanged
    );

    if (this.zoomChanged === 1) {
      this.zoomChanged = 0;
    }

    this.lastAnimationTime = new Date().getTime();
  };

  function createStar(width, height) {
    var particle = this.emitterContainer.getParticle(this);

    var x = (this.getRandom() - 0.5) * width * 1.5;
    var y = (this.getRandom() - 0.5) * height * 1.5;

    particle
      .setActivationTime(0)
      .setSize(2 + this.getRandom() * 2)
      .setOpacity(this.getRandom() * 0.2 + 0.9)
      .setPosition({ x: x, y: y })
      .setColor(new THREE.Color(1, 1, 1))
      .setParallaxFactor(0.1 + this.getRandom() * 0.1);

    if (this.getRandom() > 0.9) {
      particle
        .setSineFrequency(this.getRandom() * 200 + 50)
        .setSineAmplitude(this.getRandom());
    }
  }

  function createShiningStar(width, height) {
    var particle = this.emitterContainer.getParticle(this);

    var x = (this.getRandom() - 0.5) * width * 1.5;
    var y = (this.getRandom() - 0.5) * height * 1.5;

    var size = 6 + this.getRandom() * 6;
    var parallaxFactor = 0.1 + this.getRandom() * 0.1;
    var color = new THREE.Color(
      this.getRandom() * 0.4 + 0.6,
      this.getRandom() * 0.2 + 0.8,
      this.getRandom() * 0.4 + 0.6
    );

    particle
      .setActivationTime(0)
      .setSize(size * 0.5)
      .setOpacity(this.getRandom() * 0.2 + 0.9)
      .setPosition({ x: x, y: y })
      .setColor(new THREE.Color(1, 1, 1))
      .setParallaxFactor(parallaxFactor);

    particle = this.emitterContainer.getParticle(this);
    particle
      .setActivationTime(0)
      .setSize(size)
      .setOpacity(this.getRandom() * 0.1 + 0.1)
      .setPosition({ x: x, y: y })
      .setColor(color)
      .setParallaxFactor(parallaxFactor)
      .setSineFrequency(this.getRandom() * 200 + 100)
      .setSineAmplitude(this.getRandom() * 0.4);

    var shines = Math.round(this.getRandom() * 8) - 3;

    if (shines <= 2) {
      return;
    }

    var angle = this.getRandom() * 360;
    var angleChange = (this.getRandom() - 0.5) * 0.01;

    while (shines--) {
      angle += this.getRandom() * 60 + 40;
      particle = this.emitterContainer.getParticle(this);
      particle
        .setActivationTime(0)
        .setSize(size * this.getRandom() * 10 + 10)
        .setOpacity(this.getRandom() * 0.1 + 0.1)
        .setPosition({ x: x, y: y })
        .setColor(color)
        .setParallaxFactor(parallaxFactor)
        .setSineFrequency(this.getRandom() * 200 + 100)
        .setSineAmplitude(0.1)
        .setAngle(angle)
        .setAngleChange(angleChange)
        .setTexture(particle.texture.starLine);
    }
  }

  function createGasCloud(width, height) {
    var gas = Math.floor(this.getRandom() * 10 + 10);

    var position = {
      x: (this.getRandom() - 0.5) * width,
      y: (this.getRandom() - 0.5) * height
    };

    var vector = {
      x: (getRandomBand.call(this, 0.5, 1) * width) / 100,
      y: (getRandomBand.call(this, 0.5, 1) * width) / 100
    };

    var iterations = Math.floor(this.getRandom() * 3) + 5;

    while (iterations--) {
      createGasCloudPart.call(this, { x: position.x, y: position.y }, width);
      position.x += getRandomBand.call(this, 0, 1) * 50 + vector.x;
      position.y += getRandomBand.call(this, 0, 1) * 50 + vector.y;
    }
  }

  function getRandomBand(min, max) {
    var random = this.getRandom() * (max - min) + min;
    return this.getRandom() > 0.5 ? random * -1 : random;
  }

  function createGasCloudPart(position, width) {
    var gas = Math.floor(this.getRandom() * 5 + 5);
    var baseRotation = (this.getRandom() - 0.5) * 0.002;

    while (gas--) {
      createGas.call(
        this,
        position,
        baseRotation,
        this.getRandom() * width * 0.4 + width * 0.4
      );
    }
  }

  function createGas(position, baseRotation, size) {
    var particle = this.emitterContainer.getParticle(this);

    position.x += (this.getRandom() - 0.5) * 100;
    position.y += (this.getRandom() - 0.5) * 100;

    particle
      .setActivationTime(0)
      .setSize(this.getRandom() * size * 0.5 + size * 0.5)
      .setOpacity(this.getRandom() * 0.005 + 0.005)
      .setPosition({ x: position.x, y: position.y })
      .setColor(new THREE.Color(104 / 255, 204 / 255, 249 / 255))
      .setTexture(particle.texture.gas)
      .setAngle(this.getRandom() * 360)
      .setAngleChange(baseRotation + (this.getRandom() - 0.5) * 0.001)
      .setParallaxFactor(0.1 + this.getRandom() * 0.1);

    if (this.getRandom() > 0.9) {
      particle
        .setActivationTime(0)
        .setSize(this.getRandom() * size * 0.25 + size * 0.25)
        .setOpacity(0)
        .setPosition({ x: position.x, y: position.y })
        .setColor(new THREE.Color(1, 1, 1))
        .setTexture(particle.texture.gas)
        .setAngle(this.getRandom() * 360)
        .setAngleChange(baseRotation + (this.getRandom() - 0.5) * 0.01)
        .setParallaxFactor(0.1 + this.getRandom() * 0.1)
        .setSineFrequency(this.getRandom() * 200 + 200)
        .setSineAmplitude(this.getRandom() * 0.02);
    }
  }

  return StarField;
})();
