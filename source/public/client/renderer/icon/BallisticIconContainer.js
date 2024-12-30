'use strict';

window.BallisticIconContainer = function () {

    function BallisticIconContainer(coordinateConverter, scene) {
        this.ballisticIcons = [];
        this.ballisticLineIcons = []
        this.coordinateConverter = coordinateConverter;
        this.scene = scene;
        this.zoomScale = 1;
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

	    if (icon && ballistic.notes != 'Persistent Effect') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
	        updateBallisticIcon.call(this, icon, ballistic, iconContainer, turn);
	    } else {   	
	        createBallisticIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
	    }
    	
	}		


    function updateBallisticIcon(icon, ballistic, iconContainer, turn) {
        icon.used = true;
    }


    function createBallisticIcon(ballistic, iconContainer, turn, scene, replay = false) {
						
	        var shooterIcon = iconContainer.getById(ballistic.shooterid);	
//	        if(!shooterIcon) shooterIcon = iconContainer.getById(ballistic.shooter.id); //Do I still need?
			var targetType = 'hexRed'; //Default red hex if none of the later conditions are true.
	        var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
	        var text = ""; //Additional variable that can pass text to new BallisticSprite()
	        var textColour = ""; //Additional variable that chooses base text colour for new BallisticSprite()
			var iconImage = null; //Additional variable that can pass images to new BallisticSprite()!
			
			//New variables found to enhance Ballistic Icons further! - DK 10.24
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
					case 'Psychic Field': //Mindrider Second Sight
					        targetType = 'hexRed';
					        text = "Psychic";
					        textColour = "#e6140a";
	//				        iconImage = "./img/systemicons/SecondSightICON.png"; //Example image to pass			        
					break;	
					case 'Second Sight': //Thirdspace Psychic Field
					        targetType = 'hexPurple';
					        text = "Second Sight";
					        textColour = "#7f00ff";			        
					break;			
					default:
					        targetType = 'hexYellow';
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
			switch (ballistic.damageclass) {	
				case 'MultiModeHex': //Hex-Weapons with multiple modes.
					targetType = 'hexRed';
					text = modeName;					
					textColour = "#e6140a";		        
				break;										
				
				case 'Support': //Generic Support icon for these type of weapons. 06.24 - DK	
					targetType = 'hexGreen';
//					textColour = "#00dd00";   
					iconImage = "./img/allySupport.png"; 		        
				break;
		
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

			//Create orange launch icon on firing ship.
	        var launchSprite = null;
	        
			//Don't create launch sprite for duplicates, persistent effects or Direct Fire 
	        if ((!getByLaunchPosition(launchPosition, this.ballisticIcons)) && ballistic.notes != 'Persistent Effect' && ballistic.type !== 'normal') {        	
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
	            if(modeName == 'ThoughtWave') targetPosition = launchPosition;//Don't create target hex for Thougtwave, just create on launch hex.
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

	//New code to create ballistic lines between laucnhes and targets - Dk 12.24
    BallisticIconContainer.prototype.updateLinesForShip = function (ship, iconContainer) {

		var wasVisible = false;
/*
        this.ballisticLineIcons.forEach(function (lineIcon) {
            if (lineIcon.targetId === ship.id) {
                lineIcon.used = false;
            }
        });
*/
		this.ballisticLineIcons = this.ballisticLineIcons.filter((lineIcon) => {

		    if (lineIcon.targetId === ship.id) {	    	
		    	if (lineIcon.lineSprite.isVisible) wasVisible = true;
		        this.scene.remove(lineIcon.lineSprite.mesh);
		        lineIcon.lineSprite.destroy();
		        return false;
		    }
		    return true; // Keep the lineIcon if the condition isn't met
		});


        var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');			
	    allBallistics.forEach(function (ballistic) {
	        createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn);
	    }, this);


        this.ballisticLineIcons.forEach(function (lineIcon) {
            if (lineIcon.targetId === ship.id) {
	            if(!wasVisible){
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

    BallisticIconContainer.prototype.hideLines = function () {
        this.ballisticLineIcons.forEach(function (lineIcon) {
			lineIcon.lineSprite.hide();
	        lineIcon.lineSprite.isVisible = false;	 	
        });

        return this;
    };

    BallisticIconContainer.prototype.showLines = function () {
        this.ballisticLineIcons.forEach(function (lineIcon) {
			lineIcon.lineSprite.show();
	        lineIcon.lineSprite.isVisible = true;	 	
        });

        return this;
    };
	

    function createOrUpdateBallisticLines(ballistic, iconContainer, turn, replay = false) {
        var lineIcon = getBallisticLineIcon.call(this, ballistic.id);

	    if (lineIcon && ballistic.notes != 'Persistent Effect') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
	        if(replay){
	        	createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);	        	   	
			}else{	
				updateBallisticLineIcon.call(this, lineIcon, ballistic, iconContainer, turn);
			}	
	    } else {   	
	        createBallisticLineIcon.call(this, ballistic, iconContainer, turn, this.scene, replay);
	    }
    	
	}	
	
    function updateBallisticLineIcon(lineIcon, ballistic, iconContainer, turn) {
        lineIcon.used = true;

		if(ballistic.targetid === -1) return; //No need to update further for AoE ballistics (they don't move) 

	    var targetIcon = iconContainer.getById(ballistic.targetid);  
	          
		var lastMove = shipManager.movement.getLastCommitedMove(targetIcon.ship);
		var newPosition = this.coordinateConverter.fromHexToGame(lastMove.position);        

        lineIcon.lineSprite.end = newPosition;
    }	

    function createBallisticLineIcon(ballistic, iconContainer, turn, scene, replay = false) {
		
	        var shooterIcon = iconContainer.getById(ballistic.shooterid);
	        
	        var targetIcon = null;
	        targetIcon = iconContainer.getById(ballistic.targetid);
	        	
			var type = 'blue'; //Default blue hex if none of the later conditions are true.
			if(gamedata.isMyOrTeamOneShip(shooterIcon.ship)){
				type = 'yellow';				
			}else{
				type = 'orange';				
			}
			
			var targetPosition = null;

	        if(targetIcon && ballistic.targetId !== -1){
				targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovement(turn).position);
	        }else{
	        	targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
			}
			
			if(replay && targetIcon) targetPosition = this.coordinateConverter.fromHexToGame(targetIcon.getLastMovementOnTurn(turn).position);

	        var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);	

			//New variables found to enhance Ballistic Icons further! - DK 10.24
			var shooter = shooterIcon.ship; //Get shooter info.
			var modeName = null;
			
			if(!shooter.flight){ //Fighters would need to find weaponid differently
				var weapon = shooter.systems[ballistic.weaponid]; //Find weapon			
				var modeName = weapon.firingModes[ballistic.firingMode]; //Get actual Firing Mode name
			}	

			if(modeName == 'ThoughtWave') targetPosition = launchPosition; //Only one weapon needs, for now.
			
	        var lineSprite = null;
	        
	        var isFriendly = gamedata.isMyOrTeamOneShip(shooter);

	        this.ballisticLineIcons.push({
	            id: ballistic.id,
	            shooterId: ballistic.shooterid,
	            targetId: ballistic.targetid,
//	           	shipIcon: shooterIcon,
//	            targetIcon: targetIcon,
	            lineSprite: lineSprite =  new BallisticLineSprite(launchPosition, targetPosition, 3 * this.zoomScale, -3, getLineColorByType(type), 0.6),
	            used: true,
	            isFriendly: isFriendly
	        });

	        scene.add(lineSprite.mesh);

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
        } else if (type == "red") {
            return "rgba(230,20,10)";
        } else if (type == "blue") {
            return "rgba(0,184,230)";
        } else if (type == "green") {
            return "rgba(0, 204, 0)";
        } else if (type == "yellow") {
            return "rgba(255, 255, 0)";
        } else if (type == "purple") {
            return "rgba(127, 0, 255)";
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

/* //OLD METHOD where button just showed lines whilst held down.
    BallisticIconContainer.prototype.hideBallisticLines = function (friendly, ships) {
		if(this.ballisticLineIcons){
	        this.ballisticLineIcons.forEach(function (lineIcon) {
	            if (lineIcon.lineSprite) {
	                if (ships.some(ship => ship.id === lineIcon.shooterId)) {
		                lineIcon.lineSprite.hide();
	            		lineIcon.lineSprite.isVisible = false;	 		                
					}    
	            }
	        });
		}
        return this;
    };

	BallisticIconContainer.prototype.showBallisticLines = function (friendly, ships) {
	    if (this.ballisticLineIcons) {
	        this.ballisticLineIcons.forEach(function (lineIcon) {
	            if (lineIcon.lineSprite) {
	                if (ships.some(ship => ship.id === lineIcon.shooterId)) {
	                    lineIcon.lineSprite.show();
	            		lineIcon.lineSprite.isVisible = true;
	                }
	            }
	        });
	    }
	    return this;
	};
*/

    return BallisticIconContainer;
}();