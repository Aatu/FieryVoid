"use strict";

window.MovementPhaseStrategy = function () {

    function MovementPhaseStrategy(coordinateConverter) {
        PhaseStrategy.call(this, coordinateConverter);

        this.onZoomCallbacks.push(this.repositionThrustUi.bind(this));
        this.onScrollCallbacks.push(this.repositionThrustUi.bind(this));
        this.shipThrustUIState = null;
    }

    MovementPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    MovementPhaseStrategy.prototype.update = function (gamedata) {
        
        doForcedMovementForActiveShip();
        PhaseStrategy.prototype.update.call(this, gamedata);
        this.selectActiveShip();


        if (isMovementReady(gamedata)) {
            gamedata.showCommitButton();
        } else {
            gamedata.hideCommitButton();
        }
    };

    MovementPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {
        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        
        doForcedMovementForActiveShip();
        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene);
        this.selectActiveShip();

        this.setPhaseHeader("MOVEMENT ORDERS", this.selectedShip.name);
        return this;
    };

    MovementPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        this.hideMovementUI();
        this.uiManager.hideShipThrustUI();
        return this;
    };

    MovementPhaseStrategy.prototype.onShipRightClicked = function (ship) {
        shipWindowManager.open(ship);
    };

    MovementPhaseStrategy.prototype.onHexClicked = function (payload) {};

    MovementPhaseStrategy.prototype.selectShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        this.drawMovementUI(this.selectedShip);
    };

    MovementPhaseStrategy.prototype.deselectShip = function (ship) {
        return; //do not allow deselecting ship when moving
    };

    MovementPhaseStrategy.prototype.onMouseOutShips = function (ships) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships);

        if (this.selectedShip) {
            //this.showShipEW(this.selectedShip);
        }
    };

    MovementPhaseStrategy.prototype.targetShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.onAssignThrust = function(payload) {
        if (payload === false) {
            this.uiManager.hideShipThrustUI();
            this.shipThrustUIState = null;
            return;
        }

        var ship = payload.ship;
        var icon = this.shipIconContainer.getByShip(ship);

        this.shipThrustUIState = {
            ship: ship,
            position: window.coordinateConverter.fromGameToViewPort(icon.getPosition()),
            rotation: icon.getFacing(),
            totalRequired: payload.totalRequired,
            remainginRequired: payload.remainginRequired,
            movement: payload.movement
        };

        this.uiManager.showShipThrustUI(this.shipThrustUIState);
    }

    MovementPhaseStrategy.prototype.repositionThrustUi = function() {
        if (this.shipThrustUIState === null) {
            return true;
        }

        var icon = this.shipIconContainer.getByShip(this.shipThrustUIState.ship);
        var position = window.coordinateConverter.fromGameToViewPort(icon.getPosition());
        jQuery("#thrustUIContainer").css({left: position.x + 'px', top: position.y + 'px'})

        return true;
    }

    function isMovementReady(gamedata) {
        var ship = gamedata.getActiveShip();
        return shipManager.movement.isMovementReady(ship) && gamedata.isMyShip(ship);
    }

    function doForcedMovementForActiveShip() {
        var ship = gamedata.getActiveShip();

        if (!ship || !gamedata.isMyShip(ship)) {
            return;
        }
        shipManager.movement.doForcedPivot(ship);

        if (ship.base) {
            shipManager.movement.doRotate(ship);

            //TODO: Test if this autocommit thing works
            gamedata.autoCommitOnMovement(ship);
        }
    }

    MovementPhaseStrategy.prototype.onShipMovementChanged = function (payload) {
        PhaseStrategy.prototype.onShipMovementChanged.call(this, payload);
        if (isMovementReady(this.gamedata)) {
            this.gamedata.showCommitButton();
        } else {
            this.gamedata.hideCommitButton();
        }
    };

    MovementPhaseStrategy.prototype.showAppropriateEW = function() {
    
    }

    return MovementPhaseStrategy;
}();