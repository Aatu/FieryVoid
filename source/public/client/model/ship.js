'use strict';

var Ship = function Ship(json) {
    var inputSystems = null;
    var staticSystems = null;

    if (window.staticShips && window.staticShips[json.faction] && window.staticShips[json.faction][json.phpclass]) {
        var staticShip = window.staticShips[json.faction][json.phpclass];
        Object.keys(staticShip).forEach(function (key) {
            if (key !== 'systems') {
                this[key] = staticShip[key]; // Copy other props
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

    getHitChangeMod: function getHitChangeMod(shooter, weapon) {
        if (this.flight) return this.getHitChangeModFlight(shooter, weapon); //separate function for fighter flight - same approach, different loop

        var firingPos = null;
        if (weapon.ballistic) { //ballistic weapon uses position fron start of turn; direct fire weapons use ship itself rather than any position - important at range 0!
            firingPos = shipManager.movement.getPositionAtStartOfTurn(shooter, gamedata.turn);
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
        var sum = 0;
        for (var i in affectingSystems) {
            sum += affectingSystems[i];
        }
        return sum;
    }, //getHitChangeMod

    //loop through ALL fighters - sample fighter should be enough, but let's loop through all in case of eg. criticals
    getHitChangeModFlight: function getHitChangeModFlight(shooter, weapon) {
        var firingPos = null;
        if (weapon.ballistic) { //ballistic weapon uses position fron start of turn; direct fire weapons use ship itself rather than any position - important at range 0!
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