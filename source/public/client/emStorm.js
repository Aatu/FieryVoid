"use strict";

/* THE ELECTROMAGNETIC STORM, client half - EM_STORM_PLAN.md. The server's twin is handlers/EMStorm.php.

   Almost nothing of the storm needs client code: its sensor and power losses arrive already applied (Scanner and
   Reactor publish them in edfDrain, which shipManager.systems.getOutput and power.js subtract), its shutdowns are
   ordinary ForcedOfflineOneTurn criticals, and its initiative penalty is the server's alone. What is left here:
   the fighter offensive-bonus term the hit-chance preview must match (MIRROR PAIR with Weapon::calculateHitBase),
   the OB tooltips, and the Storm Defence declaration on an EM shield.

   ⭐ EVERY CALLER ASKS emStorm.isActive() FIRST (and `window.emStorm &&` - this file is game.php's alone, so the
   lobby and its shared React components never see it). */
window.emStorm = {

    //gamedata.rules.emStorm - EMStormRule's whole payload is 1.
    isActive: function isActive() {
        return !!(window.gamedata && gamedata.rules && gamedata.rules.emStorm);
    },

    //factionAge 3 or more: immune to the storm's power loss and attacks, -1 on its other rolls. Ask the UNIT.
    isAdvanced: function isAdvanced(unit) {
        return !!unit && (parseInt(unit.factionAge, 10) || 0) >= 3;
    },

    /* The summed param of the one-turn storm criticals of a class in force on the current turn. The server's
       sumCriticalParam filter exactly - a oneturn crit counts only on crit.turn + 1 - because this feeds a mirror pair. */
    sumParam: function sumParam(system, phpclass) {
        if (!system || !system.criticals) return 0;
        var total = 0;
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass !== phpclass) continue;
            if (crit.inEffect === false) continue;
            if (Number(crit.turn) + 1 !== Number(gamedata.turn)) continue;
            total += parseInt(crit.param, 10) || 0;
        }
        return total;
    },

    /* A flight's storm OB loss this turn (EMStormOffensive, on its sample fighter - systems[1], as the server's
       getSampleFighter). Already capped at half the bonus when rolled. ⚠️ MIRROR PAIR with Weapon::calculateHitBase. */
    offensivePenalty: function offensivePenalty(flight) {
        if (!flight || !flight.flight || !flight.systems) return 0;
        return emStorm.sumParam(flight.systems[1], 'EMStormOffensive');
    },

    /* ---------------------------------------------------------------- Storm Defence ------------------ */
    /* "Ships with EM shields may elect to accept this effect using a facing shield, but this must be declared before
       the roll for location is made." A STANDING ORDER on the shield (ruling R3, amended after play-test game 4547):
       declared or withdrawn in Deployment or Initial Orders, it stands for every storm until withdrawn.
       shield.stormDefence is a boolean: the server sends `true` for its own side's shields while the order stands
       (EMShield::stripForJson), the buttons flip it, and every commit of those two phases posts the current state
       (EMShield.prototype.doIndividualNotesTransfer) - the server writes a note only when it changes. */

    //The phase a declaration made now belongs to: Deployment's, or Initial Orders'. Null in any other phase.
    declarationPhase: function declarationPhase() {
        if (gamedata.gamephase == -1) return -1;
        if (gamedata.gamephase == 1) return 1;
        return null;
    },

    isEMShield: function isEMShield(system) {
        return !!system && (system.name === 'eMShield' || system.name === 'FlareShielding');
    },

    isDeclared: function isDeclared(system) {
        return !!system && system.stormDefence === true;
    },

    //The button's condition: a storm, a phase that takes declarations, my live non-advanced unit, a live EM shield not yet declared.
    canDeclare: function canDeclare(ship, system) {
        return emStorm.canChange(ship, system) && !emStorm.isDeclared(system);
    },

    canWithdraw: function canWithdraw(ship, system) {
        return emStorm.canChange(ship, system) && emStorm.isDeclared(system);
    },

    canChange: function canChange(ship, system) {
        return !gamedata.waiting && emStorm.isEligible(ship, system);
    },

    /* Whether this shield's order may be set in the phase now open - the buttons' test, less `waiting` (the commit asks
       it too, for what to post). */
    isEligible: function isEligible(ship, system) {
        if (!emStorm.isActive() || emStorm.declarationPhase() === null) return false;
        if (!ship || ship.flight || !gamedata.isMyShip(ship) || emStorm.isAdvanced(ship)) return false;
        if (shipManager.isDestroyed(ship)) return false;
        if (!emStorm.isEMShield(system) || shipManager.systems.isDestroyed(ship, system)) return false;
        //Deployment: only a unit placed this turn meets the Deployment storm; everything else met last turn's. Initial
        //Orders: a unit still to be deployed is not on the map for the end-of-turn storm.
        if (gamedata.gamephase == -1 && shipManager.getTurnDeployed(ship) != gamedata.turn) return false;
        if (gamedata.gamephase == 1 && shipManager.getTurnDeployed(ship) > gamedata.turn) return false;
        return true;
    },

    declare: function declare(ship, system) {
        if (!emStorm.canDeclare(ship, system)) return;
        system.stormDefence = true;
        emStorm.refresh(ship, system);
    },

    withdraw: function withdraw(ship, system) {
        if (!emStorm.canWithdraw(ship, system)) return;
        system.stormDefence = false;
        emStorm.refresh(ship, system);
    },

    //Redraw the shield's icon (its ⚡ badge), as a Meteor Defence declaration does.
    refresh: function refresh(ship, system) {
        if (window.webglScene && typeof webglScene.customEvent === 'function') {
            webglScene.customEvent('SystemDataChanged', { ship: ship, system: system });
        }
    },

    //Right-click: every EM shield on the unit, the same way (declare all, or withdraw all).
    declareAll: function declareAll(ship) {
        emStorm.eachShield(ship, function (shield) { emStorm.declare(ship, shield); });
    },

    withdrawAll: function withdrawAll(ship) {
        emStorm.eachShield(ship, function (shield) { emStorm.withdraw(ship, shield); });
    },

    eachShield: function eachShield(ship, fn) {
        if (!ship || !ship.systems) return;
        for (var i in ship.systems) {
            if (emStorm.isEMShield(ship.systems[i])) fn(ship.systems[i]);
        }
    }
};
