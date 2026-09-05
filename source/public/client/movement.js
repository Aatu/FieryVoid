"use strict";

shipManager.movement = {

    getTurnCost: function (ship) {
        var baseTurnCost = ship.turncost;
        if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
            for (var attachedId in ship.hasAttached) {
                var attachedShip = gamedata.getShip(attachedId);
                if (attachedShip && !attachedShip.flight) {
                    baseTurnCost += attachedShip.turncost;
                }
            }
        }
        return Math.round(baseTurnCost * 100) / 100;
    },

    getTurnDelayCost: function (ship) {
        var baseTurnDelayCost = ship.turndelaycost;
        if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
            for (var attachedId in ship.hasAttached) {
                var attachedShip = gamedata.getShip(attachedId);
                if (attachedShip && !attachedShip.flight) {
                    baseTurnDelayCost += attachedShip.turndelaycost;
                }
            }
        }
        return Math.round(baseTurnDelayCost * 100) / 100;
    },

    // LCV Rails (B5W §10.1): each LCV docked on a rail makes a turn/turn-delay cost
    // +1 THRUST this turn (a FLAT surcharge on the final turn-cost/turn-delay value,
    // NOT a change to the ship's turncost/turndelaycost rate). E.g. a turn-cost-1
    // carrier at speed 5 normally pays ceil(5*1)=5 thrust to turn; with 4 LCVs
    // docked it pays 5+4=9. The server sends ship.dockedLCVs (count) only when >0.
    getDockedLcvTurnSurcharge: function getDockedLcvTurnSurcharge(ship) {
        return (ship && ship.dockedLCVs) ? (parseInt(ship.dockedLCVs, 10) || 0) : 0;
    },

    isManeuverBlockedByAttachment: function (ship) {
        if (ship.hasAttached && Object.keys(ship.hasAttached).length > 0) {
            for (var attachedId in ship.hasAttached) {
                var attachedShip = gamedata.getShip(attachedId);
                // Check if it's a ship (e.g. Grappling Claw attachment) and not a fighter/pod
                if (attachedShip && !attachedShip.flight) {
                    if (attachedShip.shipSizeClass > ship.shipSizeClass) {
                        return true;
                    }
                }
            }
        }
        return false;
    },

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

        /* REINFORCEMENTS_PLAN.md STAGE 7 - AN ARRIVAL'S FACING IS THE DOORWAY'S (plan §2.4). A unit
           comes out of a jump point travelling the way the vortex points; it does not get to pick.
           Heading as well as facing, and the two are set together here so nothing downstream has to
           know that this ship was placed differently from any other.

           HERE rather than in DeploymentPhaseStrategy.onHexClicked because there is more than one
           way to place a unit - the hex click, the SelectFromShips "DEPLOY HERE" button, a
           re-placement onto the same hex - and every one of them ends in this function. Speed is
           deliberately untouched: it is the player's to choose (§2.4) and canChangeSpeed already
           allows it during Deployment.

           Set on BOTH branches above, so re-placing keeps the facing rather than only the first
           placement getting it. */
        var arrivalFacing = shipManager.movement.getArrivalFacing(ship);
        if (arrivalFacing !== null && ship.deploymove) {
            ship.deploymove.facing = arrivalFacing;
            ship.deploymove.heading = arrivalFacing;
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

        if (newSpeed >= 0 && newSpeed <= 10) { //originally 2-7, changed as people wanted to try scenarios where that was too limiting
            ship.deploymove.speed += value;
        }
    },

    isMovementReady: function isMovementReady(ship) {
        // Exempt cases are always "ready" regardless of any plotted moves: they are
        // moved by the server, cannot move, or have not deployed yet.
        if (shipManager.isDestroyed(ship) ||
            gamedata.isTerrain(ship.shipSizeClass, ship.userid) ||
            (shipManager.getTurnDeployed(ship) > gamedata.turn) ||
            shipManager.movement.isUncontrolled(ship) ||
            (Object.keys(ship.attached).length !== 0 && !ship.detached)) {
            return true;
        }

        // Otherwise the ship is ready only when it has used all its movement AND has
        // no uncommitted maneuver left dangling. A turn/pivot/roll whose required
        // thrust cannot be satisfied (e.g. its thrusters are destroyed) stays
        // uncommitted, and must block the Commit button so an unpayable maneuver can
        // never be submitted - the player sees the unmet requirement (with the
        // destroyed thruster icons) and has to delete the illegal maneuver first.
        return shipManager.movement.getRemainingMovement(ship) == 0 &&
            !shipManager.movement.checkHasUncommitted(ship);
    },

    //HK Jamming: a remote-controlled flight that is Uncontrolled this turn is moved by
    //the server (drift), so the player is never prompted for it (treated as movement-ready).
    //Gated on remoteControl (very rare) so ordinary ships short-circuit immediately.
    //Uncontrolled is a oneturn crit placed on turn T (effect T+1): match crit.turn+1 === turn.
    isUncontrolled: function isUncontrolled(ship) {
        if (!ship.remoteControl || !ship.flight) return false;
        var firstFighter = shipManager.systems.getSystem(ship, 1);
        if (!firstFighter || !firstFighter.criticals) return false;
        for (var i in firstFighter.criticals) {
            var crit = firstFighter.criticals[i];
            if (crit.phpclass === "Uncontrolled" && (crit.turn + 1) === gamedata.turn) return true;
        }
        return false;
    },

    checkHasUncommitted: function checkHasUncommitted(ship) {

        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.commit == false) return true;
        }

        return false;
    },

    hasDeletableMovements: function hasDeletableMovements(ship) {
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!        

        var lastMove = ship.movement[ship.movement.length - 1];
        if (lastMove && lastMove.type === "attached") return false; // Mirrored moves are not deletable manually

        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            if (gamedata.gamephase == 3) {
                if (movement.value == "combatpivot" && (movement.type == "pivotleft" || movement.type == "pivotright")) {
                    return true;
                }
            } else {
                if (!movement.preturn && !movement.forced && movement.type != "deploy" && movement.type != "attached") return true;
            }
        }

        return false;
    },


    deleteMove: function deleteMove(ship) {
        var movement = ship.movement[ship.movement.length - 1];
        if (movement.type == "attached") return; // Cannot delete mirrored moves

        if (!movement.preturn && !movement.forced && movement.turn == gamedata.turn) {
            if (gamedata.gamephase == 3 && (movement.value != "combatpivot" || movement.type != "pivotleft" && movement.type != "pivotright")) return;

            if (movement.type == "detach") {
                ship.detached = false;
            }

            // adjust the current turn delay if the new speed changes the turn delay
            //var oldspeed = shipManager.movement.getSpeed(ship);
            shipManager.movement.revertAutoThrust(ship);
            ship.movement.splice(ship.movement.length - 1, 1);
            //var speed = shipManager.movement.getSpeed(ship);
            //shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
            ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
            //var shipwindow = $(".shipwindow_" + ship.id);
            //shipWindowManager.cancelAssignThrust(ship);
            if (movement.type == "contract") shipManager.movement.amendContractValue(ship, -movement.value);//For Contraction, need to amend level.
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
                //console.log("I am going to delete ", movement)
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
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (gamedata.gamephase != 2) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!
        if (!ship.flight && ship.jinkinglimit <= 0) return false;
        if (accel == 0) return true;
        if (accel > 0 && shipManager.movement.getRemainingEngineThrust(ship) <= 0) return false; //only adding jink costs thrust; reducing refunds it
        var jinking = shipManager.movement.getJinking(ship);
        //Gravitic Augmenter grants forced jink levels (marked forced=true) that the player
        //may not remove: a reduction cannot take the total below that forced floor.
        var floor = shipManager.movement.getForcedJinking(ship);
        if (jinking + accel > ship.jinkinglimit || jinking + accel < floor) return false;
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

    //Sum of jink levels the player cannot remove this turn (Gravitic Augmenter forced jinks,
    //marked forced=true server-side). Acts as the lower bound for reducing jinking.
    getForcedJinking: function getForcedJinking(ship) {
        var j = 0;
        for (var i in ship.movement) {
            var move = ship.movement[i];
            if (move.turn != gamedata.turn) continue;

            if (move.type == "jink" && move.forced) j += move.value;
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

                //Never remove a forced jink (Gravitic Augmenter minimum) - only the player's own.
                if (move.type == "jink" && !move.forced) {
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
                shipManager.movement.updateAssignThrust(ship);
            }
        }
    },

    canRoll: function canRoll(ship) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (gamedata.gamephase != 2) return false;
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!         

        if (ship.flight || ship.osat) return false;
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (ship.rollcost > shipManager.movement.getRemainingEngineThrust(ship)) {
            return false;
        }
        var rolling = shipManager.movement.isRolling(ship);
        if (!shipManager.movement.isRollingForIcon(ship) && rolling) return false; //Started rolling this movement phase, player should cancel instead.
        if (rolling) return true; //rolling ship should be always able to stop...
        if ((!ship.agile) && shipManager.movement.hasRolled(ship)) {
            return false;
        }
        if (shipManager.movement.isPivoting(ship) != "no" && !ship.gravitic) {
            return false;
        }

        return true;
    },


    canEmergencyRoll: function canEmergencyRoll(ship) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (gamedata.gamephase != 2) return false;
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;
        if (ship.flight || ship.osat) return false;
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (ship.rollcost > shipManager.movement.getRemainingEngineThrust(ship)) {
            return false;
        }
        var rolling = shipManager.movement.isRolling(ship);
        if (!shipManager.movement.isRollingForIcon(ship) && rolling) return false; //Started rolling this movement phase, player should cancel instead.        	
        if (rolling) return true; //rolling ship should be always able to stop...
        if ((!ship.agile) && shipManager.movement.hasRolled(ship)) {
            return false;
        }
        //For emergency rolls ships should be pivoting, and not gravitic!
        if (shipManager.movement.isPivoting(ship) == "no") return false;
        if (ship.gravitic) return false;

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
            value: 'thisTurn'
        };
        shipManager.movement.updateAssignThrust(ship);
        ship.rolling = true;

    },


    doEmergencyRoll: function doEmergencyRoll(ship) {
        if (!shipManager.movement.canEmergencyRoll(ship)) return false;
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
            value: 'emergencyRoll'
        };
        shipManager.movement.updateAssignThrust(ship);
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
        if (!rolling && Object.keys(ship.attached).length !== 0 && !ship.detached) {
            var hostId = parseInt(Object.keys(ship.attached)[0]);
            var hostShip = gamedata.getShip(hostId);
            if (hostShip && !hostShip.agile && !hostShip.flight) {
                for (var j in hostShip.movement) {
                    var hm = hostShip.movement[j];
                    if (hm.turn != gamedata.turn) continue;
                    if (hm.type == "isRolling") rolling = true;
                    if (hm.type == "roll" && hm.commit) rolling = !rolling;
                }
            }
        }
        return rolling;
    },

    //Only difference between this and isRolling() is that this returns 'false' if Roll was initiated on THIS turn!
    isRollingForIcon: function isRollingForIcon(ship) {
        var rolling = false;
        if (ship.agile) return false;
        for (var i in ship.movement) {
            var m = ship.movement[i];
            if (m.turn != gamedata.turn) continue;
            if (m.value == 'thisTurn' || m.value == 'emergencyRoll') continue;
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
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) {
            var hostId = parseInt(Object.keys(ship.attached)[0]);
            var hostShip = gamedata.getShip(hostId);
            if (hostShip && !hostShip.flight) {
                for (var j in hostShip.movement) {
                    var hm = hostShip.movement[j];
                    if (hm.turn != gamedata.turn) continue;
                    if (hm.type == "roll" || hm.type == "isRolling") return true;
                }
            }
        }
        return false;
    },


    canMove: function canMove(ship) {
        if (gamedata.gamephase != 2) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!       

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

    /*just move ahead using all remaining movement*/
    doMoveFully: function doMoveFully(ship) {
        while (shipManager.movement.getRemainingMovement(ship) > 0) {
            if (shipManager.movement.doMove(ship) === false) break; // break only on explicit false (prevents infinite loop when canMove() returns false)
        }
    },


    /* ===== JUMPING OUT THROUGH A VORTEX (JUMP_POINTS_PLAN.md Stage 4, sections 2.2 and 2.5) ====
       A unit whose plotted path enters an OPEN vortex hex through the hex side the vortex faces
       may leave the battle there. Ordering it ends that unit's movement immediately - the hexes
       it had left are forfeit - and the server removes it at the end of the Movement phase.

       This is the CLIENT MIRROR of Movement::getJumpOutVortex (source/server/handlers/movement.php),
       which is authoritative and re-runs the same test on the submitted path. The rule is stated
       once in plan section 2.2 and implemented twice; keep the two in step. */

    /* The open jump vortex standing in hex 'pos', or null.

       A vortex is a SpawnJumpPoint terrain unit. It is OPEN from the turn AFTER it was declared
       (JumpEngine::restoreVortexFromNote sets spawned = declaration turn + 1, so on the declaring
       turn there is only the yellow "Jump Point Forming" marker) until the turn after it closes.
       removedTurn is closeTurn + 1 - the first turn the vortex is gone - so a vortex stays usable
       for the whole of the turn it closes on, which is why the closed test is >= and not >. */
    /* Is this unit a jump vortex? One place holds the class name, because two other files ask:
       getVortexInHex below, and getInterestingStuffInPosition in PhaseStrategy, which keeps the
       vortex out of the click/hover sweep entirely. phpclass reaches the client on the STATIC
       blueprint (model/ship.js merges by faction + phpclass), so it is always present. */
    isJumpVortex: function isJumpVortex(unit) {
        return !!unit && unit.phpclass === "SpawnJumpPoint";
    },

    /* REINFORCEMENTS_PLAN.md §2.6 - IS THIS THE OTHER KIND OF VORTEX, the blue one units come OUT
       of? A SpawnJumpPointExit. phpclass is set by that class's own constructor and reaches the
       client both on the live payload and on the preloaded blueprint, so this is always answerable.

       ⭐ isJumpVortex ABOVE IS DELIBERATELY LEFT ENTRANCE-ONLY, and must not be widened into a two-value
       test. Its callers do not agree on the verdict: getVortexInHex, getVortexHeldBy and everything
       downstream of them (canJumpOut, the Maintain control, the "already holding a jump point open"
       refusal, the closing-vortex commit warning) are all rules an exit must FAIL, while the
       icon's z-plane, the map overlay colour, the hex-stack sweep and the replay lifecycle animation
       are all things it must MATCH. Widening the one predicate would silently flip the first group.
       Sites that want either kind ask isAnyJumpVortex instead.

       ⚠️⚠️ TWO CLASS NAMES, NOT ONE (Stage 9), and this is the client half of plan trap 23. On the
       server SpawnJumpPointPhaseIn IS a SpawnJumpPointExit - one `instanceof` catches both, for
       free, everywhere. Here the test is a phpclass STRING, so a subclass matches nothing unless it
       is named; leave it out and the phasing doorway stops being a doorway on this side only, which
       surfaces as arrivals with nowhere to stand and a Jump Out button offered on a blue vortex. */
    isJumpVortexExit: function isJumpVortexExit(unit) {
        return !!unit
            && (unit.phpclass === "SpawnJumpPointExit" || unit.phpclass === "SpawnJumpPointPhaseIn");
    },

    /* REINFORCEMENTS_PLAN.md STAGE 9 - IS THIS THE DOORWAY THAT IS NEVER DRAWN?

       A Shadow hull (any legacy/PhasingDrive engine) fades in rather than tearing a vortex open, so
       its arrival point is a SpawnJumpPointPhaseIn: the same unit in every rule that matters, with
       no terrain on the map to show for it (user ruling 2026-08-29).

       ⭐ ONE READER THAT MATTERS - shipManager.shouldBeHidden. Everything visual keys off that one
       predicate (the icon, the facing arrow, the hover/click sweep, the hex ship-list, the replay
       lifecycle animation), so suppressing the unit there suppresses it everywhere at once. Adding
       a second `if (isPhaseInVortex)` anywhere else is a sign the suppression is in the wrong place.
       The other reader is the ballistic marker's LABEL, which says REINFORCEMENTS rather than
       "Jump Point Forming" because for a phasing hull nothing is forming. */
    isPhaseInVortex: function isPhaseInVortex(unit) {
        return !!unit && unit.phpclass === "SpawnJumpPointPhaseIn";
    },

    /* IS THIS JUMP ENGINE ON THE OLD ONE-CLICK JUMP? (Stage 9.)

       ⚠️ THERE IS NO FLAG TO READ. JumpEngine::$legacyJump is PROTECTED, so json_encode never emits
       it and it reaches neither the payload nor a static blueprint - deliberately, because a public
       default would cost all 776 jump engines in the tree a key each. What markLegacy() leaves
       behind that IS readable is the three properties it flips: ballistic and hextarget cleared
       (and ShipCompactor strips a false key outright, so both read undefined) and range zeroed.

       ⚠️ ALL THREE, and `s.range > 0` rather than `s.range !== 0`: an absent key is undefined, and
       undefined !== 0. The nine engines in the stale uncompacted "Earth Alliance (Custom)"
       blueprint carry none of the three keys, so they read as legacy here - which is the safe way
       round, since the only thing this drives is a marker LABEL.

       Used for exactly one thing: whether the blue declaration marker says REINFORCEMENTS (a hull
       that will fade in, leaving no vortex) or "Jump Point Forming" (a hull that will tear one
       open). It is NOT the gate test - a gate's engine passes all three - and it must never be used
       to decide whether an arrival is legal, which is a server rule with no legacy test in it. */
    isLegacyJumpEngine: function isLegacyJumpEngine(system) {
        if (!system || system.name !== 'jumpEngine') return false;
        return !system.ballistic || !system.hextarget || !(system.range > 0);
    },

    /* Either kind of vortex - for the sites that care that this is a hole in space, not which way
       it points: how deep to draw it, what colour it collapses to when zoomed out, that it must
       stay out of the click/hover sweep, and that it forms and closes on screen. */
    isAnyJumpVortex: function isAnyJumpVortex(unit) {
        return shipManager.movement.isJumpVortex(unit) || shipManager.movement.isJumpVortexExit(unit);
    },

    getVortexInHex: function getVortexInHex(pos) {
        for (var i in gamedata.ships) {
            var unit = gamedata.ships[i];
            if (!shipManager.movement.isJumpVortex(unit)) continue;
            if (unit.spawned !== undefined && unit.spawned !== -1 && unit.spawned > gamedata.turn) continue; //still forming
            if (unit.removed && unit.removedTurn != null && gamedata.turn >= unit.removedTurn) continue; //already closed
            var vortexMove = shipManager.movement.getLastCommitedMove(unit);
            if (!vortexMove) continue; //no deploy row yet - nothing to stand in
            var vortexPos = new hexagon.Offset(vortexMove.position);
            if (vortexPos.q == pos.q && vortexPos.r == pos.r) return unit;
        }

        return null;
    },

    /* JUMP_POINTS_PLAN.md Stage 5 - the OPEN vortex this ship is holding open, or null.

       vortexHolderId is stamped on the vortex unit by JumpEngine::restoreVortexState from the
       'Vortex' note's shipid, so it names the ship whose Jump Engine owns the jump point - not
       merely a ship belonging to the same player, which is what a userid comparison would give.
       A player may have several ships with jump engines and only the holder may maintain.

       Returns null on the turn the vortex was DECLARED: getVortexInHex skips a unit that is still
       forming, and a forming vortex cannot be maintained anyway (the opening declaration is that
       turn's declaration). Mirrors JumpEngine::hasOpenVortex on the server. */
    getVortexHeldBy: function getVortexHeldBy(ship) {
        if (!ship) return null;

        for (var i in gamedata.ships) {
            var unit = gamedata.ships[i];
            if (!shipManager.movement.isJumpVortex(unit)) continue;
            if (unit.vortexHolderId != ship.id) continue;

            //Same open/closed window getVortexInHex applies, asked of the unit we already have.
            if (shipManager.movement.getVortexInHex(shipManager.getShipPosition(unit)) === unit) return unit;
        }

        return null;
    },

    /* REINFORCEMENTS_PLAN.md STAGE 7 - THE EXIT THIS UNIT ARRIVES THROUGH, or null. The mirror
       of JumpEngine::getArrivalVortex, and the client's whole answer to "where may I put this?".

       ⭐ THE JOIN IS arrivalVia -> vortexHolderId AND BOTH HALVES ALREADY REACH THE CLIENT.
       arrivalVia names the OPENER (the vortex did not exist when the manifest was named), and
       SpawnJumpPoint::stripForJson has sent vortexHolderId since Jump Points Stage 5 - it is what
       getVortexHeldBy above reads. So no new payload field was needed for any of Stage 7.

       ⚠️ A NULL arrivalVia MEANS "ITS OWN DOORWAY", not "unassigned": an opener always comes
       through the exit it opened, and the Stage 6 sweep stamps it from the list of exits
       that formed rather than from a berth. Read as unassigned, the one unit guaranteed a doorway
       would be the one unable to use it.

       ⚠️ isJumpVortexExit, NOT isJumpVortex - the two are deliberately separate predicates and
       an ENTRANCE is never a legal arrival hex (§2.6). The open/closed window is getVortexInHex's,
       repeated here rather than delegated because that helper is entrance-only by design. */
    getArrivalVortex: function getArrivalVortex(ship) {
        if (!shipManager.isArrivingReinforcement(ship)) return null;

        var openerId = (ship.arrivalVia === null || ship.arrivalVia === undefined)
            ? ship.id : ship.arrivalVia;

        return shipManager.movement.getExitHeldBy(openerId);
    },

    /* REINFORCEMENTS_PLAN.md STAGE 8 - THE OPEN EXIT $holderId's JUMP ENGINE IS HOLDING, or
       null. The exit twin of getVortexHeldBy above, and the body getArrivalVortex was written
       as before a GATE could hold one of these.

       ⭐ IT TAKES AN ID, NOT A SHIP, and that is not a convenience. The two callers ask it of
       different things - an arriving unit's arrivalVia (which is a number off the payload) and a
       gate the Manage Reinforcements menu is deciding whether to list - and every ship object on
       this client is replaced wholesale on each poll that carries ship data, so an id is the only
       stable form of the question ([[REINFORCEMENTS_PLAN.md]] trap 17).

       ⚠️ NOT getVortexHeldBy WITH A WIDER PREDICATE. That one runs its open/closed test by asking
       getVortexInHex, which is ENTRANCE-ONLY by design (see isJumpVortex) and would answer null for
       every exit. The window is therefore repeated here rather than delegated - it is the same
       three lines the server's JumpEngine::getArrivalVortex carries for the same reason.

       ⚠️ vortexHolderId NAMES THE OPENER - a reinforcement's own hull, or the GATE. It is stamped by
       JumpEngine::restoreVortexState from the 'Vortex' note's shipid, so a gate's doorway names the
       gate and a berth on it names the gate too: the join needs nothing new. */
    getExitHeldBy: function getExitHeldBy(holderId) {
        if (holderId === null || holderId === undefined) return null;

        for (var i in gamedata.ships) {
            var unit = gamedata.ships[i];
            if (!shipManager.movement.isJumpVortexExit(unit)) continue;
            if (unit.vortexHolderId == null) continue;              //no 'Vortex' note - nothing to join on
            if (unit.vortexHolderId != holderId) continue;
            if (unit.spawned !== undefined && unit.spawned !== -1 && unit.spawned > gamedata.turn) continue; //still forming
            if (unit.removed && unit.removedTurn != null && gamedata.turn >= unit.removedTurn) continue;     //closed
            if (!shipManager.movement.getLastCommitedMove(unit)) continue; //no deploy row: no hex, no facing

            return unit;
        }

        return null;
    },

    /* The facing an arriving reinforcement is FORCED onto, or null if it has no doorway.

       ⚠️ NOT getVortexEntryDirection. On an ENTRANCE the facing names the mouth a unit crosses inbound,
       so a ship using one travels in the OPPOSITE direction (F+3). On an EXIT the arrow points
       outward - it is the doorway out - and an arriving unit is placed facing the way it points
       (plan §0 / §2.4). Reusing the entrance helper here would drop every wave onto the board facing
       backwards, and would still look plausible on screen. */
    getArrivalFacing: function getArrivalFacing(ship) {
        var vortex = shipManager.movement.getArrivalVortex(ship);
        if (!vortex) return null;

        var move = shipManager.movement.getLastCommitedMove(vortex);
        if (!move) return null;

        //isNaN rather than a bare parse: the caller writes this straight onto a MovementOrder, and
        //a NaN facing is not null - it would sail past the "is there a forced facing?" test in
        //deploy() and be submitted, where the server's === would refuse the whole placement.
        var facing = parseInt(move.facing, 10);
        return isNaN(facing) ? null : (((facing % 6) + 6) % 6);
    },

    /* The travel direction a unit must be moving in to enter 'vortex'.

       The facing names the vortex's MOUTH - the doorway into hyperspace - and a unit uses it by
       crossing that side INBOUND, so it has to be travelling in the opposite direction:
       D = (F + 3) % 6. Direction 0 is EAST and increases clockwise on screen. */
    getVortexEntryDirection: function getVortexEntryDirection(vortex) {
        var move = shipManager.movement.getLastCommitedMove(vortex);
        if (!move) return null;
        return (parseInt(move.facing, 10) + 3) % 6;
    },

    /* The direction of the step that last carried 'ship' INTO the hex it is standing in, or null
       if there is no such step.

       Walks BACKWARDS through the whole movement array, earlier turns included, because plan
       section 2.2 judges a unit already sitting in the hex on the last step that put it there. A
       unit with no such step at all - deployed there, a base, something that has never moved -
       returns null and cannot use a vortex. So does a unit that arrived by a relocation rather
       than a step (MicroJumpSystem), because the hex it came from is not a neighbour.

       Reading POSITIONS rather than move TYPES is what makes a sideslip work: a slip enters the
       hex from the side it slipped toward, and that is the direction judged, not the heading. */
    getEntryDirection: function getEntryDirection(ship) {
        var moves = ship.movement;
        if (!moves || moves.length < 2) return null;

        var here = new hexagon.Offset(moves[moves.length - 1].position);

        for (var i = moves.length - 1; i > 0; i--) {
            var from = new hexagon.Offset(moves[i - 1].position);
            if (from.equals(here)) continue; //same hex - keep walking back to the step that entered it

            for (var direction = 0; direction < 6; direction++) {
                if (from.getNeighbourAtDirection(direction).equals(here)) return direction;
            }

            return null; //not a neighbour: the unit was put here, it did not fly in
        }

        return null;
    },

    /* The vortex this unit may leave through right now, or null. Both halves of the rule: it has
       to be standing in an open vortex's hex, and the step that put it there has to have been
       travelling into that vortex's mouth. */
    getUsableVortex: function getUsableVortex(ship) {
        var vortex = shipManager.movement.getVortexInHex(shipManager.getShipPosition(ship));
        if (!vortex) return null;

        var entry = shipManager.movement.getEntryDirection(ship);
        if (entry === null) return null;
        if (entry !== shipManager.movement.getVortexEntryDirection(vortex)) return null;

        return vortex;
    },

    /* Has this unit already ordered a jump-out this turn? Read by getRemainingMovement and by
       every manoeuvre gate below, so ordering one really does end the unit's movement. Deleting
       the order (the ordinary cancel-movement button) undoes all of it. */
    hasJumpedOut: function hasJumpedOut(ship) {
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn == gamedata.turn && movement.type == "jumpout") return true;
        }

        return false;
    },

    /* Has this unit COMMITTED a jump-out - i.e. is the order on the board rather than merely
       plotted? The distinction is the movement row's id: a locally plotted order carries -1
       until it is submitted, and after the commit the reloaded gamedata carries its real
       database id.

       That is the moment the unit stops being part of the battle for display purposes. The
       server does not actually remove it until the END of the Movement phase (Movement::
       resolveJumpOuts), but movement is sequential, so without this the hex keeps a ghost in it
       while everyone else takes their turn. Read by shipManager.shouldBeHidden (the sprite) and
       by fleetListManager (the ship's row and its docked flights' rows).

       ⚠️ It only becomes true on a client that has been sent fresh ships, which is NOT the
       moment of the commit: submitTacGamedata answers the POST with a bare {}, and a WAITING
       player's poll is answered with the last_update timestamp alone, so the committing player
       keeps their own order at id -1 until they are activated again or the phase ends. Their own
       sprite therefore lingers a while; every other viewer sees it go as soon as they are served
       the order, which is also when TacGamedata::hideActiveShipMovement stops masking it (once
       that initiative bracket has passed).

       Purely presentational, and self-correcting: the server re-checks the entry rule when the
       phase resolves, so a tampered order that it refuses simply comes back on the next load. */
    hasCommittedJumpOut: function hasCommittedJumpOut(ship) {
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn == gamedata.turn && movement.type == "jumpout" && movement.id > 0) return true;
        }

        return false;
    },

    canJumpOut: function canJumpOut(ship) {
        if (gamedata.gamephase != 2) return false;
        if (!ship || shipManager.isDestroyed(ship)) return false;
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
        if (ship.mine) return false;
        /* STAGE 6 - FIGHTER FLIGHTS ARE NOW OFFERED IT TOO, closing the Stage 4 gap. They have no
           primary Structure, so Movement::applyJumpOut takes a flight off the board by destroying
           every CRAFT in it with a HyperspaceJump entry instead - see the note there for the three
           records that move one level down. Nothing is needed here beyond removing the block: a
           flight plots movement like anything else, and a DOCKED flight is already refused by the
           isDestroyed test above (a docked flight is removed=true). */
        //An attached pod leaves with its host and never decides for itself (plan section 5 trap 7).
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false;
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        //An unpayable manoeuvre still dangling would be dropped by the server's thrust validation,
        //taking the path that reached the vortex with it. Make the player resolve it first.
        if (shipManager.movement.checkHasUncommitted(ship)) return false;
        //getUsableVortex reads the last committed move; a unit with no movement rows at all has none.
        if (!ship.movement || ship.movement.length === 0) return false;

        return shipManager.movement.getUsableVortex(ship) !== null;
    },

    doJumpOut: function doJumpOut(ship) {
        if (!shipManager.movement.canJumpOut(ship)) return false;

        var vortex = shipManager.movement.getUsableVortex(ship);
        var lm = ship.movement[ship.movement.length - 1];

        //Position/facing/heading/speed all copy the move that entered the hex: this order marks
        //the unit as leaving, it does not move it. 'value' carries the vortex id, which is what
        //the server re-validates against (the same field 'detach' uses for its host id).
        ship.movement[ship.movement.length] = {
            id: -1,
            type: "jumpout",
            position: lm.position,
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
            value: vortex.id
        };

        return true;
    },

    canDetach: function canDetach(ship) {
        if (gamedata.gamephase != 2) return false;
        if (Object.keys(ship.attached).length === 0) return false;
        if (shipManager.movement.hasDeletableMovements(ship)) return false;
        return true;
    },

    doDetach: function doDetach(ship) {
        var lm = ship.movement[ship.movement.length - 1];
        var hostShipId = Object.keys(ship.attached)[0];
        ship.detached = true; //Mark detached this movement.
        var facing = lm.facing;
        if(ship.flight) facing = mathlib.addToHexFacing(lm.facing, 3); //Pods always face away from host ship when they detach.    
        
        ship.movement[ship.movement.length] = {
            id: -1,
            type: "detach",
            position: lm.position,
            xOffset: 0,
            yOffset: 0,
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
            forced: false,
            value: hostShipId
        };
    },



    canSlip: function canSlip(ship, right) {
        if (gamedata.gamephase != 2) return false;
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!         

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
            if (movement.type == othername) return false;//cannot sideslip if sideslip in opposite direction was already made (eg. can sideslip only in one direction in a turn)
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

        var requiredThrust = Array(null, null, null, null, null);

        var commit = false;
        var assignedThrust = Array();

        if (ship.flight) {
            commit = true;
            requiredThrust[0] = slipcost;
            assignedThrust[0] = slipcost;
        } else {
            var reqThrusterName = "stbd";
            if (name == "slipright") { //slip to Stbd requres Port thruster
                reqThrusterName = "port";
            }
            var requiredThruster = shipManager.movement.thrusterDirectionRequired(ship, reqThrusterName, false, true);
            requiredThrust[requiredThruster] = slipcost;
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
            shipManager.movement.updateAssignThrust(ship);
        }


    },

    canRotate: function canRotate(ship) {
        if (ship.base && (!ship.nonRotating)) {
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

    doRotate: function doRotate(ship, silent) {
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
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!         

        if (shipManager.systems.isEngineDestroyed(ship)) return false;
        if (ship.osat && (!ship.flight)) return false;
        var name = right ? "pivotright" : "pivotleft";
        var othername = right ? "pivotleft" : "pivotright";
        if (shipManager.movement.isRolling(ship) && !ship.gravitic) return false;
        var hasPivoted = shipManager.movement.hasPivoted(ship);
        var hasTurnedIntoPivot = shipManager.movement.hasTurnedIntoPivot(ship);//New check to see if ship has ended a pivot this turn by turning into it DK 09.24
        var isPivoting = shipManager.movement.isPivoting(ship);
        if (hasTurnedIntoPivot && !ship.agile) return false;//New check to see if ship has ended a pivot this turn by turning into it DK 09.24
        if (hasPivoted.right && isPivoting != "right" && right && !ship.agile) return false;
        if (hasPivoted.left && isPivoting != "left" && !right && !ship.agile) return false;
        if (right && isPivoting == "left" || !right && isPivoting == "right" && !ship.agile) {
            return false;
        }
        //		if (!shipManager.movement.hasJustTurnedIntoPivot(ship)){ //don't look at thrust available for pivot cancelling IF previous maneuver is turn into pivot. No longer needed.
        if (ship.pivotcost > shipManager.movement.getRemainingEngineThrust(ship) && gamedata.gamephase != 3) return false;
        //		}
        if (ship.flight && gamedata.gamephase == 3) {
            if (!weaponManager.canCombatTurn(ship)) return false;
            if (Math.ceil(ship.pivotcost * 1.5) > shipManager.movement.getRemainingEngineThrust(ship)) return false;
        } else if (gamedata.gamephase != 2) return false;

        return true;
    },
    /* //No longer needed
        hasJustTurnedIntoPivot: function hasJustTurnedIntoPivot(ship){
            if(shipManager.movement.isOutOfAlignment(ship)) return false; //if ship is out of alignment, then it hasn't just turned into pivot
            if (shipManager.movement.isPivoting(ship) == "no" ) return false; //if it's not pivoting, then theres noting to talk about
            //was last maneuver a turn?
            var lastmove = shipManager.movement.getLastCommitedMove(ship);
            if (lastmove.turn != gamedata.turn) return false;//not this turn
            if ((lastmove.type != 'turnright') && (lastmove.type != 'turnleft')) return false; //not actually a turn ;)
            //ship is pivoting, last maneuver was a turn and it brought ship in alignment - call it turn into pivot!
            return true;
        },
    */
    //New function to check if ship turned into apivot at any point in turn DK 09.24
    hasTurnedIntoPivot: function hasTurnedIntoPivot(ship) {
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            if (movement.value === 'turnIntoPivot') {
                return true;
            }
        }
        return false;
    },


    countCombatPivot: function countCombatPivot(ship) {
        var c = 0;
        if (ship.flight) { //Just check flights, is now called in FirePhaseStrategy.js - DK 10.24
            for (var i in ship.movement) {
                var move = ship.movement[i];
                if (move.turn != gamedata.turn) continue;
                if (move.value == "combatpivot") c++;
            }
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


        //		if (shipManager.movement.hasJustTurnedIntoPivot(ship)){ //just after turning into pivot - cancelling pivot is free! No longer needed.
        //			pivotcost = 0;
        //		}

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
            shipManager.movement.updateAssignThrust(ship);
        }


    },

    doForcedPivot: function doForcedPivot(ship, silent) {
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

            //New check to see if ship turned into a pivot this turn, and therefore is not pivoting anymore! DK 09.24
            if (movement.value === 'turnIntoPivot') pivoting = "no";

        }
        if (pivoting === "no" && Object.keys(ship.attached).length !== 0 && !ship.detached) {
            var hostId = parseInt(Object.keys(ship.attached)[0]);
            var hostShip = gamedata.getShip(hostId);
            if (hostShip && !hostShip.agile && !hostShip.flight) {
                for (var j in hostShip.movement) {
                    var hm = hostShip.movement[j];
                    if (hm.turn != gamedata.turn) continue;
                    if (hm.commit == false) continue;
                    if (hm.type == "isPivotingLeft") pivoting = "left";
                    if (hm.type == "isPivotingRight") pivoting = "right";
                    if (hm.type == "pivotright" && pivoting == "no" && hm.preturn == false) pivoting = "right";
                    if (hm.type == "pivotleft" && pivoting == "no" && hm.preturn == false) pivoting = "left";
                    if (hm.type == "pivotright" && pivoting == "left" && hm.preturn == false) pivoting = "no";
                    if (hm.type == "pivotleft" && pivoting == "right" && hm.preturn == false) pivoting = "no";
                    if (hm.value === 'turnIntoPivot') pivoting = "no";
                }
            }
        }
        return pivoting;
    },

    isHalfPhased: function isHalfPhased(ship) {
        var toReturn = false;
        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;
            if (movement.commit == false) continue;
            if (movement.type == "halfPhase") {
                toReturn = true;
                break;
            }
        }
        return toReturn;
    },

    //Shadow ships ability! here it will rely on halfPhaseThrust attribute non-zero value to recognize the ability.
    canHalfPhase: function canHalfPhase(ship) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (ship.halfPhaseThrust == 0) return false; //ship is not capable of half phasing
        if (gamedata.gamephase != 2) return false;
        if (shipManager.movement.isHalfPhased(ship)) return false;

        //needs to have appropriate thrust left 
        if (ship.halfPhaseThrust > shipManager.movement.getRemainingEngineThrust(ship)) return false;

        //needs enabled and undamaged jump drive
        var dmg = 0;
        var phasedrive = shipManager.systems.getSystemByName(ship, "jumpEngine"); //assume it's phase drive, as it's on phase-capable ship!
        if (phasedrive) {
            //full health?
            dmg = damageManager.getDamage(ship, phasedrive);
            if (dmg > 0) return false;
            //powered?
            if (shipManager.power.isOffline(ship, phasedrive)) return false;
        } else return false;//no phase drive!

        //needs two undamaged BioThrusters
        var countFreshBiothrusters = 0;
        for (var i in ship.systems) {
            var biothruster = ship.systems[i];
            if (biothruster.name == 'BioThruster') {
                dmg = damageManager.getDamage(ship, biothruster);
                if (dmg == 0) countFreshBiothrusters++;
                if (countFreshBiothrusters >= 2) break; //no point in looping through further systems!
            }
        }
        if (countFreshBiothrusters < 2) return false;

        return true; //no indication to the contrary found
    },


    doHalfPhase: function doNormalTurn(ship) {
        var requiredThrust = Array(0, 0, ship.halfPhaseThrust, 0, 0); //all through main thrusters - irrelevant really for BioThrusters!
        var lastMovement = ship.movement[ship.movement.length - 1];

        var name;
        var newfacing;
        var newheading;
        var step = 1;

        var commit = false;
        var assignedThrust = Array();

        name = "halfPhase";

        ship.movement[ship.movement.length] = {
            id: -1,
            type: name,
            position: lastMovement.position,
            xOffset: lastMovement.xOffset,
            yOffset: lastMovement.yOffset,
            facing: lastMovement.facing,
            heading: lastMovement.heading,
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
            shipManager.movement.updateAssignThrust(ship);
        }
    },



    canContract: function canContract(ship, value) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (gamedata.gamephase != 2) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!         

        var canContract = false;
        var contraction = 0;
        for (var i in ship.systems) {
            var system = ship.systems[i];
            if (system.hasOwnProperty('contraction')) {
                canContract = true;
                contraction = system.contraction;
                break;
            }
        }

        if (canContract == false) return false;

        var remThrust = shipManager.movement.getRemainingEngineThrust(ship);
        var speed = shipManager.movement.getSpeed(ship);
        var contractCostBase = Math.round(speed * shipManager.movement.getTurnCost(ship)); //turn cost should be 1.33
        var contractCost = Math.max(2, contractCostBase); //Minimum of 2 thrust

        if (value == 1 && contractCost > remThrust) return false;//Not enough thrust to contract. 
        if (value == -1 && this.getContraction(ship) == 0) return false;

        return true;
    },

    getContraction: function getContraction(ship) {
        var contraction = 0;

        for (var i in ship.movement) {
            var move = ship.movement[i];

            if (move.turn != gamedata.turn) continue;

            if (move.type == "contract") {
                contraction += move.value;//Will +1 depending on Contraction.
            }
        }

        return contraction;
    },

    doContraction: function doContraction(ship, value) {
        if (!shipManager.movement.canContract(ship, value)) return;

        var remThrust = shipManager.movement.getRemainingEngineThrust(ship);
        var speed = shipManager.movement.getSpeed(ship);
        var contractCostBase = Math.round(speed * shipManager.movement.getTurnCost(ship)); //turn cost should be 1.33
        var contractCost = Math.max(2, contractCostBase);	//Minimum of 2 thrust        	

        var lastMovement = ship.movement[ship.movement.length - 1];

        var commit = false;
        var assignedThrust = Array();
        var requiredThrust = Array(contractCost, 0, 0, 0, 0);

        var lm = shipManager.movement.getLastCommitedMove(ship);

        if (value < 0) {//Check if the decrease cancels an increase this turn, in which case just refund.
            for (var i in ship.movement) {
                var move = ship.movement[i];
                if (move.turn != gamedata.turn) continue;

                if (move.type == "contract") {
                    ship.movement.splice(i, 1);
                    shipManager.movement.amendContractValue(ship, value);
                    break;
                }
            }
        } else {

            ship.movement[ship.movement.length] = {
                id: -1,
                type: "contract",
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
                value: value
            };

            shipManager.movement.updateAssignThrust(ship);
        }

    },


    amendContractValue: function amendContractValue(ship, value) {

        var mindriderEngine = null;

        for (var i in ship.systems) {
            var engineSystem = ship.systems[i];
            if (engineSystem.hasOwnProperty('contraction')) {
                engineSystem.contraction += value;
                if (engineSystem.contraction <= 0) engineSystem.contraction = 0;
                //Now update ship stats/Engine tooltip	
                ship.forwardDefense -= value;
                ship.sideDefense -= value;
                engineSystem.data['Contraction Level'] = engineSystem.contraction;
                //And store Engine system for next part.				    
                mindriderEngine = engineSystem;
                break;
            }
        }

        if (mindriderEngine) {
            // Calculate the new armour boost based on multiples of 3
            var newArmourBoost = Math.floor(mindriderEngine.contraction / 3);

            for (var j in ship.systems) {
                var system = ship.systems[j];

                if (system instanceof ThoughtShield) {
                    // Adjust ThoughtShield health
                    system.currentHealth += value;
                } else {
                    // Adjust armour based on the change in contraction
                    if (system.armour === undefined) continue; // Skip systems without armour property
                    var currentBoost = system._armourBoost || 0; // Track previous boost
                    system.armour -= currentBoost; // Remove previous boost
                    system.armour += newArmourBoost; // Apply new boost
                    system._armourBoost = newArmourBoost; // Store the current boost for future adjustments
                }
            }
        }

        webglScene.customEvent('SystemDataChanged', { ship: ship, system: mindriderEngine });

    },


    canTurnIntoPivot: function canTurnIntoPivot(ship, right) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (gamedata.gamephase != 2) return false;
        //if (ship.agile) returnVal = false; //agile ship should be able to turn into pivot all right...
        if (ship.flight) return false; //Every turn is a turn into pivot for fighters/shuttles, no need for extra movement type.
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!         

        /*cannot turn into pivot if unit is aligned...*/
        if (!shipManager.movement.isOutOfAlignment(ship)) return false;
        if (shipManager.movement.isRolling(ship) && !ship.gravitic) return false; //Cannot turn at all if rolling, unless gravitic

        var turndelay = shipManager.movement.calculateCurrentTurndelay(ship);
        if (turndelay > 0) return false; //cannot turn into pivot if turn delay is not satisfied!

        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        var reverseheading = mathlib.addToHexFacing(heading, 3);

        //is turning affordable in the first place?
        var speed = shipManager.movement.getSpeed(ship);
        var baseTurnCost = shipManager.movement.getTurnCost(ship);
        if (ship.submarine && shipManager.movement.isGoingBackwards(ship)) baseTurnCost = baseTurnCost * 1.33; //Subs have a weird rule about turning backwards.
        var turncost = Math.ceil(speed * baseTurnCost);
        turncost = Math.max(1, turncost);//turn cost may never be less than 1!
        turncost += shipManager.movement.getDockedLcvTurnSurcharge(ship);//LCV Rails: +1 thrust/turn per docked LCV

        if (shipManager.movement.getRemainingEngineThrust(ship) < turncost) {
            return false;
        }

        var step = right ? -1 : 1;
        //if (mathlib.addToHexFacing(step, facing) === heading || mathlib.addToHexFacing(step, facing) === reverseheading) returnVal = true;
        if (mathlib.addToHexFacing(step, facing) === heading || mathlib.addToHexFacing(step, facing) === reverseheading) return true;
        return false;
    },


    doIntoPivotTurn: function doIntoPivotTurn(ship, right) {
        if (ship.hasOwnProperty('mindrider')) ship.mindrider = false;//Aug 2024 - Mindrider thurster rules don't apply to turnintopivots, remove marking

        var requiredThrust = shipManager.movement.calculateRequiredThrust(ship, right);
        var lastMovement = ship.movement[ship.movement.length - 1];

        if (ship.hasOwnProperty('mindrider')) ship.mindrider = true; //And reapply after calculateRequiredThrust()

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
            value: 'turnIntoPivot'
        };

        if (!ship.flight) {
            shipManager.movement.updateAssignThrust(ship);
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
        if (!left && !right && Object.keys(ship.attached).length !== 0 && !ship.detached) {
            var hostId = parseInt(Object.keys(ship.attached)[0]);
            var hostShip = gamedata.getShip(hostId);
            if (hostShip && !hostShip.flight) {
                for (var j in hostShip.movement) {
                    var hm = hostShip.movement[j];
                    if (hm.turn != gamedata.turn) continue;
                    if (hm.type == "pivotleft" && hm.preturn == false) left = true;
                    if (hm.type == "pivotright" && hm.preturn == false) right = true;
                }
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
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) {
            var hostId = parseInt(Object.keys(ship.attached)[0]);
            var hostShip = gamedata.getShip(hostId);
            if (hostShip && !hostShip.flight) {
                for (var j in hostShip.movement) {
                    var hm = hostShip.movement[j];
                    if (hm.turn != gamedata.turn) continue;
                    if (hm.type == "pivotleft" || hm.type == "pivotright") return true;
                    if (hm.type == "isPivotingRight" || hm.type == "isPivotingLeft") return true;
                }
            }
        }
        return false;
    },

    canChangeSpeed: function canChangeSpeed(ship, accel) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (ship.osat || ship.base || gamedata.isTerrain(ship.shipSizeClass, ship.userid)) {
            return false;
        }
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!   

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
            if (movement.preturn == false &&
                movement.forced == false &&
                movement.type != "speedchange" &&
                movement.type != "deploy" &&
                movement.type != "sync" &&
                movement.type != "attached" &&
                movement.type != "detach") return false;
        }

        //gravitic ship with enough thrust can accelerate, no matter her alignment
        if (ship.gravitic) return true;

        //ship cannot accelerate if it's not aligned OR pivoting    
        if (shipManager.movement.isOutOfAlignment(ship) || shipManager.movement.isPivoting(ship) != "no") return false;

        return true;
    },

    adjustTurnDelay: function adjustTurnDelay(ship, oldspeed, newspeed) {
        var oldturndelay = Math.ceil(oldspeed * shipManager.movement.getTurnDelayCost(ship));
        var newturndelay = Math.ceil(newspeed * shipManager.movement.getTurnDelayCost(ship));
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
            if (!accel) { //!accel means it's deceleration instead
                reqThrusterName = "retro";
            }
            var requiredThruster = shipManager.movement.thrusterDirectionRequired(ship, reqThrusterName, true);
            requiredThrust[requiredThruster] = ship.accelcost;
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
            shipManager.movement.updateAssignThrust(ship);
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
                    rem += shipManager.systems.getOutput(ship, system); //Is zero when offline.
                }
                if (system.name == "thruster") {
                    rem -= system.thrustwasted;
                }
                //tractor beams reduce thrust available!
                var crits = shipManager.criticals.hasCritical(system, "swtargetheld");
                rem -= crits;
            }
        }

        //Added loop to look for Thrust-boosted weapons and deduct boost amount from Engine total.
        var thrustWeaponList = shipManager.systems.getSystemListThrustBoosted(ship);
        for (var i in thrustWeaponList) {
            var currWeapon = thrustWeaponList[i];
            //is it alive and powered up?
            if (shipManager.systems.isDestroyed(ship, currWeapon)) continue; //Checks for destroyed and offline are ok!
            if (shipManager.power.isOffline(ship, currWeapon)) continue;
            //current boost
            var currBoost = shipManager.power.getBoost(currWeapon);
            if (currBoost > 0)
                currBoost = currWeapon.thrustPerBoost * currBoost;
            rem -= currBoost;
        }
        //		rem -= currBoost;


        //Added loop to look for Thrust-boosted weapons and deduct boost amount from Engine total.
        /*		var thrustWeaponList = shipManager.systems.getSystemListThrustBoosted(ship);
                for (var i in thrustWeaponList) {
                    var currWeapon = thrustWeaponList[i];
                    //is it alive and powered up?
                    if (shipManager.systems.isDestroyed(ship, currWeapon)) continue; //Checks for destroyed and offline are ok!
                    if (shipManager.power.isOffline(ship, currWeapon)) continue;			
                    //current boost
                    var currBoost = shipManager.power.getBoost(currWeapon);
                    if (currBoost > 0) rem -= currBoost;
                }
        */


        for (var i in ship.movement) {
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn) continue;

            for (var a in movement.assignedThrust) {
                rem -= movement.assignedThrust[a];
            }
        }

        return rem;
    }, //endof function getRemainingEngineThrust


    //Returns ship OBJECTS, not names: the commit-error dialogs render these through
    //gamedata.shipNameSpan, which needs ship.id to make the name clickable (scroll-to-ship).
    getShipsNegativeThrust: function getShipsNegativeThrust() {
        var ships = [];
        var counter = 0;

        for (var i in gamedata.ships) {
            var ship = gamedata.ships[i];
            if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) continue;
            if (ship.unavailable) continue;
            if (ship.flight) continue;
            if (ship.userid != gamedata.thisplayer) continue;
            if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) continue;

            var deployTurn = shipManager.getTurnDeployed(ship);
            if (deployTurn > gamedata.turn) continue;  //Don't bother checking for ships that haven't deployed yet.

            // Get the list of engine systems
            var engines = shipManager.systems.getSystemListByName(ship, "engine");
            if (!engines || engines.length === 0) continue; // Skip if no engines are found

            // Check if all engines are destroyed or offline
            var allEnginesDestroyedOrOffline = engines.every(engine =>
                shipManager.systems.isDestroyed(ship, engine) ||
                shipManager.power.isOffline(ship, engine)
            );
            if (allEnginesDestroyedOrOffline) continue; // Skip if all engines are destroyed or offline

            // Check if the remaining thrust is negative for any engine
            var hasNegativeThrust = engines.some(engine =>
                shipManager.movement.getRemainingEngineThrust(ship, engine) < 0
            );

            if (hasNegativeThrust) {
                ships[counter] = ship;
                counter++;
            }
        }

        return ships;
    },

    hasNegativeThrust: function hasNegativeThrust(ship) {
        if (gamedata.isTerrain(ship.shipSizeClass, ship.userid)) return false;
        if (ship.unavailable) return false;
        if (ship.flight) return false;
        if (ship.userid != gamedata.thisplayer) return false;
        if (shipManager.isDestroyed(ship) || shipManager.power.isPowerless(ship)) return false;

        var deployTurn = shipManager.getTurnDeployed(ship);
        if (deployTurn > gamedata.turn) return false;  //Don't bother checking for ships that haven't deployed yet.

        // Get the list of engine systems
        var engines = shipManager.systems.getSystemListByName(ship, "engine");
        if (!engines || engines.length === 0) return false; // Skip if no engines are found

        // Check if all engines are destroyed or offline
        var allEnginesDestroyedOrOffline = engines.every(engine =>
            shipManager.systems.isDestroyed(ship, engine) ||
            shipManager.power.isOffline(ship, engine)
        );
        if (allEnginesDestroyedOrOffline) return false; // Skip if all engines are destroyed or offline

        // Check if the remaining thrust is negative for any engine
        var hasNegativeThrust = engines.some(engine =>
            shipManager.movement.getRemainingEngineThrust(ship, engine) < 0
        );

        return hasNegativeThrust;
    },


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
        //JUMPING OUT ends the unit's movement immediately and the hexes it had left are FORFEIT
        //(JUMP_POINTS_PLAN.md section 2.5). Returning 0 here is what does it: canMove and canSlip
        //both read this, and isMovementReady reads it to arm the Commit button.
        if (shipManager.movement.hasJumpedOut(ship)) return 0;
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
    /*
        getPositionAtStartOfTurn: function getPositionAtStartOfTurn(ship, currentTurn) {
            if (currentTurn === undefined) {
                currentTurn = gamedata.turn;
            }
            var moveNo = -1;
            for (var i = ship.movement.length - 1; i >= 0; i--) {
                var move = ship.movement[i];
                moveNo = i;
                if (move.turn < currentTurn) { //first move from earlier turn! this is what we need! 
                    break; //get out of loop
                } //if such a move is not found, first move of current turn would do - should be turn 1 and deployment move
            }        
            if ( (move.type == 'start') && ((moveNo+1) < ship.movement.length) ){ //start move is not suitable! pick next one if at all possible
                move = ship.movement[moveNo+1];
            }
            
            return new hexagon.Offset(move.position);
        },
    */
    //New function as sometimes fighter movements were erroring during late Deployment phases when called from isEscorting - DK Jul 2025
    getPositionAtStartOfTurn: function getPositionAtStartOfTurn(ship, currentTurn) {
        if (currentTurn === undefined) {
            currentTurn = gamedata.turn;
        }

        // Normalize keys into sorted array (ascending numerically)
        const keys = Object.keys(ship.movement)
            .map(k => parseInt(k))
            .filter(k => !isNaN(k))
            .sort((a, b) => a - b);

        let move = null;
        let moveNo = -1;

        // Walk backward from last key
        for (let i = keys.length - 1; i >= 0; i--) {
            let idx = keys[i];
            let candidate = ship.movement[idx];

            if (candidate.turn < currentTurn) {
                move = candidate;
                moveNo = idx;
                break;
            }
        }

        // Fallback: if none from earlier turns, take earliest move available
        if (!move && keys.length > 0) {
            moveNo = keys[0];
            move = ship.movement[moveNo];
        }

        // Handle 'start' edge case
        if (move && move.type === 'start') {
            let nextIndex = keys.find(k => k > moveNo);
            if (nextIndex !== undefined) {
                move = ship.movement[nextIndex];
            }
        }

        return new hexagon.Offset(move.position);
    },


    /* Two units in one hex have no real bearing on each other, so mathlib.getCompassHeadingOfShip
       fakes one from direction of travel: it stands the unit back in the hex it came from and takes
       the bearing from there. Forced Pre-Firing movement - Gravitic Mine pull, Gravity Net,
       Transverse Drive, Warp Jump - appends a 'prefire' order that teleports the unit AFTER movement
       is done, and a plain walk back then answers with the hex it was DRAGGED out of instead of the
       hex it flew in from. That rotates the bearing by the drag angle and drops the target out of
       arc even though a drag moves everything in the hex together and changes nothing between them.
       So walk back from where movement itself left the unit, then slide that answer along the drag
       vector - a rigid translation, which is what a drag actually is. Note the translation is
       essential and not just tidiness: pairing a pre-drag origin with a post-drag destination is
       wrong by the drag angle, a whole hex facing. Done in cube space so row parity is handled.
       Undragged units take the original path below, unchanged. */
    getPreviousLocation: function getPreviousLocation(ship) {
        var oPos = shipManager.getShipPosition(ship);
        var i, move, pos;

        //Where the unit's own movement left it, ignoring any forced Pre-Firing shift.
        var movedPos = null;
        for (i = ship.movement.length - 1; i >= 0; i--) {
            if (ship.movement[i].type === "prefire") continue;
            movedPos = new hexagon.Offset(ship.movement[i].position);
            break;
        }

        if (!movedPos || movedPos.equals(oPos)) { //never dragged, or dragged within its own hex
            for (i = ship.movement.length - 1; i >= 0; i--) {
                move = ship.movement[i];
                if (!oPos.equals(new hexagon.Offset(move.position))) return move.position;
            }
            return oPos;
        }

        var drag = oPos.toCube().subtract(movedPos.toCube());
        var isTerrain = gamedata.isTerrain(ship.shipSizeClass, ship.userid);
        for (i = ship.movement.length - 1; i >= 0; i--) {
            move = ship.movement[i];
            if (move.type === "prefire") continue;
            //'start' is the off-board pre-deployment marker (x=+-30), not a position the unit was
            //ever really at - the same row getLastTurnMovement skips, and real only for Terrain.
            //It matters only here on the dragged path: the walk-back is anchored to the PRE-drag
            //hex, so a unit that never left its deploy hex (mine, OSAT, base) matches every row
            //and would otherwise run off the end of its history and answer with the marker.
            if (move.type === "start" && !isTerrain) continue;
            pos = new hexagon.Offset(move.position);
            if (!movedPos.equals(pos)) return pos.toCube().add(drag).toOffset();
        }

        //Nothing to walk back to. A dragged unit that never moved under its own power (mine, OSAT,
        //base) has no direction of travel, so the drag is the only motion there is: answer with the
        //hex it was dragged out of, which is also what the old code did. (Dragged path only - the
        //undragged branch above returns oPos itself.)
        return movedPos;
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
        return used;
    },

    getAmountWastedByCrits: function getAmountWastedByCrits(ship, system) {
        return system.thrustwasted;
    },

    /* ── Ship-window redesign Stage 2b (SHIPWINDOW_REDESIGN_PLAN.md §4.3) ──
       The four functions below moved here from the legacy shipWindowManager
       (UI/shipwindow.js, deleted in the Stage 4 retirement sweep): they are
       movement logic, not window styling — they
       mutate ship.movement and drive the React ShipThrust panel through the
       "AssignThrust" custom event. The legacy-DOM styling the originals also
       performed (thruster/assignThrust classes on the legacy window DOM,
       setData refreshes) is dropped: that DOM is never built in game.php.
       Event names and payloads are byte-identical to the originals.
       shipWindowManager.assignThrust(ship) became updateAssignThrust(ship)
       because the per-system assignThrust(ship, system) name was taken. */

    updateAssignThrust: function updateAssignThrust(ship) {
        var movement = ship.movement[ship.movement.length - 1];

        if (movement.commit) return false;

        var requiredThrust = movement.requiredThrust;
        var stillReq = shipManager.movement.calculateThrustStillReq(ship, movement);

        window.webglScene.customEvent("AssignThrust", { ship: ship, totalRequired: requiredThrust, remainginRequired: stillReq, movement: movement })
    },

    doneAssignThrust: function doneAssignThrust(ship) {
        var movement = ship.movement[ship.movement.length - 1];
        var stillReq = shipManager.movement.calculateThrustStillReq(ship, movement);

        var done = true;
        for (var i in stillReq) {
            if (stillReq[i] > 0) done = false;
        }

        if (done) {
            movement.commit = true;
            webglScene.customEvent("ShipMovementChanged", { ship: ship });
            window.webglScene.customEvent("AssignThrust", false)

            //For Contraction, need to amend level for first order.
            if (movement.type == "contract") shipManager.movement.amendContractValue(ship, movement.value);
        }
    },

    cancelAssignThrustEvent: function cancelAssignThrustEvent(ship) {
        shipManager.movement.cancelAssignThrust(ship);
        webglScene.customEvent("ShipMovementChanged", { ship: ship });
    },

    cancelAssignThrust: function cancelAssignThrust(ship) {
        if (!ship) {
            throw new Error("This requires ship")
        }

        shipManager.movement.revertAutoThrust(ship);

        ship.movement.splice(ship.movement.length - 1, 1);

        window.webglScene.customEvent("AssignThrust", false)
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

        if (shipManager.movement.isTurn(movement)) {
            var req = movement.requiredThrust;
            var dirs = [];
            var totalReq = 0;
            for (var i = 1; i <= 4; i++) {
                if (req[i] > 0) {
                    dirs.push(i);
                    totalReq += req[i];
                }
            }

            if (dirs.length === 2 && dirs.includes(system.direction)) {
                var currentAssigned = shipManager.movement.calculateAssignedThrust(ship, movement);
                var newAssignedVal = currentAssigned[system.direction] - 1;
                var otherDir = (dirs[0] === system.direction) ? dirs[1] : dirs[0];
                var otherVal = currentAssigned[otherDir];

                var totalAssigned = newAssignedVal + otherVal;

                if (totalAssigned >= totalReq) {
                    if (Math.abs(newAssignedVal - otherVal) > 1) {
                        return false;
                    }
                }
            }
        }

        if (movement.assignedThrust[system.id] >= step) {
            movement.assignedThrust[system.id] -= step;
        } else { }

        system.thrustwasted -= wasted;


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
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (ship.mine) return false;
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;
        if (Object.keys(ship.attached).length !== 0 && !ship.detached) return false; //Is attached to something!         
        /* REINFORCEMENTS_PLAN.md STAGE 7 - A UNIT ARRIVING THROUGH A JUMP POINT CANNOT BE TURNED.
           Its facing is the vortex's (§2.4) and shipManager.movement.deploy sets it; the arrows are
           refused here so the player cannot immediately turn back off it. The server checks the
           submitted facing as well (DeploymentGamePhase::validateReinforcementArrival) - this is
           the courtesy half, not the enforcement. */
        if (gamedata.gamephase == -1 && shipManager.isArrivingReinforcement(ship)) return false;
        if (gamedata.gamephase == -1 && ship.deploymove) return true;
        if (gamedata.gamephase != 2) return false;
        if (ship.osat && (!ship.flight)) { //OSAT but not MicroSAT
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
        var baseTurnCost = shipManager.movement.getTurnCost(ship);
        if (ship.submarine && shipManager.movement.isGoingBackwards(ship)) baseTurnCost = baseTurnCost * 1.33; //Subs have a weird rule about turning backwards.
        var turncost = Math.ceil(speed * baseTurnCost);
        turncost = Math.max(1, turncost);//turn cost may never be less than 1!
        turncost += shipManager.movement.getDockedLcvTurnSurcharge(ship);//LCV Rails: +1 thrust/turn per docked LCV

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

        if (!ship.gravitic && !ship.flight && shipManager.movement.isOutOfAlignment(ship)) {
            return false;
        }

        return true;
    },


    doTurn: function doTurn(ship, right) {
        if ((!ship.osat) || (ship.flight)) {
            if (!shipManager.movement.canTurn(ship, right)) {
                return false;
            }
        }
        if (gamedata.gamephase == -1) {
            shipManager.movement.doDeploymentTurn(ship, right);
            return;
        }

        shipManager.movement.doNormalTurn(ship, right);
    },

    doNormalTurn: function doNormalTurn(ship, right, gravitic = false) {
        var requiredThrust = shipManager.movement.calculateRequiredThrust(ship, right);
        if (ship.osat && (!ship.flight)) {
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

        if (ship.flight && !gravitic) newfacing = newheading; //fighter automatically change facing to heading unless Gravitic and choosing not to do so.    

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
            shipManager.movement.updateAssignThrust(ship);
        }


    },


    canGraviticTurn: function canGraviticTurn(ship, right) {
        //A unit that has ordered a jump-out has finished manoeuvring (JUMP_POINTS_PLAN.md section 2.5).
        if (shipManager.movement.hasJumpedOut(ship)) return false;
        if (gamedata.gamephase != 2) return false;
        if (!ship.gravitic) return false;
        if (!ship.flight) return false; //Fighters only for now.
        if (shipManager.movement.isManeuverBlockedByAttachment(ship)) return false;          

        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship)) return false;

        if (shipManager.movement.checkHasUncommitted(ship)) return false;
        var turndelay = shipManager.movement.calculateCurrentTurndelay(ship);
        var previous = shipManager.movement.getLastCommitedMove(ship);
        if (turndelay > 0) {
            if (!(ship.agile && previous && previous.turn == gamedata.turn && shipManager.movement.isTurn(previous))) {
                return false;
            }
        }

        var speed = shipManager.movement.getSpeed(ship);
        var baseTurnCost = shipManager.movement.getTurnCost(ship);
        var turncost = Math.ceil(speed * baseTurnCost);
        turncost = Math.max(1, turncost);//turn cost may never be less than 1!
        turncost += shipManager.movement.getDockedLcvTurnSurcharge(ship);//LCV Rails: +1 thrust/turn per docked LCV

        if (shipManager.movement.getRemainingEngineThrust(ship) < turncost) {
            return false;
        }

        if (shipManager.movement.isGoingBackwards(ship)) return true; //Backwards ships can grav turn always.

        if (ship.flight && shipManager.movement.isOutOfAlignment(ship)) return true; //No going backward but not facing forward, can grav turn

        return false;
    },


    doGraviticTurn: function doGraviticTurn(ship, right) {
        if (!shipManager.movement.canGraviticTurn(ship, right)) {
            return false;
        }

        if (ship.flight) {
            shipManager.movement.doNormalTurn(ship, right, true);
        } else { }

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
                        if (ship.systems[sys].channeled < ship.systems[sys].output) { //auto-assignment shall not overhrust
                            //if (ship.systems[sys].criticals.length == 0) {
                            //Only two criticals matter for thursters, ignore others - DK Dec 2024
                            if (shipManager.criticals.hasCritical(ship.systems[sys], "HalfEfficiency")) continue;
                            if (shipManager.criticals.hasCritical(ship.systems[sys], "FirstThrustIgnored")) continue;

                            thrusters.push(ship.systems[sys]);

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

        assignArray.forEach(function (amount, id) {
            if (amount === undefined) {
                return;
            }

            var system = ship.systems.find(function (system) { return system.id === id })

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
        var baseTurnCost = shipManager.movement.getTurnCost(ship);
        if (ship.submarine && shipManager.movement.isGoingBackwards(ship)) baseTurnCost = baseTurnCost * 1.33; //Subs have a weird rule about turning backwards.
        var turncost = Math.ceil(speed * baseTurnCost);
        //LCV Rails: each docked LCV adds +1 thrust to this turn's cost (flat, on top
        //of the maneuver cost). Flights never carry LCV rails, so 0 for them.
        var lcvSurcharge = shipManager.movement.getDockedLcvTurnSurcharge(ship);
        turncost += lcvSurcharge;

        var side, sideindex, rear, rearindex, any;

        if (ship.flight) {
            if (turncost == 0) turncost = 1;
            requiredThrust[0] = turncost;
            return requiredThrust;
        }

        if (speed === 0) { //at speed 0 - cost is 1 thrust, and can be channeled through literally any thruster
            return Array(1 + lcvSurcharge, 0, 0, 0, 0);
        }

        if (ship.mindrider) {//Aug 2024 - Mindrider's have special thurster allocation rules
            side = turncost;
            rear = 0;
            any = 0;
        } else {
            side = Math.floor(turncost / 2);
            rear = Math.floor(turncost / 2);
            any = turncost % 2;
        }

        requiredThrust[0] = any;

        var reqThrusterName = "main";
        var requiredThruster = shipManager.movement.thrusterDirectionRequired(ship, reqThrusterName, false, true);
        requiredThrust[requiredThruster] = rear;
        reqThrusterName = "stbd";
        if (right) { //turn to Stbd requres Port thruster
            reqThrusterName = "port";
        }
        requiredThruster = shipManager.movement.thrusterDirectionRequired(ship, reqThrusterName, false, true);
        requiredThrust[requiredThruster] = side;

        return requiredThrust;
    }, //endof function calculateRequiredThrust


    calculateAssignedThrust: function calculateAssignedThrust(ship, movement, overthrustCheck = false) {//fix attempt: third parameter: is it overthrust check? (else: this is thrust assignment check)
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
                if (overthrustCheck) { //another fix attempt! - call when checking for overthrust
                    if (shipManager.movement.getAmountChanneledReal(ship, system, false) == movement.assignedThrust[i]) sub = 1; //when it's entire channeled thrust - otherwise it's not first maneuver using this thruster this turn!
                } else { //original call - working correctly when assigning thrust
                    if (shipManager.movement.getAmountChanneledReal(ship, system, true) === 0) sub = 1; //original call - if this is the first point being assigned
                }
                //ok, reason of first fix failure is that the function is called both when assigning thrust (where above call is correct), and later when calculating delay (...when it is incorrect...)
                //now attempting to fix this by third parameter...
            }

            assignedarray[ship.systems[i].direction] += Math.ceil(movement.assignedThrust[i] * mod) - sub;
            if (assignedarray[ship.systems[i].direction] < 0) assignedarray[ship.systems[i].direction] = 0;
        }
        return assignedarray;
    },

    /*called when point of thrust is assigned... but also when calculationg how much overthrust was spent*/
    calculateThrustStillReq: function calculateThrustStillReq(ship, movement, overthrustCheck = false) { //fix attempt: third parameter: is it overthrust check? (else: this is thrust assignment check)
        var assignedarray = shipManager.movement.calculateAssignedThrust(ship, movement, overthrustCheck);
        var requiredThrust = movement.requiredThrust;
        var stillReq = requiredThrust.slice();
        var any = 0;

        for (var i in requiredThrust) {
            var req = requiredThrust[i]; //If null set to 0, to pick up where only 1 thrust reqd anywhere, and already assigned to a thruster
            var ass = assignedarray[i];

            if (req == null && ass == null) { //On rare occassions ass can have a value where req is null, preventing it from being added to any.
                stillReq[i] = null;
                continue;
            }

            if (ass > req) {
                stillReq[i] = 0;
                any += ass - req;
            } else {
                stillReq[i] -= ass;
            }
        }

        stillReq[0] -= any;

        if (movement.type == "pivotright" || movement.type == "pivotleft") {
            var portDirection = shipManager.movement.thrusterDirectionRequired(ship, "port");
            var stbdDirection = shipManager.movement.thrusterDirectionRequired(ship, "stbd");
            var mainDirection = shipManager.movement.thrusterDirectionRequired(ship, "main");
            var retroDirection = shipManager.movement.thrusterDirectionRequired(ship, "retro");



            if (movement.type == "pivotright") { //clockwise
                if (assignedarray[retroDirection] > 0 || assignedarray[portDirection] > 0) {
                    stillReq[mainDirection] = null;
                    stillReq[stbdDirection] = null;
                } else if (assignedarray[mainDirection] > 0 || assignedarray[stbdDirection] > 0) {
                    stillReq[retroDirection] = null;
                    stillReq[portDirection] = null;
                }
            } else { //counterclockwise
                if (assignedarray[retroDirection] > 0 || assignedarray[stbdDirection] > 0) {
                    stillReq[mainDirection] = null;
                    stillReq[portDirection] = null;
                } else if (assignedarray[mainDirection] > 0 || assignedarray[portDirection] > 0) {
                    stillReq[retroDirection] = null;
                    stillReq[stbdDirection] = null;
                }
            }
        }

        return stillReq;
    }, //endof function calculateThrustStillReq


    calculateTurndelayAtMove: function calculateTurndelayAtMove(ship, setMoveNo) { //array of moves, ID of move for which delay should be counted
        var didTurn = false;
        var turndelay = 0;
        if (setMoveNo >= 0) {
            turndelay = Math.ceil(ship.movement[setMoveNo].speed * shipManager.movement.getTurnDelayCost(ship)); //delay at current speed - at least as many moves are required for turn delay to be satisfied
            //LCV Rails: each docked LCV adds +1 to the required turn delay (only
            //meaningful when a turn was actually made — gated by didTurn below).
            turndelay += shipManager.movement.getDockedLcvTurnSurcharge(ship);
        } else {//before unit started to move there was no delay for certain
            turndelay = 0;
        }
        var movesDone = 0;
        var moveNo = setMoveNo; //number of last move to be checked
        while (moveNo >= 0) {
            var movement = ship.movement[moveNo];
            if ((movement.type == "move" || movement.type == "slipright" || movement.type == "slipleft")) movesDone++;
            if (movement.type == "speedchange") {//speed change - check if delay was satisfied at that point (which means a single move back)!
                var turndelayThen = shipManager.movement.calculateTurndelayAtMove(ship, moveNo - 1);
                if (turndelayThen == 0) { //if then was 0, it can't be more now as there were no turns between!
                    turndelay = 0;
                    break;
                }//if it was not 0, then that speed change was irrelevant, go on counting
            }
            if (movement.speed == 0) {//if at any point speed is actually 0 - then delay is 0 as well and that's the answer
                turndelay = 0;
                break;//while
            }
            if (shipManager.movement.isTurn(movement)) { //this is last turn - no point looking any further!
                didTurn = true;
                //when multiple turns are done one after another, it's a snap turn by agile ship (with turn shortening happening at FIRST step) 
                //(or speed 0 when it doesn't matter)
                //so go back to first turn made in sequence and calculate extra thrust spent for it instead of actual turn found
                var prevNo = moveNo - 1;
                while ((prevNo >= 0) && (shipManager.movement.isTurn(ship.movement[prevNo]))) {
                    movement = ship.movement[prevNo];
                    prevNo--;
                }
                movesDone += shipManager.movement.calculateExtraThrustSpent(ship, movement); //calculate turn shortening as moves done
                break;//while
            }
            if (movesDone >= turndelay) { //at this point turn delay is satisfied, no need to look further!
                break;//while
            }
            moveNo--;
        }
        if (!didTurn) {//did not turn - which means there is no delay!
            turndelay = 0;
        } else {
            turndelay = turndelay - movesDone;//required delay minus satisfied delay
        }
        turndelay = Math.max(0, turndelay);//cannot be <0		
        return turndelay;
    }, //endof calculateTurndelayAtMove


    calculateCurrentTurndelay: function calculateCurrentTurndelay(ship) {
        /* Marcin Sawicki, December 2019: this is having trouble when delay is reduced to 0 (by exactly satisfying or by slowing down to 0), and then acceleration happens
        var turndelay = Math.ceil(ship.movement[ship.movement.length - 1].speed * shipManager.movement.getTurnDelayCost(ship));
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
        if (turndelay > 0 && shipManager.movement.getTurnDelayCost(ship) > 1) {
            if (!didTurn) {
                turndelay = 0;
            }
        }
        return turndelay;
        */
        /*Marcin Sawicki, December 2019: new version, calculating backwards from current status*/
        var turndelay = shipManager.movement.calculateTurndelayAtMove(ship, ship.movement.length - 1);
        return turndelay;
    }, //endof calculateCurrentTurndelay


    calculateTurndelay: function calculateTurndelay(ship, movement, speed) {
        // speed as a seperate parameter needed to allow for calculation with new speed.
        if (speed == 0) return 0;
        if (shipManager.movement.getTurnDelayCost(ship) == 0) return 0;
        var turndelay = Math.ceil(speed * shipManager.movement.getTurnDelayCost(ship));
        if (ship.flight) return turndelay; //Marcin Sawicki: fighters are NOT exception to delay rules! But so far fighters cannot overthrust...
        turndelay -= shipManager.movement.calculateExtraThrustSpent(ship, movement);
        if (turndelay < 0) turndelay = 0; //Marcin Sawicki: just in case, no negative values
        //LCV Rails: each docked LCV adds +1 to the turn delay (flat, on top of the
        //base delay — the ship's turndelaycost rate is unchanged). Applied after the
        //overthrust reduction + zero-clamp so it always extends the delay.
        turndelay += shipManager.movement.getDockedLcvTurnSurcharge(ship);
        return turndelay;
    },

    calculateExtraThrustSpent: function calculateExtraThrustSpent(ship, movement) {
        var reg = shipManager.movement.calculateThrustStillReq(ship, movement, true); //third parameter: calculating overthrusting
        var extra = 0 - reg[0];
        if (extra < 0) extra = 0;
        return extra;
    },

    isTurn: function isTurn(movement) {
        if (!movement) console.trace();
        return movement.type == "turnright" || movement.type == "turnleft";
    },



    /*return thruster direction required from text input - for readability*/
    directionNoFromName: function directionNoFromName(direction) {
        var thrusterDirectionNo = 0;
        switch (direction) {
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
    thrusterDirectionRequired: function thrusterDirectionRequired(ship, direction, accel = false, isTurnOrSlip = false) {
        var orientationRequired = shipManager.movement.directionNoFromName(direction);

        if (orientationRequired > 2 && shipManager.movement.isRolled(ship)) { //rolled reverses side requirements
            if (orientationRequired == 3) {
                orientationRequired = 4;
            } else {
                orientationRequired = 3;
            }
        }
        /*here is a difference - for gravitic ship out of alignment reversing requirements will not fit with thruster change! so it needs to be done separately for gravitic ship...*/
        if (!ship.gravitic) { ///non-gravitic - reverse requiremets if going backwards
            if (shipManager.movement.isGoingBackwards(ship)) {
                switch (orientationRequired) {
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
        } else { //ship is gravitic! reverse requirements if going backwards
            if (shipManager.movement.isGoingBackwards(ship) && (isTurnOrSlip || !shipManager.movement.isOutOfAlignment(ship))) { //moving backwards reverses all requirements
                switch (orientationRequired) {
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
        }

        //Gravitic allows further rotations if pivoted (eg. not moving exactly forward or backwards)...
        if (ship.gravitic && !isTurnOrSlip) {
            if (shipManager.movement.isPivotedPort(ship)) { //pivoted to Port means: Stbd is Retro, Main is Stbd, Port is Main, Retro is Port
                switch (orientationRequired) {
                    case 1:
                        orientationRequired = accel ? 4 : 3;
                        break;
                    case 2:
                        orientationRequired = accel ? 3 : 4;
                        break;
                    case 3:
                        orientationRequired = 1;
                        break;
                    case 4:
                        orientationRequired = 2;
                        break;
                }
            } else if (shipManager.movement.isPivotedStbd(ship)) {//pivoted to Stbd means: Stbd is Main, Main is Port, Port is Retro, Retro is Stbd
                switch (orientationRequired) {
                    case 1:
                        orientationRequired = accel ? 3 : 4;
                        break;
                    case 2:
                        orientationRequired = accel ? 4 : 3;
                        break;
                    case 3:
                        orientationRequired = 2;
                        break;
                    case 4:
                        orientationRequired = 1;
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
        if (shipManager.movement.isPivotedPort(ship)) return false; //ship pivoted to Port is not pivoted to Starboard
        return true;
    },

    getAttachedFacingOffset: function getAttachedFacingOffset(location) {
        var locOffset = 0;
        if (location == 1) locOffset = 3; // Forward section, pod faces Aft
        else if (location == 2) locOffset = 0; // Aft section, pod faces Forward
        else if (location == 3 || location == 32) locOffset = 1; // Port, pod faces Starboard-Forward
        else if (location == 31) locOffset = 2; // Port-Forward, pod faces Starboard-Aft
        else if (location == 4 || location == 42) locOffset = 5; // Starboard, pod faces Port-Forward
        else if (location == 41) locOffset = 4; // Starboard-Forward, pod faces Port-Aft
        return locOffset;
    },




};
