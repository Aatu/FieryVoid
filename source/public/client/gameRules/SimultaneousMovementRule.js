window.SimultaneousMovementRule = (function(){

    function SimultaneousMovementRule() {

    }

    SimultaneousMovementRule.prototype.getShipCategoryIndex = function (ship) {
        if (! gamedata.rules.initiativeCategories) {
            return null;
        }

        var categories = getGategories(gamedata.rules.initiativeCategories);

        var index = null;
        categories.find(function(category, i) {
            if (ship.iniative === category) {
                index = i;
                return true;
            }
            return false;
        })


        return categories.length - index;
    }


    SimultaneousMovementRule.prototype.isActiveMovementShip = function(ship) {
        var active = gamedata.getActiveShips();
        if (active.length === 0) {
            return null;
        }

        var ini = active.pop().iniative;

        return ship.iniative === ini;
    }

    /* The MOVEMENT GROUP number the Order of Battle prints down its left edge - ships sharing an
       initiative total share a group and move together, so this is the number worth showing
       wherever movement order matters, rather than the raw initiative total.

       getIniativeOrder's own validShips filter excludes terrain, mines and not-yet-deployed ships,
       so for those the loop never matches and its `return 0` tail fires. A literal 0 would be wrong
       AND would look like a real group, so all of those cases return null here and each caller
       decides how to render "no group" (the hex stack picker prints an em dash; the map badge draws
       nothing at all). */
    SimultaneousMovementRule.prototype.getMovementGroup = function (ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return null;
        if (ship.mine) return null;
        if (shipManager.getTurnDeployed(ship) > gamedata.turn) return null;

        var order = shipManager.getIniativeOrder(ship);

        return (order > 0) ? order : null;
    }

    SimultaneousMovementRule.prototype.isNotYetMovedShip = function(ship) {
        var active = gamedata.getActiveShips();
        if (active.length === 0) {
            return null;
        }

        var ini = active.pop().iniative;

        return ship.iniative > ini;
    }
    
    function getGategories(number) {
        var categories = [];
        var step = Math.floor(200 / number);

        while (number--) {
            categories.push(step * number);
        }
        
        return categories;
    }

    return new SimultaneousMovementRule();
})();