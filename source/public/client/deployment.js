window.deployment = {
    

    drawDeploymentAreas: function(canvas){
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
    },
    
    drawDep: function(canvas, slot){
        var pos = hexgrid.hexCoToPixel(slot.depx,slot.depy);
        if (slot.deptype == "box"){
            graphics.drawBox(canvas, pos, (slot.depwidth)*hexgrid.hexWidth(), (slot.depheight)*hexgrid.hexHeight(), 1);
        }else{
            
            graphics.drawCircle(canvas, pos.x, pos.y, (slot.depwidth)*hexgrid.hexWidth(), 1);
        }
    },
    
    getValidDeploymentArea: function(ship){
    
        for (var i in gamedata.slots){
            var slot = gamedata.slots[i];
            if (slot.slot == ship.slot){
                return slot;
            }
        }
    },
    
    onHexClicked: function(hexpos){
        var selectedShip = gamedata.getSelectedShip();
        
        if (!selectedShip || !gamedata.isMyShip(selectedShip))
            return;
        
        var dep = deployment.getValidDeploymentArea(selectedShip);
        
        if (hexpos.x <= (dep.x+(dep.w/2)) && hexpos.x > (dep.x-(dep.w/2))){
            if (hexpos.y <= (dep.y+(dep.h/2)) && hexpos.y >= (dep.y-(dep.h/2))){
                if (shipManager.getShipsInSameHex(selectedShip, hexpos).length == 0){
                    shipManager.movement.deploy(selectedShip, hexpos);
                    gamedata.checkGameStatus();
                }
            }
        }
        
    },
    
    validateDeploymentPos: function(ship){
        return true;
        var dep = deployment.getValidDeploymentArea(ship);
        var hexpos = shipManager.getShipPosition(ship);

        if (hexpos.x <= (dep.x+(dep.w/2)) && hexpos.x > (dep.x-(dep.w/2))){
            if (hexpos.y <= (dep.y+(dep.h/2)) && hexpos.y >= (dep.y-(dep.h/2))){
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
    
};