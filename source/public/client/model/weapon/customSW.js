"use strict";

var SWFighterLaser = function SWFighterLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWFighterLaser.prototype = Object.create(Weapon.prototype);
SWFighterLaser.prototype.constructor = SWFighterLaser;

var SWFighterIon = function SWFighterIon(json, ship) {
    Weapon.call(this, json, ship);
};
SWFighterIon.prototype = Object.create(Weapon.prototype);
SWFighterIon.prototype.constructor = SWFighterIon;

var SWFtrProtonTorpedo = function SWFtrProtonTorpedo(json, ship) {
    Ammo.call(this, json, ship);
    //this.range = 8;
    this.hitChanceMod = 0;
};
SWFtrProtonTorpedo.prototype = Object.create(Ammo.prototype);
SWFtrProtonTorpedo.prototype.constructor = SWFtrProtonTorpedo;

var SWFtrProtonTorpedoLauncher = function SWFtrProtonTorpedoLauncher(json, ship) {
    FighterMissileRack.call(this, json, ship);
};
SWFtrProtonTorpedoLauncher.prototype = Object.create(FighterMissileRack.prototype);
SWFtrProtonTorpedoLauncher.prototype.constructor = SWFtrProtonTorpedoLauncher;

var SWFtrConcMissile = function SWFtrConcMissile(json, ship) {
    Ammo.call(this, json, ship);
    //this.range = 6;
    this.hitChanceMod = 0;
};
SWFtrConcMissile.prototype = Object.create(Ammo.prototype);
SWFtrConcMissile.prototype.constructor = SWFtrConcMissile;

var SWFtrConcMissileLauncher = function SWFtrConcMissileLauncher(json, ship) {
    FighterMissileRack.call(this, json, ship);
};
SWFtrConcMissileLauncher.prototype = Object.create(FighterMissileRack.prototype);
SWFtrConcMissileLauncher.prototype.constructor = SWFtrConcMissileLauncher;

var SWLightLaser = function SWLightLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWLightLaser.prototype = Object.create(Weapon.prototype);
SWLightLaser.prototype.constructor = SWLightLaser;

var SWMediumLaser = function SWMediumLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWMediumLaser.prototype = Object.create(Weapon.prototype);
SWMediumLaser.prototype.constructor = SWMediumLaser;

var SWHeavyLaser = function SWHeavyLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWHeavyLaser.prototype = Object.create(Weapon.prototype);
SWHeavyLaser.prototype.constructor = SWHeavyLaser;

var SWLightTLaser = function SWLightTLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWLightTLaser.prototype = Object.create(Weapon.prototype);
SWLightTLaser.prototype.constructor = SWLightTLaser;

var SWMediumTLaser = function SWMediumTLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWMediumTLaser.prototype = Object.create(Weapon.prototype);
SWMediumTLaser.prototype.constructor = SWMediumTLaser;

var SWHeavyTLaser = function SWHeavyTLaser(json, ship) {
    Weapon.call(this, json, ship);
};
SWHeavyTLaser.prototype = Object.create(Weapon.prototype);
SWHeavyTLaser.prototype.constructor = SWHeavyTLaser;

var SWLightLaserE = function SWLightLaserE(json, ship) {
    Weapon.call(this, json, ship);
};
SWLightLaserE.prototype = Object.create(Weapon.prototype);
SWLightLaserE.prototype.constructor = SWLightLaserE;

var SWMediumLaserE = function SWMediumLaserE(json, ship) {
    Weapon.call(this, json, ship);
};
SWMediumLaserE.prototype = Object.create(Weapon.prototype);
SWMediumLaserE.prototype.constructor = SWMediumLaserE;

var SWHeavyLaserE = function SWHeavyLaserE(json, ship) {
    Weapon.call(this, json, ship);
};
SWHeavyLaserE.prototype = Object.create(Weapon.prototype);
SWHeavyLaserE.prototype.constructor = SWHeavyLaserE;

var SWLightTLaserE = function SWLightTLaserE(json, ship) {
    Weapon.call(this, json, ship);
};
SWLightTLaserE.prototype = Object.create(Weapon.prototype);
SWLightTLaserE.prototype.constructor = SWLightTLaserE;

var SWMediumTLaserE = function SWMediumTLaserE(json, ship) {
    Weapon.call(this, json, ship);
};
SWMediumTLaserE.prototype = Object.create(Weapon.prototype);
SWMediumTLaserE.prototype.constructor = SWMediumTLaserE;

var SWHeavyTLaserE = function SWHeavyTLaserE(json, ship) {
    Weapon.call(this, json, ship);
};
SWHeavyTLaserE.prototype = Object.create(Weapon.prototype);
SWHeavyTLaserE.prototype.constructor = SWHeavyTLaserE;

var SWMediumLaserAF = function SWMediumLaserAF(json, ship) {
    Weapon.call(this, json, ship);
};
SWMediumLaserAF.prototype = Object.create(Weapon.prototype);
SWMediumLaserAF.prototype.constructor = SWMediumLaserAF;

var SWLightIon = function SWLightIon(json, ship) {
    Weapon.call(this, json, ship);
};
SWLightIon.prototype = Object.create(Weapon.prototype);
SWLightIon.prototype.constructor = SWLightIon;

var SWMediumIon = function SWMediumIon(json, ship) {
    Weapon.call(this, json, ship);
};
SWMediumIon.prototype = Object.create(Weapon.prototype);
SWMediumIon.prototype.constructor = SWMediumIon;

var SWHeavyIon = function SWHeavyIon(json, ship) {
    Weapon.call(this, json, ship);
};
SWHeavyIon.prototype = Object.create(Weapon.prototype);
SWHeavyIon.prototype.constructor = SWHeavyIon;

var SWCapitalConcussion = function SWCapitalConcussion(json, ship) {
    Torpedo.call(this, json, ship);
};
SWCapitalConcussion.prototype = Object.create(Torpedo.prototype);
SWCapitalConcussion.prototype.constructor = SWCapitalConcussion;

var SWCapitalProton = function SWCapitalProton(json, ship) {
    Torpedo.call(this, json, ship);
};
SWCapitalProton.prototype = Object.create(Torpedo.prototype);
SWCapitalProton.prototype.constructor = SWCapitalProton;

var SWTractorBeam = function SWTractorBeam(json, ship) {
    Weapon.call(this, json, ship);
};
SWTractorBeam.prototype = Object.create(Weapon.prototype);
SWTractorBeam.prototype.constructor = SWTractorBeam;