var Ion = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Ion.prototype = Object.create( Weapon.prototype );
Ion.prototype.constructor = Ion;


var IonBolt = function(json, ship)
{
    Ion.call( this, json, ship);
}
IonBolt.prototype = Object.create( Ion.prototype );
IonBolt.prototype.constructor = IonBolt;

