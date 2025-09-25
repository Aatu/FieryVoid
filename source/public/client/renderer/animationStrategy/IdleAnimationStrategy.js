"use strict";

window.IdleAnimationStrategy = function () {
    function IdleAnimationStrategy(shipIcons, turn) {
        AnimationStrategy.call(this, shipIcons, turn);
    }

    IdleAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    IdleAnimationStrategy.prototype.update = function (gamedata) {
        AnimationStrategy.prototype.update.call(this, gamedata);
        this.positionAndFaceAllIcons();

        this.shipIconContainer.getArray().forEach(function (icon) {
            var ship = icon.ship;

            var turnDestroyed = shipManager.getTurnDestroyed(ship);
            var destroyed = shipManager.isDestroyed(ship);

            if (turnDestroyed !== null && turnDestroyed < this.turn) {
                icon.hide();
            } else if (turnDestroyed === null && destroyed) {
                icon.hide();
            } else if (shipManager.shouldBeHidden(ship)) { //Stealth or not deployed yet.
                icon.hide();
            } else {
                icon.show();
            }

            if (icon instanceof FlightIcon) {
                icon.hideDestroyedFighters();
            }
        }, this);
        return this;
    };


    IdleAnimationStrategy.prototype.shipMovementChanged = function (ship) {
        this.shipIconContainer.positionAndFaceShip(ship);
    };

    IdleAnimationStrategy.prototype.deactivate = function () {

        if (this.shipIconContainer) {
            this.shipIconContainer.getArray().forEach(function (icon) {
                icon.show();
            }, this);
        }

        return AnimationStrategy.prototype.deactivate.call(this);
    };

    return IdleAnimationStrategy;
}();