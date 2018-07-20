(function(){function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s}return e})()({1:[function(require,module,exports){
'use strict';

var _ships = require('./ships');

var _ships2 = _interopRequireDefault(_ships);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

console.log(_ships2.default);
window.shipObjects = _ships2.default;

},{"./ships":5}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _ShipObject2 = require("./ShipObject");

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
        key: "create",
        value: function create() {
            var _this2 = this;

            window.Loader.loadObject("img/3d/gunship/gunship.obj", function (object) {

                console.log("loaded");
                console.log(object);
                console.log(_this2);
                window.Loader.loadTexturesAndAssign(object.children[0], {}, null, 'img/3d/gunship/normal.png');

                window.Loader.loadTexturesAndAssign(object.children[1], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[3], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[6], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[7], {}, null, 'img/3d/turretNormal.png');
                window.Loader.loadTexturesAndAssign(object.children[4], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');
                window.Loader.loadTexturesAndAssign(object.children[5], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

                object.scale.set(5, 5, 5);
                _this2.startRotation = { x: 90, y: 90, z: 0
                    //object.rotation.set(mathlib.degreeToRadian(90), mathlib.degreeToRadian(90), 0);
                    //object.position.set(0, 60, 0)

                };_this2.shipObject = object;
                _this2.setRotation(0, 0, 0);
                _this2.mesh.add(_this2.shipObject);
                object.position.set(0, 0, _this2.defaultHeight);
            });

            _get(Gunship.prototype.__proto__ || Object.getPrototypeOf(Gunship.prototype), "create", this).call(this);
        }
    }]);

    return Gunship;
}(_ShipObject3.default);

exports.default = Gunship;

},{"./ShipObject":4}],3:[function(require,module,exports){
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

            window.Loader.loadObject("img/3d/rhino/rhino.obj", function (object) {

                window.Loader.loadTexturesAndAssign(object.children[0], {}, 'img/3d/rhino/texture.png', 'img/3d/rhino/sculptNormal.png');

                window.Loader.loadTexturesAndAssign(object.children[1], {}, 'img/3d/diffuseDoc.png', 'img/3d/normalDoc.png');
                window.Loader.loadTexturesAndAssign(object.children[2], {}, 'img/3d/diffuseThruster.png', 'img/3d/normalThruster.png');

                object.scale.set(2, 2, 2);
                _this2.startRotation = { x: 90, y: 90, z: 0 };

                _this2.shipObject = object;
                _this2.setRotation(0, 0, 0);
                _this2.mesh.add(_this2.shipObject);
                object.position.set(0, 0, _this2.defaultHeight);
            });

            _get(Rhino.prototype.__proto__ || Object.getPrototypeOf(Rhino.prototype), 'create', this).call(this);
        }
    }]);

    return Rhino;
}(_ShipObject3.default);

exports.default = Rhino;

},{"./ShipObject":4}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ShipObject = function () {
    function ShipObject(ship, scene) {
        _classCallCheck(this, ShipObject);

        this.ship = ship;
        this.scene = scene;
        this.mesh = new THREE.Object3D();
        this.shipObject = null;

        this.defaultHeight = 50;
        this.mine = gamedata.isMyOrTeamOneShip(ship);
        this.sideSpriteSize = 100;

        this.startRotation = { x: 0, y: 0, z: 0 };
    }

    _createClass(ShipObject, [{
        key: "createMesh",
        value: function createMesh() {

            var opacity = 0.5;
            this.line = new window.LineSprite({ x: 0, y: 0, z: 1 }, { x: 0, y: 0, z: this.defaultHeight }, 1, new THREE.Color(0, 1, 0), opacity);
            this.mesh.add(this.line.mesh);

            this.shipSideSprite = new window.ShipSelectedSprite({ width: this.sideSpriteSize, height: this.sideSpriteSize }, 0.01, opacity);
            this.shipSideSprite.setOverlayColor(new THREE.Color(0, 1, 0));
            this.shipSideSprite.setOverlayColorAlpha(1);
            this.mesh.add(this.shipSideSprite.mesh);

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
            var z = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;

            this.mesh.position.set(x, y, z);
        }
    }, {
        key: "setRotation",
        value: function setRotation(x, y, z) {
            this.shipObject.rotation.set(mathlib.degreeToRadian(x + this.startRotation.x), mathlib.degreeToRadian(y + this.startRotation.y), mathlib.degreeToRadian(z + this.startRotation.z));
        }
    }]);

    return ShipObject;
}();

exports.default = ShipObject;

},{}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _Gunship = require('./Gunship');

var _Gunship2 = _interopRequireDefault(_Gunship);

var _Rhino = require('./Rhino');

var _Rhino2 = _interopRequireDefault(_Rhino);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = { Gunship: _Gunship2.default, Rhino: _Rhino2.default };

},{"./Gunship":2,"./Rhino":3}]},{},[1]);
