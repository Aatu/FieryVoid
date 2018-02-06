window.ShipMovementAnimation = (function(){

    function ShipMovementAnimation(shipIcon, turn) {
        this.shipIcon = shipIcon;
        this.turn = turn;
        this.hexAnimations = buildCurves(this.shipIcon, this.turn);
        this.totalCurveLength = calculateTotalCurveLength(this.hexAnimations);
        this.duration = 5000;
        this.time = 0;


        this.turnCurve = new THREE.CubicBezierCurve(
            new THREE.Vector2( 0, 0 ),
            new THREE.Vector2( 1, 0 ),
            new THREE.Vector2( 0, 1 ),
            new THREE.Vector2( 1, 1 )
        );

        /*
        this.hexAnimations.forEach(function (animation) {
            animation.debugCurve = drawRoute(animation.curve);
        });
        */

        Animation.call(this);
    }

    ShipMovementAnimation.prototype = Object.create(Animation.prototype);

    ShipMovementAnimation.prototype.update = function (gameData) {};

    ShipMovementAnimation.prototype.stop = function () {
        Animation.prototype.stop.call(this);
        /*
        this.hexAnimations.forEach(function (animation) {
            webglScene.scene.remove(animation.debugCurve);
        });
        */
    };

    ShipMovementAnimation.prototype.cleanUp = function () {
        /*
        this.hexAnimations.forEach(function (animation) {
            webglScene.scene.remove(animation.debugCurve);
        });
        */
    };

    ShipMovementAnimation.prototype.render =  function (now, total, last, delta) {

        var positionAndFacing = this.getPositionAndFacingAtTime(total);

        this.shipIcon.setPosition(positionAndFacing.position);
        this.shipIcon.setFacing(-positionAndFacing.facing);
    };

    ShipMovementAnimation.prototype.getPositionAndFacingAtTime = function (time) {

        var totalDone = (time - this.time) / this.duration;
        if (totalDone > 1) {
            totalDone = 1;
        }

        if (totalDone < 0) {
            totalDone = 0;
        }

        var animationAndCompletition = findAnimation.call(this, totalDone);
        var animation = animationAndCompletition.animation;
        var done = animationAndCompletition.done;

        var position;
        var facing;

        var turnPercent = this.turnCurve.getPoint(done).y;
        position = animation.curve.getPoint(done);
        var turnAngle = animation.turnAngle * turnPercent;
        facing = mathlib.addToDirection(animation.startAngle, turnAngle);

        return {position: position, facing: facing};
    };

    ShipMovementAnimation.prototype.getLength = function() {
        return this.hexAnimations.length;
    };

    ShipMovementAnimation.prototype.setDuration = function(duration) {
        this.duration = duration;
    };

    ShipMovementAnimation.prototype.getDuration = function() {
        return this.duration;
    };

    ShipMovementAnimation.prototype.setTime = function(time) {
        this.time = time;
    };
    
    function findAnimation(totalDone) {
        var length = this.totalCurveLength * totalDone;
        var result = {
            animation: null,
            done: 0
        };

        var index = 0;

        var current = this.hexAnimations.find(function(animation, i) {
           if (length <= animation.length) {
               index = i;
               return true;
           }

           length -= animation.length;
        });

        if (! current) {
            result.animation = this.hexAnimations[this.hexAnimations.length - 1];
            result.done = 1;
            return result;
        }


        var done = length / current.length;
        result.animation = current;
        result.done = done;
        return result;
    }


    function buildCurves(shipIcon, turn) {
        var moves = shipIcon.getMovements(turn);

        if (moves.length <= 1) {
            return [];
        }

        return moves.map(function(move, i){
            var start = i === 0 ? null : moves[i-1];
            var next = moves[i+1] ? moves[i+1] : null;

            var movementPoints = getMovementPoints(
                move.position,
                next ? next.position : null,
                start ? start.position : null
            );

            var curve = buildCurve(
                movementPoints.start,
                movementPoints.end,
                movementPoints.control
            );

            var turnData = calculateTurn(start, move);

            return {move: move, curve: curve, turnAngle: turnData.turnAngle, startAngle: turnData.startAngle, endAngle: turnData.endAngle, length: calculateCurveLength(curve)};
        }, this);
    }

    function calculateCurveLength(curve) {
        var start = curve.getPoint(0);
        var distance = 0;

        for (var i = 0; i <= 100; i++){
            var end = curve.getPoint(i/100);
            distance += mathlib.distance(start, end);
            start = end;
        }


        return distance;
    }

    function calculateTurn(startMove, endMove) {

        var endFacing = endMove.facing;
        var angleNew = mathlib.hexFacingToAngle(endFacing);

        if (! startMove) {
            return {turnAngle: 0, startAngle: angleNew, endAngle: angleNew};
        }

        var startFacing = startMove.facing;
        var angleOld = mathlib.hexFacingToAngle(startFacing);
        var turn = 0;

        if (startFacing === endFacing) {
            return {turnAngle: turn, startAngle: angleOld, endAngle: angleNew};
        }

        return {turnAngle: buildTurn(endMove), startAngle: angleOld, endAngle: angleNew};
    }

    function buildTurn(endMove) {
        var facings = endMove.oldFacings.concat(endMove.facing);

        var turn = 0;
        var lastFacing = null;

        facings.forEach(function(facing){
            if (lastFacing === null) {
                lastFacing = facing;
                return;
            }

            var angleLast = mathlib.hexFacingToAngle(lastFacing);
            var angleNew = mathlib.hexFacingToAngle(facing);

            var right = mathlib.getAngleBetween(angleLast, angleNew, true);
            var left = mathlib.getAngleBetween(angleLast, angleNew, false);

            if (Math.abs(right) < Math.abs(left)) {
                turn += right;
            } else {
                turn += left;
            }

            lastFacing = facing;
        });

        return turn;
    }

    function getMovementPoints(currentHex, nextHex, lastHex) {

        var start, end, control;

        var controlBetween = false;

        if (lastHex) {
            start = mathlib.getPointBetween(window.coordinateConverter.fromHexToGame(lastHex), window.coordinateConverter.fromHexToGame(currentHex), 0.5);
        } else {
            controlBetween = true;
            start = window.coordinateConverter.fromHexToGame(currentHex);
        }

        if (nextHex) {
            end = mathlib.getPointBetween(window.coordinateConverter.fromHexToGame(currentHex), window.coordinateConverter.fromHexToGame(nextHex), 0.5);
        } else {
            controlBetween = true;
            end = window.coordinateConverter.fromHexToGame(currentHex);
        }

        if (controlBetween) {
            control = mathlib.getPointBetween(start, end, 0.5);
        } else {
            control = window.coordinateConverter.fromHexToGame(currentHex);
        }


        return {start: start, end: end, control: control};
    }

    function buildCurve(start, end, control){
        return  new THREE.QuadraticBezierCurve(
            new THREE.Vector3(start.x, start.y),
            new THREE.Vector3(control.x, control.y),
            new THREE.Vector3(end.x, end.y)
        );
    }

    /*
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

*/
    function drawRoute(curve) {


        //var path = new THREE.Path( curve.getPoints( 50 ) );

        //var geometry = path.createPointsGeometry( 50 );
        var geometry = new THREE.Geometry().setFromPoints( curve.getPoints( 50 ) );
        var material = new THREE.LineBasicMaterial( { color : 0xffffff, opacity: 0.5} );

        var curveObject = new THREE.Line( geometry, material );
        curveObject.position.z = 10;
        webglScene.scene.add(curveObject);
        return curveObject;
    }

    function calculateTotalCurveLength(hexAnimations) {
        return hexAnimations.reduce(function(total, animation) {
            return total + animation.length;
        }, 0)
    }

    return ShipMovementAnimation;
})();