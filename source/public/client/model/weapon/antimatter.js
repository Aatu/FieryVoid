var Antimatter = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Antimatter.prototype = Object.create( Weapon.prototype );
Antimatter.prototype.constructor = Antimatter;


var AntimatterConverter = function(json, ship)
{
    Antimatter.call( this, json, ship);
}
AntimatterConverter.prototype = Object.create( Antimatter.prototype );
AntimatterConverter.prototype.constructor = AntimatterConverter;

