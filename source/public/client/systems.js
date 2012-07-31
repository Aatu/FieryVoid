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
        
        return system.destroyed;
        

        /*
        var d = damageManager.getDamage(ship, system);
        var stru = shipManager.systems.getStructureSystem(ship, system.location);
        if (stru && stru != system && shipManager.systems.isDestroyed(ship, stru))
            return true;
            
        if (system.fighter && shipManager.criticals.hasCritical(system, "DisengagedFighter"))
			return true;
            
            
        return (d >= system.maxhealth);
        */
    },
    
    isEngineDestroyed: function(ship){
		if (ship.flight)
			return false;
		
        return shipManager.systems.isDestroyed(ship, shipManager.systems.getSystemByName(ship, "engine"));
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
		
		var output = system.output + system.outputMod + shipManager.power.getBoost(ship, system);
		
        
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
    
        if (ship.systems[id]){
            return ship.systems[id];
        }else{
            if (ship.flight){
                for (var i in ship.systems){
                    var fighter = ship.systems[i];
                    if (fighter.systems[id])
                        return fighter.systems[id];
                }
            }
        }
        
        return null;
    
    },

    initializeSystem: function(system){
        
        if (system.dualWeapon && !system.initialized){
            var selectedWeapon = system.weapons[system.firingMode];
            selectedWeapon.firingMode = system.firingMode;
            selectedWeapon.firingModes = system.firingModes;
            selectedWeapon.power = system.power;
            selectedWeapon.displayName = system.displayName;
            selectedWeapon.id = system.id;
            selectedWeapon.dualWeapon = true;
            selectedWeapon.initialized = true;
            selectedWeapon.damage = system.damage;
            selectedWeapon.destroyed = system.destroyed;
            return selectedWeapon;
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
        
        if (shipManager.movement.isRolled(ship) && (weapon.location == 3 || weapon.location == 4)) {
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
