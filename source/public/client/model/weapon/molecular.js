"use strict";

var Molecular = function Molecular(json, ship) {
	Weapon.call(this, json, ship);
};
Molecular.prototype = Object.create(Weapon.prototype);
Molecular.prototype.constructor = Molecular;

var FusionCannon = function FusionCannon(json, ship) {
	Molecular.call(this, json, ship);
};
FusionCannon.prototype = Object.create(Molecular.prototype);
FusionCannon.prototype.constructor = FusionCannon;

var HeavyFusionCannon = function HeavyFusionCannon(json, ship) {
	Molecular.call(this, json, ship);
};
HeavyFusionCannon.prototype = Object.create(Molecular.prototype);
HeavyFusionCannon.prototype.constructor = HeavyFusionCannon;

var LightfusionCannon = function LightfusionCannon(json, ship) {
	Molecular.call(this, json, ship);
};
LightfusionCannon.prototype = Object.create(Molecular.prototype);
LightfusionCannon.prototype.constructor = LightfusionCannon;

var MolecularDisruptor = function MolecularDisruptor(json, ship) {
	Molecular.call(this, json, ship);
};
MolecularDisruptor.prototype = Object.create(Molecular.prototype);
MolecularDisruptor.prototype.constructor = MolecularDisruptor;

var SuperHeavyMolecularDisruptor = function SuperHeavyMolecularDisruptor(json, ship) {
	Molecular.call(this, json, ship);
};
SuperHeavyMolecularDisruptor.prototype = Object.create(Molecular.prototype);
SuperHeavyMolecularDisruptor.prototype.constructor = SuperHeavyMolecularDisruptor;



var LightMolecularDisruptorShip = function LightMolecularDisruptorShip(json, ship) {
	Molecular.call(this, json, ship);
};
LightMolecularDisruptorShip.prototype = Object.create(Molecular.prototype);
LightMolecularDisruptorShip.prototype.constructor = LightMolecularDisruptorShip;

var MolecularPenetrator = function MolecularPenetrator(json, ship) {
	Molecular.call(this, json, ship);
};
MolecularPenetrator.prototype = Object.create(Molecular.prototype);
MolecularPenetrator.prototype.constructor = MolecularPenetrator;

var DestabilizerBeam = function DestabilizerBeam(json, ship) {
	Molecular.call(this, json, ship);
};
DestabilizerBeam.prototype = Object.create(Molecular.prototype);
DestabilizerBeam.prototype.constructor = DestabilizerBeam;

var MolecularFlayer = function MolecularFlayer(json, ship) {
	Molecular.call(this, json, ship);
};
MolecularFlayer.prototype = Object.create(Molecular.prototype);
MolecularFlayer.prototype.constructor = MolecularFlayer;

var FusionAgitator = function FusionAgitator(json, ship) {
	Molecular.call(this, json, ship);
};
FusionAgitator.prototype = Object.create(Molecular.prototype);
FusionAgitator.prototype.constructor = FusionAgitator;

FusionAgitator.prototype.clearBoost = function () {
	for (var i in system.power) {
		var power = system.power[i];
		if (power.turn != gamedata.turn) continue;

		if (power.type == 2) {
			system.power.splice(i, 1);

			return;
		}
	}
};

FusionAgitator.prototype.hasMaxBoost = function () {
	return true;
};

FusionAgitator.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

FusionAgitator.prototype.initBoostableInfo = function () {
	switch (shipManager.power.getBoost(this)) {
		case 0:
			this.data["Damage"] = '15 - 60';
			this.data["Boostlevel"] = '0';
			break;
		case 1:
			this.data["Damage"] = '16 - 70';
			this.data["Boostlevel"] = '1';
			break;
		case 2:
			this.data["Damage"] = '17 - 80';
			this.data["Boostlevel"] = '2';
			break;
		case 3:
			this.data["Damage"] = '18 - 90';
			this.data["Boostlevel"] = '3';
			break;
		case 4:
			this.data["Damage"] = '19 - 100';
			this.data["Boostlevel"] = '4';
			break;
		default:
			this.data["Damage"] = '15 - 60';
			this.data["Boostlevel"] = '0';
			break;
	}
	return this;
};

var EarlyFusionAgitator = function EarlyFusionAgitator(json, ship) {
	Molecular.call(this, json, ship);
};
EarlyFusionAgitator.prototype = Object.create(Molecular.prototype);
EarlyFusionAgitator.prototype.constructor = EarlyFusionAgitator;

var FusionCutter = function FusionCutter(json, ship) {
	Molecular.call(this, json, ship);
};
FusionCutter.prototype = Object.create(Molecular.prototype);
FusionCutter.prototype.constructor = FusionCutter;

var FtrPolarityCannon = function FtrPolarityCannon(json, ship) {
	Weapon.call(this, json, ship);
};
FtrPolarityCannon.prototype = Object.create(Weapon.prototype);
FtrPolarityCannon.prototype.constructor = FtrPolarityCannon;


var MolecularSlicerBeamL = function MolecularSlicerBeamL(json, ship) {
	Weapon.call(this, json, ship);
};
MolecularSlicerBeamL.prototype = Object.create(Weapon.prototype);
MolecularSlicerBeamL.prototype.constructor = MolecularSlicerBeamL;

MolecularSlicerBeamL.prototype.getShotsUsed = function () {
	var shotsUsed = 0;
	for (var i = 0; i < this.fireOrders.length; i++) {
		shotsUsed += this.fireOrders[i].shots;
	}
	return shotsUsed;
};

MolecularSlicerBeamL.prototype.initializationUpdate = function () {
	var shots = 0; //Initialise	
	var minDam = 0;
	var maxDam = 0;

	switch (this.turnsloaded) {
		case 1:
			shots = 4;
			minDam = 8;
			maxDam = 44;
			break;
		case 2:
			shots = 6;
			minDam = 12;
			maxDam = 66;
			break;
		case 3:
			shots = 8;
			minDam = 16;
			maxDam = 88;
			break;
		default:
			shots = 16;
			minDam = 16;
			maxDam = 88;
			break;
	}
	this.data["Max number of Dice"] = shots;

	if (gamedata.gamephase == 3) {
		var isFiring = weaponManager.hasFiringOrder(this.ship, this);
		var shotsUsed = this.getShotsUsed();
		this.data["Defensive Dice"] = 0;
		if (isFiring) {
			for (var i in this.fireOrders) {
				var fireOrder = this.fireOrders[i];
				if (fireOrder.type == "selfIntercept") {
					this.data["Defensive Dice"]++;
					minDam -= 1; //Adjust damage values by 1d10.
					maxDam -= 10;
				}
			}

		}
		this.data["Offensive Dice"] = shotsUsed - this.data["Defensive Dice"];
		this.data["Remaining Dice"] = shots - shotsUsed;
	}

	this.data["Damage"] = "" + minDam + "-" + maxDam;

	return this;
};

MolecularSlicerBeamL.prototype.isLegalToFireMode = function (shooter) {
	return true;
};

MolecularSlicerBeamL.prototype.doMultipleFireOrders = function (shooter, target, system) {

	if (this.handlingInput) {
		this.handlingInput = false;
		return [];
	}

	// Identify all Molecular Slicers currently selected
	var slicers = [];
	var systems = gamedata.selectedSystems;
	for (var i in systems) {
		var sys = systems[i];
		if (sys instanceof MolecularSlicerBeamL) {
			if(weaponManager.isOnWeaponArc(shooter, target, sys)){
				slicers.push(sys);
			} 
		}
	}

	// Validity Checks
	var inputs = [];
	for (var i in slicers) {
		var slicer = slicers[i];

		// Mode check (for Heavy Slicer)
		if (!slicer.isLegalToFireMode(shooter)) {
			return [];
		}

		// Ammo check
		var remaining = slicer.data["Max number of Dice"] - slicer.getShotsUsed();
		if (remaining <= 0) {
			// Let's filter out empty ones silently if group, or error if all empty.
		} else {
			inputs.push({
				id: slicer.id,
				label: slicer.displayName,
				max: remaining,
				value: remaining // Default to max
			});
		}
	}

	if (inputs.length === 0) {
		confirm.error("Molecular Slicer does not have enough damage dice to target another shot.");
		return;
	}

	// Mark peers as handled
	for (var i in slicers) {
		if (slicers[i].id !== this.id) {
			slicers[i].handlingInput = true;
		}
	}

	var self = this;

	// Callback function
	var onConfirm = function (results) {
		for (var id in results) {
			var val = results[id];
			var weapon = null;

			// Find weapon instance - look in selectedSystems
			for (var k in slicers) {
				if (slicers[k].id == id) {
					weapon = slicers[k];
					break;
				}
			}

			if (weapon) {
				// We need to calculate chance for EACH weapon.
				var fireid = shooter.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
				var calledid = -1;
				var chance = window.weaponManager.calculateHitChange(shooter, target, weapon, calledid);

				if (chance < 1) continue;

				weapon.resolveFireOrder(val, shooter, target, fireid, calledid, chance);
			}
		}
	};

	if (inputs.length === 1 && inputs[0].id === this.id) {
		// Use the multi-dialog anyway for consistency.
		confirm.askForMultipleValues("Allocate d10 damage dice for this shot", inputs, onConfirm);
	} else {
		confirm.askForMultipleValues("Allocate d10 damage dice for Molecular Slicers", inputs, onConfirm);
	}

	return [];
};

MolecularSlicerBeamL.prototype.resolveFireOrder = function (dice, shooter, target, fireid, calledid, chance) {
	var fire = {
		id: fireid,
		type: 'normal',
		shooterid: shooter.id,
		targetid: target.id,
		weaponid: this.id,
		calledid: calledid,
		turn: gamedata.turn,
		firingMode: this.firingMode,
		shots: dice,
		x: "null",
		y: "null",
		damageclass: 'Sweeping',
		chance: chance,
		hitmod: 5,
		notes: "Split"
	};

	this.fireOrders.push(fire);

	webglScene.customEvent('SystemDataChanged', { ship: this.ship, system: this });

	// Check if finished
	if (this.checkFinished()) {
		weaponManager.unSelectWeapon(this.ship, this);
	}
};

MolecularSlicerBeamL.prototype.checkSelfInterceptSystem = function () {
	if ((this.data["Max number of Dice"] - this.getShotsUsed()) <= 0) return false;
	return true;
};

MolecularSlicerBeamL.prototype.doMultipleSelfIntercept = function (ship) {

	for (var s = 0; s < 1; s++) {
		var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
		var fire = {
			id: fireid,
			type: "selfIntercept",
			shooterid: ship.id,
			targetid: ship.id,
			weaponid: this.id,
			calledid: -1,
			turn: gamedata.turn,
			firingMode: 1,
			shots: 1,
			x: "null",
			y: "null",
			addToDB: true,
			damageclass: this.data["Weapon type"].toLowerCase()
		};

		this.fireOrders.unshift(fire); //Always insert selfIntercept orders first for hitMod to be applied correctly to offensive shots.
	}

	this.recalculateForIntercept(true);

	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
};

MolecularSlicerBeamL.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
	var mod = 0;
	if (this.firingMode == 1) {
		//Check fireOrders length and deduct (length -1 *5)
		var currentShots = this.fireOrders.length; //
		mod -= Math.max(0, currentShots); //This is called when considering the NEXT shot.  So can just use current length as mod.
	}
	return mod;
};

MolecularSlicerBeamL.prototype.recalculateFireOrders = function (shooter, fireOrderNo) {

	for (let i = 0; i < this.fireOrders.length; i++) {
		const fireOrder = this.fireOrders[i];

		// Ensure we only include fireOrders for the current turn and weapon, and only fireORders AFTER the one we are currently removing.
		if (fireOrder.weaponid === this.id && fireOrder.turn === gamedata.turn && i > fireOrderNo) {
			fireOrder.chance += fireOrder.hitmod;
		}
	}

};

MolecularSlicerBeamL.prototype.recalculateForIntercept = function (add) {
	for (let i = 0; i < this.fireOrders.length; i++) {
		const fireOrder = this.fireOrders[i];
		if (fireOrder.type !== "selfIntercept") {
			if (add) {
				fireOrder.chance -= fireOrder.hitmod;
			} else {
				fireOrder.chance += fireOrder.hitmod;
			}
		}
	}

};


MolecularSlicerBeamL.prototype.checkFinished = function () {
	var shots = 0; //Initialise

	switch (this.turnsloaded) {
		case 1:
			shots = 4;
			break;
		case 2:
			shots = 6;
			break;
		case 3:
			shots = 8;
			break;
		default:
			shots = 8;
			break;
	}
	if (this.getShotsUsed() >= shots) return true;
	return false;
};

var MolecularSlicerBeamM = function MolecularSlicerBeamM(json, ship) {
	MolecularSlicerBeamL.call(this, json, ship);
};
MolecularSlicerBeamM.prototype = Object.create(MolecularSlicerBeamL.prototype);
MolecularSlicerBeamM.prototype.constructor = MolecularSlicerBeamM;

MolecularSlicerBeamM.prototype.initializationUpdate = function () {
	var shots = 0; //Initialise	
	var minDam = 0;
	var maxDam = 0;

	switch (this.turnsloaded) {
		case 1:
			shots = 8;
			minDam = 20;
			maxDam = 92;
			break;
		case 2:
			shots = 12;
			minDam = 36;
			maxDam = 144;
			break;
		case 3:
			shots = 16;
			minDam = 42;
			maxDam = 196;
			break;
		default:
			shots = 16;
			minDam = 42;
			maxDam = 196;
			break;
	}
	this.data["Max number of Dice"] = shots;

	if (gamedata.gamephase == 3) {
		var isFiring = weaponManager.hasFiringOrder(this.ship, this);
		var shotsUsed = this.getShotsUsed();
		this.data["Defensive Dice"] = 0;
		if (isFiring) {
			for (var i in this.fireOrders) {
				var fireOrder = this.fireOrders[i];
				if (fireOrder.type == "selfIntercept") {
					this.data["Defensive Dice"]++;
					minDam -= 1; //Adjust damage values by 1d10.
					maxDam -= 10;
				}
			}

		}
		this.data["Offensive Dice"] = shotsUsed - this.data["Defensive Dice"];
		this.data["Remaining Dice"] = shots - shotsUsed;
	}

	this.data["Damage"] = "" + minDam + "-" + maxDam;

	return this;
};

MolecularSlicerBeamM.prototype.checkFinished = function () {
	var shots = 0; //Initialise

	switch (this.turnsloaded) {
		case 1:
			shots = 8;
			break;
		case 2:
			shots = 12;
			break;
		case 3:
			shots = 16;
			break;
		default:
			shots = 16;
			break;
	}
	if (this.getShotsUsed() >= shots) return true;
	return false;
};

var MolecularSlicerBeamH = function MolecularSlicerBeamH(json, ship) {
	MolecularSlicerBeamL.call(this, json, ship);
};
MolecularSlicerBeamH.prototype = Object.create(MolecularSlicerBeamL.prototype);
MolecularSlicerBeamH.prototype.constructor = MolecularSlicerBeamH;

MolecularSlicerBeamH.prototype.initializationUpdate = function () {
	var shots = 0; //Initialise
	var minDam = 0;
	var maxDam = 0;

	switch (this.turnsloaded) {
		case 1:
			shots = 8;
			minDam = 20;
			maxDam = 92;
			break;
		case 2:
			shots = 16;
			minDam = 40;
			maxDam = 184;
			break;
		case 3:
			shots = 24;
			minDam = 60;
			maxDam = 276;
			break;
		default:
			shots = 24;
			minDam = 60;
			maxDam = 276;
			break;
	}
	this.data["Max number of Dice"] = shots;

	if (gamedata.gamephase == 3) {
		var isFiring = weaponManager.hasFiringOrder(this.ship, this);
		var shotsUsed = this.getShotsUsed();
		this.data["Defensive Dice"] = 0;
		if (isFiring) {
			for (var i in this.fireOrders) {
				var fireOrder = this.fireOrders[i];
				if (fireOrder.type == "selfIntercept") {
					this.data["Defensive Dice"]++;
					minDam -= 1; //Adjust damage values by 1d10.
					maxDam -= 10;
				}
			}

		}
		this.data["Offensive Dice"] = shotsUsed - this.data["Defensive Dice"];
		this.data["Remaining Dice"] = shots - shotsUsed;
	}

	this.data["Damage"] = "" + minDam + "-" + maxDam;

	return this;
};

MolecularSlicerBeamH.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
	var mod = 0;
	//Check fireOrders length and deduct (length -1 *5)
	var currentShots = this.fireOrders.length; //
	mod -= Math.max(0, currentShots); //This is called when considering the NEXT shot.  So can just use current length as mod.

	if (this.turnsloaded < 3 && (this.firingMode == 1 || this.firingMode == 3)) mod += 4;

	return mod;
};

// H inherits doMultipleFireOrders from L now that L handles grouping and delegation.
// We only need to override isLegalToFireMode.
MolecularSlicerBeamH.prototype.isLegalToFireMode = function (shooter) {
	if (this.turnsloaded >= 3) {
		const currentMode = this.firingMode;

		for (let i = this.fireOrders.length - 1; i >= 0; i--) {
			const existingMode = this.fireOrders[i].firingMode;

			const existingPiercing = (existingMode === 1 || existingMode === 3);
			const currentPiercing = (currentMode === 1 || currentMode === 3);

			if (existingPiercing !== currentPiercing) {
				confirm.error("You cannot mix Piercing and Raking modes whilst at full charge.");
				return false;
			}
		}
	}
	return true;
};

MolecularSlicerBeamH.prototype.removeMultiModeSplit = function (ship, target) {

	let removed = false;

	if (target) {
		// Search from newest → oldest
		for (let i = this.fireOrders.length - 1; i >= 0; i--) {
			const fireOrder = this.fireOrders[i];

			if (this.firingMode == fireOrder.firingMode && fireOrder.targetid == target.id) {
				// Remove the matching fire order
				this.fireOrders.splice(i, 1);
				removed = true;
				break;
			}
		}
	}

	// If NONE matched, remove the last fire order instead
	if (!removed && this.fireOrders.length > 0) {
		removed = true;
		this.fireOrders.pop();  // removes last item
	}

	// Always fire the events if something was removed
	if (removed) {
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
		webglScene.customEvent('SplitOrderRemoved', { shooter: ship, target: target });
	}
};

MolecularSlicerBeamH.prototype.removeAllMultiModeSplit = function (ship) {

	for (var i = this.fireOrders.length - 1; i >= 0; i--) {
		this.fireOrders.splice(i, 1); // Remove the specific fire order

	}

	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
};

MolecularSlicerBeamH.prototype.checkFinished = function () {
	var shots = 0; //Initialise

	switch (this.turnsloaded) {
		case 1:
			shots = 8;
			break;
		case 2:
			shots = 16;
			break;
		case 3:
			shots = 24;
			break;
		default:
			shots = 24;
			break;
	}
	if (this.getShotsUsed() >= shots) return true;
	return false;
};

var MultiphasedCutterL = function MultiphasedCutterL(json, ship) {
	Weapon.call(this, json, ship);
};
MultiphasedCutterL.prototype = Object.create(Weapon.prototype);
MultiphasedCutterL.prototype.constructor = MultiphasedCutterL;

var MultiphasedCutter = function MultiphasedCutter(json, ship) {
	Weapon.call(this, json, ship);
};
MultiphasedCutter.prototype = Object.create(Weapon.prototype);
MultiphasedCutter.prototype.constructor = MultiphasedCutter;

MultiphasedCutter.prototype.initializationUpdate = function () {
	if (this.firingMode == 2) {
		this.data["Shots Remaining"] = this.guns - this.fireOrders.length;
	} else {
		delete this.data["Shots Remaining"];
	}
	return this;
};

MultiphasedCutter.prototype.checkFinished = function () {
	if (this.fireOrders.length > 2) return true;
	return false;
};

MultiphasedCutter.prototype.doMultipleFireOrders = function (shooter, target, system) {

	var shotsOnTarget = 1; //we're only ever allocating one shot at a time for this weapon.

	if (this.fireOrders.length > 2) return;

	var fireOrdersArray = []; // Store multiple fire orders

	for (var s = 0; s < shotsOnTarget; s++) {
		var fireid = shooter.id + "_" + this.id + "_" + (this.fireOrders.length + 1);
		var calledid = -1;

		if (system) {
			// When the system is a subsystem, make all damage go through
			// the parent.
			while (system.parentId > 0) {
				system = shipManager.systems.getSystem(ship, system.parentId);
			}

			calledid = system.id;
		}

		var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid);
		if (chance < 1) continue;

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
			damageclass: 'Sweeping',
			chance: chance,
			hitmod: 0,
			notes: "Split"
		};

		fireOrdersArray.push(fire); // Store each fire order
	}

	return fireOrdersArray; // Return all fire orders
};
