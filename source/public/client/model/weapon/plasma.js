"use strict";

var Plasma = function Plasma(json, ship) {
    Weapon.call(this, json, ship);
};
Plasma.prototype = Object.create(Weapon.prototype);
Plasma.prototype.constructor = Plasma;

var PlasmaAccelerator = function PlasmaAccelerator(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaAccelerator.prototype = Object.create(Plasma.prototype);
PlasmaAccelerator.prototype.constructor = PlasmaAccelerator;

var MagGun = function MagGun(json, ship) {
    Plasma.call(this, json, ship);
};
MagGun.prototype = Object.create(Plasma.prototype);
MagGun.prototype.constructor = MagGun;

var HeavyPlasma = function HeavyPlasma(json, ship) {
    Plasma.call(this, json, ship);
};
HeavyPlasma.prototype = Object.create(Plasma.prototype);
HeavyPlasma.prototype.constructor = HeavyPlasma;

var MediumPlasma = function MediumPlasma(json, ship) {
    Plasma.call(this, json, ship);
};
MediumPlasma.prototype = Object.create(Plasma.prototype);
MediumPlasma.prototype.constructor = MediumPlasma;

var LightPlasma = function LightPlasma(json, ship) {
    Plasma.call(this, json, ship);
};
LightPlasma.prototype = Object.create(Plasma.prototype);
LightPlasma.prototype.constructor = LightPlasma;

var PlasmaStream = function PlasmaStream(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaStream.prototype = Object.create(Plasma.prototype);
PlasmaStream.prototype.constructor = PlasmaStream;

var PlasmaTorch = function PlasmaTorch(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaTorch.prototype = Object.create(Plasma.prototype);
PlasmaTorch.prototype.constructor = PlasmaTorch;

var PairedPlasmaBlaster = function PairedPlasmaBlaster(json, ship) {
    Plasma.call(this, json, ship);
};
PairedPlasmaBlaster.prototype = Object.create(Plasma.prototype);
PairedPlasmaBlaster.prototype.constructor = PairedPlasmaBlaster;

var PlasmaGun = function PlasmaGun(json, ship) {
    Plasma.call(this, json, ship);
};
PlasmaGun.prototype = Object.create(Plasma.prototype);
PlasmaGun.prototype.constructor = PlasmaGun;

var RogolonLtPlasmaGun = function RogolonLtPlasmaGun(json, ship) {
    Plasma.call(this, json, ship);
};
RogolonLtPlasmaGun.prototype = Object.create(Plasma.prototype);
RogolonLtPlasmaGun.prototype.constructor = RogolonLtPlasmaGun;

var RogolonLtPlasmaCannon = function RogolonLtPlasmaCannon(json, ship) {
    Plasma.call(this, json, ship);
};
RogolonLtPlasmaCannon.prototype = Object.create(Plasma.prototype);
RogolonLtPlasmaCannon.prototype.constructor = RogolonLtPlasmaCannon;

var LightPlasmaAccelerator = function LightPlasmaAccelerator(json, ship) {
    Weapon.call(this, json, ship);
};
LightPlasmaAccelerator.prototype = Object.create(Weapon.prototype);
LightPlasmaAccelerator.prototype.constructor = LightPlasmaAccelerator;

var HeavyPlasmaBolter = function HeavyPlasmaBolter(json, ship) {
    Plasma.call(this, json, ship);
};
HeavyPlasmaBolter.prototype = Object.create(Plasma.prototype);
HeavyPlasmaBolter.prototype.constructor = HeavyPlasmaBolter;

var MediumPlasmaBolter = function MediumPlasmaBolter(json, ship) {
    Plasma.call(this, json, ship);
};
MediumPlasmaBolter.prototype = Object.create(Plasma.prototype);
MediumPlasmaBolter.prototype.constructor = MediumPlasmaBolter;

var LightPlasmaBolter = function LightPlasmaBolter(json, ship) {
    Plasma.call(this, json, ship);
};
LightPlasmaBolter.prototype = Object.create(Plasma.prototype);
LightPlasmaBolter.prototype.constructor = LightPlasmaBolter;

var LightPlasmaBolterFighter = function LightPlasmaBolterFighter(json, ship) {
    Plasma.call(this, json, ship);
};
LightPlasmaBolterFighter.prototype = Object.create(Plasma.prototype);
LightPlasmaBolterFighter.prototype.constructor = LightPlasmaBolterFighter;

var DualPlasmaCannon = function DualPlasmaCannon(json, ship) {
    Plasma.call(this, json, ship);
};
DualPlasmaCannon.prototype = Object.create(Plasma.prototype);
DualPlasmaCannon.prototype.constructor = DualPlasmaCannon;

var MegaPlasma = function MegaPlasma(json, ship) {
    Weapon.call(this, json, ship);
};
MegaPlasma.prototype = Object.create(Weapon.prototype);
MegaPlasma.prototype.constructor = MegaPlasma;

var PlasmaProjector = function PlasmaProjector(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaProjector.prototype = Object.create(Weapon.prototype);
PlasmaProjector.prototype.constructor = PlasmaProjector;

var PlasmaBlast = function PlasmaBlast(json, ship) {
    Weapon.call(this, json, ship);
};
PlasmaBlast.prototype = Object.create(Weapon.prototype);
PlasmaBlast.prototype.constructor = PlasmaBlast;