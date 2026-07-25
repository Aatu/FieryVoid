var LaserArray = function  LaserArray(json, ship) {
    Weapon.call(this, json, ship);
};
LaserArray.prototype = Object.create(Weapon.prototype);
LaserArray.prototype.constructor =  LaserArray;

var PlasmaSiegeCannon = function  PlasmaSiegeCannon(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaSiegeCannon.prototype = Object.create(Weapon.prototype);
PlasmaSiegeCannon.prototype.constructor =  PlasmaSiegeCannon;

var ImpHeavyLaser = function  ImpHeavyLaser(json, ship) {
    Weapon.call(this, json, ship);
};
ImpHeavyLaser.prototype = Object.create(Weapon.prototype);
ImpHeavyLaser.prototype.constructor =  ImpHeavyLaser;

var DirectEMine = function  DirectEMine(json, ship) {
    Weapon.call(this, json, ship);
};
DirectEMine.prototype = Object.create(Weapon.prototype);
DirectEMine.prototype.constructor =  DirectEMine;

var AncientMatterGun = function  AncientMatterGun(json, ship) {
    Weapon.call(this, json, ship);
};
AncientMatterGun.prototype = Object.create(Weapon.prototype);
AncientMatterGun.prototype.constructor =  AncientMatterGun;

var AncientPlasmaGun = function  AncientPlasmaGun(json, ship) {
    Weapon.call(this, json, ship);
};
AncientPlasmaGun.prototype = Object.create(Weapon.prototype);
AncientPlasmaGun.prototype.constructor =  AncientPlasmaGun;

var AncientParticleGun = function  AncientParticleGun(json, ship) {
    Weapon.call(this, json, ship);
};
AncientParticleGun.prototype = Object.create(Weapon.prototype);
AncientParticleGun.prototype.constructor =  AncientParticleGun;

var AncientParticleCannon = function  AncientParticleCannon(json, ship) {
    Weapon.call(this, json, ship);
};
AncientParticleCannon.prototype = Object.create(Weapon.prototype);
AncientParticleCannon.prototype.constructor =  AncientParticleCannon;

var AncientAntimatter = function AncientAntimatter(json, ship) {
    AntimatterWeapon.call(this, json, ship);
};
AncientAntimatter.prototype = Object.create(AntimatterWeapon.prototype);
AncientAntimatter.prototype.constructor = AncientAntimatter;

var AncientIonTorpedo = function AncientIonTorpedo(json, ship) {
    Torpedo.call(this, json, ship);
};
AncientIonTorpedo.prototype = Object.create(Torpedo.prototype);
AncientIonTorpedo.prototype.constructor = AncientIonTorpedo;

var AncientBurstBeam = function AncientBurstBeam(json, ship) {
    Electromagnetic.call(this, json, ship);
};
AncientBurstBeam.prototype = Object.create(Electromagnetic.prototype);
AncientBurstBeam.prototype.constructor = AncientBurstBeam;

var AncientMolecularDisruptor = function AncientMolecularDisruptor(json, ship) {
    Molecular.call(this, json, ship);
};
AncientMolecularDisruptor.prototype = Object.create(Molecular.prototype);
AncientMolecularDisruptor.prototype.constructor = AncientMolecularDisruptor;

var AncientShockCannon = function AncientShockCannon(json, ship) {
    Electromagnetic.call(this, json, ship);
};
AncientShockCannon.prototype = Object.create(Electromagnetic.prototype);
AncientShockCannon.prototype.constructor = AncientShockCannon;

var AncientPlasmaArc = function  AncientPlasmaArc(json, ship) {
    Weapon.call(this, json, ship);
};
AncientPlasmaArc.prototype = Object.create(Weapon.prototype);
AncientPlasmaArc.prototype.constructor =  AncientPlasmaArc;

var AncientParticleCutter = function AncientParticleCutter(json, ship) {
    Particle.call(this, json, ship);
};
AncientParticleCutter.prototype = Object.create(Particle.prototype);
AncientParticleCutter.prototype.constructor = AncientParticleCutter;

var NeutronBlaster = function NeutronBlaster(json, ship) {
    Weapon.call(this, json, ship);
};
NeutronBlaster.prototype = Object.create(Weapon.prototype);
NeutronBlaster.prototype.constructor = NeutronBlaster;

NeutronBlaster.prototype.initializationUpdate = function () {
    if (this.firingMode == 2) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

NeutronBlaster.prototype.doMultipleFireOrders = function (shooter, target, system) {

    var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.
    /*
    if (this.fireOrders.length > 0) {
        if (this.fireOrders.length >= this.guns) {
            // All guns already fired → retarget one gun by removing oldest fireorder.
            this.fireOrders.splice(0, 1);
        }
    } 
    */
    if (this.firingMode == 2 && this.fireOrders.length >= this.guns) return;

    var fireOrdersArray = []; // Store multiple fire orders

    for (var s = 0; s < shotsOnTarget; s++) {
        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var calledid = -1; //Raking, cannot called shot.       

        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
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

NeutronBlaster.prototype.checkFinished = function () {
    if (this.firingMode == 2 && this.fireOrders.length >= this.guns) return true;
    return false;
};

var NeutronBlasterFtr = function NeutronBlasterFtr(json, ship) {
    Weapon.call(this, json, ship);
};
NeutronBlasterFtr.prototype = Object.create(Weapon.prototype);
NeutronBlasterFtr.prototype.constructor = NeutronBlasterFtr;

var FusionBomb = function  FusionBomb(json, ship) {
    Torpedo.call(this, json, ship);
};
FusionBomb.prototype = Object.create(Weapon.prototype);
FusionBomb.prototype.constructor =  FusionBomb;

var SeekerTorp = function  SeekerTorp(json, ship) {
    Torpedo.call(this, json, ship);
};
SeekerTorp.prototype = Object.create(Weapon.prototype);
SeekerTorp.prototype.constructor =  SeekerTorp;

var PlasmaDriver = function PlasmaDriver(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaDriver.prototype = Object.create(Weapon.prototype);
PlasmaDriver.prototype.constructor = PlasmaDriver;