window.AnimationStrategy = (function(){
    function AnimationStrategy(shipIcons, turn){
        this.shipIconContainer = null;
        this.turn = 0;
        this.lastAnimationTime = 0;
        this.totalAnimationTime = 0;
        this.currentDeltaTime = 0;
        this.animations = [];
        this.paused = true;
        this.shipIconContainer = shipIcons;
        this.turn = turn;
        this.goingBack = false;
    }

    AnimationStrategy.prototype.activate = function() {
        this.play();

        return this;
    };

    AnimationStrategy.prototype.update = function(gameData) {

        this.animations.forEach(function (animation) {
           animation.update(gameData);
        });

        return this;
    };

    AnimationStrategy.prototype.stop = function(gameData) {

        this.lastAnimationTime = 0;
        this.totalAnimationTime = 0;
        this.currentDeltaTime = 0;
        this.pause();
    };

    AnimationStrategy.prototype.back = function() {
        this.goingBack = true;
        this.paused = false;
    };

    AnimationStrategy.prototype.play = function() {
        this.paused = false;
        this.goingBack = false;
    };

    AnimationStrategy.prototype.pause = function() {
        this.paused = true;
        this.goingBack = false;
    };

    AnimationStrategy.prototype.deactivate = function() {
        return this;
    };

    AnimationStrategy.prototype.render = function(coordinateConverter, scene, zoom){
        updateDeltaTime.call(this, this.paused);
        updateTotalAnimationTime.call(this, this.paused);
        this.animations.forEach(function (animation) {
            animation.render(new Date().getTime(), this.totalAnimationTime, this.lastAnimationTime, this.currentDeltaTime, zoom, this.goingBack, this.paused)
        }, this);
    };

    AnimationStrategy.prototype.positionAndFaceAllIcons = function() {
        this.shipIconContainer.positionAndFaceAllIcons();
    };

    AnimationStrategy.prototype.positionAndFaceIcon = function(icon){
        icon.positionAndFaceIcon();
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

    function updateTotalAnimationTime(paused){
        if (paused) {
            return;
        }

        if (this.goingBack) {
            this.totalAnimationTime -= this.currentDeltaTime;
        } else {
            this.totalAnimationTime += this.currentDeltaTime;
        }
    }

    function updateDeltaTime(paused){
        var now = new Date().getTime();

        if (! this.lastAnimationTime) {
            this.lastAnimationTime = now;
            this.currentDeltaTime = 0;
        }

        if (!paused) {
            this.currentDeltaTime = now - this.lastAnimationTime;
        }

        this.lastAnimationTime = now;
    }

    return AnimationStrategy;
})();