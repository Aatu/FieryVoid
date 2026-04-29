"use strict";

window.ReplayAnimationStrategy = function () {

    ReplayAnimationStrategy.type = {
        INFORMATIVE: 1,
        PHASE: 2,
        ALL: 3
    };

    function ReplayAnimationStrategy(gamedata, shipIcons, scene, type) {
        AnimationStrategy.call(this);
        this.shipIconContainer = shipIcons;
        this.gamedata = gamedata;
        this.turn = gamedata.turn;
        this.emitterContainer = new ParticleEmitterContainer(scene);
        this.animations.push(this.emitterContainer);
        this.emitterContainer.start();
        this.scene = scene;

        this.movementAnimations = {};

        this.moveHexDuration = 400;
        this.moveAnimationDuration = 2500;
        this.type = type || ReplayAnimationStrategy.type.INFORMATIVE;

        this.currentTime = 0;
        this.endTime = null;

        this.movementPhaseStartTime = null;
        this.firingPhaseStartTime = null;
    }

    ReplayAnimationStrategy.prototype = Object.create(AnimationStrategy.prototype);

    ReplayAnimationStrategy.prototype.activate = function () {
        buildAnimations.call(this);
        return this;
    };

    ReplayAnimationStrategy.prototype.deactivate = function (scene) {
        this.animations.forEach(function (animation) {
            animation.cleanUp(scene);
        });

        this.emitterContainer.cleanUp();

        this.gamedata.ships.forEach(function (ship) {
            this.shipIconContainer.getByShip(ship).show();
        }, this);

        return this;
    };

    ReplayAnimationStrategy.prototype.isDone = function () {
        return this.endTime < this.totalAnimationTime || this.totalAnimationTime < 0;
    };

    ReplayAnimationStrategy.prototype.update = function () {
        return this;
    };

    ReplayAnimationStrategy.prototype.toFiringPhase = function () {
        this.goToTime(this.firingPhaseStartTime)
        return this;
    };

    ReplayAnimationStrategy.prototype.toMovementPhase = function () {
        this.goToTime(this.movementPhaseStartTime)
        return this;
    };

    function buildAnimations() {

        var time = 0;
        var logAnimation = new LogAnimation();
        this.animations.push(logAnimation);

        this.movementPhaseStartTime = time;
        time = animateMovement.call(this, time);
        this.firingPhaseStartTime = time;
        time = animateWeaponPreFire.call(this, time, logAnimation);
        time = animateWeaponFire.call(this, time, logAnimation);
        time = animateShipDestruction.call(this, time, logAnimation);
        time += 100;

        this.endTime = time;
    }

    function animateMovement(time) {
        var animatedShips = {}; // Track ships already fully processed

        // Helper: get host ID from this ship's raw movement orders
        var self = this;
        var getShipMovementsThisTurn = function (ship) {
            if (!ship.movement) return [];
            return ship.movement.filter(function (m) { return m.turn === self.turn; });
        };

        var getHostIdFromMovements = function (ship) {
            var moves = getShipMovementsThisTurn(ship);
            for (var i = 0; i < moves.length; i++) {
                if (moves[i].type === 'attached' || moves[i].type === 'detach') {
                    return moves[i].value;
                }
            }
            return null;
        };

        // Helper: check for detach order
        var hasDetachOrder = function (ship) {
            return getShipMovementsThisTurn(ship).some(function (m) { return m.type === 'detach'; });
        };

        // Helper: get the detach move object (raw)
        var getDetachMove = function (ship) {
            return getShipMovementsThisTurn(ship).find(function (m) { return m.type === 'detach'; });
        };

        this.gamedata.ships.forEach(function (ship, index) {
            if (animatedShips[ship.id]) return;

            // Detect attachment from movement orders (ship.attached is cleared by server after detach)
            var hostIdFromMoves = getHostIdFromMovements(ship);
            var isDetachingPodAfterHost = false;

            if (hostIdFromMoves) {
                var isAttachedAtStart = Object.keys(ship.attached || {}).length > 0;
                if (!isAttachedAtStart) {
                    // It attached mid-turn (e.g. Grappling Claw). Treat as independent ship for this turn.
                    hostIdFromMoves = null;
                } else {
                    var host = this.gamedata.getShip(hostIdFromMoves);
                    if (host) {
                        var hostIndex = this.gamedata.ships.indexOf(host);
                        var detached = hasDetachOrder(ship);

                        if (!detached) {
                            // Permanently attached this turn - fully handled by host's slot
                            return;
                        }

                        if (index > hostIndex) {
                            // Pod detaches and its initiative is AFTER the host.
                            // The host's slot already created a SyncedIconAnimation for it.
                            // Now we create its independent post-detach animation.
                            isDetachingPodAfterHost = true;
                        }
                        // If index < hostIndex: pod detaches but moves BEFORE host.
                        // Falls through normally to animate all its moves independently.
                    }
                }
            }

            // Build the group: the ship itself, plus any pods to sync
            var group = [ship];
            var podsToSync = [];

            if (!isDetachingPodAfterHost) {
                // Find pods attached to this ship via their movement orders
                this.gamedata.ships.forEach(function (otherShip) {
                    if (animatedShips[otherShip.id] || otherShip.id === ship.id) return;

                    var otherHostId = getHostIdFromMovements(otherShip);
                    if (otherHostId && otherHostId == ship.id) {
                        var isOtherAttachedAtStart = Object.keys(otherShip.attached || {}).length > 0;
                        if (isOtherAttachedAtStart) {
                            var otherDetaches = hasDetachOrder(otherShip);
                            podsToSync.push({ ship: otherShip, detaches: otherDetaches });
                        }
                    }
                });
            }


            var maxDuration = 0;
            var groupAnimations = [];
            var startPosition = null;
            var hostAnimation = null;

            // First pass: create movement animations for the group
            group.forEach(function (member) {
                // Filter out undetected stealth ships
                if (!gamedata.isMyorMyTeamShip(member)) {
                    if (member.trueStealth && !shipManager.isDetected(member)) {
                        if (!weaponManager.shipHasFiringOrder(member)) {
                            animatedShips[member.id] = true;
                            return;
                        }
                    }
                }

                var icon = this.shipIconContainer.getByShip(member);
                var detachMove = isDetachingPodAfterHost ? getDetachMove(member) : null;
                var animation = new ShipMovementAnimation(icon, this.turn, this.shipIconContainer, detachMove);
                setMovementAnimationDuration.call(this, animation);

                animation.cameraFollow = false;

                if (member.id === ship.id) {
                    hostAnimation = animation;
                }

                if (animation.getLength() > 0) {
                    if (!startPosition || member.id === ship.id) {
                        startPosition = animation.getStartPosition();
                        animation.cameraFollow = true;
                    }
                    maxDuration = Math.max(maxDuration, animation.getDuration());
                }

                groupAnimations.push({ ship: member, animation: animation });
                animatedShips[member.id] = true;
            }, this);

            // Create synced animations for attached pods
            if (hostAnimation && podsToSync.length > 0) {
                podsToSync.forEach(function (entry) {
                    var podIcon = this.shipIconContainer.getByShip(entry.ship);
                    var detachMove = entry.detaches ? getDetachMove(entry.ship) : null;
                    var syncedAnim = new SyncedIconAnimation(podIcon, hostAnimation, detachMove);

                    groupAnimations.push({ ship: entry.ship, animation: syncedAnim });

                    if (entry.detaches) {
                        // Pod will get its own independent animation at its own initiative slot
                        podIcon.hasPriorSyncedAnimation = true;
                    } else {
                        // Permanently attached - no independent animation needed
                        animatedShips[entry.ship.id] = true;
                    }
                }, this);
            }

            // Add camera pan if needed
            if (startPosition) {
                var cameraAnimation = new CameraPositionAnimation(startPosition, time, 0);
                this.animations.push(cameraAnimation);
                time += cameraAnimation.getDuration();
            }

            // Second pass: set the correct start time (after camera pan)
            groupAnimations.forEach(function (entry) {
                entry.animation.setTime(time);
                this.animations.push(entry.animation);
                this.movementAnimations[entry.ship.id] = entry.animation;
            }, this);

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += maxDuration;
            }

        }, this);

        return time;
    }


    function animateWeaponPreFire(time, logAnimation) {
        var shipList = [];
        this.gamedata.ships.forEach(function (shp) { shipList.push(shp); });

        shipList.sort(function (a, b) {
            if (a.flight && !b.flight) return -1;
            if (!a.flight && b.flight) return 1;
            if (a.pointCost > b.pointCost) return 1;
            if (a.pointCost < b.pointCost) return -1;
            return 0;
        });

        // Per-ship map of movement IDs already animated (to avoid duplicates across passes)
        var handledMovementsByShip = {};

        // Pass 1: Hex Targeted PreFire (always first, matching animateWeaponFire ordering)
        var allHexBallistics = weaponManager.getAllHexTargetedBallistics();
        // Track which hex fire order IDs are handled in prefire so animateWeaponFire can skip them
        var handledHexFireOrderIds = {};

        shipList.forEach(function (ship) {
            var firesForThisShip = allHexBallistics.filter(function (f) {
                return f && (f.shooter === ship || f.shooter === ship.id);
            });

            // Accept "prefiring" type, or "ballistic" with preFires weapon (e.g. GraviticMine):
            // the DB may not persist the type promotion from "ballistic" → "prefiring".
            var hexes = firesForThisShip.filter(f => f.fireOrder?.type == "prefiring" || (f.fireOrder?.type == "ballistic" && f.weapon?.preFires));

            if (hexes.length > 0) {
                var hexAnim = new HexTargetedWeaponFireAnimation(
                    time,
                    this.movementAnimations,
                    this.shipIconContainer,
                    this.turn,
                    this.emitterContainer,
                    logAnimation,
                    hexes
                );

                this.animations.push(hexAnim);

                var hexAnimEndTime = time + hexAnim.getDuration();
                var hexPreFireMoveTime = hexAnimEndTime;

                // Hex-targeted preFire orders cause the shooting ship to teleport/move.
                // If this ship has preFire movements, animate them after the hex animation.
                var shooterIcon = this.shipIconContainer.getByShip(ship);
                if (shooterIcon && shooterIcon.preFireMovements && shooterIcon.preFireMovements.length > 0) {
                    // Get the starting state for this ship (end of normal movement on this turn)
                    var startBase = shooterIcon.getEndMovementOnTurn(this.turn);
                    if (!startBase) {
                        startBase = shooterIcon.getLastMovementOnTurn(this.turn);
                    }
                    if (startBase) {
                        var currentStartState = {
                            position: new hexagon.Offset(startBase.position),
                            facing: startBase.facing,
                            heading: startBase.heading
                        };

                        if (!handledMovementsByShip[ship.id]) {
                            handledMovementsByShip[ship.id] = [];
                        }

                        // Only animate preFire moves caused by THIS ship's own hex-targeted
                        // weapons (e.g. self-displacement like Hyperspace Jump). Moves caused
                        // by another ship's per-target effect — e.g. a GraviticMine pulling
                        // its own launcher — have movement.value pointing to that effect's
                        // fire order, which isn't in `hexes`. Let Pass 2 handle them so the
                        // explosion against the moved ship plays before the move (matching
                        // the behaviour seen for non-launcher pulled ships).
                        var ownHexFireOrderIds = hexes.map(function (h) { return h.fireOrder ? h.fireOrder.id : null; });

                        for (var i in shooterIcon.preFireMovements) {
                            var movement = shooterIcon.preFireMovements[i];

                            if (ownHexFireOrderIds.indexOf(movement.value) === -1) {
                                continue;
                            }

                            var endState = {
                                position: new hexagon.Offset(movement.position),
                                facing: movement.facing,
                                heading: movement.heading
                            };

                            var preFireMoveAnimation = new PreFireMovementAnimation(
                                shooterIcon,
                                currentStartState,
                                endState,
                                hexPreFireMoveTime,
                                this.moveHexDuration * 1.5 // Short but visible
                            );

                            this.animations.push(preFireMoveAnimation);

                            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                                hexPreFireMoveTime += preFireMoveAnimation.getDuration();
                            }

                            currentStartState = endState;
                            handledMovementsByShip[ship.id].push(movement.id);
                        }
                    }
                }

                if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                    time = Math.max(hexAnimEndTime, hexPreFireMoveTime);
                }
            }

        }, this);

        // Pass 2: Incoming Direct PreFire (Standard Exchanges)
        shipList.forEach(function (ship) {
            var handledMovements = handledMovementsByShip[ship.id] || [];
            var perShipAnimation = new AllWeaponFireAgainstShipAnimation(
                ship,
                this.shipIconContainer,
                this.emitterContainer,
                this.gamedata,
                time,
                this.scene,
                this.movementAnimations,
                logAnimation,
                true
            );

            this.animations.push(perShipAnimation);

            var preFireMovements = perShipAnimation.shipIcon.preFireMovements;
            var fireOrders = perShipAnimation.incomingFire;
            var preFireMoveTime = time + perShipAnimation.getDuration(); // Start after weapon fire animation

            if (preFireMovements.length > 0) {

                // Base starting state is the ship's end-of-movement position on this turn
                var startBase = perShipAnimation.shipIcon.getEndMovementOnTurn(this.turn);
                if (!startBase) {
                    startBase = perShipAnimation.shipIcon.getLastMovementOnTurn(this.turn);
                }

                if (startBase) {
                    var currentStartState = {
                        position: new hexagon.Offset(startBase.position),
                        facing: startBase.facing,
                        heading: startBase.heading
                    };

                    for (var i in preFireMovements) {
                        var movement = preFireMovements[i]; // Look through movements identified as preFire

                        // Skip movements already animated in the hex-targeted pass
                        if (handledMovements.indexOf(movement.id) !== -1) {
                            continue;
                        }

                        // Try to find a matching fire order for this movement.id
                        var scheduled = false;
                        for (var k in fireOrders) {
                            var subOrders = fireOrders[k];
                            for (var l in subOrders) { // Now check through fireorders to see if we can find a matching value.
                                var subOrder = subOrders[l];
                                if (movement.value == subOrder.id) { // In this batch of fireorders against this ship, there is a preFire move. 

                                    var endState = {
                                        position: new hexagon.Offset(movement.position),
                                        facing: movement.facing,
                                        heading: movement.heading
                                    };

                                    var preFireMoveAnimation = new PreFireMovementAnimation(
                                        perShipAnimation.shipIcon,
                                        currentStartState,
                                        endState,
                                        preFireMoveTime,
                                        this.moveHexDuration // Short but visible
                                    );

                                    this.animations.push(preFireMoveAnimation);

                                    if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                                        preFireMoveTime += preFireMoveAnimation.getDuration();
                                    }

                                    currentStartState = endState;
                                    scheduled = true;
                                    break;
                                }
                            }
                            if (scheduled) {
                                break;
                            }
                        }
                    }
                }
            }

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time = Math.max(time + perShipAnimation.getDuration(), preFireMoveTime);
            }

        }, this);

        return time;
    }


    function animateWeaponFire(time, logAnimation) {
        var shipList = [];
        this.gamedata.ships.forEach(function (shp) { shipList.push(shp); });

        shipList.sort(function (a, b) {
            if (a.flight && !b.flight) return -1;
            if (!a.flight && b.flight) return 1;
            if (a.pointCost > b.pointCost) return 1;
            if (a.pointCost < b.pointCost) return -1;
            return 0;
        });

        // Pass 1: Hex Targeted Fire (excluding pre-firing which is handled elsewhere)
        var allHexBallistics = weaponManager.getAllHexTargetedBallistics();

        shipList.forEach(function (ship) {
            var firesForThisShip = allHexBallistics.filter(function (f) {
                return f && (f.shooter === ship || f.shooter === ship.id);
            });

            // Exclude "prefiring" and preFires "ballistic" — those are handled in animateWeaponPreFire.
            var normals = firesForThisShip.filter(f => f.fireOrder?.type !== "prefiring" && !(f.fireOrder?.type == "ballistic" && f.weapon?.preFires));

            if (normals.length > 0) {
                var hexAnim = new HexTargetedWeaponFireAnimation(
                    time,
                    this.movementAnimations,
                    this.shipIconContainer,
                    this.turn,
                    this.emitterContainer,
                    logAnimation,
                    normals
                );

                this.animations.push(hexAnim);

                if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                    time += hexAnim.getDuration();
                }
            }
        }, this);


        // Pass 2: Incoming Direct Fire (Standard Exchanges)
        shipList.forEach(function (ship) {
            var perShipAnimation = new AllWeaponFireAgainstShipAnimation(
                ship,
                this.shipIconContainer,
                this.emitterContainer,
                this.gamedata,
                time,
                this.scene,
                this.movementAnimations,
                logAnimation
            );

            this.animations.push(perShipAnimation);
            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += perShipAnimation.getDuration();
            }

        }, this);

        return time;
    }

    function animateShipDestruction(time, logAnimation) {
        this.gamedata.ships.filter(function (ship) {
            return shipManager.getTurnDestroyed(ship) === this.turn && !ship.flight && !ship.mine;
        }, this).forEach(function (ship) {
            var jumped = shipManager.hasJumpedNotDestroyed(ship);
            if (jumped) {
                var animation = new ShipJumpAnimation(time, this.shipIconContainer.getByShip(ship), this.emitterContainer, this.movementAnimations);
                logAnimation.addLogEntryDestroyed(ship, time, true);
            } else {
                var animation = new ShipDestroyedAnimation(time, this.shipIconContainer.getByShip(ship), this.emitterContainer, this.movementAnimations);
                logAnimation.addLogEntryDestroyed(ship, time, false);
            }
            time += animation.getDuration();
            this.animations.push(animation);
        }, this);

        this.gamedata.ships.filter(function (ship) {
            var turnDestroyed = shipManager.getTurnDestroyed(ship);
            var destroyed = shipManager.isDestroyed(ship);

            // Hide if:
            // - destroyed this or a previous turn
            // - OR is an undetected stealth ship
            return (
                (turnDestroyed !== null && turnDestroyed < this.turn) ||
                (turnDestroyed === null && destroyed) ||
                (shipManager.shouldBeHidden(ship))
            );
        }, this).forEach(function (ship) {
            this.shipIconContainer.getByShip(ship).hide();
        }, this);

        this.gamedata.ships.filter(function (ship) {
            return ship.flight;
        }, this).forEach(function (ship) {
            var fightersToHide = ship.systems.filter(function (fighter) {
                var turnDestroyed = damageManager.getTurnDestroyed(ship, fighter);
                return turnDestroyed !== null && turnDestroyed < this.turn;
            }, this);

            this.shipIconContainer.getByShip(ship).hideFighters(fightersToHide);
        }, this);

        return time;
    }

    function setMovementAnimationDuration(moveAnimation) {
        if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
            moveAnimation.setDuration(moveAnimation.getLength() * this.moveHexDuration);
        } else {
            moveAnimation.setDuration(this.moveAnimationDuration);
        }
    }

    /**
     * Simple, self-contained animation that moves a single ship icon from one
     * movement state to another (both taken from replay movement data).
     *
     * It does NOT alter the underlying per-turn ShipMovementAnimation – it simply
     * overrides position/facing for its own time window, so non-preFire movement
     * animations and other phases remain unaffected.
     */
    function PreFireMovementAnimation(shipIcon, startState, endState, startTime, duration) {
        Animation.call(this);

        this.shipIcon = shipIcon;
        this.startState = startState;
        this.endState = endState;
        this.time = startTime || 0;
        this.duration = duration || 400;
    }

    PreFireMovementAnimation.prototype = Object.create(Animation.prototype);

    PreFireMovementAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    PreFireMovementAnimation.prototype.setTime = function (time) {
        this.time = time;
    };

    PreFireMovementAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {

        // Outside our time window → let other animations control the icon.
        if (total < this.time) {
            return;
        }

        var endTime = this.time + this.duration;

        var t = 1;
        if (endTime > this.time) {
            t = (total - this.time) / (endTime - this.time);
        }

        if (t < 0) t = 0;
        if (t > 1) t = 1;

        // Interpolate position between the two hexes.
        var startPos = window.coordinateConverter.fromHexToGame(this.startState.position);
        var endPos = window.coordinateConverter.fromHexToGame(this.endState.position);
        var pos = mathlib.getPointBetween(startPos, endPos, t);

        // Shortest path interpolation of facing / heading in hex-angle space.
        var startFacingAngle = mathlib.hexFacingToAngle(this.startState.facing);
        var endFacingAngle = mathlib.hexFacingToAngle(this.endState.facing);

        var facingDiff = endFacingAngle - startFacingAngle;
        if (facingDiff > 180) facingDiff -= 360;
        if (facingDiff < -180) facingDiff += 360;

        var facingAngle = startFacingAngle + facingDiff * t;

        var startHeadingAngle = mathlib.hexFacingToAngle(this.startState.heading);
        var endHeadingAngle = mathlib.hexFacingToAngle(this.endState.heading);

        var headingDiff = endHeadingAngle - startHeadingAngle;
        if (headingDiff > 180) headingDiff -= 360;
        if (headingDiff < -180) headingDiff += 360;

        var headingAngle = startHeadingAngle + headingDiff * t;

        this.shipIcon.setPosition(pos);
        this.shipIcon.setFacing(-facingAngle);
        this.shipIcon.setHeading(-headingAngle);
    };

    PreFireMovementAnimation.prototype.cleanUp = function (scene) {
        // No persistent resources to clean up - this animation only manipulates
        // the shipIcon which is managed elsewhere
    };

    /* //Old version before Pre-Firing - DK Nov 2025
    function animateWeaponFire(time, logAnimation) {
    
        var shipList = [];
        this.gamedata.ships.forEach(function (shp) { shipList.push(shp); });
    
        shipList.sort(function (a, b) {
            if (a.flight && !b.flight) return -1;
            if (!a.flight && b.flight) return 1;
            if (a.pointCost > b.pointCost) return 1;
            if (a.pointCost < b.pointCost) return -1;
            return 0;
        });
    
        var allHexBallistics = weaponManager.getAllHexTargetedBallistics();
    
        shipList.forEach(function (ship) {
            var perShipAnimation = new AllWeaponFireAgainstShipAnimation(
                ship,
                this.shipIconContainer,
                this.emitterContainer,
                this.gamedata,
                time,
                this.scene,
                this.movementAnimations,
                logAnimation
            );
            this.animations.push(perShipAnimation);
    
            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += perShipAnimation.getDuration();
            }
    
            // 🔧 filter only this ship’s ballistics
            var firesForThisShip = allHexBallistics.filter(function (f) {
                return f && (f.shooter === ship || f.shooter === ship.id);
            });
    
            if (firesForThisShip.length > 0) {
                var hexAnim = new HexTargetedWeaponFireAnimation(
                    time,
                    this.movementAnimations,
                    this.shipIconContainer,
                    this.turn,
                    this.emitterContainer,
                    logAnimation,
                    firesForThisShip   // ✅ pass per-ship fires
                );
    
                this.animations.push(hexAnim);
    
                if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                    time += hexAnim.getDuration();
                }
            }
    
        }, this);
    
        return time;
    }
    */

    /* //Even OLDER version from before Zero changed it I think
    function animateWeaponFire(time, logAnimation) {
        var animation = new HexTargetedWeaponFireAnimation(time, this.movementAnimations, this.shipIconContainer, this.turn, this.emitterContainer, logAnimation);
        this.animations.push(animation);
        if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
            time += animation.getDuration();
        }
        
        //Marcin Sawicki: start with fire at fighters - this will solve most of strange order results!
        var shipList = new Array();
        this.gamedata.ships.forEach(function (shp){
            shipList.push(shp);
        });
        
        //now sort - fighters first!
        shipList.sort(function(a, b){
        if (a.flight && !b.flight){//fighters always before ships
                return -1;
              }else if (!a.flight && b.flight){
                return 1;
              }else if (a.pointCost > b.pointCost){ //less valuable units first
                return 1;
              }else if (a.pointCost < b.pointCost){
                return -1;
              }
              else return 0;
        });
        
        
        
        //this.gamedata.ships.forEach(function (ship, i) 
        shipList.forEach(function (ship, i) {
            var animation = new AllWeaponFireAgainstShipAnimation(ship, this.shipIconContainer, this.emitterContainer, this.gamedata, time, this.scene, this.movementAnimations, logAnimation);
            this.animations.push(animation);

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += animation.getDuration();
            }
        }, this);

        return time;
    }
    */

    function SyncedIconAnimation(shipIcon, hostAnimation, detachMove) {
        Animation.call(this);
        this.shipIcon = shipIcon;
        this.hostAnimation = hostAnimation;
        this.detachMove = detachMove;

        // Calculate detach timing as a fraction of host's curve length
        if (detachMove) {
            var hostLengthToDetach = 0;
            var found = false;
            for (var i = 0; i < this.hostAnimation.hexAnimations.length; i++) {
                var hexAnim = this.hostAnimation.hexAnimations[i];
                hostLengthToDetach += hexAnim.length;
                if (hexAnim.move.position.equals(detachMove.position)) {
                    found = true;
                    break;
                }
            }
            if (this.hostAnimation.totalCurveLength > 0 && found) {
                this.detachFraction = hostLengthToDetach / this.hostAnimation.totalCurveLength;
            } else {
                this.detachFraction = 1; // Can't find detach point, sync for entire duration
            }
        } else {
            this.detachFraction = 1; // Permanently synced
        }

        this.duration = this.hostAnimation.getDuration();
        this.time = 0;
    }

    SyncedIconAnimation.prototype = Object.create(Animation.prototype);

    SyncedIconAnimation.prototype.getDuration = function () {
        return this.duration;
    };

    SyncedIconAnimation.prototype.setTime = function (time) {
        this.time = time;
    };

    SyncedIconAnimation.prototype.render = function (now, total, last, delta, zoom, back, paused) {
        // For detaching pods, clamp the query time to the detach point
        // so the pod stays at the separation position after detach
        var queryTime = total;
        if (this.detachMove) {
            var detachTime = this.time + this.hostAnimation.duration * this.detachFraction;
            queryTime = Math.min(total, detachTime);
        }

        // Compute position using the same math as the host animation
        // (no render-order dependency)
        var hostPosAndFacing = this.hostAnimation.getPositionAndFacingAtTime(queryTime);
        this.shipIcon.setPosition(hostPosAndFacing.position);
        this.shipIcon.setFacing(-hostPosAndFacing.facing);
    };

    SyncedIconAnimation.prototype.cleanUp = function (scene) {};

    return ReplayAnimationStrategy;
}();
