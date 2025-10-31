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