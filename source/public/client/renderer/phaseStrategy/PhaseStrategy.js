window.PhaseStrategy = (function(){

    function PhaseStrategy(coordinateConverter){
        this.inactive = true;
        this.gamedata = null;
        this.shipIconContainer = null;
        this.ewIconContainer = null;
        this.ballisticIconContainer = null;
        this.coordinateConverter = coordinateConverter;
        this.currentlyMouseOveredIds = null;

        this.onMouseOutCallbacks = [];
        this.onZoomCallbacks = [this.repositionTooltip.bind(this), this.positionMovementUI.bind(this)];
        this.onScrollCallbacks = [this.repositionTooltip.bind(this), this.positionMovementUI.bind(this)];
        this.onClickCallbacks = [];

        this.selectedShip = null;
        this.targetedShip = null;
        this.animationStrategy = null;
        this.replayUI = null;


        this.shipTooltip = null;
        this.movementUI = null;

        this.onDoneCallback = null;
    }

    PhaseStrategy.prototype.consumeGamedata = function() {
        this.shipIconContainer.consumeGamedata(this.gamedata);
        this.animationStrategy.update(this.gamedata);
        this.ewIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.ballisticIconContainer.consumeGamedata(this.gamedata, this.shipIconContainer);
        this.redrawMovementUI();
    };

    PhaseStrategy.prototype.render = function(coordinateConverter, scene, zoom){
        this.animationStrategy.render(coordinateConverter, scene, zoom);
    };

    PhaseStrategy.prototype.update = function (gamedata) {
        this.gamedata = gamedata;
        this.consumeGamedata();
        this.ewIconContainer.hide();
        this.ballisticIconContainer.show();
    };

    PhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, doneCallback) {
        this.shipIconContainer = shipIcons;
        this.ewIconContainer = ewIconContainer;
        this.ballisticIconContainer = ballisticIconContainer;
        this.gamedata = gamedata;
        this.inactive = false;
        this.consumeGamedata();
        this.shipIconContainer.setAllSelected(false);
        this.ballisticIconContainer.show();
        this.onDoneCallback = doneCallback;
        this.createReplayUI(gamedata);
        return this;
    };

    PhaseStrategy.prototype.deactivate = function () {
        this.inactive = true;
        this.animationStrategy.deactivate();
        this.replayUI && this.replayUI.deactivate();

        if (this.ballisticIconContainer) {
            this.ballisticIconContainer.hide();
        }

        if (this.ewIconContainer) {
            this.ewIconContainer.hide();
        }

        if (this.shipTooltip) {
            this.shipTooltip.destroy()
        }

        if (this.selectedShip) {
            this.deselectShip(this.selectedShip);
        }

        return this;
    };

    PhaseStrategy.prototype.onEvent = function(name, payload) {
        var target = this['on'+ name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    PhaseStrategy.prototype.onScrollEvent = function(payload) {
        this.onScrollCallbacks = this.onScrollCallbacks.filter(function(callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onZoomEvent = function(payload) {
        this.onZoomCallbacks = this.onZoomCallbacks.filter(function(callback) {
            return callback(payload);
        });
    };

    PhaseStrategy.prototype.onClickEvent = function(payload) {
        var icons = this.shipIconContainer.getIconsInProximity(payload);

        this.onClickCallbacks = this.onClickCallbacks.filter(function(callback) {
            callback();
            return false;
        });

        if (icons.length > 1){
            this.onShipsClicked(icons.map(function(icon) {return this.gamedata.getShip(icon.shipId);}, this));
        } else if (icons.length === 1) {
            if (payload.button !== 0) {
                this.onShipRightClicked(this.gamedata.getShip(icons[0].shipId), payload);
            } else {
                this.onShipClicked(this.gamedata.getShip(icons[0].shipId), payload);
            }
        }else{
            this.onHexClicked(payload);
        }
    };

    PhaseStrategy.prototype.onHexClicked = function(payload) {};

    PhaseStrategy.prototype.onShipsClicked = function(ships) {
        //TODO: implement clicking multiple ships
        console.log("CLICKING MULTIPLE SHIPS IS NOT YET IMPLEMENTED");
    };

    PhaseStrategy.prototype.onShipRightClicked = function(ship) {
        if (this.gamedata.isMyShip(ship)) {
            this.setSelectedShip(ship);
        }
        shipWindowManager.open(ship);
    };

    PhaseStrategy.prototype.onShipClicked = function(ship, payload) {
        if (this.gamedata.isMyShip(ship)){
            this.selectShip(ship, payload);
        } else {
            this.targetShip(ship, payload);
        }
    };

    PhaseStrategy.prototype.selectShip = function(ship) {
        this.setSelectedShip(ship);
    };

    PhaseStrategy.prototype.setSelectedShip = function(ship) {
        if (this.selectedShip) {
            this.deselectShip(this.selectedShip);
        }
        this.selectedShip = ship;
        this.shipIconContainer.getById(ship.id).setSelected(true);
    };

    PhaseStrategy.prototype.deselectShip = function(ship) {
        this.shipIconContainer.getById(ship.id).setSelected(false);

        gamedata.selectedSystems.slice(0).forEach(function (selected){
            weaponManager.unSelectWeapon(this.selectedShip, selected);
        }, this);

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

            this.onMouseOutCallbacks = this.onMouseOutCallbacks.filter(function(callback) {
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

        if (mouseOverIds === this.currentlyMouseOveredIds) {
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
            this.ewIconContainer.hide();
            //TODO: User settings, should this be hidden or not?
            icon.showSideSprite(false);
        }, this);
    };

    PhaseStrategy.prototype.onMouseOverShips = function(ships, payload) {
        this.showShipTooltip(ships, payload, null, true);
    };

    PhaseStrategy.prototype.onMouseOverShip = function(ship, payload) {
        var icon = this.shipIconContainer.getById(ship.id);
        this.showShipTooltip(ship, payload, null, true);
        this.showShipEW(ship);
        icon.showSideSprite(true);
    };

    PhaseStrategy.prototype.showShipEW = function(ship) {
        this.shipIconContainer.getByShip(ship).showEW();
        this.ewIconContainer.showForShip(ship);
    };

    PhaseStrategy.prototype.hideShipEW = function(ship) {
        this.shipIconContainer.getByShip(ship).hideEW();
        this.ewIconContainer.hide();
    };

    PhaseStrategy.prototype.showShipTooltip = function(ships, payload, menu, hide, ballisticsMenu) {

        if (this.shipTooltip) {
            return;
        }

        ships = [].concat(ships);

        var position = payload.hex;
        if (ships.length === 1) {
            position = this.shipIconContainer.getByShip(ships[0]).getPosition();
        }

        if (!ballisticsMenu) {
            ballisticsMenu = new ShipTooltipBallisticsMenu(this.shipIconContainer, this.gamedata.turn, false);
        }

        var shipTooltip = new window.ShipTooltip(this.selectedShip, ships, position, shipManager.systems.selectedShipHasSelectedWeapons(this.selectedShip), menu, payload.hex, ballisticsMenu);

        this.shipTooltip = shipTooltip;
        this.onClickCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));

        if (hide) {
            this.onMouseOutCallbacks.push(this.hideShipTooltip.bind(this, shipTooltip));
        }
    };

    PhaseStrategy.prototype.hideShipTooltip = function(shipTooltip){
        if (this.shipTooltip && this.shipTooltip === shipTooltip) {
            this.shipTooltip.destroy();
            this.shipTooltip = null;
        }
    };

    PhaseStrategy.prototype.repositionTooltip = function(){
        if (this.shipTooltip) {
            this.shipTooltip.reposition();
        }

        return true;
    };

    PhaseStrategy.prototype.positionMovementUI = function(){
        if (!this.movementUI) {
            return true;
        }

        var pos = this.coordinateConverter.fromGameToViewPort(this.movementUI.icon.getPosition());
        var heading = mathlib.hexFacingToAngle(this.movementUI.icon.getLastMovement().heading);

        UI.shipMovement.reposition(pos, heading);

        return true;
    };

    PhaseStrategy.prototype.redrawMovementUI = function(){
        if (!this.movementUI) {
            return;
        }

        if (this.movementUI.ship.movement.some(function (movement) {return !movement.commit})) {
            this.hideMovementUI();
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
        //TODO: Scroll to ship?
        //TODO: what about active ship?
        if (ship) {
            this.setSelectedShip(ship);
        }
    };

    PhaseStrategy.prototype.selectActiveShip = function() {
        var ship = gamedata.getActiveShip();

        if (ship && gamedata.isMyShip(ship)) {
            this.setSelectedShip(ship);
        }
    };

    PhaseStrategy.prototype.done = function() {
        if (this.onDoneCallback) {
            this.onDoneCallback();
        }
    };

    PhaseStrategy.prototype.onWeaponMouseOver = function(payload) {
        var ship = payload.ship;
        var weapon = payload.weapon;
        var element = payload.element;
        systemInfo.showSystemInfo(element, weapon, ship, this.selectedShip);

        this.shipIconContainer.getArray().forEach(function (icon){ icon.hideWeaponArcs();});
        var icon = this.shipIconContainer.getByShip(ship);
        icon.showWeaponArc(ship, weapon);
    };

    PhaseStrategy.prototype.onWeaponMouseOut = function() {
        this.shipIconContainer.getArray().forEach(function (icon){ icon.hideWeaponArcs();});
    };

    PhaseStrategy.prototype.createReplayUI = function(gamedata) {
        this.replayUI = new ReplayUI().activate();
    };

    PhaseStrategy.prototype.changeAnimationStrategy = function(newAnimationStartegy) {
        this.animationStrategy && this.animationStrategy.deactivate();
        this.animationStrategy = newAnimationStartegy;
        this.animationStrategy.activate();
    };

    function getInterestingStuffInPosition(payload){
       return this.shipIconContainer.getIconsInProximity(payload);
    }

    PhaseStrategy.prototype.setPhaseHeader = function(name, shipName) {

        if (name === false) {
            jQuery("#phaseheader").hide();
            return;
        }

        if (!shipName) {
            shipName = "";
        }

        $("#phaseheader .turn.value").html("TURN: " + this.gamedata.turn+ ",");
        $("#phaseheader .phase.value").html(name);
        $("#phaseheader .activeship.value").html(shipName);
        $("#phaseheader").show();
    };

    return PhaseStrategy;
})();
