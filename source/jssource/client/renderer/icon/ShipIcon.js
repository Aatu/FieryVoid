window.ShipIcon = (function (){

    function ShipIcon(ship, scene){

        this.shipId = ship.id;
        this.shipName = ship.name;
        this.imagePath = ship.imagePath;
        this.ship = ship;
        this.movements = null;
        this.defaultPosition = null;
        this.mesh = null;
        this.size = ship.canvasSize;
        this.mine = gamedata.isMyShip(ship);
        this.scene = scene;
        this.shipSprite = null;
        this.shipEWSprite = null;
        this.ShipSelectedSprite = null;
        this.ShipSideSprite = null;
        this.weaponArcs = [];
        this.hidden = false;

        this.selected = false;

        this.create(ship, scene);
        this.consumeShipdata(ship);
    }

    ShipIcon.prototype.consumeShipdata = function (ship){
        this.consumeMovement(ship.movement);
        this.consumeEW(ship);
        this.createShipWindow(ship);
    };

    ShipIcon.prototype.createShipWindow = function(ship) {
        var element = jQuery(".shipwindow.ship_"+ship.id);

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

    ShipIcon.prototype.setOpacity = function(opacity){
        this.shipSprite.setOpacity(opacity);
    };

    ShipIcon.prototype.hide = function(){
        if (this.hidden) {
            return;
        }

        this.scene.remove(this.mesh);
        this.hidden = true;
    };

    ShipIcon.prototype.show = function(){
        if (!this.hidden) {
            return;
        }

        this.scene.add(this.mesh);
        this.hidden = false;
    };

    ShipIcon.prototype.getFacing = function(facing) {
        return mathlib.radianToDegree(this.mesh.rotation.z);
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
            if (!this.selected) {
                this.mesh.position.z = this.mesh.position.z + 100;
            }
        } else {
            if (this.selected) {
                this.mesh.position.z = this.mesh.position.z - 100;
            }
            this.ShipSelectedSprite.hide();
        }

        this.selected = value;
    };

    ShipIcon.prototype.create = function(ship, scene) {
        var imagePath = ship.imagePath;
        this.mesh = new THREE.Object3D();
        this.mesh.position.set(500, 0, 0);
        this.mesh.renderDepth = 10;

        this.shipSprite = new window.webglSprite(imagePath, {width: this.size/2, height: this.size/2}, 0);
        this.shipSprite.setOverlayColor(this.mine ? new THREE.Color(160/255,250/255,100/255) : new THREE.Color(255/255,40/255,40/255));
        this.mesh.add(this.shipSprite.mesh);

        this.shipEWSprite = new window.ShipEWSprite({width: this.size/2, height: this.size/2}, -1);
        this.mesh.add(this.shipEWSprite.mesh);
        this.shipEWSprite.hide();

        this.ShipSelectedSprite = new window.ShipSelectedSprite({width: this.size/2, height: this.size/2}, -2, this.mine ? 'ally' : 'enemy', true).hide();
        this.mesh.add(this.ShipSelectedSprite.mesh);

        this.ShipSideSprite = new window.ShipSelectedSprite({width: this.size/2, height: this.size/2}, -2, this.mine ? 'ally' : 'enemy', false).hide();
        this.mesh.add(this.ShipSideSprite.mesh);

        scene.add(this.mesh);
    };

    ShipIcon.prototype.consumeMovement = function(movements){

        var movesByHexAndTurn = {};

        this.defaultPosition = {
            turn: movements[0].turn,
            facing: movements[0].facing,
            heading: movements[0].heading,
            position: new hexagon.Offset(movements[0].position),
            offset: {x: movements[0].xOffset, y: movements[0].yOffset}
        };

        var lastMovement = null;

        movements
            .filter(function (movement) {return movement.type !== 'start';})
            .filter(function (movement) {return movement.commit;})
            .forEach(function (movement) {

                if (lastMovement && movement.turn !== lastMovement.turn) {

                    if (movement.type === "move" || movement.type === "slipleft" || movement.type === "slipright"){
                        addMovementToRegistry(
                            movesByHexAndTurn,
                            {
                                turn: movement.turn,
                                facing: movement.facing,
                                heading: movement.heading,
                                position: new hexagon.Offset(lastMovement.position),
                                oldFacings: [],
                                oldHeadings: []
                            }
                        );
                    }
                }

                addMovementToRegistry(movesByHexAndTurn, movement);

                lastMovement = movement;
            });

        this.movements = Object.keys(movesByHexAndTurn).map(function (key) {return movesByHexAndTurn[key];});
    };

    function addMovementToRegistry(movesByHexAndTurn, movement) {
        if (movesByHexAndTurn[movement.position.q + "," + movement.position.r + "t" + movement.turn]){
            var saved = movesByHexAndTurn[movement.position.q + "," + movement.position.r + "t" + movement.turn];

            if (saved.facing !== movement.facing) {
                saved.oldFacings.push(saved.facing);
            }

            saved.facing = movement.facing;

            if (saved.heading !== movement.heading) {
                saved.oldHeadings.push(saved.heading);
            }

            saved.heading = movement.heading;

            saved.position = new hexagon.Offset(movement.position);
        } else {
            movesByHexAndTurn[movement.position.q + "," + movement.position.r + "t" + movement.turn] = {
                //id: movement.id,
                //type: movement.type,
                turn: movement.turn,
                facing: movement.facing,
                heading: movement.heading,
                position: new hexagon.Offset(movement.position),
                oldFacings: [],
                oldHeadings: []
            }
        }
    }

    ShipIcon.prototype.movesEqual = function (move1, move2) {
        return move1.turn === move2.turn &&
            move1.position.equals(move2.position);// &&
            //move1.facing === move2.facing &&
            //move1.heading === move2.heading &&
            //move1.offset.x === move2.offset.x &&
            //move1.offset.y === move2.offset.y;
    };

    ShipIcon.prototype.getLastMovement = function(){
        if (this.movements.length === 0) {
            return this.defaultPosition;
        }

        return this.movements[this.movements.length - 1]
    };

    ShipIcon.prototype.getFirstMovementOnTurn = function(turn, ignore){
        var movement = this.movements.filter(function (move) {
            return move.turn === turn;
        }).shift();

        if (!movement) {
            return this.getLastMovement();
        }

        return movement;
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

    ShipIcon.prototype.showWeaponArc = function (ship, weapon) {

        var hexDistance = window.coordinateConverter.getHexDistance();
        var dis = weapon.rangePenalty === 0 ? hexDistance*weapon.range : 50/weapon.rangePenalty * hexDistance;
        var arcs = shipManager.systems.getArcs(ship, weapon);

        var arcLenght = arcs.start === arcs.length ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
        var arcStart = mathlib.addToDirection(0, arcLenght * -0.5);
        var arcFacing = mathlib.addToDirection(arcs.end, arcLenght * -0.5);

        var geometry = new THREE.CircleGeometry( dis, 32, mathlib.degreeToRadian(arcStart), mathlib.degreeToRadian(arcLenght) );
        var material = new THREE.MeshBasicMaterial( { color: new THREE.Color("rgb(20,80,128)"), opacity: 0.5, transparent: true} );
        var circle = new THREE.Mesh( geometry, material );
        circle.rotation.z = mathlib.degreeToRadian(-arcFacing);
        circle.position.z = -1;
        this.mesh.add( circle );
        this.weaponArcs.push(circle);

        return null;
    };

    ShipIcon.prototype.hideWeaponArcs = function () {
        this.weaponArcs.forEach(function(arc) {
            this.mesh.remove(arc);
        }, this)
    };

    ShipIcon.prototype.positionAndFaceIcon = function(offset){
        var movement = this.getLastMovement();
        var gamePosition = window.coordinateConverter.fromHexToGame(movement.position);

        if (offset) {
            gamePosition.x += offset.x;
            gamePosition.y += offset.y;
        }

        var facing = mathlib.hexFacingToAngle(movement.facing);

        this.setPosition(gamePosition);
        this.setFacing(-facing);
    };


    return ShipIcon;
})();