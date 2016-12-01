window.animationDirector = (function() {

    function animationDirector(graphics) {
        this.graphics = graphics;
        this.shipIconContainer = null;
        this.ballisticIcons = [];
        this.timeline = [];

        this.animationStrategy = null;
        this.phaseStrategy = null;
        this.coordinateConverter = null;
    }

    animationDirector.prototype.init = function (coordinateConverter, scene) {
        this.coordinateConverter = coordinateConverter;
        this.shipIconContainer = new ShipIconContainer(this.coordinateConverter, scene);
    };

    animationDirector.prototype.receiveGamedata = function (gamedata, webglScene) {
        resolvePhaseStrategy.call(this, gamedata, webglScene);
    };

    animationDirector.prototype.relayEvent = function (name, payload) {
        if (!this.phaseStrategy || this.phaseStrategy.inactive) {
            return;
        }

        this.phaseStrategy.onEvent(name, payload);
        this.shipIconContainer.onEvent(name, payload);
    };

    animationDirector.prototype.render = function (scene, coordinateConverter) {
        if (!this.phaseStrategy || this.phaseStrategy.inactive) {
            return;
        }

        this.phaseStrategy.render(coordinateConverter, scene);
    };

    function resolvePhaseStrategy(gamedata, scene) {
        if (gamedata.waiting) {
            return activatePhaseStrategy.call(this, window.WaitingPhaseStrategy, gamedata, scene);
        }

        if (gamedata.gamephase === -1) {
            return activatePhaseStrategy.call(this, window.DeploymentPhaseStrategy, gamedata, scene);
        }

        if (gamedata.gamephase === 1) {
            return activatePhaseStrategy.call(this, window.InitialPhaseStrategy, gamedata, scene);
        }

        return activatePhaseStrategy.call(this, window.WaitingPhaseStrategy, gamedata, scene);
    }

    function activatePhaseStrategy(phaseStrategy, gamedata, scene) {
        if (this.phaseStrategy && this.phaseStrategy instanceof phaseStrategy) {
            this.phaseStrategy.update(gamedata);
            return;
        }

        if (this.phaseStrategy) {
            this.phaseStrategy.deactivate();
        }

        this.phaseStrategy = new phaseStrategy(this.coordinateConverter).activate(this.shipIconContainer, gamedata, scene);
    }


    return animationDirector;
})();