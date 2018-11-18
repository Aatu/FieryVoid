"use strict";

window.EWIconContainer = (function() {
  var COLOR_OEW_FRIENDLY = new THREE.Color(160 / 255, 250 / 255, 100 / 255);
  var COLOR_OEW_ENEMY = new THREE.Color(255 / 255, 40 / 255, 40 / 255);
  var COLOR_OEW_DIST = new THREE.Color(255 / 255, 157 / 255, 0 / 255);
  var COLOR_SDEW = new THREE.Color(109 / 255, 189 / 255, 255 / 255);
  var COLOR_OEW_SOEW = new THREE.Color(1, 1, 1);

  function EWIconContainer(coordinateConverter, scene, iconContainer) {
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
      gamedata.ships.forEach(function(target) {
        createOrUpdateOEW.call(
          this,
          ship,
          target,
          ew.getOffensiveEW(ship, target)
        );
        createOrUpdateOEW.call(
          this,
          ship,
          target,
          ew.getOffensiveEW(ship, target, "DIST"),
          "DIST"
        );
        createOrUpdateOEW.call(
          this,
          ship,
          target,
          ew.getOffensiveEW(ship, target, "SDEW"),
          "SDEW"
        );
        createOrUpdateOEW.call(
          this,
          ship,
          target,
          ew.getOffensiveEW(ship, target, "SOEW"),
          "SOEW"
        );
      }, this);
    }, this);

    this.ewIcons = this.ewIcons.filter(function(icon) {
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

    gamedata.ships.forEach(function(target) {
      createOrUpdateOEW.call(
        this,
        ship,
        target,
        ew.getOffensiveEW(ship, target)
      );
      createOrUpdateOEW.call(
        this,
        ship,
        target,
        ew.getOffensiveEW(ship, target, "DIST"),
        "DIST"
      );
      createOrUpdateOEW.call(
        this,
        ship,
        target,
        ew.getOffensiveEW(ship, target, "SDEW"),
        "SDEW"
      );
      createOrUpdateOEW.call(
        this,
        ship,
        target,
        ew.getOffensiveEW(ship, target, "SOEW"),
        "SOEW"
      );
    }, this);

    this.ewIcons = this.ewIcons.filter(function(icon) {
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
    });
  };

  EWIconContainer.prototype.showForShip = function(ship) {
    this.ewIcons
      .filter(function(icon) {
        return icon.shipId === ship.id || icon.targetId === ship.id;
      })
      .forEach(function(icon) {
        icon.sprite.update(
          { ...icon.shipIcon.getPosition(), z: 1 },
          { ...icon.targetIcon.getPosition(), z: 1 }
        );
        icon.sprite.show();
      }, this);
  };

  EWIconContainer.prototype.showByShip = function(ship) {
    this.ewIcons
      .filter(function(icon) {
        return icon.shipId === ship.id;
      })
      .forEach(function(icon) {
        icon.sprite.update(
          { ...icon.shipIcon.getPosition(), z: 1 },
          { ...icon.targetIcon.getPosition(), z: 1 }
        );
        icon.sprite.show();
      }, this);
  };

  EWIconContainer.prototype.onEvent = function(name, payload) {
    var target = this["on" + name];
    if (target && typeof target == "function") {
      target.call(this, payload);
    }
  };

  EWIconContainer.prototype.onZoomEvent = function(payload) {
    var zoom = payload.zoom;
    if (zoom <= 0.5) {
      this.zoomScale = 2 * zoom;
      this.ewIcons.forEach(function(icon) {
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

    var icon = getOEWIcon.call(this, ship, target, type);
    if (icon) {
      updateOEWIcon.call(this, icon, ship, target, amount, type);
    } else {
      this.ewIcons.push(
        createOEWIcon.call(this, ship, target, amount, this.scene, type)
      );
    }
  }

  function updateOEWIcon(icon, ship, target, amount) {
    var shipIcon = this.shipIconContainer.getByShip(ship);
    var targetIcon = this.shipIconContainer.getByShip(target);

    icon.sprite.setLineWidth(getOEWLineWidth.call(this, amount));
    icon.sprite.update(
      { ...shipIcon.getPosition(), z: 1 },
      { ...targetIcon.getPosition(), z: 1 }
    );
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
      sprite: new window.LineSprite(
        { ...shipIcon.getPosition(), z: 1 },
        { ...targetIcon.getPosition(), z: 1 },
        getOEWLineWidth.call(this, amount),
        getColor(ship, type),
        0.5
      ),
      used: true
    };

    OEWIcon.sprite.hide();
    scene.add(OEWIcon.sprite.mesh);

    return OEWIcon;
  }

  function getColor(ship, type) {
    switch (type) {
      case "OEW":
        return gamedata.isMyOrTeamOneShip(ship)
          ? COLOR_OEW_FRIENDLY
          : COLOR_OEW_ENEMY;
      case "DIST":
        return COLOR_OEW_DIST;
      case "SDEW":
        return COLOR_SDEW;
      case "SOEW":
        return COLOR_OEW_SOEW;
    }
  }

  function getOEWLineWidth(amount) {
    return this.zoomScale * amount;
  }

  function getOEWIcon(ship, target, type) {
    type = type || "OEW";

    return this.ewIcons.find(function(icon) {
      return (
        icon.type === type &&
        icon.shipId === ship.id &&
        icon.targetId === target.id
      );
    });
  }

  return EWIconContainer;
})();
