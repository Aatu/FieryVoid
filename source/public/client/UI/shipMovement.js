"use strict";

window.UI = {

    shipMovement: {

        /* ⭐ THE EXTENDED-TURN ARROW COLOUR (EXTENDED_TURNS_PLAN.md §5.1). Both the Begin and the Complete
           Extended Turn icons are the turn arrows' own art painted in this colour; change it here.
           Any CSS colour string works ("#ff8c00", "orange", "rgb(255, 140, 0)").
           The thrust panel's "owed next turn" figures use this colour too (ShipThrust.js). */
        extendedTurnColour: "#ff8c00",

        /* ...and their opacity, 0 (invisible) to 1 (as drawn). 1 matches the other movement icons,
           whose art is itself slightly see-through. Applies to the icons only, not the panel text. */
        extendedTurnOpacity: 0.7,

        /* ⭐ THE MINESWEEPING MODE ICON (MINE_DETECTION_PLAN.md Q7, §6.8): the art, how faint it is while
           the mode is OFF (0-1), and the purple glow around it while it is ON. Change them here. */
        minesweepIcon: "img/mineIcon.png",
        minesweepOffOpacity: 0.8,
        minesweepGlowColour: "#b77df0",
        //...and fainter still once the unit has moved into a hex with the mode off, when it can no longer be turned on.
        minesweepUnavailableOpacity: 0.3,
        //...and the space in px between it and the roll icon / jink + astern of it.
        minesweepGap: 12,

        /* ⭐ THE CHARGE ICON (OFFICERS_PLAN.md Stage 28, an Expert Warrior's; user requests after play test 4509): the art - in the
           movement icons' green until a unit is locked -, the colour it is painted in once one is (red; the turn arrows offering the
           charge's snap turn and the crosshair on the unit charged wear it too - ShipIcon.setChargeMarked), how faint it is while a charge
           may be declared but is not, the glow while it waits for the enemy unit to be clicked (pulsing - tactical.css) or holds one
           (steady), the crosshair laid over the art once it holds one, and how see-through the crosshair on the unit charged is (0
           invisible - 1 solid). Change them here. */
        chargeIcon: "img/chargeIcon.png",
        chargeColour: "#e8432e",
        chargeOffOpacity: 0.85,
        chargeGlowColour: "#ffb347",
        chargeReticleColour: "#fff4dc",
        chargeTargetOpacity: 0.6,
        //...and fainter once the run has begun, when it can no longer be taken back
        chargeLockedOpacity: 0.75,

        /* ⭐ THE WAR LEADER ICON (OFFICERS_PLAN.md Stage 34, an Expert War Leader's Wrath and Center of Glory in the Firing phase; user request
           2026-10-29): the art (white, painted in one of these), its yellow while nothing is picked, the red once an enemy unit is his wrath and
           the green once a unit of his fleet is the center of glory - the crosshairs on those units wear the same two
           (ShipIcon.setWarLeaderMarked; the wrath's a deeper red than the Charge's, as a Drazi ship may carry both) -, how faint it is while
           nothing is picked, its glow while it waits for the unit to be clicked (pulsing - tactical.css), and how see-through the crosshairs
           are (0 invisible - 1 solid). Change them here. */
        warLeaderIcon: "img/warLeader.png",
        //...and how far astern of the ship's centre it stands, in px (was 42 - user, game 4523: too close to the ship icon)
        warLeaderDistance: 80,
        warLeaderColour: "#f2c230",
        warLeaderWrathColour: "#c8143c",
        warLeaderGloryColour: "#3cc84e",
        warLeaderOffOpacity: 0.85,
        warLeaderGlowColour: "#ffe08a",
        warLeaderTargetOpacity: 0.65,

        /* ⭐ THE GRAVITIC ROTATE ICON (OFFICERS_PLAN.md Stage 31, an Expert Graviton Controller's gravitic drive rotation): the art - in the gravitic
           turn arrows' yellow -, how faint it is while the rotation may be made (a click rolls the die), and the glow it wears once the drive
           has been rotated this turn (when it is no longer clickable). Change them here. */
        gravRotateIcon: "img/rotateIcon.png",
        gravRotateReadyOpacity: 0.85,
        gravRotateGlowColour: "#f2d27a",

        /* ⭐ THE REDLINE ICON (OFFICERS_PLAN.md Stage 32, a Redline Pilot's afterburners): the art (the user's), how far astern of the flight's
           centre it stands in px (before the jink stack - user, play test 4515), how faint it is while the flight may burn, the glow it wears while
           burning, how it looks on the turn after, while the engines cool (not clickable), and the red the pivot arrows wear in the Firing phase
           while a pivot would be his, made without the thrust (pivotArrowTint - the Charge's snap-turn red). Change them here. */
        redlineIcon: "img/redlineIcon.png",
        redlineFlightDistance: 61,
        redlineReadyOpacity: 0.85,
        redlineGlowColour: "#ff7a1a",
        redlineCoolingOpacity: 0.8,
        redlineCoolingFilter: "grayscale(1) brightness(1.5)",
        redlinePivotColour: "#e8432e",

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

            UI.shipMovement.extendTurnLeftElement = $("#extendTurnLeft", ui);
            UI.shipMovement.extendTurnRightElement = $("#extendTurnRight", ui);
            UI.shipMovement.completeExtendTurnLeftElement = $("#completeExtendTurnLeft", ui);
            UI.shipMovement.completeExtendTurnRightElement = $("#completeExtendTurnRight", ui);

            UI.shipMovement.graviticTurnLeftElement = $("#graviticTurnLeft", ui);
            UI.shipMovement.graviticTurnRightElement = $("#graviticTurnRight", ui);            

            UI.shipMovement.turnIntoPivotLeftElement = $("#turnIntoPivotLeft", ui);
            UI.shipMovement.turnIntoPivotRightElement = $("#turnIntoPivotRight", ui);

            UI.shipMovement.slipleftElement = $("#slipleft", ui);
            UI.shipMovement.sliprightElement = $("#slipright", ui);

            UI.shipMovement.pivotleftElement = $("#pivotleft", ui);
            UI.shipMovement.pivotLeftActiveElement = $("#pivotLeftActive", ui);
            UI.shipMovement.pivotrightElement = $("#pivotright", ui);
            UI.shipMovement.pivotRightActiveElement = $("#pivotRightActive", ui);

            UI.shipMovement.rotateleftElement = $("#rotateleft", ui);
            UI.shipMovement.rotaterightElement = $("#rotateright", ui);

            UI.shipMovement.rollElement = $("#roll", ui);
            UI.shipMovement.emergencyrollElement = $("#emergencyroll", ui);
            UI.shipMovement.rollActiveElement = $("#rollActive", ui);

            UI.shipMovement.jinkElement = $("#jink", ui);
            UI.shipMovement.jinkvalueElement = UI.shipMovement.jinkElement.find(".jinkvalue");
            UI.shipMovement.evadeElement = $("#evade", ui);

            UI.shipMovement.accElement = $("#accelerate", ui);
            UI.shipMovement.deaccElement = $("#deaccelerate", ui);

            UI.shipMovement.morejinkElement = $("#morejink", ui);
            UI.shipMovement.lessjinkElement = $("#lessjink", ui);
            UI.shipMovement.cancelElement = $("#cancel", ui);
            UI.shipMovement.detachElement = $("#detach", ui);
            UI.shipMovement.minesweepElement = $("#minesweep", ui);

            UI.shipMovement.halfphaseElement = $("#halfphase", ui);
            UI.shipMovement.chargeElement = $("#charge", ui);
            UI.shipMovement.warLeaderElement = $("#warLeader", ui);
            UI.shipMovement.gravRotateElement = $("#gravRotate", ui);
            UI.shipMovement.redlineElement = $("#redline", ui);

            UI.shipMovement.contractionElement = $("#contraction", ui);
            UI.shipMovement.morecontractionElement = $("#morecontraction", ui);
            UI.shipMovement.lesscontractionElement = $("#lesscontraction", ui);
            UI.shipMovement.contractionvalueElement = UI.shipMovement.contractionElement.find(".contractionvalue");

            UI.shipMovement.cancelElement.on("click touchstart contextmenu", UI.shipMovement.cancelCallback);
            UI.shipMovement.detachElement.on("click touchstart contextmenu", UI.shipMovement.detachCallback);
            UI.shipMovement.minesweepElement.on("click touchstart", UI.shipMovement.minesweepCallback);
            UI.shipMovement.moveElement.on("click touchstart contextmenu", UI.shipMovement.moveCallback);

            UI.shipMovement.turnrightElement.on("click touchstart", UI.shipMovement.turnrightCallback);
            UI.shipMovement.turnleftElement.on("click touchstart", UI.shipMovement.turnleftCallback);

            UI.shipMovement.extendTurnRightElement.on("click touchstart", UI.shipMovement.extendTurnRightCallback);
            UI.shipMovement.extendTurnLeftElement.on("click touchstart", UI.shipMovement.extendTurnLeftCallback);
            UI.shipMovement.completeExtendTurnRightElement.on("click touchstart", UI.shipMovement.completeExtendTurnCallback);
            UI.shipMovement.completeExtendTurnLeftElement.on("click touchstart", UI.shipMovement.completeExtendTurnCallback);
            UI.shipMovement.sliprightElement.on("click touchstart", UI.shipMovement.sliprightCallback);
            UI.shipMovement.slipleftElement.on("click touchstart", UI.shipMovement.slipleftCallback);

            UI.shipMovement.pivotleftElement.on("click touchstart", UI.shipMovement.pivotleftCallback);
            UI.shipMovement.pivotLeftActiveElement.on("click touchstart", UI.shipMovement.pivotleftCallback);
            UI.shipMovement.pivotrightElement.on("click touchstart", UI.shipMovement.pivotrightCallback);
            UI.shipMovement.pivotRightActiveElement.on("click touchstart", UI.shipMovement.pivotrightCallback);

            UI.shipMovement.rotateleftElement.on("click touchstart", UI.shipMovement.rotateleftCallback);
            UI.shipMovement.rotaterightElement.on("click touchstart", UI.shipMovement.rotaterightCallback);

            UI.shipMovement.rollElement.on("click touchstart", UI.shipMovement.rollCallback);
            UI.shipMovement.rollActiveElement.on("click touchstart", UI.shipMovement.rollCallback);
            UI.shipMovement.emergencyrollElement.on("click touchstart", UI.shipMovement.emergencyrollCallback);

            UI.shipMovement.accElement.on("click touchstart contextmenu", UI.shipMovement.accelCallback);
            UI.shipMovement.deaccElement.on("click touchstart contextmenu", UI.shipMovement.deaccCallback);

            UI.shipMovement.morejinkElement.on("click touchstart contextmenu", UI.shipMovement.morejinkCallback);
            UI.shipMovement.lessjinkElement.on("click touchstart contextmenu", UI.shipMovement.lessjinkCallback);
            UI.shipMovement.evadeElement.on("click touchstart", UI.shipMovement.evadeCallback);

            UI.shipMovement.halfphaseElement.on("click touchstart", UI.shipMovement.halfphaseCallback);
            UI.shipMovement.chargeElement.on("click touchstart", UI.shipMovement.chargeCallback);
            UI.shipMovement.warLeaderElement.on("click touchstart", UI.shipMovement.warLeaderCallback);
            UI.shipMovement.gravRotateElement.on("click touchstart", UI.shipMovement.gravRotateCallback);
            UI.shipMovement.redlineElement.on("click touchstart", UI.shipMovement.redlineCallback);

            UI.shipMovement.morecontractionElement.on("click touchstart", UI.shipMovement.morecontractionCallback);
            UI.shipMovement.lesscontractionElement.on("click touchstart", UI.shipMovement.lesscontractionCallback);

            UI.shipMovement.turnIntoPivotLeftElement.on("click touchstart", UI.shipMovement.turnIntoPivotLeftCallback);
            UI.shipMovement.turnIntoPivotRightElement.on("click touchstart", UI.shipMovement.turnIntoPivotRightCallback);

            UI.shipMovement.graviticTurnLeftElement.on("click touchstart", UI.shipMovement.graviticTurnLeftCallback);
            UI.shipMovement.graviticTurnRightElement.on("click touchstart", UI.shipMovement.graviticTurnRightCallback);            

            jQuery('#shipMovementUI div').on('mousedown touchend touchmove', cancelEvent);
            jQuery('#shipMovementUI div').on('mouseup touchend touchmove', cancelEvent);
            UI.shipMovement.uiElement[0].addEventListener('contextmenu', function (e) {
                e.preventDefault();
            }, true);

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

        detachCallback: function detachCallback(e) {
            UI.shipMovement.callbackHandler.detachCallback(e);
        },

        minesweepCallback: function minesweepCallback(e) {
            //A tap can arrive as touchstart AND click: one toggle, not two that cancel out.
            if (UI.shipMovement.checkUITimeout()) return false;
            UI.shipMovement.callbackHandler.minesweepCallback(e);
        },

        morejinkCallback: function morejinkCallback(e) {
            UI.shipMovement.callbackHandler.morejinkCallback(e);
        },

        lessjinkCallback: function lessjinkCallback(e) {
            UI.shipMovement.callbackHandler.lessjinkCallback(e);
        },

        evadeCallback: function evadeCallback(e) {
            e.stopPropagation();
            UI.shipMovement.callbackHandler.evadeCallback(e);
        },

        halfphaseCallback: function halfphaseCallback(e) {
            UI.shipMovement.callbackHandler.halfphaseCallback(e);
        },

        chargeCallback: function chargeCallback(e) {
            //A tap can arrive as touchstart AND click: one toggle, not two that cancel out.
            if (UI.shipMovement.checkUITimeout()) return false;
            UI.shipMovement.callbackHandler.chargeCallback(e);
        },

        warLeaderCallback: function warLeaderCallback(e) {
            //A tap can arrive as touchstart AND click: one toggle, not two that cancel out.
            if (UI.shipMovement.checkUITimeout()) return false;
            UI.shipMovement.callbackHandler.warLeaderCallback(e);
        },

        gravRotateCallback: function gravRotateCallback(e) {
            //A tap can arrive as touchstart AND click: one roll asked for, not two (the server would answer the second with the first anyway).
            if (UI.shipMovement.checkUITimeout()) return false;
            UI.shipMovement.callbackHandler.gravRotateCallback(e);
        },

        redlineCallback: function redlineCallback(e) {
            //A tap can arrive as touchstart AND click: one toggle, not two that cancel out.
            if (UI.shipMovement.checkUITimeout()) return false;
            UI.shipMovement.callbackHandler.redlineCallback(e);
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

        extendTurnRightCallback: function extendTurnRightCallback(e) {
            e.stopPropagation();
            UI.shipMovement.callbackHandler.extendTurnCallback(e, true);
        },

        extendTurnLeftCallback: function extendTurnLeftCallback(e) {
            e.stopPropagation();
            UI.shipMovement.callbackHandler.extendTurnCallback(e, false);
        },

        //One handler for both sides: the side was named when the turn was begun.
        completeExtendTurnCallback: function completeExtendTurnCallback(e) {
            e.stopPropagation();
            UI.shipMovement.callbackHandler.completeExtendTurnCallback(e);
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

        graviticTurnRightCallback: function graviticTurnRightCallback(e) {
            e.stopPropagation();
            UI.shipMovement.graviticTurnCallback(e, true);
        },

        graviticTurnLeftCallback: function graviticTurnLeftCallback(e) {
            e.stopPropagation();
            UI.shipMovement.graviticTurnCallback(e, false);
        },

         graviticTurnCallback: function graviticTurnCallback(e, right) {
            UI.shipMovement.callbackHandler. graviticTurnCallback(e, right);
        },        

        drawShipMovementUI: function drawShipMovementUI(ship, callbackHandler) {

            UI.shipMovement.callbackHandler = callbackHandler;

            if ($(".confirm").length > 0) {
                if (gamedata.gamephase == 2) return false;
                if (gamedata.gamephase == 3 && ship.flight) return false;
            }

            UI.shipMovement.initMoveUI();
            var shipHeading = 0; //shipManager.getShipDoMAngle(ship);
            var angle = shipHeading;
            var dis = 80; //hexgrid.hexHeight();

            var ui = UI.shipMovement.uiElement;
            var shipX = ship.movement[ship.movement.length - 1].x;
            var shipY = ship.movement[ship.movement.length - 1].y;

            var pos = { x: 400, y: 400 };

            var s = 40;

            // The speed stack (- / + / move) is laid out along the facing axis and
            // is unusable with a fingertip at mouse sizes: the +/- buttons are 16px
            // boxes 5px apart, and the 52px move arrow starts 1px past the +. A
            // touch covering all three resolves to whichever box its centre lands
            // in, which is nearly always the move arrow.
            //
            // On a coarse pointer the +/- glyphs are drawn only slightly larger (20px,
            // not the 30px that read as oversized) and get a transparent 32px hit box
            // centred on them. That box is what needs the room, so + and move are
            // pushed out until the boxes clear each other:
            //   -  centre 56px, hits [40..72]   (unchanged from the mouse layout)
            //   +  centre 92px, hits [76..108]
            //   move centre 143px, hits [117..169]
            // The minus deliberately keeps its mouse distance: any closer in and it
            // crowds the ship icon. The 4.4px dead zone between the +/- boxes is the
            // binding constraint on pulling them any closer together - below that a
            // near-miss on the minus starts registering as an accelerate. Mouse layout
            // is left exactly as it was.
            var touch = window.matchMedia("(pointer: coarse)").matches;
            var speedIconSize = touch ? 20 : 16;
            var speedHitSize = touch ? 32 : 16;

            var move = UI.shipMovement.moveElement;
            if (touch) dis = 102;

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

            dis = touch ? 66 : 55;
            var acc = UI.shipMovement.accElement;
            if (shipManager.movement.canChangeSpeed(ship)) {
                UI.shipMovement.drawUIElement(acc, pos.x, pos.y, speedIconSize, dis * 1.4, angle, "img/plus.png", "acceleratecanvas", 0, speedHitSize);
            } else {
                acc.hide();
            }

            //CHANGE SPEED
            dis = 40;
            var deacc = UI.shipMovement.deaccElement;
            if (shipManager.movement.canChangeSpeed(ship)) {
                UI.shipMovement.drawUIElement(deacc, pos.x, pos.y, speedIconSize, dis * 1.4, angle, "img/minus.png", "deacceleratecanvas", 0, speedHitSize);
            } else {
                deacc.hide();
            }

            angle = mathlib.addToDirection(shipHeading, -60);
            dis = 60;

            /* EXTENDED TURNS (EXTENDED_TURNS_PLAN.md §5.2). Both icons stand in a turn arrow's spot:
               - begin, where the whole turn is unaffordable (D1-A: canBeginExtendedTurn asks !canTurn,
                 so the arrow and the begin icon never both apply);
               - complete, on the side named last turn. While a turn is owed every other gate is shut
                 (extendedTurnForbids), so this is the only icon on screen until it is completed or
                 cancelled. Asked once here: its payability test is a dry run of assignThrust. */
            var completeExtendSide = null;
            if (shipManager.movement.canCompleteExtendedTurn(ship)) {
                completeExtendSide = shipManager.movement.getOwedExtendedTurn(ship).type === 'extendTurnRight';
            }

            //TURN LEFT - in the Charge red while it is an Expert Warrior's charge snap turn (turnArrowTint)
            var chargeSnap = shipManager.movement.isChargeSnap(ship);
            var turnleft = UI.shipMovement.turnleftElement;
            if (shipManager.movement.canTurn(ship, false)) {
                UI.shipMovement.drawUIElement(turnleft, pos.x, pos.y, s, dis * 1.4, angle, "img/turnleft.png", "turnleftcanvas", shipHeading, undefined,
                    UI.shipMovement.turnArrowTint(turnleft, chargeSnap, "Turn Left"));
            } else {
                turnleft.hide();
            }
            UI.shipMovement.drawExtendedTurnIcons(ship, false, completeExtendSide, pos, s, dis * 1.4, angle, shipHeading);

            dis = 85;

            //SLIP LEFT
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

            //GRAVITIC TURN LEFT
            var graviticTurnLeft = UI.shipMovement.graviticTurnLeftElement;
            if (shipManager.movement.canGraviticTurn(ship, false)) {
                UI.shipMovement.drawUIElement(graviticTurnLeft, pos.x, pos.y, s, dis * 1.4, angle, "img/graviticTurnLeft1.png", "graviticTurnLeftCanvas", shipHeading);
            } else {
                graviticTurnLeft.hide();
            }     

            dis = 60;
            angle = mathlib.addToDirection(shipHeading, 60);

            //TURN RIGHT
            var turnright = UI.shipMovement.turnrightElement;
            if (shipManager.movement.canTurn(ship, true)) {
                UI.shipMovement.drawUIElement(turnright, pos.x, pos.y, s, dis * 1.4, angle, "img/turnright.png", "turnrightcanvas", shipHeading, undefined,
                    UI.shipMovement.turnArrowTint(turnright, chargeSnap, "Turn Right"));
            } else {
                turnright.hide();
            }
            UI.shipMovement.drawExtendedTurnIcons(ship, true, completeExtendSide, pos, s, dis * 1.4, angle, shipHeading);



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

            //GRAVTIC TURN RIGHT
            var graviticTurnRight = UI.shipMovement.graviticTurnRightElement;
            if (shipManager.movement.canGraviticTurn(ship, false)) {
                UI.shipMovement.drawUIElement(graviticTurnRight, pos.x, pos.y, s, dis * 1.4, angle, "img/graviticTurnRight1.png", "graviticTurnRightCanvas", shipHeading);
            } else {
                graviticTurnRight.hide();
            }   

            dis = 60;

            //A Redline Pilot's combat pivot without the thrust (OFFICERS_PLAN.md Stage 32): both pivot arrows in the Redline red (pivotArrowTint)
            var redlinePivot = shipManager.movement.isRedlinePivotNext(ship);

            //PIVOT LEFT
            angle = mathlib.addToDirection(shipHeading, -90);
            var pivotleft = UI.shipMovement.pivotleftElement;
            var pivotLeftActive = UI.shipMovement.pivotLeftActiveElement;

            if (shipManager.movement.canPivot(ship, false)) {
                if (shipManager.movement.isEndingPivot(ship, false)) {
                    // Active pivot state (Stop Pivoting)
                    pivotleft.hide();

                    // Draw and show the active icon at the same position
                    UI.shipMovement.drawUIElement(
                        pivotLeftActive,
                        pos.x,
                        pos.y,
                        s,
                        dis * 1.4,
                        angle,
                        "img/pivotleft_active.png",
                        "pivotLeftActiveCanvas",
                        shipHeading
                    );
                    pivotLeftActive.show();
                } else {
                    // Normal pivot state
                    pivotLeftActive.hide();

                    UI.shipMovement.drawUIElement(
                        pivotleft,
                        pos.x,
                        pos.y,
                        s,
                        dis * 1.4,
                        angle,
                        "img/pivotleft.png",
                        "pivotleftcanvas",
                        shipHeading,
                        undefined,
                        UI.shipMovement.pivotArrowTint(pivotleft, redlinePivot, "Pivot Left")
                    );
                    pivotleft.show();
                }
            } else {
                pivotleft.hide();
                pivotLeftActive.hide();
            }

            //PIVOT RIGHT
            angle = mathlib.addToDirection(shipHeading, 90);
            var pivotright = UI.shipMovement.pivotrightElement;
            var pivotRightActive = UI.shipMovement.pivotRightActiveElement;

            if (shipManager.movement.canPivot(ship, true)) {
                if (shipManager.movement.isEndingPivot(ship, true)) {
                    // Active pivot state (Stop Pivoting)
                    pivotright.hide();

                    // Draw and show the active icon at the same position
                    UI.shipMovement.drawUIElement(
                        pivotRightActive,
                        pos.x,
                        pos.y,
                        s,
                        dis * 1.4,
                        angle,
                        "img/pivotright_active.png",
                        "pivotRightActiveCanvas",
                        shipHeading
                    );
                    pivotRightActive.show();
                } else {
                    // Normal pivot state
                    pivotRightActive.hide();

                    UI.shipMovement.drawUIElement(
                        pivotright,
                        pos.x,
                        pos.y,
                        s,
                        dis * 1.4,
                        angle,
                        "img/pivotright.png",
                        "pivotrightcanvas",
                        shipHeading,
                        undefined,
                        UI.shipMovement.pivotArrowTint(pivotright, redlinePivot, "Pivot Right")
                    );
                    pivotright.show();
                }
            } else {
                pivotright.hide();
                pivotRightActive.hide();
            }

            //BASE ROTATION
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

            //ROLLING 
            var roll = UI.shipMovement.rollElement;
            var emergencyroll = UI.shipMovement.emergencyrollElement;
            var icon = "";
            var checkHeading = shipManager.getShipDoMAngle(ship);
            dis += 30; // Increment distance only once.

            /* MINESWEEPING MODE (MINE_DETECTION_PLAN.md Q7) - first astern, in the slot the roll icon
               (ships) or the jink stack (flights) would start in; everything astern of it steps back.
               40 is its hit box: the roll icon's centre moves out by that, and a flight's jink + by
               just enough to clear it, since that stack already starts further out - each plus
               minesweepGap. (dis is in units of 1/1.4 px.) */
            /* AFTERBURNERS (OFFICERS_PLAN.md Stage 32) - a Redline Pilot's flight: astern, between the flight and its jink stack, redlineFlightDistance
               px out (user, play test 4515); a Minesweeping Mode icon steps back behind it, and the stack behind that one. */
            var redlineAt = UI.shipMovement.redlineFlightDistance;
            if (UI.shipMovement.drawRedlineIcon(ship, pos, redlineAt, angle) && window.mineSweep && mineSweep.getModeState(ship)) {
                dis = Math.max(dis, (redlineAt + 40 + UI.shipMovement.minesweepGap) / 1.4); //both hit boxes are 40
            }

            if (UI.shipMovement.drawMinesweepIcon(ship, pos, dis * 1.4, angle)) {
                var gap = UI.shipMovement.minesweepGap / 1.4;
                dis += ship.flight ? 10 + gap : 40 / 1.4 + gap;
            }

            if (shipManager.movement.canRoll(ship)) {
                icon = "img/rotate.png";
                emergencyroll.hide();
                UI.shipMovement.rollActiveElement.hide(); // Hide the regular roll icon

                if (shipManager.movement.isRollingForIcon(ship)) {
                    icon = "img/rotate_active.png";
                    UI.shipMovement.rollActiveElement.show(); // Show the active roll icon
                    roll.hide();
                    emergencyroll.hide();
                }
                UI.shipMovement.drawUIElement(roll, pos.x, pos.y, s, dis * 1.4, angle, icon, "rollcanvas", shipHeading);
            } else if (shipManager.movement.canEmergencyRoll(ship)) {
                icon = "img/emergencyRoll.png";
                if (checkHeading >= 90 && checkHeading <= 270) {
                    // Flip the emergency roll icon when facing left
                    icon = "img/emergencyRollFlipped.png";
                }
                UI.shipMovement.drawUIElement(emergencyroll, pos.x, pos.y, s, dis * 1.4, angle, icon, "emergencyrollcanvas", shipHeading);
                roll.hide();
                UI.shipMovement.rollActiveElement.hide(); // Hide the regular roll icon
            } else {
                // Hide all icons if no roll or emergency roll is possible
                roll.hide();
                emergencyroll.hide();
                UI.shipMovement.rollActiveElement.hide();
            }

            var detach = UI.shipMovement.detachElement;
            if (shipManager.movement.canDetach(ship)) {
                //var detachOrientation = shipManager.getShipHeadingAngle(ship) - shipManager.getShipDoMAngle(ship);                
                var relativeFacing = shipManager.getShipHeadingAngle(ship) - shipManager.getShipDoMAngle(ship);
                var detachAngle = mathlib.addToDirection(relativeFacing, 180);
                UI.shipMovement.drawUIElement(detach, pos.x, pos.y, s, dis * 1.4, detachAngle, "img/detach.png", "detachcanvas", relativeFacing);
            } else {
                detach.hide();
            }

            //JINKING
            var morejink = UI.shipMovement.morejinkElement;
            if (shipManager.movement.canJink(ship, 1)) {
                if (!ship.flight) {
                    dis += 20;
                } else {
                    dis += 10;
                }
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

            //EVADE - an Expert Evader's Firing-phase order: the jink art, no +/- or value; gone once given (Cancel takes it back)
            var evade = UI.shipMovement.evadeElement;
            if (shipManager.movement.canEvade(ship)) {
                dis += 30;
                UI.shipMovement.drawUIElement(evade, pos.x, pos.y, s, dis * 1.4, angle, "img/jink.png", "evadecanvas", shipHeading);
            } else {
                evade.hide();
            }

            //HALF PHASING
            var halfphase = UI.shipMovement.halfphaseElement;
            if (shipManager.movement.canHalfPhase(ship)) {
                UI.shipMovement.drawUIElement(halfphase, pos.x, pos.y, 50, 35 * 1.4, angle, "img/HalfPhase.png", "halfphasecanvas", 0);
            } else {
                halfphase.hide();
            }

            //CANCEL MOVEMENT - hidden once only steps swept for mines remain (MINE_DETECTION_PLAN.md §1.4.2)
            var cancel = UI.shipMovement.cancelElement;
            if (shipManager.movement.canCancelMove(ship) && weaponManager.canCombatTurn(ship)) {
                dis += 26;
                UI.shipMovement.drawUIElement(cancel, pos.x, pos.y, 30, dis * 1.4, angle, "img/cancel.png", "cancelcanvas", 0);
            } else {
                cancel.hide();
            }

            //CHARGE - an Expert Warrior's (OFFICERS_PLAN.md Stage 28), in the Contraction / Half-Phase spot, which no Drazi ship uses
            UI.shipMovement.drawChargeIcon(ship, pos, angle);
            //ROTATE - an Expert Graviton Controller's (OFFICERS_PLAN.md Stage 31), in the same spot: only Drazi carry a Warrior, and no Drazi hull a gravitic drive
            UI.shipMovement.drawGravRotateIcon(ship, pos, angle);
            //WAR LEADER - an Expert War Leader's Wrath / Center of Glory (OFFICERS_PLAN.md Stage 34), astern, warLeaderDistance px out: Firing phase only, where the other two never show
            UI.shipMovement.drawWarLeaderIcon(ship, pos, angle);

            //CONTRACTION
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

            return true;
        },

        /* The begin and completion icons for one side, in that side's turn-arrow spot. completeSide:
           true / false - the side an owed turn is completed on - or null when there is none to complete. */
        drawExtendedTurnIcons: function drawExtendedTurnIcons(ship, right, completeSide, pos, s, dis, angle, shipHeading) {
            var begin = right ? UI.shipMovement.extendTurnRightElement : UI.shipMovement.extendTurnLeftElement;
            var complete = right ? UI.shipMovement.completeExtendTurnRightElement : UI.shipMovement.completeExtendTurnLeftElement;
            var art = right ? "img/turnright.png" : "img/turnleft.png";

            var colour = UI.shipMovement.extendedTurnColour;
            //Element opacity is safe here: the icon holds no text and nothing translucent is layered on it.
            begin.css("opacity", UI.shipMovement.extendedTurnOpacity);
            complete.css("opacity", UI.shipMovement.extendedTurnOpacity);

            if (shipManager.movement.canBeginExtendedTurn(ship, right)) {
                UI.shipMovement.drawUIElement(begin, pos.x, pos.y, s, dis, angle, art, right ? "extendTurnRightCanvas" : "extendTurnLeftCanvas", shipHeading, undefined, colour);
            } else {
                begin.hide();
            }

            if (completeSide === right) {
                UI.shipMovement.drawUIElement(complete, pos.x, pos.y, s, dis, angle, art, right ? "completeExtendTurnRightCanvas" : "completeExtendTurnLeftCanvas", shipHeading, undefined, colour);
            } else {
                complete.hide();
            }
        },

        /* The purple Minesweeping Mode icon (MINE_DETECTION_PLAN.md Q7, §6.8), for a unit that can sweep
           for mines: faint while off, glowing while on, and clickable only until the unit's first step
           into a hex - then it glows on for good, or fades further and stays off (mineSweep.getModeState).
           The art has an up, so reposition counter-rotates its canvas as it does the speed figure.
           Returns whether it was drawn, so the icons astern of it can make room. */
        drawMinesweepIcon: function drawMinesweepIcon(ship, pos, dis, angle) {
            var icon = UI.shipMovement.minesweepElement;
            var state = window.mineSweep ? mineSweep.getModeState(ship) : null;
            if (!state) {
                icon.hide();
                return false;
            }

            var labels = {
                off: "Start Minesweeping",
                on: "Stop Minesweeping",
                locked: "Minesweeping - Each move is locked",
                unavailable: "Minesweeping - Activate before first move"
            };
            var lit = state === 'on' || state === 'locked';
            var opacities = { off: UI.shipMovement.minesweepOffOpacity, unavailable: UI.shipMovement.minesweepUnavailableOpacity };
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            icon.attr("data-movement-type", labels[state]).data("movement-type", labels[state]);
            //A label already showing (the pointer is still on the icon after a click) says the new state too.
            if (icon.is(":hover")) $("#movetooltip .movementType").text(labels[state]);
            icon.css({
                opacity: lit ? 1 : opacities[state],
                cursor: state === 'off' || state === 'on' ? "pointer" : "default"
            });
            var glow = UI.shipMovement.minesweepGlowColour;
            $("#minesweepcanvas").css("filter", lit ? "drop-shadow(0 0 3px " + glow + ") drop-shadow(0 0 2px " + glow + ")" : "none");

            UI.shipMovement.drawUIElement(icon, pos.x, pos.y, 34, dis, angle, UI.shipMovement.minesweepIcon, "minesweepcanvas", 0, 40);
            return true;
        },

        /* The Charge icon (OFFICERS_PLAN.md Stage 28) for an Expert Warrior's ship, in shipManager.movement.getChargeState's state: green and
           faint while a charge may be declared, pulsing while it waits for an enemy to be clicked, red, crosshaired and glowing once declared
           (its label names the unit), a little fainter once the run has begun. The art points the way the ship moves, so it turns with the
           movement UI. */
        drawChargeIcon: function drawChargeIcon(ship, pos, angle) {
            var icon = UI.shipMovement.chargeElement;
            var state = shipManager.movement.getChargeState(ship);
            if (!state) {
                icon.hide();
                return false;
            }
            var row = shipManager.movement.getChargeRow(ship);
            var target = row ? gamedata.getShip(row.value) : null;
            var name = target ? target.name : "the unit charged";
            var labels = {
                off: "Charge - click here, then the enemy unit to charge",
                picking: "Charge - click the enemy unit to charge (click here to stop)",
                on: "Charging " + name + " (click here to cancel)",
                locked: "Charging " + name
            };
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            icon.attr("data-movement-type", labels[state]).data("movement-type", labels[state]);
            if (icon.is(":hover")) $("#movetooltip .movementType").text(labels[state]);
            icon.css({
                opacity: state === 'off' ? UI.shipMovement.chargeOffOpacity : (state === 'locked' ? UI.shipMovement.chargeLockedOpacity : 1),
                cursor: state === 'locked' ? "default" : "pointer"
            });
            var held = state === 'on' || state === 'locked';
            var glow = UI.shipMovement.chargeGlowColour;
            var canvas = $("#chargecanvas");
            canvas.css("--charge-glow", glow).toggleClass("charge-picking", state === 'picking');
            canvas.css("filter", held ? "drop-shadow(0 0 3px " + glow + ") drop-shadow(0 0 2px " + glow + ")" : "none");
            UI.shipMovement.drawUIElement(icon, pos.x, pos.y, 34, 30 * 1.4, angle, UI.shipMovement.chargeIcon, "chargecanvas", 0, 40,
                held ? UI.shipMovement.chargeColour : null, held ? UI.shipMovement.drawChargeReticle : null);
            return true;
        },

        /* The War Leader icon (OFFICERS_PLAN.md Stage 34) for an Expert War Leader's ship in the Firing phase, in officers.warLeaderState's state:
           yellow and faint while Wrath or Center of Glory may be given - a click, then an enemy unit (his wrath) or a unit of his fleet (the
           center of glory) -, pulsing while it waits for that click, red (wrath) or green (glory) and crosshaired once a unit is picked (a
           click takes it back; its label names the unit), and the same, no longer clickable, once committed this turn. */
        drawWarLeaderIcon: function drawWarLeaderIcon(ship, pos, angle) {
            var icon = UI.shipMovement.warLeaderElement;
            var state = window.officers ? officers.warLeaderState(ship) : null;
            if (!state) {
                icon.hide();
                return false;
            }
            var named = officers.warLeaderNamed(ship);
            var nameOf = function (id) {
                var unit = (id !== undefined) ? gamedata.getShip(id) : null;
                return unit ? unit.name : "the unit";
            };
            var labels = {
                off: "War Leader - click here, then an enemy unit (Wrath) or a unit of your fleet (Center of Glory)",
                picking: "War Leader - click an enemy unit (Wrath) or a unit of your fleet (Center of Glory); click here to stop",
                wrath: "Wrath on " + nameOf(named.wrath) + " (click here to cancel)",
                glory: nameOf(named.glory) + " is the Center of Glory (click here to cancel)",
                wrathDone: "Wrath on " + nameOf(named.wrath),
                gloryDone: nameOf(named.glory) + " is the Center of Glory"
            };
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            icon.attr("data-movement-type", labels[state]).data("movement-type", labels[state]);
            if (icon.is(":hover")) $("#movetooltip .movementType").text(labels[state]);
            var wrath = state === 'wrath' || state === 'wrathDone';
            var glory = state === 'glory' || state === 'gloryDone';
            var held = wrath || glory;
            var done = state === 'wrathDone' || state === 'gloryDone';
            icon.css({
                opacity: state === 'off' ? UI.shipMovement.warLeaderOffOpacity : 1,
                cursor: done ? "default" : "pointer"
            });
            var colour = wrath ? UI.shipMovement.warLeaderWrathColour : (glory ? UI.shipMovement.warLeaderGloryColour : UI.shipMovement.warLeaderColour);
            var glow = held ? colour : UI.shipMovement.warLeaderGlowColour;
            var canvas = $("#warLeadercanvas");
            canvas.css("--charge-glow", glow).toggleClass("charge-picking", state === 'picking');
            canvas.css("filter", held ? "drop-shadow(0 0 3px " + glow + ") drop-shadow(0 0 2px " + glow + ")" : "none");
            UI.shipMovement.drawUIElement(icon, pos.x, pos.y, 34, UI.shipMovement.warLeaderDistance, angle, UI.shipMovement.warLeaderIcon, "warLeadercanvas", 0, 40,
                colour, held ? UI.shipMovement.drawChargeReticle : null);
            return true;
        },

        /* The Gravitic Rotate icon (OFFICERS_PLAN.md Stage 31) for an Expert Graviton Controller's ship, in shipManager.movement.getGravRotateState's state:
           faint while the drive may be rotated - a click asks the server to roll the die -, glowing once it has been this turn (its label says
           the die; the rotation and every move before it are locked). */
        drawGravRotateIcon: function drawGravRotateIcon(ship, pos, angle) {
            var icon = UI.shipMovement.gravRotateElement;
            var state = shipManager.movement.getGravRotateState(ship);
            if (!state) {
                icon.hide();
                return false;
            }
            var row = shipManager.movement.getGravRotateRow(ship);
            var labels = {
                ready: "Rotate Gravitic Drive - once per battle: a d6 picks the new facing, and this turn's fire is at -15%",
                done: "Gravitic drive rotated (the die rolled " + (row ? row.value : "?") + ") - fire at -15% this turn"
            };
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            icon.attr("data-movement-type", labels[state]).data("movement-type", labels[state]);
            if (icon.is(":hover")) $("#movetooltip .movementType").text(labels[state]);
            icon.css({
                opacity: state === 'ready' ? UI.shipMovement.gravRotateReadyOpacity : 1,
                cursor: state === 'ready' ? "pointer" : "default"
            });
            var glow = UI.shipMovement.gravRotateGlowColour;
            $("#gravRotatecanvas").css("filter", state === 'done' ? "drop-shadow(0 0 3px " + glow + ") drop-shadow(0 0 2px " + glow + ")" : "none");
            UI.shipMovement.drawUIElement(icon, pos.x, pos.y, 34, 30 * 1.4, angle, UI.shipMovement.gravRotateIcon, "gravRotatecanvas", 0, 40);
            return true;
        },

        /* The Redline icon (OFFICERS_PLAN.md Stage 32) for a Redline Pilot's flight, `dis` px astern, in shipManager.movement.getRedlineState's state:
           faint while it may burn (its first order of the turn), glowing while it burns (a click takes it back while it is still the last order), and
           grey on the turn after, while the engines cool - its label then says why the flight cannot manoeuvre. Returns whether it was drawn. */
        drawRedlineIcon: function drawRedlineIcon(ship, pos, dis, angle) {
            var icon = UI.shipMovement.redlineElement;
            var state = shipManager.movement.getRedlineState(ship);
            if (!state) {
                icon.hide();
                return false;
            }
            var bonus = window.officers ? officers.redlineThrustBonus(ship) : 0;
            var labels = {
                ready: "Afterburners - +50% thrust, no manoeuvres next turn",
                on: "Afterburners: +" + bonus + " thrust this turn",
                locked: "Afterburners: +" + bonus + " thrust this turn",
                cooling: "Engines cooling after the afterburners"
            };
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            icon.attr("data-movement-type", labels[state]).data("movement-type", labels[state]);
            if (icon.is(":hover")) $("#movetooltip .movementType").text(labels[state]);
            var opacities = { ready: UI.shipMovement.redlineReadyOpacity, cooling: UI.shipMovement.redlineCoolingOpacity };
            icon.css({
                opacity: opacities[state] || 1,
                cursor: state === 'ready' || state === 'on' ? "pointer" : "default"
            });
            var glow = UI.shipMovement.redlineGlowColour;
            var burning = state === 'on' || state === 'locked';
            $("#redlinecanvas").css("filter", burning ? "drop-shadow(0 0 3px " + glow + ") drop-shadow(0 0 2px " + glow + ")"
                : (state === 'cooling' ? UI.shipMovement.redlineCoolingFilter : "none"));
            UI.shipMovement.drawUIElement(icon, pos.x, pos.y, 34, dis, angle, UI.shipMovement.redlineIcon, "redlinecanvas", 0, 40);
            return true;
        },

        /* A pivot arrow's label, and its tint for drawUIElement: in the Firing phase, while a Redline Pilot's flight lacks the thrust for a combat pivot
           (OFFICERS_PLAN.md Stage 32, shipManager.movement.isRedlinePivotNext) it reads "... (Redline: ...)" with what that costs and is drawn in
           the Redline red - the Charge's snap-turn arrows' way (turnArrowTint; user, play test 4515); otherwise as ever. */
        pivotArrowTint: function pivotArrowTint(element, redline, plain) {
            var label = redline ? plain + " (Redline: without the thrust - this turn's fire at -15%)" : plain;
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            element.attr("data-movement-type", label).data("movement-type", label);
            if (element.is(":hover")) $("#movetooltip .movementType").text(label);
            return redline ? UI.shipMovement.redlinePivotColour : undefined;
        },

        /* A turn arrow's label, and its tint for drawUIElement: while its turn would be an Expert Warrior's charge snap turn (OFFICERS_PLAN.md
           Stage 28, shipManager.movement.isChargeSnap) it reads "Snap Turn ... (Charge)" and is drawn in the Charge red; otherwise as ever. */
        turnArrowTint: function turnArrowTint(element, snap, plain) {
            var label = snap ? "Snap " + plain + " (Charge)" : plain;
            //Both: moveTooltip reads the label with .data(), which caches the attribute on its first read.
            element.attr("data-movement-type", label).data("movement-type", label);
            if (element.is(":hover")) $("#movetooltip .movementType").text(label);
            return snap ? UI.shipMovement.chargeColour : undefined;
        },

        //The crosshair over the Charge icon's art once it holds a unit: a ring, four ticks across it and a centre dot, dark-edged so it reads over the red.
        drawChargeReticle: function drawChargeReticle(canvas, box) {
            var c = box / 2;
            var r = box * 0.28;
            var tick = box * 0.11;
            canvas.save();
            canvas.lineCap = "round";
            [["rgba(0, 0, 0, 0.8)", 4], [UI.shipMovement.chargeReticleColour, 2]].forEach(function (pass) {
                canvas.strokeStyle = pass[0];
                canvas.lineWidth = pass[1];
                canvas.beginPath();
                canvas.arc(c, c, r, 0, Math.PI * 2);
                [[1, 0], [-1, 0], [0, 1], [0, -1]].forEach(function (d) {
                    canvas.moveTo(c + d[0] * (r - tick), c + d[1] * (r - tick));
                    canvas.lineTo(c + d[0] * (r + tick), c + d[1] * (r + tick));
                });
                canvas.stroke();
                canvas.fillStyle = pass[0];
                canvas.beginPath();
                canvas.arc(c, c, pass[1] === 4 ? 2.5 : 1.5, 0, Math.PI * 2);
                canvas.fill();
            });
            canvas.restore();
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

            //...and the Minesweeping Mode art, which has an up:
            jQuery("#minesweepcanvas").css("transform", "rotate(" + -heading + "deg)");

            UI.shipMovement.currentPosition = position;
            UI.shipMovement.currentHeading = heading;
        },

        // box is the size of the clickable div; s is the size the icon is drawn at.
        // They are the same unless a caller asks for hit slop, in which case the
        // icon is centred in the larger box rather than sitting in its corner.
        // tint (optional): a CSS colour the drawn art is painted in, keeping its shape and
        // its own transparency (the extended-turn arrows; see extendedTurnColour).
        // after (optional): function (canvas, box) drawing over the finished art (the Charge icon's crosshair).
        drawUIimage: function drawUIimage(canvas, path, s, angle, box, tint, after) {
            var img = new Image();
            img.src = path;

            $(img).on("load", function () {
                graphics.clearSmallCanvas(canvas);
                graphics.drawAndRotate(canvas, box, box, s * 2, s * 2, angle, img);
                if (tint) {
                    canvas.save();
                    canvas.globalCompositeOperation = "source-atop";
                    canvas.fillStyle = tint;
                    canvas.fillRect(0, 0, canvas.canvas.width, canvas.canvas.height);
                    canvas.restore();
                }
                if (after) after(canvas, box);
            });
        },

        drawUIElement: function drawUIElement(e, x, y, s, dis, angle, path, canvasid, shipHeading, hit, tint, after) {
            var box = hit || s;
            var UIpos = mathlib.getPointInDirection(dis, -angle, x, y);
            e.css("top", UIpos.y - y - box * 0.5 + "px").css("left", UIpos.x - x - box * 0.5 + "px");
            e.css("width", box + "px").css("height", box + "px");
            e.show();
            //$("#"+canvaid).css("top", "px").css("left", "px");

            var canvas = window.graphics.getCanvas(canvasid);
            UI.shipMovement.drawUIimage(canvas, path, s, shipHeading, box, tint, after);
        },

        hide: function hide() {
            $("#shipMovementUI").hide();
        },

        show: function show() {
            $("#shipMovementUI").show();
        }

    }

};