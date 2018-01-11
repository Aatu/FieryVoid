window.IdleAnimationStrategy = (function(){
    function IdleAnimationStrategy(){
        AnimationStrategy.call(this);
    }

    IdleAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    IdleAnimationStrategy.prototype.update = function(gamedata) {
        AnimationStrategy.prototype.update.call(this, gamedata);
        this.positionAndFaceAllIcons();
        return this;
    };

    return IdleAnimationStrategy;
})();