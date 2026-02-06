"use strict";

window.ShipMovementCallbacks = function () {

    function ShipMovementCallbacks(ship, updateCallback) {
        this.ship = ship;
        this.updateCallback = updateCallback;
    }

    ShipMovementCallbacks.prototype.cancelCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.deleteMove(this.ship);		
		if (event.which == 3) while (shipManager.movement.hasDeletableMovements(this.ship)){//r-click
			shipManager.movement.deleteMove(this.ship);	
		}
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.morejinkCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doJink(this.ship, 1);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.lessjinkCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doJink(this.ship, -1);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.halfphaseCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.doHalfPhase(this.ship);
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
        shipManager.movement.changeSpeed(this.ship, true);
        this.updateCallback({ ship: this.ship });
    };

    ShipMovementCallbacks.prototype.deaccCallback = function (e) {
        e.stopPropagation();
        shipManager.movement.changeSpeed(this.ship, false);
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