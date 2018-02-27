var Torpedo = function(json, ship)
{
    Ballistic.call( this, json, ship);
}
Torpedo.prototype = Object.create( Ballistic.prototype );
Torpedo.prototype.constructor = Torpedo;


var BallisticTorpedo = function(json, ship)
{
    Torpedo.call( this, json, ship);
}
BallisticTorpedo.prototype = Object.create( Torpedo.prototype );
BallisticTorpedo.prototype.constructor = BallisticTorpedo;


var IonTorpedo = function(json, ship)
{
    Torpedo.call( this, json, ship);
}
IonTorpedo.prototype = Object.create( Torpedo.prototype );
IonTorpedo.prototype.constructor = IonTorpedo;



var PlasmaWaveTorpedo = function(json, ship)
{
    Torpedo.call( this, json, ship);
}
PlasmaWaveTorpedo.prototype = Object.create( Torpedo.prototype );
PlasmaWaveTorpedo.prototype.constructor = PlasmaWaveTorpedo;
