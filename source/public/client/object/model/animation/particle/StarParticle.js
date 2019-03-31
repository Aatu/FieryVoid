const changeAttribute = (geometry, index, key, values) => {
  values = [].concat(values);

  var target = geometry.attributes[key].array;

  values.forEach(function(value, i) {
    target[index * values.length + i] = value;
  });

  geometry.attributes[key].needsUpdate = true;
};

class StarParticle {
  constructor(material, geometry) {
    this.material = material;
    this.geometry = geometry;
    this.index = 0;

    this.texture = {
      gas: 0,
      bolt: 1,
      glow: 2,
      ring: 3,
      starLine: 4
    };
  }

  create(index) {
    this.index = index;
    return this;
  }

  setInitialValues() {
    this.setPosition({ x: 0, y: 0 });
    this.setColor(new THREE.Color(0, 0, 0));
    this.setOpacity(0.0);
    this.setSize(0.0);
    this.setSizeChange(0.0);
    this.setAngle(0.0);
    this.setAngleChange(0.0);
    this.setActivationTime(0.0);
    this.setTexture(this.texture.glow);
    this.setParallaxFactor(0.0);
    this.setSineFrequency(0.0);
    this.setSineAmplitude(1);

    return this;
  }

  setTexture(tex) {
    changeAttribute(this.geometry, this.index, "textureNumber", tex);

    return this;
  }

  setParallaxFactor(parallaxFactor) {
    changeAttribute(
      this.geometry,
      this.index,
      "parallaxFactor",
      -1.0 + parallaxFactor
    );
    return this;
  }

  setSineFrequency(sineFrequency) {
    changeAttribute(this.geometry, this.index, "sineFrequency", sineFrequency);
    return this;
  }

  setSineAmplitude(sineAmplitude) {
    changeAttribute(this.geometry, this.index, "sineAmplitude", sineAmplitude);
    return this;
  }

  setSize(size) {
    changeAttribute(this.geometry, this.index, "size", size);
    return this;
  }

  setSizeChange(size) {
    changeAttribute(this.geometry, this.index, "sizeChange", size);
    return this;
  }

  setColor(color) {
    changeAttribute(this.geometry, this.index, "color", [
      color.r,
      color.g,
      color.b
    ]);
    return this;
  }

  setOpacity(opacity) {
    changeAttribute(this.geometry, this.index, "opacity", opacity);
    return this;
  }

  setPosition(pos) {
    changeAttribute(
      this.geometry,
      this.index,
      "position",
      [pos.x, pos.y, pos.z || 0],
      true
    );
    return this;
  }

  setAngle(angle) {
    changeAttribute(
      this.geometry,
      this.index,
      "angle",
      mathlib.degreeToRadian(angle)
    );
    return this;
  }

  setAngleChange(angle) {
    changeAttribute(
      this.geometry,
      this.index,
      "angleChange",
      mathlib.degreeToRadian(angle)
    );
    return this;
  }

  deactivate() {
    this.setInitialValues();
    return this;
  }

  setActivationTime(gameTime) {
    changeAttribute(this.geometry, this.index, "activationGameTime", gameTime);
    return this;
  }
}

export default StarParticle;
