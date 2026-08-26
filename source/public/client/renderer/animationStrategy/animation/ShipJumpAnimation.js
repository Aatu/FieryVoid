"use strict";

window.ShipJumpAnimation = function () {
    
    /* noJumpPoint - draw the fade WITHOUT the cyan vortex swirl. Shadow Association hulls do not
       tear a B5 jump point open, they simply fade out (user ruling 2026-08-25), and the flag rides
       their Phasing Drive's blueprint as noJumpPointAnimation - see PhasingDrive in
       baseSystems.php, and the caller in ReplayAnimationStrategy.animateShipDestruction.
       The camera pan, the fade and the log entry are kept; the particle effect AND the jump
       sound both go, because ShipJumpAudio is the sound of a vortex tearing open and a ship
       that never opens one should not make it (user ruling 2026-08-25). */
    function ShipJumpAnimation(time, shipIcon, emitterContainer, movementAnimations, noJumpPoint) {
        Animation.call(this);
        this.time = time;
        this.shipIcon = shipIcon;
        this.fadeoutTime = time + 2100;
        this.fadeoutDuration = 500;
        this.currentOpacity = 1.0;

        this.animations = [];

        var cameraAnimation = new CameraPositionAnimation(
            FireAnimationHelper.getShipPositionAtTime(this.shipIcon, this.time, movementAnimations),
            this.time
        );
        this.animations.push(cameraAnimation);

        if (noJumpPoint) {
            this.explosion = null;
            /* The fade is the whole animation now, so it has to state its own length - without the
               jump point there is nothing left to ask. It ends when the fade does, which is 400ms
               shorter than ShipJumpPoint's 4000 and keeps the replay from sitting on an empty hex. */
            this.duration = (this.fadeoutTime - this.time) + this.fadeoutDuration;
        } else {
            this.explosion = new ShipJumpPoint(emitterContainer, {
                time: this.time,
                position: FireAnimationHelper.getShipPositionAtTime(shipIcon, time, movementAnimations)
            });

            this.duration = this.explosion.getDuration();
        }

        // --- 🔊 Add sound support ---
        // A silent fade has no Audio object at all, so nothing is fetched for it either. render()
        // and cleanUp() both test this.sound rather than the flag, which is the same null check
        // cleanUp already made.
        this.explosionTriggered = false;
        this.sound = null;
        if (!noJumpPoint) {
            this.sound = new Audio("client/renderer/animationStrategy/animation/sound/ShipJumpAudio.mp3");
            this.sound.volume = 0.7;
        }
    }

    ShipJumpAnimation.prototype = Object.create(Animation.prototype);

    ShipJumpAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {

        // --- 🔊 Play sound once when jump starts ---
        if (this.sound && !this.explosionTriggered && total >= this.time && gamedata.playAudio && !paused && !back) {
            this.sound.currentTime = 0;
            window.applyReplayPlaybackRate(this.sound);
            this.sound.play().catch(() => {});
            this.explosionTriggered = true;
        }

        this.animations.forEach(function (animation) {
            animation.render(now, total, last, delta, zoom, back, paused);
        });

        // --- existing opacity fading code ---
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


    ShipJumpAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    ShipJumpAnimation.prototype.cleanUp = function () {
        this.shipIcon.setOpacity(1);
        if (this.sound) {
            this.sound.pause();
            this.sound.currentTime = 0;
        }
    };

    return ShipJumpAnimation;
}();

/*
"use strict";

window.ShipJumpAnimation = function () {
    function ShipJumpAnimation(time, shipIcon, emitterContainer, movementAnimations) {
        Animation.call(this);
        this.time = time;
        this.shipIcon = shipIcon;
        this.fadeoutTime = time + 2100;
        this.fadeoutDuration = 500;
        this.currentOpacity = 1.0;

        this.animations = [];

        var cameraAnimation = new CameraPositionAnimation(FireAnimationHelper.getShipPositionAtTime(this.shipIcon, this.time, movementAnimations), this.time);

        this.animations.push(cameraAnimation);

        this.explosion = new ShipJumpPoint(emitterContainer, {
            time: this.time,
            position: FireAnimationHelper.getShipPositionAtTime(shipIcon, time, movementAnimations)
        });

        this.duration = this.explosion.getDuration();
    }

    ShipJumpAnimation.prototype = Object.create(Animation.prototype);

    ShipJumpAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {

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

    ShipJumpAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    ShipJumpAnimation.prototype.cleanUp = function () {
        this.shipIcon.setOpacity(1);
    };

    return ShipJumpAnimation;
}();
*/