window.ShipMovementAnimation = (function(){

    function ShipMovementAnimation(shipIcon, turn) {
        this.shipIcon = shipIcon;
        this.turn = turn;
        this.hexAnimations = [];
        this.timeElapsed = null;
        this.currentlyAnimated = null;
        this.timePerAnimation = 500;

        this.turnCurve = new THREE.CubicBezierCurve(
            new THREE.Vector2( 0, 0 ),
            new THREE.Vector2( 1, 0 ),
            new THREE.Vector2( 0, 1 ),
            new THREE.Vector2( 1, 1 )
        );

        Animation.call(this);
    }

    ShipMovementAnimation.prototype = Object.create(Animation.prototype);

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
        if (! this.shipIcon || !this.active) {
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
        positionAndFaceIcon(this.shipIcon, this.currentlyAnimated, percentDone, this.turnCurve);
    };

    function positionAndFaceIcon(shipIcon, animation, percentDone, turnCurve) {
        var position;
        var facing;
        if (!animation) {
            position = window.coordinateConverter.fromHexToGame(shipIcon.getLastMovement().position);
            facing = shipManager.hexFacingToAngle(shipIcon.getLastMovement().facing);
        } else {
            var turnPercent = turnCurve.getPoint(percentDone).y;
            position = animation.curve.getPoint(percentDone);
            var turnAngle = animation.turnAngle * turnPercent;
            facing = mathlib.addToDirection(animation.startAngle, turnAngle);
        }

        shipIcon.setPosition(position);
        shipIcon.setFacing(-facing);
    }

    function getFirstUnAnimated(hexAnimations) {
        return hexAnimations.find(function (animation) {
           return animation.animated < 1;
        });
    }

    function buildCurves(shipIcon, turn, hexAnimations) {
        var moves = shipIcon.getMovements(turn);

        if (moves.length <= 1) {
            return [];
        }

        var newMovesFound = false;


        return moves.map(function(move, i){
            var start = i === 0 ? null : moves[i-1];
            var next = moves[i+1] ? moves[i+1] : null;
            // shipIcon.getMovementAfter(moves[0]) for continuous playback
            var animated = 0;
            var uncommittedMovement = false;

            if (hexAnimations[i] && !newMovesFound && shipIcon.movesEqual(move, hexAnimations[i].move)) {
                if (hexAnimations[i+1] && moves[i+1] && shipIcon.movesEqual(moves[i+1], hexAnimations[i+1].move)) {
                    return hexAnimations[i];
                }

                if (moves[i+1]){
                    uncommittedMovement = true;
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
            if (uncommittedMovement) {
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

            var turnData = calculateTurn(start, move);

            if (uncommittedMovement) {
                turnData.startAngle = turnData.endAngle;
                turnData.turnAngle = 0;
            }

            return {move: move, curve: curve, animated: animated, turnAngle: turnData.turnAngle, startAngle: turnData.startAngle, endAngle: turnData.endAngle};
        }, this);
    }

    function calculateTurn(startMove, endMove) {

        var endFacing = endMove.facing;
        var angleNew = shipManager.hexFacingToAngle(endFacing);

        if (! startMove) {
            return {turnAngle: 0, startAngle: angleNew, endAngle: angleNew};
        }

        var startFacing = startMove.facing;
        var angleOld = shipManager.hexFacingToAngle(startFacing);
        var turn = 0;

        if (startFacing === endFacing) {
            return {turnAngle: turn, startAngle: angleOld, endAngle: angleNew};
        }

        var lastOldFacing = endMove.oldFacings[endMove.oldFacings.length - 1];

        if (endFacing > lastOldFacing || (endFacing === 0 && lastOldFacing === 5)) {
            turn = mathlib.getAngleBetween(angleOld, angleNew, true);
        } else {
            turn = mathlib.getAngleBetween(angleOld, angleNew, false);
        }

        return {turnAngle: turn, startAngle: angleOld, endAngle: angleNew};
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