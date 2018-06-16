"use strict";

window.ParticleEmitterContainer = function () {

    function ParticleEmitterContainer(scene, defaultParticleAmount, emitterClass) {
        this.emitters = [];
        this.scene = scene;
        this.defaultParticleAmount = defaultParticleAmount;
        this.emitterClass = emitterClass || ParticleEmitter
        Animation.call(this);
    }

    ParticleEmitterContainer.prototype = Object.create(Animation.prototype);

    ParticleEmitterContainer.prototype.getParticle = function (animation) {

        var particle;
        var emitter = null;

        for (var i in this.emitters) {
            particle = this.emitters[i].emitter.getParticle();
            if (particle) {
                emitter = this.emitters[i];
            }
        }

        if (!particle) {
            this.emitters.push({ emitter: new this.emitterClass(this.scene, this.defaultParticleAmount), reservations: [] });
            return this.getParticle(animation);
        }

        var reservation = getReservation(emitter.reservations, animation, true);
        reservation.indexes.push(particle.index);
        return particle;
    };

    ParticleEmitterContainer.prototype.cleanUp = function () {
        this.emitters.forEach(function (emitter) {
            emitter.emitter.cleanUp();
        });
        this.emitters = [];
    };

    /*
    ParticleEmitterContainer.prototype.cleanUpAnimation = function (animation) {
        this.emitters.forEach(function (emitter) {
           cleanUpAnimationFromEmitter(animation, emitter);
        });
    };
    */
    ParticleEmitterContainer.prototype.setPosition = function (pos) {
        this.emitters.forEach(function (emitter) {
            emitter.emitter.mesh.position.x = pos.x;
            emitter.emitter.mesh.position.y = pos.y;
        });
    };

    ParticleEmitterContainer.prototype.render = function (now, total, last, delta, zoom) {
        this.emitters.forEach(function (emitter) {
            emitter.emitter.render(now, total, last, delta, zoom);
        });
    };

    /*
    function cleanUpAnimationFromEmitter(animation, emitter) {
        var reservation = getReservation(emitter.reservations);
         emitter.reservations = emitter.reservations.filter(function (res) {
            return res !== reservation;
        });
         emitter.emitter.freeParticles(reservation.indexes);
    }
    */
    function getReservation(reservations, animation, create) {
        var reservation = reservations.find(function (reservation) {
            return reservation.animation === animation;
        });

        if (!reservation && create) {
            reservation = { animation: animation, indexes: [] };
            reservations.push(reservation);
        }

        return reservation;
    }

    return ParticleEmitterContainer;
}();