"use strict";

window.TorpedoEffect = function () {
    function TorpedoEffect(emitterContainer, args) {
        ParticleAnimation.call(this, emitterContainer);

        if (!args) {
            args = {};
        }

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
            distance += Math.random() * 100 + 50;
        }
        this.speedVector = mathlib.getPointInDirection(this.speed, -this.angle, 0, 0, true);

        this.duration = distance / this.speed;

        this.fadeInSpeed = 25;
        this.fadeOutSpeed = this.hit ? 0 : 1000;

        // --- 🔊 Audio setup ---
        //this.noSound = args.noSound || !gamedata?.playAudio; 
        this.soundVolume = args.soundVolume ?? 0.1;
        this.playedLaunchSound = false;
        this.playedImpactSound = false;

        // Cache reusable Audio objects globally
        if (!TorpedoEffect.cachedLaunchAudio) {
            TorpedoEffect.cachedLaunchAudio = new Audio("/client/renderer/animationStrategy/animation/sound/TorpedoAudio.wav");
        }
        if (!TorpedoEffect.cachedExplosionAudio) {
            TorpedoEffect.cachedExplosionAudio = new Audio("/client/renderer/animationStrategy/animation/sound/ExplosionAudio.wav");
        }

        if (this.hit) {
            this.duration -= 25;

            new Explosion(this.emitterContainer, {
                size: this.size / 3,
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

        createTorpedoParticles.call(this, this.size, this.color, this.origin);

        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + this.duration)
        }
    }

    TorpedoEffect.prototype = Object.create(ParticleAnimation.prototype);

    TorpedoEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    // --- 🔊 Add render() just for sound timing ---
TorpedoEffect.prototype.render = function (now, total, last, delta, zoom) {
    if (this.emitterContainer && this.emitterContainer.render) {
        this.emitterContainer.render(now, total, last, delta, zoom);
    }

    // --- Only play sounds once the animation has actually started running ---
    // Prevent sounds from triggering during replay preload or setup.
    if (total < this.time) {
        return;
    }

    // --- Play launch sound (only once) ---
    if (gamedata.playAudio && !this.playedLaunchSound) {
        try {
            const launchSound = TorpedoEffect.cachedLaunchAudio.cloneNode(true);
            launchSound.volume = this.soundVolume;
            launchSound.currentTime = 0;
            launchSound.play().catch(() => {});
            this.playedLaunchSound = true;
        } catch (e) {
            console.warn("Torpedo launch sound failed:", e);
        }
    }

    // --- Play explosion sound ---
    const explosionTime = this.time + this.duration - 50; // slightly before impact
    if (gamedata.playAudio && this.hit && !this.playedImpactSound && total >= explosionTime) {
        try {
            const explosionSound = TorpedoEffect.cachedExplosionAudio.cloneNode(true);
            explosionSound.volume = this.soundVolume;
            explosionSound.currentTime = 0;
            explosionSound.play().catch(() => {});
            this.playedImpactSound = true;
        } catch (e) {
            console.warn("Torpedo explosion sound failed:", e);
        }
    }
};
    function createTorpedoParticles(size, color, position) {
        var particle;
        var amount = Math.ceil(Math.random() * 3) + 4;
        while (amount--) {
            var angle = Math.random() * 360;
            particle = this.emitterContainer.getParticle(this);
            particle.setSize(size * 0.5).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(0.3).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.bolt).setColor(color).setAngle(angle).setAngleChange(0.4);

            particle = this.emitterContainer.getParticle(this);
            particle.setSize(size * 1.2).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(0.2).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.bolt).setColor(color).setAngle(angle).setAngleChange(0.4);
        }

        particle = this.emitterContainer.getParticle(this);
        particle.setSize(size).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(0.5).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.glow).setColor(color);

        particle = this.emitterContainer.getParticle(this);
        particle.setSize(size / 4).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(1.0).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.glow).setColor(color);
    }

    return TorpedoEffect;
}();

/*
"use strict";

window.TorpedoEffect = function () {
    function TorpedoEffect(emitterContainer, args) {
        ParticleAnimation.call(this, emitterContainer);

        if (!args) {
            args = {};
        }

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
            distance += Math.random() * 100 + 50;
        }
        this.speedVector = mathlib.getPointInDirection(this.speed, -this.angle, 0, 0, true);

        this.duration = distance / this.speed;

        this.fadeInSpeed = 25;
        this.fadeOutSpeed = this.hit ? 0 : 1000;

        if (this.hit) {
            this.duration -= 25;

            new Explosion(this.emitterContainer, {
                size: this.size / 3,
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

        createTorpedoParticles.call(this, this.size, this.color, this.origin);

        
        if (args.systemDestroyedEffect) {
            args.systemDestroyedEffect.add(this.target, args.damagedNames, this.time + this.duration)
        }
    }

    TorpedoEffect.prototype = Object.create(ParticleAnimation.prototype);

    TorpedoEffect.prototype.getDuration = function () {
        return this.duration + this.fadeOutSpeed;
    };

    function createTorpedoParticles(size, color, position) {

        var particle;
        var amount = Math.ceil(Math.random() * 3) + 4;
        while (amount--) {

            var angle = Math.random() * 360;
            particle = this.emitterContainer.getParticle(this);
            particle.setSize(size * 0.5).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(0.3).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.bolt).setColor(color).setAngle(angle).setAngleChange(0.4);

            particle = this.emitterContainer.getParticle(this);
            particle.setSize(size * 1.2).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(0.2).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.bolt).setColor(color).setAngle(angle).setAngleChange(0.4);
        }

        particle = this.emitterContainer.getParticle(this);
        particle.setSize(size).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(0.5).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.glow).setColor(color);

        particle = this.emitterContainer.getParticle(this);
        particle.setSize(size / 4).setFadeIn(this.time, this.fadeInSpeed).setFadeOut(this.time + this.duration, this.fadeOutSpeed).setOpacity(1.0).setActivationTime(this.time).setVelocity(this.speedVector).setPosition(position).setTexture(BaseParticle.prototype.texture.glow).setColor(color);
    }

    return TorpedoEffect;
}();
*/