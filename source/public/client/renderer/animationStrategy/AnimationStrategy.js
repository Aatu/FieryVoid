window.AnimationStrategy = (function(){
    function IdleAnimationStrategy(){
        this.shipIconContainer = null;
        this.turn = 0;
        this.lastAnimationTime = 0;
        this.totalAnimationTime = 0;
        this.currentDeltaTime = 0;
        this.animations = [];
    }

    IdleAnimationStrategy.prototype.activate = function(shipIcons, turn, scene) {
        this.shipIconContainer = shipIcons;
        this.turn = turn;

        return this;
    };

    IdleAnimationStrategy.prototype.update = function() {
        this.positionAndFaceAllIcons();
        return this;
    };

    IdleAnimationStrategy.prototype.deactivate = function(scene) {
        return this;
    };

    IdleAnimationStrategy.prototype.render = function(){
        updateDeltaTime.call(this);
        updateTotalAnimationTime.call(this);
    };

    IdleAnimationStrategy.prototype.positionAndFaceAllIcons = function() {
        this.shipIconContainer.getArray().forEach(function (icon) {
            this.positionAndFaceIcon(icon);
        }, this);
    };

    IdleAnimationStrategy.prototype.positionAndFaceIcon = function(icon){
        var movement = icon.getLastMovement();
        var gamePosition = window.coordinateConverter.fromHexToGame(movement.position);

        var facing = shipManager.hexFacingToAngle(movement.facing);

        icon.setPosition(gamePosition);
        icon.setFacing(-facing);
    };

    IdleAnimationStrategy.prototype.initializeAnimations = function() {
        this.animations.forEach(function (animation) {
            animation.initialize();
        })
    };

    IdleAnimationStrategy.prototype.removeAnimation = function(toRemove) {
        this.animations = this.animations.filter(function (animation) {
            return animation != animation;
        });

        toRemove.deactivate();
    };

    function updateTotalAnimationTime(){
        this.totalAnimationTime += this.currentDeltaTime;
    }

    function updateDeltaTime(){
        var now = new Date().getTime();

        if (! this.lastAnimationTime) {
            this.lastAnimationTime = now;
            this.currentDeltaTime = 0;
        }

        this.currentDeltaTime = now - this.lastAnimationTime;
        this.lastAnimationTime = now;
    }


    return IdleAnimationStrategy;
})();