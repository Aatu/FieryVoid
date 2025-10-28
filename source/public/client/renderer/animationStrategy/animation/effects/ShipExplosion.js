
"use strict";

window.ShipExplosion = function () {

    function ShipExplosion(emitterContainer, args) {
        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.position = args.position;
        this.emitterContainer = emitterContainer;

        // --- Explosion particle effects ---
        new Explosion(this.emitterContainer, {
            size: 80,
            position: this.position,
            type: "gas",
            time: this.time + 2000
        });

        new Explosion(this.emitterContainer, {
            size: 200,
            position: this.position,
            type: "glow",
            time: this.time + 2000
        });

        let amount = Math.round(Math.random() * 5) + 2;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                size: (Math.random() * 2 + 1) * 12,
                position: {
                    x: this.position.x + Math.random() * 30 - 15,
                    y: this.position.y + Math.random() * 30 - 15
                },
                type: ["gas", "pillar"][Math.round(Math.random() * 2)],
                time: this.time + Math.random() * 2000
            });
        }

        // --- Explosion sound effect ---
        if (gamedata.playAudio) {
            // Cache the audio file once for all ShipExplosions
            if (!ShipExplosion.cachedAudio) {
                ShipExplosion.cachedAudio = new Audio("/client/renderer/animationStrategy/animation/sound/ShipExplosionAudio.wav");
                ShipExplosion.cachedAudio.volume = 0.5; // default volume
            }

            try {
                // Delay playback by ~2 seconds (2000 ms)
                setTimeout(() => {
                    // Clone for simultaneous overlapping explosions
                    const explosionSound = ShipExplosion.cachedAudio.cloneNode(true);
                    explosionSound.volume =
                        args.soundVolume !== undefined ? args.soundVolume : ShipExplosion.cachedAudio.volume;
                    explosionSound.currentTime = 0;
                    explosionSound.play().catch(() => {}); // handle autoplay restrictions
                }, 500); // delay in milliseconds
            } catch (e) {
                console.warn("Explosion sound playback failed:", e);
            }
        }
    }

    ShipExplosion.prototype.getDuration = function () {
        return 4000;
    };

    return ShipExplosion;
}();

/* //Old version wihtout audio
"use strict";

window.ShipExplosion = function () {

    function ShipExplosion(emitterContainer, args) {
        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.position = args.position;
        this.emitterContainer = emitterContainer;

        new Explosion(this.emitterContainer, {
            size: 80,
            position: this.position,
            type: "gas",
            time: this.time + 2000
        });

        new Explosion(this.emitterContainer, {
            size: 200,
            position: this.position,
            type: "glow",
            time: this.time + 2000
        });

        var amount = Math.round(Math.random() * 5) + 2;

        while (amount--) {
            new Explosion(this.emitterContainer, {
                size: (Math.random() * 2 + 1) * 12,
                position: { x: this.position.x + Math.random() * 30 - 15, y: this.position.y + Math.random() * 30 - 15 },
                type: ["gas", "pillar"][Math.round(Math.random() * 2)],
                time: this.time + Math.random() * 2000
            });
        }
    }

    ShipExplosion.prototype.getDuration = function () {
        return 4000;
    };

    return ShipExplosion;
}();
*/