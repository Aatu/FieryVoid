window.ShipMovementCallbacks = (function(){

    function ShipMovementCallbacks(ship, updateCallback) {
        this.ship = ship;
        this.updateCallback = updateCallback;
    }

    ShipMovementCallbacks.prototype.cancelCallback = function(e){
        e.stopPropagation();
        shipManager.movement.deleteMove(this.ship);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.morejinkCallback = function(e){
        e.stopPropagation();
        shipManager.movement.doJink(this.ship , 1);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.lessjinkCallback = function(e){
        e.stopPropagation();
        shipManager.movement.doJink(this.ship , -1);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.accelCallback = function(e){
        e.stopPropagation();
        shipManager.movement.changeSpeed(this.ship , true);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.deaccCallback = function(e){
        e.stopPropagation();
        shipManager.movement.changeSpeed(this.ship , false);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.rollCallback = function(e){
        e.stopPropagation();

        shipManager.movement.doRoll(this.ship);
        this.updateCallback();
    },

    ShipMovementCallbacks.prototype.pivotrightCallback = function(e){
        e.stopPropagation();
        this.pivotCallback(e, true);
    };

    ShipMovementCallbacks.prototype.pivotleftCallback = function(e){
        e.stopPropagation();
        this.pivotCallback(e, false);
    };

    ShipMovementCallbacks.prototype.pivotCallback = function(e, right){
        if (UI.shipMovement.checkUITimeout())
            return false;

        shipManager.movement.doPivot(this.ship, right);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.rotateleftCallback = function(e){
        e.stopPropagation();
        this.rotateCallback(e, true);
    };

    ShipMovementCallbacks.prototype.rotaterightCallback = function(e){
        e.stopPropagation();
        this.rotateCallback(e, false);
    };

    ShipMovementCallbacks.prototype.rotateCallback = function(e, right){
        e.stopPropagation();
        if (this.ship.base){
            shipManager.movement.pickRotation(this.ship, right);
            this.updateCallback();
        }
    };

    ShipMovementCallbacks.prototype.sliprightCallback = function(e){
        e.stopPropagation();
        this.slipCallback(e, true);
    };

    ShipMovementCallbacks.prototype.slipleftCallback = function(e){
        e.stopPropagation();
        this.slipCallback(e, false);
    };

    ShipMovementCallbacks.prototype.slipCallback = function(e, right){
        shipManager.movement.doSlip(this.ship, right);
        this.updateCallback();

    };

    ShipMovementCallbacks.prototype.turnrightCallback = function(e){
        e.stopPropagation();
        this.turnCallback(e, true);
    };

    ShipMovementCallbacks.prototype.turnleftCallback = function(e){
        e.stopPropagation();
        this.turnCallback(e, false);
    };

    ShipMovementCallbacks.prototype.turnCallback = function(e, right){
        shipManager.movement.doTurn(this.ship, right);
        this.updateCallback();
    };

    ShipMovementCallbacks.prototype.moveCallback = function(e){
        e.stopPropagation();

        if (UI.shipMovement.checkUITimeout())
            return false;

        shipManager.movement.doMove(this.ship);
        this.updateCallback();
    };

    return ShipMovementCallbacks;
})();
