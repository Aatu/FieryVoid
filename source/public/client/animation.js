/*
window.animation = {

    movementspeed: 10,
    turningspeed: 15,
    waitingElement: null,
    animating:null,
    shipAnimating:0,
    afterAnimationCallback: null,
    animationloopdelay:0,
    
    animationLoop: function(){

        if (window.webglScene) {
            return;
        }
        
        animation.animateActiveship();
        
        if (animation.animating){
            if(animation.animationloopdelay > 0){
                animation.animationloopdelay--;
            }else{
                animation.animating();
            }
        }

        requestAnimationFrame(animation.animationLoop);
    },
    
    setAnimating: function(animatefunction, callback){
        //console.log("setAnimating");
        animation.animationloopdelay = 0;
        animation.animating = animatefunction;
        gamedata.animating = true;
        animation.afterAnimationCallback = callback;
    
    },
    
    endAnimation: function(){
        //console.log("endAnimating");
        gamedata.animating = false;
        animation.animating = null;
        animation.shipAnimating = -1;
        if (animation.afterAnimationCallback)
            animation.afterAnimationCallback();
    },
    
    animateActiveship: function(){
    
            if (gamedata.animating)
                return false;
                
            var ship = gamedata.getActiveShip();
            
            if (ship == null){
                ship = gamedata.getSelectedShip();
            }
            
            if (ship == null)
                return;
            
            for (var a in ship.movement){
                var movement = ship.movement[a];
                
                if (movement.animated == false){
                    if (!movement.commit)
                            break;
                
                    if (animation.checkAnimationDone(movement)){
                        movement.animated = true;
                        gamedata.shipStatusChanged(ship);
                        ballistics.calculateBallisticLocations();
                        ballistics.drawBallistics();
                        shipManager.drawShip(ship);
                    }else{
                        ballistics.hideBallistics();
                        movement.animationtics ++;
                        shipManager.drawShip(ship);
                        break;
                    }
                }
            
            }
        
        
    },
    
    hasMoreforAnimate: function(ship, m){
        var found = false;
        for (var a in ship.movement){
            var movement = ship.movement[a];
            if (movement == m){
                found = true;
                continue;
            }
            
            if (found && movement.animated == false)
                return true;
            
        }
        
        return false;
    
    },
    
    animateShipMoves: function(){
        var done = false;
        var found = false;
        var shipchanged = false;
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
                    
            for (var a in ship.movement){
                var movement = ship.movement[a];
                
                if (movement.animated == false){
                    if (!movement.commit)
                        break;
                            
                    found = true;
                    if (animation.shipAnimating != ship.id){
                        animation.shipAnimating = ship.id
                        scrolling.scrollToShip(ship);
                        shipchanged = true;
                        combatLog.logMoves(ship);
                        
                    }
                    
                    if (animation.checkAnimationDone(movement)){
                        //console.log("animated: ship " +ship.name +" move: " +movement.type);
                        if (!animation.hasMoreforAnimate(ship, movement)){
                            done = true;
                        }
                        movement.animated = true;
                        gamedata.shipStatusChanged(ship);
                        ballistics.calculateBallisticLocations();
                        ballistics.drawBallistics();
                        shipManager.drawShip(ship);
                    }else{
                        //console.log(" - animating: ship " +ship.name +" move: " +movement.type);
                        ballistics.hideBallistics();
                        movement.animationtics ++;
                        shipManager.drawShip(ship);
                        break;
                    }
                }
            
            }
            
            if (found){
                if (done)
                    animation.animationloopdelay = 30;
                    
                break;
            }
            
        }
        
        if (!found)
            animation.endAnimation();
        
    },
    
    checkAnimationDone: function(movement){
    
        if ( movement.type=="move" || movement.type=="slipright" || movement.type=="slipleft"){
            return (movement.animationtics >= animation.movementspeed);
        }
        
        if (movement.type=="turnright" || movement.type=="turnleft" || movement.type=="pivotright" || movement.type=="pivotleft"){
            return (movement.animationtics >= animation.turningspeed);
        }

        return true;
    },
    
    animateWaiting: function(){
        if (animation.waitingElement == null){
            animation.waitingElement  = $("#phaseheader .waiting.value");
            animation.waitingElement.data("dots", 0);
            }
            
        var e = animation.waitingElement;
        var dots = e.data("dots");
        
        dots++;
        
        if (dots > 3)
            dots = 0;
        
        s = "";
        if (dots == 3)
            s = "...";
        if (dots == 2)
            s = "..";
        if (dots == 1)
            s = ".";            
        
        e.html("WAITING FOR TURN"+s);
        e.data("dots", dots);
    },
    
    cancelAnimation: function(){
        if (!gamedata.animating)
            return; 
        
        animation.endAnimation();
        
        for (var i in gamedata.ships){
            var ship = gamedata.ships[i];
            var found = false;
            for (var a in ship.movement){
                var move = ship.movement[a];
                
                if (move.commit !== true)
                    continue;
                
                if (!move.animated){
                    found = true;
                    move.animated = true;
                    move.animating = false;
                }
            }
            
            if (found)
                combatLog.logMoves(ship);
            
            gamedata.shipStatusChanged(ship);
            shipManager.drawShip(ship);
            
        }
        ballistics.calculateBallisticLocations();
        ballistics.drawBallistics();
        
       
    },
    
    replayMoveAnimation: function(e){
        if (gamedata.animating)
            return; 
        
        e.stopPropagation();
        var id = $(this).data("shipid");
        var ship = gamedata.getShip(id);
        
        for (var i in ship.movement){
            var move = ship.movement[i];
            if (move.turn !== gamedata.turn || move.type == "start" || move.type == "deploy")
                continue;
            
            move.animating = false;
            move.animated = false;
            move.animationtics = 0;
        }
        UI.shipMovement.hide();
        animation.setAnimating(animation.animateShipMoves, animation.resumeFromReplay);
    },
    
    resumeFromReplay: function(){
    }
    


}
*/