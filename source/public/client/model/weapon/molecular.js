"use strict";

var Molecular = function Molecular(json, ship) {
    Weapon.call(this, json, ship);
};
Molecular.prototype = Object.create(Weapon.prototype);
Molecular.prototype.constructor = Molecular;

var FusionCannon = function FusionCannon(json, ship) {
    Molecular.call(this, json, ship);
};
FusionCannon.prototype = Object.create(Molecular.prototype);
FusionCannon.prototype.constructor = FusionCannon;

var HeavyFusionCannon = function HeavyFusionCannon(json, ship) {
    Molecular.call(this, json, ship);
};
HeavyFusionCannon.prototype = Object.create(Molecular.prototype);
HeavyFusionCannon.prototype.constructor = HeavyFusionCannon;

var LightfusionCannon = function LightfusionCannon(json, ship) {
    Molecular.call(this, json, ship);
};
LightfusionCannon.prototype = Object.create(Molecular.prototype);
LightfusionCannon.prototype.constructor = LightfusionCannon;

var MolecularDisruptor = function MolecularDisruptor(json, ship) {
    Molecular.call(this, json, ship);
};
MolecularDisruptor.prototype = Object.create(Molecular.prototype);
MolecularDisruptor.prototype.constructor = MolecularDisruptor;

var DestabilizerBeam = function DestabilizerBeam(json, ship) {
    Molecular.call(this, json, ship);
};
DestabilizerBeam.prototype = Object.create(Molecular.prototype);
DestabilizerBeam.prototype.constructor = DestabilizerBeam;

var MolecularFlayer = function MolecularFlayer(json, ship) {
    Molecular.call(this, json, ship);
};
MolecularFlayer.prototype = Object.create(Molecular.prototype);
MolecularFlayer.prototype.constructor = MolecularFlayer;

var FusionAgitator = function FusionAgitator(json, ship) {
    Molecular.call(this, json, ship);
};
FusionAgitator.prototype = Object.create(Molecular.prototype);
FusionAgitator.prototype.constructor = FusionAgitator;

FusionAgitator.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};

FusionAgitator.prototype.hasMaxBoost = function () {
    return true;
};

FusionAgitator.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};

FusionAgitator.prototype.initBoostableInfo = function () {
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '15 - 60';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '16 - 70';
            this.data["Boostlevel"] = '1';
            break;
        case 2:
            this.data["Damage"] = '17 - 80';
            this.data["Boostlevel"] = '2';
            break;
        case 3:
            this.data["Damage"] = '18 - 90';
            this.data["Boostlevel"] = '3';
            break;
        case 4:
            this.data["Damage"] = '19 - 100';
            this.data["Boostlevel"] = '4';
            break;
        default:
            this.data["Damage"] = '15 - 60';
            this.data["Boostlevel"] = '0';
            break;
    }
    return this;
};



var FtrPolarityCannon = function FtrPolarityCannon(json, ship) {
    Weapon.call(this, json, ship);
};
FtrPolarityCannon.prototype = Object.create(Weapon.prototype);
FtrPolarityCannon.prototype.constructor = FtrPolarityCannon;


var MolecularSlicerBeamL = function MolecularSlicerBeamL(json, ship) {
    Weapon.call(this, json, ship);
};
MolecularSlicerBeamL.prototype = Object.create(Weapon.prototype);
MolecularSlicerBeamL.prototype.constructor = MolecularSlicerBeamL;

var MolecularSlicerBeamM = function MolecularSlicerBeamM(json, ship) {
    Weapon.call(this, json, ship);
};
MolecularSlicerBeamM.prototype = Object.create(Weapon.prototype);
MolecularSlicerBeamM.prototype.constructor = MolecularSlicerBeamM;

var MolecularSlicerBeamH = function MolecularSlicerBeamH(json, ship) {
    Weapon.call(this, json, ship);
};
MolecularSlicerBeamH.prototype = Object.create(Weapon.prototype);
MolecularSlicerBeamH.prototype.constructor = MolecularSlicerBeamH;


var MultiphasedCutterL = function MultiphasedCutterL(json, ship) {
    Weapon.call(this, json, ship);
};
MultiphasedCutterL.prototype = Object.create(Weapon.prototype);
MultiphasedCutterL.prototype.constructor = MultiphasedCutterL;

var MultiphasedCutter = function MultiphasedCutter(json, ship) {
    Weapon.call(this, json, ship);
};
MultiphasedCutter.prototype = Object.create(Weapon.prototype);
MultiphasedCutter.prototype.constructor = MultiphasedCutter;

