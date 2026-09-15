'use strict';

/* WALKERS OF SIGMA-957 - Chromatic Pulse Driver shield adaptation (WALKERS_OF_SIGMA_PLAN.md 3.4).
 *
 * Client mirror of CpdScanRegistry::applyToShieldBucket (server/lib/CpdScanRegistry.php). A CPD
 * scanning hit teaches the SHOOTER's team about the TARGET's race, and from the next turn that
 * team's shots see that race's shields as N points weaker. The server applies it to the aggregated
 * per-defensive-type bucket rather than inside each shield class, and this does the same, in the
 * same place, so the previewed hit chance and the rolled one cannot drift.
 *
 * ⚠️ Mirrors the SERVER's arithmetic exactly: only the "Shield" bucket, only when it is positive,
 * clamped at 0.
 *
 * ⭐ AND IT MIRRORS THE SERVER'S GATE TOO. `gamedata.cpdAdaptation` is absent unless a scan has
 * actually landed - the server omits it rather than sending an empty map, off the same
 * TacGamedata::$cpdAdaptationPresent boolean the four server-side call sites test. So BOTH callers
 * below test it before calling, and an ordinary game pays one property read per hit-chance preview
 * and never enters this function. The checks inside it are a second line of defence for anything
 * that calls it directly, not the gate - keep the call sites guarded.
 *
 * Both `team` and `faction` are published on every stripped ship. A chameleon-disguised ship
 * publishes its DISGUISED faction, so a fleet that has adapted to the disguise reads no benefit
 * here - which is the deception working, not a bug.
 *
 * ⚠️ SHAPED DIFFERENTLY FROM ITS PHP TWIN, on purpose. applyToShieldBucket() RETURNS the modified
 * array, because a PHP array is a value. A JS array is a reference, so this one mutates in place
 * and returns THE NUMBER OF POINTS IT ACTUALLY REMOVED - which the hit-chance tooltip needs in
 * order to show the target's real shielding and the scan's credit on two separate lines.
 *
 * ⭐⭐ "ACTUALLY REMOVED" IS NOT "points". The clamp at 0 means 3 points of adaptation against a
 * 1-point shield removes 1, not 3. Reporting `points` would put a -15% line in a tooltip whose
 * total only moved 5%, and calculateHitChange's sum-equals-hitChance invariant would start
 * warning. Always return the difference the bucket actually saw. */
function cpdApplyShieldAdaptation(affectingSystems, target, shooter) {
    if (typeof gamedata === 'undefined' || !gamedata.cpdAdaptation) return 0;
    if (!target || !shooter) return 0;
    if (!(affectingSystems['Shield'] > 0)) return 0;

    var byFaction = gamedata.cpdAdaptation[shooter.team];
    if (!byFaction) return 0;

    var points = byFaction[target.faction];
    if (!(points > 0)) return 0;

    var before = affectingSystems['Shield'];
    var after = Math.max(0, before - points);
    affectingSystems['Shield'] = after;
    return before - after;
}

var Ship = function Ship(json) {
    var inputSystems = null;
    var staticSystems = null;

    if (window.staticShips && window.staticShips[json.faction] && window.staticShips[json.faction][json.phpclass]) {
        var staticShip = window.staticShips[json.faction][json.phpclass];
        Object.keys(staticShip).forEach(function (key) {
            if (key !== 'systems') {
                if (staticShip[key] !== null && typeof staticShip[key] === 'object') {
                    // Deep clone arrays and objects to prevent shared references
                    this[key] = JSON.parse(JSON.stringify(staticShip[key]));
                } else {
                    this[key] = staticShip[key]; // Copy other props
                }
            } else {
                staticSystems = staticShip[key]; // Preserve static systems
            }
        }, this)
    }

    for (var i in json) {
        if (i == 'systems') {
            inputSystems = json[i];
        } else {
            this[i] = json[i];
        }
    }

    this.hexOffsets = json.hexOffsets || this.hexOffsets || null;

    // Optimization: Server omits empty/default ship-level properties.
    if (this.EW === undefined || this.EW === null) this.EW = [];
    if (this.spawned === undefined) this.spawned = -1;
    if (this.skinDancing === undefined) this.skinDancing = false;
    if (this.hasAttached === undefined || this.hasAttached === false) this.hasAttached = {};
    if (this.attached === undefined || this.attached === false) this.attached = {};

    if (json.enhancementOptions === undefined && (window.gamedata && window.gamedata.status !== 'LOBBY')) {
        this.enhancementOptions = [];
    }

    // If we have any system data, proceed
    var systemsToLoad = inputSystems || staticSystems;

    if (systemsToLoad) {
        Object.defineProperty(this, 'systems', {
            configurable: true,
            enumerable: true,
            get: function () {
                if (this._initializingSystems) return undefined;
                this._initializingSystems = true;

                try {
                    // Pass explicit staticSystems to factory to allow merging
                    // without needing to access this.systems (which would recurse)
                    var parsed = SystemFactory.createSystemsFromJson(systemsToLoad, this, null, staticSystems);

                    Object.defineProperty(this, 'systems', {
                        value: parsed,
                        enumerable: true,
                        writable: true
                    });

                    // Stage 21: each Hangar computes its tooltip in its OWN
                    // constructor, before sibling hangars exist — so a multi-bay
                    // docked flight's foreign occupancy (boxes it places on OTHER
                    // bays) wasn't counted, leaving those bays reading 0 capacity.
                    // Now that every system is built AND cached on `this.systems`,
                    // recompute each hangar's tooltip so per-bay capacity is
                    // occupancy-accurate across all bays. (Must run after the
                    // defineProperty above so the helpers can read this.ship.systems.)
                    for (var hi in parsed) {
                        var hsys = parsed[hi];
                        if (hsys && (hsys.name === 'hangar' || hsys.name === 'fighterRail' || hsys.name === 'catapult')
                            && typeof hsys.refreshHangarTooltip === 'function') {
                            hsys.refreshHangarTooltip();
                        }
                    }
                    return parsed;
                } finally {
                    delete this._initializingSystems;
                }
            },
            set: function (val) {
                Object.defineProperty(this, 'systems', {
                    value: val,
                    enumerable: true,
                    writable: true
                });
            }
        });
    }
};

Ship.prototype = {
    constructor: Ship,

    /* launchPos (optional): the ballistic's launch hex for THIS ORDER, which is what
       checkIsValidAffectingSystem tests arc-limited defensive systems against. The default below is
       the shooter's start-of-turn hex, and that is wrong for anything whose launch hex is not its
       launcher's - a homing missile on its second or later pass comes in from its target's previous
       hex (HOMING_MISSILE_PLAN.md). The server passes the same value as $posmod.
       Mirrors weaponManager.getFiringHex(shooter, weapon, fireOrder); callers with no order in hand
       pass nothing and get exactly the old answer.

       outDetail (optional): an object this fills in with the parts of the answer a tooltip wants
       spelled out rather than folded into the total. Today that is `shieldAdaptation` - the points
       a Chromatic Pulse Driver scan took off the target's shields, so the breakdown can show the
       real shielding and the scan's credit on separate lines. The RETURN VALUE is unchanged either
       way: it is always the final, adapted sum, so every existing caller and the server mirror are
       untouched. */
    getHitChangeMod: function getHitChangeMod(shooter, weapon, launchPos, outDetail) {
        if (this.flight) return this.getHitChangeModFlight(shooter, weapon, launchPos, outDetail); //separate function for fighter flight - same approach, different loop

        var firingPos = null;
        if (weapon.ballistic) { //ballistic weapon uses position fron start of turn; direct fire weapons use ship itself rather than any position - important at range 0!
            firingPos = launchPos || shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn);
        }

        var affectingSystems = Array();
        for (var i in this.systems) {
            var system = this.systems[i];

            //if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))

            if (!this.checkIsValidAffectingSystem(system, shooter, firingPos)) //Marcin Sawicki: change to unit itself...
                continue;

            /* redirecting - this will be covered by getDefensiveHitChangeMod function itself; it already is in back end!
                        if (system instanceof Shield && mathlib.getDistanceBetweenShipsInHex(shooter, this) === 0 && shooter.flight) {
                            // Shooter is a flight, and the flight is under the shield
                            continue;
                        }
            */

            var mod = system.getDefensiveHitChangeMod(this, shooter, weapon);
            mod = weapon.shieldInteractionDefense(this, shooter, system, mod);

            if (mod > 0) {
                //Advanced Sensors negate positive (eg. reducing profile) defensive systems' effects operated by less advanced races
                if ((this.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors"))) {
                    mod = 0;
                }
            }

            if (!(affectingSystems[system.defensiveType])
                || affectingSystems[system.defensiveType] < mod) {
                affectingSystems[system.defensiveType] = mod;
            }
        }
        /* Chromatic Pulse Driver adaptation - mirrors the server line in BaseShip::getHitChanceMod,
           including its gate: `gamedata.cpdAdaptation` is absent unless a scan has actually landed, so
           an ordinary game pays one property read here and never enters the helper. The helper mutates
           the bucket and hands back what it REALLY removed, which the tooltip shows on its own line. */
        if (window.gamedata && gamedata.cpdAdaptation) {
            var cpdRemoved = cpdApplyShieldAdaptation(affectingSystems, this, shooter);
            if (outDetail && cpdRemoved) outDetail.shieldAdaptation = cpdRemoved;
        }
        var sum = 0;
        for (var i in affectingSystems) {
            sum += affectingSystems[i];
        }
        return sum;
    }, //getHitChangeMod

    //loop through ALL fighters - sample fighter should be enough, but let's loop through all in case of eg. criticals
    //launchPos / outDetail: as getHitChangeMod above.
    getHitChangeModFlight: function getHitChangeModFlight(shooter, weapon, launchPos, outDetail) {
        var firingPos = null;
        if (weapon.ballistic) { //ballistic weapon uses position fron start of turn; direct fire weapons use ship itself rather than any position - important at range 0!
            firingPos = launchPos || shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn);
        }

        var affectingSystems = Array();
        for (var i in this.systems) {
            var fighter = this.systems[i];
            for (var j in fighter.systems) {
                var system = fighter.systems[j];

                //if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))
                if (!this.checkIsValidAffectingSystem(system, shooter, firingPos)) //Marcin Sawicki: change to unit itself...
                    continue;

                var mod = system.getDefensiveHitChangeMod(this, shooter, weapon);
                mod = weapon.shieldInteractionDefense(this, shooter, system, mod);

                if (mod > 0) {
                    //Advanced Sensors negate positive (eg. reducing profile) defensive systems' effects operated by less advanced races
                    if ((this.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors"))) {
                        mod = 0;
                    }
                }

                if (!(affectingSystems[system.defensiveType])
                    || affectingSystems[system.defensiveType] < mod) {
                    affectingSystems[system.defensiveType] = mod;
                }
            }
        }
        /* Chromatic Pulse Driver adaptation - mirrors the server line in BaseShip::getHitChanceMod,
           including its gate: `gamedata.cpdAdaptation` is absent unless a scan has actually landed, so
           an ordinary game pays one property read here and never enters the helper. The helper mutates
           the bucket and hands back what it REALLY removed, which the tooltip shows on its own line. */
        if (window.gamedata && gamedata.cpdAdaptation) {
            var cpdRemoved = cpdApplyShieldAdaptation(affectingSystems, this, shooter);
            if (outDetail && cpdRemoved) outDetail.shieldAdaptation = cpdRemoved;
        }
        var sum = 0;
        for (var i in affectingSystems) {
            sum += affectingSystems[i];
        }
        return sum;
    }, //getHitChangeModFlight


    //Marcin Sawicki: this should use shooter, not pos - OR insert pos only if necessary!
    //otherwise serious trouble at range 0
    //checkIsValidAffectingSystem: function(system, pos)
    checkIsValidAffectingSystem: function checkIsValidAffectingSystem(system, shooter, pos = null) {
        if (!system.defensiveType) return false;

        //If the system was destroyed last turn continue 
        //(If it has been destroyed during this turn, it is still usable)
        if (system.destroyed) return false;

        //If the system is offline either because of a critical or power management, continue
        if (shipManager.power.isOffline(this, system)) return false;

        //if the system has arcs, check that the position is on arc
        if (typeof system.startArc == 'number' && typeof system.endArc == 'number') {

            var tf = shipManager.getShipHeadingAngle(this);

            var heading = 0;

            //get the heading of position, not ship (in case ballistic)
            if (pos !== null) {
                heading = mathlib.getCompassHeadingOfPoint(shipManager.getShipPosition(this), pos);
            } else {
                heading = mathlib.getCompassHeadingOfShip(this, shooter);
            }

            //if not on arc, continue!
            if (!mathlib.isInArc(heading, mathlib.addToDirection(system.startArc, tf), mathlib.addToDirection(system.endArc, tf))) {
                return false;
            }
        }

        return true;
    },

    checkShieldGenerator: function checkShieldGenerator() {
        var shieldCapacity = 0;
        var activeShields = 0;

        for (var i in this.systems) {
            var system = this.systems[i];

            if (system.name == "shieldGenerator") {
                if (system.destroyed || shipManager.power.isOffline(this, system)) {
                    continue;
                }
                shieldCapacity = system.output + shipManager.power.getBoost(system);
            }

            if (system.name == "graviticShield" && !(system.destroyed || shipManager.power.isOffline(this, system))) {
                activeShields = activeShields + 1;
            }
            if (system.name == "abbaiShieldProjector" && !(system.destroyed || shipManager.power.isOffline(this, system))) {
                activeShields = activeShields + 1;
            }
        }

        return shieldCapacity >= activeShields;
    }

};


/*//OLD VERSION - CHANGED DEC 2025
'use strict';

var Ship = function Ship(json) {
    var staticShip = window.staticShips[json.faction][json.phpclass];

    if (!staticShip) {
        throw new Error("Static ship not found for " + json.phpclass)
    }

    Object.keys(staticShip).forEach(function(key) {
        this[key] = staticShip[key];
    }, this)

    for (var i in json) {
        if (i == 'systems') {
            this.systems = SystemFactory.createSystemsFromJson(json[i], this);
        } else {
            this[i] = json[i];
        }
    }
};

Ship.prototype = {
    constructor: Ship,

    getHitChangeMod: function getHitChangeMod(shooter, weapon) {
        if (this.flight) return this.getHitChangeModFlight(shooter, weapon); //separate function for fighter flight - same approach, different loop

        var firingPos = null;
        if(weapon.ballistic) { //ballistic weapon uses position fron start of turn; direct fire weapons use ship itself rather than any position - important at range 0!
            firingPos = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn); 		    
        }

        var affectingSystems = Array();
        for (var i in this.systems) {
            var system = this.systems[i];

            //if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))

            if (!this.checkIsValidAffectingSystem(system, shooter,firingPos)) //Marcin Sawicki: change to unit itself...
                continue;

/* redirecting - this will be covered by getDefensiveHitChangeMod function itself; it already is in back end!
            if (system instanceof Shield && mathlib.getDistanceBetweenShipsInHex(shooter, this) === 0 && shooter.flight) {
                // Shooter is a flight, and the flight is under the shield
                continue;
            }
*/	/*		

            var mod = system.getDefensiveHitChangeMod(this, shooter, weapon);
            mod = weapon.shieldInteractionDefense(this, shooter, system,mod);
        	
            if (mod > 0){
                //Advanced Sensors negate positive (eg. reducing profile) defensive systems' effects operated by less advanced races
                if ( (this.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors")) ){
                    mod = 0;
                }	
            }

            if ( ! (affectingSystems[system.defensiveType])
                || affectingSystems[system.defensiveType] < mod)
            {
                affectingSystems[system.defensiveType] = mod;
            }
        }
        var sum = 0;
        for (var i in affectingSystems) {
            sum += affectingSystems[i];
        }
        return sum;
    }, //getHitChangeMod
	
    //loop through ALL fighters - sample fighter should be enough, but let's loop through all in case of eg. criticals
    getHitChangeModFlight: function getHitChangeModFlight(shooter, weapon) {
        var firingPos = null;
        if(weapon.ballistic) { //ballistic weapon uses position fron start of turn; direct fire weapons use ship itself rather than any position - important at range 0!
            firingPos = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn); 		    
        }
        	
        var affectingSystems = Array();
        for (var i in this.systems) {
            var fighter = this.systems[i];
            for (var j in fighter.systems) {
                var system = fighter.systems[j];

                //if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))
                if (!this.checkIsValidAffectingSystem(system, shooter, firingPos)) //Marcin Sawicki: change to unit itself...
                    continue;

                var mod = system.getDefensiveHitChangeMod(this, shooter, weapon);
                mod = weapon.shieldInteractionDefense(this, shooter, system,mod);
            	
                if (mod > 0){
                    //Advanced Sensors negate positive (eg. reducing profile) defensive systems' effects operated by less advanced races
                    if ( (this.factionAge < 3) && (shipManager.hasSpecialAbility(shooter, "AdvancedSensors")) ){
                        mod = 0;
                    }	
                }

                if ( ! (affectingSystems[system.defensiveType])
                    || affectingSystems[system.defensiveType] < mod)
                {
                    affectingSystems[system.defensiveType] = mod;
                }
            }
        }
        var sum = 0;
        for (var i in affectingSystems) {
            sum += affectingSystems[i];
        }
        return sum;
    }, //getHitChangeModFlight
	

    //Marcin Sawicki: this should use shooter, not pos - OR insert pos only if necessary!
    //otherwise serious trouble at range 0
    //checkIsValidAffectingSystem: function(system, pos)
    checkIsValidAffectingSystem: function checkIsValidAffectingSystem(system, shooter, pos = null) {
        if (!system.defensiveType) return false;

        //If the system was destroyed last turn continue 
        //(If it has been destroyed during this turn, it is still usable)
        if (system.destroyed) return false;

        //If the system is offline either because of a critical or power management, continue
        if (shipManager.power.isOffline(this, system)) return false;

        //if the system has arcs, check that the position is on arc
        if (typeof system.startArc == 'number' && typeof system.endArc == 'number') {

            var tf = shipManager.getShipHeadingAngle(this);

            var heading = 0;

            //get the heading of position, not ship (in case ballistic)
            if(pos!==null){
                heading = mathlib.getCompassHeadingOfPoint(shipManager.getShipPosition(this), pos);
            }else{
                heading = mathlib.getCompassHeadingOfShip(this, shooter);
            }

            //if not on arc, continue!
            if (!mathlib.isInArc(heading, mathlib.addToDirection(system.startArc, tf), mathlib.addToDirection(system.endArc, tf))) {
                return false;
            }
        }

        return true;
    },

    checkShieldGenerator: function checkShieldGenerator() {
        var shieldCapacity = 0;
        var activeShields = 0;

        for (var i in this.systems) {
            var system = this.systems[i];

            if (system.name == "shieldGenerator") {
                if (system.destroyed || shipManager.power.isOffline(this, system)) {
                    continue; 
                }
                shieldCapacity = system.output + shipManager.power.getBoost(system);
            }

            if (system.name == "graviticShield" && !(system.destroyed || shipManager.power.isOffline(this, system))) {
                activeShields = activeShields + 1;
            }
            if (system.name == "abbaiShieldProjector" && !(system.destroyed || shipManager.power.isOffline(this, system))) {
                activeShields = activeShields + 1;
            }            
        }

        return shieldCapacity >= activeShields;
    }

};
*/
