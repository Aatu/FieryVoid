"use strict";

var Matter = function Matter(json, ship) {
    Weapon.call(this, json, ship);
};
Matter.prototype = Object.create(Weapon.prototype);
Matter.prototype.constructor = Matter;

var MatterCannon = function MatterCannon(json, ship) {
    Matter.call(this, json, ship);
};
MatterCannon.prototype = Object.create(Matter.prototype);
MatterCannon.prototype.constructor = MatterCannon;

var HeavyRailGun = function HeavyRailGun(json, ship) {
    Matter.call(this, json, ship);
};
HeavyRailGun.prototype = Object.create(Matter.prototype);
HeavyRailGun.prototype.constructor = HeavyRailGun;

var RailGun = function RailGun(json, ship) {
    Matter.call(this, json, ship);
};
RailGun.prototype = Object.create(Matter.prototype);
RailGun.prototype.constructor = RailGun;

var LightRailGun = function LightRailGun(json, ship) {
    Matter.call(this, json, ship);
};
LightRailGun.prototype = Object.create(Matter.prototype);
LightRailGun.prototype.constructor = LightRailGun;

var MassDriver = function MassDriver(json, ship) {
    Matter.call(this, json, ship);
};
MassDriver.prototype = Object.create(Matter.prototype);
MassDriver.prototype.constructor = MassDriver;

var GaussCannon = function GaussCannon(json, ship) {
    Matter.call(this, json, ship);
};
GaussCannon.prototype = Object.create(Matter.prototype);
GaussCannon.prototype.constructor = GaussCannon;

var HeavyGaussCannon = function HeavyGaussCannon(json, ship) {
    Matter.call(this, json, ship);
};
HeavyGaussCannon.prototype = Object.create(Matter.prototype);
HeavyGaussCannon.prototype.constructor = HeavyGaussCannon;

var RapidGatling = function RapidGatling(json, ship) {
    Matter.call(this, json, ship);
};
RapidGatling.prototype = Object.create(Matter.prototype);
RapidGatling.prototype.constructor = RapidGatling;

var PairedGatlingGun = function PairedGatlingGun(json, ship) {
    Matter.call(this, json, ship);
};
PairedGatlingGun.prototype = Object.create(Matter.prototype);
PairedGatlingGun.prototype.constructor = PairedGatlingGun;

var MatterGun = function MatterGun(json, ship) {
    Matter.call(this, json, ship);
};
MatterGun.prototype = Object.create(Matter.prototype);
MatterGun.prototype.constructor = MatterGun;

var FlakCannon = function FlakCannon(json, ship) {
    Matter.call(this, json, ship);
};
FlakCannon.prototype = Object.create(Matter.prototype);
FlakCannon.prototype.constructor = FlakCannon;

var SlugCannon = function SlugCannon(json, ship) {
    Matter.call(this, json, ship);
};
SlugCannon.prototype = Object.create(Matter.prototype);
SlugCannon.prototype.constructor = SlugCannon;