shipManager.power = {

	repeatLastTurnPower: function(){
	
		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			
			if (ship.userid != gamedata.thisplayer)
				continue;
			
			for (var a in ship.systems){
				shipManager.power.copyLastTurnPower(ship, ship.systems[a]);
			}
		}
	
	},
	
	copyLastTurnPower: function(ship, system){
		if (shipManager.systems.isDestroyed(ship, system))
			return;
			
		var powers = Array();
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn == (gamedata.turn -1)){
				var newPower = jQuery.extend({}, power);
				newPower.turn = gamedata.turn;
				powers.push(newPower);
			}
		
		}
		
		system.power = system.power.concat(powers);
	},
	

	setPowerClasses: function (ship, system, systemwindow){
	
		
		
		var off = shipManager.power.isOffline(ship, system);
		
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")){
			systemwindow.addClass("forcedoffline");
			return true;		
		}
		
		if (off){
			systemwindow.addClass("offline");
			return true;
		}
		
		if (shipManager.power.isOverloading(ship, system)){
			systemwindow.addClass("overload");
		}
		
		if (gamedata.gamephase != 1 || ship.userid != gamedata.thisplayer)
			return;
					
		
		
		
		
		if (system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system)){
			systemwindow.addClass("canoverload");
		}
		
		var boost = shipManager.power.getBoost(ship, system);
		
		if (system.boostable && !boost){
			systemwindow.addClass("canboost");
		}else if (boost){
			systemwindow.addClass("boosted");
		}
		
		if (system.powerReq > 0 && !off && !boost && !weaponManager.hasFiringOrder(ship, system)){
			systemwindow.addClass("canoffline");
		}
		
		return false;

	
	},
	
	checkPowerPositive: function(){
	
		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			
			if (ship.flight)
				continue;
			
			if (ship.userid != gamedata.thisplayer)
				continue; 
				
			if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship))
				continue;
			
			if (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor"))<0)
				return false;
			
		}
		
		return true;
	
	},
	
	getReactorPower: function(ship, system){
	
		var reactor = shipManager.systems.getSystemByName(ship, "reactor");
		var output = reactor.output + reactor.outputMod;

		for (var s in ship.systems){
			var system = ship.systems[s];
			for (var i in system.power){
				var power = system.power[i];
				if (power.turn != gamedata.turn)
					continue;

				if (power.type == 1)
					output += system.powerReq;
					
				if (power.type == 2){
					output -= shipManager.power.countBoostPowerUsed(ship, system);
				}
				
				if (power.type == 3){
					output -= system.powerReq;
				}
			}
		}
		

		return output;
	
	},
	
	isPowerless: function(ship){
	
		if (shipManager.systems.isReactorDestroyed(ship))
			return true;
		
		var power = shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor"));
		
		if (this.countPossiblePower(ship) + power > 0)
			return false;
		
		return true;
	
	},
	
	countPossiblePower: function(ship){
		
		var power = 0;
	
		for (var i in ship.systems){
			var system = ship.systems[i];
			
			if (!shipManager.systems.isDestroyed(ship, system))
				power += system.powerReq;
		
		}
		//console.log(ship.name + " possible power: " + power);
		return power;
	
	},
	
	isOffline: function(ship, system){
	
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")){
			return true;		
		}
	
		if ((system.powerReq > 0 || system.name == "reactor") && this.isPowerless(ship))
			return true;
	
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 1)
				return true;
		}
		
		return false;
	},
	
	setOnline: function(ship, system){
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 1){
				system.power.splice(i, 1);
				return;
			}
		}
	
	},
	
	getBoost: function(ship, system){
		var boost = 0;
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 2){
				boost += power.amount;

			}
		}
		
		return boost;
	
	},
		
	countBoostReqPower: function(ship, system){
	
        if (system.boostEfficiency.toString().search(/^[0-9]+$/) == 0){
            return system.boostEfficiency;
        }else if (system.boostEfficiency == "output+1"){
            return system.output + shipManager.power.getBoost(ship, system) + 1;
        }
        
		
	
	},
	
	countBoostPowerUsed: function(ship, system){
		var boost = shipManager.power.getBoost(ship, system);
		
        if (boost == 0)
            return 0;
        
        if (system.boostEfficiency.toString().search(/^[0-9]+$/) == 0){
            return system.boostEfficiency * boost;
        }else if (system.boostEfficiency == "output+1"){
            var power = 0;
            for (var i = 1;i<=boost;i++){
                power += system.output + i;
            }
			return power;
        }
        return 0;
	
	},
	
	canBoost: function(ship, system){
	
		return (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor")) >= shipManager.power.countBoostReqPower(ship, system));
	
	},
	
	canOverload: function(ship, system){
        
		if (!system.overloadable)
			return false;
		
		return (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor")) >= system.powerReq);
	},
	
	unsetBoost: function(ship, system){
			
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 2){
				power.amount--;
				
				if (power.amount==0)
					system.power.splice(i, 1);
				
				return;
			}
		}
		
	
	},
	
	setBoost: function(ship, system){
	
		if (!shipManager.power.canBoost(ship, system))
			return;
			
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 2){
				power.amount++;
				return;
			}
		}
		
		system.power.push({id:null, shipid:ship.id, systemid:system.id, type:2, turn:gamedata.turn, amount:1});
	},
	
	setOverloading: function(ship, system){
        console.log("trying to overload");
        
		if (shipManager.power.isOverloading(ship, system))
			return;
			
		if (!shipManager.power.canOverload(ship, system))
			return;
        
        console.log("overload 2");
			
		system.power.push({id:null, shipid:ship.id, systemid:system.id, type:3, turn:gamedata.turn, amount:0});
	},
	
	stopOverloading: function(ship, system){
	
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 3){
				system.power.splice(i, 1);
				return;
			}
		}
		
	},
	
	isOverloading: function(ship, system){
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 3){
				return true;
			}
		}
		
		return false;
	
	},
	
	clickPlus: function(ship, system){
        if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
			
            confirm.error("You need to unassing all electronic warfare before changing scanner power management.");
            
            return;
		}
		shipManager.power.setBoost(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	}, 
	
	clickMinus: function(ship, system){
		
		if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
			
            confirm.error("You need to unassing all electronic warfare before changing scanner power management.");
            
            return;
		}
        
        shipManager.power.unsetBoost(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
		
		
	
	},
	
	onOfflineClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];
		
		if (gamedata.gamephase != 1)
			return;
			
		
		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (shipManager.power.isOffline(ship, system))
			return;
		
		system.power.push({id:null, shipid:ship.id, systemid:system.id, type:1, turn:gamedata.turn, amount:0});
		
		if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
			
            confirm.error("You need to unassing all electronic warfare before changing scanner power management.");
            
            return;
		}
		
		shipManager.power.stopOverloading(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	},
	
	onOnlineClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];
		
		if (gamedata.gamephase != 1)
			return;
		
		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (!shipManager.power.isOffline(ship, system))
			return;
		
		shipManager.power.setOnline(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	},
	
	
	onOverloadClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];
		
		if (gamedata.gamephase != 1)
			return;
		
		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (shipManager.power.isOffline(ship, system))
			return;
		
				
		shipManager.power.setOverloading(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	},
	
	onStopOverloadClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = ship.systems[systemwindow.data("id")];
		
		if (gamedata.gamephase != 1)
			return;
		
		if (shipManager.isDestroyed(ship) || shipManager.isDestroyed(ship, system) || shipManager.isAdrift(ship))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (shipManager.power.isOffline(ship, system))
			return;
		
				
		shipManager.power.stopOverloading(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	}

}
