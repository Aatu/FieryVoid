"use strict";

var Torpedo = function Torpedo(json, ship) {
    Ballistic.call(this, json, ship);
};
Torpedo.prototype = Object.create(Ballistic.prototype);
Torpedo.prototype.constructor = Torpedo;

var BallisticTorpedo = function BallisticTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
BallisticTorpedo.prototype = Object.create(Torpedo.prototype);
BallisticTorpedo.prototype.constructor = BallisticTorpedo;

var IonTorpedo = function IonTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
IonTorpedo.prototype = Object.create(Torpedo.prototype);
IonTorpedo.prototype.constructor = IonTorpedo;

var PlasmaWaveTorpedo = function PlasmaWaveTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
PlasmaWaveTorpedo.prototype = Object.create(Torpedo.prototype);
PlasmaWaveTorpedo.prototype.constructor = PlasmaWaveTorpedo;

var PacketTorpedo = function PacketTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
PacketTorpedo.prototype = Object.create(Torpedo.prototype);
PacketTorpedo.prototype.constructor = PacketTorpedo;
PacketTorpedo.prototype.calculateSpecialRangePenalty = function (distance) {
    var distancePenalized = Math.max(0,distance - 10); //ignore first 10 hexes
    var rangePenalty = this.rangePenalty * distancePenalized;
    return rangePenalty;
};
