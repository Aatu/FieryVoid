var Particle = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Particle.prototype = Object.create( Weapon.prototype );
Particle.prototype.constructor = Particle;


var TwinArray = function(json, ship)
{
    Particle.call( this, json, ship);
}
TwinArray.prototype = Object.create( Particle.prototype );
TwinArray.prototype.constructor = TwinArray;

var HeavyArray = function(json, ship)
{
    Particle.call( this, json, ship);
}
HeavyArray.prototype = Object.create( Particle.prototype );
HeavyArray.prototype.constructor = HeavyArray;

var StdParticleBeam = function(json, ship)
{
    Particle.call( this, json, ship);
}
StdParticleBeam.prototype = Object.create( Particle.prototype );
StdParticleBeam.prototype.constructor = StdParticleBeam;

var AdvParticleBeam = function(json, ship)
{
    Particle.call( this, json, ship);
}
AdvParticleBeam.prototype = Object.create( Particle.prototype );
AdvParticleBeam.prototype.constructor = AdvParticleBeam;

var PairedParticleGun = function(json, ship)
{
    Particle.call( this, json, ship);
}
PairedParticleGun.prototype = Object.create( Particle.prototype );
PairedParticleGun.prototype.constructor = PairedParticleGun;

var GuardianArray = function(json, ship)
{
    Particle.call( this, json, ship);
}
GuardianArray.prototype = Object.create( Particle.prototype );
PairedParticleGun.prototype.constructor = GuardianArray;

var ParticleCannon = function(json, ship)
{
    Particle.call( this, json, ship);
}
ParticleCannon.prototype = Object.create(Particle.prototype);
ParticleCannon.prototype.constructor = ParticleCannon;

var ParticleBlaster = function(json, ship)
{
    Particle.call( this, json, ship);
}
ParticleBlaster.prototype = Object.create(Particle.prototype);
ParticleBlaster.prototype.constructor = ParticleBlaster;

var HvyParticleCannon = function(json, ship)
{
    Particle.call( this, json, ship);
}
HvyParticleCannon.prototype = Object.create(Particle.prototype);
HvyParticleCannon.prototype.constructor = HvyParticleCannon;

var ParticleCutter = function(json, ship)
{
    Particle.call( this, json, ship);
}
ParticleCutter.prototype = Object.create(Particle.prototype);
ParticleCutter.prototype.constructor = ParticleCutter;

var ParticleRepeater = function(json, ship)
{
    Particle.call( this, json, ship);
}
ParticleRepeater.prototype = Object.create(Particle.prototype);
ParticleRepeater.prototype.constructor = ParticleRepeater;
