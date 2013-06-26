var DualWeapon = function(json, ship)
{
    Weapon.call( this, json, ship);
}
DualWeapon.prototype = Object.create( Weapon.prototype );
DualWeapon.prototype.constructor = DualWeapon;


var LaserPulseArray = function(json, ship)
{
    DualWeapon.call( this, json, ship);
}
LaserPulseArray.prototype = Object.create( DualWeapon.prototype );
LaserPulseArray.prototype.constructor = LaserPulseArray;

var GraviticLance = function(json, ship)
{
    DualWeapon.call( this, json, ship);
}
GraviticLance.prototype = Object.create( DualWeapon.prototype );
GraviticLance.prototype.constructor = GraviticLance;