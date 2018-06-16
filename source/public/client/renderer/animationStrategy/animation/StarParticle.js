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
        this.setVelocity(new THREE.Vector3(0, 0, 0));
        this.setAcceleration(new THREE.Vector3(0, 0, 0));
        this.setTexture(this.texture.glow);
        this.setParallaxFactor(0.0);
        this.setSineFrequency(0.0);
        this.setSineAmplitude(1);

        return this;
    };

    StarParticle.prototype.setTexture = function (tex) {
        changeAttribute(this.geometry, this.index, 'textureNumber', tex);

        return this;
    };
    
    StarParticle.prototype.setParallaxFactor = function (parallaxFactor) {
        changeAttribute(this.geometry, this.index, 'parallaxFactor', parallaxFactor);
        return this;
    };

    StarParticle.prototype.setSineFrequency = function (sineFrequency) {
        changeAttribute(this.geometry, this.index, 'sineFrequency', sineFrequency);
        return this;
    };

    StarParticle.prototype.setSineAmplitude = function (sineAmplitude) {
        changeAttribute(this.geometry, this.index, 'sineAmplitude', sineAmplitude);
        return this;
    };

    StarParticle.prototype.setSize = function (size) {
        changeAttribute(this.geometry, this.index, 'size', size);
        return this;
    };

    StarParticle.prototype.setSizeChange = function (size) {
        changeAttribute(this.geometry, this.index, 'sizeChange', size);
        return this;
    };

    StarParticle.prototype.setColor = function (color) {
        changeAttribute(this.geometry, this.index, 'color', [color.r, color.g, color.b]);
        return this;
    };

    StarParticle.prototype.setOpacity = function (opacity) {
        changeAttribute(this.geometry, this.index, 'opacity', opacity);
        return this;
    };

    StarParticle.prototype.setPosition = function (pos) {
        changeAttribute(this.geometry, this.index, 'position', [pos.x, pos.y, 0], true);
        return this;
    };

    StarParticle.prototype.setAngle = function (angle) {
        changeAttribute(this.geometry, this.index, 'angle', mathlib.degreeToRadian(angle));
        return this;
    };

    StarParticle.prototype.setAngleChange = function (angle) {
        changeAttribute(this.geometry, this.index, 'angleChange', mathlib.degreeToRadian(angle));
        return this;
    };

    StarParticle.prototype.setVelocity = function (velocity) {
        changeAttribute(this.geometry, this.index, 'velocity', [velocity.x, velocity.y, 0]);
        return this;
    };

    StarParticle.prototype.setAcceleration = function (acceleration) {
        changeAttribute(this.geometry, this.index, 'acceleration', [acceleration.x, acceleration.y, 0]);
        return this;
    };

    StarParticle.prototype.deactivate = function () {
        this.setInitialValues();
        return this;
    };

    StarParticle.prototype.setActivationTime = function (gameTime) {
        changeAttribute(this.geometry, this.index, 'activationGameTime', gameTime);
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

    return StarParticle;
}();