var TrekLtPhaseCannon = function TrekLtPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLtPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekLtPhaseCannon.prototype.constructor = TrekLtPhaseCannon;

var TrekFtrPhaseCannon = function TrekFtrPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekFtrPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekFtrPhaseCannon.prototype.constructor = TrekFtrPhaseCannon;

var TrekFtrPhaser = function TrekFtrPhaser(json, ship) {
    Weapon.call(this, json, ship);
};
TrekFtrPhaser.prototype = Object.create(Weapon.prototype);
TrekFtrPhaser.prototype.constructor = TrekFtrPhaser;

var TrekPhaseCannon = function TrekPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekPhaseCannon.prototype.constructor = TrekPhaseCannon;

var TrekHvyPhaseCannon = function TrekHvyPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekHvyPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekHvyPhaseCannon.prototype.constructor = TrekHvyPhaseCannon;


var TrekPhasedPulseAccelerator = function TrekPhasedPulseAccelerator(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhasedPulseAccelerator.prototype = Object.create(Weapon.prototype);
TrekPhasedPulseAccelerator.prototype.constructor = TrekPhasedPulseAccelerator;

var TrekPhasedPulseCannon = function TrekPhasedPulseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhasedPulseCannon.prototype = Object.create(Weapon.prototype);
TrekPhasedPulseCannon.prototype.constructor = TrekPhasedPulseCannon;


var TrekPhotonicTorp = function TrekPhotonicTorp(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhotonicTorp.prototype = Object.create(Weapon.prototype);
TrekPhotonicTorp.prototype.constructor = TrekPhotonicTorp;

var TrekSpatialTorp = function TrekSpatialTorp(json, ship) {
    Weapon.call(this, json, ship);
};
TrekSpatialTorp.prototype = Object.create(Weapon.prototype);
TrekSpatialTorp.prototype.constructor = TrekSpatialTorp;


var TrekWarpDrive = function TrekWarpDrive(json, ship) {
    ShipSystem.call(this, json, ship);
};
TrekWarpDrive.prototype = Object.create(ShipSystem.prototype);
TrekWarpDrive.prototype.constructor = TrekWarpDrive;

var TrekImpulseDrive = function TrekImpulseDrive(json, ship) {
    Engine.call(this, json, ship);
};
TrekImpulseDrive.prototype = Object.create(Engine.prototype);
TrekImpulseDrive.prototype.constructor = TrekImpulseDrive;

var TrekShieldProjection = function TrekShieldProjection(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
TrekShieldProjection.prototype = Object.create(ShipSystem.prototype);
TrekShieldProjection.prototype.constructor = TrekShieldProjection;
TrekShieldProjection.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};

var TrekShieldProjector = function TrekShieldProjector(json, ship) {
    ShipSystem.call(this, json, ship);
    this.defensiveType = "none";
};
TrekShieldProjector.prototype = Object.create(ShipSystem.prototype);
TrekShieldProjector.prototype.constructor = TrekShieldProjector;
TrekShieldProjector.prototype.hasMaxBoost = function () {
    return true;
};
TrekShieldProjector.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
TrekShieldProjector.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
    //this is made to be a shield just to display arc visually, no actual protection
    return 0;
};

var TrekShieldFtr = function TrekShieldFtr(json, ship) {
    ShipSystem.call(this, json, ship);
};
TrekShieldFtr.prototype = Object.create(ShipSystem.prototype);
TrekShieldFtr.prototype.constructor = TrekShieldFtr;


var TrekPhotonTorp = function TrekPhotonTorp(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhotonTorp.prototype = Object.create(Weapon.prototype);
TrekPhotonTorp.prototype.constructor = TrekPhotonTorp;

var TrekPhaser = function TrekPhaser(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaser.prototype = Object.create(Weapon.prototype);
TrekPhaser.prototype.constructor = TrekPhaser;

var TrekPhaserLance = function TrekPhaserLance(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaserLance.prototype = Object.create(Weapon.prototype);
TrekPhaserLance.prototype.constructor = TrekPhaserLance;

var TrekLightPhaser = function TrekLightPhaser(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLightPhaser.prototype = Object.create(Weapon.prototype);
TrekLightPhaser.prototype.constructor = TrekLightPhaser;

var TrekLightPhaserLance = function TrekLightPhaserLance(json, ship) {
    Weapon.call(this, json, ship);
};
TrekLightPhaserLance.prototype = Object.create(Weapon.prototype);
TrekLightPhaserLance.prototype.constructor = TrekLightPhaserLance;

var HvyPlasmaProjector = function HvyPlasmaProjector(json, ship) {
    Weapon.call(this, json, ship);
};
HvyPlasmaProjector.prototype = Object.create(Weapon.prototype);
HvyPlasmaProjector.prototype.constructor = HvyPlasmaProjector;

var LtPlasmaProjector = function LtPlasmaProjector(json, ship) {
    Weapon.call(this, json, ship);
};
LtPlasmaProjector.prototype = Object.create(Weapon.prototype);
LtPlasmaProjector.prototype.constructor = LtPlasmaProjector;

var TrekPlasmaBurst = function TrekPlasmaBurst(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPlasmaBurst.prototype = Object.create(Weapon.prototype);
TrekPlasmaBurst.prototype.constructor = TrekPlasmaBurst;
TrekPlasmaBurst.prototype.clearBoost = function () {
    for (var i in system.power) {
        var power = system.power[i];
        if (power.turn != gamedata.turn) continue;

        if (power.type == 2) {
            system.power.splice(i, 1);

            return;
        }
    }
};
TrekPlasmaBurst.prototype.hasMaxBoost = function () {
    return true;
};
TrekPlasmaBurst.prototype.getMaxBoost = function () {
    return this.maxBoostLevel;
};
TrekPlasmaBurst.prototype.initBoostableInfo = function () {
    switch (shipManager.power.getBoost(this)) {
        case 0:
            this.data["Damage"] = '2-12';
            this.data["Boostlevel"] = '0';
            break;
        case 1:
            this.data["Damage"] = '4 - 24';
            this.data["Boostlevel"] = '1';
            break;

        default:
            this.data["Damage"] = '2-12';
            this.data["Boostlevel"] = '0';
            break;
    }
    return this;
};


var TrekFtrPhotonTorpedo = function TrekFtrPhotonTorpedo(json, ship) {
    FighterMissileRack.call(this, json, ship);
};
TrekFtrPhotonTorpedo.prototype = Object.create(FighterMissileRack.prototype);
TrekFtrPhotonTorpedo.prototype.constructor = TrekFtrPhotonTorpedo;

var TrekFtrPhotonTorpedoAmmo = function TrekFtrPhotonTorpedoAmmo(json, ship) {
    Ammo.call(this, json, ship);
    this.range = 10;
    this.distanceRange = 20;
    this.hitChanceMod = 0;
};
TrekFtrPhotonTorpedoAmmo.prototype = Object.create(Ammo.prototype);
TrekFtrPhotonTorpedoAmmo.prototype.constructor = TrekFtrPhotonTorpedoAmmo;
