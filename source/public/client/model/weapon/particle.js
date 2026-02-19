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

TwinArray.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }

    if (this.startArcArray.length > 0) { //More than one arc e.g. Battlecruiser
        this.data["Arc"] = this.startArcArray[0] + "..." + this.endArcArray[0] + ", " + this.startArcArray[1] + "..." + this.endArcArray[1];
    }

    return this;
};

TwinArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

    if (this.firingMode == 2 && this.fireOrders.length > 1) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1;

        if (system) {
            //check if weapon is eligible for called shot!
            if (!weaponManager.canWeaponCall(weapon)) continue;

            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if (chance < 1) continue;

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

TwinArray.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length > 1) return true;
    return false;
};

var QuadArray = function QuadArray(json, ship) {
    Particle.call(this, json, ship);
};
QuadArray.prototype = Object.create(Particle.prototype);
QuadArray.prototype.constructor = QuadArray;

QuadArray.prototype.initializationUpdate = function () {
    if (this.firingMode == 5) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

QuadArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */

    if (this.firingMode == 2 && this.fireOrders.length > 3) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1;

        if (system) {
            //check if weapon is eligible for called shot!
            if (!weaponManager.canWeaponCall(weapon)) continue;

            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if (chance < 1) continue;

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

QuadArray.prototype.checkFinished = function () {
    if (this.firingMode == 5 && this.fireOrders.length > 3) return true;
    return false;
};

var HeavyArray = function HeavyArray(json, ship) {
    Particle.call(this, json, ship);
};
HeavyArray.prototype = Object.create(Particle.prototype);
HeavyArray.prototype.constructor = HeavyArray;

HeavyArray.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

HeavyArray.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */

    if (this.firingMode == 2 && this.fireOrders.length > 1) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1;

        if (system) {
            //check if weapon is eligible for called shot!
            if (!weaponManager.canWeaponCall(weapon)) continue;

            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if (chance < 1) continue;

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

HeavyArray.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length > 1) return true;
    return false;
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

QuadParticleBeam.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

QuadParticleBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */

    if (this.firingMode == 2 && this.fireOrders.length > 3) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1;

        if (system) {
            //check if weapon is eligible for called shot!
            if (!weaponManager.canWeaponCall(weapon)) continue;

            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if (chance < 1) continue;

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

QuadParticleBeam.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length > 3) return true;
    return false;
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

ParticleCutter.prototype.initializationUpdate = function () {
    var ship = this.ship;
    if (gamedata.gamephase !== -2 && shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0) {
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
        this.data["Current Target"] = target.name;
    } else {
        delete this.data["Current Target"];
    }

    return this;
};

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

ParticleRepeater.prototype.initializationUpdate = function () {

    if (gamedata.gamephase == 3) {
        this.data["Shots Remaining"] = (this.data["Number of shots"] - this.getShotsFired());
    } else {
        delete this.data["Shots Remaining"];
    }

    if (this.canSplitShots && this.fireOrders.length > 0) {
        if (!weaponManager.isSelectedWeapon(this)) {
            webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
        } else if (weaponManager.isSelectedWeapon(this) && this.target) {
            webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
            var initialShot = this.fireOrders[0];
            var initialTarget = gamedata.getShip(initialShot.targetid);
            this.target = initialTarget;
            webglScene.customEvent("ShowTargetedHexagonInArc", { shooter: this.ship, target: this.target, system: this, size: 1, color: 'orange', opacity: 0.15 });
        }
    } else { //No fireorders/can't split
        if (this.target) {
            webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
        }
    }

    return this;
};

ParticleRepeater.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    var hitmod = 5;
    if (this.fireOrders.length > 0) { //An initial shot has been made.
        if (this.getShotsFired() >= this.data["Number of shots"]) return; //But no point looking further if no more shots.
        var initialShot = this.fireOrders[0];
        var initialTarget = gamedata.getShip(initialShot.targetid);
        //var intialTargetPos = shipManager.getShipPosition(initialTarget);
        var distance = mathlib.getDistanceBetweenShipsInHex(initialTarget, target).toFixed(2);
        if (distance > 1) return; //Additonal target too far away
        if (this.fireOrders.length == 1) {
            hitmod = 10;
        } else {
            hitmod = 10;
        }
    }

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1;

        if (system) {
            //check if weapon is eligible for called shot!
            if (!weaponManager.canWeaponCall(weapon)) continue;

            // When the system is a subsystem, make all damage go through
            // the parent.
            while (system.parentId > 0) {
                system = shipManager.systems.getSystem(ship, system.parentId);
            }

            calledid = system.id;
        }

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if (chance < 1) continue;

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
            hitmod: hitmod,
            notes: "Split"
        };
        if (this.fireOrders.length == 0 && fireOrdersArray.length == 0) this.target = target; //store initial target to Particle Repeater object. 
        fireOrdersArray.push(fire); // Store each fire order
    }

    return fireOrdersArray; // Return all fire orders
};

ParticleRepeater.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
    var mod = 0;
    var currentShot = this.fireOrders.length + 1;

    if (currentShot == 1) return 0;
    if (currentShot == 2) return -1;

    mod -= 1 + 2 * (currentShot - 2);
    return mod;
};

ParticleRepeater.prototype.recalculateFireOrders = function (shooter, fireOrderNo) {

    for (let i = 0; i < this.fireOrders.length; i++) {
        const fireOrder = this.fireOrders[i];

        // Ensure we only include fireOrders for the current turn and weapon, and only fireORders AFTER the one we are currently removing.
        if (fireOrder.weaponid === this.id && fireOrder.turn === gamedata.turn && i > fireOrderNo) {
            fireOrder.chance += fireOrder.hitmod;
        }
    }

    var newAnchorIndex = 0;
    if (fireOrderNo === 0) {
        newAnchorIndex = 1;
    }

    //If there are orders remaining (considering we are about to remove one at fireOrderNo)
    //We check > newAnchorIndex because if length is 2, and we remove 0, newAnchor is 1. length is > 1. Valid.
    //If length is 1, remove 0. newAnchor 1. length !> 1. No anchor.
    if (this.fireOrders.length > newAnchorIndex) {
        var initialShot = this.fireOrders[newAnchorIndex];
        var initialTarget = gamedata.getShip(initialShot.targetid);

        if (fireOrderNo === 0) {
            if (this.target) {
                webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
            }
            this.target = initialTarget;
            //Ensure the new target sprite is shown immediately
            webglScene.customEvent("ShowTargetedHexagonInArc", { shooter: shooter, target: this.target, system: this, size: 1, color: 'orange', opacity: 0.2 });
        }

        //Validate all other shots against the new anchor
        //Iterate backwards to safely splice
        for (let i = this.fireOrders.length - 1; i >= 0; i--) {
            if (i === fireOrderNo) continue; //Skip the one being removed by weaponManager
            if (i === newAnchorIndex) continue; //Skip the new anchor

            var fireOrder = this.fireOrders[i];
            //Ensure it's this weapon's order
            if (fireOrder.weaponid !== this.id || fireOrder.turn !== gamedata.turn) continue;

            var checkTarget = gamedata.getShip(fireOrder.targetid);
            var distance = mathlib.getDistanceBetweenShipsInHex(initialTarget, checkTarget).toFixed(2);

            if (distance > 1) {
                this.fireOrders.splice(i, 1);
                //Notify UI of removal
                webglScene.customEvent('SplitOrderRemoved', { shooter: shooter, target: checkTarget });

                //Adjustment: If we removed an order that was BEFORE fireOrderNo (unlikely loop direction?), we'd need to adjust fireOrderNo?
                //But we only remove orders > 1 hex from new anchor.
                //And we are only changing anchor if fireOrderNo == 0.
                //So we are removing orders i > 0.
                //fireOrderNo is 0.
                //So i > fireOrderNo. Safe.
            }
        }
    } else {
        this.target = null;
    }
};

ParticleRepeater.prototype.checkFinished = function () {
    if (this.getShotsFired() == this.data["Number of shots"]) {
        webglScene.customEvent("RemoveTargetedHexagonInArc", { target: this.target, system: this });
        return true;
    }
    return false;
};

ParticleRepeater.prototype.getShotsFired = function () {
    var shots = 0;
    for (var i in this.fireOrders) {
        var order = this.fireOrders[i];
        shots += order.shots;
    }

    return shots;
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

TelekineticCutter.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

TelekineticCutter.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */

    if (this.firingMode == 2 && this.fireOrders.length > 1) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
        if (chance < 1) continue;

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

TelekineticCutter.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length > 1) return true;
    return false;
};

var MinorThoughtPulsar = function MinorThoughtPulsar(json, ship) {
    Particle.call(this, json, ship);
};
MinorThoughtPulsar.prototype = Object.create(Particle.prototype);
MinorThoughtPulsar.prototype.constructor = MinorThoughtPulsar;

MinorThoughtPulsar.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
    var mod = 0;
    var thrust = shipManager.movement.getRemainingEngineThrust(shooter);
    var boostLevel = Math.floor(thrust / 3);

    switch (this.firingMode) {

        case 1:
            if (boostLevel < 3) { //All put into extra shots, not hit chance mod.
                return 0;
            } else {
                boostLevel = boostLevel - 2; //Deduct first two shots.
                mod += boostLevel * 2;
            }
            break;

        case 2:
            if (boostLevel < 3) { //All put into extra damage, not hit chance mod.
                return 0;
            } else {
                boostLevel = boostLevel - 2; //Deduct first two boosts.
                mod += boostLevel * 2;
            }
            break;

        case 3:
            mod += boostLevel * 2; //All dumped into hit chance.
            break;

        case 4:
            if (boostLevel < 5) { //All put into extra damage, not hit chance mod.
                return 0;
            } else {
                boostLevel = boostLevel - 4; //Deduct first four boosts.
                mod += boostLevel * 2;
            }
            break;

        case 5:
            //Never boosts hit chance.
            break;

    }

    return mod;
};
