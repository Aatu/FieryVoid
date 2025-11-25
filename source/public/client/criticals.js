"use strict";

window.shipManager.criticals = {

    hasCritical: function hasCritical(system, name) {
		return shipManager.criticals.countCriticalOnTurn(system,name,gamedata.turn);
		/*
        var amount = 0;
        if (!system) console.trace();

        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass == name) amount++;
        }
        return amount;
		*/
    },	

    countCriticalOnTurn: function countCriticalOnTurn(system, name, turn) {
        var amount = 0;
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass == name && 
			  //crit.turn == turn
			  ( (crit.turn <= turn) && ( (crit.turnend == 0) || (crit.turnend >= turn) ) )
			) {
                amount++;
            }
        }
        return amount;
    },

    hasCriticalOnTurn: function hasCriticalOnTurn(system, name, turn) {
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.phpclass == name && 
			  //crit.turn == turn
			  ( (crit.turn <= turn) && ( (crit.turnend == 0) || (crit.turnend >= turn) ) )
			) {
                return true;
            }
        }
        return false;
    },

    //Called by CombatLog to see if damage has caused a critical hit
    sufferedCritThisTurn: function sufferedCritThisTurn(system, turn) {
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if (crit.turn == turn){ //Could also add a check to exempt some crit types if they are causing confusion in Log
                return true;
            }
        }
        return false;
    },


    getCritical: function getCritical(system, name) {
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if ((crit.phpclass == name) && ( (crit.turn <= gamedata.turn) && ( (crit.turnend == 0) || (crit.turnend >= gamedata.turn) ) ) ) return crit;
        }

        return null;
    },
	
	/*gets all CURRENT criticals*/
	getAllCriticals: function getAllCriticals(system, turn = gamedata.turn) {
		var critArray = Array();
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if ( (crit.turn <= turn) && ( (crit.turnend == 0) || (crit.turnend >= turn) ) ) {
				critArray.push(crit);
			}
        }

        return critArray;
    },
	

    hasCriticals: function hasCriticals(system) {
        //return system.criticals.length > 0;
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if ( (crit.turn <= gamedata.turn) && ( (crit.turnend == 0) || (crit.turnend >= gamedata.turn) ) )
			{
                return true;
            }
        }
        return false;
    },

	//New function to give more fleixbility for system icon healthbar - 01/25 DK
    hasCriticalsIcon: function hasCriticalsIcon(system) {
        //return system.criticals.length > 0;
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            if ( (crit.turn <= gamedata.turn) && ( (crit.turnend == 0) || (crit.turnend >= gamedata.turn) ) & !crit.forInfo )
			{
                return true;
            }
        }
        return false;
    },

    hasCriticalInAnySystem: function hasCriticalInAnySystem(ship, name) {
        var amount = 0;
        for (var a in ship.systems) {
            var system = ship.systems[a];			
			amount += shipManager.criticals.countCriticalOnTurn(system, name, gamedata.turn);
        }
        return amount;
    },

    isDisengagedFighter: function (fighter) {
        return Boolean(shipManager.criticals.hasCritical(fighter, "DisengagedFighter"));
    }

};