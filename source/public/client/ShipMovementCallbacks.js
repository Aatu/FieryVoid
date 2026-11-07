"use strict";

window.ShipMovementCallbacks = function () {

    function ShipMovementCallbacks(ship, updateCallback) {
        this.ship = ship;
        this.updateCallback = updateCallback;
    }

    ShipMovementCallbacks.prototype.cancelCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.deleteMove(this.ship);
		/* r-click: undo all. Stops at the first step the mine sweep has made final (canCancelMove), and
		   whenever deleteMove removes nothing - a loop on "anything deletable?" alone spins forever when
		   that row is not the one deleteMove would take (MINE_DETECTION_PLAN.md T13). */
		if (event.which == 3) while (shipManager.movement.canCancelMove(this.ship)){
			var rowsBefore = this.ship.movement.length;
			shipManager.movement.deleteMove(this.ship);
			if (this.ship.movement.length === rowsBefore) break;
		}
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.detachCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doDetach(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    //Minesweeping Mode on / off (MINE_DETECTION_PLAN.md Q7). The update redraws the icon in its new state.
    ShipMovementCallbacks.prototype.minesweepCallback = function (e) {
        e.stopPropagation();
        if (window.mineSweep) mineSweep.toggleMode(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.morejinkCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doJink(this.ship, 1);
        if (event.which == 3) while (shipManager.movement.canJink(this.ship, 1)) { //r-click: jink to max available (thrust / jinking limit)
            shipManager.movement.doJink(this.ship, 1);
        }
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.lessjinkCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doJink(this.ship, -1);
        if (event.which == 3) while (shipManager.movement.canJink(this.ship, -1)) { //r-click: set jinking to 0, refunding all thrust
            shipManager.movement.doJink(this.ship, -1);
        }
        this.updateCallback({ ship: this.ship });
    };

    //An Expert Evader's Evade (Firing phase). The update redraws the movement UI without its icon.
    ShipMovementCallbacks.prototype.evadeCallback = function (e) {
        if (UI.shipMovement.checkUITimeout()) return false;
        shipManager.movement.doEvade(this.ship);
        jQuery("#movetooltip").remove(); //the icon hides under the pointer, which may never see a mouseleave
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.halfphaseCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doHalfPhase(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    //An Expert War Leader's icon in the Firing phase (OFFICERS_PLAN.md Stage 34): wait for a unit, stop waiting, or take a pick back. The update redraws it.
    ShipMovementCallbacks.prototype.warLeaderCallback = function (e) {
        e.stopPropagation();
        if (window.officers) officers.toggleWarLeaderPick(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    //An Expert Warrior's Charge (OFFICERS_PLAN.md Stage 28): wait for a target, stop waiting, or take a declared charge back. The update redraws the icon.
    ShipMovementCallbacks.prototype.chargeCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.toggleCharge(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    /* An Expert Graviton Controller's rotation (OFFICERS_PLAN.md Stage 31): the server rolls the die (shipManager.movement.doGravRotate), and the
       rows it answers redraw the unit when they land. No confirm - a click rolls (the user's note). */
    ShipMovementCallbacks.prototype.gravRotateCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doGravRotate(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    //A Redline Pilot's afterburners (OFFICERS_PLAN.md Stage 32): burn, or take the burn back while it is the last order. The update redraws the icon.
    ShipMovementCallbacks.prototype.redlineCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.toggleRedline(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.morecontractionCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doContraction(this.ship, 1);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.lesscontractionCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doContraction(this.ship, -1);
        this.updateCallback({ ship: this.ship });
    };
	
    ShipMovementCallbacks.prototype.accelCallback = function (e) {
        e.stopPropagation();
        if (event.which == 3 && gamedata.gamephase == -1 && this.ship.deploymove) { // r-click during deployment: set to max speed
            this.ship.deploymove.speed = 10;
        } else {
            shipManager.movement.changeSpeed(this.ship, true);
        }
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.deaccCallback = function (e) {
        e.stopPropagation();
        if (event.which == 3 && gamedata.gamephase == -1 && this.ship.deploymove) { // r-click during deployment: set to min speed
            this.ship.deploymove.speed = 0;
        } else {
            shipManager.movement.changeSpeed(this.ship, false);
        }
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.rollCallback = function (e) {
        e.stopPropagation();

        shipManager.movement.doRoll(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.emergencyrollCallback = function (e) {
        e.stopPropagation();

        shipManager.movement.doEmergencyRoll(this.ship);
        this.updateCallback({ ship: this.ship });
    };
    
    ShipMovementCallbacks.prototype.pivotrightCallback = function (e) {
        e.stopPropagation();
        this.pivotCallback(e, true);
    };

    ShipMovementCallbacks.prototype.pivotleftCallback = function (e) {
        e.stopPropagation();
        this.pivotCallback(e, false);
    };

    ShipMovementCallbacks.prototype.pivotCallback = function (e, right) {
        if (UI.shipMovement.checkUITimeout()) return false;

        shipManager.movement.doPivot(this.ship, right);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.rotateleftCallback = function (e) {
        e.stopPropagation();
        this.rotateCallback(e, true);
    };

    ShipMovementCallbacks.prototype.rotaterightCallback = function (e) {
        e.stopPropagation();
        this.rotateCallback(e, false);
    };

    ShipMovementCallbacks.prototype.rotateCallback = function (e, right) {
        e.stopPropagation();
        if (this.ship.base) {
            shipManager.movement.pickRotation(this.ship, right);
            this.updateCallback({ ship: this.ship });
        }
    };

    ShipMovementCallbacks.prototype.sliprightCallback = function (e) {
        e.stopPropagation();
        this.slipCallback(e, true);
    };

    ShipMovementCallbacks.prototype.slipleftCallback = function (e) {
        e.stopPropagation();
        this.slipCallback(e, false);
    };

    ShipMovementCallbacks.prototype.slipCallback = function (e, right) {
        shipManager.movement.doSlip(this.ship, right);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.turnrightCallback = function (e) {
        e.stopPropagation();
        this.turnCallback(e, true);
    };

    ShipMovementCallbacks.prototype.turnleftCallback = function (e) {
        e.stopPropagation();
        this.turnCallback(e, false);
    };

    ShipMovementCallbacks.prototype.turnCallback = function (e, right) {
        shipManager.movement.doTurn(this.ship, right);
        this.updateCallback({ ship: this.ship });
    };

    //EXTENDED_TURNS_PLAN.md §5.3 - both open the thrust panel on the new row.
    ShipMovementCallbacks.prototype.extendTurnCallback = function (e, right) {
        shipManager.movement.doBeginExtendedTurn(this.ship, right);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.completeExtendTurnCallback = function (e) {
        shipManager.movement.doCompleteExtendedTurn(this.ship);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.graviticTurnRightCallback = function (e) {
        e.stopPropagation();
        this.graviticTurnCallback(e, true);
    };

    ShipMovementCallbacks.prototype.graviticTurnLeftCallback = function (e) {
        e.stopPropagation();
        this.graviticTurnCallback(e, false);
    };    

    ShipMovementCallbacks.prototype.graviticTurnCallback = function (e, right) {
        shipManager.movement.doGraviticTurn(this.ship, right);
        this.updateCallback({ ship: this.ship });
    };    

    ShipMovementCallbacks.prototype.moveCallback = function (e) {
        e.stopPropagation();

        if (UI.shipMovement.checkUITimeout()) return false;
		if (event.which == 3){ //r-click - move for all remaining movement
			shipManager.movement.doMoveFully(this.ship);
		}else{
			shipManager.movement.doMove(this.ship);
		}
        this.updateCallback({ ship: this.ship });
    };
	
    ShipMovementCallbacks.prototype.turnIntoPivotCallback = function (e, right) {
        e.stopPropagation();

        shipManager.movement.doIntoPivotTurn(this.ship, right);
        this.updateCallback({ ship: this.ship });
    };

    return ShipMovementCallbacks;
}();