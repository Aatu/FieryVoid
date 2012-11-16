var Electromagnetic = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Electromagnetic.prototype = Object.create( Weapon.prototype );
Electromagnetic.prototype.constructor = Electromagnetic;


var BurstBeam = function(json, ship)
{
    Electromagnetic.call( this, json, ship);
}
BurstBeam.prototype = Object.create( Electromagnetic.prototype );
BurstBeam.prototype.constructor = BurstBeam;


var ShockCannon = function(json, ship)
{
    Electromagnetic.call( this, json, ship);
}
ShockCannon.prototype = Object.create( Electromagnetic.prototype );
ShockCannon.prototype.constructor = ShockCannon;


var ElectroPulseGun = function(json, ship)
{
    Electromagnetic.call( this, json, ship);
}
ElectroPulseGun.prototype = Object.create( Electromagnetic.prototype );
ElectroPulseGun.prototype.constructor = ElectroPulseGun;


