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

//GTS_Triad
var PhotonicPrismBeam = function PhotonicPrismBeam(json, ship) {
    Weapon.call(this, json, ship);
};
PhotonicPrismBeam.prototype = Object.create(Weapon.prototype);
PhotonicPrismBeam.prototype.constructor = PhotonicPrismBeam;

PhotonicPrismBeam.prototype.initializationUpdate = function () {
    //Show shots remaining for Mode 1 (Split) only.
    if (this.firingMode == 1) {
        this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
    } else {
        delete this.data["Shots Remaining"];
    }
    return this;
};

/* Mode 1 (Split): Twin Array pattern - one click per shot, up to guns (3) shots.
 * Each click assigns one shot to the clicked target. The player can click the same
 * target multiple times or different targets for each shot.
 * Modes 2-5: single shot - handled by the engine's default path (canSplitShots=false).
 * Only Mode 1 reaches doMultipleFireOrders since canSplitShotsArray[1]=true only. */
PhotonicPrismBeam.prototype.doMultipleFireOrders = function (shooter, target, system) {
    var shotsOnTarget = 1; //one shot per click

    //Cap at guns (3 for Mode 1, reduced by GunLost crits if ever applicable).
    if (this.fireOrders.length >= this.guns) return;

    var fireOrdersArray = [];
    for (var s = 0; s < shotsOnTarget; s++) {
        var calledid = -1;
        var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
        if (chance < 1) continue;

        var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
        var fire = {
            id: fireid,
            type: 'normal',
            shooterid: shooter.id,
            targetid: target.id,
            weaponid: this.id,
            calledid: calledid,
            turn: gamedata.turn,
            firingMode: this.firingMode,
            shots: 1,
            x: "null",
            y: "null",
            damageclass: 'Raking',
            chance: chance,
            hitmod: 0
        };
        fireOrdersArray.push(fire);
    }
    return fireOrdersArray;
};

PhotonicPrismBeam.prototype.checkFinished = function () {
    //Mode 1: finished when all 3 shots are allocated.
    if (this.firingMode == 1 && this.fireOrders.length >= this.guns) return true;
    //Modes 2-5: always one shot, finished immediately after declaring.
    if (this.firingMode != 1 && this.fireOrders.length >= 1) return true;
    return false;
};

var LtPrismBeam = function LtPrismBeam(json, ship) {
    Laser.call(this, json, ship);
};
LtPrismBeam.prototype = Object.create(Laser.prototype);
LtPrismBeam.prototype.constructor = LtPrismBeam;