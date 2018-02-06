window.IdleAnimationStrategy = (function(){
    function IdleAnimationStrategy(shipIcons, turn){
        AnimationStrategy.call(this, shipIcons, turn);
        console.log("IdleAnimationStrategy, turn:", this.turn);
    }

    IdleAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    IdleAnimationStrategy.prototype.update = function(gamedata) {
        AnimationStrategy.prototype.update.call(this, gamedata);
        this.positionAndFaceAllIcons();
        return this;
    };

    return IdleAnimationStrategy;
})();