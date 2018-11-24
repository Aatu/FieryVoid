"use strict";

window.SystemFactory = (function() {
  const findSystemById = (systems, id) =>
    systems.find(system => system.id === id);

  return {
    createSystemsFromJson: function createSystemsFromJson(
      systemsJson,
      ship,
      fighter
    ) {
      if (fighter) {
        return window.SystemFactory.createSystemsFromJsonFighter(
          systemsJson,
          ship,
          fighter
        );
      }
      var systems = Array();
      systemsJson.forEach(jsonSystem => {
        var staticSystem = ship.flight
          ? getFirstSystem(ship)
          : findSystemById(ship.systems, jsonSystem.id);

        var system = SystemFactory.createSystemFromJson(
          jsonSystem,
          staticSystem,
          ship
        );
        systems.push(system);
      });

      return systems;
    },

    createSystemsFromJsonFighter: function createSystemsFromJsonFighter(
      systemsJson,
      ship,
      fighter
    ) {
      var systems = Array();

      Object.keys(systemsJson).forEach((key, index) => {
        const jsonSystem = systemsJson[key];
        const staticSystem =
          fighter.systems[Object.keys(fighter.systems)[index]];

        var system = SystemFactory.createSystemFromJson(
          jsonSystem,
          staticSystem,
          fighter
        );
        systems[system.id] = system;
      });

      return systems;
    },

    createSystemFromJson: function createSystemFromJson(
      systemJson,
      staticSystem,
      ship
    ) {
      if (staticSystem.fighter)
        return new Fighter(systemJson, staticSystem, ship);
      var name =
        systemJson.name.charAt(0).toUpperCase() + systemJson.name.slice(1);

      var args = Object.assign(Object.assign({}, staticSystem), systemJson);
      var system = new window[name](args, ship);

      return system;
    }
  };

  function getFirstSystem(ship) {
    var system = null;
    Object.keys(ship.systems).find(function(key) {
      system = ship.systems[key];
      return true;
    });
    return system;
  }
})();
