var LaserArray = function  LaserArray(json, ship) {
    Weapon.call(this, json, ship);
};
LaserArray.prototype = Object.create(Weapon.prototype);
LaserArray.prototype.constructor =  LaserArray;

var FlexPlasma = function  FlexPlasma(json, ship) {
    Weapon.call(this, json, ship);
};
FlexPlasma.prototype = Object.create(Weapon.prototype);
FlexPlasma.prototype.constructor =  FlexPlasma;