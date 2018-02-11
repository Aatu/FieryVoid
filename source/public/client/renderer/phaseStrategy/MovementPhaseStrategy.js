window.MovementPhaseStrategy = (function(){

    function MovementPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
    }

    MovementPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    MovementPhaseStrategy.prototype.update = function (gamedata) {
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

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene);
        this.selectActiveShip();
        doForcedMovementForActiveShip();

        this.setPhaseHeader("MOVEMENT ORDERS", this.selectedShip.name);
        return this;
    };

    MovementPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        botPanel.deactivate();
        this.hideMovementUI();
        return this;
    };

    MovementPhaseStrategy.prototype.onShipRightClicked = function(ship) {
        shipWindowManager.open(ship);
    };

    MovementPhaseStrategy.prototype.onHexClicked = function(payload) {

    };

    MovementPhaseStrategy.prototype.selectShip = function(ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    MovementPhaseStrategy.prototype.setSelectedShip = function(ship) {
        PhaseStrategy.prototype.setSelectedShip.call(this, ship);
        botPanel.setMovement(ship);
        this.drawMovementUI(this.selectedShip);
    };

    MovementPhaseStrategy.prototype.deselectShip = function(ship) {
        return; //do not allow deselecting ship when moving

        /*
        PhaseStrategy.prototype.deselectShip.call(this, ship);
        botPanel.onShipStatusChanged(ship);
        this.hideMovementUI();
        */
    };


    MovementPhaseStrategy.prototype.onMouseOutShips = function(ships) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships);

        if (this.selectedShip) {
            //this.showShipEW(this.selectedShip);
        }
    };

    MovementPhaseStrategy.prototype.targetShip = function(ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    function isMovementReady(gamedata) {
        var ship = gamedata.getActiveShip();
        return shipManager.movement.isMovementReady(ship) && gamedata.isMyShip(ship);
    }

    function doForcedMovementForActiveShip(){
        var ship = gamedata.getActiveShip();

        if (!ship || !gamedata.isMyShip(ship)) {
            return
        }
        //TODO: what about rolling?
        //TODO: maybe check that this is not yet done during this turn
        shipManager.movement.doForcedPivot(ship);

        if (ship.base){
            shipManager.movement.doRotate(ship);

            //TODO: Test if this autocommit thing works
            gamedata.autoCommitOnMovement(ship);
        }
    }

    return MovementPhaseStrategy;
})();