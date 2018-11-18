import {
  MovementPath,
  MovementPathDeployment,
  MovementPathMoved
} from "../handler/Movement";

const COLOR_MINE = new THREE.Color(160 / 255, 250 / 255, 100 / 255);
const COLOR_ENEMY = new THREE.Color(255 / 255, 40 / 255, 40 / 255);

class ShipObject {
  constructor(ship, scene) {
    this.shipId = ship.id;
    this.ship = ship;
    this.mine = gamedata.isMyOrTeamOneShip(ship);

    this.scene = scene;
    this.mesh = new THREE.Object3D();
    this.shipObject = null;
    this.weaponArcs = [];
    this.shipSideSprite = null;
    this.shipEWSprite = null;
    this.line = null;

    this.defaultHeight = 50;
    this.sideSpriteSize = 100;
    this.position = { x: 0, y: 0, z: 0 };
    this.movementPath = null;

    this.movements = null;

    this.hidden = false;

    this.startRotation = { x: 0, y: 0, z: 0 };
    this.rotation = { x: 0, y: 0, z: 0 };

    this.consumeShipdata(this.ship);
  }

  consumeShipdata(ship) {
    this.ship = ship;
    this.consumeMovement(ship.movement);
    this.consumeEW(ship);
  }

  createMesh() {
    if (this.position.z === 0) {
      this.position.z = this.defaultHeight;
    }

    const opacity = 0.5;
    this.line = new window.LineSprite(
      { x: 0, y: 0, z: 1 },
      { x: 0, y: 0, z: this.position.z },
      1,
      this.mine ? COLOR_MINE : COLOR_ENEMY,
      opacity
    );
    this.mesh.add(this.line.mesh);

    this.shipSideSprite = new window.ShipSelectedSprite(
      { width: this.sideSpriteSize, height: this.sideSpriteSize },
      0.01,
      opacity
    );
    this.shipSideSprite.setOverlayColor(this.mine ? COLOR_MINE : COLOR_ENEMY);
    this.shipSideSprite.setOverlayColorAlpha(1);
    this.mesh.add(this.shipSideSprite.mesh);

    this.shipEWSprite = new window.ShipEWSprite(
      { width: this.sideSpriteSize, height: this.sideSpriteSize },
      0.01
    );
    this.mesh.add(this.shipEWSprite.mesh);
    this.shipEWSprite.hide();

    this.mesh.name = "ship";
    this.mesh.userData = { icon: this };
    this.scene.add(this.mesh);
    this.consumeEW(this.ship);
  }

  create() {
    this.createMesh();
  }

  setPosition(x, y, z = this.defaultHeight) {
    if (typeof x === "object") {
      z = x.z || this.defaultHeight;
      y = x.y;
      x = x.x;
    }

    this.position = { x, y, z };

    if (this.mesh) {
      this.mesh.position.set(x, y, 0);
    }

    if (this.shipObject) {
      this.shipObject.position.set(0, 0, z);
    }
  }

  getPosition() {
    return this.position;
  }

  setRotation(x, y, z) {
    this.rotation = { x, y, z };

    if (this.shipObject) {
      this.shipObject.rotation.set(
        mathlib.degreeToRadian(x + this.startRotation.x),
        mathlib.degreeToRadian(y + this.startRotation.y),
        mathlib.degreeToRadian(z + this.startRotation.z)
      );
    }
  }

  getRotation(x, y, z) {
    return this.rotation;
  }

  setOpacity(opacity) {}

  hide() {
    if (this.hidden) {
      return;
    }

    this.scene.remove(this.mesh);
    this.hidden = true;
  }

  show() {
    if (!this.hidden) {
      return;
    }

    this.scene.add(this.mesh);
    this.hidden = false;
  }

  getFacing() {
    return this.getRotation().y;
  }

  setFacing(facing) {
    this.setRotation(0, facing, 0);
  }

  setOverlayColorAlpha(alpha) {}

  getMovements(turn) {
    return this.movements.filter(function(movement) {
      return turn === undefined || movement.turn === turn;
    }, this);
  }

  setScale(width, height) {
    //console.log("ShipObject.setScale is not yet implemented")
    //console.trace();
  }

  consumeEW(ship) {
    if (!this.shipEWSprite) {
      return;
    }

    let dew = ew.getDefensiveEW(ship);
    if (ship.flight) {
      dew = shipManager.movement.getJinking(ship);
    }

    const ccew = ew.getCCEW(ship);

    this.shipEWSprite.update(dew, ccew);
  }

  showEW() {
    if (this.shipEWSprite) {
      this.shipEWSprite.show();
    }
  }

  hideEW() {
    if (this.shipEWSprite) {
      this.shipEWSprite.hide();
    }
  }

  showSideSprite(value) {
    //console.log("ShipObject.showSideSprite is not yet implemented")
  }

  setHighlighted(value) {
    //console.log("ShipObject.showSideSprite is not yet implemented")
  }

  setSelected(value) {
    if (value) {
      this.shipSideSprite.setOverlayColor(new THREE.Color(1, 1, 1));
      this.shipSideSprite.setOverlayColorAlpha(1);
    } else {
      this.shipSideSprite.setOverlayColor(this.mine ? COLOR_MINE : COLOR_ENEMY);
      this.shipSideSprite.setOverlayColorAlpha(0.8);
    }
  }

  setNotMoved(value) {
    //console.log("ShipObject.showSideSprite is not yet implemented")
  }

  consumeMovement(movements) {
    this.movements = movements.filter(function(move) {
      return !move.isEvade();
    });
  }

  showWeaponArc(ship, weapon) {
    var hexDistance = window.coordinateConverter.getHexDistance();
    var dis =
      weapon.rangePenalty === 0
        ? hexDistance * weapon.range
        : (50 / weapon.rangePenalty) * hexDistance;
    var arcs = shipManager.systems.getArcs(ship, weapon);

    var arcLenght =
      arcs.start === arcs.end
        ? 360
        : mathlib.getArcLength(arcs.start, arcs.end);
    var arcStart = mathlib.addToDirection(0, arcLenght * -0.5);
    var arcFacing = mathlib.addToDirection(arcs.end, arcLenght * -0.5);

    var geometry = new THREE.CircleGeometry(
      dis,
      32,
      mathlib.degreeToRadian(arcStart),
      mathlib.degreeToRadian(arcLenght)
    );
    var material = new THREE.MeshBasicMaterial({
      color: new THREE.Color("rgb(20,80,128)"),
      opacity: 0.5,
      transparent: true
    });
    var circle = new THREE.Mesh(geometry, material);
    circle.rotation.z = mathlib.degreeToRadian(
      -mathlib.addToDirection(arcFacing, -this.getFacing())
    );
    circle.position.z = -1;
    this.mesh.add(circle);
    this.weaponArcs.push(circle);

    return null;
  }

  hideWeaponArcs() {
    this.weaponArcs.forEach(function(arc) {
      this.mesh.remove(arc);
    }, this);
  }

  showBDEW() {
    var BDEW = ew.getBDEW(this.ship);
    if (!BDEW || this.BDEWSprite) {
      return;
    }

    var hexDistance = window.coordinateConverter.getHexDistance();
    var dis = 20 * hexDistance;

    var color = gamedata.isMyShip(this.ship)
      ? new THREE.Color(160 / 255, 250 / 255, 100 / 255)
      : new THREE.Color(255 / 255, 157 / 255, 0 / 255);

    var geometry = new THREE.CircleGeometry(dis, 64, 0);
    var material = new THREE.MeshBasicMaterial({
      color: color,
      opacity: 0.2,
      transparent: true
    });
    var circle = new THREE.Mesh(geometry, material);
    circle.position.z = -1;
    this.mesh.add(circle);
    this.BDEWSprite = circle;

    return null;
  }

  hideBDEW() {
    this.mesh.remove(this.BDEWSprite);
    this.BDEWSprite = null;
  }

  positionAndFaceIcon(offset, movementService) {
    var movement = movementService.getMostRecentMove(this.ship);
    var gamePosition = window.coordinateConverter.fromHexToGame(
      movement.position
    );

    if (offset) {
      gamePosition.x += offset.x;
      gamePosition.y += offset.y;
    }

    var facing = mathlib.hexFacingToAngle(movement.facing);

    this.setPosition(gamePosition);
    this.setFacing(-facing);
  }

  hideMovementPath(ship) {
    if (this.movementPath) {
      this.movementPath.remove(this.scene);
      this.movementPath = null;
    }
  }

  showMovementPath(ship, movementService) {
    this.hideMovementPath(ship);
    this.movementPath = new MovementPath(ship, movementService, this.scene);
  }
}

window.ShipObject = ShipObject;

export default ShipObject;
