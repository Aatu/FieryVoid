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
    // guns is 2 for Mode 1, 1 for Modes 2/3 - this works the same regardless.
    this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    return this;
};
/* True once this blaster has been held unfired long enough for the given mode - Mode N
 * needs turnsloaded >= N (Mode 2 needs 2 turns of charge, Mode 3 needs 3). Hardcoded to
 * turnsloaded rather than reading a normalload property, since normalload isn't known to
 * be serialized to the client (mirrors how HypergravitonBlaster.js hardcodes its own
 * turnsloaded thresholds rather than reading normalload). */
NeutronBlaster.prototype.isCharged = function (blastersNeeded) {
    return this.turnsloaded >= blastersNeeded;
};
/* True if this blaster AND enough other NeutronBlasters on the same ship are all charged
 * for the currently-selected mode - the client-side mirror of the server's readyToCombine
 * gate. For this weapon, the firing mode number IS the blasters-needed count (Mode 2 needs
 * 2, Mode 3 needs 3), so no separate lookup table is needed. Used to stop the player from
 * declaring an incomplete combine in the first place, rather than letting it commit and
 * auto-miss (which would still cost every blaster involved its charge). */
NeutronBlaster.prototype.readyToCombine = function (shooter) {
    var blastersNeeded = this.firingMode;
    if (blastersNeeded < 2) return true; //Mode 1 doesn't combine, nothing to gate
    if (!this.isCharged(blastersNeeded)) return false;
    var readyPartners = 0;
    for (var i in shooter.systems) {
        var sys = shooter.systems[i];
        if (!sys || sys === this) continue;
        if (!(sys instanceof NeutronBlaster)) continue;
        if (sys.isCharged(blastersNeeded)) readyPartners++;
    }
    return readyPartners >= (blastersNeeded - 1);
};
/* Only reached at all when canSplitShots is true for the current mode - which, per
 * $canSplitShotsArray, is Modes 2/3. Mode 1 goes through weaponManager.targetShip()'s
 * default (non-split) path instead, which already handles firing twice per turn fine. */
NeutronBlaster.prototype.doMultipleFireOrders = function (shooter, target, system) {
    if (!this.readyToCombine(shooter)) return []; //not enough blasters charged - don't commit to firing
    if (this.fireOrders.length >= this.guns) return []; //already declared (guns is 1 for every mode)

    weaponManager.removeFiringOrder(shooter, this); //replace any prior order (e.g. re-targeting) now that we know this declaration will succeed

    var calledid = -1; //Raking, cannot called shot.
    var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
    if (chance < 1) return [];

    var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
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
        hitmod: 0
    };
    return [fire];
};
NeutronBlaster.prototype.checkFinished = function () {
    return this.fireOrders.length >= this.guns;
};




var NeutronBlasterFtr = function NeutronBlasterFtr(json, ship) {
    Weapon.call(this, json, ship);
};
NeutronBlasterFtr.prototype = Object.create(Weapon.prototype);
NeutronBlasterFtr.prototype.constructor = NeutronBlasterFtr;

var NeutronBeam = function NeutronBeam(json, ship) {
    Laser.call(this, json, ship);
};
NeutronBeam.prototype = Object.create(Laser.prototype);
NeutronBeam.prototype.constructor = NeutronBeam;

var NeutronCannon = function NeutronCannon(json, ship) {
    Laser.call(this, json, ship);
};
NeutronCannon.prototype = Object.create(Laser.prototype);
NeutronCannon.prototype.constructor = NeutronCannon;

var PlasmaArray = function PlasmaArray(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaArray.prototype = Object.create(Laser.prototype);
PlasmaArray.prototype.constructor = PlasmaArray;

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

// GTS_Triad
var SpatialCutter = function SpatialCutter(json, ship) {
    Weapon.call(this, json, ship);
};
SpatialCutter.prototype = Object.create(Weapon.prototype);
SpatialCutter.prototype.constructor = SpatialCutter;

var AsteroidSalvo = function AsteroidSalvo(json, ship) {
    Aoe.call(this, json, ship);
};
AsteroidSalvo.prototype = Object.create(Aoe.prototype);
AsteroidSalvo.prototype.constructor = AsteroidSalvo;