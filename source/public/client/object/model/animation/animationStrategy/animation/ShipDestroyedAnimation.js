"use strict";

window.ShipDestroyedAnimation = function () {
    function ShipDestroyedAnimation(time, shipIcon, emitterContainer, movementAnimations) {
        Animation.call(this);
        this.time = time;
        this.shipIcon = shipIcon;
        this.fadeoutTime = time + 2100;
        this.fadeoutDuration = 500;
        this.currentOpacity = 1.0;

        this.animations = [];

        var cameraAnimation = new CameraPositionAnimation(FireAnimationHelper.getShipPositionAtTime(this.shipIcon, this.time, movementAnimations), this.time);

        this.animations.push(cameraAnimation);

        this.explosion = new ShipExplosion(emitterContainer, {
            time: this.time,
            position: FireAnimationHelper.getShipPositionAtTime(shipIcon, time, movementAnimations)
        });

        this.duration = this.explosion.getDuration();
    }

    ShipDestroyedAnimation.prototype = Object.create(Animation.prototype);

    ShipDestroyedAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {

        this.animations.forEach(function (animation) {
            animation.render(now, total, last, delta, zoom, back, paused);
        });

        var opacity;

        if (total > this.fadeoutTime && total < this.fadeoutTime + this.fadeoutDuration) {
            opacity = 1 - (total - this.fadeoutTime) / this.fadeoutDuration;
        } else if (total < this.fadeoutTime) {
            opacity = 1;
        } else {
            opacity = 0;
        }

        if (this.currentOpacity !== opacity) {
            this.currentOpacity = opacity;

            this.shipIcon.setOpacity(opacity);
        }
    };

    ShipDestroyedAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    ShipDestroyedAnimation.prototype.cleanUp = function () {
        this.shipIcon.setOpacity(1);
    };

    return ShipDestroyedAnimation;
}();