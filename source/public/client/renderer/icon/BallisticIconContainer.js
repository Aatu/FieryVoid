window.BallisticIconContainer = (function(){

    function BallisticIconContainer(coordinateConverter, scene){
        this.ballisticIcons = [];
        this.coordinateConverter = coordinateConverter;
        this.scene = scene;
        this.zoomScale = 1;
    }

    BallisticIconContainer.prototype.consumeGamedata = function(gamedata, iconContainer) {
        this.ballisticIcons.forEach(function(ballisticIcon) {
            ballisticIcon.used = false;
        });

        var allBallistics = weaponManager.getAllFireOrdersForAllShipsForTurn(gamedata.turn, 'ballistic');

        allBallistics.forEach(function(ballistic) {
            createOrUpdateBallistic.call(this, ballistic, iconContainer, gamedata.turn);
        }, this);

        this.ballisticIcons = this.ballisticIcons.filter(function (icon) {
            if (!icon.used) {
                this.scene.remove(icon.launchSprite.mesh);
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
        }, this)
    };

    BallisticIconContainer.prototype.hide = function() {
        this.ballisticIcons.forEach(function(icon) {
            icon.launchSprite.hide();
            if (icon.targetSprite){
                icon.targetSprite.hide();
            }
        });

        return this;
    };

    BallisticIconContainer.prototype.show = function() {
        this.ballisticIcons.forEach(function(icon) {
            icon.launchSprite.show();
            if (icon.targetSprite){
                icon.targetSprite.show();
            }
        });

        return this;
    };

    BallisticIconContainer.prototype.onEvent = function(name, payload) {
        var target = this['on'+ name];
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
        if (icon){
            updateBallisticIcon.call(this, icon, ballistic, iconContainer, turn)
        }else {
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

        if (ballistic.targetid === -1) {
            targetPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
        } else {
            targetIcon = iconContainer.getById(ballistic.targetid);
            targetPosition = {x: 0, y:0};
        }

        var launchSprite = new BallisticSprite(launchPosition, 'launch');
        var targetSprite = new BallisticSprite(targetPosition, 'hex');

        scene.add(launchSprite.mesh);
        if (targetIcon && targetSprite) {
            targetIcon.mesh.add(targetSprite.mesh);
        } else if (targetSprite)  {
            scene.add(targetSprite.mesh);
        }

        this.ballisticIcons.push({
            id: ballistic.id,
            shooterId: ballistic.shooterid,
            targetId: ballistic.targetid,
            position: new hexagon.Offset(ballistic.x, ballistic.y),
            launchSprite: launchSprite,
            targetSprite: targetSprite,
            used: true
        });
    }

    function getBallisticIcon(id) {
        return this.ballisticIcons.filter(function (icon) {
           return icon.id === id;
        }).pop();
    }

    return BallisticIconContainer;
})();