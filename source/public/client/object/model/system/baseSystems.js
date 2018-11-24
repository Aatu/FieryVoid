"use strict";

var Reactor = function Reactor(json, ship) {
  ShipSystem.call(this, json, ship);
};
Reactor.prototype = Object.create(ShipSystem.prototype);
Reactor.prototype.constructor = Reactor;
Reactor.prototype.hasMaxBoost = function() {
  return true;
};

var MagGravReactor = function MagGravReactor(json, ship) {
  Reactor.call(this, json, ship);
};
MagGravReactor.prototype = Object.create(Reactor.prototype);
MagGravReactor.prototype.constructor = MagGravReactor;
MagGravReactor.prototype.hasMaxBoost = function() {
  return true;
};

var Scanner = function Scanner(json, ship) {
  ShipSystem.call(this, json, ship);
};
Scanner.prototype = Object.create(ShipSystem.prototype);
Scanner.prototype.constructor = Scanner;
Scanner.prototype.isScanner = function() {
  return true;
};

var SWScanner = function SWScanner(json, ship) {
  Scanner.call(this, json, ship);
};
SWScanner.prototype = Object.create(Scanner.prototype);
SWScanner.prototype.constructor = SWScanner;
SWScanner.prototype.hasMaxBoost = function() {
  return true;
};

var ElintScanner = function ElintScanner(json, ship) {
  Scanner.call(this, json, ship);
};
ElintScanner.prototype = Object.create(Scanner.prototype);
ElintScanner.prototype.constructor = ElintScanner;

var Engine = function Engine(json, ship) {
  ShipSystem.call(this, json, ship);
};
Engine.prototype = Object.create(ShipSystem.prototype);
Engine.prototype.constructor = Engine;
Engine.prototype.addInfo = function() {
  this.data["Effiency"] = this.boostEfficiency;
};

//  this.data["Weapon type"] ="Gravitic";
//  this.data["Damage type"] ="Standard";

var CnC = function CnC(json, ship) {
  ShipSystem.call(this, json, ship);
};
CnC.prototype = Object.create(ShipSystem.prototype);
CnC.prototype.constructor = CnC;

var Thruster = function Thruster(json, ship) {
  ShipSystem.call(this, json, ship);
};
Thruster.prototype = Object.create(ShipSystem.prototype);
Thruster.prototype.constructor = Thruster;

var ManouveringThruster = function ManouveringThruster(json, ship) {
  ShipSystem.call(this, json, ship);
};
ManouveringThruster.prototype = Object.create(ShipSystem.prototype);
ManouveringThruster.prototype.constructor = ManouveringThruster;

var GraviticThruster = function GraviticThruster(json, ship) {
  Thruster.call(this, json, ship);
};
GraviticThruster.prototype = Object.create(Thruster.prototype);
GraviticThruster.prototype.constructor = GraviticThruster;

var MagGraviticThruster = function(json, ship) {
  Thruster.call(this, json, ship);
};
MagGraviticThruster.prototype = Object.create(Thruster.prototype);
MagGraviticThruster.prototype.constructor = MagGraviticThruster;

var Hangar = function(json, ship) {
  ShipSystem.call(this, json, ship);
};
Hangar.prototype = Object.create(ShipSystem.prototype);
Hangar.prototype.constructor = Hangar;

var Catapult = function Catapult(json, ship) {
  ShipSystem.call(this, json, ship);
};
Catapult.prototype = Object.create(ShipSystem.prototype);
Catapult.prototype.constructor = Catapult;

var CargoBay = function CargoBay(json, ship) {
  ShipSystem.call(this, json, ship);
};
CargoBay.prototype = Object.create(ShipSystem.prototype);
CargoBay.prototype.constructor = CargoBay;

var JumpEngine = function JumpEngine(json, ship) {
  ShipSystem.call(this, json, ship);
};
JumpEngine.prototype = Object.create(ShipSystem.prototype);
JumpEngine.prototype.constructor = JumpEngine;

var Structure = function Structure(json, ship) {
  ShipSystem.call(this, json, ship);
};
Structure.prototype = Object.create(ShipSystem.prototype);
Structure.prototype.constructor = Structure;

var Jammer = function Jammer(json, ship) {
  ShipSystem.call(this, json, ship);
};
Jammer.prototype = Object.create(ShipSystem.prototype);
Jammer.prototype.constructor = Jammer;

var Stealth = function Stealth(json, ship) {
  ShipSystem.call(this, json, ship);
};
Stealth.prototype = Object.create(ShipSystem.prototype);
Stealth.prototype.constructor = Stealth;

var Stealth = function Stealth(json, ship) {
  ShipSystem.call(this, json, ship);
};
Stealth.prototype = Object.create(ShipSystem.prototype);
Stealth.prototype.constructor = Stealth;

var HkControlNode = function HkControlNode(json, ship) {
  ShipSystem.call(this, json, ship);
};
HkControlNode.prototype = Object.create(ShipSystem.prototype);
HkControlNode.prototype.constructor = HkControlNode;

var DrakhRaiderController = function DrakhRaiderController(json, ship) {
  ShipSystem.call(this, json, ship);
};
DrakhRaiderController.prototype = Object.create(ShipSystem.prototype);
DrakhRaiderController.prototype.constructor = DrakhRaiderController;
DrakhRaiderController.prototype.hasMaxBoost = function() {
  return true;
};
