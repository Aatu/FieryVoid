window.phaseDirector = (function() {

    function phaseDirector(graphics) {
        this.graphics = graphics;
        this.shipIconContainer = null;
        this.ewIconContainer = null;
        this.ballisticIconContainer = null;
        this.timeline = [];

        this.animationStrategy = null;
        this.phaseStrategy = null;
        this.coordinateConverter = null;
    }

    phaseDirector.prototype.init = function (coordinateConverter, scene) {
        this.coordinateConverter = coordinateConverter;
        this.shipIconContainer = new ShipIconContainer(this.coordinateConverter, scene);
        this.ewIconContainer = new EWIconContainer(this.coordinateConverter, scene);
        this.ballisticIconContainer = new BallisticIconContainer(this.coordinateConverter, scene);
    };

    phaseDirector.prototype.receiveGamedata = function (gamedata, webglScene) {
        resolvePhaseStrategy.call(this, gamedata, webglScene);
    };

    phaseDirector.prototype.relayEvent = function (name, payload) {
        if (!this.phaseStrategy || this.phaseStrategy.inactive) {
            return;
        }

        this.phaseStrategy.onEvent(name, payload);
        this.shipIconContainer.onEvent(name, payload);
        this.ewIconContainer.onEvent(name, payload);
    };

    phaseDirector.prototype.render = function (scene, coordinateConverter, zoom) {
        if (!this.phaseStrategy || this.phaseStrategy.inactive) {
            return;
        }

        this.phaseStrategy.render(coordinateConverter, scene, zoom);
    };

    function resolvePhaseStrategy(gamedata, scene) {
        if (!gamedata.thisplayer || gamedata.thisplayer === -1 || gamedata.replay) {
            return activatePhaseStrategy.call(this, window.ReplayPhaseStrategy, gamedata, scene);
        }

        if (gamedata.waiting) {
            return activatePhaseStrategy.call(this, window.WaitingPhaseStrategy, gamedata, scene);
        }

        switch (gamedata.gamephase) {
            case -1:
                return activatePhaseStrategy.call(this, window.DeploymentPhaseStrategy, gamedata, scene);
            case 1:
                return activatePhaseStrategy.call(this, window.InitialPhaseStrategy, gamedata, scene);
            case 2:
                return activatePhaseStrategy.call(this, window.MovementPhaseStrategy, gamedata, scene);
            case 3:
                return activatePhaseStrategy.call(this, window.FirePhaseStrategy, gamedata, scene);
            default:
                return activatePhaseStrategy.call(this, window.WaitingPhaseStrategy, gamedata, scene);
        }
    }

    function activatePhaseStrategy(phaseStrategy, gamedata, scene, onDoneCallback) {
        if (this.phaseStrategy && this.phaseStrategy instanceof phaseStrategy) {
            this.phaseStrategy.update(gamedata);
            return;
        }

        if (this.phaseStrategy) {
            this.phaseStrategy.deactivate();
        }

        this.phaseStrategy = new phaseStrategy(this.coordinateConverter).activate(this.shipIconContainer, this.ewIconContainer, this.ballisticIconContainer, gamedata, scene, onDoneCallback);
    }

    return phaseDirector;
})();