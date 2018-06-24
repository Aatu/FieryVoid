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