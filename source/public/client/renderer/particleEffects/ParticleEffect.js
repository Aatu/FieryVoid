window.ParticleEffect = (function(){
    function ParticleEffect(emitterType, seed, turn)
    {
        this.emitter = null;
        this.particles = [];
        this.seed = seed || Math.random();
        this.turn = turn;

        this.emitterType = emitterType || 'normal';
    }


    ParticleEffect.prototype.getTurn = function()
    {
        if (this.turn === undefined)
            throw new Error("Particle effect needs a turn!");

        return this.turn;
    };

    ParticleEffect.prototype.destroy = function()
    {
        //this.emitter.freeParticles(this.particles);
        this.emitter.unregister(this);
        this.emitter = null;
        this.particles = [];
    };

    ParticleEffect.prototype.create = function(emitters)
    {
        Math.seedrandom(this.seed);
        this.emitter = emitters[this.emitterType];

        this._create();

        this.emitter.register(this);
    };

    ParticleEffect.prototype._getParticle = function()
    {
        var particle = this.emitter.getFreeParticle();
        this.particles.push(particle.index);

        return particle;
    };

    ParticleEffect.prototype._getColor = function(r,g,b)
    {
        return new THREE.Color().setRGB(
            r / 255,
            g / 255,
            b / 255
        );
    };

    ParticleEffect.prototype._getRandomColor = function()
    {
        return new THREE.Color().setRGB(
            1,
            (255 - Math.floor(Math.random()*255)) / 255,
            (Math.floor(Math.random()*155)) / 255
        );
    };

    return ParticleEffect;
})();


