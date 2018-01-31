window.BoltEffect = (function() {
    function BoltEffect(emitterContainer, args) {
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
            x: args.target.x + Math.random() * 30 - 15,
            y: args.target.y + Math.random() * 30 - 15
        };

        this.size = args.size || 100;
        this.angle = -mathlib.getCompassHeadingOfPoint(this.origin, this.target);

        var distance = mathlib.distance(this.origin, this.target);
        if (!this.hit) {
            var missFactor = distance / 1500;
            this.angle = mathlib.addToDirection(this.angle, Math.random() * missFactor - missFactor/2);
            distance += Math.random() * 400 + 100;
        }
        this.speedVector = mathlib.getPointInDirection(this.speed, this.angle, 0, 0, true);


        this.duration = distance / this.speed;

        this.fadeInSpeed = 25;
        this.fadeOutSpeed = this.hit ? 0 : 1000;


        if (this.hit) {
            this.duration -= 25;

            new Explosion(this.emitterContainer, {
                size: 16,
                position: this.target,
                type: "glow",
                color: args.color,
                time: this.time +  this.duration,
            });


            new Explosion(this.emitterContainer, {
                size: 16,
                position: this.target,
                type: ["gas", "pillar"][Math.round(Math.random()*2)],
                time: this.time +  this.duration
            });

        }

        createBoltParticle.call(this, this.size, this.color);
        createBoltParticle.call(this, this.size/2, {r:1, g:1, b:1});
    }

    function createBoltParticle(size) {

        var particle = this.emitterContainer.getParticle(this);
        particle
            .setSize(size)
            .setFadeIn(this.time, this.fadeInSpeed)
            .setFadeOut(this.time + this.duration, this.fadeOutSpeed)
            .setOpacity(1.0)
            .setActivationTime(this.time)
            .setVelocity(this.speedVector)
            .setPosition(this.origin)
            .setTexture(BaseParticle.prototype.texture.bolt)
            .setColor(this.color)
            .setAngle(this.angle);
    }

    BoltEffect.prototype = Object.create(ParticleAnimation.prototype);

    return BoltEffect;
})();