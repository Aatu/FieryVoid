"use strict";

var Laser = function Laser(json, ship) {
    Weapon.call(this, json, ship);
};
Laser.prototype = Object.create(Weapon.prototype);
Laser.prototype.constructor = Laser;

var HeavyLaser = function HeavyLaser(json, ship) {
    Laser.call(this, json, ship);
};
HeavyLaser.prototype = Object.create(Laser.prototype);
HeavyLaser.prototype.constructor = HeavyLaser;

HeavyLaser.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];        
    }

	return this;
};

var MediumLaser = function MediumLaser(json, ship) {
    Laser.call(this, json, ship);
};
MediumLaser.prototype = Object.create(Laser.prototype);
MediumLaser.prototype.constructor = MediumLaser;

var LightLaser = function LightLaser(json, ship) {
    Laser.call(this, json, ship);
};
LightLaser.prototype = Object.create(Laser.prototype);
LightLaser.prototype.constructor = LightLaser;

var BattleLaser = function BattleLaser(json, ship) {
    Laser.call(this, json, ship);
};
BattleLaser.prototype = Object.create(Laser.prototype);
BattleLaser.prototype.constructor = BattleLaser;

var AssaultLaser = function AssaultLaser(json, ship) {
    Laser.call(this, json, ship);
};
AssaultLaser.prototype = Object.create(Laser.prototype);
AssaultLaser.prototype.constructor = AssaultLaser;

var HvyAssaultLaser = function HvyAssaultLaser(json, ship) {
    Laser.call(this, json, ship);
};
HvyAssaultLaser.prototype = Object.create(Laser.prototype);
HvyAssaultLaser.prototype.constructor = HvyAssaultLaser;

var AdvancedAssaultLaser = function AdvancedAssaultLaser(json, ship) {
    Laser.call(this, json, ship);
};
AdvancedAssaultLaser.prototype = Object.create(Laser.prototype);
AdvancedAssaultLaser.prototype.constructor = AdvancedAssaultLaser;

var NeutronLaser = function NeutronLaser(json, ship) {
    Laser.call(this, json, ship);
};
NeutronLaser.prototype = Object.create(Laser.prototype);
NeutronLaser.prototype.constructor = NeutronLaser;

NeutronLaser.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];          
    }

	return this;
};

var ImprovedNeutronLaser = function ImprovedNeutronLaser(json, ship) {
    Laser.call(this, json, ship);
};
ImprovedNeutronLaser.prototype = Object.create(Laser.prototype);
ImprovedNeutronLaser.prototype.constructor = ImprovedNeutronLaser;

ImprovedNeutronLaser.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];        
    }

	return this;
};

var PowerLaser = function PowerLaser(json, ship) {
    Laser.call(this, json, ship);
};
PowerLaser.prototype = Object.create(Laser.prototype);
PowerLaser.prototype.constructor = PowerLaser;

PowerLaser.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];         
    }

	return this;
};

var MedPowerLaser = function MedPowerLaser(json, ship) {
    Laser.call(this, json, ship);
};
MedPowerLaser.prototype = Object.create(Laser.prototype);
MedPowerLaser.prototype.constructor = MedPowerLaser;

MedPowerLaser.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];         
    }

	return this;
};

var LaserLance = function LaserLance(json, ship) {
    Laser.call(this, json, ship);
};
LaserLance.prototype = Object.create(Laser.prototype);
LaserLance.prototype.constructor = LaserLance;

var HeavyLaserLance = function HeavyLaserLance(json, ship) {
    Laser.call(this, json, ship);
};
HeavyLaserLance.prototype = Object.create(Laser.prototype);
HeavyLaserLance.prototype.constructor = HeavyLaserLance;

var TacLaser = function TacLaser(json, ship) {
    Laser.call(this, json, ship);
};
TacLaser.prototype = Object.create(Laser.prototype);
TacLaser.prototype.constructor = TacLaser;

var CustomStrikeLaser = function CustomStrikeLaser(json, ship) {
    Laser.call(this, json, ship);
};
CustomStrikeLaser.prototype = Object.create(Laser.prototype);
CustomStrikeLaser.prototype.constructor = CustomStrikeLaser;

var ImperialLaser = function ImperialLaser(json, ship) {
    Laser.call(this, json, ship);
};
ImperialLaser.prototype = Object.create(Laser.prototype);
ImperialLaser.prototype.constructor = ImperialLaser;

var BlastLaser = function BlastLaser(json, ship) {
    Weapon.call(this, json, ship);
};
BlastLaser.prototype = Object.create(Weapon.prototype);
BlastLaser.prototype.constructor = BlastLaser;

var ImprovedBlastLaser = function ImprovedBlastLaser(json, ship) {
    Weapon.call(this, json, ship);
};
ImprovedBlastLaser.prototype = Object.create(Weapon.prototype);
ImprovedBlastLaser.prototype.constructor = ImprovedBlastLaser;

var CombatLaser = function CombatLaser(json, ship) {
    Laser.call(this, json, ship);
};
CombatLaser.prototype = Object.create(Laser.prototype);
CombatLaser.prototype.constructor = CombatLaser;

var LaserCutter = function LaserCutter(json, ship) {
    Laser.call(this, json, ship);
};
LaserCutter.prototype = Object.create(Laser.prototype);
LaserCutter.prototype.constructor = LaserCutter;


var LaserAccelerator = function LaserAccelerator(json, ship) {
    Laser.call(this, json, ship);
};
LaserAccelerator.prototype = Object.create(Laser.prototype);
LaserAccelerator.prototype.constructor = LaserAccelerator;

var Maser = function Maser(json, ship) {
    Laser.call(this, json, ship);
};
Maser.prototype = Object.create(Laser.prototype);
Maser.prototype.constructor = Maser;

var SpinalLaser = function SpinalLaser(json, ship) {
    Laser.call(this, json, ship);
};
SpinalLaser.prototype = Object.create(Laser.prototype);
SpinalLaser.prototype.constructor = SpinalLaser;

SpinalLaser.prototype.initializationUpdate = function() {
    var ship = this.ship;
	if(shipManager.power.isOverloading(ship, this) && Object.keys(this.sustainedTarget).length > 0){
        const targetId = Object.keys(this.sustainedTarget)[0];
        const target = gamedata.getShip(targetId);
		this.data["Current Target"] = target.name;
	}else{
        delete this.data["Current Target"];         
    }

	return this;
};

var LtBlastLaser = function LtBlastLaser(json, ship) {
    Laser.call(this, json, ship);
};
LtBlastLaser.prototype = Object.create(Laser.prototype);
LtBlastLaser.prototype.constructor = LtBlastLaser;

var UltralightLaser = function UltralightLaser(json, ship) {
    Laser.call(this, json, ship);
};
UltralightLaser.prototype = Object.create(Laser.prototype);
UltralightLaser.prototype.constructor = UltralightLaser;

var UnreliableBattleLaser = function UnreliableBattleLaser(json, ship) {
    Laser.call(this, json, ship);
};
UnreliableBattleLaser.prototype = Object.create(Laser.prototype);
UnreliableBattleLaser.prototype.constructor = UnreliableBattleLaser;