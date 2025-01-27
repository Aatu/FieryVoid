"use strict";

window.ShipJumpPoint = function () {
    function ShipJumpPoint(emitterContainer, args) {
        if (!args) {
            args = {};
        }

        this.time = args.time || 0;
        this.position = args.position;
        this.emitterContainer = emitterContainer;

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


    ShipJumpPoint.prototype.getDuration = function () {
        return 4000; // Match the duration to ShipExplosion
    };

    return ShipJumpPoint;
}();