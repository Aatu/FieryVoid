window.UI = {

    shipMovement:  {
    
        iniated: false,
        moveElement:null,
        turnleftElement:null,
        turnrightElement:null,
        slipleftElement:null,
        sliprightElement:null,
        uiElement:null,
        lastUITime: null,
        
        initMoveUI: function(){
            if (UI.shipMovement.iniated == true)
                return;
                
                UI.shipMovement.uiElement = $("#shipMovementUI");
                var ui = UI.shipMovement.uiElement;
                UI.shipMovement.moveElement = $("#move", ui);
                UI.shipMovement.speedElement = UI.shipMovement.moveElement.find(".speedvalue");
                UI.shipMovement.turnleftElement =  $("#turnleft", ui);
                UI.shipMovement.turnrightElement = $("#turnright", ui);
                UI.shipMovement.slipleftElement =  $("#slipleft", ui);
                UI.shipMovement.sliprightElement = $("#slipright", ui);
                
                UI.shipMovement.pivotleftElement =  $("#pivotleft", ui);
                UI.shipMovement.pivotrightElement = $("#pivotright", ui);
                UI.shipMovement.rollElement = $("#roll", ui);
                UI.shipMovement.jinkElement = $("#jink", ui);
                UI.shipMovement.jinkvalueElement = UI.shipMovement.jinkElement.find(".jinkvalue");
                
                UI.shipMovement.accElement = $("#accelerate", ui);
                UI.shipMovement.deaccElement = $("#deaccelerate", ui);
                
                UI.shipMovement.morejinkElement = $("#morejink", ui);
                UI.shipMovement.lessjinkElement = $("#lessjink", ui);
                UI.shipMovement.cancelElement = $("#cancel", ui);
                
                UI.shipMovement.cancelElement.on("click", UI.shipMovement.cancelCallback);
                
                UI.shipMovement.moveElement.on("click", UI.shipMovement.moveCallback);
                UI.shipMovement.turnrightElement.on("click", UI.shipMovement.turnrightCallback);
                UI.shipMovement.turnleftElement.on("click", UI.shipMovement.turnleftCallback);
                UI.shipMovement.sliprightElement.on("click", UI.shipMovement.sliprightCallback);
                UI.shipMovement.slipleftElement.on("click", UI.shipMovement.slipleftCallback);
                
                UI.shipMovement.pivotleftElement.on("click", UI.shipMovement.pivotleftCallback);
                UI.shipMovement.pivotrightElement.on("click", UI.shipMovement.pivotrightCallback);
                UI.shipMovement.rollElement.on("click", UI.shipMovement.rollCallback);
                
                UI.shipMovement.accElement.on("click", UI.shipMovement.accelCallback);
                UI.shipMovement.deaccElement.on("click", UI.shipMovement.deaccCallback);
                
                UI.shipMovement.morejinkElement.on("click", UI.shipMovement.morejinkCallback);
                UI.shipMovement.lessjinkElement.on("click", UI.shipMovement.lessjinkCallback);
                
                
                
                UI.shipMovement.iniated = true;
        },
        
        checkUITimeout: function(){
            if (UI.shipMovement.lastUITime == null){
                UI.shipMovement.lastUITime  = ((new Date()).getTime());
                return false;
            }
                
            
            var answer = (((new Date()).getTime()) - UI.shipMovement.lastUITime );
    
            UI.shipMovement.lastUITime  = ((new Date()).getTime());
    
            return (answer < 100);
             
        },
        
        eventmask: function(e){
            e.handled = true;
            cancelEvent(e);
        },
        
        cancelCallback: function(e){
			e.stopPropagation();
                
            if (gamedata.gamephase == 2){
                var ship = gamedata.getActiveShip();
                shipManager.movement.deleteMove(ship);
            }

            if (gamedata.gamephase == 3){
                var ship = gamedata.getSelectedShip();
                shipManager.movement.deleteMove(ship);
            }
		},
        
        morejinkCallback: function(e){
			e.stopPropagation();
                
            var ship = gamedata.getActiveShip();
            shipManager.movement.doJink(ship, 1);
		},
		
        lessjinkCallback: function(e){
			e.stopPropagation();
                
            var ship = gamedata.getActiveShip();
            shipManager.movement.doJink(ship, -1);
		},        
        
        accelCallback: function(e){
            e.stopPropagation();
                
            var ship = gamedata.getActiveShip();
            shipManager.movement.changeSpeed(ship, true);
        },
        
        deaccCallback: function(e){
            e.stopPropagation();
                
            var ship = gamedata.getActiveShip();
            shipManager.movement.changeSpeed(ship, false);
        },
        
        rollCallback: function(e){
            e.stopPropagation();
                
            var ship = gamedata.getActiveShip();
            shipManager.movement.doRoll(ship);
            
        }, 
        
        pivotrightCallback: function(e){
            e.stopPropagation();
			//console.log("pivotCallback1");
            UI.shipMovement.pivotCallback(e, true);
        }, 
        
        pivotleftCallback: function(e){
            e.stopPropagation();
			//console.log("pivotCallback1");
            UI.shipMovement.pivotCallback(e, false);
        }, 
        
        pivotCallback: function(e, right){
			if (UI.shipMovement.checkUITimeout())
                return false;
			//console.log("pivotCallback2");
                
            var ship = gamedata.getActiveShip();
            
            if (gamedata.gamephase == 3)
                ship = gamedata.getSelectedShip();
            
            shipManager.movement.doPivot(ship, right);
            
        },
        
        sliprightCallback: function(e){
            e.stopPropagation();
            UI.shipMovement.slipCallback(e, true);
        }, 
        
        slipleftCallback: function(e){
            e.stopPropagation();
            UI.shipMovement.slipCallback(e, false);
        }, 
        
        slipCallback: function(e, right){
                        
            var ship = gamedata.getActiveShip();
                    
            shipManager.drawShip(ship);     
            shipManager.movement.doSlip(ship, right);
            
        },
        
        turnrightCallback: function(e){
            e.stopPropagation();
            UI.shipMovement.turnCallback(e, true);
        }, 
        
        turnleftCallback: function(e){
            e.stopPropagation();
            UI.shipMovement.turnCallback(e, false);
        }, 
        
        turnCallback: function(e, right){
            var ship = gamedata.getActiveShip();
            if (!ship)
                ship = gamedata.getSelectedShip();
            
            shipManager.movement.doTurn(ship, right);
                
            shipManager.drawShip(ship);
            
        }, 
        
        moveCallback: function(e){
            e.stopPropagation();
            if (UI.shipMovement.checkUITimeout())
                return false;
                
            
    
            var ship = gamedata.getActiveShip();
            
            shipManager.movement.doMove(ship);
        
            shipManager.drawShip(ship);
        
            
        },
        
        drawShipMovementUI: function(ship){
        
            if (!shipManager.hasAnimationsDone(ship)){
                UI.shipMovement.hide();
                return;
            }
            
            
                    
            UI.shipMovement.initMoveUI();
            var shipHeading = shipManager.getShipDoMAngle(ship);
            var angle = shipHeading;
            var dis = 80; //hexgrid.hexHeight();
            
            var ui = UI.shipMovement.uiElement;
            var shipX = ship.movement[ship.movement.length-1].x;
            var shipY = ship.movement[ship.movement.length-1].y;
            
            var pos = shipManager.getShipPositionInWindowCo(ship);
            ui.css("top", pos.y +"px").css("left", pos.x +"px");
            
            var move = UI.shipMovement.moveElement;
            var s = 40;
            
            if (shipManager.movement.canMove(ship)){
                UI.shipMovement.drawUIElement(move, pos.x, pos.y, s*1.3, dis*1.4, angle, "img/move.png", "movecanvas", shipHeading);
                UI.shipMovement.speedElement.html(shipManager.movement.getRemainingMovement(ship));
            }else{
                move.hide();
            }
            
            dis = 55;
            var acc = UI.shipMovement.accElement;
            if (shipManager.movement.canChangeSpeed(ship)){
                UI.shipMovement.drawUIElement(acc, pos.x, pos.y, 16, dis*1.4, angle, "img/plus.png", "acceleratecanvas", 0);
            }else{
                acc.hide();
            }
            
            dis = 40;
            var deacc = UI.shipMovement.deaccElement;
            if (shipManager.movement.canChangeSpeed(ship)){
                UI.shipMovement.drawUIElement(deacc, pos.x, pos.y, 16, dis*1.4, angle, "img/minus.png", "deacceleratecanvas", 0);
            }else{
                deacc.hide();
            }
            angle = mathlib.addToDirection(shipHeading, -60);
            dis = 60;
            
            
            var turnleft = UI.shipMovement.turnleftElement;
            if (shipManager.movement.canTurn(ship, false)){
                UI.shipMovement.drawUIElement(turnleft, pos.x, pos.y, s, dis*1.4, angle, "img/turnleft.png", "turnleftcanvas", shipHeading);
            }else{
                turnleft.hide();
            }
            
            dis = 85;
            var slipleft = UI.shipMovement.slipleftElement;
            if (shipManager.movement.canSlip(ship, false)){
                UI.shipMovement.drawUIElement(slipleft, pos.x, pos.y, s*0.7, dis*1.4, angle, "img/move.png", "slipleftcanvas", mathlib.addToDirection(shipHeading, -60));
            }else{
                slipleft.hide();
            }
            
            dis = 60;
            angle = mathlib.addToDirection(shipHeading, 60);
            var turnright = UI.shipMovement.turnrightElement;
            
            if (shipManager.movement.canTurn(ship, true)){
                UI.shipMovement.drawUIElement(turnright, pos.x, pos.y, s, dis*1.4, angle, "img/turnright.png", "turnrightcanvas", shipHeading);
            }else{
                turnright.hide();
            }
            
            dis = 85;
            var slipright = UI.shipMovement.sliprightElement;
            if (shipManager.movement.canSlip(ship, true)){
                UI.shipMovement.drawUIElement(slipright, pos.x, pos.y, s*0.7, dis*1.4, angle, "img/move.png", "sliprightcanvas", mathlib.addToDirection(shipHeading, 60));
            }else{
                slipright.hide();
            }
            dis = 60;
            angle = mathlib.addToDirection(shipHeading, -90);
            var pivotleft = UI.shipMovement.pivotleftElement;
            if (shipManager.movement.canPivot(ship, false)){
                var icon = "img/pivotleft.png";
                if (shipManager.movement.isEndingPivot(ship, false))
                    icon = "img/pivotleft_active.png";
                UI.shipMovement.drawUIElement(pivotleft, pos.x, pos.y, s, dis*1.4, angle, icon, "pivotleftcanvas", shipHeading);
            }else{
                pivotleft.hide();
            }
            
            angle = mathlib.addToDirection(shipHeading, 90);
            var pivotright = UI.shipMovement.pivotrightElement;
            if (shipManager.movement.canPivot(ship, true)){
                var icon = "img/pivotright.png";
                if (shipManager.movement.isEndingPivot(ship, true))
                    icon = "img/pivotright_active.png";
                UI.shipMovement.drawUIElement(pivotright, pos.x, pos.y, s, dis*1.4, angle, icon, "pivotrightcanvas", shipHeading);
            }else{
                pivotright.hide();
            }
            
            
            
            dis = 30;
            angle = mathlib.addToDirection(shipHeading, 180);
            var roll = UI.shipMovement.rollElement;
            if (shipManager.movement.canRoll(ship)){
                var icon = "img/rotate.png";
                if (shipManager.movement.isRolling(ship))
                    icon = "img/rotate_active.png";
                
                dis += 30;
                UI.shipMovement.drawUIElement(roll, pos.x, pos.y, s, dis*1.4, angle, icon, "rollcanvas", shipHeading);
            }else{
                roll.hide();
            }
            
            var morejink = UI.shipMovement.morejinkElement;
            if (shipManager.movement.canJink(ship, 1)){
				dis += 10;
                UI.shipMovement.drawUIElement(morejink, pos.x, pos.y, 16, dis*1.4, angle, "img/plus.png", "morejinkcanvas", 0);
            }else{
                morejink.hide();
            }
            
            
            var jink = UI.shipMovement.jinkElement;
            if (shipManager.movement.canJink(ship, 0)){
                var icon = "img/jink.png";
                dis += 20;
                           
                UI.shipMovement.jinkvalueElement.html(shipManager.movement.getJinking(ship));
                UI.shipMovement.drawUIElement(jink, pos.x, pos.y, s, dis*1.4, angle, icon, "jinkcanvas", shipHeading);
            }else{
                jink.hide();
            }
            
            var lessjink = UI.shipMovement.lessjinkElement;
            if (shipManager.movement.canJink(ship, -1)){
				dis += 22;
                UI.shipMovement.drawUIElement(lessjink, pos.x, pos.y, 16, dis*1.4, angle, "img/minus.png", "lessjinkcanvas", 0);
            }else{
                lessjink.hide();
            }
            
            
            var cancel = UI.shipMovement.cancelElement;
            if (shipManager.movement.hasDeletableMovements(ship) && weaponManager.canCombatTurn(ship)){
                dis += 26;
                UI.shipMovement.drawUIElement(cancel, pos.x, pos.y, 30, dis*1.4, angle, "img/cancel.png", "cancelcanvas", 0);
            }else{
                cancel.hide();
            }
            
            
            
            
            
            ui.show();
        },
        
        drawUIimage: function(canvas, path, s, angle){
        
            var img = new Image();
            img.src = path; 
            
            $(img).on("load", function(){
                graphics.clearSmallCanvas(canvas);
                graphics.drawAndRotate(canvas, s, s, s*2, s*2, angle, img)
            });
        
        },
        
        drawUIElement: function(e, x, y, s, dis, angle, path, canvasid, shipHeading){
        
            var UIpos = mathlib.getPointInDirection( dis, angle, x, y);
            e.css("top", UIpos.y - y - s*0.5+"px").css("left", UIpos.x - x - s*0.5+"px");
            e.css("width", s+"px").css("height", s+"px");
            e.show();
            //$("#"+canvaid).css("top", "px").css("left", "px");
            
            var canvas = window.graphics.getCanvas(canvasid);
            UI.shipMovement.drawUIimage(canvas, path, s, shipHeading);
        },
        
        hide: function(){
            if ($("#shipMovementUI").hide)
                $("#shipMovementUI").hide();
        },
    
        
        
        
    
    }

}
