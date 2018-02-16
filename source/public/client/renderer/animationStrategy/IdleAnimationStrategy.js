window.IdleAnimationStrategy = (function(){
    function IdleAnimationStrategy(shipIcons, turn){
        AnimationStrategy.call(this, shipIcons, turn);
        console.log("IdleAnimationStrategy, turn:", this.turn);
        console.trace();
    }

    IdleAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    IdleAnimationStrategy.prototype.update = function(gamedata) {
        AnimationStrategy.prototype.update.call(this, gamedata);
        this.positionAndFaceAllIcons();

        this.shipIconContainer.getArray().forEach(function(icon){
            var ship = icon.ship;

            var turnDestroyed = shipManager.getTurnDestroyed(ship);

            if (turnDestroyed !== null && turnDestroyed < this.turn) {
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

    IdleAnimationStrategy.prototype.deactivate = function() {

        if (this.shipIconContainer){
            this.shipIconContainer.getArray().forEach(function(icon){
                icon.show();
            }, this);
        }

        return AnimationStrategy.prototype.deactivate.call(this);
    };

    return IdleAnimationStrategy;
})();