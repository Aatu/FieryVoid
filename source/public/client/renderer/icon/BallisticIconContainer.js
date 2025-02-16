'use strict';

window.BallisticIconContainer = function () {

    function BallisticIconContainer(coordinateConverter, scene) {
        this.ballisticIcons = [];
        this.ballisticLineIcons = []
        this.coordinateConverter = coordinateConverter;
        this.scene = scene;
        this.zoomScale = 1;
        this.hexNumberIcons = [];
        this.hexNumbersGenerated = false;
    }

    BallisticIconContainer.prototype.consumeGamedata = function (gamedata, iconContainer, replayData = null) {
        this.ballisticIcons.forEach(function (ballisticIcon) {
        	if(gamedata.gamephase !== 1) ballisticIcon.launchPosition = []; //If not cleared, doesn't always display launch hex between Initial and Movement/Firing without browser refresh - DK 12.24
            ballisticIcon.used = false;
        });

        this.ballisticLineIcons.forEach(function (lineIcon) {
            lineIcon.used = false;
        });
               
		if(replayData){ //Pass true marker for Replay
			var allBallistics = replayData;
			
	        allBallistics.forEach(function (ballistic) {
				if(ballistic.turn === gamedata.turn){ 
		            createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn, true); 
		        	createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn, true);	
				}	            
	        }, this);
					
		} else {
        	var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');			
	        allBallistics.forEach(function (ballistic) {
	            createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn);
	            createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn);
	        }, this);
		}
		
        this.ballisticIcons = this.ballisticIcons.filter(function (icon) {
            if (!icon.used) {
                
                if (icon.launchSprite) {
                    this.scene.remove(icon.launchSprite.mesh);
                }

                if (icon.targetSprite) {
                    if (icon.targetId !== -1) {
                        iconContainer.getById(icon.targetId).mesh.remove(icon.targetSprite.mesh);
                    } else {
                        this.scene.remove(icon.targetSprite.mesh);
                    }
                }
                return false;
            }

            return true;
        }, this);

        
        this.ballisticLineIcons = this.ballisticLineIcons.filter(function (lineIcon) {
            if (!lineIcon.used) {
            	if (lineIcon.lineSprite) {
                	this.scene.remove(lineIcon.lineSprite.mesh);           	
				}
			return false;	
			}	

		var isFriendlyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === true
		);

		var isEnemyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === false
		); 
	            
		this.ballisticLineIcons.forEach(lineIcon => {
		  if (lineIcon.lineSprite) {
		    if (lineIcon.isFriendly) {
		      // Handle friendly lines
		      if (isFriendlyLinesVisible) {
		        lineIcon.lineSprite.show();
		        lineIcon.lineSprite.isVisible = true;
		      } else {
		        lineIcon.lineSprite.hide();
		        lineIcon.lineSprite.isVisible = false;
		      }
		    } else {
		      // Handle enemy lines
		      if (isEnemyLinesVisible) {
		        lineIcon.lineSprite.show();
		        lineIcon.lineSprite.isVisible = true;
		      } else {
		        lineIcon.lineSprite.hide();
		        lineIcon.lineSprite.isVisible = false;
		      }
		    }
		  }
		});
		
            return true;
        }, this);
                
    };


    BallisticIconContainer.prototype.onEvent = function (name, payload) {
        var target = this['on' + name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    BallisticIconContainer.prototype.onZoomEvent = function (payload) {
        var zoom = payload.zoom;
        if (zoom <= 0.5) {
            this.zoomScale = 2 * zoom;
            this.ballisticLineIcons.forEach(function (lineIcon) {
                lineIcon.lineSprite.setLineWidth(this.zoomScale * 2);
            }, this);
        } else {
            this.zoomScale = 1;
        }
/*        
        //Immediately hide if zooming while lines are up.
        this.ballisticLineIcons = this.ballisticLineIcons.filter(function (lineIcon) {	           
	        if (lineIcon.lineSprite) {
	            lineIcon.lineSprite.hide();
	            lineIcon.lineSprite.isVisible = false;	 	            
	        }
            return true;
        }, this);        
*/        
    };



    BallisticIconContainer.prototype.hide = function () {
        this.ballisticIcons.forEach(function (icon) {
            if (icon.launchSprite) {
                icon.launchSprite.hide();
            }
            if (icon.targetSprite) {
                icon.targetSprite.hide();
            }
        });

        return this;
    };

    BallisticIconContainer.prototype.show = function () {
        this.ballisticIcons.forEach(function (icon) {
            if (icon.launchSprite) {
                icon.launchSprite.show();
            }
            if (icon.targetSprite) {
                icon.targetSprite.show();
            }
        });

        return this;
    };

    function createOrUpdateBallistic(ballistic, iconContainer, turn, replay = false) {
        var icon = getBallisticIcon.call(this, ballistic.id);

	    if (icon && ballistic.notes != 'PersistentEffect' && ballistic.notes != 'Split') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
	        updateBallisticIcon.call(this, icon, ballistic, iconContainer, turn);
	    } else {   	
	        createBallisticIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
	    }
    	
	}		


    function updateBallisticIcon(icon, ballistic, iconContainer, turn) {
        icon.used = true;
    }


    function createBallisticIcon(ballistic, iconContainer, turn, scene, replay = false) {

			if(replay){
				if(ballistic.damageclass == 'PersistentEffectPlasma' && ballistic.targetid == -1 && ballistic.notes != 'PlasmaCloud') return;
			}

	        var shooterIcon = iconContainer.getById(ballistic.shooterid);	
//	        if(!shooterIcon) shooterIcon = iconContainer.getById(ballistic.shooter.id); //Do I still need?
			var targetType = 'hexRed'; //Default red hex if none of the later conditions are true.
	        var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
	        var text = ""; //Additional variable that can pass text to new BallisticSprite()
	        var textColour = ""; //Additional variable that chooses base text colour for new BallisticSprite()
			var iconImage = null; //Additional variable that can pass images to new BallisticSprite()!
			
			//New variables to enhance Ballistic Icons further! - DK 10.24
			var shooter = shooterIcon.ship; //Get shooter info.
			var modeName = null;
			
			if(!shooter.flight){ //Fighters don't currently have hex target weapons (plus, would need to find weaponid differently)
				var weapon = shooter.systems[ballistic.weaponid]; //Find weapon			
				var modeName = weapon.firingModes[ballistic.firingMode]; //Get actual Firing Mode name, so we can be more specific below!
			}
			
			if (ballistic.type == 'normal') { //it's direct fire after all!
			    launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
				if(modeName){
					switch (modeName) {			
					case 'Shredder': //Vree Anti-Matter Shredder
					        targetType = 'hexBlue';
					        text = "Shredder";
					        textColour = "#00b8e6";				        
					break;
					case 'Defensive Plasma Web': //Pak'ma'ra Plasma Web Defensive
					        targetType = 'hexGreen';
					        textColour = "#787800";				        	
					break;									
					case 'Anti-Fighter Plasma Web': //Pak'ma'ra Plasma Web Offensive
					        targetType = 'hexGreen';
					        text = '!';
					        textColour = "#787800";				        	
					break;
					case 'Psychic Field': //Thirdspace Psychic Field
					        targetType = 'hexRed';
					        text = "Psychic";
					        textColour = "#e6140a";		        
					break;						
					case 'Second Sight': //Mindrider Second Sight
					        targetType = 'hexPurple';
					        text = "Second Sight";
					        textColour = "#7f00ff";			        
					break;								
					default:
					        targetType = 'hexRed';
					break;													        
					}				
				}

			}else if (ballistic.targetid == -1){ //Maybe its nice to have other colours for certain types of hex targetted weapons?
				
				if(modeName){
					switch (modeName) {			
					case 'Energy Mine': //Narn Energy Mine
					        targetType = 'hexRed';
					        text = "Energy Mine";
					        textColour = "#e6140a";				        
					break;				
					case 'Ion Storm': //Cascor Ion Field
					        targetType = 'hexPurple';
					        text = "Ion Field";
					        textColour = "#7f00ff";				        
					break;
					case 'Jammer': //Jammer Missile
						    targetType = 'hexPurple';
						    text = "Jammer Missile";
						    textColour = "#7f00ff";		        
						break;					
					case 'Anti-Fighter Plasma Web': //Pak'ma'ra Plasma Web Persistent Effect
					        targetType = 'hexGreen';
					        text = 'Plasma';
					        textColour = "#787800";				        	
					break;
					case 'Proximity Laser': //KL Proximity Laser
					        targetType = 'hexRed';
					        text = "Proximity Laser";
					        textColour = "#e6140a";		        
					break;
					case 'Thought Wave': //Mindrider Thoughwave
					        targetType = 'hexPurple';
					        text = "Thought Wave";
					        textColour = "#bc3782"; //Actually a pink-purple, as it blends with luanch hex Orange!
	//				        iconImage = "./img/systemicons/ThoughtWaveICON.png";  //Example image to pass	 		        
					break;													        
					}				
				}
			} 
			 
		//Ballistic Mines / Support etc need to be handled separately using damageClass/initializationUpdate method! - DK 10.24
		if(ballistic.damageclass){
			if(modeName){
				switch (ballistic.damageclass) {	
				case 'MultiModeHex': //Hex-Weapons with multiple modes.
					targetType = 'hexRed';
					text = modeName;					
					textColour = "#e6140a";		        
				break;											
				case 'support': //Generic Support icon for these type of weapons. 06.24 - DK	
					targetType = 'hexGreen';
					iconImage = "./img/allySupport.png"; 		        
				break;
				case 'Sweeping': //Shadow Slicers
					    targetType = 'hexPurple';		        
				break;			
				}
			}		 
		} 
		  		
	        var targetPosition = null;
	        var targetIcon = null;

	        if (ballistic.targetid === -1 && ballistic.x !== "null" && ballistic.y !== "null") {
	            targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
	        } else if (ballistic.targetid && ballistic.targetid !== -1) {
	            targetIcon = iconContainer.getById(ballistic.targetid);
	            targetPosition = { x: 0, y: 0 };
	        }

			if(!shooter.flight){ //Don't create target hex for certain ship weapons.
				if(weapon.noTargetHexIcon) targetPosition = launchPosition;
			}	

			//Create orange launch icon on firing ship.
	        var launchSprite = null;
	        
			//Don't create launch sprite for duplicates, persistent effects or Direct Fire/Support Weapon 
	        if ((!getByLaunchPosition(launchPosition, this.ballisticIcons)) && ballistic.notes != 'PersistentEffect' && ballistic.type !== 'normal' && ballistic.damageclass !== 'support') {        	
				    if(gamedata.isMyOrTeamOneShip(shooter)){
						launchSprite = new BallisticSprite(launchPosition, 'hexYellow');       
				        scene.add(launchSprite.mesh);	        		
			       	}else{
						launchSprite = new BallisticSprite(launchPosition, 'hexOrange');       
				        scene.add(launchSprite.mesh);
					}
		    }

	        var targetSprite = null;

	        if (!getByTargetIdOrTargetPosition(targetPosition, ballistic.targetId, this.ballisticIcons)) {	
	            if (targetIcon && targetPosition) {
	                targetSprite =  new BallisticSprite(targetPosition, targetType, text, textColour, iconImage);//'hex');
	                targetIcon.mesh.add(targetSprite.mesh);
	            } else if (targetPosition) {
	                targetSprite =  new BallisticSprite(targetPosition, targetType, text, textColour, iconImage);//'hex');
	                scene.add(targetSprite.mesh);
	            }
	        }


	        this.ballisticIcons.push({
	            id: ballistic.id,
	            shooterId: ballistic.shooterid,
	            targetId: ballistic.targetid,
	            launchPosition: launchPosition,
	            position: new hexagon.Offset(ballistic.x, ballistic.y),
	            launchSprite: launchSprite,
	            targetSprite: targetSprite,
	            used: true
	        });
    
		
    }//endof createBallisticIcon()

    const getByLaunchPosition = (position, icons) => icons.find(icon => icon.launchPosition.x === position.x && icon.launchPosition.y === position.y)

    const getByTargetIdOrTargetPosition = (position, targetId, icons) => icons.find(icon => position && ((icon.position.x === position.x && icon.position.y === position.y) || (targetId !== -1 && icon.targetId === targetId )) )


    function getBallisticIcon(id) {
        return this.ballisticIcons.filter(function (icon) {
            return icon.id === id;
        }).pop();
    }

	//New code to create ballistic lines between launches and targets - DK 12.24
    BallisticIconContainer.prototype.updateLinesForShip = function (ship, iconContainer) {

		var wasVisibleTarget = false; //Variable to track if destroyed lines were visible. If one was, they all were.
		var wasVisibleShooter = false; //Variable to track if destroyed lines were visible. If one was, they all were.
		
		this.ballisticLineIcons = this.ballisticLineIcons.filter((lineIcon) => {
			// Destroy lines where the ship is either the target or the shooter.  Ship being checked should only ever be one or the other.
			if (lineIcon.targetId === ship.id) {
			    if (lineIcon.lineSprite.isVisible) wasVisibleTarget = true;
			    this.scene.remove(lineIcon.lineSprite.mesh);
			    lineIcon.lineSprite.destroy();
			    return false;
			}else if (lineIcon.shooterId === ship.id) {
			    if (lineIcon.lineSprite.isVisible) wasVisibleShooter = true;
			    this.scene.remove(lineIcon.lineSprite.mesh);
			    lineIcon.lineSprite.destroy();
			    return false;
			}else{		    
		    	return true; // Keep the lineIcon if the condition isn't met
			}
		});

		//Now recreate them using usual method.
        var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');			
	    allBallistics.forEach(function (ballistic) {
	        createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn);
	    }, this);

		//Check if lines were visible and if so continue to show.
        this.ballisticLineIcons.forEach(function (lineIcon) {
            if (lineIcon.targetId === ship.id) {
	            if(!wasVisibleTarget){
	            	lineIcon.lineSprite.hide();
	            	lineIcon.lineSprite.isVisible = false;	 	            
            	}else{
	            	lineIcon.lineSprite.show();
	            	lineIcon.lineSprite.isVisible = true;	            		
				}
            }else if(lineIcon.shooterId === ship.id) {
	            if(!wasVisibleShooter){
	            	lineIcon.lineSprite.hide();
	            	lineIcon.lineSprite.isVisible = false;	 	            
            	}else{
	            	lineIcon.lineSprite.show();
	            	lineIcon.lineSprite.isVisible = true;	            		
				}
			}	
        });        
    };

	//New method that toggles Ballistic Lines on and off.
	BallisticIconContainer.prototype.toggleBallisticLines = function (ships) {
	    if (this.ballisticLineIcons) {
	        this.ballisticLineIcons.forEach(function (lineIcon) {
	            if (lineIcon.lineSprite) {
		            if (ships.some(ship => ship.id === lineIcon.shooterId)) {
	            		if (!lineIcon.lineSprite.isVisible){		                	
		                    lineIcon.lineSprite.show();
	            			lineIcon.lineSprite.isVisible = true;	                    
		                }else{
		                    lineIcon.lineSprite.hide();
	            			lineIcon.lineSprite.isVisible = false;	 		                    		             	
		                }
					}    
	            }	            
	        });
	    }
	    return this;
	};

    BallisticIconContainer.prototype.hideLines = function (ships) {
        this.ballisticLineIcons.forEach(function (lineIcon) {
	        if (lineIcon.lineSprite) {
		        if (ships.some(ship => ship.id === lineIcon.shooterId)) {
					lineIcon.lineSprite.hide();
	        		lineIcon.lineSprite.isVisible = false;
				}
			}			 	
        });

        return this;
    };

    BallisticIconContainer.prototype.showLines = function (ships) {
        this.ballisticLineIcons.forEach(function (lineIcon) {
	        if (lineIcon.lineSprite) {
		        if (ships.some(ship => ship.id === lineIcon.shooterId)) {        	
					lineIcon.lineSprite.show();
			        lineIcon.lineSprite.isVisible = true;
				}
			}		        	 	
        });

        return this;
    };
	

    function createOrUpdateBallisticLines(ballistic, iconContainer, turn, replay = false) {
        var lineIcon = getBallisticLineIcon.call(this, ballistic.id);

	    if (lineIcon && ballistic.notes != 'PersistentEffect' && ballistic.notes != 'Split') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
	        if(replay){
	        	createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);	        	   	
			}else{	
				updateBallisticLineIcon.call(this, lineIcon, ballistic, iconContainer, turn);
			}	
	    } else {   	
	        if(ballistic.notes != 'PersistentEffect') createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
	    }
    	
	}	


    function updateBallisticLineIcon(lineIcon, ballistic, iconContainer, turn) {
        lineIcon.used = true;

		if(ballistic.targetid === -1) return; //No need to update further for AoE ballistics (they don't move) 
 
	    var shooterIcon = null;		
	    shooterIcon = iconContainer.getById(ballistic.shooterid);        
	    var targetIcon = null;
	    targetIcon = iconContainer.getById(ballistic.targetid);

		//Update start and end points for Ballistic Line as required.
		var shooterLastMove = shipManager.movement.getLastCommitedMove(shooterIcon.ship);
		var shooterNewPosition = this.coordinateConverter.fromHexToGame(shooterLastMove.position);  	          
		var targetLastMove = shipManager.movement.getLastCommitedMove(targetIcon.ship);
		var targetNewPosition = this.coordinateConverter.fromHexToGame(targetLastMove.position);        

        lineIcon.lineSprite.start = shooterNewPosition;
        lineIcon.lineSprite.end = targetNewPosition;
    }	


    function createBallisticLineIcon(ballistic, iconContainer, turn, scene, replay = false) {

	    var shooterIcon = null;		
	    shooterIcon = iconContainer.getById(ballistic.shooterid);        
	    var targetIcon = null;
	    targetIcon = iconContainer.getById(ballistic.targetid);

		//Get Launch Position and Target Position
	    var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
	        						
		var targetPosition = null;
	    if(targetIcon && ballistic.targetid !== -1){
			targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovement(turn).position);
	    }else{
	        targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
		}
		//Slightly different method for Replays.  Could I make a shipManager.movement.getLastCommitedMoveONTURN(targetIcon.ship) function???	
		if(replay && targetIcon) targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovementOnTurn(turn).position);	

		//Get shooter and modeName from it.
		var shooter = shooterIcon.ship; //Get shooter info.
		var modeName = null;	
		if(!shooter.flight){ //Fighters would need to find weaponid differently
			var weapon = shooter.systems[ballistic.weaponid]; //Find weapon			
			var modeName = weapon.firingModes[ballistic.firingMode]; //Get actual Firing Mode name
		}	
 
		if(!shooter.flight){ //Don't create target hex for certain ship weapons.
			if(weapon.noTargetHexIcon) targetPosition = launchPosition;
		}
			
		if (launchPosition == null || targetPosition == null || 
		    (launchPosition.x === targetPosition.x && 
		     launchPosition.y === targetPosition.y && 
		     launchPosition.z === targetPosition.z)) {
//		    console.warn("Skipped creating line sprite for zero-length line:", ballistic.id);
		    return;
		} //New check to NOT create a line if ballistic is in same hex (or positions are null/undefined.
		    	
		var type = 'white'; //Default white line if none of the later conditions are true.
		if(gamedata.isMyOrTeamOneShip(shooterIcon.ship)){
			type = 'yellow';				
		}else{
			type = 'orange';				
		}

		if(ballistic.type == 'normal'){
			    launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn).position);			
				if(modeName){
					switch (modeName) {			
						case 'Shredder': //Vree Anti-Matter Shredder
							type = 'blue';				        
						break;
						case 'Defensive Plasma Web': //Pak'ma'ra Plasma Web Defensive
							type = 'green';				        	
						break;									
						case 'Anti-Fighter Plasma Web': //Pak'ma'ra Plasma Web Offensive
							type = 'green';				        	
						break;									
						default:
						    type = 'white';
						break;													        
					}				
				}	 
		}
		
		if(ballistic.damageclass){
			switch (ballistic.damageclass) {	
				case 'support': //Generic Support icon for these type of weapons. 06.24 - DK	
					type = 'green';
					launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement(turn).position); //More important to show where ship support weapon originates, not hex.								        
				break;
				case 'Sweeping': //Shadow Slicer
					type = 'purple';			        
				break;					
			}		 
		}
			
	        var lineSprite = null;	        
	    	var isFriendly = gamedata.isMyOrTeamOneShip(shooter);

	        this.ballisticLineIcons.push({
	            id: ballistic.id,
	            shooterId: ballistic.shooterid,
	            targetId: ballistic.targetid,
	            lineSprite: lineSprite =  new BallisticLineSprite(launchPosition, targetPosition, 3 * this.zoomScale, -3, getLineColorByType(type), 0.4),
	            used: true,
	            isFriendly: isFriendly
	        });

	        scene.add(lineSprite.mesh);

		//Need some checks here to handle when lines are toggled on and new ballistic fireORder/line is added

		var isFriendlyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === true
		);

		var isEnemyLinesVisible = this.ballisticLineIcons.some(lineIcon => 
		  lineIcon.lineSprite?.isVisible === true && lineIcon?.isFriendly === false
		);  

		var ballisticIdToFind = ballistic.id;
		var currentIcon = this.ballisticLineIcons.find(lineIcon => lineIcon.id === ballisticIdToFind );

		if(isFriendly && isFriendlyLinesVisible){
			currentIcon.lineSprite.isVisible = true;		
		}else if(!isFriendly && isEnemyLinesVisible){
			currentIcon.lineSprite.isVisible = true;						
		}else{
			currentIcon.lineSprite.isVisible = false;			
		}
		
    }//endof createBallisticLineIcon()


    function getBallisticLineIcon(id) {
        return this.ballisticLineIcons.filter(function (lineIcon) {
            return lineIcon.id === id;
        }).pop();
    }

    function getLineColorByType(type) {
        if (type == "orange") {
            return "rgba(250,153,53)"; 
        } else if (type == "yellow") {
            return "rgba(255, 255, 0)";
        } else if (type == "red") {
            return "rgba(230,20,10)";
        } else if (type == "blue") {
            return "rgba(0,184,230)";
        } else if (type == "green") {
            return "rgba(0, 204, 0)";
        } else if (type == "purple") {
            return "rgba(127, 0, 255)";
        } else if (type == "white") {
            return "rgba(255, 255, 255)";
        } else {
            return "rgba(144,185,208)";
        }
    }

/* //Future functions that I might need/use - DK 12.24
    BallisticIconContainer.prototype.showForShip = function (ship) {
        this.ballisticLineIcons.filter(function (lineIcon) {
            return lineIcon.shooterId === ship.id || icon.targetId === ship.id;
        }).forEach(function (lineIcon) {
            lineIcon.lineSprite.setStartAndEnd(lineIcon.shooterIcon.getPosition(), lineIcon.targetIcon.getPosition());
            lineIcon.lineSprite.show();
        }, this);
    };

    BallisticIconContainer.prototype.showByShip = function (ship) {
        this.ballisticLineIcons.filter(function (lineIcon) {
            return lineIcon.shooterId === ship.id;
        }).forEach(function (lineIcon) {
            lineIcon.lineSprite.setStartAndEnd(lineIcon.shipIcon.getPosition(), lineIcon.targetIcon.getPosition());
            lineIcon.lineSprite.show();
	        lineIcon.lineSprite.isVisible = true;            
        }, this);
    };
*/

/* //OLD METHOD FOR GENERATING HEX NUMBERS, WHICH CREATED 2000ish individual sprites.  Leaving for now until new method is tested on main server. 
BallisticIconContainer.prototype.createHexNumbers = function (scene) {

    // Check if hex numbers are already created
    if (this.hexNumberIcons.length > 0) {
        // If the visibility state hasn't changed, do nothing
        if (this.hexNumbersVisible) {
            this.hexNumberIcons.forEach(icon => icon.hide());
        } else {
            this.hexNumberIcons.forEach(icon => icon.show());
        }

        this.hexNumbersVisible = !this.hexNumbersVisible;  // Toggle visibility state
    } else {
        // Start at (q: -25, r: 19)
        let startHex = { q: -22, r: 16 };
        let currentHex = startHex;
        let number = 1;
        let textColour = "#ffffff";
        let hexCreate = null;

        // Array to store HexNumberSprites for later addition to the scene
        let hexNumberSprites = [];

        while (currentHex.r >= -16) {
            // Loop through the columns of the current row (from q: -25 to q: 25)
            for (let q = -22; q <= 22; q++) {
                currentHex = { q: q, r: currentHex.r };
                hexCreate = this.coordinateConverter.fromHexToGame(currentHex);

                // Create HexNumberSprite for the current hex
                let hexNumberSprite = new HexNumberSprite(
                    hexCreate, 'hexTransparent', String(number).padStart(4, '0'), textColour, 27, 0.8
                );

                // Store the sprite in the batch array for the current row
                hexNumberSprites.push(hexNumberSprite);

                // Increment the number for the next sprite
                number++;
            }

            // After finishing the row, add all sprites in the batch to the scene
            this.hexNumberIcons.push(...hexNumberSprites);  // Store the batch in hexNumberIcons

            // Instead of adding sprites one by one, add all to the scene at once
            scene.add(...hexNumberSprites.map(sprite => sprite.mesh));

            // Reset the batch array for the next row
            hexNumberSprites = [];

            // Move to the next row (decrease r)
            currentHex.r--;
        }

        this.hexNumbersVisible = true;  // Ensure the hex numbers are visible
    }
};
*/

BallisticIconContainer.prototype.createHexNumbers = function (scene) {
    if (this.hexNumberMesh) {
        // Toggle visibility
        this.hexNumberMesh.visible = !this.hexNumberMesh.visible;
        return;
    }

    // Define grid dimensions based on hex count
    const gridWidth = 72; // Adjust based on your hex layout
    const gridHeight = 48;
    const hexSize = 50;

    // Create single large texture with all hex numbers
    const largeTexture = createLargeHexNumberTexture(gridWidth, gridHeight, hexSize);

    // Hexagon grid dimensions (corrected aspect ratio)
    const totalWidth = gridWidth * hexSize * 2;
    const totalHeight = gridHeight * hexSize * Math.sqrt(4);

    // Create a plane to apply the texture (or adjust for hexagonal shape)
    const geometry = new THREE.PlaneGeometry(totalWidth, totalHeight);
    const material = new THREE.MeshBasicMaterial({ 
        map: largeTexture, 
        transparent: true 
    });

    this.hexNumberMesh = new THREE.Mesh(geometry, material);
    this.hexNumberMesh.position.set(502.5, -651, -1); // Adjust as needed	
    scene.add(this.hexNumberMesh);
};

function createLargeHexNumberTexture(gridWidth, gridHeight, hexSize, textColour = "#ffffff") {
	const HEX_WIDTH = Math.sqrt(3) * hexSize;  // Corrected for side-standing hexagons
	const HEX_HEIGHT = 2 * hexSize; // Corrected for vertical stacking
	const SCALE_FACTOR = 2;  // Increase resolution for sharp text
	const TEXTURE_WIDTH = gridWidth * HEX_WIDTH * SCALE_FACTOR;
	const TEXTURE_HEIGHT = gridHeight * HEX_HEIGHT * SCALE_FACTOR;
    
    // Create canvas with refined dimensions
    const canvas = document.createElement("canvas");
    canvas.width = TEXTURE_WIDTH;
    canvas.height = TEXTURE_HEIGHT;
    const ctx = canvas.getContext("2d");

    // Clear the canvas
    ctx.clearRect(0, 0, TEXTURE_WIDTH, TEXTURE_HEIGHT);

    // Set text properties
	const fontSize = Math.floor(hexSize * 0.2 * SCALE_FACTOR);  // Smaller text but sharp
    ctx.globalAlpha = 0.85;
    ctx.font = `bold ${fontSize}px Arial`;
    ctx.fillStyle = textColour;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
	ctx.globalAlpha = 0.6; // 50% transparency

    let number = 1;

	for (let r = 0; r < gridHeight; r++) {
		for (let q = 0; q < gridWidth; q++) {
			let x = q * HEX_WIDTH * 1.7315 + HEX_WIDTH / 2; // REDUCED COLUMN SPACING			
			let y = r * HEX_HEIGHT * 1.5 + HEX_HEIGHT / 2; // INCREASED ROW SPACING
	
			// Offset odd rows for staggered hex layout
			if (r % 2 !== 0) x += HEX_WIDTH * 0.855; 
	
			ctx.fillText(String(number).padStart(4, '0'), x, y);
			number++;
		}
	}

    // Convert canvas to a texture
    const texture = new THREE.Texture(canvas);
    texture.needsUpdate = true;
    return texture;
}
    return BallisticIconContainer;
}();