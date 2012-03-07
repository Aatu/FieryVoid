window.ew = {


    getScannerOutput: function(ship){
        var ret = 0;
        
        for (var i in ship.systems){
            var system = ship.systems[i];
			            
            if (system.name == "scanner"){
                ret += shipManager.systems.getOutput(ship, system);
            }
            
            if (shipManager.criticals.hasCritical(system, "RestrictedEW"))
				ret -= 2;
        
        }
        
        return ret;
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

			if (EW.type == "OEW"){
				var posE = shipManager.getShipPositionForDrawing(gamedata.getShip(EW.targetid));
				var a = (EW.amount == 1) ? 0.50 : 0.50;
				
				if (ship.userid == gamedata.thisplayer){
					canvas.strokeStyle = "rgba(20,80,128,"+a+")";
					canvas.fillStyle = "rgba(20,80,128,"+a+")";
				}else{
					canvas.strokeStyle = "rgba(125,12,12,"+a+")";
					canvas.fillStyle = "rgba(125,12,12,"+a+")";
				}
				var w = Math.ceil(( EW.amount )*gamedata.zoom*0.5);
				var start = mathlib.getPointInDistanceBetween(pos, posE, 38*gamedata.zoom);
				graphics.drawLine(canvas, start.x, start.y, posE.x, posE.y, w);
				graphics.drawCircleAndFill(canvas, posE.x, posE.y, 5*gamedata.zoom, 0 );
			}
		};
		
		return effect;
	
	},
    
    AssignOEW: function(ship){
	
		if (gamedata.waiting == true || gamedata.gamephase != 1)
			return; 
			
        var selected = gamedata.getSelectedShip();
        
		if (!selected)
			return;
			
        for (var i in selected.EW){
            var EWentry = selected.EW[i];
			
			if (EWentry.turn != gamedata.turn)
				continue;
				
            if (EWentry.type=="OEW" && EWentry.targetid == ship.id)
                return;
        }
        var left = ew.getDefensiveEW(selected);
        
        if (left < 1)
            return;
            
		if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(ship, "CnC"), "RestrictedEW")){
			var allOEW = ew.getAllOffensiveEW(selected);
			var all = ew.getScannerOutput(selected);
			
			if (allOEW+1 > all*0.5)
				return false;
		}
			
            
        selected.EW.push({shipid:selected.id, type:"OEW", amount:1, targetid:ship.id, turn:gamedata.turn});
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
		
        if (entry == "CCEW"){
            ship.EW.push({shipid:ship.id, type:"CCEW", amount:1, targetid:-1, turn:gamedata.turn});
        }else{
            
            if (shipManager.criticals.hasCritical(shipManager.systems.getSystemByName(ship, "CnC"), "RestrictedEW") && entry.type == "OEW"){
				var allOEW = ew.getAllOffensiveEW(selected);
				var all = ew.getScannerOutput(selected);
				
				if (allOEW+1 > all*0.5)
					return false;
			}
           
            
                
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
        
        if (entry == "CCEW"){
            return;
        }
        
        entry.amount--;
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
	
	}
    
    

}
