"use strict";

var DirectWeaponAmmo = function DirectWeaponAmmo(json, ship) {
    Weapon.call(this, json, ship);
};
DirectWeaponAmmo.prototype = Object.create(Weapon.prototype);
DirectWeaponAmmo.prototype.constructor = DirectWeaponAmmo;

var AmmoHeavyRailGun = function AmmoHeavyRailGun(json, ship) {
    Weapon.call(this, json, ship);
};
AmmoHeavyRailGun.prototype = Object.create(Weapon.prototype);
AmmoHeavyRailGun.prototype.constructor = AmmoHeavyRailGun;

var AmmoMediumRailGun = function AmmoMediumRailGun(json, ship) {
    Weapon.call(this, json, ship);
};
AmmoMediumRailGun.prototype = Object.create(Weapon.prototype);
AmmoMediumRailGun.prototype.constructor = AmmoMediumRailGun;

var AmmoLightRailGun = function AmmoLightRailGun(json, ship) {
    Weapon.call(this, json, ship);
};
AmmoLightRailGun.prototype = Object.create(Weapon.prototype);
AmmoLightRailGun.prototype.constructor = AmmoLightRailGun;

