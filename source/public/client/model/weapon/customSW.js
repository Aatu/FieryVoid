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

