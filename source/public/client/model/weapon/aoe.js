"use strict";

var Aoe = function Aoe(json, ship) {
    Weapon.call(this, json, ship);
};
Aoe.prototype = Object.create(Weapon.prototype);
Aoe.prototype.constructor = Aoe;

var EnergyMine = function EnergyMine(json, ship) {
    Aoe.call(this, json, ship);
};
EnergyMine.prototype = Object.create(Aoe.prototype);
EnergyMine.prototype.constructor = EnergyMine;


var LightEnergyMine = function LightEnergyMine(json, ship) {
    Aoe.call(this, json, ship);
};
LightEnergyMine.prototype = Object.create(Aoe.prototype);
LightEnergyMine.prototype.constructor = LightEnergyMine;
