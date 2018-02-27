window.infowindow = {

    infoElement: null,

    initInfo: function(){
        if (infowindow.infoElement != null)
            return;
            
        infowindow.infoElement = $("#infowindow");
    
    },
    
    informPhase: function(timeout, callback){
        infowindow.initInfo();
        
        var e = infowindow.infoElement;
        
        var h = $("h2", e);
		
		if (gamedata.status == "FINISHED"){
			h.html("TURN " + gamedata.turn + ", GAME OVER");
		}else{
        
            if (gamedata.gamephase == -1){
				h.html(gamedata.getPhasename());
			}
            
			if (gamedata.gamephase == 4){
				h.html("TURN " + gamedata.turn + ", " + gamedata.getPhasename());
			}
			
			if (gamedata.gamephase == 3){
				h.html("TURN " + gamedata.turn + ", " + gamedata.getPhasename());
			}
			
			if (gamedata.gamephase == 2){
				h.html("TURN " + gamedata.turn + ", " + gamedata.getPhasename() +" "+ gamedata.getActiveShipName());
			}
			
			if (gamedata.gamephase == 1){
				h.html("TURN " + gamedata.turn + ", " + gamedata.getPhasename());
			}
		}
        
        infowindow.infoElement.fadeTo(1000, 0.65);
        infowindow.hideInfo(timeout);
        if (callback){
            setTimeout(callback, timeout+1000);
        }
    },
    
    hideInfo: function(timeout){
        setTimeout(function(){infowindow.infoElement.fadeTo(1000, 0).hide();}, timeout);
    },
    
    informFire: function(timeout, fire, firstcallback, callback){
        infowindow.initInfo();
        
        var e = infowindow.infoElement;
        var h = $("h2", e);
        var target = gamedata.getShip(fire.targetid);
        var shooter = gamedata.getShip(fire.shooterid);
        var weapon = shipManager.systems.getSystem(shooter, fire.weaponid);
        var header ="";
        
        
        if (fire.rolled <= fire.needed){
            header = shooter.name +" hitting " + target.name + " with " + weapon.displayName;
        }else{
            header = shooter.name +" missing " + target.name + " with " + weapon.displayName;
        }
        h.html(header);
        
       
        
        infowindow.infoElement.fadeTo(500, 0.65);
        infowindow.hideInfo(timeout);
        
        if (firstcallback){
            setTimeout(firstcallback, 1000);
        }
        
        if (callback){
            setTimeout(callback, timeout+1000);
        }
    }
    
    

} 
