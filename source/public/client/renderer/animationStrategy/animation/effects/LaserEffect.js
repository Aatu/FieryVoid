window.LaserEffect = (function() {
    function LaserEffect(shooter, target, scene, args) {
        Animation.call(this);

        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.duration = args.duration || 2000;

        this.color = args.color || new THREE.Color(0, 0, 0);
        this.shooter = shooter;
        this.target = target;
        this.scene = scene;
        this.particleEmitter = new ParticleEmitter(scene);

        this.fadeInSpeed = Math.random()*250;
        this.fadeOutSpeed = Math.random()*500 + 500;
        this.pulsatingFactor = Math.random() * 100 + 100;
        this.offset = {x: Math.random() * 0.02, y: Math.random() * 0.02};

        this.lasers = [
            createLaser.call(this, this.color, 0.5, 10),
            createLaser.call(this, new THREE.Color(1, 1, 1), 0.6, 3)
        ];
        this.lasers.forEach(function (laser) {
            laser.multiplyOpacity(0);
            this.scene.add(laser.mesh);
        }, this);
        this.scene.add(this.particleEmitter.mesh)
    }

    LaserEffect.prototype = Object.create(Animation.prototype);


    LaserEffect.prototype.cleanUp = function () {
        this.lasers.forEach(function (laser) {
            this.scene.remove(laser.mesh);
        }, this);

        this.scene.remove(this.particleEmitter.mesh);
    };

    LaserEffect.prototype.render = function (now, total, last, delta) {

        if (total < this.time || total > this.time + this.duration + this.fadeOutSpeed) {
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

        var startAndEnd = getStartAndEnd.call(this, {x: this.offset.x * elapsedTime, y: this.offset.x * elapsedTime});
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

    function getStartAndEnd(offset) {

        if (!offset) {
            offset = {x: 0, y: 0};
        }

        var endPosition = this.target instanceof ShipIcon ? this.target.getPosition() : this.target;
        var start = this.shooter.getPosition();
        var end =  {x: endPosition.x + offset.x, y: endPosition.y + offset.y};
        return {start: start, end: end}
    }

    return LaserEffect;
})();