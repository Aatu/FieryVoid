var Pulse = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Pulse.prototype = Object.create( Weapon.prototype );
Pulse.prototype.constructor = Pulse;


var LightPulse = function(json, ship)
{
    Pulse.call( this, json, ship);
}
LightPulse.prototype = Object.create( Pulse.prototype );
LightPulse.prototype.constructor = LightPulse;


var MediumPulse = function(json, ship)
{
    Pulse.call( this, json, ship);
}
MediumPulse.prototype = Object.create( Pulse.prototype );
MediumPulse.prototype.constructor = MediumPulse;

var HeavyPulse = function(json, ship)
{
    Pulse.call( this, json, ship);
}
HeavyPulse.prototype = Object.create( Pulse.prototype );
HeavyPulse.prototype.constructor = HeavyPulse;


var MolecularPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
MolecularPulsar.prototype = Object.create( Pulse.prototype );
MolecularPulsar.prototype.constructor = MolecularPulsar;


var GatlingPulseCannon = function(json, ship)
{
    Pulse.call( this, json, ship);
}
GatlingPulseCannon.prototype = Object.create( Pulse.prototype );
GatlingPulseCannon.prototype.constructor = GatlingPulseCannon;
