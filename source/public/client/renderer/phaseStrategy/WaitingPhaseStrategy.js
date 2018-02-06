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
        return this;
    };

    WaitingPhaseStrategy.prototype.onHexClicked = function(payload) {};

    WaitingPhaseStrategy.prototype.selectShip = function(ship) {};

    WaitingPhaseStrategy.prototype.targetShip = function(ship) {};

    return WaitingPhaseStrategy;
})();