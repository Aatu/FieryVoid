var Aoe = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Aoe.prototype = Object.create( Weapon.prototype );
Aoe.prototype.constructor = Aoe;


var EnergyMine = function(json, ship)
{
    Aoe.call( this, json, ship);
}
EnergyMine.prototype = Object.create( Aoe.prototype );
EnergyMine.prototype.constructor = EnergyMine;

