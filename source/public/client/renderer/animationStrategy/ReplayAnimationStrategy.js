"use strict";

window.ReplayAnimationStrategy = function () {

    ReplayAnimationStrategy.type = {
        INFORMATIVE: 1,
        PHASE: 2,
        ALL: 3
    };

    function ReplayAnimationStrategy(gamedata, shipIcons, scene, type) {
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

        this.movementPhaseStartTime = null;
        this.firingPhaseStartTime = null;

        /*
        this.explosion = new ShipExplosion(this.emitterContainer, {
            time: 0,
            position: {x:0, y:0}
        });
        */

        buildAnimations.call(this);
    }

    ReplayAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    ReplayAnimationStrategy.prototype.activate = function () {
        return this;
    };

    ReplayAnimationStrategy.prototype.deactivate = function (scene) {
        this.animations.forEach(function (animation) {
            animation.cleanUp(scene);
        });

        this.emitterContainer.cleanUp();

        this.gamedata.ships.forEach(function (ship) {
            this.shipIconContainer.getByShip(ship).show();
        }, this);

        return this;
    };

    ReplayAnimationStrategy.prototype.isDone = function () {
        return this.endTime < this.totalAnimationTime || this.totalAnimationTime < 0;
    };

    ReplayAnimationStrategy.prototype.update = function () {
        return this;
    };

    ReplayAnimationStrategy.prototype.toFiringPhase = function () {
        this.goToTime(this.firingPhaseStartTime)
        return this;
    };

    ReplayAnimationStrategy.prototype.toMovementPhase = function () {
        this.goToTime(this.movementPhaseStartTime)
        return this;
    };

    function buildAnimations() {

        var time = 0;
        var logAnimation = new LogAnimation();
        this.animations.push(logAnimation);

        this.movementPhaseStartTime = time;
        time = animateMovement.call(this, time);
        this.firingPhaseStartTime = time;
        time = animateWeaponFire.call(this, time, logAnimation);
        time = animateShipDestruction.call(this, time, logAnimation);
        time += 100;

        this.endTime = time;
    }

    function animateShipDestruction(time, logAnimation) {
        this.gamedata.ships.filter(function (ship) {
            return shipManager.getTurnDestroyed(ship) === this.turn && !ship.flight;
        }, this).forEach(function (ship) {
            console.log("SHIP DESTROYED", ship.imagePath);

            var animation = new ShipDestroyedAnimation(time, this.shipIconContainer.getByShip(ship), this.emitterContainer, this.movementAnimations);
            time += animation.getDuration();
            this.animations.push(animation);
        }, this);

        this.gamedata.ships.filter(function (ship) {
            var turnDestroyed = shipManager.getTurnDestroyed(ship);
            var destroyed = shipManager.isDestroyed(ship);
            return (turnDestroyed !== null && turnDestroyed < this.turn) || (turnDestroyed === null && destroyed);
        }, this).forEach(function (ship) {
            this.shipIconContainer.getByShip(ship).hide();
        }, this);

        this.gamedata.ships.filter(function (ship) {
            return ship.flight;
        }, this).forEach(function (ship) {
            var fightersToHide = ship.systems.filter(function (fighter) {
                var turnDestroyed = damageManager.getTurnDestroyed(ship, fighter);
                return turnDestroyed !== null && turnDestroyed < this.turn;
            }, this);

            this.shipIconContainer.getByShip(ship).hideFighters(fightersToHide);
        }, this);

        return time;
    }

    function animateMovement(time) {
        this.gamedata.ships.forEach(function (ship) {
            var icon = this.shipIconContainer.getByShip(ship);

            var animation = new ShipMovementAnimation(icon, this.turn, this.shipIconContainer);
            setMovementAnimationDuration.call(this, animation);

            if (animation.getLength() > 0) {
                var cameraAnimation = new CameraPositionAnimation(animation.getStartPosition(), time, 0);
                this.animations.push(cameraAnimation);
                time += cameraAnimation.getDuration();
            }

            animation.setTime(time);
            this.animations.push(animation);
            this.movementAnimations[ship.id] = animation;

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += animation.getDuration();
            }
        }, this);

        return time;
    }

    function animateWeaponFire(time, logAnimation) {
        var animation = new HexTargetedWeaponFireAnimation(time, this.movementAnimations, this.shipIconContainer, this.turn, this.emitterContainer, logAnimation);
        this.animations.push(animation);
        if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
            time += animation.getDuration();
        }

        this.gamedata.ships.forEach(function (ship, i) {
            var animation = new AllWeaponFireAgainstShipAnimation(ship, this.shipIconContainer, this.emitterContainer, this.gamedata, time, this.scene, this.movementAnimations, logAnimation);
            this.animations.push(animation);

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += animation.getDuration();
            }
        }, this);

        return time;
    }

    function setMovementAnimationDuration(moveAnimation) {
        if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
            moveAnimation.setDuration(moveAnimation.getLength() * this.moveHexDuration);
        } else {
            moveAnimation.setDuration(this.moveAnimationDuration);
        }
    }

    return ReplayAnimationStrategy;
}();