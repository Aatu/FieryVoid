'use strict';

window.EWIconContainer = function () {

    var COLOR_OEW_FRIENDLY = new THREE.Color(160 / 255, 250 / 255, 100 / 255).convertSRGBToLinear();
    var COLOR_OEW_ENEMY = new THREE.Color(255 / 255, 40 / 255, 40 / 255).convertSRGBToLinear();
    var COLOR_OEW_DIST = new THREE.Color(255 / 255, 157 / 255, 0 / 255).convertSRGBToLinear();
    var COLOR_SDEW = new THREE.Color(109 / 255, 189 / 255, 255 / 255).convertSRGBToLinear();
    var COLOR_OEW_SOEW = new THREE.Color(1, 1, 1).convertSRGBToLinear();
    var COLOR_JAM = new THREE.Color(210 / 255, 80 / 255, 255 / 255).convertSRGBToLinear(); //Hunter-Killer Jamming

    function EWIconContainer(coordinateConverter, scene, iconContainer) {
        this.ewIcons = [];
        this.scene = scene;
        this.zoomScale = 1;
        this.shipIconContainer = iconContainer;
    }

    EWIconContainer.prototype.consumeGamedata = function (gamedata) {
        this.ewIcons.forEach(function (ewIcon) {
            ewIcon.used = false;
        });

        gamedata.ships.forEach(function (ship) {
            gamedata.ships.forEach(function (target) {

                createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target));
                createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "DIST"), "DIST");
                createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "SDEW"), "SDEW");
                createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "SOEW"), "SOEW");
                createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "JAM"), "JAM");

            }, this);
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

    EWIconContainer.prototype.updateForShip = function (ship) {

        var length = this.ewIcons.length;

        this.ewIcons.forEach(function (ewIcon) {
            if (ewIcon.shipId === ship.id) {
                ewIcon.used = false;
            }
        });

        gamedata.ships.forEach(function (target) {
            createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target));
            createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "DIST"), "DIST");
            createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "SDEW"), "SDEW");
            createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "SOEW"), "SOEW");
            createOrUpdateOEW.call(this, ship, target, ew.getOffensiveEW(ship, target, "JAM"), "JAM");
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

    EWIconContainer.prototype.hide = function () {
        this.ewIcons.forEach(function (icon) {
            icon.sprite.hide();
        });
    };

    EWIconContainer.prototype.showForShip = function (ship) {
        this.ewIcons.filter(function (icon) {
            return icon.shipId === ship.id || icon.targetId === ship.id;
        }).forEach(function (icon) {
            icon.sprite.setStartAndEnd(icon.shipIcon.getPosition(), icon.targetIcon.getPosition());
            icon.sprite.show();
        }, this);
    };

    EWIconContainer.prototype.showByShip = function (ship) {
        this.ewIcons.filter(function (icon) {
            return icon.shipId === ship.id;
        }).forEach(function (icon) {
            icon.sprite.setStartAndEnd(icon.shipIcon.getPosition(), icon.targetIcon.getPosition());
            icon.sprite.show();
        }, this);
    };

    //Ship-window EW strip hover (SHIPWINDOW_REDESIGN_PLAN.md Stage 1e): emphasise the
    //line sprite between shooter and hovered target - wider line, full opacity - and
    //restore it (including its previous visibility) on mouse-out. A hidden target has
    //no sprite at all (createOrUpdateOEW skips it), so this silently no-ops and never
    //leaks a stealthed/undeployed ship's position.
    EWIconContainer.prototype.highlightForTarget = function (shipId, targetId, type, active) {
        this.ewIcons.forEach(function (icon) {
            if (icon.shipId !== shipId || icon.targetId !== targetId) return;
            if (type && icon.type !== type) return;

            if (active) {
                if (icon.highlighted) return;
                icon.highlighted = true;
                icon.preHighlightVisible = icon.sprite.mesh.visible;
                icon.sprite.setStartAndEnd(icon.shipIcon.getPosition(), icon.targetIcon.getPosition());
                icon.sprite.setLineWidth(Math.max(getOEWLineWidth.call(this, icon.amount) * 2, 2));
                icon.sprite.multiplyOpacity(2);
                icon.sprite.show();
            } else {
                if (!icon.highlighted) return;
                icon.highlighted = false;
                icon.sprite.setLineWidth(getOEWLineWidth.call(this, icon.amount));
                icon.sprite.multiplyOpacity(1);
                if (!icon.preHighlightVisible) {
                    icon.sprite.hide();
                }
            }
        }, this);
    };

    EWIconContainer.prototype.onEwTargetHighlight = function (payload) {
        this.highlightForTarget(payload.shipId, payload.targetId, payload.type, payload.active);
    };

    EWIconContainer.prototype.onEvent = function (name, payload) {
        var target = this['on' + name];
        if (target && typeof target == 'function') {
            target.call(this, payload);
        }
    };

    EWIconContainer.prototype.onZoomEvent = function (payload) {
        var zoom = payload.zoom;
        if (zoom <= 0.5) {
            this.zoomScale = 2 * zoom;
            this.ewIcons.forEach(function (icon) {
                icon.sprite.setLineWidth(getOEWLineWidth.call(this, icon.amount));
            }, this);
        } else {
            this.zoomScale = 1;
        }
    };

    function createOrUpdateOEW(ship, target, amount, type) {
        if (amount === 0) {
            return;
        }

        if ((shipManager.shouldBeHidden(ship)) || (shipManager.shouldBeHidden(target))) return;

        var icon = getOEWIcon.call(this, ship, target, type);
        if (icon) {
            updateOEWIcon.call(this, icon, ship, target, amount, type);
        } else {
            this.ewIcons.push(createOEWIcon.call(this, ship, target, amount, this.scene, type));
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

    function createOEWIcon(ship, target, amount, scene, type) {

        type = type || "OEW";

        var shipIcon = this.shipIconContainer.getByShip(ship);
        var targetIcon = this.shipIconContainer.getByShip(target);

        var OEWIcon = {
            type: type,
            shipId: ship.id,
            targetId: target.id,
            amount: amount,
            shipIcon: shipIcon,
            targetIcon: targetIcon,
            sprite: new LineSprite(shipIcon.getPosition(), targetIcon.getPosition(), getOEWLineWidth.call(this, amount), -5, getColor(ship, type), 0.5),
            used: true
        };

        OEWIcon.sprite.hide();
        scene.add(OEWIcon.sprite.mesh);

        return OEWIcon;
    }

    function getColor(ship, type) {
        switch (type) {
            case "OEW":
                return gamedata.isMyOrTeamOneShip(ship) ? COLOR_OEW_FRIENDLY : COLOR_OEW_ENEMY;
            case "DIST":
                return COLOR_OEW_DIST;
            case "SDEW":
                return COLOR_SDEW;
            case "SOEW":
                return COLOR_OEW_SOEW;
            case "JAM":
                return COLOR_JAM;
        }
    }

    function getOEWLineWidth(amount) {
        return this.zoomScale * amount;
    }

    function getOEWIcon(ship, target, type) {
        type = type || "OEW";

        return this.ewIcons.find(function (icon) {
            return icon.type === type && icon.shipId === ship.id && icon.targetId === target.id;
        });
    }

    return EWIconContainer;
}();