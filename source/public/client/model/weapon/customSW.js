var SWFighterLaser = function(json, ship){
    Particle.call( this, json, ship);
}
SWFighterLaser.prototype = Object.create( Particle.prototype );
SWFighterLaser.prototype.constructor = SWFighterLaser;


var SWFighterIon = function(json, ship){
    Particle.call( this, json, ship);
}
SWFighterIon.prototype = Object.create( Particle.prototype );
SWFighterIon.prototype.constructor = SWFighterIon;


var SWFtrProtonTorpedo = function(json, ship)
{
    Ammo.call( this, json, ship);
    this.range = 15;
    this.hitChanceMod = 0;
}
SWFtrProtonTorpedo.prototype = Object.create( Ammo.prototype );
SWFtrProtonTorpedo.prototype.constructor = SWFtrProtonTorpedo;


