import Animation from "../Animation";

class ParticleEmitterContainer extends Animation {
  constructor(scene, defaultParticleAmount, emitterClass, emitterArgs) {
    super();
    this.emitters = [];
    this.scene = scene;
    this.defaultParticleAmount = defaultParticleAmount;
    this.emitterClass = emitterClass || ParticleEmitter;
    this.emitterArgs = emitterArgs || {};
  }

  getParticle(animation) {
    var particle;
    var emitter = null;

    for (var i in this.emitters) {
      particle = this.emitters[i].emitter.getParticle();
      if (particle) {
        emitter = this.emitters[i];
      }
    }

    if (!particle) {
      this.emitters.push({
        emitter: new this.emitterClass(
          this.scene,
          this.defaultParticleAmount,
          this.emitterArgs
        ),
        reservations: []
      });
      return this.getParticle(animation);
    }

    var reservation = this.getReservation(
      emitter.reservations,
      animation,
      true
    );
    reservation.indexes.push(particle.index);
    return particle;
  }

  cleanUp() {
    this.emitters.forEach(function(emitter) {
      emitter.emitter.cleanUp();
    });
    this.emitters = [];
  }

  /*
    ParticleEmitterContainer.prototype.cleanUpAnimation = function (animation) {
        this.emitters.forEach(function (emitter) {
           cleanUpAnimationFromEmitter(animation, emitter);
        });
    };
    */

  setRotation(rotation) {
    this.emitters.forEach(function(emitter) {
      emitter.emitter.mesh.rotation.y = (rotation * Math.PI) / 180;
    });
  }

  setPosition(pos) {
    this.emitters.forEach(function(emitter) {
      emitter.emitter.mesh.position.x = pos.x;
      emitter.emitter.mesh.position.y = pos.y;
      emitter.emitter.mesh.position.z = pos.z;
    });
  }

  lookAt(thing) {
    this.emitters.forEach(function(emitter) {
      emitter.emitter.mesh.quaternion.copy(thing.quaternion);
    });
  }

  render(now, total, last, delta, zoom) {
    this.emitters.forEach(function(emitter) {
      emitter.emitter.render(now, total, last, delta, zoom);
    });
  }

  /*
    function cleanUpAnimationFromEmitter(animation, emitter) {
        var reservation = getReservation(emitter.reservations);
         emitter.reservations = emitter.reservations.filter(function (res) {
            return res !== reservation;
        });
         emitter.emitter.freeParticles(reservation.indexes);
    }
    */
  getReservation(reservations, animation, create) {
    var reservation = reservations.find(function(reservation) {
      return reservation.animation === animation;
    });

    if (!reservation && create) {
      reservation = { animation: animation, indexes: [] };
      reservations.push(reservation);
    }

    return reservation;
  }
}

export default ParticleEmitterContainer;
