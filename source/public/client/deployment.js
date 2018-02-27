"use strict";

window.deployment = {
    initialized: false

    /*
    drawDeploymentAreas: function(canvas){
        gameInfoManager.prepare();
        deployment.drawDeploymentForSelectedShip(canvas);
    },
     drawDeploymentForSelectedShip: function(canvas){
        
        var selectedShip = gamedata.getSelectedShip();
        
        if (!selectedShip)
            return;
        
        canvas.strokeStyle = "rgba(160,250,100,0.5)";
        canvas.fillStyle = "rgba(160,250,100,0.1)";
        var slot = deployment.getValidDeploymentArea(selectedShip);
        deployment.drawDep(canvas, slot);
         var side1Players = new Array();
        var side2Players = new Array();
        
        for (var i in gamedata.slots){
            var otherslot = gamedata.slots[i];
            if (slot.slot == otherslot.slot){
                var index = side1Players.length;
                side1Players[index] = otherslot.playername;
                
                continue;
            }
            
            if (slot.team != otherslot.team)
                continue;
            
            var index = side1Players.length;
            side1Players[index] = otherslot.playername;
            
            canvas.strokeStyle = "rgba(100,170,250,0.5)";
            canvas.fillStyle = "rgba(100,170,250,0.1)";
            deployment.drawDep(canvas, otherslot);
       }
       
       
       
       for (var i in gamedata.slots){
            var otherslot = gamedata.slots[i];
            if (slot.slot == otherslot.slot)
                continue;
            
            if (slot.team == otherslot.team)
                continue;
             var index = side2Players.length;
            side2Players[index] = otherslot.playername;
             canvas.strokeStyle = "rgba(250,100,100,0.5)";
            canvas.fillStyle = "rgba(250,100,100,0.1)";
            deployment.drawDep(canvas, otherslot);
       }
       
       var welcomeString = "The opponents: ";
       
       for (var i in side1Players){
           var name = side1Players[i];
           
           if(i > 0){
               welcomeString += " and ";
           }
           
           welcomeString += name;
       }
       
       welcomeString += " versus ";
       
       for (var i in side2Players){
           var name = side2Players[i];
           
           if(i > 0){
               welcomeString += " and ";
           }
           
           welcomeString += name;
       }
       
       if(!window.deployment.initialized){
           confirm.error(welcomeString);
           window.deployment.initialized = true;
       }
    },
    
    drawDep: function(canvas, slot){
        var pos = hexgrid.hexCoToPixel(slot.depx,slot.depy);
        if (slot.deptype == "box"){
            graphics.drawBox(canvas, pos, (slot.depwidth)*hexgrid.hexWidth(), (slot.depheight)*hexgrid.hexHeight(), 1);
        }else if (slot.deptype=="distance"){
            graphics.drawHollowCircleAndFill(canvas, pos.x, pos.y, (slot.depwidth)*hexgrid.hexWidth(), (slot.depheight)*hexgrid.hexWidth(), 1);
            pos.y -= Math.floor(((slot.depwidth+slot.depheight)/2)*hexgrid.hexWidth());
        }else{
            graphics.drawCircleAndFill(canvas, pos.x, pos.y, (slot.depwidth)*hexgrid.hexWidth(), 1); 
        }
        var fontsize = 50*gamedata.zoom;
        if (fontsize < 20)
            fontsize = 20;
        
        canvas.fillStyle = canvas.strokeStyle;
        canvas.font         = 'italic '+fontsize+'px sans-serif';
        canvas.textAlign = "center";
        canvas.textBaseline = 'middle';
        canvas.fillText((slot.name+" ("+slot.points+" points)"), pos.x, pos.y);
        canvas.fillText(("Available on turn " + slot.depavailable), pos.x, pos.y+fontsize);
        
    },
    
    getValidDeploymentArea: function(ship){
    
        for (var i in gamedata.slots){
            var slot = gamedata.slots[i];
            if (slot.slot == ship.slot){
                return slot;
            }
        }
    },
     /*
    onHexClicked: function(hexpos){
        var selectedShip = gamedata.getSelectedShip();
        
        if (!selectedShip || !gamedata.isMyShip(selectedShip))
            return;
        
        var dep = deployment.getValidDeploymentArea(selectedShip);
        
        if (deployment.validateDeploymentPos(selectedShip, hexpos)){
            if (shipManager.getShipsInSameHex(selectedShip, hexpos).length == 0){
                shipManager.movement.deploy(selectedShip, hexpos);
                gamedata.checkGameStatus();
            }
        }
        
    },
     
    validateDeploymentPos: function(ship, hexpos){
        if (!hexpos)
            hexpos = shipManager.getShipPosition(ship);
         var slot = deployment.getValidDeploymentArea(ship);
        hexpos = hexgrid.hexCoToPixel(hexpos.x, hexpos.y);
        var deppos = hexgrid.hexCoToPixel(slot.depx, slot.depy);
        
        if (slot.deptype == "box"){
            var depw = slot.depwidth*hexgrid.hexWidth();
            var deph = slot.depheight*hexgrid.hexHeight();
            if (hexpos.x <= (deppos.x+(depw/2)) && hexpos.x > (deppos.x-(depw/2))){
                if (hexpos.y <= (deppos.y+(deph/2)) && hexpos.y >= (deppos.y-(deph/2))){
                    return true;
                }
            }
        }else if (slot.deptype=="distance"){
            if (mathlib.distance(deppos.x, deppos.y, hexpos.x, hexpos.y) <= slot.depheight*hexgrid.hexWidth()){
                if (mathlib.distance(deppos.x, deppos.y, hexpos.x, hexpos.y) > slot.depwidth*hexgrid.hexWidth()){
                    return true;
                }
            }
        }else{
            if (mathlib.distance(deppos.x, deppos.y, hexpos.x, hexpos.y) <= slot.depwidth*hexgrid.hexWidth()){
                return true;
            }
        }
        return false;
    },
    
    validateAllDeployment: function(){
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            if (!gamedata.isMyShip(ship))
                continue;
            
            if (!deployment.validateDeploymentPos(ship))
                return false;
        }
    
        return true;
    }
    */
};