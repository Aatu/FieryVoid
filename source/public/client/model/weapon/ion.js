"use strict";

var Ion = function Ion(json, ship) {
    Weapon.call(this, json, ship);
};
Ion.prototype = Object.create(Weapon.prototype);
Ion.prototype.constructor = Ion;

var IonBolt = function IonBolt(json, ship) {
    Ion.call(this, json, ship);
};
IonBolt.prototype = Object.create(Ion.prototype);
IonBolt.prototype.constructor = IonBolt;

var IonCannon = function IonCannon(json, ship) {
    Ion.call(this, json, ship);
};
IonCannon.prototype = Object.create(Ion.prototype);
IonCannon.prototype.constructor = IonCannon;

var ImprovedIonCannon = function ImprovedIonCannon(json, ship) {
    Ion.call(this, json, ship);
};
ImprovedIonCannon.prototype = Object.create(Ion.prototype);
ImprovedIonCannon.prototype.constructor = ImprovedIonCannon;