"use strict";

shipManager.power = {

	repeatLastTurnPower: function repeatLastTurnPower() {

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];

			if (ship.userid != gamedata.thisplayer) continue;

			if (ship.flight) {
				for (var i in ship.systems) {
					var fighter = ship.systems[i]; //The fighter		
					for (var j in fighter.systems) {
						shipManager.power.copyLastTurnPower(ship, fighter.systems[j]);
					}
				}
			} else {
				for (var a in ship.systems) {
					shipManager.power.copyLastTurnPower(ship, ship.systems[a]);
				}
			}
		}
	},

	copyLastTurnPower: function copyLastTurnPower(ship, system) {
		if (shipManager.systems.isDestroyed(ship, system)) return;

		//Defensive: mid-game-spawned fighter systems can arrive without
		//$power populated if their static blueprint wasn't preloaded into
		//window.staticShips. Without this, the .concat() call below crashes
		//the InitialPhaseStrategy on the next-turn transition.
		if (!Array.isArray(system.power)) system.power = [];

		//if system WAS forcibly shut down last turn but is NOT forced to shut down any longer - it should get back online without player input!
		var wasShutDown = false;
		var isShutDown = false;
		if ((shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")) || (shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns"))) {
			isShutDown = true;
		} else {
			if ((shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineOneTurn", gamedata.turn - 1))
				|| (shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineForTurns", gamedata.turn - 1))
			) {
				wasShutDown = true;
			}
		}

		//copy last turn power 
		var powers = Array();
		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn == gamedata.turn - 1) {
				var newPower = jQuery.extend({}, power);
				newPower.turn = gamedata.turn;
				/* HYPERSPACE_IMPROVEMENTS_PLAN.md 5 (Stage H4) - a jump engine decides how much of last
				   turn's BOOST may carry: on an Ancient-charging drive, last turn's extra charging carried
				   onto a turn the drive is charged would be a JUMP TO HYPERSPACE. See
				   JumpEngine.getRepeatableBoost; every other system copies exactly as before. */
				if (power.type == 2 && typeof system.getRepeatableBoost === 'function') {
					newPower.amount = system.getRepeatableBoost(parseInt(power.amount, 10) || 0);
					if (!(newPower.amount > 0)) continue;
				}
				powers.push(newPower);
			}
		}

		system.power = system.power.concat(powers);

		if (wasShutDown && (!isShutDown)) { //was forced to shut down, but is no longer - power up!
			shipManager.power.setOnline(ship, system, true); //do skip message to player - if system cannot be powered up, it won't be powered up and that's it
		}

		//A power-locked system may not stay voluntarily offline (Antigravity Beam whose
		//Kirishiac Orbital just deployed: it could be powered down while docked, but a
		//deployed beam is always on). Force it back online without player input - same
		//no-message convention as the forced-shutdown recovery above.
		if (system.powerLocked && shipManager.power.isOffline(ship, system)) {
			shipManager.power.setOnline(ship, system, true);
		}

		//If the system is forced offline THIS turn (cooldown crit etc.), make sure the
		//offline power entry exists. This used to be created only as a side-effect of
		//rendering the legacy ship status window (setPowerClasses), which is now built
		//lazily on first open (see ShipIcon.createShipWindow). Without this, a forced-
		//offline weapon on a ship whose window is never opened never gets its type:1
		//entry, so it never enters cooldown and the server (which trusts client power
		//entries) lets the player turn it back on. Mirrors setPowerClasses' behaviour
		//so the two paths agree and never double-add for the same turn.
		//Scoped to non-flight ships: the legacy status window (and thus the original
		//setPowerClasses side-effect) only ran for capital ships, never the flight
		//window, so we don't introduce new offline behaviour for fighter subsystems.
		if (isShutDown && !ship.flight) {
			shipManager.power.applyForcedOfflineEntry(ship, system);
		}
	},

	//True when the system is forced offline THIS turn by a cooldown / forced-shutdown
	//crit (ForcedOfflineForTurns or ForcedOfflineOneTurn), as opposed to being powered
	//down by player choice. Mirrors the isShutDown condition in copyLastTurnPower. Used
	//to forbid the player from re-enabling a cooling weapon — the legacy DOM window used
	//to silently re-add the offline entry on every render, but the React power UI does
	//not, so the toggle has to be blocked at the source.
	isForcedOffline: function isForcedOffline(ship, system) {
		return Boolean(shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")
			|| shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns"));
	},

	/* ===== JUMP_POINTS_PLAN.md STAGE 5 - HOLDING A JUMP POINT OPEN ==========================

	   Maintaining a vortex costs the ship everything it has: every power-absorbing system except
	   the Scanner and the Jump Engine itself must be OFF for the whole turn (plan section 2.4).
	   The Maintain toggle in the Jump Engine's menu switches them off in one go, and these four
	   helpers are what keeps them off until the player turns Maintain back off.

	   MIRROR of JumpEngine::getVortexPowerViolations on the server, which is authoritative: it
	   re-tests the rule at the end of the turn and closes the vortex if it was broken. The server
	   asks `instanceof Scanner`, which catches the ELINT / SW / Antiquated subclasses; a client
	   system carries no class information, so the four names are listed here instead. A fifth
	   scanner class would need adding to this list - and until then the only cost of drift is a
	   system needlessly shut down, never a rule that differs. */
	vortexMaintainExemptNames: ['jumpEngine', 'reactor', 'scanner', 'elintScanner', 'SWScanner', 'AntiquatedScanner'],

	isVortexExemptSystem: function isVortexExemptSystem(system) {
		return Boolean(system) && shipManager.power.vortexMaintainExemptNames.indexOf(system.name) !== -1;
	},

	//Is this ship's Jump Engine declaring Maintain this turn? The firing-mode-7 ballistic order IS
	//the declaration - there is no separate flag anywhere - so this answer survives a poll, a page
	//reload and the commit for free, and it is the same thing the server reads.
	isMaintainingVortex: function isMaintainingVortex(ship) {
		if (!ship || !ship.systems) return false;

		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (!system || system.name !== 'jumpEngine') continue;
			if (typeof system.isMaintainingVortex !== 'function') continue;
			if (system.isMaintainingVortex()) return true;
		}

		return false;
	},

	/* HYPERSPACE_IMPROVEMENTS_PLAN.md 4 (Stage H3) - DOES THIS SHIP'S JUMP DRIVE PAY AN UPKEEP
	   INSTEAD OF GOING DARK? The Vorlon rule (R4), and the one predicate the two Maintain power
	   sites below both ask, so "which ships are exempt from the all-systems-dark rule" is stated
	   once.

	   A SHIP-level question because both callers are: they are about this hull's power allocation,
	   not about one engine. That is unambiguous in practice - no hull in the game mixes a
	   capacitor-fed drive with an ordinary one, and a hull with two Vorlon drives has both marked.
	   `vortexUpkeep` is sent by JumpEngine::stripForJson only on a drive the server will really
	   charge, so this is false on every other ship in the game and in the lobby. */
	hasVortexUpkeepDrive: function hasVortexUpkeepDrive(ship) {
		if (!ship || !ship.systems) return false;

		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (!system || system.name !== 'jumpEngine') continue;
			if (typeof system.chargesVortexUpkeep !== 'function') continue;
			if (system.chargesVortexUpkeep()) return true;
		}

		return false;
	},

	/* Stage H3 - the upkeep this ship's drives are RESERVING out of the reactor balance this turn,
	   summed. Read by PowerCapacitor.doIndividualNotesTransfer, which has to put it back into the
	   power the capacitor stores because the SERVER is what spends it - see the long note there.
	   0 on every ship that is not a Vorlon holding a jump point open. */
	getVortexUpkeepReserved: function getVortexUpkeepReserved(ship) {
		var reserved = 0;
		if (!ship || !ship.systems) return reserved;

		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (!system || system.name !== 'jumpEngine') continue;
			if (typeof system.getVortexUpkeepDraw !== 'function') continue;
			if (shipManager.systems.isDestroyed(ship, system)) continue;
			if (shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn)) continue;

			reserved += system.getVortexUpkeepDraw();
		}

		return reserved;
	},

	//Every power-absorbing system that is still ONLINE and so would break the rule. SYSTEMS, not
	//names, because the caller both names them (the menu) and switches them off (the toggle).
	//
	//Stage H3: EMPTY on a capacitor-fed (Vorlon) drive's ship. Paying the upkeep REPLACES the
	//all-systems-dark rule (R4), so there is nothing here that would break it - and answering
	//anything else would have doActivate black the ship out and then isVortexLockedOffline refuse to
	//give the power back, for a rule that does not apply to it.
	getVortexMaintainBlockers: function getVortexMaintainBlockers(ship) {
		var blockers = [];
		if (!ship || !ship.systems) return blockers;
		if (shipManager.power.hasVortexUpkeepDrive(ship)) return blockers;

		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (!system) continue;
			if (shipManager.power.isVortexExemptSystem(system)) continue;
			if (!(system.powerReq > 0)) continue;                                //nothing to switch off
			if (shipManager.systems.isDestroyed(ship, system)) continue;         //draws nothing
			if (shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn)) continue; //already off

			blockers.push(system);
		}

		return blockers;
	},

	//THE LOCK. While the Maintain declaration stands, a system it shut down may not be powered back
	//on - the rule is 'everything off for the whole turn', not 'everything off at the moment you
	//click'. Enforced in setOnline (so every route is covered) and again in the two canOnline gates
	//(so the button reads as unavailable rather than warning on click).
	isVortexLockedOffline: function isVortexLockedOffline(ship, system) {
		if (!system || shipManager.power.isVortexExemptSystem(system)) return false;
		if (!(system.powerReq > 0)) return false;
		//Stage H3 (R4): a capacitor-fed Vorlon drive pays an upkeep instead of taking the ship dark,
		//so there is no all-or-nothing shutdown to lock. Without this the one-click Maintain would
		//refuse to power anything back on for a rule the ship is not subject to.
		if (shipManager.power.hasVortexUpkeepDrive(ship)) return false;

		return shipManager.power.isMaintainingVortex(ship);
	},

	//Window-independent forced-offline enforcement, extracted from setPowerClasses so it
	//runs for every owned ship at turn start (repeatLastTurnPower) regardless of whether
	//the legacy status window was ever opened. Adds the type:1 offline entry for the
	//current turn if not already present, resets engine power, and stops overloading.
	applyForcedOfflineEntry: function applyForcedOfflineEntry(ship, system) {
		if (!Array.isArray(system.power)) system.power = [];

		var alreadyOffline = false;
		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;
			if (power.type == 1) {
				alreadyOffline = true;
				break;
			}
		}

		if (system.name == "engine") {
			system.power = [];
			alreadyOffline = false; //array just cleared, re-add below
		}

		if (!alreadyOffline) {
			system.power.push({ id: null, shipid: ship.id, systemid: system.id, type: 1, turn: gamedata.turn, amount: 0 });
			shipManager.power.stopOverloading(ship, system);
		}
	},

	setPowerClasses: function setPowerClasses(ship, system, systemwindow) {
		var off = shipManager.power.isOffline(ship, system);

		if (shipManager.criticals.hasCritical(system, "OutputReducedOneTurn")) {
			for (var j = 0; j < system.criticals.length; j++) {
				if (system.criticals[j].phpclass == "OutputReducedOneTurn") {
					if (system.criticals[j].turn == gamedata.turn - 1 || system.criticals[j].turn == gamedata.turn) {
						systemwindow.addClass("forcedoffline");
					}
				}
			}
		} else if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn") || shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns")) {
			systemwindow.addClass("forcedoffline");

			// Because of the crit, add a power entry to the power array of this
			// system (only once per turn). Shared with copyLastTurnPower so the
			// window-render path and the turn-start path stay in lockstep.
			shipManager.power.applyForcedOfflineEntry(ship, system);

			return true;
		}

		if (off) {
			if (system.name == "reactor") {
				// This is the reactor. It has recovered from a ForcedOffline crit
				// (If it still had one, it would have entered the previous if-statement)
				// Remove class offline and give user feedback.
				shipManager.power.setOnline(ship, system);
				systemwindow.removeClass("offline");

				//    var userMessage = "The reactor of the " + ship.name +" has recovered from a forced shutdown.<br>";
				//    userMessage += "Power up all necessary systems.";
				//    window.confirm.error(userMessage, function(){});

				return;
			} else {
				systemwindow.addClass("offline");
				return true;
			}
		}

		if (shipManager.power.isOverloading(ship, system)) {
			systemwindow.addClass("overload");
		}

		if (gamedata.gamephase != 1 || ship.userid != gamedata.thisplayer) return;

		if (system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system)) {
			systemwindow.addClass("canoverload");
		}

		var boost = shipManager.power.getBoost(system);

		if (system.boostable && !boost) {
			//if(system.name == "scanner" || system.name == "elintScanner"){
			if (system.isScanner()) {
				if ((ship.base) //02.12.2024, Marcin Sawicki - bases can boost any sonsors, not just strongest
					|| (system.id == shipManager.power.getHighestSensorsId(ship))
				) {
					// You can only boost the highest sensor rating
					// if multiple sensors are present on one ship
					systemwindow.addClass("canboost");
				}
			} else {
				systemwindow.addClass("canboost");
			}
		} else if (boost) {
			systemwindow.addClass("boosted");
		}

		if (system.canOffLine || system.powerReq > 0 && !off && !boost && !weaponManager.hasFiringOrder(ship, system)) {
			systemwindow.addClass("canoffline");
		}

		return false;
	},

	getHighestSensorsId: function getHighestSensorsId(ship) {
		var highestRating = -1;
		var highestId = -1;

		for (var i in ship.systems) {
			var system = ship.systems[i];

			//if(system.name == "scanner" || system.name == "elintScanner"){
			if (system.isScanner()) {
				if (!shipManager.power.isOffline(ship, system)) {
					var rating = shipManager.systems.getOutput(ship, system);
					if (rating > highestRating) {
						highestId = system.id;
						highestRating = rating;
					}
				}
			}
		}

		return highestId;
	},

	//Returns ship OBJECTS, not names: the commit-error dialogs render these through
	//gamedata.shipNameSpan, which needs ship.id to make the name clickable (scroll-to-ship).
	getShipsGraviticShield: function getShipsGraviticShield() {
		var ships = new Array();
		var counter = 0;

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
			if (ship.unavailable) continue;

			if (ship.flight) continue;

			if (ship.userid != gamedata.thisplayer) continue;

			if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;

			var deployTurn = shipManager.getTurnDeployed(ship);
			if (deployTurn > gamedata.turn && !shipManager.canManagePowerFromHyperspace(ship)) continue;  //Not on the board yet - but a reinforcement may set its power up while it waits in hyperspace (HYPERSPACE_IMPROVEMENTS_PLAN.md item 5), and a commit gate that skipped it would let an illegal allocation through.

			if (!ship.checkShieldGenerator()) {
				ships[counter] = ship;
				counter++;
			}
		}

		return ships;
	},

	//Returns ship OBJECTS, not names - see getShipsGraviticShield above.
	getShipsNegativePower: function getShipsNegativePower() {
		var ships = new Array();
		var counter = 0;

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];

			if (ship.unavailable) continue;

			if (ship.flight) continue;
			if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
			if (ship.mine) continue;

			if (ship.userid != gamedata.thisplayer) continue;

			if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;

			var deployTurn = shipManager.getTurnDeployed(ship);
			if (deployTurn > gamedata.turn && !shipManager.canManagePowerFromHyperspace(ship)) continue;  //Not on the board yet - but a reinforcement may set its power up while it waits in hyperspace (HYPERSPACE_IMPROVEMENTS_PLAN.md item 5), and a commit gate that skipped it would let an illegal allocation through.

			//A reactor output-reduction crit forces the player to power systems down until
			//the reactor balance is non-negative. Some systems draw reactor power yet CANNOT
			//be voluntarily powered down (deployed Kirishiac orbital beams: powerLocked, and
			//copyLastTurnPower forces them back online) — their draw is real and stays counted
			//in getReactorPower. So the rule is: block while there is still a deficit AND the
			//player has at least one eligible system left to switch off. Once every switchable
			//system is already off, the residual deficit is unavoidable (locked draw only) and
			//the commit is allowed — the player is not blocked with no legal move.
			var reactorPower = shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor"));

			if (reactorPower < 0 && shipManager.power.getRemainingFreeablePower(ship) > 0) {
				ships[counter] = ship;
				counter++;
			}
		}

		return ships;
	},

	//Total reactor power the player could still free by voluntarily powering systems down
	//RIGHT NOW — the powerReq of every system that is currently drawing power and is still
	//eligible for the phase-1 Off toggle. Mirrors the client Off-button gate
	//(SystemPowerSettings / SystemInfoButtons): a system is switchable when it draws power
	//(powerReq > 0), is NOT powerLocked (deployed orbital beams are — they draw power but
	//can't be switched off), is not already offline, is not destroyed, and has no firing
	//order. getShipsNegativePower uses this to tell "you still have load to shed" (> 0, keep
	//blocking) from "everything switchable is already off" (0, allow the residual deficit).
	getRemainingFreeablePower: function getRemainingFreeablePower(ship) {
		var freeable = 0;
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (system.name == "reactor") continue;
			if (!(system.powerReq > 0)) continue;            //nothing to free by switching it off
			if (system.powerLocked) continue;                //deployed orbital beam etc. — draws power but cannot be switched off
			if (shipManager.systems.isDestroyed(ship, system)) continue;
			if (shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn)) continue;  //already off
			if (weaponManager.hasFiringOrder(ship, system)) continue;  //locked on by a declared fire order
			freeable += system.powerReq;
		}
		return freeable;
	},

	//like getShipsNegativePower BUT only looks for PowerCapacitor-equipped ships
	//Returns ship OBJECTS, not names - see getShipsGraviticShield above.
	getCapacitorShipsNegativePower: function getCapacitorShipsNegativePower() {
		var ships = new Array();
		var counter = 0;
		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
			if (ship.mine) continue;
			if (ship.unavailable) continue;
			if (ship.flight) continue;
			if (ship.userid != gamedata.thisplayer) continue;
			var deployTurn = shipManager.getTurnDeployed(ship);
			if (deployTurn > gamedata.turn && !shipManager.canManagePowerFromHyperspace(ship)) continue;  //...including in hyperspace - see getShipsNegativePower above.
			if (!(shipManager.systems.getSystemByName(ship, "powerCapacitor"))) continue;
			if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;
			if (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor")) < 0) {
				ships[counter] = ship;
				counter++;
			}
		}
		return ships;
	},	//endof getCapacitorShipsNegativePower


	//like getShipsNegativePower BUT only looks for PlasmaBattery-equipped ships
	//Returns ship OBJECTS, not names - see getShipsGraviticShield above.
	getPlasmaBatteryShipsNegativePower: function getPlasmaBatteryShipsNegativePower() {
		var batteryShips = new Array();
		var counter = 0;
		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if (ship.faction !== "Pak'ma'ra Confederacy") continue;
			if (ship.unavailable) continue;
			if (ship.flight) continue;
			if (ship.userid != gamedata.thisplayer) continue;
			var deployTurn = shipManager.getTurnDeployed(ship);
			if (deployTurn > gamedata.turn && !shipManager.canManagePowerFromHyperspace(ship)) continue;  //...including in hyperspace - see getShipsNegativePower above.
			if (!(shipManager.systems.getSystemByName(ship, "PlasmaBattery"))) continue;
			if (shipManager.isDestroyed(ship)) continue;

			var batteryPowerAvailable = 0;
			//Calculate battery power available (find all batteries that are not destroyed, sum up their contents)
			for (var i = 0; i < ship.systems.length; i++) {
				var currBattery = ship.systems[i];
				if (currBattery.name == "PlasmaBattery" && !(shipManager.systems.isDestroyed(ship, currBattery))) { //only Plasma Batteries which are not destroyed are of interest 
					batteryPowerAvailable += shipManager.systems.getOutput(ship, currBattery);
				}
			}

			var batteryPowerRequired = 0;
			//Calculate battery power required (find all Plasma Webs that are firing offensively without being boosted)			
			for (var i = 0; i < ship.systems.length; i++) {
				var currWeb = ship.systems[i];
				if (currWeb.name == "PakmaraPlasmaWeb") { //only Plasma Webs  are of interest 
					for (var k = 0; k < currWeb.fireOrders.length; k++) {
						var currFireOrder = currWeb.fireOrders[k];
						if ((currFireOrder.firingMode == "2") && (shipManager.power.getBoost(currWeb) == 0)) {
							batteryPowerRequired += 1;
						}
					}
				}
			}

			if (batteryPowerAvailable < batteryPowerRequired) {
				batteryShips[counter] = ship;
				counter++;
			}
		}
		return batteryShips;
	},	//endof getPlasmaBatteryShipsNegativePower


	getPowerNeedForSection: function getPowerNeedForSection(ship, loc) {
		var power = 0;

		for (var i = 0; i < ship.systems.length; i++) {
			if (ship.systems[i].location == loc) {
				if (!ship.systems[i].destroyed) {
					power += ship.systems[i].powerReq;
				}
			}
		}

		return power;
	},

	getAllReactors: function getAllReactors(ship) {
		var array = [];

		for (var i in ship.systems) {
			if (ship.systems[i].name == "reactor") {
				array.push(ship.systems[i]);
			}
		}

		array.sort(function (a, b) {
			if (a.location < b.location) {
				return -1;
			} else {
				return 1;
			}
		});

		return array;
	},

	getReactorPower: function getReactorPower(ship, system) {
		var fixedPower = false;
		var output;


		if (ship.base) {
			var reactors = shipManager.power.getAllReactors(ship);

			output = reactors[0].output;

			for (var i = 0; i < reactors.length; i++) {
				var reactor = reactors[i];
				if (!reactor.destroyed) {
					output += reactors[i].outputMod;
					if (reactors[i].edfDrain) output -= reactors[i].edfDrain; //see the note in the non-base branch below
					fixedPower = fixedPower || reactors[i].fixedPower; //assume fixed power if ANY reactor gives fixed power

					if (reactor.criticals.length > 0) {
						for (var j = 0; j < reactor.criticals.length; j++) {
							if (reactor.criticals[j].phpclass == "OutputReducedOneTurn") {
								if (reactor.criticals[j].turn == gamedata.turn - 1 || reactor.criticals[j].turn == gamedata.turn) {
									output -= shipManager.power.getPowerNeedForSection(ship, reactor.location);
								}
							}
						}
					}
				}
			}
		} else {
			var reactor = shipManager.systems.getSystemByName(ship, "reactor");
			output = reactor.output + reactor.outputMod;
			/* Walkers of Sigma-957 (WALKERS_OF_SIGMA_PLAN.md 2.2): the Energy Draining Field takes
			   power off the reactor for one turn, published as edfDrain by Reactor::stripForJson
			   (it cannot ride outputMod - see the plan's note on turn-filtered param criticals).
			   It has to come off HERE and not only in shipManager.systems.getOutput: this function
			   is the ship's whole power BALANCE, and it is what getShipsNegativePower and the
			   Initial Orders commit gate read. Without it a drained ship showed no deficit at all,
			   so its owner was never asked to power anything down (user report, 2026-09-04).
			   NOT clamped at 0 - unlike an output figure, a balance is meant to go negative; that
			   negative IS the deficit the player has to cover. */
			if (reactor.edfDrain) output -= reactor.edfDrain;
			fixedPower = reactor.fixedPower;
		}

		for (var s in ship.systems) {
			var system = ship.systems[s];

			//Some systems only know their true powerReq after initializationUpdate()
			//runs (e.g. PowerCapacitor sets a NEGATIVE powerReq to inject its stored
			//power into the reactor display; PlasmaBattery likewise). That init used to
			//run eagerly for every system when the legacy ship status window was built
			//at load. The window is now built lazily on first open (ShipIcon.createShipWindow),
			//so for a never-opened ship the capacitor stays uninitialised and the reactor
			//icon reads powerReq 0 — showing 0 available power until the player clicks a
			//system. Initialise here so the displayed power is correct regardless of
			//whether the window has ever been opened. initializeSystem() is idempotent.
			system = shipManager.systems.initializeSystem(system);

			/*temporary power down critical - may happen on C&C*/
			if (system.displayName == "C&C") { //no point checking other systems
				output -= shipManager.criticals.hasCritical(system, "tmppowerdown"); //Power output reduced
			}

			/*standard: add power for every system powered off
			  fixed: subtract power for every system powered on (instead!)
			*/
			if ((!system.destroyed)) { //destroyed system gets no power either way
				if (fixedPower == true) { //for Mag-Grav reactor: all systems draw power, unless off or destroyed (accounted for in a moment)
					output -= system.powerReq;
				}
				var isOff = shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn);

				if (isOff == true) {
					output += system.powerReq; //power off => base power is available after all; ignore boosts, if any
				} else {
					for (var i in system.power) {
						var power = system.power[i];
						if (power.turn != gamedata.turn) continue;
						//types: 1:offline 2:boost, 3:overload
						if (power.type == 1) isOff = true; //should not happen as it was accounted for earlier
						if (power.type == 2) {
							var currBoost = shipManager.power.countBoostPowerUsed(ship, system);
							output -= currBoost;
						}
						if (power.type == 3) output -= system.powerReq;
					}
					/* WALKERS_OF_SIGMA_PLAN.md 3.18 (Stage 20) - an Extra-Dimensional Jump Drive abduction
					   costs the drive's powerReq again per power level. Inside the online branch, so an
					   offline drive (which cannot hold an order - canOffline refuses one) charges nothing.
					   Exactly 0 on every other system: only a jump engine defines it, and only one holding
					   an abduction order this turn answers anything but 0. */
					if (system.name === 'jumpEngine' && typeof system.getAbductionPowerDraw === 'function') {
						output -= system.getAbductionPowerDraw();
					}
					/* HYPERSPACE_IMPROVEMENTS_PLAN.md 4 (Stage H3) - THE VORLON JUMP DRIVE IS A
					   PER-USE COST, NOT A STANDING ONE (user ruling 2026-09-18): it draws power only
					   on a turn it is actually used to OPEN or MAINTAIN a jump point, and nothing at
					   all on every other turn.

					   ⚠️⚠️ WHICH TAKES TWO STEPS ON A VORLON, AND THE FIRST ONE IS EASY TO MISS.
					   MagGravReactorTechnical sets fixedPower, so the branch above has ALREADY
					   subtracted this drive's powerReq as a standing cost like every other system's -
					   and the drive is the only Vorlon system with a non-zero one. Give that back
					   first, then charge the USE. Without the give-back an idle Vorlon holding no
					   jump point at all is 5-8 power poorer every turn, which is what this code did
					   when H3 first landed.

					   ⚠️ GUARDED ON fixedPower, because the give-back is only undoing something. On a
					   hull with an ordinary reactor nothing was subtracted (a reactor's output already
					   nets its ship's base draw), so giving it back there would be a gift.

					   RESERVATION, NOT PAYMENT - the server spends it at end of turn
					   (JumpEngine::payVortexUpkeep) and PowerCapacitor.doIndividualNotesTransfer adds
					   it back into the stored figure so it is not charged twice.

					   Exactly 0 on every other system and every other jump engine: only JumpEngine
					   defines these, and only a marked drive with a jump-point order this turn
					   answers anything but 0. */
					if (system.name === 'jumpEngine' && typeof system.getVortexUpkeepDraw === 'function') {
						if (fixedPower == true && typeof system.chargesVortexUpkeep === 'function'
						    && system.chargesVortexUpkeep()) {
							output += system.powerReq;   //undo the standing draw - this drive has none
						}
						output -= system.getVortexUpkeepDraw();
					}
				}
			}
		}

		/* WALKERS_OF_SIGMA_PLAN.md 3.16 (Stage 18, D20) - THE SHIPS IN THIS HULL'S DOCKING BAY FEED IT.
		   LAST, after this hull's own balance is fully known, and additive: a grant from elsewhere is
		   not a system's draw and must not be folded into the loop above, which is indexed by this
		   ship's own systems and reads powerReq off each one. Exactly 0 on every other hull in the
		   game - see getDockedPowerSummary for what it costs to answer that. */
		output += shipManager.power.getDockedPowerShared(ship);

		return output;
	},

	/* WALKERS_OF_SIGMA_PLAN.md 3.16 (Stage 18, D20) - POWER SHARED BY THE SHIPS IN THIS HULL'S BAYS.
	   Every Docking Bay marked `sharesDockedPower` (the Traveler's aft bay, and nothing else in the
	   game) is asked for the SHIPS aboard it. Each one's reactor surplus is read with the same
	   getReactorPower every hull uses, the surpluses are summed, and the total is divided by 4 and
	   FLOORED: four points of docked surplus give the carrier one, three give it none.
	   Returns {donors, surplus, shared} because the Reactor tooltip has to show the pooled surplus
	   and the donor count as well as the grant - a player who has pooled 3 and been given 0 needs to
	   be able to see why (SystemInfo.js).

	   ⚠️ SHIPS ONLY, NEVER A FLIGHT (D20). Docked fighters are not in `shipsDocked` at all - they
	   ride hangarUsage - so the `flight` test below is belt and braces against a bay that ever
	   lists one.
	   ⚠️ THE DONOR IS NOT CHARGED. D20 is a transfer at a quarter rate, not a spend, and it has to
	   be: deducting the four points would drop the donor's surplus and the very next recompute would
	   take the grant away again, oscillating. What the donor really pays is the powering-down its
	   owner must do to have a surplus at all, which is exactly why 3.16(a) is the same stage.
	   ⚠️ A NEGATIVE surplus contributes NOTHING rather than taxing the carrier - clamped per ship,
	   BEFORE the sum, so one over-boosted docked hull cannot drain the Traveler.
	   ⚠️⚠️ THE FIGURE IS ADVISORY, and that is a recorded decision rather than an oversight: there
	   is no server twin of getReactorPower anywhere in the tree, the whole power balance lives in
	   this file, and the server trusts the power rows it is handed. See DockingBay::$sharesDockedPower
	   (baseSystems.php) and plan 3.16a.
	   ⭐ AN ENEMY VIEWER COMPUTES 0 HERE AND IS MEANT TO. DockingBay::stripForJson masks shipsDocked
	   to [] for anyone outside the owning team, so the grant is INVISIBLE to an opponent rather than
	   leaking what the Traveler is carrying - the safe direction for a number nothing enforces.
	   ⚠️ THE LOBBY HAS A DIFFERENT gamedata.getShip - `getShip(phpclass, faction)`, answering with a
	   BLUEPRINT - so this walk must never start there. Two things guarantee it does not: the
	   gamephase -2 early-out, and a bay bought in the lobby having an empty shipsDocked. */
	getDockedPowerSummary: function getDockedPowerSummary(carrier) {
		var none = { donors: 0, surplus: 0, shared: 0 };
		if (!carrier || carrier.flight || !carrier.systems) return none;
		if (gamedata.gamephase === -2) return none;          //the lobby - see the ⚠️ above
		/* Re-entrancy guard. getReactorPower(donor) calls straight back here for the DONOR's own
		   bays; a docked ship cannot itself hold docked ships today, so this can only ever break a
		   cycle that should not exist - but a stack overflow is not the way to find out that it does. */
		if (shipManager.power.dockedPowerWalk) return none;

		var donors = 0, surplus = 0;
		shipManager.power.dockedPowerWalk = true;
		try {
			for (var s = 0; s < carrier.systems.length; s++) {
				var bay = carrier.systems[s];
				if (!bay || !bay.sharesDockedPower || !bay.isDockingBay) continue;
				/* ⭐ EITHER LIST, and an OUTSIDE VIEWER gets the second one (plan 3.16b, user
				   ruling 2026-09-12). The owner and their team get the real shipsDocked; everyone
				   else gets sharesDockedPowerIds - bare ids, published by DockingBay::stripForJson
				   precisely so that this function, and not a second implementation on the server,
				   is what answers the question for both players. The docked ships' own rows and
				   power are in every viewer's payload already (the removed flag is published
				   unconditionally), so the ids are the only thing that was missing.
				   ⚠️ NEVER read boxes/phpclass/dockTurn off an entry here: the outside-viewer
				   form has none, which is exactly why it is a separate key. */
				var dockedIds = null;
				if (Array.isArray(bay.shipsDocked) && bay.shipsDocked.length > 0) {
					dockedIds = bay.shipsDocked.map(function (e) { return e.shipId; });
				} else if (Array.isArray(bay.sharesDockedPowerIds) && bay.sharesDockedPowerIds.length > 0) {
					dockedIds = bay.sharesDockedPowerIds;
				}
				if (!dockedIds) continue;
				//A destroyed bay has already put its ships back on the board
				//(HangarOps::onDockingBayDestroyed), so it taps nothing while the list drains.
				if (shipManager.systems.isDestroyed(carrier, bay)) continue;

				for (var i = 0; i < dockedIds.length; i++) {
					var docked = gamedata.getShip(dockedIds[i]);
					if (!docked || docked === carrier || docked.flight) continue;
					if (!docked.removed) continue;                             //launched again already
					if (shipManager.isDestroyedByDamage(docked)) continue;      //a wreck generates nothing
					if (shipManager.systems.isReactorDestroyed(docked)) continue;
					var reactor = shipManager.systems.getSystemByName(docked, "reactor");
					if (!reactor) continue;
					donors++;
					var own = shipManager.power.getReactorPower(docked, reactor);
					if (own > 0) surplus += own;
				}
			}
		} finally {
			shipManager.power.dockedPowerWalk = false;
		}

		return { donors: donors, surplus: surplus, shared: Math.floor(surplus / 4) };
	},

	//The grant alone - what getReactorPower adds to the carrier's balance. See the summary above.
	getDockedPowerShared: function getDockedPowerShared(carrier) {
		return shipManager.power.getDockedPowerSummary(carrier).shared;
	},

	//Transient re-entrancy latch for the walk above; never persisted, never serialised.
	dockedPowerWalk: false,

	/* WALKERS_OF_SIGMA_PLAN.md 3.16 (Stage 18) - "MAY THE PLAYER STILL MANAGE THIS UNIT'S POWER?",
	   and it is the ONE named predicate the power mutations below ask instead of isDestroyed.

	   A ship sitting in a Traveler's Docking Bay - and equally a rail-parked LCV or a docked
	   flight - is `removed`, and shipManager.isDestroyed folds `removed` in. So every mutation in
	   this file refused on one: onOfflineClicked, onOnlineClicked, onOverloadClicked and
	   onStopOverloadClicked each opened with `if (shipManager.isDestroyed(ship)) return;`, which is
	   why a docked ship's Power Settings menu drew its buttons and then did nothing at all when they
	   were clicked (user report 2026-09-12). Boost and unboost already worked - clickPlus/clickMinus
	   never had the guard - which is what made the menu look half-broken rather than switched off.

	   ⚠️⚠️ NOT A CHANGE TO shipManager.isDestroyed, which also stands in front of shouldBeHidden,
	   the fleet list, the icon, the movement sequence and every "is this unit on the board" test in
	   the client - widening it would put a docked ship back on the map (plan 3.16). This is the same
	   carve-out, on the same reasoning and with the same existing predicate, as
	   SystemIcon.clickSystem's `stowed` from Stage 17: isDestroyedByDamage is the codebase's name
	   for "the same question asked of the damage alone", so a unit that is removed but not a wreck is
	   parked out of sight and is still its owner's to manage.
	   ⚠️ POWER PATHS ONLY, and it changes NOTHING for a unit on the board: with `removed` false this
	   answers exactly what `!shipManager.isDestroyed(ship)` answered. Ownership is still checked
	   separately at every call site - this predicate is about the unit's state, not about whose it is. */
	isPowerManageable: function isPowerManageable(ship) {
		if (!ship) return false;
		return !shipManager.isDestroyedByDamage(ship);
	},

	isPowerless: function isPowerless(ship) {
		var reactor = shipManager.systems.getSystemByName(ship, "reactor");

		if (shipManager.systems.isReactorDestroyed(ship) || shipManager.criticals.hasCritical(reactor, "ForcedOfflineOneTurn")) return true;


		var power = shipManager.power.getReactorPower(ship, reactor);

		if (power >= 0) {
			return false;
		}

		/* if all power-using systems are offline and still power <0, then it's powerless and that's it!
		if (this.countPossiblePower(ship) + power > 0) {
			return false;			
		}
		*/

		/*check if all power-using systems are offline - if not, then it's not powerless*/
		for (var i in ship.systems) {
			var system = ship.systems[i];
			if (system.powerReq > 0) {
				//system is neither destroyed nor offline
				if ((!shipManager.systems.isDestroyed(ship, system)) && (!shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn))) {
					return false;
				}
			}
			//and no system except Reactor may be boosted, too
			if (system.name != 'reactor') {
				if (shipManager.power.getBoost(system) > 0) {
					return false;
				}
			}
		}

		return true;
	},

	countPossiblePower: function countPossiblePower(ship) {
		var power = 0;

		for (var i in ship.systems) {
			var system = ship.systems[i];

			if (!shipManager.systems.isDestroyed(ship, system)) power += system.powerReq;
		}
		//console.log(ship.name + " possible power: " + power);
		return power;

	},



	isOfflineOnTurn: function (ship, system, turn) {
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")) {
			return true;
		}
		if (shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineOneTurn", turn)) {
			return true;
		}

		//Treat an active cooldown / forced-shutdown crit as offline directly, not just
		//via the type:1 power entry. That entry is only created for OWN ships (in
		//applyForcedOfflineEntry / setPowerClasses), so without this an OPPONENT would
		//see an enemy's cooling weapon (SurgeBlaster/SurgeCannon/ResonanceGenerator/
		//burst-beam) as online during initial orders. These crits can't be cleared by
		//the owner (we block re-enable), so they're safe to render offline for everyone.
		//Mirrors the ForcedOfflineOneTurn handling above. (All callers pass gamedata.turn,
		//so this only ever affects the current turn — the firing turn is excluded because
		//the crit isn't present client-side until the turn after firing.)
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns")) {
			return true;
		}
		if (shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineForTurns", turn)) {
			return true;
		}

		/* Marcin Sawicki - I _think_ this condition may be skipped
		if ((system.powerReq > 0 || system.name == "reactor") && this.isPowerless(ship)){
			return true;
		}
		*/

		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != turn) continue;
			if (power.type == 1) return true;
		}

		return false;
	},


	isOffline: function (ship, system) {
		return shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn);
	},

	setOnline: function setOnline(ship, system, skipMessage = false) {
		//JumpEngine is a Weapon subclass only so it can declare a hex-targeted vortex
		//(JUMP_POINTS_PLAN.md section 3.1) - the Vorlon Power Capacitor rule is about weapons and
		//shields, and 11 Vorlon hulls carry a jump engine. Without this it could not be powered
		//back up while the capacitor is doubling generation.
		if (ship.faction === "Vorlon Empire" && !ship.flight && system.name !== "jumpEngine" && (system instanceof Weapon || system instanceof Shield)) {
			var capacitor = shipManager.systems.getSystemByName(ship, "powerCapacitor");
			if (capacitor && capacitor.active) {
				if (!skipMessage) window.confirm.warning("You cannot activate " + system.displayName + " while Power Capacitor is doubling power generation.");
				return;
			}
		}

		//JUMP_POINTS_PLAN.md Stage 5: a ship maintaining a jump point has to stay dark for the WHOLE
		//turn, so nothing it shut down may be powered back up while the declaration stands. Guarded
		//here rather than only in the button gates because setOnline is the choke point every route
		//funnels through - the Off/On toggle, onlineAll, and the forced-shutdown recovery above.
		//Same shape as the Vorlon Power Capacitor guard immediately above it.
		//(JumpEngine::doDeactivate removes the declaration BEFORE it restores anything, so turning
		//Maintain off is not blocked by its own lock.)
		if (shipManager.power.isVortexLockedOffline(ship, system)) {
			if (!skipMessage) window.confirm.warning("You cannot power up " + system.displayName
				+ " while the Jump Engine is maintaining a jump point. Turn Maintain off first.");
			return;
		}

		if (system.name == "graviticShield") {
			if (ship.checkShieldGenerator()) {
				for (var i in ship.systems) {
					var syst = ship.systems[i];

					if (syst.name == "shieldGenerator") {
						if (syst.destroyed) {
							if (!skipMessage) window.confirm.error("You cannot activate shields. Your shield generator has been destroyed.");
							return;
						}

						if (shipManager.power.isOffline(ship, syst)) {
							if (!skipMessage) window.confirm.error("You cannot activate shields. Power up your shield generator first.");
							return;
						}
					}
				}
			}
		}

		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;

			if (power.type == 1) {
				system.power.splice(i, 1);
				//				return;
			}
		}
	},

	getBoost: function getBoost(system) {
		var boost = 0;
		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;

			if (power.type == 2) {
				boost += power.amount;
			}
		}

		return boost;
	},

	getBoostOnTurn: function getBoostOnTurn(system, turn) {
		var boost = 0;
		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != turn) continue;

			if (power.type == 2) {
				boost += power.amount;
			}
		}

		return boost;
	},


	isBoosted: function (ship, system) {

		let turnToCheck = gamedata.turn;

		//In later Deployment phases Boost won't show until after phase 1, so we look at previous turn.
		if (gamedata.gamephase === -1 && gamedata.turn > 1) {
			if (shipManager.getTurnDeployed(ship) !== gamedata.turn) {
				turnToCheck = gamedata.turn - 1;
				return (shipManager.power.getBoostOnTurn(system, turnToCheck) > 0);
			}
		}

		return (shipManager.power.getBoost(system) !== 0); //is boosted if boost > 0
	},

	countTotalEffectiveEW: function countTotalEffectiveEW(ship) {
		var scanner = [];

		for (var i = 0; i < ship.systems.length; i++) {
			var sys = ship.systems[i];

			if (sys.isScanner()) {
				var online = true;
				for (var j in sys.power) {
					var power = sys.power[j];

					if (power.turn != gamedata.turn) {
						continue;
					}

					if (power.type == 1) {
						online = false;
					}
				}

				if (online) {
					scanner.push(sys);
				} else {
					continue;
				}
			}
		}

		scanner.sort(function (a, b) {
			if (a.id > b.id) {
				return 1;
			} else return -1;
		});

		var prim = scanner[0];

		var output = scanner.length * prim.output;
		var boost = 0;

		for (var i = 0; i < prim.power.length; i++) {
			var power = prim.power[i];

			if (power.turn != gamedata.turn) {
				continue;
			}

			if (power.type == 2) {
				boost += power.amount;
			}
		}

		var effective = output + boost;
		return effective;
	},

	countBoostReqPower: function countBoostReqPower(ship, system) {
		if (system.boostEfficiency.toString().search(/^[0-9]+$/) == 0) {
			return system.boostEfficiency;
		} else if (system.boostEfficiency == "output+1") {
			if (ship.base) {
				var ew = shipManager.power.countTotalEffectiveEW(ship);
				return ew + 1;
			} else {
				return system.output + shipManager.power.getBoost(system) + 1;
			}
		}
	},

	countBoostPowerUsed: function countBoostPowerUsed(ship, system) {
		var boost = shipManager.power.getBoost(system); 2;

		if (boost == 0 || shipManager.systems.isDestroyed(ship, system)) return 0;

		/* HYPERSPACE_IMPROVEMENTS_PLAN.md 5 (Stage H4) - a jump engine prices its own boost: powerReq a
		   level while it is EXTRA CHARGING, and 0 as a Jump to Hyperspace (boostEfficiency is 0 on every
		   jump engine and the jump must stay free). Priced HERE, as a boost, because this is also what
		   PowerCapacitor.initializationUpdate adds back after the commit - a charge cost counted anywhere
		   else was subtracted a second time from a stored figure that had already paid it (game 4348). */
		if (typeof system.getChargeBoostPowerDraw === 'function') return system.getChargeBoostPowerDraw();

		if (system.boostEfficiency.toString().search(/^[0-9]+$/) == 0) {
			return system.boostEfficiency * boost;
		} else if (system.boostEfficiency == "output+1") {
			var power = 0;

			/*02.12.2024, Marcin Sawicki: bases have the same boost cost for sensors as everything else! no idea why there was exception for them...
			if (ship.base) {
				var ew = shipManager.power.countTotalEffectiveEW(ship);

				for (var i = 1; i <= boost; i++) {
					power += ew;
					ew--;
				}
			} else */{
				for (var i = 1; i <= boost; i++) {
					power += system.output + i;
				}
			}

			return power;
		}
		return 0;
	},

	canBoost: function canBoost(ship, system) {

		if (shipManager.power.isOffline(ship, system)) return false;
		return true;
		/* no longer needed, I'm leaving the code in case in the future ideas change again
		//can always boost reactor (to overload)!
		if (system.name == 'reactor') {
			return shipManager.power.getBoost(system) === 0;
		}

		var has = shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor"));
		var need = shipManager.power.countBoostReqPower(ship, system);

		if (has >= need) {
			return true;
		} else {
			return false;
		}
		*/
	},

	canDeboost: function canDeboost(ship, system) {
		if (shipManager.power.isOffline(ship, system)) return false;
		return true;
	},

	canOverload: function canOverload(ship, system) {
		if (!system.overloadable) return false;
		return shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor")) >= system.powerReq;
	},

	unsetBoost: function unsetBoost(ship, system) {

		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;

			if (power.type == 2) {
				power.amount--;

				if (power.amount == 0) system.power.splice(i, 1);

				return;
			}
		}

	},


	checkBoostValid: function checkBoostValid(ship, system) {
		var validBoost = true;

		if (ship.faction === "Pak'ma'ra Confederacy" && system instanceof Scanner) {
			var batteryPowerAvailable = 0;
			var scannerStrength = shipManager.systems.getOutput(ship, system);
			var reactorSurplus = shipManager.power.getReactorPower(ship, system);

			// Calculate total Plasma Battery power available
			ship.systems.forEach(currBattery => {
				if (currBattery.name === "PlasmaBattery" && !shipManager.systems.isDestroyed(ship, currBattery)) {
					batteryPowerAvailable += shipManager.systems.getOutput(ship, currBattery);
				}
			});

			var effectiveReactorPower = reactorSurplus - batteryPowerAvailable;
			var powerNeeded = scannerStrength + 1;

			// Validate boost power
			if (batteryPowerAvailable > 0 && effectiveReactorPower < powerNeeded) {
				confirm.error("Power from Plasma Batteries cannot be used to boost sensors.");
				validBoost = false;
			}
		}

		return validBoost;
	},


	setBoost: function setBoost(ship, system) {
		if (!shipManager.power.canBoost(ship, system)) {
			window.confirm.error("You do not have sufficient energy to boost this system.", function () { });
			return;
		}

		for (var i in system.power) {
			var power = system.power[i];

			if (power.turn != gamedata.turn) continue;

			if (power.type == 2) {
				power.amount++;
				return;
			}
		}

		system.power.push({ id: null, shipid: ship.id, systemid: system.id, type: 2, turn: gamedata.turn, amount: 1 });
	},

	setOverloading: function setOverloading(ship, system) {

		if (shipManager.power.isOverloading(ship, system)) return;
		/* power is now checked elsewhere - do overload even if power for it is not available
		if (!shipManager.power.canOverload(ship, system)) return;
		*/
		system.power.push({ id: null, shipid: ship.id, systemid: system.id, type: 3, turn: gamedata.turn, amount: 0 });
	},

	stopOverloading: function stopOverloading(ship, system) {
		if (system.alwaysoverloading) {
			return;
		}

		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;

			if (power.type == 3) {
				system.power.splice(i, 1);
				return;
			}
		}
	},

	/* //Old version - DK Nov 2025
	isOverloading: function isOverloading(ship, system) {
		if (system.alwaysoverloading) {
			return true;
		}

		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;

			if (power.type == 3) {
				return true;
			}
		}

		return false;
	},
	*/

	isOverloading: function isOverloading(ship, system) {

		if (shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn)) return false;

		if (system.alwaysoverloading) {
			return true;
		}

		for (var i in system.power) {
			var power = system.power[i];
			if (power.turn != gamedata.turn) continue;

			if (power.type == 3) {
				return true;
			}
		}

		return false;
	},

	clickPlus: function clickPlus(ship, system) {

		//if (gamedata.gamephase !== 1) return;

		let isBoostPhase = false;
		/*
		// Check if boostOtherPhases is defined as an array
		if (system.boostOtherPhases.length > 0) {
			isBoostPhase = system.boostOtherPhases.includes(gamedata.gamephase);

		// Fallback: default boost phase (1)
		} else if (gamedata.gamephase === 1) {
			isBoostPhase = true;
		}*/

		if (gamedata.gamephase === 1) {
			isBoostPhase = true;
		}
		// Stop here if not a boostable phase
		if (!isBoostPhase) return;

		//if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
		/*no longer needed - EW allocation is checked before commit, so You can't attain illegal effects by boosting/deboosting with EW set
		if (system.isScanner() && ew.getUsedEW(ship) > 0) {
			confirm.error("You need to unassign all electronic warfare before changing scanner power management.");
			return;
		}
		*/

		if (system.hasMaxBoost()) {
			if (system.maxBoostLevel <= shipManager.power.getBoost(system)) {
				confirm.error("You can not boost this system any further.");
				return;
			}
		}

		//New function to check for things like Pak'ma'ra power boosting exceptions - DK 10/24
		if (!shipManager.power.checkBoostValid(ship, system)) return;

		shipManager.power.setBoost(ship, system);
		system.onBoostIncrease(); //To apply conditions/effects when a system is actually boosted.		
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	clickMinus: function clickMinus(ship, system) {

		//if (gamedata.gamephase !== 1) return;
		let isBoostPhase = false;
		/*
		// Check if boostOtherPhases is defined as an array
		if (system.boostOtherPhases.length > 0) {
			isBoostPhase = system.boostOtherPhases.includes(gamedata.gamephase);

		// Fallback: default boost phase (1)
		} else if (gamedata.gamephase === 1) {
			isBoostPhase = true;
		}
		*/

		if (gamedata.gamephase === 1) {
			isBoostPhase = true;
		}
		// Stop here if not a boostable phase
		if (!isBoostPhase) return;

		//if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){

		/*no longer needed - EW allocation is checked before commit, so You can't attain illegal effects by boosting/deboosting with EW set
		if (system.isScanner() && ew.getUsedEW(ship) > 0) {
			confirm.error("You need to unassign all electronic warfare before changing scanner power management.");
			return;
		}
		*/
		shipManager.power.unsetBoost(ship, system);
		system.onBoostDecrease();
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	offlineAll: function offlineAll(ship, system) {
		var array = [];

		for (var i = 0; i < ship.systems.length; i++) {
			if (system.name === ship.systems[i].name) {
				//if (system.weapon) { //make this work for non-weapons too
				array.push(ship.systems[i]);
				//}
			}
		}

		for (var i = 0; i < array.length; i++) {
			if (gamedata.gamephase != 1) continue;

			if (shipManager.systems.isDestroyed(ship, array[i])) continue;

			if (ship.userid != gamedata.thisplayer) continue;

			if (shipManager.power.isOffline(ship, array[i])) continue;

			array[i].power.push({ id: null, shipid: ship.id, systemid: array[i].id, type: 1, turn: gamedata.turn, amount: 0 });

			//Stage 20 - same rule as onOfflineClicked: a deactivated jump drive's abduction is cancelled.
			if (typeof array[i].getAbductionOrder === 'function' && array[i].getAbductionOrder()) array[i].removeAbductionOrder();

			shipManager.power.stopOverloading(ship, array[i]);
		}

		webglScene.customEvent('SystemDataChanged', { ship: ship });
	},

	onOfflineClicked: function onOfflineClicked(ship, system) {
		/*
		if (system.duoWeapon) {
			// create an iconMask at the top of the DOM for the system.
			var iconmask_element = document.createElement('div');
			iconmask_element.className = "iconmask";
			systemwindow.find(".icon").append(iconmask_element);
		}
		*/

		if (system.parentId > 0) {
			system = shipManager.systems.getSystem(ship, system.parentId);
		}

		if (system.active) return; //Prevent powering off systems that were activated in Pre-Turn orders e.g. Shading Field

		if (gamedata.gamephase != 1) return;

		if (!shipManager.power.isPowerManageable(ship) || shipManager.systems.isDestroyed(ship, system)) return;

		if (!gamedata.isMyShip(ship)) return;

		if (shipManager.power.isOffline(ship, system)) return;

		system.power.push({ id: null, shipid: ship.id, systemid: system.id, type: 1, turn: gamedata.turn, amount: 0 });

		//if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
		/*no longer needed
		if (system.isScanner() && ew.getUsedEW(ship) > 0) {
			confirm.error("You need to unassign all electronic warfare before changing scanner power management.");
			return;
		}*/

		if (system.name == "shieldGenerator" || system instanceof ThirdspaceShieldGenerator) {
			system.onTurnOff(ship);
		}

		if (system.overloadshots == 0) { //To prevent stop overload AFTER an initial sustained shot is fired.
			shipManager.power.stopOverloading(ship, system);
		}

		if (system.weapon) {
			weaponManager.unSelectWeapon(ship, system);
		}

		/* WALKERS_OF_SIGMA_PLAN.md 3.18 (Stage 20, user ruling 2026-09-13): deactivating a jump drive CANCELS
		   the abduction it was declaring, rather than leaving an order that delivers and costs nothing.
		   canOffline cannot refuse the click the way it refuses a gun with a shot declared -
		   weaponManager.hasFiringOrder does not count this order in Initial Orders (plan trap 58). */
		if (system instanceof JumpEngine && typeof system.getAbductionOrder === 'function' && system.getAbductionOrder()) {
			system.removeAbductionOrder();
		}

		//Add new warning for when people ignore tooltip and try to deactivate Jump Drive before they should - DK 10/24
		if (system instanceof JumpEngine) {
			var healthThreshold = system.maxhealth / 2;
			var currHealth = shipManager.systems.getRemainingHealth(system);
			var html = '';
			//Check Jump Drive is over 50% health, and Desperate Rules do not apply to player team or both teams		
			if (currHealth > healthThreshold) {
				// No warning for ships if desperate rules apply
				if (gamedata.rules.desperate === undefined ||
					(gamedata.rules.desperate !== ship.team && gamedata.rules.desperate !== -1)) {
					html += "WARNING - Jump Drive should only be deactivated after it’s taken 50% damage or more.";
					html += "<br>";
					confirm.warning(html);
				}
			}
		}

		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	onlineAll: function onlineAll(ship, system) {
		var array = [];

		for (var i = 0; i < ship.systems.length; i++) {
			if (system.name === ship.systems[i].name) {
				//if (system.weapon) { //make this work for non-weapons too
				array.push(ship.systems[i]);
				//}
			}
		}

		for (var i = 0; i < array.length; i++) {

			if (shipManager.systems.isDestroyed(ship, array[i])) {
				continue;
			} else if (shipManager.power.isForcedOffline(ship, array[i])) {
				continue; //cooldown crit — cannot be re-enabled by the player
			} else if (shipManager.power.isOffline(ship, array[i])) {
				shipManager.power.setOnline(ship, array[i]);
			}
		}

		webglScene.customEvent('SystemDataChanged', { ship: ship });
	},

	onOnlineClicked: function onOnlineClicked(ship, system) {

		if (system.parentId > 0) {
			system = shipManager.systems.getSystem(ship, system.parentId);
		}

		if (gamedata.gamephase != 1) return;

		if (!shipManager.power.isPowerManageable(ship) || shipManager.systems.isDestroyed(ship, system)) return;

		if (ship.userid != gamedata.thisplayer) return;

		if (!shipManager.power.isOffline(ship, system)) return;

		//A weapon/system forced offline by a cooldown crit cannot be powered back on by
		//the player (the server now also rejects this — Firing::fire / isOfflineOnTurn).
		if (shipManager.power.isForcedOffline(ship, system)) return;

		shipManager.power.setOnline(ship, system);

		if (system.name == "shieldGenerator" || system instanceof ThirdspaceShieldGenerator) {
			system.onTurnOn(ship);
		}

		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	onOverloadClicked: function onOverloadClicked(ship, system) {
		if (gamedata.gamephase != 1) return;

		if (!shipManager.power.isPowerManageable(ship) || shipManager.systems.isDestroyed(ship, system)) return;

		if (ship.userid != gamedata.thisplayer) return;

		if (shipManager.power.isOffline(ship, system)) return;

		console.log("I am boosting!")

		shipManager.power.setOverloading(ship, system);
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	onStopOverloadClicked: function onStopOverloadClicked(ship, system) {

		if (gamedata.gamephase != 1) return;

		//⚠️ The second clause has always been isDestroyed(ship) a second time - that function takes ONE
		//argument - so it never asked anything about the system. Left exactly as it was in effect
		//(deliberately NOT quietly turned into a real system test, which would be a rules change on
		//every hull in the game); only the ship-level half is relaxed for a stowed unit. See
		//isPowerManageable, and the same observation on SystemIcon.clickSystem's guard in Stage 17.
		if (!shipManager.power.isPowerManageable(ship) /*|| shipManager.isAdrift(ship)*/) return; //should work with disabled ship after all!

		if (ship.userid != gamedata.thisplayer) return;

		if (shipManager.power.isOffline(ship, system)) return;

		if (system.overloadshots < system.extraoverloadshots && system.overloadshots !== 0) return; //To prevent stop overload AFTER an initial sustained shot is fired.

		shipManager.power.stopOverloading(ship, system);
		webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

};
