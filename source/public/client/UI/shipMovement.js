"use strict";

window.UI = {

    shipMovement: {

        iniated: false,
        moveElement: null,
        turnleftElement: null,
        turnrightElement: null,
        slipleftElement: null,
        sliprightElement: null,
        uiElement: null,
        lastUITime: null,
        currentPosition: null,
        currentHeading: null,
		
		halfphaseElement: null,
		contractionElement: null,
		morecontractionElement: null,
		lesscontractionElement: null,				
		emergencyrollElement: null,


        initMoveUI: function initMoveUI() {
            if (UI.shipMovement.iniated == true) return;

            console.log("INIT")

            UI.shipMovement.uiElement = $("#shipMovementUI");
            var ui = UI.shipMovement.uiElement;
            UI.shipMovement.moveElement = $("#move", ui);
            UI.shipMovement.speedElement = UI.shipMovement.moveElement.find(".speedvalue");
            UI.shipMovement.turnleftElement = $("#turnleft", ui);
            UI.shipMovement.turnrightElement = $("#turnright", ui);
            
            UI.shipMovement.turnIntoPivotLeftElement = $("#turnIntoPivotLeft", ui);
            UI.shipMovement.turnIntoPivotRightElement = $("#turnIntoPivotRight", ui);

            UI.shipMovement.slipleftElement = $("#slipleft", ui);
            UI.shipMovement.sliprightElement = $("#slipright", ui);

            UI.shipMovement.pivotleftElement = $("#pivotleft", ui);
            UI.shipMovement.pivotrightElement = $("#pivotright", ui);

            UI.shipMovement.rotateleftElement = $("#rotateleft", ui);
            UI.shipMovement.rotaterightElement = $("#rotateright", ui);

            UI.shipMovement.rollElement = $("#roll", ui);
            UI.shipMovement.emergencyrollElement = $("#emergencyroll", ui);            
            
            UI.shipMovement.jinkElement = $("#jink", ui);
            UI.shipMovement.jinkvalueElement = UI.shipMovement.jinkElement.find(".jinkvalue");
			
            UI.shipMovement.accElement = $("#accelerate", ui);
            UI.shipMovement.deaccElement = $("#deaccelerate", ui);

            UI.shipMovement.morejinkElement = $("#morejink", ui);
            UI.shipMovement.lessjinkElement = $("#lessjink", ui);
            UI.shipMovement.cancelElement = $("#cancel", ui);
			
			UI.shipMovement.halfphaseElement = $("#halfphase", ui);

			UI.shipMovement.contractionElement = $("#contraction", ui);			
            UI.shipMovement.morecontractionElement = $("#morecontraction", ui);
            UI.shipMovement.lesscontractionElement = $("#lesscontraction", ui);
            UI.shipMovement.contractionvalueElement = UI.shipMovement.contractionElement.find(".contractionvalue");            			

            UI.shipMovement.cancelElement.on("click touchstart contextmenu", UI.shipMovement.cancelCallback);
            UI.shipMovement.moveElement.on("click touchstart contextmenu", UI.shipMovement.moveCallback);

            UI.shipMovement.turnrightElement.on("click touchstart", UI.shipMovement.turnrightCallback);
            UI.shipMovement.turnleftElement.on("click touchstart", UI.shipMovement.turnleftCallback);
            UI.shipMovement.sliprightElement.on("click touchstart", UI.shipMovement.sliprightCallback);
            UI.shipMovement.slipleftElement.on("click touchstart", UI.shipMovement.slipleftCallback);

            UI.shipMovement.pivotleftElement.on("click touchstart", UI.shipMovement.pivotleftCallback);
            UI.shipMovement.pivotrightElement.on("click touchstart", UI.shipMovement.pivotrightCallback);

            UI.shipMovement.rotateleftElement.on("click touchstart", UI.shipMovement.rotateleftCallback);
            UI.shipMovement.rotaterightElement.on("click touchstart", UI.shipMovement.rotaterightCallback);

            UI.shipMovement.rollElement.on("click touchstart", UI.shipMovement.rollCallback);
            UI.shipMovement.emergencyrollElement.on("click touchstart", UI.shipMovement.emergencyrollCallback);
            
            UI.shipMovement.accElement.on("click touchstart", UI.shipMovement.accelCallback);
            UI.shipMovement.deaccElement.on("click touchstart", UI.shipMovement.deaccCallback);

            UI.shipMovement.morejinkElement.on("click touchstart", UI.shipMovement.morejinkCallback);
            UI.shipMovement.lessjinkElement.on("click touchstart", UI.shipMovement.lessjinkCallback);
			
            UI.shipMovement.halfphaseElement.on("click touchstart", UI.shipMovement.halfphaseCallback);

            UI.shipMovement.morecontractionElement.on("click touchstart", UI.shipMovement.morecontractionCallback);
            UI.shipMovement.lesscontractionElement.on("click touchstart", UI.shipMovement.lesscontractionCallback);

            UI.shipMovement.turnIntoPivotLeftElement.on("click touchstart", UI.shipMovement.turnIntoPivotLeftCallback);
            UI.shipMovement.turnIntoPivotRightElement.on("click touchstart", UI.shipMovement.turnIntoPivotRightCallback);

            jQuery('#shipMovementUI div').on('mousedown touchend touchmove', cancelEvent);
            jQuery('#shipMovementUI div').on('mouseup touchend touchmove', cancelEvent);

            function cancelEvent(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            UI.shipMovement.iniated = true;
        },

        checkUITimeout: function checkUITimeout() {
            if (UI.shipMovement.lastUITime == null) {
                UI.shipMovement.lastUITime = new Date().getTime();
                return false;
            }

            var answer = new Date().getTime() - UI.shipMovement.lastUITime;

            UI.shipMovement.lastUITime = new Date().getTime();

            return answer < 100;
        },

        eventmask: function eventmask(e) {
            e.handled = true;
            cancelEvent(e);
        },

        cancelCallback: function cancelCallback(e) {
            UI.shipMovement.callbackHandler.cancelCallback(e);
        },

        morejinkCallback: function morejinkCallback(e) {
            UI.shipMovement.callbackHandler.morejinkCallback(e);
        },

        lessjinkCallback: function lessjinkCallback(e) {
            UI.shipMovement.callbackHandler.lessjinkCallback(e);
        },

        halfphaseCallback: function halfphaseCallback(e) {
            UI.shipMovement.callbackHandler.halfphaseCallback(e);
        },

        morecontractionCallback: function morecontractionCallback(e) {
            UI.shipMovement.callbackHandler.morecontractionCallback(e);
        },

        lesscontractionCallback: function lesscontractionCallback(e) {
            UI.shipMovement.callbackHandler.lesscontractionCallback(e);
        },

        accelCallback: function accelCallback(e) {
            UI.shipMovement.callbackHandler.accelCallback(e);
        },

        deaccCallback: function deaccCallback(e) {
            UI.shipMovement.callbackHandler.deaccCallback(e);
        },

        rollCallback: function rollCallback(e) {
            UI.shipMovement.callbackHandler.rollCallback(e);
        },

        emergencyrollCallback: function emergencyrollCallback(e) {
            UI.shipMovement.callbackHandler.emergencyrollCallback(e);
        },

        pivotrightCallback: function pivotrightCallback(e) {
            e.stopPropagation();
            UI.shipMovement.pivotCallback(e, true);
        },

        pivotleftCallback: function pivotleftCallback(e) {
            e.stopPropagation();
            UI.shipMovement.pivotCallback(e, false);
        },

        pivotCallback: function pivotCallback(e, right) {
            UI.shipMovement.callbackHandler.pivotCallback(e, right);
        },

        rotateleftCallback: function rotateleftCallback(e) {
            e.stopPropagation();
            UI.shipMovement.rotateCallback(e, true);
        },

        rotaterightCallback: function rotaterightCallback(e) {
            e.stopPropagation();
            UI.shipMovement.rotateCallback(e, false);
        },

        rotateCallback: function rotateCallback(e, right) {
            UI.shipMovement.callbackHandler.rotateCallback(e, right);
        },

        sliprightCallback: function sliprightCallback(e) {
            e.stopPropagation();
            UI.shipMovement.slipCallback(e, true);
        },

        slipleftCallback: function slipleftCallback(e) {
            e.stopPropagation();
            UI.shipMovement.slipCallback(e, false);
        },

        slipCallback: function slipCallback(e, right) {
            UI.shipMovement.callbackHandler.slipCallback(e, right);
        },

        turnrightCallback: function turnrightCallback(e) {
            e.stopPropagation();
            UI.shipMovement.turnCallback(e, true);
        },

        turnleftCallback: function turnleftCallback(e) {
            e.stopPropagation();
            UI.shipMovement.turnCallback(e, false);
        },

        turnCallback: function turnCallback(e, right) {
            UI.shipMovement.callbackHandler.turnCallback(e, right);
        },

        moveCallback: function moveCallback(e) {
            UI.shipMovement.callbackHandler.moveCallback(e);
        },

        turnIntoPivotLeftCallback: function turnIntoPivotLeftCallback(e) {
            UI.shipMovement.callbackHandler.turnIntoPivotCallback(e, false);
        },

        turnIntoPivotRightCallback: function turnIntoPivotRightCallback(e) {
            UI.shipMovement.callbackHandler.turnIntoPivotCallback(e, true);
        },

        drawShipMovementUI: function drawShipMovementUI(ship, callbackHandler) {

            UI.shipMovement.callbackHandler = callbackHandler;

            UI.shipMovement.initMoveUI();
            var shipHeading = 0; //shipManager.getShipDoMAngle(ship);
            var angle = shipHeading;
            var dis = 80; //hexgrid.hexHeight();

            var ui = UI.shipMovement.uiElement;
            var shipX = ship.movement[ship.movement.length - 1].x;
            var shipY = ship.movement[ship.movement.length - 1].y;

            var pos = { x: 400, y: 400 };
            //ui.css("top", pos.y +"px").css("left", pos.x +"px");

            var move = UI.shipMovement.moveElement;
            var s = 40;

            if (shipManager.movement.canMove(ship)) {
                UI.shipMovement.drawUIElement(move, pos.x, pos.y, s * 1.3, dis * 1.4, angle, "img/move.png", "movecanvas", shipHeading);
                UI.shipMovement.speedElement.html(shipManager.movement.getRemainingMovement(ship));
            } else {
                move.hide();
            }

            if (gamedata.gamephase == -1 && shipManager.movement.canChangeSpeed(ship)) {
                UI.shipMovement.drawUIElement(move, pos.x, pos.y, s * 1.3, dis * 1.4, angle, "img/move.png", "movecanvas", shipHeading);
                UI.shipMovement.speedElement.html(shipManager.movement.getRemainingMovement(ship));
            }

            dis = 55;
            var acc = UI.shipMovement.accElement;
            if (shipManager.movement.canChangeSpeed(ship)) {
                UI.shipMovement.drawUIElement(acc, pos.x, pos.y, 16, dis * 1.4, angle, "img/plus.png", "acceleratecanvas", 0);
            } else {
                acc.hide();
            }

            dis = 40;
            var deacc = UI.shipMovement.deaccElement;
            if (shipManager.movement.canChangeSpeed(ship)) {
                UI.shipMovement.drawUIElement(deacc, pos.x, pos.y, 16, dis * 1.4, angle, "img/minus.png", "deacceleratecanvas", 0);
            } else {
                deacc.hide();
            }
            angle = mathlib.addToDirection(shipHeading, -60);
            dis = 60;

            var turnleft = UI.shipMovement.turnleftElement;
            if (shipManager.movement.canTurn(ship, false)) {
                UI.shipMovement.drawUIElement(turnleft, pos.x, pos.y, s, dis * 1.4, angle, "img/turnleft.png", "turnleftcanvas", shipHeading);
            } else {
                turnleft.hide();
            }

            dis = 85;
            var slipleft = UI.shipMovement.slipleftElement;
            if (shipManager.movement.canSlip(ship, false)) {
                UI.shipMovement.drawUIElement(slipleft, pos.x, pos.y, s * 0.7, dis * 1.4, angle, "img/move.png", "slipleftcanvas", mathlib.addToDirection(shipHeading, -60));
            } else {
                slipleft.hide();
            }


            // TURN INTO PIVOT LEFT
            dis = 70;   
            angle = mathlib.addToDirection(shipHeading, -35);
            var turnPivotLeft = UI.shipMovement.turnIntoPivotLeftElement;
            if (shipManager.movement.canTurnIntoPivot(ship, false)) {
                UI.shipMovement.drawUIElement(turnPivotLeft, pos.x, pos.y, s, dis * 1.4, angle, "img/turnIntoPivotLeft.png", "turnIntoPivotLeftCanvas", shipHeading);
            } else {
                turnPivotLeft.hide();
            }

            dis = 60;
            angle = mathlib.addToDirection(shipHeading, 60);
            var turnright = UI.shipMovement.turnrightElement;

            if (shipManager.movement.canTurn(ship, true)) {
                UI.shipMovement.drawUIElement(turnright, pos.x, pos.y, s, dis * 1.4, angle, "img/turnright.png", "turnrightcanvas", shipHeading);
            } else {
                turnright.hide();
            }

            dis = 85;
            var slipright = UI.shipMovement.sliprightElement;
            if (shipManager.movement.canSlip(ship, true)) {
                UI.shipMovement.drawUIElement(slipright, pos.x, pos.y, s * 0.7, dis * 1.4, angle, "img/move.png", "sliprightcanvas", mathlib.addToDirection(shipHeading, 60));
            } else {
                slipright.hide();
            }

            // TURN INTO PIVOT RIGHT

            dis = 70;   
            angle = mathlib.addToDirection(shipHeading, 35);
            var turnPivotRight = UI.shipMovement.turnIntoPivotRightElement;
            if (shipManager.movement.canTurnIntoPivot(ship, true)) {
                UI.shipMovement.drawUIElement(turnPivotRight, pos.x, pos.y, s, dis * 1.4, angle, "img/turnIntoPivotRight.png", "turnIntoPivotRightCanvas", shipHeading);
            } else {
                turnPivotRight.hide();
            }

            dis = 60;
            angle = mathlib.addToDirection(shipHeading, -90);
            var pivotleft = UI.shipMovement.pivotleftElement;
            if (shipManager.movement.canPivot(ship, false)) {
                var icon = "img/pivotleft.png";
                if (shipManager.movement.isEndingPivot(ship, false)) icon = "img/pivotleft_active.png";
                UI.shipMovement.drawUIElement(pivotleft, pos.x, pos.y, s, dis * 1.4, angle, icon, "pivotleftcanvas", shipHeading);
            } else {
                pivotleft.hide();
            }

            angle = mathlib.addToDirection(shipHeading, 90);
            var pivotright = UI.shipMovement.pivotrightElement;
            if (shipManager.movement.canPivot(ship, true)) {
                var icon = "img/pivotright.png";
                if (shipManager.movement.isEndingPivot(ship, true)) icon = "img/pivotright_active.png";
                UI.shipMovement.drawUIElement(pivotright, pos.x, pos.y, s, dis * 1.4, angle, icon, "pivotrightcanvas", shipHeading);
            } else {
                pivotright.hide();
            }

            // Base Rotation
            var rotateleft = UI.shipMovement.rotateleftElement;
            var rotateright = UI.shipMovement.rotaterightElement;
            if (shipManager.movement.canRotate(ship)) {
                angle = mathlib.addToDirection(shipHeading, -100);
                dis = 60;
                var icon = "img/rotateleft.png";
                UI.shipMovement.drawUIElement(rotateleft, pos.x, pos.y, s, dis * 1.4, angle, icon, "rotateleftcanvas", shipHeading);

                dis = 60;
                angle = mathlib.addToDirection(shipHeading, 100);
                var icon = "img/rotateright.png";
                UI.shipMovement.drawUIElement(rotateright, pos.x, pos.y, s, dis * 1.4, angle, icon, "rotaterightcanvas", shipHeading);
            } else {
                rotateleft.hide();
                rotateright.hide();
            }

            dis = 30;
            angle = mathlib.addToDirection(shipHeading, 180);
			var checkHeading = shipManager.getShipDoMAngle(ship);
			            
            var roll = UI.shipMovement.rollElement;
            var emergencyroll = UI.shipMovement.emergencyrollElement;            
            if (shipManager.movement.canRoll(ship)) {
                var icon = "img/rotate.png";
                if (shipManager.movement.isRolling(ship)) icon = "img/rotate_active.png";

                dis += 30;
                UI.shipMovement.drawUIElement(roll, pos.x, pos.y, s, dis * 1.4, angle, icon, "rollcanvas", shipHeading);
                emergencyroll.hide()
            } else if (shipManager.movement.canEmergencyRoll(ship)){
                var icon = "img/emergencyRoll.png";
				// Check if the ship is facing left (adjust condition as needed)
				var rollIconAngle = mathlib.addToDirection(shipHeading, 180);;
				if (checkHeading >= 90 && checkHeading <= 270) {	
				    // Swap angles for the morecontraction and lesscontraction buttons
				    icon = "img/emergencyRollFlipped.png";
				} 
                dis += 30;
                UI.shipMovement.drawUIElement(emergencyroll, pos.x, pos.y, s, dis * 1.4, angle, icon, "emergencyrollcanvas", shipHeading);
                roll.hide();                            
			}else {
                roll.hide();
                emergencyroll.hide()                
            }

            var morejink = UI.shipMovement.morejinkElement;
            if (shipManager.movement.canJink(ship, 1)) {
                dis += 10;
                UI.shipMovement.drawUIElement(morejink, pos.x, pos.y, 16, dis * 1.4, angle, "img/plus.png", "morejinkcanvas", 0);
            } else {
                morejink.hide();
            }

            var jink = UI.shipMovement.jinkElement;
            if (shipManager.movement.canJink(ship, 0)) {
                var icon = "img/jink.png";
                dis += 20;

                UI.shipMovement.jinkvalueElement.html(shipManager.movement.getJinking(ship));
                UI.shipMovement.drawUIElement(jink, pos.x, pos.y, s, dis * 1.4, angle, icon, "jinkcanvas", shipHeading);
            } else {
                jink.hide();
            }

            var lessjink = UI.shipMovement.lessjinkElement;
            if (shipManager.movement.canJink(ship, -1)) {
                dis += 22;
                UI.shipMovement.drawUIElement(lessjink, pos.x, pos.y, 16, dis * 1.4, angle, "img/minus.png", "lessjinkcanvas", 0);
            } else {
                lessjink.hide();
            }

			//Shadows half phasing
            var halfphase = UI.shipMovement.halfphaseElement;
            if (shipManager.movement.canHalfPhase(ship)) {
                UI.shipMovement.drawUIElement(halfphase, pos.x, pos.y, 50, 35 * 1.4, angle, "img/HalfPhase.png", "halfphasecanvas", 0);
            } else {
                halfphase.hide();
            }

            var cancel = UI.shipMovement.cancelElement;
            if (shipManager.movement.hasDeletableMovements(ship) && weaponManager.canCombatTurn(ship)) {
                dis += 26;
                UI.shipMovement.drawUIElement(cancel, pos.x, pos.y, 30, dis * 1.4, angle, "img/cancel.png", "cancelcanvas", 0);
            } else {
                cancel.hide();
            }
 
            var contraction = UI.shipMovement.contractionElement;
            if (shipManager.movement.canContract(ship, 0)) {
                var icon = "img/contraction.png";
                UI.shipMovement.contractionvalueElement.html(shipManager.movement.getContraction(ship));
                UI.shipMovement.drawUIElement(contraction, pos.x, pos.y, 40, 30 * 1.4, angle, icon, "contractioncanvas", shipHeading);
            } else {
                contraction.hide();
            }
 
			var moreContractionAngle = mathlib.addToDirection(shipHeading, 218);
			var lessContractionAngle = mathlib.addToDirection(shipHeading, 142);

			// Check if the ship is facing left (adjust condition as needed)
			if (checkHeading >= 90 && checkHeading <= 270) {	
			    // Swap angles for the morecontraction and lesscontraction buttons
			    moreContractionAngle = mathlib.addToDirection(shipHeading, 142);
			    lessContractionAngle = mathlib.addToDirection(shipHeading, 218);
			} 
			                    
            var morecontraction = UI.shipMovement.morecontractionElement;
            if (shipManager.movement.canContract(ship, 1)) {
                UI.shipMovement.drawUIElement(morecontraction, pos.x, pos.y, 16, 38 * 1.4, moreContractionAngle, "img/plus.png", "morecontractioncanvas", 0);
            } else {
                morecontraction.hide();
            }
          
            var lesscontraction = UI.shipMovement.lesscontractionElement;
            if (shipManager.movement.canContract(ship, -1)) {
                UI.shipMovement.drawUIElement(lesscontraction, pos.x, pos.y, 16, 38 * 1.4, lessContractionAngle, "img/minus.png", "lesscontractioncanvas", 0);
            } else {
                lesscontraction.hide();
            }

            ui.show();
        },

        reposition: function reposition(position, heading) {
            var element = UI.shipMovement.uiElement;

            var currentPosition = UI.shipMovement.currentPosition;

            if (currentPosition && currentPosition.x === position.x && currentPosition.y === position.y && UI.shipMovement.currentHeading === heading) {
                return;
            }

            element.css("top", position.y + "px").css("left", position.x + "px").css("transform", "rotate(" + heading + "deg)");
            jQuery(".speedvalue.value").css("transform", "rotate(" + -heading + "deg)").css("display", "block");

			//align jinking value with player:
			jQuery(".jinkvalue.value").css("transform", "rotate(" + -heading + "deg)").css("display", "block");

			//align contraction value with player:
			jQuery(".contractionvalue.value").css("transform", "rotate(" + -heading + "deg)").css("display", "block");

            UI.shipMovement.currentPosition = position;
            UI.shipMovement.currentHeading = heading;
        },

        drawUIimage: function drawUIimage(canvas, path, s, angle) {
            var img = new Image();
            img.src = path;

            $(img).on("load", function () {
                graphics.clearSmallCanvas(canvas);
                graphics.drawAndRotate(canvas, s, s, s * 2, s * 2, angle, img);
            });
        },

        drawUIElement: function drawUIElement(e, x, y, s, dis, angle, path, canvasid, shipHeading) {
            var UIpos = mathlib.getPointInDirection(dis, -angle, x, y);
            e.css("top", UIpos.y - y - s * 0.5 + "px").css("left", UIpos.x - x - s * 0.5 + "px");
            e.css("width", s + "px").css("height", s + "px");
            e.show();
            //$("#"+canvaid).css("top", "px").css("left", "px");

            var canvas = window.graphics.getCanvas(canvasid);
            UI.shipMovement.drawUIimage(canvas, path, s, shipHeading);
        },

        hide: function hide() {
            $("#shipMovementUI").hide();
        },

        show: function show() {
            $("#shipMovementUI").show();
        }

    }

};