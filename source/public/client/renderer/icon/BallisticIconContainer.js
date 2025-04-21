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

        //Remove old line sprites when they are no longer required or being recreated.
        this.ballisticLineIcons = this.ballisticLineIcons.filter(function (lineIcon) {
            if (!lineIcon.used) {
            	if (lineIcon.lineSprite) {
                	this.scene.remove(lineIcon.lineSprite.mesh);           	
				}
			return false;	
			}	

		//This section looks to see if Ballistic Lines are showing at moment consumeGamedata() is called (which is often!)		
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

		//Now create perimter hex icons to illustrate the hexes occupied by really large terrain occupy.
		generateTerrainHexes.call(this, gamedata);

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


    function generateTerrainHexes(gamedata) {
		// Filter for ships with Huge value
		gamedata.ships
		.filter(ship => ship.Huge > 0) // Find Huge Terrain
		.forEach(ship => {
			if(gamedata.gamephase !== -1){ //Don't generate sprites until Terrain is in place!
				const position = shipManager.getShipPosition(ship); // Get ship's position
	/*			var positionGame = this.coordinateConverter.fromHexToGame(position);
				// Create a sprite at the ship's position
				const sprite = new BallisticSprite(positionGame, "hexWhite");
				this.scene.add(sprite.mesh);

				this.ballisticIcons.push({
					id: -5,
					shooterId: ship.id,
					targetId: ship.id,
					launchPosition: position,
					position: new hexagon.Offset(positionGame.x, positionGame.y),
					launchSprite: sprite,
					targetSprite: null,
					used: true
				});
*/

				let perimeterHexes = [];
				// Get neighboring hexes based on the ship's size (Huge)
				if(ship.Huge == 2){
					perimeterHexes = mathlib.getPerimeterHexes(position, ship.Huge);
				}else{
					perimeterHexes = mathlib.getNeighbouringHexes(position, ship.Huge);
				}	

				// Create sprites for neighboring hexes
				perimeterHexes.forEach(neighbour => {
					var neighbourPosGame = this.coordinateConverter.fromHexToGame(neighbour);
					const neighbourSprite = new BallisticSprite(neighbourPosGame, "hexWhite");
					this.scene.add(neighbourSprite.mesh);

					this.ballisticIcons.push({
						id: -5,
						shooterId: ship.id,
						targetId: ship.id,
						launchPosition: neighbour,
						position: new hexagon.Offset(neighbourPosGame.x, neighbourPosGame.y),
						launchSprite: neighbourSprite,
						targetSprite: neighbourSprite,
						used: true
					});
				
				});
			}	

		});
    } //endof generateTerrainHexes()

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
			if(ballistic.damageclass == 'Sweeping')	return;	//For Shadow Slicers, Gravs Beams etc. Let's just rely on lines and targeting tooltip and not clutter with Hex colours.	

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
					case 'Proximity Launcher': //KL Proximity Laser
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
/*				case 'Sweeping': //Shadow Slicers, remove hex target for now and rely on just lines and targeting tooltip I think.
					targetType = 'hexClear'; //Adding hexes for Sweeping weapons created a bit too much clutter, replace with clear hex.
					targetType = 'hexPurple'; //Default for slicers
					if(weapon.weaponClass == "Gravitic") targetType = 'hexGreen'; //But now other weapon types use sweeping.			        
				break;		*/	
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
			}/*else if (lineIcon.shooterId === ship.id) { //When would we ever need to destroy origin lines, only target can move...?
			    if (lineIcon.lineSprite.isVisible) wasVisibleShooter = true;
			    this.scene.remove(lineIcon.lineSprite.mesh);
			    lineIcon.lineSprite.destroy();
			    return false;
			}*/else{		    
		    	return true; // Keep the lineIcon if the condition isn't met
			}
		});

		//Now recreate line using usual method.
        var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');			
		allBallistics.forEach(function (ballistic) {
//			if (ship.id === ballistic.targetid || ship.id === ballistic.shooterid) {
			if (ship.id === ballistic.targetid) {				
				createOrUpdateBallisticLines.call(this, ballistic, iconContainer, gamedata.turn);
			}
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
            }/*else if(lineIcon.shooterId === ship.id) {
	            if(!wasVisibleShooter){
	            	lineIcon.lineSprite.hide();
	            	lineIcon.lineSprite.isVisible = false;	 	            
            	}else{
	            	lineIcon.lineSprite.show();
	            	lineIcon.lineSprite.isVisible = true;	            		
				}
			}	*/
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

	//Called during movement phase to recreate line after a target ship moves.
    function updateBallisticLineIcon(lineIcon, ballistic, iconContainer, turn) {
        lineIcon.used = true;

		if(ballistic.targetid === -1) return; //No need to update further for AoE ballistics (they don't move) 

		var wasVisible = false; //Variable to track if destroyed lines were visible. If one was, they all were.

		// Destroy lines where the ship is either the target or the shooter.  Ship being checked should only ever be one or the other.
		if (lineIcon.lineSprite.isVisible) wasVisible = true;
		lineIcon.lineSprite.destroy();
		this.scene.remove(lineIcon.lineSprite.mesh);

		//Now recreate them using usual method.
		if(ballistic.notes != 'PersistentEffect') createBallisticLineIcon.call(this, ballistic, iconContainer, gamedata.turn, this.scene);

		//Check if lines were visible and if so continue to show.
		if(!wasVisible){
			lineIcon.lineSprite.hide();
			lineIcon.lineSprite.isVisible = false;	 	            
		}else{
			lineIcon.lineSprite.show();
			lineIcon.lineSprite.isVisible = true;	            		
		}
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
					type = 'purple'; //Default for slicers
					if(weapon.weaponClass == "Gravitic"){
						type = 'green'; //But now other weapon types use sweeping.
					}else if(weapon.weaponClass == "Psychic"){ //Thirdspace Psionic Concentrator
						type = 'red';
					}else if(weapon.weaponClass == "Molecular" && !(weapon instanceof MolecularSlicerBeamL)){ //Shadow Multiphased Cutters,, leave slicers as purple.
						type = 'blue';
					}else if(weapon.weaponClass == "Particle"){ //Mindrider Telekinetic Cutter
						type = 'orange';
					}else if(weapon.weaponClass == "Electromagnetic"){ //Vorlon Discharge Gunsb
						type = 'yellow';
					}														        
				break;					
			}		 
		}
			
	        var lineSprite = null;	        
	    	var isFriendly = gamedata.isMyOrTeamOneShip(shooter);

	        this.ballisticLineIcons.push({
	            id: ballistic.id,
	            shooterId: ballistic.shooterid,
	            targetId: ballistic.targetid,
	            lineSprite: lineSprite =  new BallisticLineSprite(launchPosition, targetPosition, 3 * this.zoomScale, -3, getLineColorByType(type), 0.5),
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
            return "rgba(204, 51, 255)";
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