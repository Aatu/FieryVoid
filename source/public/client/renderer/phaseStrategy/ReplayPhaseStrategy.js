window.ReplayPhaseStrategy = (function(){

    function ReplayPhaseStrategy(coordinateConverter){
        PhaseStrategy.call(this, coordinateConverter);
        this.animationStrategy = new window.IdleAnimationStrategy();
        this.webglScene = null;
    }

    ReplayPhaseStrategy.prototype = Object.create(window.PhaseStrategy.prototype);

    ReplayPhaseStrategy.prototype.activate = function (shipIcons, ewIconContainer, ballisticIconContainer, gamedata, webglScene) {
        this.shipIconContainer = shipIcons;
        this.ewIconContainer = ewIconContainer;
        this.ballisticIconContainer = ballisticIconContainer;
        this.gamedata = gamedata;
        this.webglScene = webglScene;
        this.inactive = false;
        this.animationStrategy = new ReplayAnimationStrategy(null, gamedata, this.shipIconContainer, webglScene.scene);
        this.animationStrategy.activate();
        this.consumeGamedata();
        console.log("enabled replay phase strategy");
        return this;
    };

    ReplayPhaseStrategy.prototype.deactivate = function () {
        PhaseStrategy.prototype.deactivate.call(this, true);
        return this;
    };

    ReplayPhaseStrategy.prototype.onReplayForward = function(payload) {
        console.log("onReplayForward");
        this.animationStrategy.start();
    };

    ReplayPhaseStrategy.prototype.done = function () {};

    ReplayPhaseStrategy.prototype.onHexClicked = function(payload) {};

    ReplayPhaseStrategy.prototype.selectShip = function(ship) {};

    ReplayPhaseStrategy.prototype.deselectShip = function(ship) {};

    ReplayPhaseStrategy.prototype.onMouseOutShips = function(ships) {};

    ReplayPhaseStrategy.prototype.targetShip = function(ship) {};

    return ReplayPhaseStrategy;
})();