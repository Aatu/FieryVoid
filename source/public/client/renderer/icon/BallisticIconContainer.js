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
        if (icon) {
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
        var launchPosition = this.coordinateConverter.fromHexToGame(shooterIcon.getFirstMovementOnTurn(turn).position);
        var targetPosition = null;
        var targetIcon = null;

        if (ballistic.targetid === -1 && ballistic.x !== "null" && ballistic.y !== "null") {
            targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
        } else if (ballistic.targetid && ballistic.targetid !== -1) {
            targetIcon = iconContainer.getById(ballistic.targetid);
            targetPosition = { x: 0, y: 0 };
        }

        var launchSprite = null;

        if (!getByLaunchPosition(launchPosition, this.ballisticIcons)) {
            launchSprite = new BallisticSprite(launchPosition, 'launch');
            scene.add(launchSprite.mesh);
        }

        var targetSprite = null;

        if (!getByTargetIdOrTargetPosition(targetPosition, ballistic.targetId, this.ballisticIcons)) {
            if (targetIcon && targetPosition) {
                targetSprite =  new BallisticSprite(targetPosition, 'hex');
                targetIcon.mesh.add(targetSprite.mesh);
            } else if (targetPosition) {
                targetSprite =  new BallisticSprite(targetPosition, 'hex');
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