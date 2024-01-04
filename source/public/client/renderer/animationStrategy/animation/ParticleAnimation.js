"use strict";

window.ParticleAnimation = function () {
    function ParticleAnimation(emitterContainer, seed) {
        this.seed = seed || Math.random();
        this.emitterContainer = emitterContainer;
        this.create();
    }

    ParticleAnimation.prototype = Object.create(Animation.prototype);

    ParticleAnimation.prototype.create = function () {
        Math.seedrandom(this.seed);
    };

    ParticleAnimation.prototype.getRandomColor = function () {
        if (this.color) {
            return this.color;
        }

        return new THREE.Color().setRGB(1, (255 - Math.floor(Math.random() * 255)) / 255, Math.floor(Math.random() * 155) / 255);
    };

     ParticleAnimation.prototype.getWeaponColor = function () { //Added so that if a weapon colour is passed in arg variables, it will be used.
        if (this.color) {
            return this.color;
        }
        return new THREE.Color().setRGB(122 / 255, 221 / 255, 255 / 255);
    }
    
    ParticleAnimation.prototype.getSmokeColor = function () {
        var c = (Math.random() * 50 + 20) / 255;
        return new THREE.Color().setRGB(c, c, c + 0.05);
    };

    return ParticleAnimation;
}();