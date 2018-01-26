window.Explosion = (function(){

    function Explosion(emitterContainer, args)
    {
        if ( ! args)
            args = {};

        this.position = args.position || {x: 0, y: 0};
        this.time = args.time || 0;
        this.type = args.type || 'gas';
        this.size = args.size || 16;
        this.speed = args.speed || 1;
        this.ring = args.ring || false;
        this.movement = args.movement || {x:0, y:0};

        ParticleAnimation.call(this, emitterContainer, args.seed);
    }

    Explosion.prototype =  Object.create(ParticleAnimation.prototype);

    Explosion.prototype.create = function()
    {
        ParticleAnimation.prototype.create.call(this);

        switch (this.type)
        {
            case 'gas':
                this.createGas();
                break;

            case 'glow':
                this.createGlow();
                break;

            case 'emp':
                this.createEMP();
                break;

            case 'pillar':
                this.createPillar();
                break;

            default:
                this.createGas();
                break;
        }
    };

    Explosion.prototype.createGlow = function()
    {
        //if ( this.ring)
        //	this.createRing(this.size, emitter);

        this.createMainGlow(Math.ceil(this.size/8), this.size);
        this.createCore(this.size, BaseParticle.prototype.texture.glow);
        this.createCore(this.size, BaseParticle.prototype.texture.glow);
        this.createCore(this.size, BaseParticle.prototype.texture.glow);
    };


    Explosion.prototype.createGas = function()
    {
        //if ( this.ring)
        //	this.createRing(this.size, emitter);

        var amount = Math.ceil(this.size/4);
        if (amount > 6) {
            amount = 6;
        }

        var shootoffs = Math.ceil(Math.random()*20 + 10);

        this.createShootOffs(shootoffs, this.size);
        this.createMainGlow(Math.ceil(amount/2), this.size);
        this.createCore(this.size, BaseParticle.prototype.texture.glow);
        this.createCore(this.size/2, BaseParticle.prototype.texture.glow);
        this.createCore(this.size/4, BaseParticle.prototype.texture.glow);
        this.createMain(amount, this.size);
        //this.createCore(this.size, BaseParticle.prototype.texture.gas);
    };

    Explosion.prototype.createPillar = function()
    {
        //if ( this.ring)
        //	this.createRing(this.size, emitter);

        var amount = Math.ceil(this.size/4);
        if (amount > 6) {
            amount = 6;
        }

        var shootoffs = Math.ceil(Math.random()*20 + 10);

        this.createPillars(shootoffs, this.size);
        this.createMainGlow(Math.ceil(amount/2), this.size);
        this.createCore(this.size, BaseParticle.prototype.texture.glow);
        this.createCore(this.size/2, BaseParticle.prototype.texture.glow);
        this.createCore(this.size/4, BaseParticle.prototype.texture.glow);
        this.createMain(amount, this.size);
        //this.createCore(this.size, BaseParticle.prototype.texture.gas);
    };

    Explosion.prototype.createEMP = function()
    {
        //if ( this.ring)
        //	this.createRing(this.size, emitter);

        var amount = Math.ceil(this.size/4);
        if (amount > 6) {
            amount = 6;
        }

        //this.createShootOffs(Math.ceil(Math.random()*this.size/8 + this.size/8), this.size);
        this.createMainGlow(Math.ceil(amount/2), this.size);
        //this.createMain(amount, this.size);
        this.createCore(this.size, BaseParticle.prototype.texture.gas);
    };

    Explosion.prototype.createRing = function(size)
    {

        var step = 360 / size;
        var steps = Math.ceil(size);

        var distance = size * 3;
        var activation = this.time + Math.floor(Math.random()*300/this.speed);
        var fadeOutAt = activation;
        var fadeOutSpeed = Math.random()*1000 + 1000;
        //amount = 1;
        while (steps--)
        {
            var angle = steps * step;
            var particle = this.emitterContainer.getParticle(this);
            var target = mathlib.getPointInDirection(distance + Math.random()*30, angle, 0, 0);

            particle
                .setSize(Math.random() * 50 + 100)
                //.setSizeChange(128)
                .setOpacity(Math.random() * 0.2 + 0.3)
                .setFadeIn(activation, 1000)
                .setFadeOut(fadeOutAt, fadeOutSpeed)
                .setColor(this.getSmokeColor())
                .setPosition({x:this.position.x, y:this.position.y})
                .setVelocity(target)
                .setAngle(angle)
                .setTexture(BaseParticle.prototype.texture.glow)
                .setActivationTime(activation);

        }
    };

    Explosion.prototype.createShootOffs = function(amount, radius, args)
    {
        while (amount--) {
            this.createShootOff(radius, args)
        }
    };

    Explosion.prototype.createShootOff = function(radius, args, noAdditional)
    {
        if (! args) {
            args = {};
        }

        var size = radius;
        if (size < 40) {
            size = 40;
        }
        //amount = 1;

        var particle = this.emitterContainer.getParticle(this);
        var activationTime = this.time + Math.floor(Math.random()*30/this.speed);
        var fadeInSpeed = args.fadeInSpeed || Math.random()*50 + 25;
        var fadeOutAt = args.fadeOutAt || activationTime + Math.floor(Math.random()*50/this.speed);
        var fadeOutSpeed =  args.fadeOutSpeed || Math.random()*500/this.speed + 250/this.speed;
        var angle = args.angle || Math.floor(Math.random()*360);
        var speed = (Math.random()*0.2 + 0.1) * this.speed;

        var target = args.target || mathlib.getPointInDirection(speed, angle, 0, 0, true);
        var position = args.position || mathlib.getPointInDirection(size/2, angle, this.position.x, this.position.y, true);
        var color = args.color || getYellowColor();

        particle
            .setSize(Math.floor(Math.random()*size/2) + size/2)
            .setOpacity(Math.random() * 0.2 + 0.2)
            .setFadeIn(activationTime, fadeInSpeed)
            .setFadeOut(fadeOutAt, fadeOutSpeed)
            .setColor(color)
            .setPosition({x:position.x, y:position.y})
            .setVelocity(target)
            .setAngle(angle)
            .setTexture(BaseParticle.prototype.texture.bolt)
            .setActivationTime(activationTime);

        if (noAdditional) {
            return;
        }

        this.createShootOff(
            radius/2,
            {
                activationTime: activationTime,
                fadeInSpeed: fadeInSpeed,
                fadeOutAt: fadeOutAt,
                fadeOutSpeed: fadeOutSpeed,
                angle: angle,
                target: target,
                position: mathlib.getPointInDirection(size/2 + size*0.10, angle, this.position.x, this.position.y, true),
                color: {r:1, g:1, b: 1}
            },
            true
        );

        this.createShootOff(
            radius/2,
            {
                activationTime: activationTime,
                fadeInSpeed: fadeInSpeed,
                fadeOutAt: fadeOutAt,
                fadeOutSpeed: fadeOutSpeed,
                angle: angle,
                target: target,
                position: mathlib.getPointInDirection(size/2 + size*0.10, angle, this.position.x, this.position.y, true),
                color: {r:1, g:1, b: 1}
            },
            true
        );

    };

    Explosion.prototype.createPillars = function(amount, radius)
    {
        var size = radius;
        //amount = 1;
        while (amount--)
        {
            var particle = this.emitterContainer.getParticle(this);
            var activationTime = this.time + Math.floor(Math.random()*30/this.speed);
            var fadeOutAt = activationTime + Math.floor(Math.random()*50/this.speed);

            var angle = Math.floor(Math.random()*360);
            var target = mathlib.getPointInDirection(0.1, angle, 0, 0);

            particle
                .setSize(Math.floor(Math.random()*size) + size/2)
                .setOpacity(Math.random() * 0.1 + 0.9)
                .setFadeIn(activationTime, Math.random()*50 + 25)
                .setFadeOut(fadeOutAt, Math.random()*500/this.speed + 250/this.speed)
                .setColor(getYellowColor())
                .setPosition({x:this.position.x, y:this.position.y})
                .setVelocity(target)
                .setAngle(angle)
                .setTexture(BaseParticle.prototype.texture.bolt)
                .setActivationTime(activationTime);

        }
    };


    Explosion.prototype.createCore = function(radius, texture)
    {
        var size = radius;

        var particle = this.emitterContainer.getParticle(this);
        var activationTime = this.time + Math.random()*0.005/this.speed;
        var fadeOutAt = activationTime + (Math.random()*0.02/this.speed) + 0.03/this.speed;

        particle
            .setSize(Math.floor(Math.random()*size) + size/2)
            .setOpacity(Math.random() * 0.2 + 0.6)
            .setFadeIn(activationTime, Math.random()*0.005 + 0.0025)
            //.setFadeOut(fadeOutAt, Math.random()*0.05/this.speed + 0.025/this.speed)
            .setColor(getCoreColor())
            .setPosition({
                x: this.position.x, // + Math.floor(Math.random()*radius/10)-radius/5,
                y: this.position.y // + Math.floor(Math.random()*radius/10)-radius/5,
            })
            .setAngle(45)
            .setTexture(texture)
            .setVelocity(this.movement)
            .setAngle(Math.floor(Math.random()*360))
            .setAngleChange(Math.floor(Math.random()*20*this.speed)-10*this.speed)
            .setActivationTime(activationTime);


    };


    Explosion.prototype.createMain = function(amount, radius)
    {
        var size = radius;
        while (amount--)
        {
            var particle = this.emitterContainer.getParticle(this);
            var activationTime = this.time + Math.floor(Math.random()*300/this.speed);
            var fadeOutAt = activationTime + Math.floor(Math.random()*500/this.speed);

            particle
                .setSize(Math.floor(Math.random()*size) + size/2)
                .setOpacity(Math.random() * 0.1 + 0.05)
                .setFadeIn(activationTime, Math.random()*50 + 25)
                .setFadeOut(fadeOutAt, Math.random()*500/this.speed + 250/this.speed)
                .setColor(this.getRandomColor())
                .setPosition({
                    x: this.position.x + (Math.floor(Math.random()*radius)-radius)/4,
                    y: this.position.y + (Math.floor(Math.random()*radius)-radius)/4,
                })
                .setVelocity(this.movement)
                .setAngle(Math.floor(Math.random()*360))
                .setAngleChange(Math.floor(Math.random()*20*this.speed)-10*this.speed)
                .setActivationTime(activationTime)
                .setTexture(BaseParticle.prototype.texture.gas);

        }
    };

    Explosion.prototype.createMainGlow = function(amount, radius)
    {
        var size = radius*2;
        while (amount--)
        {
            var particle = this.emitterContainer.getParticle(this);
            var activationTime = this.time + Math.floor(Math.random()*0.03/this.speed);
            var fadeOutAt = activationTime + Math.floor(Math.random()*0.05/this.speed);

            particle
                .setSize(Math.floor(Math.random()*size) + size/2)
                .setOpacity(Math.random() * 0.1 + 0.4)
                .setFadeIn(activationTime, Math.random()*0.005 + 0.0025)
                .setFadeOut(fadeOutAt, (Math.random()*200 + 800) / this.speed)
                .setColor(this.getRandomColor())
                .setVelocity(this.movement)
                .setPosition({x:this.position.x, y:this.position.y})
                .setTexture(BaseParticle.prototype.texture.glow)
                .setActivationTime(activationTime);

        }
    };

    function getCoreColor()
    {
        return new THREE.Color().setRGB(
            255,
            255,
            255
        );
    }

    function getYellowColor()
    {
        return new THREE.Color().setRGB(
            1,
            (Math.floor(Math.random()*20) + 235) / 255,
            (Math.floor(Math.random()*10) + 130) / 255
        );
    }

    return Explosion;
})();

