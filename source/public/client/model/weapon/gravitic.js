"use strict";

var Gravitic = function Gravitic(json, ship) {
    Weapon.call(this, json, ship);
};
Gravitic.prototype = Object.create(Weapon.prototype);
Gravitic.prototype.constructor = Gravitic;

var GravitonPulsar = function GravitonPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
GravitonPulsar.prototype = Object.create(Pulse.prototype);
GravitonPulsar.prototype.constructor = GravitonPulsar;

GravitonPulsar.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.   

    if (window.weaponManager.isLoaded(this)) {
        /*no longer needed
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload = 1 + shipManager.power.getBoost(this);
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;

    return this;
};

GravitonPulsar.prototype.getInterceptRating = function () {
    return 1 + shipManager.power.getBoost(this);
};

GravitonPulsar.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

GravitonPulsar.prototype.hasMaxBoost = function () {
    return true;
};

GravitonPulsar.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var GraviticBolt = function GraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticBolt.prototype = Object.create(Gravitic.prototype);
GraviticBolt.prototype.constructor = GraviticBolt;

GraviticBolt.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.

    if (window.weaponManager.isLoaded(this)) {
        /*no longer needed!
        this.loadingtime = 1 + shipManager.power.getBoost(this);
        this.turnsloaded = 1 + shipManager.power.getBoost(this);
        this.normalload = 1 + shipManager.power.getBoost(this);
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = this.getInterceptRating();
    this.data.Intercept = this.getInterceptRating() * -5;

    return this;
};

GraviticBolt.prototype.getInterceptRating = function () {
    return 1 + shipManager.power.getBoost(this);
};

GraviticBolt.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;
        if (power.type == 2) {
            system.power.splice(i, 1);
            return;
        }
    }
};

GraviticBolt.prototype.hasMaxBoost = function () {
    return true;
};

GraviticBolt.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

var GravitonBeam = function GravitonBeam(json, ship) {
    Weapon.call(this, json, ship);
};
GravitonBeam.prototype = Object.create(Weapon.prototype);
GravitonBeam.prototype.constructor = GravitonBeam;

var LightGravitonBeam = function LightGravitonBeam(json, ship) {
    Gravitic.call(this, json, ship);
};
LightGravitonBeam.prototype = Object.create(Gravitic.prototype);
LightGravitonBeam.prototype.constructor = LightGravitonBeam;

var GraviticCannon = function GraviticCannon(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticCannon.prototype = Object.create(Gravitic.prototype);
GraviticCannon.prototype.constructor = GraviticCannon;

var LightGraviticBolt = function LightGraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
LightGraviticBolt.prototype = Object.create(Gravitic.prototype);
LightGraviticBolt.prototype.constructor = LightGraviticBolt;

var UltraLightGraviticBolt = function UltraLightGraviticBolt(json, ship) {
    Gravitic.call(this, json, ship);
};
UltraLightGraviticBolt.prototype = Object.create(Gravitic.prototype);
UltraLightGraviticBolt.prototype.constructor = UltraLightGraviticBolt;


var GraviticLance = function(json, ship) {
    Weapon.call( this, json, ship);
};
GraviticLance.prototype = Object.create( Weapon.prototype );
GraviticLance.prototype.constructor = GraviticLance;

GraviticLance.prototype.initializationUpdate = function() {
	if(this.firingMode == 3){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

GraviticLance.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = this.guns; // Default guns initially.  We never want teh palyer to miss firing a shot for such a powerful weapon (and it can't intercept).
    if (this.fireOrders.length == 2) { // Two shots have been locked in, remove the first.
        this.fireOrders.splice(0, 1); // Remove the first fire order.
        shotsOnTarget--; //Reduce guns to 1, the one currently being retargeted!
    }else if(this.fireOrders.length == 1){
        shotsOnTarget--; //Reduce guns to 1, one shot is already locked and we don't want to target 3 :)        
    }

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Grav Beams are raking, can never called shot.

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if(chance < 1) continue;

        var fire = {
            id: fireid,
            type: 'normal',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Sweeping', 
            chance: chance,
            hitmod: 0,
            notes: "Split"
        };
        
        fireOrdersArray.push(fire); // Store each fire order
    }
    
    return fireOrdersArray; // Return all fire orders
};

var GraviticCutter = function GraviticCutter(json, ship) {
    Gravitic.call(this, json, ship);
};
GraviticCutter.prototype = Object.create(Gravitic.prototype);
GraviticCutter.prototype.constructor = GraviticCutter;

GraviticCutter.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.

    this.data["Weapon type"] = "Raking";
    this.data["Damage type"] = "Standard";

    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '10-28';
            break;
        case 1:
            this.data["Damage"] = '13-40';
            break;
        default:
            this.data["Damage"] = '10-28';
            break;
    }

    if (window.weaponManager.isLoaded(this)) {
        this.loadingtime = 2 + shipManager.power.getBoost(this);
        this.turnsloaded = 2 + shipManager.power.getBoost(this);
        this.normalload = 2 + shipManager.power.getBoost(this);
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    return this;
};

GraviticCutter.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};
GraviticCutter.prototype.hasMaxBoost = function () {
    return true;
};
GraviticCutter.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
