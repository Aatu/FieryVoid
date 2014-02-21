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

var LightParticleCannon = function(json, ship)
{
    Particle.call( this, json, ship);
}
LightParticleCannon.prototype = Object.create(Particle.prototype);
LightParticleCannon.prototype.constructor = LightParticleCannon;

var ParticleCutter = function(json, ship)
{
    Particle.call( this, json, ship);
}
ParticleCutter.prototype = Object.create(Particle.prototype);
ParticleCutter.prototype.constructor = ParticleCutter;

var SolarCannon = function(json, ship)
{
    Particle.call( this, json, ship);
}
SolarCannon.prototype = Object.create(Particle.prototype);
SolarCannon.prototype.constructor = SolarCannon;

var LightParticleBlaster = function(json, ship)
{
    Particle.call( this, json, ship);
}
LightParticleBlaster.prototype = Object.create( Particle.prototype );
LightParticleBlaster.prototype.constructor = LightParticleBlaster;

var LightParticleBeam = function(json, ship)
{
    Particle.call( this, json, ship);
}
LightParticleBeam.prototype = Object.create( Particle.prototype );
LightParticleBeam.prototype.constructor = LightParticleBeam;

var ParticleRepeater = function(json, ship)
{
    Particle.call( this, json, ship);
}
ParticleRepeater.prototype = Object.create(Particle.prototype);
ParticleRepeater.prototype.constructor = ParticleRepeater;

ParticleRepeater.prototype.initBoostableInfo = function(){
    // Needed because it can chance during initial phase
    // because of adding extra power.
    
    this.data["Weapon type"] ="Particle";
    this.data["Damage type"] ="Standard";
    this.data["Number of shots"] = shipManager.power.getBoost(this) + 1;

    if(window.weaponManager.isLoaded(this)){
        this.loadingtime = this.getLoadingTime();
        this.turnsloaded = this.getLoadingTime();
        this.normalload =  this.getLoadingTime();
    }
    else{
        var count = shipManager.power.getBoost(this);
        
        for(var i = 0; i < count; i++){
            shipManager.power.unsetBoost(null, this);
        }
    }
    
    return this;
}

ParticleRepeater.prototype.clearBoost = function(){
        for (var i in system.power){
                var power = system.power[i];
                if (power.turn != gamedata.turn)
                        continue;

                if (power.type == 2){
                    system.power.splice(i, 1);

                    return;
                }
        }
}

ParticleRepeater.prototype.getLoadingTime = function(){
    var boostAmount = shipManager.power.getBoost(this);
    
    return 1 + Math.floor(boostAmount/2);
}

var RepeaterGun = function(json, ship)
{
    ParticleRepeater.call( this, json, ship);
}
RepeaterGun.prototype = Object.create(ParticleRepeater.prototype);
RepeaterGun.prototype.constructor = RepeaterGun;


RepeaterGun.prototype.getLoadingTime = function(){
    var boostAmount = shipManager.power.getBoost(this);
    
    return 1 + boostAmount;
}

var HeavyBolter = function(json, ship)
{
    Particle.call( this, json, ship);
}
HeavyBolter.prototype = Object.create(Particle.prototype);
HeavyBolter.prototype.constructor = HeavyBolter;

var MediumBolter = function(json, ship)
{
    Particle.call( this, json, ship);
}
MediumBolter.prototype = Object.create(Particle.prototype);
MediumBolter.prototype.constructor = MediumBolter;

var LightBolter = function(json, ship)
{
    Particle.call( this, json, ship);
}
LightBolter.prototype = Object.create(Particle.prototype);
LightBolter.prototype.constructor = LightBolter;
