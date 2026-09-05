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
            // Guard: gamedata is a singleton mutated in place by parseServerData,
            // so by the time deactivate runs the ship list may include new units
            // (e.g. flights/mines launched in a Pre-Turn Orders phase the local
            // player skipped) whose icons haven't been built yet.
            var icon = this.shipIconContainer.getByShip(ship);
            if (icon) {
                icon.show();
            }
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
        time = animateVortexLifecycle.call(this, time);
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

        /* Every fire order id in play, so isOwnHexPreFireMove below can tell a movement.value
           that REFERENCES a fire order from one that merely happens to hold a number. */
        var allFireOrderIds = new Set();
        this.gamedata.ships.forEach(function (shp) {
            weaponManager.getAllFireOrders(shp).forEach(function (f) {
                allFireOrderIds.add(String(f.id));
            });
        });

        /* Does this preFire movement belong to Pass 1 - i.e. was it caused by one of the
           shooting ship's OWN hex-targeted preFire orders?

           The guard this replaces exists to leave a move caused by ANOTHER ship's per-target
           effect (a GraviticMine pulling its own launcher) to Pass 2, so the explosion plays
           before the move. That intent is right, but movement.value is not the reliable key it
           looks like, and self-displacement weapons stopped moving their sprite as a result:

             - tac_shipmovement.value is varchar(100), so it reaches the client as a STRING,
               while tac_fireorder.id is int(11) and arrives as a NUMBER. The old test used
               Array.indexOf, which compares with ===, so the id path matched NOTHING. Pass 2
               compares with == and was unaffected - which is exactly why mine pulls and
               augmenter shifts kept working while hex-targeted self-jumps stopped.
             - Transverse Drive and Warp Drive (MicroJumpSystem) do not put a fire order id in
               value at all. doTransverseJump/doWarpJump store the jump DISTANCE, so no id
               comparison of any kind can match them.

           So: when value really does name a fire order, keep the original rule (ours only if
           that order is one of this ship's own hex-targeted ones) - now string-normalised so it
           can actually match. When value names no fire order, it is not a reference, and we fall
           back to what is always true of a self-jump: the ship ended up in the hex its own
           hex-targeted order was aimed at. Both jump weapons rewrite fireOrder.x/y when the jump
           deviates, so the destination stays authoritative.

           Gating the hex fallback on "value is not a fire-order reference" is what keeps it from
           re-stealing the case the guard was added for: a GraviticMine pull carries a real fire
           order id, and a launcher dragged into the very hex it lobbed the mine at would
           otherwise match on destination. tac_fireorder.x/y are varchar too, hence Number() on
           both sides rather than hexagon.Offset.equals, which compares with ===. */
        var isOwnHexPreFireMove = function (movement, ownHexOrders) {
            var value = String(movement.value);

            if (allFireOrderIds.has(value)) {
                return ownHexOrders.some(function (order) {
                    return String(order.id) === value;
                });
            }

            var destQ = Number(movement.position ? movement.position.q : NaN);
            var destR = Number(movement.position ? movement.position.r : NaN);
            if (!isFinite(destQ) || !isFinite(destR)) return false;

            return ownHexOrders.some(function (order) {
                return Number(order.x) === destQ && Number(order.y) === destR;
            });
        };

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
                        // weapons (e.g. self-displacement like Transverse Drive / Warp Drive).
                        // Moves caused by another ship's per-target effect — e.g. a GraviticMine
                        // pulling its own launcher — are left to Pass 2 so the explosion against
                        // the moved ship plays before the move (matching the behaviour seen for
                        // non-launcher pulled ships). See isOwnHexPreFireMove for why this is not
                        // the plain movement.value/fireOrder.id comparison it looks like it
                        // should be.
                        var ownHexOrders = hexes.map(function (h) { return h.fireOrder; })
                            .filter(function (order) { return order; });

                        for (var i in shooterIcon.preFireMovements) {
                            var movement = shooterIcon.preFireMovements[i];

                            if (!isOwnHexPreFireMove(movement, ownHexOrders)) {
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


        // One reverse damage map for both of the passes below, instead of one full fleet sweep
        // per target per pass (see getAllFireOrdersForDisplayingAgainst). Safe to share: it is
        // built from static replay data that neither pass mutates.
        var damageIndex = weaponManager.buildDamageIndex(this.gamedata.ships);

        // Pass 2a: Multi-target volleys (Antimatter Shredder, Hypergraviton Blaster).
        // Runs BEFORE the per-target pass, so a volley opens the exchange as one event rather
        // than being scattered through it. Note that build order also decides which animation
        // claims a given system's floating crit name (window.combatLog.critAnimations is a
        // build-time dedupe), so a system critted by both a volley and ordinary fire now shows
        // its name on the volley.
        var volleys = animateWeaponVolleys.call(this, time, logAnimation, shipList, damageIndex);
        time = volleys.time;

        // Pass 2b: Incoming Direct Fire (Standard Exchanges), minus anything a volley drew.
        shipList.forEach(function (ship) {
            var perShipAnimation = new AllWeaponFireAgainstShipAnimation(
                ship,
                this.shipIconContainer,
                this.emitterContainer,
                this.gamedata,
                time,
                this.scene,
                this.movementAnimations,
                logAnimation,
                false,
                {
                    damageIndex: damageIndex,
                    fireOrderFilter: function (fire) {
                        return !volleys.handledIds.has(String(fire.id));
                    }
                }
            );

            this.animations.push(perShipAnimation);
            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += perShipAnimation.getDuration();
            }

        }, this);

        return time;
    }

    // Gap between successive victims within one volley.
    var VOLLEY_STAGGER = 400;

    /* Multi-target volleys.
     *
     * A few weapons resolve ONE trigger pull into many separate fire orders against many
     * different ships: the Antimatter Shredder (an area burst - one order per attack per unit
     * within a hex of the aim point) and the Hypergraviton Blaster (a beam that hops from victim
     * to victim, one synthetic order per hop). The per-target pass plays those back one ship at
     * a time, each with its own 1.3s camera pan and 1s tail, so a Shredder catching five units
     * costs something like fourteen seconds of replay to show a single instant.
     *
     * This pass pulls a volley's orders out of the per-target pass and plays them as one event:
     * a single camera move, then the per-ship animations overlapped on a short stagger.
     *
     * Staggered rather than truly simultaneous, deliberately, for three reasons. It keeps each
     * victim's own hit and damage visuals readable instead of piling them into one frame. It
     * reads correctly for the Blaster, whose chain is genuinely sequential - each hop costs 20
     * damage and needs the previous victim dead - so collapsing it to one instant would
     * misrepresent the weapon. And it keeps the combat-log entries at distinct times, which is
     * load-bearing: LogAnimation.calculateDisplay only ever routes an entry with a STRICTLY
     * positive time difference into nextToDisplay, so two entries sharing an identical time
     * would silently drop the second one. (Merging a volley into a single log entry is not an
     * option either - combatLog.logFireOrders reads the target off orders[0] and renders one
     * line for the whole group, so a cross-target group would name only the first victim.)
     *
     * Opting in is the volleyAnimation flag on the client weapon prototype. The whole mechanism
     * is presentational, so nothing changes server-side or in the serialised payload.
     */
    function animateWeaponVolleys(time, logAnimation, shipList, damageIndex) {

        var handledIds = new Set();

        collectVolleys.call(this).forEach(function (volley) {

            // Visit victims in the same order the per-target pass would, so a volley never
            // re-orders units relative to the rest of the exchange.
            var victims = shipList.filter(function (ship) {
                return volley.targetIds.has(String(ship.id));
            });

            // A single-victim volley has nothing to overlap and would only lose its camera pan.
            // Leave it to the per-target pass by not claiming its orders.
            if (victims.length < 2) {
                return;
            }

            volley.orderIds.forEach(function (id) {
                handledIds.add(id);
            });

            var cameraDuration = 0;
            var cameraPosition = getVolleyCameraPosition.call(this, volley, victims, time);
            if (cameraPosition) {
                var cameraAnimation = new CameraPositionAnimation(cameraPosition, time);
                this.animations.push(cameraAnimation);
                cameraDuration = cameraAnimation.getDuration();
            }

            var volleyDuration = 0;

            victims.forEach(function (ship, index) {
                var offset = index * VOLLEY_STAGGER;

                var animation = new AllWeaponFireAgainstShipAnimation(
                    ship,
                    this.shipIconContainer,
                    this.emitterContainer,
                    this.gamedata,
                    time + cameraDuration + offset,
                    this.scene,
                    this.movementAnimations,
                    logAnimation,
                    false,
                    {
                        damageIndex: damageIndex,
                        skipCamera: true, //one shared pan for the whole volley, above
                        fireOrderFilter: function (fire) {
                            return volley.orderIds.has(String(fire.id));
                        }
                    }
                );

                this.animations.push(animation);

                // Overlapping, so the volley lasts as long as its LONGEST victim takes to
                // finish - not the sum, which is what the per-target pass charges.
                volleyDuration = Math.max(volleyDuration, offset + animation.getDuration());
            }, this);

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += cameraDuration + volleyDuration;
            }

        }, this);

        return { time: time, handledIds: handledIds };
    }

    /* Buckets this turn's fire orders into volleys, one per (shooter, volley-animated weapon).
     * Both weapons that use this fire once per turn, so the weapon id identifies a single
     * trigger pull; a weapon that could fire two volleys in one turn would need a finer key.
     *
     * The type and resolved filters mirror getAllFireOrdersForDisplayingAgainst exactly, so an
     * id claimed here is always an id that pass would otherwise have drawn - a volley can never
     * claim an order and then fail to draw it.
     */
    function collectVolleys() {

        var volleys = [];
        var byKey = {};

        this.gamedata.ships.forEach(function (shooter) {

            weaponManager.getAllFireOrders(shooter).forEach(function (fireOrder) {

                if (fireOrder.turn != this.turn) return;
                if (fireOrder.type !== "normal" && fireOrder.type !== "ballistic") return;

                var weapon = shipManager.systems.getSystem(shooter, fireOrder.weaponid);
                if (!weapon || !weapon.volleyAnimation) return;

                var key = shooter.id + "-" + fireOrder.weaponid;
                var volley = byKey[key];
                if (!volley) {
                    volley = {
                        shooter: shooter,
                        weapon: weapon,
                        orderIds: new Set(),
                        targetIds: new Set(),
                        hex: null
                    };
                    byKey[key] = volley;
                    volleys.push(volley);
                }

                if (fireOrder.targetid == -1) {
                    // The aim point rather than a shot at anything. The Shredder's aiming order
                    // is never resolved (AntimatterShredder::fire returns before rolling) so it
                    // is never drawn, but it carries the hex the burst is centred on - which is
                    // exactly where the camera wants to be.
                    if (volley.hex === null) {
                        var q = Number(fireOrder.x);
                        var r = Number(fireOrder.y);
                        // FireOrder.x/y can be null or the literal string "null" - see
                        // weaponManager.damageIndexKey. Number(null) is 0, so null has to be
                        // rejected explicitly rather than left to isFinite.
                        if (fireOrder.x !== null && fireOrder.y !== null && isFinite(q) && isFinite(r)) {
                            volley.hex = new hexagon.Offset(q, r);
                        }
                    }
                    return;
                }

                if (!weaponManager.isResolvedFireOrder(fireOrder)) return;
                if (weaponManager.isTerrainReturnDamage(fireOrder)) return;

                volley.orderIds.add(String(fireOrder.id));
                volley.targetIds.add(String(fireOrder.targetid));

            }, this);

        }, this);

        return volleys;
    }

    /* Where to point the camera for a whole volley. The Shredder centres a burst on a hex and
     * carries it on its aiming order, which is the right anchor - the burst is the event, not
     * any one victim. The Blaster has no hex, so fall back to the first victim: the beam starts
     * there and hops outward from it. */
    function getVolleyCameraPosition(volley, victims, time) {

        if (volley.hex) {
            var hexPosition = window.coordinateConverter.fromHexToGame(volley.hex);
            if (hexPosition && !isNaN(hexPosition.x) && !isNaN(hexPosition.y)) {
                return hexPosition;
            }
        }

        var icon = this.shipIconContainer.getByShip(victims[0]);
        if (!icon) {
            return null;
        }

        // Same rule the per-target pass uses for its own pan: a victim that was displaced by a
        // pre-fire effect is shot at where it ENDED UP, so point at the last pre-fire position
        // rather than at where the movement animation has it.
        if (icon.preFireMovements && icon.preFireMovements.length > 0) {
            var lastMove = icon.preFireMovements[icon.preFireMovements.length - 1];
            return window.coordinateConverter.fromHexToGame(lastMove.position);
        }

        return FireAnimationHelper.getShipPositionAtTime(icon, time, this.movementAnimations);
    }

    /* ⭐ JUMP_POINTS_PLAN.md Stage 6 - A JUMP POINT FORMING, AND A JUMP POINT COLLAPSING.
     *
     * The ship that GOES through one has had its own animation since the boost-to-jump days
     * (ShipJumpAnimation, below). The jump point itself simply blinked into existence between one
     * turn's replay and the next, which is the least readable moment of the whole feature: the
     * hex is empty for the entire turn it was declared on - deliberately, a forming vortex is a
     * marker, not a unit - and then a vortex is just there.
     *
     * Both ends use the SAME effect the departing ship uses (ShipJumpPoint: a cyan swirl opening
     * out, then a burst), because they are the same event seen from two sides. It plays after the
     * turn's fire and before the destructions, which is where a vortex actually forms and closes -
     * end of turn, after Firing.
     *
     * ⚠️ THE TURNS ARE OFF BY ONE IN BOTH DIRECTIONS, and that is the point:
     *   spawned      == openTurn + 1  -> the FORMING animation belongs to turn spawned - 1
     *   removedTurn  == closeTurn + 1 -> the CLOSING animation belongs to turn removedTurn - 1
     * (JumpEngine::restoreVortexState sets both; see the note there for why each is the FIRST
     * turn its state is true rather than the turn the event happened.)
     *
     * ShipJumpPoint is not an Animation - it has no render() - it just seeds Explosions into the
     * emitterContainer at absolute times, and the container IS in this.animations. So it is
     * constructed and left alone; only the camera pan is pushed. */
    function animateVortexLifecycle(time) {
        this.gamedata.ships.forEach(function (vortex) {
            //EITHER KIND: an exit forms and collapses exactly like an entrance, off the same
            //spawned/removedTurn pair that restoreVortexState writes for both. A replay with no
            //forming animation for the exit would be the least readable moment of the
            //feature - the turn the reinforcements are announced (REINFORCEMENTS_PLAN.md §4).
            if (!shipManager.movement.isAnyJumpVortex(vortex)) return;

            var forming = vortex.spawned !== undefined && vortex.spawned !== -1
                && vortex.spawned === this.turn + 1;
            var closing = Boolean(vortex.removed) && vortex.removedTurn != null
                && vortex.removedTurn === this.turn + 1;
            if (!forming && !closing) return;

            //A vortex that never formed at all - the holder's damaged drive failed on the turn it
            //was declared - is born and removed on the same turn. Nothing was ever there to see.
            if (forming && closing) return;

            var move = shipManager.movement.getLastCommitedMove(vortex);
            if (!move) return;

            var position = window.coordinateConverter.fromHexToGame(new hexagon.Offset(move.position));
            if (!position || isNaN(position.x) || isNaN(position.y)) return;

            this.animations.push(new CameraPositionAnimation(position, time));

            /* ⭐ PUSHED INTO this.animations, unlike every other use of this effect, and that is
               what gives it a SOUND (user report 2026-08-25: a jump point forming was silent).
               The particles never needed it - the constructor seeds them into the emitterContainer,
               which is already in the list - but render() is where the audio fires, and nothing
               calls render() on an object the strategy is not holding. ShipJumpPoint now inherits
               Animation so it survives the update/cleanUp sweeps that come with membership. */
            var effect = new ShipJumpPoint(this.emitterContainer,
                { time: time, position: position, playSound: true });
            this.animations.push(effect);
            time += effect.getDuration();
        }, this);

        return time;
    }

    function animateShipDestruction(time, logAnimation) {
        this.gamedata.ships.filter(function (ship) {
//GTS_Triad
//            return shipManager.getTurnDestroyed(ship) === this.turn && !ship.flight && !ship.mine;
			  return shipManager.getTurnDestroyed(ship) === this.turn && !ship.flight && !ship.mine && ship.phpclass !== 'spawnHyperspaceWaveform';
        }, this).forEach(function (ship) {
            var jumped = shipManager.hasJumpedNotDestroyed(ship);
            if (jumped) {
                /* A SHADOW HULL JUST FADES OUT - no cyan vortex (user ruling 2026-08-25). The flag
                   is a blueprint property of the Phasing Drive itself (PhasingDrive in
                   baseSystems.php), not a faction-string test, so the four Shadow hulls filed under
                   "Custom Ships" get the same treatment as the 19 under Shadow Association.
                   Absent on every other jump engine in the tree, which reads as the falsy default. */
                var drive = shipManager.systems.getSystemByName(ship, "jumpEngine");
                var noJumpPoint = Boolean(drive && drive.noJumpPointAnimation);

                var animation = new ShipJumpAnimation(time, this.shipIconContainer.getByShip(ship), this.emitterContainer, this.movementAnimations, noJumpPoint);
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

            // Hangar Ops: a docked flight is removed=true, which makes
            // isDestroyed() true for ALL turns (ships.js). But a FULL dock only
            // sets the ship-level removed flag — it applies no per-fighter
            // DockedFighter crit — so getTurnDestroyed() returns null and the
            // flight would be hidden on the very turn it docked, suppressing its
            // firing animation in this turn's replay. Treat removedTurn the same
            // way turnDestroyed is treated below: a flight removed THIS turn (or
            // later in replay time) is still on the board for this turn and must
            // stay visible so it can be seen firing before it docks. Only hide it
            // once the replay has advanced past its dock turn.
            //
            // EXCEPT a partial-dock fragment (spawnFragmentFlight): it is born
            // removed=true with spawned == removedTurn == the dock turn. It never
            // existed on the board as its own flight — the craft that docked are
            // already shown firing as part of the SOURCE flight — so a fragment
            // must stay hidden on its spawn/dock turn, otherwise a 3-of-6 dock
            // renders as the surviving flight PLUS a phantom 3-fighter fragment.
            // A genuine flight that docked this turn was spawned earlier, so
            // spawned < removedTurn distinguishes the two.
            var bornAndRemovedSameTurn = ship.spawned !== undefined &&
                ship.spawned !== -1 && ship.removedTurn != null &&
                ship.spawned >= ship.removedTurn;
            var removedFuture = ship.removed && !bornAndRemovedSameTurn &&
                (ship.removedTurn == null || ship.removedTurn >= this.turn);

            // Hide if:
            // - destroyed this or a previous turn
            // - OR is an undetected stealth ship
            return (
                (turnDestroyed !== null && turnDestroyed < this.turn) ||
                (turnDestroyed === null && destroyed && !removedFuture) ||
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
        // Compute position using the same math as the host animation
        // (no render-order dependency)
        var hostPosAndFacing = this.getPositionAndFacingAtTime(total);
        this.shipIcon.setPosition(hostPosAndFacing.position);
        this.shipIcon.setFacing(-hostPosAndFacing.facing);
    };

    SyncedIconAnimation.prototype.getPositionAndFacingAtTime = function (time) {
        // For detaching pods, clamp the query time to the detach point
        // so the pod stays at the separation position after detach
        var queryTime = time;
        if (this.detachMove) {
            var detachTime = this.time + this.hostAnimation.duration * this.detachFraction;
            queryTime = Math.min(time, detachTime);
        }

        return this.hostAnimation.getPositionAndFacingAtTime(queryTime);
    };

    SyncedIconAnimation.prototype.cleanUp = function (scene) {};

    return ReplayAnimationStrategy;
}();
