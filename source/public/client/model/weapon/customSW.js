var SWFighterLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWFighterLaser.prototype = Object.create( Weapon.prototype );
SWFighterLaser.prototype.constructor = SWFighterLaser;


var SWFighterIon = function(json, ship){
    Weapon.call( this, json, ship);
}
SWFighterIon.prototype = Object.create( Weapon.prototype );
SWFighterIon.prototype.constructor = SWFighterIon;


var SWFtrProtonTorpedo = function(json, ship)
{
    Ammo.call( this, json, ship);
    this.range = 15;
    this.hitChanceMod = 0;
}
SWFtrProtonTorpedo.prototype = Object.create( Ammo.prototype );
SWFtrProtonTorpedo.prototype.constructor = SWFtrProtonTorpedo;


var SWFtrProtonTorpedoLauncher = function(json, ship)
{
    FighterMissileRack.call( this, json, ship);
}
SWFtrProtonTorpedoLauncher.prototype = Object.create( FighterMissileRack.prototype );
SWFtrProtonTorpedoLauncher.prototype.constructor = SWFtrProtonTorpedoLauncher;


var SWLightLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWLightLaser.prototype = Object.create( Weapon.prototype );
SWLightLaser.prototype.constructor = SWLightLaser;

var SWMediumLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWMediumLaser.prototype = Object.create( Weapon.prototype );
SWMediumLaser.prototype.constructor = SWMediumLaser;

var SWHeavyLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWHeavyLaser.prototype = Object.create( Weapon.prototype );
SWHeavyLaser.prototype.constructor = SWHeavyLaser;


var SWLightTLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWLightTLaser.prototype = Object.create( Weapon.prototype );
SWLightTLaser.prototype.constructor = SWLightTLaser;

var SWMediumTLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWMediumTLaser.prototype = Object.create( Weapon.prototype );
SWMediumTLaser.prototype.constructor = SWMediumTLaser;

var SWHeavyTLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWHeavyTLaser.prototype = Object.create( Weapon.prototype );
SWHeavyTLaser.prototype.constructor = SWHeavyTLaser;



var SWLightIon = function(json, ship){
    Weapon.call( this, json, ship);
}
SWLightIon.prototype = Object.create( Weapon.prototype );
SWLightIon.prototype.constructor = SWLightIon;

var SWMediumIon = function(json, ship){
    Weapon.call( this, json, ship);
}
SWMediumIon.prototype = Object.create( Weapon.prototype );
SWMediumIon.prototype.constructor = SWMediumIon;

var SWHeavyTLaser = function(json, ship){
    Weapon.call( this, json, ship);
}
SWHeavyIon.prototype = Object.create( Weapon.prototype );
SWHeavyIon.prototype.constructor = SWHeavyIon;
