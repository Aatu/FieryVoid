window.WaitingPhaseStrategy = (function(){

    function WaitingPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();
    }

    WaitingPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    WaitingPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {
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