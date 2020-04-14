"use strict";

window.ew = {
    getScannerOutput: function getScannerOutput(ship) {
        var ret = 0;

        if (shipManager.isAdrift(ship)) return 0;

        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (system.outputType === "EW") {
                var output = shipManager.systems.getOutput(ship, system);

                if (output > 0) {
                    ret += output;
                }
            }
        }

        /*if (ship.base)*/{
            var primary = shipManager.getPrimaryCnC(ship);
            if (primary && shipManager.criticals.hasCritical(primary, "RestrictedEW")) {
                ret -= 2;
            }
            if (primary) {
                ret -= shipManager.criticals.hasCritical(primary, "tmpsensordown"); //Sensors reduced
            }
        } /* always go for primary C&C...	
          else if (! ship.flight){
             var cnc = shipManager.systems.getSystemByName(ship, "cnC");
             if (cnc && shipManager.criticals.hasCritical(cnc, "RestrictedEW")){
                 ret -= 2;
             }
          }*/

        return ret > 0 ? ret : 0;
    },

    isTargetDistByOtherElint: function isTargetDistByOtherElint(elint, target) {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            //if(ship.faction == elint.faction && ship.id != elint.id){
            if (ship.userid == elint.userid && ship.id != elint.id) {
                for (var i in ship.EW) {
                    var entry = ship.EW[i];

                    if (entry.turn != gamedata.turn) continue;

                    if (entry.type == "DIST") {
                        return true;
                    }
                }
            }
        }

        return false;
    },

    getUsedEW: function getUsedEW(ship) {
        var used = 0;

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            used += EWentry.amount;
        }

        return used;
    },

    getDefensiveEW: function getDefensiveEW(ship) {
		return ew.getEWLeft(ship);//turns out to be an alias now, effectively
		/* defensive == everything not allocated for other functions!
        var listed = ew.getListedDEW(ship);

        if (listed === null) {
			
            return ew.getScannerOutput(ship) - ew.getUsedEW(ship);
			
        }

        return listed;*/
    },

    getTargetingEW: function getTargetingEW(ship, target) {
        var amountOEW = 0;
        if (target.flight) {            
			//check range - CCEW works up to 10 hexes!
			var distance = mathlib.getDistanceBetweenShipsInHex(ship, target);
			if (distance <= 10){
				amountOEW += ew.getCCEW(ship);
			}
            //return ew.getCCEW(ship);
        } /*else {
            return ew.getOffensiveEW(ship, target);
        }*/
        amountOEW += ew.getOffensiveEW(ship, target);
        return amountOEW;
    },

    getOffensiveEW: function getOffensiveEW(ship, target, type) {
        type = type || "OEW";
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn !== gamedata.turn) continue;
            if (entry.type === type && entry.targetid === target.id) return entry.amount;
        }
        return 0;
    },

    getAllOffensiveEW: function getAllOffensiveEW(ship) {
        var amount = 0;
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (entry.type == "OEW") amount += entry.amount;
        }
        return amount;
    },

    getNumOfOffensiveTargets: function getNumOfOffensiveTargets(ship) {
        var amount = 0;
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if ((entry.type == "OEW") || (entry.type == "CCEW" && entry.amount > 0)) amount++;
        }
        return amount;
    },

    getEWByType: function getEWByType(type, ship, target) {
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (target && entry.targetid != target.id) continue;
            if (entry.type == type) {
                return entry.amount;
            }
        }
        return 0;
    },

    convertUnusedToDEW: function convertUnusedToDEW(ship) {
        //var dew = ew.getScannerOutput(ship) - ew.getUsedEW(ship);
		var dew = ew.getEWLeft(ship);
        if (dew < 0) { 
			//return flag that something is wrong with EW
			return false;
		/*//DEW should NOT be negative - reset EW in this case! (most probably Sensors disabled after setting EW)
            this.removeEW(ship);
            dew = ew.getScannerOutput(ship) - ew.getUsedEW(ship);
			*/
        }
		/*game does not react well to more than one DEW entry, hence condition*/
		for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn !== gamedata.turn) continue;
            if (EWentry.type === 'DEW') {
				EWentry.amount = dew;
				return true; //found, changed, nothing more to do
			}
        }
		//else: not found: create DEW entry!
        ship.EW.push({ shipid: ship.id, type: "DEW", amount: dew, targetid: -1, turn: gamedata.turn });
		return true;
    },

    /*Ship with LCVSensors trait must have all but 2 EW points set to OEW
    	returns false if this is not met
    */
    checkLCVSensors: function checkLCVSensors(ship) {
		var toReturn = true;
		if(shipManager.hasSpecialAbility(ship, "LCVSensors")){ //otherwise no check
			var totalEW = ew.getScannerOutput(ship);
			if (totalEW > 2){
				var offensiveEW = ew.getAllOffensiveEW(ship);
				if ( totalEW > (offensiveEW+2) ){
					toReturn = false;
				}
			}
		}
		return toReturn;
    },
	
    /*checks whether RestrictedEW crit is conformed to (can get around setting lock by clever boosting and deboosting)
	obviously LCV Sensors cannot ever get this critical! relying oc C&C being unhittable in this case
    */
    checkRestrictedEW: function checkRestrictedEW(ship) {
		var toReturn = true;
        if ((!ship.flight) && (!ship.osat)) {
            if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(ship, "cnC"), "RestrictedEW")) {
                var def = ew.getDefensiveEW(ship);
                var all = ew.getScannerOutput(ship);

                if (def < all * 0.5) toReturn = false;
            }
        }
		return toReturn;
    },
	
    getListedDEW: function getListedDEW(ship) {
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;

            if (entry.type == "DEW") return entry.amount;
        }
        return null;
    },
	
	
	/*returns real amount of EW points free to allocate - by free to allocate DEW or unallocated are understood*/
	getEWLeft: function getEWLeft(ship) {
		var usedEW = 0;
		for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (entry.type != "DEW"){
				usedEW += entry.amount;
			}
        }
		var totalAvailable = ew.getScannerOutput(ship);
		var leftEW = totalAvailable-usedEW;
		return leftEW;
	},

    getBDEW: function getBDEW(ship) {
        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "BDEW") {
                return EWentry.amount;
            }
        }

        return 0;
    },

    getBDEWentry: function getBDEWentry(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "BDEW") {
                return EWentry;
            }
        }

        return null;
    },

    getCCEW: function getCCEW(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "CCEW") {
                return EWentry.amount;
            }
        }

        return 0;
    },

    getCCEWentry: function getCCEWentry(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "CCEW") {
                return EWentry;
            }
        }

        return null;
    },

    getEntryByTargetAndType: function getEntryByTargetAndType(ship, target, type, turn) {
        return ship.EW.filter(function (entry) {
            return entry.shipid === ship.id && (target === null || entry.targetid === target.id) && entry.type === type && entry.turn === turn;
        }).pop();
    },
	

    AssignOEW: function AssignOEW(selected, ship, type) {
        if (!type) type = "OEW";

        for (var i in selected.EW) {
            var EWentry = selected.EW[i];

            if (EWentry.turn !== gamedata.turn) continue;

            if (EWentry.type === type && EWentry.targetid === ship.id) return;
        }
		//var left = ew.getDefensiveEW(selected);
		var left = ew.getEWLeft(selected);

        if (left < 1 || type === "DIST" && left < 3) {
            return;
        }

        if (!selected.osat) {
            if (ship.base) {
                var primary = shipManager.getPrimaryCnC(ship);
                if (shipManager.criticals.hasCritical(primary, "RestrictedEW")) {
                    var def = ew.getDefensiveEW(selected);
                    var all = ew.getScannerOutput(selected);

                    if (def - 1 < all * 0.5) {
                        return false;
                    }
                }
            } else if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(selected, "cnC"), "RestrictedEW")) {
                var def = ew.getDefensiveEW(selected);
                var all = ew.getScannerOutput(selected);

                if (def - 1 < all * 0.5) {
                    return false;
                }
            }
        }

        var amount = 1;
        if (type == "DIST") amount = 3;

        selected.EW.push({ shipid: selected.id, type: type, amount: amount, targetid: ship.id, turn: gamedata.turn });
        webglScene.customEvent("ShipEwChanged", { ship: selected });
    },

    buttonAssignEW: function buttonAssignEW(e) {
        var e = $(this).parent();
        var ship = e.data("ship");
        var entry = e.data("EW");
        ew.assignEW(ship, entry);
    },

    assignEW: function assignEW(ship, entry) {
        //var left = ew.getDefensiveEW(ship);		
		var left = ew.getEWLeft(ship);
        if (left < 1) return;

        if (!ship.osat) {
            if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(ship, "cnC"), "RestrictedEW")) {
                var def = ew.getDefensiveEW(ship);
                var all = ew.getScannerOutput(ship);

                if (def - 1 < all * 0.5) return false;
            }
        }

        if (entry == "CCEW") {
            ship.EW.push({ shipid: ship.id, type: "CCEW", amount: 1, targetid: -1, turn: gamedata.turn });
        } else if (entry == "BDEW") {
            if (ew.getEWByType("DIST", ship) > 0 || ew.getEWByType("SOEW", ship) > 0 || ew.getEWByType("SDEW", ship) > 0) {
                window.confirm.error("You cannot use blanket protection together with other ELINT functions.", function () {});
                return;
            } else {
                ship.EW.push({ shipid: ship.id, type: "BDEW", amount: 1, targetid: -1, turn: gamedata.turn });
            }
        } else if (entry.type == "DIST") {
            if (left < 3) return;
            entry.amount += 3;
        } else if (entry.type == "SOEW") {
            return;
        } else {
            entry.amount++;
        }

        webglScene.customEvent("ShipEwChanged", { ship: ship });
    },

    buttonDeassignEW: function buttonDeassignEW(e) {
        if (gamedata.waiting == true || gamedata.gamephase != 1) return;

        var e = $(this).parent();
        var ship = e.data("ship");
        var entry = e.data("EW");

        if (entry == "CCEW" || entry == "BDEW") {
            return;
        }

        var amount = 1;
        if (entry.type == "DIST") amount = 3;

        entry.amount -= amount;
        if (entry.amount < 1) {
            var i = $.inArray(entry, ship.EW);
            ship.EW.splice(i, 1);
            e.data("EW", "");
        }
        webglScene.customEvent("ShipEwChanged", { ship: ship });
    },

    deassignEW: function deassignEW(ship, entry) {
        var amount = 1;
        if (entry.type === "DIST") amount = 3;

        entry.amount -= amount;

        ship.EW = ship.EW.filter(function (shipEwEntry) {
            return shipEwEntry.amount > 0;
        });

        webglScene.customEvent("ShipEwChanged", { ship: ship });
    },

    removeEW: function removeEW(ship) {
        for (var i = ship.EW.length - 1; i >= 0; i--) {
            var ew = ship.EW[i];
            if (ew.turn == gamedata.turn) ship.EW.splice(i, 1);
        }		
		webglScene.customEvent("ShipEwChanged", { ship: ship });
    },
    checkInELINTDistance: function checkInELINTDistance(ship, target, distance) {
        if (!distance) distance = 30;

        return mathlib.getDistanceBetweenShipsInHex(ship, target) <= distance;
    },
	
	getJammerValueFromTo: function getJammerValueFromTo(shooter, target) {
		if (shooter.faction === target.faction) return 0;
		
		var jammerSystem = shipManager.systems.getSystemByName(target, "jammer");
		var jammerValue = 0;		
		if(jammerSystem != null) {
			jammerValue = shipManager.systems.getOutput(target, jammerSystem);
		}
		var stealthSystem = shipManager.systems.getSystemByName(target, "stealth");
		var stealthValue = 0;		
		if( (stealthSystem != null) && (mathlib.getDistanceBetweenShipsInHex(shooter, target) > 5) ) { //stealth-protected target at range >5 hexes gains STealth properties
			stealthValue = shipManager.systems.getOutput(target, stealthSystem);
		}
		if(stealthValue > jammerValue) jammerValue = stealthValue;//larger value is used
		
		if (jammerValue > 0){ //else no point
			//Advanced Sensors negate Jammer, Improved Sensors halve Jammer
			if (shipManager.hasSpecialAbility(shooter, "AdvancedSensors")) {
				jammerValue = 0; //negated
			} else if (shipManager.hasSpecialAbility(shooter, "ImprovedSensors")) {
				jammerValue = jammerValue * 0.5; //halved
			}
		} else {
			jammerValue = 0; //never negative
		}
			
        return jammerValue;		
	},

    getSupportedOEW: function getSupportedOEW(ship, target) {
		var jammerValue = ew.getJammerValueFromTo(ship,target);
		if (jammerValue>0) {
			return 0; //no lock-on on supported ship negates SOEW, if any
		}
		/*replaced by code above
        if(!shipManager.hasSpecialAbility(ship, "AdvancedSensors")){ //Advanced Sensors negate Jammer
            var jammer = shipManager.systems.getSystemByName(target, "jammer");

            if (jammer != null && shipManager.systems.getOutput(target, jammer) > 0 && !shipManager.systems.isDestroyed(target, jammer) && !shipManager.power.isOffline(target, jammer)) {
                // Ships with active jammers are immune to SOEW
                return 0;
            }
        }
		*/

        var amount = 0;

        for (var i in gamedata.ships) {
            var elint = gamedata.ships[i];
            if (elint == ship || !shipManager.isElint(elint)) continue;

            if (!ew.checkInELINTDistance(target, elint, 30)) continue;

            if (!ew.getEWByType("SOEW", elint, ship)) continue;

			jammerValue = ew.getJammerValueFromTo(elint,target);
			if (jammerValue>0) continue; //no lock-on negates SOEW, if any
			
            var foew = ew.getEWByType("OEW", elint, target) * 0.5;

			var dist = ew.getDistruptionEW(elint); //account for ElInt being disrupted
			foew = foew-dist;
				
            if (foew > amount) amount = foew;
        }

        if (ship.flight) {
            // fighters only receive half the amount of SOEW
            amount = amount * 0.5;
        }

        return amount;
    },

    getSupportedDEW: function getSupportedDEW(ship) {
        var amount = 0;
        var elints = gamedata.getElintShips();
        for (var i in elints) {
            var elint = elints[i];
            if (elint.id === ship.id) continue;

            var fdew = ew.getEWByType("SDEW", elint, ship) * 0.5;

            if (fdew > amount) amount = fdew;
        }

        return amount;
    },

    getSupportedBDEW: function getSupportedBDEW(ship) {
        var amount = 0;
        var elints = gamedata.getElintShips();
        for (var i in elints) {
            var elint = elints[i];

            //if(ship.faction != elint.faction)
            if (ship.userid != elint.userid) continue;

            if (!ew.checkInELINTDistance(ship, elint, 20)) continue;

            var fdew = ew.getEWByType("BDEW", elint) * 0.25;

            if (fdew > amount) amount = fdew;
        }

        return amount;
    },

    getDistruptionEW: function getDistruptionEW(ship) {

        var amount = 0;
        for (var i in gamedata.ships) {
            var elint = gamedata.ships[i];
            if (elint == ship || !shipManager.isElint(elint)) continue;

            var fdew = ew.getEWByType("DIST", elint, ship) / 3;

            //if (fdew > amount)
            amount += fdew;
        }

        var num = ew.getNumOfOffensiveTargets(ship);
        if (num > 0) return amount / num;

        return amount;
    },

    showAllEnemyEW: function showAllEnemyEW() {
        if (gamedata.gamephase > 1) {

            for (var i in gamedata.ships) {
                var ship = gamedata.ships[i];

                if (gamedata.isMyShip(ship)) continue;

                ew.adEWindicators(ship);
            }
            drawEntities();
        }
    },

    showAllFriendlyEW: function showAllFriendlyEW() {
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];

            if (!gamedata.isMyShip(ship)) continue;

            ew.adEWindicators(ship);
        }
        drawEntities();
    },
 
    
};
