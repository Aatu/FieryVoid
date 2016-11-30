window.ShipIconContainer = (function(){
    function ShipIconContainer(coordinateConverter, scene){
        this.iconsAsObject = {};
        this.iconsAsArray = [];
        this.coordinateConverter = coordinateConverter;
        this.scene = scene;
    }

    ShipIconContainer.prototype.consumeGamedata = function(gamedata) {
        setShips.call(this, gamedata.ships);
    };

    function setShips (ships) {
        ships.forEach(function (ship) {
            if (! this.hasIcon(ship.id)) {
                this.iconsAsObject[ship.id] = createIcon(ship, this.scene);
            } else {
                this.iconsAsObject[ship.id].consumeShipdata(ship);
            }
        }, this);

        buildShipArray.call(this);
    }

    ShipIconContainer.prototype.getById = function(id) {
        return this.iconsAsObject[id];
    };

    ShipIconContainer.prototype.onEvent = function(name, payload) {
        var target = this['on'+ name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    ShipIconContainer.prototype.onZoomEvent = function (payload) {
        var zoom = payload.zoom;
        if (zoom <= 0.5) {
            var newzoom = 2 * zoom;
            this.iconsAsArray.forEach(function (icon) {
                icon.setScale(newzoom, newzoom);
            })
        }

        var alpha = zoom > 6 ? zoom - 6 : 0;
        if (alpha > 1) {
            alpha = 1;
        }
        this.iconsAsArray.forEach(function (icon) {
            icon.setOverlayColorAlpha(alpha);
        })

    };

    ShipIconContainer.prototype.getArray = function () {
        return this.iconsAsArray;
    };

    ShipIconContainer.prototype.hasIcon = function (shipId) {
        return Boolean(this.iconsAsObject[shipId]);
    };

    ShipIconContainer.prototype.getIconById = function (shipId) {
        return this.iconsAsObject[shipId];
    };

    ShipIconContainer.prototype.getIconsInProximity = function (payload) {
        /* TODO: sort this out when we have two ships in same hex
        var hexHeight = this.coordinateConverter.getHexHeightViewport();
        var distance = hexHeight/10;

        console.log(distance);

        if (distance < 10) {
        */
            return this.iconsAsArray.filter(function(shipIcon){
                return this.coordinateConverter.fromGameToHex(shipIcon.getPosition()).equals(payload.hex);
            }, this);
        /*
        } else {

            return this.iconsAsArray.filter(function(shipIcon){
                return mathlib.distance(this.coordinateConverter.fromGameToViewPort(shipIcon.getPosition()), payload.view) <= distance;
            }, this);
        }
        */

    };

    function buildShipArray(){
        this.iconsAsArray = Object.keys(this.iconsAsObject).map(function(key){
           return this.iconsAsObject[key];
        }, this);
    }

    function createIcon(ship, scene) {
        if (ship.flight) {
            //TODO: not the best place to create SCS
            return new window.FlightIcon(ship, scene);
        } else {
            return new window.ShipIcon(ship, scene);
        }
    }

    return ShipIconContainer;
})();