'use strict';

window.ShipIcon = function () {

    var directionOfMovementTexture = new THREE.TextureLoader().load(window.AssetManager.getSmartImagePath('./img/directionOfMovement.png'));
    directionOfMovementTexture.colorSpace = THREE.SRGBColorSpace;
    directionOfMovementTexture.colorSpace = THREE.SRGBColorSpace;
    var directionOfProwTexture = new THREE.TextureLoader().load(window.AssetManager.getSmartImagePath('./img/directionOfProw.png'));
    directionOfProwTexture.colorSpace = THREE.SRGBColorSpace;
    directionOfProwTexture.colorSpace = THREE.SRGBColorSpace;
    const THRUSTER_TEXTURE = new THREE.TextureLoader().load(window.AssetManager.getSmartImagePath("./img/systemicons/thrusterICON1.png"));
    THRUSTER_TEXTURE.colorSpace = THREE.SRGBColorSpace;
    THRUSTER_TEXTURE.colorSpace = THREE.SRGBColorSpace;

    function ShipIcon(ship, scene) {

        this.shipId = ship.id;
        this.shipName = ship.name;
        this.imagePath = ship.imagePath;
        this.ship = ship;
        this.movements = null;
        this.preFireMovements = [];
        this.defaultPosition = null;
        this.mesh = null;
        this.size = ship.canvasSize;
        //this.mine = gamedata.isMyOrTeamOneShip(ship); //Old, singular variable.
        this.mine = gamedata.isMyShip(ship);
        this.ally = gamedata.isMyorMyTeamShip(ship);
        this.terrain = gamedata.isTerrain(ship.shipSizeClass, ship.userid);
        this.scene = scene;
        this.shipSprite = null;
        this.shipEWSprite = null;
        this.ShipSelectedSprite = null;
        this.ShipSideSprite = null;
        this.shipDirectionOfMovementSprite = null;
        this.shipDirectionOfProwSprite = null;
        this.weaponArcs = [];
        this.structureArcs = []; //structure facing wedges (own array so they never fight the weapon arcs)
        this.hidden = false;
        this.BDEWSprite = null;
        this.MDEWSprite = null;
        this.shipHexagonSpritesMap = new Map();
        this.NotMovedSprite = null;

        this.selected = false;
        this.baseZ = this.terrain ? -50 : 0;

        this.create(ship, scene);
        this.consumeShipdata(ship);
    }

    ShipIcon.prototype.consumeShipdata = function (ship) {
        this.ship = ship;
        this.consumeMovement(ship.movement);
        this.consumeEW(ship);
        //STAGE4-RETIRED this.createShipWindow(ship);
    };

    /* STAGE4-RETIRED legacy status-window re-link (the legacy window DOM no longer
       exists on any page). Delete once the redesign is stable on live.
    ShipIcon.prototype.createShipWindow = function (ship) {
        // Lazy: build the (expensive) legacy DOM status window only when it is first
        // opened (shipWindowManager.open / ensureShipWindow). At load and on each turn
        // refresh we only re-link to an already-built window — if a ship's window has
        // never been opened, ship.shipStatusWindow stays null and setData no-ops.
        // This removes the per-ship DOM build that blocked first paint in large games.
        var element = jQuery(".shipwindow.ship_" + ship.id);

        if (element.length) {
            ship.shipStatusWindow = element;
            shipWindowManager.setData(ship);
        } else {
            ship.shipStatusWindow = null;
        }
    };
    */

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

    //shouldn't use provided heading as it's GET method
    ShipIcon.prototype.getFacing = function (facing) {
        var facingActual = this.shipSprite.mesh.rotation.z;
        if (!this.terrain) this.shipDirectionOfProwSprite.mesh.rotation.z = facingActual;
        return mathlib.radianToDegree(facingActual);
    };

    ShipIcon.prototype.setFacing = function (facing) {

        var facingActual = mathlib.degreeToRadian(facing);
        if (!this.terrain) this.shipDirectionOfProwSprite.mesh.rotation.z = facingActual;  //No sprite for Terrain 
        this.shipSprite.mesh.rotation.z = facingActual;//mathlib.degreeToRadian(facing);

    };

    ShipIcon.prototype.setHeading = function (heading) {
        if (!this.terrain) { //No sprite for Terrain  
            this.shipDirectionOfMovementSprite.mesh.rotation.z = mathlib.degreeToRadian(heading);
        }
    };

    //this function is never used actually... and certainly shouldn't use provided heading as it's GET method
    ShipIcon.prototype.getHeading = function (heading) {
        this.shipDirectionOfMovementSprite.mesh.rotation.z = mathlib.degreeToRadian(heading);
    };

    ShipIcon.prototype.setOverlayColorAlpha = function (alpha) {
        this.shipSprite.setOverlayColorAlpha(alpha);
    };
    //No longer called 
    /*
    ShipIcon.prototype.getMovements = function (turn) {
        return this.movements.filter(function (movement) {
            return turn === undefined || movement.turn === turn;
        }, this);
    };
    */

    //New function to fix pivot facing bug in Replay 
    ShipIcon.prototype.getMovementsReplay = function (turn) {
        if (turn === undefined) {
            return this.movements; // fallback: return everything
        }

        // movements from the requested turn
        let currentTurnMoves = this.movements.filter(m => m.turn === turn);

        // find the last movement from the previous turn
        let prevTurnMoves = this.movements.filter(m => m.turn === turn - 1);
        let lastPrevMove = prevTurnMoves.length > 0 ? prevTurnMoves[prevTurnMoves.length - 1] : null;

        // return combined
        if (lastPrevMove) {
            return [lastPrevMove, ...currentTurnMoves];
        }
        return currentTurnMoves;
    };


    /* Below zoom 0.5 the zoom handler shrinks the whole icon (ShipIconContainer.applyZoomToIcon) so
       ships don't balloon on screen when you zoom right in. Anything measured in GRID units rather
       than icon units has to sit that out, or it silently lies about how far it reaches: a weapon's
       10-hex arc drew as 4 hexes at full zoom-in, and the straight-arc hex highlights stopped lining
       up with the grid they are drawn on top of.

       Such children are flagged with a gridLockedOffset (their offset from the icon in game units)
       and get the icon's scale cancelled back out here. The offset is divided too: a child's local
       position is multiplied by the parent's scale just as its geometry is. Routed through setScale
       because that is the single funnel for the icon's scale, so the correction can never go stale
       while an overlay is on screen and the player zooms.

       Anything sized in ICON units stays out of this - the thruster icons and the direction arrows
       are part of the ship's iconography and are meant to shrink with it. */
    function normaliseGridLockedChildren(mesh) {
        var scaleX = mesh.scale.x || 1;
        var scaleY = mesh.scale.y || 1;

        mesh.children.forEach(function (child) {
            var offset = child.userData.gridLockedOffset;

            if (!offset) return;

            child.scale.set(1 / scaleX, 1 / scaleY, 1);
            child.position.x = offset.x / scaleX;
            child.position.y = offset.y / scaleY;
        });
    }

    /* Add an overlay whose size means something in hexes, so it holds that size however the icon is
       rescaled. offset is the overlay's position relative to the icon in game units - omit it for
       "centred on the icon", which is what every range overlay wants. */
    function addGridLockedOverlay(mesh, overlay, offset) {
        overlay.userData.gridLockedOffset = offset || { x: 0, y: 0 };
        mesh.add(overlay);
        normaliseGridLockedChildren(mesh);
    }

    ShipIcon.prototype.setScale = function (width, height) {
        this.mesh.scale.set(width, height, 1);
        normaliseGridLockedChildren(this.mesh);
    };

    ShipIcon.prototype.consumeEW = function (ship) {
        var dew = ew.getDefensiveEW(ship);
        //if (ship.flight) {
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
            this.mesh.position.z = this.baseZ + (this.terrain ? 10 : 499);
            if (!this.terrain) { //No sprite for Terrain  
                this.shipDirectionOfProwSprite.show();
                this.shipDirectionOfMovementSprite.show();
            }
        } else {
            if (this.selected) {
                this.mesh.position.z = this.baseZ + (this.terrain ? 5 : 100);
            } else {
                this.mesh.position.z = this.baseZ;
            }

            // On mobile, if selected, don't hide sprites
            if (window.matchMedia("(pointer: coarse)").matches && this.selected && !this.terrain) {
                this.shipDirectionOfProwSprite.show();
                this.shipDirectionOfMovementSprite.show();
            } else if (!this.terrain) { //No sprite for Terrain  
                this.shipDirectionOfProwSprite.hide();
                this.shipDirectionOfMovementSprite.hide();
            }
        }
        //this.selected = value; //Removed when I added code for mobile browsers to show direction sprites.        
    };

    ShipIcon.prototype.setSelected = function (value, showMobileSprites) {
        if (!this.terrain) { // Don't show selection circle for terrain.
            if (value) {
                this.ShipSelectedSprite.show();
                if (!this.selected) {
                    this.mesh.position.z = this.baseZ + (this.terrain ? 5 : 100);
                }
            } else {
                if (this.selected) {
                    this.mesh.position.z = this.baseZ;
                }
                this.ShipSelectedSprite.hide();
            }

            // Mobile/Tablet specific logic: Show direction sprites on selection since there is no hover
            if (window.matchMedia("(pointer: coarse)").matches) {
                if (value && showMobileSprites === true) {
                    this.shipDirectionOfProwSprite.show();
                    this.shipDirectionOfMovementSprite.show();
                } else {
                    this.shipDirectionOfProwSprite.hide();
                    this.shipDirectionOfMovementSprite.hide();
                }
            }
        }
        this.selected = value;
    };

    ShipIcon.prototype.setNotMoved = function (value) {
        if (!this.terrain) {
            if (value) {
                this.NotMovedSprite.show();
            } else {
                this.NotMovedSprite.hide();
            }
        }
        // NOTE: deliberately does NOT touch this.selected. "Not moved" is a
        // movement-phase status flag, not selection. Setting this.selected here
        // falsely marked every un-moved ship as selected, so on mobile
        // (pointer: coarse) the setHighlighted(false) path triggered by tapping
        // an empty hex would reveal all their facing/heading arrows at once.
    };

    // Selection/side circle args, matching getShipOverlayColor. 2-team
    // participants get the friend/foe type; observers AND 3+-team participants
    // get a per-team key + colour so the filled side circle matches the
    // team-coloured ship overlay. Terrain is unaffected.
    ShipIcon.prototype.getSideSpriteArgs = function (ship) {
        if (this.terrain) {
            return { type: 'terrain', teamColor: null };
        }

        if (!gamedata.isPlayerInGame() || gamedata.getDistinctTeamCount() !== 2) {
            return { type: 'team' + ship.team, teamColor: gamedata.getTeamColorRGB(ship.team) };
        }

        return {
            type: this.mine ? 'mine' : (this.ally ? 'ally' : 'enemy'),
            teamColor: null
        };
    };

    ShipIcon.prototype.create = function (ship, scene) {
        var imagePath = ship.imagePath;
        this.mesh = new THREE.Object3D();
        this.mesh.position.set(500, 0, this.baseZ);
        this.mesh.renderDepth = 10;

        // Defined a maximum width and height, some new ships like Thirdspace are MUCH larger and benefit from this - DK 25.3.24
        var maxWidth = 250;
        var maxHeight = 250;

        var spriteWidthDirection = Math.min(this.size / 1.5, maxWidth - 25);
        var spriteHeightDirection = Math.min(this.size / 1.5, maxHeight - 25);
        if (!this.terrain) { //No sprite for Terrain
            this.shipDirectionOfProwSprite = new window.webglSprite('./img/directionOfProw.png', { width: spriteWidthDirection, height: spriteHeightDirection }, -2);
            this.mesh.add(this.shipDirectionOfProwSprite.mesh);
            this.shipDirectionOfProwSprite.hide();

            this.shipDirectionOfMovementSprite = new window.webglSprite('./img/directionOfMovement.png', { width: spriteWidthDirection, height: spriteHeightDirection }, -2);
            this.mesh.add(this.shipDirectionOfMovementSprite.mesh);
            this.shipDirectionOfMovementSprite.hide();
        }
        this.shipSprite = new window.webglSprite(imagePath, { width: this.size / 2, height: this.size / 2 }, 1);

        this.shipSprite.setOverlayColor(
            gamedata.getShipOverlayColor(ship, this.mine, this.ally, this.terrain)
        );

        //if (ship.imageFlipped) { //Old variable used to manually flip iamges in older version of THREE.js - DK
        //    this.shipSprite.mesh.scale.y = -1;
        //}

        this.mesh.add(this.shipSprite.mesh);

        //29.03.2022: people called for more visible circles - change from the same as ship image to half again as large (original: this.size / 2, new: this.size*0.75 ); unit icon and arrows size left as previously
        var spriteWidth = Math.min(this.size * 0.75, maxWidth);
        var spriteHeight = Math.min(this.size * 0.75, maxHeight);

        this.shipEWSprite = new window.ShipEWSprite({ width: spriteWidth, height: spriteHeight }, -2);
        this.mesh.add(this.shipEWSprite.mesh);
        this.shipEWSprite.hide();

        var sideArgs = this.getSideSpriteArgs(ship);

        //teamColor goes to BOTH sprites: the selection ring is shown for every ship
        //active in the current simultaneous-movement bracket, not just a ship the
        //viewer can select, so it needs the per-team colour as much as the side
        //circle does. Passing null here left 'teamN' unmatched in chooseTexture and
        //collapsed every ring to the neutral orange one.
        this.ShipSelectedSprite = new window.ShipSelectedSprite(
            { width: spriteWidth, height: spriteHeight },
            -2,
            sideArgs.type,
            true,
            sideArgs.teamColor
        ).hide();
        this.mesh.add(this.ShipSelectedSprite.mesh);

        this.ShipSideSprite = new window.ShipSelectedSprite(
            { width: spriteWidth, height: spriteHeight },
            -2,
            sideArgs.type,
            false,
            sideArgs.teamColor
        ).hide();
        this.mesh.add(this.ShipSideSprite.mesh);

        this.NotMovedSprite = new window.ShipSelectedSprite({ width: spriteWidth, height: spriteHeight }, -2, 'neutral', false).hide();
        this.mesh.add(this.NotMovedSprite.mesh);

        scene.add(this.mesh);
    };


    ShipIcon.prototype.consumeMovement = function (movements) {

        var movesByHexAndTurn = [];

        /* Defensive normalisation. PHP's json_encode emits a JSON OBJECT rather than an
           array whenever the server-side movement array has gaps in its integer keys -
           i.e. any unset() that wasn't followed by array_values() (TacGamedata's hide*
           helpers strip an enemy's in-progress moves while keeping start/deploy markers
           and the Gravitic Augmenter's transient forced jink, which sits LAST). An object
           has no .filter, so a single affected ship used to throw out of createIcon and
           abort setShips' whole loop - the board came up empty. Object.values() walks
           integer-like keys in ascending order, so the move order is preserved and a
           server slip degrades to a correctly ordered array instead of a dead board.
           Both branches warn: this should never happen, and we want it in the console. */
        if (movements && !Array.isArray(movements) && typeof movements === 'object') {
            console.warn('ShipIcon.consumeMovement: ship ' + this.shipId +
                ' received movement as an object (gappy server array?); coercing.', movements);

            var normalised = Object.values(movements);
            // Also repair the shared gamedata ship, otherwise every array-only consumer
            // (shipManager.movement's .length/.splice, ajaxInterface.construcGamedata,
            // the attached-pod mirroring in PhaseStrategy) still trips over the object.
            if (this.ship && this.ship.movement === movements) {
                this.ship.movement = normalised;
            }
            movements = normalised;
        }

        if (!Array.isArray(movements)) {
            if (movements !== undefined && movements !== null) {
                console.warn('ShipIcon.consumeMovement: ship ' + this.shipId +
                    ' received unusable movement; treating as empty.', movements);
            }
            movements = [];
        }

        if (movements.length > 0 && movements[0]) {
            this.defaultPosition = {
                turn: movements[0].turn,
                facing: movements[0].facing,
                heading: movements[0].heading,
                position: new hexagon.Offset(movements[0].position),
                offset: { x: movements[0].xOffset || 0, y: movements[0].yOffset || 0 }
            };
        }

        var lastMovement = null;

        /*movements.filter(function (movement) { //This seemed to cause issues when I added Deployment Zones outside Turn 1 - DK
            return movement.type !== 'start';
        }).filter(function (movement) {
            return movement.commit;
        }).forEach(function (movement) { */

        //Replacement code below
        movements.filter(function (movement) {
            return movement.type !== 'start';
        })
            // During replay, exclude preFire moves from the main movement path – they will
            // be animated separately in ReplayAnimationStrategy, after the relevant weapon hits.
            .filter(function (movement) {
                return !(gamedata.replay && movement.type === 'prefire' && movement.turn === gamedata.turn);
            })
            .filter(function (movement) {
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

        this.preFireMovements = []; //reset
        movements.filter(function (m) { return m.type === 'prefire'; })
            .forEach(function (m) {
                if (!this.preFireMovements.some(existing => existing.id === m.id)) {
                    if (m.turn == gamedata.turn) this.preFireMovements.push(m);
                }
            }, this);


        this.movements = movesByHexAndTurn;
    };

    function addMovementToRegistry(movesByHexAndTurn, movement) {

        var getPreviousMatchingMove = function (moves, move) {
            var previousMove = moves[moves.length - 1];
            if (!previousMove) {
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
            saved.type = movement.type;
            saved.value = movement.value;

            saved.position = new hexagon.Offset(movement.position);
        } else if (!gamedata.replay) {
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
        } else {
            movesByHexAndTurn.push({
                //id: movement.id,
                type: movement.type, //use type for Replays, is really helpful for animations
                value: movement.value, //host ship ID for attached/detach orders
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

    ShipIcon.prototype.getEndMovementOnTurn = function (turn) {
        // Filter movements on the given turn that are of type 'end'
        var movementsOnTurn = this.movements.filter(function (move) {
            return move.turn === turn && move.type === 'end';
        });

        // Get the last one
        var lastMovement = movementsOnTurn.pop();

        // If none found, fallback to the last movement overall
        if (!lastMovement) {
            return this.getLastMovement();
        }

        return lastMovement;
    };

    ShipIcon.prototype.getLastMovementOnTurn = function (turn, ignore) {
        var movement = this.movements.filter(function (move) {
            return move.turn === turn;
        }).pop(); // Use pop() to get the last element in the filtered array

        if (!movement) {
            return this.getLastMovement(); // Fallback to the last movement overall
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


    /* Overlap of two circular arcs, or null if they don't overlap. Used to combine a jammed turret's
       restricted arc (ReducedArcs critical) with its firing-link wedge - a weapon under both may only
       bear where the two agree.

       Assumes the two arcs together span less than 360 degrees, which is what makes the result a
       single contiguous piece: two arcs can only overlap at BOTH ends (two separate pieces) when
       their lengths sum past a full circle. Every mount this is used for is far below that (a 60
       degree jam arc plus a 120 degree spread wedge), so the single-piece result is exact. */
    function intersectArcs(a, b) {
        var start = mathlib.isInArc(b.start, a.start, a.end) ? b.start
                  : (mathlib.isInArc(a.start, b.start, b.end) ? a.start : null);
        var end = mathlib.isInArc(b.end, a.start, a.end) ? b.end
                : (mathlib.isInArc(a.end, b.start, b.end) ? a.end : null);

        if (start === null || end === null) return null;
        return { start: start, end: end };
    }

    ShipIcon.prototype.showWeaponArc = function (ship, weapon) {
        if (!(weapon instanceof Weapon) && !(weapon instanceof Thruster) && !(weapon instanceof Shield)) return null; // Only show arcs for weapons
        if(weapon.stowed && weapon.stowedArcStart == null) return null; //stowed weapon with no stowed arc (Kirishiac Orbital docked) - non-operational, no arc to show. A stowed arc set (Heavy Orbital) keeps the weapon live: draw its current (reduced) arc.

        var hexDistance = window.coordinateConverter.getHexDistance();

        if (weapon instanceof Thruster) {

            this.showThrusterIcon(ship, weapon); //Creates small thruster icon on relevant side of ship on hover over system.

        } else if (weapon.shootsStraight) { //Some weapons can only fire in straight lines e.g. Transverse Drive.  Show rectangular arcs along hex lines instead.
            var arcs = shipManager.systems.getArcs(ship, weapon);
            this.showStraightArcs(weapon, hexDistance, arcs);

        } else if (weapon.splitArcs) { //Some weapons might have two separate arcs, like Shadow Battlecruiser.
            var dis = weapon.rangePenalty === 0 ? hexDistance * weapon.range : 50 / weapon.rangePenalty * hexDistance;
            if (isNaN(dis) || !isFinite(dis)) dis = hexDistance; // Fallback for non-weapon systems without rangePenalty
            if (weapon.range > 0 && dis > hexDistance * weapon.range) dis = hexDistance * weapon.range;
            var allArcs = shipManager.systems.getMultipleArcs(ship, weapon);

            for (const arcs of allArcs) {
                var arcLength = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
                var arcStart = mathlib.addToDirection(0, arcLength * -0.5);
                var arcFacing = mathlib.addToDirection(arcs.end, arcLength * -0.5);
                var geometry = new THREE.CircleGeometry(dis, 32, mathlib.degreeToRadian(arcStart), mathlib.degreeToRadian(arcLength));
                var material = new THREE.MeshBasicMaterial({ color: new THREE.Color("rgb(20,80,128)"), opacity: 0.5, transparent: true });
                var circle = new THREE.Mesh(geometry, material);
                circle.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));
                circle.position.z = -1;
                addGridLockedOverlay(this.mesh, circle); //radius is a hex range - hold it against the icon's zoom rescale
                this.weaponArcs.push(circle);
            }

        } else { //Normal weapons with circular weapon arcs
            var dis = weapon.rangePenalty === 0 ? hexDistance * weapon.range : 50 / weapon.rangePenalty * hexDistance;
            if (isNaN(dis) || !isFinite(dis)) dis = hexDistance; // Fallback for non-weapon systems without rangePenalty
            if (weapon.range > 0 && dis > hexDistance * weapon.range) dis = hexDistance * weapon.range;
            var arcs = shipManager.systems.getArcs(ship, weapon);
            var arcColour = "rgb(20,80,128)";

            //Firing-link reduced arc (e.g. Vree turret): if this weapon shares an angular-spread
            //group and any member has declared fire this turn (a sibling, OR this weapon itself once
            //its own order is locked), it can now only bear within linkedFiringSpread degrees of that
            //target. Draw that reduced wedge in a distinct amber colour so the restriction is visible
            //on hover. The wedge is centred on the target's bearing and expressed in the same
            //ship-frame as getArcs(), so the existing rotation maths below renders it correctly.
            //
            //A weapon can be under BOTH restrictions at once: a turret that has JAMMED (ReducedArcs
            //critical - the server sends the reduced startArc/endArc) is no longer a full circle, and
            //the link wedge still applies on top. Then only the OVERLAP can actually be fired into, so
            //that is what gets drawn - and an empty overlap means the weapon cannot bear at all this
            //turn, so no arc is shown. (For the Vree numbers the jam arc is 60 degrees and the spread
            //is 60, so the overlap is always the whole jam arc; the intersection matters for any
            //future mount whose restricted arc is wider than its spread.)
            var baseArcLength = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
            if (weapon.linkedFiringSpread != null) {
                //A bearing, not a ship - the committed order may be on a HEX (Shredder mode 1).
                var centreBearing = weaponManager.getLinkedGroupDeclaredBearing(ship, weapon);
                if (centreBearing !== null) {
                    var spread = weapon.linkedFiringSpread;
                    var centreRel = mathlib.addToDirection(centreBearing, -this.getFacing());
                    var wedge = { start: mathlib.addToDirection(centreRel, -spread), end: mathlib.addToDirection(centreRel, spread) };
                    arcs = baseArcLength >= 360 ? wedge : intersectArcs(arcs, wedge);
                    if (!arcs) return null; //restricted arc and link wedge don't overlap - nothing can be fired at
                    arcColour = "rgb(170,95,25)"; //amber: reduced (linked) arc
                }
            }

            var arcLength = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
            var arcStart = mathlib.addToDirection(0, arcLength * -0.5);
            var arcFacing = mathlib.addToDirection(arcs.end, arcLength * -0.5);

            var geometry = new THREE.CircleGeometry(dis, 32, mathlib.degreeToRadian(arcStart), mathlib.degreeToRadian(arcLength));
            var material = new THREE.MeshBasicMaterial({ color: new THREE.Color(arcColour), opacity: 0.5, transparent: true });
            var circle = new THREE.Mesh(geometry, material);
            circle.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));
            circle.position.z = -1;
            addGridLockedOverlay(this.mesh, circle); //radius is a hex range - hold it against the icon's zoom rescale
            this.weaponArcs.push(circle);

        }

        return null;
    };

    ShipIcon.prototype.showThrusterIcon = function (ship, weapon) {
        var graphicSize = 32;
        var geometry = new THREE.PlaneGeometry(graphicSize, graphicSize);
        var material = new THREE.MeshBasicMaterial({
            map: THRUSTER_TEXTURE, // Use the preloaded texture
            transparent: true,
            opacity: 0.7
        });

        var meshGraphic = new THREE.Mesh(geometry, material);

        var shipFacing = this.getFacing();
        var offsetDistance = 80;
        var offsetX = 0;
        var offsetY = 0;
        var rolled = shipManager.movement.isRolled(ship);
        var rollAdd = rolled ? 180 : 0;

        switch (weapon.direction) {
            case 1:
                offsetX = Math.cos(mathlib.degreeToRadian(shipFacing)) * offsetDistance;
                offsetY = Math.sin(mathlib.degreeToRadian(shipFacing)) * offsetDistance;
                break;
            case 2:
                offsetX = Math.cos(mathlib.degreeToRadian(shipFacing + 180)) * offsetDistance;
                offsetY = Math.sin(mathlib.degreeToRadian(shipFacing + 180)) * offsetDistance;
                break;
            case 3:
                offsetX = Math.cos(mathlib.degreeToRadian(shipFacing + 90 + rollAdd)) * offsetDistance;
                offsetY = Math.sin(mathlib.degreeToRadian(shipFacing + 90 + rollAdd)) * offsetDistance;
                break;
            case 4:
                offsetX = Math.cos(mathlib.degreeToRadian(shipFacing + 270 + rollAdd)) * offsetDistance;
                offsetY = Math.sin(mathlib.degreeToRadian(shipFacing + 270 + rollAdd)) * offsetDistance;
                break;
            default:
                offsetX = 0;
                offsetY = 0;
        }

        meshGraphic.position.set(offsetX, offsetY, 1);

        var arcs = shipManager.systems.getArcs(ship, weapon);
        var arcLength = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
        var arcFacing = mathlib.addToDirection(arcs.end, arcLength * -0.5);
        meshGraphic.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));

        this.mesh.add(meshGraphic);
        this.weaponArcs.push(meshGraphic);
    };


    /* Corners of a pointy-top hex as unit offsets from its centre - the same 30/90/.../330 degree
       corners mathlib.getHexCorners uses to build the grid, so a hexagon drawn from these lands
       exactly on a grid hex.

       The grid-aligned overlays (the EW blankets, the gravitic target area) use them as they are,
       on an unrotated mesh. The weapon arcs emit them in the icon's LOCAL space and turn the
       finished mesh to the ship's facing, which is safe without any per-facing correction because a
       pointy-top hexagon maps onto itself under any 60 degree rotation and a ship's facing is always
       a multiple of 60 (mathlib.hexFacingToAngle) - the hexes stay grid-aligned however the ship is
       pointing. */
    var HEX_CORNERS = [30, 90, 150, 210, 270, 330].map(function (degrees) {
        var radians = degrees * Math.PI / 180;
        return { x: Math.cos(radians), y: Math.sin(radians) };
    });

    //Shrink each highlighted hex slightly so the hex outlines read individually instead of merging
    //into one solid beam. 1 = hexes touch edge to edge, which is what the cobalt border below is for.
    //var STRAIGHT_ARC_HEX_INSET = 0.94;
    var STRAIGHT_ARC_HEX_INSET = 1;

    var STRAIGHT_ARC_FILL_COLOUR = 0x11446e;
    var STRAIGHT_ARC_BORDER_COLOUR = 0x08485e; //cyan, brighter than the fill so the hex edges carry

    /* Centres of every hex within `range` steps of the origin hex, the origin included, as offsets
       in game units. Both lattice vectors are real grid neighbour steps - direction 0 (due East) and
       direction 1 - so the centres land exactly on grid hexes however far out they run: checked
       against coordinateConverter.fromHexToGame over both row parities out to range 20, worst error
       5e-13 game units.

       Cube coordinates in disguise: a steps in direction 0 plus b steps in direction 1 is the cube
       (a+b, -a, -b), whose distance from the origin is max(|a+b|, |a|, |b|). Clamping b to
       [-range-a, range-a] as well as to [-range, range] is therefore exactly "within range", and
       the count comes out at the expected 3*range*(range+1) + 1.

       Offsets, not hexes, so no hexagon.Offset/Cube objects are built - a 20-hex BDEW blanket is
       1261 of them. */
    function hexCentresWithin(range, hexDistance) {
        var centres = [];
        //bearings are clockwise, game space is counter-clockwise, so direction 1 sits at math angle -60
        var stepX = hexDistance * 0.5;
        var stepY = -hexDistance * Math.sqrt(3) / 2;

        for (var a = -range; a <= range; a++) {
            var from = Math.max(-range, -a - range);
            var to = Math.min(range, -a + range);

            for (var b = from; b <= to; b++) {
                centres.push({ x: a * hexDistance + b * stepX, y: b * stepY });
            }
        }

        return centres;
    }

    /* A whole set of hexes as ONE indexed mesh: six vertices and four triangles each (a hexagon is
       convex, so a fan from one corner covers it), all in a single buffer - one draw call whether
       that is a three-hex arc arm or the 1261-hex BDEW blanket.

       Built directly rather than through a THREE.Shape per hex + ShapeGeometry: the triangulation
       of a hexagon is known, so there is nothing for a triangulator to work out, and the blanket is
       built for every EW-showing ship at once. Indexing shares each corner between the triangles
       that meet on it, which keeps the buffer to a third of the size an unindexed fan would need.

       Distinct hexes never overlap, so the alpha needs no special handling. */
    function buildHexFill(centres, radius, material) {
        var positions = [];
        var indices = [];

        centres.forEach(function (centre, hex) {
            var base = hex * 6;

            HEX_CORNERS.forEach(function (corner) {
                positions.push(centre.x + corner.x * radius, centre.y + corner.y * radius, 0);
            });

            for (var corner = 1; corner < 5; corner++) {
                indices.push(base, base + corner, base + corner + 1);
            }
        });

        var geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
        geometry.setIndex(indices);

        return new THREE.Mesh(geometry, material);
    }

    /* Overlays measured in hexes have to sit on the GRID rather than on the sprite: an icon is
       nudged off its hex centre when several ships share a hex (ShipIconContainer.getHexOffset) and
       it sits between hexes mid-animation. This is the centre of the hex the icon is currently over,
       plus the offset from the icon to it - the offset addGridLockedOverlay records so that
       normaliseGridLockedChildren can hold both position and scale right through a zoom rescale. */
    function getHexAnchor(icon) {
        var position = icon.getPosition();
        var centre = window.coordinateConverter.fromHexToGame(window.coordinateConverter.fromGameToHex(position));

        return { centre: centre, offset: { x: centre.x - position.x, y: centre.y - position.y } };
    }

    /* A one-pixel outline around each highlighted hex, all of them in a single LineSegments so the
       whole set is still one draw call.

       LineBasicMaterial.linewidth is ignored by the WebGL renderer - normally a nuisance, here
       exactly what is wanted: the border is one device pixel at every zoom level, so it neither
       thickens as you zoom in nor thins away to nothing as you zoom out, the way a world-unit ring
       would. Same reasoning as the structure wedge's outline (buildArcOutline).

       Only the SILHOUETTE of the whole arc is outlined - an edge with a highlighted hex on both
       sides is an interior seam and is dropped entirely, so the arms read as solid shapes rather than
       as a string of beads. Every edge is counted first and only the ones appearing exactly once
       survive, which is precisely the boundary of the union.

       Edges are keyed on their midpoint at half-unit precision. Two hexes sharing an edge produce
       exactly the same midpoint; inset hexes (STRAIGHT_ARC_HEX_INSET < 1), whose facing edges really
       are two separate lines a few units apart, key differently and both stay - correctly, because
       then each hex is its own island and every edge of it IS an external edge.

       Counted in one pass and emitted in a second rather than keeping 6 edge objects per hex around
       in between: BDEW feeds this the whole 1261-hex blanket, for every ship showing EW at once.

       Colour and opacity default to the straight arcs' cobalt; the EW blankets and the gravitic
       target area pass their own so each overlay keeps the colour it is recognised by. */
    function buildHexOutlines(centres, radius, colour, opacity) {
        var counts = new Map();

        /* The doubled midpoint packed into one number - the same identity the string
           "x,y" carried, but without building 7566 keys' worth of strings for a blanket. The
           offset keeps both halves non-negative; it holds for anything within ~189 hexes of the
           icon, which is an order of magnitude past the largest overlay FV draws. */
        function edgeKey(doubledX, doubledY) {
            return (Math.round(doubledX) + 32768) * 65536 + Math.round(doubledY) + 32768;
        }

        function forEachEdge(handle) {
            centres.forEach(function (centre) {
                for (var corner = 0; corner < 6; corner++) {
                    var from = HEX_CORNERS[corner];
                    var to = HEX_CORNERS[(corner + 1) % 6];

                    var x1 = centre.x + from.x * radius;
                    var y1 = centre.y + from.y * radius;
                    var x2 = centre.x + to.x * radius;
                    var y2 = centre.y + to.y * radius;

                    handle(x1, y1, x2, y2, edgeKey(x1 + x2, y1 + y2));
                }
            });
        }

        forEachEdge(function (x1, y1, x2, y2, key) {
            counts.set(key, (counts.get(key) || 0) + 1);
        });

        var positions = [];

        forEachEdge(function (x1, y1, x2, y2, key) {
            if (counts.get(key) > 1) return; //interior seam - highlighted hex on both sides

            positions.push(x1, y1, 0, x2, y2, 0);
        });

        var geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));

        var outlines = new THREE.LineSegments(
            geometry,
            new THREE.LineBasicMaterial({
                color: colour === undefined ? STRAIGHT_ARC_BORDER_COLOUR : colour,
                opacity: opacity === undefined ? 0.9 : opacity,
                transparent: true
            })
        );
        outlines.position.z = 0.01; //clear of the coplanar fill, still behind the ship sprite

        return outlines;
    }

    /* A weapon that shoots straight (Transverse Drive, Warp Jump) can only reach the hexes lying on
       the six grid lines out of its own hex, so its arc is drawn as those actual hexes rather than
       an approximating oblong. The firing ship's own hex is never a target, so the chains start one
       hex out.

       Every hex of every direction goes into ONE geometry (buildHexFill), so a range-3 weapon with a
       360 degree arc is 18 highlighted hexes but still one mesh, one material and one draw call -
       cheaper than the up-to-six meshes the oblong version made, and no per-hex sprite. */
    ShipIcon.prototype.showStraightArcs = function (weapon, hexDistance, arcs) {
        //Reach in whole hexes. Same rule the circular arcs use: a range penalty caps the useful
        //reach where the penalty reaches 50, and weapon.range caps it again when it is set.
        var maxHexes = weapon.rangePenalty === 0 ? weapon.range : Math.floor(50 / weapon.rangePenalty);
        if (weapon.range > 0 && maxHexes > weapon.range) maxHexes = weapon.range;
        if (isNaN(maxHexes) || !isFinite(maxHexes)) maxHexes = 1; // fallback for systems without a rangePenalty, as in the circular branch
        if (maxHexes < 1) return; //genuinely no reach (e.g. a Warp Jump whose nacelle damage has taken its range to 0) - nothing to highlight

        // Arc start/end relative to ship's facing
        var arcStart = arcs.start % 360;
        var arcEnd = arcs.end % 360;

        if (arcStart === arcEnd) {
            arcStart = 0;
            arcEnd = 360;
        } else if (arcEnd <= arcStart) {
            arcEnd += 360;
        }

        var hexRadius = hexDistance / Math.sqrt(3); //hexDistance is centre-to-centre (the flat-to-flat width); this is centre-to-corner
        var centres = [];

        for (var i = 0; i < 6; i++) {
            var dir = i * 60; // clockwise

            // Wrap around for arc selection
            var dirAdjusted = dir;
            if (dirAdjusted < arcStart) dirAdjusted += 360;
            if (dirAdjusted < arcStart || dirAdjusted > arcEnd) continue;

            //Bearings are clockwise, local space is counter-clockwise, hence the negated angle.
            var angle = mathlib.degreeToRadian(-dir);
            var stepX = Math.cos(angle) * hexDistance;
            var stepY = Math.sin(angle) * hexDistance;

            for (var step = 1; step <= maxHexes; step++) { //from 1: the ship's own hex is not highlighted
                centres.push({ x: stepX * step, y: stepY * step });
            }
        }

        if (!centres.length) return;

        var radius = hexRadius * STRAIGHT_ARC_HEX_INSET;

        var hexes = buildHexFill(centres, radius, new THREE.MeshBasicMaterial({
            color: STRAIGHT_ARC_FILL_COLOUR,
            opacity: 0.5,
            transparent: true
        }));

        //child, so it inherits the fill's rotation, grid-lock correction and removal
        hexes.add(buildHexOutlines(centres, radius));

        hexes.rotation.z = mathlib.degreeToRadian(this.getFacing());
        hexes.position.z = -1;

        addGridLockedOverlay(this.mesh, hexes, getHexAnchor(this).offset);
        this.weaponArcs.push(hexes);
    };


    ShipIcon.prototype.hideWeaponArcs = function () {
        this.weaponArcs.forEach(function (arc) {
            this.mesh.remove(arc);
        }, this);
    };


    /* Structure arc indicator (STRUCTURE_ARCS_PLAN.md). Hovering / long-pressing a section's
       structure health bar in the ship window draws that section's facing coverage on the icon,
       the sibling of showWeaponArc. The arcs are the ones getLocations() uses to allocate
       incoming hits, filled onto the Structure by BaseShip::addSystem and carried in the STATIC
       ship bundle, so nothing extra travels in gamedata.

       Unlike a weapon, a structure has no range to size the wedge from - it uses a fixed radius
       that clears the icon silhouette (the selection circle sits at size * 0.375) with a floor
       for the smallest hulls. Own array + own hide, so weapon and structure arcs are independent. */
    ShipIcon.prototype.showStructureArc = function (ship, structure) {
        if (!structure || structure.name !== 'structure') return null;
        if (structure.location == 0 && ship.shipSizeClass > 1) return null; //PRIMARY is hit from every facing - no wedge to draw
        if (ship.flight) return null; //fighter flights have no sections
        if (typeof structure.startArc !== 'number' || typeof structure.endArc !== 'number') return null;

        var hexDistance = window.coordinateConverter.getHexDistance();
        var dis = Math.max(this.size * 0.5, hexDistance * 0.75);

        var arcs = shipManager.systems.getArcs(ship, structure); //applies the rolled-ship flip
        var arcLength = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
        var arcStart = mathlib.addToDirection(0, arcLength * -0.5);
        var arcFacing = mathlib.addToDirection(arcs.end, arcLength * -0.5);

        var thetaStart = mathlib.degreeToRadian(arcStart);
        var thetaLength = mathlib.degreeToRadian(arcLength);

        var geometry = new THREE.CircleGeometry(dis, 32, thetaStart, thetaLength);
        //health-bar green (theme.colors.healthOk), so the wedge reads as "this bar's section"
        var color = new THREE.Color("rgb(66,114,49)");
        var material = new THREE.MeshBasicMaterial({ color: color, opacity: 0.5, transparent: true });
        var circle = new THREE.Mesh(geometry, material);
        circle.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));
        circle.position.z = -1;
        //outline, same idea as the BDEW hexagon's border: the fill alone bleeds into the map at
        //low zoom, a crisper edge defines where the section's coverage actually stops. Drawn as a
        //LINE rather than the BDEW's inset-hole ring so it stays 1px on screen at every zoom -
        //a world-unit border would vanish when zoomed out on a wedge this small. Added as a CHILD
        //so it inherits the wedge's rotation/position (and its removal).
        circle.add(buildArcOutline(dis, thetaStart, thetaLength, color));
        //Grid-locked with the weapon arcs. This one is the odd case: a structure has no range, so the
        //radius is arbitrary (roughly the icon's own size) rather than a count of hexes. It is held
        //fixed anyway so a section wedge and a weapon wedge - routinely on screen together - stay in
        //the same proportion to each other at every zoom instead of only below 0.5.
        addGridLockedOverlay(this.mesh, circle);
        this.structureArcs.push(circle);

        return null;
    };

    /* Perimeter of the pie wedge in the CircleGeometry's own local frame: apex, out along the
       arc, back to the apex. A full-circle wedge skips the apex so no spurious radius line is
       drawn across it. Segment count matches the fill's 32 so the two edges sit flush. */
    function buildArcOutline(radius, thetaStart, thetaLength, color) {
        var segments = 32;
        var points = [];
        var fullCircle = thetaLength >= Math.PI * 2 - 0.0001;

        if (!fullCircle) points.push(new THREE.Vector3(0, 0, 0));

        for (var i = 0; i <= segments; i++) {
            var theta = thetaStart + (i / segments) * thetaLength;
            points.push(new THREE.Vector3(radius * Math.cos(theta), radius * Math.sin(theta), 0));
        }

        if (!fullCircle) points.push(new THREE.Vector3(0, 0, 0));

        var outline = new THREE.Line(
            new THREE.BufferGeometry().setFromPoints(points),
            new THREE.LineBasicMaterial({ color: color, opacity: 0.9, transparent: true })
        );
        outline.position.z = 0.01; //clear of the coplanar fill, still behind the ship sprite

        return outline;
    }

    ShipIcon.prototype.hideStructureArcs = function () {
        this.structureArcs.forEach(function (arc) {
            this.mesh.remove(arc);
        }, this);
        this.structureArcs = [];
    };

    var BDEW_HEXES = 20; //the blanket is a fixed 20 hexes whatever the ship's BDEW rating

    /* Blanket EW, drawn as the actual grid hexes it covers rather than a smooth hexagon
       approximating them - same treatment as the straight weapon arcs, so "am I under that
       blanket?" is answered by the hex the ship sits in instead of by eye.

       The hexes a range-20 disc covers do NOT make a clean hexagon: every side of the union is a
       zigzag, and the old smooth outline (a hexagon of circumradius 20.6 hexes, the extra 0.6 there
       to reach the far corner of the 20th hex) cut across those hexes on both sides of it. */
    ShipIcon.prototype.showBDEW = function () {
        var BDEW = ew.getBDEW(this.ship);
        if (!BDEW || this.BDEWSprite) {
            return;
        }

        var hexDistance = window.coordinateConverter.getHexDistance();
        var hexRadius = hexDistance / Math.sqrt(3); //hexDistance is centre-to-centre; this is centre-to-corner
        var centres = hexCentresWithin(BDEW_HEXES, hexDistance);

        var color = gamedata.isMyShip(this.ship) ? new THREE.Color(160 / 255, 250 / 255, 100 / 255).convertSRGBToLinear() : new THREE.Color(255 / 255, 157 / 255, 0 / 255).convertSRGBToLinear();

        var hexagon = buildHexFill(centres, hexRadius, new THREE.MeshBasicMaterial({
            color: color,
            opacity: 0.2,
            transparent: true
        }));
        hexagon.position.z = -1;

        //Border in the same colour, defining where the blanket stops. Only the silhouette of the
        //union is drawn, so this is the boundary of the blanket and not 1261 little hexagons.
        //Child, so it inherits the fill's grid-lock correction and its removal.
        hexagon.add(buildHexOutlines(centres, hexRadius, color, 0.5));

        //the blanket has to cover 20 hexes at every zoom, and sit on the grid rather than the sprite
        addGridLockedOverlay(this.mesh, hexagon, getHexAnchor(this).offset);
        this.BDEWSprite = hexagon;

        return null;
    };


    ShipIcon.prototype.hideBDEW = function () {
        this.mesh.remove(this.BDEWSprite);
        this.BDEWSprite = null;
    };

    // Mine Detection (MDEW) overlay. Mirrors showBDEW, but the detection range is
    // variable: a mine is detected when Detect Mines EW > distance + mine signature,
    // so the radius equals the ship's Detect Mines amount (in hexes) rather than the
    // fixed 20-hex BDEW blanket. Uses a single base colour (#5e338a) for all ships.
    // Hex-mapped like the blanket - the range is a whole number of hexes, so the hexes
    // themselves are what it should be showing.
    ShipIcon.prototype.showMDEW = function () {
        var MDEW = ew.getDetectMEW(this.ship);
        if (!MDEW || !gamedata.areMinesPresent || this.MDEWSprite) {
            return;
        }

        var hexDistance = window.coordinateConverter.getHexDistance();
        var hexRadius = hexDistance / Math.sqrt(3);
        var centres = hexCentresWithin(Math.floor(MDEW), hexDistance);

        // Brightened a touch over BDEW (0.4 fill / stronger border) so the purple reads clearly.
        var color = new THREE.Color(0x5e338a).convertSRGBToLinear();
        var colorBorder = new THREE.Color(0x8045ba).convertSRGBToLinear();

        var hexagon = buildHexFill(centres, hexRadius, new THREE.MeshBasicMaterial({
            color: color,
            opacity: 0.4,
            transparent: true
        }));
        hexagon.position.z = -1;

        //silhouette of the detection area, in the lighter purple - child, so it inherits the fill's
        //grid-lock correction and its removal
        hexagon.add(buildHexOutlines(centres, hexRadius, colorBorder, 0.6));

        //the detection radius is a hex count - hold it, and sit it on the grid rather than the sprite
        addGridLockedOverlay(this.mesh, hexagon, getHexAnchor(this).offset);
        this.MDEWSprite = hexagon;

        return null;
    };


    ShipIcon.prototype.hideMDEW = function () {
        this.mesh.remove(this.MDEWSprite);
        this.MDEWSprite = null;
    };

    /* Where a shot can still go once a target is designated: every hex within `size` of the TARGET
       that also lies inside the SHOOTER's firing arc. A Gravitic's net shows the hexes it could shove
       its target into (size = the weapon's moveDistance); a Particle Repeater's shows where the rest
       of a split burst may spill (size = 1). It hangs off the target's icon, which is why both ships
       have to be passed in.

       Hex-mapped like the weapon arcs: the area is the hexes it actually covers, so its edges land
       on hex boundaries instead of slicing hexes in half and leaving the player to guess which side
       a hex fell on.

       That also disposes of the old construction - a smooth hexagon of (size + 0.6) hexes sliced by
       two WORLD-space THREE.Plane clipping planes through the shooter, plus two more sheared meshes
       to draw the cut edges. The planes belonged to one icon and the geometry to another, so the two
       had to be kept in step across icons that rescale independently; an arc test per hex has no
       such coupling, and the border falls out of the ordinary silhouette routine.

       Arc membership is measured the way weaponManager.isPosOnWeaponArc measures it: the clockwise
       compass bearing of the hex from the shooter's hex, against the weapon's arc turned into world
       bearings by the shooter's facing. getArcs() is ship-frame and already roll-corrected. The
       facing is read off the icon rather than the movement data so the overlay follows what is drawn
       on screen while a ship is animating. */
    ShipIcon.prototype.showTargetedHexagonInArc = function (shooter, shooterIcon, system, size, color = null, opacity = null) {

        //Check if we already have a sprite for this system, if so, remove it.
        if (this.shipHexagonSpritesMap.has(system)) {
            this.removeTargetedHexagonInArc(system);
        }

        size = Math.floor(size);
        if (isNaN(size) || size < 0) return; //no reach to show

        var hexDistance = window.coordinateConverter.getHexDistance();
        var hexRadius = hexDistance / Math.sqrt(3);

        var systemArcs = shipManager.systems.getArcs(shooter, system);
        /* Icon space is counter-clockwise and bearings are clockwise, hence the negated facing.
           Rounded because getFacing() reads the sprite's rotation back out of radians and the round
           trip leaves ~1e-14 of dust on it: isInArc rounds the BEARING to a whole degree but compares
           it against the arc bounds raw, so an arc ending a hair under 120 silently drops every hex
           sitting exactly on 120 - the whole edge of the wedge. Facings are multiples of 60 and arcs
           are whole degrees, so rounding is exact, and it puts this in step with
           weaponManager.isPosOnWeaponArc, which takes its facing straight from the movement data. */
        var shooterFacing = mathlib.addToDirection(0, -Math.round(shooterIcon.getFacing()));
        var arcStart = mathlib.addToDirection(systemArcs.start, shooterFacing);
        var arcEnd = mathlib.addToDirection(systemArcs.end, shooterFacing);

        var anchor = getHexAnchor(this);
        var shooterHexCentre = getHexAnchor(shooterIcon).centre;

        //A 360 degree arc keeps every hex: getArcs returns start === end for one, and isInArc says
        //yes to everything in that case.
        var centres = hexCentresWithin(size, hexDistance).filter(function (centre) {
            var bearing = mathlib.getCompassHeadingOfPoint(shooterHexCentre, {
                x: anchor.centre.x + centre.x,
                y: anchor.centre.y + centre.y
            });

            return mathlib.isInArc(bearing, arcStart, arcEnd);
        });

        if (!centres.length) return; //the whole area is outside the arc - nothing to draw

        if (color == null) {
            color = new THREE.Color(0.1, 0.5, 0.1).convertSRGBToLinear()
        }

        if (opacity == null) {
            opacity = 0.3
        }

        var hexagon = buildHexFill(centres, hexRadius, new THREE.MeshBasicMaterial({
            color: color,
            opacity: opacity,
            transparent: true,
            side: THREE.DoubleSide
        }));
        hexagon.position.z = -1;

        //Border in the caller's colour, fully opaque so the boundary carries over the pale fill.
        //Silhouette only, so the arc's cut edges are drawn but the seams between hexes are not.
        //Child, so it inherits the fill's grid-lock correction and its removal.
        hexagon.add(buildHexOutlines(centres, hexRadius, color, 1));

        //size is a count of hexes, so grid-lock it like the other range overlays, and anchor it on
        //the target's hex rather than on its sprite
        addGridLockedOverlay(this.mesh, hexagon, anchor.offset);
        this.shipHexagonSpritesMap.set(system, hexagon);
    };

    ShipIcon.prototype.removeTargetedHexagonInArc = function (system) {
        if (this.shipHexagonSpritesMap.has(system)) {
            this.mesh.remove(this.shipHexagonSpritesMap.get(system));
            this.shipHexagonSpritesMap.delete(system);
        }
    }

    ShipIcon.prototype.removeHexagonArcs = function () {
        this.shipHexagonSpritesMap.forEach(function (system, arc, map) {
            this.mesh.remove(system);
        }, this);
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