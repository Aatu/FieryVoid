class ShipObject {

    constructor(ship, scene) {

        this.shipId = ship.id;
        this.ship = ship;
        this.scene = scene;
        this.mesh = new THREE.Object3D()
        this.shipObject = null;
        this.weaponArcs = []
        this.shipSideSprite = null;
        this.line = null;

        this.defaultHeight = 50;
        this.mine = gamedata.isMyOrTeamOneShip(ship);
        this.sideSpriteSize = 100;
        this.position = {x: 0, y: 0, z: 0}

        this.movements = null;

        this.hidden = false;
    

        this.startRotation = {x: 0, y: 0, z: 0}  
        this.rotation = {x: 0, y: 0, z: 0}

        console.log("ship", this.ship)
        this.consumeShipdata(this.ship);  
    }

    consumeShipdata(ship) {
        this.ship = ship;
        this.consumeMovement(ship.movement);
        this.consumeEW(ship);
    }

    createMesh() {
        
        if (this.position.z === 0) {
            this.position.z = this.defaultHeight;
        }

        const opacity = 0.5;
        this.line = new window.LineSprite({x:0, y:0, z:1}, {x:0, y:0, z:this.position.z}, 1, new THREE.Color(0, 1, 0), opacity)
        this.mesh.add(this.line.mesh)


        this.shipSideSprite = new window.ShipSelectedSprite({ width:  this.sideSpriteSize, height:  this.sideSpriteSize}, 0.01, opacity);
        this.shipSideSprite.setOverlayColor(new THREE.Color(0, 1, 0))
        this.shipSideSprite.setOverlayColorAlpha(1)
        this.mesh.add(this.shipSideSprite.mesh);

        this.mesh.name = "ship";
        this.mesh.userData = {icon: this};
        this.scene.add(this.mesh)
    }

    create() {
        this.createMesh()
    }

    setPosition(x, y, z = this.defaultHeight){
        if (typeof x === "object") {
            z = x.z || this.defaultHeight;
            y = x.y;
            x = x.x;
        }

        this.position = {x, y, z};

        if (this.mesh) {
            this.mesh.position.set(x, y, 0)
        }

        if (this.shipObject) {
            this.shipObject.position.set(0, 0, z)
        }
    }

    getPosition() {
        return this.position;
    }
    
    setRotation(x, y, z){
        this.rotation = {x, y, z}

        if (this.shipObject) {
            this.shipObject.rotation.set(mathlib.degreeToRadian(x + this.startRotation.x), mathlib.degreeToRadian(y + this.startRotation.y), mathlib.degreeToRadian(z + this.startRotation.z));
        }
    }

    getRotation(x, y, z){
        return this.rotation.z;
    }

    setOpacity(opacity) {
        
    };

    hide() {
        if (this.hidden) {
            return;
        }
        
        this.scene.remove(this.mesh);
        this.hidden = true;
    };

    show () {
        if (!this.hidden) {
            return;
        }

        this.scene.add(this.mesh);
        this.hidden = false;
    };

    getFacing(facing) {
        return this.getRotation().y
    };

    setFacing(facing) {
        this.setRotation(0, facing, 0)
    };

    setOverlayColorAlpha(alpha) {
    };

    getMovements(turn) {
        return this.movements.filter(function (movement) {
            return turn === undefined || movement.turn === turn;
        }, this);
    };

    setScale(width, height) {
        //console.log("ShipObject.setScale is not yet implemented")
        //console.trace();
    };

   consumeEW(ship) {

        if (!this.shipEWSprite) {
            return
        }

        let dew = ew.getDefensiveEW(ship);
        if (ship.flight) {
            dew = shipManager.movement.getJinking(ship);
        }

        const ccew = ew.getCCEW(ship);

        this.shipEWSprite.update(dew, ccew);
    };

    showEW() {
        if (this.shipEWSprite) {
            this.shipEWSprite.show();
        }
    };

    hideEW() {
        if (this.shipEWSprite) {
            this.shipEWSprite.hide();
        }
    };

    showSideSprite(value) {
        //console.log("ShipObject.showSideSprite is not yet implemented")
    };

    setHighlighted(value) {
        //console.log("ShipObject.showSideSprite is not yet implemented")
    };

    setSelected(value) {
        //console.log("ShipObject.showSideSprite is not yet implemented")
    };

    setNotMoved(value) {
        //console.log("ShipObject.showSideSprite is not yet implemented")
    };

    
    consumeMovement(movements) {

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
                    this.addMovementToRegistry(movesByHexAndTurn, {
                        turn: movement.turn,
                        facing: movement.facing,
                        heading: movement.heading,
                        position: new hexagon.Offset(lastMovement.position),
                        oldFacings: [],
                        oldHeadings: []
                    });
                }
            }

            this.addMovementToRegistry(movesByHexAndTurn, movement);

            lastMovement = movement;
        }, this);

        this.movements = movesByHexAndTurn;
    }

    addMovementToRegistry(movesByHexAndTurn, movement){

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

    movesEqual(move1, move2) {
        return move1.turn === move2.turn && move1.position.equals(move2.position); // &&
    }

    getLastMovement() {
        if (this.movements.length === 0) {
            return this.defaultPosition;
        }

        return this.movements[this.movements.length - 1];
    }

    getFirstMovementOnTurn(turn, ignore) {
        var movement = this.movements.filter(function (move) {
            return move.turn === turn;
        }).shift();

        if (!movement) {
            return this.getLastMovement();
        }

        return movement;
    }

    getMovementBefore(move) {
        for (var i in this.movements) {
            if (this.movements[i] === move) {
                return this.movements[i - 1];
            }
        }

        return null;
    }

    getMovementAfter(move) {
        for (var i in this.movements) {
            if (this.movements[i] === move) {
                if (this.movements[i + 1]) {
                    return this.movements[i + 1];
                }
                return null;
            }
        }

        return null;
    }

    showWeaponArc(ship, weapon) {

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
    }

    hideWeaponArcs() {
        this.weaponArcs.forEach(function (arc) {
            this.mesh.remove(arc);
        }, this);
    }

    showBDEW() {

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
    }

    hideBDEW() {
        this.mesh.remove(this.BDEWSprite);
        this.BDEWSprite = null;
    }

    positionAndFaceIcon(offset) {
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
    }

}

window.ShipObject = ShipObject;

export default ShipObject