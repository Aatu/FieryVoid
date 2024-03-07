"use strict";

var InterceptorMkI = function InterceptorMkI(json, ship) {
    Weapon.call(this, json, ship);
    this.defensiveType = "Interceptor";
};
InterceptorMkI.prototype = Object.create(Weapon.prototype);
InterceptorMkI.prototype.constructor = InterceptorMkI;

InterceptorMkI.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return shipManager.systems.getOutput(target, this);
};

var InterceptorMkII = function InterceptorMkII(json, ship) {
    InterceptorMkI.call(this, json, ship);
};
InterceptorMkII.prototype = Object.create(InterceptorMkI.prototype);
InterceptorMkII.prototype.constructor = InterceptorMkII;

var InterceptorPrototype = function InterceptorPrototype(json, ship) {
    InterceptorMkI.call(this, json, ship);
};
InterceptorPrototype.prototype = Object.create(InterceptorMkI.prototype);
InterceptorPrototype.prototype.constructor = InterceptorPrototype;

var Shield = function Shield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};

Shield.prototype = Object.create(ShipSystem.prototype);
Shield.prototype.constructor = Shield;
Shield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    if (!weapon.ballistic) {
        if (shooter.flight && (mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)) return 0;
    }
    return shipManager.systems.getOutput(target, this);
};

var EMShield = function EMShield(json, ship) {
    Shield.call(this, json, ship);
    this.defensiveType = "Shield";
};
EMShield.prototype = Object.create(Shield.prototype);
EMShield.prototype.constructor = EMShield;

var GraviticShield = function GraviticShield(json, ship) {
    Shield.call(this, json, ship);
    this.defensiveType = "Shield";
};
GraviticShield.prototype = Object.create(Shield.prototype);
GraviticShield.prototype.constructor = GraviticShield;

var ShieldGenerator = function ShieldGenerator(json, ship) {
    ShipSystem.call(this, json, ship);
};

ShieldGenerator.prototype = Object.create(ShipSystem.prototype);
ShieldGenerator.prototype.constructor = ShieldGenerator;

ShieldGenerator.prototype.onTurnOff = function (ship) {
    for (var i in ship.systems) {
        var system = ship.systems[i];
        if (system.name == 'graviticShield') {
            // Shut it down.
            system.power.push({
                id: null,
                shipid: ship.id,
                systemid: system.id,
                type: 1,
                turn: gamedata.turn,
                amount: 0
            });
            shipWindowManager.setDataForSystem(ship, system);
        }
    }
};

ShieldGenerator.prototype.onTurnOn = function (ship) {
    for (var i in ship.systems) {
        var system = ship.systems[i];
        if (system.name == 'graviticShield') {
            // Turn it all on.
            shipManager.power.setOnline(ship, system);
            shipWindowManager.setDataForSystem(ship, system);
        }
    }
};

var Swrayshield = function Swrayshield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
Swrayshield.prototype = Object.create(ShipSystem.prototype);
Swrayshield.prototype.constructor = Swrayshield;
Swrayshield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //Ray shield does not affect hit chance
};
Swrayshield.prototype.hasMaxBoost = function () {
    return true;
};
Swrayshield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var CWShield = function CWShield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
CWShield.prototype = Object.create(ShipSystem.prototype);
CWShield.prototype.constructor = CWShield;
CWShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //Ray shield does not affect hit chance
};
CWShield.prototype.hasMaxBoost = function () {
    return true;
};
CWShield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var SatyraShield = function SatyraShield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
SatyraShield.prototype = Object.create(ShipSystem.prototype);
SatyraShield.prototype.constructor = SatyraShield;
SatyraShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //Ray shield does not affect hit chance
};
SatyraShield.prototype.hasMaxBoost = function () {
    return true;
};
SatyraShield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var Absorbtionshield = function Absorbtionshield(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "Shield";
};
Absorbtionshield.prototype = Object.create(ShipSystem.prototype);
Absorbtionshield.prototype.constructor = Absorbtionshield;
Absorbtionshield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    return 0; //absorbtion shield does not affect hit chance
};
Absorbtionshield.prototype.hasMaxBoost = function () {
    return true;
};
Absorbtionshield.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var Particleimpeder = function Particleimpeder(json, ship) {
    Weapon.call(this, json, ship);
    this.defensiveType = "Impeder";
};
Particleimpeder.prototype = Object.create(Weapon.prototype);
Particleimpeder.prototype.constructor = Particleimpeder;
Particleimpeder.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return shipManager.systems.getOutput(target, this);
	/* now it affects everything!
    if (shooter.flight) {
        //only affects fighters
        return shipManager.systems.getOutput(target, this);
    } else {
        return 0;
    }
	*/
};
Particleimpeder.prototype.hasMaxBoost = function () {
    return true;
};
Particleimpeder.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
Particleimpeder.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    if (window.weaponManager.isLoaded(this)) {} else {
        var count = shipManager.power.getBoost(this);
        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;
    this.data.Boostlevel = shipManager.power.getBoost(this);

	//display current boost as output - because that is actual shield rating from Impeder!
	if (this.data.Boostlevel > 0) {
		this.outputDisplay = this.data.Boostlevel;
	} else {
		this.outputDisplay = '-'; //'0' is not shown!
	}
	

    return this;
};
Particleimpeder.prototype.getInterceptRating = function () {
    return 3 + shipManager.power.getBoost(this);
};

var FtrShield = function(json, ship)
{
    ShipSystem.call( this, json, ship);
    this.defensiveType = "Shield";
}
FtrShield.prototype = Object.create( ShipSystem.prototype );
FtrShield.prototype.constructor = FtrShield;
FtrShield.prototype.getDefensiveHitChangeMod = function(target, shooter, weapon)
{
    return shipManager.systems.getOutput(target, this);
}

var HeavyInterceptorBattery = function HeavyInterceptorBattery(json, ship) {
    InterceptorMkI.call(this, json, ship);
};
HeavyInterceptorBattery.prototype = Object.create(InterceptorMkI.prototype);
HeavyInterceptorBattery.prototype.constructor = HeavyInterceptorBattery;

var Interdictor = function Interdictor(json, ship) {
    Weapon.call(this, json, ship);
};
Interdictor.prototype = Object.create(Weapon.prototype);
Interdictor.prototype.constructor = Interdictor;

var FtrInterdictor = function FtrInterdictor(json, ship) {
    Weapon.call(this, json, ship);
};
FtrInterdictor.prototype = Object.create(Weapon.prototype);
FtrInterdictor.prototype.constructor = FtrInterdictor;

var ThirdspaceShieldProjection = function ThirdspaceShieldProjection(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
ThirdspaceShieldProjection.prototype = Object.create(ShipSystem.prototype);
ThirdspaceShieldProjection.prototype.constructor = ThirdspaceShieldProjection;
ThirdspaceShieldProjection.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};

var ThirdspaceShieldProjector = function ThirdspaceShieldProjector(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
ThirdspaceShieldProjector.prototype = Object.create(ShipSystem.prototype);
ThirdspaceShieldProjector.prototype.constructor = ThirdspaceShieldProjector;
ThirdspaceShieldProjector.prototype.hasMaxBoost = function () {
    return true;
};
ThirdspaceShieldProjector.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
ThirdspaceShieldProjector.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};