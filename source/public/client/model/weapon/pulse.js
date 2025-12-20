"use strict";

var Pulse = function Pulse(json, ship) {
    Weapon.call(this, json, ship);
};
Pulse.prototype = Object.create(Weapon.prototype);
Pulse.prototype.constructor = Pulse;

var EnergyPulsar = function EnergyPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
EnergyPulsar.prototype = Object.create(Pulse.prototype);
EnergyPulsar.prototype.constructor = EnergyPulsar;

var ScatterPulsar = function ScatterPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
ScatterPulsar.prototype = Object.create(Pulse.prototype);
ScatterPulsar.prototype.constructor = ScatterPulsar;

var QuadPulsar = function QuadPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
QuadPulsar.prototype = Object.create(Pulse.prototype);
QuadPulsar.prototype.constructor = QuadPulsar;

var LightPulse = function LightPulse(json, ship) {
    Pulse.call(this, json, ship);
};
LightPulse.prototype = Object.create(Pulse.prototype);
LightPulse.prototype.constructor = LightPulse;

var MediumPulse = function MediumPulse(json, ship) {
    Pulse.call(this, json, ship);
};
MediumPulse.prototype = Object.create(Pulse.prototype);
MediumPulse.prototype.constructor = MediumPulse;

var HeavyPulse = function HeavyPulse(json, ship) {
    Pulse.call(this, json, ship);
};
HeavyPulse.prototype = Object.create(Pulse.prototype);
HeavyPulse.prototype.constructor = HeavyPulse;

var MolecularPulsar = function MolecularPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
MolecularPulsar.prototype = Object.create(Pulse.prototype);
MolecularPulsar.prototype.constructor = MolecularPulsar;

var GatlingPulseCannon = function GatlingPulseCannon(json, ship) {
    Pulse.call(this, json, ship);
};
GatlingPulseCannon.prototype = Object.create(Pulse.prototype);
GatlingPulseCannon.prototype.constructor = GatlingPulseCannon;

var PointPulsar = function PointPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
PointPulsar.prototype = Object.create(Pulse.prototype);
PointPulsar.prototype.constructor = PointPulsar;

PointPulsar.prototype.initializationUpdate = function() {
	if (this.firingMode == 2) {
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

PointPulsar.prototype.checkFinished = function () {
	if(this.fireOrders.length > 2) return true;
    return false;
};

PointPulsar.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
	/*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
	*/

	if(this.fireOrders.length > 2) return;

    for (var i in this.fireOrders){
        var thisOrder = this.fireOrders[i];
        if(thisOrder.targetid !== target.id && thisOrder.type !== "selfIntercept") return; //trying to hit a different target!
    }

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; 

        if (system) {
            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }        

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

PointPulsar.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
	var mod = 0;
    //    var fireOrder = this.fireOrders[i];
        if(target.flight && calledid !== null){ //Has fireorder against fighter unit, and is a called shot
            mod += 4; //CalledShotmod is -4, so just compensate for that.            
        }

	return mod; 
};

PointPulsar.prototype.checkSelfInterceptSystem = function() {
	if(this.fireOrders.length > 2) return false;
    return true;
};

PointPulsar.prototype.doMultipleSelfIntercept = function(ship) {

    for (var s = 0; s < 1; s++) {    
        var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var fire = {
        id: fireid,
        type: "selfIntercept",
        shooterid: ship.id,
        targetid: ship.id,
        weaponid: this.id,
        calledid: -1,
        turn: gamedata.turn,
        firingMode: 1, //So that powerReqd display accurately always.
        shots: 1,
        x: "null",
        y: "null",
        addToDB: true,
        damageclass: this.data["Weapon type"].toLowerCase()
        };

        this.fireOrders.push(fire);
    } 
    webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });   
};

var PairedLightBoltCannon = function PairedLightBoltCannon(json, ship) {
    Pulse.call(this, json, ship);
};
PairedLightBoltCannon.prototype = Object.create(Particle.prototype);
PairedLightBoltCannon.prototype.constructor = PairedLightBoltCannon;

var ScatterGun = function ScatterGun(json, ship) {
    Weapon.call(this, json, ship);
};
ScatterGun.prototype = Object.create(Weapon.prototype);
ScatterGun.prototype.constructor = ScatterGun;

var LightScattergun = function LightScattergun(json, ship) {
    Weapon.call(this, json, ship);
};
LightScattergun.prototype = Object.create(Weapon.prototype);
LightScattergun.prototype.constructor = LightScattergun;

var LtBlastCannon = function LtBlastCannon(json, ship) {
    Pulse.call(this, json, ship);
};
LtBlastCannon.prototype = Object.create(Pulse.prototype);
LtBlastCannon.prototype.constructor = LtBlastCannon;

var MedBlastCannon = function MedBlastCannon(json, ship) {
    Pulse.call(this, json, ship);
};
MedBlastCannon.prototype = Object.create(Pulse.prototype);
MedBlastCannon.prototype.constructor = MedBlastCannon;

var HvyBlastCannon = function HvyBlastCannon(json, ship) {
    Pulse.call(this, json, ship);
};
HvyBlastCannon.prototype = Object.create(Pulse.prototype);
HvyBlastCannon.prototype.constructor = HvyBlastCannon;


var PulseAccelerator = function PulseAccelerator(json, ship) {
    Pulse.call(this, json, ship);
};
PulseAccelerator.prototype = Object.create(Pulse.prototype);
PulseAccelerator.prototype.constructor = PulseAccelerator;


var PhasingPulseCannon = function PhasingPulseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
PhasingPulseCannon.prototype = Object.create(Weapon.prototype);
PhasingPulseCannon.prototype.constructor = PhasingPulseCannon;
PhasingPulseCannon.prototype.shieldInteractionDefense = function (target, shooter, shield, mod) {
	//ignores non-Ancient non-EM shield and shield-like systems
	if (target.factionAge >= 3) return mod;
	if (shield.name == 'eMShield') return mod;
    return Math.min(0,mod);
};

var PhasingPulseCannonH = function PhasingPulseCannonH(json, ship) {
    PhasingPulseCannon.call(this, json, ship);
};
PhasingPulseCannonH.prototype = Object.create(PhasingPulseCannon.prototype);
PhasingPulseCannonH.prototype.constructor = PhasingPulseCannonH;

var UltraPulseCannon = function UltraPulseCannon(json, ship) {
    Pulse.call(this, json, ship);
};
UltraPulseCannon.prototype = Object.create(Pulse.prototype);
UltraPulseCannon.prototype.constructor = UltraPulseCannon;

var TriopticPulsar = function TriopticPulsar(json, ship) {
    Pulse.call(this, json, ship);
};
TriopticPulsar.prototype = Object.create(Pulse.prototype);
TriopticPulsar.prototype.constructor = TriopticPulsar;

var VolleyLaser = function VolleyLaser(json, ship) {
    Pulse.call(this, json, ship);
};
VolleyLaser.prototype = Object.create(Pulse.prototype);
VolleyLaser.prototype.constructor = VolleyLaser;