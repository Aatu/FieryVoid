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