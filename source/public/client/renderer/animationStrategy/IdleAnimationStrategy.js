window.IdleAnimationStrategy = (function(){
    function IdleAnimationStrategy(){
        AnimationStrategy.call(this);
    }

    IdleAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    return IdleAnimationStrategy;
})();