window.TorpedoEffect = (function() {
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
                size: 24,
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

        createTorpedoParticles.call(this, this.size, this.color, this.origin);
    }

    TorpedoEffect.prototype = Object.create(ParticleAnimation.prototype);

    TorpedoEffect.prototype.getDuration = function() {
        return this.duration + this.fadeOutSpeed;
    };

    function createTorpedoParticles(size, color, position) {

        var particle;
        var amount = Math.ceil(Math.random()*3) + 4;
        while (amount--) {

            var angle = Math.random()*360;
            particle = this.emitterContainer.getParticle(this);
            particle
                .setSize(size*0.7)
                .setFadeIn(this.time, this.fadeInSpeed)
                .setFadeOut(this.time + this.duration, this.fadeOutSpeed)
                .setOpacity(0.3)
                .setActivationTime(this.time)
                .setVelocity(this.speedVector)
                .setPosition(position)
                .setTexture(BaseParticle.prototype.texture.bolt)
                .setColor(color)
                .setAngle(angle)
                .setAngleChange(0.4);

            particle = this.emitterContainer.getParticle(this);
            particle
                .setSize(size*1.5)
                .setFadeIn(this.time, this.fadeInSpeed)
                .setFadeOut(this.time + this.duration, this.fadeOutSpeed)
                .setOpacity(0.2)
                .setActivationTime(this.time)
                .setVelocity(this.speedVector)
                .setPosition(position)
                .setTexture(BaseParticle.prototype.texture.bolt)
                .setColor(color)
                .setAngle(angle)
                .setAngleChange(0.4);
        }

        particle = this.emitterContainer.getParticle(this);
        particle
            .setSize(size)
            .setFadeIn(this.time, this.fadeInSpeed)
            .setFadeOut(this.time + this.duration, this.fadeOutSpeed)
            .setOpacity(0.5)
            .setActivationTime(this.time)
            .setVelocity(this.speedVector)
            .setPosition(position)
            .setTexture(BaseParticle.prototype.texture.glow)
            .setColor(color);

        particle = this.emitterContainer.getParticle(this);
        particle
            .setSize(size/4)
            .setFadeIn(this.time, this.fadeInSpeed)
            .setFadeOut(this.time + this.duration, this.fadeOutSpeed)
            .setOpacity(1.0)
            .setActivationTime(this.time)
            .setVelocity(this.speedVector)
            .setPosition(position)
            .setTexture(BaseParticle.prototype.texture.glow)
            .setColor({r: 1, g: 1, b:1});
    }

    return TorpedoEffect;
})();