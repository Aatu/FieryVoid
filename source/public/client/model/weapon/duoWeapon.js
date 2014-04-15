var DuoWeapon = function(json, ship)
{
    Weapon.call( this, json, ship);
}
DuoWeapon.prototype = Object.create( Weapon.prototype );
DuoWeapon.prototype.constructor = DuoWeapon;

var DuoGravitonBeam = function(json, ship)
{
    DuoWeapon.call( this, json, ship);
}

DuoGravitonBeam.prototype = Object.create( DuoWeapon.prototype );
DuoGravitonBeam.prototype.constructor = DuoGravitonBeam;

