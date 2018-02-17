window.EWIconContainer = (function(){

    var COLOR_OEW_FIRENDLY = new THREE.Color(160/255,250/255,100/255);
    var COLOR_OEW_ENEMY = new THREE.Color(255/255,40/255,40/255);


    function EWIconContainer(coordinateConverter, scene, iconContainer){
        this.ewIcons = [];
        this.scene = scene;
        this.zoomScale = 1;
        this.shipIconContainer = iconContainer;
    }

    EWIconContainer.prototype.consumeGamedata = function(gamedata) {
        this.ewIcons.forEach(function(ewIcon) {
            ewIcon.used = false;
        });

        gamedata.ships.forEach(function(ship) {
           gamedata.ships.forEach(function(target){
               var oew = ew.getOffensiveEW(ship, target);

               if (oew) {
                   createOrUpdateOEW.call(this, ship, target, oew);
               }
           }, this)
        }, this);



        this.ewIcons = this.ewIcons.filter(function (icon) {
            if (!icon.used) {
                this.scene.remove(icon.sprite.mesh);
                icon.sprite.destroy();
                return false;
            }

            return true;
        }, this);

    };

    EWIconContainer.prototype.updateForShip = function(ship) {

        var length = this.ewIcons.length;

        this.ewIcons.forEach(function(ewIcon) {
            if (ewIcon.shipId === ship.id) {
                ewIcon.used = false;
            }
        });


        gamedata.ships.forEach(function(target){
            var oew = ew.getOffensiveEW(ship, target);

            if (oew) {
                createOrUpdateOEW.call(this, ship, target, oew);
            }
        }, this);

        this.ewIcons = this.ewIcons.filter(function (icon) {
            if (!icon.used && icon.shipId === ship.id) {
                this.scene.remove(icon.sprite.mesh);
                icon.sprite.destroy();
                return false;
            }

            return true;
        }, this);

        if (this.ewIcons.length > length) {
            this.showForShip(ship);
        }

    };

    EWIconContainer.prototype.hide = function() {
        this.ewIcons.forEach(function(icon) {
            icon.sprite.hide();
        })
    };

    EWIconContainer.prototype.showForShip = function(ship) {
        this.ewIcons.filter(function(icon) {
            return icon.shipId === ship.id || icon.targetId === ship.id;
        }).forEach(function(icon) {
            icon.sprite.setStartAndEnd(icon.shipIcon.getPosition(), icon.targetIcon.getPosition());
            icon.sprite.show();
        }, this);
    };

    EWIconContainer.prototype.onEvent = function(name, payload) {
        var target = this['on'+ name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    EWIconContainer.prototype.onZoomEvent = function (payload) {
        var zoom = payload.zoom;
        if (zoom <= 0.5) {
            this.zoomScale = 2 * zoom;
            this.ewIcons.forEach(function(icon){
                icon.sprite.setLineWidth(getOEWLineWidth.call(this, icon.amount));
            }, this);
        }else{
            this.zoomScale = 1;
        }

    };

    function createOrUpdateOEW (ship, target, amount) {
        var icon = getOEWIcon.call(this, ship, target);
        if (icon) {
            updateOEWIcon.call(this, icon, ship, target, amount);
        } else {
            this.ewIcons.push(createOEWIcon.call(this, ship, target, amount, this.scene));
        }
    }

    function updateOEWIcon(icon, ship, target, amount) {
        var shipIcon = this.shipIconContainer.getByShip(ship);
        var targetIcon = this.shipIconContainer.getByShip(target);

        icon.sprite.setLineWidth(getOEWLineWidth.call(this, amount));
        icon.sprite.setStartAndEnd(shipIcon.getPosition(), targetIcon.getPosition());
        icon.shipIcon = shipIcon;
        icon.targetIcon = targetIcon;
        icon.amount = amount;
        icon.used = true;
    }

    function createOEWIcon(ship, target, amount, scene) {
        var shipIcon = this.shipIconContainer.getByShip(ship);
        var targetIcon = this.shipIconContainer.getByShip(target);

        var color = gamedata.isMyShip(ship) ? COLOR_OEW_FIRENDLY : COLOR_OEW_ENEMY;
        var OEWIcon = {
            type:"OEW",
            shipId: ship.id,
            targetId: target.id,
            amount: amount,
            shipIcon: shipIcon,
            targetIcon: targetIcon,
            sprite: new LineSprite(shipIcon.getPosition(), targetIcon.getPosition(), getOEWLineWidth.call(this, amount), -3, color, 0.5),
            used: true
        };

        OEWIcon.sprite.hide();
        scene.add(OEWIcon.sprite.mesh);

        return OEWIcon;
    }

    function getOEWLineWidth(amount) {
        return this.zoomScale * amount;
    }

    function getOEWIcon(ship, target) {
        return this.ewIcons.find(function(icon) {
            return icon.type === "OEW" && icon.shipId === ship.id && icon.targetId === target.id;
        });
    }

    return EWIconContainer;
})();