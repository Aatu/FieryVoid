"use strict";

window.damageManager = {

    getDamage: function getDamage(ship, system) {
        var damage = 0;
        if (system == null) {
            console.log("system is null");
            console.trace();
        }
        for (var i in system.damage) {
            var damageEntry = system.damage[i];

            var d = damageEntry.damage - damageEntry.armour;
            if (d < 0) d = 0;

            damage += d;
        }

        if (damage > system.maxhealth) damage = system.maxhealth;

        return damage;
    },

    getTurnDestroyed: function getTurnDestroyed(ship, system) {
        if (!system) return null;

        for (var i in system.damage) {
            var damageEntry = system.damage[i];

            if (damageEntry.destroyed) return damageEntry.turn;
        }

        if (system.fighter) {
            var crit = shipManager.criticals.getCritical(system, "DisengagedFighter");
            if (crit) return crit.turn;
        } else {
            return null;
        }

        var stru = shipManager.systems.getStructureSystem(ship, system.location);
        if (stru && stru != system) {
            return damageManager.getTurnDestroyed(ship, stru);
        }

        return null;
    }
};