'use strict';

window.ShipIcon = function () {

    var directionOfMovementTexture = new THREE.TextureLoader().load('./img/directionOfMovement.png');

    function ShipIcon(ship, scene) {

        this.shipId = ship.id;
        this.shipName = ship.name;
        this.imagePath = ship.imagePath;
        this.ship = ship;
        this.movements = null;
        this.defaultPosition = null;
        this.mesh = null;
        this.size = ship.canvasSize;
        this.mine = gamedata.isMyOrTeamOneShip(ship);
        this.scene = scene;
        this.shipSprite = null;
        this.shipEWSprite = null;
        this.ShipSelectedSprite = null;
        this.ShipSideSprite = null;
        this.shipDirectionOfMovementSprite = null;
        this.weaponArcs = [];
        this.hidden = false;
        this.BDEWSprite = null;

        this.selected = false;

        this.create(ship, scene);
        this.consumeShipdata(ship);
    }

    ShipIcon.prototype.consumeShipdata = function (ship) {
        this.ship = ship;
        this.consumeMovement(ship.movement);
        this.consumeEW(ship);
        this.createShipWindow(ship);
    };

    ShipIcon.prototype.createShipWindow = function (ship) {
        var element = jQuery(".shipwindow.ship_" + ship.id);

        if (!element.length) {
            ship.shipStatusWindow = shipWindowManager.createShipWindow(ship);
        } else {
            ship.shipStatusWindow = element;
        }

        shipWindowManager.setData(ship);
    };

    ShipIcon.prototype.setPosition = function (position) {
        this.mesh.position.x = position.x;
        this.mesh.position.y = position.y;
    };

    ShipIcon.prototype.getPosition = function () {
        return { x: this.mesh.position.x, y: this.mesh.position.y };
    };

    ShipIcon.prototype.setOpacity = function (opacity) {
        this.shipSprite.setOpacity(opacity);
    };

    ShipIcon.prototype.hide = function () {
        if (this.hidden) {
            return;
        }
        
        this.scene.remove(this.mesh);
        this.hidden = true;
    };

    ShipIcon.prototype.show = function () {
        if (!this.hidden) {
            return;
        }

        this.scene.add(this.mesh);
        this.hidden = false;
    };

    ShipIcon.prototype.getFacing = function (facing) {
        return mathlib.radianToDegree(this.shipSprite.mesh.rotation.z);
    };

    ShipIcon.prototype.setFacing = function (facing) {
        this.shipSprite.mesh.rotation.z = mathlib.degreeToRadian(facing);
    };

    ShipIcon.prototype.setHeading = function (heading) {
        this.shipDirectionOfMovementSprite.mesh.rotation.z = mathlib.degreeToRadian(heading);
    };

    ShipIcon.prototype.getHeading = function (heading) {
        this.shipDirectionOfMovementSprite.mesh.rotation.z = mathlib.degreeToRadian(heading);
    };

    ShipIcon.prototype.setOverlayColorAlpha = function (alpha) {
        this.shipSprite.setOverlayColorAlpha(alpha);
    };

    ShipIcon.prototype.getMovements = function (turn) {
        return this.movements.filter(function (movement) {
            return turn === undefined || movement.turn === turn;
        }, this);
    };

    ShipIcon.prototype.setScale = function (width, height) {
        this.mesh.scale.set(width, height, 1);
    };

    ShipIcon.prototype.consumeEW = function (ship) {
        var dew = ew.getDefensiveEW(ship);
        if (ship.flight) {
            dew = shipManager.movement.getJinking(ship);
        }

        var ccew = ew.getCCEW(ship);

        this.shipEWSprite.update(dew, ccew);
    };

    ShipIcon.prototype.showEW = function () {
        this.shipEWSprite.show();
    };

    ShipIcon.prototype.hideEW = function () {
        if (this.shipEWSprite) {
            this.shipEWSprite.hide();
        }
    };

    ShipIcon.prototype.showSideSprite = function (value) {
        if (value) {
            this.ShipSideSprite.show();
        } else {
            this.ShipSideSprite.hide();
        }
    };

    ShipIcon.prototype.setHighlighted = function (value) {
        if (value) {
            this.mesh.position.z = 101;
            this.shipDirectionOfMovementSprite.show();
        } else {
            if (this.selected) {
                this.mesh.position.z = 100;
            } else {
                this.mesh.position.z = 0;
            }
            this.shipDirectionOfMovementSprite.hide();
        }

        this.selected = value;
    };

    ShipIcon.prototype.setSelected = function (value) {
        if (value) {
            this.ShipSelectedSprite.show();
            if (!this.selected) {
                this.mesh.position.z = 100;
            }
        } else {
            if (this.selected) {
                this.mesh.position.z = 0;
            }
            this.ShipSelectedSprite.hide();
        }

        this.selected = value;
    };

    ShipIcon.prototype.create = function (ship, scene) {
        var imagePath = ship.imagePath;
        this.mesh = new THREE.Object3D();
        this.mesh.position.set(500, 0, 0);
        this.mesh.renderDepth = 10;

        
        this.shipDirectionOfMovementSprite = new window.webglSprite('./img/directionOfMovement.png', { width: this.size / 1.5, height: this.size / 1.5 }, -2);
        this.mesh.add(this.shipDirectionOfMovementSprite.mesh);
        this.shipDirectionOfMovementSprite.hide();

        this.shipSprite = new window.webglSprite(imagePath, { width: this.size / 2, height: this.size / 2 }, 1);
        this.shipSprite.setOverlayColor(this.mine ? new THREE.Color(160 / 255, 250 / 255, 100 / 255) : new THREE.Color(255 / 255, 40 / 255, 40 / 255));
        this.mesh.add(this.shipSprite.mesh);

        this.shipEWSprite = new window.ShipEWSprite({ width: this.size / 2, height: this.size / 2 }, -1);
        this.mesh.add(this.shipEWSprite.mesh);
        this.shipEWSprite.hide();

        this.ShipSelectedSprite = new window.ShipSelectedSprite({ width: this.size / 2, height: this.size / 2 }, -2, this.mine ? 'ally' : 'enemy', true).hide();
        this.mesh.add(this.ShipSelectedSprite.mesh);

        this.ShipSideSprite = new window.ShipSelectedSprite({ width: this.size / 2, height: this.size / 2 }, -2, this.mine ? 'ally' : 'enemy', false).hide();
        this.mesh.add(this.ShipSideSprite.mesh);

        scene.add(this.mesh);
    };

    ShipIcon.prototype.consumeMovement = function (movements) {

        var movesByHexAndTurn = [];

        this.defaultPosition = {
            turn: movements[0].turn,
            facing: movements[0].facing,
            heading: movements[0].heading,
            position: new hexagon.Offset(movements[0].position),
            offset: { x: movements[0].xOffset, y: movements[0].yOffset }
        };

        var lastMovement = null;

        movements.filter(function (movement) {
            return movement.type !== 'start';
        }).filter(function (movement) {
            return movement.commit;
        }).forEach(function (movement) {

            if (lastMovement && movement.turn !== lastMovement.turn) {

                if (movement.type === "move" || movement.type === "slipleft" || movement.type === "slipright") {
                    addMovementToRegistry(movesByHexAndTurn, {
                        turn: movement.turn,
                        facing: movement.facing,
                        heading: movement.heading,
                        position: new hexagon.Offset(lastMovement.position),
                        oldFacings: [],
                        oldHeadings: []
                    });
                }
            }

            addMovementToRegistry(movesByHexAndTurn, movement);

            lastMovement = movement;
        });

       this.movements = movesByHexAndTurn;
    };

    function addMovementToRegistry(movesByHexAndTurn, movement) {

        var getPreviousMatchingMove = function(moves, move) {
            var previousMove = moves[moves.length-1];
            if (! previousMove) {
                return null;
            }

            if (previousMove.turn === move.turn && previousMove.position.q === move.position.q && previousMove.position.r === move.position.r) {
                return previousMove;
            }
            return null;
        }

        var previousMove = getPreviousMatchingMove(movesByHexAndTurn, movement);


        if (previousMove) {
            var saved = previousMove

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
            movesByHexAndTurn.push({
                //id: movement.id,
                //type: movement.type,
                turn: movement.turn,
                facing: movement.facing,
                heading: movement.heading,
                position: new hexagon.Offset(movement.position),
                oldFacings: [],
                oldHeadings: []
            });
        }
    }

    ShipIcon.prototype.movesEqual = function (move1, move2) {
        return move1.turn === move2.turn && move1.position.equals(move2.position); // &&
        //move1.facing === move2.facing &&
        //move1.heading === move2.heading &&
        //move1.offset.x === move2.offset.x &&
        //move1.offset.y === move2.offset.y;
    };

    ShipIcon.prototype.getLastMovement = function () {
        if (this.movements.length === 0) {
            return this.defaultPosition;
        }

        return this.movements[this.movements.length - 1];
    };

    ShipIcon.prototype.getFirstMovementOnTurn = function (turn, ignore) {
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
                return this.movements[i - 1];
            }
        }

        return null;
    };

    ShipIcon.prototype.getMovementAfter = function (move) {
        for (var i in this.movements) {
            if (this.movements[i] === move) {
                if (this.movements[i + 1]) {
                    return this.movements[i + 1];
                }
                return null;
            }
        }

        return null;
    };

    ShipIcon.prototype.showWeaponArc = function (ship, weapon) {

        var hexDistance = window.coordinateConverter.getHexDistance();
        var dis = weapon.rangePenalty === 0 ? hexDistance * weapon.range : 50 / weapon.rangePenalty * hexDistance;
        var arcs = shipManager.systems.getArcs(ship, weapon);

        var arcLenght = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
        var arcStart = mathlib.addToDirection(0, arcLenght * -0.5);
        var arcFacing = mathlib.addToDirection(arcs.end, arcLenght * -0.5);

        var geometry = new THREE.CircleGeometry(dis, 32, mathlib.degreeToRadian(arcStart), mathlib.degreeToRadian(arcLenght));
        var material = new THREE.MeshBasicMaterial({ color: new THREE.Color("rgb(20,80,128)"), opacity: 0.5, transparent: true });
        var circle = new THREE.Mesh(geometry, material);
        circle.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));
        circle.position.z = -1;
        this.mesh.add(circle);
        this.weaponArcs.push(circle);

        return null;
    };

    ShipIcon.prototype.hideWeaponArcs = function () {
        this.weaponArcs.forEach(function (arc) {
            this.mesh.remove(arc);
        }, this);
    };


    ShipIcon.prototype.showBDEW = function () {

        var BDEW = ew.getBDEW(this.ship);
        if (!BDEW || this.BDEWSprite){
            return;
        }

        var hexDistance = window.coordinateConverter.getHexDistance();
        var dis = 20 * hexDistance;

        var color = gamedata.isMyShip(this.ship) ? new THREE.Color(160 / 255, 250 / 255, 100 / 255) : new THREE.Color(255 / 255,  157 / 255, 0 / 255);

        var geometry = new THREE.CircleGeometry(dis, 64, 0);
        var material = new THREE.MeshBasicMaterial({ color: color, opacity: 0.2, transparent: true });
        var circle = new THREE.Mesh(geometry, material);
        circle.position.z = -1;
        this.mesh.add(circle);
        this.BDEWSprite = circle;

        return null;
    };

    ShipIcon.prototype.hideBDEW = function () {
        this.mesh.remove(this.BDEWSprite);
        this.BDEWSprite = null;
    };

    ShipIcon.prototype.positionAndFaceIcon = function (offset) {
        var movement = this.getLastMovement();
        var gamePosition = window.coordinateConverter.fromHexToGame(movement.position);

        if (offset) {
            gamePosition.x += offset.x;
            gamePosition.y += offset.y;
        }

        var facing = mathlib.hexFacingToAngle(movement.facing);
        var heading = mathlib.hexFacingToAngle(movement.heading);

        this.setPosition(gamePosition);
        this.setFacing(-facing);
        this.setHeading(-heading);

    };

    return ShipIcon;
}();