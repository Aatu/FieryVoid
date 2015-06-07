var Matter = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Matter.prototype = Object.create( Weapon.prototype );
Matter.prototype.constructor = Matter;


var MatterCannon = function(json, ship)
{
    Matter.call( this, json, ship);
}
MatterCannon.prototype = Object.create( Matter.prototype );
MatterCannon.prototype.constructor = MatterCannon;


var HeavyRailGun = function(json, ship)
{
    Matter.call( this, json, ship);
}
HeavyRailGun.prototype = Object.create( Matter.prototype );
HeavyRailGun.prototype.constructor = HeavyRailGun;


var RailGun = function(json, ship)
{
    Matter.call( this, json, ship);
}
RailGun.prototype = Object.create( Matter.prototype );
RailGun.prototype.constructor = RailGun;


var LightRailGun = function(json, ship)
{
    Matter.call( this, json, ship);
}
LightRailGun.prototype = Object.create( Matter.prototype );
LightRailGun.prototype.constructor = LightRailGun;

var MassDriver = function(json, ship)
{
    Matter.call( this, json, ship);
}
MassDriver.prototype = Object.create( Matter.prototype );
MassDriver.prototype.constructor = MassDriver;

var GaussCannon = function(json, ship)
{
    Matter.call( this, json, ship);
}
GaussCannon.prototype = Object.create( Matter.prototype );
GaussCannon.prototype.constructor = GaussCannon;

var RapidGatling = function(json, ship)
{
    Matter.call( this, json, ship);
}
RapidGatling.prototype = Object.create( Matter.prototype );
RapidGatling.prototype.constructor = RapidGatling;





