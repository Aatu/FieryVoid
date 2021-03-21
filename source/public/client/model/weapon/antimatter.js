"use strict";

var Antimatter = function Antimatter(json, ship) {
    Weapon.call(this, json, ship);
};
Antimatter.prototype = Object.create(Weapon.prototype);
Antimatter.prototype.constructor = Antimatter;

var AntimatterConverter = function AntimatterConverter(json, ship) {
    Antimatter.call(this, json, ship);
};
AntimatterConverter.prototype = Object.create(Antimatter.prototype);
AntimatterConverter.prototype.constructor = AntimatterConverter;


var AntiprotonGun = function AntiprotonGun(json, ship) {
    Antimatter.call(this, json, ship);
};
AntiprotonGun.prototype = Object.create(Antimatter.prototype);
AntiprotonGun.prototype.constructor = AntiprotonGun;
AntiprotonGun.prototype.calculateSpecialRangePenalty = function (distance) {
    var distancePenalized = Math.max(0,distance - 5); //ignore first 5 hexes
    var rangePenalty = this.rangePenalty * distancePenalized;
    return rangePenalty;
};