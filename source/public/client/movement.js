"use strict";

shipManager.movement = {

    deploy: function deploy(ship, pos) {

        if (!ship.deploymove) {
            var lm = ship.movement[ship.movement.length - 1];
            var move = {
                id: -1,
                type: "deploy",
                position: pos,
                xOffset: 0,
                yOffset: 0,
                facing: lm.facing,
                heading: lm.heading,
                speed: lm.speed,
                animating: false,
                animated: true,
                animationtics: 0,
                requiredThrust: Array(null, null, null, null, null),
                assignedThrust: Array(),
                commit: true,
                preturn: false,
                at_initiative: shipManager.getIniativeOrder(ship),
                turn: gamedata.turn,
                forced: false,
                value: 0
            };

            ship.deploymove = move;
            ship.movement[ship.movement.length] = move;
        } else {
            ship.deploymove.position = pos;
        }

        if (ship.deploymove && ship.osat || ship.deploymove && ship.base) {
            ship.deploymove.speed = 0;
        }
    },

    doDeploymentTurn: function doDeploymentTurn(ship, right) {

        var step = 1;
        if (!right) {
            step = -1;
        }

        var newfacing = mathlib.addToHexFacing(ship.deploymove.facing, step);
        var newheading = mathlib.addToHexFacing(ship.deploymove.heading, step);

        ship.deploymove.facing = newfacing;
        ship.deploymove.heading = newheading;
    },

    doDeploymentAccel: function doDeploymentAccel(ship, accel) {
        var value = 1;
        if (!accel) {
            value = -1;
        }

        var speed = ship.deploymove.speed;
        var newSpeed = speed + value;

        if (newSpeed >= 3 && newSpeed <= 7) {
            ship.deploymove.speed += value;
        }
    },

    isMovementReady: function isMovementReady(ship) {
        return shipManager.movement.getRemainingMovement(ship) == 0 || shipManager.isDestroyed(ship);
    },

    checkHasUncommitted: function checkHasUncommitted(ship) {

        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.commit == false) return true;
        }

        return false;
    },

    hasDeletableMovements: function hasDeletableMovements(ship) {
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            if (gamedata.gamephase == 3) {
                if (movement.value == "combatpivot" && (movement.type == "pivotleft" || movement.type == "pivotright")) {
                    return true;
                }
            } else {
                if (!movement.preturn && !movement.forced && movement.type != "deploy") return true;
            }
        }

        return false;
    },

    deleteMove: function deleteMove(ship) {
        var movement = ship.movement[ship.movement.length - 1];
        if (!movement.preturn && !movement.forced && movement.turn == gamedata.turn) {
            if (gamedata.gamephase == 3 && (movement.value != "combatpivot" || movement.type != "pivotleft" && movement.type != "pivotright")) return;

            // adjust the current turn delay if the new speed changes the turn delay
            var oldspeed = shipManager.movement.getSpeed(ship);
            shipManager.movement.revertAutoThrust(ship);
            ship.movement.splice(ship.movement.length - 1, 1);
            var speed = shipManager.movement.getSpeed(ship);
            //shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
            ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
            var shipwindow = $(".shipwindow_" + ship.id);
            //shipWindowManager.cancelAssignThrust(ship);
        }
    },

    deleteSpeedChange: function deleteSpeedChange(ship, accel) {
        var curheading = shipManager.movement.getLastCommitedMove(ship).heading;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn || movement.type != "speedchange") continue;
            if (movement.value != accel && movement.heading == curheading || movement.value == accel && movement.heading != curheading) {
                // adjust the current turn delay if the new speed changes the turn delay
                var oldspeed = shipManager.movement.getSpeed(ship);
                console.log("I am going to delete ", movement)
                shipManager.movement.revertAutoThrust(ship);
                ship.movement.splice(ship.movement.length - 1, 1);
                var speed = shipManager.movement.getSpeed(ship);
                //                            shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
                ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
                return true;
            }
        }
        return false;
    },

    canJink: function canJink(ship, accel) {
        if (gamedata.gamephase != 2) return false;
        if (!ship.flight) return false;
        if (accel == 0) return true;
        if (shipManager.movement.getRemainingEngineThrust(ship) <= 0) return false;
        var jinking = shipManager.movement.getJinking(ship);
        if (jinking + accel > ship.jinkinglimit || jinking + accel < 0) return false;
        return true;
    },

    getJinking: function getJinking(ship) {
        var j = 0;
        for (var i in ship.movement) {
            var move = ship.movement[i];
            if (move.turn != gamedata.turn) continue;

            if (move.type == "jink") j += move.value;
        }
        return j;
    },

    doJink: function doJink(ship, accel) {
        if (!shipManager.movement.canJink(ship, accel)) return;
        var commit = false;
        var requiredThrust = Array();
        var assignedThrust = Array();
        if (ship.flight) {
            commit = true;
            requiredThrust[0] = 1;
            assignedThrust[0] = 1;
        } else {
            //this is a ship, not fighter flight!
            requiredThrust = Array(ship.pivotcost, 0, 0, 0, 0);
        }
        if (accel < 0) {
            for (var i in ship.movement) {
                var move = ship.movement[i];
                if (move.turn != gamedata.turn) continue;

                if (move.type == "jink") {
                    ship.movement.splice(i, 1);
                    break;
                }
            }
        } else {
            var lm = shipManager.movement.getLastCommitedMove(ship);
            ship.movement[ship.movement.length] = {
                id: -1,
                type: "jink",
                position: lm.position,
                xOffset: lm.xOffset,
                yOffset: lm.yOffset,
                facing: lm.facing,
                heading: lm.heading,
                speed: lm.speed,
                animating: false,
                animated: true,
                animationtics: 0,
                requiredThrust: requiredThrust,
                assignedThrust: assignedThrust,
                commit: commit,
                preturn: false,
                at_initiative: shipManager.getIniativeOrder(ship),
                turn: gamedata.turn,
                forced: false,
                value: accel
            };
            if (!ship.flight) {
                shipWindowManager.assignThrust(ship);
            }
        }
    },

    canRoll: function canRoll(ship) {
        if (gamedata.gamephase != 2) return false;
        if (ship.flight || ship.osat) return false;
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (shipManager.movement.isRolling(ship)) return true; //rolling ship should be always able to stop...
        if (shipManager.movement.hasRolled(ship) && !ship.agile) {
            return false;
        }
        if (shipManager.movement.isPivoting(ship) != "no" && !ship.gravitic) {
            return false;
        }
        if (ship.rollcost > shipManager.movement.getRemainingEngineThrust(ship)) {
            return false;
        }
        return true;
    },

    doRoll: function doRoll(ship) {
        if (!shipManager.movement.canRoll(ship)) return false;        
        var lm = ship.movement[ship.movement.length - 1];
        var requiredThrust = Array(ship.rollcost, 0, 0, 0, 0);
        ship.movement[ship.movement.length] = {
            id: -1,
            type: "roll",
            position: lm.position,
            xOffset: lm.xOffset,
            yOffset: lm.xOffset,
            facing: lm.facing,
            heading: lm.heading,
            speed: lm.speed,
            animating: false,
            animated: true,
            animationtics: 0,
            requiredThrust: requiredThrust,
            assignedThrust: Array(),
            commit: false,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: 0
        };
        shipWindowManager.assignThrust(ship);
        ship.rolling = true;
    },

    isRolling: function isRolling(ship) {
        var rolling = false;
        if (ship.agile) return false;
        for (var i in ship.movement) {
            var m = ship.movement[i];
            if (m.turn != gamedata.turn) continue;
            if (m.type == "isRolling") rolling = true;
            if (m.type == "roll" && m.commit) rolling = !rolling;
        }
        return rolling;
    },

    isRolled: function isRolled(ship) {
        var ret = false;
        if (ship.agile) {
            for (var i in ship.movement) {
                var m = ship.movement[i];
                if (m.type == "isRolled") {
                    ret = true;
                }
                if (m.type == "roll") {
                    ret = !ret;
                }
            }
        } else {
            for (var i in ship.movement) {
                var m = ship.movement[i];
                if (m.turn != gamedata.turn) continue;
                if (m.type == "isRolled") {
                    return true;
                }
            }
            return false;
        }
        return ret;
    },

    
    hasRolled: function hasRolled(ship) {
        for (var i in ship.movement) {
            var m = ship.movement[i];
            if (m.turn != gamedata.turn) continue;
            if (m.type == "roll" || m.type == "isRolling") return true;
        }
        return false;
    },

    
    canMove: function canMove(ship) {
        if (gamedata.gamephase != 2) return false;
        if (shipManager.isDestroyed(ship)) return false;
        return shipManager.movement.getRemainingMovement(ship) > 0;
    },

    
    doMove: function doMove(ship) {
        if (!shipManager.movement.canMove(ship)) return false;
        var lm = ship.movement[ship.movement.length - 1];
        var lastPosition = ship.movement[ship.movement.length - 1].position;
        var pos = new hexagon.Offset(lastPosition).getNeighbourAtDirection(lm.heading);
        ship.movement[ship.movement.length] = {
            id: -1,
            type: "move",
            position: pos,
            xOffset: 0,
            yOffset: 0,
            facing: lm.facing,
            heading: lm.heading,
            speed: lm.speed,
            animating: false,
            animated: false,
            animationtics: 0,
            requiredThrust: Array(null, null, null, null, null),
            assignedThrust: Array(),
            commit: true,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: 0
        };
    },

    
    canSlip: function canSlip(ship, right) {
        if (gamedata.gamephase != 2) return false;
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        var name = right ? "slipright" : "slipleft";
        var othername = right ? "slipleft" : "slipright";
        var movebetween = true;

        if (shipManager.movement.isRolling(ship) && !ship.gravitic) return false;

        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.type == othername || movement.type == name) movebetween = false;
            if (movement.type == "move") movebetween = true;
        }
        if (movebetween == false) return false;
        if (!ship.flight && Math.ceil(shipManager.movement.getSpeed(ship) / 5) > shipManager.movement.getRemainingEngineThrust(ship)) {
            return false;
        }
        if (shipManager.movement.getRemainingEngineThrust(ship) == 0) return false;
        if (shipManager.movement.getRemainingMovement(ship) < 1) return false;
        return true;
    },

    doSlip: function doSlip(ship, right) {
        if (!shipManager.movement.canSlip(ship, right)) return false;

        var name = right ? "slipright" : "slipleft";
        var lm = ship.movement[ship.movement.length - 1];
        var newheading = right ? mathlib.addToHexFacing(lm.heading, 1) : mathlib.addToHexFacing(lm.heading, -1);
        var angle = mathlib.hexFacingToAngle(newheading);

        var pos = new hexagon.Offset(lm.position).getNeighbourAtDirection(newheading);

        //var isPivoting = shipManager.movement.isPivoting(ship);

        var slipcost = Math.ceil(shipManager.movement.getSpeed(ship) / 5);
        if (ship.flight) slipcost = 1;
/*
        var reversed = shipManager.movement.hasSidesReversedForMovement(ship);
        if (reversed) right = !right;
*/
        var requiredThrust = Array(null, null, null, null, null);

        var commit = false;
        var assignedThrust = Array();

        if (ship.flight) {
            commit = true;
            requiredThrust[0] = slipcost;
            assignedThrust[0] = slipcost;
        } else {
            var reqThrusterName = "stbd";
            if (name == "slipright"){ //slip to Stbd requres Port thruster
                reqThrusterName = "port";  
            }
            var requiredThruster = shipManager.movement.thrusterDirectionRequired(ship,reqThrusterName);
            requiredThrust[requiredThruster] = slipcost;
            /*
            var facing = ship.movement[ship.movement.length - 1].facing;
            var heading = ship.movement[ship.movement.length - 1].heading;
            var pivot = isPivoting;
            angle = angle / 60;

            if (facing === heading) {
                if (heading - 1 === angle || heading + 5 === angle) {
                    requiredThrust[4] = slipcost;
                } else if (heading + 1 === angle || heading - 5 === angle) {
                    requiredThrust[3] = slipcost;
                }
            } else if (facing + 3 === heading || facing - 3 === heading) {
                if (heading - 1 === angle || heading + 5 === angle) {
                    requiredThrust[3] = slipcost;
                } else if (heading + 1 === angle || heading - 5 === angle) {
                    requiredThrust[4] = slipcost;
                }
            } else if (!ship.gravitic) {
                if (heading + 1 === angle || heading - 5 === angle) {
                    requiredThrust[3] = slipcost;
                } else if (heading - 1 === angle || heading + 5 === angle) {
                    requiredThrust[4] = slipcost;
                }
            } else if (ship.gravitic) {
                if (facing === angle) {
                    requiredThrust[2] = slipcost;
                } else if (facing + 3 === angle || facing - 3 === angle) {
                    requiredThrust[1] = slipcost;
                } else if (facing + 1 === angle || facing + 2 === angle || facing - 4 === angle || facing - 5 === angle) {
                    requiredThrust[3] = slipcost;
                } else if (facing - 1 === angle || facing - 2 === angle || facing + 4 === angle || facing + 5 === angle) {
                    requiredThrust[4] = slipcost;
                }
            }
            */
        }

        ship.movement[ship.movement.length] = {
            id: -1,
            type: name,
            position: pos,
            xOffset: 0,
            yOffset: 0,
            facing: lm.facing,
            heading: lm.heading,
            speed: lm.speed,
            animating: false,
            animated: false,
            animationtics: 0,
            requiredThrust: requiredThrust,
            assignedThrust: assignedThrust,
            commit: commit,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: 0
        };

        if (!ship.flight) {
            shipManager.movement.autoAssignThrust(ship);
            shipWindowManager.assignThrust(ship);
        }
    },

    canRotate: function canRotate(ship) {
        if (ship.base) {
            if (gamedata.gamephase == -1 && gamedata.turn == 1 && ship.deploymove) {
                return true;
            }
        } else {
            return false;
        }
    },

    pickRotation: function pickRotation(ship, right) {
        if (right) {
            confirm.confirm("Are you sure you want to set the base' rotation towards port ?", function (response) {
                if (response) {
                    shipManager.movement.setRotation(ship, right);
                }
            });
        } else {
            confirm.confirm("Are you sure you want to set the base' rotation towards starboard ?", function (response) {
                if (response) {
                    shipManager.movement.setRotation(ship, right);
                }
            });
        }
    },

    setRotation: function setRotation(ship, right) {
        if (right) {
            ship.movement[1].value = -1;
        } else {
            ship.movement[1].value = 1;
        }
    },

    doRotate: function doRotate(ship) {
        if (gamedata.turn > 0) {
            var name;
            var step = ship.movement[1].value;

            if (step == -1) {
                name = "rotateLeft";
            } else if (step == 1) {
                name = "rotateRight";
            }

            var lm = ship.movement[ship.movement.length - 1];
            var facing = mathlib.addToHexFacing(lm.facing, step);

            ship.movement[ship.movement.length] = {
                id: -1,
                type: name,
                position: lm.position,
                xOffset: lm.xOffset,
                yOffset: lm.yOffset,
                facing: facing,
                heading: facing,
                speed: lm.speed,
                animating: false,
                animated: false,
                animationtics: 0,
                requiredThrust: Array(null, null, null, null, null),
                assignedThrust: Array(),
                commit: true,
                preturn: false,
                at_initiative: shipManager.getIniativeOrder(ship),
                turn: gamedata.turn,
                forced: true,
                value: 0
            };
        }
    },

    isEndingPivot: function isEndingPivot(ship, right) {
        var isPivoting = shipManager.movement.isPivoting(ship);
        if (isPivoting == "no") return false;
        if (isPivoting == "left" && !right) return true;
        if (isPivoting == "right" && right) return true;
        return false;
    },

    
    canPivot: function canPivot(ship, right) {
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (ship.osat) return false;
        var name = right ? "pivotright" : "pivotleft";
        var othername = right ? "pivotleft" : "pivotright";
        if (shipManager.movement.isRolling(ship) && !ship.gravitic) return false;
        var hasPivoted = shipManager.movement.hasPivoted(ship);
        var isPivoting = shipManager.movement.isPivoting(ship);
        if (hasPivoted.right && isPivoting != "right" && right && !ship.agile) return false;
        if (hasPivoted.left && isPivoting != "left" && !right && !ship.agile) return false;
        //var isPivoting = shipManager.movement.isPivoting(ship);
        if (right && isPivoting == "left" || !right && isPivoting == "right" && !ship.agile) {
            return false;
        }
        if (ship.pivotcost > shipManager.movement.getRemainingEngineThrust(ship)) return false;
        if (ship.flight && gamedata.gamephase == 3) {
            if (!weaponManager.canCombatTurn(ship)) return false;
            if (Math.ceil(ship.pivotcost * 1.5) > shipManager.movement.getRemainingEngineThrust(ship)) return false;
        } else if (gamedata.gamephase != 2) return false;

        return true;
    },

    
    countCombatPivot: function countCombatPivot(ship) {
        var c = 0;
        for (var i in ship.movement) {
            var move = ship.movement[i];
            if (move.value == "combatpivot") c++;
        }
        return c;
    },

    
    doPivot: function doPivot(ship, right) {
        if (!shipManager.movement.canPivot(ship, right)) return false;
        var lm = ship.movement[ship.movement.length - 1];
        var name;
        var newfacing = lm.facing;
        var step = 1;
        var pivoting = shipManager.movement.isPivoting(ship);
        var pivotcost = ship.pivotcost;
        var value = 0;
        if (gamedata.gamephase == 3) {
            pivotcost = Math.ceil(pivotcost * 1.5); //2 for fighters, 3 for shuttles
            value = "combatpivot";
        }
        if (pivoting != "no") {
            right = !right;
        }
        name = "pivotright";
        if (!right) {
            step = -1;
            name = "pivotleft";
        }
        var commit = false;
        var assignedThrust = Array();
        var requiredThrust = Array();
        if (ship.flight) {
            commit = true;
            requiredThrust[0] = pivotcost;
            assignedThrust[0] = pivotcost;
        } else {
            var side = Math.floor(pivotcost / 2);
            var rear = Math.floor(pivotcost / 2);
            var any = ship.pivotcost % 2;
            requiredThrust = Array(any, rear, rear, side, side); //actually, rear and side requirements are always the same...
        }
        if (pivoting == "no") newfacing = mathlib.addToHexFacing(lm.facing, step);

        ship.movement[ship.movement.length] = {
            id: -1,
            type: name,
            position: lm.position,
            xOffset: lm.xOffset,
            yOffset: lm.yOffset,
            facing: newfacing,
            heading: lm.heading,
            speed: lm.speed,
            animating: false,
            animated: false,
            animationtics: 0,
            requiredThrust: requiredThrust,
            assignedThrust: assignedThrust,
            commit: commit,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: value
        };

        if (!ship.flight) {
            shipWindowManager.assignThrust(ship);
        }
    },

    doForcedPivot: function doForcedPivot(ship) {
        var pivoting = shipManager.movement.isPivoting(ship);
        if (pivoting == "no") return;

        var name = "pivotright";
        var step = 1;

        if (pivoting == "left") {
            var name = "pivotleft";
            var step = -1;
        }

        var lm = ship.movement[ship.movement.length - 1];
        var facing = mathlib.addToHexFacing(lm.facing, step);

        var alreadyDone = ship.movement.some(function (inspectedMovement) {
            return inspectedMovement.turn === gamedata.turn && inspectedMovement.type === name && inspectedMovement.forced === true;
        });

        if (alreadyDone) {
            return;
        }

        ship.movement[ship.movement.length] = {
            id: -1,
            type: name,
            position: lm.position,
            xOffset: lm.xOffset,
            yOffset: lm.yOffset,
            facing: facing,
            heading: lm.heading,
            speed: lm.speed,
            animating: false,
            animated: false,
            animationtics: 0,
            requiredThrust: Array(null, null, null, null, null),
            assignedThrust: Array(),
            commit: true,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: true,
            value: 0
        };
    },

    isPivoting: function isPivoting(ship) {
        var pivoting = "no";
        if (ship.agile) return pivoting;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.commit == false) continue;
            if (movement.type == "isPivotingLeft") pivoting = "left";
            if (movement.type == "isPivotingRight") pivoting = "right";
            if (movement.type == "pivotright" && pivoting == "no" && movement.preturn == false) {
                pivoting = "right";
            }
            if (movement.type == "pivotleft" && pivoting == "no" && movement.preturn == false) {
                pivoting = "left";
            }
            if (movement.type == "pivotright" && pivoting == "left" && movement.preturn == false) {
                pivoting = "no";
            }
            if (movement.type == "pivotleft" && pivoting == "right" && movement.preturn == false) {
                pivoting = "no";
            }
            if (!ship.gravitic && shipManager.movement.isTurn(movement) && pivoting != "no") {
                pivoting = "no";
            }
        }
        return pivoting;
    },

    
    canTurnIntoPivot: function canTurnIntoPivot(ship, right) {
        if (gamedata.gamephase != 2) return false;
        //if (ship.agile) returnVal = false; //agile ship should be able to turn into pivot all right...

        /*cannot turn into pivot if unit is aligned...*/
        if(!shipManager.movement.isOutOfAlignment(ship)) return false;
        
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;        
        var reverseheading = mathlib.addToHexFacing(heading, 3);
        /*
        if (heading === facing) returnVal = false;
        */
        
        var step = right ? -1 : 1;
        //if (mathlib.addToHexFacing(step, facing) === heading || mathlib.addToHexFacing(step, facing) === reverseheading) returnVal = true;
        if (mathlib.addToHexFacing(step, facing) === heading || mathlib.addToHexFacing(step, facing) === reverseheading) return true;
        return false;
    },
    
/*no longer needed
    isTurningIntoBackwardsPivot: function isTurningIntoBackwardsPivot(ship) {
        //if ship is moving backwards, it's turning into pivot backwards; else, it's not.
        return shipManager.movement.isGoingBackwards(ship);
               
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        var reverseheading = mathlib.addToHexFacing(heading, 3);
        return mathlib.addToHexFacing(1, facing) === reverseheading || mathlib.addToHexFacing(-1, facing) === reverseheading;
    },
*/
    
    doIntoPivotTurn: function doIntoPivotTurn(ship, right) {
        var requiredThrust = shipManager.movement.calculateRequiredThrust(ship, right);
        var lastMovement = ship.movement[ship.movement.length - 1];

        var name;
        var step = 1;
        var commit = false;
        var assignedThrust = Array();

        name = "turnright";

        if (!right) {
            step = -1;
            name = "turnleft";
        }

        //TODO: support new hex coordinate system?
        var newfacing = lastMovement.facing;
        var newheading = lastMovement.facing;

        if (shipManager.movement.isGoingBackwards(ship)) { //ship going backwards is turning _backwards_ into pivot, which affects facing
            newfacing = lastMovement.facing;
            newheading = mathlib.addToHexFacing(lastMovement.facing, 3);
        }

        if (ship.flight) {
            commit = true;
            assignedThrust[0] = requiredThrust[0];
        }
        ship.movement[ship.movement.length] = {
            id: -1,
            type: name,
            position: lastMovement.position,
            xOffset: lastMovement.xOffset,
            yOffset: lastMovement.yOffset,
            facing: newfacing,
            heading: newheading,
            speed: lastMovement.speed,
            animating: false,
            animated: false,
            animationtics: 0,
            requiredThrust: requiredThrust,
            assignedThrust: assignedThrust,
            commit: commit,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: 0
        };

        if (!ship.flight) {
            shipWindowManager.assignThrust(ship);
        }
    },

    hasPivoted: function hasPivoted(ship) {
        var left = false;
        var right = false;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            if (movement.type == "pivotleft" && movement.preturn == false) {
                left = true;
            }
            if (movement.type == "pivotright" && movement.preturn == false) {
                right = true;
            }
        }
        return { left: left, right: right };
    },

    hasCombatPivoted: function hasCombatPivoted(ship) {
        if (!ship.flight) return false;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.value != 'combatpivot') continue;
            if (movement.type == "pivotleft" || movement.type == "pivotright") {
                return true;
            }
            if (movement.type == "isPivotingRight" || movement.type == "isPivotingLeft") {
                return true;
            }
        }
        return false;
    },

    hasPivotedForShooting: function hasPivotedForShooting(ship) {
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.type == "pivotleft" || movement.type == "pivotright") {
                return true;
            }
            if (movement.type == "isPivotingRight" || movement.type == "isPivotingLeft") {
                return true;
            }
        }
        return false;
    },

    canChangeSpeed: function canChangeSpeed(ship, accel) {
        if (ship.osat || ship.base) {
            return false;
        }
        if (gamedata.gamephase == -1 && ship.deploymove) return true;
        if (gamedata.gamephase != 2) {
            return false;
        }
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.movement.checkHasUncommitted(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (ship.accelcost > shipManager.movement.getRemainingEngineThrust(ship)) {
            return false;
        }      
        
        //acceleration must be the first thing in a turn...
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.preturn == false && movement.forced == false && movement.type != "speedchange" && movement.type != "deploy") return false;
        }
        
        
        //gravitic ship with enough thrust can accelerate, no matter her alignment
        if (ship.gravitic) return true;

        /* old version
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        if (!ship.gravitic) {
            if (heading !== facing) {
                if (heading < 3) {
                    if (heading + 3 !== facing) {
                        return false;
                    }
                } else if (heading > 3) {
                    if (heading - 3 !== facing) {
                        return false;
                    }
                } else {
                    if (heading - 3 !== 0) {
                        return false;
                    }
                }
            }
        }
        */
        
        //ship cannot accelerate if it's not aligned OR pivoting    
        if (shipManager.movement.isOutOfAlignment(ship) || shipManager.movement.isPivoting(ship) != "no") return false;

        /* not necessary any longer, checked above
        var curheading = shipManager.movement.getLastCommitedMove(ship).heading;
        for (var i in ship.movement) {
            var movement = ship.movement[i];

            if (movement.turn != gamedata.turn || movement.type != "speedchange") continue;

            if (movement.value != accel && movement.heading == curheading || movement.value == accel && movement.heading != curheading) {
                return true;
            }
        }
        */

        return true;
    },

    adjustTurnDelay: function adjustTurnDelay(ship, oldspeed, newspeed) {
        var oldturndelay = Math.ceil(oldspeed * ship.turndelaycost);
        var newturndelay = Math.ceil(newspeed * ship.turndelaycost);
        var step = newturndelay - oldturndelay;
        var spentturndelay = newturndelay;

        if (ship.currentturndelay == 0 && step == 1) {
            // turndelay was 0. Re-check previous turn to see if the ship
            // moved enough to have also moved enough to cancel the new turn delay.
            for (var i in ship.movement) {
                var movement = ship.movement[i];
                if (movement.turn != gamedata.turn - 1) continue;

                if (movement.commit == false) continue;

                if (movement.type == "move" || movement.type == "slipright" || movement.type == "slipleft") spentturndelay--;

                if (shipManager.movement.isTurn(movement)) {
                    if (!ship.agile || !last || !shipManager.movement.isTurn(last)) spentturndelay = newturndelay;
                }
            }
        }

        ship.currentturndelay = ship.currentturndelay + step;

        if (ship.currentturndelay < 0) {
            ship.currentturndelay = 0;
        }

        ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);

        //        if(oldturndelay == 0){
        //            ship.currentturndelay = curTurnDelay;
        //        }else{
        //            ship.currentturndelay = adjustTurnDelay;
        //        }
        //        
        if (ship.currentturndelay < 0) {
            ship.currentturndelay = 0;
        }
    },

    changeSpeed: function changeSpeed(ship, accel) {
        if (!shipManager.movement.canChangeSpeed(ship, accel)) return false;
        if (gamedata.gamephase == -1) {
            shipManager.movement.doDeploymentAccel(ship, accel);
            return;
        }
        if (shipManager.movement.deleteSpeedChange(ship, accel)) {
            ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
            return;
        }

        var value = 0;
        if (accel) value = 1;

        var requiredThrust = Array(null, null, null, null, null);
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        var direction;

        if (shipManager.movement.isGoingBackwards(ship)) {
            direction = accel ? 1 : 2;
        } else {
            direction = accel ? 2 : 1;
        }

        var step = accel ? 1 : -1;
        var oldspeed = shipManager.movement.getSpeed(ship);
        var speed = oldspeed + step;

        // adjust the current turn delay if the new speed changes the turn delay
        //       shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
        
        if (speed < 0) {
            heading = mathlib.addToHexFacing(heading, 3);
            speed = speed * -1;
            value = 1;
        }

        var commit = false;
        var assignedThrust = Array();
        if (ship.flight) {
            commit = true;
            requiredThrust[0] = ship.accelcost;
            assignedThrust[0] = ship.accelcost;
        } else {
            var reqThrusterName = "main";
            if (!accel){ //!accel means it's deceleration instead
                reqThrusterName = "retro";  
            }
            var requiredThruster = shipManager.movement.thrusterDirectionRequired(ship,reqThrusterName);
            requiredThrust[requiredThruster] = ship.accelcost;
            
            /*no longer needed
            if (facing === heading) {
                requiredThrust[direction] = ship.accelcost;
            } else if (facing - 3 === heading) {
                requiredThrust[direction] = ship.accelcost;
            } else if (facing + 3 === heading) {
                requiredThrust[direction] = ship.accelcost;
            } else if (ship.gravitic) {
                if (facing + 5 === heading) {
                    if (accel) {
                        requiredThrust[4] = ship.accelcost;
                    } else requiredThrust[3] = ship.accelcost;
                } else if (facing - 5 === heading) {
                    if (accel) {
                        requiredThrust[3] = ship.accelcost;
                    } else requiredThrust[4] = ship.accelcost;
                } else if (facing + 4 === heading) {
                    if (accel) {
                        requiredThrust[4] = ship.accelcost;
                    } else requiredThrust[3] = ship.accelcost;
                } else if (facing - 4 === heading) {
                    if (accel) {
                        requiredThrust[3] = ship.accelcost;
                    } else requiredThrust[4] = ship.accelcost;
                } else if (facing + 3 === heading) {
                    if (accel) {
                        requiredThrust[2] = ship.accelcost;
                    } else requiredThrust[1] = ship.accelcost;
                } else if (facing - 3 === heading) {
                    if (accel) {
                        requiredThrust[1] = ship.accelcost;
                    } else requiredThrust[2] = ship.accelcost;
                } else if (facing + 2 === heading) {
                    if (accel) {
                        requiredThrust[3] = ship.accelcost;
                    } else requiredThrust[4] = ship.accelcost;
                } else if (facing - 2 === heading) {
                    if (accel) {
                        requiredThrust[4] = ship.accelcost;
                    } else requiredThrust[3] = ship.accelcost;
                } else if (facing + 1 === heading) {
                    if (accel) {
                        requiredThrust[3] = ship.accelcost;
                    } else requiredThrust[4] = ship.accelcost;
                } else if (facing - 1 === heading) {
                    if (accel) {
                        requiredThrust[4] = ship.accelcost;
                    } else requiredThrust[3] = ship.accelcost;
                }
            }
            */
        }

        var lm = shipManager.movement.getLastCommitedMove(ship);
        ship.movement[ship.movement.length] = {
            id: -1,
            type: "speedchange",
            position: lm.position,
            xOffset: lm.xOffset,
            yOffset: lm.yOffset,
            facing: lm.facing,
            heading: heading,
            speed: speed,
            animating: false,
            animated: true,
            animationtics: 0,
            requiredThrust: requiredThrust,
            assignedThrust: assignedThrust,
            commit: commit,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: value
        };

        ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);

        if (!ship.flight) {
            shipManager.movement.autoAssignThrust(ship);
            shipWindowManager.assignThrust(ship);
        }
    },

    getRemainingEngineThrust: function getRemainingEngineThrust(ship) {
        var rem = 0;
        if (ship.flight) {
            rem = ship.freethrust;
        } else {
            for (var i in ship.systems) {
                var system = ship.systems[i];
                if (shipManager.systems.isDestroyed(ship, system)) continue;

                if (system.name == "engine") {
                    rem += shipManager.systems.getOutput(ship, system);
                }
                if (system.name == "thruster") {
                    rem -= system.thrustwasted;
                }
                //tractor beams reduce thrust available!
                var crits = shipManager.criticals.hasCritical(system, "swtargetheld");
                rem -= crits;
            }
        }

        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            for (var a in movement.assignedThrust) {
                rem -= movement.assignedThrust[a];
            }
        }

        return rem;
    }, //endof function getRemainingEngineThrust
    
    getFullEngineThrust: function getRemainingEngineThrust(ship) {
        var rem = 0;
        if (ship.flight) {
            rem = ship.freethrust;
        } else {
            for (var i in ship.systems) {
                var system = ship.systems[i];
                if (shipManager.systems.isDestroyed(ship, system)) continue;

                if (system.name == "engine") {
                    rem += shipManager.systems.getOutput(ship, system);
                }
                //tractor beams reduce thrust available!
                var crits = shipManager.criticals.hasCritical(system, "swtargetheld");
                rem -= crits;
            }
        }

        return rem;
    }, //endof function getFullEngineThrust    

    getRemainingMovement: function getRemainingMovement(ship) {
        return shipManager.movement.getSpeed(ship) - shipManager.movement.getUsedMovement(ship);
    },

    getUsedMovement: function getUsedMovement(ship) {
        var used = 0;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.type == "move" || movement.type == "slipright" || movement.type == "slipleft") {
                if (movement.commit) used++;
            }
        }
        return used;
    },

    getSpeed: function getSpeed(ship) {
        return shipManager.movement.getLastCommitedMove(ship).speed;
    },

    getLastCommitedMove: function getLastCommitedMove(ship) {
        var lm;
        var first;
        if (!ship) {
            console.log("movement.getLastCommitedMove, ship is undefined");
            console.trace();
        }
        for (var i in ship.movement) {
            if (!first) first = ship.movement[i];
            if (ship.movement[i].commit == true) {
                lm = ship.movement[i];
            }
        }
        if (!lm) return first;
        return lm;
    },

    getFirstMoveOfTurn: function getFirstMoveOfTurn(ship) {
        for (var i in ship.movement) {
            var move = ship.movement[i];
            if (move.turn == gamedata.turn) return move;
        }
    },

    getPositionAtStartOfTurn: function getPositionAtStartOfTurn(ship, currentTurn) {
        if (currentTurn === undefined) {
            currentTurn = gamedata.turn;
        }
        
        /* this gives position after current turn move, NOT good
        var move = ship.movement.find(function(move) {
            return move.turn === currentTurn;
        });
        if (!move) {
            move = ship.movement[ship.movement.length - 1];
        }
        */
        //replacement:
        for (var i = ship.movement.length - 1; i >= 0; i--) {
            var move = ship.movement[i];
            if (move.turn < currentTurn) { //first move from earlier turn! this is what we need!
                break; //get out of loop
            } //if such a move is not found, first move of current turn would do - should be turn 1 and deployment move
        }        
        
        return new hexagon.Offset(move.position);
    },

    getPreviousLocation: function getPreviousLocation(ship) {
        var oPos = shipManager.getShipPosition(ship);
        for (var i = ship.movement.length - 1; i >= 0; i--) {
            var move = ship.movement[i];
            if (!oPos.equals(new hexagon.Offset(move.position))) return move.position;
        }
        return oPos;
    },

    getAmountChanneledReal: function getAmountChanneledReal(ship, system, ignoreUncommitted) {
        var used = 0;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (ignoreUncommitted && !movement.commit) continue;
            var assigned = movement.assignedThrust[system.id];
            if (assigned != undefined) {
                used += assigned;
            }
        }
        return used;
    },

    countAmountChanneled: function countAmountChanneled(ship, system) {
        var used = 0;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            var assigned = movement.assignedThrust[system.id];

            if (assigned != undefined) {
                used += assigned;
            }
        }
        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored")) used--;
        var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
        used = Math.ceil(used / (crits + 1));
        return used;
    },

    /*Marcin Sawicki: this seems to be a legacy function, returning directly countAmountChanneled instead of original result...*/
    getAmountChanneled: function getAmountChanneled(ship, system) {
        var used = shipManager.movement.countAmountChanneled(ship, system);

        /*
        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored")){
            used--;
        }
        
        var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
        if (crits > 0){
            used = used/(crits+1);
        }
        */

        return used;
    },

    getAmountWastedByCrits: function getAmountWastedByCrits(ship, system) {
        return system.thrustwasted;
    },

    assignThrust: function assignThrust(ship, system) {
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (shipManager.systems.isDestroyed(ship, system)) return false;
        var movement = ship.movement[ship.movement.length - 1];
        //var already = shipManager.movement.getAmountChanneledReal(ship, system);
        var already = shipManager.movement.getAmountChanneled(ship, system); //do check effective thrust, not engine thrust
        var step = 1;
        var wasted = 0;
        var turndelay = shipManager.movement.calculateTurndelay(ship, movement, movement.speed);
        var remainingThrust = shipManager.movement.getRemainingEngineThrust(ship);
        var thrustReq = shipManager.movement.calculateThrustStillReq(ship, movement);
        var isTurn = shipManager.movement.isTurn(movement);
        if (thrustReq[system.direction] <= 0 && thrustReq[0] <= 0 && !isTurn) {
            return false;
        }
        if (thrustReq[system.direction] <= 0 && thrustReq[0] <= 0 && isTurn && turndelay - 1 < 1) {
            return false;
        }

        if (shipManager.systems.getOutput(ship, system) * 2 < already + 1) //do check effective thrust, not engine thrust
            return false;

        var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
        if (crits > 0) {
            step = step * (crits + 1);
        }

        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored") && already <= 0) {
            step++;
        }

        var assigned = shipManager.movement.calculateAssignedThrust(ship, movement);
        var oreg = movement.requiredThrust;

        var maxreg = 0;
        var maxassigned = 0;

        for (var i = 0; i <= 4; i++) {
            if (oreg[i] && oreg[i] > maxreg) maxreg = oreg[i];

            if (system.direction != i && assigned[i] && assigned[i] > maxassigned) maxassigned = assigned[i];
        }

        if (assigned[system.direction] > maxreg && assigned[system.direction] > maxassigned) return false;
        if (remainingThrust < step) return false;

        if (movement.assignedThrust[system.id]) {
            movement.assignedThrust[system.id] += step;
        } else {
            movement.assignedThrust[system.id] = step;
        }

        system.thrustwasted += wasted;

        shipWindowManager.setDataForSystem(ship, system);
        shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "engine"));
        return true;
    },

    
    unAssignThrust: function unAssignThrust(ship, system) {
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (shipManager.systems.isDestroyed(ship, system)) return false;
        var movement = ship.movement[ship.movement.length - 1];
        var already = shipManager.movement.getAmountChanneledReal(ship, system);
        var step = 1;
        var wasted = 0;
        if (already - step < 0) return false;
        var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
        if (crits > 0) {
            step = step * (crits + 1);
        }

        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored") && already - step == 1) {
            step++;
        }

        if (movement.assignedThrust[system.id] >= step) {
            movement.assignedThrust[system.id] -= step;
        } else {}

        system.thrustwasted -= wasted;

        shipWindowManager.setDataForSystem(ship, system);
        shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "engine"));

        return true;
    },

    isGoingSideways: function isGoingSideways(ship) {
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        if (mathlib.addToHexFacing(facing, 2) == heading || mathlib.addToHexFacing(facing, -2) == heading) {
            return true;
        }
    },

    /*Marcin Sawicki: backwards in general (eg. Aft half circle), not necessary exactly aligned backwards)*/
    isGoingBackwards: function isGoingBackwards(ship) {
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        if (facing == heading || mathlib.addToHexFacing(facing, 1) == heading || mathlib.addToHexFacing(facing, -1) == heading) return false;
        return true;
    },

    hasTurned: function hasTurned(ship) {
        for (var i = 0; i < ship.movement.length; i++) {
            var m = ship.movement[i];
            if (m.turn != gamedata.turn) {
                continue;
            } else if (m.type == "turnleft" || m.type == "turnright") {
                return true;
            }
        }
        return false;
    },

    canTurn: function canTurn(ship, right) {
        if (gamedata.gamephase == -1 && ship.deploymove) return true;
        if (gamedata.gamephase != 2) return false;
        if (ship.osat) {
            for (var i = 0; i < ship.systems.length; i++) {
                var system = ship.systems[i];
                if (system.name === "thruster") {
                    if (system.destroyed) {
                        return false;
                    } else if (system.criticals[0] != null && system.criticals[0].phpclass == "OSatThrusterCrit") {
                        if (ship.movement[ship.movement.length - 1].type == "turnleft" || ship.movement[ship.movement.length - 1].type == "turnright") {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        
        /*no longer needed
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        */
        
        if (shipManager.movement.isRolling(ship) && !ship.gravitic) return false;
        if (shipManager.movement.checkHasUncommitted(ship)) return false;
        var turndelay = shipManager.movement.calculateCurrentTurndelay(ship);
        var previous = shipManager.movement.getLastCommitedMove(ship);
        if (turndelay > 0) {
            if (!(ship.agile && previous && previous.turn == gamedata.turn && shipManager.movement.isTurn(previous))) {
                return false;
            }
        }

        var speed = shipManager.movement.getSpeed(ship);
        var turncost = Math.ceil(speed * ship.turncost);
        turncost = Math.max(1,turncost);//turn cost may never be less than 1!
        if (shipManager.movement.getRemainingEngineThrust(ship) < turncost) {
            return false;
        }
        var pivoting = shipManager.movement.isPivoting(ship);
        if (pivoting != "no" && !ship.gravitic) {
            return false;
        }
        var rolling = shipManager.movement.isRolling(ship);
        if (rolling && !ship.gravitic) {
            return false;
        }
        
        if (!ship.gravitic && shipManager.movement.isOutOfAlignment(ship) ){
            return false;
        }
        /* replaced by code above
        if (heading !== facing && mathlib.addToHexFacing(heading, 3) !== facing && !ship.gravitic) {
            return false;
        }
        */

        return true;
    },

    doTurn: function doTurn(ship, right) {
        if (!ship.osat) {
            if (!shipManager.movement.canTurn(ship, right)) {
                return false;
            }
        }
        if (gamedata.gamephase == -1) {
            shipManager.movement.doDeploymentTurn(ship, right);
            return;
        }
        shipManager.movement.doNormalTurn(ship, right);

        /*
        if (ship.flight) {
            if (shipManager.movement.canTurnIntoPivot(ship, right)) {
                shipManager.movement.askForIntoPivotTurn(ship, right, "Do you wish to turn or turn into the pivot?");
            } else {
                shipManager.movement.doNormalTurn(ship, right);
            }
        } else if (ship.osat) {
            if (shipManager.movement.canTurn(ship, right)) {
                shipManager.movement.doNormalTurn(ship, right);
            }
        } else {
            if (shipManager.movement.canTurnIntoPivot(ship, right) && !ship.gravitic) {
                shipManager.movement.doIntoPivotTurn(ship, right);
            } else if (shipManager.movement.canTurnIntoPivot(ship, right) && ship.gravitic) {
                shipManager.movement.askForIntoPivotTurn(ship, right, "This ship has gravitic engines. Do you wish to turn or turn into the pivot?");
            } else {
                shipManager.movement.doNormalTurn(ship, right);
            }
        }
        */
    },

    doNormalTurn: function doNormalTurn(ship, right) {
        var requiredThrust = shipManager.movement.calculateRequiredThrust(ship, right);
        if (ship.osat) {
            requiredThrust = 0;
        }

        var lastMovement = ship.movement[ship.movement.length - 1];

        var name;
        var newfacing;
        var newheading;
        var step = 1;

        var commit = false;
        var assignedThrust = Array();

        name = "turnright";

        if (!right) {
            step = -1;
            name = "turnleft";
        }

        newfacing = mathlib.addToHexFacing(lastMovement.facing, step);
        newheading = mathlib.addToHexFacing(lastMovement.heading, step);

        if (ship.flight || ship.osat) {
            commit = true;
            assignedThrust[0] = requiredThrust[0];
        }

        ship.movement[ship.movement.length] = {
            id: -1,
            type: name,
            position: lastMovement.position,
            xOffset: lastMovement.xOffset,
            yOffset: lastMovement.yOffset,
            facing: newfacing,
            heading: newheading,
            speed: lastMovement.speed,
            animating: false,
            animated: false,
            animationtics: 0,
            requiredThrust: requiredThrust,
            assignedThrust: assignedThrust,
            commit: commit,
            preturn: false,
            at_initiative: shipManager.getIniativeOrder(ship),
            turn: gamedata.turn,
            forced: false,
            value: 0
        };

        if (!ship.flight) {
            shipManager.movement.autoAssignThrust(ship);
            shipWindowManager.assignThrust(ship);
        }
    },

    autoAssignThrust: function autoAssignThrust(ship) {
        var move = ship.movement[ship.movement.length - 1];
        var needArray = move.requiredThrust;
        var thrusterLoc = 0;               
                
        //Marcin Sawicki: no auto assignment for pivots!
        if (move.type == "pivotright" || move.type == "pivotleft") {
            return;   
        }

        //reset "channeled" value for all thrusters on a ship! (don't count on it to be correct BETWEEN assignments)
        for (var sys in ship.systems) {
            if (ship.systems[sys].displayName == "Thruster") {
                var thruster = ship.systems[sys];
                 thruster.channeled = shipManager.movement.getAmountChanneledReal(ship, thruster);
            }
        }        
        
        for (var loc in needArray) {
            var checked = 0;
            if (needArray[loc] == null || needArray[loc] < 1) {
                continue;
            }
            var thrusters = [];
            var toDo = needArray[loc];
            thrusterLoc = loc;
            //assign "any" thrust to main/retro thrusters
            if (thrusterLoc == 0) {
                if (shipManager.movement.isGoingBackwards(ship)) { //Marcin Sawicki: this skips Gravitic recognition but is good enough for auto!
                    thrusterLoc = 1;
                } else thrusterLoc = 2;
            }

            for (var sys in ship.systems) {
                if (ship.systems[sys].displayName == "Thruster") {
                    if (ship.systems[sys].direction == thrusterLoc && !ship.systems[sys].destroyed) {
                        if (ship.systems[sys].channeled < ship.systems[sys].output ) { //auto-assignment shall not overhrust
                            if (ship.systems[sys].criticals.length == 0) {
                                thrusters.push(ship.systems[sys]);
                            }
                        }
                    }
                }
            }

            if (thrusters.length < 1) {
                continue;
            }

            while (toDo > 0) {
                for (var j in thrusters) {
                    if (checked > 10) {
                        return;
                    }

                    if (thrusters[j].channeled + 1 > thrusters[j].output) {
                        checked++;
                        continue;
                    }
                    if (typeof move.assignedThrust[thrusters[j].id] == "undefined") {
                        move.assignedThrust[thrusters[j].id] = 1;
                        thrusters[j].channeled++;
                        toDo--;
                    } else {
                        move.assignedThrust[thrusters[j].id]++;
                        thrusters[j].channeled++;
                        toDo--;
                    }

                    shipWindowManager.setDataForSystem(ship, thrusters[j]);
                    if (toDo < 1) {
                        break;
                    }
                }
            }
        }
    },

    revertAutoThrust: function revertAutoThrust(ship) {
        if (ship.flight) {
            return;
        }

        var move = ship.movement[ship.movement.length - 1];
        var assignArray = move.assignedThrust;

        assignArray.forEach(function(amount, id) {
            if (amount === undefined) {
                return;
            }

            var system = ship.systems.find(function (system) { return system.id === id})

            if (!system) {
                throw new Error("Thruster not found")
            }

            system.channeled -= amount;
        })

        move.assignedThrust = []
    },

    /*calculate thrust required for turning*/
    calculateRequiredThrust: function calculateRequiredThrust(ship, right) {
        var requiredThrust = Array(null, null, null, null, null);

        var speed = shipManager.movement.getSpeed(ship);
        var turncost = Math.ceil(speed * ship.turncost);

        var side, sideindex, rear, rearindex, any;

        if (ship.flight) {
            if (turncost == 0) turncost = 1;
            requiredThrust[0] = turncost;
            return requiredThrust;
        }

        if (speed === 0) { //at speed 0 - cost is 1 thrust, and can be channeled through literally any thruster
            return Array(1, 0, 0, 0, 0);
        }

        side = Math.floor(turncost / 2);
        rear = Math.floor(turncost / 2);
        any = turncost % 2;

        requiredThrust[0] = any;

        var reqThrusterName = "main";
        var requiredThruster = shipManager.movement.thrusterDirectionRequired(ship,reqThrusterName);
        requiredThrust[requiredThruster] = rear;
        reqThrusterName = "stbd";
        if (right){ //turn to Stbd requres Port thruster
            reqThrusterName = "port";  
        }
        requiredThruster = shipManager.movement.thrusterDirectionRequired(ship,reqThrusterName);
        requiredThrust[requiredThruster] = side;        

        return requiredThrust;
    }, //endof function calculateRequiredThrust

    
    calculateAssignedThrust: function calculateAssignedThrust(ship, movement) {
        var assignedarray = Array(null, null, null, null, null);
        for (var i in movement.assignedThrust) {
            if (!ship.systems[i]) continue;
            var system = ship.systems[i];

            var mod = 1;
            var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
            if (crits > 0) {
                mod = 1 / (crits + 1);
            }

            var sub = 0;
            if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored")) {
                if (shipManager.movement.getAmountChanneledReal(ship, system, true) === 0) sub = 1;
            }

            assignedarray[ship.systems[i].direction] += Math.ceil(movement.assignedThrust[i] * mod) - sub;
            if (assignedarray[ship.systems[i].direction] < 0) assignedarray[ship.systems[i].direction] = 0;
        }
        return assignedarray;
    },

    /*called when point of thrust is assigned*/
    calculateThrustStillReq: function calculateThrustStillReq(ship, movement) {
        var assignedarray = shipManager.movement.calculateAssignedThrust(ship, movement);
        var requiredThrust = movement.requiredThrust;
        var stillReq = requiredThrust.slice();
        var any = 0;

        for (var i in requiredThrust) {
            var req = requiredThrust[i];
            if (req == null) {
                stillReq[i] = null;
                continue;
            }
            var ass = assignedarray[i];

            if (ass > req) {
                stillReq[i] = 0;
                any += ass - req;
            } else {
                stillReq[i] -= ass;
            }
        }

        stillReq[0] -= any;

        if (movement.type == "pivotright" || movement.type == "pivotleft") {
            /*
              pivot clockwise (right) requires main/stbd or retro/port combo;
              pivot counterclockwise (left) - main/port or retro/stbd
              so when first point of thrust is allocated (to any thruster), some other thrusters become unavailable
              and generic requirement transforms into specific one
              below determination which one!
            */
            var portDirection = shipManager.movement.thrusterDirectionRequired(ship,"port");
            var stbdDirection = shipManager.movement.thrusterDirectionRequired(ship,"stbd");
            var mainDirection = shipManager.movement.thrusterDirectionRequired(ship,"main");
            var retroDirection = shipManager.movement.thrusterDirectionRequired(ship,"retro");
            if (movement.type == "pivotright") { //clockwise
                if (assignedarray[retroDirection] > 0 || assignedarray[portDirection] > 0) {
                    stillReq[mainDirection] = null;
                    stillReq[stbdDirection] = null;
                }else if (assignedarray[mainDirection] > 0 || assignedarray[stbdDirection] > 0) {
                    stillReq[retroDirection] = null;
                    stillReq[portDirection] = null;
                }
            } else { //counterclockwise
                if (assignedarray[retroDirection] > 0 || assignedarray[stbdDirection] > 0) {
                    stillReq[mainDirection] = null;
                    stillReq[portDirection] = null;
                }else if (assignedarray[mainDirection] > 0 || assignedarray[portDirection] > 0) {
                    stillReq[retroDirection] = null;
                    stillReq[stbdDirection] = null;
                }
            }
            
            /*replaced by above
            var reversed = shipManager.movement.hasSidesReversedForMovement(ship);
            var right = movement.type == "pivotright";
            if (reversed) {
                right = !right;
            }

            if (right) {
                if (assignedarray[1] > 0 || assignedarray[3] > 0) {
                    stillReq[2] = null;
                    stillReq[4] = null;
                }
                if (assignedarray[2] > 0 || assignedarray[4] > 0) {
                    stillReq[1] = null;
                    stillReq[3] = null;
                }
            } else {
                if (assignedarray[1] > 0 || assignedarray[4] > 0) {
                    stillReq[2] = null;
                    stillReq[3] = null;
                }
                if (assignedarray[2] > 0 || assignedarray[3] > 0) {
                    stillReq[1] = null;
                    stillReq[4] = null;
                }
            }
            */
        }

        return stillReq;
    }, //endof function calculateThrustStillReq

    
    calculateCurrentTurndelay: function calculateCurrentTurndelay(ship) {
        var turndelay = Math.ceil(ship.movement[ship.movement.length - 1].speed * ship.turndelaycost);
        var last = null;
        var didTurn = false;
        if (gamedata.turn == 1) {
            turndelay = 0;
        }
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.commit == false) continue;
            if ((movement.type == "move" || movement.type == "slipright" || movement.type == "slipleft") && turndelay > 0) turndelay--;
            if (shipManager.movement.isTurn(movement)) {
                didTurn = true;
                if (!ship.agile || !last || !shipManager.movement.isTurn(last)) {
                    // calculate the turndelay using the NEW speed, iso of the one
                    // in this old movement.
                    turndelay = shipManager.movement.calculateTurndelay(ship, movement, ship.movement[ship.movement.length - 1].speed);
                }
            }
            last = movement;
        }
        if (turndelay < 0) turndelay = 0;
        if (turndelay > 0 && ship.turndelaycost > 1) {
            if (!didTurn) {
                turndelay = 0;
            }
        }
        return turndelay;
    }, //endof calculateCurrentTurndelay

    
    calculateTurndelay: function calculateTurndelay(ship, movement, speed) {
        // speed as a seperate parameter needed to allow for calculation with new speed.
        if (speed == 0) return 0;
        if (ship.turndelaycost == 0) return 0;
        var turndelay = Math.ceil(speed * ship.turndelaycost);
        if (ship.flight) return turndelay; //Marcin Sawicki: fighters are NOT exception to delay rules! But so far fighters cannot overthrust...
        turndelay -= shipManager.movement.calculateExtraThrustSpent(ship, movement);
        //if (turndelay < 1) turndelay = 1; //Marcin Sawicki: I think this just adds turn delay after accel when delay is satisfied exactly...
        if (turndelay < 0) turndelay = 0; //Marcin Sawicki: just in case, no negative values
        return turndelay;
    },

    calculateExtraThrustSpent: function calculateExtraThrustSpent(ship, movement) {
        var reg = shipManager.movement.calculateThrustStillReq(ship, movement);
        var extra = 0 - reg[0];
        if (extra < 0) extra = 0;
        return extra;
    },

    isTurn: function isTurn(movement) {
        if (!movement) console.trace();
        return movement.type == "turnright" || movement.type == "turnleft";
    },

    /*Marcin Sawicki - actually this should no longer be needed! as new thruster allocation takes care of such things
    hasSidesReversedForMovement: function hasSidesReversedForMovement(ship) {
        var back = shipManager.movement.isGoingBackwards(ship);
        var reversed = (back || shipManager.movement.isRolled(ship)) && !(back && shipManager.movement.isRolled(ship));
        return reversed;
    },
    */
    
    
    /*return thruster direction required from text input - for readability*/
    directionNoFromName: function directionNoFromName(direction) {
        var thrusterDirectionNo = 0;
        switch(direction) {
            case "retro": 
                thrusterDirectionNo = 1;
                break;
            case "main": 
                thrusterDirectionNo = 2;
                break;
            case "port": 
                thrusterDirectionNo = 3;
                break;
            case "stbd": 
                thrusterDirectionNo = 4;
                break;
            default: //let's assume number was used...
                thrusterDirectionNo = direction;
                break;
        }
        return thrusterDirectionNo;
    },
    
    
    /*returns thruster direction actually required - from thruster direction ship in default alignment would need
      eg. if requirement is '2' (main), it should return:
      - normal alignment: 2
      - reverse: 1
      - side: 1 or 2 usually, 3 or 4 Gravitic
      - ...add roll on top of that!
      
      DIRECTION may be text ("port","stbd","main","retro")
    */
    thrusterDirectionRequired: function thrusterDirectionRequired(ship,direction) {
        var orientationRequired = shipManager.movement.directionNoFromName(direction);
        
        if (orientationRequired>2 && shipManager.movement.isRolled(ship)){ //rolled reverses side requirements
            if (orientationRequired==3){
                orientationRequired=4;
            }else{
                orientationRequired=3;
            }
        }
        if (shipManager.movement.isGoingBackwards(ship)){ //moving backwards reverses all requirements
            switch(orientationRequired) {
                case 1: 
                    orientationRequired = 2;
                    break;
                case 2: 
                    orientationRequired = 1;
                    break;
                case 3: 
                    orientationRequired = 4;
                    break;
                case 4: 
                    orientationRequired = 3;
                    break;
            }
        }
        
        //Gravitic allowsfurther rotations if pivoted (eg. not moving exactly forward or backwards)...
        if(ship.gravitic){
            if(shipManager.movement.isPivotedPort(ship)){ //pivoted to Port means: Stbd is Retro, Main is Stbd, Port is Main, Retro is Port
                switch(orientationRequired) {
                    case 1: 
                        orientationRequired = 3;
                        break;
                    case 2: 
                        orientationRequired = 4;
                        break;
                    case 3: 
                        orientationRequired = 2;
                        break;
                    case 4: 
                        orientationRequired = 1;
                        break;
                }
            }else if (shipManager.movement.isPivotedStbd(ship)){//pivoted to Stbd means: Stbd if Main, Main is Port, Port is Retro, Retro is Stbd
                switch(orientationRequired) {
                    case 1: 
                        orientationRequired = 4;
                        break;
                    case 2: 
                        orientationRequired = 3;
                        break;
                    case 3: 
                        orientationRequired = 1;
                        break;
                    case 4: 
                        orientationRequired = 2;
                        break;
                }
            }
        }        
        return orientationRequired;
    }, //endof thrusterDirectionRequired
    
    
    /*basically goes neither ahead nor backwards*/
    isOutOfAlignment: function isOutOfAlignment(ship) {
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;        
        if (facing == heading || mathlib.addToHexFacing(facing, 3) == heading || mathlib.addToHexFacing(facing, -3) == heading) return false; //in alignment either way
        return true;
    },
    
    
    /*is pivoted to Port == goes Stbd-forward*/
    isPivotedPort: function isPivotedPort(ship) {
        if (!shipManager.movement.isOutOfAlignment(ship)) return false; //ship in alignment is certainly not pivoted anywhere
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;        
        if (mathlib.addToHexFacing(facing, 1) == heading || mathlib.addToHexFacing(facing, 2) == heading) return true; 
        return false;
    },
    
    /*is pivoted to Stbd == goes Port-forward*/
    isPivotedStbd: function isPivotedStbd(ship) {
        if (!shipManager.movement.isOutOfAlignment(ship)) return false; //ship in alignment is certainly not pivoted anywhere
        if (!shipManager.movement.isPivotedPort(ship)) return false; //ship pivoted to Port is not pivoted to Starboard
        return false;
    },
    
    
    /*
    addMove: function(ship, name, x, y, facing, heading, speed, animated, requiredThrust, commit, preturn, forced){
        
        ship.movement[ship.movement.length] = {
            id:-1,
            type:name,
            x:lm.x,
            y:lm.y,
            xOffset:0,
            yOffset:0,
            facing:facing,
            heading:heading,
            speed:speed,
            animating:false,
            animated:animated,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:Array(),
            commit:commit,
            preturn:preturn,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:forced,
            value:0
        }
    },
    */

};
