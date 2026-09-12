"use strict";

window.ew = {
    getScannerOutput: function getScannerOutput(ship) {
        var ret = 0;

        if (shipManager.isAdrift(ship)) return 0;

        if (ship.flight){
			if(ship.minesweeper){
                return Math.floor(ship.offensivebonus);                 
            }else{
                return Math.floor(ship.offensivebonus / 2); //Normal Fighters can assign OB to mine detection and get half (round down) the equivalent in mine detection EW
            }    
        }
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
                ret -= 2 * shipManager.criticals.hasCritical(primary, "RestrictedEW"); //-2 does stack!
            }
            //GTS
            if (shipManager.criticals.hasCritical("SensorLoss")) {
                ret -= 3 * shipManager.criticals.hasCritical("SensorLoss"); //-3 does stack!
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

    /* ==========================================================================================
       WALKERS OF SIGMA-957 - MAPMAKER ELECTRONIC WARFARE (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12)
       Server mirror: EW::getFlightEwCapacity / EW::getFlightEwLeft in server/handlers/EW.php.
       ⚠️ MIRROR SET - a change to any one of these is a change to its twin.

       ⭐⭐ TWO POOLS, AND THE NAME COLLISION THAT WOULD OTHERWISE BITE. getScannerOutput() ALREADY
       has a flight branch, immediately above, and it answers a DIFFERENT QUESTION: floor(OB / 2)
       (full OB for a minesweeper) as the MINE-DETECTION allowance, which assignEW then spends on
       "Detect Mines" entries - and getEWLeft() is measured against it by every assign path.
       Adding 3 to that one number would let an ordinary fighter spend its mine-detection allowance
       on OEW AND let a Mapmaker spend its OEW pool on mine detection, in both directions and
       silently. So getScannerOutput() is left exactly as it was and the OEW/DEW paths consult
       these instead.
       ========================================================================================== */

    /* THE POOL. 0 for every flight in the game but the Mapmaker Sensor Probes, and 0 for every
       ship (a ship's pool is its scanner output). ship.ewCapacity rides the STATIC BLUEPRINT - see
       FighterFlight::$ewCapacity - so on a blueprint that predates Stage 12 it is undefined, and
       undefined fails the > 0 test, which is the wanted answer. */
    getFlightEwCapacity: function getFlightEwCapacity(ship) {
        if (!ship || !ship.flight) return 0;

        var capacity = parseInt(ship.ewCapacity, 10);
        return capacity > 0 ? capacity : 0;
    },

    /* THE GATE, spelled as a predicate because it reads better at the sites that ask it. */
    isFlightEwPool: function isFlightEwPool(ship) {
        return ew.getFlightEwCapacity(ship) > 0;
    },

    /* Which EW types come out of the flight pool rather than out of the Offensive Bonus. Exactly
       two: OEW is what a Mapmaker allocates, DEW is what the remainder becomes at commit.
       "Detect Mines" is deliberately absent - it is the OTHER pool. */
    isFlightEwType: function isFlightEwType(type) {
        return type === 'OEW' || type === 'DEW';
    },

    /* WHAT IS LEFT OF THE POOL. Counts OEW rows only, mirroring the ship-side rule that DEW is the
       REMAINDER rather than an allocation - which is what lets convertUnusedToDEW rewrite the DEW
       row on every commit without the number drifting.
       ⚠️ MIRROR PAIR with EW::getFlightEwLeft (PHP). */
    getFlightEwLeft: function getFlightEwLeft(ship) {
        var capacity = ew.getFlightEwCapacity(ship);
        if (capacity <= 0) return 0;

        var used = 0;
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (entry.type !== 'OEW') continue;
            used += entry.amount;
        }

        return capacity - used;
    },

    /* THE ONE QUESTION EVERY ALLOCATION PATH ASKS: how much may this unit still spend on an entry
       of this type? For a ship, and for a flight's mine detection, that is the long-standing
       getEWLeft(). For a Mapmaker's OEW/DEW it is the flight pool, which is a different budget
       entirely - see the block comment above. `type` is a string; the callers hold either a type
       literal or an EW entry object, so they resolve it before calling.

       ⚠️⚠️ THE FLIGHT TEST IS `ship.flight`, NOT `isFlightEwPool`, AND THAT IS THE WHOLE POINT.
       On ANY flight, OEW and DEW come out of the flight pool - and an ordinary flight's pool is
       0, so it can never allocate either. Asking isFlightEwPool here instead would fall through
       to getEWLeft() for an ordinary flight, which answers with its MINE-DETECTION allowance, and
       a Kotha would happily buy 2 points of OEW with the Offensive Bonus it was supposed to be
       spending on mine detection. No button offers that today (sourceCanAllocateOEW refuses a
       flight without a pool) and EW::clampFlightEw drops it server-side either way - but the
       two-pool rule has to hold HERE, where the arithmetic is, not only at the two places that
       currently happen to guard it. */
    getEwLeftFor: function getEwLeftFor(ship, type) {
        if (ship && ship.flight && ew.isFlightEwType(type)) return ew.getFlightEwLeft(ship);

        return ew.getEWLeft(ship);
    },

    /* A FLIGHT'S DEFENSIVE EW, and the mirror of the server's FighterFlight::getDEW().

       ⚠️ THE COMMITTED ROW WINS AND THE LIVE REMAINDER IS THE FALLBACK - the same two-step
       ew.getSavedEwPool() uses, and for the same reason: convertUnusedToDEW writes the row inside
       doCommit, so during Initial Orders there is nothing listed and the remainder IS the answer,
       which is what makes the flight window's DEW figure track the player's clicking.
       ⚠️ NOT getDefensiveEW(), which is an alias for getEWLeft() and would answer with the
       flight's MINE-DETECTION allowance. */
    getFlightDEW: function getFlightDEW(ship) {
        if (!ew.isFlightEwPool(ship)) return 0;

        var listed = ew.getListedDEW(ship);
        if (listed === null || listed === undefined) return Math.max(0, ew.getFlightEwLeft(ship));

        return Math.max(0, listed);
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
            if (distance <= 10) {
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

    getAllOEWandCCEW: function getAllOffensiveEW(ship) { //Required for HARM missile hit calculations
        var amount = 0;
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (entry.type == "OEW") amount += entry.amount;
            if (entry.type == "CCEW") amount += entry.amount;
        }
        return amount;
    },

    getAllEWExceptDEW: function getAllEWExceptDEW(ship) { //Required for HARM missile hit calculations
        var amount = 0;
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (entry.type == "DEW") continue;
            amount += entry.amount;
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
        /* Stage 12 (3.11): a Mapmaker's unspent points become DEW the way a ship's do - which is
           the whole of "3 OEW or DEW per turn, like a ship", since nothing allocates flight DEW
           explicitly. Every OTHER flight is still refused outright, and that refusal is
           load-bearing beyond this function: EW::hasCommittedDewRow() - the EW Detector's
           saved-point gate - asks whether a unit has a DEW row at all. See the explicit flight
           guard Stage 12 had to add to EW::getDetectorAllowance once one flight class started
           getting one. */
        if (ship.flight && !ew.isFlightEwPool(ship)) return false;

        //var dew = ew.getScannerOutput(ship) - ew.getUsedEW(ship);
        var dew = ship.flight ? ew.getFlightEwLeft(ship) : ew.getEWLeft(ship);
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
        if (shipManager.hasSpecialAbility(ship, "LCVSensors")) { //otherwise no check
            var totalEW = ew.getScannerOutput(ship);
            if (totalEW > 2) {
                var offensiveEW = ew.getAllOffensiveEW(ship);
                if (totalEW > (offensiveEW + 2)) {
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
        /* Stage 12 (3.11): on a Mapmaker this function still answers about the MINE-DETECTION
           pool, so its OEW rows - spent out of the separate flight EW pool - must not count as
           having used any of it. Without this, allocating OEW would silently shrink the flight's
           mine-detection allowance, which is exactly the cross-contamination the two-pool split
           exists to prevent. One property read on every other unit in the game. */
        var isFlightEwUnit = ew.isFlightEwPool(ship);

        var usedEW = 0;
        for (var i in ship.EW) {
            var entry = ship.EW[i];
            if (entry.turn != gamedata.turn) continue;
            if (isFlightEwUnit && ew.isFlightEwType(entry.type)) continue;
            if (entry.type != "DEW") {
                usedEW += entry.amount;
            }
        }
        /*		
        //22.10.2022: count Particle Impeder boost as used EW!
                var impederList = shipManager.systems.getSystemListByName(ship, "Particleimpeder");
                for (var i in impederList) {
                    var currImpeder = impederList[i];
                    //is it alive and powered up?
                    if (shipManager.systems.isDestroyed(ship, currImpeder)) continue;
                    if (shipManager.power.isOffline(ship, currImpeder)) continue;			
                    //current boost
                    var currBoost = shipManager.power.getBoost(currImpeder);
                    if (currBoost > 0) usedEW += currBoost;
                }
        //end of Impeder impact	
        */
        //Consolidated entries for EW boosted systems e.g. Particle Impeders and Psionic Lances.
        var ewBoostedSystemList = shipManager.systems.getSystemListEWBoosted(ship);
        for (var i in ewBoostedSystemList) {
            var currSystem = ewBoostedSystemList[i];
            //is it alive and powered up?
            if (shipManager.systems.isDestroyed(ship, currSystem)) continue;
            if (shipManager.power.isOffline(ship, currSystem)) continue;
            //current boost
            var currBoost = shipManager.power.getBoost(currSystem);
            if (currBoost > 0) usedEW += currBoost;
        }


        var totalAvailable = ew.getScannerOutput(ship);
        var leftEW = totalAvailable - usedEW;
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

    getDetectSEW: function getDetectSEW(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "Detect Stealth") {
                return EWentry.amount;
            }
        }

        return 0;
    },

    getDetectSEWentry: function geDetectSEWentry(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "Detect Stealth") {
                return EWentry;
            }
        }

        return null;
    },


    getDetectMEW: function getDetectMEW(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "Detect Mines") {
                return EWentry.amount + ship.minesweeperbonus;
            }
        }

        return ship.minesweeperbonus;
    },

    getDetectMEWentry: function getDetectMEWentry(ship) {

        for (var i in ship.EW) {
            var EWentry = ship.EW[i];
            if (EWentry.turn != gamedata.turn) continue;

            if (EWentry.type == "Detect Mines") {
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


    /* ⭐ WALKERS_OF_SIGMA_PLAN.md 3.14f (Stage 19, user ruling 2026-09-12) - A SHIP RIDING A
       DOCKING BAY IS OUT OF THE ACTIVE EW GAME, BOTH WAYS.

       "Waymarkers should also not use EW on transition Docking/Launching turns, nor should ships
       have the opportunity to use any targeted EW on it." Its sensors are slaved to the manoeuvre
       for the turn it spends clamped to the carrier's aft.

       ⚠️ IT KEEPS ITS DEW (user ruling, same day). Only ACTIVE allocations stop - OEW, CCEW, DIST,
       JAM, SOEW, SDEW, BDEW and the two Detect types. Unspent points still fall into DEW through
       convertUnusedToDEW exactly as they do for every other ship, so the rider is no easier to hit
       than usual; what it loses is the ability to spend, and what its enemies lose is the lock.
       An attacker with no lock then takes the ordinary doubled range penalty, which is the engine's
       standing rule and is left alone.

       ⚠️ shipManager.isDockingRider, NOT `ship.attached` - a breaching pod's host wears that too,
       and a boarded ship's EW is emphatically its own business. */
    isEwSuspended: function isEwSuspended(ship) {
        return !!(ship && window.shipManager && shipManager.isDockingRider(ship));
    },

    AssignOEW: function AssignOEW(selected, ship, type) {
        if (!type) type = "OEW";

        //Stage 19: the rider spends nothing, and nothing may be spent AT it. The menu hides these
        //buttons too (shipTooltipInitialOrdersMenu) - this is the backstop behind that, and the
        //server strips anything that still gets through (EW::stripDockingRiderEw).
        if (ew.isEwSuspended(selected) || ew.isEwSuspended(ship)) return;

        for (var i in selected.EW) {
            var EWentry = selected.EW[i];

            if (EWentry.turn !== gamedata.turn) continue;

            if (EWentry.type === type && EWentry.targetid === ship.id) return;
        }
        //var left = ew.getDefensiveEW(selected);
        //Stage 12 (3.11): a Mapmaker's OEW is budgeted against its 3-point flight pool, not
        //against the mine-detection allowance getEWLeft() answers with for a flight. Every other
        //unit in the game gets getEWLeft(), unchanged.
        var left = ew.getEwLeftFor(selected, type);

        var mod = 0;
        if (shipManager.hasSpecialAbility(selected, "ConstrainedEW")) mod += 1;//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.

        /* WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.8, Stage 10B). Outside Initial Orders this
           returns FALSE unless an EW Detector has left this unit a saved point and there is still
           budget for THIS click - see ew.canAllocateEwNow. Inside Initial Orders it is an
           unconditional true, so the phase behaves exactly as it always has. */
        if (!ew.canAllocateEwNow(selected, (type === "DIST") ? (3 + mod) : 1)) return;

        if (left < 1 || type === "DIST" && left < (3 + mod)) {
            return;
        }

        /* ⚠️ Stage 12 ADDED THE FLIGHT GUARD, and it is not caution. This path had never been
           reachable for a flight (the OEW buttons all carried sourceNotFlight), and the else-if
           below does hasCritical(getSystemByName(selected, "cnC"), ...) - which returns null on a
           flight and then reads null.criticals, a TypeError. assignEW() has carried the same
           !ship.flight guard for years for exactly this reason: the RestrictedEW critical lives on
           a C&C, and no flight has one. */
        if (!selected.osat && !selected.flight) {
            if (selected.base) {
                var primary = shipManager.getPrimaryCnC(selected);
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
        if (type == "DIST") amount = (3 + mod);

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
        /* `entry` is a TYPE STRING for the self-EW types and an EW ENTRY OBJECT for the rest (the
           increment paths), so the type has to be resolved defensively before it can be budgeted.
           Stage 12 (3.11): on a Mapmaker an OEW/DEW increment is measured against the 3-point
           flight pool and a "Detect Mines" increment against the Offensive Bonus, which is what
           getEwLeftFor separates. Every other unit gets getEWLeft(), unchanged. */
        var entryType = (typeof entry === 'string') ? entry : (entry && entry.type);

        /* Stage 19 (3.14f): the INCREMENT path, and it needs the same two tests as AssignOEW - this
           is where a second point is added to an OEW row that already exists, and where every
           self-EW type is added at all. DEW is deliberately exempt: the rider keeps it. */
        if (entryType !== 'DEW') {
            if (ew.isEwSuspended(ship)) return;
            var incTargetId = (typeof entry === 'object' && entry) ? entry.targetid : null;
            if (incTargetId !== null && incTargetId !== undefined
                && ew.isEwSuspended(gamedata.getShip(incTargetId))) return;
        }
        //var left = ew.getDefensiveEW(ship);		
        var left = ew.getEwLeftFor(ship, entryType);


        if (left < 1) return;

        if (!ship.osat && !ship.flight) {
            if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(ship, "cnC"), "RestrictedEW")) {
                var def = ew.getDefensiveEW(ship);
                var all = ew.getScannerOutput(ship);

                if (def - 1 < all * 0.5) return false;
            }
        }

        var mod = 0;
        if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) mod += 1;//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.

        //Stage 10B - see the note in AssignOEW. `entry` is a STRING for the self-EW types and an
        //EW entry object for the rest, so the Disruption cost is read defensively.
        if (!ew.canAllocateEwNow(ship, (entry && entry.type == "DIST") ? (3 + mod) : 1)) return;

        if (entry == "CCEW") {
            ship.EW.push({ shipid: ship.id, type: "CCEW", amount: 1, targetid: -1, turn: gamedata.turn });
        } else if (entry == "Detect Stealth") {
            ship.EW.push({ shipid: ship.id, type: "Detect Stealth", amount: 1, targetid: -1, turn: gamedata.turn });
        } else if (entry == "Detect Mines") {
            ship.EW.push({ shipid: ship.id, type: "Detect Mines", amount: 1, targetid: -1, turn: gamedata.turn });
        } else if (entry == "BDEW") {
            if (ew.getEWByType("DIST", ship) > 0 || ew.getEWByType("SOEW", ship) > 0 || ew.getEWByType("SDEW", ship) > 0) {
                window.confirm.error("You cannot use blanket protection together with other ELINT functions.", function () { });
                return;
            } else {
                ship.EW.push({ shipid: ship.id, type: "BDEW", amount: 1, targetid: -1, turn: gamedata.turn });
            }
        } else if (entry.type == "DIST") {
            if (left < (3 + mod)) return;
            entry.amount += (3 + mod);
        } else if (entry.type == "SOEW") {
            return;
        } else {
            entry.amount++;
        }

        webglScene.customEvent("ShipEwChanged", { ship: ship });
    },

    buttonDeassignEW: function buttonDeassignEW(e) {
        /* Stage 10B RELAXED THIS GATE, and only this far: Initial Orders is unchanged, and the
           Pre-Firing/Firing window opens only for a unit that has actually spent a saved point,
           which is what ew.canDeallocateEwNow answers. It still refuses once the player has
           committed (gamedata.waiting), which isLateEwWindowOpen carries. */
        var e = $(this).parent();
        var ship = e.data("ship");
        var entry = e.data("EW");

        if (gamedata.waiting == true) return;
        if (gamedata.gamephase != 1 && !ew.isLateEwWindowOpen(ship)) return;

        if (entry == "CCEW" || entry == "BDEW") {
            return;
        }

        var mod = 0;
        if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) mod += 1;//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.

        var amount = 1;
        if (entry.type == "DIST") amount = 3 + mod;

        //Stage 10B: outside Initial Orders a de-assign may only give back what the LATE window
        //itself spent. Taking an Initial Orders allocation back is refused here because the server
        //ignores it anyway (EW::diffLateEw writes positive deltas only) - so allowing the click
        //would show the player a number the next payload silently undoes.
        if (!ew.canDeallocateEwNow(ship, amount)) return;

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
        var mod = 0;
        if (shipManager.hasSpecialAbility(ship, "ConstrainedEW")) mod += 1;//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.     

        if (entry.type === "DIST") amount = 3 + mod;

        //Stage 10B - see buttonDeassignEW. This is the tooltip-menu path to the same act.
        if (!ew.canDeallocateEwNow(ship, amount)) return;

        entry.amount -= amount;

        ship.EW = ship.EW.filter(function (shipEwEntry) {
            return shipEwEntry.amount > 0;
        });

        webglScene.customEvent("ShipEwChanged", { ship: ship });
    },

    removeEW: function removeEW(ship) {
        /* ⚠️ STAGE 10B - INITIAL ORDERS ONLY, AND THAT IS A REAL RESTRICTION RATHER THAN CAUTION.
           "Remove All EW" clears every entry for the turn, Initial Orders allocations included. In
           the late window those are committed rows the server will not un-write (EW::diffLateEw
           takes positive deltas only), so the button would blank the panel and the next payload
           would put it all back. The per-entry remove buttons are the late window's way to undo,
           and they are bounded by ew.canDeallocateEwNow. */
        if (gamedata.gamephase != 1) return;

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
        var jammerSystem = null;
        var jammerValue = 0;

        if (target.faction == "Torvalus Speculators") {
            if (target.flight) return 0; //Torvalus fighters do not get Jammer effect.
            var shadingField = shipManager.systems.getSystemByName(target, "ShadingField");
            if (shadingField){
                if(!shipManager.systems.isDestroyed(target, shadingField) && !shipManager.power.isOffline(target, shadingField)) {
                    return 1; //Not destroyed or offline
                } else {
                    return 0; //Destroyed or offline
                }
            }else{
                var alphaShadingField = shipManager.systems.getSystemByName(target, "AlphaShadingField");
                if (alphaShadingField){
                    if(shooter.factionAge <= 2 && !shipManager.systems.isDestroyed(target, alphaShadingField) && !shipManager.power.isOffline(target, alphaShadingField)) {
                        return 1; //Not destroyed or offline
                    } else {
                        return 0; //Destroyed or offline
                    }                    
                }else{
                    return 0;
                }                
            }    
        }

        if (shooter.faction != target.faction) { //in-faction units ignore jammer (but not stealth!)
            jammerSystem = shipManager.systems.getSystemByName(target, "jammer");
            if (jammerSystem != null) {
                jammerValue = shipManager.systems.getOutput(target, jammerSystem);
            }
        }
        var stealthSystem = shipManager.systems.getSystemByName(target, "stealth");
        var stealthValue = 0;
        var distance = mathlib.getDistanceBetweenShipsInHex(shooter, target);
        //Amended this section to accommodate Hyach Stealth ships - DK 18.3.24				
        if ((stealthSystem != null) && (distance > 5) && target.flight) { //stealth-protected fighter at range >5 hexes may gain Stealth properties
            stealthValue = shipManager.systems.getOutput(target, stealthSystem);
        }
        var stealthDistance = 12; //Default for ships
        if (shooter.flight) stealthDistance = 4; //Fighters
        if (shooter.base) stealthDistance = 24; //Bases
        if ((stealthSystem != null) && (distance > stealthDistance) && target.shipSizeClass >= 0) { //stealth-protected ship at range >10 hexes may gain Stealth properties
            stealthValue = shipManager.systems.getOutput(target, stealthSystem);
        }

        if (stealthValue > jammerValue) jammerValue = stealthValue;//larger value is used

        if (jammerValue > 0) { //else no point
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
        var jammerValue = ew.getJammerValueFromTo(ship, target);
        if (jammerValue > 0) {
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

            if (!ew.checkInELINTDistance(target, elint, 30)) continue; //Check distance between target ship and ELINT
            if (!ew.checkInELINTDistance(ship, elint, 30)) continue; //Check distance between firing ship and ELINT

            if (!ew.getEWByType("SOEW", elint, ship)) continue;

            jammerValue = ew.getJammerValueFromTo(elint, target);
            if (jammerValue > 0) continue; //no lock-on negates SOEW, if any

            //Check for Line of sight - DK Nov 2025
            //var blockedLosHex = weaponManager.getBlockedHexes();
            var blockedLosHex = gamedata.blockedHexes;
            var loSBlockedshooter = false;
            var loSBlockedtarget = false;

            if (blockedLosHex && blockedLosHex.length > 0) {
                var sPosELINT = shipManager.getShipPosition(elint);
                var sPosShooter = shipManager.getShipPosition(ship);
                var sPosTarget = shipManager.getShipPosition(target);

                loSBlockedtarget = mathlib.isLoSBlocked(sPosELINT, sPosTarget, blockedLosHex);
                if (loSBlockedtarget) continue; //Line of sight blocked to one of the relevant units, skip.  

                loSBlockedshooter = mathlib.isLoSBlocked(sPosELINT, sPosShooter, blockedLosHex);
                if (loSBlockedshooter) continue; //Line of sight blocked to one of the relevant units, skip.                                       
            }



            if (shipManager.hasSpecialAbility(elint, "ConstrainedEW")) {//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
                var foew = ew.getEWByType("OEW", elint, target) * 0.33;
                foew = Math.round(foew * 3) / 3;
            } else {
                var foew = ew.getEWByType("OEW", elint, target) * 0.5;
            }

            var dist = ew.getDistruptionEW(elint); //account for ElInt being disrupted
            foew = foew - dist;

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

            if (shipManager.hasSpecialAbility(elint, "ConstrainedEW")) {//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
                var fdew = ew.getEWByType("SDEW", elint, ship) * 0.33;
                fdew = Math.round(fdew * 3) / 3;
            } else {
                var fdew = ew.getEWByType("SDEW", elint, ship) * 0.5;
            }

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
            //if (ship.userid != elint.userid) continue;
            if (ship.team != elint.team) continue;

            if (!ew.checkInELINTDistance(ship, elint, 20)) continue;

            if (shipManager.hasSpecialAbility(elint, "ConstrainedEW")) {//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
                var fdew = ew.getEWByType("BDEW", elint) * 0.2;
            } else {
                var fdew = ew.getEWByType("BDEW", elint) * 0.25;
            }

            if (fdew > amount) amount = fdew;
        }

        return amount;
    },

    getDistruptionEW: function getDistruptionEW(ship) {

        var amount = 0;
        //var blockedLosHex = weaponManager.getBlockedHexes();        
        var blockedLosHex = gamedata.blockedHexes; //Are there any blocked hexes, no point checking if no.	  

        for (var i in gamedata.ships) {
            var elint = gamedata.ships[i];
            if (elint == ship || !shipManager.isElint(elint)) continue;

            if (blockedLosHex && blockedLosHex.length > 0) {
                var loSBlocked = mathlib.isLoSBlocked(shipManager.getShipPosition(elint), shipManager.getShipPosition(ship), blockedLosHex);
                if (loSBlocked) continue; //Line of sight blocked to one of the relevant units, skip.
            }

            if (shipManager.hasSpecialAbility(elint, "ConstrainedEW")) {//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
                var fdew = ew.getEWByType("DIST", elint, ship) / 4;
            } else {
                var fdew = ew.getEWByType("DIST", elint, ship) / 3;
            }

            //if (fdew > amount)
            amount += fdew;
        }

        var num = ew.getNumOfOffensiveTargets(ship);
        if (num > 0) return amount / num;

        return amount;
    },

    /* ============================================================================================
       WALKERS OF SIGMA-957 - EW DETECTOR (WALKERS_OF_SIGMA_PLAN.md 3.8, Stage 10A)
       Server mirror: EW::collectEwDetectors / EW::savedEwAllowanceFromDetectors /
       EW::countEwDetectorsCovering / EW::getSavedEwAllowance in server/handlers/EW.php.
       ⚠️ MIRROR SET - a change to any one of these is a change to its twin.

       ⭐ WHY THIS IS MIRRORED AT ALL, when every other EW sweep is server-only. The rule is "in
       range BOTH BEFORE AND AFTER movement", and the after-movement position is a PLOTTED,
       UNCOMMITTED one - the server has not been told about it and by definition cannot be, because
       the whole point of the allowance is that the player spends it at the end of the Movement
       segment on the strength of where they ended up. shipManager.getShipPosition() already
       returns the plotted position during Movement, so this sweep tracks the drag for free.
       ============================================================================================ */

    /* Every EW DETECTOR on the board that is currently doing its job, as flat tuples
       (team, position, range). Collected ONCE and handed to the per-ship question below, so a
       fleet-wide loop costs one sweep rather than one per ship.

       ⚠️ THE SYSTEM SWEEP COMES FIRST AND EVERY OTHER QUESTION IS DEFERRED BEHIND IT, exactly as
       the server's twin does: in all but a handful of games nothing is an EW Detector, and a name
       comparison is the cheapest question available here.

       ⚠️ `effectiveRange` is the SERVER's answer after destruction and power-down; the two local
       tests after it exist because a player may power a detector down during Initial Orders and
       the server will not know until the phase is committed - which is exactly the window in which
       the allowance is being read. `range` is the fallback for the lobby, which has no
       effectiveRange (stripForJson is the in-game payload). */
    collectEwDetectors: function collectEwDetectors() {
        var detectors = [];

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (!ship || !ship.systems) continue;

            var systems = [];
            for (var s in ship.systems) {
                if (ship.systems[s] && ship.systems[s].name === 'EWDetector') systems.push(ship.systems[s]);
            }
            if (systems.length === 0) continue;

            if (ship.team === undefined || ship.team === null) continue;

            /* ⭐ "DOCKED SHIPS WITH EW DETECTORS STILL CONTRIBUTE THEIR SAVED EW TO SHIPS WITHIN 20
               HEXES" (user ruling 2026-09-12, WALKERS_OF_SIGMA_PLAN.md 3.14d). The three tests that
               used to stand here - destroyed, not deployed yet, no position - are all inside
               shipManager.getProjectionOrigin now, together with the one this rule adds: a unit
               STOWED in a carrier keeps detecting, from its CARRIER'S hex. Mirror of
               HangarOps::projectionOriginFor, and it keeps tracking the drag during Movement for
               the same reason it always did - getShipPosition returns the plotted position.
               ⚠️ An enemy's docked list is masked, so this answers null for their stowed detectors
               and they are silently dropped. That is harmless HERE and only here: the allowance is
               filtered to the viewer's own team (countEwDetectorsCovering), whose bays are
               disclosed to them. */
            var position = shipManager.getProjectionOrigin(ship);
            if (!position) continue;

            for (var d = 0; d < systems.length; d++) {
                var system = systems[d];

                if (shipManager.systems.isDestroyed(ship, system)) continue;
                if (shipManager.power.isOffline(ship, system)) continue;

                var published = parseInt(system.effectiveRange, 10);
                var range = isNaN(published) ? (parseInt(system.range, 10) || 0) : published;
                if (range <= 0) continue;

                detectors.push({ team: parseInt(ship.team, 10), pos: position, range: range });
            }
        }

        return detectors;
    },

    /* The rules' degrading ladder, and the ONLY place it is written on the client.
       "The first four EW Detectors allow the fleet to save 1 point of EW each. EW Detectors number
       5-8 allow the fleet to save 1/2 of a point each. All additional EW detectors allow only 1/4
       of a point each. Round down fractions of 1/4 and 1/2 and round up fractions of 3/4."

       ⭐ COUNTED IN QUARTERS, AS INTEGERS, START TO FINISH - see the server twin for why (every
       term is a multiple of 1/4, and integer arithmetic is the only kind that can be PROMISED
       identical in PHP and JavaScript). "Down at 1/4 and 1/2, up at 3/4" is floor(x + 1/4), i.e.
       floor((quarters + 1) / 4):
         1 -> 4q -> 1      4 -> 16q -> 4      5 -> 18q -> 4      8 -> 24q -> 6
         9 -> 25q -> 6    10 -> 26q -> 6     11 -> 27q -> 7     12 -> 28q -> 7   */
    savedEwAllowanceFromDetectors: function savedEwAllowanceFromDetectors(count) {
        count = parseInt(count, 10) || 0;
        if (count <= 0) return 0;

        var full = Math.min(count, 4);                          //detectors 1-4: a whole point each
        var halves = Math.min(Math.max(count - 4, 0), 4);       //detectors 5-8: half a point each
        var quarters = Math.max(count - 8, 0);                  //detectors 9+ : a quarter each

        var totalQuarters = (4 * full) + (2 * halves) + quarters;

        return Math.floor((totalQuarters + 1) / 4);
    },

    /* How many of `detectors` cover `position` for `team`. Split out from the ship question below
       because the rule asks it TWICE of the same unit - once at Initial Orders and once at the end
       of Movement - and the second position is one only the client holds. */
    countEwDetectorsCovering: function countEwDetectorsCovering(detectors, team, position) {
        if (!detectors || detectors.length === 0 || !position) return 0;

        team = parseInt(team, 10);
        var count = 0;

        for (var i = 0; i < detectors.length; i++) {
            if (parseInt(detectors[i].team, 10) !== team) continue;
            if (detectors[i].pos.distanceTo(position) > detectors[i].range) continue;
            count++;
        }

        return count;
    },

    /* THE LADDER AT THIS SHIP'S POSITION, before the pool clamp below. Pass `detectors` when
       looping over a fleet; it is collected for you otherwise.
       ⚠️ MIRROR PAIR with EW::getDetectorAllowance (PHP).

       ⚠️ A unit that ends its movement out of range LOSES the point ("If a vessel declares that it
       is saving an EW point but ends its movement step out of range of the EW Detector, the point
       is lost"). Nothing declares: this is the same function asked at two different moments, and
       because getShipPosition() follows the plot it answers about where the ship WILL be while the
       player is still dragging, then about where it ended up once the Pre-Firing window opens. */
    getDetectorAllowance: function getDetectorAllowance(ship, detectors) {
        if (!ship) return 0;
        if (detectors === undefined || detectors === null) detectors = ew.collectEwDetectors();
        if (detectors.length === 0) return 0;

        /* ⭐ FLIGHTS ARE IN (user ruling 2026-09-10: "the effect applies to all Walker units").
           No test for one is needed and none is wanted: the ladder is a question about POSITION,
           and getSavedEwAllowance() then clamps it by what the unit actually has left to hold
           back. An ordinary flight has no EW pool at all, so that clamp answers 0 for it without
           this function ever having to know what a flight is - which is why the Mapmaker is the
           only flight in the game this reaches. */
        if (shipManager.isDestroyed(ship)) return 0;
        if (shipManager.getTurnDeployed(ship) > gamedata.turn) return 0;
        if (ship.team === undefined || ship.team === null) return 0;

        var position = shipManager.getShipPosition(ship);
        if (!position) return 0;

        var count = ew.countEwDetectorsCovering(detectors, ship.team, position);

        return ew.savedEwAllowanceFromDetectors(count);
    },

    /* ============================== STAGE 10B - THE POOL AND THE WINDOW =========================
       User ruling 2026-09-09: "If a ship spends all their EW on non-DEW EW types, then they are
       unable to save a point of EW (and the EW panel should reflect this during EW orders, so it
       doesn't misleadingly show a player saving some EW points for later when in fact they've spent
       them all on non-DEW uses). Essentially DEW is the only pool of unspent EW that saved EW can
       be drawn from in Pre-Firing/Firing."
       ============================================================================================ */

    /* THE POOL A SAVED POINT COMES OUT OF, and the ANCHOR every late-allocation sum is measured
       against.

       ⭐⭐ IT HAS TO BE THE COMMITTED DEW ROW, NOT getEWLeft(), and that is the whole trick of the
       late window. getEWLeft() is the LIVE remainder: it falls by one with every point spent. Using
       it as the budget would shrink the budget as the budget was spent - each point costing two -
       so the anchor must be a number that does not move while the player is clicking. The committed
       DEW row is exactly that: the server wrote it at the Initial Orders commit and the late window
       never touches it (only the server's own debit does, after the phase).

       ⚠️ THERE IS NO ROW DURING INITIAL ORDERS. convertUnusedToDEW writes it inside doCommit
       (gamedata.js), so while the player is still allocating there is nothing listed and the live
       remainder IS the answer - which is what makes the panel's figure track their clicking in the
       phase where the user asked it to.
       ⚠️ MIRROR PAIR with EW::getUnspentEw (PHP), including that fallback. */
    /* ⚠️ THE LIVE REMAINDER, FROM WHICHEVER POOL THIS UNIT ACTUALLY SPENDS (Stage 12).
       For a ship that is getEWLeft() - scanner output minus everything not DEW. For a FLIGHT it
       must be getFlightEwLeft(), because getEWLeft() answers about the flight's MINE-DETECTION
       allowance, which has nothing to do with the EW a saved point is held back from. Every late
       window sum below runs through here so the two can never drift apart. */
    getUnspentEwLive: function getUnspentEwLive(ship) {
        if (!ship) return 0;
        if (ship.flight) return Math.max(0, ew.getFlightEwLeft(ship));

        return Math.max(0, ew.getEWLeft(ship));
    },

    getSavedEwPool: function getSavedEwPool(ship) {
        if (!ship) return 0;

        var listed = ew.getListedDEW(ship);
        if (listed === null || listed === undefined) return ew.getUnspentEwLive(ship);

        return Math.max(0, listed);
    },

    /* HOW MANY EW POINTS THIS SHIP MAY ACTUALLY HOLD BACK - the ladder, clamped by the pool. This
       is the number the ship window shows and the number the late window budgets to.
       ⚠️ MIRROR PAIR with EW::getSavedEwAllowance (PHP). */
    getSavedEwAllowance: function getSavedEwAllowance(ship, detectors) {
        var allowance = ew.getDetectorAllowance(ship, detectors);
        if (allowance <= 0) return 0;

        return Math.min(allowance, ew.getSavedEwPool(ship));
    },

    /* WHEN A SAVED POINT MAY BE SPENT (user ruling 2026-09-09): "'End of movement' in FV is
       essentially the start of Pre-Firing phase (if there is one) or start of Firing phase."
       5 is Pre-Firing and 3 is Firing. BOTH, sharing ONE budget - a turn whose Initial Orders had
       nothing to activate skips phase 5 entirely, and gating on 5 alone would silently deny the
       allowance in exactly the games where a player has fewest units left.
       ⚠️ MIRROR PAIR with EW::isLateEwPhase (PHP). */
    isLateEwPhase: function isLateEwPhase() {
        return (gamedata.gamephase === 5 || gamedata.gamephase === 3);
    },

    /* Is this unit's late-allocation window open right now? */
    isLateEwWindowOpen: function isLateEwWindowOpen(ship) {
        if (!ship) return false;
        if (!ew.isLateEwPhase()) return false;
        if (gamedata.waiting) return false;              //already committed this phase
        if (!gamedata.isMyShip(ship)) return false;      //EW is submitted per userid, not per team
        if (shipManager.isDestroyed(ship)) return false;

        return ew.getSavedEwAllowance(ship) > 0;
    },

    /* HOW MUCH OF THE ALLOWANCE HAS ALREADY GONE, derived rather than tracked.

       ⭐ ONE DERIVED NUMBER AND TWO BOUNDS IS THE WHOLE OF THE LATE-WINDOW BOOKKEEPING. Every point
       spent moves a point OUT of the unspent pool, so `pool - getEWLeft()` is what has been spent
       since the commit - no snapshot of the committed EW array, no per-entry marking, and nothing
       that a poll rebuilding gamedata.ships could get out of step with. The upper bound (may not
       exceed the allowance) is the budget; the lower bound (may not go below zero, i.e. getEWLeft
       may not rise above the pool) is what stops the player UNDOING an Initial Orders allocation in
       a phase where the server would ignore the removal anyway. */
    getLateEwSpent: function getLateEwSpent(ship) {
        if (!ship) return 0;

        //getUnspentEwLive, not getEWLeft: on a flight the latter is the mine-detection pool and
        //this subtraction would read a spend that never happened. See getUnspentEwLive.
        return Math.max(0, ew.getSavedEwPool(ship) - ew.getUnspentEwLive(ship));
    },

    /* What is left of the allowance. */
    getLateEwRemaining: function getLateEwRemaining(ship) {
        if (!ship) return 0;

        return Math.max(0, ew.getSavedEwAllowance(ship) - ew.getLateEwSpent(ship));
    },

    /* THE TWO GATES every allocation path funnels through.

       ⚠️ INITIAL ORDERS IS RETURNED UNCHANGED, deliberately and by an early `return true` rather
       than by a condition that happens to pass: phase 1 has its own long-standing rules (and its own
       `gamedata.waiting` handling, which differs between the assign and de-assign paths), and Stage
       10B must not quietly move any of them.

       `cost` is the EW the click actually consumes - 1 for everything except Disruption, which is 3
       (4 on a ConstrainedEW hull) and means nothing as a fragment. */
    canAllocateEwNow: function canAllocateEwNow(ship, cost) {
        if (gamedata.gamephase === 1) return true;
        if (!ew.isLateEwWindowOpen(ship)) return false;

        return ew.getLateEwRemaining(ship) >= (cost || 1);
    },

    canDeallocateEwNow: function canDeallocateEwNow(ship, cost) {
        if (gamedata.gamephase === 1) return true;
        if (!ew.isLateEwWindowOpen(ship)) return false;

        return ew.getLateEwSpent(ship) >= (cost || 1);
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
