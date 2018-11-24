import { ShipSystem } from "./";

const getFirstSystem = ship => {
  var system = null;
  Object.keys(ship.systems).find(function(key) {
    system = ship.systems[key];
    return true;
  });
  return system;
};

const findSystemById = (systems, id) =>
  systems.find(system => system.id === id);

class SystemFactory {
  constructor() {}

  createSystemsFromJson(systemsJson, ship, fighter) {
    if (fighter) {
      return this.createSystemsFromJsonFighter(systemsJson, ship, fighter);
    }
    var systems = Array();
    systemsJson.forEach(jsonSystem => {
      var staticSystem = ship.flight
        ? getFirstSystem(ship)
        : findSystemById(ship.systems, jsonSystem.id);

      var system = this.createSystemFromJson(jsonSystem, staticSystem, ship);
      systems.push(system);
    });

    return systems;
  }

  createSystemsFromJsonFighter(systemsJson, ship, fighter) {
    var systems = Array();

    Object.keys(systemsJson).forEach((key, index) => {
      const jsonSystem = systemsJson[key];
      const staticSystem = fighter.systems[Object.keys(fighter.systems)[index]];

      var system = this.createSystemFromJson(jsonSystem, staticSystem, fighter);
      systems[system.id] = system;
    });

    return systems;
  }

  createSystemFromJson(systemJson, staticSystem, ship) {
    if (staticSystem.fighter)
      return new Fighter(systemJson, staticSystem, ship);
    var name =
      systemJson.name.charAt(0).toUpperCase() + systemJson.name.slice(1);

    var args = Object.assign(Object.assign({}, staticSystem), systemJson);
    var system = new ShipSystem(args, ship);

    return system;
  }
}

window.SystemFactory = SystemFactory;

export default new SystemFactory();
