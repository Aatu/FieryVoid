window.IdleAnimationStrategy = (function(){
    function IdleAnimationStrategy(){
        this.shipIconContainer = null;
        this.turn = 0;
    }

    IdleAnimationStrategy.prototype.activate = function(shipIcons, turn, scene) {
        this.shipIconContainer = shipIcons;
        this.turn = turn;

        return this;
    };

    IdleAnimationStrategy.prototype.deactivate = function(scene) {


        return this;
    };

    IdleAnimationStrategy.prototype.render = function(coordinateConverter){
        this.shipIconContainer.getArray().forEach(function (icon) {
            this.positionAndFaceIcon(icon, coordinateConverter);
        }, this);
    };

    IdleAnimationStrategy.prototype.positionAndFaceIcon = function(icon, coordinateConverter){
        var movements = icon.getMovements();
        var movement = this.getLastMovement(movements);
        var gamePosition = coordinateConverter.fromHexToGame(movement.position);

        var facing = shipManager.hexFacingToAngle(movement.facing);

        icon.setPosition(gamePosition);
        icon.setFacing(-facing);
    };

    IdleAnimationStrategy.prototype.getLastMovement = function(movements) {
        return movements[movements.length - 1];
    };

    return IdleAnimationStrategy;
})();