"use strict";

window.shipManager = {
  getShipDoMAngle: function getShipDoMAngle(ship) {
    var d = shipManager.movement.getLastCommitedMove(ship).heading;
    if (d == 0) {
      return 0;
    }
    if (d == 1) {
      return 60;
    }
    if (d == 2) {
      return 120;
    }
    if (d == 3) {
      return 180;
    }
    if (d == 4) {
      return 240;
    }
    if (d == 5) {
      return 300;
    }
  },

  getShipHeadingAngle: function getShipHeadingAngle(ship) {
    var d = shipManager.movement.getLastCommitedMove(ship).facing;
    if (d == 0) {
      return 0;
    }
    if (d == 1) {
      return 60;
    }
    if (d == 2) {
      return 120;
    }
    if (d == 3) {
      return 180;
    }
    if (d == 4) {
      return 240;
    }
    if (d == 5) {
      return 300;
    }
  },

  getShipPosition: function getShipPosition(ship) {
    //TODO: Remove this and use movement service
    var movement = shipManager.movement.getLastCommitedMove(ship);
    return new hexagon.Offset(movement.position);
  },

  getShipPositionForDrawing: function getShipPositionForDrawing(ship) {
    var movement = null;
    for (var i in ship.movement) {
      if (ship.movement[i].commit == false) break;

      movement = ship.movement[i];

      if (movement.animated == true) continue;

      if (
        movement.type == "move" ||
        movement.type == "slipright" ||
        movement.type == "slipleft"
      ) {
        var last = ship.movement[i - 1];

        if (!last) {
          break;
        }
        var lastpos = hexgrid.hexCoToPixel(last.x, last.y);
        lastpos.x = lastpos.x + last.xOffset * gamedata.zoom;
        lastpos.y = lastpos.y + last.yOffset * gamedata.zoom;
        var destination = hexgrid.hexCoToPixel(movement.x, movement.y);
        destination.x = destination.x + movement.xOffset * gamedata.zoom;
        destination.y = destination.y + movement.yOffset * gamedata.zoom;
        var perc = movement.animationtics / animation.movementspeed;

        return mathlib.getPointBetween(lastpos, destination, perc);
      }

      break;
    }

    var x = movement.x;
    var y = movement.y;

    var lastpos = hexgrid.hexCoToPixel(x, y);
    lastpos.x = Math.floor(lastpos.x + movement.xOffset * gamedata.zoom);
    lastpos.y = Math.floor(lastpos.y + movement.yOffset * gamedata.zoom);
    return lastpos;
  },

  onShipContextMenu: function onShipContextMenu(e) {
    var id = $(e).data("id");
    var ship = gamedata.getShip(id);

    if (shipSelectList.haveToShowList(ship, e)) {
      shipSelectList.showList(ship);
    } else {
      shipManager.doShipContextMenu(ship);
    }
  },

  doShipContextMenu: function doShipContextMenu(ship) {
    shipSelectList.remove();

    if (shipManager.isDestroyed(ship)) return;

    if (
      ship.userid == gamedata.thisplayer &&
      (gamedata.gamephase == 1 || gamedata.gamephase > 2)
    ) {
      shipWindowManager.open(ship);
      gamedata.selectShip(ship, false);
      gamedata.shipStatusChanged(ship);
      drawEntities();
    } else {
      shipWindowManager.open(ship);
    }
    return false;
  },

  onShipDblClick: function onShipDblClick(e) {},

  onShipClick: function onShipClick(e) {
    //console.log("click on ship");

    if (!e || e.which !== 1) return;

    e.stopPropagation();
    var id = $(this).data("id");
    var ship = gamedata.getShip(id);

    if (shipSelectList.haveToShowList(ship, e)) {
      shipSelectList.showList(ship);
    } else {
      shipManager.doShipClick(ship);
    }
  },

  doShipClick: function doShipClick(ship) {
    shipSelectList.remove();

    if (ship == null) {
      return;
    }

    if (gamedata.thisplayer == -1) return;

    if (shipManager.isDestroyed(ship)) return;

    if (gamedata.gamephase == 2) return;

    if (gamedata.waiting) return;

    if (ship.userid == gamedata.thisplayer) {
      gamedata.selectShip(ship, false);
    }

    if (ship.userid != gamedata.thisplayer && gamedata.gamephase == 3) {
      weaponManager.targetShip(ship, false);
    }

    if (gamedata.gamephase == 1 && ship.userid != gamedata.thisplayer) {
      if (gamedata.selectedSystems.length > 0) {
        weaponManager.targetShip(ship, false);
      } else if (!ship.flight) {
        ew.AssignOEW(ship);
      }
    }
    gamedata.shipStatusChanged(ship);
    drawEntities();
    //scrolling.scrollToShip(ship);
  },

  getPrimaryCnC: function getPrimaryCnC(ship) {
    var cncs = [];

    for (var system in ship.systems) {
      if (ship.systems[system].displayName == "C&C") {
        cncs.push(ship.systems[system]);
      }
    }

    cncs.sort(function(a, b) {
      if (
        shipManager.systems.getRemainingHealth(a) >
        shipManager.systems.getRemainingHealth(b)
      ) {
        return 1;
      } else {
        return -1;
      }
    });

    var primary = cncs[0];

    return primary;
  },

  isDisabled: function isDisabled(ship) {
    if (ship.base) {
      var primary = shipManager.getPrimaryCnC(ship);

      if (
        !shipManager.criticals.hasCriticalOnTurn(
          primary,
          "ShipDisabledOneTurn",
          gamedata.turn - 1
        )
      ) {
        return false;
      }
    } else {
      for (var i = 0; i < ship.systems.length; i++) {
        if (ship.systems[i].displayName == "C&C") {
          if (
            shipManager.criticals.hasCriticalOnTurn(
              ship.systems[i],
              "ShipDisabledOneTurn",
              gamedata.turn - 1
            )
          ) {
            return true;
          }
        }
      }
    }
    return false;
  },

  isDestroyed: function isDestroyed(ship) {
    if (ship == null) {
      return;
    }

    if (ship.flight) {
      for (var i in ship.systems) {
        var fighter = ship.systems[i];
        if (!shipManager.systems.isDestroyed(ship, fighter)) {
          return false;
        }
      }
      return true;
    } else {
      if (!ship.base) {
        var stru = shipManager.systems.getStructureSystem(ship, 0);
        if (shipManager.systems.isDestroyed(ship, stru)) {
          return true;
        }

        var react = shipManager.systems.getSystemByName(ship, "reactor");
        if (shipManager.systems.isDestroyed(ship, react)) {
          return true;
        }
      } else {
        var stru = shipManager.systems.getStructureSystem(ship, 0);
        if (shipManager.systems.isDestroyed(ship, stru)) {
          return true;
        }

        var mainReactor = shipManager.systems.getSystemByNameInLoc(
          ship,
          "reactor",
          0
        );
        if (shipManager.systems.isDestroyed(ship, mainReactor)) {
          return true;
        }
      }
    }

    return false;
  },

  getStructuresDestroyedThisTurn: function getStructuresDestroyedThisTurn(
    ship
  ) {
    var array = [];

    for (var j = 0; j < ship.systems.length; j++) {
      system = ship.systems[j];
      if (system.displayName == "Structure" && system.location != 0) {
        if (system.destroyed) {
          for (var k = 0; k < system.damage.length; k++) {
            var dmg = system.damage[k];
            if (dmg.destroyed) {
              if (gamedata.turn == dmg.turn) {
                array.push(system);
                break;
              }
            }
          }
        }
      }
    }

    if (array.length > 0) {
      return array;
    } else return null;
  },

  getOuterReactorDestroyedThisTurn: function getOuterReactorDestroyedThisTurn(
    ship
  ) {
    var array = [];

    for (var j = 0; j < ship.systems.length; j++) {
      system = ship.systems[j];
      if (system.displayName == "Reactor" && system.location != 0) {
        if (system.destroyed) {
          for (var k = 0; k < system.damage.length; k++) {
            var dmg = system.damage[k];
            if (dmg.destroyed) {
              if (gamedata.turn == dmg.turn) {
                array.push(system);
                break;
              }
            }
          }
        }
      }
    }

    if (array.length > 0) {
      return array;
    } else return null;
  },

  isAdrift: function isAdrift(ship) {
    if (ship.flight || ship.osat || ship.base) return false;

    if (
      shipManager.criticals.hasCriticalInAnySystem(ship, "ShipDisabledOneTurn")
    )
      return true;

    if (
      shipManager.systems.isDestroyed(
        ship,
        shipManager.systems.getSystemByName(ship, "cnC")
      )
    ) {
      return true;
    }
    return false;
  },

  isEngineless: function isEngineless(ship) {
    var engines = [];
    for (var sys in ship.systems) {
      if (ship.systems[sys].displayName == "Engine") {
        engines.push(ship.systems[sys]);
      }
    }

    for (var i = 0; i < engines.length; i++) {
      if (engines[i].destroyed == false) {
        return false;
      }
    }

    return true;
  },

  getTurnDestroyed: function getTurnDestroyed(ship) {
    var turn = null;
    if (ship.flight) {
      var fightersSurviving = ship.systems.some(function(fighter) {
        return damageManager.getTurnDestroyed(ship, fighter) === null;
      });

      if (fightersSurviving) {
        return null;
      }

      ship.systems.forEach(function(fighter) {
        var dturn = damageManager.getTurnDestroyed(ship, fighter);
        if (dturn > turn) turn = dturn;
      });
    } else {
      var react = shipManager.systems.getSystemByName(ship, "reactor");
      var rturn = damageManager.getTurnDestroyed(ship, react);
      var stru = shipManager.systems.getStructureSystem(ship, 0);
      var sturn = damageManager.getTurnDestroyed(ship, stru);

      if (rturn != null && (rturn < sturn || sturn == null)) turn = rturn;
      else turn = sturn;
    }

    return turn;
  },

  getIniativeOrder: function getIniativeOrder(ship) {
    var previousInitiative = -100000; //same Ini move together now!
    var order = 0;

    for (var i in gamedata.ships) {
      if (shipManager.isDestroyed(gamedata.ships[i])) continue;
      if (gamedata.ships[i].iniative > previousInitiative) {
        //new Ini higher than previous!
        order++;
        previousInitiative = gamedata.ships[i].iniative;
      }
      if (gamedata.ships[i].id == ship.id) return order;
    }

    return 0; //should not happen
  },

  hasBetterInitive: function hasBetterInitive(a, b) {
    //console.log(a.name);
    if (a.iniative > b.iniative) return true;

    if (a.iniative < b.iniative) return false;

    if (a.unmodifiedIniative != null && b.unmodifiedIniative != null) {
      if (a.unmodifiedIniative > b.unmodifiedIniative) return true;

      if (a.unmodifiedIniative < b.unmodifiedIniative) return false;
    }

    if (a.iniative == b.iniative) {
      if (a.iniativebonus > b.iniativebonus) return true;

      if (b.iniativebonus > a.iniativebonus) return false;

      for (var i in gamedata.ships) {
        if (gamedata.ships[i] == a) return false;

        if (gamedata.ships[i] == b) return true;
      }
    }

    return false;
  },

  getShipsInSameHex: function getShipsInSameHex(ship, pos1) {
    if (!pos1) var pos1 = shipManager.getShipPosition(ship);

    var shipsInHex = Array();
    for (var i in gamedata.ships) {
      var ship2 = gamedata.ships[i];

      if (shipManager.isDestroyed(ship2)) continue;

      //if (ship.id = ship2.d)
      //  continue;

      var pos2 = shipManager.getShipPosition(ship2);

      if (pos1.equals(pos2)) {
        shipsInHex.push(ship2);
      }
    }

    return shipsInHex;
  },

  getFighterPosition: function getFighterPosition(pos, angle, zoom) {
    var dir = 0;
    if (pos == 0) {
      dir = mathlib.addToDirection(0, angle);
      return mathlib.getPointInDirection(19 * zoom, dir, 0, 0);
    } else if (pos == 1) {
      dir = mathlib.addToDirection(300, angle);
      return mathlib.getPointInDirection(13 * zoom, dir, 0, 0);
    } else if (pos == 2) {
      dir = mathlib.addToDirection(60, angle);
      return mathlib.getPointInDirection(13 * zoom, dir, 0, 0);
    } else if (pos == 3) {
      dir = mathlib.addToDirection(180, angle);
      return mathlib.getPointInDirection(12 * zoom, dir, 0, 0);
    } else if (pos == 4) {
      dir = mathlib.addToDirection(250, angle);
      return mathlib.getPointInDirection(21 * zoom, dir, 0, 0);
    } else if (pos == 5) {
      dir = mathlib.addToDirection(110, angle);
      return mathlib.getPointInDirection(21 * zoom, dir, 0, 0);
    } else if (pos == 6) {
      dir = mathlib.addToDirection(180, angle);
      return mathlib.getPointInDirection(29 * zoom, dir, 0, 0);
    } else if (pos == 7) {
      dir = mathlib.addToDirection(230, angle);
      return mathlib.getPointInDirection(32 * zoom, dir, 0, 0);
    } else if (pos == 8) {
      dir = mathlib.addToDirection(130, angle);
      return mathlib.getPointInDirection(32 * zoom, dir, 0, 0);
    } else if (pos == 9) {
      dir = mathlib.addToDirection(0, angle);
      return mathlib.getPointInDirection(35 * zoom, dir, 0, 0);
    } else if (pos == 10) {
      dir = mathlib.addToDirection(295, angle);
      return mathlib.getPointInDirection(28 * zoom, dir, 0, 0);
    } else if (pos == 11) {
      dir = mathlib.addToDirection(65, angle);
      return mathlib.getPointInDirection(28 * zoom, dir, 0, 0);
    }

    return { x: 0, y: 0 };
  },

  getSpecialAbilitySystem: function getSpecialAbilitySystem(ship, ability) {
    for (var i in ship.systems) {
      var system = ship.systems[i];

      if (shipManager.systems.isDestroyed(ship, system)) continue;

      if (shipManager.power.isOffline(ship, system)) continue;

      for (var a in system.specialAbilities) {
        if (system.specialAbilities[a] == ability) return system;
      }
    }

    return false;
  },

  hasSpecialAbility: function hasSpecialAbility(ship, ability) {
    if (shipManager.getSpecialAbilitySystem(ship, ability)) return true;

    return false;
  },

  isElint: function isElint(ship) {
    if (shipManager.hasSpecialAbility(ship, "ELINT")) {
      return true;
    }

    return false;
  },

  isEscorting: function isEscorting(ship, target) {
    if (!ship.flight) return false;
    //var ships = shipManager.getShipsInSameHex(ship);
    //for (var i in ships) {
    //var othership = ships[i];
    if (gamedata.turn == 1) return true; //on turn 1 all friendly ships can be protected!

    for (var i in gamedata.ships) {
      //doesn't need to be on the same hex NOW... only at the start and end of move :)
      var othership = gamedata.ships[i];
      if (shipManager.isDestroyed(othership)) continue; //no need to list ships already destroyed
      if (othership.flight === true) continue; //can escort only ships
      if (othership.id == ship.id) continue;

      if (gamedata.isEnemy(ship, othership)) continue;

      var oPos = shipManager.movement.getPositionAtStartOfTurn(othership);
      var tPos = shipManager.movement.getPositionAtStartOfTurn(ship);

      if (!target || target.id == othership.id) {
        if (oPos.equals(tPos)) return true;
      }
    }
    return false;
  },

  /*list of names of escorted ships*/
  listEscorting: function listEscorting(ship) {
    var resultTxt = "";
    if (!ship.flight) return resultTxt;

    if (gamedata.turn == 1) return "All"; //turn 1: all ships can be escorted

    for (var i in gamedata.ships) {
      var othership = gamedata.ships[i];
      /*
            if (othership.flight === true) continue; //can escort only ships
            if (othership.id == ship.id) continue; //self
            if (gamedata.isEnemy(ship, othership)) continue; //no escorting opponent

            var oPos = shipManager.movement.getPositionAtStartOfTurn(othership);
            var tPos = shipManager.movement.getPositionAtStartOfTurn(ship);

            if (oPos.equals(tPos)){
            */
      if (shipManager.isEscorting(ship, othership)) {
        if (resultTxt != "") resultTxt += ", ";
        resultTxt += othership.name;
      }
    }

    return resultTxt;
  }
};
