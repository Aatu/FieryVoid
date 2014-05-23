shipManager.movement = {


    deploy: function(ship, pos){
        
        if (!ship.deploymove){
            var lm = ship.movement[ship.movement.length-1];
            var move = {
                id:-1,
                type:"deploy",
                x:pos.x,
                y:pos.y,
                xOffset:0,
                yOffset:0,
                facing:lm.facing,
                heading:lm.heading,
                speed:lm.speed,
                animating:false,
                animated:true,
                animationtics:0,
                requiredThrust:Array(null, null, null, null, null),
                assignedThrust:Array(),
                commit:true,
                preturn:false,
                at_initiative:shipManager.getIniativeOrder(ship),
                turn:gamedata.turn,
                forced:false,
                value:0
            };
            
            ship.deploymove = move;
            ship.movement[ship.movement.length] = move;
        }else{
            ship.deploymove.x = pos.x;
            ship.deploymove.y = pos.y;
        }
        
        shipManager.drawShip(ship);
        
    },

    doDeploymentTurn: function(ship, right){
        
        var step = 1;
        if (!right){
            step = -1;
        }
        
        var newfacing = mathlib.addToHexFacing(ship.deploymove.facing, step);
        var newheading = mathlib.addToHexFacing(ship.deploymove.heading, step);
        
        ship.deploymove.facing = newfacing;
        ship.deploymove.heading = newheading;
    },

    isMovementReady: function(ship){
        return (shipManager.movement.getRemainingMovement(ship) == 0);
    },
    
    checkHasUncommitted: function(ship){
    
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.commit == false)
                return true;
        }
    
        return false;
    },
    
    hasDeletableMovements: function(ship){
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
            
            if (gamedata.gamephase == 3){
                if (movement.value == "combatpivot" && (movement.type == "pivotleft" || movement.type == "pivotright" )){
                   return true;
                }
                    
            }else{
                if (!movement.preturn && !movement.forced && movement.type != "deploy")
                    return true;
            }
        }
        
        return false;
    },
    
    deleteMove: function(ship){
		
      
		
        var movement = ship.movement[ship.movement.length -1];
        if (!movement.preturn && !movement.forced && movement.turn == gamedata.turn){
            
            if (gamedata.gamephase == 3 && (movement.value != "combatpivot" || (movement.type != "pivotleft" && movement.type != "pivotright" )))
                return;

            // adjust the current turn delay if the new speed changes the turn delay
            var oldspeed = shipManager.movement.getSpeed(ship);

            ship.movement.splice(ship.movement.length -1, 1);

            var speed = shipManager.movement.getSpeed(ship);

//            shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
            ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
            
            var shipwindow = $(".shipwindow_"+ship.id);
            shipWindowManager.cancelAssignThrust(shipwindow);
            shipManager.drawShip(ship);
            gamedata.shipStatusChanged(ship);
        }
    },
    
    deleteSpeedChange: function(ship, accel){

        var curheading = shipManager.movement.getLastCommitedMove(ship).heading;
        
		for (var i in ship.movement){
			var movement = ship.movement[i];
			if (movement.turn != gamedata.turn || movement.type != "speedchange")
				continue;
	
			if ((movement.value != accel && movement.heading == curheading) || (movement.value == accel && movement.heading != curheading)){
                            // adjust the current turn delay if the new speed changes the turn delay
                            var oldspeed = shipManager.movement.getSpeed(ship);

                            ship.movement.splice(ship.movement.length -1, 1);

                            var speed = shipManager.movement.getSpeed(ship);
        
//                            shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
                            ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
                            
                            var shipwindow = $(".shipwindow_"+ship.id);
                            shipWindowManager.cancelAssignThrust(shipwindow);
                            shipManager.drawShip(ship);
                            gamedata.shipStatusChanged(ship);

                            return true;
			}
		}
		
		return false;
    },
    
    canJink: function(ship, accel){
        
        if (gamedata.gamephase != 2)
            return false;
        
		if (!ship.flight)
			return false;
	
		if (accel == 0)
			return true;
	
		if (shipManager.movement.getRemainingEngineThrust(ship) <= 0)
			return false;
		
		var jinking = shipManager.movement.getJinking(ship);
		if (( (jinking+accel) > ship.jinkinglimit) || (jinking+accel < 0))
			return false;
			
		
			
			
		
		return true;
	},
	
	getJinking: function(ship){
		
		var j = 0;
		
		for (var i in ship.movement){
			var move = ship.movement[i];
			if (move.turn != gamedata.turn)
				continue;
			
			if (move.type =="jink")
				j += move.value;
		}
		return j;
	},
	
	doJink: function(ship, accel){
		
		if (!shipManager.movement.canJink(ship, accel))
			return;
			
		var commit = false;
		var requiredThrust = Array();
        var assignedThrust = Array();
        if (ship.flight){
			commit = true;
			requiredThrust[0] = 1;
			assignedThrust[0] = 1;
		}else{
			requiredThrust[0] = 1;
		}
        
    
        
        if (accel<0){
			for (var i in ship.movement){
				var move = ship.movement[i];
				if (move.turn != gamedata.turn)
					continue;
					
				if (move.type == "jink"){
					ship.movement.splice(i, 1);
					break;
				}
					
			}
			
			gamedata.shipStatusChanged(ship);
			shipManager.drawShip(ship);
              
		}else{
			var lm = shipManager.movement.getLastCommitedMove(ship);
			ship.movement[ship.movement.length] = {
                id:-1,
				type:"jink",
				x:lm.x,
				y:lm.y,
				xOffset:lm.xOffset,
				yOffset:lm.yOffset,
				facing:lm.facing,
				heading:lm.heading,
				speed:lm.speed,
				animating:false,
				animated:true,
				animationtics:0,
				requiredThrust:requiredThrust,
				assignedThrust:assignedThrust,
				commit:commit,
				preturn:false,
                                at_initiative:shipManager.getIniativeOrder(ship),
				turn:gamedata.turn,
				forced:false,
				value:accel
			};
			
			gamedata.shipStatusChanged(ship);
			shipManager.drawShip(ship);
			if (!ship.flight)
				shipWindowManager.assignThrust(ship);
        
		}
		
	},
    
    canRoll: function(ship){
		
        if (gamedata.gamephase != 2)
            return false;
        
		if (ship.flight)
			return false;
	
		if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
			return false;
	
		if (shipManager.systems.isEngineDestroyed(ship))
			return false;
    
        if (shipManager.movement.hasRolled(ship) && !ship.agile){
            return false;
        }
            
        if (shipManager.movement.isPivoting(ship) != "no" && !ship.gravitic){
            return false;
        }
            
        if (ship.rollcost > shipManager.movement.getRemainingEngineThrust(ship)){
            return false;
        }
            
        return true;
    },
    
    doRoll: function(ship){
       
        if (!shipManager.movement.canRoll(ship))
            return false;
            
        var lm = ship.movement[ship.movement.length-1];
        var requiredThrust = Array(ship.rollcost, 0, 0, 0, 0);
        
        
        ship.movement[ship.movement.length] = {
            id:-1,
            type:"roll",
            x:lm.x,
            y:lm.y,
            xOffset:lm.xOffset,
            yOffset:lm.xOffset,
            facing:lm.facing,
            heading:lm.heading,
            speed:lm.speed,
            animating:false,
            animated:true,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:Array(),
            commit:false,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:0
        };
        
        hexgrid.unSelectHex();
        shipManager.drawShip(ship);
        shipWindowManager.assignThrust(ship);
            
            
    },
    
    isRolling: function(ship){
        var rolling = false;
        
        if (ship.agile)
			return false;
        
        for (var i in ship.movement){
            var m = ship.movement[i];
            if (m.turn != gamedata.turn)
                continue;
            if (m.type == "isRolling")
                rolling = true;
            
            if (m.type == "roll" && m.commit)
                rolling = !rolling;
        }
 
        return rolling;
        
    },
    
    isRolled: function(ship){
        var ret = false;
        if (ship.agile){
			for (var i in ship.movement){
				var m = ship.movement[i];
//				if (m.turn != gamedata.turn)
//					continue;
					
				if (m.type == "isRolled"){
					ret = true;
				}
				
				if (m.type == "roll"){
					ret = !ret;
				}
				
			}
			
		}else{
			   
			for (var i in ship.movement){
				var m = ship.movement[i];
				if (m.turn != gamedata.turn)
					continue;
					
				if (m.type == "isRolled"){
					//console.log(ship.name + " is rolled");
					return true;
				}
				
			}
			return false;
		}
        //console.log(ship.name + " is NOT rolled");
        return ret;
    },
    
    hasRolled: function(ship){
        for (var i in ship.movement){
            var m = ship.movement[i];
            if (m.turn != gamedata.turn)
                continue;
                
            if (m.type == "roll" || m.type == "isRoling")
                return true;
        }
        
        return false;
    },
    
    canMove: function(ship){
        
        if (gamedata.gamephase != 2)
            return false;
	
		if (shipManager.isDestroyed(ship))
			return false;
			
        return (shipManager.movement.getRemainingMovement(ship) > 0);
        
        
    },
    
    doMove: function(ship){
    
        if (!shipManager.movement.canMove(ship))
            return false;
            
        var lm = ship.movement[ship.movement.length-1];
        
        var angle = shipManager.hexFacingToAngle(lm.heading);
        var shipX = ship.movement[ship.movement.length-1].x;
        var shipY = ship.movement[ship.movement.length-1].y;
        var pos = hexgrid.getHexToDirection(angle, shipX, shipY);
		var off = shipManager.movement.getMovementOffsetPos(ship, lm.heading, pos);
        ship.movement[ship.movement.length] = {
            id:-1,
            type:"move",
            x:pos.x,
            y:pos.y,
            xOffset:off.xO,
            yOffset:off.yO,
            facing:lm.facing,
            heading:lm.heading,
            speed:lm.speed,
            animating:false,
            animated:false,
            animationtics:0,
            requiredThrust:Array(null, null, null, null, null),
            assignedThrust:Array(),
            commit:true,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:0
        };
        hexgrid.unSelectHex();
        //gamedata.shipStatusChanged(ship);
    },
    
    canSlip: function(ship, right){
        
        if (gamedata.gamephase != 2)
            return false;
	
		if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
			return false;
			
		if (shipManager.systems.isEngineDestroyed(ship))
			return false;
    
        var name = (right) ? "slipright" : "slipleft";
        var othername = (right) ? "slipleft" : "slipright";
        var movebetween = true;
        
        if (shipManager.movement.isRolling(ship) && !ship.gravitic)
            return false;
        
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
            /*    
            if (movement.type == othername){
                return false;
            }
            */
            if (movement.type == othername || movement.type == name)
                movebetween = false;
                
            if (movement.type == "move")
                movebetween = true;
        }
        
        if ( movebetween == false)
            return false;
                
        if ( !ship.flight && Math.ceil(shipManager.movement.getSpeed(ship) / 5) > shipManager.movement.getRemainingEngineThrust(ship)){
            return false;
            
        }
        
        if (shipManager.movement.getRemainingEngineThrust(ship) == 0)
            return false;
        
        if (shipManager.movement.getRemainingMovement(ship) < 1)
            return false;
        
        return true;
    
    },
    
    doSlip: function(ship, right){
        
        if (!shipManager.movement.canSlip(ship, right))
            return false;
            
        var name= (right) ? "slipright" : "slipleft";
        var lm = ship.movement[ship.movement.length-1];
        var newheading = (right) ? mathlib.addToHexFacing(lm.heading, 1) : mathlib.addToHexFacing(lm.heading, -1)
        var angle = shipManager.hexFacingToAngle(newheading);
        var shipX = ship.movement[ship.movement.length-1].x;
        var shipY = ship.movement[ship.movement.length-1].y;
        var pos = hexgrid.getHexToDirection(angle, shipX, shipY);
        
        var slipcost = Math.ceil(shipManager.movement.getSpeed(ship) / 5);
        if (ship.flight)
            slipcost = 1;
        
        var reversed = shipManager.movement.hasSidesReversedForMovement(ship);
        if (reversed)
            right = !right;
            
        var requiredThrust = Array(null, null, null, null, null);
        
        var commit = false;
        var assignedThrust = Array();
        if (ship.flight){
			commit = true;
			requiredThrust[0] = slipcost;
			assignedThrust[0] = slipcost;
		}else{
			if (right)
				requiredThrust[3] = slipcost;
			else
				requiredThrust[4] = slipcost;
			
		}
        
        
        
		var off = shipManager.movement.getMovementOffsetPos(ship, newheading, pos);
        ship.movement[ship.movement.length] = {
            id:-1,
            type:name,
            x:pos.x,
            y:pos.y,
            xOffset:off.xO,
            yOffset:off.yO,
            facing:lm.facing,
            heading:lm.heading,
            speed:lm.speed,
            animating:false,
            animated:false,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:assignedThrust,
            commit:commit,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:0
        };
        
        hexgrid.unSelectHex();
        shipManager.drawShip(ship);
        if (!ship.flight)
			shipWindowManager.assignThrust(ship);

    },
    
    isEndingPivot: function(ship, right){
        var isPivoting = shipManager.movement.isPivoting(ship);
        
        if (isPivoting == "no")
            return false;
            
        if (isPivoting == "left" && !right)
            return true;
            
        if (isPivoting == "right" && right)
            return true;
            
        return false;
    },
    
    canPivot: function(ship, right){
        
        
	
		if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
			return false;
			
		if (shipManager.systems.isEngineDestroyed(ship))
			return false;
			
        var name = (right) ? "pivotright" : "pivotleft";
        var othername = (right) ? "pivotleft" : "pivotright";
        
        if (shipManager.movement.isRolling(ship) && !ship.gravitic)
            return false;
        
        var hasPivoted = shipManager.movement.hasPivoted(ship);
        var isPivoting = shipManager.movement.isPivoting(ship);
        
        if (hasPivoted.right && isPivoting != "right" && right && !ship.agile)
            return false;
            
        if (hasPivoted.left && isPivoting != "left" && !right && !ship.agile)
            return false;
            
        var isPivoting = shipManager.movement.isPivoting(ship);
        
        if ((right && isPivoting == "left") || (!right && isPivoting == "right") && !ship.agile){
            return false;
        }
            
        if (ship.pivotcost > shipManager.movement.getRemainingEngineThrust(ship))
            return false;
        
        if (ship.flight && gamedata.gamephase == 3){
            
            if (!weaponManager.canCombatTurn(ship))
                return false;
            
            if (ship.pivotcost*2 > shipManager.movement.getRemainingEngineThrust(ship))
                return false;
        
        }else if (gamedata.gamephase != 2)
            return false;
            
        return true;
        
    },
    
    countCombatPivot: function (ship){
		var c = 0;
		
		for (var i in ship.movement){
			var move = ship.movement[i];
		
			if (move.value == "combatpivot")
				c++;
		}
		
		return c;
	},
    
    doPivot: function(ship, right){
        
        if (!shipManager.movement.canPivot(ship, right))
            return false;
        
        var lm = ship.movement[ship.movement.length-1];
                
        var name;
        var newfacing = lm.facing;
        var step = 1;
        var pivoting = shipManager.movement.isPivoting(ship);
        var pivotcost = ship.pivotcost;
        var value = 0;
        if (gamedata.gamephase == 3){
			pivotcost *= 2;
			value = "combatpivot";
		}
        
        if (pivoting != "no"){
            right = !right;
        }
            
        name = "pivotright";
        
        if (!right){
            step = -1;
            name = "pivotleft";
        }
        var commit = false;
        var assignedThrust = Array();
        var requiredThrust = Array();
        if (ship.flight){
			commit = true;
			requiredThrust[0] = pivotcost;
			assignedThrust[0] = pivotcost;
		}else{
			side = Math.floor(pivotcost / 2);
			rear = Math.floor(pivotcost / 2);
			any = ship.pivotcost % 2;
			
			requiredThrust = Array(any, rear, rear, side, side);
		}
        if (pivoting == "no") 
            newfacing = mathlib.addToHexFacing(lm.facing, step);
                
        ship.movement[ship.movement.length] = {
            id:-1,
            type:name,
            x:lm.x,
            y:lm.y,
            xOffset:lm.xOffset,
            yOffset:lm.yOffset,
            facing:newfacing,
            heading:lm.heading,
            speed:lm.speed,
            animating:false,
            animated:false,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:assignedThrust,
            commit:commit,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:value
        }
        
        hexgrid.unSelectHex();
        shipManager.drawShip(ship);
        if (!ship.flight)
			shipWindowManager.assignThrust(ship);
    },
    
    doForcedPivot: function(ship){
        var pivoting = shipManager.movement.isPivoting(ship);
        if (pivoting == "no")
            return;
            
        var name = "pivotright";
        var step = 1;
        
        if (pivoting == "left"){
            var name = "pivotleft";
            var step = -1;
        }
            
        var lm = ship.movement[ship.movement.length-1];
        var facing = mathlib.addToHexFacing(lm.facing, step);
        
        ship.movement[ship.movement.length] = {
            id:-1,
            type:name,
            x:lm.x,
            y:lm.y,
            xOffset:lm.xOffset,
            yOffset:lm.yOffset,
            facing:facing,
            heading:lm.heading,
            speed:lm.speed,
            animating:false,
            animated:false,
            animationtics:0,
            requiredThrust:Array(null, null, null, null, null),
            assignedThrust:Array(),
            commit:true,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:true,
            value:0
        }
        
        
        shipManager.drawShip(ship);
        
        
        
    },
    
    isPivoting: function(ship){
        var pivoting = "no";
        
        if (ship.agile)
			return pivoting;
        
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
                
            if ( movement.commit == false )
                continue;
            
            if (movement.type == "isPivotingLeft")
                pivoting ="left";
            
            if (movement.type == "isPivotingRight")
                pivoting ="right";
            
            
            if (movement.type == "pivotright" && pivoting == "no" && movement.preturn == false){
                pivoting = "right";
            }
            
            if (movement.type == "pivotleft" && pivoting == "no" && movement.preturn == false){
                pivoting = "left";
            }
            
            if (movement.type == "pivotright" && pivoting == "left" && movement.preturn == false){
                pivoting = "no";
            }
            
            if (movement.type == "pivotleft" && pivoting == "right" && movement.preturn == false){
                pivoting = "no";
            }
            
            
            if (!ship.gravitic && shipManager.movement.isTurn(movement) && pivoting != "no"){
                pivoting = "no";
            }
            
            
            
        }
     
        return pivoting;
    },
    
    canTurnIntoPivot: function(ship, right){
        var returnVal = false;
        
	if (ship.agile)
            returnVal = false;
			
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        var reverseheading = mathlib.addToHexFacing(heading, 3);
        
        if (heading === facing)
            returnVal = false;
        
        var step = (right) ? 1 : -1;
        
        if (mathlib.addToHexFacing(step, heading) === facing || mathlib.addToHexFacing(step, heading) === reverseheading)
            returnVal = true;
        
        return returnVal;
    },
    
    hasPivoted: function(ship){
    
        var left = false;
        var right = false;
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
                
            if ( movement.type == "pivotleft" && movement.preturn == false){
                left = true;
            }
            if (movement.type == "pivotright"  && movement.preturn == false){
                right = true;
            }
            
            
        }
        
        return {left:left, right:right};
    },
    
    hasCombatPivoted: function(ship){
    
        if (! ship.flight)
            return false;
        
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
            
            if (movement.value != 'combatpivot')
                continue;
                
            if ( movement.type == "pivotleft" || movement.type == "pivotright"){
                return true;
            }
            
            if ( movement.type == "isPivotingRight" || movement.type == "isPivotingLeft"){
                return true;
            }
            
            
        }
        
        return false;
    },
    
    hasPivotedForShooting: function(ship){
  
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
                
            if ( movement.type == "pivotleft" || movement.type == "pivotright"){
                return true;
            }
            
            if ( movement.type == "isPivotingRight" || movement.type == "isPivotingLeft"){
                return true;
            }
            
            
        }
        
        return false;
    },
    
    canChangeSpeed: function(ship, accel){
        
        if (gamedata.gamephase != 2)
            return false;
	
        if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
            return false;
        			
        if (shipManager.movement.checkHasUncommitted(ship))
            return false;
            
        if (shipManager.systems.isEngineDestroyed(ship))
            return false;
        
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
            
            if (movement.preturn == false && movement.forced == false && movement.type != "speedchange" && movement.type != "deploy")
                return false;
        }
        
        var curheading = shipManager.movement.getLastCommitedMove(ship).heading;
        
	for (var i in ship.movement){
            var movement = ship.movement[i];

            if (movement.turn != gamedata.turn || movement.type != "speedchange")
                continue;
            
            if ((movement.value != accel && movement.heading == curheading) || (movement.value == accel && movement.heading != curheading)){
		return true;
            }
	}
        
        if ( ship.accelcost <= shipManager.movement.getRemainingEngineThrust(ship)){
            return true;
        }
        
        return false;
    },
    
    adjustTurnDelay: function(ship, oldspeed, newspeed){
        var oldturndelay = Math.ceil(oldspeed * ship.turndelaycost);
        var newturndelay = Math.ceil(newspeed * ship.turndelaycost);
        var step = (newturndelay - oldturndelay);
        var spentturndelay = newturndelay;
        
        if(ship.currentturndelay == 0 && step == 1){
            // turndelay was 0. Re-check previous turn to see if the ship
            // moved enough to have also moved enough to cancel the new turn delay.
            for (var i in ship.movement){
                var movement = ship.movement[i];
                if (movement.turn != gamedata.turn - 1)
                    continue;

                if (movement.commit == false)
                    continue;

                if ((movement.type == "move" 
                    || movement.type == "slipright" 
                    || movement.type == "slipleft" ))
                    spentturndelay--;

                if (shipManager.movement.isTurn(movement)){
                    if (!ship.agile || !last || !shipManager.movement.isTurn(last))
                        spentturndelay = newturndelay;
                }
            }
        }
        
        ship.currentturndelay = ship.currentturndelay + step;
        
        if(ship.currentturndelay < 0){
            ship.currentturndelay = 0;
        }
        
        ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
        
//        if(oldturndelay == 0){
//            ship.currentturndelay = curTurnDelay;
//        }else{
//            ship.currentturndelay = adjustTurnDelay;
//        }
//        
        if(ship.currentturndelay < 0){
            ship.currentturndelay = 0;
        }
    },
    
    changeSpeed: function(ship, accel){

        if (!shipManager.movement.canChangeSpeed(ship, accel))
            return false;

	if (shipManager.movement.deleteSpeedChange(ship, accel)){
            ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
            return;
        }

        var value = 0;
        if (accel)
			value = 1;
			
        requiredThrust = Array(null,null,null,null,null);
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        var direction;
        
        if (shipManager.movement.isGoingBackwards(ship)){
            direction = (accel) ? 1 : 2;
        }else{
             direction = (accel) ? 2 : 1;
        }
                
        var step = (accel) ? 1: -1;
        var oldspeed = shipManager.movement.getSpeed(ship);
        var speed = oldspeed + step;
        
        // adjust the current turn delay if the new speed changes the turn delay
 //       shipManager.movement.adjustTurnDelay(ship, oldspeed, speed);
        
        
        if (speed < 0){
            heading = mathlib.addToHexFacing(heading, 3);
            speed = speed *-1;
            value = 1;
        }
        
        var commit = false;
        var assignedThrust = Array();
        if (ship.flight){
			commit = true;
			requiredThrust[0] = ship.accelcost;
			assignedThrust[0] = ship.accelcost;
		}else{
			 requiredThrust[direction] = ship.accelcost;
		}
        
       
        
        var lm = shipManager.movement.getLastCommitedMove(ship);
        ship.movement[ship.movement.length] = {
            id:-1,
            type:"speedchange",
            x:lm.x,
            y:lm.y,
            xOffset:lm.xOffset,
            yOffset:lm.yOffset,
            facing:lm.facing,
            heading:heading,
            speed:speed,
            animating:false,
            animated:true,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:assignedThrust,
            commit:commit,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:value
            };
        
        ship.currentturndelay = shipManager.movement.calculateCurrentTurndelay(ship);
        
        gamedata.shipStatusChanged(ship);
        shipManager.drawShip(ship);
        if (!ship.flight){
            shipWindowManager.assignThrust(ship);
        }
    },
        
            
    getRemainingEngineThrust: function(ship){
        
        var rem = 0;
        if (ship.flight){
			rem = ship.freethrust;
		}else{
			for (var i in ship.systems){
				var system = ship.systems[i];
				if (shipManager.systems.isDestroyed(ship, system))
					continue;
					
				if (system.name == "engine"){
					rem += shipManager.systems.getOutput(ship, system);
				}
				if (system.name == "thruster"){
					rem -= system.thrustwasted;
				}
			
			}
		}
        
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
                        
            for ( var a in movement.assignedThrust){
                rem -= movement.assignedThrust[a];
            }
            
        }
        
        return rem;
    
    },

    getRemainingMovement: function(ship){
            
        return shipManager.movement.getSpeed(ship) - shipManager.movement.getUsedMovement(ship);
        
    },
    
    getUsedMovement: function(ship){
        var used = 0;
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
            
            if ( movement.type == "move" || movement.type=="slipright" || movement.type=="slipleft"){
                if (movement.commit)
                    used++;
            }
            
        }
        
        return used;
    },
    
    getSpeed: function(ship){
        return shipManager.movement.getLastCommitedMove(ship).speed;
    },
    
    getLastCommitedMove: function(ship){
        var lm;
        var first;
        if (!ship){
            console.log("movement.getLastCommitedMove, ship is undefined");
            console.trace();
        }
			
			
        for (var i in ship.movement){
            
            if (!first)
                first = ship.movement[i];
            
            if (ship.movement[i].commit==true && ship.movement[i].animated == true)
                lm = ship.movement[i];
           
        }
        
        if (!lm)
            return first;
        return lm;
    },
    
    getFirstMoveOfTurn: function(ship){
        for (var i in ship.movement){
			var move = ship.movement[i];
			if (move.turn == gamedata.turn)
                return move;
		}
    },
	
	getPreviousLocation: function(ship){
		var oPos = shipManager.getShipPosition(ship);
		//console.log("cur loc: "  + oPos.x + ","+oPos.y);
		for (var i = ship.movement.length -1; i >= 0; i--){
			var move = ship.movement[i];
			
			//console.log("prev loc: "  + move.x + ","+move.y);
			if (move.x != oPos.x || move.y != oPos.y)
				return  {x:move.x, y:move.y};
							
		}
		
		return oPos;
	},
    
	getAmountChanneledReal: function(ship, system, ignoreUncommitted){
		var used = 0;
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
            
            if (ignoreUncommitted && !movement.commit)
                continue;
            
            var assigned = movement.assignedThrust[system.id];
            
            if (assigned != undefined){
                used += assigned;
            }
            
        }
        
        
        return used;
	},
    
	
    countAmountChanneled: function(ship, system){
        var used = 0;

        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn != gamedata.turn)
                continue;
                
            var assigned = movement.assignedThrust[system.id];
            
            if (assigned != undefined){
                used += assigned;
            }
            
        }
        
        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored"))
			used--;
			
		var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
		used = Math.ceil(used/(crits+1));
        
        return used;
    },
    
    getAmountChanneled: function(ship, system){
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
        
    getAmountWastedByCrits: function(ship, system){
    
        return system.thrustwasted;
    },
    
    assignThrust: function(ship, system){
	
		
		
		if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
			return false;
			
		if (shipManager.systems.isEngineDestroyed(ship))
			return false;
			
		if (shipManager.systems.isDestroyed(ship, system))
			return false;
			
		
		
		var movement = ship.movement[ship.movement.length-1];
        var already = shipManager.movement.getAmountChanneledReal(ship, system);
        var step = 1;
        var wasted = 0;
        var turndelay = shipManager.movement.calculateTurndelay(ship, movement, movement.speed);
        
        var remainingThrust = shipManager.movement.getRemainingEngineThrust(ship);
        var thrustReq = shipManager.movement.calculateThrustStillReq(ship, movement);
        
        var isTurn = shipManager.movement.isTurn(movement);
        
        
        if (thrustReq[system.direction] <= 0 && thrustReq[0] <= 0 && !isTurn){
            return false;
        }
        //console.log(thrustReq);
        //console.log(thrustReq[system.direction] + " " + thrustReq[0] + " " + isTurn + " " + (turndelay - 1));
        if (thrustReq[system.direction] <= 0 && thrustReq[0] <= 0 && isTurn && ((turndelay - 1) < 1)){
            return false;
        }
            
    
        if (shipManager.systems.getOutput(ship, system)*2 < already + step)
            return false;
			
		
        var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
        if (crits > 0){

            step = step*(crits+1);
        }
        
        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored") && already == 0){

            step++;
        }
        
        var assigned = shipManager.movement.calculateAssignedThrust(ship, movement);
        var oreg = movement.requiredThrust;
        
        var maxreg = 0;
        var maxassigned = 0;
        
        for (var i = 0;i<=4;i++){
            if (oreg[i] && oreg[i]>maxreg)
                maxreg = oreg[i];
            
            if (system.direction != i && assigned[i] && assigned[i]>maxassigned)
                maxassigned = assigned[i];
        
        }
        //console.log(oreg);
        //console.log("maxreg: "+ maxreg +" maxassigned: "+ maxassigned);
        if (assigned[system.direction]>maxreg && assigned[system.direction]>maxassigned )
            return false;
        
       

        if ( remainingThrust < step)
            return false;
        
        if (movement.assignedThrust[system.id]){
            movement.assignedThrust[system.id] += step;
        }else{
            movement.assignedThrust[system.id] = step;
        }
        
        system.thrustwasted += wasted;
        
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "engine"));
		//console.log(movement.assignedThrust[system.id]);
        return true;
        
        
    
    },
    
    unAssignThrust: function(ship, system){
	
		if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
			return false;
    
		if (shipManager.systems.isEngineDestroyed(ship))
			return false;
			
		if (shipManager.systems.isDestroyed(ship, system))
			return false;
			
        var movement = ship.movement[ship.movement.length-1];
        var already = shipManager.movement.getAmountChanneledReal(ship, system);
        var step = 1;
        var wasted = 0;
        
                
        if ((already - step) < 0)
            return false;
        
        var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
        if (crits > 0){
        
            step = step*(crits+1);
        }
        
        if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored") && already-step == 1 ){
            step++;
        }
        
        
        
        if (movement.assignedThrust[system.id]>=step){
            movement.assignedThrust[system.id] -= step;
        }else{
            
        }
        
        system.thrustwasted -= wasted;
        
		shipWindowManager.setDataForSystem(ship, system);
		shipWindowManager.setDataForSystem(ship, shipManager.systems.getSystemByName(ship, "engine"));
		
		//console.log(movement.assignedThrust[system.id]);
        return true;
        
        
    
    },
    
    isGoingBackwards: function(ship){
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        if (facing == heading || mathlib.addToHexFacing(facing, 1) == heading || mathlib.addToHexFacing(facing, -1) == heading)
			return false;
		
		return true;
		
        //return (mathlib.addToHexFacing(heading, 3) == facing)
            
    },
    
    //TURN
    
    canTurn: function(ship, right){
        //console.log(ship.name + " checking turn");
        
        if ( gamedata.gamephase == -1 && ship.deploymove)
            return true;
        
        if (gamedata.gamephase != 2)
            return false;
        
		if (shipManager.isDestroyed(ship) || shipManager.isAdrift(ship))
			return false;
		
		if (shipManager.systems.isEngineDestroyed(ship))
			return false;
			
			
        var heading = shipManager.movement.getLastCommitedMove(ship).heading;
        var facing = shipManager.movement.getLastCommitedMove(ship).facing;
        
        if (shipManager.movement.isRolling(ship) && !ship.gravitic)
            return false;
        
        if (shipManager.movement.checkHasUncommitted(ship))
            return false;
            
        var turndelay = shipManager.movement.calculateCurrentTurndelay(ship);
        
        var previous = shipManager.movement.getLastCommitedMove(ship);
               
       
        if ( turndelay > 0 ){
            
            if (!(ship.agile && previous && previous.turn == gamedata.turn && shipManager.movement.isTurn(previous)))
            {
                //console.log(ship.name + " has turn delay, cant turn");
                return false;
            }
        }
        
        var speed = shipManager.movement.getSpeed(ship);        
        var turncost = Math.ceil(speed * ship.turncost);
        
        //console.log("remaining thrust: " + shipManager.movement.getRemainingEngineThrust(ship) + " turncost: "  + turncost);
        if (shipManager.movement.getRemainingEngineThrust(ship) < turncost){
            //console.log(ship.name + " does not have enough thrust");
            return false;
        }
        
        
        var pivoting = shipManager.movement.isPivoting(ship);
        if (pivoting != "no" && !ship.gravitic ){ //&& !shipManager.movement.isTurningToPivot(ship, right) && !ship.gravitic){
            //console.log(ship.name + " pivoting and not gravitic");
            return false;
        }
        if (heading !== facing && mathlib.addToHexFacing(heading, 3) !== facing && !shipManager.movement.canTurnIntoPivot(ship, right) 
            && !ship.gravitic)
        {
            //console.log(ship.name + " heading is not facing, and cant turn to pivot");
            return false;
        }
            
        
        //console.log(ship.name + " can turn");
        return true;
        
        
        
    },
    
    doTurn: function(ship, right){
        
        if (!shipManager.movement.canTurn(ship, right)){
            return false;
        }
    
        if (gamedata.gamephase == -1){
            shipManager.movement.doDeploymentTurn(ship, right);
            return;
        }
            
        var commit = false;
        var assignedThrust = Array();
        
        if (ship.flight){
            if(shipManager.movement.canTurnIntoPivot(ship, right)){
                shipManager.movement.askForIntoPivotTurn(ship, right,
                    "Do you wish to turn or turn into the pivot?");
            }else{
                shipManager.movement.doNormalTurn(ship, right);
            }
	}else{
            if (shipManager.movement.canTurnIntoPivot(ship, right) && !ship.gravitic){
                shipManager.movement.doIntoPivotTurn(ship, right);
            }else if(shipManager.movement.canTurnIntoPivot(ship, right) && ship.gravitic){
                shipManager.movement.askForIntoPivotTurn(ship, right,
                    "This ship has gravitic engines. Do you wish to turn or turn into the pivot?");
            }else{
                shipManager.movement.doNormalTurn(ship, right);
            }
        }
    },
    
    doIntoPivotTurn: function(ship, right){
        var requiredThrust = shipManager.movement.calculateRequiredThrust(ship, right);
        var lastMovement = ship.movement[ship.movement.length-1];
            
        var name;
        var step = 1;
        var commit = false;
        var assignedThrust = Array();

        name = "turnright";
        
        
        if (!right){
            step = -1;
            name = "turnleft";
        }
        
        newfacing = mathlib.addToHexFacing(lastMovement.facing, step);
        newheading = mathlib.addToHexFacing(lastMovement.heading, step);

        if(ship.flight){
            commit = true;
            assignedThrust[0] = requiredThrust[0];
        }
        
        ship.movement[ship.movement.length] = {
            id:-1,
            type:name,
            x:lastMovement.x,
            y:lastMovement.y,
            xOffset:lastMovement.xOffset,
            yOffset:lastMovement.yOffset,
            facing:lastMovement.facing,
            heading:lastMovement.facing,
            speed:lastMovement.speed,
            animating:false,
            animated:false,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:assignedThrust,
            commit:commit,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:0
        }

        hexgrid.unSelectHex();        

        if(!ship.flight){
            shipWindowManager.assignThrust(ship);
        }
    },

    doNormalTurn: function(ship, right){
        var requiredThrust = shipManager.movement.calculateRequiredThrust(ship, right);
        var lastMovement = ship.movement[ship.movement.length-1];
            
        var name;
        var newfacing;
        var newheading;
        var step = 1;

        var commit = false;
        var assignedThrust = Array();

        name = "turnright";
        
        if (!right){
            step = -1;
            name = "turnleft";
        }
        
        newfacing = mathlib.addToHexFacing(lastMovement.facing, step);
        newheading = mathlib.addToHexFacing(lastMovement.heading, step);

        if(ship.flight){
            commit = true;
            assignedThrust[0] = requiredThrust[0];
        }

        ship.movement[ship.movement.length] = {
            id:-1,
            type:name,
            x:lastMovement.x,
            y:lastMovement.y,
            xOffset:lastMovement.xOffset,
            yOffset:lastMovement.yOffset,
            facing:newfacing,
            heading:newheading,
            speed:lastMovement.speed,
            animating:false,
            animated:false,
            animationtics:0,
            requiredThrust:requiredThrust,
            assignedThrust:assignedThrust,
            commit:commit,
            preturn:false,
            at_initiative:shipManager.getIniativeOrder(ship),
            turn:gamedata.turn,
            forced:false,
            value:0
        }

        hexgrid.unSelectHex();

        if(!ship.flight){
            shipWindowManager.assignThrust(ship);
        }
    },

    askForIntoPivotTurn: function(ship, right, message){
        confirm.confirmWithOptions(message, "Turn into Pivot", "Turn", function(respons){
            if(respons){
                shipManager.movement.doIntoPivotTurn(ship, right);
            }else{
                shipManager.movement.doNormalTurn(ship, right);
            }
        });
    },
    
    calculateRequiredThrust: function(ship, right){
        var requiredThrust = Array(null,null,null,null,null);
        
        var speed = shipManager.movement.getSpeed(ship);        
        var turncost = Math.ceil(speed * ship.turncost);
        
        var side, sideindex, rear, rearindex, any;
        
        if (ship.flight){
			if (turncost == 0)
				turncost = 1;
				
			requiredThrust[0] = turncost;
			return requiredThrust;
		}
        
        if (speed===0){
            return Array(1,0,0,0,0);
        }
        
        side = Math.floor(turncost / 2);
        rear = Math.floor(turncost / 2);
        any = turncost % 2;
        
        var back = shipManager.movement.isGoingBackwards(ship);

        var reversed = ((back || shipManager.movement.isRolled(ship)) && !(back && shipManager.movement.isRolled(ship)));
        if (reversed)
            right = !right;
        
        if ( right){
            sideindex = 3;
        }else{
            sideindex = 4;
        }
        
        if ( back ){
            rearindex = 1;
        }else{
            rearindex = 2;
        }
        
        requiredThrust[0] = any;
        requiredThrust[sideindex] = side;
        requiredThrust[rearindex] = rear;
        
		var empty = true;
		for (var i in requiredThrust){
			if (requiredThrust[i] > 0){
				empty = false;
				break;
			}
				
		}
		
		if (empty){
			requiredThrust[0] = 1;
		}
		
        return requiredThrust;
    },
        
    calculateAssignedThrust:    function(ship, movement){
        var assignedarray = Array(null,null,null,null,null);
        
   
        for (var i in movement.assignedThrust){
			if (!ship.systems[i])
				continue;
			var system = ship.systems[i];
			
            
			var mod = 1;
			var crits = shipManager.criticals.hasCritical(system, "HalfEfficiency");
			if (crits > 0){
        		mod = 1/(crits+1);
			}
			

			var sub = 0;
			if (shipManager.criticals.hasCritical(system, "FirstThrustIgnored")){
				if (shipManager.movement.getAmountChanneledReal(ship, system, true) ===0 )
                    sub =1;
			}

            assignedarray[ship.systems[i].direction] += (Math.ceil(movement.assignedThrust[i]*mod)-sub);
			if (assignedarray[ship.systems[i].direction] < 0)
				assignedarray[ship.systems[i].direction] = 0;
        }
        return assignedarray;
    },
    
    calculateThrustStillReq: function(ship, movement){
        var assignedarray = shipManager.movement.calculateAssignedThrust(ship, movement);
        var requiredThrust = movement.requiredThrust;
        var stillReq = requiredThrust.slice();
        var any = 0;
        
        for (var i in requiredThrust){
            var req = requiredThrust[i];
			if (req == null){
				stillReq[i] = null;
				continue;
			}
            var ass =	assignedarray[i];
            
            if ( ass>req){
                stillReq[i] = 0;
                any += ass-req;
            }else{
                stillReq[i] -= ass;
            }   
        }
        
        stillReq[0] -= any;
        
        
        if (movement.type == "pivotright" || movement.type == "pivotleft"){
        
            var reversed = shipManager.movement.hasSidesReversedForMovement(ship);
            var right = (movement.type == "pivotright");
            if (reversed){
                right = !right;
            }
            
            if (right){
                if (assignedarray[1]>0 || assignedarray[3]>0){
                    stillReq[2] = null;
                    stillReq[4] = null;
                }
                if (assignedarray[2]>0 || assignedarray[4]>0){
                    stillReq[1] = null;
                    stillReq[3] = null;
                }
            
            }else{
                if (assignedarray[1]>0 || assignedarray[4]>0){
                    stillReq[2] = null;
                    stillReq[3] = null;
                }
                if (assignedarray[2]>0 || assignedarray[3]>0){
                    stillReq[1] = null;
                    stillReq[4] = null;
                }
            }
            
            
        }
        
        return stillReq;
        
        
        
    },
    
    calculateCurrentTurndelay: function(ship){
        // Get the current speed, whether it's commited or not. (If it's cancelled,
        // We recalculate the turndelay anyway.
        var turndelay = Math.ceil(ship.movement[ship.movement.length-1].speed * ship.turndelaycost);
        var last = null;
        
        if(gamedata.turn == 1){
            turndelay = 0;
        }
        
        for (var i in ship.movement){
            var movement = ship.movement[i];
            if (movement.turn < gamedata.turn-1)
                continue;
                
            if (movement.commit == false)
                continue;
                
            if ((movement.type == "move" 
                || movement.type == "slipright" 
                || movement.type == "slipleft" ) && turndelay > 0)
                turndelay--;
                
            
                
            if (shipManager.movement.isTurn(movement)){
                if (!ship.agile || !last || !shipManager.movement.isTurn(last)){
                    // calculate the turndelay using the NEW speed, iso of the one
                    // in this old movement.
                    turndelay = 
                        shipManager.movement.calculateTurndelay(ship,
                            movement, ship.movement[ship.movement.length-1].speed);
                }
            }
            last = movement;
            
        }
        
        if (turndelay < 0)
            turndelay = 0;
        
        return turndelay;
        
        
    },
    
    calculateTurndelay: function(ship, movement, speed){

        // speed as a seperate parameter needed to allow for calculation with
        // new speed.
		if (speed == 0)
			return 0;
			
        var turndelay = Math.ceil(speed * ship.turndelaycost);
  
        if (ship.flight)
			return turndelay;
   
        turndelay -= shipManager.movement.calculateExtraThrustSpent(ship, movement);
        if (turndelay < 1)
            turndelay = 1;
            
        return turndelay;
    },
    
    calculateExtraThrustSpent: function (ship, movement){
        var reg = shipManager.movement.calculateThrustStillReq(ship, movement);

        var extra = 0 - reg[0];
        
        if ( extra < 0)
            extra = 0;
            
        return extra;

    },
    
    isTurn: function(movement){
		if (!movement)
			console.trace();
			
        return (movement.type == "turnright" || movement.type == "turnleft");
    },
    
    hasSidesReversedForMovement: function(ship){
        var back = shipManager.movement.isGoingBackwards(ship);
        
        var reversed = ((back || shipManager.movement.isRolled(ship)) && !(back && shipManager.movement.isRolled(ship)));
        
        return reversed;
    },
    
    
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
    

	getMovementOffsetPos: function(ship, heading, pos){
		
		if (!hexgrid.isOccupiedPos(pos)){
			return {xO:0, yO:0};
		}
		
		var dir = shipManager.hexFacingToAngle(mathlib.addToHexFacing(heading, 3));
		var per = 0.2;
		if (ship.shipSizeClass<0)
			per *= 2;
		
		
		return hexgrid.getOffsetPositionInHex(pos, dir, per, true)
				
	
	
	},
    
    moveToHex: function(ship, hex, hexpos, shippos){
        
        var movedist = Math.round(shipManager.movement.getRemainingMovement(ship)*hexgrid.hexWidth());
        var drawmore = false;

        if (Math.round(mathlib.getDistance(shippos, hexpos)) < movedist){
           movedist = Math.round(mathlib.getDistance(shippos, hexpos));
        }
        
        var moves = Math.round(movedist/hexgrid.hexWidth());
        
        for (var i = 0;i<moves;i++)
            shipManager.movement.doMove(ship);

    },

    moveStraightForwardHex: function(hex, selected){
        var ship = gamedata.getActiveShip();
        
        if (!shipManager.movement.canMove(ship))
            return;
        
        var pos = shipManager.getShipPosition(ship);
        pos = hexgrid.hexCoToPixel(pos.x, pos.y);
        
        var tpos = hexgrid.hexCoToPixel(hex.x, hex.y);
        
        var a = mathlib.getCompassHeadingOfPoint(pos, tpos);
        
        if (a !== shipManager.getShipDoMAngle(ship))
            return;
        
        if (selected){
            shipManager.movement.moveToHex(ship, hex, tpos, pos);
            

        }else{
            shipManager.movement.adMovementIndicators(ship, hex);
        }
        
    },
    
    
    RemoveMovementIndicators: function(){
        for(var i = EWindicators.indicators.length-1; i >= 0; i--){  
            if(EWindicators.indicators[i].type == "movement"){              
                EWindicators.indicators.splice(i,1);                 
            }
        }
        EWindicators.drawEWindicators();                
        
    },
    
    adMovementIndicators: function(ship, hex){
        
        
        
        var indicators = Array(); 
        
        var indi = shipManager.movement.makeMovementIndicator(ship, hex);
        if (indi)
            indicators.push(indi);
        
              
        if (indicators.length > 0)
            EWindicators.indicators = EWindicators.indicators.concat(indicators);
      
        EWindicators.drawEWindicators();
    }, 
    
    
    
    makeMovementIndicator: function(ship, hex){
       
        var effect = {};
            
         
        effect.ship = ship;
        effect.type = "movement";
        effect.hex = hex;
        //console.log(ball.launchPos + " " + ball.targetPos );
        effect.draw = function(self){
            var ship = self.ship;
            var start = shipManager.getShipPosition(ship);
            start = hexgrid.hexCoToPixel(start.x, start.y);
            
            var end = hexgrid.hexCoToPixel(self.hex.x, self.hex.y);
            var linestart = mathlib.getPointInDistanceBetween(start, end, 38*gamedata.zoom);
           
            var posmove = null;
            var movedist = Math.round(shipManager.movement.getRemainingMovement(ship)*hexgrid.hexWidth());
            var drawmore = false;
            
            if (Math.round(mathlib.getDistance(start, end)) > movedist){
                posmove = mathlib.getPointInDistanceBetween(start, end, movedist);
                drawmore = true;
            }else{
                posmove = end;
            }
            
            var canvas = EWindicators.getEwCanvas();

            
            
            if (drawmore){
                canvas.strokeStyle = "rgba(229,87,38,0.40)";
				canvas.fillStyle = "rgba(179,65,25,0.40)";
                graphics.drawLine(canvas, posmove.x, posmove.y, end.x, end.y, 4*gamedata.zoom);            
                graphics.drawCircleAndFill(canvas, end.x, end.y, 5*gamedata.zoom, 0 );
            }
            
            canvas.strokeStyle = "rgba(86,200,45,0.40)";
            canvas.fillStyle = "rgba(50,122,24,0.40)";
            graphics.drawLine(canvas, linestart.x, linestart.y, posmove.x, posmove.y, 4*gamedata.zoom);            
            graphics.drawCircleAndFill(canvas, posmove.x, posmove.y, 5*gamedata.zoom, 0 );
        };
        
        return effect;
    
    }
    

}
