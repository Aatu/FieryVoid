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

    //Returns true when the system currently has active criticals AND every one
    //of them is the named phpclass. Used to recolour the icon healthbar cyan
    //instead of orange when the only critical is a benign initiative-penalty
    //event (HangarOperations on a CnC, LaunchedThisTurn on a fighter). 05/26 DK
    //  excludeForInfo mirrors hasCriticalsIcon vs hasCriticals: the SystemIcon
    //  ignores forInfo criticals when colouring, the fighter healthbar does not,
    //  so the "only critical" test must consider the same set that turns it orange.
    hasOnlyCritical: function hasOnlyCritical(system, name, excludeForInfo) {
        var found = false;
        for (var i in system.criticals) {
            var crit = system.criticals[i];
            var active = (crit.turn <= gamedata.turn) && ((crit.turnend == 0) || (crit.turnend >= gamedata.turn));
            if (!active) continue;
            if (excludeForInfo && crit.forInfo) continue;
            if (crit.phpclass != name) return false;
            found = true;
        }
        return found;
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
    },

    //Hangar Ops Stage 9.1: parallel to isDisengagedFighter for fighters that
    //left the flight by entering a hangar (partial dock or partial-launch
    //split). The flight window renders these with a cyan DOCKED label.
    isDockedFighter: function (fighter) {
        return Boolean(shipManager.criticals.hasCritical(fighter, "DockedFighter"));
    },

    //Hangar Ops Stage 21.7: a fighter that left a DOCKED flight via a partial
    //launch (now on its own "- Split" row). Distinct from DisengagedFighter,
    //which is reserved for combat dropout (took too much damage and left the
    //game, losing its value). Both this and DockedFighter mean "departed to its
    //own row" — used by the fleet list to re-base a remnant's value past them.
    isSplitLaunchedFighter: function (fighter) {
        return Boolean(shipManager.criticals.hasCritical(fighter, "SplitLaunchedFighter"));
    },

    //Stage S (S-d): a Shadow integrated fighter SEVERED from its carrier — its
    //structure-tether box was destroyed, so it can never land/reabsorb. UNLIKE the
    //departed-to-own-row states above, a cut-off fighter is STILL ALIVE and fighting;
    //it renders a persistent red "CUT OFF" marker (not gated on destroyed).
    isCutOffFighter: function (fighter) {
        return Boolean(shipManager.criticals.hasCritical(fighter, "ShadowFighterCutOff"));
    }

};