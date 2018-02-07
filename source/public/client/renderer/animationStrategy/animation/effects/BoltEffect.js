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


        if (this.hit) {
            this.duration -= 25;

            new Explosion(this.emitterContainer, {
                size: 12,
                position: this.target,
                type: "glow",
                color: args.color,
                time: this.time +  this.duration
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
        createBoltParticle.call(this, this.size/2, {r:1, g:1, b:1}, mathlib.getPointInDirection(this.size / 4.5, -this.angle, this.origin.x, this.origin.y, true));
    }

    BoltEffect.prototype = Object.create(ParticleAnimation.prototype);

    BoltEffect.prototype.getDuration = function() {
        return this.duration + this.fadeOutSpeed;
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
})();