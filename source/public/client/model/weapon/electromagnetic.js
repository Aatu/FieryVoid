"use strict";

var Electromagnetic = function Electromagnetic(json, ship) {
    Weapon.call(this, json, ship);
};
Electromagnetic.prototype = Object.create(Weapon.prototype);
Electromagnetic.prototype.constructor = Electromagnetic;

var BurstBeam = function BurstBeam(json, ship) {
    Electromagnetic.call(this, json, ship);
};
BurstBeam.prototype = Object.create(Electromagnetic.prototype);
BurstBeam.prototype.constructor = BurstBeam;

var ShockCannon = function ShockCannon(json, ship) {
    Electromagnetic.call(this, json, ship);
};
ShockCannon.prototype = Object.create(Electromagnetic.prototype);
ShockCannon.prototype.constructor = ShockCannon;

var ElectroPulseGun = function ElectroPulseGun(json, ship) {
    Electromagnetic.call(this, json, ship);
};
ElectroPulseGun.prototype = Object.create(Electromagnetic.prototype);
ElectroPulseGun.prototype.constructor = ElectroPulseGun;

var DualBurstBeam = function DualBurstBeam(json, ship) {
    Electromagnetic.call(this, json, ship);
};
DualBurstBeam.prototype = Object.create(Electromagnetic.prototype);
DualBurstBeam.prototype.constructor = DualBurstBeam;

var MediumBurstBeam = function MediumBurstBeam(json, ship) {
    Electromagnetic.call(this, json, ship);
};
MediumBurstBeam.prototype = Object.create(Electromagnetic.prototype);
MediumBurstBeam.prototype.constructor = MediumBurstBeam;

var HeavyBurstBeam = function HeavyBurstBeam(json, ship) {
    Electromagnetic.call(this, json, ship);
};
HeavyBurstBeam.prototype = Object.create(Electromagnetic.prototype);
HeavyBurstBeam.prototype.constructor = HeavyBurstBeam;

var BurstPulseCannon = function BurstPulseCannon(json, ship) {
    Pulse.call(this, json, ship);
};
BurstPulseCannon.prototype = Object.create(Pulse.prototype);
BurstPulseCannon.prototype.constructor = BurstPulseCannon;

var StunBeam = function StunBeam(json, ship) {
    Electromagnetic.call(this, json, ship);
};
StunBeam.prototype = Object.create(Electromagnetic.prototype);
StunBeam.prototype.constructor = StunBeam;