window.MovementAnimationStrategy = (function(){

    function MovementAnimationStrategy(){
        AnimationStrategy.call(this);
        this.animatedMovements = {};
    }

    MovementAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    MovementAnimationStrategy.prototype.activate = function(shipIcons, turn, scene) {
        this.shipIconContainer = shipIcons;
        this.turn = turn;

        return this;
    };

    MovementAnimationStrategy.prototype.update = function(gamedata) {
        console.log("Animation startegy update");
        buildAnimationsFromUnAnimated.call(this);
        if (this.animations.length === 0){
            this.positionAndFaceAllIcons();
        } else {
            this.initializeAnimations();
        }

        return this;
    };

    MovementAnimationStrategy.prototype.deactivate = function(scene) {
        return this;
    };

    MovementAnimationStrategy.prototype.render = function(){
        AnimationStrategy.prototype.render.call(this);
    };

    function buildAnimationsFromUnAnimated() {
        this.shipIconContainer.getArray().forEach(function(icon) {
            var moves = icon.getMovingMovements(this.turn);
            console.log(icon.shipName, "movements", moves);
            moves = moves.filter(function(move, i) {
                var result = !this.animatedMovements[getMovementPseudoId(move, i)];
                this.animatedMovements[getMovementPseudoId(move, i)] = true;
                return result;
            }, this);

            if (moves.length === 0) {
                return;
            }

            this.animations.push(new ShipMovementAnimation(icon, moves, icon.getMovementBefore(moves[0])));
        }, this)
    }

    function getMovementPseudoId(move, i) {
        return window.shipManager.movement.getMovementPseudoId(move, i);
    }

    return MovementAnimationStrategy;
})();