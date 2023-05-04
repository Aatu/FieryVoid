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