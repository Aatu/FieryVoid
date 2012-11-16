var TractorBeam = function(json, ship)
{
    Weapon.call( this, json, ship);
}
TractorBeam.prototype = Object.create( Weapon.prototype );
TractorBeam.prototype.constructor = TractorBeam;