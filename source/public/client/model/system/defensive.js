"use strict";

var InterceptorMkI = function InterceptorMkI(json, ship) {
	Weapon.call(this, json, ship);
	this.defensiveType = "Interceptor";
};
InterceptorMkI.prototype = Object.create(Weapon.prototype);
InterceptorMkI.prototype.constructor = InterceptorMkI;

InterceptorMkI.prototype.hasMaxBoost = function () {
	return true;
};
InterceptorMkI.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

InterceptorMkI.prototype.initializationUpdate = function () {
	var boost = shipManager.power.getBoost(this);
	if (boost > 0) {
		this.data["Intercept"] = "-"; //To inform player.
		this.data["Fire control (fighter/med/cap)"] = "30/-/-";
	}
	return this;
};

InterceptorMkI.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return shipManager.systems.getOutputNoBoost(target, this);
};

var InterceptorMkII = function InterceptorMkII(json, ship) {
	InterceptorMkI.call(this, json, ship);
};
InterceptorMkII.prototype = Object.create(InterceptorMkI.prototype);
InterceptorMkII.prototype.constructor = InterceptorMkII;

InterceptorMkII.prototype.initializationUpdate = function () {
	var boost = shipManager.power.getBoost(this);
	if (boost > 0) {
		this.data["Intercept"] = "-"; //To inform player.
		this.data["Fire control (fighter/med/cap)"] = "40/-/-";
	}
	return this;
};

var InterceptorPrototype = function InterceptorPrototype(json, ship) {
	InterceptorMkI.call(this, json, ship);
};
InterceptorPrototype.prototype = Object.create(InterceptorMkI.prototype);
InterceptorPrototype.prototype.constructor = InterceptorPrototype;

var Shield = function Shield(json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
};

Shield.prototype = Object.create(ShipSystem.prototype);
Shield.prototype.constructor = Shield;
Shield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	if (!weapon.ballistic) {
		if (shooter.flight && (mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)) return 0;
	}
	return shipManager.systems.getOutput(target, this);
};

var EMShield = function EMShield(json, ship) {
	Shield.call(this, json, ship);
	this.defensiveType = "Shield";
};
EMShield.prototype = Object.create(Shield.prototype);
EMShield.prototype.constructor = EMShield;

EMShield.prototype.initializationUpdate = function () {
	// Turns systems back on after Capacitor was double charged the previous turn
	var ship = this.ship;
	if (ship.faction == "Vorlon Empire") {
		this.reactivated = true;		
		if (!this.reactivated && gamedata.gamephase === 1 && shipManager.power.isOffline(ship, this)) {
			shipManager.power.setOnline(ship, this);
		}
	}
	return this;
};

var GraviticShield = function GraviticShield(json, ship) {
	Shield.call(this, json, ship);
	this.defensiveType = "Shield";
};
GraviticShield.prototype = Object.create(Shield.prototype);
GraviticShield.prototype.constructor = GraviticShield;

GraviticShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	if (!weapon.ballistic) {
		if (shooter.flight && (mathlib.getDistanceBetweenShipsInHex(target, shooter) == 0)) return 0;
	}

	var defenceMod = shipManager.systems.getOutput(target, this);; // Default value

	// New check looking for Abbai Shield Projectors and updating front-end values
	var thisShip = this.ship;
	if (thisShip.faction == "Abbai Matriarchate") {

		gamedata.ships.forEach(ship => {
			if (ship.faction !== "Abbai Matriarchate") return; //Only Abbai have Projectors.
			if (ship.team !== target.team) return; // On same team as the target
			if (shipManager.isDestroyed(ship)) return; // Skip destroyed ships
			if (ship.phpclass == 'alanti' || ship.phpclass == 'pirocia') {  // Only OSAT and Base matter
				// Process each system in the current ship
				ship.systems.forEach(system => {
					if (!(system instanceof AbbaiShieldProjector)) return; // Only Shield Reinforcement systems

					// Process all fire orders for the system, should only be one.
					system.fireOrders.forEach(fireOrder => {
						if (fireOrder.targetid === thisShip.id) {
							defenceMod += system.output; // Increase defenceMod for this shield
						}
					});

				});
			}
		});
	}

	return defenceMod;
};

var ShieldGenerator = function ShieldGenerator(json, ship) {
	ShipSystem.call(this, json, ship);
};

ShieldGenerator.prototype = Object.create(ShipSystem.prototype);
ShieldGenerator.prototype.constructor = ShieldGenerator;

ShieldGenerator.prototype.onTurnOff = function (ship) {
	for (var i in ship.systems) {
		var system = ship.systems[i];
		if (system.name == 'graviticShield') {
			// Shut it down.
			system.power.push({
				id: null,
				shipid: ship.id,
				systemid: system.id,
				type: 1,
				turn: gamedata.turn,
				amount: 0
			});
			shipWindowManager.setDataForSystem(ship, system);
		}
	}
};

ShieldGenerator.prototype.onTurnOn = function (ship) {
	for (var i in ship.systems) {
		var system = ship.systems[i];
		if (system.name == 'graviticShield') {
			// Turn it all on.
			shipManager.power.setOnline(ship, system);
			shipWindowManager.setDataForSystem(ship, system);
		}
	}
};

var Swrayshield = function Swrayshield(json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
};
Swrayshield.prototype = Object.create(ShipSystem.prototype);
Swrayshield.prototype.constructor = Swrayshield;
Swrayshield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return 0; //Ray shield does not affect hit chance
};
Swrayshield.prototype.hasMaxBoost = function () {
	return true;
};
Swrayshield.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

var CWShield = function CWShield(json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
};
CWShield.prototype = Object.create(ShipSystem.prototype);
CWShield.prototype.constructor = CWShield;
CWShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return 0; //Ray shield does not affect hit chance
};
CWShield.prototype.hasMaxBoost = function () {
	return true;
};
CWShield.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

var SatyraShield = function SatyraShield(json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
};
SatyraShield.prototype = Object.create(ShipSystem.prototype);
SatyraShield.prototype.constructor = SatyraShield;
SatyraShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return 0; //Ray shield does not affect hit chance
};
SatyraShield.prototype.hasMaxBoost = function () {
	return true;
};
SatyraShield.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

var Absorbtionshield = function Absorbtionshield(json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
};
Absorbtionshield.prototype = Object.create(ShipSystem.prototype);
Absorbtionshield.prototype.constructor = Absorbtionshield;
Absorbtionshield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return 0; //absorbtion shield does not affect hit chance
};
Absorbtionshield.prototype.hasMaxBoost = function () {
	return true;
};
Absorbtionshield.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};

var Particleimpeder = function Particleimpeder(json, ship) {
	Weapon.call(this, json, ship);
	this.defensiveType = "Impeder";
};
Particleimpeder.prototype = Object.create(Weapon.prototype);
Particleimpeder.prototype.constructor = Particleimpeder;
Particleimpeder.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return shipManager.systems.getOutput(target, this);
	/* now it affects everything!
	if (shooter.flight) {
		//only affects fighters
		return shipManager.systems.getOutput(target, this);
	} else {
		return 0;
	}
	*/
};
Particleimpeder.prototype.hasMaxBoost = function () {
	return true;
};
Particleimpeder.prototype.getMaxBoost = function () {
	return this.maxBoostLevel;
};
Particleimpeder.prototype.initBoostableInfo = function () {
	// Needed because it can chance during initial phase
	if (window.weaponManager.isLoaded(this)) { } else {
		var count = shipManager.power.getBoost(this);
		for (var i = 0; i < count; i++) {
			shipManager.power.unsetBoost(null, this);
		}
	}

	this.intercept = this.getInterceptRating();
	this.data.Intercept = this.getInterceptRating() * -5;
	this.data.Boostlevel = shipManager.power.getBoost(this);

	//display current boost as output - because that is actual shield rating from Impeder!
	if (this.data.Boostlevel > 0) {
		this.outputDisplay = this.data.Boostlevel;
	} else {
		this.outputDisplay = '-'; //'0' is not shown!
	}


	return this;
};
Particleimpeder.prototype.getInterceptRating = function () {
	return 3 + shipManager.power.getBoost(this);
};

var FtrShield = function (json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
}
FtrShield.prototype = Object.create(ShipSystem.prototype);
FtrShield.prototype.constructor = FtrShield;
FtrShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	return shipManager.systems.getOutput(target, this);
}

/*
	Grav Shield variant for the Abbai Kotha. May be "dropped" during Initial Orders (active = true)
	to gain +2 thrust; whilst dropped the shield provides no defensive benefit. Toggle persistence
	and flight-wide replication mirror FtrPetals.
*/
var FtrGravShield = function (json, ship) {
	ShipSystem.call(this, json, ship);
	this.defensiveType = "Shield";
	this.activeMeansOff = true; //when active, the system is switched OFF (shield dropped) - drives a distinct icon state
}
FtrGravShield.prototype = Object.create(ShipSystem.prototype);
FtrGravShield.prototype.constructor = FtrGravShield;

//while dropped (active) the shield provides no defensive benefit
FtrGravShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	if (this.active) return 0;
	return shipManager.systems.getOutput(target, this);
}

//Called from systemFactory.js (initializeOnLoad) so the flight's thrust/tooltip update on page load.
FtrGravShield.prototype.initializationUpdate = function () {
	var ship = this.ship;
	var flight = gamedata.getShip(ship.flightid);//Need to convert to full ship info.

	if (this.active) {
		this.outputDisplay = "OFF"; //shield dropped
		//Incredibly specific here, but it avoids alot of issues trying to pass effects from server
		if (flight && ship.name == "Kotha") {
			flight.freethrust = 13; //11 base +2 thrust whilst shield dropped
		}
	} else {
		this.outputDisplay = "-";
		if (flight && ship.name == "Kotha") {
			flight.freethrust = 11;
		}
	}

	return this;
}

//Buttons are reversed vs FtrPetals: the shield is normally ON, so "Deactivate" drops it (active = true)
//for +2 thrust and "Activate" raises it again (active = false).
FtrGravShield.prototype.canActivate = function () {
	if (gamedata.gamephase == 1 && this.active) return true; //raise shield (currently dropped)
	return false;
};

FtrGravShield.prototype.canDeactivate = function () {
	if (gamedata.gamephase == 1 && !this.active) return true; //drop shield (currently raised)
	return false;
};

FtrGravShield.prototype.doActivate = function () { //raise the shield across the whole flight
	this.setFlightDropped(false);
};

FtrGravShield.prototype.doDeactivate = function () { //drop the shield across the whole flight
	this.setFlightDropped(true);
};

//Set the dropped state on every FtrGravShield in the flight (flight-wide toggle, like FtrPetals).
FtrGravShield.prototype.setFlightDropped = function (dropped) {
	var ship = this.ship;
	var flight = gamedata.getShip(ship.flightid);//Need to convert to full ship info.
	if (flight) {
		for (var i in flight.systems) {
			var system = flight.systems[i]; //The fighter
			if (!shipManager.systems.isDestroyed(ship, flight.systems[i])) {
				for (var j in system.systems) {
					var fighterSystem = system.systems[j];	//The fighter's systems.
					if (fighterSystem.name == "FtrGravShield") {
						fighterSystem.active = dropped; //Set marker for notes.
					}
				}
			}
		}
		webglScene.customEvent('SystemDataChanged', { ship: flight, system: this });
	}
};

FtrGravShield.prototype.doIndividualNotesTransfer = function () {
	if (gamedata.gamephase == 1) {
		var active = this.active; //Was dropped this turn.
		this.individualNotesTransfer = Array();
		if (active) {
			this.individualNotesTransfer.push(1);
		}
	}
};

var HeavyInterceptorBattery = function HeavyInterceptorBattery(json, ship) {
	InterceptorMkI.call(this, json, ship);
};
HeavyInterceptorBattery.prototype = Object.create(InterceptorMkI.prototype);
HeavyInterceptorBattery.prototype.constructor = HeavyInterceptorBattery;

HeavyInterceptorBattery.prototype.initializationUpdate = function () {
	var boost = shipManager.power.getBoost(this);
	if (boost > 0) {
		this.data["Intercept"] = "-"; //To inform player.
		this.data["Fire control (fighter/med/cap)"] = "50/-/-";
	}
	return this;
};

var Interdictor = function Interdictor(json, ship) {
	Weapon.call(this, json, ship);
};
Interdictor.prototype = Object.create(Weapon.prototype);
Interdictor.prototype.constructor = Interdictor;

var FtrInterdictor = function FtrInterdictor(json, ship) {
	Weapon.call(this, json, ship);
	this.name = "FtrInterdictor";
	this.displayName = "Light Interdictor";
	this.iconPath = "Interdictor.png";
	this.fireControl = [null, null, null];
	this.data = {
		"Damage type": "Standard",
		"Weapon type": "Particle",
		"Damage": "0",
		"Range penalty": "10.00 per hex",
		"Fire control (fighter/med/cap)": "-/-/-",
		"Intercept": "-15",
		"Resolution Priority (ship/fighter)": "1/1",
		"Special": "May intercept for friendly units. Must have friendly and enemy unit in arc and have friendly unit within 3 hexes.<br>Only one interdictor can be applied to any incoming shot, including ballistics.<br>When intercepting non-ballistic fire from an enemy fighter flight, all enemy fighters have their chance to hit reduced by 15.<br>Note - Automated intercept routines will often prioritise ship or missile fire, so aim Interdictor carefully to make full use of it against enemy fighter fire."
	};
	if (this.guns > 1) {
		this.data["Number of guns"] = this.guns;
	}
};
FtrInterdictor.prototype = Object.create(Weapon.prototype);
FtrInterdictor.prototype.constructor = FtrInterdictor;


var ThirdspaceShield = function ThirdspaceShield(json, ship) {
	Shield.call(this, json, ship);
	this.defensiveType = "none";
};
ThirdspaceShield.prototype = Object.create(Shield.prototype);
ThirdspaceShield.prototype.constructor = ThirdspaceShield;
ThirdspaceShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	//this is made to be a shield just to display arc visually, no actual protection
	return 0;
};

ThirdspaceShield.prototype.initializationUpdate = function () {
	this.outputDisplay = this.currentHealth;

	if (this.currentHealth == 0) {
		this.outputDisplay = '-'; //'0' is not shown!							
	}

	return this;
};

ThirdspaceShield.prototype.getGenerator = function () {
	var ship = this.ship;
	for (var i in ship.systems) {
		if (ship.systems[i] instanceof ThirdspaceShieldGenerator) {
			return ship.systems[i];
		}
	}
	return null;
};

ThirdspaceShield.prototype.canIncrease = function () {
	if (this.ship.flight) return false; //Fighters can't increase or decrease shields
	if (this.currentHealth >= this.maxhealth) return false; //Shield at maximum output.
	return !!this.getGenerator(); //No generator, can't move shields around.
};

ThirdspaceShield.prototype.canDecrease = function () {
	if (this.ship.flight) return false;
	if (this.currentHealth <= 0) return false;
	return !!this.getGenerator();
};

ThirdspaceShield.prototype.doIncrease = function (amount) {
	var generator = this.getGenerator();
	if (!generator) return;

	var increment = Math.min(amount || 1, this.maxhealth - this.currentHealth);
	if (increment <= 0) return;

	this.currentHealth += increment;
	generator.storedCapacity -= increment;
	this.outputDisplay = this.currentHealth;
};

ThirdspaceShield.prototype.doDecrease = function (amount) {
	var generator = this.getGenerator();
	if (!generator) return;

	var decrement = Math.min(amount || 1, this.currentHealth);
	if (decrement <= 0) return;

	this.currentHealth -= decrement;
	generator.storedCapacity += decrement;
	this.outputDisplay = this.currentHealth === 0 ? '-' : this.currentHealth;
};

ThirdspaceShield.prototype.doMin = function () {
	this.doDecrease(this.currentHealth);
};

ThirdspaceShield.prototype.doMax = function () {
	this.doIncrease(this.maxhealth - this.currentHealth);
};

ThirdspaceShield.prototype.doIndividualNotesTransfer = function () { //prepare individualNotesTransfer variable - if relevant for this particular system
	this.individualNotesTransfer = Array();
	//Now pass a note to create a damage entry that will either increase or decrease shields strength.
	var startHealth = shipManager.systems.getRemainingHealth(this); //What was the health at start of Initial Orders?
	var endHealth = this.currentHealth; //currentHealth is effectively the counter for where shield strength ends up.
	var shieldChange = startHealth - endHealth;//What is the change that should go into Damage Entry. Positive if decreased, negative if increased.

	if (gamedata.gamephase == 1 && (shieldChange != 0)) {
		this.individualNotesTransfer.push(shieldChange); //Push change in shield strength to back end for Damage Entry creation if required e.g. over or under 0.
	}

	return true;
};


//MINDRIDER THOUGHT SHIELDS
var ThoughtShield = function ThoughtShield(json, ship) {
	ThirdspaceShield.call(this, json, ship);
	this.defensiveType = "none";
};
ThoughtShield.prototype = Object.create(ThirdspaceShield.prototype);
ThoughtShield.prototype.constructor = ThoughtShield;

ThoughtShield.prototype.getDefensiveHitChangeMod = function (target, shooter, weapon) {
	var thisShip = this.ship;
	var defenceMod = 0; // Default value

	// Iterate through all ships
	gamedata.ships.forEach(ship => {
		if (ship.phpclass !== 'Consortium') return; // Only Consortium ships
		if (ship.team !== target.team) return; // Same team as the target
		if (shipManager.isDestroyed(ship)) return; // Skip destroyed ships

		// Process each system in the current ship
		ship.systems.forEach(system => {
			if (!(system instanceof ShieldReinforcement)) return; // Only Shield Reinforcement systems

			// Calculate baseOutput once
			const baseOutput = shipManager.systems.getRemainingHealth(system);
			let noOfShieldsBoosted = 0;

			// Process all fire orders for the system
			system.fireOrders.forEach(fireOrder => {
				if (fireOrder.targetid === thisShip.id) {
					defenceMod = system.reinforceAmount; // Set defenceMod for this ship
				}

				// Check the target ship's systems for Thought Shields
				const reinforceTarget = gamedata.getShip(fireOrder.targetid);
				noOfShieldsBoosted += reinforceTarget.systems.filter(s => s instanceof ThoughtShield).length;
			});

			// Handle the case where the Consortium is the target ship
			if (ship.id === thisShip.id) {
				const usedOutput = noOfShieldsBoosted * system.reinforceAmount;
				const remainingOutput = baseOutput - usedOutput;

				// Count Thought Shields on this ship
				const ownShields = thisShip.systems.filter(s => s instanceof ThoughtShield).length;

				if (ownShields > 0) {
					defenceMod = Math.floor(remainingOutput / ownShields);
				}
			}
		});
	});

	return defenceMod; // Return the calculated defenceMod
};
