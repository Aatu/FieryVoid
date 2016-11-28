window.DeploymentPhaseStrategy = (function(){

    function DeploymentPhaseStrategy(coordinateConverter){
        this.animationStrategy = new window.IdleAnimationStrategy();
        PhaseStrategy.call(this, coordinateConverter);
    }

    DeploymentPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    DeploymentPhaseStrategy.prototype.activate = function (shipIcons, gamedata, scene) {
        this.animationStrategy.activate(shipIcons, gamedata.turn, scene);
        this.gamedata = gamedata;





        return PhaseStrategy.prototype.activate.call(this, shipIcons, gamedata);
    };

    return DeploymentPhaseStrategy;
})();