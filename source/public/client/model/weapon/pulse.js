var Pulse = function(json, ship)
{
    Weapon.call( this, json, ship);
}
Pulse.prototype = Object.create( Weapon.prototype );
Pulse.prototype.constructor = Pulse;

var EnergyPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
EnergyPulsar.prototype = Object.create( Pulse.prototype );
EnergyPulsar.prototype.constructor = EnergyPulsar;

var ScatterPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
ScatterPulsar.prototype = Object.create( Pulse.prototype );
ScatterPulsar.prototype.constructor = ScatterPulsar;

var QuadPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
QuadPulsar.prototype = Object.create( Pulse.prototype );
QuadPulsar.prototype.constructor = QuadPulsar;

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

var PointPulsar = function(json, ship)
{
    Pulse.call( this, json, ship);
}
PointPulsar.prototype = Object.create( Pulse.prototype );
PointPulsar.prototype.constructor = PointPulsar;

var PairedLightBoltCannon = function(json, ship)
{
    Pulse.call( this, json, ship);
}
PairedLightBoltCannon.prototype = Object.create( Particle.prototype );
PairedLightBoltCannon.prototype.constructor = PairedLightBoltCannon;


var ScatterGun = function(json, ship)
{
    Weapon.call( this, json, ship);
}
ScatterGun.prototype = Object.create( Weapon.prototype );
ScatterGun.prototype.constructor = ScatterGun;


var LtBlastCannon = function(json, ship)
{
    Pulse.call( this, json, ship);
}
LtBlastCannon.prototype = Object.create( Pulse.prototype );
LtBlastCannon.prototype.constructor = LtBlastCannon;

var MedBlastCannon = function(json, ship)
{
    Pulse.call( this, json, ship);
}
MedBlastCannon.prototype = Object.create( Pulse.prototype );
MedBlastCannon.prototype.constructor = MedBlastCannon;

var HvyBlastCannon = function(json, ship)
{
    Pulse.call( this, json, ship);
}
HvyBlastCannon.prototype = Object.create( Pulse.prototype );
HvyBlastCannon.prototype.constructor = HvyBlastCannon;
