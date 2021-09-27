var TrekPhaseCannon = function TrekPhaseCannon(json, ship) {
    Weapon.call(this, json, ship);
};
TrekPhaseCannon.prototype = Object.create(Weapon.prototype);
TrekPhaseCannon.prototype.constructor = TrekPhaseCannon;

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