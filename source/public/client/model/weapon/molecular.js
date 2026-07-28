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


/* ---------------------------------------------------------------------------------
 * Molecular Slicer Beams (Light / regular / Heavy)
 *
 * A Slicer's volley is TWO independent pools, both fixed by how long the weapon has been
 * charging (and, for the Heavy, by firing mode - Raking never draws on more than two
 * turns' worth of charge):
 *     - damage DICE : d10s
 *     - SET damage  : flat points added on top of the dice
 * The player splits BOTH freely across as many shots as they like. Dice ride in the fire
 * order's ->shots field (as they always have); the set-damage allocation rides in ->notes
 * as "MSB|d:<dice>|s:<set>" - the same client->server channel the Hypergraviton Blaster
 * uses for its transfer list (Manager.php copies notes onto the rehydrated FireOrder).
 * molecular.php re-parses and re-clamps both pools server-side; never trust these numbers
 * to arrive intact.
 *
 * Either pool can also be spent on DEFENCE instead: the self-interception dialog takes
 * damage dice and whole blocks of SLICER_SET_DAMAGE_BLOCK set damage, and each die and
 * each block committed becomes one selfIntercept fire order (= one incoming shot the
 * Slicer may engage). The Light Slicer has no interception rating and is excluded.
 * ------------------------------------------------------------------------------- */
var SLICER_SET_DAMAGE_BLOCK = 6; //points of set damage that buy one self-intercept

var MolecularSlicerBeamL = function MolecularSlicerBeamL(json, ship) {
	Weapon.call(this, json, ship);
};
MolecularSlicerBeamL.prototype = Object.create(Weapon.prototype);
MolecularSlicerBeamL.prototype.constructor = MolecularSlicerBeamL;

//Dice / set-damage available by turns charged. Overridden by the heavier varieties; kept
//in step with maxDiceArray + setDamageArray in molecular.php.
MolecularSlicerBeamL.prototype.slicerPools = {
	1: { dice: 4, set: 4 },
	2: { dice: 6, set: 6 },
	3: { dice: 8, set: 8 }
};

MolecularSlicerBeamL.prototype.getPools = function () {
	var turns = Math.min(3, Math.max(1, this.turnsloaded));
	return this.slicerPools[turns];
};

MolecularSlicerBeamL.prototype.getShotsUsed = function () {
	var shotsUsed = 0;
	for (var i = 0; i < this.fireOrders.length; i++) {
		shotsUsed += this.fireOrders[i].shots;
	}
	return shotsUsed;
};

/* Set damage committed to a single fire order. Freshly declared orders carry it in
   ->setDam; an order that has round-tripped through the server (page reloaded after
   submitting, before the turn resolves) only has the encoded ->notes token left, so fall
   back to reading that. */
MolecularSlicerBeamL.prototype.getOrderSetDamage = function (fireOrder) {
	if (typeof fireOrder.setDam === 'number') return fireOrder.setDam;
	if (typeof fireOrder.notes === 'string') {
		var match = fireOrder.notes.match(/MSB\|d:(\d+)\|s:(\d+)/);
		if (match) return parseInt(match[2], 10);
	}
	return 0;
};

MolecularSlicerBeamL.prototype.getSetDamageUsed = function () {
	var used = 0;
	for (var i = 0; i < this.fireOrders.length; i++) {
		used += this.getOrderSetDamage(this.fireOrders[i]);
	}
	return used;
};

MolecularSlicerBeamL.prototype.getRemainingDice = function () {
	return Math.max(0, this.getPools().dice - this.getShotsUsed());
};

MolecularSlicerBeamL.prototype.getRemainingSetDamage = function () {
	return Math.max(0, this.getPools().set - this.getSetDamageUsed());
};

MolecularSlicerBeamL.prototype.checkForWastedShots = function () {
	//Drives the "this ship still has shots left" warning at commit. Unspent capacity in
	//either pool is simply lost, so flag both.
	return (this.getRemainingDice() > 0 || this.getRemainingSetDamage() > 0);
};

MolecularSlicerBeamL.prototype.initializationUpdate = function () {
	var pools = this.getPools();
	var defensiveDice = 0;
	var defensiveSet = 0;
	var defensiveShots = 0;

	this.data["Max number of Dice"] = pools.dice;
	this.data["Max Set Damage"] = pools.set;

	if (gamedata.gamephase == 3) {
		for (var i in this.fireOrders) {
			var fireOrder = this.fireOrders[i];
			if (fireOrder.type != "selfIntercept") continue;
			//Each self-intercept order costs one die OR one block of set damage and buys one
			//engagement, so the two are reported as a single "Defensive Shots" count rather
			//than as two separate lines the player has to add up.
			defensiveShots++;
			defensiveDice += fireOrder.shots;
			defensiveSet += this.getOrderSetDamage(fireOrder);
		}

		this.data["Defensive Shots"] = defensiveShots;
		this.data["Offensive Dice"] = this.getShotsUsed() - defensiveDice;
		this.data["Remaining Dice"] = this.getRemainingDice();
		this.data["Remaining Set Damage"] = this.getRemainingSetDamage();
	}

	//Displayed damage is what the OFFENSIVE half of the volley can still produce, so
	//anything already committed to self-interception comes off the top.
	var dice = Math.max(0, pools.dice - defensiveDice);
	var set = Math.max(0, pools.set - defensiveSet);
	this.data["Damage"] = "" + (dice + set) + "-" + (dice * 10 + set);

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
			if (weaponManager.isOnWeaponArc(shooter, target, sys)) {
				slicers.push(sys);
			}
		}
	}

	// Validity Checks
	var inputs = [];
	var isFlight = target.flight;
	for (var i in slicers) {
		var slicer = slicers[i];

		// Mode check (for Heavy Slicer)
		if (!slicer.isLegalToFireMode(shooter)) {
			return [];
		}

		// Ammo check - either pool on its own is enough to declare a shot with
		var remainingDice = slicer.getRemainingDice();
		var remainingSet = slicer.getRemainingSetDamage();
		if (remainingDice <= 0 && remainingSet <= 0) continue; //Skip empty

		inputs.push({
			id: slicer.id,
			label: slicer.displayName,
			max: remainingDice,
			//A shot may be made of set damage alone, so committing 0 dice is legal.
			min: 0,
			value: isFlight ? Math.min(1, remainingDice) : remainingDice,
			multiplier: isFlight,
			extra: {
				max: remainingSet,
				//Ships: pour the whole remaining set-damage pool into this shot by default,
				//mirroring the dice box. Fighters: default to none, since a flight rarely
				//needs it and spending it here would starve the rest of the volley.
				value: isFlight ? 0 : remainingSet,
				min: 0,
				label: 'damage'
			}
		});
	}

	if (inputs.length === 0) {
		confirm.error("Molecular Slicer does not have enough damage dice or set damage to target another shot.");
		return;
	}

	// Mark peers as handled
	for (var i in slicers) {
		if (slicers[i].id !== this.id) {
			slicers[i].handlingInput = true;
		}
	}

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
			if (!weapon) continue;

			var damagePerShot = val.value;
			var setPerShot = val.extra || 0;
			var shotsToFire = val.count || 1;

			if (damagePerShot <= 0 && setPerShot <= 0) continue; //nothing allocated to this weapon

			//Resolve the called system per weapon - don't reassign the shared `system`, or
			//the second Slicer in the dialog would inherit the first one's walked-up parent.
			var calledid = -1;
			if (system && target.flight) { //Slicers CAN target individual fighters!
				var calledSystem = system;
				// When the system is a subsystem, make all damage go through the parent.
				while (calledSystem.parentId > 0) {
					calledSystem = shipManager.systems.getSystem(target, calledSystem.parentId);
				}
				calledid = calledSystem.id;
			}

			//Check valid shot?
			var chance = window.weaponManager.calculateHitChange(shooter, target, weapon, calledid).hitChance;
			if (chance < 1) continue;

			for (var s = 0; s < shotsToFire; s++) {
				//Recalculated per shot: every order this weapon has already declared adds a
				//further cumulative -5% (calculateSpecialHitChanceMod counts existing orders).
				chance = window.weaponManager.calculateHitChange(shooter, target, weapon, calledid).hitChance;
				var fireid = shooter.id + "_" + weapon.id + "_" + (weapon.fireOrders.length + 1);
				weapon.resolveFireOrder(damagePerShot, setPerShot, shooter, target, fireid, calledid, chance);
			}
		}
	};

	if (isFlight) {
		confirm.askForMultipleValues("Allocate damage dice (d10), shots and set damage", inputs, onConfirm);
	} else {
		confirm.askForMultipleValues("Allocate damage dice (d10) and set damage", inputs, onConfirm);
	}

	return [];
};

MolecularSlicerBeamL.prototype.resolveFireOrder = function (dice, setDam, shooter, target, fireid, calledid, chance) {
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
		setDam: setDam,
		x: "null",
		y: "null",
		damageclass: 'Sweeping',
		chance: chance,
		hitmod: 5,
		//"Split" stays as the long-standing split-shot marker; the MSB token carries this
		//shot's dice/set-damage allocation to molecular.php, which strips it back out
		//before anything is written to the fire order log.
		notes: "Split MSB|d:" + dice + "|s:" + setDam
	};

	this.fireOrders.push(fire);

	webglScene.customEvent('SystemDataChanged', { ship: this.ship, system: this });

	// Check if finished
	if (this.checkFinished()) {
		weaponManager.unSelectWeapon(this.ship, this);
	}
	var weaponArray = [] //onShipTargeted() expects an array, so convert.
	weaponArray.push(this);
    webglScene.customEvent('ShipTargeted', {shooter: this.ship, target: target, weapons: weaponArray})	
};

MolecularSlicerBeamL.prototype.checkSelfInterceptSystem = function () {
	//The Light Slicer has no interception rating and can never be committed to defensive
	//fire (weaponManager.canSelfInterceptSingle rejects it on that basis too - stated here
	//as well so the rule lives with the weapon).
	if (this.intercept < 1) return false;
	//Needs a spare die, or a whole spare block of set damage, to commit anything.
	if (this.getRemainingDice() <= 0 && this.getRemainingSetDamage() < SLICER_SET_DAMAGE_BLOCK) return false;
	return true;
};

/* Self-interception is declared through its own allocation dialog: the player commits a
   number of damage dice AND/OR a number of whole blocks of set damage, and EACH die and
   EACH block becomes one selfIntercept fire order. The server counts orders (not points)
   when working out how many incoming shots the Slicer may engage, so one order per unit
   of committed capacity is what makes the two ends agree.

   weaponManager.onDeclareSelfInterceptSingleAll (right-click) calls doMultipleSelfIntercept
   synchronously for every similar weapon on the ship, so collect whoever asks within the
   current tick and open a SINGLE dialog for the lot instead of stacking one per weapon. */
var slicerSelfInterceptQueue = [];   // [{ship, weapon}]
var slicerSelfInterceptTimer = null;

function openSlicerSelfInterceptDialog() {
	slicerSelfInterceptTimer = null;
	var queued = slicerSelfInterceptQueue;
	slicerSelfInterceptQueue = [];
	if (queued.length === 0) return;

	var inputs = [];
	for (var i = 0; i < queued.length; i++) {
		var weapon = queued[i].weapon;
		var maxDice = weapon.getRemainingDice();
		var maxBlocks = Math.floor(weapon.getRemainingSetDamage() / SLICER_SET_DAMAGE_BLOCK);
		if (maxDice <= 0 && maxBlocks <= 0) continue;

		inputs.push({
			id: weapon.id,
			label: weapon.displayName,
			max: maxDice,
			min: 0,
			value: maxDice > 0 ? 1 : 0,
			extra: {
				//Entered as POINTS of set damage rather than as a block count, so the number
				//the player types is the number they see everywhere else on the weapon. The
				//step keeps it on whole blocks - a part-block buys no interception.
				max: maxBlocks * SLICER_SET_DAMAGE_BLOCK,
				value: 0,
				min: 0,
				step: SLICER_SET_DAMAGE_BLOCK,
				label: 'damage'
			}
		});
	}

	if (inputs.length === 0) {
		confirm.error("Molecular Slicer has nothing left to commit to interception.");
		return;
	}

	var header = "Commit to interception - 1 die or " + SLICER_SET_DAMAGE_BLOCK + " set damage per intercept";
	confirm.askForMultipleValues(header, inputs, function (results) {
		for (var id in results) {
			var val = results[id];
			var entry = null;
			for (var k = 0; k < queued.length; k++) {
				if (queued[k].weapon.id == id) {
					entry = queued[k];
					break;
				}
			}
			if (!entry) continue;
			//Back from points to whole blocks - one intercept order per block.
			var blocks = Math.floor((val.extra || 0) / SLICER_SET_DAMAGE_BLOCK);
			entry.weapon.addSelfInterceptOrders(entry.ship, val.value, blocks);
		}
	});
}

MolecularSlicerBeamL.prototype.doMultipleSelfIntercept = function (ship) {
	for (var q = 0; q < slicerSelfInterceptQueue.length; q++) {
		if (slicerSelfInterceptQueue[q].weapon === this) return; //already queued this tick
	}
	slicerSelfInterceptQueue.push({ ship: ship, weapon: this });

	if (slicerSelfInterceptTimer !== null) return;
	slicerSelfInterceptTimer = window.setTimeout(openSlicerSelfInterceptDialog, 0);
};

/* Creates one selfIntercept fire order per committed die and per committed block of set
   damage. Orders are unshifted so they sit at the FRONT of the array: both the client's
   hit-chance display and the server's calculateHitBase derive each offensive shot's
   cumulative -5% from its index in this list, so defensive orders must come first for the
   two ends to agree. */
MolecularSlicerBeamL.prototype.addSelfInterceptOrders = function (ship, dice, blocks) {
	var total = dice + blocks;
	if (total <= 0) return;

	for (var i = 0; i < total; i++) {
		//Dice first, then the set-damage blocks. Each order spends exactly one or the other.
		var usesDie = (i < dice);
		var diceSpent = usesDie ? 1 : 0;
		var setSpent = usesDie ? 0 : SLICER_SET_DAMAGE_BLOCK;
		var fireid = ship.id + "_" + this.id + "_" + (this.fireOrders.length + 1);

		this.fireOrders.unshift({
			id: fireid,
			type: "selfIntercept",
			shooterid: ship.id,
			targetid: ship.id,
			weaponid: this.id,
			calledid: -1,
			turn: gamedata.turn,
			firingMode: this.firingMode,
			shots: diceSpent,
			setDam: setSpent,
			x: "null",
			y: "null",
			addToDB: true,
			damageclass: this.data["Weapon type"].toLowerCase(),
			//Self-intercept orders never pass through the server's calculateHitBase, so this
			//token survives in the stored row (harmless - the combat log only prints orders
			//with rolled !== 0). It is also what getOrderSetDamage reads back if the page is
			//reloaded after submitting but before the turn resolves.
			notes: "MSB|d:" + diceSpent + "|s:" + setSpent
		});

		this.recalculateForIntercept(true); //each defensive order costs the offensive shots 5%
	}

	webglScene.customEvent('SystemDataChanged', { ship: ship, system: this });
	if (this.checkFinished()) weaponManager.unSelectWeapon(ship, this);
};

MolecularSlicerBeamL.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
	var mod = 0;
	//Check fireOrders length and deduct (length -1 *5)
	var currentShots = this.fireOrders.length; //
	mod -= Math.max(0, currentShots); //This is called when considering the NEXT shot.  So can just use current length as mod.

    if(target.flight &&  calledid && calledid !== -1){ //Has fireorder against fighter unit, and is a called shot
        mod += 8; //CalledShotmod is -8, so just compensate for that.            
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
	//Nothing left to declare only once BOTH pools are spent - leftover set damage can still
	//be poured into a dice-less shot, so dice running out alone isn't "finished".
	return (this.getRemainingDice() <= 0 && this.getRemainingSetDamage() <= 0);
};

var MolecularSlicerBeamM = function MolecularSlicerBeamM(json, ship) {
	MolecularSlicerBeamL.call(this, json, ship);
};
MolecularSlicerBeamM.prototype = Object.create(MolecularSlicerBeamL.prototype);
MolecularSlicerBeamM.prototype.constructor = MolecularSlicerBeamM;

MolecularSlicerBeamM.prototype.slicerPools = {
	1: { dice: 8, set: 12 },
	2: { dice: 12, set: 24 },
	3: { dice: 16, set: 36 }
};

var MolecularSlicerBeamH = function MolecularSlicerBeamH(json, ship) {
	MolecularSlicerBeamL.call(this, json, ship);
};
MolecularSlicerBeamH.prototype = Object.create(MolecularSlicerBeamL.prototype);
MolecularSlicerBeamH.prototype.constructor = MolecularSlicerBeamH;

MolecularSlicerBeamH.prototype.slicerPools = {
	1: { dice: 8, set: 12 },
	2: { dice: 16, set: 24 },
	3: { dice: 24, set: 36 }
};

MolecularSlicerBeamH.prototype.getPools = function () {
	var turns = Math.min(3, Math.max(1, this.turnsloaded));
	//Raking mode can never draw on a three-turn charge - it is capped at two turns' output
	//(see setSystemDataWindow). Mirrored server-side by getEffectiveTurnsLoaded().
	if (this.firingMode == 2) turns = Math.min(2, turns);
	return this.slicerPools[turns];
};

MolecularSlicerBeamH.prototype.initializationUpdate = function () {
	this.fireControl = this.fireControlArray[this.firingMode]; //reset

	//Pool sizes and the damage readout (including the Raking-at-full-charge cap) all come
	//out of getPools(), so the base implementation covers them.
	MolecularSlicerBeamL.prototype.initializationUpdate.call(this);

	//Piercing Mode at 1 or 2 turn charge doesn't get -20% hitchance
	//if(this.turnsloaded < 3 && (this.firingMode == 1 || this.firingMode == 3)){
	if(this.turnsloaded < 3 && this.firingMode == 1){
		this.data["Fire control (fighter/med/cap)"] = '20/30/40';
	}

    if(this.startArcArray.length > 0){ //More than one arc e.g. Battlecruiser
		this.data["Arc"] = this.startArcArray[0] + "..." + this.endArcArray[0] + ", " +  this.startArcArray[1] + "..." + this.endArcArray[1];
	}

	return this;
};

MolecularSlicerBeamH.prototype.calculateSpecialHitChanceMod = function (shooter, target, calledid) {
	var mod = 0;
	//Check fireOrders length and deduct (length -1 *5)
	var currentShots = this.fireOrders.length; //
	mod -= Math.max(0, currentShots); //This is called when considering the NEXT shot.  So can just use current length as mod.

	//if (this.turnsloaded < 3 && (this.firingMode == 1 || this.firingMode == 3)) mod += 4;
	if (this.turnsloaded < 3 && this.firingMode == 1) mod += 4;	

    if(target.flight &&  calledid && calledid !== -1){ //Has fireorder against fighter unit, and is a called shot
        mod += 8; //CalledShotmod is -8, so just compensate for that.            
    }

	return mod;
};

// H inherits doMultipleFireOrders from L now that L handles grouping and delegation.
// We only need to override isLegalToFireMode.
MolecularSlicerBeamH.prototype.isLegalToFireMode = function (shooter) {
	if (this.turnsloaded >= 3) {
		const currentMode = this.firingMode;

		for (let i = this.fireOrders.length - 1; i >= 0; i--) {
			//Self-intercept orders are defensive and carry whatever mode the weapon happened
			//to be set to when they were declared - they aren't a mode commitment, so they
			//must not trip the Piercing/Raking mixing guard.
			if (this.fireOrders[i].type === "selfIntercept") continue;

			const existingMode = this.fireOrders[i].firingMode;

			//const existingPiercing = (existingMode === 1 || existingMode === 3);
			//const currentPiercing = (currentMode === 1 || currentMode === 3);
			const existingPiercing = existingMode === 1;
			const currentPiercing = currentMode === 1;

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
			if (fireOrder.type === "selfIntercept") continue; //has its own remove button

			if (this.firingMode == fireOrder.firingMode && fireOrder.targetid == target.id) {
				// Remove the matching fire order
				this.fireOrders.splice(i, 1);
				removed = true;
				break;
			}
		}
	}

	// If NONE matched, remove the last OFFENSIVE fire order instead. Self-intercept orders
	// are peeled off with the dedicated intercept-remove button (which also fixes up the
	// remaining shots' hit chances), so they must not be swept up here.
	if (!removed) {
		for (let i = this.fireOrders.length - 1; i >= 0; i--) {
			if (this.fireOrders[i].type === "selfIntercept") continue;
			this.fireOrders.splice(i, 1);
			removed = true;
			break;
		}
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

		var chance = window.weaponManager.calculateHitChange(shooter, target, this, calledid).hitChance;
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
