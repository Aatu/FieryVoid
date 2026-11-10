"use strict";

/* THE NEBULA, client half - NEBULA_PLAN.md. The server's twin is handlers/Nebula.php.

   Most of the nebula never reaches this file: thrusters, fighter thrust, gravitic shields and missile ranges
   arrive already changed in the gamedata (the server sends the numbers rather than the client re-deriving
   them). What is left here are the rules the client has to apply itself - the per-shot to-hit terms the
   hit-chance preview must match the server on, the jink's price (a client-only rule), the EW lock-out and
   the EM weapons' refusal.

   ⭐ EVERY CALLER ASKS nebula.isActive() FIRST, so a game with no nebula pays one property read.

   ⚠️ MIRROR PAIRS with Nebula.php: fireControl, offensiveBonus, interceptRating, isAdvanced,
   isEmDisabled. A change to one is a change to its twin. */
window.nebula = {

    //gamedata.rules.nebula - NebulaRule's whole payload is 1.
    isActive: function isActive() {
        return !!(window.gamedata && gamedata.rules && gamedata.rules.nebula);
    },

    //KS 12.5.14: Ancient and Primordial units ignore the thruster and jinking effects. Ask the UNIT, never a weapon.
    isAdvanced: function isAdvanced(unit) {
        return !!unit && (parseInt(unit.factionAge, 10) || 0) >= 3;
    },

    //"Jinking requires 2 thrust per level, not 1" - a multiplier on what a level costs (a flight's 1, a jinking ship's pivot cost).
    jinkCostMultiplier: function jinkCostMultiplier(unit) {
        return nebula.isAdvanced(unit) ? 1 : 2;
    },

    //Fire control: a bonus halves rounding DOWN (user ruling D4, revised after game 4545), a penalty doubles, 0 and null (cannot fire) stay.
    fireControl: function fireControl(fc) {
        if (fc === null || fc === undefined) return fc;
        if (fc > 0) return Math.floor(fc / 2);
        return fc * 2;
    },

    //A fighter's offensive bonus halves rounding DOWN (D4, revised).
    offensiveBonus: function offensiveBonus(ob) {
        if (!(ob > 0)) return ob;
        return Math.floor(ob / 2);
    },

    /* A flight's offensive bonus as a tooltip SHOWS it (SystemInfo's weapon tooltip, ShipInfo's flight lines): halved in a
       nebula at the point the server halves it - the base, before mine detection is taken off and an Electrician's shunt
       added (Weapon::calculateHitBase, weaponManager.computeOEW). Unchanged in any other game. */
    showOffensiveBonus: function showOffensiveBonus(ob) {
        return nebula.isActive() ? nebula.offensiveBonus(ob) : ob;
    },

    //⚠️ Only a negative fire control VALUE doubles: a called shot and a Piercing shot do not (user, after game 4545 - built and rolled back).

    //Intercept rating, as it is SPENT: 1 less, never below 0.
    interceptRating: function interceptRating(rating) {
        return Math.max(0, rating - 1);
    },

    /* "Electromagnetic weapons do not function unless mounted on advanced race ships." For the mode given (or
       the weapon's current one): three BSG guided missiles mix EM and non-EM modes. `ship` is the unit the
       weapon is mounted on - for a fighter's weapon, its flight. */
    isEmDisabled: function isEmDisabled(ship, weapon, mode) {
        var weaponClass = weapon.weaponClass;
        if (mode != null && weapon.weaponClassArray && weapon.weaponClassArray[mode] !== undefined) weaponClass = weapon.weaponClassArray[mode];
        if (weaponClass !== 'Electromagnetic') return false;
        return !nebula.isAdvanced(ship);
    },

    /* The unit a system is mounted on. ⚠️ On the client a FIGHTER's system has the CRAFT as its `ship`, not the
       flight (SystemFactory builds a fighter's systems with (args, fighter)), and the craft carries no factionAge -
       so join on flightid, as JumpEngine.prototype.getOwningUnit does. gamedata.getShip: the client has no
       getShipById. */
    owningUnit: function owningUnit(system) {
        var host = system ? system.ship : null;
        if (host && host.fighter && host.flightid != null && window.gamedata && typeof gamedata.getShip === 'function') {
            return gamedata.getShip(host.flightid) || host;
        }
        return host;
    }
};
