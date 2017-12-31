var TractorBeam = function(json, ship)
{
    Weapon.call( this, json, ship);
}
TractorBeam.prototype = Object.create( Weapon.prototype );
TractorBeam.prototype.constructor = TractorBeam;


var CommDisruptor = function(json, ship)
{
    Weapon.call( this, json, ship);
}
CommDisruptor.prototype = Object.create( Weapon.prototype );
CommDisruptor.prototype.constructor = CommDisruptor;
