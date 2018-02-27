var Ammo = function(json, ship)
{
    this.range = 0;
    this.hitChanceMod = 0;
    this.damage = 0;
    
    Weapon.call( this, json, ship);
}
Ammo.prototype = Object.create( Weapon.prototype );
Ammo.prototype.constructor = Ammo;


Ammo.prototype.getRange = function(){
    return this.range;
}

Ammo.prototype.getHitChanceMod = function(){
    return this.hitChanceMod;
}

Ammo.prototype.getDamage = function(){
    return this.damage;
}

var BasicMissile = function()
{
    Ammo.call( this );
    this.range = 20;
    this.hitChanceMod = 3;
    this.damage = 20;
}
BasicMissile.prototype = Object.create( Ammo.prototype );
BasicMissile.prototype.constructor = BasicMissile;

var MissileFB = function(json, ship)
{
    Ammo.call( this, json, ship);
    this.range = 10;
    this.hitChanceMod = 0;
    this.damage = 10;
}
MissileFB.prototype = Object.create( Ammo.prototype );
MissileFB.prototype.constructor = MissileFB;

var MissileFY = function(json, ship)
{
    Ammo.call( this, json, ship);
    this.range = 8;
    this.hitChanceMod = 0;
    this.damage = 6;
}
MissileFY.prototype = Object.create( Ammo.prototype );
MissileFY.prototype.constructor = MissileFY;

var LightBallisticTorpedo = function(json, ship)
{
    Ammo.call( this, json, ship);
    this.range = 25;
    this.hitChanceMod = 0;
}
LightBallisticTorpedo.prototype = Object.create( Ammo.prototype );
LightBallisticTorpedo.prototype.constructor = LightBallisticTorpedo;

var LightIonTorpedo = function(json, ship)
{
    Ammo.call( this, json, ship);
    this.range = 20;
    this.hitChanceMod = 0;
}
LightIonTorpedo.prototype = Object.create( Ammo.prototype );
LightIonTorpedo.prototype.constructor = LightIonTorpedo;

