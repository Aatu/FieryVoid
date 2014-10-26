shipManager.systems = {

	selectedShipHasSelectedWeapons: function(ballistic){
		var selectedShip = gamedata.getSelectedShip();
		
		for (var i in gamedata.selectedSystems){
			var system = gamedata.selectedSystems[i];
			if (!ballistic && system.weapon)
				return true;
			if (ballistic && system.weapon && system.ballistic)
				return true;
		}
		
		
	},
	

	getArmour: function(ship, system){
		var armour = system.armour - shipManager.criticals.hasCritical(system, "ArmorReduced");
		if (armour<0)
			armour=0;
			
		return armour;
		
	},
	
	isDestroyed: function(ship, system){
        
        if(system.parentId > 0){
            var parentSystem = system;
            
            while(parentSystem.parentId > 0){
                parentSystem = shipManager.systems.getSystem(ship, parentSystem.parentId);
            }
            
            return parentSystem.destroyed;
        }
        
        return system.destroyed;
    },
    
    isEngineDestroyed: function(ship){
	if (ship.flight)
            return false;
		
        // Check all engines, as Dilgar have two of them.
        for (var i in ship.systems){
            var system = ship.systems[i];
            if (system.name == "engine"){
                if(!shipManager.systems.isDestroyed(ship, system)){
                    // At least one of the engines is still alive
                    return false;
                }
            }
        }
        
        return true;
    },
    
    isReactorDestroyed: function(ship){
		if (ship.flight)
			return false;
			
        return shipManager.systems.isDestroyed(ship, shipManager.systems.getSystemByName(ship, "reactor"));
    },

    getOutput: function(ship, system){
		if (!system){
			console.log("ERROR: getOutput system missing");
			console.trace();
		}
		
		if (this.isDestroyed(ship, system))
			return 0;
        
		if (shipManager.power.isOffline(ship, system))
			return 0;
		
		var output = system.output + system.outputMod + shipManager.power.getBoost(system);
        
        return output;
    },
    
    getFighterSystem: function(ship, fighterid, systemid){
		for (var i in ship.systems){
			var fighter = ship.systems[i];
			if (fighter.id == fighterid){
				for (var a in fighter.systems){
					if (fighter.systems[a].id == systemid)
						return fighter.systems[a];
				}
			}
		}
	},
    
    getFighterBySystem: function(ship, systemid){
		for (var i in ship.systems){
			var fighter = ship.systems[i];
	
			for (var a in fighter.systems){
				if (fighter.systems[a].id == systemid)
					return fighter;
			}
			
		}
	},
    
    getSystem: function(ship, id){
    
        if(ship == null){
            console.log("That's weird");
            return null;
        }
        
        for(var i in ship.systems){
            var system = ship.systems[i];
            
            if(system.id == id){
                return system
            }
            
            if(system.fighter){
                for(var i in system.systems){
                    var fighter_system = system.systems[i];
                    
                    if(fighter_system.id == id){
                        return fighter_system;
                    }
                }
            }
            
            if(system.duoWeapon || system.dualWeapon){
                for(var i in system.weapons){
                    var weapon = system.weapons[i];
                    
                    if(weapon.id == id){
                        return weapon;
                    }
                }
            }
            
            if(system.dualWeapon){
                if(system.weapons[system.firingMode].duoWeapon){
                    for(var i in system.weapons[system.firingMode].weapons){
                        var duoWeapon = system.weapons[system.firingMode].weapons[i];
                        
                        if(duoWeapon.id == id){
                            return duoWeapon;
                        }
                    }
                }
            }
        }
        
        return null;
    
    },
    
    initializeSystem: function(system){
        
        if(system.dualWeapon && system.weapons == null){
            return system;
        }
        
        if (system.dualWeapon){
            var selectedWeapon = system.weapons[system.firingMode];
            
            if(selectedWeapon.duoWeapon){
                selectedWeapon.damage = system.weapons[1].damage;
            }
            else{
                selectedWeapon.damage = system.damage;
            }
            
            selectedWeapon.power = system.power;
            selectedWeapon.firingMode = system.firingMode;
            selectedWeapon.firingModes = system.firingModes;
            selectedWeapon.dualWeapon = true;
            selectedWeapon.initialized = true;
            
            selectedWeapon.destroyed = system.destroyed;
            return selectedWeapon;
        }
        
        if(system.boostable){
            system = system.initBoostableInfo();
        }
        
        // Check the number of elements in missileArray
        // This has to be done like this, as length doesn't give the correct
        // return because the elements in the missileArray aren't on consequetive
        // indices.
        var cnt = 0;
        for(var i in system.missileArray){
            cnt++;
        }
        
        if(system.missileArray!== null && cnt > 0){
            system.range = system.missileArray[system.firingMode].range + system.rangeMod;
        }

        return system;
    },
            
    getSystemByName: function(ship, name){
        for (var i in ship.systems){
            var system = ship.systems[i];
            if (system.fighter){
                for (var a in system.systems){
                    var figsys = system.systems[a];
                    
                    if (figsys.name == name){
                        return figsys;
                    }
                }
            }
            if (system.name == name){
                return system;
            }
            
        
        }
        
        return null;
    
    },
    
    getArcs: function (ship, weapon){
        
        if (shipManager.movement.isRolled(ship)) {
            return {start: mathlib.addToDirection(weapon.endArc, (weapon.endArc*-2)), end: mathlib.addToDirection(weapon.startArc, (weapon.startArc*-2))}
        }else{
            return {start:weapon.startArc, end:weapon.endArc};
        }
    
        
    },
    
    getDisplayName: function(system){
    
        if (system.name == "structure"){
            if (system.location == 0)
                return "Primary structure";
            if (system.location == 1)
                return "Forward structure";
            if (system.location == 2)
                return "Aft structure";
            if (system.location == 3)
                return "Port structure";
            if (system.location == 4)
                return "Starboard structure";
        }
    
        return system.displayName;
    },
    
    getLocationName: function(system){
        
        if (system.location == 0)
            return "Primary";
        if (system.location == 1)
            return "Forward";
        if (system.location == 2)
            return "Aft";
        if (system.location == 3)
            return "Port";
        if (system.location == 4)
            return "Starboard";
            
        return "error, system location not defined";

    },
    
    getSystemsInLocation: function(ship, location){
        var systems = Array();
        
        for (var i in ship.systems){
            if (ship.systems[i].location == location)
                systems.push(ship.systems[i]);
        }
        
        return systems;
    
    },
    
    getSystemsForShipStatus: function(ship, location){

        var systems = Array();
        
        
        for (var i in ship.systems){
            if (ship.systems[i].location == location && ship.systems[i].name != "structure")
                systems.push(ship.systems[i]);
        }
        
        return systems;
    
    },
    
    getStructureSystem: function(ship, location){
        
        if (!ship.structures[location])
            return null;
			
        return shipManager.systems.getSystem(ship, ship.structures[location]);
    },
    
    groupSystems: function(systems){
        var grouped = Array();
        var found = false;
        
        for (var i in systems){
            var system = systems[i];
            
            var found = false;
            for (var a in grouped){
                for (var b in grouped[a]){
                    if (!found && (grouped[a][b].name == system.name || (grouped[a][b].primary && system.primary) ) && grouped[a].length<4){
                        grouped[a].push(system);
                        found = true;
                        break;
                    }
                }
            }
            if (!found){
                var group = Array();
                group.push(system);
                grouped.push(group);
            }
            
            
        }
        
        grouped.sort(function(a, b){
        
            var al = a.length
            var bl = b.length;
            
            if (al == 3 && bl == 2)
                return -1;
                
            if (bl == 3 && al == 2)
                return 1;
                        
            if (al > bl)
                return 1;
            
            
            
            if (bl > al)
                return -1;
                
            return 0;
            
        });
        
                
        return grouped;
        
    },

    getMisc: function(ship){
        var tc = ship.turncost;
        var td = ship.turndelaycost;
        
        return "static TurnCost: " + tc + " TurnDelay: " + td;
    },
	  
    getFlightArmour: function(ship, system){
        var front = ship.systems[1].armour[0];
        var aft = ship.systems[1].armour[1];
        var side = ship.systems[1].armour[2];
        
        var armour = "Armor (F/S/A): " + front + " / " + side + " / " + aft;
        
        return armour;
    },
		
    getThrusters: function(ship, direction){
        var list = Array();
        for (var i in ship.systems){
            var system = ship.systems[i];
            
            if (system.name == "thruster" && system.direction == direction)
                list.push(system);
            }
		
	return list;
    }
    

}
