'use strict';

var ShipSystem = function ShipSystem(json, ship) {
				this.ship = ship;

				for (var i in json) {
								this[i] = json[i];
				}
};

ShipSystem.prototype = {

				constructor: ShipSystem
};

ShipSystem.prototype.initBoostableInfo = function () {
				return this;
};

ShipSystem.prototype.hasMaxBoost = function () {
				return false;
};
ShipSystem.prototype.isScanner = function () {
				return false;
};

var Fighter = function Fighter(json, staticFighter, ship) {

	Object.keys(staticFighter).forEach(function(key) {
        this[key] = staticFighter[key];
    }, this)

	for (var i in json) {
		if (i == 'systems') {
			this.systems = SystemFactory.createSystemsFromJson(json[i], ship, this);
		} else {
			this[i] = json[i];
		}
	}
};

Fighter.prototype = Object.create(ShipSystem.prototype);
Fighter.prototype.constructor = Fighter;

var SuperHeavyFighter = function SuperHeavyFighter(json, ship) {
	Object.keys(staticFighter).forEach(function(key) {
        this[key] = staticFighter[key];
    }, this)

	for (var i in json) {
		if (i == 'systems') {
			this.systems = SystemFactory.createSystemsFromJson(json[i], ship, this);
		} else {
			this[i] = json[i];
		}
	}
}

SuperHeavyFighter.prototype = Object.create(ShipSystem.prototype);
SuperHeavyFighter.prototype.constructor = SuperHeavyFighter;

var Weapon = function Weapon(json, ship) {
				ShipSystem.call(this, json, ship);
				this.targetsShips = true;
};

Weapon.prototype = Object.create(ShipSystem.prototype);
Weapon.prototype.constructor = Weapon;

Weapon.prototype.getAmmo = function (fireOrder) {
				return null;
};

Weapon.prototype.translateFCtoD100txt = function (fireControl) {
				var FCtxt = '';
				var i = 0;
				var toAdd;
				for (i = 0; i <= 2; i++) {
								toAdd = fireControl[i];
								if (fireControl[i] === null) {
												toAdd = '-';
								} else {
												toAdd = toAdd * 5; //d20 to d100
								}
								FCtxt = FCtxt + toAdd;
								if (i < 2) FCtxt = FCtxt + '/';
				}
				return FCtxt;
}; //endof Weapon.prototype.translateFCtoD100txt


Weapon.prototype.changeFiringMode = function () {
	var mode = this.firingMode + 1;

	if (this.firingModes[mode]) {
		this.firingMode = mode;
	} else {
				this.firingMode = 1;
	}

	//set data for that firing mode...
	//change both attributes (used in various situations) and .data array (used for display)
	if (!mathlib.arrayIsEmpty(this.maxDamageArray)) this.maxDamage = this.maxDamageArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.minDamageArray)) {
		this.minDamage = this.minDamageArray[this.firingMode];
		this.data["Damage"] = this.minDamage;
		if (this.maxDamage > 0) this.data["Damage"] = this.data["Damage"] + "-" + this.maxDamage;
	}
	if (!mathlib.arrayIsEmpty(this.priorityArray)) {
		this.priority = this.priorityArray[this.firingMode];
		this.data["Resolution Priority"] = this.priority;
	}
	if (!mathlib.arrayIsEmpty(this.rangeDamagePenaltyArray)) this.rangeDamagePenalty = this.rangeDamagePenaltyArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.rangePenaltyArray)) {
		this.rangePenalty = this.rangePenaltyArray[this.firingMode];
		this.data["Range penalty"] = this.rangePenalty + " per hex";
	}
	if (!mathlib.arrayIsEmpty(this.rangeArray)) {
		this.range = this.rangeArray[this.firingMode];
		this.data["Range"] = this.range;
	}
	if (!mathlib.arrayIsEmpty(this.fireControlArray)) {
		this.fireControl = this.fireControlArray[this.firingMode];
		this.data["Fire control (fighter/med/cap)"] = this.translateFCtoD100txt(this.fireControl);
		//this.fireControl[0]+'/'+this.fireControl[1]+'/'+this.fireControl[2];
	}
	if (!mathlib.arrayIsEmpty(this.loadingtimeArray)) this.loadingtime = this.loadingtimeArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.turnsloadedArray)) this.turnsloaded = this.turnsloadedArray[this.firingMode];
	this.data["Loading"] = this.turnsloaded + '/' + this.loadingtime;
	if (!mathlib.arrayIsEmpty(this.extraoverloadshotsArray)) this.extraoverloadshots = this.extraoverloadshotsArray[this.firingMode];

	if (!mathlib.arrayIsEmpty(this.uninterceptableArray)) this.uninterceptable = this.uninterceptableArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.shotsArray)) this.shots = this.shotsArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.damageTypeArray)) {
		this.damageType = this.damageTypeArray[this.firingMode];
		this.data["Damage type"] = this.damageType;
	}
	if (!mathlib.arrayIsEmpty(this.weaponClassArray)) {
		this.weaponClass = this.weaponClassArray[this.firingMode];
		this.data["Weapon type"] = this.weaponClass;
	}
	if (!mathlib.arrayIsEmpty(this.defaultShotsArray)) this.defaultShots = this.defaultShotsArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.groupingArray)) this.grouping = this.groupingArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.gunsArray)) this.guns = this.gunsArray[this.firingMode];

	//firing animation related...
	if (!mathlib.arrayIsEmpty(this.animationArray)) this.animation = this.animationArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationImgArray)) this.animationImg = this.animationImgArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationImgSpriteArray)) this.animationImgSprite = this.animationImgSpriteArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationColorArray)) this.animationColor = this.animationColorArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationColor2Array)) this.animationColor2 = this.animationColor2Array[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationWidthArray)) this.animationWidth = this.animationWidthArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationExplosionScaleArray)) this.animationExplosionScale = this.animationExplosionScaleArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.animationExplosionTypeArray)) this.animationExplosionType = this.animationExplosionTypeArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.explosionColorArray)) this.explosionColor = this.explosionColorArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.trailLengthArray)) this.trailLength = this.trailLengthArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.trailColorArray)) this.trailColor = this.trailColorArray[this.firingMode];
	if (!mathlib.arrayIsEmpty(this.projectilespeedArray)) this.projectilespeed = this.projectilespeedArray[this.firingMode];
}; //end of Weapon.prototype.changeFiringMode


Weapon.prototype.getTurnsloaded = function () {
				return this.turnsloaded;
};

Weapon.prototype.getInterceptRating = function () {
				return this.intercept;
};

var Ballistic = function Ballistic(json, ship) {
				Weapon.call(this, json, ship);
};

Ballistic.prototype = Object.create(Weapon.prototype);
Ballistic.prototype.constructor = Ballistic;