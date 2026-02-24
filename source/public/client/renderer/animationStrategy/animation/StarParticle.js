'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

window.StarParticle = function () {
    function StarParticle(material, geometry) {
        this.material = material;
        this.geometry = geometry;
        this.index = 0;
    }

    StarParticle.prototype.texture = {
        gas: 0,
        bolt: 1,
        glow: 2,
        ring: 3,
        starLine: 4
    };

    StarParticle.prototype.create = function (index) {
        this.index = index;
        return this;
    };

    StarParticle.prototype.setInitialValues = function () {
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
    };

    StarParticle.prototype.setTexture = function (tex) {
        changePackedAttribute(this.geometry, this.index, 'timeTextureData', 3, 1, tex);

        return this;
    };

    StarParticle.prototype.setParallaxFactor = function (parallaxFactor) {
        changePackedAttribute(this.geometry, this.index, 'starData', 3, 0, -1.0 + parallaxFactor);
        return this;
    };

    StarParticle.prototype.setSineFrequency = function (sineFrequency) {
        changePackedAttribute(this.geometry, this.index, 'starData', 3, 1, sineFrequency);
        return this;
    };

    StarParticle.prototype.setSineAmplitude = function (sineAmplitude) {
        changePackedAttribute(this.geometry, this.index, 'starData', 3, 2, sineAmplitude);
        return this;
    };

    StarParticle.prototype.setSize = function (size) {
        changePackedAttribute(this.geometry, this.index, 'sizeAngleData', 4, 0, size);
        return this;
    };

    StarParticle.prototype.setSizeChange = function (size) {
        changePackedAttribute(this.geometry, this.index, 'sizeAngleData', 4, 1, size);
        return this;
    };

    StarParticle.prototype.setColor = function (color) {
        changeAttribute(this.geometry, this.index, 'color', [color.r, color.g, color.b]);
        return this;
    };

    StarParticle.prototype.setOpacity = function (opacity) {
        changePackedAttribute(this.geometry, this.index, 'timeTextureData', 3, 2, opacity);
        return this;
    };

    StarParticle.prototype.setPosition = function (pos) {
        changeAttribute(this.geometry, this.index, 'position', [pos.x, pos.y, pos.z || 0], true);
        return this;
    };

    StarParticle.prototype.setAngle = function (angle) {
        changePackedAttribute(this.geometry, this.index, 'sizeAngleData', 4, 2, mathlib.degreeToRadian(angle));
        return this;
    };

    StarParticle.prototype.setAngleChange = function (angle) {
        changePackedAttribute(this.geometry, this.index, 'sizeAngleData', 4, 3, mathlib.degreeToRadian(angle));
        return this;
    };

    StarParticle.prototype.deactivate = function () {
        this.setInitialValues();
        return this;
    };

    StarParticle.prototype.setActivationTime = function (gameTime) {
        changePackedAttribute(this.geometry, this.index, 'timeTextureData', 3, 0, gameTime);
        return this;
    };

    function changeAttribute(geometry, index, key, values, debug) {
        values = [].concat(values);

        var target = geometry.attributes[key].array;

        values.forEach(function (value, i) {
            target[index * values.length + i] = value;
        });

        geometry.attributes[key].needsUpdate = true;
    }

    function changePackedAttribute(geometry, index, key, componentCount, offset, value) {
        var target = geometry.attributes[key].array;
        target[index * componentCount + offset] = value;
        geometry.attributes[key].needsUpdate = true;
    }

    return StarParticle;
}();