'use strict';

window.BallisticIconContainer = function () {

    function BallisticIconContainer(coordinateConverter, scene) {
        this.ballisticIcons = [];
        this.coordinateConverter = coordinateConverter;
        this.scene = scene;
        this.zoomScale = 1;
    }

    BallisticIconContainer.prototype.consumeGamedata = function (gamedata, iconContainer, replayData = null) {
        this.ballisticIcons.forEach(function (ballisticIcon) {
            ballisticIcon.used = false;
        });

		if(replayData){ //Pass true marker for Replay
			var allBallistics = replayData;				
	        allBallistics.forEach(function (ballistic) {
	            createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn, true);
	        }, this);					
		} else {
        	var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');			
	        allBallistics.forEach(function (ballistic) {
	            createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn);
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

    BallisticIconContainer.prototype.onEvent = function (name, payload) {
        var target = this['on' + name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    BallisticIconContainer.prototype.onZoomEvent = function (payload) {
        /* TODO: lines between launch and target
        var zoom = payload.zoom;
        if (zoom <= 0.5) {
            this.zoomScale = 2 * zoom;
            this.ewIcons.forEach(function(icon){
                icon.sprite.setLineWidth(getOEWLineWidth.call(this, icon.amount));
            }, this);
        }else{
            this.zoomScale = 1;
        }
        */
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
		if(replay) ballistic = ballistic.fireOrder; //Replay passes slightly different type of data, so adjust ballistic variable here.
			
        var shooterIcon = iconContainer.getById(ballistic.shooterid);	
        if(!shooterIcon) shooterIcon = iconContainer.getById(ballistic.shooter.id);
		var targetType = 'hexRed'; //Default red hex if none of the later conditions are true.
        var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
        var text = ""; //Additional variable that can pass text to new BallisticSprite()
        var textColour = ""; //Additional variable that chooses base text colour for new BallisticSprite()
		var iconImage = null; //Additional variable that can pass images to new BallisticSprite()!
		
		//New variables found to enhance Ballistic Icons further! - DK 10.24
		var shooter = shooterIcon.ship; //Get shooter info.
		var weapon = shooter.systems[ballistic.weaponid]; //Find weapon			
		var modeName = weapon.firingModes[ballistic.firingMode]; //Get actual Firing Mode name, so we can be more specific below!
		
		if (ballistic.type == 'normal') { //it's direct fire after all!
		    launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
			if(modeName){
				switch (modeName) {			
				case 'Shredder': //Vree Anti-Matter Shredder
				        targetType = 'hexBlue';
				        text = "Antimatter Shredder";
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
				case 'SecondSight': //Mindrider Second Sight
				        targetType = 'hexPurple';
				        text = "Second Sight";
				        textColour = "#7f00ff";
//				        iconImage = "./img/systemicons/SecondSightICON.png"; //Example image to pass			        
				break;
				default:
				        targetType = 'hexYellow';
				break;													        
				}				
			}
			//OLD CODE using damageclass, new method does not require.
/*			switch (ballistic.damageclass) {
				case 'antimatter':
				        targetType = 'hexBlue';
				break;
				case 'plasma':
				        targetType = 'hexGreen';
				        if(ballistic.firingMode == 2) text = '!';//For plasma webs, if this is being used for other weapons will need refined.
				        textColour = "#787800";				        	
				break;
				case 'SecondSight': //Mindrider Second Sight
				        targetType = 'hexPurple';
				        text = "Second Sight";
				        textColour = "#7f00ff";
//				        iconImage = "./img/systemicons/SecondSightICON.png"; //Example image to pass
				break;				        
				default:
				        targetType = 'hexYellow';
				break;

	        }
*/
		}else if (ballistic.targetid == -1){ //Maybe its nice to have other colours for certain types of hex targetted weapons?
/*              //OLD CODE using damageclass, new method does not require.
				case 'BallisticMine': //KL Proximity Laser
				        targetType = 'hexYellow';
				        text = "Ballistic Mine";
				        textColour = "#ffff00";		        
				break;	
				case 'IonField': //Cascor Ion Field
				        targetType = 'hexPurple';
				        text = "Ion Field";
				        textColour = "#7f00ff";				        
				break;
				case 'ProximityLaser': //KL Proximity Laser
				        targetType = 'hexRed';
				        text = "Proximity Laser";
				        textColour = "#e6140a";		        
				break;	
				case 'Thoughtwave': //Mindrider Thoughwave
				        targetType = 'hexPurple';
				        text = "Thoughtwave";
				        textColour = "#bc3782";
//				        iconImage = "./img/systemicons/ThoughtWaveICON.png";			        
				break;				
	        }*/
			
			if(modeName){
				switch (modeName) {			
				case 'Energy Mine': //Narn Energy Mine
				        targetType = 'hexRed';
				        text = "Energy Mine";
				        textColour = "#e6140a";				        
				break;				
				case 'IonStorm': //Cascor Ion Field
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
				        text = '!';
				        textColour = "#787800";				        	
				break;
				case 'Proximity Laser Launcher': //KL Proximity Laser
				        targetType = 'hexRed';
				        text = "Proximity Laser";
				        textColour = "#e6140a";		        
				break;
				case 'ThoughtWave': //Mindrider Thoughwave
				        targetType = 'hexPurple';
				        text = "Thought Wave";
				        textColour = "#bc3782"; //Actually a pink-purple, as it blends with luanch hex Orange!
//				        iconImage = "./img/systemicons/ThoughtWaveICON.png";  //Example image to pass	 		        
				break;													        
				}				
			}
		} 
		 
		//Ballistic Mines etc can have different ammo types, handle separately using damageClass/initializationUpdate method! - DK 10.24
		if (ballistic.damageclass == 'MultiModeHex') { 
			targetType = 'hexYellow';
			text = modeName;
			textColour = "#ffff00";					
		}
		
		//Generic Support icon for these type of weapons. 06.24 - DK			
		if (ballistic.damageclass == 'support') {
			targetType = 'hexGreen';
			iconImage = "./img/allySupport.png";				
		} 
/*				
		//OLD CODE using notes, new method does not require.
		if (ballistic.notes == 'Persistent Effect') { //30 June 2024 - DK - Added for Persistent Effects e.g. Plasma Web.
			switch (ballistic.damageclass) {
				case 'Persistent Effect Plasma':
				        targetType = 'hexGreen';
				        text = "!";
				        textColour = "#787800";				            		            
					break;
				default:
				        targetType = 'hexYellow';
					break;

	        }
		} 	
*/		  		
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

        if ((!getByLaunchPosition(launchPosition, this.ballisticIcons)) && ballistic.notes != 'Persistent Effect') { //Don't create launch sprite for persistant effects!
			launchSprite = new BallisticSprite(launchPosition, 'hexOrange');       
            scene.add(launchSprite.mesh);
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
    }

    const getByLaunchPosition = (position, icons) => icons.find(icon => icon.launchPosition.x === position.x && icon.launchPosition.y === position.y)

    const getByTargetIdOrTargetPosition = (position, targetId, icons) => icons.find(icon => position && ((icon.position.x === position.x && icon.position.y === position.y) || (targetId !== -1 && icon.targetId === targetId )) )

    function getBallisticIcon(id) {
        return this.ballisticIcons.filter(function (icon) {
            return icon.id === id;
        }).pop();
    }

    return BallisticIconContainer;
}();