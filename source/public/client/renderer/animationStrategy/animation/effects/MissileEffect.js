"use strict";

window.MissileEffect = function () {
    function BoltEffect(emitterContainer, args) {
        ParticleAnimation.call(this, emitterContainer);
        args = args || {};

        this.scene = args.scene;
        this.time = args.time || 0;
        this.hit = args.hit || false;
        this.speed = args.speed || 1.6;
        this.color = args.color || new THREE.Color(0, 0, 0);
        this.origin = args.origin;
        this.target = { x: args.target.x, y: args.target.y };
        this.damage = args.damage || 0;
        this.size = args.size || 100;
        this.angle = -mathlib.getCompassHeadingOfPoint(this.origin, this.target);

        let distance = mathlib.distance(this.origin, this.target);
        if (!this.hit) {
            distance += Math.random() * 100 + 50;
        }
        this.speedVector = mathlib.getPointInDirection(this.speed, -this.angle, 0, 0, true);
        this.duration = distance / this.speed;

        this.fadeInSpeed = 25;
        this.fadeOutSpeed = this.hit ? 0 : 1000;

        this.hasParticle = args.hasParticle;

        // ---------------- AUDIO ----------------
        //this.noSound = args.noSound || !gamedata?.playAudio;
        this.soundVolume = args.soundVolume ?? 0.1;
        this.playedLaunchSound = false;
        this.playedImpactSound = false;

        if (!BoltEffect.cachedLaunchAudio) {
            BoltEffect.cachedLaunchAudio = new Audio("client/renderer/animationStrategy/animation/sound/TorpedoAudio.wav");
        }
        if (!BoltEffect.cachedImpactAudio) {
            BoltEffect.cachedImpactAudio = new Audio("client/renderer/animationStrategy/animation/sound/ExplosionAudio.wav");
        }

        //if (this.hit) this.duration -= 25;

        if (this.hit) {
            this.duration -= 25;

            new Explosion(this.emitterContainer, {
                size: this.size / 4,
                position: this.target,
                type: "glow",
                color: args.color,
                time: this.time + this.duration
            });

            if (this.damage) {
                new Explosion(this.emitterContainer, {
                    size: 12 * this.damage + 12,
                    position: this.target,
                    type: ["gas", "pillar"][Math.round(Math.random() * 2)],
                    time: this.time + this.duration
                });
            }
        }

        createBoltParticle.call(this, this.size, this.color, this.origin);
        if (this.hasParticle) {
            createBoltParticle.call(
                this,
                this.size / 2,
                { r: 1, g: 1, b: 1 },
                mathlib.getPointInDirection(this.size / 4.5, -this.angle, this.origin.x, this.origin.y, true)
            );
            
        }

        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + this.duration);
        }
    }

    BoltEffect.prototype = Object.create(ParticleAnimation.prototype);

    BoltEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    // ---------------- AUDIO IN RENDER ----------------
    BoltEffect.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        if (this.emitterContainer && this.emitterContainer.render) {    
            this.emitterContainer.render(now, total, last, delta, zoom, back, paused);
        }    

        // Launch sound
        if (gamedata.playAudio && !this.playedLaunchSound && total >= this.time) {
            try {
                const launchSound = BoltEffect.cachedLaunchAudio.cloneNode(true);
                launchSound.volume = this.soundVolume;
                launchSound.currentTime = 0;
                launchSound.play().catch(() => {});
                this.playedLaunchSound = true;
            } catch (e) {
                console.warn("Bolt launch sound failed:", e);
            }
        }

        // Impact sound
        if (gamedata.playAudio && this.hit && !this.playedImpactSound && total >= this.time + this.duration - 50) {
            try {
                const impactSound = BoltEffect.cachedImpactAudio.cloneNode(true);
                impactSound.volume = this.soundVolume;
                impactSound.currentTime = 0;
                impactSound.play().catch(() => {});
                this.playedImpactSound = true;
            } catch (e) {
                console.warn("Bolt impact sound failed:", e);
            }
        }
    };

    function createBoltParticle(size, color, position) {
        var particle = this.emitterContainer.getParticle(this);
        particle
            .setSize(size)
            .setFadeIn(this.time, this.fadeInSpeed)
            .setFadeOut(this.time + this.duration, this.fadeOutSpeed)
            .setOpacity(1.0)
            .setActivationTime(this.time)
            .setVelocity(this.speedVector)
            .setPosition(position)
            .setTexture(BaseParticle.prototype.texture.bolt)
            .setColor(color)
            .setAngle(this.angle);
    }

    return BoltEffect;
}();

/* Old version without audio
"use strict";

window.BoltEffect = function () {
    function BoltEffect(emitterContainer, args) {
        ParticleAnimation.call(this, emitterContainer);

        if (!args) {
            args = {};
        }

        this.scene = args.scene;
        this.time = args.time || 0;
        this.hit = args.hit || false;
        this.speed = args.speed || 1.6;
        this.color = args.color || new THREE.Color(0, 0, 0);
        this.origin = args.origin;
        this.target = {
            x: args.target.x,
            y: args.target.y
        };
        this.damage = args.damage || 0;

        this.size = args.size || 100;
        this.angle = -mathlib.getCompassHeadingOfPoint(this.origin, this.target);

        var distance = mathlib.distance(this.origin, this.target);
        if (!this.hit) {
            //var missFactor = distance / 1500;
            //this.angle = mathlib.addToDirection(this.angle, Math.random() * missFactor - missFactor/2);
            distance += Math.random() * 100 + 50;
        }
        this.speedVector = mathlib.getPointInDirection(this.speed, -this.angle, 0, 0, true);

        this.duration = distance / this.speed;

        this.fadeInSpeed = 25;
        this.fadeOutSpeed = this.hit ? 0 : 1000;

        this.hasParticle = args.hasParticle; //Some weapons you don't want to show a particle e.g. ramming.

        if (this.hit) {
            this.duration -= 25;

            new Explosion(this.emitterContainer, {
                size: this.size / 4,
                position: this.target,
                type: "glow",
                color: args.color,
                time: this.time + this.duration
            });

            if (this.damage) {
                new Explosion(this.emitterContainer, {
                    size: 12 * this.damage + 12,
                    position: this.target,
                    type: ["gas", "pillar"][Math.round(Math.random() * 2)],
                    time: this.time + this.duration
                });
            }
        }

        createBoltParticle.call(this, this.size, this.color, this.origin);
        if(this.hasParticle) createBoltParticle.call(this, this.size / 2, { r: 1, g: 1, b: 1 }, mathlib.getPointInDirection(this.size / 4.5, -this.angle, this.origin.x, this.origin.y, true));
        
        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + this.duration)
        }
    }

    BoltEffect.prototype = Object.create(ParticleAnimation.prototype);

    BoltEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };


    function createBoltParticle(size, color, position) {

        var particle = this.emitterContainer.getParticle(this);
        particle.setSize(size).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(1.0).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.bolt).setColor(color).setAngle(this.angle);
    }

    return BoltEffect;
}();
*/