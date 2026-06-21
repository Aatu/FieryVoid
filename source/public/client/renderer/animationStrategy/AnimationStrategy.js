"use strict";

// Current replay playback rate, published each frame by the active
// AnimationStrategy.render and read by the sound effects when they fire so a
// slowed/sped-up replay also slows/speeds its audio (tape-style pitch shift).
// 1 = normal. Lives on window because the leaf effects are global singletons
// that don't hold a reference to the strategy.
window.replayPlaybackRate = 1;

// Clamp to the range HTMLMediaElement.playbackRate accepts across browsers
// (values outside ~[0.0625, 16] throw or are ignored). Our replay speeds
// (0.25–8) are well inside this, but clamp defensively.
window.applyReplayPlaybackRate = function (audio) {
    if (!audio) {
        return;
    }
    var rate = window.replayPlaybackRate || 1;
    if (rate < 0.0625) rate = 0.0625;
    if (rate > 16) rate = 16;
    try {
        audio.playbackRate = rate;
    } catch (e) {
        // Some browsers throw for out-of-range/unsupported rates; ignore and
        // let the clip play at its natural speed.
    }
};

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
        this.lastAnimationTime = 0; // Re-baseline after an idle gap (see play()).
    };

    AnimationStrategy.prototype.play = function () {
        this.paused = false;
        this.goingBack = false;
        this.speedMultiplier = 1;
        // Idle render-loop gating: the loop stops calling render() while the board
        // is static, so lastAnimationTime can be stale by an arbitrary gap when an
        // animation (re)starts. Clear it so updateDeltaTime re-baselines on the
        // first frame (delta 0) instead of fast-forwarding totalAnimationTime by
        // the whole idle gap — which skipped projectile travel straight to impact.
        this.lastAnimationTime = 0;
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
        this.lastAnimationTime = 0; // Re-baseline after an idle gap (see play()).
    };

    AnimationStrategy.prototype.isPaused = function () {
        return this.paused;
    };

    // Idle render-loop gating: true while frames need to keep advancing.
    // Active (non-paused) playback with at least one animation means the board
    // is moving; a paused strategy or an empty animation list is static.
    AnimationStrategy.prototype.isAnimating = function () {
        return !this.paused && this.animations.length > 0;
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
        // Publish the rate sounds should play at this frame. Effects only fire
        // their clips while playing forward (not paused/back), so the rate is
        // simply the current speedMultiplier.
        window.replayPlaybackRate = this.speedMultiplier;
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