"use strict";

window.FlightIcon = function () {

    var FIGHTER_SPRITE_SIZE = 34;

    function FlightIcon(ship, scene) {
        this.fighters = consumeFighters.call(this, ship);
        this.fighterSprites = [];

        ShipIcon.call(this, ship, scene);
        this.size = FIGHTER_SPRITE_SIZE;
    }

    FlightIcon.prototype = Object.create(ShipIcon.prototype);

    FlightIcon.prototype.consumeShipdata = function (ship) {
        ShipIcon.prototype.consumeShipdata.call(this, ship);
        this.fighters = consumeFighters.call(this, ship);
    };

    FlightIcon.prototype.createShipWindow = function (ship) {
        var element = jQuery(".shipwindow.ship_" + ship.id);

        if (!element.length) {
            ship.shipStatusWindow = flightWindowManager.createShipWindow(ship);
        } else {
            ship.shipStatusWindow = element;
        }

        shipWindowManager.setData(ship);
    };

    FlightIcon.prototype.hideDestroyedFighters = function () {
        this.fighterSprites.forEach(function (sprite) {
            sprite.hide();
        });

        this.fighters.forEach(function (fighter, i) {
            if (fighter.destroyed) {
                this.fighterSprites[fighter.location].hide();
            } else {
                this.fighterSprites[fighter.location].show();
            }
        }, this);
    };

    FlightIcon.prototype.hideFighters = function (fightersToHide) {
        this.fighters.forEach(function (fighter) {
            var found = fightersToHide.some(function (fighterToHide) {
                return fighter.id === fighterToHide.id;
            });

            if (!found) {
                this.fighterSprites[fighter.location].show();
            } else {
                this.fighterSprites[fighter.location].hide();
            }
        }, this);
    };

    FlightIcon.prototype.setOverlayColorAlpha = function (alpha) {
        this.fighterSprites.forEach(function (sprite) {
            sprite.setOverlayColorAlpha(alpha);
        });
    };

    FlightIcon.prototype.setOpacity = function (opacity) {
        //TODO: set opacity
        console.log("TODO: set opacity for fighters");
    };

    FlightIcon.prototype.create = function (ship, scene) {
        var imagePath = ship.imagePath;
        this.mesh = new THREE.Object3D();
        this.mesh.position.set(500, 0, 0);
        this.mesh.renderDepth = 10;

        this.fighters.forEach(function (fighter) {
            var sprite = new window.webglSprite(imagePath, { width: FIGHTER_SPRITE_SIZE / 2, height: FIGHTER_SPRITE_SIZE / 2 }, 0);
            sprite.setOverlayColor(this.mine ? new THREE.Color(160 / 255, 250 / 255, 100 / 255) : new THREE.Color(255 / 255, 40 / 255, 40 / 255));
            positionFighter(fighter, sprite);
            this.mesh.add(sprite.mesh);
            this.fighterSprites.push(sprite);
            fighter.sprite = sprite;
        }, this);

        this.shipEWSprite = new window.ShipEWSprite({ width: this.size / 2, height: this.size / 2 }, -1);
        this.mesh.add(this.shipEWSprite.mesh);

        this.ShipSelectedSprite = new window.ShipSelectedSprite({ width: this.size / 2, height: this.size / 2 }, -2, this.mine ? 'ally' : 'enemy', true).hide();
        this.mesh.add(this.ShipSelectedSprite.mesh);

        this.ShipSideSprite = new window.ShipSelectedSprite({ width: this.size / 2, height: this.size / 2 }, -2, this.mine ? 'ally' : 'enemy', false).hide();
        this.mesh.add(this.ShipSideSprite.mesh);

        scene.add(this.mesh);
    };

    function positionFighter(fighter, sprite) {
        var position = shipManager.getFighterPosition(fighter.location, 0, 1);
        sprite.setPosition(position);
    }

    function consumeFighters(ship) {
        return ship.systems.map(function (fighter) {
            return {
                id: fighter.id,
                imagePath: fighter.imagePath,
                destroyed: fighter.destroyed,
                location: fighter.location
            };
        });
    }

    return FlightIcon;
}();