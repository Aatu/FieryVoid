"use strict";

window.phaseDirector = function () {

    function phaseDirector(graphics) {
        this.graphics = graphics;
        this.shipIconContainer = null;
        this.ewIconContainer = null;
        this.ballisticIconContainer = null;
        this.timeline = [];

        this.animationStrategy = null;
        this.phaseStrategy = null;
        this.coordinateConverter = null;
        this.shipWindowManager = null;
    }

    phaseDirector.prototype.init = function (coordinateConverter, scene) {
        this.coordinateConverter = coordinateConverter;
        this.shipIconContainer = new ShipIconContainer(this.coordinateConverter, scene);
        this.ewIconContainer = new EWIconContainer(this.coordinateConverter, scene, this.shipIconContainer);
        this.ballisticIconContainer = new BallisticIconContainer(this.coordinateConverter, scene);
        this.shipWindowManager = new ShipWindowManager(new window.UIManager($("body")[0]));
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
        this.ballisticIconContainer.onEvent(name, payload);        
    };

    phaseDirector.prototype.render = function (scene, coordinateConverter, zoom) {
        if (!this.phaseStrategy || this.phaseStrategy.inactive) {
            return;
        }

        this.phaseStrategy.render(coordinateConverter, scene, zoom);
    };

    // Idle render-loop gating: true while something on the board is moving
    // (playing animations). webglScene uses this to decide whether the next
    // frame needs a full render or can be skipped. Conservatively returns true
    // if the strategy is missing the accessor.
    phaseDirector.prototype.isAnimating = function () {
        if (!this.phaseStrategy || this.phaseStrategy.inactive) {
            return false;
        }

        if (typeof this.phaseStrategy.isAnimating !== 'function') {
            return true;
        }

        return this.phaseStrategy.isAnimating();
    };

    function resolvePhaseStrategy(gamedata, scene) {
        if (!gamedata.isPlayerInGame() || gamedata.replay || gamedata.status === "SURRENDERED" || gamedata.status === "FINISHED") {
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
            case 5:
                return activatePhaseStrategy.call(this, window.PreFiringPhaseStrategy, gamedata, scene);                
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

        this.phaseStrategy = new phaseStrategy(this.coordinateConverter).activate(this.shipIconContainer, this.ewIconContainer, this.ballisticIconContainer, gamedata, scene, this.shipWindowManager, onDoneCallback);

        /* Save Orders (SAVE_ORDERS_PLAN.md §1.3). A draft game.php inlined goes back in HERE, after
           activate() - which has just re-copied last turn's power and re-declared any standing
           abduction or maintained vortex - so the restore can replace this turn's entries rather
           than be added to (trap T3). One site for all three phase strategies; the draft is spent on
           first use. Also keeps the OPTIONS block in step with replay, waiting and phase changes. */
        if (window.savedOrders) savedOrders.onStrategyActivated(gamedata, this.phaseStrategy);

        /* Live sweeping (MINE_DETECTION_PLAN.md §1.4.6): the steps this player's units have already
           swept come back, locked - AFTER the draft above, which replaces this turn's rows (T14). */
        if (window.mineSweep) mineSweep.onStrategyActivated(gamedata, this.phaseStrategy);
    }

    return phaseDirector;
}();