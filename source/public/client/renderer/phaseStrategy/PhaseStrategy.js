window.PhaseStrategy = (function(){

    function PhaseStrategy(coordinateConverter){
        this.inactive = true;
        this.gamedata = null;
        this.shipIconContainer = null;
        this.coordinateConverter = coordinateConverter;
        this.currentlyMouseOveredIds = null;

        this.onMouseOutCallbacks = [];
        this.onZoomCallbacks = [];
        this.onScrollCallbacks = [];

        this.selectedShip = null;
        this.targetedShip = null;
        this.activeShip = null;
    }

    PhaseStrategy.prototype.consumeGamedata = function() {
        this.shipIconContainer.consumeGamedata(this.gamedata);
    };

    PhaseStrategy.prototype.activate = function (shipIcons, gamedata) {
        this.shipIconContainer = shipIcons;
        this.gamedata = gamedata;
        this.inactive = false;
        this.consumeGamedata();
        return this;
    };

    PhaseStrategy.prototype.deactivate = function () {
        this.inactive = true;
        return this;
    };

    PhaseStrategy.prototype.onEvent = function(name, payload) {
        var target = this['on'+ name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    PhaseStrategy.prototype.onScrollEvent = function(payload) {
        this.onScrollCallbacks.filter(function(callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onZoomEvent = function(payload) {
        this.onZoomCallbacks.filter(function(callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onClickEvent = function(payload) {
        var icons = this.shipIconContainer.getIconsInProximity(payload);

        if (icons.length > 1){
            this.onShipsClicked(icons.map(function(icon) {return this.gamedata.getShip(icon.shipId);}, this));
        } else if (icons.length === 1) {
            if (payload.button != 0) {
                this.onShipRightClicked(this.gamedata.getShip(icons[0].shipId));
            } else {
                this.onShipClicked(this.gamedata.getShip(icons[0].shipId));
            }
        }else{
            this.onHexClicked(payload);
        }
    };

    PhaseStrategy.prototype.onHexClicked = function(payload) {};

    PhaseStrategy.prototype.onShipsClicked = function(ships) {
        console.log("CLICKING MULTIPLE SHIPS IS NOT YET IMPLEMENTED");
    };

    PhaseStrategy.prototype.onShipRightClicked = function(ship) {
        shipWindowManager.open(ship);
    };

    PhaseStrategy.prototype.onShipClicked = function(ship) {
        if (this.gamedata.isMyShip(ship)){
            this.selectShip(ship);
        } else {
            this.targetShip(ship);
        }
    };

    PhaseStrategy.prototype.selectShip = function(ship) {
        if (this.selectedShip) {
            this.deselectShip(this.selectedShip);
        }
        this.selectedShip = ship;
        this.shipIconContainer.getById(ship.id).setSelected(true);
    };

    PhaseStrategy.prototype.deselectShip = function(ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);
        this.selectedShip = null;
    };

    PhaseStrategy.prototype.targetShip = function(ship) {
        if (this.targetedShip) {
            this.untargetShip(this.targetedShip);
        }
        this.targetedShip = ship;
        this.shipIconContainer.getById(ship.id).setSelected(true);
    };

    PhaseStrategy.prototype.untargetShip = function(ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);
        this.targetedShip = null;
    };

    PhaseStrategy.prototype.onMouseMoveEvent = function(payload) {
        var icons = getInterestingStuffInPosition.call(this, payload);

        function doMouseOut(){
            if (this.currentlyMouseOveredIds) {
                this.currentlyMouseOveredIds = null;
            }

            this.onMouseOutCallbacks.filter(function(callback) {
                callback();
                return false;
            });

            this.onMouseOutShips(gamedata.ships, payload);
        }

        if (icons.length === 0) {
            doMouseOut.call(this);
            return;
        }

        var mouseOverIds = icons.reduce(function(value, icon){
            return value + icon.shipId;
        }, '');

        if (mouseOverIds == this.currentlyMouseOveredIds) {
            return;
        }

        doMouseOut.call(this);

        this.currentlyMouseOveredIds = mouseOverIds;

        var ships = icons.map(function(icon) {return this.gamedata.getShip(icon.shipId);}, this);
        if (ships.length > 1) {
            this.onMouseOverShips(ships, payload);
        } else {
            this.onMouseOverShip(ships[0], payload);
        }
    };

    PhaseStrategy.prototype.onMouseOutShips = function(ships, payload) {
        ships = [].concat(ships);
        ships.forEach(function(ship) {
            var icon = this.shipIconContainer.getById(ship.id);
            icon.hideEW();
            //TODO: User settings, should this be hidden or not?
            icon.showSideSprite(false);
        }, this);
    };

    PhaseStrategy.prototype.onMouseOverShips = function(ships, payload) {
        this.showShipTooltip(ships, payload);
    };

    PhaseStrategy.prototype.onMouseOverShip = function(ship, payload) {
        var icon = this.shipIconContainer.getById(ship.id);
        this.showShipTooltip(ship, payload);
        this.showShipEW(ship);
        icon.showSideSprite(true);
    };

    PhaseStrategy.prototype.showShipEW = function(ship) {
        var dew = window.ew.getDefensiveEW(ship);
        if (ship.flight)
            dew = shipManager.movement.getJinking(ship);

        var ccew = window.ew.getCCEW(ship);
        this.shipIconContainer.getById(ship.id).displayEW(dew, ccew);
    };

    PhaseStrategy.prototype.showShipTooltip = function(ships, payload) {
        ships = [].concat(ships);
        var shipTooltip = new window.ShipTooltip(ships, payload.hex);
        this.onMouseOutCallbacks.push(function(){ shipTooltip.destroy(); });
        this.onZoomCallbacks.push(function(){ shipTooltip.reposition(); });
        this.onScrollCallbacks.push(function(){shipTooltip.reposition();});
    };

    PhaseStrategy.prototype.onMouseOverShips = function(ship, hex) {
        icon.displayEW(8, 2);
        var ships = shipIcons.map(function(icon){
            return gamedata.getShip(icon.shipId);
        }, this);

        var shipTooltip = new window.ShipTooltip(ships, hex);
        this.onMouseOutCallbacks.push(function(){ shipTooltip.destroy(); });
        this.onZoomCallbacks.push(function(){ shipTooltip.reposition(); });
        this.onScrollCallbacks.push(function(){shipTooltip.reposition();});

    };



    function getInterestingStuffInPosition(payload){
       return this.shipIconContainer.getIconsInProximity(payload);
    }


    return PhaseStrategy;
})();
