"use strict";

shipManager.systems = {

    selectedShipHasSelectedWeapons: function selectedShipHasSelectedWeapons(ship, ballistic) {
        for (var i in gamedata.selectedSystems) {
            var system = gamedata.selectedSystems[i];
            if (!ballistic && system.weapon) return true;
            if (ballistic && system.weapon && system.ballistic) return true;
        }

        return false;
    },

    getArmour: function getArmour(ship, system) {
        var armour = system.armour - shipManager.criticals.hasCritical(system, "ArmorReduced");
        if (armour < 0) armour = 0;

        return armour;
    },

    getFighterForSystem: function getFighterForSystem(ship, system) {

        return ship.systems.find(function (fighter) {
            return fighter && fighter.systems.includes(system);
        })
    },

    isDestroyed: function isDestroyed(ship, system) {
        if (!system) return false;
        if (system.parentId > 0) {
            var parentSystem = system;

            while (parentSystem.parentId > 0) {
                parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
            }

            return parentSystem.destroyed;
        }


        if (ship.flight && !(system instanceof Fighter)) {
            var fighter = shipManager.systems.getFighterForSystem(ship, system);
            if (fighter.destroyed) {
                return true;
            }
        }

        return system.destroyed;
    },

    isEngineDestroyed: function isEngineDestroyed(ship) {
        if (ship.flight || ship.osat) return false;

        // Check all engines, as Dilgar have two of them.
        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (system.name == "engine") {
                if (!shipManager.systems.isDestroyed(ship, system)) {
                    // At least one of the engines is still alive
                    return false;
                }
            }
        }

        return true;
    },

    isReactorDestroyed: function isReactorDestroyed(ship) {
        if (ship.flight) return false;
        if (ship.base) {
            return shipManager.systems.isDestroyed(ship, shipManager.systems.getSystemByNameInLoc(ship, "reactor", 0));
        }

        return shipManager.systems.isDestroyed(ship, shipManager.systems.getSystemByName(ship, "reactor"));
    },

    getOutput: function (ship, system) {
        if (!system) {
            console.log("ERROR: getOutput system missing");
            console.trace();
        }

        if (this.isDestroyed(ship, system))
            return 0;

        if (shipManager.power.isOffline(ship, system))
            return 0;

        var output = system.output + system.outputMod + shipManager.power.getBoost(system);
        output = Math.max(0, output); //output cannot be negative!

        return output;
    },

    getOutputNoBoost: function getOutputNoBoost(ship, system) {
        if (!system) {
            console.log("ERROR: getOutputNoBoost system missing");
            console.trace();
        }

        if (this.isDestroyed(ship, system))
            return 0;

        if (shipManager.power.isOffline(ship, system))
            return 0;

        var output = system.output + system.outputMod;
        output = Math.max(0, output); //output cannot be negative!

        return output;
    },

    getFighterSystem: function getFighterSystem(ship, fighterid, systemid) {
        for (var i in ship.systems) {
            var fighter = ship.systems[i];
            if (fighter.id == fighterid) {
                for (var a in fighter.systems) {
                    if (fighter.systems[a].id == systemid) return fighter.systems[a];
                }
            }
        }
    },

    getFighterBySystem: function getFighterBySystem(ship, systemid) {
        for (var i in ship.systems) {
            var fighter = ship.systems[i];

            for (var a in fighter.systems) {
                if (fighter.systems[a].id == systemid) return fighter;
            }
        }
    },

    getSystem: function getSystem(ship, id) {

        if (ship == null) {
            return null;
        }

        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (system.id == id) {
                return system;
            }

            if (system.fighter) {
                for (var i in system.systems) {
                    var fighter_system = system.systems[i];

                    if (fighter_system.id == id) {
                        return fighter_system;
                    }
                }
            }
        }

        return null;
    },

    initializeSystem: function initializeSystem(system) {

        if (system.boostable) {
            system = system.initBoostableInfo();
        }
        system = system.initializationUpdate(); //very rarely - system needs to update data not on a particular event

        if (system.name == "engine") {
            system.addInfo();
        }

        // Check the number of elements in missileArray
        // This has to be done like this, as length doesn't give the correct
        // return because the elements in the missileArray aren't on consequetive
        // indices.
        var cnt = 0;
        for (var i in system.missileArray) {
            cnt++;
        }

        if (system.missileArray !== null && cnt > 0) {
            system.range = system.missileArray[system.firingMode].range + system.rangeMod;
        }

        return system;
    },

    getSystemByName: function getSystemByName(ship, name) {
        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (system.fighter) {
                for (var a in system.systems) {
                    var figsys = system.systems[a];

                    if ((figsys.name == name)
                        && (!shipManager.systems.isDestroyed(ship, figsys))) { //only on alive fighters!
                        return figsys;
                    }
                }
            }
            if (system.name == name) {
                return system;
            }
        }

        return null;
    },


    getSystemListByName: function getSystemByName(ship, name) {
        var toReturn = Array();
        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (system.fighter) {
                for (var a in system.systems) {
                    var figsys = system.systems[a];

                    if ((figsys.name == name)
                        && (!shipManager.systems.isDestroyed(ship, figsys))) { //only on alive fighters!
                        toReturn.push(figsys);
                    }
                }
            }
            if (system.name == name) {
                toReturn.push(system);
            }
        }

        return toReturn;
    },

    getSystemByNameInLoc: function getSystemByNameInLoc(ship, name, loc) {
        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (system.location == loc && system.name == name) {
                return system;
            }
        }

        return null;
    },

    getArcs: function getArcs(ship, weapon) {

        if (shipManager.movement.isRolled(ship)) {
            return { start: mathlib.addToDirection(weapon.endArc, weapon.endArc * -2), end: mathlib.addToDirection(weapon.startArc, weapon.startArc * -2) };
        } else {
            return { start: weapon.startArc, end: weapon.endArc };
        }
    },


    getMultipleArcs: function getMultipleArcs(ship, weapon) {
        const arcs = [];

        const isRolled = shipManager.movement.isRolled(ship);

        // Nothing to return if arrays are missing or empty
        if (!weapon.startArcArray?.length || !weapon.endArcArray?.length) {
            return arcs;
        }

        for (let i = 0; i < weapon.startArcArray.length; i++) {
            const start = weapon.startArcArray[i];
            const end = weapon.endArcArray[i];

            // Skip unmatched pairs
            if (end === undefined) continue;

            if (isRolled) {
                arcs.push({
                    start: mathlib.addToDirection(end, end * -2),
                    end: mathlib.addToDirection(start, start * -2)
                });
            } else {
                arcs.push({ start, end });
            }
        }

        return arcs;
    },


    getDisplayName: function getDisplayName(system) {

        if (system.name == "structure") {
            if (system.location == 0) return "Primary structure";
            if (system.location == 1) return "Forward structure";
            if (system.location == 2) return "Aft structure";
            if (system.location == 3) return "Port structure";
            if (system.location == 4) return "Starboard structure";
        }

        return system.displayName;
    },

    getLocationName: function getLocationName(system) {

        if (system.location == 0) return "Primary";
        if (system.location == 1) return "Forward";
        if (system.location == 2) return "Aft";
        if (system.location == 3) return "Port";
        if (system.location == 4) return "Starboard";

        return "error, system location not defined";
    },

    getSystemsInLocation: function getSystemsInLocation(ship, location) {
        var systems = Array();

        for (var i in ship.systems) {
            if (ship.systems[i].location == location) systems.push(ship.systems[i]);
        }

        return systems;
    },

    getSystemsForShipStatus: function getSystemsForShipStatus(ship, location) {

        var systems = Array();

        for (var i in ship.systems) {
            if (ship.systems[i].location == location && ship.systems[i].name != "structure") systems.push(ship.systems[i]);
        }

        return systems;
    },

    getStructureSystem: function getStructureSystem(ship, location) {
        if (ship.flight) {
            return null;
        }

        if (ship.structures && ship.structures[location]) {
            return shipManager.systems.getSystem(ship, ship.structures[location]);
        }

        // Fallback for dynamically created ships (e.g. mines) that don't have structures array serialized
        for (var i in ship.systems) {
            if (ship.systems[i].name == "structure" && ship.systems[i].location == location) {
                return ship.systems[i];
            }
        }

        return null;
    },

    groupSystems: function groupSystems(systems) {
        var grouped = Array();
        var found = false;

        for (var i in systems) {
            var system = systems[i];

            var found = false;
            for (var a in grouped) {
                for (var b in grouped[a]) {
                    if (!found && (grouped[a][b].name == system.name || grouped[a][b].primary && system.primary) && grouped[a].length < 4) {
                        grouped[a].push(system);
                        found = true;
                        break;
                    }
                }
            }
            if (!found) {
                var group = Array();
                group.push(system);
                grouped.push(group);
            }
        }

        grouped.sort(function (a, b) {

            var al = a.length;
            var bl = b.length;

            if (al == 3 && bl == 2) return -1;

            if (bl == 3 && al == 2) return 1;

            if (al > bl) return 1;

            if (bl > al) return -1;

            return 0;
        });

        return grouped;
    },

    getFlightArmour: function getFlightArmour(ship, system) {
        var front = ship.systems[1].armour[0];
        var aft = ship.systems[1].armour[1];
        var side = ship.systems[1].armour[2];

        var armour = front + " / " + side + " / " + aft;

        return armour;
    },

    //Total declared hangar slots on a ship (sum of maxhealth across Hangar systems).
    //Includes Hangars but not other system types.
    getTotalHangarCapacity: function getTotalHangarCapacity(ship) {
        var total = 0;
        if (!ship || !ship.systems) return 0;
        for (var i in ship.systems) {
            var system = ship.systems[i];
            //Catapults (name "catapult") are excluded: their boxes are structural
            //HP only and never contribute to the default-shuttle pool (Stage 16).
            if (system && system.name == "hangar") total += parseInt(system.maxhealth, 10) || 0;
        }
        return total;
    },

    //Stage 16: does this ship carry a Catapult? Superheavy fighters live in the
    //catapult, not the shuttle-pool hangars, so when a catapult is present the
    //'superheavy' fighters declaration must be excluded from the leftover-shuttle
    //math (mirrors HangarOps::populateInitialHangarUsage skipping 'superheavy'
    //from $totalDeclared when $hasCatapult).
    shipHasCatapult: function shipHasCatapult(ship) {
        if (!ship || !ship.systems) return false;
        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (s && (s.name == "catapult" || s.isCatapult)) return true;
        }
        return false;
    },

    //Fighter Rails mirror the catapult exclusion: rail boxes are structure HP
    //(already excluded from getTotalHangarCapacity, which sums only name=="hangar"),
    //and the rail-borne fighter category (e.g. "light") must be excluded from the
    //declared-shuttle-pool sum so leftover-shuttle math matches the server's
    //HangarOps::getDefaultShuttles. Mirrors HangarOps::shipHasRail / railFighterCategories.
    shipHasRail: function shipHasRail(ship) {
        if (!ship || !ship.systems) return false;
        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (s && (s.name == "fighterRail" || s.isRail)) return true;
        }
        return false;
    },

    //Mirror of HangarOps::excludesDefaultShuttles: a hangar flagged
    //excludeFromDefaultShuttles (e.g. ScoravarefittedAM's heavy-fighter bay)
    //is steered OUT of the default-shuttle distribution — its boxes still
    //count toward total capacity (so the leftover COUNT is unchanged) but the
    //shuttles pile into the other hangar(s). Catapults/rails are excluded from
    //the pool wholesale elsewhere and never reach this flag.
    excludesDefaultShuttles: function excludesDefaultShuttles(hangar) {
        if (!hangar) return false;
        if (hangar.isCatapult || hangar.isRail || hangar.isLCVRail) return false;
        if (hangar.excludeFromDefaultShuttles) return true;
        //Mirror of the server: a per-bay fighter-class allow-list that doesn't
        //permit any default-shuttle class steers default shuttles away, so the
        //auto-fill never blocks the reserved fighter (e.g. Reska-only Suom bays).
        //The full PHP subclass hierarchy isn't visible client-side, so match the
        //known default-shuttle phpclasses by name.
        var allowed = hangar.allowedFighterClasses;
        if (Array.isArray(allowed) && allowed.length > 0) {
            var shuttleClasses = { "Shuttle": 1, "MinesweepingShuttle": 1, "CargoShuttle": 1, "MedicalShuttle": 1, "Flyer": 1, "FlyerProtectorate": 1 };
            for (var i = 0; i < allowed.length; i++) {
                if (shuttleClasses[allowed[i]]) return false;   //bay accepts a shuttle class, keep it in the pool
            }
            return true;
        }
        return false;
    },

    //Lowercased set of ship.fighters category keys (e.g. "light") that ride this
    //ship's rails. Returns a plain object used as a membership set.
    railFighterCategories: function railFighterCategories(ship) {
        var cats = {};
        if (!ship || !ship.systems) return cats;
        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (s && (s.name == "fighterRail" || s.isRail)) {
                var cat = String(s.hangarType || "").toLowerCase().trim();
                if (cat !== "") cats[cat] = true;
            }
        }
        return cats;
    },

    //Sum of declared fighters that consume default-shuttle-pool hangar boxes.
    //Excludes catapult-destined 'superheavy' fighters when the ship has a
    //catapult. Single source of truth for getDefaultShuttles / Composition.
    //Per B5W §10.1, ultralight fighters fit two per box (0.5 each); ceil()
    //so an odd ultralight count doesn't yield a free half-box. Mirrors
    //HangarOps::shuttlePoolBoxesFor on the server.
    getShuttlePoolDeclared: function getShuttlePoolDeclared(fighters, ship) {
        var declared = 0;
        if (!fighters) return 0;
        var hasCatapult = shipManager.systems.shipHasCatapult(ship);
        var railCategories = shipManager.systems.railFighterCategories(ship);
        for (var k in fighters) {
            var key = String(k).toLowerCase().trim();
            if (hasCatapult && key === "superheavy") continue;
            if (railCategories[key]) continue;   //rail-borne fighters aren't shuttle pool
            if (key === "lcvs") continue;        //LCVs ride LCV rails, not the shuttle pool
            var count = parseInt(fighters[k], 10) || 0;
            if (count <= 0) continue;
            declared += (key === "ultralight") ? Math.ceil(count / 2) : count;
        }
        return declared;
    },

    //Mirrors HangarOps::populateInitialHangarUsage step 3: any hangar capacity
    //that isn't accounted for by entries in ship.fighters auto-fills with the
    //faction-appropriate default shuttle (or MinesweepingShuttles when
    //minesweeperbonus > 0). The hangar SLOT key stays "shuttles" regardless
    //of faction — Flyers / Shuttles / armed variants all compete for the
    //same pool in checkChoices.
    //Returns {count, type, key} where:
    //  type — display string ("Shuttles" / "Flyers" / "Minesweeping Shuttles")
    //  key  — matching ship.fighters slot key ("shuttles" / "minesweeping shuttles")
    //  count — leftover slot count (>= 0)
    getDefaultShuttles: function getDefaultShuttles(ship) {
        if (shipManager.systems.getTotalHangarCapacity(ship) <= 0) {
            return { count: 0, type: "Shuttles", key: "shuttles" };
        }
        var capacity = shipManager.systems.getTotalHangarCapacity(ship);
        var declared = shipManager.systems.getShuttlePoolDeclared(ship.fighters, ship);
        var leftover = capacity - declared;
        if (leftover < 0) leftover = 0;
        //Explicit "minesweeping shuttles" in ship.fighters is the designer's
        //authoritative MSW count — leftover falls through to the faction shuttle
        //even on minesweeper-bonus carriers, matching HangarOps::populateInitialHangarUsage.
        var hasExplicitMsw = !!(ship.fighters && parseInt(ship.fighters["minesweeping shuttles"], 10) > 0);
        var minesweeper = !!(ship.minesweeperbonus && parseInt(ship.minesweeperbonus, 10) > 0);
        if (minesweeper && !hasExplicitMsw) {
            return { count: leftover, type: "Minesweeping Shuttles", key: "minesweeping shuttles" };
        }
        return {
            count: leftover,
            type: shipManager.systems.factionDefaultShuttleLabel(ship),
            key: "shuttles"
        };
    },

    //Picks the single Hangar system that should display the ship's default
    //shuttle load (see getDefaultShuttles). Prefers a hangar in the primary
    //section (location 0); otherwise the first hangar found. Returns the
    //system, or null if the ship has no hangars.
    getDefaultShuttleHangar: function getDefaultShuttleHangar(ship) {
        if (!ship || !ship.systems) return null;
        var firstHangar = null;
        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (!system || system.name != "hangar") continue;
            if (firstHangar === null) firstHangar = system;
            if (system.location == 0) return system;
        }
        return firstHangar;
    },

    //Find a fighter-flight blueprint by phpclass from whatever client-side ship
    //catalogue is populated. Two different catalogues exist depending on entry
    //point — both are checked so the allow-list display works in BOTH contexts:
    //  - In-game (game.php): window.staticShips, keyed faction -> phpclass ->
    //    blueprint. A carrier's allowedFighterClasses are preloaded there even
    //    when no such flight is deployed (game.php's Hangar preload loop).
    //  - Lobby (gamelobby.php): window.staticShips is NOT populated; ships live
    //    in gamedata.allShips[faction] as an array of Ship objects, loaded per
    //    faction on demand. The carrier's faction is always already open (you're
    //    looking at its ship window), so its reserved fighter is in the same
    //    faction array.
    //Returns the blueprint/Ship object or null when not yet loaded.
    findFighterBlueprint: function findFighterBlueprint(phpclass) {
        if (!phpclass) return null;
        if (window.staticShips) {
            for (var faction in window.staticShips) {
                var bp = window.staticShips[faction] && window.staticShips[faction][phpclass];
                if (bp) return bp;
            }
        }
        if (window.gamedata && gamedata.allShips) {
            for (var fac in gamedata.allShips) {
                var list = gamedata.allShips[fac];
                if (!Array.isArray(list)) continue;
                for (var i = 0; i < list.length; i++) {
                    if (list[i] && list[i].phpclass == phpclass) return list[i];
                }
            }
        }
        return null;
    },

    //Resolve a fighter-flight phpclass (e.g. "gaimReskaFighter") to a friendly
    //display label. Prefer the sample fighter's own displayName ("Reska") — the
    //craft inside the flight at systems[1], matching the server's
    //getSampleFighter() and SystemInfo.js's flight-name display. Fall back to the
    //flight's shipClass ("Reska Light flight") with a trailing " flight" stripped,
    //then to the phpclass verbatim, so callers degrade gracefully whatever the
    //blueprint shape (lobby Ship object: systems is an array; in-game JSON
    //blueprint: systems is an object keyed by id).
    //Used by the per-bay fighter-class allow-list display (Hangar systemInfo
    //"Type:" line + the lobby ship-window fighter complement).
    displayNameForFighterClass: function displayNameForFighterClass(phpclass) {
        if (!phpclass) return "";
        var bp = shipManager.systems.findFighterBlueprint(phpclass);
        if (bp) {
            //systems[1] is the sample craft in both shapes (object key "1" or
            //array index 1). Guard the lazy getter / missing slot defensively.
            var sample = bp.systems ? bp.systems[1] : null;
            if (sample && sample.displayName) return String(sample.displayName);
            if (bp.shipClass) return String(bp.shipClass).replace(/\s+flight$/i, "");
        }
        return phpclass;
    },

    //Classify a fighter-flight blueprint into a ship.fighters size category key
    //("light"/"medium"/"heavy"/"ultralight" or an explicit hangarRequired).
    //Mirrors the jinkinglimit classification in gamelobby.js checkChoices() so a
    //reserved-fighter row lands on the same $fighters size slot the carrier
    //declares (e.g. Reska jinkinglimit 10 -> "light", matching gaimSuom's
    //fighters.light). Returns null when the class isn't loaded.
    fighterSizeCategory: function fighterSizeCategory(phpclass) {
        var bp = shipManager.systems.findFighterBlueprint(phpclass);
        if (!bp) return null;
        if (bp.hangarRequired && bp.hangarRequired != 'fighters') return String(bp.hangarRequired);
        var jl = parseInt(bp.jinkinglimit || 0, 10);
        if (jl >= 99) return 'ultralight';
        if (jl >= 10) return 'light';
        if (jl >= 8)  return 'medium';
        if (jl >= 6)  return 'heavy';
        return null;
    },

    //Per-bay fighter-class allow-list display (arch_hangar_class_allowlist): the
    //reserved-fighter complement of a carrier, derived from its class-restricted
    //hangars. Returns an array of {category, phpclass, displayName, count} where
    //count is the bay's box capacity (1 box = 1 light/medium/heavy fighter; this
    //display path doesn't carry ultralight/superheavy reservers today). Pure
    //display: ship.fighters stays size-keyed and drives all accounting — this
    //just lets the lobby ship window name the specific fighter a reserved bay
    //will hold instead of the generic "N Light Fighters".
    //Cheap pre-check: does this ship have ANY hangar bay with a non-empty
    //allowedFighterClasses list? Restricted bays are rare in FV, so this lets the
    //common case (no restricted bay) skip getReservedFighterComposition and its
    //per-class blueprint lookups entirely. Result is memoised on the ship object
    //(ships are rebuilt per faction-load, so the cache can't go stale) so a
    //carrier's window can be reopened without re-walking systems each time.
    shipHasRestrictedHangar: function shipHasRestrictedHangar(ship) {
        if (!ship || !ship.systems) return false;
        if (ship._hasRestrictedHangar !== undefined) return ship._hasRestrictedHangar;
        var found = false;
        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (s && s.name == "hangar" && Array.isArray(s.allowedFighterClasses) && s.allowedFighterClasses.length > 0) {
                found = true;
                break;
            }
        }
        ship._hasRestrictedHangar = found;
        return found;
    },

    getReservedFighterComposition: function getReservedFighterComposition(ship) {
        var rows = [];
        if (!shipManager.systems.shipHasRestrictedHangar(ship)) return rows;
        for (var i in ship.systems) {
            var s = ship.systems[i];
            if (!s || s.name != "hangar") continue;
            if (s.isCatapult || s.isRail || s.isLCVRail || s.isShadowHangar) continue;
            var allowed = s.allowedFighterClasses;
            if (!Array.isArray(allowed) || allowed.length == 0) continue;
            //One reserved row per bay, attributed to its first allowed class —
            //first-fit bays each name exactly one fighter (e.g. the Suom's
            //Reska-only aft bay). A multi-class allow-list (none today) would
            //list the first; extend here if that case ever ships.
            var phpclass = allowed[0];
            var category = shipManager.systems.fighterSizeCategory(phpclass);
            if (!category) continue;   //unknown size — leave the generic line intact
            rows.push({
                category: category,
                phpclass: phpclass,
                displayName: shipManager.systems.displayNameForFighterClass(phpclass),
                count: parseInt(s.maxhealth || 0, 10)
            });
        }
        return rows;
    },

    //Display label for a ship's auto-populated default shuttle. Mirrors
    //HangarOps::factionShuttleClass on the server — extend this switch in
    //lockstep whenever a faction-specific default shuttle subclass is added
    //(e.g. Flyer for Minbari). Plural form, used in the ship-info window
    //and in the gamelobby fleet check report.
    factionDefaultShuttleLabel: function factionDefaultShuttleLabel(ship) {
        var faction = ship && ship.faction ? String(ship.faction) : "";
        switch (faction) {
            case "Minbari Federation":
            case "Minbari Protectorate":
                return "Cargo Flyers";
            default:
                return "Shuttles";
        }
    },

    //Post-enhancement breakdown of the auto-populated default shuttle pool, for
    //display only (Hangar systemInfo tooltip, ship-info window). Applies the two
    //shuttle-slot lobby enhancements:
    //  HANG_BP  — converts N default slots into dedicated Breaching Pod slots.
    //  HANG_MSW — retypes N default shuttles as Minesweeping Shuttles (no
    //             capacity change; gated to non-minesweeper carriers server-side).
    //Returns an ordered array of {type, count} rows (empty when no default pool).
    //
    //The pool size is read from a Breaching-Pod-clean fighters base: the
    //gamelobby fleet check bakes HANG_BP into ship.fighters["Breaching Pods"]
    //(snapshotting the pre-bake shape in ship._originalFighters), so using
    //_originalFighters when present keeps this correct regardless of whether the
    //fleet check has run — and avoids double-subtracting the converted slots.
    getDefaultShuttleComposition: function getDefaultShuttleComposition(ship) {
        var rows = [];
        var capacity = shipManager.systems.getTotalHangarCapacity(ship);
        if (capacity <= 0) return rows;
        var base = ship._originalFighters || ship.fighters || {};
        var declared = shipManager.systems.getShuttlePoolDeclared(base, ship);

        //Explicit shuttle-category declarations ("shuttles", "minesweeping shuttles",
        //"cargo shuttles") are auto-populated free shuttles per HangarOps step 1 —
        //not purchasable slots like combat-fighter declarations. Surface them as
        //composition rows so the Hangar tooltip reflects the full auto-populated
        //picture (declared + leftover), not just the leftover. Cargo shuttles are
        //opt-in only — they never join the leftover-fill, so they show up here only
        //when declared.
        var declaredMsw = parseInt(base["minesweeping shuttles"], 10) || 0;
        var declaredShuttle = parseInt(base["shuttles"], 10) || 0;
        var declaredCargo = parseInt(base["cargo shuttles"], 10) || 0;
        var declaredMedical = parseInt(base["medical shuttles"], 10) || 0; 
        var declaredLifeboats = parseInt(base["lifeboats"], 10) || 0;                           
        if (declaredMsw > 0) rows.push({ type: "Minesweeping Shuttles", count: declaredMsw });
        if (declaredShuttle > 0) rows.push({ type: shipManager.systems.factionDefaultShuttleLabel(ship), count: declaredShuttle });
        if (declaredCargo > 0) rows.push({ type: "Cargo Shuttles", count: declaredCargo });
        if (declaredMedical > 0) rows.push({ type: "Medical Shuttles", count: declaredMedical }); 
        if (declaredLifeboats > 0) rows.push({ type: "Lifeboats", count: declaredLifeboats });                

        var pool = capacity - declared;
        if (pool <= 0) return rows;

        var bp = 0, msw = 0;
        if (ship.enhancementOptions) {
            for (var e in ship.enhancementOptions) {
                var id = ship.enhancementOptions[e][0];
                var n = parseInt(ship.enhancementOptions[e][2], 10) || 0;
                if (n <= 0) continue;
                if (id === "HANG_BP") bp += n;
                else if (id === "HANG_MSW") msw += n;
            }
        }
        if (bp > pool) bp = pool;          //can't convert more slots than exist
        var afterBP = pool - bp;           //BP conversion removes slots from the pool
        if (msw > afterBP) msw = afterBP;  //MSW retypes within the remaining pool

        //Explicit "minesweeping shuttles" in ship.fighters is the designer's
        //authoritative MSW count — the leftover pool falls through to the faction
        //shuttle even on minesweeper-bonus carriers, matching HangarOps step 2.
        var hasExplicitMsw = !!(base && parseInt(base["minesweeping shuttles"], 10) > 0);
        var minesweeper = !!(ship.minesweeperbonus && parseInt(ship.minesweeperbonus, 10) > 0);
        if (minesweeper && !hasExplicitMsw) {
            //Default pool is already MinesweepingShuttle; HANG_MSW is a no-op here.
            if (afterBP > 0) rows.push({ type: "Minesweeping Shuttles", count: afterBP });
        } else {
            var plain = afterBP - msw;
            if (plain > 0) rows.push({ type: shipManager.systems.factionDefaultShuttleLabel(ship), count: plain });
            if (msw > 0) rows.push({ type: "Minesweeping Shuttles", count: msw });
        }
        //HANG_BP only converts a hangar slot to be BP-capable; the pod unit is
        //bought separately. slotOnly flags this as available capacity, not a
        //present unit, so the in-game Hangar tooltip can suppress it while the
        //lobby loadout (shipwindow) still reflects the converted slot.
        if (bp > 0) rows.push({ type: "Breaching Pods", count: bp, slotOnly: true });
        return rows;
    },

    //Per-hangar slice of getDefaultShuttleComposition for the lobby/system-info
    //tooltip — mirrors HangarOps::populateInitialHangarUsage so a hangar's
    //tooltip in the lobby matches the in-game initial population. Each
    //composition row is distributed with the same least-used + fair-share
    //algorithm as pickHangarForShuttle + fairShareCap on the server, over the
    //distribution set (HangarOps::distributionHangars):
    //
    //  • Primary-structure hangars present → the pool splits evenly across the
    //    primary (location 0) hangars (Pirocia's three → 2+2+2); a lone primary
    //    still shows the whole pool, and side hangars show only overflow.
    //  • No primary hangar (e.g. Marata: two side hangars) → the pool splits
    //    evenly across all hangars (6 default shuttles → 3+3, not 6+0).
    getDefaultShuttleCompositionForHangar: function getDefaultShuttleCompositionForHangar(ship, targetHangar) {
        if (!ship || !targetHangar || !ship.systems) return [];
        var hangars = [];
        for (var si in ship.systems) {
            var s = ship.systems[si];
            //ShadowHangars (integrated-fighter bays) never hold default shuttles —
            //excluded server-side in HangarOps too. Catapults already excluded here.
            if (s && s.name == "hangar" && !s.isCatapult && !s.isShadowHangar) hangars.push(s);
        }
        if (hangars.length === 0) return [];

        var fullRows = shipManager.systems.getDefaultShuttleComposition(ship);
        if (fullRows.length === 0) return [];

        //Distribution set mirrors HangarOps::distributionHangars on the server:
        //the primary (location 0) hangars if the ship has any, else every hangar.
        //Several primary hangars (e.g. Pirocia's three) share the pool evenly
        //(2+2+2); a lone primary still shows the whole pool. Side hangars on a
        //ship that has primaries get only overflow once the primaries are full.
        //
        //$excludeFromDefaultShuttles bays are dropped from the eligible set
        //first (mirrors HangarOps::distributionHangars / excludesDefaultShuttles):
        //their boxes still count in capacity, but shuttles steer to the other
        //hangar(s). Guarded so a ship whose hangars are ALL flagged still places
        //shuttles somewhere (fall back to treating none as excluded).
        var anyEligible = false;
        for (var ei = 0; ei < hangars.length; ei++) {
            if (!shipManager.systems.excludesDefaultShuttles(hangars[ei])) { anyEligible = true; break; }
        }
        var eligible = function (hgr) {
            return anyEligible ? !shipManager.systems.excludesDefaultShuttles(hgr) : true;
        };
        //Shuttle-only narrowing (mirrors HangarOps::isShuttleOnlyHangar): if any
        //eligible bay is explicitly shuttle-tagged (hangarType 'shuttles'/
        //'minesweeping shuttles' — e.g. Vree Xeecra/Xaarix/Vyreel's small shuttle
        //bay next to big fighter bays, or EA Babylon5's front shuttle bay), the
        //default-shuttle pool prefers those bays only, so the fighter bays stay
        //free. The general bays still get overflow once the shuttle bays fill.
        //
        //This narrowing takes PRECEDENCE over the primary-section narrowing
        //below (mirrors HangarOps::distributionHangars): a dedicated shuttle bay
        //wins the pool no matter which section it sits on, so Babylon5's
        //front-section ('shuttles') bay isn't stranded by the location-0 fighter
        //bay. The Vree/Kowart, whose shuttle-only bay is already on the primary,
        //resolve to the same bay either way.
        var isShuttleOnly = function (hgr) {
            if (hgr.isCatapult || hgr.isRail) return false;
            var t = ("" + (hgr.hangarType || "")).toLowerCase().trim();
            return t === "shuttles" || t === "minesweeping shuttles";
        };
        var hasShuttleOnly = false;
        for (var sh = 0; sh < hangars.length; sh++) {
            if (eligible(hangars[sh]) && isShuttleOnly(hangars[sh])) { hasShuttleOnly = true; break; }
        }

        //Primary-section narrowing only applies when there's no shuttle-only bay
        //to claim the pool: prefer the location-0 hangars if the ship has any
        //eligible ones, else every eligible hangar.
        var hasPrimary = false;
        if (!hasShuttleOnly) {
            for (var h = 0; h < hangars.length; h++) {
                if (eligible(hangars[h]) && parseInt(hangars[h].location, 10) === 0) { hasPrimary = true; break; }
            }
        }
        var inSet = function (hgr) {
            if (!eligible(hgr)) return false;
            return hasPrimary ? (parseInt(hgr.location, 10) === 0) : true;
        };

        var per = [];
        for (var p = 0; p < hangars.length; p++) {
            var isPref = hasShuttleOnly ? (eligible(hangars[p]) && isShuttleOnly(hangars[p])) : inSet(hangars[p]);
            per.push({
                id: hangars[p].id,
                max: parseInt(hangars[p].maxhealth, 10) || 0,
                usage: 0,
                rows: [],
                excluded: !eligible(hangars[p]),
                pref: isPref,
                //Overflow bays: eligible and NOT in the preferred distribution set
                //(mirrors HangarOps::overflowHangars). The spillover spreads evenly
                //across these once the preferred bays fill — e.g. Babylon5 Refit's
                //4 spill shuttles → 2+2 across its two primary fighter bays.
                overflow: eligible(hangars[p]) && !isPref
            });
        }

        //Least-used hangar in the distribution set; once that's full, overflow to
        //the LEAST-USED overflow bay so spillover spreads evenly (mirrors
        //pickHangarForShuttle + overflowHangars).
        var pickIdx = function (flightSize) {
            var bestIdx = -1, bestUsage = Infinity;
            for (var i = 0; i < per.length; i++) {
                if (!per[i].pref) continue;
                if (per[i].max - per[i].usage < flightSize) continue;
                if (per[i].usage < bestUsage) { bestUsage = per[i].usage; bestIdx = i; }
            }
            if (bestIdx !== -1) return bestIdx;
            //Overflow: least-used eligible non-pref bay (excluded bays never
            //receive shuttles even once the preferred ones fill).
            bestIdx = -1; bestUsage = Infinity;
            for (var j = 0; j < per.length; j++) {
                if (!per[j].overflow) continue;
                if (per[j].max - per[j].usage < flightSize) continue;
                if (per[j].usage < bestUsage) { bestUsage = per[j].usage; bestIdx = j; }
            }
            return bestIdx;
        };

        //Fair-share cap over the distribution-set hangars with room (mirrors
        //fairShareCap): Infinity when only one remains so it takes the rest. Once
        //the preferred set is full, fall through to the overflow bays so spillover
        //also rounds out evenly.
        var fairCap = function (remaining) {
            var withRoom = 0;
            for (var i = 0; i < per.length; i++) {
                if (per[i].pref && per[i].max - per[i].usage > 0) withRoom++;
            }
            if (withRoom === 0) {
                for (var o = 0; o < per.length; o++) {
                    if (per[o].overflow && per[o].max - per[o].usage > 0) withRoom++;
                }
            }
            if (withRoom <= 1) return Infinity;
            return Math.ceil(remaining / withRoom);
        };

        for (var r = 0; r < fullRows.length; r++) {
            var row = fullRows[r];
            var count = parseInt(row.count, 10) || 0;
            while (count > 0) {
                var idx = pickIdx(1);
                if (idx < 0) break;
                var free = per[idx].max - per[idx].usage;
                var take = Math.min(count, free, fairCap(count));
                if (take <= 0) break;
                var existing = null;
                for (var er = 0; er < per[idx].rows.length; er++) {
                    if (per[idx].rows[er].type === row.type && !!per[idx].rows[er].slotOnly === !!row.slotOnly) {
                        existing = per[idx].rows[er];
                        break;
                    }
                }
                if (existing) existing.count += take;
                else per[idx].rows.push({ type: row.type, count: take, slotOnly: row.slotOnly });
                per[idx].usage += take;
                count -= take;
            }
        }

        for (var k = 0; k < per.length; k++) {
            if (per[k].id == targetHangar.id) return per[k].rows;
        }
        return [];
    },

    getThrusters: function getThrusters(ship, direction) {
        var list = Array();
        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (system.name == "thruster" && system.direction == direction) list.push(system);
        }

        return list;
    },

    getTotalDamage: function getTotalDamage(system) {
        var total = 0;

        for (var i = 0; i < system.damage.length; i++) {
            var damage = system.damage[i].damage - system.damage[i].armour;
            //if (damage > 0) {// healing is a thing!
            total += damage;
            //}
        }

        return total;
    },

    //Looks for ships with Hyach Computer and lists any where the balance of BFCP is negative for error message.
    getNegativeBFCP: function getNegativeBFCP() {
        var shipNames = new Array();
        var counter = 0;
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            var deployTurn = shipManager.getTurnDeployed(ship);
            if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
            if (deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.
            if (ship.unavailable) continue;
            if (ship.flight) continue;
            if (ship.userid != gamedata.thisplayer) continue;
            if (!(shipManager.systems.getSystemByName(ship, "hyachComputer"))) continue; //Does it have a computer?
            if (shipManager.isDestroyed(ship)) continue;
            var computer = (shipManager.systems.getSystemByName(ship, "hyachComputer"));
            if (shipManager.systems.isDestroyed(ship, computer)) continue;
            if (computer.BFCPtotal_used > computer.output) { //Is the total BFCP used greater than output and Computer NOT destroyed, usually due to damage to Computer.
                shipNames[counter] = ship.name;
                counter++;
            }
        }
        return shipNames;
    },	//endof getNegativeBFCP

    //Looks for ships with Hyach Specialists and lists any where these have not been selected in Deployment Phase.
    getUnusedSpecialists: function getUnusedSpecialists() {
        var shipNames = new Array();
        var counter = 0;

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
            var deployTurn = shipManager.getTurnDeployed(ship);
            if (deployTurn !== gamedata.turn) continue;   //Don't bother checking for ships that haven't deployed yet.

            if (shipManager.isDestroyed(ship)) continue;
            if (ship.unavailable) continue;
            if (ship.flight) continue;
            if (ship.userid != gamedata.thisplayer) continue;
            if (!(shipManager.systems.getSystemByName(ship, "hyachSpecialists"))) continue; //Does it Specialists?

            var specialists = (shipManager.systems.getSystemByName(ship, "hyachSpecialists"));
            if (specialists.canSelectAnything()) { //Can anymore Specialists be selected?
                shipNames[counter] = ship.name;
                counter++;
            }
        }

        return shipNames;
    },	//endof getUnusedSpecialists

    // Looks for ships with Thirdspace Shield Generators or ThoughtShieldGenerators and compiles a list of any with negative capacity.
    checkShieldGenValue: function checkShieldGenValue() {
        var shipNames = [];
        var counter = 0;
        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
            if (ship.unavailable) continue;
            if (ship.flight) continue;
            if (ship.userid != gamedata.thisplayer) continue;

            var deployTurn = shipManager.getTurnDeployed(ship);
            if (deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.

            // Check for either ThirdspaceShieldGenerator or ThoughtShieldGenerator
            var generator = shipManager.systems.getSystemByName(ship, "ThirdspaceShieldGenerator") ||
                shipManager.systems.getSystemByName(ship, "ThoughtShieldGenerator");
            if (!generator) continue; // No generator found

            if (shipManager.isDestroyed(ship)) continue;
            if (shipManager.systems.isDestroyed(ship, generator)) continue;

            if (generator.storedCapacity != 0) { // Generator is not zero, either too much or too little shield allocation.
                shipNames[counter] = ship.name;
                counter++;
            }
        }
        return shipNames;
    }, // end of checkShieldGenValue

    getSystemListThrustBoosted: function getSystemListThrustBoosted(ship) { //For Nexus PLasma Charge, but coulod be used for other Thrust-boosted system - DK 25.3.24
        var toReturn = Array();
        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (system.thrustBoosted) {
                toReturn.push(system);
            }
        }

        return toReturn;
    },


    getSystemListEWBoosted: function getSystemListEWBoosted(ship) { //Instead of listing weapons like Psionic Lances separately, call one function - DK 25.3.24
        var toReturn = Array();
        for (var i in ship.systems) {
            var system = ship.systems[i];

            if (system.ewBoosted) {
                toReturn.push(system);
            }
        }

        return toReturn;
    },

    hasBorderHighlight: function hasBorderHighlight(ship, system) {
        // Try to prioritise effects and optimise performance. Can only return ONE border highlight colour.

        // Check Abbai faction-specific conditions
        if (ship.faction === "Abbai Matriarchate") {
            const mayOverheat = shipManager.criticals.countCriticalOnTurn(system, "MayOverheat", gamedata.turn);
            if (mayOverheat === 2) return 'Red';
            // Uncomment if orange highlight for "MayOverheat === 1" is required
            // if (mayOverheat === 1) return 'orange';
        }

        // Check CnC critical effects (most important first)
        if (system.name === "cnC") {
            const cnCCrits = shipManager.criticals.getAllCriticals(system, gamedata.turn);
            for (const crit of cnCCrits) {
                if (["Sabotage", "SabotageElite", "CaptureShip", "CaptureShipElite",
                    "RescueMission", "RescueMissionElite", "DefenderLost"].includes(crit.phpclass)) {
                    return 'Orange';
                }
            }
        }

        // Check critical effects for the current system
        const allCrits = shipManager.criticals.getAllCriticals(system, gamedata.turn);
        for (const crit of allCrits) {
            // Prioritise red effects
            if (["Sabotage", "SabotageElite", "LimpetBore"].includes(crit.phpclass)) {
                return 'Red';
            }
            // Check orange effects
            if (["LimpetBoreTravelling", "MayOverheat"].includes(crit.phpclass)) {
                return 'Orange';
            }
        }

        // Check for overloading systems
        if (shipManager.power.isOverloading(ship, system)){
            return 'Yellow';
        }else if(system.name == "PlasmaBattery"){
            //if(system.output > 0 && gamedata.gamephase > 1){
            if(system.output > 0){            
                return 'Yellow';
            }                
        }

        // No highlight if none of the conditions are met
        return null;
    },


    getRemainingHealth: function getRemainingHealth(system) {
        var damage = shipManager.systems.getTotalDamage(system);
        var max = system.maxhealth;

        var rem = max - damage;

        if (rem < 0) {
            rem = 0;
        }

        return rem;
    }

};