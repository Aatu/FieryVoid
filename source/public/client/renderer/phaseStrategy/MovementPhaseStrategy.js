window.MovementPhaseStrategy = (function(){

    function MovementPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.MovementAnimationStrategy();
    }

    MovementPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    MovementPhaseStrategy.prototype.update = function (gamedata) {
        PhaseStrategy.prototype.update.call(this, gamedata);
        this.selectActiveShip();
        //doForcedMovementForActiveShip();
    };

    MovementPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {
        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene);
        console.log("enabled movement phase strategy");
        this.selectActiveShip();
        doForcedMovementForActiveShip();
        return this;
    };

    MovementPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        botPanel.deactivate();
        this.hideMovementUI();
        return this;
    };

    MovementPhaseStrategy.prototype.onHexClicked = function(payload) {

    };

    MovementPhaseStrategy.prototype.selectShip = function(ship) {

        var selectedShip = gamedata.getActiveShip();
        if (ship !== selectedShip) {
            return;
        }

        PhaseStrategy.prototype.selectShip.call(this, ship);
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

    MovementPhaseStrategy.prototype.targetShip = function(ship) {
    };

    function doForcedMovementForActiveShip(){
        var ship = gamedata.getActiveShip();

        if (!ship || !gamedata.isMyShip(ship)) {
            return
        }
        shipManager.movement.doForcedPivot(ship);

        if (ship.base){
            shipManager.movement.doRotate(ship);

            //TODO: Test if this autocommit thing works
            gamedata.autoCommitOnMovement(ship);
        }
    }

    return MovementPhaseStrategy;
})();