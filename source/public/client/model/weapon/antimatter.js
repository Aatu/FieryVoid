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