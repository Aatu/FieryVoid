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


var AntimatterWeapon = function AntimatterWeapon(json, ship) {
    Antimatter.call(this, json, ship);
};
AntimatterWeapon.prototype = Object.create(Antimatter.prototype);
AntimatterWeapon.prototype.constructor = AntimatterWeapon;
AntimatterWeapon.prototype.calculateSpecialRangePenalty = function (distance) {
	var rangePenalty = 0;
    rangePenalty += this.rangePenalty * Math.max(0,distance - this.rngNoPenalty);
    rangePenalty += this.rangePenalty * Math.max(0,distance - this.rngNormalPenalty);
    return rangePenalty;
};


var AntiprotonGun = function AntiprotonGun(json, ship) {
    Antimatter.call(this, json, ship);
};
AntiprotonGun.prototype = Object.create(AntimatterWeapon.prototype);
AntiprotonGun.prototype.constructor = AntiprotonGun;

var AntimatterCannon = function AntimatterCannon(json, ship) {
    Antimatter.call(this, json, ship);
};
AntimatterCannon.prototype = Object.create(AntimatterWeapon.prototype);
AntimatterCannon.prototype.constructor = AntimatterCannon;