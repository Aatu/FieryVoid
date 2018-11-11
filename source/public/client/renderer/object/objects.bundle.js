(function(){function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s}return e})()({1:[function(require,module,exports){
'use strict';

var _ships = require('./ships');

var _ships2 = _interopRequireDefault(_ships);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

console.log(_ships2.default);
window.shipObjects = _ships2.default;

},{"./ships":6}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _ShipObject2 = require('./ShipObject');

var _ShipObject3 = _interopRequireDefault(_ShipObject2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Capital = function (_ShipObject) {
    _inherits(Capital, _ShipObject);

    function Capital(ship, scene) {
        _classCallCheck(this, Capital);

        var _this = _possibleConstructorReturn(this, (Capital.__proto__ || Object.getPrototypeOf(Capital)).call(this, ship, scene));

        _this.defaultHeight = 35;
        _this.sideSpriteSize = 90;
        _this.create();
        return _this;
    }

    _createClass(Capital, [{
        key: 'create',
        value: function create() {
            var _this2 = this;

            _get(Capital.prototype.__proto__ || Object.getPrototypeOf(Capital.prototype), 'create', this).call(this);

            window.Loader.loadObject("img/3d/capital/capital.obj", function (object) {

                window.Loader.loadTexturesAndAssign(object.children[0], { normalScale: new THREE.Vector2(0.5, 0.5), shininess: 10, color: new THREE.Color(1, 1, 1) }, 'img/3d/capital/diffuse.png', 'img/3d/capital/normalEdit.png');

                object.scale.set(3, 3, 3);
                _this2.startRotation = { x: 90, y: 90, z: 0 };
                _this2.shipObject = object;
                _this2.setRotation(_this2.rotation.x, _this2.rotation.y, _this2.rotation.z);
                _this2.mesh.add(_this2.shipObject);
                object.position.set(0, 0, _this2.position.z);
            });
        }
    }]);

    return Capital;
}(_ShipObject3.default);

exports.default = Capital;

},{"./ShipObject":5}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _ShipObject2 = require('./ShipObject');

var _ShipObject3 = _interopRequireDefault(_ShipObject2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Gunship = function (_ShipObject) {
    _inherits(Gunship, _ShipObject);

    function Gunship(ship, scene) {
        _classCallCheck(this, Gunship);

        var _this = _possibleConstructorReturn(this, (Gunship.__proto__ || Object.getPrototypeOf(Gunship)).call(this, ship, scene));

        _this.defaultHeight = 30;
        _this.sideSpriteSize = 50;
        _this.create();
        return _this;
    }

    _createClass(Gunship, [{
        key: 'create',
        value: function create() {
            var _this2 = this;

            _get(Gunship.prototype.__proto__ || Object.getPrototypeOf(Gunship.prototype), 'create', this).call(this);

            window.Loader.loadObject("img/3d/gunship/gunship.obj", function (object) {

                window.Loader.loadTexturesAndAssign(object.children[0], {}, null, 'img/3d/gunship/normal.png');

                window.Loader.loadTexturesAndAssign(object.children[1], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[3], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[6], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[7], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[4], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');
                window.Loader.loadTexturesAndAssign(object.children[5], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

                object.scale.set(5, 5, 5);
                _this2.startRotation = { x: 90, y: 90, z: 0 };
                _this2.shipObject = object;
                _this2.setRotation(_this2.rotation.x, _this2.rotation.y, _this2.rotation.z);
                _this2.mesh.add(_this2.shipObject);
                object.position.set(0, 0, _this2.position.z);
            });
        }
    }]);

    return Gunship;
}(_ShipObject3.default);

exports.default = Gunship;

},{"./ShipObject":5}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _ShipObject2 = require('./ShipObject');

var _ShipObject3 = _interopRequireDefault(_ShipObject2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Rhino = function (_ShipObject) {
    _inherits(Rhino, _ShipObject);

    function Rhino(ship, scene) {
        _classCallCheck(this, Rhino);

        var _this = _possibleConstructorReturn(this, (Rhino.__proto__ || Object.getPrototypeOf(Rhino)).call(this, ship, scene));

        _this.sideSpriteSize = 30;
        _this.create();
        return _this;
    }

    _createClass(Rhino, [{
        key: 'create',
        value: function create() {
            var _this2 = this;

            _get(Rhino.prototype.__proto__ || Object.getPrototypeOf(Rhino.prototype), 'create', this).call(this);

            window.Loader.loadObject("img/3d/rhino/rhino.obj", function (object) {

                window.Loader.loadTexturesAndAssign(object.children[0], { normalScale: new THREE.Vector2(1, 1), shininess: 10, color: new THREE.Color(1, 1, 1) }, 'img/3d/rhino/texture.png', 'img/3d/rhino/sculptNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[1], {}, 'img/3d/diffuseDoc.png', 'img/3d/normalDoc.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

                object.scale.set(2, 2, 2);
                _this2.startRotation = { x: 90, y: 90, z: 0 };

                _this2.shipObject = object;
                _this2.setRotation(_this2.rotation.x, _this2.rotation.y, _this2.rotation.z);
                _this2.mesh.add(_this2.shipObject);
                object.position.set(0, 0, _this2.position.z);
            });
        }
    }]);

    return Rhino;
}(_ShipObject3.default);

exports.default = Rhino;

},{"./ShipObject":5}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ShipObject = function () {
    function ShipObject(ship, scene) {
        _classCallCheck(this, ShipObject);

        this.shipId = ship.id;
        this.ship = ship;
        this.scene = scene;
        this.mesh = new THREE.Object3D();
        this.shipObject = null;
        this.weaponArcs = [];
        this.shipSideSprite = null;
        this.line = null;

        this.defaultHeight = 50;
        this.mine = gamedata.isMyOrTeamOneShip(ship);
        this.sideSpriteSize = 100;
        this.position = { x: 0, y: 0, z: 0 };

        this.movements = null;

        this.hidden = false;

        this.startRotation = { x: 0, y: 0, z: 0 };
        this.rotation = { x: 0, y: 0, z: 0 };

        console.log("ship", this.ship);
        this.consumeShipdata(this.ship);
    }

    _createClass(ShipObject, [{
        key: "consumeShipdata",
        value: function consumeShipdata(ship) {
            this.ship = ship;
            this.consumeMovement(ship.movement);
            this.consumeEW(ship);
        }
    }, {
        key: "createMesh",
        value: function createMesh() {

            if (this.position.z === 0) {
                this.position.z = this.defaultHeight;
            }

            var opacity = 0.5;
            this.line = new window.LineSprite({ x: 0, y: 0, z: 1 }, { x: 0, y: 0, z: this.position.z }, 1, new THREE.Color(0, 1, 0), opacity);
            this.mesh.add(this.line.mesh);

            this.shipSideSprite = new window.ShipSelectedSprite({ width: this.sideSpriteSize, height: this.sideSpriteSize }, 0.01, opacity);
            this.shipSideSprite.setOverlayColor(new THREE.Color(0, 1, 0));
            this.shipSideSprite.setOverlayColorAlpha(1);
            this.mesh.add(this.shipSideSprite.mesh);

            this.mesh.name = "ship";
            this.mesh.userData = { icon: this };
            this.scene.add(this.mesh);
        }
    }, {
        key: "create",
        value: function create() {
            this.createMesh();
        }
    }, {
        key: "setPosition",
        value: function setPosition(x, y) {
            var z = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.defaultHeight;

            if ((typeof x === "undefined" ? "undefined" : _typeof(x)) === "object") {
                z = x.z || this.defaultHeight;
                y = x.y;
                x = x.x;
            }

            this.position = { x: x, y: y, z: z };

            if (this.mesh) {
                this.mesh.position.set(x, y, 0);
            }

            if (this.shipObject) {
                this.shipObject.position.set(0, 0, z);
            }
        }
    }, {
        key: "getPosition",
        value: function getPosition() {
            return this.position;
        }
    }, {
        key: "setRotation",
        value: function setRotation(x, y, z) {
            this.rotation = { x: x, y: y, z: z };

            if (this.shipObject) {
                this.shipObject.rotation.set(mathlib.degreeToRadian(x + this.startRotation.x), mathlib.degreeToRadian(y + this.startRotation.y), mathlib.degreeToRadian(z + this.startRotation.z));
            }
        }
    }, {
        key: "getRotation",
        value: function getRotation(x, y, z) {
            return this.rotation.z;
        }
    }, {
        key: "setOpacity",
        value: function setOpacity(opacity) {}
    }, {
        key: "hide",
        value: function hide() {
            if (this.hidden) {
                return;
            }

            this.scene.remove(this.mesh);
            this.hidden = true;
        }
    }, {
        key: "show",
        value: function show() {
            if (!this.hidden) {
                return;
            }

            this.scene.add(this.mesh);
            this.hidden = false;
        }
    }, {
        key: "getFacing",
        value: function getFacing(facing) {
            return this.getRotation().y;
        }
    }, {
        key: "setFacing",
        value: function setFacing(facing) {
            this.setRotation(0, facing, 0);
        }
    }, {
        key: "setOverlayColorAlpha",
        value: function setOverlayColorAlpha(alpha) {}
    }, {
        key: "getMovements",
        value: function getMovements(turn) {
            return this.movements.filter(function (movement) {
                return turn === undefined || movement.turn === turn;
            }, this);
        }
    }, {
        key: "setScale",
        value: function setScale(width, height) {
            //console.log("ShipObject.setScale is not yet implemented")
            //console.trace();
        }
    }, {
        key: "consumeEW",
        value: function consumeEW(ship) {

            if (!this.shipEWSprite) {
                return;
            }

            var dew = ew.getDefensiveEW(ship);
            if (ship.flight) {
                dew = shipManager.movement.getJinking(ship);
            }

            var ccew = ew.getCCEW(ship);

            this.shipEWSprite.update(dew, ccew);
        }
    }, {
        key: "showEW",
        value: function showEW() {
            if (this.shipEWSprite) {
                this.shipEWSprite.show();
            }
        }
    }, {
        key: "hideEW",
        value: function hideEW() {
            if (this.shipEWSprite) {
                this.shipEWSprite.hide();
            }
        }
    }, {
        key: "showSideSprite",
        value: function showSideSprite(value) {
            //console.log("ShipObject.showSideSprite is not yet implemented")
        }
    }, {
        key: "setHighlighted",
        value: function setHighlighted(value) {
            //console.log("ShipObject.showSideSprite is not yet implemented")
        }
    }, {
        key: "setSelected",
        value: function setSelected(value) {
            //console.log("ShipObject.showSideSprite is not yet implemented")
        }
    }, {
        key: "setNotMoved",
        value: function setNotMoved(value) {
            //console.log("ShipObject.showSideSprite is not yet implemented")
        }
    }, {
        key: "consumeMovement",
        value: function consumeMovement(movements) {

            var movesByHexAndTurn = [];

            this.defaultPosition = {
                turn: movements[0].turn,
                facing: movements[0].facing,
                heading: movements[0].heading,
                position: new hexagon.Offset(movements[0].position),
                offset: { x: movements[0].xOffset, y: movements[0].yOffset }
            };

            var lastMovement = null;

            movements.filter(function (movement) {
                return movement.type !== 'start';
            }).filter(function (movement) {
                return movement.commit;
            }).forEach(function (movement) {

                if (lastMovement && movement.turn !== lastMovement.turn) {

                    if (movement.type === "move" || movement.type === "slipleft" || movement.type === "slipright") {
                        this.addMovementToRegistry(movesByHexAndTurn, {
                            turn: movement.turn,
                            facing: movement.facing,
                            heading: movement.heading,
                            position: new hexagon.Offset(lastMovement.position),
                            oldFacings: [],
                            oldHeadings: []
                        });
                    }
                }

                this.addMovementToRegistry(movesByHexAndTurn, movement);

                lastMovement = movement;
            }, this);

            this.movements = movesByHexAndTurn;
        }
    }, {
        key: "addMovementToRegistry",
        value: function addMovementToRegistry(movesByHexAndTurn, movement) {

            var getPreviousMatchingMove = function getPreviousMatchingMove(moves, move) {
                var previousMove = moves[moves.length - 1];
                if (!previousMove) {
                    return null;
                }

                if (previousMove.turn === move.turn && previousMove.position.q === move.position.q && previousMove.position.r === move.position.r) {
                    return previousMove;
                }
                return null;
            };

            var previousMove = getPreviousMatchingMove(movesByHexAndTurn, movement);

            if (previousMove) {
                var saved = previousMove;

                if (saved.facing !== movement.facing) {
                    saved.oldFacings.push(saved.facing);
                }

                saved.facing = movement.facing;

                if (saved.heading !== movement.heading) {
                    saved.oldHeadings.push(saved.heading);
                }

                saved.heading = movement.heading;

                saved.position = new hexagon.Offset(movement.position);
            } else {
                movesByHexAndTurn.push({
                    //id: movement.id,
                    //type: movement.type,
                    turn: movement.turn,
                    facing: movement.facing,
                    heading: movement.heading,
                    position: new hexagon.Offset(movement.position),
                    oldFacings: [],
                    oldHeadings: []
                });
            }
        }
    }, {
        key: "movesEqual",
        value: function movesEqual(move1, move2) {
            return move1.turn === move2.turn && move1.position.equals(move2.position); // &&
        }
    }, {
        key: "getLastMovement",
        value: function getLastMovement() {
            if (this.movements.length === 0) {
                return this.defaultPosition;
            }

            return this.movements[this.movements.length - 1];
        }
    }, {
        key: "getFirstMovementOnTurn",
        value: function getFirstMovementOnTurn(turn, ignore) {
            var movement = this.movements.filter(function (move) {
                return move.turn === turn;
            }).shift();

            if (!movement) {
                return this.getLastMovement();
            }

            return movement;
        }
    }, {
        key: "getMovementBefore",
        value: function getMovementBefore(move) {
            for (var i in this.movements) {
                if (this.movements[i] === move) {
                    return this.movements[i - 1];
                }
            }

            return null;
        }
    }, {
        key: "getMovementAfter",
        value: function getMovementAfter(move) {
            for (var i in this.movements) {
                if (this.movements[i] === move) {
                    if (this.movements[i + 1]) {
                        return this.movements[i + 1];
                    }
                    return null;
                }
            }

            return null;
        }
    }, {
        key: "showWeaponArc",
        value: function showWeaponArc(ship, weapon) {

            var hexDistance = window.coordinateConverter.getHexDistance();
            var dis = weapon.rangePenalty === 0 ? hexDistance * weapon.range : 50 / weapon.rangePenalty * hexDistance;
            var arcs = shipManager.systems.getArcs(ship, weapon);

            var arcLenght = arcs.start === arcs.end ? 360 : mathlib.getArcLength(arcs.start, arcs.end);
            var arcStart = mathlib.addToDirection(0, arcLenght * -0.5);
            var arcFacing = mathlib.addToDirection(arcs.end, arcLenght * -0.5);

            var geometry = new THREE.CircleGeometry(dis, 32, mathlib.degreeToRadian(arcStart), mathlib.degreeToRadian(arcLenght));
            var material = new THREE.MeshBasicMaterial({ color: new THREE.Color("rgb(20,80,128)"), opacity: 0.5, transparent: true });
            var circle = new THREE.Mesh(geometry, material);
            circle.rotation.z = mathlib.degreeToRadian(-mathlib.addToDirection(arcFacing, -this.getFacing()));
            circle.position.z = -1;
            this.mesh.add(circle);
            this.weaponArcs.push(circle);

            return null;
        }
    }, {
        key: "hideWeaponArcs",
        value: function hideWeaponArcs() {
            this.weaponArcs.forEach(function (arc) {
                this.mesh.remove(arc);
            }, this);
        }
    }, {
        key: "showBDEW",
        value: function showBDEW() {

            var BDEW = ew.getBDEW(this.ship);
            if (!BDEW || this.BDEWSprite) {
                return;
            }

            var hexDistance = window.coordinateConverter.getHexDistance();
            var dis = 20 * hexDistance;

            var color = gamedata.isMyShip(this.ship) ? new THREE.Color(160 / 255, 250 / 255, 100 / 255) : new THREE.Color(255 / 255, 157 / 255, 0 / 255);

            var geometry = new THREE.CircleGeometry(dis, 64, 0);
            var material = new THREE.MeshBasicMaterial({ color: color, opacity: 0.2, transparent: true });
            var circle = new THREE.Mesh(geometry, material);
            circle.position.z = -1;
            this.mesh.add(circle);
            this.BDEWSprite = circle;

            return null;
        }
    }, {
        key: "hideBDEW",
        value: function hideBDEW() {
            this.mesh.remove(this.BDEWSprite);
            this.BDEWSprite = null;
        }
    }, {
        key: "positionAndFaceIcon",
        value: function positionAndFaceIcon(offset) {
            var movement = this.getLastMovement();
            var gamePosition = window.coordinateConverter.fromHexToGame(movement.position);

            if (offset) {
                gamePosition.x += offset.x;
                gamePosition.y += offset.y;
            }

            var facing = mathlib.hexFacingToAngle(movement.facing);
            var heading = mathlib.hexFacingToAngle(movement.heading);

            this.setPosition(gamePosition);
            this.setFacing(-facing);
        }
    }]);

    return ShipObject;
}();

window.ShipObject = ShipObject;

exports.default = ShipObject;

},{}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _Gunship = require('./Gunship');

var _Gunship2 = _interopRequireDefault(_Gunship);

var _Rhino = require('./Rhino');

var _Rhino2 = _interopRequireDefault(_Rhino);

var _Capital = require('./Capital');

var _Capital2 = _interopRequireDefault(_Capital);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = { Gunship: _Gunship2.default, Rhino: _Rhino2.default, Capital: _Capital2.default };

},{"./Capital":2,"./Gunship":3,"./Rhino":4}]},{},[1]);
