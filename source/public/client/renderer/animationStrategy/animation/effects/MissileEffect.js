"use strict";

// Missile-with-trail effect. Previously a byte-equivalent copy of BoltEffect;
// now adds a smoke/gas trail dropped along the projectile's path. All trail
// particles are pre-spawned with explicit activation times so timeline
// scrubbing in the replay shows them at the correct positions.

window.MissileEffect = function () {
    function MissileEffect(emitterContainer, args) {
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
        this.soundVolume = args.soundVolume ?? 0.1;
        this.playedLaunchSound = false;
        this.playedImpactSound = false;

        if (!MissileEffect.cachedLaunchAudio) {
            MissileEffect.cachedLaunchAudio = new Audio("client/renderer/animationStrategy/animation/sound/TorpedoAudio.mp3");
        }
        if (!MissileEffect.cachedImpactAudio) {
            MissileEffect.cachedImpactAudio = new Audio("client/renderer/animationStrategy/animation/sound/ExplosionAudio.mp3");
        }

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

        // Missile head — same pattern as BoltEffect.
        createMissileHead.call(this, this.size, this.color, this.origin);
        if (this.hasParticle) {
            createMissileHead.call(
                this,
                this.size / 2,
                { r: 1, g: 1, b: 1 },
                mathlib.getPointInDirection(this.size / 4.5, -this.angle, this.origin.x, this.origin.y, true)
            );
        }

        spawnTrail.call(this);

        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + this.duration);
            args.systemDestroyedEffect.add(this.target, args.critNames, this.time + this.duration, 'crit');
        }
    }

    MissileEffect.prototype = Object.create(ParticleAnimation.prototype);

    MissileEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    MissileEffect.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        if (this.emitterContainer && this.emitterContainer.render) {
            this.emitterContainer.render(now, total, last, delta, zoom, back, paused);
        }

        if (gamedata.playAudio && !this.playedLaunchSound && total >= this.time) {
            try {
                const launchSound = MissileEffect.cachedLaunchAudio.cloneNode(true);
                launchSound.volume = this.soundVolume;
                launchSound.currentTime = 0;
                launchSound.play().catch(() => { });
                this.playedLaunchSound = true;
            } catch (e) {
                console.warn("Missile launch sound failed:", e);
            }
        }

        if (gamedata.playAudio && this.hit && !this.playedImpactSound && total >= this.time + this.duration - 50) {
            try {
                const impactSound = MissileEffect.cachedImpactAudio.cloneNode(true);
                impactSound.volume = this.soundVolume;
                impactSound.currentTime = 0;
                impactSound.play().catch(() => { });
                this.playedImpactSound = true;
            } catch (e) {
                console.warn("Missile impact sound failed:", e);
            }
        }
    };

    function createMissileHead(size, color, position) {
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

    function spawnTrail() {
        var scale = 0.5; //Maybe change
        var trailColor = new THREE.Color(
            Math.min(1, this.color.r * scale),
            Math.min(1, this.color.g * scale),
            Math.min(1, this.color.b * scale)
        );

        var trailInterval = 30;                  // ms between drops
        var trailFadeIn = 25;
        var trailFadeOut = 500;
        var trailSize = Math.max(6, this.size / 4);

        var t = this.time;
        var endTime = this.time + this.duration;
        while (t <= endTime) {
            var elapsed = t - this.time;
            var pos = {
                x: this.origin.x + this.speedVector.x * elapsed,
                y: this.origin.y + this.speedVector.y * elapsed
            };
            var particle = this.emitterContainer.getParticle(this);
            particle
                .setSize(trailSize)
                .setFadeIn(t, trailFadeIn)
                .setFadeOut(t + trailFadeIn, trailFadeOut)
                .setOpacity(0.6)
                .setActivationTime(t)
                .setVelocity({ x: 0, y: 0 })
                .setPosition(pos)
                .setTexture(BaseParticle.prototype.texture.gas)
                .setColor(trailColor)
                .setAngle(Math.random() * 360);
            t += trailInterval;
        }
    }

    return MissileEffect;
}();
