'use strict';

window.BallisticIconContainer = function () {

    function BallisticIconContainer(coordinateConverter, scene) {
        this.ballisticIcons = [];
        this.coordinateConverter = coordinateConverter;
        this.scene = scene;
        this.zoomScale = 1;
    }

    BallisticIconContainer.prototype.consumeGamedata = function (gamedata, iconContainer) {
        this.ballisticIcons.forEach(function (ballisticIcon) {
            ballisticIcon.used = false;
        });

        var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');

        allBallistics.forEach(function (ballistic) {
            createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn);
        }, this);

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

    function createOrUpdateBallistic(ballistic, iconContainer, turn) {
        var icon = getBallisticIcon.call(this, ballistic.id);
        if (icon && ballistic.notes != 'Persistent Effect') {//We want Persistent Effects to show up in initial Orders! - DK 09.24
            updateBallisticIcon.call(this, icon, ballistic, iconContainer, turn);
        } else {
            createBallisticIcon.call(this, ballistic, iconContainer, turn, this.scene);
        }
    }

    function updateBallisticIcon(icon, ballistic, iconContainer, turn) {
        icon.used = true;
    }

    function createBallisticIcon(ballistic, iconContainer, turn, scene) {
        var shooterIcon = iconContainer.getById(ballistic.shooterid);	
		var targetType = 'hexRed';
        var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
        var text = "";
        var textColour = "";
		var iconImage = null; //Additional variable that can pass images to new BallisticSprite()!        

		if (ballistic.type == 'normal') { //it's direct fire after all!
		    launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getLastMovement().position);
			switch (ballistic.damageclass) {
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
		}else if (ballistic.targetid == -1){ //Maybe its nice to have other colours for certain types of hex targetted weapons?
			switch (ballistic.damageclass) {
				case 'BallisticMine': //KL Proximity Laser
				        targetType = 'hexYellow';
				        text = "Ballistic Mine";
				        textColour = "#ffff00";		        
				break;	
				case 'IonField': //Cascor Ion Field
				        targetType = 'hexPurple';
				        text = "Ion Field";
				        textColour = "#bc3782";				        
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
				default:
				        targetType = 'hexRed';
				break;

	        }				
		}  
		
		if (ballistic.damageclass == 'support') { //30 June 2024 - DK - Added for Ally targeting.
			targetType = 'hexGreen';
			iconImage = "./img/allySupport.png";				
		} 
				
		//We want Persistent Effects to have a separate colour from normal ballistics! DK 09.24
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
            if(ballistic.damageclass == 'Thoughtwave') targetPosition = launchPosition;//Don't create target hex for Thougtwave
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