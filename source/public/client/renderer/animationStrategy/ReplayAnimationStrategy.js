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
        this.gamedata.ships.forEach(function (ship) {

            // Filter out enemy stealth ships that are undetected
            if (!gamedata.isMyorMyTeamShip(ship)) {
                if (shipManager.isStealthShip(ship) && !shipManager.isDetected(ship)) {
                    return; // Skip this ship
                }
            }

            var icon = this.shipIconContainer.getByShip(ship);

            var animation = new ShipMovementAnimation(icon, this.turn, this.shipIconContainer);
            setMovementAnimationDuration.call(this, animation);

            if (animation.getLength() > 0) {
                var cameraAnimation = new CameraPositionAnimation(animation.getStartPosition(), time, 0);
                this.animations.push(cameraAnimation);
                time += cameraAnimation.getDuration();
            }

            animation.setTime(time);
            this.animations.push(animation);
            this.movementAnimations[ship.id] = animation;

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time += animation.getDuration();
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
                true
            );

            this.animations.push(perShipAnimation);

            var preFireMovements = perShipAnimation.shipIcon.preFireMovements;
            var fireOrders = perShipAnimation.incomingFire;
            var preFireMoveTime = time + perShipAnimation.getDuration(); // Start after weapon fire animation

            if (preFireMovements.length > 0) {
                for (var i in preFireMovements) {
                    var movement = preFireMovements[i]; //Look through movements identified as prefire
                    for (var k in fireOrders) {
                        var subOrders = fireOrders[k];
                        for (var l in subOrders) { //Now check through fireorder, so see if we can find a matching value.
                            var subOrder = subOrders[l];
                            if (movement.value == subOrder.id) { //In this batch of fireorders against this ship, there is a prefire move. 
                                /*// Create a movement animation for this preFire movement
                                var preFireMoveAnimation = //I don't know the best way to create this without disrupting normal movement animations! 

                                preFireMoveAnimation.setTime(preFireMoveTime);
                                this.animations.push(preFireMoveAnimation);

                                if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                                    preFireMoveTime += preFireMoveAnimation.getDuration();
                                }
                                    */
                            }
                        }
                    }
                }
            }

            if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                time = Math.max(time + perShipAnimation.getDuration(), preFireMoveTime);
            }

            var allHexBallistics = weaponManager.getAllHexTargetedBallistics();
            var firesForThisShip = allHexBallistics.filter(function (f) {
                return f && (f.shooter === ship || f.shooter === ship.id);
            });

            var hexes = firesForThisShip.filter(f => f.fireOrder?.type == "prefiring");

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

                if (this.type === ReplayAnimationStrategy.type.INFORMATIVE) {
                    time += hexAnim.getDuration();
                }
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

            var allHexBallistics = weaponManager.getAllHexTargetedBallistics();
            var firesForThisShip = allHexBallistics.filter(function (f) {
                return f && (f.shooter === ship || f.shooter === ship.id);
            });

            var normals = firesForThisShip.filter(f => f.fireOrder?.type !== "prefiring");

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

        return time;
    }

    function animateShipDestruction(time, logAnimation) {
        this.gamedata.ships.filter(function (ship) {
            return shipManager.getTurnDestroyed(ship) === this.turn && !ship.flight;
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

    return ReplayAnimationStrategy;
}();
