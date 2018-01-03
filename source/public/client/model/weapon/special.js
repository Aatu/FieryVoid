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

var CommJammer = function(json, ship)
{
    Weapon.call( this, json, ship);
}
CommJammer.prototype = Object.create( Weapon.prototype );
CommJammer.prototype.constructor = CommJammer;

var ImpCommJammer = function(json, ship)
{
    Weapon.call( this, json, ship);
}
ImpCommJammer.prototype = Object.create( Weapon.prototype );
ImpCommJammer.prototype.constructor = ImpCommJammer;

var SensorSpear = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SensorSpear.prototype = Object.create( Weapon.prototype );
SensorSpear.prototype.constructor = SensorSpear;

var SensorSpike = function(json, ship)
{
    Weapon.call( this, json, ship);
}
SensorSpike.prototype = Object.create( Weapon.prototype );
SensorSpike.prototype.constructor = SensorSpike;


