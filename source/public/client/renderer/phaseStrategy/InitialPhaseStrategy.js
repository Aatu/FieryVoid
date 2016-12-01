window.InitialPhaseStrategy = (function(){

    function InitialPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();
    }

    InitialPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    InitialPhaseStrategy.prototype.activate = function (shipIcons, gamedata, webglScene) {
        PhaseStrategy.prototype.activate.call(this, shipIcons, gamedata, webglScene);
        console.log("enabled initial phase strategy");
        this.selectFirstOwnShipOrActiveShip();
        gamedata.showCommitButton();
        return this;
    };

    InitialPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        botPanel.deactivate();
        return this;
    };

    InitialPhaseStrategy.prototype.onHexClicked = function(payload) {};

    InitialPhaseStrategy.prototype.selectShip = function(ship) {
        PhaseStrategy.prototype.selectShip.call(this, ship);

        botPanel.setEW(ship);
        this.showShipEW(this.selectedShip);
    };

    InitialPhaseStrategy.prototype.deselectShip = function(ship) {
        PhaseStrategy.prototype.deselectShip.call(this, ship);
        botPanel.onShipStatusChanged(ship);
        this.hideShipEW(ship);
    };


    InitialPhaseStrategy.prototype.onMouseOutShips = function(ships) {
        PhaseStrategy.prototype.onMouseOutShips.call(this, ships);

        if (this.selectedShip) {
            this.showShipEW(this.selectedShip);
        }
    };

    InitialPhaseStrategy.prototype.targetShip = function(ship) {

    };

    return InitialPhaseStrategy;
})();