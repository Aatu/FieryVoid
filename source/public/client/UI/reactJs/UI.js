"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require("react");

var React = _interopRequireWildcard(_react);

var _reactDom = require("react-dom");

var _reactDom2 = _interopRequireDefault(_reactDom);

var _PlayerSettings2 = require("./PlayerSettings");

var _PlayerSettings3 = _interopRequireDefault(_PlayerSettings2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var UIManager = function () {
    function UIManager(parentElement) {
        _classCallCheck(this, UIManager);

        this.parentElement = parentElement;
    }

    _createClass(UIManager, [{
        key: "PlayerSettings",
        value: function PlayerSettings(element, args) {
            _reactDom2.default.render(React.createElement(_PlayerSettings3.default, args), jQuery("#playerSettings", this.parentElement)[0]);
        }
    }]);

    return UIManager;
}();

window.UIManager = UIManager;