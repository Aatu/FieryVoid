window.DeploymentAnimationStrategy = (function(){
    function DeploymentAnimationStrategy(){
        this.shipIcons = null;
        this.turn = 0;
    }

    DeploymentAnimationStrategy.prototype = Object.create(window.IdleAnimationStrategy);

    DeploymentAnimationStrategy.prototype.activate = function(shipIcons, turn, scene) {
        return window.IdleAnimationStrategy.prototype.activate.call(this, shipIcons, turn, scene);
    };

    DeploymentAnimationStrategy.prototype.deactivate = function(scene) {
        return window.IdleAnimationStrategy.prototype.deactivate.call(this, scene);
    };

    return DeploymentAnimationStrategy;
})();