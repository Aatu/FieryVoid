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
        } else if (i == 'movement') {
            this.movement = json[i].map(function(move) {
                return new window.MovementOrder(move.id, move.type, new hexagon.Offset(move.position), new hexagon.Offset(move.target), move.facing, move.turn, move.value, move.requiredThrust, move.assignedThrust);
            })
        } else {
            this[i] = json[i];
        }
    }
};

Ship.prototype = {
    constructor: Ship,

    getHitChangeMod: function getHitChangeMod(shooter) {
        var affectingSystems = Array();
        for (var i in this.systems) {
            var system = this.systems[i];

            //if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))
            if (!this.checkIsValidAffectingSystem(system, shooter)) //Marcin Sawicki: change to unit itself...
                continue;

            if (system instanceof Shield && mathlib.getDistanceBetweenShipsInHex(shooter, this) === 0 && shooter.flight) {
                // Shooter is a flight, and the flight is under the shield
                continue;
            }

            var mod = system.getDefensiveHitChangeMod(this, shooter);

            if ( ! (affectingSystems[system.defensiveType])
                || affectingSystems[system.defensiveType] < mod)
            {
      //          console.log("getting defensive: " + system.name + " mod: " + mod);
      //          affectingSystems[system.getDefensiveType] = mod;
                affectingSystems[system.defensiveType] = mod;
            }
        }
        var sum = 0;
        for (var i in affectingSystems) {
            sum += affectingSystems[i];
        }
        return sum;
    },

    //Marcin Sawicki: this should use shooter, not pos - OR insert pos only if necessary!
    //otherwise serious trouble at range 0
    //checkIsValidAffectingSystem: function(system, pos)
    checkIsValidAffectingSystem: function checkIsValidAffectingSystem(system, shooter) {
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
            //var heading = mathlib.getCompassHeadingOfPosition(this, pos);
            //Marcin Sawicki: should be otherwise in this case?...
            heading = mathlib.getCompassHeadingOfShip(this, shooter);

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
        }

        return shieldCapacity >= activeShields;
    }

};
