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

var CaptorMine = function CaptorMine(json, ship) {
    Aoe.call(this, json, ship);
};
CaptorMine.prototype = Object.create(Aoe.prototype);
CaptorMine.prototype.constructor = CaptorMine;

CaptorMine.prototype.initializationUpdate = function () {
    var ship = this.ship;
    var stealthSystem = shipManager.systems.getSystemByName(ship, "mineStealth");
    if (stealthSystem && !stealthSystem.isMineRevealed(ship)) {
        this.range = 0;
    }  
    return this  
}    