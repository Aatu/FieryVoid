window.animationDirector = (function() {

    function animationDirector(graphics) {
        this.graphics = graphics;
        this.shipIconContainer = new ShipIconContainer();
        this.ballisticIcons = [];
        this.timeline = [];

        this.animationStrategy = null;
        this.phaseStrategy = null;
        this.coordinateConverter = null;
    }

    animationDirector.prototype.init = function (coordinateConverter) {
        this.coordinateConverter = coordinateConverter;
        this.shipIconContainer = new ShipIconContainer(this.coordinateConverter);
    };

    animationDirector.prototype.receiveGamedata = function (gamedata, scene) {
        this.receiveShips(gamedata.ships, scene);
        resolvePhaseStrategy.call(this, gamedata, scene);
    };

    animationDirector.prototype.receiveShips = function (ships, scene) {
        this.shipIconContainer.setShips(ships, scene);
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

        this.phaseStrategy.animationStrategy.render(coordinateConverter, scene);
    };

    function resolvePhaseStrategy(gamedata, scene) {
        if (gamedata.waiting) {
            return activatePhaseStrategy.call(this, window.WaitingPhaseStrategy, gamedata, scene);
        }

        if (gamedata.gamephase === -1) {
            return activatePhaseStrategy.call(this, window.DeploymentPhaseStrategy, gamedata, scene);
        }

        return activatePhaseStrategy.call(this, window.WaitingPhaseStrategy, gamedata, scene);
    }

    function activatePhaseStrategy(phaseStrategy, scene) {
        if (this.phaseStrategy) {
            this.phaseStrategy.deactivate();
        }

        this.phaseStrategy = new phaseStrategy(this.coordinateConverter).activate(this.shipIconContainer, gamedata, scene);
    }


    return animationDirector;
})();