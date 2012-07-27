window.deployment = {
    

    drawDeploymentAreas: function(canvas){
        var selectedShip = gamedata.getSelectedShip();
        
        if (!selectedShip)
            return;
        
        var dep = deployment.getValidDeploymentArea(selectedShip);
        
        canvas.strokeStyle = "rgba(160,250,100,0.5)";
        canvas.fillStyle = "rgba(160,250,100,0.1)";
                    
        graphics.drawBox(canvas, hexgrid.hexCoToPixel(dep.x,dep.y), (dep.w)*hexgrid.hexWidth(), (dep.h)*hexgrid.hexHeight(), 1);
    },
    
    getValidDeploymentArea: function(ship){
    
        if (ship.team == 1){
            return ({x:-30, y:0, w:16, h:50});
        }else{
            return ({x:30, y:0, w:16, h:50});
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