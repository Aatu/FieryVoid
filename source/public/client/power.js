"use strict";

shipManager.power = {

	repeatLastTurnPower: function repeatLastTurnPower() {

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];

			if (ship.userid != gamedata.thisplayer) continue;

			if(ship.flight){
				for (var i in ship.systems) {
					var fighter = ship.systems[i]; //The fighter		
					for (var j in fighter.systems) {
						shipManager.power.copyLastTurnPower(ship, fighter.systems[j]);					
					}
				}		
			}else{
				for (var a in ship.systems) {
					shipManager.power.copyLastTurnPower(ship, ship.systems[a]);
				}
			}	
		}
	},

	copyLastTurnPower: function copyLastTurnPower(ship, system) {
		if (shipManager.systems.isDestroyed(ship, system)) return;

		//if system WAS forcibly shut down last turn but is NOT forced to shut down any longer - it should get back online without player input!
		var wasShutDown = false;
		var isShutDown = false;
		if ( (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")) || (shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns")) )
		{
			isShutDown = true;
		}else{
			if ( (shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineOneTurn", gamedata.turn - 1)) 
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
				powers.push(newPower);
			}
		}		

		system.power = system.power.concat(powers);
		
		if (wasShutDown && (!isShutDown)){ //was forced to shut down, but is no longer - power up!
			shipManager.power.setOnline(ship, system, true); //do skip message to player - if system cannot be powered up, it won't be powered up and that's it
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

			// Because of the crit, add a power entry to the power array
			// of this system.
			// A bit of code is necessary to make sure this only happens once.
			var isOnline = true;

			//for Jammer, copy last turn's power - it's important for opponent!
			//induces trouble AND does not work as intended
			//if (system.name == "jammer"){
			//	copyLastTurnPower(ship, system);
			//}

			for (var i in system.power) {
				var power = system.power[i];
				if (power.turn != gamedata.turn) continue;

				if (power.type == 1) {
					isOnline = false;
					break;
				}
			}

			if (system.name == "engine") {
				system.power = [];
			}

			if (isOnline) {
				system.power.push({ id: null, shipid: ship.id, systemid: system.id, type: 1, turn: gamedata.turn, amount: 0 });
				shipManager.power.stopOverloading(ship, system);
			}

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
				if ( (ship.base) //02.12.2024, Marcin Sawicki - bases can boost any sonsors, not just strongest
				     || (system.id == shipManager.power.getHighestSensorsId(ship))  
				   ){
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

	getShipsGraviticShield: function getShipsGraviticShield() {
		var shipNames = new Array();
		var counter = 0;

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];
			if(gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
			if (ship.unavailable) continue;

			if (ship.flight) continue;

			if (ship.userid != gamedata.thisplayer) continue;

			if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;

            var deployTurn = shipManager.getTurnDeployed(ship);
            if(deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.

			if (!ship.checkShieldGenerator()) {
				shipNames[counter] = ship.name;
				counter++;
			}
		}

		return shipNames;
	},

	getShipsNegativePower: function getShipsNegativePower() {
		var shipNames = new Array();
		var counter = 0;

		for (var i in gamedata.ships) {
			var ship = gamedata.ships[i];

			if (ship.unavailable) continue;

			if (ship.flight) continue;
			if(gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;

			if (ship.userid != gamedata.thisplayer) continue;

			if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;

            var deployTurn = shipManager.getTurnDeployed(ship);
            if(deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.

			if (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor")) < 0) {
				shipNames[counter] = ship.name;
				counter++;
			}
		}

		return shipNames;
	},

	//like getShipsNegativePower BUT only looks for PowerCapacitor-equipped ships
	getCapacitorShipsNegativePower: function getCapacitorShipsNegativePower() {
			var shipNames = new Array();
			var counter = 0;
			for (var i in gamedata.ships) {
				var ship = gamedata.ships[i];
				if(gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;							
				if (ship.unavailable) continue;
				if (ship.flight) continue;
				if (ship.userid != gamedata.thisplayer) continue;
				var deployTurn = shipManager.getTurnDeployed(ship);
				if(deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.							
				if (!(shipManager.systems.getSystemByName(ship, "powerCapacitor"))) continue;
				if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;
				if (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor")) < 0) {
					shipNames[counter] = ship.name;
					counter++;
				}
			}
			return shipNames;
		},	//endof getCapacitorShipsNegativePower
		

	//like getShipsNegativePower BUT only looks for PlasmaBattery-equipped ships
	getPlasmaBatteryShipsNegativePower: function getPlasmaBatteryShipsNegativePower() {
			var batteryShips = new Array();
			var counter = 0;
			for (var i in gamedata.ships) {
				var ship = gamedata.ships[i];
					if(ship.faction !== "Pak'ma'ra Confederacy") continue;
		            if (ship.unavailable) continue;
		            if (ship.flight) continue;
		            if (ship.userid != gamedata.thisplayer) continue;
					var deployTurn = shipManager.getTurnDeployed(ship);
					if(deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.					
		            if (!(shipManager.systems.getSystemByName(ship, "PlasmaBattery"))) continue;
		            if (shipManager.isDestroyed(ship)) continue;

				var batteryPowerAvailable = 0;
				//Calculate battery power available (find all batteries that are not destroyed, sum up their contents)
	                for (var i = 0; i < ship.systems.length; i++) {
	                	var currBattery = ship.systems[i];
	              	    if (currBattery.name == "PlasmaBattery" && !(shipManager.systems.isDestroyed(ship, currBattery))){ //only Plasma Batteries which are not destroyed are of interest 
						batteryPowerAvailable += shipManager.systems.getOutput(ship, currBattery);                                                              
					}
				}	
	                          
				var batteryPowerRequired = 0;
				//Calculate battery power required (find all Plasma Webs that are firing offensively without being boosted)			
				for (var i = 0; i < ship.systems.length; i++) {
	                var currWeb = ship.systems[i];
	 				if(currWeb.name == "PakmaraPlasmaWeb"){ //only Plasma Webs  are of interest 
	 					for (var k = 0; k < currWeb.fireOrders.length; k++) {
	                    var currFireOrder = currWeb.fireOrders[k];
	                        if ((currFireOrder.firingMode == "2") && (shipManager.power.getBoost(currWeb) == 0)) {
		                        batteryPowerRequired += 1;
		                    }
						}       	
					}	
				}
	            
	            if (batteryPowerAvailable < batteryPowerRequired) {
	                batteryShips[counter] = ship.name;
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
			fixedPower = reactor.fixedPower;
		}

		for (var s in ship.systems) {
			var system = ship.systems[s];
			
			/* Dual/Duo weapons no longer present
			if (system.parentId > 0) {
				// This is a subsystem of a dual/duo weapon. Ignore
				continue;
			}
			*/
			
			/*temporary power down critical - may happen on C&C*/	
			if(system.displayName=="C&C"){ //no point checking other systems
               			output -= shipManager.criticals.hasCritical(system, "tmppowerdown"); //Power output reduced
			}
                        
			/*standard: add power for every system powered off
			  fixed: subtract power for every system powered on (instead!)
			*/
			if ( (!system.destroyed)   ){ //destroyed system gets no power either way
				if (fixedPower==true){ //for Mag-Grav reactor: all systems draw power, unless off or destroyed (accounted for in a moment)
					output -= system.powerReq;
				}					
				var isOff = shipManager.power.isOfflineOnTurn(ship,system,gamedata.turn) ;

				if (isOff == true){
					output += system.powerReq; //power off => base power is available after all; ignore boosts, if any
				} else {
					for (var i in system.power){
						var power = system.power[i];
						if (power.turn != gamedata.turn) continue;
						//types: 1:offline 2:boost, 3:overload
						if (power.type == 1) isOff = true; //should not happen as it was accounted for earlier
						if (power.type == 2){
							var currBoost = shipManager.power.countBoostPowerUsed(ship, system);
							output -= currBoost;
						}
						if (power.type == 3) output -= system.powerReq;
					}
				}				
			}
		}

		return output;
	},

	isPowerless: function isPowerless(ship) {
		var reactor = shipManager.systems.getSystemByName(ship, "reactor");

		if (shipManager.systems.isReactorDestroyed(ship) || shipManager.criticals.hasCritical(reactor, "ForcedOfflineOneTurn")) return true;

		
		var power = shipManager.power.getReactorPower(ship, reactor);

		if (power >= 0){
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
				if ( (!shipManager.systems.isDestroyed(ship, system)) && (!shipManager.power.isOfflineOnTurn(ship,system,gamedata.turn)) )	{
					return false;
				}
			}
			//and no system except Reactor may be boosted, too
			if (system.name != 'reactor'){
				if (shipManager.power.getBoost(system) > 0){
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
	

	
	isOfflineOnTurn: function(ship, system, turn){		
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")){
			return true;
		}
		if (shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineOneTurn",turn)){
			return true;
		}

		/* Marcin Sawicki - I _think_ this condition may be skipped
		if ((system.powerReq > 0 || system.name == "reactor") && this.isPowerless(ship)){ 
			return true;
		}
		*/

		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != turn) continue;
			if (power.type == 1) return true;
		}

		return false;
	},


	isOffline: function(ship, system){
		return shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn);
	},

	setOnline: function setOnline(ship, system, skipMessage = false) {
		if (ship.faction === "Vorlon Empire" && !ship.flight && (system instanceof Weapon || system instanceof Shield)) {
             var capacitor = shipManager.systems.getSystemByName(ship, "powerCapacitor");
             if (capacitor && capacitor.active) {
                 if (!skipMessage) window.confirm.warning("You cannot activate " + system.displayName + " while Power Capacitor is doubling power generation.");
                 return;
             }
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

	
	isBoosted: function(ship, system){

		let turnToCheck = gamedata.turn;

		//In later Deployment phases Boost won't show until after phase 1, so we look at previous turn.
		if (gamedata.gamephase === -1 && gamedata.turn > 1) {
			if(shipManager.getTurnDeployed(ship) !== gamedata.turn){
				turnToCheck = gamedata.turn - 1;
				return (shipManager.power.getBoostOnTurn(system, turnToCheck) > 0);
			}					
		}	
		
		return (shipManager.power.getBoost(system)!==0); //is boosted if boost > 0
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
		var boost = shipManager.power.getBoost(system);2;

		if (boost == 0 || shipManager.systems.isDestroyed(ship, system)) return 0;

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

		if(shipManager.power.isOffline(ship, system)) return false;
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
		if(shipManager.power.isOffline(ship, system)) return false;
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
			window.confirm.error("You do not have sufficient energy to boost this system.", function () {});
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

		if(shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn)) return false;

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
		if(!shipManager.power.checkBoostValid(ship, system)) return;	
				
		shipManager.power.setBoost(ship, system);
		system.onBoostIncrease(); //To apply conditions/effects when a system is actually boosted.		
		shipWindowManager.setDataForSystem(ship, system);
		if(!ship.flight)shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
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
		shipWindowManager.setDataForSystem(ship, system);
		if(!ship.flight)shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	offlineAll: function offlineAll(ship, system) {
		var array = [];
        /* Cleaned 19.8.25 - DK
		if (system.duoWeapon || system.dualWeapon) {
			return;
		}
		*/

		for (var i = 0; i < ship.systems.length; i++) {
			if (system.displayName === ship.systems[i].displayName) {
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

			shipManager.power.stopOverloading(ship, array[i]);
			shipWindowManager.setDataForSystem(ship, array[i]);
			shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
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

		if(system.active) return; //Prevent powering off systems that were activated in Pre-Turn orders e.g. Shading Field

		if (gamedata.gamephase != 1) return;

		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system)) return;

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

		if(system.overloadshots == 0) { //To prevent stop overload AFTER an initial sustained shot is fired.
			shipManager.power.stopOverloading(ship, system); 
		}
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));

		if (system.weapon) {
			weaponManager.unSelectWeapon(ship, system);
		}

		//Add new warning for when people ignore tooltip and try to deactivate Jump Drive before they should - DK 10/24
        if (system instanceof JumpEngine) {
			var healthThreshold = system.maxhealth / 2;
			var currHealth = shipManager.systems.getRemainingHealth(system);
            var html = '';
			//Check Jump Drive is over 50% health, and Desperate Rules do not apply to player team or both teams		
			if(currHealth > healthThreshold){
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

        /* Cleaned 19.8.25 - DK
		if (system.duoWeapon || system.dualWeapon) {
			return;
		}
		*/	

		for (var i = 0; i < ship.systems.length; i++) {
			if (system.displayName === ship.systems[i].displayName) {
				//if (system.weapon) { //make this work for non-weapons too
					array.push(ship.systems[i]);
				//}
			}
		}

		for (var i = 0; i < array.length; i++) {

			if (shipManager.systems.isDestroyed(ship, array[i])) {
				continue;
			} else if (shipManager.power.isOffline(ship, array[i])) {
				shipManager.power.setOnline(ship, array[i]);
				shipWindowManager.setDataForSystem(ship, array[i]);
				shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
			}
		}
		
        webglScene.customEvent('SystemDataChanged', { ship: ship });
	},

	onOnlineClicked: function onOnlineClicked(ship, system) {

		if (system.parentId > 0) {
			system = shipManager.systems.getSystem(ship, system.parentId);
		}

		if (gamedata.gamephase != 1) return;

		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system)) return;

		if (ship.userid != gamedata.thisplayer) return;

		if (!shipManager.power.isOffline(ship, system)) return;

		shipManager.power.setOnline(ship, system);
		shipWindowManager.setDataForSystem(ship, system);

		if (system.name == "shieldGenerator" || system instanceof ThirdspaceShieldGenerator) {
			system.onTurnOn(ship);
		}

		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	onOverloadClicked: function onOverloadClicked(ship, system) {
		if (gamedata.gamephase != 1) return;

		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system)) return;

		if (ship.userid != gamedata.thisplayer) return;

		if (shipManager.power.isOffline(ship, system)) return;

		console.log("I am boosting!")

		shipManager.power.setOverloading(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	},

	onStopOverloadClicked: function onStopOverloadClicked(ship, system) {

		if (gamedata.gamephase != 1) return;

		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system) /*|| shipManager.isAdrift(ship)*/) return; //should work with disabled ship after all!

		if (ship.userid != gamedata.thisplayer) return;

		if (shipManager.power.isOffline(ship, system)) return;

		if(system.overloadshots < system.extraoverloadshots && system.overloadshots !== 0) return; //To prevent stop overload AFTER an initial sustained shot is fired.

		shipManager.power.stopOverloading(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
        webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
	}

};
