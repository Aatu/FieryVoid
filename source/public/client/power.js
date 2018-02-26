shipManager.power = {

	repeatLastTurnPower: function(){
	
		for (var i in gamedata.ships){
			var ship = gamedata.ships[i];
			
			if (ship.userid != gamedata.thisplayer) continue;
			
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

            if (shipManager.criticals.hasCritical(system, "OutputReducedOneTurn")){
				for (var j = 0; j < system.criticals.length; j++){
					if (system.criticals[j].phpclass == "OutputReducedOneTurn"){
						if (system.criticals[j].turn == gamedata.turn-1 || system.criticals[j].turn == gamedata.turn){
			                systemwindow.addClass("forcedoffline");
						}
					}
				}
			}
            else if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn") || shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns"))
            {                  	
                systemwindow.addClass("forcedoffline");

                // Because of the crit, add a power entry to the power array
                // of this system.
                // A bit of code is necessary to make sure this only happens once.
                var isOnline = true;

		//for Jammer, copy last turn's power - it's important for opponent!
		//induces trouble AND does not work as intended
                //if (system.name == "jammer"){
		//	copyLastTurnPower(ship, system);
                //}

                for (var i in system.power){
                    var power = system.power[i];
                    if (power.turn != gamedata.turn)
                            continue;

                    if (power.type == 1){
                        isOnline = false;
                        break;
                    }
                }

                if (system.name == "engine"){
            		system.power = [];
                }

                if(isOnline){
                    system.power.push({id:null, shipid:ship.id, systemid:system.id, type:1, turn:gamedata.turn, amount:0});
                    shipManager.power.stopOverloading(ship, system);
                }

                return true;		
            }

            if (off){
                if(system.name == "reactor"){
                    // This is the reactor. It has recovered from a ForcedOffline crit
                    // (If it still had one, it would have entered the previous if-statement)
                    // Remove class offline and give user feedback.
                    shipManager.power.setOnline(ship, system);
                    systemwindow.removeClass("offline");
                    
                //    var userMessage = "The reactor of the " + ship.name +" has recovered from a forced shutdown.<br>";
                //    userMessage += "Power up all necessary systems.";                    
                //    window.confirm.error(userMessage, function(){});
                    
                    return;
                }else{
                    systemwindow.addClass("offline");
                    return true;
                }
            }

            if (shipManager.power.isOverloading(ship, system)){
                    systemwindow.addClass("overload");
            }

            if (gamedata.gamephase != 1 || ship.userid != gamedata.thisplayer)
                    return;

            if (system.weapon && system.overloadable && !shipManager.power.isOverloading(ship, system)){
                    systemwindow.addClass("canoverload");
            }

            var boost = shipManager.power.getBoost(system);

            if (system.boostable && !boost){
                //if(system.name == "scanner" || system.name == "elintScanner"){
		if(system.isScanner()){
                    if(system.id == shipManager.power.getHighestSensorsId(ship)){
                        // You can only boost the highest sensor rating
                        // if multiple sensors are present on one ship
                        systemwindow.addClass("canboost");
                    }
                }else{
                    systemwindow.addClass("canboost");
                }
            }else if (boost){
                    systemwindow.addClass("boosted");
            }

            if (system.canOffLine || (system.powerReq > 0 && !off && !boost && !weaponManager.hasFiringOrder(ship, system))){
                    systemwindow.addClass("canoffline");
            }

            return false;
	},
        
        getHighestSensorsId: function(ship){
            var highestRating = -1;
            var highestId = -1;
            
            for(var i in ship.systems){
                var system = ship.systems[i];
                
                //if(system.name == "scanner" || system.name == "elintScanner"){
		if(system.isScanner()){
                    if(!shipManager.power.isOffline(ship, system)){
                        var rating = shipManager.systems.getOutput(ship, system);
                        if(rating > highestRating){
                            highestId = system.id;
                            highestRating = rating;
                        }
                    }
                }
            }
            
            return highestId;
        },
	
        getShipsGraviticShield: function(){
            var shipNames = new Array();
            var counter = 0;

            for (var i in gamedata.ships){
                var ship = gamedata.ships[i];

                if (ship.unavailable)
                    continue;
			
		if (ship.flight)
                    continue;
			
                if (ship.userid != gamedata.thisplayer)
                    continue; 

                if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship))
                    continue;

                if(!ship.checkShieldGenerator()){
                    shipNames[counter] = ship.name;
                    counter++;
                }
            }
            
            return shipNames;
        },
        
	getShipsNegativePower: function(){
            var shipNames = new Array();
            var counter = 0;
	
            for (var i in gamedata.ships){
				var ship = gamedata.ships[i];
            
	            if (ship.unavailable)
	                continue;
				
				if (ship.flight)
					continue;
				
				if (ship.userid != gamedata.thisplayer)
					continue; 
					
				if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship))
					continue;
				
				if (shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor"))<0){
	                            shipNames[counter] = ship.name;
	                            counter++;
	                        }
			}
		
		return shipNames;
	
	},

	getPowerNeedForSection: function(ship, loc){
		var power = 0;

		for (var i = 0; i < ship.systems.length; i++){
			if (ship.systems[i].location == loc){
				if (! ship.systems[i].destroyed){
					power += ship.systems[i].powerReq;
				}
			}
		}

		return power;
	},

	getAllReactors: function(ship){
		var array = [];

		for (var i in ship.systems){
			if (ship.systems[i].name == "reactor"){
				array.push(ship.systems[i]);
			}
		}

		array.sort(function(a, b){
			if (a.location < b.location){
				return -1;
			}
			else {
				return 1;
			}
		});

		return array;
	},
	
	getReactorPower: function(ship, system){
		var fixedPower = false;
		var output;

		if (ship.base){
			var reactors = shipManager.power.getAllReactors(ship);

			output = reactors[0].output;

			for (var i = 0; i < reactors.length; i++){
				var reactor = reactors[i];
				if (! reactor.destroyed){
					output += reactors[i].outputMod;
					fixedPower = (fixedPower || reactors[i].fixedPower); //assume fixed power if ANY reactor gives fixed power

					if (reactor.criticals.length > 0){
						for( var j = 0; j < reactor.criticals.length; j++){
							if (reactor.criticals[j].phpclass == "OutputReducedOneTurn"){
								if (reactor.criticals[j].turn == gamedata.turn-1 || reactor.criticals[j].turn == gamedata.turn){
									output -= shipManager.power.getPowerNeedForSection(ship, reactor.location);
								}
							}
						}		
					}
				}
			}
		}
		else {			
			var reactor = shipManager.systems.getSystemByName(ship, "reactor");
				output = reactor.output + reactor.outputMod;
				fixedPower = reactor.fixedPower;
		}


		for (var s in ship.systems){
			var system = ship.systems[s];
			if(system.parentId > 0){ // This is a subsystem of a dual/duo weapon. Ignore
				continue;
			}
                        
			/*standard: add power for every system powered off
			  fixed: subtract power for every system powered on (instead!)
			*/
			if (!system.destroyed){ //destroyed system gets no power either way...
				if (fixedPower==true){ //for Mag-Grav reactor: all systems draw power, unless off or destroyed (accounted for in a moment)
					output -= system.powerReq;
				}
				var isOff = false;
				for (var i in system.power){
					var power = system.power[i];
					if (power.turn != gamedata.turn) continue;
					//types: 1:offline 2:boost, 3:overload
					if (power.type == 1) isOff = true; //just note the fact, so multiple disables do not count multiple times!
					if (power.type == 2){
						output -= shipManager.power.countBoostPowerUsed(ship, system);
					}				
					if (power.type == 3) output -= system.powerReq;
				}
				if (isOff == true){
					output += system.powerReq; //power off = increase available power
				}
			}
		}
		

		return output;
	
	},
	
	isPowerless: function(ship){
        var reactor = shipManager.systems.getSystemByName(ship, "reactor");
        
		if (shipManager.systems.isReactorDestroyed(ship) || shipManager.criticals.hasCritical(reactor, "ForcedOfflineOneTurn"))
			return true;
		
		var power = shipManager.power.getReactorPower(ship, reactor);
		
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
	
	isOfflineOnTurn: function(ship, system, turn){
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")){
			return true;		
		}
		if (shipManager.criticals.hasCriticalOnTurn(system, "ForcedOfflineOneTurn",turn)){
			return true;		
		}
	
		if ((system.powerReq > 0 || system.name == "reactor") && this.isPowerless(ship)){
			return true;
		}
	
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != turn) continue;				
			if (power.type == 1) return true;
		}
		
		return false;
	},
	
	
	isOffline: function(ship, system){
		return shipManager.power.isOfflineOnTurn(ship, system, gamedata.turn);
		/*
		if (shipManager.criticals.hasCritical(system, "ForcedOfflineOneTurn")){
			return true;		
		}

		if (shipManager.criticals.hasCritical(system, "ForcedOfflineForTurns")){
	//		var crit = shipManager.criticals.getCritical(system, "ForcedOfflineForTurns");

	//		if (gamedata.turn >= crit.turn && gamedata.turn <= crit.turn + crit.param){
				return true;
			}
	//	}

	
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
		*/
	},
	
	setOnline: function(ship, system){
            if(system.name == "graviticShield"){
                if(ship.checkShieldGenerator()){
                    for(var i in ship.systems){
                        var syst = ship.systems[i];
            
                        if(syst.name == "shieldGenerator"){
                            if(syst.destroyed){
                                window.confirm.error("You cannot activate shields. Your shield generator has been destroyed.");
                                return;
                            }
                            
                             if(shipManager.power.isOffline(ship, syst)){
                                 window.confirm.error("You cannot activate shields. Power up your shield generator first.");
                                 return;
                             }
                        }
                    }
                }
            }
            
            
		for (var i in system.power){
			var power = system.power[i];
			if (power.turn != gamedata.turn)
				continue;
				
			if (power.type == 1){
				system.power.splice(i, 1);
//				return;
			}
		}
	
	},
	
	getBoost: function(system){
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

	countTotalEffectiveEW: function(ship){
		var scanner = [];

		for (var i = 0; i < ship.systems.length; i++){
			var sys = ship.systems[i];

			//if (sys.name == "scanner" || sys.name == "elintScanner"){
			if(sys.isScanner()){
				var online = true;	
				for (var j in sys.power){
					var power = sys.power[j];

					if (power.turn != gamedata.turn){
						continue;
					}

					if (power.type == 1){
						online = false;
					}
				}

				if (online){
					scanner.push(sys);
				}
				else {
					continue;
				}
			}
		}

		scanner.sort(function(a, b){
			if (a.id > b.id){
				return 1;
			}
			else return -1;
		});

		var prim = scanner[0];

		var output = scanner.length * prim.output;
		var boost = 0;

		for (var i = 0; i < prim.power.length; i++){
			var power = prim.power[i];

			if (power.turn != gamedata.turn){
				continue;
			}

			if (power.type == 2){
				boost += power.amount;
			}
		}


		var effective = output + boost;
		return effective;
	},
		
	countBoostReqPower: function(ship, system){
	
        if (system.boostEfficiency.toString().search(/^[0-9]+$/) == 0){
            return system.boostEfficiency;
        }
        else if (system.boostEfficiency == "output+1"){
        	if (ship.base){
        		var ew = shipManager.power.countTotalEffectiveEW(ship);
        		return ew + 1;
        	}
        	else {
            	return system.output + shipManager.power.getBoost(system) + 1;
            }
        }
	},
	
	countBoostPowerUsed: function(ship, system){
		var boost = shipManager.power.getBoost(system);        2

        if (boost == 0 || shipManager.systems.isDestroyed(ship, system))
            return 0;
        
        if (system.boostEfficiency.toString().search(/^[0-9]+$/) == 0){
            return system.boostEfficiency * boost;
        }
        else if (system.boostEfficiency == "output+1"){
            var power = 0;

        	if (ship.base){
        		var ew = shipManager.power.countTotalEffectiveEW(ship);

	            for (var i = 1;i<=boost;i++){
	                power += ew;
	                ew--;
                }
        	}
        	else {
	            for (var i = 1;i<=boost;i++){
	                power += system.output + i;
                }
            }

			return power;
        }
        return 0;
	
	},
	
	canBoost: function(ship, system){
		
		//can always boost reactor (to overload)!
		if (system.name == 'reactor'){
			return true;
		}

		var has = shipManager.power.getReactorPower(ship, shipManager.systems.getSystemByName(ship, "reactor"));
		var need = shipManager.power.countBoostReqPower(ship, system);

		if (has >= need){
			return true;
		}
		else {
			return false;
		}
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
            if (!shipManager.power.canBoost(ship, system)){
                window.confirm.error("You do not have sufficient energy to boost this system.", function(){});
                return;
            }

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
        
		if (shipManager.power.isOverloading(ship, system))
			return;
			
		if (!shipManager.power.canOverload(ship, system))
			return;
			
		system.power.push({id:null, shipid:ship.id, systemid:system.id, type:3, turn:gamedata.turn, amount:0});
	},
	
	stopOverloading: function(ship, system){
            if(system.alwaysoverloading){
                return;
            }
	
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
            if(system.alwaysoverloading){
                return true;
            }
            
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
            //if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
	    if (system.isScanner() &&  ew.getUsedEW(ship) > 0){
                confirm.error("You need to unassign all electronic warfare before changing scanner power management.");
                return;
            }
            
            if(system.hasMaxBoost()){
                if(system.maxBoostLevel <= shipManager.power.getBoost(system)){
                    confirm.error("You can not boost this weapon any further.");
                    return;
                }
            }
            
            shipManager.power.setBoost(ship, system);
            shipWindowManager.setDataForSystem(ship, system);
            shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	}, 
	
	clickMinus: function(ship, system){
		
		//if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
		if (system.isScanner() &&  ew.getUsedEW(ship) > 0){
			
            confirm.error("You need to unassign all electronic warfare before changing scanner power management.");
            
            return;
		}
        
        shipManager.power.unsetBoost(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
		
		
	
	},

	offlineAll: function(e){
		var array = [];

		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));


		if (system.duoWeapon || system.dualWeapon){
			return;
		}
		

		for (var i = 0; i < ship.systems.length; i++){
			if (system.displayName === ship.systems[i].displayName){
				if (system.weapon){
					array.push(ship.systems[i]);
				}
			}
		}

		for (var i = 0; i < array.length; i++){
			if (gamedata.gamephase != 1)
				continue;
			
			if (shipManager.systems.isDestroyed(ship, array[i]))
				continue;
			
			if (ship.userid != gamedata.thisplayer)
				continue;
			
			if (shipManager.power.isOffline(ship, array[i]))
				continue;
		
            array[i].power.push({id:null, shipid:ship.id, systemid:array[i].id, type:1, turn:gamedata.turn, amount:0});

			shipManager.power.stopOverloading(ship, array[i]);
			shipWindowManager.setDataForSystem(ship, array[i]);
			shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
		}
	},

	onOfflineClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));
		
        if(system.duoWeapon){
            // create an iconMask at the top of the DOM for the system.
            var iconmask_element = document.createElement('div');
            iconmask_element.className = "iconmask";
            systemwindow.find(".icon").append(iconmask_element);
        }
        
        if(system.parentId > 0){
            system = shipManager.systems.getSystem(ship, system.parentId);
        }
                
		if (gamedata.gamephase != 1)
			return;
			
		
		if (shipManager.isDestroyed(ship) || shipManager.systems.isDestroyed(ship, system))
			return;
		
		if (ship.userid != gamedata.thisplayer)
			return;
		
		if (shipManager.power.isOffline(ship, system))
			return;
		
        system.power.push({id:null, shipid:ship.id, systemid:system.id, type:1, turn:gamedata.turn, amount:0});
		
		//if (system.name=="scanner" &&  ew.getUsedEW(ship) > 0){
		if (system.isScanner() &&  ew.getUsedEW(ship) > 0){	
			
			
            confirm.error("You need to unassign all electronic warfare before changing scanner power management.");
            
            return;
		}
                
        if(system.name=="shieldGenerator"){
            system.onTurnOff(ship);
        }
		
		shipManager.power.stopOverloading(ship, system);
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
	},

	onlineAll: function(e){
		var array = [];

		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));


		if (system.duoWeapon || system.dualWeapon){
			return;
		}

		for (var i = 0; i < ship.systems.length; i++){
			if (system.displayName === ship.systems[i].displayName){
				if (system.weapon){
					array.push(ship.systems[i]);
				}
			}
		}

		for (var i = 0; i < array.length; i++){

			if (shipManager.systems.isDestroyed(ship, array[i])){
				continue;
			}
			else if (shipManager.power.isOffline(ship, array[i])){
				shipManager.power.setOnline(ship, array[i]);
				shipWindowManager.setDataForSystem(ship, array[i]);
				shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "reactor"));
			}
		}
	},

	
	onOnlineClicked: function(e){
		e.stopPropagation();
		var shipwindow = $(".shipwindow").has($(this));
		var systemwindow = $(".system").has($(this));
		var ship = gamedata.getShip(shipwindow.data("ship"));
		var system = shipManager.systems.getSystem(ship, systemwindow.data("id"));

        if(system.duoWeapon){
            // remove the iconmask again.
            systemwindow.find(".iconmask").remove();
        }
        
        if(system.parentId > 0){
            system = shipManager.systems.getSystem(ship, system.parentId);
        }
                
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

        if(system.name=="shieldGenerator")
        {
            system.onTurnOn(ship);
        }

        if(system.dualWeapon || system.duoWeapon){
            for(var i in system.weapons){
                var weapon = system.weapons[i];
                
                if(weapon.duoWeapon){
                    for(var index in weapon.weapons){
                        var subweapon = weapon.weapons[index];
                        
                        shipManager.power.setOnline(ship, subweapon);
                        shipWindowManager.setDataForSystem(ship, subweapon);
                    }
                }else{
                    shipManager.power.setOnline(ship, weapon);
                    shipWindowManager.setDataForSystem(ship, weapon);
                }
            }
        }
                
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
