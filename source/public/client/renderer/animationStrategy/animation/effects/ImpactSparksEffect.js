"use strict";

window.ImpactSparksEffect = (function () {

    function ImpactSparksEffect(scene, args) {
        args = args || {};
        this.position = args.position || { x: 0, y: 0 };
        this.time = args.time || 0;
        this.color = args.color || new THREE.Color(1, 1, 1);
        this.emitterContainer = new ParticleEmitterContainer(scene, 30);
        this.spawnSparks();
    }

    ImpactSparksEffect.prototype.spawnSparks = function () {
        var scale = 1.0; //Maybe change
        var sparkColor = new THREE.Color(
            Math.min(1, this.color.r * scale),
            Math.min(1, this.color.g * scale),
            Math.min(1, this.color.b * scale)
        );

        var amount = Math.floor(Math.random() * 8) + 8; // 8-15 particles
        var t = this.time;

        while (amount--) {
            var particle = this.emitterContainer.getParticle(this);
            var angle = Math.random() * 360;
            var speed = Math.random() * 0.045 + 0.045;  // ~0.045-0.09 game units/ms
            var velocity = mathlib.getPointInDirection(speed, -angle, 0, 0, true);
            var size = Math.random() * 10 + 10;          // 10-20
            var fadeOutDelay = Math.random() * 100;       // stagger fade start
            var duration = Math.random() * 150 + 250;    // 250-400ms

            particle
                .setSize(size)
                .setOpacity(Math.random() * 0.4 + 0.6)
                .setFadeIn(t, 20)
                .setFadeOut(t + fadeOutDelay, duration)
                .setColor(sparkColor)
                .setPosition({ x: this.position.x, y: this.position.y })
                .setVelocity(velocity)
                .setAngle(angle)
                .setTexture(BaseParticle.prototype.texture.bolt)
                .setActivationTime(t);
        }
    };

    ImpactSparksEffect.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        this.emitterContainer.render(now, total, last, delta, zoom, back, paused);
    };

    ImpactSparksEffect.prototype.cleanUp = function () {
        this.emitterContainer.cleanUp();
    };

    return ImpactSparksEffect;
})();
