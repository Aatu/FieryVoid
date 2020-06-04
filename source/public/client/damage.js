"use strict";

window.damageManager = {

    getDamage: function getDamage(ship, system) {
        var damage = 0;
        if (system == null) {
            throw new Error("system null")
        }
        for (var i in system.damage) {
            var damageEntry = system.damage[i];

            var d = damageEntry.damage - damageEntry.armour;
            //if (d < 0) d = 0; //healing is possible!

            damage += d;
        }

        if (damage > system.maxhealth) damage = system.maxhealth;

        return damage;
    },

    getTurnDestroyed: function getTurnDestroyed(ship, system) {
        if (!system) return null;
		var turnDestroyed = null;

        for (var i in system.damage) {
            var damageEntry = system.damage[i];

            //if (damageEntry.destroyed) return damageEntry.turn;
			//can undestroy - so return LAST destroyed... and look for undestroyed, too!
			if (damageEntry.destroyed){
				turnDestroyed = damageEntry.turn;
			}else if (damageEntry.undestroyed){
				turnDestroyed = null;
			}
        }
		if(turnDestroyed!==null) return turnDestroyed;

		/* I think there's a bug here, rewriting but leaving original code as well just in case!*/
		/*
        if (system.fighter) {
            var crit = shipManager.criticals.getCritical(system, "DisengagedFighter");
            if (crit) return crit.turn;
        } else {
            return null;
        }
		*/
        if (system.fighter) {
            var crit = shipManager.criticals.getCritical(system, "DisengagedFighter");
            if (crit) {
				return crit.turn;
			} else {
				return null;
			}
        }

		var stru = shipManager.systems.getStructureSystem(ship, system.location);
		if (stru && stru != system) {
			return damageManager.getTurnDestroyed(ship, stru);
		}

        return null;
    }
};