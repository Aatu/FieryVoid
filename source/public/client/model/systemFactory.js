"use strict";

window.SystemFactory = (function () {
    return {
        createSystemsFromJson: function createSystemsFromJson(systemsJson, ship, fighter, staticSystemsSource) {

            if (fighter) {
                return window.SystemFactory.createSystemsFromJsonFighter(systemsJson, ship, fighter, staticSystemsSource);
            }
            var systems = Array();
            for (var i in systemsJson) {

                var jsonSystem = systemsJson[i];
                var staticSystem = jsonSystem;

                if (staticSystemsSource) {
                    if (ship.flight) {
                        var keys = Object.keys(staticSystemsSource);
                        if (keys.length > 0) staticSystem = staticSystemsSource[keys[0]];
                    } else if (staticSystemsSource[jsonSystem.id]) {
                        staticSystem = staticSystemsSource[jsonSystem.id];
                    }
                } else if (ship.systems) {
                    staticSystem = ship.flight ? getFirstSystem(ship) : ship.systems[jsonSystem.id];
                }

                var system = SystemFactory.createSystemFromJson(jsonSystem, staticSystem, ship);

                //if(system.initializeOnLoad) system.initializationUpdate(); //Not used by any systems yet, but available if you wanted to runs system.initialisationUpdate() immediately on page load.                 
                systems[system.id] = system;
            }

            return systems;
        },

        createSystemsFromJsonFighter: function createSystemsFromJsonFighter(systemsJson, ship, fighter, staticSystemsSource) {
            var systems = Array();

            Object.keys(systemsJson).forEach((key, index) => {
                const jsonSystem = systemsJson[key];
                var staticSystem = jsonSystem;

                if (staticSystemsSource) {
                    // 1. Try Key match
                    if (staticSystemsSource[key]) {
                        staticSystem = staticSystemsSource[key];
                    }
                    // 2. Try ID match
                    else if (staticSystemsSource[jsonSystem.id]) {
                        staticSystem = staticSystemsSource[jsonSystem.id];
                    }
                    // 3. Fallback to Index match (Legacy/Array behavior)
                    else {
                        var staticKeys = Object.keys(staticSystemsSource);
                        if (staticKeys[index]) {
                            staticSystem = staticSystemsSource[staticKeys[index]];
                        }
                    }
                } else if (fighter.systems) {
                    // Fallback to reading from fighter instance
                    var fighterKeys = Object.keys(fighter.systems);
                    if (fighterKeys[index]) {
                        staticSystem = fighter.systems[fighterKeys[index]];
                    }
                }

                var system = SystemFactory.createSystemFromJson(jsonSystem, staticSystem, fighter);
                if(system.initializeOnLoad) system.initializationUpdate(); //Runs system.initialisationUpdate() immediately on page loading. Useful for updating tooltip etc.  

                systems[system.id] = system;
            })

            return systems;
        },

        createSystemFromJson: function createSystemFromJson(systemJson, staticSystem, ship) {
            if (staticSystem && staticSystem.fighter) return new Fighter(systemJson, staticSystem, ship);
            var name = systemJson.name.charAt(0).toUpperCase() + systemJson.name.slice(1);

            var args = Object.assign(Object.assign({}, staticSystem), systemJson);
            var system = new window[name](args, ship);

            return system;
        }
    }

    function getFirstSystem(ship) {

        var system = null;
        Object.keys(ship.systems).find(function (key) {
            system = ship.systems[key];
            return true;
        })
        return system;
    }
})();



/*//OLD VERSION - CHANGED DEC 2025
"use strict";

window.SystemFactory = (function() {
    return {
        createSystemsFromJson: function createSystemsFromJson(systemsJson, ship, fighter) {
            if (fighter) {
                return  window.SystemFactory.createSystemsFromJsonFighter(systemsJson, ship, fighter);
            }
            var systems = Array();
            for (var i in systemsJson) {

                var jsonSystem = systemsJson[i];
                var staticSystem = ship.flight ? getFirstSystem(ship) : ship.systems[jsonSystem.id];

                var system = SystemFactory.createSystemFromJson(jsonSystem, staticSystem, ship);
                systems[system.id] = system;
            }

            return systems;
        },

        createSystemsFromJsonFighter: function createSystemsFromJsonFighter(systemsJson, ship, fighter) {
            var systems = Array();

            Object.keys(systemsJson).forEach((key, index) => {
                const jsonSystem = systemsJson[key];
                const staticSystem = fighter.systems[Object.keys(fighter.systems)[index]]

                var system = SystemFactory.createSystemFromJson(jsonSystem, staticSystem, fighter);
                systems[system.id] = system;
            })

            return systems;
        },

        createSystemFromJson: function createSystemFromJson(systemJson, staticSystem, ship) {
            if (staticSystem.fighter) return new Fighter(systemJson, staticSystem, ship);
            var name = systemJson.name.charAt(0).toUpperCase() + systemJson.name.slice(1);

            var args = Object.assign(Object.assign({}, staticSystem), systemJson);
            var system = new window[name](args, ship);

            return system;
        }
    }

    function getFirstSystem(ship) {
    
        var system = null;
        Object.keys(ship.systems).find(function(key) {
            system = ship.systems[key];
            return true;
        })
        return system;
    }
})();
*/