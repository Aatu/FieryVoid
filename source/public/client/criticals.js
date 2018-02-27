"use strict";

window.shipManager.criticals = {

    hasCritical: function hasCritical(system, name) {
        var amount = 0;
        if (!system) console.trace();

        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass == name) amount++;
        }
        return amount;
    },

    hasCriticalOnTurn: function hasCriticalOnTurn(system, name, turn) {
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass == name && crit.turn == turn) {
                return true;
            }
        }
        return false;
    },

    getCritical: function getCritical(system, name) {
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass == name) return crit;
        }

        return null;
    },

    hasCriticals: function hasCriticals(system) {

        return system.criticals.length > 0;
    },

    hasCriticalInAnySystem: function hasCriticalInAnySystem(ship, name) {
        var amount = 0;
        for (var a in ship.systems) {
            var system = ship.systems[a];
            for (var i in system.criticals) {
                var crit = system.criticals[i];
                if (crit.phpclass == name) amount++;
            }
        }
        return amount;
    }

};