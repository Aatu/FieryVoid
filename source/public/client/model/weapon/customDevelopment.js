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