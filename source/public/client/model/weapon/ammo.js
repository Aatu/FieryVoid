var Ammo = function()
{
    this.range = 0;
    this.hitChanceMod = 0;
    this.damage = 0;
}

Ammo.prototype = {
    
	constructor: Ammo,
            
    getRange: function(){ return this.range; },
    getHitChanceMod: function(){ return this.hitChanceMod; },
    getDamage: function(){ return this.damage; }
    
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
}
MissileFB.prototype = Object.create( Ammo.prototype );
MissileFB.prototype.constructor = MissileFB;
