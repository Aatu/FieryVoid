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

    MovementPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager) {
        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        
        doForcedMovementForActiveShip();
        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene, shipWindowManager);
        this.selectActiveShip();

        this.setPhaseHeader("MOVEMENT ORDERS", this.selectedShip.name);

        this.highlightUnmovedShips();

        if (isMovementReady(gamedata)) {
            gamedata.showCommitButton();
        } else {
            gamedata.hideCommitButton();
        }

        return this;
    };

    MovementPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        this.hideMovementUI();
        this.uiManager.hideShipThrustUI();

        gamedata.ships.forEach(function(ship) {
            var icon = this.shipIconContainer.getByShip(ship);
            icon.showSideSprite(false);
        }, this);

        return this;
    };

    MovementPhaseStrategy.prototype.onShipRightClicked = function (ship) {
        this.shipWindowManager.open(ship);
    };

    MovementPhaseStrategy.prototype.onHexClicked = function (payload) {};

    MovementPhaseStrategy.prototype.selectShip = function (ship, payload) {
        if (gamedata.getMyActiveShips().includes(ship)) {
            this.setSelectedShip(ship);
        }

        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.setSelectedShip = function (ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        this.drawMovementUI(this.selectedShip);
    };

    MovementPhaseStrategy.prototype.targetShip = function (ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.showShipTooltip = function (ships, payload, menu, hide, ballisticsMenu) {
        ships = [].concat(ships);

        if (this.selectedShip && this.shipThrustUIState) {
            return;
        } 

        PhaseStrategy.prototype.showShipTooltip.call(this, ships, payload, menu, hide, ballisticsMenu);
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
        return gamedata.getMyActiveShips().every(function(ship) {
            console.log(ship.name, shipManager.movement.isMovementReady(ship));
            return shipManager.movement.isMovementReady(ship);
        });
    }


    function doForcedMovementForActiveShip() {
        gamedata.getMyActiveShips().forEach(function (ship) {
            shipManager.movement.doForcedPivot(ship);

            if (ship.base) {
                shipManager.movement.doRotate(ship);
    
                //TODO: Test if this autocommit thing works
                gamedata.autoCommitOnMovement(ship);
            }
        });
    }

    MovementPhaseStrategy.prototype.onShipMovementChanged = function (payload) {
        PhaseStrategy.prototype.onShipMovementChanged.call(this, payload);
        if (isMovementReady(this.gamedata)) {
            this.gamedata.showCommitButton();
        } else {
            this.gamedata.hideCommitButton();
        }

        this.onClickCallbacks = this.onClickCallbacks.filter(function (callback) {
            return callback();
        });
    };

    MovementPhaseStrategy.prototype.showAppropriateEW = function() {
    
    }

    MovementPhaseStrategy.prototype.selectActiveShip = function () {

        var ship = gamedata.getMyActiveShips().filter(function(ship) {
            return !shipManager.movement.isMovementReady(ship) && !shipManager.isDestroyed(ship);
        }).pop();

        if (ship) {
            this.setSelectedShip(ship);
        } else {
            this.setSelectedShip(gamedata.getMyActiveShips().pop())
        }
    };

    MovementPhaseStrategy.prototype.onMouseOutShips = function (ships, payload) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships, payload);
        this.highlightUnmovedShips();
    };

    MovementPhaseStrategy.prototype.highlightUnmovedShips = function () {

        gamedata.ships.forEach(function(ship) {
            var icon = this.shipIconContainer.getByShip(ship);
            icon.showSideSprite(false);
        }, this);

        gamedata.ships
            .filter(window.SimultaneousMovementRule.isActiveMovementShip)
            .filter(function(ship) {
                return !shipManager.movement.isMovementReady(ship) || !gamedata.isMyShip(ship);
            })
            .forEach(function (ship) {
                var icon = this.shipIconContainer.getByShip(ship);
                icon.showSideSprite(true);
            }, this);
    }

    return MovementPhaseStrategy;
}();