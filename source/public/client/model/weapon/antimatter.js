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
    Weapon.call(this, json, ship);
};
AntimatterWeapon.prototype = Object.create(Weapon.prototype);
AntimatterWeapon.prototype.constructor = AntimatterWeapon;
AntimatterWeapon.prototype.calculateSpecialRangePenalty = function (distance) {
	var rangePenalty = 0;
    rangePenalty += this.rangePenalty * Math.max(0,distance - this.rngNoPenalty);
    rangePenalty += this.rangePenalty * Math.max(0,distance - this.rngNormalPenalty);
    return rangePenalty;
};
/* I don't know how to make this work - calling parent eludes me ATM; moving entire code to shipSystem.js!
AntimatterWeapon.prototype.changeFiringMode = function () { //additional arrays to change during mode change - Antimatter-specific
Weapon.prototype.changeFiringMode(this);
	var updateDataPenalty = false; 
	if (!mathlib.arrayIsEmpty(this.rngNoPenaltyArray)) {
		this.rngNoPenalty = this.rngNoPenaltyArray[this.firingMode];
		updateDataPenalty = true;
	}
	if (!mathlib.arrayIsEmpty(this.rngNormalPenaltyArray)) {
		this.rngNormalPenaltyPenalty = this.rngNormalPenaltyArray[this.firingMode];
		updateDataPenalty = true;
	}
	if (updateDataPenalty == true){
		this.data["Range brackets"] = 'no penalty up to ' + this.rngNoPenalty + ' / regular up to ' + this.rngNormalPenalty + ' / double' ;
	}
	
	updateDataPenalty = false;
	if (!mathlib.arrayIsEmpty(this.maxXArray)) {
		this.maxX = this.maxXArray[this.firingMode];
		updateDataPenalty = true;
	}
	if (!mathlib.arrayIsEmpty(this.dmgEquationArray)) {
		this.dmgEquation = this.dmgEquationArray[this.firingMode];
		updateDataPenalty = true;
	}
	if (updateDataPenalty == true){
		this.data["X-dependent damage"] = this.dmgEquation + ' ( max X = ' + this.maxX + ')';
	}
}
*/

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


var AntiprotonDefender = function AntiprotonDefender(json, ship) {
    Antimatter.call(this, json, ship);
};
AntiprotonDefender.prototype = Object.create(AntimatterWeapon.prototype);
AntiprotonDefender.prototype.constructor = AntiprotonDefender;


var AntimatterTorpedo = function AntimatterTorpedo(json, ship) {
    Antimatter.call(this, json, ship);
};
AntimatterTorpedo.prototype = Object.create(AntimatterWeapon.prototype);
AntimatterTorpedo.prototype.constructor = AntimatterTorpedo;


var LightAntiprotonGun = function LightAntiprotonGun(json, ship) {
    Antimatter.call(this, json, ship);
};
LightAntiprotonGun.prototype = Object.create(AntimatterWeapon.prototype);
LightAntiprotonGun.prototype.constructor = LightAntiprotonGun;


var LtAntimatterCannon = function LtAntimatterCannon(json, ship) {
    Antimatter.call(this, json, ship);
};
LtAntimatterCannon.prototype = Object.create(AntimatterWeapon.prototype);
LtAntimatterCannon.prototype.constructor = LtAntimatterCannon;

var AntimatterShredder = function AntimatterShredder(json, ship) {
    Antimatter.call(this, json, ship);
};
AntimatterShredder.prototype = Object.create(AntimatterWeapon.prototype);
AntimatterShredder.prototype.constructor = AntimatterShredder;
