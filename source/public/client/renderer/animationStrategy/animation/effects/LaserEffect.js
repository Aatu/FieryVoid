window.LaserEffect = (function() {
    function LaserEffect(shooter, target, scene, args) {
        Animation.call(this);

        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.duration = args.duration || 2000;
        this.hit = args.hit || false;

        this.color = args.color || new THREE.Color(0, 0, 0);
        this.shooter = shooter;
        this.target = target;
        if (!this.hit) {
            var targetPosition = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
            this.target = mathlib.getPointBetween(this.shooter.getPosition(), targetPosition, Math.random() * 0.1 + 1.1);
        }

        this.scene = scene;

        this.fadeInSpeed = Math.random()*250;
        this.fadeOutSpeed = Math.random()*500 + 500;
        this.pulsatingFactor = Math.random() * 100 + 100;
        var offsetVelocityFactor = this.hit ? 1 : 10;
        this.offsetVelocity = {
            x: Math.random() * 0.01 * offsetVelocityFactor - 0.005 * offsetVelocityFactor,
            y: Math.random() * 0.01 * offsetVelocityFactor - 0.005 * offsetVelocityFactor
        };

        this.lasers = [
            createLaser.call(this, this.color, 0.5, 10),
            createLaser.call(this, new THREE.Color(1, 1, 1), 0.6, 3)
        ];
        this.lasers.forEach(function (laser) {
            laser.multiplyOpacity(0);
            this.scene.add(laser.mesh);
        }, this);


        this.particleEmitter = new ParticleEmitterContainer(scene);

        new Explosion(this.particleEmitter, {
            size: 20, type: "glow"
        });

        if (this.hit) {
            new Explosion(this.particleEmitter, {
                size: 200,
                position: {x:0, y:0},
                type: "glow",
                time: this.time
            });
        }
    }

    LaserEffect.prototype = Object.create(Animation.prototype);


    LaserEffect.prototype.cleanUp = function () {
        this.lasers.forEach(function (laser) {
            this.scene.remove(laser.mesh);
        }, this);

        this.particleEmitter.cleanUp();
    };

    LaserEffect.prototype.render = function (now, total, last, delta) {

        this.particleEmitter.render(now, total, last, delta);
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
        }else if (total < this.time + this.duration + this.fadeOutSpeed) {
            fadeoutFactor = (total - (this.time + this.duration)) / this.fadeOutSpeed;
            opacity = 1 - ((total - (this.time + this.duration)) / this.fadeOutSpeed);
        }


        var pulseFrequency = this.pulsatingFactor - (this.pulsatingFactor * 0.9 * fadeoutFactor);
        var pulseIntensity = 0.001 * (10 * fadeoutFactor + 1);

        var pulse = 1 - ((total % pulseFrequency) * pulseIntensity);

        opacity *= pulse;

        var elapsedTime = total - this.time;

        var startAndEnd = getStartAndEnd.call(this, {x: this.offsetVelocity.x * elapsedTime, y: this.offsetVelocity.x * elapsedTime});
        this.lasers.forEach(function (laser) {
            laser.multiplyOpacity(opacity);
            laser.setStartAndEnd(startAndEnd.start, startAndEnd.end);
        }, this);
    };

    function createLaser(color, opacity, widht) {
        var startAndEnd = getStartAndEnd.call(this);
        return new LineSprite(
            startAndEnd.start,
            startAndEnd.end,
            widht,
            1,
            color,
            opacity,
            {
                blending: THREE.AdditiveBlending,
                texture: new THREE.TextureLoader().load("img/effect/laser16.png")
            }
        );
    }

    function getStartAndEnd(offsetVelocity) {

        if (!offsetVelocity) {
            offsetVelocity = {x: 0, y: 0};
        }

        var endPosition = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
        var start = this.shooter.getPosition();
        var end =  {x: endPosition.x + offsetVelocity.x, y: endPosition.y + offsetVelocity.y};
        return {start: start, end: end}
    }

    return LaserEffect;
})();