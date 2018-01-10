window.ShipMovementAnimation = (function(){

    function ShipMovementAnimation(shipIcon, moves, startingMove) {
        this.shipIcon = shipIcon;
        this.moves = moves;
        this.startingMove = startingMove;
        this.curves = [];
        this.initialized = false;

        console.log("Instantiated ship movement animation", this);
    }

    ShipMovementAnimation.prototype.initialize = function () {
        if (this.initialized) {
            return;
        }

        this.curves = buildCurves(this.startingMove, getMovingMoves(this.moves))
        this.initialized = true;
    };

    function buildCurves(startingMove, moves) {
        return moves.map(function(move, i){
            console.log("build curves");
            var start = i === 0 ? startingMove : moves[i-1];
            var startGuide = projectForward(start.heading, start.position);
            var end = move;
            var endGuide = projectBackwards(end.facing, end.position);

            console.log("curving move", end.type, start.position, end.position);
            var curve = buildCurve(
                window.coordinateConverter.fromHexToGame(start.position),
                startGuide,
                window.coordinateConverter.fromHexToGame(end.position),
                endGuide
            );

            drawDebugCurve(curve);
        });
    }

    function buildCurve(start, startGuide, end, endGuide){
        return  new THREE.CubicBezierCurve(
            new THREE.Vector3( start.x, start.y, 0 ),
            new THREE.Vector3( startGuide.x, startGuide.y, 0 ),
            new THREE.Vector3( endGuide.x, endGuide.y, 0 ),
            new THREE.Vector3( end.x, end.y, 0 )
        );
    }
    function getMovingMoves(moves) {
        console.log(moves);
        var accept = ["move", "slipright", "slipleft"];
        return moves.filter(function (move) {
            return accept.indexOf(move.type) !== -1;
        });
    }

    function projectBackwards(facing, hex) {
        return projectForward(mathlib.addToHexFacing(facing, 3), hex);
    }

    function projectForward(facing, hex) {
        var start = window.coordinateConverter.fromHexToGame(hex);
        var end = window.coordinateConverter.fromHexToGame(hex.getNeighbourAtDirection(facing));
        return mathlib.getPointBetween(start, end, 0.25);
    }

    //getCurveFor

    function drawDebugCurve(curve) {

        var path = new THREE.Path( curve.getPoints( 50 ) );

        var geometry = path.createPointsGeometry( 50 );
        var material = new THREE.LineBasicMaterial( { color : 0xff0000 } );

        // Create the final Object3d to add to the scene
        var curveObject = new THREE.Line( geometry, material );
        webglScene.scene.add(curveObject);
    };

    /*
     var curve = new THREE.CubicBezierCurve(
     new THREE.Vector3( -10, 0, 0 ),
     new THREE.Vector3( -5, 15, 0 ),
     new THREE.Vector3( 20, 15, 0 ),
     new THREE.Vector3( 10, 0, 0 )
     );

     var path = new THREE.Path( curve.getPoints( 50 ) );

     var geometry = path.createPointsGeometry( 50 );
     var material = new THREE.LineBasicMaterial( { color : 0xff0000 } );

     // Create the final Object3d to add to the scene
     var curveObject = new THREE.Line( geometry, material );
     */
    return ShipMovementAnimation;
})();