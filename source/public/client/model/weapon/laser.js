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


var BattleLaser = function(json, ship)
{
    Laser.call( this, json, ship);
}
BattleLaser.prototype = Object.create( Laser.prototype );
BattleLaser.prototype.constructor = BattleLaser;


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