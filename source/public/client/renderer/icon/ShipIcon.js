window.ShipIcon = (function (){

    function ShipIcon(ship, scene){

        this.shipId = ship.id;
        this.shipName = ship.name;
        this.imagePath = ship.imagePath;
        this.movements = null;
        this.defaultPosition = null;
        this.mesh = null;
        this.size = ship.canvasSize;
        this.mine = gamedata.isMyShip(ship);

        this.shipSprite = null;
        this.shipEWSprite = null;
        this.ShipSelectedSprite = null;
        this.ShipSideSprite = null;

        this.create(ship, scene);
        this.consumeShipdata(ship);
    }

    ShipIcon.prototype.consumeShipdata = function (ship){
        this.movements = this.consumeMovement(ship.movement);
        this.consumeEW(ship);
        this.createShipWindow(ship);
    };

    ShipIcon.prototype.createShipWindow = function(ship) {
        var element = jQuery(".shipwindow.ship_"+ship.id);
;
        if (!element.length) {
            ship.shipStatusWindow = shipWindowManager.createShipWindow(ship);
        } else {
            ship.shipStatusWindow = element;
        }

        shipWindowManager.setData(ship);
    };

    ShipIcon.prototype.setPosition = function(position) {
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
    };

    ShipIcon.prototype.getPosition = function() {
        return {x: this.mesh.position.x, y: this.mesh.position.y};
    };

    ShipIcon.prototype.setFacing = function(facing) {
        this.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };

    ShipIcon.prototype.setOverlayColorAlpha = function(alpha) {
        this.shipSprite.setOverlayColorAlpha(alpha);
    };

    ShipIcon.prototype.getMovements = function(turn){
        return this.movements.filter(function(movement){
            return (turn === undefined || movement.turn === turn);
        }, this);
    };

    ShipIcon.prototype.setScale = function(width, height){
        this.mesh.scale.set(
            width,
            height,
            1
        );
    };

    ShipIcon.prototype.consumeEW = function(ship) {
        var dew = ew.getDefensiveEW(ship);
        if (ship.flight) {
            dew = shipManager.movement.getJinking(ship);
        }

        var ccew = ew.getCCEW(ship);

        this.shipEWSprite.update(dew, ccew);
    };

    ShipIcon.prototype.showEW = function(){
        this.shipEWSprite.show();
    };

    ShipIcon.prototype.hideEW = function(){
        if (this.shipEWSprite) {
            this.shipEWSprite.hide();
        }
    };

    ShipIcon.prototype.showSideSprite = function(value) {
        if (value) {
            this.ShipSideSprite.show();
        } else {
            this.ShipSideSprite.hide();
        }
    };

    ShipIcon.prototype.setSelected = function(value) {
        if (value) {
            this.ShipSelectedSprite.show();
        } else {
            this.ShipSelectedSprite.hide();
        }
    };

    ShipIcon.prototype.create = function(ship, scene) {
        var imagePath = ship.imagePath;
        this.mesh = new THREE.Object3D();
        this.mesh.position = new THREE.Vector3(500, 0, 0);
        this.mesh.renderDepth = 10;

        this.shipSprite = new window.webglSprite(imagePath, {width: this.size/2, height: this.size/2}, 0);
        this.shipSprite.setOverlayColor(this.mine ? new THREE.Color(160/255,250/255,100/255) : new THREE.Color(255/255,40/255,40/255));
        this.mesh.add(this.shipSprite.mesh);

        this.shipEWSprite = new window.ShipEWSprite({width: this.size/2, height: this.size/2}, -1);
        this.mesh.add(this.shipEWSprite.mesh);

        this.ShipSelectedSprite = new window.ShipSelectedSprite({width: this.size/2, height: this.size/2}, -2, this.mine ? 'ally' : 'enemy', true).hide();
        this.mesh.add(this.ShipSelectedSprite.mesh);

        this.ShipSideSprite = new window.ShipSelectedSprite({width: this.size/2, height: this.size/2}, -2, this.mine ? 'ally' : 'enemy', false).hide();
        this.mesh.add(this.ShipSideSprite.mesh);

        scene.add(this.mesh);
    };

    ShipIcon.prototype.consumeMovement = function(movements){
        //console.log(JSON.stringify(movements));

        var movesByHexAndTurn = {};

        this.defaultPosition = {
            turn: movements[0].turn,
            facing: movements[0].facing,
            heading: movements[0].heading,
            position: new hexagon.Offset(movements[0].position),
            offset: {x: movements[0].xOffset, y: movements[0].yOffset}
        };

        movements
            .filter(function (movement) {return movement.type !== 'start';})
            .filter(function (movement) {return movement.commit;})
            .forEach(function (movement) {
                if (movesByHexAndTurn[movement.position.q + "," + movement.position.r + "t" + movement.turn]){
                    var saved = movesByHexAndTurn[movement.position.q + "," + movement.position.r + "t" + movement.turn];
                    saved.facing = movement.facing;
                    saved.heading = movement.heading;
                    saved.position = new hexagon.Offset(movement.position);
                    saved.offset = {x: movement.xOffset, y: movement.yOffset};
                } else {
                    movesByHexAndTurn[movement.position.q + "," + movement.position.r + "t" + movement.turn] = {
                        //id: movement.id,
                        //type: movement.type,
                        turn: movement.turn,
                        facing: movement.facing,
                        heading: movement.heading,
                        position: new hexagon.Offset(movement.position),
                        offset: {x: movement.xOffset, y: movement.yOffset}
                    }
                }
            });

        return Object.keys(movesByHexAndTurn).map(function (key) {return movesByHexAndTurn[key];});
    };

    ShipIcon.prototype.movesEqual = function (move1, move2) {
        return move1.turn === move2.turn &&
            move1.position.equals(move2.position);// &&
            //move1.facing === move2.facing &&
            //move1.heading === move2.heading &&
            //move1.offset.x === move2.offset.x &&
            //move1.offset.y === move2.offset.y;
    };

/*
    ShipIcon.prototype.getMovingMovements = function(movements){
        var accept = ["deploy", "move", "slipleft", "slipright", "rotateRight", "rotateLeft", "turnleft", "turnright", "pivotleft", "pivotright"];
        return movements.filter(function (move) {
            return accept.indexOf(move.type) !== -1;
        });
    };
*/
    ShipIcon.prototype.getLastMovement = function(){
        if (this.movements.length === 0) {
            return this.defaultPosition;
        }

        return this.movements[this.movements.length - 1]
    };

    /*
    ShipIcon.prototype.getLastCommittedMovement = function(){
        var moves = this.movements.filter(function (movement) {return movement.commit;});
        return moves[moves.length - 1];
    };
    */

    ShipIcon.prototype.getFirstMovementOnTurn = function(turn, ignore){

        return this.movements.filter(function (move) {
            return move.turn === turn;
        }).shift();
    };

    ShipIcon.prototype.getMovementBefore = function (move) {
        for (var i in this.movements) {
            if (this.movements[i] === move) {
                return this.movements[i-1];
            }
        }

        return null;
    };

    ShipIcon.prototype.getMovementAfter = function (move) {
        for (var i in this.movements) {
            if (this.movements[i] === move) {
                if (this.movements[i+1]) {
                    return this.movements[i+1]
                }
                return null;
            }
        }

        return null;
    };


    return ShipIcon;
})();