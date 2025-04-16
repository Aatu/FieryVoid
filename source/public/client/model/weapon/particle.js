"use strict";

var Particle = function Particle(json, ship) {
    Weapon.call(this, json, ship);
};
Particle.prototype = Object.create(Weapon.prototype);
Particle.prototype.constructor = Particle;

var TwinArray = function TwinArray(json, ship) {
    Particle.call(this, json, ship);
};
TwinArray.prototype = Object.create(Particle.prototype);
TwinArray.prototype.constructor = TwinArray;

TwinArray.prototype.initializationUpdate = function() {
	if(this.firingMode == 2){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

TwinArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

var QuadArray = function QuadArray(json, ship) {
    Particle.call(this, json, ship);
};
QuadArray.prototype = Object.create(Particle.prototype);
QuadArray.prototype.constructor = QuadArray;

QuadArray.prototype.initializationUpdate = function() {
	if(this.firingMode == 5){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

QuadArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

var HeavyArray = function HeavyArray(json, ship) {
    Particle.call(this, json, ship);
};
HeavyArray.prototype = Object.create(Particle.prototype);
HeavyArray.prototype.constructor = HeavyArray;

HeavyArray.prototype.initializationUpdate = function() {
	if(this.firingMode == 2){
		this.data["Shots Remaining"] = this.guns-this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

HeavyArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

var HeavyParticleBeam = function HeavyParticleBeam(json, ship) {
    Particle.call(this, json, ship);
};
HeavyParticleBeam.prototype = Object.create(Particle.prototype);
HeavyParticleBeam.prototype.constructor = HeavyParticleBeam;

var StdParticleBeam = function StdParticleBeam(json, ship) {
    Particle.call(this, json, ship);
};
StdParticleBeam.prototype = Object.create(Particle.prototype);
StdParticleBeam.prototype.constructor = StdParticleBeam;

var QuadParticleBeam = function QuadParticleBeam(json, ship) {
    Particle.call(this, json, ship);
};
QuadParticleBeam.prototype = Object.create(Particle.prototype);
QuadParticleBeam.prototype.constructor = QuadParticleBeam;

QuadParticleBeam.prototype.initializationUpdate = function() {
	if(this.firingMode == 2){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

QuadParticleBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

var AdvParticleBeam = function AdvParticleBeam(json, ship) {
    Particle.call(this, json, ship);
};
AdvParticleBeam.prototype = Object.create(Particle.prototype);
AdvParticleBeam.prototype.constructor = AdvParticleBeam;

var PairedParticleGun = function PairedParticleGun(json, ship) {
    Particle.call(this, json, ship);
};
PairedParticleGun.prototype = Object.create(Particle.prototype);
PairedParticleGun.prototype.constructor = PairedParticleGun;

var GuardianArray = function GuardianArray(json, ship) {
    Particle.call(this, json, ship);
};
GuardianArray.prototype = Object.create(Particle.prototype);
PairedParticleGun.prototype.constructor = GuardianArray;

var ParticleCannon = function ParticleCannon(json, ship) {
    Particle.call(this, json, ship);
};
ParticleCannon.prototype = Object.create(Particle.prototype);
ParticleCannon.prototype.constructor = ParticleCannon;

var ParticleBlaster = function ParticleBlaster(json, ship) {
    Particle.call(this, json, ship);
};
ParticleBlaster.prototype = Object.create(Particle.prototype);
ParticleBlaster.prototype.constructor = ParticleBlaster;

var ParticleBlasterFtr = function ParticleBlasterFtr(json, ship) {
    Particle.call(this, json, ship);
};
ParticleBlasterFtr.prototype = Object.create(Particle.prototype);
ParticleBlasterFtr.prototype.constructor = ParticleBlasterFtr;

var HvyParticleCannon = function HvyParticleCannon(json, ship) {
    Particle.call(this, json, ship);
};
HvyParticleCannon.prototype = Object.create(Particle.prototype);
HvyParticleCannon.prototype.constructor = HvyParticleCannon;

var LightParticleCannon = function LightParticleCannon(json, ship) {
    Particle.call(this, json, ship);
};
LightParticleCannon.prototype = Object.create(Particle.prototype);
LightParticleCannon.prototype.constructor = LightParticleCannon;

var ParticleCutter = function ParticleCutter(json, ship) {
    Particle.call(this, json, ship);
};
ParticleCutter.prototype = Object.create(Particle.prototype);
ParticleCutter.prototype.constructor = ParticleCutter;

var SolarCannon = function SolarCannon(json, ship) {
    Particle.call(this, json, ship);
};
SolarCannon.prototype = Object.create(Particle.prototype);
SolarCannon.prototype.constructor = SolarCannon;

var LightParticleBlaster = function LightParticleBlaster(json, ship) {
    Particle.call(this, json, ship);
};
LightParticleBlaster.prototype = Object.create(Particle.prototype);
LightParticleBlaster.prototype.constructor = LightParticleBlaster;

var LightParticleBeam = function LightParticleBeam(json, ship) {
    Particle.call(this, json, ship);
};
LightParticleBeam.prototype = Object.create(Particle.prototype);
LightParticleBeam.prototype.constructor = LightParticleBeam;

var LightParticleBeamShip = function LightParticleBeamShip(json, ship) {
    Particle.call(this, json, ship);
};
LightParticleBeamShip.prototype = Object.create(Particle.prototype);
LightParticleBeamShip.prototype.constructor = LightParticleBeamShip;


var ParticleRepeater = function ParticleRepeater(json, ship) {
    Particle.call(this, json, ship);
};
ParticleRepeater.prototype = Object.create(Particle.prototype);
ParticleRepeater.prototype.constructor = ParticleRepeater;
ParticleRepeater.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.
/*
    this.data["Weapon type"] = "Particle";
    this.data["Damage type"] = "Standard";
*/
    this.data["Number of shots"] = shipManager.power.getBoost(this) + 1;

    if (window.weaponManager.isLoaded(this)) {
        /*
        this.loadingtime = this.getLoadingTime();
        this.turnsloaded = this.getLoadingTime();
        this.normalload = this.getLoadingTime();
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = 1 + shipManager.power.getBoost(this);
    this.data.Intercept = this.intercept * -5;
    
    return this;
};
ParticleRepeater.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};


var RepeaterGun = function RepeaterGun(json, ship) {
    Particle.call(this, json, ship);
};
RepeaterGun.prototype = Object.create(Particle.prototype);
RepeaterGun.prototype.constructor = RepeaterGun;
RepeaterGun.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.
/*
    this.data["Weapon type"] = "Particle";
    this.data["Damage type"] = "Standard";
*/
    this.data["Number of shots"] = shipManager.power.getBoost(this) + 1;

    if (window.weaponManager.isLoaded(this)) {
        /*
        this.loadingtime = this.getLoadingTime();
        this.turnsloaded = this.getLoadingTime();
        this.normalload = this.getLoadingTime();
        */
    } else {
        var count = shipManager.power.getBoost(this);

        for (var i = 0; i < count; i++) {
            shipManager.power.unsetBoost(null, this);
        }
    }

    this.intercept = 1 + shipManager.power.getBoost(this);
    this.data.Intercept = this.intercept * -5;
    
    return this;
};
RepeaterGun.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

var HeavyBolter = function HeavyBolter(json, ship) {
    Particle.call(this, json, ship);
};
HeavyBolter.prototype = Object.create(Particle.prototype);
HeavyBolter.prototype.constructor = HeavyBolter;

var MediumBolter = function MediumBolter(json, ship) {
    Particle.call(this, json, ship);
};
MediumBolter.prototype = Object.create(Particle.prototype);
MediumBolter.prototype.constructor = MediumBolter;

var LightBolter = function LightBolter(json, ship) {
    Particle.call(this, json, ship);
};
LightBolter.prototype = Object.create(Particle.prototype);
LightBolter.prototype.constructor = LightBolter;

var SentinelPointDefense = function SentinelPointDefense(json, ship) {
    Particle.call(this, json, ship);
};
SentinelPointDefense.prototype = Object.create(Particle.prototype);
SentinelPointDefense.prototype.constructor = SentinelPointDefense;

var ParticleProjector = function ParticleProjector(json, ship) {
    Particle.call(this, json, ship);
};
ParticleProjector.prototype = Object.create(Particle.prototype);
ParticleProjector.prototype.constructor = ParticleProjector;

var EMWaveDisruptor = function EMWaveDisruptor(json, ship) {
    Particle.call(this, json, ship);
};
EMWaveDisruptor.prototype = Object.create(Particle.prototype);
EMWaveDisruptor.prototype.constructor = EMWaveDisruptor;

EMWaveDisruptor.prototype.initBoostableInfo = function () {
    // Needed because it can chance during initial phase
    // because of adding extra power.
    //in this case: increase number of "guns" (that is, separate shots)

    var count = shipManager.power.getBoost(this);

    this.data["Number of guns"] = count + 2;
    this.guns = count + 2;

    return this;
};

var BAInterceptorMkI = function BAInterceptorMkI(json, ship) {
    Particle.call(this, json, ship);
};
BAInterceptorMkI.prototype = Object.create(Particle.prototype);
BAInterceptorMkI.prototype.constructor = BAInterceptorMkI;

var BAInterceptorPrototype = function BAInterceptorPrototype(json, ship) {
    Particle.call(this, json, ship);
};
BAInterceptorPrototype.prototype = Object.create(Particle.prototype);
BAInterceptorPrototype.prototype.constructor = BAInterceptorPrototype;

var ParticleHammer = function ParticleHammer(json, ship) {
    Particle.call(this, json, ship);
};
ParticleHammer.prototype = Object.create(Particle.prototype);
ParticleHammer.prototype.constructor = ParticleHammer;

var HvyParticleProjector = function HvyParticleProjector(json, ship) {
    Particle.call(this, json, ship);
};
HvyParticleProjector.prototype = Object.create(Particle.prototype);
HvyParticleProjector.prototype.constructor = HvyParticleProjector;

var LightParticleProjector = function LightParticleProjector(json, ship) {
    Particle.call(this, json, ship);
};
LightParticleProjector.prototype = Object.create(Particle.prototype);
LightParticleProjector.prototype.constructor = LightParticleProjector;

var PentagonArray = function PentagonArray(json, ship) {
    Weapon.call(this, json, ship);
};
PentagonArray.prototype = Object.create(Weapon.prototype);
PentagonArray.prototype.constructor = PentagonArray;


var ParticleAccelerator = function ParticleAccelerator(json, ship) {
    Weapon.call(this, json, ship);
};
ParticleAccelerator.prototype = Object.create(Weapon.prototype);
ParticleAccelerator.prototype.constructor = ParticleAccelerator;


var LightParticleAccelerator = function LightParticleAccelerator(json, ship) {
    Weapon.call(this, json, ship);
};
LightParticleAccelerator.prototype = Object.create(Weapon.prototype);
LightParticleAccelerator.prototype.constructor = LightParticleAccelerator;


var LightParticleBolt = function LightParticleBolt(json, ship) {
    Weapon.call(this, json, ship);
};
LightParticleBolt.prototype = Object.create(Weapon.prototype);
LightParticleBolt.prototype.constructor = LightParticleBolt;


var UnreliableTwinArray = function UnreliableTwinArray(json, ship) {
    Particle.call(this, json, ship);
};
UnreliableTwinArray.prototype = Object.create(Particle.prototype);
UnreliableTwinArray.prototype.constructor = UnreliableTwinArray;

var BoltAccelerator = function BoltAccelerator(json, ship) {
    Particle.call(this, json, ship);
};
BoltAccelerator.prototype = Object.create(Particle.prototype);
BoltAccelerator.prototype.constructor = BoltAccelerator;

var TelekineticCutter = function TelekineticCutter(json, ship) {
    Particle.call(this, json, ship);
};
TelekineticCutter.prototype = Object.create(Particle.prototype);
TelekineticCutter.prototype.constructor = TelekineticCutter;

TelekineticCutter.prototype.initializationUpdate = function() {
	if(this.firingMode == 2){
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

TelekineticCutter.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

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

var MinorThoughtPulsar = function MinorThoughtPulsar(json, ship) {
    Particle.call(this, json, ship);
};
MinorThoughtPulsar.prototype = Object.create(Particle.prototype);
MinorThoughtPulsar.prototype.constructor = MinorThoughtPulsar;
