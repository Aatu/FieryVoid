window.ShipMovementAnimation = (function(){

    function ShipMovementAnimation(shipIcon, turn) {
        this.shipIcon = shipIcon;
        this.turn = turn;
        //this.moves = moves;
        //this.startingMove = startingMove;
        this.hexAnimations = [];
        this.timeElapsed = null;
        this.currentlyAnimated = null;
        this.timePerAnimation = 500;
    }

    ShipMovementAnimation.prototype.update = function (gameData) {
        this.hexAnimations.forEach(function (animation) {
            webglScene.scene.remove(animation.debugCurve);
        });

        this.currentlyAnimated = null;
        this.hexAnimations = buildCurves(this.shipIcon, this.turn, this.hexAnimations);

        this.hexAnimations.forEach(function (animation) {
            animation.debugCurve = drawDebugCurve(animation.curve);
        });

    };

    ShipMovementAnimation.prototype.render =  function (now, total, last, delta) {
        //console.log(total, last, delta);
        if (! this.shipIcon) {
            return;
        }

        this.timeElapsed += delta;

        if (this.currentlyAnimated && this.currentlyAnimated.animated === 1) {
            this.currentlyAnimated = null;
        }

        if (this.currentlyAnimated === null && getFirstUnAnimated(this.hexAnimations)){
            this.currentlyAnimated = getFirstUnAnimated(this.hexAnimations);
            this.timeElapsed = this.timePerAnimation * this.currentlyAnimated.animated;
        }



        var percentDone = this.currentlyAnimated ? this.timeElapsed / this.timePerAnimation : 0;

        if (percentDone > 1) {
            percentDone = 1;
        }

        if (this.currentlyAnimated) {
            this.currentlyAnimated.animated = percentDone;
        }
        positionAndFaceIcon(this.shipIcon, this.currentlyAnimated, percentDone);
    };

    function positionAndFaceIcon(shipIcon, animation, percentDone) {
        var position;

        if (!animation) {
            position = window.coordinateConverter.fromHexToGame(shipIcon.getLastMovement().position);
        } else {
            position = animation.curve.getPoint(percentDone)
        }

        //var facing = shipManager.hexFacingToAngle(movement.facing);

        shipIcon.setPosition(position);
        //shipIcon.setFacing(-facing);
    }

    function getFirstUnAnimated(hexAnimations) {
        var animation = hexAnimations.find(function (animation) {
           return animation.animated < 1;
        });

        if (animation) {
            console.log("found animation with animated: ", animation.animated);
        }
        return animation;
    }

    function buildCurves(shipIcon, turn, hexAnimations) {
        var moves = shipIcon.getMovements(turn);
        /*
        var lastMove = shipIcon.getMovementBefore(moves[0]);
        if (lastMove) {
            moves.unshift(lastMove);
        }
        */

        if (moves.length <= 1) {
            return [];
        }

        var newMovesFound = false;


        return moves.map(function(move, i){
            var start = i === 0 ? null : moves[i-1];
            var next = moves[i+1] ? moves[i+1] : null;
            // shipIcon.getMovementAfter(moves[0]) for continuous playback
            var animated = 0;
            var shouldBuildPath = false;

            if (hexAnimations[i] && !newMovesFound && shipIcon.movesEqual(move, hexAnimations[i].move)) {
                if (hexAnimations[i+1] && moves[i+1] && shipIcon.movesEqual(moves[i+1], hexAnimations[i+1].move)) {
                    return hexAnimations[i];
                }

                if (moves[i+1]){
                    shouldBuildPath = true;
                    animated = hexAnimations[i].animated > 0.5 ? 0.5 : hexAnimations[i].animated;
                } else {
                    animated = hexAnimations[i].animated > 0.5 ? 1 : hexAnimations[i].animated * 2;
                }
            }

            newMovesFound = true;

            var movementPoints = getMovementPoints(
                move.position,
                next ? next.position : null,
                start ? start.position : null
            );

            var curve;
            if (shouldBuildPath) {
                curve = buildPath(
                    movementPoints.start,
                    movementPoints.end,
                    movementPoints.control
                );
            } else {
                curve = buildCurve(
                    movementPoints.start,
                    movementPoints.end,
                    movementPoints.control
                );
            }

            return {move: move, curve: curve, animated: animated};
        }, this);
    }


    function getMovementPoints(currentHex, nextHex, lastHex) {

        var start, end, control;

        if (lastHex) {
            start = mathlib.getPointBetween(window.coordinateConverter.fromHexToGame(lastHex), window.coordinateConverter.fromHexToGame(currentHex), 0.5);
        } else {
            start = window.coordinateConverter.fromHexToGame(currentHex);
        }

        if (nextHex) {
            end = mathlib.getPointBetween(window.coordinateConverter.fromHexToGame(currentHex), window.coordinateConverter.fromHexToGame(nextHex), 0.5);
        } else {
            end = window.coordinateConverter.fromHexToGame(currentHex);
        }

        control = window.coordinateConverter.fromHexToGame(currentHex);

        return {start: start, end: end, control: control};
    }

    function buildCurve(start, end, control){
        return  new THREE.QuadraticBezierCurve(
            new THREE.Vector3(start.x, start.y),
            new THREE.Vector3(control.x, control.y),
            new THREE.Vector3(end.x, end.y)
        );
    }

    function buildPath(start, end, middle){
        return {
            getPoint: function(percent){
                if (percent === 0.5){
                    return middle;
                } else if ( percent < 0.5) {
                    return mathlib.getPointBetween(start, middle, percent*2);
                } else {
                    return mathlib.getPointBetween(middle, end, (percent-0.5)*2);
                }
            },
            getPoints: function () {
                return [
                    new THREE.Vector3(start.x, start.y),
                    new THREE.Vector3(middle.x, middle.y),
                    new THREE.Vector3(end.x, end.y)
                ];
            }

        };
    }

    function drawDebugCurve(curve) {


        //var path = new THREE.Path( curve.getPoints( 50 ) );

        //var geometry = path.createPointsGeometry( 50 );
        var geometry = new THREE.Geometry().setFromPoints( curve.getPoints( 50 ) )
        var material = new THREE.LineBasicMaterial( { color : 0xff0000 } );


        var curveObject = new THREE.Line( geometry, material );
        webglScene.scene.add(curveObject);
        return curveObject;
    }

    return ShipMovementAnimation;
})();