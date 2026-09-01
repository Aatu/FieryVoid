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
        //Always-on facing arrow — see create(). Only units whose blueprint sets ship.facingArrow
        //have one (currently the jump vortex); it is null on everything else.
        this.facingArrowSprite = null;
        this.weaponArcs = [];
        this.structureArcs = []; //structure facing wedges (own array so they never fight the weapon arcs)
        this.hidden = false;
        this.BDEWSprite = null;
        this.MDEWSprite = null;
        this.shipHexagonSpritesMap = new Map();
        //The movement-group number drawn over an un-moved unit during the movement phase.
        //Null on terrain, which never carries one - see create().
        this.iniOrderSprite = null;
        //Declared-area overlays (see showDeclaredArea), keyed by SYSTEM ID rather than by the system
        //object: a gamedata poll rebuilds every ship and its systems, so an object key would go stale
        //each poll and leak the old mesh while a duplicate was drawn over it.
        this.declaredAreas = new Map();
        this.NotMovedSprite = null;

        this.selected = false;
        /* A JUMP VORTEX SITS A PLANE BELOW EVEN THE OTHER TERRAIN (user request 2026-08-23).
           -50 is enough separation to keep a ship readable over an asteroid field, but a vortex is
           200px of bright art that is routinely COPLANAR with the unit that opened it: a fixed
           gate's vortex forms in the GATE'S OWN HEX (JumpEngine::openVortexAtGate spawns it at
           $gate->getHexPos()), so at the shared terrain depth the two fight for the same pixels and
           the vortex wins on draw order, hiding the gate. Dropping it further puts it under
           everything - gate, ships, mines - which is also the honest reading: a vortex is a hole in
           space that things stand in front of.
           The gap is bigger than the +10 setHighlighted lifts terrain by, so hovering a vortex
           cannot float it back over the gate. */
        this.baseZ = this.terrain ? (ShipIcon.isVortex(ship) ? -150 : -50) : 0;

        this.create(ship, scene);
        this.consumeShipdata(ship);
    }

    /* THE ALWAYS-ON FACING ARROW — the two knobs, meant to be retuned by eye. Only units whose
       blueprint sets `facingArrow` have one (currently the jump vortex, SpawnJumpPoint.php).
       SCALE is a multiple of the HEX HEIGHT, not of the unit's canvasSize, so the arrowhead lands
       on the hex side the unit faces regardless of how big its own art is — and so it matches the
       identical arrow drawn by UI.vortexFacing and by the "Jump Point Forming" ballistic marker.
       Keep all three in step: BallisticIconContainer.VORTEX_ARROW_SCALE / _OPACITY and
       UI.vortexFacing.MARKER_ARROW_SCALE / _OPACITY are the other two. */
    ShipIcon.FACING_ARROW_SCALE = 1.15;
    ShipIcon.FACING_ARROW_OPACITY = 0.85;

    /* The movement-group badge's size, as a multiple of the HEX HEIGHT rather than of the unit's
       own canvasSize - the same reasoning as the facing arrow above. Only ONE badge is drawn per
       stacked hex (MovementPhaseStrategy.refreshNotMovedMarkers), so it has to be equally legible
       standing over a 34px fighter flight and over a 330px Pirocia; sizing it off canvasSize would
       make it vanish on exactly the small units that are hardest to pick out. */
    ShipIcon.INI_BADGE_SCALE = 0.55;

    /* Is this unit a jump vortex? Delegates to the ONE place that holds the class names
       (shipManager.movement) rather than repeating them here - the same discipline
       gamedata.isJumpGate follows. Guarded because this runs from the icon constructor:
       a missing shipManager should cost a vortex its z-plane, not throw.

       EITHER KIND (isAnyJumpVortex). The only thing this drives is the −150 z-plane, and an
       EXIT needs it more than an entrance does: arriving reinforcements deliberately stack in its
       hex, so the art has to sit behind them rather than over them
       (REINFORCEMENTS_PLAN.md §4 Stage 3). */
    ShipIcon.isVortex = function isVortex(ship) {
        return !!(window.shipManager && shipManager.movement && shipManager.movement.isAnyJumpVortex(ship));
    };

    ShipIcon.prototype.consumeShipdata = function (ship) {
        this.ship = ship;
        this.consumeMovement(ship.movement);
        this.consumeEW(ship);
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
        //The always-on facing arrow turns with the unit, terrain included (see create()).
        if (this.facingArrowSprite) this.facingArrowSprite.mesh.rotation.z = facingActual;

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

    /* Show the unit's movement-group number, or clear it with a falsy label. Driven ONLY from
       MovementPhaseStrategy, which owns the "one badge per stacked hex, lowest group wins, '+' if
       there are more" rule - an icon has no idea what else is standing in its hex. */
    ShipIcon.prototype.setIniOrderLabel = function (label) {
        if (this.iniOrderSprite) {
            this.iniOrderSprite.setLabel(label);
        }
    };

    //The badge is the one thing on an icon that gets BIGGER as the board is zoomed out; the
    //reasoning lives in ShipIniOrderSprite.js. Fed from ShipIconContainer.applyZoomToIcon.
    ShipIcon.prototype.setIniOrderZoom = function (zoom) {
        if (this.iniOrderSprite) {
            this.iniOrderSprite.setZoom(zoom);
        }
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
        /* THE ALWAYS-ON FACING ARROW. A blueprint that sets `facingArrow` (a path) gets that image
           laid over its icon, permanently — not hover-gated the way the prow/heading arrows above
           are, and NOT skipped for terrain, which is the whole point: the jump vortex is terrain
           and its facing is a rule, not decoration.

           Sized off the HEX rather than off canvasSize so it matches the identical arrow drawn by
           the Stage 2b facing control and by the "Jump Point Forming" ballistic marker — the same
           asset at the same size at all three points of a vortex's life. z 2 puts it just above the
           unit's own art (z 1) and well under the UI layers.

           Rotation is handled in setFacing alongside shipSprite, so it swings with the unit. */
        if (ship.facingArrow) {
            var arrowSize = window.HexagonMath.getHexHeight() * ShipIcon.FACING_ARROW_SCALE;
            this.facingArrowSprite = new window.webglSprite(ship.facingArrow, { width: arrowSize, height: arrowSize }, 2);
            this.facingArrowSprite.setOpacity(ShipIcon.FACING_ARROW_OPACITY);
            this.mesh.add(this.facingArrowSprite.mesh);
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

        /* The movement-group number that accompanies that dotted ring - see ShipIniOrderSprite.js.
           z 3 puts it above the hull art (1) and above the always-on facing arrow (2), which is the
           point: it is a label ON the unit. Terrain never gets one, exactly like the ring, so
           asteroid fields don't each carry an unused mesh. */
        if (!this.terrain) {
            var badgeSize = window.HexagonMath.getHexHeight() * ShipIcon.INI_BADGE_SCALE;
            this.iniOrderSprite = new window.ShipIniOrderSprite({ width: badgeSize, height: badgeSize }, 3);
            this.mesh.add(this.iniOrderSprite.mesh);
        }

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
        if (!(weapon instanceof Weapon) && !(weapon instanceof Thruster) && !(weapon.defensiveSystem)) return null; // Only show arcs for weapons
        if(weapon.stowed && weapon.stowedArcStart == null) return null; //stowed weapon with no stowed arc (Kirishiac Orbital docked) - non-operational, no arc to show. A stowed arc set (Heavy Orbital) keeps the weapon live: draw its current (reduced) arc.

        var hexDistance = window.coordinateConverter.getHexDistance();

        //Drawn BEFORE the firing-arc branches, because one of them can bail out early: a linked-fire
        //wedge that doesn't overlap a jammed turret's arc means nothing can be FIRED at, but the
        //weapon can still intercept, so its intercept wedge must survive that return.
        this.showInterceptArc(ship, weapon);

        if (weapon instanceof Thruster) {

            this.showThrusterIcon(ship, weapon); //Creates small thruster icon on relevant side of ship on hover over system.

        } else if (weapon.defensiveSystem) {
            //Shields are BEARING-based, not ranged: the wedge only says which way the shield faces,
            //and there is no reach in hexes to map onto the grid (a Shield is a ShipSystem, so it has
            //neither range nor rangePenalty). It keeps the smooth wedge it always had, at the same
            //one-hex radius the old NaN fallback gave it - which now also reads as a deliberate
            //distinction on screen: hex-edged means "these hexes are in reach", smooth means "this
            //way is covered, at any range".
            this.showCircularArc(SHIELD_ARC_RADIUS, shipManager.systems.getArcs(ship, weapon), SHIELD_ARC_COLOUR,
                {
                    fillOpacity: SHIELD_ARC_COLOUR_OPACITY,
                    borderColour: SHIELD_ARC_BORDER_COLOUR,
                    borderOpacity: SHIELD_ARC_BORDER_OPACITY
                });

        } else if (weapon.shootsStraight) { //Some weapons can only fire in straight lines e.g. Transverse Drive.  Show rectangular arcs along hex lines instead.
            var arcs = shipManager.systems.getArcs(ship, weapon);
            this.showStraightArcs(weapon, hexDistance, arcs);

        } else if (weapon.splitArcs) { //Some weapons might have two separate arcs, like Shadow Battlecruiser.
            //One overlay for BOTH arcs rather than one per arc: the arcs are separate regions of the
            //same set of reachable hexes, and drawing them as one keeps any hexes they share from
            //being filled - and so alpha-blended - twice.
            this.showRangeArc(getWeaponReachInHexes(weapon), hexDistance, shipManager.systems.getMultipleArcs(ship, weapon), "rgb(20,80,128)");

        } else { //Normal weapons with circular weapon arcs
            var arcs = shipManager.systems.getArcs(ship, weapon);
            /* JUMP_POINTS_PLAN.md Stage 6 - THE JUMP ENGINE'S REACH IS YELLOW, not the cobalt every
               gun uses (user request 2026-08-22). It is not a firing arc: it is where this ship can
               project a jump point, and it reads in the same --fv-warn yellow as everything else in
               that feature - the Stage 2b facing control, the "Jump Point Forming" hex marker, and
               the vortex unit's own zoomed-out overlay. A player who has seen one has seen them all.
               Opacity comes down with it for the same reason REDUCED_ARC_COLOUR's does: yellow over
               the dark map reads a good deal hotter than cobalt at the same alpha. */
            var isVortexReach = weapon.name === 'jumpEngine';
            var arcColour = isVortexReach ? VORTEX_ARC_COLOUR : "rgb(20,80,128)";
            var arcFillOpacity = isVortexReach ? VORTEX_ARC_FILL_OPACITY : ARC_FILL_OPACITY;

            //Firing-link reduced arc (e.g. Vree turret): if this weapon shares an angular-spread
            //group and any member has declared fire this turn (a sibling, OR this weapon itself once
            //its own order is locked), it can now only bear within linkedFiringSpread degrees of that
            //target. Draw that reduced wedge in a distinct amber colour so the restriction is visible
            //on hover. The wedge is centred on the target's bearing and expressed in the same
            //ship-frame as getArcs(), so it drops straight into the same arc test as any other arc.
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
                    //Rounded facing - see showTargetedHexagonInArc: getFacing() round-trips through
                    //radians, and dust on an arc BOUND silently drops the hexes sitting exactly on it.
                    var centreRel = mathlib.addToDirection(centreBearing, -Math.round(this.getFacing()));
                    var wedge = { start: mathlib.addToDirection(centreRel, -spread), end: mathlib.addToDirection(centreRel, spread) };
                    arcs = baseArcLength >= 360 ? wedge : intersectArcs(arcs, wedge);
                    if (!arcs) return null; //restricted arc and link wedge don't overlap - nothing can be fired at
                    arcColour = REDUCED_ARC_COLOUR; //amber: reduced (linked) arc
                    arcFillOpacity = REDUCED_ARC_FILL_OPACITY;
                }
            }

            this.showRangeArc(getWeaponReachInHexes(weapon), hexDistance, [arcs], arcColour, arcFillOpacity);
        }

        return null;
    };

    /* A weapon's reach in whole hexes. A range penalty caps the useful reach at the point the penalty
       would reach 50, and weapon.range caps it again when it is set. Systems that land in the arc code
       without being weapons at all (shields) have neither, and fall back to a single hex as they always
       did. 0 means "no reach at all" - e.g. a Warp Jump whose nacelle damage has taken its range to
       0 - and the callers draw nothing for it. */
    function getWeaponReachInHexes(weapon) {
        var maxHexes = weapon.rangePenalty === 0 ? weapon.range : Math.floor(50 / weapon.rangePenalty);

        if (weapon.range > 0 && maxHexes > weapon.range) maxHexes = weapon.range;

        maxHexes = Math.floor(maxHexes);
        if (isNaN(maxHexes) || !isFinite(maxHexes)) maxHexes = 1;

        return maxHexes < 1 ? 0 : maxHexes;
    }

    /* How far a weapon arc is drawn, at most. Reach is very often nominal rather than real: a
       weapon with no declared range and a -1/2 hex penalty works out at 50 hexes, one at -1/4 at 200,
       and the TestLaser in customs.php at -0.1/hex comes to 500. Those are "no practical range limit"
       dressed up as a number, and drawing them literally means sweeping a quarter of a million grid
       positions for an arc whose far side is several screens off the map.

       60 hexes is past any engagement that happens on an FV map, so a weapon that nominally reaches
       further simply has its arc drawn to here. The point of the cap is not just cost: it is what
       lets EVERY ranged weapon be hex-mapped, so there is no second, smooth-edged look to jar
       against. The smooth wedge is reserved for the BEARING-based overlays - shields, the intercept
       envelope, the structure sections - where it says something different on purpose. */
    var MAX_ARC_HEXES = 60;

    /* ---- Hex-edged range-arc strengths. THESE ARE THE KNOBS ---------------------------------
       The colour itself is opaque (THREE.Color has no alpha - see the note further down), so how
       strong an arc looks on the map is these opacities, not the rgb(). 0 = invisible fill,
       1 = solid; raise for a stronger arc, lower for a fainter one.

       The reduced (linked / jammed) arc is filled fainter than the normal one on purpose: amber
       over the dark map reads a good deal hotter than cobalt does at the same alpha, so matching
       the numbers would NOT match the apparent brightness. Its outline stays at the shared border
       opacity, which is what keeps the restricted wedge crisply readable while its fill is quiet. */
    var ARC_FILL_OPACITY = 0.35;                     //normal (cobalt) firing arc
    var ARC_BORDER_OPACITY = 0.9;                   //outline of every hex-edged arc, in the arc's own colour
    var REDUCED_ARC_COLOUR = "rgb(170,95,25)";      //amber: this weapon's arc is restricted this turn
    var REDUCED_ARC_FILL_OPACITY = 0.25;             //fainter than ARC_FILL_OPACITY - see above
    /* --fv-warn (#e1b000), the jump-point yellow. Written as an rgb() literal like every other arc
       colour here rather than read from the CSS token, because these are consumed by THREE.Color
       inside a WebGL scene that has no computed style to read. Keep it in step with
       BallisticIconContainer's 'Jump Point Forming' text colour and UI.vortexFacing's arrow. */
    var VORTEX_ARC_COLOUR = "rgb(225,176,0)";
    var VORTEX_ARC_FILL_OPACITY = 0.22;              //see REDUCED_ARC_FILL_OPACITY - yellow runs hot

    /* A ranged weapon's arc, as the grid hexes it covers. arcsList is one or more ship-frame arcs -
       a split-arc mount hands both over at once, so its two regions share a single overlay and any
       hex they have in common is filled once rather than blended twice.

       Built in the icon's LOCAL space, where a ship-frame bearing b sits at math angle -b, so the
       arcs go in exactly as getArcs() gives them and the finished mesh is simply turned to the ship's
       facing (a pointy-top hex maps onto itself under any 60 degree turn, and facings are always
       multiples of 60). No facing arithmetic on the arc bounds, and so no chance of float dust on
       one - the trap that cost showTargetedHexagonInArc a whole wedge edge. */
    ShipIcon.prototype.showRangeArc = function (maxHexes, hexDistance, arcsList, colour, fillOpacity) {
        if (!maxHexes || !arcsList.length) return; //no reach, or nothing to bear with

        var loops = buildHexRegion(Math.min(maxHexes, MAX_ARC_HEXES), hexDistance, function (x, y) {
            /* The ship's own hex is not a member in its own right: the centreSectors argument below
               splits it into its six corner-to-corner triangles, each filled exactly when the range-1
               hex it faces is. So the wedge runs unbroken from the ship's centre out to its far edge,
               and where the arc turns you get a clean radial line from the centre to the hex corner
               between the two directions - leaving the part of its own hex the weapon does not bear
               on visibly empty. A 0-360 mount has no such turn and fills the hex outright.

               It used to be left wholly empty, on the grounds that a fill identical for every system
               hovered told the player nothing while sitting right on top of the stacked icons. That
               holds for a FLAT fill; a sectored one is the opposite, and is in fact the only place
               the arc's real bounds are drawn as lines rather than approximated by a hex staircase.

               None of this is a claim about what may be SHOT AT at range 0. By the rules a target
               sharing your hex is in arc for every weapon on the ship - its bearing is genuinely
               undefined, the game falls back to the previous turn's positions to get an arc out of it
               (mathlib.getCompassHeadingOfShip) and ballistics ignore arc at range 0 outright. The
               declared-area overlay, which IS such a claim, keeps its whole centre hex - see
               DECLARED_AREA_SHAPES. */
            var bearing = bearingFromOrigin(x, y);

            for (var i = 0; i < arcsList.length; i++) {
                if (mathlib.isInArc(bearing, arcsList[i].start, arcsList[i].end)) return true;
            }

            return false;
        }, true);

        if (!loops.length) return;

        //Border in the arc's own colour, the way the structure wedge outlines itself, so the amber
        //linked-fire arc stays amber. Fill strength is the caller's when it gives one - the amber
        //arc is filled fainter than the cobalt one (see REDUCED_ARC_FILL_OPACITY).
        var arcColour = new THREE.Color(colour);
        var hexes = buildRegionOverlay(loops, arcColour, fillOpacity === undefined ? ARC_FILL_OPACITY : fillOpacity, arcColour, ARC_BORDER_OPACITY);

        hexes.rotation.z = mathlib.degreeToRadian(this.getFacing());

        addGridLockedOverlay(this.mesh, hexes, getHexAnchor(this).offset);
        this.weaponArcs.push(hexes);
    };

    /* ---- The bearing-based wedges: shields, and the intercept envelope ----------------------
       Radii are in GAME UNITS, and one hex is coordinateConverter.getHexDistance() ~= 86.6 of them,
       so 250 is a shade under three hexes. Change either number here and nothing else needs to move:
       the outline, the label and the label's placement are all derived from the radius.

       Neither wedge is a statement about range. A shield covers a direction at any distance, and
       interception is decided purely by the bearing of the incoming shot (weaponManager.
       isPosOnWeaponArc - no distance test anywhere in it), so both are drawn just far enough out to
       be read at a glance.

       A COLOUR HERE IS OPAQUE. THREE.Color has no alpha channel: a fourth component in an rgb()
       string is parsed and then silently thrown away, so "rgb(35,100,200, 0.5)" renders as solid
       cobalt, not half-strength cobalt. Transparency belongs to the material - that is what the
       matching *_OPACITY constants below are, and they are the ones to turn to soften an edge. */
    var SHIELD_ARC_RADIUS = 250;
    var SHIELD_ARC_COLOUR = "rgb(20,80,128)";
    var SHIELD_ARC_COLOUR_OPACITY = 0.4; //under showCircularArc's default 0.5 - the fill is a backdrop for the border, not the read
    var SHIELD_ARC_BORDER_COLOUR = "rgb(35,100,200)"; //cobalt - lifted off the fill the way the structure wedge's outline is
    var SHIELD_ARC_BORDER_OPACITY = 0.8; //softer than the structure wedge's 0.9: a shield is often on screen next to a weapon arc

    var INTERCEPT_ARC_RADIUS = 150;
    var INTERCEPT_ARC_COLOUR = "rgba(240, 237, 228)"; //off-white: no other overlay is neutral, so it can't be mistaken for a firing arc
    var INTERCEPT_ARC_FILL_OPACITY = 0.05; //barely a tint - the hex-edged firing arc underneath has to stay the thing you read first
    var INTERCEPT_ARC_BORDER_OPACITY = 0.6; //the dotting already lightens the edge, so the dots themselves stay crisp

    /* ---- Wedge labels -----------------------------------------------------------------------
       Off-white, on the same reasoning as INTERCEPT_ARC_COLOUR: no wedge fill on the map is
       neutral, so a neutral word can never be mistaken for part of one. The structure wedge is
       green and the intercept wedge is off-white, and the same lettering reads on both. */
    var ARC_LABEL_COLOUR = 'rgba(240, 237, 228)';
    var ARC_LABEL_FONT = 'bold 30px "Trebuchet MS", Helvetica, Arial, sans-serif';
    var ARC_LABEL_CANVAS_HEIGHT = 64;   //the plane's height maps onto this, so it sets the glyph scale
    var ARC_LABEL_PADDING = 8;          //room for the 6-wide stroke, so no glyph is clipped at the edge
    var ARC_LABEL_PLACEMENT = 0.72;     //how far out along the arc's mid-bearing the word sits

    var ARC_LABEL_TEXTURES = {};

    /* A word as a texture, drawn once on first use and shared by every wedge that asks for it
       afterwards (only one system's arcs are on screen at a time, but the icons rebuild their
       overlays on every hover, and a section label is wanted on every hull the player hovers).
       Kept out of disposeOverlay's way by the fact that Material.dispose() doesn't touch textures -
       the same reason the shared thruster icon survives. The cache is bounded by the number of
       distinct words: INTERCEPT plus the nine section names.

       The canvas is fitted to the WORD rather than being a fixed 256 wide, so a short label
       ("Aft") is drawn at the same glyph size as a long one ("INTERCEPT") instead of shrinking to
       fit the same plane. Its aspect ratio goes back with the texture, which is what lets
       buildArcLabel size the plane from a height alone.

       The word is stroked in near-black before it is filled, so it stays legible where it crosses a
       bright ship sprite or the fill of the arc underneath. */
    function getArcLabelTexture(text) {
        if (ARC_LABEL_TEXTURES[text]) return ARC_LABEL_TEXTURES[text];

        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');

        //Measured before the resize and set up again after it: assigning width or height RESETS
        //every bit of 2D context state, the font included.
        context.font = ARC_LABEL_FONT;
        canvas.width = Math.ceil(context.measureText(text).width) + ARC_LABEL_PADDING * 2;
        canvas.height = ARC_LABEL_CANVAS_HEIGHT;

        context.font = ARC_LABEL_FONT;
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.lineJoin = 'round';
        context.lineWidth = 6;
        context.strokeStyle = 'rgba(0,12,20,0.9)';
        context.strokeText(text, canvas.width / 2, 34);
        context.fillStyle = ARC_LABEL_COLOUR;
        context.fillText(text, canvas.width / 2, 34);

        var texture = new THREE.CanvasTexture(canvas);
        texture.colorSpace = THREE.SRGBColorSpace;

        ARC_LABEL_TEXTURES[text] = { texture: texture, aspect: canvas.width / canvas.height };

        return ARC_LABEL_TEXTURES[text];
    }

    /* The tallest a label can be drawn and still sit inside its own wedge. At the placement radius
       a wedge is 2 x r x tan(half its angle) across, and a long word on a narrow section (an
       eight-section base's quarter arcs) would otherwise hang out over both edges. Past 120 degrees
       the wedge is wider than it is deep and the constraint stops meaning anything, hence the cap -
       which also keeps tan() away from the vertical. The 0.9 leaves a margin off the edges. */
    function fitArcLabel(height, aspect, radius, arcLength) {
        var halfAngle = Math.min(arcLength, 120) / 2;
        var available = 2 * radius * ARC_LABEL_PLACEMENT * Math.tan(mathlib.degreeToRadian(halfAngle)) * 0.9;

        return height * aspect > available ? available / aspect : height;
    }

    /* The wedge's label, in the CircleGeometry's own local frame - where the wedge is built centred
       on +X, so sitting the label on that axis puts it on the arc's mid-bearing whatever the arc is.
       Placed as a fraction of the radius; sized from `height` (defaulting to the same 0.105r the
       intercept label has always used) and the texture's own aspect, so the plane hugs the word. */
    function buildArcLabel(label, radius, height) {
        if (height === undefined) height = radius * 0.105;

        var mesh = new THREE.Mesh(
            new THREE.PlaneGeometry(height * label.aspect, height),
            new THREE.MeshBasicMaterial({ map: label.texture, transparent: true, opacity: 0.85 })
        );

        mesh.position.set(radius * ARC_LABEL_PLACEMENT, 0, 0.02); //clear of both the fill and the outline at 0.01

        return mesh;
    }

    /* The smooth pie wedge every weapon arc used to be. Now only the BEARING-based overlays use it,
       deliberately - a shield and an interceptor both answer "which way is covered?" rather than
       "which hexes can I reach?", so the smooth shape is the honest picture for them and the
       contrast with the hex-edged ranged arcs carries meaning. See showWeaponArc.

       dis is in game units and arcs is ship-frame. options carries the trimmings - fillOpacity,
       borderColour, dashedBorder, label (a getArcLabelTexture entry) - and left out entirely gives
       the plain half-opaque wedge outlined solid in its own colour, which is what a shield was
       before any of this. */
    ShipIcon.prototype.showCircularArc = function (dis, arcs, colour, options) {
        options = options || {};

        var arcLength = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
        var arcStart = mathlib.addToDirection(0, arcLength * -0.5);
        var arcFacing = mathlib.addToDirection(arcs.end, arcLength * -0.5);

        var thetaStart = mathlib.degreeToRadian(arcStart);
        var thetaLength = mathlib.degreeToRadian(arcLength);

        var geometry = new THREE.CircleGeometry(dis, 32, thetaStart, thetaLength);
        var material = new THREE.MeshBasicMaterial({
            color: new THREE.Color(colour),
            opacity: options.fillOpacity === undefined ? 0.5 : options.fillOpacity,
            transparent: true
        });
        var circle = new THREE.Mesh(geometry, material);
        circle.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));
        circle.position.z = -1;

        //Outline, on the same reasoning as the structure wedge's (buildArcOutline): a 1px LINE, not
        //a world-unit ring, so the edge stays crisp at every zoom instead of fattening as you zoom
        //in and vanishing as you zoom out. A CHILD, so it inherits the wedge's rotation, its
        //grid-lock correction and its removal.
        circle.add(buildArcOutline(
            dis,
            thetaStart,
            thetaLength,
            options.borderColour === undefined ? colour : options.borderColour,
            options.dashedBorder,
            options.borderOpacity
        ));

        if (options.label) {
            var label = buildArcLabel(options.label, dis);
            //Counter-rotated out of the wedge's own rotation so the word reads upright however the
            //ship is pointing - safe because the map camera never rotates. Its POSITION still turns
            //with the parent, which is what keeps it on the mid-bearing.
            label.rotation.z = -circle.rotation.z;
            circle.add(label);
        }

        addGridLockedOverlay(this.mesh, circle); //radius is a hex range - hold it against the icon's zoom rescale
        this.weaponArcs.push(circle);

        return circle;
    };

    /* A weapon's INTERCEPT envelope, drawn alongside - and inside - its firing arc.

       The hex-edged firing arc is exact about where a weapon can shoot, and that precision is
       misleading about interception, which is a different question with a different answer: a shot
       is interceptable if its bearing from this ship falls in the weapon's arc, full stop. There is
       no hex test and no range test in that decision (weaponManager.isPosOnWeaponArc), so the
       envelope is drawn as a smooth wedge - the same shape language as the shields, and the visible
       opposite of "these hexes".

       Deliberately small, faint and neutral: it sits on top of the firing arc, so it has to be
       legible without competing with it. Ships that can't intercept never see it. */
    ShipIcon.prototype.showInterceptArc = function (ship, weapon) {
        if (weapon instanceof Thruster || weapon.defensiveSystem) return; //no interception from either
        if (weapon.specialArcs) return; //arc isn't a wedge at all (isPosOnSpecialArc decides it) - a wedge would misdescribe it
        if (getInterceptRating(weapon) <= 0) return;

        //ONE WEDGE PER ARC. A split-arc mount (Shadow Battlecruiser's Heavy Slicer) intercepts in
        //every arc it has - the server tests the incoming bearing against all of them at once
        //(mathlib::isInAnyArc in Firing::isLegalIntercept) - so all of them have to be drawn.
        //
        //getArcs() would give a single arc, and on a split mount not even a meaningful one: both
        //changeFiringMode implementations index startArcArray by FIRING MODE (weapon.php and
        //shipSystem.js updateFiringModeData), and a split pair is stored at indices 0 and 1, so
        //selecting mode 1 leaves the live startArc/endArc holding the SECOND arc. That is why this
        //wedge used to appear on the Heavy Slicer's rear arc alone.
        var arcs = getInterceptArcs(ship, weapon);
        var interceptArcRange = INTERCEPT_ARC_RADIUS;
        if(ship.flight) interceptArcRange = 100;

        for (var i = 0; i < arcs.length; i++) {
            var arc = this.showCircularArc(
                interceptArcRange,
                arcs[i], //the weapon's real bearing arc, jam critical and all - the linked-fire wedge restricts FIRING, not interception
                INTERCEPT_ARC_COLOUR,
                {
                    fillOpacity: INTERCEPT_ARC_FILL_OPACITY,
                    //Dotted, where every other wedge on the map is outlined solid. Doing the distinction
                    //twice - neutral colour AND broken line - is what lets the wedge be this faint and
                    //still read as its own thing rather than as an edge of the arc underneath it.
                    dashedBorder: true,
                    borderOpacity: INTERCEPT_ARC_BORDER_OPACITY,
                    //Labelled individually rather than once for the pair: the wedges of a split mount
                    //are on opposite sides of the hull, so an unlabelled one has nothing near it to
                    //explain what it is.
                    label: getArcLabelTexture('INTERCEPT')
                }
            );

            //Every other range overlay sits at z -1, and this one shares the screen with the firing arc
            //it belongs to. Two transparent meshes at the SAME depth are ordered by nothing more
            //meaningful than the order they were created in, so lift this one clear: it is the smaller
            //and much fainter of the two and has to be the one on top to be read at all.
            arc.position.z = -0.9;
        }
    };

    /* Every arc this weapon can intercept in, as a list - one entry for an ordinary mount, one per
       arc for a split mount. Falls back to the single live arc if a weapon claims splitArcs without
       usable arrays, so a blueprint mistake costs the second wedge rather than the whole overlay. */
    function getInterceptArcs(ship, weapon) {
        if (weapon.splitArcs) {
            var arcs = shipManager.systems.getMultipleArcs(ship, weapon);
            if (arcs.length) return arcs;
        }

        return [shipManager.systems.getArcs(ship, weapon)];
    }

    /* Intercept rating in this weapon's CURRENT firing mode. getInterceptRating() is where a weapon
       whose rating isn't a constant computes it (a Particle Impeder's climbs with its boost), and
       plain Weapons just return this.intercept from it - but shields and thrusters reach this code
       too and are not Weapons at all, hence the guard. */
    function getInterceptRating(weapon) {
        if (typeof weapon.getInterceptRating === 'function') return weapon.getInterceptRating() || 0;

        return weapon.intercept || 0;
    }

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


    var STRAIGHT_ARC_FILL_COLOUR = 0x11446e;

    /* Clockwise compass bearing of a point from the origin, 0 = East - the same convention and the
       same answer as mathlib.getCompassHeadingOfPoint, without an object allocated per call and
       without its two instanceof checks. An arc filter runs this once per hex in range, which at the
       60-hex cap is nearly eleven thousand times. */
    function bearingFromOrigin(x, y) {
        var heading = mathlib.radianToDegree(Math.atan2(y, x));

        return heading > 0 ? 360 - heading : Math.abs(heading);
    }

    /* The region pipeline - a patch of grid hexes built into ONE blanket polygon with a hex-true
       outline - moved to HexRegion.js so BallisticIconContainer can share it for terrain footprints
       and weapon splash areas. The geometry is subtle enough (winding, hole nesting, corner
       rounding) that a second copy of it would be a liability.

       These wrappers keep every call site below unchanged, and they bind LATE - reading
       window.HexRegion when called rather than when this IIFE runs - so this file stays independent
       of script order. */
    function buildHexRegion(range, hexDistance, accept, centreSectors) {
        return window.HexRegion.buildRegion(range, hexDistance, accept, centreSectors);
    }

    function buildRegionOverlay(loops, colour, fillOpacity, borderColour, borderOpacity) {
        return window.HexRegion.buildOverlay(loops, colour, fillOpacity, borderColour, borderOpacity);
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

    /* A weapon that shoots straight (Transverse Drive, Warp Jump) can only reach the hexes lying on
       the six grid lines out of its own hex, so its arc is drawn as those actual hexes rather than
       an approximating oblong. The firing ship's own hex is never a target, so the arms start one
       hex out - which leaves the star with a hole in the middle when the arc is a full circle, and
       that hole is the reason HexRegion.buildFill knows how to punch one.

       In axial terms the six grid lines are exactly the hexes with a, b or a+b zero, so membership
       is that plus the same arc test every other overlay uses. */
    ShipIcon.prototype.showStraightArcs = function (weapon, hexDistance, arcs) {
        var maxHexes = getWeaponReachInHexes(weapon);
        if (!maxHexes) return; //genuinely no reach (e.g. a Warp Jump whose nacelle damage has taken its range to 0) - nothing to highlight

        //Capped like the circular arcs even though the six arms only grow linearly: buildHexRegion
        //sweeps the whole range-N square looking for members, so the cost is the same either way.
        var loops = buildHexRegion(Math.min(maxHexes, MAX_ARC_HEXES), hexDistance, function (x, y, a, b) {
            if (a === 0 && b === 0) return false; //the ship's own hex is never a target
            if (a !== 0 && b !== 0 && a + b !== 0) return false; //off the six grid lines

            return mathlib.isInArc(bearingFromOrigin(x, y), arcs.start, arcs.end);
        });

        if (!loops.length) return;

        var hexes = buildRegionOverlay(loops, STRAIGHT_ARC_FILL_COLOUR, 0.5);
        hexes.rotation.z = mathlib.degreeToRadian(this.getFacing());

        addGridLockedOverlay(this.mesh, hexes, getHexAnchor(this).offset);
        this.weaponArcs.push(hexes);
    };


    /* Every overlay here is rebuilt from scratch each time it is shown, so once one leaves the scene
       its buffers are dead - but THREE frees a geometry's GPU memory on dispose(), not on remove().
       That was survivable while an arc was a 32-segment pie wedge; a hex-mapped one runs to a few
       hundred KB and hovering along a ship's weapon list builds one per system. Children (the
       silhouette outline) go with their parent. Materials only ever reference shared textures
       (the thruster icon), and Material.dispose() leaves those alone.

       Implementation moved to HexRegion.js with the rest of the pipeline; see the note there. */
    function disposeOverlay(overlay) {
        window.HexRegion.dispose(overlay);
    }

    ShipIcon.prototype.hideWeaponArcs = function () {
        this.weaponArcs.forEach(function (arc) {
            this.mesh.remove(arc);
            disposeOverlay(arc);
        }, this);

        //The array was never emptied, so every arc ever drawn stayed referenced for the icon's whole
        //lifetime - and re-removed on every subsequent hide. hideStructureArcs already got this right.
        this.weaponArcs = [];
    };


    /* What a section's wedge calls itself: the ship window's SECTION_NAMES word for word (see
       reactJs/shipWindow/ShipSection.js) - MIRROR, EDIT BOTH. The wedge is raised by hovering the
       section's health bar, so the map has to answer in the same words the bar does; a wedge
       reading "Stbd Fwd" against a bar reading "Starboard Forward" would leave the player matching
       up two vocabularies for no reason. Long names cost nothing here - buildArcLabel sizes the
       plane to the word and fitArcLabel shrinks whatever will not fit its wedge.

       Location 0 is PRIMARY, which showStructureArc only draws at all for the smallest hulls -
       everything bigger takes primary hits from every facing and gets no wedge. 5 is the
       structure-less placeholder section (the window gives it an empty name), and anything
       unrecognised falls through to no label rather than to a wrong one: the wedge itself is still
       worth drawing.

       Not the last word on a quarter section - getSideStructureLabel below collapses one to its
       plain side name on the hulls where that is what it really is, exactly as the window's
       nameOverride does. */
    var STRUCTURE_ARC_LABELS = {
        0: 'Primary',
        1: 'Forward',
        2: 'Aft',
        3: 'Port',
        4: 'Starboard',
        31: 'Port Fwd',
        32: 'Port Aft',
        41: 'Stbd Fwd',
        42: 'Stbd Aft'
    };

    /* Sides whose quarter sections can collapse into one name, mid location FIRST - see
       getSideStructureLabel. */
    var STRUCTURE_SIDES = [
        { locations: [3, 31, 32], label: 'Port' },
        { locations: [4, 41, 42], label: 'Starboard' }
    ];

    /* MIRROR OF getSectionNameOverrides in reactJs/shipWindow/ShipWindow.js - EDIT BOTH.

       A hull can use both quarter sections on a side purely to place systems while carrying only
       ONE structure between them, and then there is no fore/aft distinction to draw: the section
       is simply that side. Vorlon capitals are the case in point - VorlonCapitalShip::getLocations
       puts the port structure in 32 and gives it the arc 210-330, the WHOLE port side, with 31 a
       structureless weapons shelf that cannot be hit at all. Its wedge is a full 120 degree side,
       so calling it "Port Aft" would misdescribe both the bar it feeds and the shape on screen.

       A structure already sitting in the mid location (3/4) reads "Port"/"Starboard" from the table
       anyway, hence the "not the first entry" test - the same reason the window's version compares
       against side.locations[0].

       hideInShipWindow is honoured so the count can never disagree with the window's; no structure
       actually sets it, but the two rules are only worth mirroring if they mirror exactly. */
    function getSideStructureLabel(ship, structure) {
        var side = null;

        STRUCTURE_SIDES.forEach(function (candidate) {
            if (candidate.locations.indexOf(Number(structure.location)) > 0) side = candidate;
        });

        if (!side || !ship.systems) return null;

        var withStructure = side.locations.filter(function (location) {
            return ship.systems.some(function (system) {
                return system.location == location && system.name === 'structure' && !system.hideInShipWindow;
            });
        });

        return withStructure.length === 1 ? side.label : null;
    }

    function getStructureArcLabel(ship, structure) {
        return getSideStructureLabel(ship, structure) || STRUCTURE_ARC_LABELS[structure.location] || null;
    }

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

        /* The section's name across the wedge, the way the intercept envelope names itself - and
           for the same reason. A wedge on its own says "damage from here", but not into WHICH bar,
           and on an eight-section base the quarter arcs are neighbours a player has no way to tell
           apart by shape. Named from the section rather than from the bearing on purpose: a ROLLED
           ship's port wedge is drawn on the starboard side (getArcs applies the flip), and it is
           still the Port bar it feeds - which is exactly what the label has to say.

           Sized in HEXES rather than as a fraction of the wedge, so a corvette's label and a
           dreadnought's read the same size on the map instead of scaling with the hull; the fit
           keeps it inside a narrow section. hexDistance * 0.18 is the intercept label's own height
           (0.105 x its 150 radius), so the two match wherever they appear together. */
        var label = getStructureArcLabel(ship, structure);

        if (label) {
            var labelTexture = getArcLabelTexture(label);
            var labelMesh = buildArcLabel(labelTexture, dis,
                fitArcLabel(hexDistance * 0.18, labelTexture.aspect, dis, arcLength));

            //upright however the ship is pointing - see showCircularArc's label
            labelMesh.rotation.z = -circle.rotation.z;
            circle.add(labelMesh);
        }

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
       drawn across it. Segment count matches the fill's 32 so the two edges sit flush.

       `dashed` dots the CURVE only and leaves the two radii solid - the wedge's sides are hard
       edges (the weapon either bears or it doesn't), while the curve is the arbitrary one, drawn
       where it is only so the wedge can be seen at all. Dotting says so. A full circle has no radii
       and is dotted all the way round. `opacity` defaults to the 0.9 the structure wedge has
       always used. */
    function buildArcOutline(radius, thetaStart, thetaLength, color, dashed, opacity) {
        var segments = 32;
        var curve = [];
        var fullCircle = thetaLength >= Math.PI * 2 - 0.0001;
        var apex = new THREE.Vector3(0, 0, 0);
        var i;

        for (i = 0; i <= segments; i++) {
            var theta = thetaStart + (i / segments) * thetaLength;
            curve.push(new THREE.Vector3(radius * Math.cos(theta), radius * Math.sin(theta), 0));
        }

        var material = new THREE.LineBasicMaterial({
            color: color,
            opacity: opacity === undefined ? 0.9 : opacity,
            transparent: true
        });

        if (!dashed) {
            //One unbroken run: apex, out along the arc, back to the apex. A full-circle wedge skips
            //the apex so no spurious radius line is drawn across it.
            var points = fullCircle ? curve : [apex].concat(curve, [apex]);

            var line = new THREE.Line(new THREE.BufferGeometry().setFromPoints(points), material);
            line.position.z = 0.01; //clear of the coplanar fill, still behind the ship sprite

            return line;
        }

        //Dots and solid radii go in ONE LineSegments - a dash and a radius are both just segments,
        //so there is no reason to pay for a second geometry, material and draw call.
        var positions = dashPolyline(curve, radius * ARC_DASH_LENGTH, radius * ARC_DASH_GAP, fullCircle);

        if (!fullCircle) {
            var last = curve[curve.length - 1];

            positions.push(apex.x, apex.y, 0, curve[0].x, curve[0].y, 0);
            positions.push(last.x, last.y, 0, apex.x, apex.y, 0);
        }

        var geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));

        var outline = new THREE.LineSegments(geometry, material);
        outline.position.z = 0.01; //clear of the coplanar fill, still behind the ship sprite

        return outline;
    }

    /* Dash pattern for a dotted outline, as fractions of the wedge's radius so the dotting keeps its
       proportions if the radius is retuned. At the 250 the intercept wedge uses these come out at 5
       units on, 6 off - about 5px on, 6px off at default zoom. */
    var ARC_DASH_LENGTH = 0.02;
    var ARC_DASH_GAP = 0.024;

    /* The same path as a DOTTED line: walk the polyline and emit the on-parts as separate segments,
       for a LineSegments rather than a Line. Done as geometry instead of THREE's LineDashedMaterial
       for two reasons - it needs no new symbol in the tree-shaken THREE shim, and the dash length
       ends up measured along the real path rather than in the material's own scaled space.

       The pattern is always STRETCHED to fit the path rather than left to stop wherever it happens
       to run out, which is what would otherwise leave a ragged part-dash at one end. How it is
       stretched depends on what the path is:

       - `closed` (a full circle): a whole number of dash+gap periods, so the pattern meets itself
         at the seam with a proper gap rather than two dashes butting together.
       - open (a wedge's curve, running between two solid radii): a dash at BOTH ends, so the dotting
         visibly joins the lines it runs between. That is n dashes with n-1 gaps between them, which
         spans n-1+duty periods, not n. */
    function dashPolyline(points, dashLength, gapLength, closed) {
        var lengths = [];
        var total = 0;
        var i;

        for (i = 1; i < points.length; i++) {
            var length = Math.hypot(points[i].x - points[i - 1].x, points[i].y - points[i - 1].y);

            lengths.push(length);
            total += length;
        }

        if (!total) return [];

        var duty = dashLength / (dashLength + gapLength);
        var period;

        if (closed) {
            period = total / Math.max(1, Math.round(total / (dashLength + gapLength)));
        } else {
            //+gapLength in the count because n dashes need only n-1 gaps
            var count = Math.max(1, Math.round((total + gapLength) / (dashLength + gapLength)));

            period = total / (count - 1 + duty); //count 1 gives period = total/duty, i.e. one dash the whole way
        }

        var dash = period * duty;

        var positions = [];
        var walked = 0; //distance along the whole path at the start of the segment being walked

        for (i = 1; i < points.length; i++) {
            var from = points[i - 1];
            var to = points[i];
            var segment = lengths[i - 1];

            if (segment > 0) {
                //every dash that opens before this segment ends, clipped to the part inside it - a
                //dash longer than one chord simply reappears in the next segment's pass
                for (var start = Math.floor(walked / period) * period; start < walked + segment; start += period) {
                    var on = Math.max(start, walked);
                    var off = Math.min(start + dash, walked + segment);

                    if (off <= on) continue;

                    positions.push(
                        from.x + (to.x - from.x) * (on - walked) / segment,
                        from.y + (to.y - from.y) * (on - walked) / segment,
                        0,
                        from.x + (to.x - from.x) * (off - walked) / segment,
                        from.y + (to.y - from.y) * (off - walked) / segment,
                        0
                    );
                }
            }

            walked += segment;
        }

        return positions;
    }

    ShipIcon.prototype.hideStructureArcs = function () {
        this.structureArcs.forEach(function (arc) {
            this.mesh.remove(arc);
            disposeOverlay(arc);
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
        //no arc to test - the blanket covers every hex in range, so the region is the whole disc
        var loops = buildHexRegion(BDEW_HEXES, hexDistance, function () { return true; });

        var color = gamedata.isMyShip(this.ship) ? new THREE.Color(160 / 255, 250 / 255, 100 / 255).convertSRGBToLinear() : new THREE.Color(255 / 255, 157 / 255, 0 / 255).convertSRGBToLinear();

        //Border in the same colour, defining where the blanket stops. It traces the blanket's own
        //zigzag boundary - 246 segments, not the edges of 1261 little hexagons.
        var hexagon = buildRegionOverlay(loops, color, 0.2, color, 0.4);

        //the blanket has to cover 20 hexes at every zoom, and sit on the grid rather than the sprite
        addGridLockedOverlay(this.mesh, hexagon, getHexAnchor(this).offset);
        this.BDEWSprite = hexagon;

        return null;
    };


    ShipIcon.prototype.hideBDEW = function () {
        this.mesh.remove(this.BDEWSprite);
        disposeOverlay(this.BDEWSprite); //1261 hexes of buffers - see disposeOverlay
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
        var loops = buildHexRegion(Math.floor(MDEW), hexDistance, function () { return true; });

        // Brightened a touch over BDEW (0.4 fill / stronger border) so the purple reads clearly.
        var color = new THREE.Color(0x5e338a).convertSRGBToLinear();
        var colorBorder = new THREE.Color(0x8c57c1).convertSRGBToLinear();

        //boundary of the detection area, in the lighter purple
        var hexagon = buildRegionOverlay(loops, color, 0.4, colorBorder, 0.7);

        //the detection radius is a hex count - hold it, and sit it on the grid rather than the sprite
        addGridLockedOverlay(this.mesh, hexagon, getHexAnchor(this).offset);
        this.MDEWSprite = hexagon;

        return null;
    };


    ShipIcon.prototype.hideMDEW = function () {
        this.mesh.remove(this.MDEWSprite);
        disposeOverlay(this.MDEWSprite);
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

        /* The region is measured from the TARGET but the arc from the SHOOTER, so unlike every other
           overlay here the bearing is taken in world space - hence the offset back onto the target's
           hex centre. A 360 degree arc keeps every hex: getArcs returns start === end for one, and
           isInArc says yes to everything in that case. */
        var loops = buildHexRegion(size, hexDistance, function (x, y) {
            var bearing = mathlib.getCompassHeadingOfPoint(shooterHexCentre, {
                x: anchor.centre.x + x,
                y: anchor.centre.y + y
            });

            return mathlib.isInArc(bearing, arcStart, arcEnd);
        });

        if (!loops.length) return; //the whole area is outside the arc - nothing to draw

        if (color == null) {
            color = new THREE.Color(0.1, 0.5, 0.1).convertSRGBToLinear()
        }

        if (opacity == null) {
            opacity = 0.3
        }

        //Border in the caller's colour, fully opaque so the boundary carries over the pale fill.
        var hexagon = buildRegionOverlay(loops, color, opacity, color, 1);

        //size is a count of hexes, so grid-lock it like the other range overlays, and anchor it on
        //the target's hex rather than on its sprite
        addGridLockedOverlay(this.mesh, hexagon, anchor.offset);
        this.shipHexagonSpritesMap.set(system, hexagon);
    };

    /* ---- Declared-area overlay -------------------------------------------------------------------
       The hexes a weapon has COMMITTED to, drawn on its own ship's icon and left up until the order
       is withdrawn. A different question from the arcs above, so it gets a different answer: an arc
       says "where COULD this shoot" and lives only while the system is hovered; this says "where IS
       this shot going", in its own colour, for as long as the order stands.

       Any weapon on your own side can raise one - it declares a shape from its own client class
       (Weapon.getDeclaredArea) and PhaseStrategy.syncDeclaredAreas keeps the picture in step with its
       fire orders. The shape is the only thing that varies between weapons, so it is the only thing a
       caller has to name:

         'forward'  the straight line of hexes off the nose   - Planet-Cracker Beam
         'arc'      the weapon's own wedge, out to its reach  - any ordinary directional mount
         'radius'   every hex within reach, arc ignored       - an omnidirectional / area effect

       'arc' and 'radius' include the ship's OWN hex; 'forward' does not, because a beam fired down
       the ship's nose cannot hit the hex it is fired from.

       Everything else in the spec is optional and defaults to what the weapon itself says (see
       resolveDeclaredArea) or, for the outline strength, to what the shape says - so opting a weapon
       in is usually one line: `return { shape: 'arc' };`.

       Built in the icon's LOCAL frame like the firing arcs - axial `a` steps along local direction 0
       and the finished mesh is turned to the ship's facing, so 'forward' is the line off the nose and
       'arc' is the weapon's wedge, whichever way the ship is pointing. */
    var DECLARED_AREA_COLOUR = 0xd8c02a;        //yellow: reads as a declared kill zone, not as one more cobalt arc
    var DECLARED_AREA_FILL_OPACITY = 0.35;

    /* `directional` says whether turning the ship changes the region, and so whether the facing
       belongs in the rebuild signature: a combat pivot must re-aim a 'forward' line or an 'arc'
       wedge, while a 'radius' is symmetric under the 60 degree steps a facing takes and would only
       be rebuilt for nothing.

       `borderOpacity` is the shape's DEFAULT outline strength, which a spec can override. It belongs
       to the shape rather than being one shared number because how hard an outline reads depends on
       how much of it there is: a four-hex line wants a solid edge to be findable at all, while the
       perimeter of a forty-hex blanket at the same strength is a bright ring around the whole map
       - and the fainter the fill, the more the outline is all you see. */
    var DECLARED_AREA_SHAPES = {
        forward: {
            directional: true,
            borderOpacity: 0.8,
            //starts one hex out - the firing ship's own hex is never a declared target
            accept: function (spec) {
                return function (x, y, a, b) { return b === 0 && a >= 1; };
            }
        },
        arc: {
            directional: true,
            borderOpacity: 0.3,
            accept: function (spec) {
                //The ship's own hex counts, whatever the arc says - a unit sharing your hex is at
                //range 0, inside every reach, and its bearing is genuinely undefined.
                //
                //The hovered arcs (showRangeArc) fill their centre hex only in the SECTORS the arc
                //bears on, so don't "fix" one to match the other: they answer different questions. A
                //hover arc is a transient legibility aid and its centre is drawn to show which way it
                //points, not what it may hit. A declared area names a shot that has actually been
                //committed, and a unit in the firing ship's own hex is a legal target of it - so the
                //whole hex stays in the region however the arc runs.
                return function (x, y, a, b) {
                    if (a === 0 && b === 0) return true;

                    return mathlib.isInArc(bearingFromOrigin(x, y), spec.arcs.start, spec.arcs.end);
                };
            }
        },
        radius: {
            directional: false,
            borderOpacity: 0.3,
            //everything in reach, centre hex included
            accept: function (spec) {
                return function () { return true; };
            }
        }
    };

    /* A caller's spec filled out with the weapon's own numbers, plus a signature that says whether an
       overlay already on screen is still the right one. Returns null when there is nothing to draw.

       The signature is what makes the sync cheap AND live: syncDeclaredAreas runs on every gamedata
       poll and every SystemDataChanged, so rebuilding blindly would churn geometry constantly, while
       never rebuilding would leave a stale overlay behind a combat pivot (which turns the ship, and so
       turns 'forward' and 'arc' with it) or behind an arc narrowed by a jamming critical. */
    ShipIcon.prototype.resolveDeclaredArea = function (system, spec) {
        var shape = spec ? DECLARED_AREA_SHAPES[spec.shape] : null;
        if (!shape) return null;

        //Only the 'arc' shape reads the arcs, so only it pays for getArcs - and a system that somehow
        //has none still gets a usable pair rather than throwing on .start below.
        var arcs = spec.arcs;
        if (arcs === undefined && spec.shape === 'arc') arcs = shipManager.systems.getArcs(this.ship, system);
        if (!arcs) arcs = { start: 0, end: 0 }; //start === end - isInArc reads that as the full circle

        var resolved = {
            shape: spec.shape,
            hexes: spec.hexes === undefined ? getWeaponReachInHexes(system) : spec.hexes,
            arcs: arcs,
            //null as well as undefined falls back - getAnimationColourCss returns null for a weapon
            //that declares no animation colour, and THREE.Color(null) is not a colour
            colour: (spec.color === undefined || spec.color === null) ? DECLARED_AREA_COLOUR : spec.color,
            opacity: (spec.opacity === undefined || spec.opacity === null) ? DECLARED_AREA_FILL_OPACITY : spec.opacity,
            borderOpacity: (spec.borderOpacity === undefined || spec.borderOpacity === null) ? shape.borderOpacity : spec.borderOpacity,
            //rounded: getFacing() reads the sprite's rotation back out of radians, and mid-animation it
            //is between facings - see showTargetedHexagonInArc for the same round trip
            facing: shape.directional ? Math.round(this.getFacing()) : 0
        };

        resolved.hexes = Math.floor(resolved.hexes);
        if (isNaN(resolved.hexes) || resolved.hexes < 1) return null; //no reach - nothing to show
        resolved.hexes = Math.min(resolved.hexes, MAX_ARC_HEXES); //a nominal range of 100 is "no limit" dressed up as a number

        resolved.signature = [resolved.shape, resolved.hexes, resolved.arcs.start, resolved.arcs.end,
            resolved.colour, resolved.opacity, resolved.borderOpacity, resolved.facing].join('|');

        return resolved;
    };

    ShipIcon.prototype.showDeclaredArea = function (system, spec) {
        var resolved = this.resolveDeclaredArea(system, spec);

        if (!resolved) {
            this.removeDeclaredArea(system);
            return;
        }

        var existing = this.declaredAreas.get(system.id);
        if (existing && existing.signature === resolved.signature) return; //already up, and still correct

        this.removeDeclaredArea(system);

        var loops = buildHexRegion(resolved.hexes, window.coordinateConverter.getHexDistance(),
            DECLARED_AREA_SHAPES[resolved.shape].accept(resolved));

        if (!loops.length) return;

        //border in the same colour, at the shape's own strength (see DECLARED_AREA_SHAPES)
        var colour = new THREE.Color(resolved.colour);
        var overlay = buildRegionOverlay(loops, colour, resolved.opacity, colour, resolved.borderOpacity);

        //Turned to the ship only for the shapes that mean something relative to it. A 'radius' is
        //not one of them, and rotating it would be actively wrong mid-animation, when getFacing()
        //reads back a part-way angle that is not a multiple of 60 and so would tilt the region off
        //the grid.
        if (DECLARED_AREA_SHAPES[resolved.shape].directional) overlay.rotation.z = mathlib.degreeToRadian(this.getFacing());

        //measured in hexes, so grid-locked and anchored on the hex rather than on the sprite
        addGridLockedOverlay(this.mesh, overlay, getHexAnchor(this).offset);
        this.declaredAreas.set(system.id, { mesh: overlay, signature: resolved.signature });
    };

    ShipIcon.prototype.removeDeclaredArea = function (system) {
        var existing = this.declaredAreas.get(system.id);
        if (!existing) return;

        this.mesh.remove(existing.mesh);
        disposeOverlay(existing.mesh);
        this.declaredAreas.delete(system.id);
    };

    ShipIcon.prototype.removeTargetedHexagonInArc = function (system) {
        if (this.shipHexagonSpritesMap.has(system)) {
            this.mesh.remove(this.shipHexagonSpritesMap.get(system));
            disposeOverlay(this.shipHexagonSpritesMap.get(system));
            this.shipHexagonSpritesMap.delete(system);
        }
    }

    //Map.forEach hands over (value, key), so the first argument is the MESH and the second the system
    //it was drawn for - the parameter names here used to say the opposite. Called on phase teardown,
    //so the map is emptied too rather than left holding every mesh the phase ever drew.
    ShipIcon.prototype.removeHexagonArcs = function () {
        this.shipHexagonSpritesMap.forEach(function (hexagon) {
            this.mesh.remove(hexagon);
            disposeOverlay(hexagon);
        }, this);

        this.shipHexagonSpritesMap.clear();
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