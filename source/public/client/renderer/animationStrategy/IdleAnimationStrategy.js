window.IdleAnimationStrategy = (function(){
    function IdleAnimationStrategy(){
        this.shipIcons = null;
        this.turn = 0;
    }

    IdleAnimationStrategy.prototype.activate = function(shipIcons, turn) {
        this.shipIcons = shipIcons;
        this.turn = turn;

        return this;
    };

    IdleAnimationStrategy.prototype.render = function(coordinateConverter){
        Object.keys(this.shipIcons).forEach(function (key) {
            var icon = this.shipIcons[key];
            positionAndFaceIcon(icon, coordinateConverter);
        }, this);
    };

    function positionAndFaceIcon(icon, coordinateConverter){
        var movements = icon.getMovements();
        var movement = getLastMovement(movements);
        var gamePosition = coordinateConverter.fromFVHexToGame(movement.position);

        var facing = shipManager.hexFacingToAngle(movement.facing);

        icon.setPosition(gamePosition);
        icon.setFacing(facing);
    }

    function getLastMovement(movements) {
        return movements[movements.length - 1];
    }

    return IdleAnimationStrategy;
})();