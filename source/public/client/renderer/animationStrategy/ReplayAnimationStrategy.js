window.ReplayAnimationStrategy = (function(){

    ReplayAnimationStrategy.type = {
        INFORMATIVE: 1,
        PHASE: 2,
        ALL: 3
    };

    function ReplayAnimationStrategy(gamedata, shipIcons, scene, type){
        AnimationStrategy.call(this);
        this.shipIconContainer = shipIcons;
        this.gamedata = gamedata;
        this.turn = gamedata.turn;
        this.emitterContainer = new ParticleEmitterContainer(scene);
        this.animations.push(this.emitterContainer);
        this.emitterContainer.start();
        this.scene = scene;

        this.movementAnimations = {};

        this.moveHexDuration = 400;
        this.moveAnimationDuration = 2500;
        this.type = type || ReplayAnimationStrategy.type.INFORMATIVE;

        this.currentTime = 0;
        this.endTime = null;

        buildAnimations.call(this);

    }

    ReplayAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    ReplayAnimationStrategy.prototype.activate = function() {
        return this;
    };

    ReplayAnimationStrategy.prototype.deactivate = function(scene) {
        this.animations.forEach(function (animation) {
           animation.cleanUp(scene);
        });

        this.emitterContainer.cleanUp();

        return this;
    };

    ReplayAnimationStrategy.prototype.isDone = function() {
        return this.endTime < this.totalAnimationTime;
    };

    ReplayAnimationStrategy.prototype.update = function() {
        return this;
    };

    function buildAnimations() {

        var time = 0;
        //var animation = new ShipMovementAnimation(this.shipIconContainer.getByShip(gamedata.ships[1]), this.turn);
        //this.animations.push(animation);

        var logAnimation = new LogAnimation();
        this.animations.push(logAnimation);
        //return;

        this.gamedata.ships.forEach(function (ship, i) {
            var icon = this.shipIconContainer.getByShip(ship);


            var animation = new ShipMovementAnimation(icon, this.turn, this.shipIconContainer);
            setMovementAnimationDuration.call(this, animation);
            animation.setTime(time);
            this.animations.push(animation);

            this.movementAnimations[ship.id] = animation;

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += animation.getDuration();
            }

        }, this);

        this.gamedata.ships.forEach(function (ship, i) {
            var animation = new AllWeaponFireAgainstShipAnimation(ship, this.shipIconContainer, this.emitterContainer, this.gamedata, time, this.scene, this.movementAnimations, logAnimation);
            this.animations.push(animation);

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += animation.getDuration();
            }

        }, this);

        time += 100;

        this.endTime = time;
        //TODO: ship destruction animations

    }

    function setMovementAnimationDuration(moveAnimation) {
        if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
            moveAnimation.setDuration(moveAnimation.getLength() * this.moveHexDuration);
        } else {
            moveAnimation.setDuration(this.moveAnimationDuration);
        }
    }

    return ReplayAnimationStrategy;
})();