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
        this.animationStrategy = null;


        this.movementUI = null;
    }

    PhaseStrategy.prototype.consumeGamedata = function() {
        this.shipIconContainer.consumeGamedata(this.gamedata);
        this.redrawMovementUI();
    };

    PhaseStrategy.prototype.render = function(coordinateConverter, scene){
        this.animationStrategy.render(coordinateConverter, scene);
        this.positionMovementUI();
    };

    PhaseStrategy.prototype.update = function (gamedata) {
        this.gamedata = gamedata;
        this.consumeGamedata();
    };

    PhaseStrategy.prototype.activate = function (shipIcons, gamedata, webglScene) {
        this.shipIconContainer = shipIcons;
        this.gamedata = gamedata;
        this.inactive = false;
        this.consumeGamedata();
        this.animationStrategy.activate(shipIcons, gamedata.turn, webglScene.scene);
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
        this.shipIconContainer.getByShip(ship).showEW();
    };

    PhaseStrategy.prototype.hideShipEW = function(ship) {
        this.shipIconContainer.getByShip(ship).hideEW();
    };

    PhaseStrategy.prototype.showShipTooltip = function(ships, payload) {
        ships = [].concat(ships);
        var shipTooltip = new window.ShipTooltip(ships, payload.hex);
        this.onMouseOutCallbacks.push(function(){ shipTooltip.destroy(); });
        this.onZoomCallbacks.push(function(){ shipTooltip.reposition(); });
        this.onScrollCallbacks.push(function(){shipTooltip.reposition();});
    };

    PhaseStrategy.prototype.positionMovementUI = function(){
        if (!this.movementUI) {
            return;
        }

        var pos = this.coordinateConverter.fromGameToViewPort(this.movementUI.icon.getPosition());
        this.movementUI.element.css("top", pos.y +"px").css("left", pos.x +"px");
    };

    PhaseStrategy.prototype.redrawMovementUI = function(){
        if (!this.movementUI) {
            return;
        }

        this.drawMovementUI(this.movementUI.ship);
    };

    PhaseStrategy.prototype.drawMovementUI = function(ship) {
        UI.shipMovement.drawShipMovementUI(ship, new ShipMovementCallbacks(ship, this.consumeGamedata.bind(this)));
        this.movementUI = {
            element: UI.shipMovement.uiElement,
            ship: ship,
            icon: this.shipIconContainer.getByShip(ship),
            position: null
        };

        this.positionMovementUI();
    };

    PhaseStrategy.prototype.hideMovementUI = function() {
        UI.shipMovement.hide();
        this.movementUI = null;
    };

    PhaseStrategy.prototype.selectFirstOwnShipOrActiveShip = function() {
        var ship = gamedata.getFirstFriendlyShip();

        if (ship) {
            this.selectShip(ship);
        }
    };

    function getInterestingStuffInPosition(payload){
       return this.shipIconContainer.getIconsInProximity(payload);
    }


    return PhaseStrategy;
})();
