window.ew = {


    getScannerOutput: function(ship){
        var ret = 0;
        
        if (shipManager.isAdrift(ship))
            return 0;
        
        for (var i in ship.systems){
            var system = ship.systems[i];
            
            if (system.outputType === "EW"){
                var output = shipManager.systems.getOutput(ship, system);
            
                if (output > 0)
                    ret += output;
            }
            
            if (system.name == "CnC" && shipManager.criticals.hasCritical(system, "RestrictedEW"))
                ret -= 2;
            
        }
    
        
        return (ret > 0) ? ret : 0;
    },
    
    isTargetDistByOtherElint: function(elint, target){
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            
            if(ship.faction == elint.faction && ship.id != elint.id){
                for (var i in ship.EW){
                    var entry = ship.EW[i];
                    
                    if (entry.turn != gamedata.turn)
                        continue;

                    if (entry.type == "DIST"){
                        return true;
                    }
                }                
            }
        }
        
        return false;
    },
    
    getUsedEW: function(ship){
        var used = 0;
    
        for (var i in ship.EW){
            var EWentry = ship.EW[i];
			if (EWentry.turn != gamedata.turn)
				continue;
				
            used += EWentry.amount;
        
        }

        return used;
    },  
    
    getDefensiveEW: function(ship){
	
		var listed = ew.getListedDEW(ship);
		
		if (listed == 0)
			return (ew.getScannerOutput(ship) - ew.getUsedEW(ship));
    
		return listed;
    },
    
    getTargetingEW: function(ship, target){
		
		if (target.flight){
			return ew.getCCEW(ship);
		}else{
			return ew.getOffensiveEW(ship, target);
		}
		
	},
	
	getOffensiveEW: function(ship, target){
	
		for (var i in ship.EW){
			var entry = ship.EW[i];
			if (entry.turn != gamedata.turn)
				continue;
			
			if (entry.type == "OEW" && entry.targetid == target.id)
				return entry.amount;
		}
		
		return 0;
		
	},
	
	getAllOffensiveEW: function(ship){
		var amount = 0;
		for (var i in ship.EW){
			var entry = ship.EW[i];
			if (entry.turn != gamedata.turn)
				continue;
			
			if (entry.type == "OEW")
				amount += entry.amount;
		}
		
		return amount;
		
	},
    
    getNumOfOffensiveTargets: function(ship){
		var amount = 0;
		for (var i in ship.EW){
			var entry = ship.EW[i];
			if (entry.turn != gamedata.turn)
				continue;
			
			if (entry.type == "OEW")
				amount ++;
		}
		
		return amount;
		
	},
    
    getEWByType: function(type, ship, target){
        for (var i in ship.EW){
			var entry = ship.EW[i];
			if (entry.turn != gamedata.turn)
				continue;
			
            if (target && entry.targetid != target.id)
                continue;
            
            if (entry.type == type){
		return entry.amount;
            }
        }
	
	return 0;
    },
	
	convertUnusedToDEW: function(ship){
		var listed = ew.getListedDEW(ship);
		
		if (listed > 0)
			return;
			
		var dew = (ew.getScannerOutput(ship) - ew.getUsedEW(ship));
		ship.EW.push({shipid:ship.id, type:"DEW", amount:dew, targetid:-1, turn:gamedata.turn});
		
		
	},
	
	getListedDEW: function(ship){
		
		for (var i in ship.EW){
			var entry = ship.EW[i];
			if (entry.turn != gamedata.turn)
				continue;
			
			if (entry.type == "DEW")
				return entry.amount;
		}
		
		return 0;
	},
    
    getBDEW: function(ship){
        for (var i in ship.EW){
            var EWentry = ship.EW[i];
			if (EWentry.turn != gamedata.turn)
				continue;
				
            if (EWentry.type == "BDEW"){
                return EWentry.amount;
            }        
        }
        
        return 0;
    },
    
    getBDEWentry: function(ship){
    
        for (var i in ship.EW){
            var EWentry = ship.EW[i];
			if (EWentry.turn != gamedata.turn)
				continue;
				
            if (EWentry.type == "BDEW"){
                return EWentry;
            }        
        }
        
        return null;
    },
    
    getCCEW: function(ship){
        
        for (var i in ship.EW){
            var EWentry = ship.EW[i];
			if (EWentry.turn != gamedata.turn)
				continue;
				
            if (EWentry.type == "CCEW"){
                return EWentry.amount;
            }        
        }
        
        return 0;
        
    },
    
    getCCEWentry: function(ship){
    
        for (var i in ship.EW){
            var EWentry = ship.EW[i];
			if (EWentry.turn != gamedata.turn)
				continue;
				
            if (EWentry.type == "CCEW"){
                return EWentry;
            }        
        }
        
        return null;
    },
    
    RemoveEWEffects: function(){
        for(var i = EWindicators.indicators.length-1; i >= 0; i--){  
            if(EWindicators.indicators[i].type == "EW"){              
                EWindicators.indicators.splice(i,1);                 
            }
        }                
        
    },
    
    RemoveEWEffectsFromShip: function(ship){
     
        for(var i = EWindicators.indicators.length-1; i >= 0; i--){  
            if(EWindicators.indicators[i].ship == ship && EWindicators.indicators[i].type == "EW"){              
                EWindicators.indicators.splice(i,1);                 
            }
        }

        
    }, 
    
    adEWindicators: function(ship){
        ew.RemoveEWEffectsFromShip(ship);
        var ewEffects = ew.makeEWindicators(ship);
        
        if (ewEffects.length > 0)
            EWindicators.indicators = EWindicators.indicators.concat(ewEffects);
      
    },    
	
	adHostileOEWindicatiors: function(ship){
		var ewEffects = Array();

		
		for (var i in gamedata.ships){
			var other = gamedata.ships[i];
							
			for (var a in other.EW){
				var EW = other.EW[a];
				if (EW.type == "OEW" && EW.targetid == ship.id){
					ewEffects.push(ew.makeEWindicator(other, EW));
				}
			}
		}
		
		if (ewEffects.length > 0)
            EWindicators.indicators = EWindicators.indicators.concat(ewEffects);
	
	
	},
    
    makeEWindicators: function(ship){

        var efArray = Array();
        for (var i in ship.EW){
            var EW = ship.EW[i];
			if (EW.turn != gamedata.turn)
				continue;
 
            if (EW.type == "CCEW" || EW.type == "DEW")
                continue;
                
            
            efArray.push(ew.makeEWindicator(ship, EW));
        }
        
        return efArray;
        
    },
	
	makeEWindicator: function(ship, EW){
		var effect = {};
            
         
		effect.ship = ship;
		effect.type = "EW"
		effect.EW = EW;
		effect.draw = function(self){
			var EW = self.EW;
			var canvas = EWindicators.getEwCanvas();
			
			var pos = shipManager.getShipPositionForDrawing(ship);
            

			if (EW.type == "OEW" || EW.type == "SOEW" || EW.type == "SDEW" || EW.type == "DIST"){
				var posE = shipManager.getShipPositionForDrawing(gamedata.getShip(EW.targetid));
				var a = (EW.amount == 1) ? 0.50 : 0.50;
				
                if (EW.type == "OEW"){
                    if (ship.userid == gamedata.thisplayer){
                        canvas.strokeStyle = "rgba(225,225,250,"+a+")";
                        canvas.fillStyle = "rgba(225,225,250,"+a+")";
                    }else{
                        canvas.strokeStyle = "rgba(125,12,12,"+a+")";
                        canvas.fillStyle = "rgba(125,12,12,"+a+")";
                    }
                }else if (EW.type == "SOEW"){
                    if (ship.userid == gamedata.thisplayer){
                        canvas.strokeStyle = "rgba(100,220,240,"+a+")";
                        canvas.fillStyle = "rgba(100,220,240,"+a+")";
                    }else{
                        canvas.strokeStyle = "rgba(125,12,12,"+a+")";
                        canvas.fillStyle = "rgba(125,12,12,"+a+")";
                    }
                }else if (EW.type == "SDEW"){
                    if (ship.userid == gamedata.thisplayer){
                        canvas.strokeStyle = "rgba(160,250,100,"+a+")";
                        canvas.fillStyle = "rgba(160,250,100,"+a+")";
                    }else{
                        canvas.strokeStyle = "rgba(125,12,12,"+a+")";
                        canvas.fillStyle = "rgba(125,12,12,"+a+")";
                    }
                }else if (EW.type == "DIST"){
                    if (ship.userid == gamedata.thisplayer){
                        canvas.strokeStyle = "rgba(255,115,40,"+a+")";
                        canvas.fillStyle = "rgba(255,115,40,"+a+")";
                    }else{
                        canvas.strokeStyle = "rgba(255,115,40,"+a+")";
                        canvas.fillStyle = "rgba(255,115,40,"+a+")";
                    }
                }
                
				var w = Math.ceil(( EW.amount )*gamedata.zoom*0.5);
				var start = mathlib.getPointInDistanceBetween(pos, posE, 38*gamedata.zoom);
				graphics.drawLine(canvas, start.x, start.y, posE.x, posE.y, w);
				graphics.drawCircleAndFill(canvas, posE.x, posE.y, 5*gamedata.zoom, 0 );
			}
            
            if (EW.type == "BDEW"){
                var a = EW.amount *0.01;
                a += 0.1;
				if (ship.userid == gamedata.thisplayer){
					canvas.strokeStyle = "rgba(160,250,100,0.5)";
                    canvas.fillStyle = "rgba(160,250,100,"+a+")";
				}else{
					canvas.strokeStyle = "rgba(125,12,12,0.5)";
					canvas.fillStyle = "rgba(125,12,12,"+a+")";
				}
				
				graphics.drawCircleAndFill(canvas, pos.x, pos.y, 20.5*hexgrid.hexWidth(), 2 );
			}
		};
        
        
		
		return effect;
	
	},
    
    AssignOEW: function(ship, type){
		if (gamedata.waiting == true || gamedata.gamephase != 1)
			return; 
        
        if (!type) 
            type = "OEW";
			
        var selected = gamedata.getSelectedShip();
        
		if (!selected)
			return;
			
        for (var i in selected.EW){
            var EWentry = selected.EW[i];
			
			if (EWentry.turn != gamedata.turn)
				continue;
				
            if (EWentry.type==type && EWentry.targetid == ship.id)
                return;
        }
        var left = ew.getDefensiveEW(selected);
        
        if (left < 1 || (type == "DIST" && left < 3)){
            return;
        }
        
        if (ship.osat == false){            
    		if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(selected, "CnC"), "RestrictedEW")){
    			var def = ew.getDefensiveEW(selected);
    			var all = ew.getScannerOutput(selected);
    			
    			if (def-1 < all*0.5)
    				return false;
    		}
        }

		var amount = 1;
        if (type == "DIST")
            amount = 3;
            
        selected.EW.push({shipid:selected.id, type:type, amount:amount, targetid:ship.id, turn:gamedata.turn});
        ew.adEWindicators(selected);
        gamedata.shipStatusChanged(selected);
    },
    
    buttonAssignEW: function(e){
		if (gamedata.waiting == true || gamedata.gamephase != 1)
			return; 
			
        var e = $(this).parent();
        var ship = e.data("ship");
        var entry = e.data("EW");
	var left = ew.getDefensiveEW(ship);
		if (left < 1)
            return;
        
        if (ship.osat == false){    
            if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(ship, "CnC"), "RestrictedEW")){
    			var def = ew.getDefensiveEW(ship);
    			var all = ew.getScannerOutput(ship);
    			
    			if (def-1 < all*0.5)
    				return false;
    		}
        }
		
        if (entry == "CCEW"){
            ship.EW.push({shipid:ship.id, type:"CCEW", amount:1, targetid:-1, turn:gamedata.turn});
        }else if (entry == "BDEW"){
            if(ew.getEWByType("DIST", ship) > 0 || ew.getEWByType("SOEW", ship) > 0 || ew.getEWByType("SDEW", ship) > 0){
                window.confirm.error("You cannot use blanket protection together with other ELINT functions.", function(){});
                return;
            }else{
                ship.EW.push({shipid:ship.id, type:"BDEW", amount:1, targetid:-1, turn:gamedata.turn});
                ew.adEWindicators(ship);
            }
        }else if (entry.type == "DIST"){
            if (left < 3)
                return;
            entry.amount += 3;
        }else if (entry.type == "SOEW"){
            return;
        }else{    
            entry.amount++;
        }
        gamedata.shipStatusChanged(ship);
        drawEntities();
    },
    
    buttonDeassignEW: function(e){
		if (gamedata.waiting == true || gamedata.gamephase != 1)
			return; 
			
        var e = $(this).parent();
        var ship = e.data("ship");
        var entry = e.data("EW");
        
        if (entry == "CCEW" || entry == "BDEW"){
            return;
        }
        
        amount = 1;
        if (entry.type == "DIST")
            amount = 3;
        
        entry.amount -= amount;
        if (entry.amount<1){
            var i = $.inArray(entry, ship.EW);
            ship.EW.splice(i, 1);
            ew.RemoveEWEffects(ship);
            ew.adEWindicators(ship);
            e.data("EW", "");
        }
        gamedata.shipStatusChanged(ship);
        drawEntities();
    },
	
	removeEW: function(ship){
	
		for(var i = ship.EW.length-1; i >= 0; i--){  
            var ew =  ship.EW[i];
			if (ew.turn == gamedata.turn)
				ship.EW.splice(i, 1);
        }     
	
	},
    checkInELINTDistance: function(ship, target, distance){
        var shipPos = shipManager.getShipPositionInWindowCo(ship);
        var targetPos = shipManager.getShipPositionInWindowCo(target)
       
        if (!distance)
            distance = 30;
        
        return (mathlib.getDistanceHex(shipPos, targetPos)<=distance);
    },
    
    getSupportedOEW: function(ship, target){
        var jammer = shipManager.systems.getSystemByName(target, "jammer");

        if(jammer != null &&
            shipManager.systems.getOutput(target, jammer) > 0
            && !shipManager.systems.isDestroyed(target, jammer)
            && !shipManager.power.isOffline(target, jammer))
        {
            // Ships with active jammers are immune to SOEW
            return 0;
        }

        var amount = 0;
        
        for (var i in gamedata.ships){
            var elint = gamedata.ships[i];
            if (elint == ship || !shipManager.isElint(elint))
                continue;
            
            if (!ew.checkInELINTDistance(target, elint, 30))
                continue;
            
            if (!ew.getEWByType("SOEW", elint, ship))
                continue;
            
            var foew = ew.getEWByType("OEW", elint, target) * 0.5;
            

            if (foew > amount)
               amount = foew;
        }
        
        if(ship.flight){
            // fighters only receive half the amount of SOEW
            return amount * 0.5;
        }
        
        return amount;
    },
    
    getSupportedDEW: function(ship){
        var amount = 0;
        var elints = gamedata.getElintShips();
        for (var i in elints){
            var elint = elints[i];
            if (elint.id === ship.id)
                continue;
            
            var fdew = ew.getEWByType("SDEW", elint, ship)*0.5;

            if (fdew > amount)
               amount = fdew;
        }
        
        return amount;
    },
    
    getSupportedBDEW: function(ship){
        var amount = 0;
        var elints = gamedata.getElintShips();
        for (var i in elints){
            var elint = elints[i];
            
            if(ship.faction != elint.faction)
                continue;
            
            if ( !ew.checkInELINTDistance(ship, elint, 20))
                continue;
            
            var fdew = ew.getEWByType("BDEW", elint)*0.25;

            if (fdew > amount)
               amount = fdew;
        }
    
        return amount;
    },
    
    getDistruptionEW: function(ship){
        
        var amount = 0;
        for (var i in gamedata.ships){
            var elint = gamedata.ships[i];
            if (elint == ship || !shipManager.isElint(elint))
                continue;
            
            var fdew = ew.getEWByType("DIST", elint, ship)/3;

            //if (fdew > amount)
            amount += fdew;
        }
        
        var num = ew.getNumOfOffensiveTargets(ship);
        if (num > 0)
            return amount/num;
        
        return amount;
    },
    
    showAllEnemyEW: function(){
        if (gamedata.gamephase > 1){
            
            for (var i in gamedata.ships){
                var ship = gamedata.ships[i];
                
                if (gamedata.isMyShip(ship))
                    continue;
                
                ew.adEWindicators(ship);
            }
			drawEntities();
		}
    },
    
    showAllFriendlyEW: function(){
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];

            if (!gamedata.isMyShip(ship))
                continue;

            ew.adEWindicators(ship);
        }
        drawEntities();
    }
    
    

}
