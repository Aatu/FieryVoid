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



var TrekWarpDrive = function TrekWarpDrive(json, ship) {
    ShipSystem.call(this, json, ship);
};
TrekWarpDrive.prototype = Object.create(ShipSystem.prototype);
TrekWarpDrive.prototype.constructor = TrekWarpDrive;