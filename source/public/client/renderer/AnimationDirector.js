window.animationDirector = (function() {

    function animationDirector(graphics) {
        this.graphics = graphics;
        this.shipIcons = {};
        this.ballisticIcons = [];
        this.timeline = [];

        this.animationStrategy = null;
    }

    animationDirector.prototype.receiveShips = function (ships, scene) {
        console.log(ships);

        ships.forEach(function (ship) {
            if (! this.shipIcons[ship.id]) {
                var icon = new window.webglShipIcon(ship, scene);
                this.shipIcons[ship.id] = icon;
            }
        }, this);

        this.animationStrategy = new window.IdleAnimationStrategy().activate(this.shipIcons, 0);
    };

    animationDirector.prototype.render = function (scene, coordinateConverter, zoom) {
        this.animationStrategy.render(coordinateConverter);
    };

    return animationDirector;
})();