window.AnimationStrategy = (function(){
    function AnimationStrategy(onDoneCallback){
        this.shipIconContainer = null;
        this.turn = 0;
        this.lastAnimationTime = 0;
        this.totalAnimationTime = 0;
        this.currentDeltaTime = 0;
        this.animations = [];
        this.onDoneCallback = onDoneCallback;
    }

    AnimationStrategy.prototype.activate = function(shipIcons, turn, scene) {
        this.shipIconContainer = shipIcons;
        this.turn = turn;

        return this;
    };

    AnimationStrategy.prototype.update = function(gameData) {

        this.animations.forEach(function (animation) {
           animation.update(gameData);
        });

        return this;
    };

    AnimationStrategy.prototype.deactivate = function(scene) {
        return this;
    };

    AnimationStrategy.prototype.render = function(){
        updateDeltaTime.call(this);
        updateTotalAnimationTime.call(this);
        this.animations.forEach(function (animation) {
            animation.render(new Date().getTime(), this.totalAnimationTime, this.lastAnimationTime, this.currentDeltaTime)
        }, this);
    };

    AnimationStrategy.prototype.positionAndFaceAllIcons = function() {
        this.shipIconContainer.getArray().forEach(function (icon) {
            this.positionAndFaceIcon(icon);
        }, this);
    };

    AnimationStrategy.prototype.positionAndFaceIcon = function(icon){
        var movement = icon.getLastMovement();
        var gamePosition = window.coordinateConverter.fromHexToGame(movement.position);

        var facing = mathlib.hexFacingToAngle(movement.facing);

        icon.setPosition(gamePosition);
        icon.setFacing(-facing);
    };

    /*
    AnimationStrategy.prototype.initializeAnimations = function() {
        this.animations.forEach(function (animation) {
            animation.initialize();
        })
    };
    */

    AnimationStrategy.prototype.removeAnimation = function(toRemove) {
        this.animations = this.animations.filter(function (animation) {
            return animation !== animation;
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

    AnimationStrategy.prototype.done = function() {
        if (this.onDoneCallback) {
            this.onDoneCallback();
        }
    };


    return AnimationStrategy;
})();