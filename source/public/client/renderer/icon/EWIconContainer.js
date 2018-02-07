window.EWIconContainer = (function(){

    var COLOR_OEW = new THREE.Color(1,1,1);

    function EWIconContainer(coordinateConverter, scene){
        this.ewIcons = [];
        this.scene = scene;
        this.zoomScale = 1;
    }

    EWIconContainer.prototype.consumeGamedata = function(gamedata, iconContainer) {

        this.ewIcons.forEach(function(ewIcon) {
            ewIcon.used = false;
        });

        gamedata.ships.forEach(function(ship) {
           gamedata.ships.forEach(function(target){
               var oew = ew.getOffensiveEW(ship, target);

               if (oew) {
                   createOrUpdateOEW.call(this, ship, target, oew, iconContainer);
               }
           }, this)
        }, this);

        this.ewIcons = this.ewIcons.filter(function (icon) {
            if (!icon.used) {
                this.scene.remove(icon.sprite.mesh);
                return false;
            }

            return true;
        }, this);
    };

    EWIconContainer.prototype.hide = function() {
        this.ewIcons.forEach(function(icon) {
            icon.sprite.hide();
        })
    };

    EWIconContainer.prototype.showForShip = function(ship) {
        this.ewIcons.filter(function(icon) {
            return icon.shipId == ship.id || icon.targetId == ship.id;
        }).forEach(function(icon) {
            icon.sprite.show();
        });
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

    function createOrUpdateOEW (ship, target, amount, iconContainer) {
        var icon = getOEWIcon.call(this, ship, target);
        if (icon) {
            updateOEWIcon.call(this, icon, ship, target, amount, iconContainer);
        } else {
            this.ewIcons.push(createOEWIcon.call(this, ship, target, amount, iconContainer, this.scene));
        }
    }

    function updateOEWIcon(icon, ship, target, amount, iconContainer) {
        var shipIcon = iconContainer.getByShip(ship);
        var targetIcon = iconContainer.getByShip(target);

        icon.sprite.setLineWidth(getOEWLineWidth.call(this, amount));
        icon.sprite.setStartAndEnd(shipIcon.getPosition(), targetIcon.getPosition());
        icon.amount = amount;
        icon.used = true;

    }

    function createOEWIcon(ship, target, amount, iconContainer, scene) {
        var shipIcon = iconContainer.getByShip(ship);
        var targetIcon = iconContainer.getByShip(target);
        var OEWIcon = {
            type:"OEW",
            shipId: ship.id,
            targetId: target.id,
            amount: amount,
            sprite: new LineSprite(shipIcon.getPosition(), targetIcon.getPosition(), getOEWLineWidth.call(this, amount), -3, COLOR_OEW, 0.5),
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
        return this.ewIcons.filter(function(icon) {
            return icon.type == "OEW" && icon.shipId == ship.id && icon.targetId == target.id;
        }).pop();
    }

    return EWIconContainer;
})();