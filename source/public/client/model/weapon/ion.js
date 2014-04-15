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

var IonCannon = function(json, ship)
{
    Ion.call( this, json, ship);
}
IonCannon.prototype = Object.create( Ion.prototype );
IonCannon.prototype.constructor = IonCannon;

var ImprovedIonCannon = function(json, ship)
{
    Ion.call( this, json, ship);
}
ImprovedIonCannon.prototype = Object.create( Ion.prototype );
ImprovedIonCannon.prototype.constructor = ImprovedIonCannon;

