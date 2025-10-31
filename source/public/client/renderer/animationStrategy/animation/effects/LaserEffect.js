"use strict";

window.LaserEffect = function () {
    function LaserEffect(weapon, weaponOrigin, shooter, target, scene, args) {
        Animation.call(this);

        args = args || {};

        this.time = args.time || 0;
        this.duration = args.duration || 2000;
        this.hit = args.hit || false;
        this.color = args.color || new THREE.Color(0, 0, 0);
        this.shooter = shooter;
        this.startOffset = {
            x: Math.random() * 8 - 4,
            y: Math.random() * 8 - 4
        };
        this.weapon = weapon;
        this.weaponOrigin = weaponOrigin;
        this.damage = args.damage || 0;
        this.size = args.size || 10;
        this.target = target;

        if (!this.hit) {
            const targetPos = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
            this.target = mathlib.getPointBetween(this.shooter.getPosition(), targetPos, Math.random() * 0.1 + 2.5);
        }

        this.scene = scene;
        this.fadeInSpeed = Math.random() * 250;
        this.fadeOutSpeed = Math.random() * 500 + 500;
        this.pulsatingFactor = Math.random() * 100 + 100;

        const offsetVelocityFactor = this.hit ? 1 : 3;
        this.offsetVelocity = {
            x: Math.random() * 0.01 * offsetVelocityFactor - 0.005 * offsetVelocityFactor,
            y: Math.random() * 0.01 * offsetVelocityFactor - 0.005 * offsetVelocityFactor
        };

        // --- Laser meshes ---
        const beamWidth = Math.ceil(this.size * 0.2);
        const haloWidth = Math.ceil(this.size * 0.3);
        this.lasers = [
            createLaser.call(this, this.color, 0.8, haloWidth),
            createLaser.call(this, new THREE.Color(1, 1, 1), 0.6, beamWidth)
        ];

        this.lasers.forEach(laser => {
            laser.multiplyOpacity(0);
            this.scene.add(laser.mesh);
        });

        // --- Particle emitter ---
        this.particleEmitter = new ParticleEmitterContainer(scene, 200);

        if (this.hit) {
            new Explosion(this.particleEmitter, {
                size: 22,
                position: { x: 0, y: 0 },
                type: "glow",
                color: args.color,
                time: this.time,
                duration: this.duration
            });

            let amount = this.damage;
            while (amount--) {
                new Explosion(this.particleEmitter, {
                    size: 22,
                    position: { x: 0, y: 0 },
                    type: ["gas", "pillar"][Math.round(Math.random())],
                    time: this.time + Math.random() * this.duration
                });
            }
        }

        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + Math.random() * this.duration);
        }

        // --- Cached laser sound setup ---
        if (!LaserEffect.cachedAudio) {
            LaserEffect.cachedAudio = new Audio("client/renderer/animationStrategy/animation/sound/LaserAudio1.wav");
            LaserEffect.cachedAudio.volume = 0.1;
            LaserEffect.cachedAudio.preload = "auto";
        }

        this.playedSound = false; // flag so we only play once
        this.soundVolume = args.soundVolume !== undefined ? args.soundVolume : LaserEffect.cachedAudio.volume;
        //this.noSound = args.noSound || !gamedata?.playAudio; 
    }

    LaserEffect.prototype = Object.create(Animation.prototype);

    LaserEffect.prototype.cleanUp = function () {
        this.lasers.forEach(laser => this.scene.remove(laser.mesh));
        this.particleEmitter.cleanUp();
    };

    LaserEffect.prototype.render = function (now, total, last, delta, zoom) {
        this.particleEmitter.render(now, total, last, delta, zoom);

        // --- Play sound exactly when the laser starts ---
        if (gamedata.playAudio && !this.playedSound && total >= this.time) {
            try {
                this.laserSound = LaserEffect.cachedAudio.cloneNode(true);
                this.laserSound.volume = this.soundVolume;
                this.laserSound.currentTime = 0;
                this.laserSound.play().catch(() => {});
                this.playedSound = true;
            } catch (e) {
                console.warn("Laser sound playback failed:", e);
            }
        }

        // --- Stop sound with fade-out when the laser visual finishes ---
        if (this.laserSound && total > this.time + this.duration + this.fadeOutSpeed) {
            const sound = this.laserSound;
            this.laserSound = null;

            const fadeDuration = 300; // milliseconds
            const fadeSteps = 10;
            const fadeStep = sound.volume / fadeSteps;
            const fadeInterval = fadeDuration / fadeSteps;

            let currentStep = 0;
            const fadeOutTimer = setInterval(() => {
                if (currentStep++ >= fadeSteps) {
                    clearInterval(fadeOutTimer);
                    sound.pause();
                    sound.currentTime = 0;
                } else {
                    sound.volume = Math.max(0, sound.volume - fadeStep);
                }
            }, fadeInterval);
        }

        let opacity;
        let fadeoutFactor = 0;

        if (total < this.time + this.fadeInSpeed) {
            opacity = (total - this.time) / this.fadeInSpeed;
        } else if (total < this.time + this.duration) {
            opacity = 1;
        } else if (total < this.time + this.duration + this.fadeOutSpeed) {
            fadeoutFactor = (total - (this.time + this.duration)) / this.fadeOutSpeed;
            opacity = 1 - fadeoutFactor;
        }

        const pulseFrequency = this.pulsatingFactor - this.pulsatingFactor * 0.9 * fadeoutFactor;
        const pulseIntensity = 0.001 * (10 * fadeoutFactor + 1);
        const pulse = 1 - (total % pulseFrequency) * pulseIntensity;

        opacity *= pulse;

        const elapsedTime = total - this.time;
        const startAndEnd = getStartAndEnd.call(this, {
            x: this.offsetVelocity.x * elapsedTime,
            y: this.offsetVelocity.y * elapsedTime
        });

        this.particleEmitter.setPosition(startAndEnd.end);

        this.lasers.forEach(laser => {
            laser.multiplyOpacity(opacity);
            laser.setStartAndEnd(startAndEnd.start, startAndEnd.end);
        });
    };

    LaserEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    function createLaser(color, opacity, width) {
        const startAndEnd = getStartAndEnd.call(this);
        return new LineSprite(startAndEnd.start, startAndEnd.end, width, 201, color, opacity, {
            blending: THREE.AdditiveBlending,
            texture: new THREE.TextureLoader().load("img/effect/laser19.png")
        });
    }

    function getStartAndEnd(offsetVelocity) {
        offsetVelocity = offsetVelocity || { x: 0, y: 0 };

        const endPosition = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
        const start = this.weapon.hasSpecialLaunchHexCalculation
            ? this.weaponOrigin
            : {
                  x: this.shooter.getPosition().x + this.startOffset.x,
                  y: this.shooter.getPosition().y + this.startOffset.y
              };

        const end = { x: endPosition.x + offsetVelocity.x, y: endPosition.y + offsetVelocity.y };

        return { start, end };
    }

    return LaserEffect;
}();

/* Old version without sound
"use strict";

window.LaserEffect = function () {
    function LaserEffect(weapon, weaponOrigin, shooter, target, scene, args) {
        Animation.call(this);

        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.duration = args.duration || 2000;
        this.hit = args.hit || false;

        this.color = args.color || new THREE.Color(0, 0, 0);
        this.shooter = shooter;
        this.startOffset = { //Previously * 30 - 10 - DK 01.25
            x: Math.random() * 8 - 4,
            y: Math.random() * 8 - 4
        };

        this.weapon = weapon;
        this.weaponOrigin = weaponOrigin;
        
        this.damage = args.damage || 0;
        
        this.size = args.size || 10;

        this.target = target;
        if (!this.hit) {
            var targetPosition = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
            this.target = mathlib.getPointBetween(this.shooter.getPosition(), targetPosition, Math.random() * 0.1 + 2.5);
        }

        this.scene = scene;

        this.fadeInSpeed = Math.random() * 250;
        this.fadeOutSpeed = Math.random() * 500 + 500;
        this.pulsatingFactor = Math.random() * 100 + 100;
        var offsetVelocityFactor = this.hit ? 1 : 3;
        this.offsetVelocity = {
            x: Math.random() * 0.01 * offsetVelocityFactor - 0.005 * offsetVelocityFactor,
            y: Math.random() * 0.01 * offsetVelocityFactor - 0.005 * offsetVelocityFactor
        };

        //adding width depending on animation size:
        //this.lasers = [createLaser.call(this, this.color, 0.5, 10), createLaser.call(this, new THREE.Color(1, 1, 1), 0.6, 3)];
        var beamWidth = Math.ceil(this.size*0.2);
        var haloWidth = Math.ceil(this.size*0.3);
        this.lasers = [createLaser.call(this, this.color, 0.8, haloWidth), createLaser.call(this, new THREE.Color(1, 1, 1), 0.6, beamWidth)];
        
        this.lasers.forEach(function (laser) {
            laser.multiplyOpacity(0);
            this.scene.add(laser.mesh);
        }, this);

        this.particleEmitter = new ParticleEmitterContainer(scene, 200);


        if (this.hit) {
            new Explosion(this.particleEmitter, {
                size: 22, //16, - scaling up a bit!
                position: { x: 0, y: 0 },
                type: "glow",
                color: args.color,
                time: this.time,
                duration: this.duration
            });

            var amount = this.damage;

            while (amount--) {
                new Explosion(this.particleEmitter, {
                    size: 22, //16, - scaling up a bit!
                    position: { x: 0, y: 0 },
                    type: ["gas", "pillar"][Math.round(Math.random() * 2)],
                    time: this.time + Math.random() * this.duration
                });
            }
        }

        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + Math.random() * this.duration)
        }
    }

    LaserEffect.prototype = Object.create(Animation.prototype);

    LaserEffect.prototype.cleanUp = function () {
        this.lasers.forEach(function (laser) {
            this.scene.remove(laser.mesh);
        }, this);

        this.particleEmitter.cleanUp();
    };

    LaserEffect.prototype.render = function (now, total, last, delta, zoom) {
        this.particleEmitter.render(now, total, last, delta, zoom);
        if (total < this.time || total > this.time + this.duration + this.fadeOutSpeed) {
            this.lasers.forEach(function (laser) {
                laser.multiplyOpacity(0);
            }, this);
            return;
        }

        var opacity;
        var fadeoutFactor = 0;

        if (total < this.time + this.fadeInSpeed) {
            opacity = (total - this.time) / this.fadeInSpeed;
        } else if (total < this.time + this.duration) {
            opacity = 1;
        } else if (total < this.time + this.duration + this.fadeOutSpeed) {
            fadeoutFactor = (total - (this.time + this.duration)) / this.fadeOutSpeed;
            opacity = 1 - (total - (this.time + this.duration)) / this.fadeOutSpeed;
        }

        var pulseFrequency = this.pulsatingFactor - this.pulsatingFactor * 0.9 * fadeoutFactor;
        var pulseIntensity = 0.001 * (10 * fadeoutFactor + 1);

        var pulse = 1 - total % pulseFrequency * pulseIntensity;

        opacity *= pulse;

        var elapsedTime = total - this.time;

        var startAndEnd = getStartAndEnd.call(this, { x: this.offsetVelocity.x * elapsedTime, y: this.offsetVelocity.x * elapsedTime });

        this.particleEmitter.setPosition(startAndEnd.end);

        this.lasers.forEach(function (laser) {
            laser.multiplyOpacity(opacity);
            laser.setStartAndEnd(startAndEnd.start, startAndEnd.end);
        }, this);
    };

    LaserEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    function createLaser(color, opacity, widht) {
        var startAndEnd = getStartAndEnd.call(this);
        return new LineSprite(startAndEnd.start, startAndEnd.end, widht, 201, color, opacity, {
            blending: THREE.AdditiveBlending,
            texture: new THREE.TextureLoader().load("img/effect/laser19.png")
        });
    }

    function getStartAndEnd(offsetVelocity) {

        if (!offsetVelocity) {
            offsetVelocity = { x: 0, y: 0 };
        }

        var endPosition = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
        
        if (this.weapon.hasSpecialLaunchHexCalculation){
         var start = this.weaponOrigin;      	
        }else{
        var start = this.shooter.getPosition();
        start.x += this.startOffset.x;
        start.y += this.startOffset.y;
		}
        var end = { x: endPosition.x + offsetVelocity.x, y: endPosition.y + offsetVelocity.y };
        return { start: start, end: end };
    }

    return LaserEffect;
}();
*/