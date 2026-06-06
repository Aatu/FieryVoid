"use strict";

window.AnimationStrategy = function () {
    function AnimationStrategy(shipIcons, turn) {
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
        // Replay fast-forward / rewind: scales how fast totalAnimationTime
        // advances each frame. 1 = normal speed. Applied to the already
        // sanitised currentDeltaTime (see updateTotalAnimationTime), so the
        // >1000ms audio-spam guard in updateDeltaTime is preserved.
        this.speedMultiplier = 1;
    }

    AnimationStrategy.prototype.activate = function () {
        this.play();

        return this;
    };

    AnimationStrategy.prototype.update = function (gameData) {

        this.animations.forEach(function (animation) {
            animation.update(gameData);
        });

        return this;
    };

    AnimationStrategy.prototype.stop = function (gameData) {

        this.lastAnimationTime = 0;
        this.totalAnimationTime = 0;
        this.currentDeltaTime = 0;
        this.pause();
    };

    AnimationStrategy.prototype.back = function () {
        this.goingBack = true;
        this.paused = false;
        this.speedMultiplier = 1;
    };

    AnimationStrategy.prototype.play = function () {
        this.paused = false;
        this.goingBack = false;
        this.speedMultiplier = 1;
    };

    AnimationStrategy.prototype.pause = function () {
        this.paused = true;
        this.goingBack = false;
        this.speedMultiplier = 1;
    };

    // Replay fast-forward / rewind. forward=false rewinds. multiplier is the
    // playback rate (e.g. 2/4/8). multiplier 1 with forward true is identical
    // to play(); with forward false identical to back().
    AnimationStrategy.prototype.fastSeek = function (multiplier, forward) {
        this.paused = false;
        this.goingBack = !forward;
        this.speedMultiplier = multiplier;
    };

    AnimationStrategy.prototype.isPaused = function () {
        return this.paused;
    };

    AnimationStrategy.prototype.deactivate = function () {
        return this;
    };

    AnimationStrategy.prototype.goToTime = function (time) {
        this.totalAnimationTime = time;
        return this
    };

    AnimationStrategy.prototype.render = function (coordinateConverter, scene, zoom) {
        updateDeltaTime.call(this, this.paused);
        updateTotalAnimationTime.call(this, this.paused);
        this.animations.forEach(function (animation) {
            animation.render(new Date().getTime(), this.totalAnimationTime, this.lastAnimationTime, this.currentDeltaTime, zoom, this.goingBack, this.paused);
        }, this);
    };

    AnimationStrategy.prototype.positionAndFaceAllIcons = function () {
        this.shipIconContainer.positionAndFaceAllIcons();
    };

    AnimationStrategy.prototype.positionAndFaceIcon = function (icon) {
        icon.positionAndFaceIcon();
    };

    /*
    AnimationStrategy.prototype.initializeAnimations = function() {
        this.animations.forEach(function (animation) {
            animation.initialize();
        })
    };
    */

    AnimationStrategy.prototype.removeAnimation = function (toRemove) {
        this.animations = this.animations.filter(function (animation) {
            return animation !== animation;
        });

        toRemove.deactivate();
    };

    AnimationStrategy.prototype.shipMovementChanged = function () {};

    function updateTotalAnimationTime(paused) {
        if (paused) {
            return;
        }

        var step = this.currentDeltaTime * this.speedMultiplier;

        if (this.goingBack) {
            this.totalAnimationTime -= step;
        } else {
            this.totalAnimationTime += step;
        }
    }

    function updateDeltaTime(paused) {
        var now = Date.now();

        if (!this.lastAnimationTime) {
            this.lastAnimationTime = now;
            this.currentDeltaTime = 0;
            return;
        }

        var delta = now - this.lastAnimationTime;
        this.lastAnimationTime = now;

        // If paused, hidden, or a massive time jump occurred (e.g. tab sleep), zero the delta
        // to prevent animations "catching up" all at once (and blasting overlapping audio).
        if (paused || document.hidden || delta > 1000) {
            this.currentDeltaTime = 0;
        } else {
            this.currentDeltaTime = delta;
        }
    }

    return AnimationStrategy;
}();