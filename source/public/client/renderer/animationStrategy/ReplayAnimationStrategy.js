window.ReplayAnimationStrategy = (function(){

    function ReplayAnimationStrategy(onDoneCallback, gameData, shipIcons, turn){
        AnimationStrategy.call(this, onDoneCallback);
        this.shipIconContainer = shipIcons;
        this.turn = gameData.turn;
        buildAnimations.call(this, gamedata);
    }

    ReplayAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    ReplayAnimationStrategy.prototype.activate = function() {
        return this;
    };

    ReplayAnimationStrategy.prototype.deactivate = function(scene) {
        this.animations.forEach(function (animation) {
           animation.cleanUp(scene);
        });
        return this;
    };

    ReplayAnimationStrategy.prototype.render = function(){
        AnimationStrategy.prototype.render.call(this);
    };

    ReplayAnimationStrategy.prototype.update = function() {
        return this;
    };

    ReplayAnimationStrategy.prototype.start = function() {

        this.animations.forEach(function (animation) {
            animation.stop();
            animation.reset();
        });

        this.animations[0].start();
        return this;
    };

    ReplayAnimationStrategy.prototype.animationDone = function(index) {
        console.log("done with animation ", index);
        this.animations[index].stop();
        if (! this.animations[index+1]) {
            this.done();
            return;
        }

        this.animations[index+1].start();
    };

    function buildAnimations(gamedata) {
        gamedata.ships.forEach(function (ship, i) {
            var icon = this.shipIconContainer.getByShip(ship);
            if (this.animations.some(function (animation) { return animation.shipIcon === icon})) {
                return;
            }

            var animation = new ShipMovementAnimation(icon, this.turn, this.animationDone.bind(this, i));
            this.animations.push(animation);

        }, this);
    }

    return ReplayAnimationStrategy;
})();