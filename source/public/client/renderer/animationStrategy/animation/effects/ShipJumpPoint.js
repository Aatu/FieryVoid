"use strict";

window.ShipJumpPoint = function () {
    /* args.playSound - play ShipJumpAudio.mp3 when the swirl starts.
     *
     * ⚠️ OPT-IN, and it has to be. This effect has TWO callers with opposite needs:
     *   - ShipJumpAnimation (a ship leaving through a jump point) owns its own Audio object and
     *     plays it from its own render(), so it must NOT ask for one here or the clip doubles;
     *   - ReplayAnimationStrategy.animateVortexLifecycle (a jump point forming or collapsing)
     *     constructs this effect bare, with nothing above it to make a noise - which is why that
     *     animation was silent from Stage 6 until 2026-08-25.
     * The second caller also has to PUSH the effect into the strategy's animation list, because
     * nothing calls render() on an object that is not in it. See the note there. */
    function ShipJumpPoint(emitterContainer, args) {
        Animation.call(this);

        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.position = args.position;
        this.emitterContainer = emitterContainer;

        // --- 🔊 sound, off unless the caller asks ---
        // One cached Audio cloned per playback, the pattern BlinkEffect/BoltEffect already use:
        // a replay can open several jump points in one turn and each needs its own playhead.
        this.playSound = Boolean(args.playSound);
        this.playedSound = false;
        this.soundVolume = args.soundVolume !== undefined ? args.soundVolume : 0.7;
        if (this.playSound && !ShipJumpPoint.cachedAudio) {
            ShipJumpPoint.cachedAudio = new Audio("client/renderer/animationStrategy/animation/sound/ShipJumpAudio.mp3");
        }

        // Swirl effect
        const swirlParticles = 300; // Number of particles for the swirl
        const maxRadius = 600; // Maximum radius for the swirl
        const swirlSpeed = 0.15; // Speed of rotation for the swirl

        for (let i = 0; i < swirlParticles; i++) {
            const angle = (i / swirlParticles) * Math.PI * 2; // Spread particles in a circle
            const delay = i * (3500 / swirlParticles); // Stagger their appearance

            new Explosion(this.emitterContainer, {
                size: 10, // Particle size
                position: { x: this.position.x, y: this.position.y },
                type: "swirl",
                color: "cyan",
                time: this.time + delay,
                update: (particle, progress) => {
                    const radius = progress * maxRadius;
                    const newAngle = angle + progress * swirlSpeed * Math.PI * 2;
                    particle.position.x = this.position.x + Math.cos(newAngle) * radius;
                    particle.position.y = this.position.y + Math.sin(newAngle) * radius;
                    particle.opacity = Math.max(1 - progress, 0); // Fade out
                }
            });
        }

        // Overlapping bursts to simulate a circular jump point
        const burstCount = 20; // Number of overlapping bursts
        for (let i = 0; i < burstCount; i++) {
            new Explosion(this.emitterContainer, {
                size: 40,
                position: { x: this.position.x, y: this.position.y }, // All bursts overlap at the same position
                type: "pillar",
                color: new THREE.Color(0.9, 0.4, 0),
                time: this.time + 2000 + i * 50 // Slight delay between each burst
            });
        }
    }
/*

// Central circular burst effect
setTimeout(() => {
    const burstCount = 15; // Number of overlapping bursts in the circle
    const radius = 0; // Radius of the circular pattern

    for (let i = 0; i < burstCount; i++) {
        const angle = (i / burstCount) * Math.PI * 2; // Spread bursts evenly in a circle
        const burstX = this.position.x + Math.cos(angle) * radius;
        const burstY = this.position.y + Math.sin(angle) * radius;

        new Explosion(this.emitterContainer, {
            size: 80, // Size of each burst
            position: { x: burstX, y: burstY },
            type: "burst",
            color: "#b35900",
            time: this.time + this.swirlDuration
        });
    }
}, this.swirlDuration);
*/


    /* ⚠️ INHERITS Animation SO IT IS SAFE IN AnimationStrategy.animations. That list is walked for
       update(gameData), render(...) and - from ReplayAnimationStrategy.deactivate - cleanUp(scene).
       This effect defined none of the three before, so pushing it into the list would have thrown
       on the first frame. Animation's no-ops cover update and cleanUp; render is overridden below.
       Assigned BEFORE getDuration so the swap does not discard it. */
    ShipJumpPoint.prototype = Object.create(Animation.prototype);
    ShipJumpPoint.prototype.constructor = ShipJumpPoint;

    ShipJumpPoint.prototype.getDuration = function () {
        return 4000; // Match the duration to ShipExplosion
    };

    /* Sound only - the particles were already seeded into the emitterContainer by the constructor
       and that container renders itself. Same guards every other effect's sound uses: not before
       the effect's own start time (so a replay preload cannot fire it), never while paused or
       scrubbing backwards, and the replay's playback rate applied so a slowed replay pitches the
       clip down with everything else. */
    ShipJumpPoint.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        if (!this.playSound || this.playedSound) return;
        if (total < this.time) return;
        if (paused || back || !gamedata.playAudio) return;

        try {
            const sound = ShipJumpPoint.cachedAudio.cloneNode(true);
            sound.volume = this.soundVolume;
            sound.currentTime = 0;
            window.applyReplayPlaybackRate(sound);
            sound.play().catch(() => {});
        } catch (e) {
            console.warn("Jump point sound failed:", e);
        }
        this.playedSound = true;
    };

    return ShipJumpPoint;
}();