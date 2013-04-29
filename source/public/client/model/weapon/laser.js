var Laser = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Laser.prototype = Object.create( Weapon.prototype );
Laser.prototype.constructor = Laser;


var HeavyLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
HeavyLaser.prototype = Object.create( Laser.prototype );
HeavyLaser.prototype.constructor = HeavyLaser;


var MediumLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
MediumLaser.prototype = Object.create( Laser.prototype );
MediumLaser.prototype.constructor = MediumLaser;

var LightLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
LightLaser.prototype = Object.create( Laser.prototype );
LightLaser.prototype.constructor = LightLaser;


var BattleLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
BattleLaser.prototype = Object.create( Laser.prototype );
BattleLaser.prototype.constructor = BattleLaser;

var AssaultLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
AssaultLaser.prototype = Object.create( Laser.prototype );
AssaultLaser.prototype.constructor = AssaultLaser;

var AdvancedAssaultLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
AdvancedAssaultLaser.prototype = Object.create( Laser.prototype );
AdvancedAssaultLaser.prototype.constructor = AdvancedAssaultLaser;

var NeutronLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
NeutronLaser.prototype = Object.create( Laser.prototype );
NeutronLaser.prototype.constructor = NeutronLaser;


var ImprovedNeutronLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
ImprovedNeutronLaser.prototype = Object.create( Laser.prototype );
ImprovedNeutronLaser.prototype.constructor = ImprovedNeutronLaser;

var AssaultLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
AssaultLaser.prototype = Object.create( Laser.prototype );
AssaultLaser.prototype.constructor = AssaultLaser;