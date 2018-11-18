(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MovementOrder = function () {
    function MovementOrder(id, type, position, target, facing, turn) {
        var value = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : 0;
        var requiredThrust = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : null;
        var assignedThrust = arguments.length > 8 && arguments[8] !== undefined ? arguments[8] : null;

        _classCallCheck(this, MovementOrder);

        if (!(position instanceof hexagon.Offset)) {
            throw new Error("MovementOrder requires position as offset hexagon");
        }

        this.id = id;
        this.type = type;
        this.position = position;
        this.target = target;
        this.facing = facing;
        this.turn = turn;
        this.value = value;
        this.requiredThrust = requiredThrust;
        this.assignedThrust = assignedThrust;
    }

    _createClass(MovementOrder, [{
        key: "isDeploy",
        value: function isDeploy() {
            return this.type === _.movementTypes.DEPLOY;
        }
    }, {
        key: "isStart",
        value: function isStart() {
            return this.type === _.movementTypes.START;
        }
    }, {
        key: "isEvade",
        value: function isEvade() {
            return this.type === _.movementTypes.EVADE;
        }
    }, {
        key: "isEnd",
        value: function isEnd() {
            return this.type === _.movementTypes.END;
        }
    }]);

    return MovementOrder;
}();

window.MovementOrder = MovementOrder;
exports.default = MovementOrder;

},{".":5}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MovementPath = function () {
  function MovementPath(ship, movementService, scene) {
    _classCallCheck(this, MovementPath);

    this.ship = ship;
    this.movementService = movementService;
    this.scene = scene;

    this.objects = [];

    this.create();
  }

  _createClass(MovementPath, [{
    key: "remove",
    value: function remove() {
      var _this = this;

      this.objects.forEach(function (object3d) {
        _this.scene.remove(object3d.mesh);
        object3d.destroy();
      });
    }
  }, {
    key: "create",
    value: function create() {
      var deployMovement = this.movementService.getDeployMove(this.ship);

      if (!deployMovement) {
        return;
      }

      var move = this.movementService.getMostRecentMove(this.ship);

      var line = createMovementLine(move);
      this.scene.add(line.mesh);
      this.objects.push(line);

      var facing = createMovementFacing(move);
      this.scene.add(facing.mesh);
      this.objects.push(facing);
    }
  }]);

  return MovementPath;
}();

var createMovementLine = function createMovementLine(move) {
  var start = window.coordinateConverter.fromHexToGame(move.position);
  var end = window.coordinateConverter.fromHexToGame(move.position.add(move.target));

  return new window.LineSprite(mathlib.getPointBetweenInDistance(start, end, window.coordinateConverter.getHexDistance() * 0.45, true), mathlib.getPointBetweenInDistance(end, start, window.coordinateConverter.getHexDistance() * 0.45, true), 10, new THREE.Color(0, 0, 1), 0.5);
};

var createMovementFacing = function createMovementFacing(move) {
  var size = window.coordinateConverter.getHexDistance() * 1.5;
  var facing = new window.ShipFacingSprite({ width: size, height: size }, 0.01, 0.8, move.facing);
  facing.setPosition(window.coordinateConverter.fromHexToGame(move.position.add(move.target)));
  facing.setFacing(mathlib.hexFacingToAngle(move.facing));

  return facing;
};

exports.createMovementLine = createMovementLine;
exports.default = MovementPath;

},{}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MovementService = function () {
  function MovementService() {
    _classCallCheck(this, MovementService);

    this.gamedata = null;
  }

  _createClass(MovementService, [{
    key: "update",
    value: function update(gamedata) {
      this.gamedata = gamedata;
    }
  }, {
    key: "getDeployMove",
    value: function getDeployMove(ship) {
      return ship.movement.find(function (move) {
        return move.type === "deploy";
      });
    }
  }, {
    key: "getMostRecentMove",
    value: function getMostRecentMove(ship) {
      var _this = this;

      var move = ship.movement.slice().reverse().find(function (move) {
        return move.turn === _this.gamedata.turn;
      });
      if (move) {
        return move;
      }

      return ship.movement[ship.movement.length - 1];
    }
  }, {
    key: "getPreviousTurnLastMove",
    value: function getPreviousTurnLastMove(ship) {
      var _this2 = this;

      return ship.movement.slice().reverse().find(function (move) {
        return move.turn === _this2.gamedata.turn - 1 && move.isEnd();
      });
    }
  }, {
    key: "getAllMovesOfTurn",
    value: function getAllMovesOfTurn(ship) {
      var _this3 = this;

      return ship.movement.filter(function (move) {
        return move.turn === _this3.gamedata.turn;
      });
    }
  }, {
    key: "getShipsInSameHex",
    value: function getShipsInSameHex(ship, hex) {
      var _this4 = this;

      hex = hex && this.getMostRecentMove(ship).position;
      return this.gamedata.ships.filter(function (ship2) {
        return !shipManager.isDestroyed(ship2) && ship !== ship2 && _this4.getMostRecentMove(ship2).position.equals(hex);
      });
    }
  }, {
    key: "deploy",
    value: function deploy(ship, pos) {
      var deployMove = this.getDeployMove(ship);

      if (!deployMove) {
        var lastMove = this.getMostRecentMove(ship);
        deployMove = new _.MovementOrder(-1, _.movementTypes.DEPLOY, pos, lastMove.target, lastMove.facing, this.gamedata.turn);
        ship.movement.push(deployMove);
      } else {
        deployMove.position = pos;
      }
    }
  }, {
    key: "doDeploymentTurn",
    value: function doDeploymentTurn(ship, right) {
      var step = 1;
      if (!right) {
        step = -1;
      }

      var deployMove = this.getDeployMove(ship);
      var newfacing = mathlib.addToHexFacing(ship.deploymove.facing, step);
      deploymove.facing = newfacing;
    }
  }, {
    key: "canEvade",
    value: function canEvade(ship) {
      //TODO: get maunouvering systems, get amount of already evaded. Return true if can still evade
    }
  }, {
    key: "getEvadeMove",
    value: function getEvadeMove(ship) {
      var _this5 = this;

      return ship.movement.find(function (move) {
        return move.isEvade() && move.turn === _this5.gamedata.turn;
      });
    }
  }, {
    key: "getEvade",
    value: function getEvade(ship) {
      var evadeMove = this.getEvadeMove(ship);
      return evadeMove ? evadeMove.value : 0;
    }
  }, {
    key: "evade",
    value: function evade(ship) {}
  }, {
    key: "getTotalProducedThrust",
    value: function getTotalProducedThrust(ship) {
      if (ship.flight) {
        return ship.freethrust;
      }

      return ship.systems.filter(function (system) {
        return system.outputType === "thrust";
      }).filter(function (system) {
        return !system.isDestroyed();
      }).reduce(function (accumulated, system) {
        var crits = shipManager.criticals.hasCritical(system, "swtargetheld");
        return accumulated + system.getOutput() - crits;
      }, 0);
    }
  }, {
    key: "getRemainingEngineThrust",
    value: function getRemainingEngineThrust(ship) {
      var thrustProduced = this.getTotalProducedThrust(ship);
      var thrustChanneled = this.getAllMovesOfTurn(ship).reduce(function (accumulator, move) {
        return move.getThrustChanneled();
      }, 0);

      return thrustProduced - thrustChanneled;
    }
  }, {
    key: "getPositionAtStartOfTurn",
    value: function getPositionAtStartOfTurn(ship, currentTurn) {
      if (currentTurn === undefined) {
        currentTurn = this.gamedata.turn;
      }

      var move = null;

      for (var i = ship.movement.length - 1; i >= 0; i--) {
        move = ship.movement[i];
        if (move.turn < currentTurn) {
          break;
        }
      }

      return new hexagon.Offset(move.position);
    }
  }, {
    key: "getPreviousLocation",
    value: function getPreviousLocation(ship) {
      var oPos = shipManager.getShipPosition(ship);
      for (var i = ship.movement.length - 1; i >= 0; i--) {
        var move = ship.movement[i];
        if (!oPos.equals(new hexagon.Offset(move.position))) return move.position;
      }
      return oPos;
    }
  }]);

  return MovementService;
}();

window.MovementService = MovementService;
exports.default = MovementService;

},{".":5}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var movementTypes = {
    START: 'start',
    END: 'end',
    DEPLOY: 'deploy',
    SPEED: 'speed',
    PIVOT_LEFT: 'pivot_left',
    PIVOT_RIGHT: 'pivot_right',
    EVADE: 'evade'
};

window.movementTypes = movementTypes;
exports.default = movementTypes;

},{}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.movementTypes = exports.MovementPath = exports.MovementOrder = exports.MovementService = undefined;

var _MovementService = require("./MovementService");

var _MovementService2 = _interopRequireDefault(_MovementService);

var _MovementOrder = require("./MovementOrder");

var _MovementOrder2 = _interopRequireDefault(_MovementOrder);

var _MovementPath = require("./MovementPath");

var _MovementPath2 = _interopRequireDefault(_MovementPath);

var _MovementTypes = require("./MovementTypes");

var _MovementTypes2 = _interopRequireDefault(_MovementTypes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.MovementService = _MovementService2.default;
exports.MovementOrder = _MovementOrder2.default;
exports.MovementPath = _MovementPath2.default;
exports.movementTypes = _MovementTypes2.default;

},{"./MovementOrder":1,"./MovementPath":2,"./MovementService":3,"./MovementTypes":4}],6:[function(require,module,exports){
arguments[4][1][0].apply(exports,arguments)
},{".":10,"dup":1}],7:[function(require,module,exports){
arguments[4][2][0].apply(exports,arguments)
},{"dup":2}],8:[function(require,module,exports){
arguments[4][3][0].apply(exports,arguments)
},{".":10,"dup":3}],9:[function(require,module,exports){
arguments[4][4][0].apply(exports,arguments)
},{"dup":4}],10:[function(require,module,exports){
arguments[4][5][0].apply(exports,arguments)
},{"./MovementOrder":6,"./MovementPath":7,"./MovementService":8,"./MovementTypes":9,"dup":5}],11:[function(require,module,exports){
"use strict";

var _ships = require("./ships");

var _ships2 = _interopRequireDefault(_ships);

var _movement = require("./handler/movement");

var Movement = _interopRequireWildcard(_movement);

var _uiStrategy = require("./uiStrategy");

var UiStrategy = _interopRequireWildcard(_uiStrategy);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.shipObjects = _ships2.default;

},{"./handler/movement":10,"./ships":16,"./uiStrategy":22}],12:[function(require,module,exports){
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

var Capital = function (_ShipObject) {
  _inherits(Capital, _ShipObject);

  function Capital(ship, scene) {
    _classCallCheck(this, Capital);

    var _this = _possibleConstructorReturn(this, (Capital.__proto__ || Object.getPrototypeOf(Capital)).call(this, ship, scene));

    _this.defaultHeight = 25;
    _this.sideSpriteSize = 50;
    _this.create();
    return _this;
  }

  _createClass(Capital, [{
    key: "create",
    value: function create() {
      var _this2 = this;

      _get(Capital.prototype.__proto__ || Object.getPrototypeOf(Capital.prototype), "create", this).call(this);

      window.Loader.loadObject("img/3d/capital/capital.obj", function (object) {
        window.Loader.loadTexturesAndAssign(object.children[0], {
          normalScale: new THREE.Vector2(0.5, 0.5),
          shininess: 10,
          color: new THREE.Color(1, 1, 1)
        }, "img/3d/capital/diffuse.png", "img/3d/capital/normalEdit.png");

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

},{"./ShipObject":15}],13:[function(require,module,exports){
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

},{"./ShipObject":15}],14:[function(require,module,exports){
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

},{"./ShipObject":15}],15:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Movement = require("../handler/Movement");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var COLOR_MINE = new THREE.Color(160 / 255, 250 / 255, 100 / 255);
var COLOR_ENEMY = new THREE.Color(255 / 255, 40 / 255, 40 / 255);

var ShipObject = function () {
  function ShipObject(ship, scene) {
    _classCallCheck(this, ShipObject);

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
      this.line = new window.LineSprite({ x: 0, y: 0, z: 1 }, { x: 0, y: 0, z: this.position.z }, 1, this.mine ? COLOR_MINE : COLOR_ENEMY, opacity);
      this.mesh.add(this.line.mesh);

      this.shipSideSprite = new window.ShipSelectedSprite({ width: this.sideSpriteSize, height: this.sideSpriteSize }, 0.01, opacity);
      this.shipSideSprite.setOverlayColor(this.mine ? COLOR_MINE : COLOR_ENEMY);
      this.shipSideSprite.setOverlayColorAlpha(1);
      this.mesh.add(this.shipSideSprite.mesh);

      this.shipEWSprite = new window.ShipEWSprite({ width: this.sideSpriteSize, height: this.sideSpriteSize }, 0.01);
      this.mesh.add(this.shipEWSprite.mesh);
      this.shipEWSprite.hide();

      this.mesh.name = "ship";
      this.mesh.userData = { icon: this };
      this.scene.add(this.mesh);
      this.consumeEW(this.ship);
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
      return this.rotation;
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
    value: function getFacing() {
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
      if (value) {
        this.shipSideSprite.setOverlayColor(new THREE.Color(1, 1, 1));
        this.shipSideSprite.setOverlayColorAlpha(1);
      } else {
        this.shipSideSprite.setOverlayColor(this.mine ? COLOR_MINE : COLOR_ENEMY);
        this.shipSideSprite.setOverlayColorAlpha(0.8);
      }
    }
  }, {
    key: "setNotMoved",
    value: function setNotMoved(value) {
      //console.log("ShipObject.showSideSprite is not yet implemented")
    }
  }, {
    key: "consumeMovement",
    value: function consumeMovement(movements) {
      this.movements = movements.filter(function (move) {
        return !move.isEvade();
      });
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
      var material = new THREE.MeshBasicMaterial({
        color: new THREE.Color("rgb(20,80,128)"),
        opacity: 0.5,
        transparent: true
      });
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
  }, {
    key: "hideBDEW",
    value: function hideBDEW() {
      this.mesh.remove(this.BDEWSprite);
      this.BDEWSprite = null;
    }
  }, {
    key: "positionAndFaceIcon",
    value: function positionAndFaceIcon(offset, movementService) {
      var movement = movementService.getMostRecentMove(this.ship);
      var gamePosition = window.coordinateConverter.fromHexToGame(movement.position);

      if (offset) {
        gamePosition.x += offset.x;
        gamePosition.y += offset.y;
      }

      var facing = mathlib.hexFacingToAngle(movement.facing);

      this.setPosition(gamePosition);
      this.setFacing(-facing);
    }
  }, {
    key: "hideMovementPath",
    value: function hideMovementPath(ship) {
      if (this.movementPath) {
        this.movementPath.remove(this.scene);
        this.movementPath = null;
      }
    }
  }, {
    key: "showMovementPath",
    value: function showMovementPath(ship, movementService) {
      this.hideMovementPath(ship);
      this.movementPath = new _Movement.MovementPath(ship, movementService, this.scene);
    }
  }]);

  return ShipObject;
}();

window.ShipObject = ShipObject;

exports.default = ShipObject;

},{"../handler/Movement":5}],16:[function(require,module,exports){
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

},{"./Capital":12,"./Gunship":13,"./Rhino":14}],17:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _UiStrategy2 = require("./UiStrategy");

var _UiStrategy3 = _interopRequireDefault(_UiStrategy2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var HighlightSelectedShip = function (_UiStrategy) {
  _inherits(HighlightSelectedShip, _UiStrategy);

  function HighlightSelectedShip() {
    _classCallCheck(this, HighlightSelectedShip);

    return _possibleConstructorReturn(this, (HighlightSelectedShip.__proto__ || Object.getPrototypeOf(HighlightSelectedShip)).apply(this, arguments));
  }

  _createClass(HighlightSelectedShip, [{
    key: "deactivated",
    value: function deactivated() {
      this.shipIconContainer.hideAllMovementPaths();
    }
  }, {
    key: "setShipSelected",
    value: function setShipSelected(_ref) {
      var ship = _ref.ship;

      this.shipIconContainer.getByShip(ship).setSelected(true);
    }
  }, {
    key: "shipDeselected",
    value: function shipDeselected(_ref2) {
      var ship = _ref2.ship;

      this.shipIconContainer.getByShip(ship).setSelected(false);
    }
  }]);

  return HighlightSelectedShip;
}(_UiStrategy3.default);

exports.default = HighlightSelectedShip;

},{"./UiStrategy":21}],18:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _UiStrategy2 = require("./UiStrategy");

var _UiStrategy3 = _interopRequireDefault(_UiStrategy2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var MovementPathMouseOver = function (_UiStrategy) {
  _inherits(MovementPathMouseOver, _UiStrategy);

  function MovementPathMouseOver() {
    _classCallCheck(this, MovementPathMouseOver);

    return _possibleConstructorReturn(this, (MovementPathMouseOver.__proto__ || Object.getPrototypeOf(MovementPathMouseOver)).apply(this, arguments));
  }

  _createClass(MovementPathMouseOver, [{
    key: "deactivated",
    value: function deactivated() {
      this.shipIconContainer.hideAllMovementPaths();
    }
  }, {
    key: "shipMouseOver",
    value: function shipMouseOver(_ref) {
      var ship = _ref.ship;

      this.shipIconContainer.hideAllMovementPaths();
      this.shipIconContainer.showMovementPath(ship);
    }
  }, {
    key: "shipsMouseOver",
    value: function shipsMouseOver(_ref2) {
      var ships = _ref2.ships;
    }
  }, {
    key: "shipsMouseOut",
    value: function shipsMouseOut(_ref3) {
      var ships = _ref3.ships;

      this.shipIconContainer.hideAllMovementPaths();
    }
  }]);

  return MovementPathMouseOver;
}(_UiStrategy3.default);

exports.default = MovementPathMouseOver;

},{"./UiStrategy":21}],19:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _UiStrategy2 = require("./UiStrategy");

var _UiStrategy3 = _interopRequireDefault(_UiStrategy2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var MovementPathSelectedShip = function (_UiStrategy) {
  _inherits(MovementPathSelectedShip, _UiStrategy);

  function MovementPathSelectedShip() {
    _classCallCheck(this, MovementPathSelectedShip);

    return _possibleConstructorReturn(this, (MovementPathSelectedShip.__proto__ || Object.getPrototypeOf(MovementPathSelectedShip)).apply(this, arguments));
  }

  _createClass(MovementPathSelectedShip, [{
    key: "deactivated",
    value: function deactivated() {
      this.shipIconContainer.hideAllMovementPaths();
    }
  }, {
    key: "setShipSelected",
    value: function setShipSelected(_ref) {
      var ship = _ref.ship;

      this.shipIconContainer.showMovementPath(ship);
      this.ship = ship;
    }
  }, {
    key: "shipDeselected",
    value: function shipDeselected(_ref2) {
      var ship = _ref2.ship;

      this.shipIconContainer.hideAllMovementPaths();
    }
  }, {
    key: "shipsMouseOut",
    value: function shipsMouseOut(_ref3) {
      var ships = _ref3.ships;

      if (this.ship) {
        this.shipIconContainer.showMovementPath(this.ship);
      }
    }
  }]);

  return MovementPathSelectedShip;
}(_UiStrategy3.default);

exports.default = MovementPathSelectedShip;

},{"./UiStrategy":21}],20:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _UiStrategy2 = require("./UiStrategy");

var _UiStrategy3 = _interopRequireDefault(_UiStrategy2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var SelectedShipMovementUi = function (_UiStrategy) {
  _inherits(SelectedShipMovementUi, _UiStrategy);

  function SelectedShipMovementUi() {
    _classCallCheck(this, SelectedShipMovementUi);

    return _possibleConstructorReturn(this, (SelectedShipMovementUi.__proto__ || Object.getPrototypeOf(SelectedShipMovementUi)).apply(this, arguments));
  }

  _createClass(SelectedShipMovementUi, [{
    key: "deactivated",
    value: function deactivated() {
      this.uiManager.hideMovementUi();
    }
  }, {
    key: "setShipSelected",
    value: function setShipSelected(_ref) {
      var ship = _ref.ship;

      this.uiManager.showMovementUi({
        ship: ship,
        movementService: this.movementService
      });
    }
  }, {
    key: "shipDeselected",
    value: function shipDeselected(_ref2) {
      var ship = _ref2.ship;

      this.uiManager.hideMovementUi();
    }
  }, {
    key: "onScroll",
    value: function onScroll() {
      this.uiManager.repositionMovementUi();
    }
  }, {
    key: "onZoom",
    value: function onZoom() {
      this.uiManager.repositionMovementUi();
    }
  }]);

  return SelectedShipMovementUi;
}(_UiStrategy3.default);

exports.default = SelectedShipMovementUi;

},{"./UiStrategy":21}],21:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var UiStrategy = function () {
  function UiStrategy() {
    _classCallCheck(this, UiStrategy);

    this.selectedShip = null;
    this.movementService = null;
    this.shipIconContainer = null;
    this.gamedata = null;
    this.uiManager = null;
  }

  _createClass(UiStrategy, [{
    key: "activate",
    value: function activate(movementService, shipIconContainer, gamedata, uiManager) {
      this.movementService = movementService;
      this.shipIconContainer = shipIconContainer;
      this.gamedata = gamedata;
      this.uiManager = uiManager;
      this.activated();
    }
  }, {
    key: "update",
    value: function update(gamedata) {
      this.gamedata = gamedata;
      this.updated();
    }
  }, {
    key: "updated",
    value: function updated() {}
  }, {
    key: "deactivate",
    value: function deactivate() {
      this.deactivated();
    }
  }, {
    key: "activated",
    value: function activated() {}
  }, {
    key: "deactivated",
    value: function deactivated() {}

    //this is called when user selects a ship. Use this only if you want to do something when only the user selects ship

  }, {
    key: "shipSelected",
    value: function shipSelected(_ref) {
      var ship = _ref.ship;

      this.selectedShip = ship;
    }
  }, {
    key: "shipDeselected",
    value: function shipDeselected(_ref2) {
      var ship = _ref2.ship;

      this.selectedShip = null;
    }

    //This is called when something is selected without user input. Always called after shipSelected

  }, {
    key: "setShipSelected",
    value: function setShipSelected(_ref3) {
      var ship = _ref3.ship;

      this.selectedShip = ship;
    }
  }, {
    key: "shipMouseOver",
    value: function shipMouseOver(_ref4) {
      var ship = _ref4.ship;
    }
  }, {
    key: "shipsMouseOver",
    value: function shipsMouseOver(_ref5) {
      var ships = _ref5.ships;
    }
  }, {
    key: "shipsMouseOut",
    value: function shipsMouseOut(_ref6) {
      var ships = _ref6.ships;
    }
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged(ship) {}
  }, {
    key: "onScroll",
    value: function onScroll() {}
  }, {
    key: "onZoom",
    value: function onZoom() {}
  }]);

  return UiStrategy;
}();

exports.default = UiStrategy;

},{}],22:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.MovementPathMouseOver = exports.MovementPathSelectedShip = undefined;

var _MovementPathMouseOver = require("./MovementPathMouseOver");

var _MovementPathMouseOver2 = _interopRequireDefault(_MovementPathMouseOver);

var _MovementPathSelectedShip = require("./MovementPathSelectedShip");

var _MovementPathSelectedShip2 = _interopRequireDefault(_MovementPathSelectedShip);

var _HighlightSelectedShip = require("./HighlightSelectedShip");

var _HighlightSelectedShip2 = _interopRequireDefault(_HighlightSelectedShip);

var _SelectedShipMovementUi = require("./SelectedShipMovementUi");

var _SelectedShipMovementUi2 = _interopRequireDefault(_SelectedShipMovementUi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.uiStrategy = {
  MovementPathMouseOver: _MovementPathMouseOver2.default,
  MovementPathSelectedShip: _MovementPathSelectedShip2.default,
  HighlightSelectedShip: _HighlightSelectedShip2.default,
  SelectedShipMovementUi: _SelectedShipMovementUi2.default
};

exports.MovementPathSelectedShip = _MovementPathSelectedShip2.default;
exports.MovementPathMouseOver = _MovementPathMouseOver2.default;

},{"./HighlightSelectedShip":17,"./MovementPathMouseOver":18,"./MovementPathSelectedShip":19,"./SelectedShipMovementUi":20}]},{},[11]);
