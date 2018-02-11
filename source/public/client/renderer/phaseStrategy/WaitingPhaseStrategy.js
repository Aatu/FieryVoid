window.WaitingPhaseStrategy = (function(){

    function WaitingPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);

    }

    WaitingPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    WaitingPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {

        this.changeAnimationStrategy(new window.IdleAnimationStrategy(shipIcons, gamedata.turn));

        PhaseStrategy.prototype.activate.call(this, shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene);
        console.log("enabled waiting phase strategy");
        gamedata.hideCommitButton();

        ajaxInterface.startPollingGamedata();

        this.setPhaseHeader("WAITING FOR TURN...");
        return this;
    };

    WaitingPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this);
        ajaxInterface.stopPolling();
        return this;
    };

    WaitingPhaseStrategy.prototype.onHexClicked = function(payload) {};

    WaitingPhaseStrategy.prototype.selectShip = function(ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };

    WaitingPhaseStrategy.prototype.targetShip = function(ship, payload) {
        var menu = new ShipTooltipMenu(this.selectedShip, ship, this.gamedata.turn);
        this.showShipTooltip(ship, payload, menu, false);
    };


    return WaitingPhaseStrategy;
})();