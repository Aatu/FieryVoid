(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
(function (global){
"use strict";

require("core-js/es6");

require("core-js/fn/array/includes");

require("core-js/fn/array/flat-map");

require("core-js/fn/string/pad-start");

require("core-js/fn/string/pad-end");

require("core-js/fn/string/trim-start");

require("core-js/fn/string/trim-end");

require("core-js/fn/symbol/async-iterator");

require("core-js/fn/object/get-own-property-descriptors");

require("core-js/fn/object/values");

require("core-js/fn/object/entries");

require("core-js/fn/promise/finally");

require("core-js/web");

require("regenerator-runtime/runtime");

if (global._babelPolyfill && typeof console !== "undefined" && console.warn) {
  console.warn("@babel/polyfill is loaded more than once on this page. This is probably not desirable/intended " + "and may have consequences if different versions of the polyfills are applied sequentially. " + "If you do need to load the polyfill more than once, use @babel/polyfill/noConflict " + "instead to bypass the warning.");
}

global._babelPolyfill = true;
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"core-js/es6":2,"core-js/fn/array/flat-map":3,"core-js/fn/array/includes":4,"core-js/fn/object/entries":5,"core-js/fn/object/get-own-property-descriptors":6,"core-js/fn/object/values":7,"core-js/fn/promise/finally":8,"core-js/fn/string/pad-end":9,"core-js/fn/string/pad-start":10,"core-js/fn/string/trim-end":11,"core-js/fn/string/trim-start":12,"core-js/fn/symbol/async-iterator":13,"core-js/web":287,"regenerator-runtime/runtime":288}],2:[function(require,module,exports){
require('../modules/es6.symbol');
require('../modules/es6.object.create');
require('../modules/es6.object.define-property');
require('../modules/es6.object.define-properties');
require('../modules/es6.object.get-own-property-descriptor');
require('../modules/es6.object.get-prototype-of');
require('../modules/es6.object.keys');
require('../modules/es6.object.get-own-property-names');
require('../modules/es6.object.freeze');
require('../modules/es6.object.seal');
require('../modules/es6.object.prevent-extensions');
require('../modules/es6.object.is-frozen');
require('../modules/es6.object.is-sealed');
require('../modules/es6.object.is-extensible');
require('../modules/es6.object.assign');
require('../modules/es6.object.is');
require('../modules/es6.object.set-prototype-of');
require('../modules/es6.object.to-string');
require('../modules/es6.function.bind');
require('../modules/es6.function.name');
require('../modules/es6.function.has-instance');
require('../modules/es6.parse-int');
require('../modules/es6.parse-float');
require('../modules/es6.number.constructor');
require('../modules/es6.number.to-fixed');
require('../modules/es6.number.to-precision');
require('../modules/es6.number.epsilon');
require('../modules/es6.number.is-finite');
require('../modules/es6.number.is-integer');
require('../modules/es6.number.is-nan');
require('../modules/es6.number.is-safe-integer');
require('../modules/es6.number.max-safe-integer');
require('../modules/es6.number.min-safe-integer');
require('../modules/es6.number.parse-float');
require('../modules/es6.number.parse-int');
require('../modules/es6.math.acosh');
require('../modules/es6.math.asinh');
require('../modules/es6.math.atanh');
require('../modules/es6.math.cbrt');
require('../modules/es6.math.clz32');
require('../modules/es6.math.cosh');
require('../modules/es6.math.expm1');
require('../modules/es6.math.fround');
require('../modules/es6.math.hypot');
require('../modules/es6.math.imul');
require('../modules/es6.math.log10');
require('../modules/es6.math.log1p');
require('../modules/es6.math.log2');
require('../modules/es6.math.sign');
require('../modules/es6.math.sinh');
require('../modules/es6.math.tanh');
require('../modules/es6.math.trunc');
require('../modules/es6.string.from-code-point');
require('../modules/es6.string.raw');
require('../modules/es6.string.trim');
require('../modules/es6.string.iterator');
require('../modules/es6.string.code-point-at');
require('../modules/es6.string.ends-with');
require('../modules/es6.string.includes');
require('../modules/es6.string.repeat');
require('../modules/es6.string.starts-with');
require('../modules/es6.string.anchor');
require('../modules/es6.string.big');
require('../modules/es6.string.blink');
require('../modules/es6.string.bold');
require('../modules/es6.string.fixed');
require('../modules/es6.string.fontcolor');
require('../modules/es6.string.fontsize');
require('../modules/es6.string.italics');
require('../modules/es6.string.link');
require('../modules/es6.string.small');
require('../modules/es6.string.strike');
require('../modules/es6.string.sub');
require('../modules/es6.string.sup');
require('../modules/es6.date.now');
require('../modules/es6.date.to-json');
require('../modules/es6.date.to-iso-string');
require('../modules/es6.date.to-string');
require('../modules/es6.date.to-primitive');
require('../modules/es6.array.is-array');
require('../modules/es6.array.from');
require('../modules/es6.array.of');
require('../modules/es6.array.join');
require('../modules/es6.array.slice');
require('../modules/es6.array.sort');
require('../modules/es6.array.for-each');
require('../modules/es6.array.map');
require('../modules/es6.array.filter');
require('../modules/es6.array.some');
require('../modules/es6.array.every');
require('../modules/es6.array.reduce');
require('../modules/es6.array.reduce-right');
require('../modules/es6.array.index-of');
require('../modules/es6.array.last-index-of');
require('../modules/es6.array.copy-within');
require('../modules/es6.array.fill');
require('../modules/es6.array.find');
require('../modules/es6.array.find-index');
require('../modules/es6.array.species');
require('../modules/es6.array.iterator');
require('../modules/es6.regexp.constructor');
require('../modules/es6.regexp.exec');
require('../modules/es6.regexp.to-string');
require('../modules/es6.regexp.flags');
require('../modules/es6.regexp.match');
require('../modules/es6.regexp.replace');
require('../modules/es6.regexp.search');
require('../modules/es6.regexp.split');
require('../modules/es6.promise');
require('../modules/es6.map');
require('../modules/es6.set');
require('../modules/es6.weak-map');
require('../modules/es6.weak-set');
require('../modules/es6.typed.array-buffer');
require('../modules/es6.typed.data-view');
require('../modules/es6.typed.int8-array');
require('../modules/es6.typed.uint8-array');
require('../modules/es6.typed.uint8-clamped-array');
require('../modules/es6.typed.int16-array');
require('../modules/es6.typed.uint16-array');
require('../modules/es6.typed.int32-array');
require('../modules/es6.typed.uint32-array');
require('../modules/es6.typed.float32-array');
require('../modules/es6.typed.float64-array');
require('../modules/es6.reflect.apply');
require('../modules/es6.reflect.construct');
require('../modules/es6.reflect.define-property');
require('../modules/es6.reflect.delete-property');
require('../modules/es6.reflect.enumerate');
require('../modules/es6.reflect.get');
require('../modules/es6.reflect.get-own-property-descriptor');
require('../modules/es6.reflect.get-prototype-of');
require('../modules/es6.reflect.has');
require('../modules/es6.reflect.is-extensible');
require('../modules/es6.reflect.own-keys');
require('../modules/es6.reflect.prevent-extensions');
require('../modules/es6.reflect.set');
require('../modules/es6.reflect.set-prototype-of');
module.exports = require('../modules/_core');

},{"../modules/_core":33,"../modules/es6.array.copy-within":135,"../modules/es6.array.every":136,"../modules/es6.array.fill":137,"../modules/es6.array.filter":138,"../modules/es6.array.find":140,"../modules/es6.array.find-index":139,"../modules/es6.array.for-each":141,"../modules/es6.array.from":142,"../modules/es6.array.index-of":143,"../modules/es6.array.is-array":144,"../modules/es6.array.iterator":145,"../modules/es6.array.join":146,"../modules/es6.array.last-index-of":147,"../modules/es6.array.map":148,"../modules/es6.array.of":149,"../modules/es6.array.reduce":151,"../modules/es6.array.reduce-right":150,"../modules/es6.array.slice":152,"../modules/es6.array.some":153,"../modules/es6.array.sort":154,"../modules/es6.array.species":155,"../modules/es6.date.now":156,"../modules/es6.date.to-iso-string":157,"../modules/es6.date.to-json":158,"../modules/es6.date.to-primitive":159,"../modules/es6.date.to-string":160,"../modules/es6.function.bind":161,"../modules/es6.function.has-instance":162,"../modules/es6.function.name":163,"../modules/es6.map":164,"../modules/es6.math.acosh":165,"../modules/es6.math.asinh":166,"../modules/es6.math.atanh":167,"../modules/es6.math.cbrt":168,"../modules/es6.math.clz32":169,"../modules/es6.math.cosh":170,"../modules/es6.math.expm1":171,"../modules/es6.math.fround":172,"../modules/es6.math.hypot":173,"../modules/es6.math.imul":174,"../modules/es6.math.log10":175,"../modules/es6.math.log1p":176,"../modules/es6.math.log2":177,"../modules/es6.math.sign":178,"../modules/es6.math.sinh":179,"../modules/es6.math.tanh":180,"../modules/es6.math.trunc":181,"../modules/es6.number.constructor":182,"../modules/es6.number.epsilon":183,"../modules/es6.number.is-finite":184,"../modules/es6.number.is-integer":185,"../modules/es6.number.is-nan":186,"../modules/es6.number.is-safe-integer":187,"../modules/es6.number.max-safe-integer":188,"../modules/es6.number.min-safe-integer":189,"../modules/es6.number.parse-float":190,"../modules/es6.number.parse-int":191,"../modules/es6.number.to-fixed":192,"../modules/es6.number.to-precision":193,"../modules/es6.object.assign":194,"../modules/es6.object.create":195,"../modules/es6.object.define-properties":196,"../modules/es6.object.define-property":197,"../modules/es6.object.freeze":198,"../modules/es6.object.get-own-property-descriptor":199,"../modules/es6.object.get-own-property-names":200,"../modules/es6.object.get-prototype-of":201,"../modules/es6.object.is":205,"../modules/es6.object.is-extensible":202,"../modules/es6.object.is-frozen":203,"../modules/es6.object.is-sealed":204,"../modules/es6.object.keys":206,"../modules/es6.object.prevent-extensions":207,"../modules/es6.object.seal":208,"../modules/es6.object.set-prototype-of":209,"../modules/es6.object.to-string":210,"../modules/es6.parse-float":211,"../modules/es6.parse-int":212,"../modules/es6.promise":213,"../modules/es6.reflect.apply":214,"../modules/es6.reflect.construct":215,"../modules/es6.reflect.define-property":216,"../modules/es6.reflect.delete-property":217,"../modules/es6.reflect.enumerate":218,"../modules/es6.reflect.get":221,"../modules/es6.reflect.get-own-property-descriptor":219,"../modules/es6.reflect.get-prototype-of":220,"../modules/es6.reflect.has":222,"../modules/es6.reflect.is-extensible":223,"../modules/es6.reflect.own-keys":224,"../modules/es6.reflect.prevent-extensions":225,"../modules/es6.reflect.set":227,"../modules/es6.reflect.set-prototype-of":226,"../modules/es6.regexp.constructor":228,"../modules/es6.regexp.exec":229,"../modules/es6.regexp.flags":230,"../modules/es6.regexp.match":231,"../modules/es6.regexp.replace":232,"../modules/es6.regexp.search":233,"../modules/es6.regexp.split":234,"../modules/es6.regexp.to-string":235,"../modules/es6.set":236,"../modules/es6.string.anchor":237,"../modules/es6.string.big":238,"../modules/es6.string.blink":239,"../modules/es6.string.bold":240,"../modules/es6.string.code-point-at":241,"../modules/es6.string.ends-with":242,"../modules/es6.string.fixed":243,"../modules/es6.string.fontcolor":244,"../modules/es6.string.fontsize":245,"../modules/es6.string.from-code-point":246,"../modules/es6.string.includes":247,"../modules/es6.string.italics":248,"../modules/es6.string.iterator":249,"../modules/es6.string.link":250,"../modules/es6.string.raw":251,"../modules/es6.string.repeat":252,"../modules/es6.string.small":253,"../modules/es6.string.starts-with":254,"../modules/es6.string.strike":255,"../modules/es6.string.sub":256,"../modules/es6.string.sup":257,"../modules/es6.string.trim":258,"../modules/es6.symbol":259,"../modules/es6.typed.array-buffer":260,"../modules/es6.typed.data-view":261,"../modules/es6.typed.float32-array":262,"../modules/es6.typed.float64-array":263,"../modules/es6.typed.int16-array":264,"../modules/es6.typed.int32-array":265,"../modules/es6.typed.int8-array":266,"../modules/es6.typed.uint16-array":267,"../modules/es6.typed.uint32-array":268,"../modules/es6.typed.uint8-array":269,"../modules/es6.typed.uint8-clamped-array":270,"../modules/es6.weak-map":271,"../modules/es6.weak-set":272}],3:[function(require,module,exports){
require('../../modules/es7.array.flat-map');
module.exports = require('../../modules/_core').Array.flatMap;

},{"../../modules/_core":33,"../../modules/es7.array.flat-map":273}],4:[function(require,module,exports){
require('../../modules/es7.array.includes');
module.exports = require('../../modules/_core').Array.includes;

},{"../../modules/_core":33,"../../modules/es7.array.includes":274}],5:[function(require,module,exports){
require('../../modules/es7.object.entries');
module.exports = require('../../modules/_core').Object.entries;

},{"../../modules/_core":33,"../../modules/es7.object.entries":275}],6:[function(require,module,exports){
require('../../modules/es7.object.get-own-property-descriptors');
module.exports = require('../../modules/_core').Object.getOwnPropertyDescriptors;

},{"../../modules/_core":33,"../../modules/es7.object.get-own-property-descriptors":276}],7:[function(require,module,exports){
require('../../modules/es7.object.values');
module.exports = require('../../modules/_core').Object.values;

},{"../../modules/_core":33,"../../modules/es7.object.values":277}],8:[function(require,module,exports){
'use strict';
require('../../modules/es6.promise');
require('../../modules/es7.promise.finally');
module.exports = require('../../modules/_core').Promise['finally'];

},{"../../modules/_core":33,"../../modules/es6.promise":213,"../../modules/es7.promise.finally":278}],9:[function(require,module,exports){
require('../../modules/es7.string.pad-end');
module.exports = require('../../modules/_core').String.padEnd;

},{"../../modules/_core":33,"../../modules/es7.string.pad-end":279}],10:[function(require,module,exports){
require('../../modules/es7.string.pad-start');
module.exports = require('../../modules/_core').String.padStart;

},{"../../modules/_core":33,"../../modules/es7.string.pad-start":280}],11:[function(require,module,exports){
require('../../modules/es7.string.trim-right');
module.exports = require('../../modules/_core').String.trimRight;

},{"../../modules/_core":33,"../../modules/es7.string.trim-right":282}],12:[function(require,module,exports){
require('../../modules/es7.string.trim-left');
module.exports = require('../../modules/_core').String.trimLeft;

},{"../../modules/_core":33,"../../modules/es7.string.trim-left":281}],13:[function(require,module,exports){
require('../../modules/es7.symbol.async-iterator');
module.exports = require('../../modules/_wks-ext').f('asyncIterator');

},{"../../modules/_wks-ext":132,"../../modules/es7.symbol.async-iterator":283}],14:[function(require,module,exports){
module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};

},{}],15:[function(require,module,exports){
var cof = require('./_cof');
module.exports = function (it, msg) {
  if (typeof it != 'number' && cof(it) != 'Number') throw TypeError(msg);
  return +it;
};

},{"./_cof":29}],16:[function(require,module,exports){
// 22.1.3.31 Array.prototype[@@unscopables]
var UNSCOPABLES = require('./_wks')('unscopables');
var ArrayProto = Array.prototype;
if (ArrayProto[UNSCOPABLES] == undefined) require('./_hide')(ArrayProto, UNSCOPABLES, {});
module.exports = function (key) {
  ArrayProto[UNSCOPABLES][key] = true;
};

},{"./_hide":53,"./_wks":133}],17:[function(require,module,exports){
'use strict';
var at = require('./_string-at')(true);

 // `AdvanceStringIndex` abstract operation
// https://tc39.github.io/ecma262/#sec-advancestringindex
module.exports = function (S, index, unicode) {
  return index + (unicode ? at(S, index).length : 1);
};

},{"./_string-at":110}],18:[function(require,module,exports){
module.exports = function (it, Constructor, name, forbiddenField) {
  if (!(it instanceof Constructor) || (forbiddenField !== undefined && forbiddenField in it)) {
    throw TypeError(name + ': incorrect invocation!');
  } return it;
};

},{}],19:[function(require,module,exports){
var isObject = require('./_is-object');
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};

},{"./_is-object":62}],20:[function(require,module,exports){
// 22.1.3.3 Array.prototype.copyWithin(target, start, end = this.length)
'use strict';
var toObject = require('./_to-object');
var toAbsoluteIndex = require('./_to-absolute-index');
var toLength = require('./_to-length');

module.exports = [].copyWithin || function copyWithin(target /* = 0 */, start /* = 0, end = @length */) {
  var O = toObject(this);
  var len = toLength(O.length);
  var to = toAbsoluteIndex(target, len);
  var from = toAbsoluteIndex(start, len);
  var end = arguments.length > 2 ? arguments[2] : undefined;
  var count = Math.min((end === undefined ? len : toAbsoluteIndex(end, len)) - from, len - to);
  var inc = 1;
  if (from < to && to < from + count) {
    inc = -1;
    from += count - 1;
    to += count - 1;
  }
  while (count-- > 0) {
    if (from in O) O[to] = O[from];
    else delete O[to];
    to += inc;
    from += inc;
  } return O;
};

},{"./_to-absolute-index":118,"./_to-length":122,"./_to-object":123}],21:[function(require,module,exports){
// 22.1.3.6 Array.prototype.fill(value, start = 0, end = this.length)
'use strict';
var toObject = require('./_to-object');
var toAbsoluteIndex = require('./_to-absolute-index');
var toLength = require('./_to-length');
module.exports = function fill(value /* , start = 0, end = @length */) {
  var O = toObject(this);
  var length = toLength(O.length);
  var aLen = arguments.length;
  var index = toAbsoluteIndex(aLen > 1 ? arguments[1] : undefined, length);
  var end = aLen > 2 ? arguments[2] : undefined;
  var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
  while (endPos > index) O[index++] = value;
  return O;
};

},{"./_to-absolute-index":118,"./_to-length":122,"./_to-object":123}],22:[function(require,module,exports){
// false -> Array#indexOf
// true  -> Array#includes
var toIObject = require('./_to-iobject');
var toLength = require('./_to-length');
var toAbsoluteIndex = require('./_to-absolute-index');
module.exports = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIObject($this);
    var length = toLength(O.length);
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare
    if (IS_INCLUDES && el != el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare
      if (value != value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) if (IS_INCLUDES || index in O) {
      if (O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

},{"./_to-absolute-index":118,"./_to-iobject":121,"./_to-length":122}],23:[function(require,module,exports){
// 0 -> Array#forEach
// 1 -> Array#map
// 2 -> Array#filter
// 3 -> Array#some
// 4 -> Array#every
// 5 -> Array#find
// 6 -> Array#findIndex
var ctx = require('./_ctx');
var IObject = require('./_iobject');
var toObject = require('./_to-object');
var toLength = require('./_to-length');
var asc = require('./_array-species-create');
module.exports = function (TYPE, $create) {
  var IS_MAP = TYPE == 1;
  var IS_FILTER = TYPE == 2;
  var IS_SOME = TYPE == 3;
  var IS_EVERY = TYPE == 4;
  var IS_FIND_INDEX = TYPE == 6;
  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
  var create = $create || asc;
  return function ($this, callbackfn, that) {
    var O = toObject($this);
    var self = IObject(O);
    var f = ctx(callbackfn, that, 3);
    var length = toLength(self.length);
    var index = 0;
    var result = IS_MAP ? create($this, length) : IS_FILTER ? create($this, 0) : undefined;
    var val, res;
    for (;length > index; index++) if (NO_HOLES || index in self) {
      val = self[index];
      res = f(val, index, O);
      if (TYPE) {
        if (IS_MAP) result[index] = res;   // map
        else if (res) switch (TYPE) {
          case 3: return true;             // some
          case 5: return val;              // find
          case 6: return index;            // findIndex
          case 2: result.push(val);        // filter
        } else if (IS_EVERY) return false; // every
      }
    }
    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : result;
  };
};

},{"./_array-species-create":26,"./_ctx":35,"./_iobject":58,"./_to-length":122,"./_to-object":123}],24:[function(require,module,exports){
var aFunction = require('./_a-function');
var toObject = require('./_to-object');
var IObject = require('./_iobject');
var toLength = require('./_to-length');

module.exports = function (that, callbackfn, aLen, memo, isRight) {
  aFunction(callbackfn);
  var O = toObject(that);
  var self = IObject(O);
  var length = toLength(O.length);
  var index = isRight ? length - 1 : 0;
  var i = isRight ? -1 : 1;
  if (aLen < 2) for (;;) {
    if (index in self) {
      memo = self[index];
      index += i;
      break;
    }
    index += i;
    if (isRight ? index < 0 : length <= index) {
      throw TypeError('Reduce of empty array with no initial value');
    }
  }
  for (;isRight ? index >= 0 : length > index; index += i) if (index in self) {
    memo = callbackfn(memo, self[index], index, O);
  }
  return memo;
};

},{"./_a-function":14,"./_iobject":58,"./_to-length":122,"./_to-object":123}],25:[function(require,module,exports){
var isObject = require('./_is-object');
var isArray = require('./_is-array');
var SPECIES = require('./_wks')('species');

module.exports = function (original) {
  var C;
  if (isArray(original)) {
    C = original.constructor;
    // cross-realm fallback
    if (typeof C == 'function' && (C === Array || isArray(C.prototype))) C = undefined;
    if (isObject(C)) {
      C = C[SPECIES];
      if (C === null) C = undefined;
    }
  } return C === undefined ? Array : C;
};

},{"./_is-array":60,"./_is-object":62,"./_wks":133}],26:[function(require,module,exports){
// 9.4.2.3 ArraySpeciesCreate(originalArray, length)
var speciesConstructor = require('./_array-species-constructor');

module.exports = function (original, length) {
  return new (speciesConstructor(original))(length);
};

},{"./_array-species-constructor":25}],27:[function(require,module,exports){
'use strict';
var aFunction = require('./_a-function');
var isObject = require('./_is-object');
var invoke = require('./_invoke');
var arraySlice = [].slice;
var factories = {};

var construct = function (F, len, args) {
  if (!(len in factories)) {
    for (var n = [], i = 0; i < len; i++) n[i] = 'a[' + i + ']';
    // eslint-disable-next-line no-new-func
    factories[len] = Function('F,a', 'return new F(' + n.join(',') + ')');
  } return factories[len](F, args);
};

module.exports = Function.bind || function bind(that /* , ...args */) {
  var fn = aFunction(this);
  var partArgs = arraySlice.call(arguments, 1);
  var bound = function (/* args... */) {
    var args = partArgs.concat(arraySlice.call(arguments));
    return this instanceof bound ? construct(fn, args.length, args) : invoke(fn, args, that);
  };
  if (isObject(fn.prototype)) bound.prototype = fn.prototype;
  return bound;
};

},{"./_a-function":14,"./_invoke":57,"./_is-object":62}],28:[function(require,module,exports){
// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = require('./_cof');
var TAG = require('./_wks')('toStringTag');
// ES3 wrong here
var ARG = cof(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (e) { /* empty */ }
};

module.exports = function (it) {
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};

},{"./_cof":29,"./_wks":133}],29:[function(require,module,exports){
var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};

},{}],30:[function(require,module,exports){
'use strict';
var dP = require('./_object-dp').f;
var create = require('./_object-create');
var redefineAll = require('./_redefine-all');
var ctx = require('./_ctx');
var anInstance = require('./_an-instance');
var forOf = require('./_for-of');
var $iterDefine = require('./_iter-define');
var step = require('./_iter-step');
var setSpecies = require('./_set-species');
var DESCRIPTORS = require('./_descriptors');
var fastKey = require('./_meta').fastKey;
var validate = require('./_validate-collection');
var SIZE = DESCRIPTORS ? '_s' : 'size';

var getEntry = function (that, key) {
  // fast case
  var index = fastKey(key);
  var entry;
  if (index !== 'F') return that._i[index];
  // frozen object case
  for (entry = that._f; entry; entry = entry.n) {
    if (entry.k == key) return entry;
  }
};

module.exports = {
  getConstructor: function (wrapper, NAME, IS_MAP, ADDER) {
    var C = wrapper(function (that, iterable) {
      anInstance(that, C, NAME, '_i');
      that._t = NAME;         // collection type
      that._i = create(null); // index
      that._f = undefined;    // first entry
      that._l = undefined;    // last entry
      that[SIZE] = 0;         // size
      if (iterable != undefined) forOf(iterable, IS_MAP, that[ADDER], that);
    });
    redefineAll(C.prototype, {
      // 23.1.3.1 Map.prototype.clear()
      // 23.2.3.2 Set.prototype.clear()
      clear: function clear() {
        for (var that = validate(this, NAME), data = that._i, entry = that._f; entry; entry = entry.n) {
          entry.r = true;
          if (entry.p) entry.p = entry.p.n = undefined;
          delete data[entry.i];
        }
        that._f = that._l = undefined;
        that[SIZE] = 0;
      },
      // 23.1.3.3 Map.prototype.delete(key)
      // 23.2.3.4 Set.prototype.delete(value)
      'delete': function (key) {
        var that = validate(this, NAME);
        var entry = getEntry(that, key);
        if (entry) {
          var next = entry.n;
          var prev = entry.p;
          delete that._i[entry.i];
          entry.r = true;
          if (prev) prev.n = next;
          if (next) next.p = prev;
          if (that._f == entry) that._f = next;
          if (that._l == entry) that._l = prev;
          that[SIZE]--;
        } return !!entry;
      },
      // 23.2.3.6 Set.prototype.forEach(callbackfn, thisArg = undefined)
      // 23.1.3.5 Map.prototype.forEach(callbackfn, thisArg = undefined)
      forEach: function forEach(callbackfn /* , that = undefined */) {
        validate(this, NAME);
        var f = ctx(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
        var entry;
        while (entry = entry ? entry.n : this._f) {
          f(entry.v, entry.k, this);
          // revert to the last existing entry
          while (entry && entry.r) entry = entry.p;
        }
      },
      // 23.1.3.7 Map.prototype.has(key)
      // 23.2.3.7 Set.prototype.has(value)
      has: function has(key) {
        return !!getEntry(validate(this, NAME), key);
      }
    });
    if (DESCRIPTORS) dP(C.prototype, 'size', {
      get: function () {
        return validate(this, NAME)[SIZE];
      }
    });
    return C;
  },
  def: function (that, key, value) {
    var entry = getEntry(that, key);
    var prev, index;
    // change existing entry
    if (entry) {
      entry.v = value;
    // create new entry
    } else {
      that._l = entry = {
        i: index = fastKey(key, true), // <- index
        k: key,                        // <- key
        v: value,                      // <- value
        p: prev = that._l,             // <- previous entry
        n: undefined,                  // <- next entry
        r: false                       // <- removed
      };
      if (!that._f) that._f = entry;
      if (prev) prev.n = entry;
      that[SIZE]++;
      // add to index
      if (index !== 'F') that._i[index] = entry;
    } return that;
  },
  getEntry: getEntry,
  setStrong: function (C, NAME, IS_MAP) {
    // add .keys, .values, .entries, [@@iterator]
    // 23.1.3.4, 23.1.3.8, 23.1.3.11, 23.1.3.12, 23.2.3.5, 23.2.3.8, 23.2.3.10, 23.2.3.11
    $iterDefine(C, NAME, function (iterated, kind) {
      this._t = validate(iterated, NAME); // target
      this._k = kind;                     // kind
      this._l = undefined;                // previous
    }, function () {
      var that = this;
      var kind = that._k;
      var entry = that._l;
      // revert to the last existing entry
      while (entry && entry.r) entry = entry.p;
      // get next entry
      if (!that._t || !(that._l = entry = entry ? entry.n : that._t._f)) {
        // or finish the iteration
        that._t = undefined;
        return step(1);
      }
      // return step by kind
      if (kind == 'keys') return step(0, entry.k);
      if (kind == 'values') return step(0, entry.v);
      return step(0, [entry.k, entry.v]);
    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

    // add [@@species], 23.1.2.2, 23.2.2.2
    setSpecies(NAME);
  }
};

},{"./_an-instance":18,"./_ctx":35,"./_descriptors":39,"./_for-of":49,"./_iter-define":66,"./_iter-step":68,"./_meta":75,"./_object-create":79,"./_object-dp":80,"./_redefine-all":98,"./_set-species":104,"./_validate-collection":130}],31:[function(require,module,exports){
'use strict';
var redefineAll = require('./_redefine-all');
var getWeak = require('./_meta').getWeak;
var anObject = require('./_an-object');
var isObject = require('./_is-object');
var anInstance = require('./_an-instance');
var forOf = require('./_for-of');
var createArrayMethod = require('./_array-methods');
var $has = require('./_has');
var validate = require('./_validate-collection');
var arrayFind = createArrayMethod(5);
var arrayFindIndex = createArrayMethod(6);
var id = 0;

// fallback for uncaught frozen keys
var uncaughtFrozenStore = function (that) {
  return that._l || (that._l = new UncaughtFrozenStore());
};
var UncaughtFrozenStore = function () {
  this.a = [];
};
var findUncaughtFrozen = function (store, key) {
  return arrayFind(store.a, function (it) {
    return it[0] === key;
  });
};
UncaughtFrozenStore.prototype = {
  get: function (key) {
    var entry = findUncaughtFrozen(this, key);
    if (entry) return entry[1];
  },
  has: function (key) {
    return !!findUncaughtFrozen(this, key);
  },
  set: function (key, value) {
    var entry = findUncaughtFrozen(this, key);
    if (entry) entry[1] = value;
    else this.a.push([key, value]);
  },
  'delete': function (key) {
    var index = arrayFindIndex(this.a, function (it) {
      return it[0] === key;
    });
    if (~index) this.a.splice(index, 1);
    return !!~index;
  }
};

module.exports = {
  getConstructor: function (wrapper, NAME, IS_MAP, ADDER) {
    var C = wrapper(function (that, iterable) {
      anInstance(that, C, NAME, '_i');
      that._t = NAME;      // collection type
      that._i = id++;      // collection id
      that._l = undefined; // leak store for uncaught frozen objects
      if (iterable != undefined) forOf(iterable, IS_MAP, that[ADDER], that);
    });
    redefineAll(C.prototype, {
      // 23.3.3.2 WeakMap.prototype.delete(key)
      // 23.4.3.3 WeakSet.prototype.delete(value)
      'delete': function (key) {
        if (!isObject(key)) return false;
        var data = getWeak(key);
        if (data === true) return uncaughtFrozenStore(validate(this, NAME))['delete'](key);
        return data && $has(data, this._i) && delete data[this._i];
      },
      // 23.3.3.4 WeakMap.prototype.has(key)
      // 23.4.3.4 WeakSet.prototype.has(value)
      has: function has(key) {
        if (!isObject(key)) return false;
        var data = getWeak(key);
        if (data === true) return uncaughtFrozenStore(validate(this, NAME)).has(key);
        return data && $has(data, this._i);
      }
    });
    return C;
  },
  def: function (that, key, value) {
    var data = getWeak(anObject(key), true);
    if (data === true) uncaughtFrozenStore(that).set(key, value);
    else data[that._i] = value;
    return that;
  },
  ufstore: uncaughtFrozenStore
};

},{"./_an-instance":18,"./_an-object":19,"./_array-methods":23,"./_for-of":49,"./_has":52,"./_is-object":62,"./_meta":75,"./_redefine-all":98,"./_validate-collection":130}],32:[function(require,module,exports){
'use strict';
var global = require('./_global');
var $export = require('./_export');
var redefine = require('./_redefine');
var redefineAll = require('./_redefine-all');
var meta = require('./_meta');
var forOf = require('./_for-of');
var anInstance = require('./_an-instance');
var isObject = require('./_is-object');
var fails = require('./_fails');
var $iterDetect = require('./_iter-detect');
var setToStringTag = require('./_set-to-string-tag');
var inheritIfRequired = require('./_inherit-if-required');

module.exports = function (NAME, wrapper, methods, common, IS_MAP, IS_WEAK) {
  var Base = global[NAME];
  var C = Base;
  var ADDER = IS_MAP ? 'set' : 'add';
  var proto = C && C.prototype;
  var O = {};
  var fixMethod = function (KEY) {
    var fn = proto[KEY];
    redefine(proto, KEY,
      KEY == 'delete' ? function (a) {
        return IS_WEAK && !isObject(a) ? false : fn.call(this, a === 0 ? 0 : a);
      } : KEY == 'has' ? function has(a) {
        return IS_WEAK && !isObject(a) ? false : fn.call(this, a === 0 ? 0 : a);
      } : KEY == 'get' ? function get(a) {
        return IS_WEAK && !isObject(a) ? undefined : fn.call(this, a === 0 ? 0 : a);
      } : KEY == 'add' ? function add(a) { fn.call(this, a === 0 ? 0 : a); return this; }
        : function set(a, b) { fn.call(this, a === 0 ? 0 : a, b); return this; }
    );
  };
  if (typeof C != 'function' || !(IS_WEAK || proto.forEach && !fails(function () {
    new C().entries().next();
  }))) {
    // create collection constructor
    C = common.getConstructor(wrapper, NAME, IS_MAP, ADDER);
    redefineAll(C.prototype, methods);
    meta.NEED = true;
  } else {
    var instance = new C();
    // early implementations not supports chaining
    var HASNT_CHAINING = instance[ADDER](IS_WEAK ? {} : -0, 1) != instance;
    // V8 ~  Chromium 40- weak-collections throws on primitives, but should return false
    var THROWS_ON_PRIMITIVES = fails(function () { instance.has(1); });
    // most early implementations doesn't supports iterables, most modern - not close it correctly
    var ACCEPT_ITERABLES = $iterDetect(function (iter) { new C(iter); }); // eslint-disable-line no-new
    // for early implementations -0 and +0 not the same
    var BUGGY_ZERO = !IS_WEAK && fails(function () {
      // V8 ~ Chromium 42- fails only with 5+ elements
      var $instance = new C();
      var index = 5;
      while (index--) $instance[ADDER](index, index);
      return !$instance.has(-0);
    });
    if (!ACCEPT_ITERABLES) {
      C = wrapper(function (target, iterable) {
        anInstance(target, C, NAME);
        var that = inheritIfRequired(new Base(), target, C);
        if (iterable != undefined) forOf(iterable, IS_MAP, that[ADDER], that);
        return that;
      });
      C.prototype = proto;
      proto.constructor = C;
    }
    if (THROWS_ON_PRIMITIVES || BUGGY_ZERO) {
      fixMethod('delete');
      fixMethod('has');
      IS_MAP && fixMethod('get');
    }
    if (BUGGY_ZERO || HASNT_CHAINING) fixMethod(ADDER);
    // weak collections should not contains .clear method
    if (IS_WEAK && proto.clear) delete proto.clear;
  }

  setToStringTag(C, NAME);

  O[NAME] = C;
  $export($export.G + $export.W + $export.F * (C != Base), O);

  if (!IS_WEAK) common.setStrong(C, NAME, IS_MAP);

  return C;
};

},{"./_an-instance":18,"./_export":43,"./_fails":45,"./_for-of":49,"./_global":51,"./_inherit-if-required":56,"./_is-object":62,"./_iter-detect":67,"./_meta":75,"./_redefine":99,"./_redefine-all":98,"./_set-to-string-tag":105}],33:[function(require,module,exports){
var core = module.exports = { version: '2.6.5' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef

},{}],34:[function(require,module,exports){
'use strict';
var $defineProperty = require('./_object-dp');
var createDesc = require('./_property-desc');

module.exports = function (object, index, value) {
  if (index in object) $defineProperty.f(object, index, createDesc(0, value));
  else object[index] = value;
};

},{"./_object-dp":80,"./_property-desc":97}],35:[function(require,module,exports){
// optional / simple context binding
var aFunction = require('./_a-function');
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 1: return function (a) {
      return fn.call(that, a);
    };
    case 2: return function (a, b) {
      return fn.call(that, a, b);
    };
    case 3: return function (a, b, c) {
      return fn.call(that, a, b, c);
    };
  }
  return function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};

},{"./_a-function":14}],36:[function(require,module,exports){
'use strict';
// 20.3.4.36 / 15.9.5.43 Date.prototype.toISOString()
var fails = require('./_fails');
var getTime = Date.prototype.getTime;
var $toISOString = Date.prototype.toISOString;

var lz = function (num) {
  return num > 9 ? num : '0' + num;
};

// PhantomJS / old WebKit has a broken implementations
module.exports = (fails(function () {
  return $toISOString.call(new Date(-5e13 - 1)) != '0385-07-25T07:06:39.999Z';
}) || !fails(function () {
  $toISOString.call(new Date(NaN));
})) ? function toISOString() {
  if (!isFinite(getTime.call(this))) throw RangeError('Invalid time value');
  var d = this;
  var y = d.getUTCFullYear();
  var m = d.getUTCMilliseconds();
  var s = y < 0 ? '-' : y > 9999 ? '+' : '';
  return s + ('00000' + Math.abs(y)).slice(s ? -6 : -4) +
    '-' + lz(d.getUTCMonth() + 1) + '-' + lz(d.getUTCDate()) +
    'T' + lz(d.getUTCHours()) + ':' + lz(d.getUTCMinutes()) +
    ':' + lz(d.getUTCSeconds()) + '.' + (m > 99 ? m : '0' + lz(m)) + 'Z';
} : $toISOString;

},{"./_fails":45}],37:[function(require,module,exports){
'use strict';
var anObject = require('./_an-object');
var toPrimitive = require('./_to-primitive');
var NUMBER = 'number';

module.exports = function (hint) {
  if (hint !== 'string' && hint !== NUMBER && hint !== 'default') throw TypeError('Incorrect hint');
  return toPrimitive(anObject(this), hint != NUMBER);
};

},{"./_an-object":19,"./_to-primitive":124}],38:[function(require,module,exports){
// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};

},{}],39:[function(require,module,exports){
// Thank's IE8 for his funny defineProperty
module.exports = !require('./_fails')(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});

},{"./_fails":45}],40:[function(require,module,exports){
var isObject = require('./_is-object');
var document = require('./_global').document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};

},{"./_global":51,"./_is-object":62}],41:[function(require,module,exports){
// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');

},{}],42:[function(require,module,exports){
// all enumerable object keys, includes symbols
var getKeys = require('./_object-keys');
var gOPS = require('./_object-gops');
var pIE = require('./_object-pie');
module.exports = function (it) {
  var result = getKeys(it);
  var getSymbols = gOPS.f;
  if (getSymbols) {
    var symbols = getSymbols(it);
    var isEnum = pIE.f;
    var i = 0;
    var key;
    while (symbols.length > i) if (isEnum.call(it, key = symbols[i++])) result.push(key);
  } return result;
};

},{"./_object-gops":85,"./_object-keys":88,"./_object-pie":89}],43:[function(require,module,exports){
var global = require('./_global');
var core = require('./_core');
var hide = require('./_hide');
var redefine = require('./_redefine');
var ctx = require('./_ctx');
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] || (global[name] = {}) : (global[name] || {})[PROTOTYPE];
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE] || (exports[PROTOTYPE] = {});
  var key, own, out, exp;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    // export native or passed
    out = (own ? target : source)[key];
    // bind timers to global for call from export context
    exp = IS_BIND && own ? ctx(out, global) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // extend global
    if (target) redefine(target, key, out, type & $export.U);
    // export
    if (exports[key] != out) hide(exports, key, exp);
    if (IS_PROTO && expProto[key] != out) expProto[key] = out;
  }
};
global.core = core;
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library`
module.exports = $export;

},{"./_core":33,"./_ctx":35,"./_global":51,"./_hide":53,"./_redefine":99}],44:[function(require,module,exports){
var MATCH = require('./_wks')('match');
module.exports = function (KEY) {
  var re = /./;
  try {
    '/./'[KEY](re);
  } catch (e) {
    try {
      re[MATCH] = false;
      return !'/./'[KEY](re);
    } catch (f) { /* empty */ }
  } return true;
};

},{"./_wks":133}],45:[function(require,module,exports){
module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};

},{}],46:[function(require,module,exports){
'use strict';
require('./es6.regexp.exec');
var redefine = require('./_redefine');
var hide = require('./_hide');
var fails = require('./_fails');
var defined = require('./_defined');
var wks = require('./_wks');
var regexpExec = require('./_regexp-exec');

var SPECIES = wks('species');

var REPLACE_SUPPORTS_NAMED_GROUPS = !fails(function () {
  // #replace needs built-in support for named groups.
  // #match works fine because it just return the exec results, even if it has
  // a "grops" property.
  var re = /./;
  re.exec = function () {
    var result = [];
    result.groups = { a: '7' };
    return result;
  };
  return ''.replace(re, '$<a>') !== '7';
});

var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = (function () {
  // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
  var re = /(?:)/;
  var originalExec = re.exec;
  re.exec = function () { return originalExec.apply(this, arguments); };
  var result = 'ab'.split(re);
  return result.length === 2 && result[0] === 'a' && result[1] === 'b';
})();

module.exports = function (KEY, length, exec) {
  var SYMBOL = wks(KEY);

  var DELEGATES_TO_SYMBOL = !fails(function () {
    // String methods call symbol-named RegEp methods
    var O = {};
    O[SYMBOL] = function () { return 7; };
    return ''[KEY](O) != 7;
  });

  var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL ? !fails(function () {
    // Symbol-named RegExp methods call .exec
    var execCalled = false;
    var re = /a/;
    re.exec = function () { execCalled = true; return null; };
    if (KEY === 'split') {
      // RegExp[@@split] doesn't call the regex's exec method, but first creates
      // a new one. We need to return the patched regex when creating the new one.
      re.constructor = {};
      re.constructor[SPECIES] = function () { return re; };
    }
    re[SYMBOL]('');
    return !execCalled;
  }) : undefined;

  if (
    !DELEGATES_TO_SYMBOL ||
    !DELEGATES_TO_EXEC ||
    (KEY === 'replace' && !REPLACE_SUPPORTS_NAMED_GROUPS) ||
    (KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC)
  ) {
    var nativeRegExpMethod = /./[SYMBOL];
    var fns = exec(
      defined,
      SYMBOL,
      ''[KEY],
      function maybeCallNative(nativeMethod, regexp, str, arg2, forceStringMethod) {
        if (regexp.exec === regexpExec) {
          if (DELEGATES_TO_SYMBOL && !forceStringMethod) {
            // The native String method already delegates to @@method (this
            // polyfilled function), leasing to infinite recursion.
            // We avoid it by directly calling the native @@method method.
            return { done: true, value: nativeRegExpMethod.call(regexp, str, arg2) };
          }
          return { done: true, value: nativeMethod.call(str, regexp, arg2) };
        }
        return { done: false };
      }
    );
    var strfn = fns[0];
    var rxfn = fns[1];

    redefine(String.prototype, KEY, strfn);
    hide(RegExp.prototype, SYMBOL, length == 2
      // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
      // 21.2.5.11 RegExp.prototype[@@split](string, limit)
      ? function (string, arg) { return rxfn.call(string, this, arg); }
      // 21.2.5.6 RegExp.prototype[@@match](string)
      // 21.2.5.9 RegExp.prototype[@@search](string)
      : function (string) { return rxfn.call(string, this); }
    );
  }
};

},{"./_defined":38,"./_fails":45,"./_hide":53,"./_redefine":99,"./_regexp-exec":101,"./_wks":133,"./es6.regexp.exec":229}],47:[function(require,module,exports){
'use strict';
// 21.2.5.3 get RegExp.prototype.flags
var anObject = require('./_an-object');
module.exports = function () {
  var that = anObject(this);
  var result = '';
  if (that.global) result += 'g';
  if (that.ignoreCase) result += 'i';
  if (that.multiline) result += 'm';
  if (that.unicode) result += 'u';
  if (that.sticky) result += 'y';
  return result;
};

},{"./_an-object":19}],48:[function(require,module,exports){
'use strict';
// https://tc39.github.io/proposal-flatMap/#sec-FlattenIntoArray
var isArray = require('./_is-array');
var isObject = require('./_is-object');
var toLength = require('./_to-length');
var ctx = require('./_ctx');
var IS_CONCAT_SPREADABLE = require('./_wks')('isConcatSpreadable');

function flattenIntoArray(target, original, source, sourceLen, start, depth, mapper, thisArg) {
  var targetIndex = start;
  var sourceIndex = 0;
  var mapFn = mapper ? ctx(mapper, thisArg, 3) : false;
  var element, spreadable;

  while (sourceIndex < sourceLen) {
    if (sourceIndex in source) {
      element = mapFn ? mapFn(source[sourceIndex], sourceIndex, original) : source[sourceIndex];

      spreadable = false;
      if (isObject(element)) {
        spreadable = element[IS_CONCAT_SPREADABLE];
        spreadable = spreadable !== undefined ? !!spreadable : isArray(element);
      }

      if (spreadable && depth > 0) {
        targetIndex = flattenIntoArray(target, original, element, toLength(element.length), targetIndex, depth - 1) - 1;
      } else {
        if (targetIndex >= 0x1fffffffffffff) throw TypeError();
        target[targetIndex] = element;
      }

      targetIndex++;
    }
    sourceIndex++;
  }
  return targetIndex;
}

module.exports = flattenIntoArray;

},{"./_ctx":35,"./_is-array":60,"./_is-object":62,"./_to-length":122,"./_wks":133}],49:[function(require,module,exports){
var ctx = require('./_ctx');
var call = require('./_iter-call');
var isArrayIter = require('./_is-array-iter');
var anObject = require('./_an-object');
var toLength = require('./_to-length');
var getIterFn = require('./core.get-iterator-method');
var BREAK = {};
var RETURN = {};
var exports = module.exports = function (iterable, entries, fn, that, ITERATOR) {
  var iterFn = ITERATOR ? function () { return iterable; } : getIterFn(iterable);
  var f = ctx(fn, that, entries ? 2 : 1);
  var index = 0;
  var length, step, iterator, result;
  if (typeof iterFn != 'function') throw TypeError(iterable + ' is not iterable!');
  // fast case for arrays with default iterator
  if (isArrayIter(iterFn)) for (length = toLength(iterable.length); length > index; index++) {
    result = entries ? f(anObject(step = iterable[index])[0], step[1]) : f(iterable[index]);
    if (result === BREAK || result === RETURN) return result;
  } else for (iterator = iterFn.call(iterable); !(step = iterator.next()).done;) {
    result = call(iterator, f, step.value, entries);
    if (result === BREAK || result === RETURN) return result;
  }
};
exports.BREAK = BREAK;
exports.RETURN = RETURN;

},{"./_an-object":19,"./_ctx":35,"./_is-array-iter":59,"./_iter-call":64,"./_to-length":122,"./core.get-iterator-method":134}],50:[function(require,module,exports){
module.exports = require('./_shared')('native-function-to-string', Function.toString);

},{"./_shared":107}],51:[function(require,module,exports){
// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef

},{}],52:[function(require,module,exports){
var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};

},{}],53:[function(require,module,exports){
var dP = require('./_object-dp');
var createDesc = require('./_property-desc');
module.exports = require('./_descriptors') ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};

},{"./_descriptors":39,"./_object-dp":80,"./_property-desc":97}],54:[function(require,module,exports){
var document = require('./_global').document;
module.exports = document && document.documentElement;

},{"./_global":51}],55:[function(require,module,exports){
module.exports = !require('./_descriptors') && !require('./_fails')(function () {
  return Object.defineProperty(require('./_dom-create')('div'), 'a', { get: function () { return 7; } }).a != 7;
});

},{"./_descriptors":39,"./_dom-create":40,"./_fails":45}],56:[function(require,module,exports){
var isObject = require('./_is-object');
var setPrototypeOf = require('./_set-proto').set;
module.exports = function (that, target, C) {
  var S = target.constructor;
  var P;
  if (S !== C && typeof S == 'function' && (P = S.prototype) !== C.prototype && isObject(P) && setPrototypeOf) {
    setPrototypeOf(that, P);
  } return that;
};

},{"./_is-object":62,"./_set-proto":103}],57:[function(require,module,exports){
// fast apply, http://jsperf.lnkit.com/fast-apply/5
module.exports = function (fn, args, that) {
  var un = that === undefined;
  switch (args.length) {
    case 0: return un ? fn()
                      : fn.call(that);
    case 1: return un ? fn(args[0])
                      : fn.call(that, args[0]);
    case 2: return un ? fn(args[0], args[1])
                      : fn.call(that, args[0], args[1]);
    case 3: return un ? fn(args[0], args[1], args[2])
                      : fn.call(that, args[0], args[1], args[2]);
    case 4: return un ? fn(args[0], args[1], args[2], args[3])
                      : fn.call(that, args[0], args[1], args[2], args[3]);
  } return fn.apply(that, args);
};

},{}],58:[function(require,module,exports){
// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = require('./_cof');
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};

},{"./_cof":29}],59:[function(require,module,exports){
// check on default Array iterator
var Iterators = require('./_iterators');
var ITERATOR = require('./_wks')('iterator');
var ArrayProto = Array.prototype;

module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};

},{"./_iterators":69,"./_wks":133}],60:[function(require,module,exports){
// 7.2.2 IsArray(argument)
var cof = require('./_cof');
module.exports = Array.isArray || function isArray(arg) {
  return cof(arg) == 'Array';
};

},{"./_cof":29}],61:[function(require,module,exports){
// 20.1.2.3 Number.isInteger(number)
var isObject = require('./_is-object');
var floor = Math.floor;
module.exports = function isInteger(it) {
  return !isObject(it) && isFinite(it) && floor(it) === it;
};

},{"./_is-object":62}],62:[function(require,module,exports){
module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

},{}],63:[function(require,module,exports){
// 7.2.8 IsRegExp(argument)
var isObject = require('./_is-object');
var cof = require('./_cof');
var MATCH = require('./_wks')('match');
module.exports = function (it) {
  var isRegExp;
  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
};

},{"./_cof":29,"./_is-object":62,"./_wks":133}],64:[function(require,module,exports){
// call something on iterator step with safe closing on error
var anObject = require('./_an-object');
module.exports = function (iterator, fn, value, entries) {
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (e) {
    var ret = iterator['return'];
    if (ret !== undefined) anObject(ret.call(iterator));
    throw e;
  }
};

},{"./_an-object":19}],65:[function(require,module,exports){
'use strict';
var create = require('./_object-create');
var descriptor = require('./_property-desc');
var setToStringTag = require('./_set-to-string-tag');
var IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
require('./_hide')(IteratorPrototype, require('./_wks')('iterator'), function () { return this; });

module.exports = function (Constructor, NAME, next) {
  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
  setToStringTag(Constructor, NAME + ' Iterator');
};

},{"./_hide":53,"./_object-create":79,"./_property-desc":97,"./_set-to-string-tag":105,"./_wks":133}],66:[function(require,module,exports){
'use strict';
var LIBRARY = require('./_library');
var $export = require('./_export');
var redefine = require('./_redefine');
var hide = require('./_hide');
var Iterators = require('./_iterators');
var $iterCreate = require('./_iter-create');
var setToStringTag = require('./_set-to-string-tag');
var getPrototypeOf = require('./_object-gpo');
var ITERATOR = require('./_wks')('iterator');
var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`
var FF_ITERATOR = '@@iterator';
var KEYS = 'keys';
var VALUES = 'values';

var returnThis = function () { return this; };

module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
  $iterCreate(Constructor, NAME, next);
  var getMethod = function (kind) {
    if (!BUGGY && kind in proto) return proto[kind];
    switch (kind) {
      case KEYS: return function keys() { return new Constructor(this, kind); };
      case VALUES: return function values() { return new Constructor(this, kind); };
    } return function entries() { return new Constructor(this, kind); };
  };
  var TAG = NAME + ' Iterator';
  var DEF_VALUES = DEFAULT == VALUES;
  var VALUES_BUG = false;
  var proto = Base.prototype;
  var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
  var $default = $native || getMethod(DEFAULT);
  var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
  var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
  var methods, key, IteratorPrototype;
  // Fix native
  if ($anyNative) {
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));
    if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if (DEF_VALUES && $native && $native.name !== VALUES) {
    VALUES_BUG = true;
    $default = function values() { return $native.call(this); };
  }
  // Define iterator
  if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG] = returnThis;
  if (DEFAULT) {
    methods = {
      values: DEF_VALUES ? $default : getMethod(VALUES),
      keys: IS_SET ? $default : getMethod(KEYS),
      entries: $entries
    };
    if (FORCED) for (key in methods) {
      if (!(key in proto)) redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};

},{"./_export":43,"./_hide":53,"./_iter-create":65,"./_iterators":69,"./_library":70,"./_object-gpo":86,"./_redefine":99,"./_set-to-string-tag":105,"./_wks":133}],67:[function(require,module,exports){
var ITERATOR = require('./_wks')('iterator');
var SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function () { SAFE_CLOSING = true; };
  // eslint-disable-next-line no-throw-literal
  Array.from(riter, function () { throw 2; });
} catch (e) { /* empty */ }

module.exports = function (exec, skipClosing) {
  if (!skipClosing && !SAFE_CLOSING) return false;
  var safe = false;
  try {
    var arr = [7];
    var iter = arr[ITERATOR]();
    iter.next = function () { return { done: safe = true }; };
    arr[ITERATOR] = function () { return iter; };
    exec(arr);
  } catch (e) { /* empty */ }
  return safe;
};

},{"./_wks":133}],68:[function(require,module,exports){
module.exports = function (done, value) {
  return { value: value, done: !!done };
};

},{}],69:[function(require,module,exports){
module.exports = {};

},{}],70:[function(require,module,exports){
module.exports = false;

},{}],71:[function(require,module,exports){
// 20.2.2.14 Math.expm1(x)
var $expm1 = Math.expm1;
module.exports = (!$expm1
  // Old FF bug
  || $expm1(10) > 22025.465794806719 || $expm1(10) < 22025.4657948067165168
  // Tor Browser bug
  || $expm1(-2e-17) != -2e-17
) ? function expm1(x) {
  return (x = +x) == 0 ? x : x > -1e-6 && x < 1e-6 ? x + x * x / 2 : Math.exp(x) - 1;
} : $expm1;

},{}],72:[function(require,module,exports){
// 20.2.2.16 Math.fround(x)
var sign = require('./_math-sign');
var pow = Math.pow;
var EPSILON = pow(2, -52);
var EPSILON32 = pow(2, -23);
var MAX32 = pow(2, 127) * (2 - EPSILON32);
var MIN32 = pow(2, -126);

var roundTiesToEven = function (n) {
  return n + 1 / EPSILON - 1 / EPSILON;
};

module.exports = Math.fround || function fround(x) {
  var $abs = Math.abs(x);
  var $sign = sign(x);
  var a, result;
  if ($abs < MIN32) return $sign * roundTiesToEven($abs / MIN32 / EPSILON32) * MIN32 * EPSILON32;
  a = (1 + EPSILON32 / EPSILON) * $abs;
  result = a - (a - $abs);
  // eslint-disable-next-line no-self-compare
  if (result > MAX32 || result != result) return $sign * Infinity;
  return $sign * result;
};

},{"./_math-sign":74}],73:[function(require,module,exports){
// 20.2.2.20 Math.log1p(x)
module.exports = Math.log1p || function log1p(x) {
  return (x = +x) > -1e-8 && x < 1e-8 ? x - x * x / 2 : Math.log(1 + x);
};

},{}],74:[function(require,module,exports){
// 20.2.2.28 Math.sign(x)
module.exports = Math.sign || function sign(x) {
  // eslint-disable-next-line no-self-compare
  return (x = +x) == 0 || x != x ? x : x < 0 ? -1 : 1;
};

},{}],75:[function(require,module,exports){
var META = require('./_uid')('meta');
var isObject = require('./_is-object');
var has = require('./_has');
var setDesc = require('./_object-dp').f;
var id = 0;
var isExtensible = Object.isExtensible || function () {
  return true;
};
var FREEZE = !require('./_fails')(function () {
  return isExtensible(Object.preventExtensions({}));
});
var setMeta = function (it) {
  setDesc(it, META, { value: {
    i: 'O' + ++id, // object ID
    w: {}          // weak collections IDs
  } });
};
var fastKey = function (it, create) {
  // return primitive with prefix
  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
  if (!has(it, META)) {
    // can't set metadata to uncaught frozen object
    if (!isExtensible(it)) return 'F';
    // not necessary to add metadata
    if (!create) return 'E';
    // add missing metadata
    setMeta(it);
  // return object ID
  } return it[META].i;
};
var getWeak = function (it, create) {
  if (!has(it, META)) {
    // can't set metadata to uncaught frozen object
    if (!isExtensible(it)) return true;
    // not necessary to add metadata
    if (!create) return false;
    // add missing metadata
    setMeta(it);
  // return hash weak collections IDs
  } return it[META].w;
};
// add metadata on freeze-family methods calling
var onFreeze = function (it) {
  if (FREEZE && meta.NEED && isExtensible(it) && !has(it, META)) setMeta(it);
  return it;
};
var meta = module.exports = {
  KEY: META,
  NEED: false,
  fastKey: fastKey,
  getWeak: getWeak,
  onFreeze: onFreeze
};

},{"./_fails":45,"./_has":52,"./_is-object":62,"./_object-dp":80,"./_uid":128}],76:[function(require,module,exports){
var global = require('./_global');
var macrotask = require('./_task').set;
var Observer = global.MutationObserver || global.WebKitMutationObserver;
var process = global.process;
var Promise = global.Promise;
var isNode = require('./_cof')(process) == 'process';

module.exports = function () {
  var head, last, notify;

  var flush = function () {
    var parent, fn;
    if (isNode && (parent = process.domain)) parent.exit();
    while (head) {
      fn = head.fn;
      head = head.next;
      try {
        fn();
      } catch (e) {
        if (head) notify();
        else last = undefined;
        throw e;
      }
    } last = undefined;
    if (parent) parent.enter();
  };

  // Node.js
  if (isNode) {
    notify = function () {
      process.nextTick(flush);
    };
  // browsers with MutationObserver, except iOS Safari - https://github.com/zloirock/core-js/issues/339
  } else if (Observer && !(global.navigator && global.navigator.standalone)) {
    var toggle = true;
    var node = document.createTextNode('');
    new Observer(flush).observe(node, { characterData: true }); // eslint-disable-line no-new
    notify = function () {
      node.data = toggle = !toggle;
    };
  // environments with maybe non-completely correct, but existent Promise
  } else if (Promise && Promise.resolve) {
    // Promise.resolve without an argument throws an error in LG WebOS 2
    var promise = Promise.resolve(undefined);
    notify = function () {
      promise.then(flush);
    };
  // for other environments - macrotask based on:
  // - setImmediate
  // - MessageChannel
  // - window.postMessag
  // - onreadystatechange
  // - setTimeout
  } else {
    notify = function () {
      // strange IE + webpack dev server bug - use .call(global)
      macrotask.call(global, flush);
    };
  }

  return function (fn) {
    var task = { fn: fn, next: undefined };
    if (last) last.next = task;
    if (!head) {
      head = task;
      notify();
    } last = task;
  };
};

},{"./_cof":29,"./_global":51,"./_task":117}],77:[function(require,module,exports){
'use strict';
// 25.4.1.5 NewPromiseCapability(C)
var aFunction = require('./_a-function');

function PromiseCapability(C) {
  var resolve, reject;
  this.promise = new C(function ($$resolve, $$reject) {
    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
    resolve = $$resolve;
    reject = $$reject;
  });
  this.resolve = aFunction(resolve);
  this.reject = aFunction(reject);
}

module.exports.f = function (C) {
  return new PromiseCapability(C);
};

},{"./_a-function":14}],78:[function(require,module,exports){
'use strict';
// 19.1.2.1 Object.assign(target, source, ...)
var getKeys = require('./_object-keys');
var gOPS = require('./_object-gops');
var pIE = require('./_object-pie');
var toObject = require('./_to-object');
var IObject = require('./_iobject');
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || require('./_fails')(function () {
  var A = {};
  var B = {};
  // eslint-disable-next-line no-undef
  var S = Symbol();
  var K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function (k) { B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
  var T = toObject(target);
  var aLen = arguments.length;
  var index = 1;
  var getSymbols = gOPS.f;
  var isEnum = pIE.f;
  while (aLen > index) {
    var S = IObject(arguments[index++]);
    var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
    var length = keys.length;
    var j = 0;
    var key;
    while (length > j) if (isEnum.call(S, key = keys[j++])) T[key] = S[key];
  } return T;
} : $assign;

},{"./_fails":45,"./_iobject":58,"./_object-gops":85,"./_object-keys":88,"./_object-pie":89,"./_to-object":123}],79:[function(require,module,exports){
// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject = require('./_an-object');
var dPs = require('./_object-dps');
var enumBugKeys = require('./_enum-bug-keys');
var IE_PROTO = require('./_shared-key')('IE_PROTO');
var Empty = function () { /* empty */ };
var PROTOTYPE = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = require('./_dom-create')('iframe');
  var i = enumBugKeys.length;
  var lt = '<';
  var gt = '>';
  var iframeDocument;
  iframe.style.display = 'none';
  require('./_html').appendChild(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while (i--) delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty();
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};

},{"./_an-object":19,"./_dom-create":40,"./_enum-bug-keys":41,"./_html":54,"./_object-dps":81,"./_shared-key":106}],80:[function(require,module,exports){
var anObject = require('./_an-object');
var IE8_DOM_DEFINE = require('./_ie8-dom-define');
var toPrimitive = require('./_to-primitive');
var dP = Object.defineProperty;

exports.f = require('./_descriptors') ? Object.defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return dP(O, P, Attributes);
  } catch (e) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};

},{"./_an-object":19,"./_descriptors":39,"./_ie8-dom-define":55,"./_to-primitive":124}],81:[function(require,module,exports){
var dP = require('./_object-dp');
var anObject = require('./_an-object');
var getKeys = require('./_object-keys');

module.exports = require('./_descriptors') ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = getKeys(Properties);
  var length = keys.length;
  var i = 0;
  var P;
  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
  return O;
};

},{"./_an-object":19,"./_descriptors":39,"./_object-dp":80,"./_object-keys":88}],82:[function(require,module,exports){
var pIE = require('./_object-pie');
var createDesc = require('./_property-desc');
var toIObject = require('./_to-iobject');
var toPrimitive = require('./_to-primitive');
var has = require('./_has');
var IE8_DOM_DEFINE = require('./_ie8-dom-define');
var gOPD = Object.getOwnPropertyDescriptor;

exports.f = require('./_descriptors') ? gOPD : function getOwnPropertyDescriptor(O, P) {
  O = toIObject(O);
  P = toPrimitive(P, true);
  if (IE8_DOM_DEFINE) try {
    return gOPD(O, P);
  } catch (e) { /* empty */ }
  if (has(O, P)) return createDesc(!pIE.f.call(O, P), O[P]);
};

},{"./_descriptors":39,"./_has":52,"./_ie8-dom-define":55,"./_object-pie":89,"./_property-desc":97,"./_to-iobject":121,"./_to-primitive":124}],83:[function(require,module,exports){
// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
var toIObject = require('./_to-iobject');
var gOPN = require('./_object-gopn').f;
var toString = {}.toString;

var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
  ? Object.getOwnPropertyNames(window) : [];

var getWindowNames = function (it) {
  try {
    return gOPN(it);
  } catch (e) {
    return windowNames.slice();
  }
};

module.exports.f = function getOwnPropertyNames(it) {
  return windowNames && toString.call(it) == '[object Window]' ? getWindowNames(it) : gOPN(toIObject(it));
};

},{"./_object-gopn":84,"./_to-iobject":121}],84:[function(require,module,exports){
// 19.1.2.7 / 15.2.3.4 Object.getOwnPropertyNames(O)
var $keys = require('./_object-keys-internal');
var hiddenKeys = require('./_enum-bug-keys').concat('length', 'prototype');

exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return $keys(O, hiddenKeys);
};

},{"./_enum-bug-keys":41,"./_object-keys-internal":87}],85:[function(require,module,exports){
exports.f = Object.getOwnPropertySymbols;

},{}],86:[function(require,module,exports){
// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has = require('./_has');
var toObject = require('./_to-object');
var IE_PROTO = require('./_shared-key')('IE_PROTO');
var ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};

},{"./_has":52,"./_shared-key":106,"./_to-object":123}],87:[function(require,module,exports){
var has = require('./_has');
var toIObject = require('./_to-iobject');
var arrayIndexOf = require('./_array-includes')(false);
var IE_PROTO = require('./_shared-key')('IE_PROTO');

module.exports = function (object, names) {
  var O = toIObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) if (key != IE_PROTO) has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (has(O, key = names[i++])) {
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};

},{"./_array-includes":22,"./_has":52,"./_shared-key":106,"./_to-iobject":121}],88:[function(require,module,exports){
// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = require('./_object-keys-internal');
var enumBugKeys = require('./_enum-bug-keys');

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};

},{"./_enum-bug-keys":41,"./_object-keys-internal":87}],89:[function(require,module,exports){
exports.f = {}.propertyIsEnumerable;

},{}],90:[function(require,module,exports){
// most Object methods by ES6 should accept primitives
var $export = require('./_export');
var core = require('./_core');
var fails = require('./_fails');
module.exports = function (KEY, exec) {
  var fn = (core.Object || {})[KEY] || Object[KEY];
  var exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function () { fn(1); }), 'Object', exp);
};

},{"./_core":33,"./_export":43,"./_fails":45}],91:[function(require,module,exports){
var getKeys = require('./_object-keys');
var toIObject = require('./_to-iobject');
var isEnum = require('./_object-pie').f;
module.exports = function (isEntries) {
  return function (it) {
    var O = toIObject(it);
    var keys = getKeys(O);
    var length = keys.length;
    var i = 0;
    var result = [];
    var key;
    while (length > i) if (isEnum.call(O, key = keys[i++])) {
      result.push(isEntries ? [key, O[key]] : O[key]);
    } return result;
  };
};

},{"./_object-keys":88,"./_object-pie":89,"./_to-iobject":121}],92:[function(require,module,exports){
// all object keys, includes non-enumerable and symbols
var gOPN = require('./_object-gopn');
var gOPS = require('./_object-gops');
var anObject = require('./_an-object');
var Reflect = require('./_global').Reflect;
module.exports = Reflect && Reflect.ownKeys || function ownKeys(it) {
  var keys = gOPN.f(anObject(it));
  var getSymbols = gOPS.f;
  return getSymbols ? keys.concat(getSymbols(it)) : keys;
};

},{"./_an-object":19,"./_global":51,"./_object-gopn":84,"./_object-gops":85}],93:[function(require,module,exports){
var $parseFloat = require('./_global').parseFloat;
var $trim = require('./_string-trim').trim;

module.exports = 1 / $parseFloat(require('./_string-ws') + '-0') !== -Infinity ? function parseFloat(str) {
  var string = $trim(String(str), 3);
  var result = $parseFloat(string);
  return result === 0 && string.charAt(0) == '-' ? -0 : result;
} : $parseFloat;

},{"./_global":51,"./_string-trim":115,"./_string-ws":116}],94:[function(require,module,exports){
var $parseInt = require('./_global').parseInt;
var $trim = require('./_string-trim').trim;
var ws = require('./_string-ws');
var hex = /^[-+]?0[xX]/;

module.exports = $parseInt(ws + '08') !== 8 || $parseInt(ws + '0x16') !== 22 ? function parseInt(str, radix) {
  var string = $trim(String(str), 3);
  return $parseInt(string, (radix >>> 0) || (hex.test(string) ? 16 : 10));
} : $parseInt;

},{"./_global":51,"./_string-trim":115,"./_string-ws":116}],95:[function(require,module,exports){
module.exports = function (exec) {
  try {
    return { e: false, v: exec() };
  } catch (e) {
    return { e: true, v: e };
  }
};

},{}],96:[function(require,module,exports){
var anObject = require('./_an-object');
var isObject = require('./_is-object');
var newPromiseCapability = require('./_new-promise-capability');

module.exports = function (C, x) {
  anObject(C);
  if (isObject(x) && x.constructor === C) return x;
  var promiseCapability = newPromiseCapability.f(C);
  var resolve = promiseCapability.resolve;
  resolve(x);
  return promiseCapability.promise;
};

},{"./_an-object":19,"./_is-object":62,"./_new-promise-capability":77}],97:[function(require,module,exports){
module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};

},{}],98:[function(require,module,exports){
var redefine = require('./_redefine');
module.exports = function (target, src, safe) {
  for (var key in src) redefine(target, key, src[key], safe);
  return target;
};

},{"./_redefine":99}],99:[function(require,module,exports){
var global = require('./_global');
var hide = require('./_hide');
var has = require('./_has');
var SRC = require('./_uid')('src');
var $toString = require('./_function-to-string');
var TO_STRING = 'toString';
var TPL = ('' + $toString).split(TO_STRING);

require('./_core').inspectSource = function (it) {
  return $toString.call(it);
};

(module.exports = function (O, key, val, safe) {
  var isFunction = typeof val == 'function';
  if (isFunction) has(val, 'name') || hide(val, 'name', key);
  if (O[key] === val) return;
  if (isFunction) has(val, SRC) || hide(val, SRC, O[key] ? '' + O[key] : TPL.join(String(key)));
  if (O === global) {
    O[key] = val;
  } else if (!safe) {
    delete O[key];
    hide(O, key, val);
  } else if (O[key]) {
    O[key] = val;
  } else {
    hide(O, key, val);
  }
// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
})(Function.prototype, TO_STRING, function toString() {
  return typeof this == 'function' && this[SRC] || $toString.call(this);
});

},{"./_core":33,"./_function-to-string":50,"./_global":51,"./_has":52,"./_hide":53,"./_uid":128}],100:[function(require,module,exports){
'use strict';

var classof = require('./_classof');
var builtinExec = RegExp.prototype.exec;

 // `RegExpExec` abstract operation
// https://tc39.github.io/ecma262/#sec-regexpexec
module.exports = function (R, S) {
  var exec = R.exec;
  if (typeof exec === 'function') {
    var result = exec.call(R, S);
    if (typeof result !== 'object') {
      throw new TypeError('RegExp exec method returned something other than an Object or null');
    }
    return result;
  }
  if (classof(R) !== 'RegExp') {
    throw new TypeError('RegExp#exec called on incompatible receiver');
  }
  return builtinExec.call(R, S);
};

},{"./_classof":28}],101:[function(require,module,exports){
'use strict';

var regexpFlags = require('./_flags');

var nativeExec = RegExp.prototype.exec;
// This always refers to the native implementation, because the
// String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
// which loads this file before patching the method.
var nativeReplace = String.prototype.replace;

var patchedExec = nativeExec;

var LAST_INDEX = 'lastIndex';

var UPDATES_LAST_INDEX_WRONG = (function () {
  var re1 = /a/,
      re2 = /b*/g;
  nativeExec.call(re1, 'a');
  nativeExec.call(re2, 'a');
  return re1[LAST_INDEX] !== 0 || re2[LAST_INDEX] !== 0;
})();

// nonparticipating capturing group, copied from es5-shim's String#split patch.
var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;

var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED;

if (PATCH) {
  patchedExec = function exec(str) {
    var re = this;
    var lastIndex, reCopy, match, i;

    if (NPCG_INCLUDED) {
      reCopy = new RegExp('^' + re.source + '$(?!\\s)', regexpFlags.call(re));
    }
    if (UPDATES_LAST_INDEX_WRONG) lastIndex = re[LAST_INDEX];

    match = nativeExec.call(re, str);

    if (UPDATES_LAST_INDEX_WRONG && match) {
      re[LAST_INDEX] = re.global ? match.index + match[0].length : lastIndex;
    }
    if (NPCG_INCLUDED && match && match.length > 1) {
      // Fix browsers whose `exec` methods don't consistently return `undefined`
      // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
      // eslint-disable-next-line no-loop-func
      nativeReplace.call(match[0], reCopy, function () {
        for (i = 1; i < arguments.length - 2; i++) {
          if (arguments[i] === undefined) match[i] = undefined;
        }
      });
    }

    return match;
  };
}

module.exports = patchedExec;

},{"./_flags":47}],102:[function(require,module,exports){
// 7.2.9 SameValue(x, y)
module.exports = Object.is || function is(x, y) {
  // eslint-disable-next-line no-self-compare
  return x === y ? x !== 0 || 1 / x === 1 / y : x != x && y != y;
};

},{}],103:[function(require,module,exports){
// Works with __proto__ only. Old v8 can't work with null proto objects.
/* eslint-disable no-proto */
var isObject = require('./_is-object');
var anObject = require('./_an-object');
var check = function (O, proto) {
  anObject(O);
  if (!isObject(proto) && proto !== null) throw TypeError(proto + ": can't set as prototype!");
};
module.exports = {
  set: Object.setPrototypeOf || ('__proto__' in {} ? // eslint-disable-line
    function (test, buggy, set) {
      try {
        set = require('./_ctx')(Function.call, require('./_object-gopd').f(Object.prototype, '__proto__').set, 2);
        set(test, []);
        buggy = !(test instanceof Array);
      } catch (e) { buggy = true; }
      return function setPrototypeOf(O, proto) {
        check(O, proto);
        if (buggy) O.__proto__ = proto;
        else set(O, proto);
        return O;
      };
    }({}, false) : undefined),
  check: check
};

},{"./_an-object":19,"./_ctx":35,"./_is-object":62,"./_object-gopd":82}],104:[function(require,module,exports){
'use strict';
var global = require('./_global');
var dP = require('./_object-dp');
var DESCRIPTORS = require('./_descriptors');
var SPECIES = require('./_wks')('species');

module.exports = function (KEY) {
  var C = global[KEY];
  if (DESCRIPTORS && C && !C[SPECIES]) dP.f(C, SPECIES, {
    configurable: true,
    get: function () { return this; }
  });
};

},{"./_descriptors":39,"./_global":51,"./_object-dp":80,"./_wks":133}],105:[function(require,module,exports){
var def = require('./_object-dp').f;
var has = require('./_has');
var TAG = require('./_wks')('toStringTag');

module.exports = function (it, tag, stat) {
  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
};

},{"./_has":52,"./_object-dp":80,"./_wks":133}],106:[function(require,module,exports){
var shared = require('./_shared')('keys');
var uid = require('./_uid');
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};

},{"./_shared":107,"./_uid":128}],107:[function(require,module,exports){
var core = require('./_core');
var global = require('./_global');
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: require('./_library') ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});

},{"./_core":33,"./_global":51,"./_library":70}],108:[function(require,module,exports){
// 7.3.20 SpeciesConstructor(O, defaultConstructor)
var anObject = require('./_an-object');
var aFunction = require('./_a-function');
var SPECIES = require('./_wks')('species');
module.exports = function (O, D) {
  var C = anObject(O).constructor;
  var S;
  return C === undefined || (S = anObject(C)[SPECIES]) == undefined ? D : aFunction(S);
};

},{"./_a-function":14,"./_an-object":19,"./_wks":133}],109:[function(require,module,exports){
'use strict';
var fails = require('./_fails');

module.exports = function (method, arg) {
  return !!method && fails(function () {
    // eslint-disable-next-line no-useless-call
    arg ? method.call(null, function () { /* empty */ }, 1) : method.call(null);
  });
};

},{"./_fails":45}],110:[function(require,module,exports){
var toInteger = require('./_to-integer');
var defined = require('./_defined');
// true  -> String#at
// false -> String#codePointAt
module.exports = function (TO_STRING) {
  return function (that, pos) {
    var s = String(defined(that));
    var i = toInteger(pos);
    var l = s.length;
    var a, b;
    if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
    a = s.charCodeAt(i);
    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
      ? TO_STRING ? s.charAt(i) : a
      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
  };
};

},{"./_defined":38,"./_to-integer":120}],111:[function(require,module,exports){
// helper for String#{startsWith, endsWith, includes}
var isRegExp = require('./_is-regexp');
var defined = require('./_defined');

module.exports = function (that, searchString, NAME) {
  if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
  return String(defined(that));
};

},{"./_defined":38,"./_is-regexp":63}],112:[function(require,module,exports){
var $export = require('./_export');
var fails = require('./_fails');
var defined = require('./_defined');
var quot = /"/g;
// B.2.3.2.1 CreateHTML(string, tag, attribute, value)
var createHTML = function (string, tag, attribute, value) {
  var S = String(defined(string));
  var p1 = '<' + tag;
  if (attribute !== '') p1 += ' ' + attribute + '="' + String(value).replace(quot, '&quot;') + '"';
  return p1 + '>' + S + '</' + tag + '>';
};
module.exports = function (NAME, exec) {
  var O = {};
  O[NAME] = exec(createHTML);
  $export($export.P + $export.F * fails(function () {
    var test = ''[NAME]('"');
    return test !== test.toLowerCase() || test.split('"').length > 3;
  }), 'String', O);
};

},{"./_defined":38,"./_export":43,"./_fails":45}],113:[function(require,module,exports){
// https://github.com/tc39/proposal-string-pad-start-end
var toLength = require('./_to-length');
var repeat = require('./_string-repeat');
var defined = require('./_defined');

module.exports = function (that, maxLength, fillString, left) {
  var S = String(defined(that));
  var stringLength = S.length;
  var fillStr = fillString === undefined ? ' ' : String(fillString);
  var intMaxLength = toLength(maxLength);
  if (intMaxLength <= stringLength || fillStr == '') return S;
  var fillLen = intMaxLength - stringLength;
  var stringFiller = repeat.call(fillStr, Math.ceil(fillLen / fillStr.length));
  if (stringFiller.length > fillLen) stringFiller = stringFiller.slice(0, fillLen);
  return left ? stringFiller + S : S + stringFiller;
};

},{"./_defined":38,"./_string-repeat":114,"./_to-length":122}],114:[function(require,module,exports){
'use strict';
var toInteger = require('./_to-integer');
var defined = require('./_defined');

module.exports = function repeat(count) {
  var str = String(defined(this));
  var res = '';
  var n = toInteger(count);
  if (n < 0 || n == Infinity) throw RangeError("Count can't be negative");
  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) res += str;
  return res;
};

},{"./_defined":38,"./_to-integer":120}],115:[function(require,module,exports){
var $export = require('./_export');
var defined = require('./_defined');
var fails = require('./_fails');
var spaces = require('./_string-ws');
var space = '[' + spaces + ']';
var non = '\u200b\u0085';
var ltrim = RegExp('^' + space + space + '*');
var rtrim = RegExp(space + space + '*$');

var exporter = function (KEY, exec, ALIAS) {
  var exp = {};
  var FORCE = fails(function () {
    return !!spaces[KEY]() || non[KEY]() != non;
  });
  var fn = exp[KEY] = FORCE ? exec(trim) : spaces[KEY];
  if (ALIAS) exp[ALIAS] = fn;
  $export($export.P + $export.F * FORCE, 'String', exp);
};

// 1 -> String#trimLeft
// 2 -> String#trimRight
// 3 -> String#trim
var trim = exporter.trim = function (string, TYPE) {
  string = String(defined(string));
  if (TYPE & 1) string = string.replace(ltrim, '');
  if (TYPE & 2) string = string.replace(rtrim, '');
  return string;
};

module.exports = exporter;

},{"./_defined":38,"./_export":43,"./_fails":45,"./_string-ws":116}],116:[function(require,module,exports){
module.exports = '\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003' +
  '\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

},{}],117:[function(require,module,exports){
var ctx = require('./_ctx');
var invoke = require('./_invoke');
var html = require('./_html');
var cel = require('./_dom-create');
var global = require('./_global');
var process = global.process;
var setTask = global.setImmediate;
var clearTask = global.clearImmediate;
var MessageChannel = global.MessageChannel;
var Dispatch = global.Dispatch;
var counter = 0;
var queue = {};
var ONREADYSTATECHANGE = 'onreadystatechange';
var defer, channel, port;
var run = function () {
  var id = +this;
  // eslint-disable-next-line no-prototype-builtins
  if (queue.hasOwnProperty(id)) {
    var fn = queue[id];
    delete queue[id];
    fn();
  }
};
var listener = function (event) {
  run.call(event.data);
};
// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
if (!setTask || !clearTask) {
  setTask = function setImmediate(fn) {
    var args = [];
    var i = 1;
    while (arguments.length > i) args.push(arguments[i++]);
    queue[++counter] = function () {
      // eslint-disable-next-line no-new-func
      invoke(typeof fn == 'function' ? fn : Function(fn), args);
    };
    defer(counter);
    return counter;
  };
  clearTask = function clearImmediate(id) {
    delete queue[id];
  };
  // Node.js 0.8-
  if (require('./_cof')(process) == 'process') {
    defer = function (id) {
      process.nextTick(ctx(run, id, 1));
    };
  // Sphere (JS game engine) Dispatch API
  } else if (Dispatch && Dispatch.now) {
    defer = function (id) {
      Dispatch.now(ctx(run, id, 1));
    };
  // Browsers with MessageChannel, includes WebWorkers
  } else if (MessageChannel) {
    channel = new MessageChannel();
    port = channel.port2;
    channel.port1.onmessage = listener;
    defer = ctx(port.postMessage, port, 1);
  // Browsers with postMessage, skip WebWorkers
  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
  } else if (global.addEventListener && typeof postMessage == 'function' && !global.importScripts) {
    defer = function (id) {
      global.postMessage(id + '', '*');
    };
    global.addEventListener('message', listener, false);
  // IE8-
  } else if (ONREADYSTATECHANGE in cel('script')) {
    defer = function (id) {
      html.appendChild(cel('script'))[ONREADYSTATECHANGE] = function () {
        html.removeChild(this);
        run.call(id);
      };
    };
  // Rest old browsers
  } else {
    defer = function (id) {
      setTimeout(ctx(run, id, 1), 0);
    };
  }
}
module.exports = {
  set: setTask,
  clear: clearTask
};

},{"./_cof":29,"./_ctx":35,"./_dom-create":40,"./_global":51,"./_html":54,"./_invoke":57}],118:[function(require,module,exports){
var toInteger = require('./_to-integer');
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};

},{"./_to-integer":120}],119:[function(require,module,exports){
// https://tc39.github.io/ecma262/#sec-toindex
var toInteger = require('./_to-integer');
var toLength = require('./_to-length');
module.exports = function (it) {
  if (it === undefined) return 0;
  var number = toInteger(it);
  var length = toLength(number);
  if (number !== length) throw RangeError('Wrong length!');
  return length;
};

},{"./_to-integer":120,"./_to-length":122}],120:[function(require,module,exports){
// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};

},{}],121:[function(require,module,exports){
// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = require('./_iobject');
var defined = require('./_defined');
module.exports = function (it) {
  return IObject(defined(it));
};

},{"./_defined":38,"./_iobject":58}],122:[function(require,module,exports){
// 7.1.15 ToLength
var toInteger = require('./_to-integer');
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};

},{"./_to-integer":120}],123:[function(require,module,exports){
// 7.1.13 ToObject(argument)
var defined = require('./_defined');
module.exports = function (it) {
  return Object(defined(it));
};

},{"./_defined":38}],124:[function(require,module,exports){
// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = require('./_is-object');
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (it, S) {
  if (!isObject(it)) return it;
  var fn, val;
  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  throw TypeError("Can't convert object to primitive value");
};

},{"./_is-object":62}],125:[function(require,module,exports){
'use strict';
if (require('./_descriptors')) {
  var LIBRARY = require('./_library');
  var global = require('./_global');
  var fails = require('./_fails');
  var $export = require('./_export');
  var $typed = require('./_typed');
  var $buffer = require('./_typed-buffer');
  var ctx = require('./_ctx');
  var anInstance = require('./_an-instance');
  var propertyDesc = require('./_property-desc');
  var hide = require('./_hide');
  var redefineAll = require('./_redefine-all');
  var toInteger = require('./_to-integer');
  var toLength = require('./_to-length');
  var toIndex = require('./_to-index');
  var toAbsoluteIndex = require('./_to-absolute-index');
  var toPrimitive = require('./_to-primitive');
  var has = require('./_has');
  var classof = require('./_classof');
  var isObject = require('./_is-object');
  var toObject = require('./_to-object');
  var isArrayIter = require('./_is-array-iter');
  var create = require('./_object-create');
  var getPrototypeOf = require('./_object-gpo');
  var gOPN = require('./_object-gopn').f;
  var getIterFn = require('./core.get-iterator-method');
  var uid = require('./_uid');
  var wks = require('./_wks');
  var createArrayMethod = require('./_array-methods');
  var createArrayIncludes = require('./_array-includes');
  var speciesConstructor = require('./_species-constructor');
  var ArrayIterators = require('./es6.array.iterator');
  var Iterators = require('./_iterators');
  var $iterDetect = require('./_iter-detect');
  var setSpecies = require('./_set-species');
  var arrayFill = require('./_array-fill');
  var arrayCopyWithin = require('./_array-copy-within');
  var $DP = require('./_object-dp');
  var $GOPD = require('./_object-gopd');
  var dP = $DP.f;
  var gOPD = $GOPD.f;
  var RangeError = global.RangeError;
  var TypeError = global.TypeError;
  var Uint8Array = global.Uint8Array;
  var ARRAY_BUFFER = 'ArrayBuffer';
  var SHARED_BUFFER = 'Shared' + ARRAY_BUFFER;
  var BYTES_PER_ELEMENT = 'BYTES_PER_ELEMENT';
  var PROTOTYPE = 'prototype';
  var ArrayProto = Array[PROTOTYPE];
  var $ArrayBuffer = $buffer.ArrayBuffer;
  var $DataView = $buffer.DataView;
  var arrayForEach = createArrayMethod(0);
  var arrayFilter = createArrayMethod(2);
  var arraySome = createArrayMethod(3);
  var arrayEvery = createArrayMethod(4);
  var arrayFind = createArrayMethod(5);
  var arrayFindIndex = createArrayMethod(6);
  var arrayIncludes = createArrayIncludes(true);
  var arrayIndexOf = createArrayIncludes(false);
  var arrayValues = ArrayIterators.values;
  var arrayKeys = ArrayIterators.keys;
  var arrayEntries = ArrayIterators.entries;
  var arrayLastIndexOf = ArrayProto.lastIndexOf;
  var arrayReduce = ArrayProto.reduce;
  var arrayReduceRight = ArrayProto.reduceRight;
  var arrayJoin = ArrayProto.join;
  var arraySort = ArrayProto.sort;
  var arraySlice = ArrayProto.slice;
  var arrayToString = ArrayProto.toString;
  var arrayToLocaleString = ArrayProto.toLocaleString;
  var ITERATOR = wks('iterator');
  var TAG = wks('toStringTag');
  var TYPED_CONSTRUCTOR = uid('typed_constructor');
  var DEF_CONSTRUCTOR = uid('def_constructor');
  var ALL_CONSTRUCTORS = $typed.CONSTR;
  var TYPED_ARRAY = $typed.TYPED;
  var VIEW = $typed.VIEW;
  var WRONG_LENGTH = 'Wrong length!';

  var $map = createArrayMethod(1, function (O, length) {
    return allocate(speciesConstructor(O, O[DEF_CONSTRUCTOR]), length);
  });

  var LITTLE_ENDIAN = fails(function () {
    // eslint-disable-next-line no-undef
    return new Uint8Array(new Uint16Array([1]).buffer)[0] === 1;
  });

  var FORCED_SET = !!Uint8Array && !!Uint8Array[PROTOTYPE].set && fails(function () {
    new Uint8Array(1).set({});
  });

  var toOffset = function (it, BYTES) {
    var offset = toInteger(it);
    if (offset < 0 || offset % BYTES) throw RangeError('Wrong offset!');
    return offset;
  };

  var validate = function (it) {
    if (isObject(it) && TYPED_ARRAY in it) return it;
    throw TypeError(it + ' is not a typed array!');
  };

  var allocate = function (C, length) {
    if (!(isObject(C) && TYPED_CONSTRUCTOR in C)) {
      throw TypeError('It is not a typed array constructor!');
    } return new C(length);
  };

  var speciesFromList = function (O, list) {
    return fromList(speciesConstructor(O, O[DEF_CONSTRUCTOR]), list);
  };

  var fromList = function (C, list) {
    var index = 0;
    var length = list.length;
    var result = allocate(C, length);
    while (length > index) result[index] = list[index++];
    return result;
  };

  var addGetter = function (it, key, internal) {
    dP(it, key, { get: function () { return this._d[internal]; } });
  };

  var $from = function from(source /* , mapfn, thisArg */) {
    var O = toObject(source);
    var aLen = arguments.length;
    var mapfn = aLen > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    var iterFn = getIterFn(O);
    var i, length, values, result, step, iterator;
    if (iterFn != undefined && !isArrayIter(iterFn)) {
      for (iterator = iterFn.call(O), values = [], i = 0; !(step = iterator.next()).done; i++) {
        values.push(step.value);
      } O = values;
    }
    if (mapping && aLen > 2) mapfn = ctx(mapfn, arguments[2], 2);
    for (i = 0, length = toLength(O.length), result = allocate(this, length); length > i; i++) {
      result[i] = mapping ? mapfn(O[i], i) : O[i];
    }
    return result;
  };

  var $of = function of(/* ...items */) {
    var index = 0;
    var length = arguments.length;
    var result = allocate(this, length);
    while (length > index) result[index] = arguments[index++];
    return result;
  };

  // iOS Safari 6.x fails here
  var TO_LOCALE_BUG = !!Uint8Array && fails(function () { arrayToLocaleString.call(new Uint8Array(1)); });

  var $toLocaleString = function toLocaleString() {
    return arrayToLocaleString.apply(TO_LOCALE_BUG ? arraySlice.call(validate(this)) : validate(this), arguments);
  };

  var proto = {
    copyWithin: function copyWithin(target, start /* , end */) {
      return arrayCopyWithin.call(validate(this), target, start, arguments.length > 2 ? arguments[2] : undefined);
    },
    every: function every(callbackfn /* , thisArg */) {
      return arrayEvery(validate(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    fill: function fill(value /* , start, end */) { // eslint-disable-line no-unused-vars
      return arrayFill.apply(validate(this), arguments);
    },
    filter: function filter(callbackfn /* , thisArg */) {
      return speciesFromList(this, arrayFilter(validate(this), callbackfn,
        arguments.length > 1 ? arguments[1] : undefined));
    },
    find: function find(predicate /* , thisArg */) {
      return arrayFind(validate(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
    },
    findIndex: function findIndex(predicate /* , thisArg */) {
      return arrayFindIndex(validate(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
    },
    forEach: function forEach(callbackfn /* , thisArg */) {
      arrayForEach(validate(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    indexOf: function indexOf(searchElement /* , fromIndex */) {
      return arrayIndexOf(validate(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
    },
    includes: function includes(searchElement /* , fromIndex */) {
      return arrayIncludes(validate(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
    },
    join: function join(separator) { // eslint-disable-line no-unused-vars
      return arrayJoin.apply(validate(this), arguments);
    },
    lastIndexOf: function lastIndexOf(searchElement /* , fromIndex */) { // eslint-disable-line no-unused-vars
      return arrayLastIndexOf.apply(validate(this), arguments);
    },
    map: function map(mapfn /* , thisArg */) {
      return $map(validate(this), mapfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    reduce: function reduce(callbackfn /* , initialValue */) { // eslint-disable-line no-unused-vars
      return arrayReduce.apply(validate(this), arguments);
    },
    reduceRight: function reduceRight(callbackfn /* , initialValue */) { // eslint-disable-line no-unused-vars
      return arrayReduceRight.apply(validate(this), arguments);
    },
    reverse: function reverse() {
      var that = this;
      var length = validate(that).length;
      var middle = Math.floor(length / 2);
      var index = 0;
      var value;
      while (index < middle) {
        value = that[index];
        that[index++] = that[--length];
        that[length] = value;
      } return that;
    },
    some: function some(callbackfn /* , thisArg */) {
      return arraySome(validate(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    sort: function sort(comparefn) {
      return arraySort.call(validate(this), comparefn);
    },
    subarray: function subarray(begin, end) {
      var O = validate(this);
      var length = O.length;
      var $begin = toAbsoluteIndex(begin, length);
      return new (speciesConstructor(O, O[DEF_CONSTRUCTOR]))(
        O.buffer,
        O.byteOffset + $begin * O.BYTES_PER_ELEMENT,
        toLength((end === undefined ? length : toAbsoluteIndex(end, length)) - $begin)
      );
    }
  };

  var $slice = function slice(start, end) {
    return speciesFromList(this, arraySlice.call(validate(this), start, end));
  };

  var $set = function set(arrayLike /* , offset */) {
    validate(this);
    var offset = toOffset(arguments[1], 1);
    var length = this.length;
    var src = toObject(arrayLike);
    var len = toLength(src.length);
    var index = 0;
    if (len + offset > length) throw RangeError(WRONG_LENGTH);
    while (index < len) this[offset + index] = src[index++];
  };

  var $iterators = {
    entries: function entries() {
      return arrayEntries.call(validate(this));
    },
    keys: function keys() {
      return arrayKeys.call(validate(this));
    },
    values: function values() {
      return arrayValues.call(validate(this));
    }
  };

  var isTAIndex = function (target, key) {
    return isObject(target)
      && target[TYPED_ARRAY]
      && typeof key != 'symbol'
      && key in target
      && String(+key) == String(key);
  };
  var $getDesc = function getOwnPropertyDescriptor(target, key) {
    return isTAIndex(target, key = toPrimitive(key, true))
      ? propertyDesc(2, target[key])
      : gOPD(target, key);
  };
  var $setDesc = function defineProperty(target, key, desc) {
    if (isTAIndex(target, key = toPrimitive(key, true))
      && isObject(desc)
      && has(desc, 'value')
      && !has(desc, 'get')
      && !has(desc, 'set')
      // TODO: add validation descriptor w/o calling accessors
      && !desc.configurable
      && (!has(desc, 'writable') || desc.writable)
      && (!has(desc, 'enumerable') || desc.enumerable)
    ) {
      target[key] = desc.value;
      return target;
    } return dP(target, key, desc);
  };

  if (!ALL_CONSTRUCTORS) {
    $GOPD.f = $getDesc;
    $DP.f = $setDesc;
  }

  $export($export.S + $export.F * !ALL_CONSTRUCTORS, 'Object', {
    getOwnPropertyDescriptor: $getDesc,
    defineProperty: $setDesc
  });

  if (fails(function () { arrayToString.call({}); })) {
    arrayToString = arrayToLocaleString = function toString() {
      return arrayJoin.call(this);
    };
  }

  var $TypedArrayPrototype$ = redefineAll({}, proto);
  redefineAll($TypedArrayPrototype$, $iterators);
  hide($TypedArrayPrototype$, ITERATOR, $iterators.values);
  redefineAll($TypedArrayPrototype$, {
    slice: $slice,
    set: $set,
    constructor: function () { /* noop */ },
    toString: arrayToString,
    toLocaleString: $toLocaleString
  });
  addGetter($TypedArrayPrototype$, 'buffer', 'b');
  addGetter($TypedArrayPrototype$, 'byteOffset', 'o');
  addGetter($TypedArrayPrototype$, 'byteLength', 'l');
  addGetter($TypedArrayPrototype$, 'length', 'e');
  dP($TypedArrayPrototype$, TAG, {
    get: function () { return this[TYPED_ARRAY]; }
  });

  // eslint-disable-next-line max-statements
  module.exports = function (KEY, BYTES, wrapper, CLAMPED) {
    CLAMPED = !!CLAMPED;
    var NAME = KEY + (CLAMPED ? 'Clamped' : '') + 'Array';
    var GETTER = 'get' + KEY;
    var SETTER = 'set' + KEY;
    var TypedArray = global[NAME];
    var Base = TypedArray || {};
    var TAC = TypedArray && getPrototypeOf(TypedArray);
    var FORCED = !TypedArray || !$typed.ABV;
    var O = {};
    var TypedArrayPrototype = TypedArray && TypedArray[PROTOTYPE];
    var getter = function (that, index) {
      var data = that._d;
      return data.v[GETTER](index * BYTES + data.o, LITTLE_ENDIAN);
    };
    var setter = function (that, index, value) {
      var data = that._d;
      if (CLAMPED) value = (value = Math.round(value)) < 0 ? 0 : value > 0xff ? 0xff : value & 0xff;
      data.v[SETTER](index * BYTES + data.o, value, LITTLE_ENDIAN);
    };
    var addElement = function (that, index) {
      dP(that, index, {
        get: function () {
          return getter(this, index);
        },
        set: function (value) {
          return setter(this, index, value);
        },
        enumerable: true
      });
    };
    if (FORCED) {
      TypedArray = wrapper(function (that, data, $offset, $length) {
        anInstance(that, TypedArray, NAME, '_d');
        var index = 0;
        var offset = 0;
        var buffer, byteLength, length, klass;
        if (!isObject(data)) {
          length = toIndex(data);
          byteLength = length * BYTES;
          buffer = new $ArrayBuffer(byteLength);
        } else if (data instanceof $ArrayBuffer || (klass = classof(data)) == ARRAY_BUFFER || klass == SHARED_BUFFER) {
          buffer = data;
          offset = toOffset($offset, BYTES);
          var $len = data.byteLength;
          if ($length === undefined) {
            if ($len % BYTES) throw RangeError(WRONG_LENGTH);
            byteLength = $len - offset;
            if (byteLength < 0) throw RangeError(WRONG_LENGTH);
          } else {
            byteLength = toLength($length) * BYTES;
            if (byteLength + offset > $len) throw RangeError(WRONG_LENGTH);
          }
          length = byteLength / BYTES;
        } else if (TYPED_ARRAY in data) {
          return fromList(TypedArray, data);
        } else {
          return $from.call(TypedArray, data);
        }
        hide(that, '_d', {
          b: buffer,
          o: offset,
          l: byteLength,
          e: length,
          v: new $DataView(buffer)
        });
        while (index < length) addElement(that, index++);
      });
      TypedArrayPrototype = TypedArray[PROTOTYPE] = create($TypedArrayPrototype$);
      hide(TypedArrayPrototype, 'constructor', TypedArray);
    } else if (!fails(function () {
      TypedArray(1);
    }) || !fails(function () {
      new TypedArray(-1); // eslint-disable-line no-new
    }) || !$iterDetect(function (iter) {
      new TypedArray(); // eslint-disable-line no-new
      new TypedArray(null); // eslint-disable-line no-new
      new TypedArray(1.5); // eslint-disable-line no-new
      new TypedArray(iter); // eslint-disable-line no-new
    }, true)) {
      TypedArray = wrapper(function (that, data, $offset, $length) {
        anInstance(that, TypedArray, NAME);
        var klass;
        // `ws` module bug, temporarily remove validation length for Uint8Array
        // https://github.com/websockets/ws/pull/645
        if (!isObject(data)) return new Base(toIndex(data));
        if (data instanceof $ArrayBuffer || (klass = classof(data)) == ARRAY_BUFFER || klass == SHARED_BUFFER) {
          return $length !== undefined
            ? new Base(data, toOffset($offset, BYTES), $length)
            : $offset !== undefined
              ? new Base(data, toOffset($offset, BYTES))
              : new Base(data);
        }
        if (TYPED_ARRAY in data) return fromList(TypedArray, data);
        return $from.call(TypedArray, data);
      });
      arrayForEach(TAC !== Function.prototype ? gOPN(Base).concat(gOPN(TAC)) : gOPN(Base), function (key) {
        if (!(key in TypedArray)) hide(TypedArray, key, Base[key]);
      });
      TypedArray[PROTOTYPE] = TypedArrayPrototype;
      if (!LIBRARY) TypedArrayPrototype.constructor = TypedArray;
    }
    var $nativeIterator = TypedArrayPrototype[ITERATOR];
    var CORRECT_ITER_NAME = !!$nativeIterator
      && ($nativeIterator.name == 'values' || $nativeIterator.name == undefined);
    var $iterator = $iterators.values;
    hide(TypedArray, TYPED_CONSTRUCTOR, true);
    hide(TypedArrayPrototype, TYPED_ARRAY, NAME);
    hide(TypedArrayPrototype, VIEW, true);
    hide(TypedArrayPrototype, DEF_CONSTRUCTOR, TypedArray);

    if (CLAMPED ? new TypedArray(1)[TAG] != NAME : !(TAG in TypedArrayPrototype)) {
      dP(TypedArrayPrototype, TAG, {
        get: function () { return NAME; }
      });
    }

    O[NAME] = TypedArray;

    $export($export.G + $export.W + $export.F * (TypedArray != Base), O);

    $export($export.S, NAME, {
      BYTES_PER_ELEMENT: BYTES
    });

    $export($export.S + $export.F * fails(function () { Base.of.call(TypedArray, 1); }), NAME, {
      from: $from,
      of: $of
    });

    if (!(BYTES_PER_ELEMENT in TypedArrayPrototype)) hide(TypedArrayPrototype, BYTES_PER_ELEMENT, BYTES);

    $export($export.P, NAME, proto);

    setSpecies(NAME);

    $export($export.P + $export.F * FORCED_SET, NAME, { set: $set });

    $export($export.P + $export.F * !CORRECT_ITER_NAME, NAME, $iterators);

    if (!LIBRARY && TypedArrayPrototype.toString != arrayToString) TypedArrayPrototype.toString = arrayToString;

    $export($export.P + $export.F * fails(function () {
      new TypedArray(1).slice();
    }), NAME, { slice: $slice });

    $export($export.P + $export.F * (fails(function () {
      return [1, 2].toLocaleString() != new TypedArray([1, 2]).toLocaleString();
    }) || !fails(function () {
      TypedArrayPrototype.toLocaleString.call([1, 2]);
    })), NAME, { toLocaleString: $toLocaleString });

    Iterators[NAME] = CORRECT_ITER_NAME ? $nativeIterator : $iterator;
    if (!LIBRARY && !CORRECT_ITER_NAME) hide(TypedArrayPrototype, ITERATOR, $iterator);
  };
} else module.exports = function () { /* empty */ };

},{"./_an-instance":18,"./_array-copy-within":20,"./_array-fill":21,"./_array-includes":22,"./_array-methods":23,"./_classof":28,"./_ctx":35,"./_descriptors":39,"./_export":43,"./_fails":45,"./_global":51,"./_has":52,"./_hide":53,"./_is-array-iter":59,"./_is-object":62,"./_iter-detect":67,"./_iterators":69,"./_library":70,"./_object-create":79,"./_object-dp":80,"./_object-gopd":82,"./_object-gopn":84,"./_object-gpo":86,"./_property-desc":97,"./_redefine-all":98,"./_set-species":104,"./_species-constructor":108,"./_to-absolute-index":118,"./_to-index":119,"./_to-integer":120,"./_to-length":122,"./_to-object":123,"./_to-primitive":124,"./_typed":127,"./_typed-buffer":126,"./_uid":128,"./_wks":133,"./core.get-iterator-method":134,"./es6.array.iterator":145}],126:[function(require,module,exports){
'use strict';
var global = require('./_global');
var DESCRIPTORS = require('./_descriptors');
var LIBRARY = require('./_library');
var $typed = require('./_typed');
var hide = require('./_hide');
var redefineAll = require('./_redefine-all');
var fails = require('./_fails');
var anInstance = require('./_an-instance');
var toInteger = require('./_to-integer');
var toLength = require('./_to-length');
var toIndex = require('./_to-index');
var gOPN = require('./_object-gopn').f;
var dP = require('./_object-dp').f;
var arrayFill = require('./_array-fill');
var setToStringTag = require('./_set-to-string-tag');
var ARRAY_BUFFER = 'ArrayBuffer';
var DATA_VIEW = 'DataView';
var PROTOTYPE = 'prototype';
var WRONG_LENGTH = 'Wrong length!';
var WRONG_INDEX = 'Wrong index!';
var $ArrayBuffer = global[ARRAY_BUFFER];
var $DataView = global[DATA_VIEW];
var Math = global.Math;
var RangeError = global.RangeError;
// eslint-disable-next-line no-shadow-restricted-names
var Infinity = global.Infinity;
var BaseBuffer = $ArrayBuffer;
var abs = Math.abs;
var pow = Math.pow;
var floor = Math.floor;
var log = Math.log;
var LN2 = Math.LN2;
var BUFFER = 'buffer';
var BYTE_LENGTH = 'byteLength';
var BYTE_OFFSET = 'byteOffset';
var $BUFFER = DESCRIPTORS ? '_b' : BUFFER;
var $LENGTH = DESCRIPTORS ? '_l' : BYTE_LENGTH;
var $OFFSET = DESCRIPTORS ? '_o' : BYTE_OFFSET;

// IEEE754 conversions based on https://github.com/feross/ieee754
function packIEEE754(value, mLen, nBytes) {
  var buffer = new Array(nBytes);
  var eLen = nBytes * 8 - mLen - 1;
  var eMax = (1 << eLen) - 1;
  var eBias = eMax >> 1;
  var rt = mLen === 23 ? pow(2, -24) - pow(2, -77) : 0;
  var i = 0;
  var s = value < 0 || value === 0 && 1 / value < 0 ? 1 : 0;
  var e, m, c;
  value = abs(value);
  // eslint-disable-next-line no-self-compare
  if (value != value || value === Infinity) {
    // eslint-disable-next-line no-self-compare
    m = value != value ? 1 : 0;
    e = eMax;
  } else {
    e = floor(log(value) / LN2);
    if (value * (c = pow(2, -e)) < 1) {
      e--;
      c *= 2;
    }
    if (e + eBias >= 1) {
      value += rt / c;
    } else {
      value += rt * pow(2, 1 - eBias);
    }
    if (value * c >= 2) {
      e++;
      c /= 2;
    }
    if (e + eBias >= eMax) {
      m = 0;
      e = eMax;
    } else if (e + eBias >= 1) {
      m = (value * c - 1) * pow(2, mLen);
      e = e + eBias;
    } else {
      m = value * pow(2, eBias - 1) * pow(2, mLen);
      e = 0;
    }
  }
  for (; mLen >= 8; buffer[i++] = m & 255, m /= 256, mLen -= 8);
  e = e << mLen | m;
  eLen += mLen;
  for (; eLen > 0; buffer[i++] = e & 255, e /= 256, eLen -= 8);
  buffer[--i] |= s * 128;
  return buffer;
}
function unpackIEEE754(buffer, mLen, nBytes) {
  var eLen = nBytes * 8 - mLen - 1;
  var eMax = (1 << eLen) - 1;
  var eBias = eMax >> 1;
  var nBits = eLen - 7;
  var i = nBytes - 1;
  var s = buffer[i--];
  var e = s & 127;
  var m;
  s >>= 7;
  for (; nBits > 0; e = e * 256 + buffer[i], i--, nBits -= 8);
  m = e & (1 << -nBits) - 1;
  e >>= -nBits;
  nBits += mLen;
  for (; nBits > 0; m = m * 256 + buffer[i], i--, nBits -= 8);
  if (e === 0) {
    e = 1 - eBias;
  } else if (e === eMax) {
    return m ? NaN : s ? -Infinity : Infinity;
  } else {
    m = m + pow(2, mLen);
    e = e - eBias;
  } return (s ? -1 : 1) * m * pow(2, e - mLen);
}

function unpackI32(bytes) {
  return bytes[3] << 24 | bytes[2] << 16 | bytes[1] << 8 | bytes[0];
}
function packI8(it) {
  return [it & 0xff];
}
function packI16(it) {
  return [it & 0xff, it >> 8 & 0xff];
}
function packI32(it) {
  return [it & 0xff, it >> 8 & 0xff, it >> 16 & 0xff, it >> 24 & 0xff];
}
function packF64(it) {
  return packIEEE754(it, 52, 8);
}
function packF32(it) {
  return packIEEE754(it, 23, 4);
}

function addGetter(C, key, internal) {
  dP(C[PROTOTYPE], key, { get: function () { return this[internal]; } });
}

function get(view, bytes, index, isLittleEndian) {
  var numIndex = +index;
  var intIndex = toIndex(numIndex);
  if (intIndex + bytes > view[$LENGTH]) throw RangeError(WRONG_INDEX);
  var store = view[$BUFFER]._b;
  var start = intIndex + view[$OFFSET];
  var pack = store.slice(start, start + bytes);
  return isLittleEndian ? pack : pack.reverse();
}
function set(view, bytes, index, conversion, value, isLittleEndian) {
  var numIndex = +index;
  var intIndex = toIndex(numIndex);
  if (intIndex + bytes > view[$LENGTH]) throw RangeError(WRONG_INDEX);
  var store = view[$BUFFER]._b;
  var start = intIndex + view[$OFFSET];
  var pack = conversion(+value);
  for (var i = 0; i < bytes; i++) store[start + i] = pack[isLittleEndian ? i : bytes - i - 1];
}

if (!$typed.ABV) {
  $ArrayBuffer = function ArrayBuffer(length) {
    anInstance(this, $ArrayBuffer, ARRAY_BUFFER);
    var byteLength = toIndex(length);
    this._b = arrayFill.call(new Array(byteLength), 0);
    this[$LENGTH] = byteLength;
  };

  $DataView = function DataView(buffer, byteOffset, byteLength) {
    anInstance(this, $DataView, DATA_VIEW);
    anInstance(buffer, $ArrayBuffer, DATA_VIEW);
    var bufferLength = buffer[$LENGTH];
    var offset = toInteger(byteOffset);
    if (offset < 0 || offset > bufferLength) throw RangeError('Wrong offset!');
    byteLength = byteLength === undefined ? bufferLength - offset : toLength(byteLength);
    if (offset + byteLength > bufferLength) throw RangeError(WRONG_LENGTH);
    this[$BUFFER] = buffer;
    this[$OFFSET] = offset;
    this[$LENGTH] = byteLength;
  };

  if (DESCRIPTORS) {
    addGetter($ArrayBuffer, BYTE_LENGTH, '_l');
    addGetter($DataView, BUFFER, '_b');
    addGetter($DataView, BYTE_LENGTH, '_l');
    addGetter($DataView, BYTE_OFFSET, '_o');
  }

  redefineAll($DataView[PROTOTYPE], {
    getInt8: function getInt8(byteOffset) {
      return get(this, 1, byteOffset)[0] << 24 >> 24;
    },
    getUint8: function getUint8(byteOffset) {
      return get(this, 1, byteOffset)[0];
    },
    getInt16: function getInt16(byteOffset /* , littleEndian */) {
      var bytes = get(this, 2, byteOffset, arguments[1]);
      return (bytes[1] << 8 | bytes[0]) << 16 >> 16;
    },
    getUint16: function getUint16(byteOffset /* , littleEndian */) {
      var bytes = get(this, 2, byteOffset, arguments[1]);
      return bytes[1] << 8 | bytes[0];
    },
    getInt32: function getInt32(byteOffset /* , littleEndian */) {
      return unpackI32(get(this, 4, byteOffset, arguments[1]));
    },
    getUint32: function getUint32(byteOffset /* , littleEndian */) {
      return unpackI32(get(this, 4, byteOffset, arguments[1])) >>> 0;
    },
    getFloat32: function getFloat32(byteOffset /* , littleEndian */) {
      return unpackIEEE754(get(this, 4, byteOffset, arguments[1]), 23, 4);
    },
    getFloat64: function getFloat64(byteOffset /* , littleEndian */) {
      return unpackIEEE754(get(this, 8, byteOffset, arguments[1]), 52, 8);
    },
    setInt8: function setInt8(byteOffset, value) {
      set(this, 1, byteOffset, packI8, value);
    },
    setUint8: function setUint8(byteOffset, value) {
      set(this, 1, byteOffset, packI8, value);
    },
    setInt16: function setInt16(byteOffset, value /* , littleEndian */) {
      set(this, 2, byteOffset, packI16, value, arguments[2]);
    },
    setUint16: function setUint16(byteOffset, value /* , littleEndian */) {
      set(this, 2, byteOffset, packI16, value, arguments[2]);
    },
    setInt32: function setInt32(byteOffset, value /* , littleEndian */) {
      set(this, 4, byteOffset, packI32, value, arguments[2]);
    },
    setUint32: function setUint32(byteOffset, value /* , littleEndian */) {
      set(this, 4, byteOffset, packI32, value, arguments[2]);
    },
    setFloat32: function setFloat32(byteOffset, value /* , littleEndian */) {
      set(this, 4, byteOffset, packF32, value, arguments[2]);
    },
    setFloat64: function setFloat64(byteOffset, value /* , littleEndian */) {
      set(this, 8, byteOffset, packF64, value, arguments[2]);
    }
  });
} else {
  if (!fails(function () {
    $ArrayBuffer(1);
  }) || !fails(function () {
    new $ArrayBuffer(-1); // eslint-disable-line no-new
  }) || fails(function () {
    new $ArrayBuffer(); // eslint-disable-line no-new
    new $ArrayBuffer(1.5); // eslint-disable-line no-new
    new $ArrayBuffer(NaN); // eslint-disable-line no-new
    return $ArrayBuffer.name != ARRAY_BUFFER;
  })) {
    $ArrayBuffer = function ArrayBuffer(length) {
      anInstance(this, $ArrayBuffer);
      return new BaseBuffer(toIndex(length));
    };
    var ArrayBufferProto = $ArrayBuffer[PROTOTYPE] = BaseBuffer[PROTOTYPE];
    for (var keys = gOPN(BaseBuffer), j = 0, key; keys.length > j;) {
      if (!((key = keys[j++]) in $ArrayBuffer)) hide($ArrayBuffer, key, BaseBuffer[key]);
    }
    if (!LIBRARY) ArrayBufferProto.constructor = $ArrayBuffer;
  }
  // iOS Safari 7.x bug
  var view = new $DataView(new $ArrayBuffer(2));
  var $setInt8 = $DataView[PROTOTYPE].setInt8;
  view.setInt8(0, 2147483648);
  view.setInt8(1, 2147483649);
  if (view.getInt8(0) || !view.getInt8(1)) redefineAll($DataView[PROTOTYPE], {
    setInt8: function setInt8(byteOffset, value) {
      $setInt8.call(this, byteOffset, value << 24 >> 24);
    },
    setUint8: function setUint8(byteOffset, value) {
      $setInt8.call(this, byteOffset, value << 24 >> 24);
    }
  }, true);
}
setToStringTag($ArrayBuffer, ARRAY_BUFFER);
setToStringTag($DataView, DATA_VIEW);
hide($DataView[PROTOTYPE], $typed.VIEW, true);
exports[ARRAY_BUFFER] = $ArrayBuffer;
exports[DATA_VIEW] = $DataView;

},{"./_an-instance":18,"./_array-fill":21,"./_descriptors":39,"./_fails":45,"./_global":51,"./_hide":53,"./_library":70,"./_object-dp":80,"./_object-gopn":84,"./_redefine-all":98,"./_set-to-string-tag":105,"./_to-index":119,"./_to-integer":120,"./_to-length":122,"./_typed":127}],127:[function(require,module,exports){
var global = require('./_global');
var hide = require('./_hide');
var uid = require('./_uid');
var TYPED = uid('typed_array');
var VIEW = uid('view');
var ABV = !!(global.ArrayBuffer && global.DataView);
var CONSTR = ABV;
var i = 0;
var l = 9;
var Typed;

var TypedArrayConstructors = (
  'Int8Array,Uint8Array,Uint8ClampedArray,Int16Array,Uint16Array,Int32Array,Uint32Array,Float32Array,Float64Array'
).split(',');

while (i < l) {
  if (Typed = global[TypedArrayConstructors[i++]]) {
    hide(Typed.prototype, TYPED, true);
    hide(Typed.prototype, VIEW, true);
  } else CONSTR = false;
}

module.exports = {
  ABV: ABV,
  CONSTR: CONSTR,
  TYPED: TYPED,
  VIEW: VIEW
};

},{"./_global":51,"./_hide":53,"./_uid":128}],128:[function(require,module,exports){
var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};

},{}],129:[function(require,module,exports){
var global = require('./_global');
var navigator = global.navigator;

module.exports = navigator && navigator.userAgent || '';

},{"./_global":51}],130:[function(require,module,exports){
var isObject = require('./_is-object');
module.exports = function (it, TYPE) {
  if (!isObject(it) || it._t !== TYPE) throw TypeError('Incompatible receiver, ' + TYPE + ' required!');
  return it;
};

},{"./_is-object":62}],131:[function(require,module,exports){
var global = require('./_global');
var core = require('./_core');
var LIBRARY = require('./_library');
var wksExt = require('./_wks-ext');
var defineProperty = require('./_object-dp').f;
module.exports = function (name) {
  var $Symbol = core.Symbol || (core.Symbol = LIBRARY ? {} : global.Symbol || {});
  if (name.charAt(0) != '_' && !(name in $Symbol)) defineProperty($Symbol, name, { value: wksExt.f(name) });
};

},{"./_core":33,"./_global":51,"./_library":70,"./_object-dp":80,"./_wks-ext":132}],132:[function(require,module,exports){
exports.f = require('./_wks');

},{"./_wks":133}],133:[function(require,module,exports){
var store = require('./_shared')('wks');
var uid = require('./_uid');
var Symbol = require('./_global').Symbol;
var USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function (name) {
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;

},{"./_global":51,"./_shared":107,"./_uid":128}],134:[function(require,module,exports){
var classof = require('./_classof');
var ITERATOR = require('./_wks')('iterator');
var Iterators = require('./_iterators');
module.exports = require('./_core').getIteratorMethod = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};

},{"./_classof":28,"./_core":33,"./_iterators":69,"./_wks":133}],135:[function(require,module,exports){
// 22.1.3.3 Array.prototype.copyWithin(target, start, end = this.length)
var $export = require('./_export');

$export($export.P, 'Array', { copyWithin: require('./_array-copy-within') });

require('./_add-to-unscopables')('copyWithin');

},{"./_add-to-unscopables":16,"./_array-copy-within":20,"./_export":43}],136:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $every = require('./_array-methods')(4);

$export($export.P + $export.F * !require('./_strict-method')([].every, true), 'Array', {
  // 22.1.3.5 / 15.4.4.16 Array.prototype.every(callbackfn [, thisArg])
  every: function every(callbackfn /* , thisArg */) {
    return $every(this, callbackfn, arguments[1]);
  }
});

},{"./_array-methods":23,"./_export":43,"./_strict-method":109}],137:[function(require,module,exports){
// 22.1.3.6 Array.prototype.fill(value, start = 0, end = this.length)
var $export = require('./_export');

$export($export.P, 'Array', { fill: require('./_array-fill') });

require('./_add-to-unscopables')('fill');

},{"./_add-to-unscopables":16,"./_array-fill":21,"./_export":43}],138:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $filter = require('./_array-methods')(2);

$export($export.P + $export.F * !require('./_strict-method')([].filter, true), 'Array', {
  // 22.1.3.7 / 15.4.4.20 Array.prototype.filter(callbackfn [, thisArg])
  filter: function filter(callbackfn /* , thisArg */) {
    return $filter(this, callbackfn, arguments[1]);
  }
});

},{"./_array-methods":23,"./_export":43,"./_strict-method":109}],139:[function(require,module,exports){
'use strict';
// 22.1.3.9 Array.prototype.findIndex(predicate, thisArg = undefined)
var $export = require('./_export');
var $find = require('./_array-methods')(6);
var KEY = 'findIndex';
var forced = true;
// Shouldn't skip holes
if (KEY in []) Array(1)[KEY](function () { forced = false; });
$export($export.P + $export.F * forced, 'Array', {
  findIndex: function findIndex(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});
require('./_add-to-unscopables')(KEY);

},{"./_add-to-unscopables":16,"./_array-methods":23,"./_export":43}],140:[function(require,module,exports){
'use strict';
// 22.1.3.8 Array.prototype.find(predicate, thisArg = undefined)
var $export = require('./_export');
var $find = require('./_array-methods')(5);
var KEY = 'find';
var forced = true;
// Shouldn't skip holes
if (KEY in []) Array(1)[KEY](function () { forced = false; });
$export($export.P + $export.F * forced, 'Array', {
  find: function find(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});
require('./_add-to-unscopables')(KEY);

},{"./_add-to-unscopables":16,"./_array-methods":23,"./_export":43}],141:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $forEach = require('./_array-methods')(0);
var STRICT = require('./_strict-method')([].forEach, true);

$export($export.P + $export.F * !STRICT, 'Array', {
  // 22.1.3.10 / 15.4.4.18 Array.prototype.forEach(callbackfn [, thisArg])
  forEach: function forEach(callbackfn /* , thisArg */) {
    return $forEach(this, callbackfn, arguments[1]);
  }
});

},{"./_array-methods":23,"./_export":43,"./_strict-method":109}],142:[function(require,module,exports){
'use strict';
var ctx = require('./_ctx');
var $export = require('./_export');
var toObject = require('./_to-object');
var call = require('./_iter-call');
var isArrayIter = require('./_is-array-iter');
var toLength = require('./_to-length');
var createProperty = require('./_create-property');
var getIterFn = require('./core.get-iterator-method');

$export($export.S + $export.F * !require('./_iter-detect')(function (iter) { Array.from(iter); }), 'Array', {
  // 22.1.2.1 Array.from(arrayLike, mapfn = undefined, thisArg = undefined)
  from: function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
    var O = toObject(arrayLike);
    var C = typeof this == 'function' ? this : Array;
    var aLen = arguments.length;
    var mapfn = aLen > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    var index = 0;
    var iterFn = getIterFn(O);
    var length, result, step, iterator;
    if (mapping) mapfn = ctx(mapfn, aLen > 2 ? arguments[2] : undefined, 2);
    // if object isn't iterable or it's array with default iterator - use simple case
    if (iterFn != undefined && !(C == Array && isArrayIter(iterFn))) {
      for (iterator = iterFn.call(O), result = new C(); !(step = iterator.next()).done; index++) {
        createProperty(result, index, mapping ? call(iterator, mapfn, [step.value, index], true) : step.value);
      }
    } else {
      length = toLength(O.length);
      for (result = new C(length); length > index; index++) {
        createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
      }
    }
    result.length = index;
    return result;
  }
});

},{"./_create-property":34,"./_ctx":35,"./_export":43,"./_is-array-iter":59,"./_iter-call":64,"./_iter-detect":67,"./_to-length":122,"./_to-object":123,"./core.get-iterator-method":134}],143:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $indexOf = require('./_array-includes')(false);
var $native = [].indexOf;
var NEGATIVE_ZERO = !!$native && 1 / [1].indexOf(1, -0) < 0;

$export($export.P + $export.F * (NEGATIVE_ZERO || !require('./_strict-method')($native)), 'Array', {
  // 22.1.3.11 / 15.4.4.14 Array.prototype.indexOf(searchElement [, fromIndex])
  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
    return NEGATIVE_ZERO
      // convert -0 to +0
      ? $native.apply(this, arguments) || 0
      : $indexOf(this, searchElement, arguments[1]);
  }
});

},{"./_array-includes":22,"./_export":43,"./_strict-method":109}],144:[function(require,module,exports){
// 22.1.2.2 / 15.4.3.2 Array.isArray(arg)
var $export = require('./_export');

$export($export.S, 'Array', { isArray: require('./_is-array') });

},{"./_export":43,"./_is-array":60}],145:[function(require,module,exports){
'use strict';
var addToUnscopables = require('./_add-to-unscopables');
var step = require('./_iter-step');
var Iterators = require('./_iterators');
var toIObject = require('./_to-iobject');

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = require('./_iter-define')(Array, 'Array', function (iterated, kind) {
  this._t = toIObject(iterated); // target
  this._i = 0;                   // next index
  this._k = kind;                // kind
// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var kind = this._k;
  var index = this._i++;
  if (!O || index >= O.length) {
    this._t = undefined;
    return step(1);
  }
  if (kind == 'keys') return step(0, index);
  if (kind == 'values') return step(0, O[index]);
  return step(0, [index, O[index]]);
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
Iterators.Arguments = Iterators.Array;

addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');

},{"./_add-to-unscopables":16,"./_iter-define":66,"./_iter-step":68,"./_iterators":69,"./_to-iobject":121}],146:[function(require,module,exports){
'use strict';
// 22.1.3.13 Array.prototype.join(separator)
var $export = require('./_export');
var toIObject = require('./_to-iobject');
var arrayJoin = [].join;

// fallback for not array-like strings
$export($export.P + $export.F * (require('./_iobject') != Object || !require('./_strict-method')(arrayJoin)), 'Array', {
  join: function join(separator) {
    return arrayJoin.call(toIObject(this), separator === undefined ? ',' : separator);
  }
});

},{"./_export":43,"./_iobject":58,"./_strict-method":109,"./_to-iobject":121}],147:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var toIObject = require('./_to-iobject');
var toInteger = require('./_to-integer');
var toLength = require('./_to-length');
var $native = [].lastIndexOf;
var NEGATIVE_ZERO = !!$native && 1 / [1].lastIndexOf(1, -0) < 0;

$export($export.P + $export.F * (NEGATIVE_ZERO || !require('./_strict-method')($native)), 'Array', {
  // 22.1.3.14 / 15.4.4.15 Array.prototype.lastIndexOf(searchElement [, fromIndex])
  lastIndexOf: function lastIndexOf(searchElement /* , fromIndex = @[*-1] */) {
    // convert -0 to +0
    if (NEGATIVE_ZERO) return $native.apply(this, arguments) || 0;
    var O = toIObject(this);
    var length = toLength(O.length);
    var index = length - 1;
    if (arguments.length > 1) index = Math.min(index, toInteger(arguments[1]));
    if (index < 0) index = length + index;
    for (;index >= 0; index--) if (index in O) if (O[index] === searchElement) return index || 0;
    return -1;
  }
});

},{"./_export":43,"./_strict-method":109,"./_to-integer":120,"./_to-iobject":121,"./_to-length":122}],148:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $map = require('./_array-methods')(1);

$export($export.P + $export.F * !require('./_strict-method')([].map, true), 'Array', {
  // 22.1.3.15 / 15.4.4.19 Array.prototype.map(callbackfn [, thisArg])
  map: function map(callbackfn /* , thisArg */) {
    return $map(this, callbackfn, arguments[1]);
  }
});

},{"./_array-methods":23,"./_export":43,"./_strict-method":109}],149:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var createProperty = require('./_create-property');

// WebKit Array.of isn't generic
$export($export.S + $export.F * require('./_fails')(function () {
  function F() { /* empty */ }
  return !(Array.of.call(F) instanceof F);
}), 'Array', {
  // 22.1.2.3 Array.of( ...items)
  of: function of(/* ...args */) {
    var index = 0;
    var aLen = arguments.length;
    var result = new (typeof this == 'function' ? this : Array)(aLen);
    while (aLen > index) createProperty(result, index, arguments[index++]);
    result.length = aLen;
    return result;
  }
});

},{"./_create-property":34,"./_export":43,"./_fails":45}],150:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $reduce = require('./_array-reduce');

$export($export.P + $export.F * !require('./_strict-method')([].reduceRight, true), 'Array', {
  // 22.1.3.19 / 15.4.4.22 Array.prototype.reduceRight(callbackfn [, initialValue])
  reduceRight: function reduceRight(callbackfn /* , initialValue */) {
    return $reduce(this, callbackfn, arguments.length, arguments[1], true);
  }
});

},{"./_array-reduce":24,"./_export":43,"./_strict-method":109}],151:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $reduce = require('./_array-reduce');

$export($export.P + $export.F * !require('./_strict-method')([].reduce, true), 'Array', {
  // 22.1.3.18 / 15.4.4.21 Array.prototype.reduce(callbackfn [, initialValue])
  reduce: function reduce(callbackfn /* , initialValue */) {
    return $reduce(this, callbackfn, arguments.length, arguments[1], false);
  }
});

},{"./_array-reduce":24,"./_export":43,"./_strict-method":109}],152:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var html = require('./_html');
var cof = require('./_cof');
var toAbsoluteIndex = require('./_to-absolute-index');
var toLength = require('./_to-length');
var arraySlice = [].slice;

// fallback for not array-like ES3 strings and DOM objects
$export($export.P + $export.F * require('./_fails')(function () {
  if (html) arraySlice.call(html);
}), 'Array', {
  slice: function slice(begin, end) {
    var len = toLength(this.length);
    var klass = cof(this);
    end = end === undefined ? len : end;
    if (klass == 'Array') return arraySlice.call(this, begin, end);
    var start = toAbsoluteIndex(begin, len);
    var upTo = toAbsoluteIndex(end, len);
    var size = toLength(upTo - start);
    var cloned = new Array(size);
    var i = 0;
    for (; i < size; i++) cloned[i] = klass == 'String'
      ? this.charAt(start + i)
      : this[start + i];
    return cloned;
  }
});

},{"./_cof":29,"./_export":43,"./_fails":45,"./_html":54,"./_to-absolute-index":118,"./_to-length":122}],153:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $some = require('./_array-methods')(3);

$export($export.P + $export.F * !require('./_strict-method')([].some, true), 'Array', {
  // 22.1.3.23 / 15.4.4.17 Array.prototype.some(callbackfn [, thisArg])
  some: function some(callbackfn /* , thisArg */) {
    return $some(this, callbackfn, arguments[1]);
  }
});

},{"./_array-methods":23,"./_export":43,"./_strict-method":109}],154:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var aFunction = require('./_a-function');
var toObject = require('./_to-object');
var fails = require('./_fails');
var $sort = [].sort;
var test = [1, 2, 3];

$export($export.P + $export.F * (fails(function () {
  // IE8-
  test.sort(undefined);
}) || !fails(function () {
  // V8 bug
  test.sort(null);
  // Old WebKit
}) || !require('./_strict-method')($sort)), 'Array', {
  // 22.1.3.25 Array.prototype.sort(comparefn)
  sort: function sort(comparefn) {
    return comparefn === undefined
      ? $sort.call(toObject(this))
      : $sort.call(toObject(this), aFunction(comparefn));
  }
});

},{"./_a-function":14,"./_export":43,"./_fails":45,"./_strict-method":109,"./_to-object":123}],155:[function(require,module,exports){
require('./_set-species')('Array');

},{"./_set-species":104}],156:[function(require,module,exports){
// 20.3.3.1 / 15.9.4.4 Date.now()
var $export = require('./_export');

$export($export.S, 'Date', { now: function () { return new Date().getTime(); } });

},{"./_export":43}],157:[function(require,module,exports){
// 20.3.4.36 / 15.9.5.43 Date.prototype.toISOString()
var $export = require('./_export');
var toISOString = require('./_date-to-iso-string');

// PhantomJS / old WebKit has a broken implementations
$export($export.P + $export.F * (Date.prototype.toISOString !== toISOString), 'Date', {
  toISOString: toISOString
});

},{"./_date-to-iso-string":36,"./_export":43}],158:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var toObject = require('./_to-object');
var toPrimitive = require('./_to-primitive');

$export($export.P + $export.F * require('./_fails')(function () {
  return new Date(NaN).toJSON() !== null
    || Date.prototype.toJSON.call({ toISOString: function () { return 1; } }) !== 1;
}), 'Date', {
  // eslint-disable-next-line no-unused-vars
  toJSON: function toJSON(key) {
    var O = toObject(this);
    var pv = toPrimitive(O);
    return typeof pv == 'number' && !isFinite(pv) ? null : O.toISOString();
  }
});

},{"./_export":43,"./_fails":45,"./_to-object":123,"./_to-primitive":124}],159:[function(require,module,exports){
var TO_PRIMITIVE = require('./_wks')('toPrimitive');
var proto = Date.prototype;

if (!(TO_PRIMITIVE in proto)) require('./_hide')(proto, TO_PRIMITIVE, require('./_date-to-primitive'));

},{"./_date-to-primitive":37,"./_hide":53,"./_wks":133}],160:[function(require,module,exports){
var DateProto = Date.prototype;
var INVALID_DATE = 'Invalid Date';
var TO_STRING = 'toString';
var $toString = DateProto[TO_STRING];
var getTime = DateProto.getTime;
if (new Date(NaN) + '' != INVALID_DATE) {
  require('./_redefine')(DateProto, TO_STRING, function toString() {
    var value = getTime.call(this);
    // eslint-disable-next-line no-self-compare
    return value === value ? $toString.call(this) : INVALID_DATE;
  });
}

},{"./_redefine":99}],161:[function(require,module,exports){
// 19.2.3.2 / 15.3.4.5 Function.prototype.bind(thisArg, args...)
var $export = require('./_export');

$export($export.P, 'Function', { bind: require('./_bind') });

},{"./_bind":27,"./_export":43}],162:[function(require,module,exports){
'use strict';
var isObject = require('./_is-object');
var getPrototypeOf = require('./_object-gpo');
var HAS_INSTANCE = require('./_wks')('hasInstance');
var FunctionProto = Function.prototype;
// 19.2.3.6 Function.prototype[@@hasInstance](V)
if (!(HAS_INSTANCE in FunctionProto)) require('./_object-dp').f(FunctionProto, HAS_INSTANCE, { value: function (O) {
  if (typeof this != 'function' || !isObject(O)) return false;
  if (!isObject(this.prototype)) return O instanceof this;
  // for environment w/o native `@@hasInstance` logic enough `instanceof`, but add this:
  while (O = getPrototypeOf(O)) if (this.prototype === O) return true;
  return false;
} });

},{"./_is-object":62,"./_object-dp":80,"./_object-gpo":86,"./_wks":133}],163:[function(require,module,exports){
var dP = require('./_object-dp').f;
var FProto = Function.prototype;
var nameRE = /^\s*function ([^ (]*)/;
var NAME = 'name';

// 19.2.4.2 name
NAME in FProto || require('./_descriptors') && dP(FProto, NAME, {
  configurable: true,
  get: function () {
    try {
      return ('' + this).match(nameRE)[1];
    } catch (e) {
      return '';
    }
  }
});

},{"./_descriptors":39,"./_object-dp":80}],164:[function(require,module,exports){
'use strict';
var strong = require('./_collection-strong');
var validate = require('./_validate-collection');
var MAP = 'Map';

// 23.1 Map Objects
module.exports = require('./_collection')(MAP, function (get) {
  return function Map() { return get(this, arguments.length > 0 ? arguments[0] : undefined); };
}, {
  // 23.1.3.6 Map.prototype.get(key)
  get: function get(key) {
    var entry = strong.getEntry(validate(this, MAP), key);
    return entry && entry.v;
  },
  // 23.1.3.9 Map.prototype.set(key, value)
  set: function set(key, value) {
    return strong.def(validate(this, MAP), key === 0 ? 0 : key, value);
  }
}, strong, true);

},{"./_collection":32,"./_collection-strong":30,"./_validate-collection":130}],165:[function(require,module,exports){
// 20.2.2.3 Math.acosh(x)
var $export = require('./_export');
var log1p = require('./_math-log1p');
var sqrt = Math.sqrt;
var $acosh = Math.acosh;

$export($export.S + $export.F * !($acosh
  // V8 bug: https://code.google.com/p/v8/issues/detail?id=3509
  && Math.floor($acosh(Number.MAX_VALUE)) == 710
  // Tor Browser bug: Math.acosh(Infinity) -> NaN
  && $acosh(Infinity) == Infinity
), 'Math', {
  acosh: function acosh(x) {
    return (x = +x) < 1 ? NaN : x > 94906265.62425156
      ? Math.log(x) + Math.LN2
      : log1p(x - 1 + sqrt(x - 1) * sqrt(x + 1));
  }
});

},{"./_export":43,"./_math-log1p":73}],166:[function(require,module,exports){
// 20.2.2.5 Math.asinh(x)
var $export = require('./_export');
var $asinh = Math.asinh;

function asinh(x) {
  return !isFinite(x = +x) || x == 0 ? x : x < 0 ? -asinh(-x) : Math.log(x + Math.sqrt(x * x + 1));
}

// Tor Browser bug: Math.asinh(0) -> -0
$export($export.S + $export.F * !($asinh && 1 / $asinh(0) > 0), 'Math', { asinh: asinh });

},{"./_export":43}],167:[function(require,module,exports){
// 20.2.2.7 Math.atanh(x)
var $export = require('./_export');
var $atanh = Math.atanh;

// Tor Browser bug: Math.atanh(-0) -> 0
$export($export.S + $export.F * !($atanh && 1 / $atanh(-0) < 0), 'Math', {
  atanh: function atanh(x) {
    return (x = +x) == 0 ? x : Math.log((1 + x) / (1 - x)) / 2;
  }
});

},{"./_export":43}],168:[function(require,module,exports){
// 20.2.2.9 Math.cbrt(x)
var $export = require('./_export');
var sign = require('./_math-sign');

$export($export.S, 'Math', {
  cbrt: function cbrt(x) {
    return sign(x = +x) * Math.pow(Math.abs(x), 1 / 3);
  }
});

},{"./_export":43,"./_math-sign":74}],169:[function(require,module,exports){
// 20.2.2.11 Math.clz32(x)
var $export = require('./_export');

$export($export.S, 'Math', {
  clz32: function clz32(x) {
    return (x >>>= 0) ? 31 - Math.floor(Math.log(x + 0.5) * Math.LOG2E) : 32;
  }
});

},{"./_export":43}],170:[function(require,module,exports){
// 20.2.2.12 Math.cosh(x)
var $export = require('./_export');
var exp = Math.exp;

$export($export.S, 'Math', {
  cosh: function cosh(x) {
    return (exp(x = +x) + exp(-x)) / 2;
  }
});

},{"./_export":43}],171:[function(require,module,exports){
// 20.2.2.14 Math.expm1(x)
var $export = require('./_export');
var $expm1 = require('./_math-expm1');

$export($export.S + $export.F * ($expm1 != Math.expm1), 'Math', { expm1: $expm1 });

},{"./_export":43,"./_math-expm1":71}],172:[function(require,module,exports){
// 20.2.2.16 Math.fround(x)
var $export = require('./_export');

$export($export.S, 'Math', { fround: require('./_math-fround') });

},{"./_export":43,"./_math-fround":72}],173:[function(require,module,exports){
// 20.2.2.17 Math.hypot([value1[, value2[, … ]]])
var $export = require('./_export');
var abs = Math.abs;

$export($export.S, 'Math', {
  hypot: function hypot(value1, value2) { // eslint-disable-line no-unused-vars
    var sum = 0;
    var i = 0;
    var aLen = arguments.length;
    var larg = 0;
    var arg, div;
    while (i < aLen) {
      arg = abs(arguments[i++]);
      if (larg < arg) {
        div = larg / arg;
        sum = sum * div * div + 1;
        larg = arg;
      } else if (arg > 0) {
        div = arg / larg;
        sum += div * div;
      } else sum += arg;
    }
    return larg === Infinity ? Infinity : larg * Math.sqrt(sum);
  }
});

},{"./_export":43}],174:[function(require,module,exports){
// 20.2.2.18 Math.imul(x, y)
var $export = require('./_export');
var $imul = Math.imul;

// some WebKit versions fails with big numbers, some has wrong arity
$export($export.S + $export.F * require('./_fails')(function () {
  return $imul(0xffffffff, 5) != -5 || $imul.length != 2;
}), 'Math', {
  imul: function imul(x, y) {
    var UINT16 = 0xffff;
    var xn = +x;
    var yn = +y;
    var xl = UINT16 & xn;
    var yl = UINT16 & yn;
    return 0 | xl * yl + ((UINT16 & xn >>> 16) * yl + xl * (UINT16 & yn >>> 16) << 16 >>> 0);
  }
});

},{"./_export":43,"./_fails":45}],175:[function(require,module,exports){
// 20.2.2.21 Math.log10(x)
var $export = require('./_export');

$export($export.S, 'Math', {
  log10: function log10(x) {
    return Math.log(x) * Math.LOG10E;
  }
});

},{"./_export":43}],176:[function(require,module,exports){
// 20.2.2.20 Math.log1p(x)
var $export = require('./_export');

$export($export.S, 'Math', { log1p: require('./_math-log1p') });

},{"./_export":43,"./_math-log1p":73}],177:[function(require,module,exports){
// 20.2.2.22 Math.log2(x)
var $export = require('./_export');

$export($export.S, 'Math', {
  log2: function log2(x) {
    return Math.log(x) / Math.LN2;
  }
});

},{"./_export":43}],178:[function(require,module,exports){
// 20.2.2.28 Math.sign(x)
var $export = require('./_export');

$export($export.S, 'Math', { sign: require('./_math-sign') });

},{"./_export":43,"./_math-sign":74}],179:[function(require,module,exports){
// 20.2.2.30 Math.sinh(x)
var $export = require('./_export');
var expm1 = require('./_math-expm1');
var exp = Math.exp;

// V8 near Chromium 38 has a problem with very small numbers
$export($export.S + $export.F * require('./_fails')(function () {
  return !Math.sinh(-2e-17) != -2e-17;
}), 'Math', {
  sinh: function sinh(x) {
    return Math.abs(x = +x) < 1
      ? (expm1(x) - expm1(-x)) / 2
      : (exp(x - 1) - exp(-x - 1)) * (Math.E / 2);
  }
});

},{"./_export":43,"./_fails":45,"./_math-expm1":71}],180:[function(require,module,exports){
// 20.2.2.33 Math.tanh(x)
var $export = require('./_export');
var expm1 = require('./_math-expm1');
var exp = Math.exp;

$export($export.S, 'Math', {
  tanh: function tanh(x) {
    var a = expm1(x = +x);
    var b = expm1(-x);
    return a == Infinity ? 1 : b == Infinity ? -1 : (a - b) / (exp(x) + exp(-x));
  }
});

},{"./_export":43,"./_math-expm1":71}],181:[function(require,module,exports){
// 20.2.2.34 Math.trunc(x)
var $export = require('./_export');

$export($export.S, 'Math', {
  trunc: function trunc(it) {
    return (it > 0 ? Math.floor : Math.ceil)(it);
  }
});

},{"./_export":43}],182:[function(require,module,exports){
'use strict';
var global = require('./_global');
var has = require('./_has');
var cof = require('./_cof');
var inheritIfRequired = require('./_inherit-if-required');
var toPrimitive = require('./_to-primitive');
var fails = require('./_fails');
var gOPN = require('./_object-gopn').f;
var gOPD = require('./_object-gopd').f;
var dP = require('./_object-dp').f;
var $trim = require('./_string-trim').trim;
var NUMBER = 'Number';
var $Number = global[NUMBER];
var Base = $Number;
var proto = $Number.prototype;
// Opera ~12 has broken Object#toString
var BROKEN_COF = cof(require('./_object-create')(proto)) == NUMBER;
var TRIM = 'trim' in String.prototype;

// 7.1.3 ToNumber(argument)
var toNumber = function (argument) {
  var it = toPrimitive(argument, false);
  if (typeof it == 'string' && it.length > 2) {
    it = TRIM ? it.trim() : $trim(it, 3);
    var first = it.charCodeAt(0);
    var third, radix, maxCode;
    if (first === 43 || first === 45) {
      third = it.charCodeAt(2);
      if (third === 88 || third === 120) return NaN; // Number('+0x1') should be NaN, old V8 fix
    } else if (first === 48) {
      switch (it.charCodeAt(1)) {
        case 66: case 98: radix = 2; maxCode = 49; break; // fast equal /^0b[01]+$/i
        case 79: case 111: radix = 8; maxCode = 55; break; // fast equal /^0o[0-7]+$/i
        default: return +it;
      }
      for (var digits = it.slice(2), i = 0, l = digits.length, code; i < l; i++) {
        code = digits.charCodeAt(i);
        // parseInt parses a string to a first unavailable symbol
        // but ToNumber should return NaN if a string contains unavailable symbols
        if (code < 48 || code > maxCode) return NaN;
      } return parseInt(digits, radix);
    }
  } return +it;
};

if (!$Number(' 0o1') || !$Number('0b1') || $Number('+0x1')) {
  $Number = function Number(value) {
    var it = arguments.length < 1 ? 0 : value;
    var that = this;
    return that instanceof $Number
      // check on 1..constructor(foo) case
      && (BROKEN_COF ? fails(function () { proto.valueOf.call(that); }) : cof(that) != NUMBER)
        ? inheritIfRequired(new Base(toNumber(it)), that, $Number) : toNumber(it);
  };
  for (var keys = require('./_descriptors') ? gOPN(Base) : (
    // ES3:
    'MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,' +
    // ES6 (in case, if modules with ES6 Number statics required before):
    'EPSILON,isFinite,isInteger,isNaN,isSafeInteger,MAX_SAFE_INTEGER,' +
    'MIN_SAFE_INTEGER,parseFloat,parseInt,isInteger'
  ).split(','), j = 0, key; keys.length > j; j++) {
    if (has(Base, key = keys[j]) && !has($Number, key)) {
      dP($Number, key, gOPD(Base, key));
    }
  }
  $Number.prototype = proto;
  proto.constructor = $Number;
  require('./_redefine')(global, NUMBER, $Number);
}

},{"./_cof":29,"./_descriptors":39,"./_fails":45,"./_global":51,"./_has":52,"./_inherit-if-required":56,"./_object-create":79,"./_object-dp":80,"./_object-gopd":82,"./_object-gopn":84,"./_redefine":99,"./_string-trim":115,"./_to-primitive":124}],183:[function(require,module,exports){
// 20.1.2.1 Number.EPSILON
var $export = require('./_export');

$export($export.S, 'Number', { EPSILON: Math.pow(2, -52) });

},{"./_export":43}],184:[function(require,module,exports){
// 20.1.2.2 Number.isFinite(number)
var $export = require('./_export');
var _isFinite = require('./_global').isFinite;

$export($export.S, 'Number', {
  isFinite: function isFinite(it) {
    return typeof it == 'number' && _isFinite(it);
  }
});

},{"./_export":43,"./_global":51}],185:[function(require,module,exports){
// 20.1.2.3 Number.isInteger(number)
var $export = require('./_export');

$export($export.S, 'Number', { isInteger: require('./_is-integer') });

},{"./_export":43,"./_is-integer":61}],186:[function(require,module,exports){
// 20.1.2.4 Number.isNaN(number)
var $export = require('./_export');

$export($export.S, 'Number', {
  isNaN: function isNaN(number) {
    // eslint-disable-next-line no-self-compare
    return number != number;
  }
});

},{"./_export":43}],187:[function(require,module,exports){
// 20.1.2.5 Number.isSafeInteger(number)
var $export = require('./_export');
var isInteger = require('./_is-integer');
var abs = Math.abs;

$export($export.S, 'Number', {
  isSafeInteger: function isSafeInteger(number) {
    return isInteger(number) && abs(number) <= 0x1fffffffffffff;
  }
});

},{"./_export":43,"./_is-integer":61}],188:[function(require,module,exports){
// 20.1.2.6 Number.MAX_SAFE_INTEGER
var $export = require('./_export');

$export($export.S, 'Number', { MAX_SAFE_INTEGER: 0x1fffffffffffff });

},{"./_export":43}],189:[function(require,module,exports){
// 20.1.2.10 Number.MIN_SAFE_INTEGER
var $export = require('./_export');

$export($export.S, 'Number', { MIN_SAFE_INTEGER: -0x1fffffffffffff });

},{"./_export":43}],190:[function(require,module,exports){
var $export = require('./_export');
var $parseFloat = require('./_parse-float');
// 20.1.2.12 Number.parseFloat(string)
$export($export.S + $export.F * (Number.parseFloat != $parseFloat), 'Number', { parseFloat: $parseFloat });

},{"./_export":43,"./_parse-float":93}],191:[function(require,module,exports){
var $export = require('./_export');
var $parseInt = require('./_parse-int');
// 20.1.2.13 Number.parseInt(string, radix)
$export($export.S + $export.F * (Number.parseInt != $parseInt), 'Number', { parseInt: $parseInt });

},{"./_export":43,"./_parse-int":94}],192:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var toInteger = require('./_to-integer');
var aNumberValue = require('./_a-number-value');
var repeat = require('./_string-repeat');
var $toFixed = 1.0.toFixed;
var floor = Math.floor;
var data = [0, 0, 0, 0, 0, 0];
var ERROR = 'Number.toFixed: incorrect invocation!';
var ZERO = '0';

var multiply = function (n, c) {
  var i = -1;
  var c2 = c;
  while (++i < 6) {
    c2 += n * data[i];
    data[i] = c2 % 1e7;
    c2 = floor(c2 / 1e7);
  }
};
var divide = function (n) {
  var i = 6;
  var c = 0;
  while (--i >= 0) {
    c += data[i];
    data[i] = floor(c / n);
    c = (c % n) * 1e7;
  }
};
var numToString = function () {
  var i = 6;
  var s = '';
  while (--i >= 0) {
    if (s !== '' || i === 0 || data[i] !== 0) {
      var t = String(data[i]);
      s = s === '' ? t : s + repeat.call(ZERO, 7 - t.length) + t;
    }
  } return s;
};
var pow = function (x, n, acc) {
  return n === 0 ? acc : n % 2 === 1 ? pow(x, n - 1, acc * x) : pow(x * x, n / 2, acc);
};
var log = function (x) {
  var n = 0;
  var x2 = x;
  while (x2 >= 4096) {
    n += 12;
    x2 /= 4096;
  }
  while (x2 >= 2) {
    n += 1;
    x2 /= 2;
  } return n;
};

$export($export.P + $export.F * (!!$toFixed && (
  0.00008.toFixed(3) !== '0.000' ||
  0.9.toFixed(0) !== '1' ||
  1.255.toFixed(2) !== '1.25' ||
  1000000000000000128.0.toFixed(0) !== '1000000000000000128'
) || !require('./_fails')(function () {
  // V8 ~ Android 4.3-
  $toFixed.call({});
})), 'Number', {
  toFixed: function toFixed(fractionDigits) {
    var x = aNumberValue(this, ERROR);
    var f = toInteger(fractionDigits);
    var s = '';
    var m = ZERO;
    var e, z, j, k;
    if (f < 0 || f > 20) throw RangeError(ERROR);
    // eslint-disable-next-line no-self-compare
    if (x != x) return 'NaN';
    if (x <= -1e21 || x >= 1e21) return String(x);
    if (x < 0) {
      s = '-';
      x = -x;
    }
    if (x > 1e-21) {
      e = log(x * pow(2, 69, 1)) - 69;
      z = e < 0 ? x * pow(2, -e, 1) : x / pow(2, e, 1);
      z *= 0x10000000000000;
      e = 52 - e;
      if (e > 0) {
        multiply(0, z);
        j = f;
        while (j >= 7) {
          multiply(1e7, 0);
          j -= 7;
        }
        multiply(pow(10, j, 1), 0);
        j = e - 1;
        while (j >= 23) {
          divide(1 << 23);
          j -= 23;
        }
        divide(1 << j);
        multiply(1, 1);
        divide(2);
        m = numToString();
      } else {
        multiply(0, z);
        multiply(1 << -e, 0);
        m = numToString() + repeat.call(ZERO, f);
      }
    }
    if (f > 0) {
      k = m.length;
      m = s + (k <= f ? '0.' + repeat.call(ZERO, f - k) + m : m.slice(0, k - f) + '.' + m.slice(k - f));
    } else {
      m = s + m;
    } return m;
  }
});

},{"./_a-number-value":15,"./_export":43,"./_fails":45,"./_string-repeat":114,"./_to-integer":120}],193:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $fails = require('./_fails');
var aNumberValue = require('./_a-number-value');
var $toPrecision = 1.0.toPrecision;

$export($export.P + $export.F * ($fails(function () {
  // IE7-
  return $toPrecision.call(1, undefined) !== '1';
}) || !$fails(function () {
  // V8 ~ Android 4.3-
  $toPrecision.call({});
})), 'Number', {
  toPrecision: function toPrecision(precision) {
    var that = aNumberValue(this, 'Number#toPrecision: incorrect invocation!');
    return precision === undefined ? $toPrecision.call(that) : $toPrecision.call(that, precision);
  }
});

},{"./_a-number-value":15,"./_export":43,"./_fails":45}],194:[function(require,module,exports){
// 19.1.3.1 Object.assign(target, source)
var $export = require('./_export');

$export($export.S + $export.F, 'Object', { assign: require('./_object-assign') });

},{"./_export":43,"./_object-assign":78}],195:[function(require,module,exports){
var $export = require('./_export');
// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
$export($export.S, 'Object', { create: require('./_object-create') });

},{"./_export":43,"./_object-create":79}],196:[function(require,module,exports){
var $export = require('./_export');
// 19.1.2.3 / 15.2.3.7 Object.defineProperties(O, Properties)
$export($export.S + $export.F * !require('./_descriptors'), 'Object', { defineProperties: require('./_object-dps') });

},{"./_descriptors":39,"./_export":43,"./_object-dps":81}],197:[function(require,module,exports){
var $export = require('./_export');
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !require('./_descriptors'), 'Object', { defineProperty: require('./_object-dp').f });

},{"./_descriptors":39,"./_export":43,"./_object-dp":80}],198:[function(require,module,exports){
// 19.1.2.5 Object.freeze(O)
var isObject = require('./_is-object');
var meta = require('./_meta').onFreeze;

require('./_object-sap')('freeze', function ($freeze) {
  return function freeze(it) {
    return $freeze && isObject(it) ? $freeze(meta(it)) : it;
  };
});

},{"./_is-object":62,"./_meta":75,"./_object-sap":90}],199:[function(require,module,exports){
// 19.1.2.6 Object.getOwnPropertyDescriptor(O, P)
var toIObject = require('./_to-iobject');
var $getOwnPropertyDescriptor = require('./_object-gopd').f;

require('./_object-sap')('getOwnPropertyDescriptor', function () {
  return function getOwnPropertyDescriptor(it, key) {
    return $getOwnPropertyDescriptor(toIObject(it), key);
  };
});

},{"./_object-gopd":82,"./_object-sap":90,"./_to-iobject":121}],200:[function(require,module,exports){
// 19.1.2.7 Object.getOwnPropertyNames(O)
require('./_object-sap')('getOwnPropertyNames', function () {
  return require('./_object-gopn-ext').f;
});

},{"./_object-gopn-ext":83,"./_object-sap":90}],201:[function(require,module,exports){
// 19.1.2.9 Object.getPrototypeOf(O)
var toObject = require('./_to-object');
var $getPrototypeOf = require('./_object-gpo');

require('./_object-sap')('getPrototypeOf', function () {
  return function getPrototypeOf(it) {
    return $getPrototypeOf(toObject(it));
  };
});

},{"./_object-gpo":86,"./_object-sap":90,"./_to-object":123}],202:[function(require,module,exports){
// 19.1.2.11 Object.isExtensible(O)
var isObject = require('./_is-object');

require('./_object-sap')('isExtensible', function ($isExtensible) {
  return function isExtensible(it) {
    return isObject(it) ? $isExtensible ? $isExtensible(it) : true : false;
  };
});

},{"./_is-object":62,"./_object-sap":90}],203:[function(require,module,exports){
// 19.1.2.12 Object.isFrozen(O)
var isObject = require('./_is-object');

require('./_object-sap')('isFrozen', function ($isFrozen) {
  return function isFrozen(it) {
    return isObject(it) ? $isFrozen ? $isFrozen(it) : false : true;
  };
});

},{"./_is-object":62,"./_object-sap":90}],204:[function(require,module,exports){
// 19.1.2.13 Object.isSealed(O)
var isObject = require('./_is-object');

require('./_object-sap')('isSealed', function ($isSealed) {
  return function isSealed(it) {
    return isObject(it) ? $isSealed ? $isSealed(it) : false : true;
  };
});

},{"./_is-object":62,"./_object-sap":90}],205:[function(require,module,exports){
// 19.1.3.10 Object.is(value1, value2)
var $export = require('./_export');
$export($export.S, 'Object', { is: require('./_same-value') });

},{"./_export":43,"./_same-value":102}],206:[function(require,module,exports){
// 19.1.2.14 Object.keys(O)
var toObject = require('./_to-object');
var $keys = require('./_object-keys');

require('./_object-sap')('keys', function () {
  return function keys(it) {
    return $keys(toObject(it));
  };
});

},{"./_object-keys":88,"./_object-sap":90,"./_to-object":123}],207:[function(require,module,exports){
// 19.1.2.15 Object.preventExtensions(O)
var isObject = require('./_is-object');
var meta = require('./_meta').onFreeze;

require('./_object-sap')('preventExtensions', function ($preventExtensions) {
  return function preventExtensions(it) {
    return $preventExtensions && isObject(it) ? $preventExtensions(meta(it)) : it;
  };
});

},{"./_is-object":62,"./_meta":75,"./_object-sap":90}],208:[function(require,module,exports){
// 19.1.2.17 Object.seal(O)
var isObject = require('./_is-object');
var meta = require('./_meta').onFreeze;

require('./_object-sap')('seal', function ($seal) {
  return function seal(it) {
    return $seal && isObject(it) ? $seal(meta(it)) : it;
  };
});

},{"./_is-object":62,"./_meta":75,"./_object-sap":90}],209:[function(require,module,exports){
// 19.1.3.19 Object.setPrototypeOf(O, proto)
var $export = require('./_export');
$export($export.S, 'Object', { setPrototypeOf: require('./_set-proto').set });

},{"./_export":43,"./_set-proto":103}],210:[function(require,module,exports){
'use strict';
// 19.1.3.6 Object.prototype.toString()
var classof = require('./_classof');
var test = {};
test[require('./_wks')('toStringTag')] = 'z';
if (test + '' != '[object z]') {
  require('./_redefine')(Object.prototype, 'toString', function toString() {
    return '[object ' + classof(this) + ']';
  }, true);
}

},{"./_classof":28,"./_redefine":99,"./_wks":133}],211:[function(require,module,exports){
var $export = require('./_export');
var $parseFloat = require('./_parse-float');
// 18.2.4 parseFloat(string)
$export($export.G + $export.F * (parseFloat != $parseFloat), { parseFloat: $parseFloat });

},{"./_export":43,"./_parse-float":93}],212:[function(require,module,exports){
var $export = require('./_export');
var $parseInt = require('./_parse-int');
// 18.2.5 parseInt(string, radix)
$export($export.G + $export.F * (parseInt != $parseInt), { parseInt: $parseInt });

},{"./_export":43,"./_parse-int":94}],213:[function(require,module,exports){
'use strict';
var LIBRARY = require('./_library');
var global = require('./_global');
var ctx = require('./_ctx');
var classof = require('./_classof');
var $export = require('./_export');
var isObject = require('./_is-object');
var aFunction = require('./_a-function');
var anInstance = require('./_an-instance');
var forOf = require('./_for-of');
var speciesConstructor = require('./_species-constructor');
var task = require('./_task').set;
var microtask = require('./_microtask')();
var newPromiseCapabilityModule = require('./_new-promise-capability');
var perform = require('./_perform');
var userAgent = require('./_user-agent');
var promiseResolve = require('./_promise-resolve');
var PROMISE = 'Promise';
var TypeError = global.TypeError;
var process = global.process;
var versions = process && process.versions;
var v8 = versions && versions.v8 || '';
var $Promise = global[PROMISE];
var isNode = classof(process) == 'process';
var empty = function () { /* empty */ };
var Internal, newGenericPromiseCapability, OwnPromiseCapability, Wrapper;
var newPromiseCapability = newGenericPromiseCapability = newPromiseCapabilityModule.f;

var USE_NATIVE = !!function () {
  try {
    // correct subclassing with @@species support
    var promise = $Promise.resolve(1);
    var FakePromise = (promise.constructor = {})[require('./_wks')('species')] = function (exec) {
      exec(empty, empty);
    };
    // unhandled rejections tracking support, NodeJS Promise without it fails @@species test
    return (isNode || typeof PromiseRejectionEvent == 'function')
      && promise.then(empty) instanceof FakePromise
      // v8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
      // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
      // we can't detect it synchronously, so just check versions
      && v8.indexOf('6.6') !== 0
      && userAgent.indexOf('Chrome/66') === -1;
  } catch (e) { /* empty */ }
}();

// helpers
var isThenable = function (it) {
  var then;
  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
};
var notify = function (promise, isReject) {
  if (promise._n) return;
  promise._n = true;
  var chain = promise._c;
  microtask(function () {
    var value = promise._v;
    var ok = promise._s == 1;
    var i = 0;
    var run = function (reaction) {
      var handler = ok ? reaction.ok : reaction.fail;
      var resolve = reaction.resolve;
      var reject = reaction.reject;
      var domain = reaction.domain;
      var result, then, exited;
      try {
        if (handler) {
          if (!ok) {
            if (promise._h == 2) onHandleUnhandled(promise);
            promise._h = 1;
          }
          if (handler === true) result = value;
          else {
            if (domain) domain.enter();
            result = handler(value); // may throw
            if (domain) {
              domain.exit();
              exited = true;
            }
          }
          if (result === reaction.promise) {
            reject(TypeError('Promise-chain cycle'));
          } else if (then = isThenable(result)) {
            then.call(result, resolve, reject);
          } else resolve(result);
        } else reject(value);
      } catch (e) {
        if (domain && !exited) domain.exit();
        reject(e);
      }
    };
    while (chain.length > i) run(chain[i++]); // variable length - can't use forEach
    promise._c = [];
    promise._n = false;
    if (isReject && !promise._h) onUnhandled(promise);
  });
};
var onUnhandled = function (promise) {
  task.call(global, function () {
    var value = promise._v;
    var unhandled = isUnhandled(promise);
    var result, handler, console;
    if (unhandled) {
      result = perform(function () {
        if (isNode) {
          process.emit('unhandledRejection', value, promise);
        } else if (handler = global.onunhandledrejection) {
          handler({ promise: promise, reason: value });
        } else if ((console = global.console) && console.error) {
          console.error('Unhandled promise rejection', value);
        }
      });
      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
      promise._h = isNode || isUnhandled(promise) ? 2 : 1;
    } promise._a = undefined;
    if (unhandled && result.e) throw result.v;
  });
};
var isUnhandled = function (promise) {
  return promise._h !== 1 && (promise._a || promise._c).length === 0;
};
var onHandleUnhandled = function (promise) {
  task.call(global, function () {
    var handler;
    if (isNode) {
      process.emit('rejectionHandled', promise);
    } else if (handler = global.onrejectionhandled) {
      handler({ promise: promise, reason: promise._v });
    }
  });
};
var $reject = function (value) {
  var promise = this;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  promise._v = value;
  promise._s = 2;
  if (!promise._a) promise._a = promise._c.slice();
  notify(promise, true);
};
var $resolve = function (value) {
  var promise = this;
  var then;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  try {
    if (promise === value) throw TypeError("Promise can't be resolved itself");
    if (then = isThenable(value)) {
      microtask(function () {
        var wrapper = { _w: promise, _d: false }; // wrap
        try {
          then.call(value, ctx($resolve, wrapper, 1), ctx($reject, wrapper, 1));
        } catch (e) {
          $reject.call(wrapper, e);
        }
      });
    } else {
      promise._v = value;
      promise._s = 1;
      notify(promise, false);
    }
  } catch (e) {
    $reject.call({ _w: promise, _d: false }, e); // wrap
  }
};

// constructor polyfill
if (!USE_NATIVE) {
  // 25.4.3.1 Promise(executor)
  $Promise = function Promise(executor) {
    anInstance(this, $Promise, PROMISE, '_h');
    aFunction(executor);
    Internal.call(this);
    try {
      executor(ctx($resolve, this, 1), ctx($reject, this, 1));
    } catch (err) {
      $reject.call(this, err);
    }
  };
  // eslint-disable-next-line no-unused-vars
  Internal = function Promise(executor) {
    this._c = [];             // <- awaiting reactions
    this._a = undefined;      // <- checked in isUnhandled reactions
    this._s = 0;              // <- state
    this._d = false;          // <- done
    this._v = undefined;      // <- value
    this._h = 0;              // <- rejection state, 0 - default, 1 - handled, 2 - unhandled
    this._n = false;          // <- notify
  };
  Internal.prototype = require('./_redefine-all')($Promise.prototype, {
    // 25.4.5.3 Promise.prototype.then(onFulfilled, onRejected)
    then: function then(onFulfilled, onRejected) {
      var reaction = newPromiseCapability(speciesConstructor(this, $Promise));
      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
      reaction.fail = typeof onRejected == 'function' && onRejected;
      reaction.domain = isNode ? process.domain : undefined;
      this._c.push(reaction);
      if (this._a) this._a.push(reaction);
      if (this._s) notify(this, false);
      return reaction.promise;
    },
    // 25.4.5.1 Promise.prototype.catch(onRejected)
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });
  OwnPromiseCapability = function () {
    var promise = new Internal();
    this.promise = promise;
    this.resolve = ctx($resolve, promise, 1);
    this.reject = ctx($reject, promise, 1);
  };
  newPromiseCapabilityModule.f = newPromiseCapability = function (C) {
    return C === $Promise || C === Wrapper
      ? new OwnPromiseCapability(C)
      : newGenericPromiseCapability(C);
  };
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, { Promise: $Promise });
require('./_set-to-string-tag')($Promise, PROMISE);
require('./_set-species')(PROMISE);
Wrapper = require('./_core')[PROMISE];

// statics
$export($export.S + $export.F * !USE_NATIVE, PROMISE, {
  // 25.4.4.5 Promise.reject(r)
  reject: function reject(r) {
    var capability = newPromiseCapability(this);
    var $$reject = capability.reject;
    $$reject(r);
    return capability.promise;
  }
});
$export($export.S + $export.F * (LIBRARY || !USE_NATIVE), PROMISE, {
  // 25.4.4.6 Promise.resolve(x)
  resolve: function resolve(x) {
    return promiseResolve(LIBRARY && this === Wrapper ? $Promise : this, x);
  }
});
$export($export.S + $export.F * !(USE_NATIVE && require('./_iter-detect')(function (iter) {
  $Promise.all(iter)['catch'](empty);
})), PROMISE, {
  // 25.4.4.1 Promise.all(iterable)
  all: function all(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var resolve = capability.resolve;
    var reject = capability.reject;
    var result = perform(function () {
      var values = [];
      var index = 0;
      var remaining = 1;
      forOf(iterable, false, function (promise) {
        var $index = index++;
        var alreadyCalled = false;
        values.push(undefined);
        remaining++;
        C.resolve(promise).then(function (value) {
          if (alreadyCalled) return;
          alreadyCalled = true;
          values[$index] = value;
          --remaining || resolve(values);
        }, reject);
      });
      --remaining || resolve(values);
    });
    if (result.e) reject(result.v);
    return capability.promise;
  },
  // 25.4.4.4 Promise.race(iterable)
  race: function race(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var reject = capability.reject;
    var result = perform(function () {
      forOf(iterable, false, function (promise) {
        C.resolve(promise).then(capability.resolve, reject);
      });
    });
    if (result.e) reject(result.v);
    return capability.promise;
  }
});

},{"./_a-function":14,"./_an-instance":18,"./_classof":28,"./_core":33,"./_ctx":35,"./_export":43,"./_for-of":49,"./_global":51,"./_is-object":62,"./_iter-detect":67,"./_library":70,"./_microtask":76,"./_new-promise-capability":77,"./_perform":95,"./_promise-resolve":96,"./_redefine-all":98,"./_set-species":104,"./_set-to-string-tag":105,"./_species-constructor":108,"./_task":117,"./_user-agent":129,"./_wks":133}],214:[function(require,module,exports){
// 26.1.1 Reflect.apply(target, thisArgument, argumentsList)
var $export = require('./_export');
var aFunction = require('./_a-function');
var anObject = require('./_an-object');
var rApply = (require('./_global').Reflect || {}).apply;
var fApply = Function.apply;
// MS Edge argumentsList argument is optional
$export($export.S + $export.F * !require('./_fails')(function () {
  rApply(function () { /* empty */ });
}), 'Reflect', {
  apply: function apply(target, thisArgument, argumentsList) {
    var T = aFunction(target);
    var L = anObject(argumentsList);
    return rApply ? rApply(T, thisArgument, L) : fApply.call(T, thisArgument, L);
  }
});

},{"./_a-function":14,"./_an-object":19,"./_export":43,"./_fails":45,"./_global":51}],215:[function(require,module,exports){
// 26.1.2 Reflect.construct(target, argumentsList [, newTarget])
var $export = require('./_export');
var create = require('./_object-create');
var aFunction = require('./_a-function');
var anObject = require('./_an-object');
var isObject = require('./_is-object');
var fails = require('./_fails');
var bind = require('./_bind');
var rConstruct = (require('./_global').Reflect || {}).construct;

// MS Edge supports only 2 arguments and argumentsList argument is optional
// FF Nightly sets third argument as `new.target`, but does not create `this` from it
var NEW_TARGET_BUG = fails(function () {
  function F() { /* empty */ }
  return !(rConstruct(function () { /* empty */ }, [], F) instanceof F);
});
var ARGS_BUG = !fails(function () {
  rConstruct(function () { /* empty */ });
});

$export($export.S + $export.F * (NEW_TARGET_BUG || ARGS_BUG), 'Reflect', {
  construct: function construct(Target, args /* , newTarget */) {
    aFunction(Target);
    anObject(args);
    var newTarget = arguments.length < 3 ? Target : aFunction(arguments[2]);
    if (ARGS_BUG && !NEW_TARGET_BUG) return rConstruct(Target, args, newTarget);
    if (Target == newTarget) {
      // w/o altered newTarget, optimization for 0-4 arguments
      switch (args.length) {
        case 0: return new Target();
        case 1: return new Target(args[0]);
        case 2: return new Target(args[0], args[1]);
        case 3: return new Target(args[0], args[1], args[2]);
        case 4: return new Target(args[0], args[1], args[2], args[3]);
      }
      // w/o altered newTarget, lot of arguments case
      var $args = [null];
      $args.push.apply($args, args);
      return new (bind.apply(Target, $args))();
    }
    // with altered newTarget, not support built-in constructors
    var proto = newTarget.prototype;
    var instance = create(isObject(proto) ? proto : Object.prototype);
    var result = Function.apply.call(Target, instance, args);
    return isObject(result) ? result : instance;
  }
});

},{"./_a-function":14,"./_an-object":19,"./_bind":27,"./_export":43,"./_fails":45,"./_global":51,"./_is-object":62,"./_object-create":79}],216:[function(require,module,exports){
// 26.1.3 Reflect.defineProperty(target, propertyKey, attributes)
var dP = require('./_object-dp');
var $export = require('./_export');
var anObject = require('./_an-object');
var toPrimitive = require('./_to-primitive');

// MS Edge has broken Reflect.defineProperty - throwing instead of returning false
$export($export.S + $export.F * require('./_fails')(function () {
  // eslint-disable-next-line no-undef
  Reflect.defineProperty(dP.f({}, 1, { value: 1 }), 1, { value: 2 });
}), 'Reflect', {
  defineProperty: function defineProperty(target, propertyKey, attributes) {
    anObject(target);
    propertyKey = toPrimitive(propertyKey, true);
    anObject(attributes);
    try {
      dP.f(target, propertyKey, attributes);
      return true;
    } catch (e) {
      return false;
    }
  }
});

},{"./_an-object":19,"./_export":43,"./_fails":45,"./_object-dp":80,"./_to-primitive":124}],217:[function(require,module,exports){
// 26.1.4 Reflect.deleteProperty(target, propertyKey)
var $export = require('./_export');
var gOPD = require('./_object-gopd').f;
var anObject = require('./_an-object');

$export($export.S, 'Reflect', {
  deleteProperty: function deleteProperty(target, propertyKey) {
    var desc = gOPD(anObject(target), propertyKey);
    return desc && !desc.configurable ? false : delete target[propertyKey];
  }
});

},{"./_an-object":19,"./_export":43,"./_object-gopd":82}],218:[function(require,module,exports){
'use strict';
// 26.1.5 Reflect.enumerate(target)
var $export = require('./_export');
var anObject = require('./_an-object');
var Enumerate = function (iterated) {
  this._t = anObject(iterated); // target
  this._i = 0;                  // next index
  var keys = this._k = [];      // keys
  var key;
  for (key in iterated) keys.push(key);
};
require('./_iter-create')(Enumerate, 'Object', function () {
  var that = this;
  var keys = that._k;
  var key;
  do {
    if (that._i >= keys.length) return { value: undefined, done: true };
  } while (!((key = keys[that._i++]) in that._t));
  return { value: key, done: false };
});

$export($export.S, 'Reflect', {
  enumerate: function enumerate(target) {
    return new Enumerate(target);
  }
});

},{"./_an-object":19,"./_export":43,"./_iter-create":65}],219:[function(require,module,exports){
// 26.1.7 Reflect.getOwnPropertyDescriptor(target, propertyKey)
var gOPD = require('./_object-gopd');
var $export = require('./_export');
var anObject = require('./_an-object');

$export($export.S, 'Reflect', {
  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(target, propertyKey) {
    return gOPD.f(anObject(target), propertyKey);
  }
});

},{"./_an-object":19,"./_export":43,"./_object-gopd":82}],220:[function(require,module,exports){
// 26.1.8 Reflect.getPrototypeOf(target)
var $export = require('./_export');
var getProto = require('./_object-gpo');
var anObject = require('./_an-object');

$export($export.S, 'Reflect', {
  getPrototypeOf: function getPrototypeOf(target) {
    return getProto(anObject(target));
  }
});

},{"./_an-object":19,"./_export":43,"./_object-gpo":86}],221:[function(require,module,exports){
// 26.1.6 Reflect.get(target, propertyKey [, receiver])
var gOPD = require('./_object-gopd');
var getPrototypeOf = require('./_object-gpo');
var has = require('./_has');
var $export = require('./_export');
var isObject = require('./_is-object');
var anObject = require('./_an-object');

function get(target, propertyKey /* , receiver */) {
  var receiver = arguments.length < 3 ? target : arguments[2];
  var desc, proto;
  if (anObject(target) === receiver) return target[propertyKey];
  if (desc = gOPD.f(target, propertyKey)) return has(desc, 'value')
    ? desc.value
    : desc.get !== undefined
      ? desc.get.call(receiver)
      : undefined;
  if (isObject(proto = getPrototypeOf(target))) return get(proto, propertyKey, receiver);
}

$export($export.S, 'Reflect', { get: get });

},{"./_an-object":19,"./_export":43,"./_has":52,"./_is-object":62,"./_object-gopd":82,"./_object-gpo":86}],222:[function(require,module,exports){
// 26.1.9 Reflect.has(target, propertyKey)
var $export = require('./_export');

$export($export.S, 'Reflect', {
  has: function has(target, propertyKey) {
    return propertyKey in target;
  }
});

},{"./_export":43}],223:[function(require,module,exports){
// 26.1.10 Reflect.isExtensible(target)
var $export = require('./_export');
var anObject = require('./_an-object');
var $isExtensible = Object.isExtensible;

$export($export.S, 'Reflect', {
  isExtensible: function isExtensible(target) {
    anObject(target);
    return $isExtensible ? $isExtensible(target) : true;
  }
});

},{"./_an-object":19,"./_export":43}],224:[function(require,module,exports){
// 26.1.11 Reflect.ownKeys(target)
var $export = require('./_export');

$export($export.S, 'Reflect', { ownKeys: require('./_own-keys') });

},{"./_export":43,"./_own-keys":92}],225:[function(require,module,exports){
// 26.1.12 Reflect.preventExtensions(target)
var $export = require('./_export');
var anObject = require('./_an-object');
var $preventExtensions = Object.preventExtensions;

$export($export.S, 'Reflect', {
  preventExtensions: function preventExtensions(target) {
    anObject(target);
    try {
      if ($preventExtensions) $preventExtensions(target);
      return true;
    } catch (e) {
      return false;
    }
  }
});

},{"./_an-object":19,"./_export":43}],226:[function(require,module,exports){
// 26.1.14 Reflect.setPrototypeOf(target, proto)
var $export = require('./_export');
var setProto = require('./_set-proto');

if (setProto) $export($export.S, 'Reflect', {
  setPrototypeOf: function setPrototypeOf(target, proto) {
    setProto.check(target, proto);
    try {
      setProto.set(target, proto);
      return true;
    } catch (e) {
      return false;
    }
  }
});

},{"./_export":43,"./_set-proto":103}],227:[function(require,module,exports){
// 26.1.13 Reflect.set(target, propertyKey, V [, receiver])
var dP = require('./_object-dp');
var gOPD = require('./_object-gopd');
var getPrototypeOf = require('./_object-gpo');
var has = require('./_has');
var $export = require('./_export');
var createDesc = require('./_property-desc');
var anObject = require('./_an-object');
var isObject = require('./_is-object');

function set(target, propertyKey, V /* , receiver */) {
  var receiver = arguments.length < 4 ? target : arguments[3];
  var ownDesc = gOPD.f(anObject(target), propertyKey);
  var existingDescriptor, proto;
  if (!ownDesc) {
    if (isObject(proto = getPrototypeOf(target))) {
      return set(proto, propertyKey, V, receiver);
    }
    ownDesc = createDesc(0);
  }
  if (has(ownDesc, 'value')) {
    if (ownDesc.writable === false || !isObject(receiver)) return false;
    if (existingDescriptor = gOPD.f(receiver, propertyKey)) {
      if (existingDescriptor.get || existingDescriptor.set || existingDescriptor.writable === false) return false;
      existingDescriptor.value = V;
      dP.f(receiver, propertyKey, existingDescriptor);
    } else dP.f(receiver, propertyKey, createDesc(0, V));
    return true;
  }
  return ownDesc.set === undefined ? false : (ownDesc.set.call(receiver, V), true);
}

$export($export.S, 'Reflect', { set: set });

},{"./_an-object":19,"./_export":43,"./_has":52,"./_is-object":62,"./_object-dp":80,"./_object-gopd":82,"./_object-gpo":86,"./_property-desc":97}],228:[function(require,module,exports){
var global = require('./_global');
var inheritIfRequired = require('./_inherit-if-required');
var dP = require('./_object-dp').f;
var gOPN = require('./_object-gopn').f;
var isRegExp = require('./_is-regexp');
var $flags = require('./_flags');
var $RegExp = global.RegExp;
var Base = $RegExp;
var proto = $RegExp.prototype;
var re1 = /a/g;
var re2 = /a/g;
// "new" creates a new object, old webkit buggy here
var CORRECT_NEW = new $RegExp(re1) !== re1;

if (require('./_descriptors') && (!CORRECT_NEW || require('./_fails')(function () {
  re2[require('./_wks')('match')] = false;
  // RegExp constructor can alter flags and IsRegExp works correct with @@match
  return $RegExp(re1) != re1 || $RegExp(re2) == re2 || $RegExp(re1, 'i') != '/a/i';
}))) {
  $RegExp = function RegExp(p, f) {
    var tiRE = this instanceof $RegExp;
    var piRE = isRegExp(p);
    var fiU = f === undefined;
    return !tiRE && piRE && p.constructor === $RegExp && fiU ? p
      : inheritIfRequired(CORRECT_NEW
        ? new Base(piRE && !fiU ? p.source : p, f)
        : Base((piRE = p instanceof $RegExp) ? p.source : p, piRE && fiU ? $flags.call(p) : f)
      , tiRE ? this : proto, $RegExp);
  };
  var proxy = function (key) {
    key in $RegExp || dP($RegExp, key, {
      configurable: true,
      get: function () { return Base[key]; },
      set: function (it) { Base[key] = it; }
    });
  };
  for (var keys = gOPN(Base), i = 0; keys.length > i;) proxy(keys[i++]);
  proto.constructor = $RegExp;
  $RegExp.prototype = proto;
  require('./_redefine')(global, 'RegExp', $RegExp);
}

require('./_set-species')('RegExp');

},{"./_descriptors":39,"./_fails":45,"./_flags":47,"./_global":51,"./_inherit-if-required":56,"./_is-regexp":63,"./_object-dp":80,"./_object-gopn":84,"./_redefine":99,"./_set-species":104,"./_wks":133}],229:[function(require,module,exports){
'use strict';
var regexpExec = require('./_regexp-exec');
require('./_export')({
  target: 'RegExp',
  proto: true,
  forced: regexpExec !== /./.exec
}, {
  exec: regexpExec
});

},{"./_export":43,"./_regexp-exec":101}],230:[function(require,module,exports){
// 21.2.5.3 get RegExp.prototype.flags()
if (require('./_descriptors') && /./g.flags != 'g') require('./_object-dp').f(RegExp.prototype, 'flags', {
  configurable: true,
  get: require('./_flags')
});

},{"./_descriptors":39,"./_flags":47,"./_object-dp":80}],231:[function(require,module,exports){
'use strict';

var anObject = require('./_an-object');
var toLength = require('./_to-length');
var advanceStringIndex = require('./_advance-string-index');
var regExpExec = require('./_regexp-exec-abstract');

// @@match logic
require('./_fix-re-wks')('match', 1, function (defined, MATCH, $match, maybeCallNative) {
  return [
    // `String.prototype.match` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.match
    function match(regexp) {
      var O = defined(this);
      var fn = regexp == undefined ? undefined : regexp[MATCH];
      return fn !== undefined ? fn.call(regexp, O) : new RegExp(regexp)[MATCH](String(O));
    },
    // `RegExp.prototype[@@match]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@match
    function (regexp) {
      var res = maybeCallNative($match, regexp, this);
      if (res.done) return res.value;
      var rx = anObject(regexp);
      var S = String(this);
      if (!rx.global) return regExpExec(rx, S);
      var fullUnicode = rx.unicode;
      rx.lastIndex = 0;
      var A = [];
      var n = 0;
      var result;
      while ((result = regExpExec(rx, S)) !== null) {
        var matchStr = String(result[0]);
        A[n] = matchStr;
        if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
        n++;
      }
      return n === 0 ? null : A;
    }
  ];
});

},{"./_advance-string-index":17,"./_an-object":19,"./_fix-re-wks":46,"./_regexp-exec-abstract":100,"./_to-length":122}],232:[function(require,module,exports){
'use strict';

var anObject = require('./_an-object');
var toObject = require('./_to-object');
var toLength = require('./_to-length');
var toInteger = require('./_to-integer');
var advanceStringIndex = require('./_advance-string-index');
var regExpExec = require('./_regexp-exec-abstract');
var max = Math.max;
var min = Math.min;
var floor = Math.floor;
var SUBSTITUTION_SYMBOLS = /\$([$&`']|\d\d?|<[^>]*>)/g;
var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&`']|\d\d?)/g;

var maybeToString = function (it) {
  return it === undefined ? it : String(it);
};

// @@replace logic
require('./_fix-re-wks')('replace', 2, function (defined, REPLACE, $replace, maybeCallNative) {
  return [
    // `String.prototype.replace` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.replace
    function replace(searchValue, replaceValue) {
      var O = defined(this);
      var fn = searchValue == undefined ? undefined : searchValue[REPLACE];
      return fn !== undefined
        ? fn.call(searchValue, O, replaceValue)
        : $replace.call(String(O), searchValue, replaceValue);
    },
    // `RegExp.prototype[@@replace]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@replace
    function (regexp, replaceValue) {
      var res = maybeCallNative($replace, regexp, this, replaceValue);
      if (res.done) return res.value;

      var rx = anObject(regexp);
      var S = String(this);
      var functionalReplace = typeof replaceValue === 'function';
      if (!functionalReplace) replaceValue = String(replaceValue);
      var global = rx.global;
      if (global) {
        var fullUnicode = rx.unicode;
        rx.lastIndex = 0;
      }
      var results = [];
      while (true) {
        var result = regExpExec(rx, S);
        if (result === null) break;
        results.push(result);
        if (!global) break;
        var matchStr = String(result[0]);
        if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
      }
      var accumulatedResult = '';
      var nextSourcePosition = 0;
      for (var i = 0; i < results.length; i++) {
        result = results[i];
        var matched = String(result[0]);
        var position = max(min(toInteger(result.index), S.length), 0);
        var captures = [];
        // NOTE: This is equivalent to
        //   captures = result.slice(1).map(maybeToString)
        // but for some reason `nativeSlice.call(result, 1, result.length)` (called in
        // the slice polyfill when slicing native arrays) "doesn't work" in safari 9 and
        // causes a crash (https://pastebin.com/N21QzeQA) when trying to debug it.
        for (var j = 1; j < result.length; j++) captures.push(maybeToString(result[j]));
        var namedCaptures = result.groups;
        if (functionalReplace) {
          var replacerArgs = [matched].concat(captures, position, S);
          if (namedCaptures !== undefined) replacerArgs.push(namedCaptures);
          var replacement = String(replaceValue.apply(undefined, replacerArgs));
        } else {
          replacement = getSubstitution(matched, S, position, captures, namedCaptures, replaceValue);
        }
        if (position >= nextSourcePosition) {
          accumulatedResult += S.slice(nextSourcePosition, position) + replacement;
          nextSourcePosition = position + matched.length;
        }
      }
      return accumulatedResult + S.slice(nextSourcePosition);
    }
  ];

    // https://tc39.github.io/ecma262/#sec-getsubstitution
  function getSubstitution(matched, str, position, captures, namedCaptures, replacement) {
    var tailPos = position + matched.length;
    var m = captures.length;
    var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;
    if (namedCaptures !== undefined) {
      namedCaptures = toObject(namedCaptures);
      symbols = SUBSTITUTION_SYMBOLS;
    }
    return $replace.call(replacement, symbols, function (match, ch) {
      var capture;
      switch (ch.charAt(0)) {
        case '$': return '$';
        case '&': return matched;
        case '`': return str.slice(0, position);
        case "'": return str.slice(tailPos);
        case '<':
          capture = namedCaptures[ch.slice(1, -1)];
          break;
        default: // \d\d?
          var n = +ch;
          if (n === 0) return match;
          if (n > m) {
            var f = floor(n / 10);
            if (f === 0) return match;
            if (f <= m) return captures[f - 1] === undefined ? ch.charAt(1) : captures[f - 1] + ch.charAt(1);
            return match;
          }
          capture = captures[n - 1];
      }
      return capture === undefined ? '' : capture;
    });
  }
});

},{"./_advance-string-index":17,"./_an-object":19,"./_fix-re-wks":46,"./_regexp-exec-abstract":100,"./_to-integer":120,"./_to-length":122,"./_to-object":123}],233:[function(require,module,exports){
'use strict';

var anObject = require('./_an-object');
var sameValue = require('./_same-value');
var regExpExec = require('./_regexp-exec-abstract');

// @@search logic
require('./_fix-re-wks')('search', 1, function (defined, SEARCH, $search, maybeCallNative) {
  return [
    // `String.prototype.search` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.search
    function search(regexp) {
      var O = defined(this);
      var fn = regexp == undefined ? undefined : regexp[SEARCH];
      return fn !== undefined ? fn.call(regexp, O) : new RegExp(regexp)[SEARCH](String(O));
    },
    // `RegExp.prototype[@@search]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@search
    function (regexp) {
      var res = maybeCallNative($search, regexp, this);
      if (res.done) return res.value;
      var rx = anObject(regexp);
      var S = String(this);
      var previousLastIndex = rx.lastIndex;
      if (!sameValue(previousLastIndex, 0)) rx.lastIndex = 0;
      var result = regExpExec(rx, S);
      if (!sameValue(rx.lastIndex, previousLastIndex)) rx.lastIndex = previousLastIndex;
      return result === null ? -1 : result.index;
    }
  ];
});

},{"./_an-object":19,"./_fix-re-wks":46,"./_regexp-exec-abstract":100,"./_same-value":102}],234:[function(require,module,exports){
'use strict';

var isRegExp = require('./_is-regexp');
var anObject = require('./_an-object');
var speciesConstructor = require('./_species-constructor');
var advanceStringIndex = require('./_advance-string-index');
var toLength = require('./_to-length');
var callRegExpExec = require('./_regexp-exec-abstract');
var regexpExec = require('./_regexp-exec');
var fails = require('./_fails');
var $min = Math.min;
var $push = [].push;
var $SPLIT = 'split';
var LENGTH = 'length';
var LAST_INDEX = 'lastIndex';
var MAX_UINT32 = 0xffffffff;

// babel-minify transpiles RegExp('x', 'y') -> /x/y and it causes SyntaxError
var SUPPORTS_Y = !fails(function () { RegExp(MAX_UINT32, 'y'); });

// @@split logic
require('./_fix-re-wks')('split', 2, function (defined, SPLIT, $split, maybeCallNative) {
  var internalSplit;
  if (
    'abbc'[$SPLIT](/(b)*/)[1] == 'c' ||
    'test'[$SPLIT](/(?:)/, -1)[LENGTH] != 4 ||
    'ab'[$SPLIT](/(?:ab)*/)[LENGTH] != 2 ||
    '.'[$SPLIT](/(.?)(.?)/)[LENGTH] != 4 ||
    '.'[$SPLIT](/()()/)[LENGTH] > 1 ||
    ''[$SPLIT](/.?/)[LENGTH]
  ) {
    // based on es5-shim implementation, need to rework it
    internalSplit = function (separator, limit) {
      var string = String(this);
      if (separator === undefined && limit === 0) return [];
      // If `separator` is not a regex, use native split
      if (!isRegExp(separator)) return $split.call(string, separator, limit);
      var output = [];
      var flags = (separator.ignoreCase ? 'i' : '') +
                  (separator.multiline ? 'm' : '') +
                  (separator.unicode ? 'u' : '') +
                  (separator.sticky ? 'y' : '');
      var lastLastIndex = 0;
      var splitLimit = limit === undefined ? MAX_UINT32 : limit >>> 0;
      // Make `global` and avoid `lastIndex` issues by working with a copy
      var separatorCopy = new RegExp(separator.source, flags + 'g');
      var match, lastIndex, lastLength;
      while (match = regexpExec.call(separatorCopy, string)) {
        lastIndex = separatorCopy[LAST_INDEX];
        if (lastIndex > lastLastIndex) {
          output.push(string.slice(lastLastIndex, match.index));
          if (match[LENGTH] > 1 && match.index < string[LENGTH]) $push.apply(output, match.slice(1));
          lastLength = match[0][LENGTH];
          lastLastIndex = lastIndex;
          if (output[LENGTH] >= splitLimit) break;
        }
        if (separatorCopy[LAST_INDEX] === match.index) separatorCopy[LAST_INDEX]++; // Avoid an infinite loop
      }
      if (lastLastIndex === string[LENGTH]) {
        if (lastLength || !separatorCopy.test('')) output.push('');
      } else output.push(string.slice(lastLastIndex));
      return output[LENGTH] > splitLimit ? output.slice(0, splitLimit) : output;
    };
  // Chakra, V8
  } else if ('0'[$SPLIT](undefined, 0)[LENGTH]) {
    internalSplit = function (separator, limit) {
      return separator === undefined && limit === 0 ? [] : $split.call(this, separator, limit);
    };
  } else {
    internalSplit = $split;
  }

  return [
    // `String.prototype.split` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.split
    function split(separator, limit) {
      var O = defined(this);
      var splitter = separator == undefined ? undefined : separator[SPLIT];
      return splitter !== undefined
        ? splitter.call(separator, O, limit)
        : internalSplit.call(String(O), separator, limit);
    },
    // `RegExp.prototype[@@split]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@split
    //
    // NOTE: This cannot be properly polyfilled in engines that don't support
    // the 'y' flag.
    function (regexp, limit) {
      var res = maybeCallNative(internalSplit, regexp, this, limit, internalSplit !== $split);
      if (res.done) return res.value;

      var rx = anObject(regexp);
      var S = String(this);
      var C = speciesConstructor(rx, RegExp);

      var unicodeMatching = rx.unicode;
      var flags = (rx.ignoreCase ? 'i' : '') +
                  (rx.multiline ? 'm' : '') +
                  (rx.unicode ? 'u' : '') +
                  (SUPPORTS_Y ? 'y' : 'g');

      // ^(? + rx + ) is needed, in combination with some S slicing, to
      // simulate the 'y' flag.
      var splitter = new C(SUPPORTS_Y ? rx : '^(?:' + rx.source + ')', flags);
      var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
      if (lim === 0) return [];
      if (S.length === 0) return callRegExpExec(splitter, S) === null ? [S] : [];
      var p = 0;
      var q = 0;
      var A = [];
      while (q < S.length) {
        splitter.lastIndex = SUPPORTS_Y ? q : 0;
        var z = callRegExpExec(splitter, SUPPORTS_Y ? S : S.slice(q));
        var e;
        if (
          z === null ||
          (e = $min(toLength(splitter.lastIndex + (SUPPORTS_Y ? 0 : q)), S.length)) === p
        ) {
          q = advanceStringIndex(S, q, unicodeMatching);
        } else {
          A.push(S.slice(p, q));
          if (A.length === lim) return A;
          for (var i = 1; i <= z.length - 1; i++) {
            A.push(z[i]);
            if (A.length === lim) return A;
          }
          q = p = e;
        }
      }
      A.push(S.slice(p));
      return A;
    }
  ];
});

},{"./_advance-string-index":17,"./_an-object":19,"./_fails":45,"./_fix-re-wks":46,"./_is-regexp":63,"./_regexp-exec":101,"./_regexp-exec-abstract":100,"./_species-constructor":108,"./_to-length":122}],235:[function(require,module,exports){
'use strict';
require('./es6.regexp.flags');
var anObject = require('./_an-object');
var $flags = require('./_flags');
var DESCRIPTORS = require('./_descriptors');
var TO_STRING = 'toString';
var $toString = /./[TO_STRING];

var define = function (fn) {
  require('./_redefine')(RegExp.prototype, TO_STRING, fn, true);
};

// 21.2.5.14 RegExp.prototype.toString()
if (require('./_fails')(function () { return $toString.call({ source: 'a', flags: 'b' }) != '/a/b'; })) {
  define(function toString() {
    var R = anObject(this);
    return '/'.concat(R.source, '/',
      'flags' in R ? R.flags : !DESCRIPTORS && R instanceof RegExp ? $flags.call(R) : undefined);
  });
// FF44- RegExp#toString has a wrong name
} else if ($toString.name != TO_STRING) {
  define(function toString() {
    return $toString.call(this);
  });
}

},{"./_an-object":19,"./_descriptors":39,"./_fails":45,"./_flags":47,"./_redefine":99,"./es6.regexp.flags":230}],236:[function(require,module,exports){
'use strict';
var strong = require('./_collection-strong');
var validate = require('./_validate-collection');
var SET = 'Set';

// 23.2 Set Objects
module.exports = require('./_collection')(SET, function (get) {
  return function Set() { return get(this, arguments.length > 0 ? arguments[0] : undefined); };
}, {
  // 23.2.3.1 Set.prototype.add(value)
  add: function add(value) {
    return strong.def(validate(this, SET), value = value === 0 ? 0 : value, value);
  }
}, strong);

},{"./_collection":32,"./_collection-strong":30,"./_validate-collection":130}],237:[function(require,module,exports){
'use strict';
// B.2.3.2 String.prototype.anchor(name)
require('./_string-html')('anchor', function (createHTML) {
  return function anchor(name) {
    return createHTML(this, 'a', 'name', name);
  };
});

},{"./_string-html":112}],238:[function(require,module,exports){
'use strict';
// B.2.3.3 String.prototype.big()
require('./_string-html')('big', function (createHTML) {
  return function big() {
    return createHTML(this, 'big', '', '');
  };
});

},{"./_string-html":112}],239:[function(require,module,exports){
'use strict';
// B.2.3.4 String.prototype.blink()
require('./_string-html')('blink', function (createHTML) {
  return function blink() {
    return createHTML(this, 'blink', '', '');
  };
});

},{"./_string-html":112}],240:[function(require,module,exports){
'use strict';
// B.2.3.5 String.prototype.bold()
require('./_string-html')('bold', function (createHTML) {
  return function bold() {
    return createHTML(this, 'b', '', '');
  };
});

},{"./_string-html":112}],241:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $at = require('./_string-at')(false);
$export($export.P, 'String', {
  // 21.1.3.3 String.prototype.codePointAt(pos)
  codePointAt: function codePointAt(pos) {
    return $at(this, pos);
  }
});

},{"./_export":43,"./_string-at":110}],242:[function(require,module,exports){
// 21.1.3.6 String.prototype.endsWith(searchString [, endPosition])
'use strict';
var $export = require('./_export');
var toLength = require('./_to-length');
var context = require('./_string-context');
var ENDS_WITH = 'endsWith';
var $endsWith = ''[ENDS_WITH];

$export($export.P + $export.F * require('./_fails-is-regexp')(ENDS_WITH), 'String', {
  endsWith: function endsWith(searchString /* , endPosition = @length */) {
    var that = context(this, searchString, ENDS_WITH);
    var endPosition = arguments.length > 1 ? arguments[1] : undefined;
    var len = toLength(that.length);
    var end = endPosition === undefined ? len : Math.min(toLength(endPosition), len);
    var search = String(searchString);
    return $endsWith
      ? $endsWith.call(that, search, end)
      : that.slice(end - search.length, end) === search;
  }
});

},{"./_export":43,"./_fails-is-regexp":44,"./_string-context":111,"./_to-length":122}],243:[function(require,module,exports){
'use strict';
// B.2.3.6 String.prototype.fixed()
require('./_string-html')('fixed', function (createHTML) {
  return function fixed() {
    return createHTML(this, 'tt', '', '');
  };
});

},{"./_string-html":112}],244:[function(require,module,exports){
'use strict';
// B.2.3.7 String.prototype.fontcolor(color)
require('./_string-html')('fontcolor', function (createHTML) {
  return function fontcolor(color) {
    return createHTML(this, 'font', 'color', color);
  };
});

},{"./_string-html":112}],245:[function(require,module,exports){
'use strict';
// B.2.3.8 String.prototype.fontsize(size)
require('./_string-html')('fontsize', function (createHTML) {
  return function fontsize(size) {
    return createHTML(this, 'font', 'size', size);
  };
});

},{"./_string-html":112}],246:[function(require,module,exports){
var $export = require('./_export');
var toAbsoluteIndex = require('./_to-absolute-index');
var fromCharCode = String.fromCharCode;
var $fromCodePoint = String.fromCodePoint;

// length should be 1, old FF problem
$export($export.S + $export.F * (!!$fromCodePoint && $fromCodePoint.length != 1), 'String', {
  // 21.1.2.2 String.fromCodePoint(...codePoints)
  fromCodePoint: function fromCodePoint(x) { // eslint-disable-line no-unused-vars
    var res = [];
    var aLen = arguments.length;
    var i = 0;
    var code;
    while (aLen > i) {
      code = +arguments[i++];
      if (toAbsoluteIndex(code, 0x10ffff) !== code) throw RangeError(code + ' is not a valid code point');
      res.push(code < 0x10000
        ? fromCharCode(code)
        : fromCharCode(((code -= 0x10000) >> 10) + 0xd800, code % 0x400 + 0xdc00)
      );
    } return res.join('');
  }
});

},{"./_export":43,"./_to-absolute-index":118}],247:[function(require,module,exports){
// 21.1.3.7 String.prototype.includes(searchString, position = 0)
'use strict';
var $export = require('./_export');
var context = require('./_string-context');
var INCLUDES = 'includes';

$export($export.P + $export.F * require('./_fails-is-regexp')(INCLUDES), 'String', {
  includes: function includes(searchString /* , position = 0 */) {
    return !!~context(this, searchString, INCLUDES)
      .indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
  }
});

},{"./_export":43,"./_fails-is-regexp":44,"./_string-context":111}],248:[function(require,module,exports){
'use strict';
// B.2.3.9 String.prototype.italics()
require('./_string-html')('italics', function (createHTML) {
  return function italics() {
    return createHTML(this, 'i', '', '');
  };
});

},{"./_string-html":112}],249:[function(require,module,exports){
'use strict';
var $at = require('./_string-at')(true);

// 21.1.3.27 String.prototype[@@iterator]()
require('./_iter-define')(String, 'String', function (iterated) {
  this._t = String(iterated); // target
  this._i = 0;                // next index
// 21.1.5.2.1 %StringIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var index = this._i;
  var point;
  if (index >= O.length) return { value: undefined, done: true };
  point = $at(O, index);
  this._i += point.length;
  return { value: point, done: false };
});

},{"./_iter-define":66,"./_string-at":110}],250:[function(require,module,exports){
'use strict';
// B.2.3.10 String.prototype.link(url)
require('./_string-html')('link', function (createHTML) {
  return function link(url) {
    return createHTML(this, 'a', 'href', url);
  };
});

},{"./_string-html":112}],251:[function(require,module,exports){
var $export = require('./_export');
var toIObject = require('./_to-iobject');
var toLength = require('./_to-length');

$export($export.S, 'String', {
  // 21.1.2.4 String.raw(callSite, ...substitutions)
  raw: function raw(callSite) {
    var tpl = toIObject(callSite.raw);
    var len = toLength(tpl.length);
    var aLen = arguments.length;
    var res = [];
    var i = 0;
    while (len > i) {
      res.push(String(tpl[i++]));
      if (i < aLen) res.push(String(arguments[i]));
    } return res.join('');
  }
});

},{"./_export":43,"./_to-iobject":121,"./_to-length":122}],252:[function(require,module,exports){
var $export = require('./_export');

$export($export.P, 'String', {
  // 21.1.3.13 String.prototype.repeat(count)
  repeat: require('./_string-repeat')
});

},{"./_export":43,"./_string-repeat":114}],253:[function(require,module,exports){
'use strict';
// B.2.3.11 String.prototype.small()
require('./_string-html')('small', function (createHTML) {
  return function small() {
    return createHTML(this, 'small', '', '');
  };
});

},{"./_string-html":112}],254:[function(require,module,exports){
// 21.1.3.18 String.prototype.startsWith(searchString [, position ])
'use strict';
var $export = require('./_export');
var toLength = require('./_to-length');
var context = require('./_string-context');
var STARTS_WITH = 'startsWith';
var $startsWith = ''[STARTS_WITH];

$export($export.P + $export.F * require('./_fails-is-regexp')(STARTS_WITH), 'String', {
  startsWith: function startsWith(searchString /* , position = 0 */) {
    var that = context(this, searchString, STARTS_WITH);
    var index = toLength(Math.min(arguments.length > 1 ? arguments[1] : undefined, that.length));
    var search = String(searchString);
    return $startsWith
      ? $startsWith.call(that, search, index)
      : that.slice(index, index + search.length) === search;
  }
});

},{"./_export":43,"./_fails-is-regexp":44,"./_string-context":111,"./_to-length":122}],255:[function(require,module,exports){
'use strict';
// B.2.3.12 String.prototype.strike()
require('./_string-html')('strike', function (createHTML) {
  return function strike() {
    return createHTML(this, 'strike', '', '');
  };
});

},{"./_string-html":112}],256:[function(require,module,exports){
'use strict';
// B.2.3.13 String.prototype.sub()
require('./_string-html')('sub', function (createHTML) {
  return function sub() {
    return createHTML(this, 'sub', '', '');
  };
});

},{"./_string-html":112}],257:[function(require,module,exports){
'use strict';
// B.2.3.14 String.prototype.sup()
require('./_string-html')('sup', function (createHTML) {
  return function sup() {
    return createHTML(this, 'sup', '', '');
  };
});

},{"./_string-html":112}],258:[function(require,module,exports){
'use strict';
// 21.1.3.25 String.prototype.trim()
require('./_string-trim')('trim', function ($trim) {
  return function trim() {
    return $trim(this, 3);
  };
});

},{"./_string-trim":115}],259:[function(require,module,exports){
'use strict';
// ECMAScript 6 symbols shim
var global = require('./_global');
var has = require('./_has');
var DESCRIPTORS = require('./_descriptors');
var $export = require('./_export');
var redefine = require('./_redefine');
var META = require('./_meta').KEY;
var $fails = require('./_fails');
var shared = require('./_shared');
var setToStringTag = require('./_set-to-string-tag');
var uid = require('./_uid');
var wks = require('./_wks');
var wksExt = require('./_wks-ext');
var wksDefine = require('./_wks-define');
var enumKeys = require('./_enum-keys');
var isArray = require('./_is-array');
var anObject = require('./_an-object');
var isObject = require('./_is-object');
var toIObject = require('./_to-iobject');
var toPrimitive = require('./_to-primitive');
var createDesc = require('./_property-desc');
var _create = require('./_object-create');
var gOPNExt = require('./_object-gopn-ext');
var $GOPD = require('./_object-gopd');
var $DP = require('./_object-dp');
var $keys = require('./_object-keys');
var gOPD = $GOPD.f;
var dP = $DP.f;
var gOPN = gOPNExt.f;
var $Symbol = global.Symbol;
var $JSON = global.JSON;
var _stringify = $JSON && $JSON.stringify;
var PROTOTYPE = 'prototype';
var HIDDEN = wks('_hidden');
var TO_PRIMITIVE = wks('toPrimitive');
var isEnum = {}.propertyIsEnumerable;
var SymbolRegistry = shared('symbol-registry');
var AllSymbols = shared('symbols');
var OPSymbols = shared('op-symbols');
var ObjectProto = Object[PROTOTYPE];
var USE_NATIVE = typeof $Symbol == 'function';
var QObject = global.QObject;
// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
var setter = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
var setSymbolDesc = DESCRIPTORS && $fails(function () {
  return _create(dP({}, 'a', {
    get: function () { return dP(this, 'a', { value: 7 }).a; }
  })).a != 7;
}) ? function (it, key, D) {
  var protoDesc = gOPD(ObjectProto, key);
  if (protoDesc) delete ObjectProto[key];
  dP(it, key, D);
  if (protoDesc && it !== ObjectProto) dP(ObjectProto, key, protoDesc);
} : dP;

var wrap = function (tag) {
  var sym = AllSymbols[tag] = _create($Symbol[PROTOTYPE]);
  sym._k = tag;
  return sym;
};

var isSymbol = USE_NATIVE && typeof $Symbol.iterator == 'symbol' ? function (it) {
  return typeof it == 'symbol';
} : function (it) {
  return it instanceof $Symbol;
};

var $defineProperty = function defineProperty(it, key, D) {
  if (it === ObjectProto) $defineProperty(OPSymbols, key, D);
  anObject(it);
  key = toPrimitive(key, true);
  anObject(D);
  if (has(AllSymbols, key)) {
    if (!D.enumerable) {
      if (!has(it, HIDDEN)) dP(it, HIDDEN, createDesc(1, {}));
      it[HIDDEN][key] = true;
    } else {
      if (has(it, HIDDEN) && it[HIDDEN][key]) it[HIDDEN][key] = false;
      D = _create(D, { enumerable: createDesc(0, false) });
    } return setSymbolDesc(it, key, D);
  } return dP(it, key, D);
};
var $defineProperties = function defineProperties(it, P) {
  anObject(it);
  var keys = enumKeys(P = toIObject(P));
  var i = 0;
  var l = keys.length;
  var key;
  while (l > i) $defineProperty(it, key = keys[i++], P[key]);
  return it;
};
var $create = function create(it, P) {
  return P === undefined ? _create(it) : $defineProperties(_create(it), P);
};
var $propertyIsEnumerable = function propertyIsEnumerable(key) {
  var E = isEnum.call(this, key = toPrimitive(key, true));
  if (this === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key)) return false;
  return E || !has(this, key) || !has(AllSymbols, key) || has(this, HIDDEN) && this[HIDDEN][key] ? E : true;
};
var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(it, key) {
  it = toIObject(it);
  key = toPrimitive(key, true);
  if (it === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key)) return;
  var D = gOPD(it, key);
  if (D && has(AllSymbols, key) && !(has(it, HIDDEN) && it[HIDDEN][key])) D.enumerable = true;
  return D;
};
var $getOwnPropertyNames = function getOwnPropertyNames(it) {
  var names = gOPN(toIObject(it));
  var result = [];
  var i = 0;
  var key;
  while (names.length > i) {
    if (!has(AllSymbols, key = names[i++]) && key != HIDDEN && key != META) result.push(key);
  } return result;
};
var $getOwnPropertySymbols = function getOwnPropertySymbols(it) {
  var IS_OP = it === ObjectProto;
  var names = gOPN(IS_OP ? OPSymbols : toIObject(it));
  var result = [];
  var i = 0;
  var key;
  while (names.length > i) {
    if (has(AllSymbols, key = names[i++]) && (IS_OP ? has(ObjectProto, key) : true)) result.push(AllSymbols[key]);
  } return result;
};

// 19.4.1.1 Symbol([description])
if (!USE_NATIVE) {
  $Symbol = function Symbol() {
    if (this instanceof $Symbol) throw TypeError('Symbol is not a constructor!');
    var tag = uid(arguments.length > 0 ? arguments[0] : undefined);
    var $set = function (value) {
      if (this === ObjectProto) $set.call(OPSymbols, value);
      if (has(this, HIDDEN) && has(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
      setSymbolDesc(this, tag, createDesc(1, value));
    };
    if (DESCRIPTORS && setter) setSymbolDesc(ObjectProto, tag, { configurable: true, set: $set });
    return wrap(tag);
  };
  redefine($Symbol[PROTOTYPE], 'toString', function toString() {
    return this._k;
  });

  $GOPD.f = $getOwnPropertyDescriptor;
  $DP.f = $defineProperty;
  require('./_object-gopn').f = gOPNExt.f = $getOwnPropertyNames;
  require('./_object-pie').f = $propertyIsEnumerable;
  require('./_object-gops').f = $getOwnPropertySymbols;

  if (DESCRIPTORS && !require('./_library')) {
    redefine(ObjectProto, 'propertyIsEnumerable', $propertyIsEnumerable, true);
  }

  wksExt.f = function (name) {
    return wrap(wks(name));
  };
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, { Symbol: $Symbol });

for (var es6Symbols = (
  // 19.4.2.2, 19.4.2.3, 19.4.2.4, 19.4.2.6, 19.4.2.8, 19.4.2.9, 19.4.2.10, 19.4.2.11, 19.4.2.12, 19.4.2.13, 19.4.2.14
  'hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables'
).split(','), j = 0; es6Symbols.length > j;)wks(es6Symbols[j++]);

for (var wellKnownSymbols = $keys(wks.store), k = 0; wellKnownSymbols.length > k;) wksDefine(wellKnownSymbols[k++]);

$export($export.S + $export.F * !USE_NATIVE, 'Symbol', {
  // 19.4.2.1 Symbol.for(key)
  'for': function (key) {
    return has(SymbolRegistry, key += '')
      ? SymbolRegistry[key]
      : SymbolRegistry[key] = $Symbol(key);
  },
  // 19.4.2.5 Symbol.keyFor(sym)
  keyFor: function keyFor(sym) {
    if (!isSymbol(sym)) throw TypeError(sym + ' is not a symbol!');
    for (var key in SymbolRegistry) if (SymbolRegistry[key] === sym) return key;
  },
  useSetter: function () { setter = true; },
  useSimple: function () { setter = false; }
});

$export($export.S + $export.F * !USE_NATIVE, 'Object', {
  // 19.1.2.2 Object.create(O [, Properties])
  create: $create,
  // 19.1.2.4 Object.defineProperty(O, P, Attributes)
  defineProperty: $defineProperty,
  // 19.1.2.3 Object.defineProperties(O, Properties)
  defineProperties: $defineProperties,
  // 19.1.2.6 Object.getOwnPropertyDescriptor(O, P)
  getOwnPropertyDescriptor: $getOwnPropertyDescriptor,
  // 19.1.2.7 Object.getOwnPropertyNames(O)
  getOwnPropertyNames: $getOwnPropertyNames,
  // 19.1.2.8 Object.getOwnPropertySymbols(O)
  getOwnPropertySymbols: $getOwnPropertySymbols
});

// 24.3.2 JSON.stringify(value [, replacer [, space]])
$JSON && $export($export.S + $export.F * (!USE_NATIVE || $fails(function () {
  var S = $Symbol();
  // MS Edge converts symbol values to JSON as {}
  // WebKit converts symbol values to JSON as null
  // V8 throws on boxed symbols
  return _stringify([S]) != '[null]' || _stringify({ a: S }) != '{}' || _stringify(Object(S)) != '{}';
})), 'JSON', {
  stringify: function stringify(it) {
    var args = [it];
    var i = 1;
    var replacer, $replacer;
    while (arguments.length > i) args.push(arguments[i++]);
    $replacer = replacer = args[1];
    if (!isObject(replacer) && it === undefined || isSymbol(it)) return; // IE8 returns string on undefined
    if (!isArray(replacer)) replacer = function (key, value) {
      if (typeof $replacer == 'function') value = $replacer.call(this, key, value);
      if (!isSymbol(value)) return value;
    };
    args[1] = replacer;
    return _stringify.apply($JSON, args);
  }
});

// 19.4.3.4 Symbol.prototype[@@toPrimitive](hint)
$Symbol[PROTOTYPE][TO_PRIMITIVE] || require('./_hide')($Symbol[PROTOTYPE], TO_PRIMITIVE, $Symbol[PROTOTYPE].valueOf);
// 19.4.3.5 Symbol.prototype[@@toStringTag]
setToStringTag($Symbol, 'Symbol');
// 20.2.1.9 Math[@@toStringTag]
setToStringTag(Math, 'Math', true);
// 24.3.3 JSON[@@toStringTag]
setToStringTag(global.JSON, 'JSON', true);

},{"./_an-object":19,"./_descriptors":39,"./_enum-keys":42,"./_export":43,"./_fails":45,"./_global":51,"./_has":52,"./_hide":53,"./_is-array":60,"./_is-object":62,"./_library":70,"./_meta":75,"./_object-create":79,"./_object-dp":80,"./_object-gopd":82,"./_object-gopn":84,"./_object-gopn-ext":83,"./_object-gops":85,"./_object-keys":88,"./_object-pie":89,"./_property-desc":97,"./_redefine":99,"./_set-to-string-tag":105,"./_shared":107,"./_to-iobject":121,"./_to-primitive":124,"./_uid":128,"./_wks":133,"./_wks-define":131,"./_wks-ext":132}],260:[function(require,module,exports){
'use strict';
var $export = require('./_export');
var $typed = require('./_typed');
var buffer = require('./_typed-buffer');
var anObject = require('./_an-object');
var toAbsoluteIndex = require('./_to-absolute-index');
var toLength = require('./_to-length');
var isObject = require('./_is-object');
var ArrayBuffer = require('./_global').ArrayBuffer;
var speciesConstructor = require('./_species-constructor');
var $ArrayBuffer = buffer.ArrayBuffer;
var $DataView = buffer.DataView;
var $isView = $typed.ABV && ArrayBuffer.isView;
var $slice = $ArrayBuffer.prototype.slice;
var VIEW = $typed.VIEW;
var ARRAY_BUFFER = 'ArrayBuffer';

$export($export.G + $export.W + $export.F * (ArrayBuffer !== $ArrayBuffer), { ArrayBuffer: $ArrayBuffer });

$export($export.S + $export.F * !$typed.CONSTR, ARRAY_BUFFER, {
  // 24.1.3.1 ArrayBuffer.isView(arg)
  isView: function isView(it) {
    return $isView && $isView(it) || isObject(it) && VIEW in it;
  }
});

$export($export.P + $export.U + $export.F * require('./_fails')(function () {
  return !new $ArrayBuffer(2).slice(1, undefined).byteLength;
}), ARRAY_BUFFER, {
  // 24.1.4.3 ArrayBuffer.prototype.slice(start, end)
  slice: function slice(start, end) {
    if ($slice !== undefined && end === undefined) return $slice.call(anObject(this), start); // FF fix
    var len = anObject(this).byteLength;
    var first = toAbsoluteIndex(start, len);
    var fin = toAbsoluteIndex(end === undefined ? len : end, len);
    var result = new (speciesConstructor(this, $ArrayBuffer))(toLength(fin - first));
    var viewS = new $DataView(this);
    var viewT = new $DataView(result);
    var index = 0;
    while (first < fin) {
      viewT.setUint8(index++, viewS.getUint8(first++));
    } return result;
  }
});

require('./_set-species')(ARRAY_BUFFER);

},{"./_an-object":19,"./_export":43,"./_fails":45,"./_global":51,"./_is-object":62,"./_set-species":104,"./_species-constructor":108,"./_to-absolute-index":118,"./_to-length":122,"./_typed":127,"./_typed-buffer":126}],261:[function(require,module,exports){
var $export = require('./_export');
$export($export.G + $export.W + $export.F * !require('./_typed').ABV, {
  DataView: require('./_typed-buffer').DataView
});

},{"./_export":43,"./_typed":127,"./_typed-buffer":126}],262:[function(require,module,exports){
require('./_typed-array')('Float32', 4, function (init) {
  return function Float32Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],263:[function(require,module,exports){
require('./_typed-array')('Float64', 8, function (init) {
  return function Float64Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],264:[function(require,module,exports){
require('./_typed-array')('Int16', 2, function (init) {
  return function Int16Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],265:[function(require,module,exports){
require('./_typed-array')('Int32', 4, function (init) {
  return function Int32Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],266:[function(require,module,exports){
require('./_typed-array')('Int8', 1, function (init) {
  return function Int8Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],267:[function(require,module,exports){
require('./_typed-array')('Uint16', 2, function (init) {
  return function Uint16Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],268:[function(require,module,exports){
require('./_typed-array')('Uint32', 4, function (init) {
  return function Uint32Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],269:[function(require,module,exports){
require('./_typed-array')('Uint8', 1, function (init) {
  return function Uint8Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});

},{"./_typed-array":125}],270:[function(require,module,exports){
require('./_typed-array')('Uint8', 1, function (init) {
  return function Uint8ClampedArray(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
}, true);

},{"./_typed-array":125}],271:[function(require,module,exports){
'use strict';
var global = require('./_global');
var each = require('./_array-methods')(0);
var redefine = require('./_redefine');
var meta = require('./_meta');
var assign = require('./_object-assign');
var weak = require('./_collection-weak');
var isObject = require('./_is-object');
var validate = require('./_validate-collection');
var NATIVE_WEAK_MAP = require('./_validate-collection');
var IS_IE11 = !global.ActiveXObject && 'ActiveXObject' in global;
var WEAK_MAP = 'WeakMap';
var getWeak = meta.getWeak;
var isExtensible = Object.isExtensible;
var uncaughtFrozenStore = weak.ufstore;
var InternalMap;

var wrapper = function (get) {
  return function WeakMap() {
    return get(this, arguments.length > 0 ? arguments[0] : undefined);
  };
};

var methods = {
  // 23.3.3.3 WeakMap.prototype.get(key)
  get: function get(key) {
    if (isObject(key)) {
      var data = getWeak(key);
      if (data === true) return uncaughtFrozenStore(validate(this, WEAK_MAP)).get(key);
      return data ? data[this._i] : undefined;
    }
  },
  // 23.3.3.5 WeakMap.prototype.set(key, value)
  set: function set(key, value) {
    return weak.def(validate(this, WEAK_MAP), key, value);
  }
};

// 23.3 WeakMap Objects
var $WeakMap = module.exports = require('./_collection')(WEAK_MAP, wrapper, methods, weak, true, true);

// IE11 WeakMap frozen keys fix
if (NATIVE_WEAK_MAP && IS_IE11) {
  InternalMap = weak.getConstructor(wrapper, WEAK_MAP);
  assign(InternalMap.prototype, methods);
  meta.NEED = true;
  each(['delete', 'has', 'get', 'set'], function (key) {
    var proto = $WeakMap.prototype;
    var method = proto[key];
    redefine(proto, key, function (a, b) {
      // store frozen objects on internal weakmap shim
      if (isObject(a) && !isExtensible(a)) {
        if (!this._f) this._f = new InternalMap();
        var result = this._f[key](a, b);
        return key == 'set' ? this : result;
      // store all the rest on native weakmap
      } return method.call(this, a, b);
    });
  });
}

},{"./_array-methods":23,"./_collection":32,"./_collection-weak":31,"./_global":51,"./_is-object":62,"./_meta":75,"./_object-assign":78,"./_redefine":99,"./_validate-collection":130}],272:[function(require,module,exports){
'use strict';
var weak = require('./_collection-weak');
var validate = require('./_validate-collection');
var WEAK_SET = 'WeakSet';

// 23.4 WeakSet Objects
require('./_collection')(WEAK_SET, function (get) {
  return function WeakSet() { return get(this, arguments.length > 0 ? arguments[0] : undefined); };
}, {
  // 23.4.3.1 WeakSet.prototype.add(value)
  add: function add(value) {
    return weak.def(validate(this, WEAK_SET), value, true);
  }
}, weak, false, true);

},{"./_collection":32,"./_collection-weak":31,"./_validate-collection":130}],273:[function(require,module,exports){
'use strict';
// https://tc39.github.io/proposal-flatMap/#sec-Array.prototype.flatMap
var $export = require('./_export');
var flattenIntoArray = require('./_flatten-into-array');
var toObject = require('./_to-object');
var toLength = require('./_to-length');
var aFunction = require('./_a-function');
var arraySpeciesCreate = require('./_array-species-create');

$export($export.P, 'Array', {
  flatMap: function flatMap(callbackfn /* , thisArg */) {
    var O = toObject(this);
    var sourceLen, A;
    aFunction(callbackfn);
    sourceLen = toLength(O.length);
    A = arraySpeciesCreate(O, 0);
    flattenIntoArray(A, O, O, sourceLen, 0, 1, callbackfn, arguments[1]);
    return A;
  }
});

require('./_add-to-unscopables')('flatMap');

},{"./_a-function":14,"./_add-to-unscopables":16,"./_array-species-create":26,"./_export":43,"./_flatten-into-array":48,"./_to-length":122,"./_to-object":123}],274:[function(require,module,exports){
'use strict';
// https://github.com/tc39/Array.prototype.includes
var $export = require('./_export');
var $includes = require('./_array-includes')(true);

$export($export.P, 'Array', {
  includes: function includes(el /* , fromIndex = 0 */) {
    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
  }
});

require('./_add-to-unscopables')('includes');

},{"./_add-to-unscopables":16,"./_array-includes":22,"./_export":43}],275:[function(require,module,exports){
// https://github.com/tc39/proposal-object-values-entries
var $export = require('./_export');
var $entries = require('./_object-to-array')(true);

$export($export.S, 'Object', {
  entries: function entries(it) {
    return $entries(it);
  }
});

},{"./_export":43,"./_object-to-array":91}],276:[function(require,module,exports){
// https://github.com/tc39/proposal-object-getownpropertydescriptors
var $export = require('./_export');
var ownKeys = require('./_own-keys');
var toIObject = require('./_to-iobject');
var gOPD = require('./_object-gopd');
var createProperty = require('./_create-property');

$export($export.S, 'Object', {
  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
    var O = toIObject(object);
    var getDesc = gOPD.f;
    var keys = ownKeys(O);
    var result = {};
    var i = 0;
    var key, desc;
    while (keys.length > i) {
      desc = getDesc(O, key = keys[i++]);
      if (desc !== undefined) createProperty(result, key, desc);
    }
    return result;
  }
});

},{"./_create-property":34,"./_export":43,"./_object-gopd":82,"./_own-keys":92,"./_to-iobject":121}],277:[function(require,module,exports){
// https://github.com/tc39/proposal-object-values-entries
var $export = require('./_export');
var $values = require('./_object-to-array')(false);

$export($export.S, 'Object', {
  values: function values(it) {
    return $values(it);
  }
});

},{"./_export":43,"./_object-to-array":91}],278:[function(require,module,exports){
// https://github.com/tc39/proposal-promise-finally
'use strict';
var $export = require('./_export');
var core = require('./_core');
var global = require('./_global');
var speciesConstructor = require('./_species-constructor');
var promiseResolve = require('./_promise-resolve');

$export($export.P + $export.R, 'Promise', { 'finally': function (onFinally) {
  var C = speciesConstructor(this, core.Promise || global.Promise);
  var isFunction = typeof onFinally == 'function';
  return this.then(
    isFunction ? function (x) {
      return promiseResolve(C, onFinally()).then(function () { return x; });
    } : onFinally,
    isFunction ? function (e) {
      return promiseResolve(C, onFinally()).then(function () { throw e; });
    } : onFinally
  );
} });

},{"./_core":33,"./_export":43,"./_global":51,"./_promise-resolve":96,"./_species-constructor":108}],279:[function(require,module,exports){
'use strict';
// https://github.com/tc39/proposal-string-pad-start-end
var $export = require('./_export');
var $pad = require('./_string-pad');
var userAgent = require('./_user-agent');

// https://github.com/zloirock/core-js/issues/280
var WEBKIT_BUG = /Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(userAgent);

$export($export.P + $export.F * WEBKIT_BUG, 'String', {
  padEnd: function padEnd(maxLength /* , fillString = ' ' */) {
    return $pad(this, maxLength, arguments.length > 1 ? arguments[1] : undefined, false);
  }
});

},{"./_export":43,"./_string-pad":113,"./_user-agent":129}],280:[function(require,module,exports){
'use strict';
// https://github.com/tc39/proposal-string-pad-start-end
var $export = require('./_export');
var $pad = require('./_string-pad');
var userAgent = require('./_user-agent');

// https://github.com/zloirock/core-js/issues/280
var WEBKIT_BUG = /Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(userAgent);

$export($export.P + $export.F * WEBKIT_BUG, 'String', {
  padStart: function padStart(maxLength /* , fillString = ' ' */) {
    return $pad(this, maxLength, arguments.length > 1 ? arguments[1] : undefined, true);
  }
});

},{"./_export":43,"./_string-pad":113,"./_user-agent":129}],281:[function(require,module,exports){
'use strict';
// https://github.com/sebmarkbage/ecmascript-string-left-right-trim
require('./_string-trim')('trimLeft', function ($trim) {
  return function trimLeft() {
    return $trim(this, 1);
  };
}, 'trimStart');

},{"./_string-trim":115}],282:[function(require,module,exports){
'use strict';
// https://github.com/sebmarkbage/ecmascript-string-left-right-trim
require('./_string-trim')('trimRight', function ($trim) {
  return function trimRight() {
    return $trim(this, 2);
  };
}, 'trimEnd');

},{"./_string-trim":115}],283:[function(require,module,exports){
require('./_wks-define')('asyncIterator');

},{"./_wks-define":131}],284:[function(require,module,exports){
var $iterators = require('./es6.array.iterator');
var getKeys = require('./_object-keys');
var redefine = require('./_redefine');
var global = require('./_global');
var hide = require('./_hide');
var Iterators = require('./_iterators');
var wks = require('./_wks');
var ITERATOR = wks('iterator');
var TO_STRING_TAG = wks('toStringTag');
var ArrayValues = Iterators.Array;

var DOMIterables = {
  CSSRuleList: true, // TODO: Not spec compliant, should be false.
  CSSStyleDeclaration: false,
  CSSValueList: false,
  ClientRectList: false,
  DOMRectList: false,
  DOMStringList: false,
  DOMTokenList: true,
  DataTransferItemList: false,
  FileList: false,
  HTMLAllCollection: false,
  HTMLCollection: false,
  HTMLFormElement: false,
  HTMLSelectElement: false,
  MediaList: true, // TODO: Not spec compliant, should be false.
  MimeTypeArray: false,
  NamedNodeMap: false,
  NodeList: true,
  PaintRequestList: false,
  Plugin: false,
  PluginArray: false,
  SVGLengthList: false,
  SVGNumberList: false,
  SVGPathSegList: false,
  SVGPointList: false,
  SVGStringList: false,
  SVGTransformList: false,
  SourceBufferList: false,
  StyleSheetList: true, // TODO: Not spec compliant, should be false.
  TextTrackCueList: false,
  TextTrackList: false,
  TouchList: false
};

for (var collections = getKeys(DOMIterables), i = 0; i < collections.length; i++) {
  var NAME = collections[i];
  var explicit = DOMIterables[NAME];
  var Collection = global[NAME];
  var proto = Collection && Collection.prototype;
  var key;
  if (proto) {
    if (!proto[ITERATOR]) hide(proto, ITERATOR, ArrayValues);
    if (!proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
    Iterators[NAME] = ArrayValues;
    if (explicit) for (key in $iterators) if (!proto[key]) redefine(proto, key, $iterators[key], true);
  }
}

},{"./_global":51,"./_hide":53,"./_iterators":69,"./_object-keys":88,"./_redefine":99,"./_wks":133,"./es6.array.iterator":145}],285:[function(require,module,exports){
var $export = require('./_export');
var $task = require('./_task');
$export($export.G + $export.B, {
  setImmediate: $task.set,
  clearImmediate: $task.clear
});

},{"./_export":43,"./_task":117}],286:[function(require,module,exports){
// ie9- setTimeout & setInterval additional parameters fix
var global = require('./_global');
var $export = require('./_export');
var userAgent = require('./_user-agent');
var slice = [].slice;
var MSIE = /MSIE .\./.test(userAgent); // <- dirty ie9- check
var wrap = function (set) {
  return function (fn, time /* , ...args */) {
    var boundArgs = arguments.length > 2;
    var args = boundArgs ? slice.call(arguments, 2) : false;
    return set(boundArgs ? function () {
      // eslint-disable-next-line no-new-func
      (typeof fn == 'function' ? fn : Function(fn)).apply(this, args);
    } : fn, time);
  };
};
$export($export.G + $export.B + $export.F * MSIE, {
  setTimeout: wrap(global.setTimeout),
  setInterval: wrap(global.setInterval)
});

},{"./_export":43,"./_global":51,"./_user-agent":129}],287:[function(require,module,exports){
require('../modules/web.timers');
require('../modules/web.immediate');
require('../modules/web.dom.iterable');
module.exports = require('../modules/_core');

},{"../modules/_core":33,"../modules/web.dom.iterable":284,"../modules/web.immediate":285,"../modules/web.timers":286}],288:[function(require,module,exports){
/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

var runtime = (function (exports) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  exports.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  IteratorPrototype[iteratorSymbol] = function () {
    return this;
  };

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
  GeneratorFunctionPrototype.constructor = GeneratorFunction;
  GeneratorFunctionPrototype[toStringTagSymbol] =
    GeneratorFunction.displayName = "GeneratorFunction";

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      prototype[method] = function(arg) {
        return this._invoke(method, arg);
      };
    });
  }

  exports.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  exports.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      if (!(toStringTagSymbol in genFun)) {
        genFun[toStringTagSymbol] = "GeneratorFunction";
      }
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  exports.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return Promise.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return Promise.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration.
          result.value = unwrapped;
          resolve(result);
        }, function(error) {
          // If a rejected Promise was yielded, throw the rejection back
          // into the async generator function so it can be handled there.
          return invoke("throw", error, resolve, reject);
        });
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new Promise(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  AsyncIterator.prototype[asyncIteratorSymbol] = function () {
    return this;
  };
  exports.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  exports.async = function(innerFn, outerFn, self, tryLocsList) {
    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList)
    );

    return exports.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        // Note: ["return"] must be used for ES3 parsing compatibility.
        if (delegate.iterator["return"]) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  Gp[toStringTagSymbol] = "Generator";

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  Gp[iteratorSymbol] = function() {
    return this;
  };

  Gp.toString = function() {
    return "[object Generator]";
  };

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  exports.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  exports.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };

  // Regardless of whether this script is executing as a CommonJS module
  // or not, return the runtime object so that we can declare the variable
  // regeneratorRuntime in the outer scope, which allows this module to be
  // injected easily by `bin/regenerator --include-runtime script.js`.
  return exports;

}(
  // If this script is executing as a CommonJS module, use module.exports
  // as the regeneratorRuntime namespace. Otherwise create a new empty
  // object. Either way, the resulting object will be used to initialize
  // the regeneratorRuntime variable at the top of this file.
  typeof module === "object" ? module.exports : {}
));

try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  // This module should not be running in strict mode, so the above
  // assignment should always work unless something is misconfigured. Just
  // in case runtime.js accidentally runs in strict mode, we can escape
  // strict mode using a global Function call. This could conceivably fail
  // if a Content Security Policy forbids using Function, but in that case
  // the proper solution is to fix the accidental strict mode problem. If
  // you've misconfigured your bundler to force strict mode and applied a
  // CSP to forbid Function, and you're not willing to fix either of those
  // problems, please detail your unique predicament in a GitHub issue.
  Function("r", "regeneratorRuntime = r")(runtime);
}

},{}],289:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MovementOrder = function () {
  function MovementOrder(id, type, position, target, facing, rolled, turn) {
    var value = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : 0;
    var requiredThrust = arguments.length > 8 && arguments[8] !== undefined ? arguments[8] : null;

    _classCallCheck(this, MovementOrder);

    if (!(position instanceof window.hexagon.Offset)) {
      throw new Error("MovementOrder requires position as offset hexagon");
    }

    this.id = id;
    this.type = type;
    this.position = position;
    this.target = target;
    this.facing = facing;
    this.rolled = rolled;
    this.turn = turn;
    this.value = value;
    this.requiredThrust = requiredThrust;
  }

  _createClass(MovementOrder, [{
    key: "serialize",
    value: function serialize() {
      return {
        id: this.id,
        type: this.type,
        position: this.position,
        target: this.target,
        facing: this.facing,
        rolled: this.rolled,
        turn: this.turn,
        value: this.value,
        requiredThrust: this.requiredThrust ? this.requiredThrust.serialize() : null
      };
    }
  }, {
    key: "isSpeed",
    value: function isSpeed() {
      return this.type === _.movementTypes.SPEED;
    }
  }, {
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
    key: "isRoll",
    value: function isRoll() {
      return this.type === _.movementTypes.ROLL;
    }
  }, {
    key: "isEnd",
    value: function isEnd() {
      return this.type === _.movementTypes.END;
    }
  }, {
    key: "isPivot",
    value: function isPivot() {
      return this.type === _.movementTypes.PIVOT;
    }
  }, {
    key: "isCancellable",
    value: function isCancellable() {
      return this.isSpeed() || this.isPivot();
    }
  }, {
    key: "isPlayerAdded",
    value: function isPlayerAdded() {
      return this.isSpeed() || this.isPivot() || this.isEvade() || this.isRoll();
    }
  }, {
    key: "clone",
    value: function clone() {
      return new MovementOrder(this.id, this.type, this.position, this.target, this.facing, this.rolled, this.turn, this.value, this.requiredThrust);
    }
  }, {
    key: "isOpposite",
    value: function isOpposite(move) {
      switch (move.type) {
        case _.movementTypes.SPEED:
          return this.isSpeed() && this.value === mathlib.addToHexFacing(move.value, 3);
        default:
          return false;
      }
    }
  }]);

  return MovementOrder;
}();

window.MovementOrder = MovementOrder;
exports.default = MovementOrder;

},{".":298}],290:[function(require,module,exports){
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

    this.color = new THREE.Color(132 / 255, 165 / 255, 206 / 255);

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

      var end = this.movementService.getLastEndMove(this.ship);
      var move = this.movementService.getMostRecentMove(this.ship);
      var target = this.movementService.getCurrentMovementVector(this.ship);

      var startPosition = end.position;
      var middlePosition = end.position.add(end.target);
      var finalPosition = startPosition.add(target);

      var line = createMovementLine(startPosition, middlePosition, this.color, 0.5);
      this.scene.add(line.mesh);
      this.objects.push(line);

      if (!middlePosition.equals(finalPosition)) {
        var middle = createMovementMiddleStep(middlePosition, this.color);
        this.scene.add(middle.mesh);
        this.objects.push(middle);
      }

      var line2 = createMovementLine(middlePosition, finalPosition, this.color);
      this.scene.add(line2.mesh);
      this.objects.push(line2);

      var facing = createMovementFacing(move.facing, finalPosition, this.color);
      this.scene.add(facing.mesh);
      this.objects.push(facing);
    }
  }]);

  return MovementPath;
}();

var createMovementMiddleStep = function createMovementMiddleStep(position, color) {
  var size = window.coordinateConverter.getHexDistance() * 0.5;
  var circle = new window.ShipSelectedSprite({ width: size, height: size }, 0.01, 1.6);
  circle.setPosition(window.coordinateConverter.fromHexToGame(position));
  circle.setOverlayColor(color);
  circle.setOverlayColorAlpha(1);
  return circle;
};

var createMovementLine = function createMovementLine(position, target, color) {
  var opacity = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0.8;

  var start = window.coordinateConverter.fromHexToGame(position);
  var end = window.coordinateConverter.fromHexToGame(target);

  return new window.LineSprite(mathlib.getPointBetweenInDistance(start, end, window.coordinateConverter.getHexDistance() * 0.45, true), mathlib.getPointBetweenInDistance(end, start, window.coordinateConverter.getHexDistance() * 0.45, true), 10, color, opacity);
};

var createMovementFacing = function createMovementFacing(facing, target, color) {
  var size = window.coordinateConverter.getHexDistance() * 1.5;
  var facingSprite = new window.ShipFacingSprite({ width: size, height: size }, 0.01, 1.6, facing);
  facingSprite.setPosition(window.coordinateConverter.fromHexToGame(target));
  facingSprite.setOverlayColor(color);
  facingSprite.setOverlayColorAlpha(1);
  facingSprite.setFacing(mathlib.hexFacingToAngle(facing));

  return facingSprite;
};

exports.createMovementLine = createMovementLine;
exports.default = MovementPath;

},{}],291:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MovementResolver = function () {
  function MovementResolver(ship, movementService, turn) {
    _classCallCheck(this, MovementResolver);

    this.ship = ship;
    this.movementService = movementService;
    this.turn = turn;
  }

  _createClass(MovementResolver, [{
    key: "billAndPay",
    value: function billAndPay(bill) {
      var commit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      if (bill.pay()) {
        var newMovement = bill.getMoves();

        var initialOverChannel = new _.OverChannelResolver(this.movementService.getThrusters(this.ship), this.movementService.getThisTurnMovement(this.ship)).getAmountOverChanneled();

        var newOverChannel = new _.OverChannelResolver(this.movementService.getThrusters(this.ship), newMovement).getAmountOverChanneled();

        /*
        console.log(
          "overChannel",
          initialOverChannel,
          newOverChannel,
          newOverChannel - initialOverChannel
        );
        */

        if (commit) {
          this.movementService.replaceTurnMovement(this.ship, newMovement);
          this.movementService.shipMovementChanged(this.ship);
        }
        return {
          result: true,
          overChannel: newOverChannel - initialOverChannel > 0
        };
      } else if (commit) {
        throw new Error("Tried to commit move that was not legal. Check legality first!");
      } else {
        return false;
      }
    }
  }, {
    key: "canRoll",
    value: function canRoll() {
      return this.roll(false);
    }
  }, {
    key: "roll",
    value: function roll() {
      var commit = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

      var rollMove = this.movementService.getRollMove(this.ship);

      var endMove = this.movementService.getLastEndMove(this.ship);

      var movements = this.movementService.getThisTurnMovement(this.ship);

      if (rollMove) {
        movements = movements.filter(function (move) {
          return move !== rollMove;
        });
      } else {
        rollMove = new _.MovementOrder(null, _.movementTypes.ROLL, endMove.position, new hexagon.Offset(0, 0), endMove.facing, endMove.rolled, this.turn, endMove.rolled ? false : true);

        var playerAdded = movements.filter(function (move) {
          return move.isPlayerAdded();
        });
        var nonPlayerAdded = movements.filter(function (move) {
          return !move.isPlayerAdded();
        });

        movements = [].concat(_toConsumableArray(nonPlayerAdded), [rollMove], _toConsumableArray(playerAdded));
      }

      var bill = new _.ThrustBill(this.ship, this.movementService.getTotalProducedThrust(this.ship), movements);

      return this.billAndPay(bill, commit);
    }
  }, {
    key: "canEvade",
    value: function canEvade(step) {
      return this.evade(step, false);
    }
  }, {
    key: "evade",
    value: function evade(step) {
      var commit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      var evadeMove = this.movementService.getEvadeMove(this.ship);

      var endMove = this.movementService.getLastEndMove(this.ship);

      if (evadeMove) {
        evadeMove = evadeMove.clone();

        if (evadeMove.value + step > this.movementService.getMaximumEvasion(this.ship)) {
          return false;
        }

        if (evadeMove.value + step < 0) {
          return false;
        }
        evadeMove.value += step;
      } else {
        if (step < 0) {
          return false;
        }

        evadeMove = new _.MovementOrder(null, _.movementTypes.EVADE, endMove.position, new hexagon.Offset(0, 0), endMove.facing, endMove.rolled, this.turn, 1);
      }

      var playerAdded = this.movementService.getThisTurnMovement(this.ship).filter(function (move) {
        return move.isPlayerAdded() && !move.isRoll() && !move.isEvade();
      });
      var nonPlayerAdded = this.movementService.getThisTurnMovement(this.ship).filter(function (move) {
        return !move.isPlayerAdded() || move.isRoll();
      });

      var movements = evadeMove.value === 0 ? [].concat(_toConsumableArray(nonPlayerAdded), _toConsumableArray(playerAdded)) : [].concat(_toConsumableArray(nonPlayerAdded), [evadeMove], _toConsumableArray(playerAdded));

      var bill = new _.ThrustBill(this.ship, this.movementService.getTotalProducedThrust(this.ship), movements);

      return this.billAndPay(bill, commit);
    }
  }, {
    key: "canPivot",
    value: function canPivot(pivotDirection) {
      return this.pivot(pivotDirection, false);
    }
  }, {
    key: "pivot",
    value: function pivot(pivotDirection) {
      var commit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      var lastMove = this.movementService.getMostRecentMove(this.ship);
      var pivotMove = new _.MovementOrder(null, _.movementTypes.PIVOT, lastMove.position, new hexagon.Offset(0, 0), mathlib.addToHexFacing(lastMove.facing, pivotDirection), lastMove.rolled, this.turn, pivotDirection);

      var movements = this.movementService.getThisTurnMovement(this.ship);

      if (lastMove.isPivot() && lastMove.value !== pivotDirection) {
        movements.pop();
      } else {
        movements.push(pivotMove);
      }

      var bill = new _.ThrustBill(this.ship, this.movementService.getTotalProducedThrust(this.ship), movements);

      return this.billAndPay(bill, commit);
    }
  }, {
    key: "canThrust",
    value: function canThrust(direction) {
      return this.thrust(direction, false);
    }
  }, {
    key: "thrust",
    value: function thrust(direction) {
      var commit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      var lastMove = this.movementService.getMostRecentMove(this.ship);

      var thrustMove = new _.MovementOrder(null, _.movementTypes.SPEED, lastMove.position, new hexagon.Offset(0, 0).moveToDirection(direction), lastMove.facing, lastMove.rolled, this.turn, direction);

      var movements = this.movementService.getThisTurnMovement(this.ship);
      var opposite = this.getOpposite(movements, thrustMove);
      if (opposite) {
        movements = movements.filter(function (move) {
          return move !== opposite;
        });
      } else {
        movements.push(thrustMove);
      }

      var bill = new _.ThrustBill(this.ship, this.movementService.getTotalProducedThrust(this.ship), movements);

      return this.billAndPay(bill, commit);
    }
  }, {
    key: "canCancel",
    value: function canCancel() {
      return this.movementService.getThisTurnMovement(this.ship).some(function (move) {
        return move.isCancellable();
      });
    }
  }, {
    key: "cancel",
    value: function cancel() {
      var toCancel = this.ship.movement[this.ship.movement.length - 1];

      if (!toCancel || !toCancel.isCancellable()) {
        return;
      }

      this.removeMove(toCancel);

      var bill = new _.ThrustBill(this.ship, this.movementService.getTotalProducedThrust(this.ship), this.movementService.getThisTurnMovement(this.ship));

      return this.billAndPay(bill, true);
    }
  }, {
    key: "canRevert",
    value: function canRevert() {
      return this.movementService.getThisTurnMovement(this.ship).some(function (move) {
        return move.isPlayerAdded();
      });
    }
  }, {
    key: "revert",
    value: function revert() {
      this.movementService.getThisTurnMovement(this.ship).filter(function (move) {
        return move.isCancellable() || move.isEvade();
      }).forEach(this.removeMove.bind(this));

      this.movementService.shipMovementChanged(this.ship);
    }
  }, {
    key: "getOpposite",
    value: function getOpposite(movements, move) {
      return movements.find(function (other) {
        return other.isOpposite(move);
      });
    }
  }, {
    key: "removeMove",
    value: function removeMove(move) {
      this.ship.movement = this.ship.movement.filter(function (other) {
        return other !== move;
      });
    }
  }]);

  return MovementResolver;
}();

exports.default = MovementResolver;

},{".":298}],292:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MovementService = function () {
  function MovementService() {
    _classCallCheck(this, MovementService);

    this.gamedata = null;
  }

  _createClass(MovementService, [{
    key: "getCurrentMovementVector",
    value: function getCurrentMovementVector(ship) {
      var moves = this.getThisTurnMovement(ship);
      return moves.reduce(function (vector, move) {
        if (move.isDeploy() || move.isEnd()) {
          return move.target;
        } else if (move.isSpeed()) {
          return vector.add(move.target);
        }

        return vector;
      }, new hexagon.Offset(0, 0));
    }
  }, {
    key: "update",
    value: function update(gamedata, phaseStrategy) {
      this.gamedata = gamedata;
      this.phaseStrategy = phaseStrategy;
    }
  }, {
    key: "replaceTurnMovement",
    value: function replaceTurnMovement(ship, newMovement) {
      var _this = this;

      ship.movement = [].concat(_toConsumableArray(ship.movement.filter(function (move) {
        return move.turn !== _this.gamedata.turn || move.turn === _this.gamedata.turn && !move.isPlayerAdded();
      })), _toConsumableArray(newMovement.filter(function (move) {
        return move.isPlayerAdded();
      })));
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
      var _this2 = this;

      var move = ship.movement.slice().reverse().find(function (move) {
        return move.turn === _this2.gamedata.turn;
      });
      if (move) {
        return move;
      }

      return ship.movement[ship.movement.length - 1];
    }
  }, {
    key: "isMoved",
    value: function isMoved(ship, turn) {
      var end = this.getLastEndMove(ship);

      if (!end || !end.isEnd()) {
        return false;
      }

      return end.turn === turn;
    }
  }, {
    key: "getLastEndMove",
    value: function getLastEndMove(ship) {
      var end = ship.movement.slice().reverse().find(function (move) {
        return move.isEnd();
      });

      if (!end) {
        end = this.getDeployMove(ship);
      }

      if (!end) {
        end = ship.movement[0];
      }

      return end;
    }
  }, {
    key: "getLastTurnEndMove",
    value: function getLastTurnEndMove(ship) {
      var _this3 = this;

      var end = ship.movement.slice().reverse().find(function (move) {
        return move.isEnd() && move.turn === _this3.turn - 1;
      });

      if (!end) {
        end = this.getDeployMove(ship);
      }

      if (!end) {
        end = ship.movement[0];
      }

      return end;
    }
  }, {
    key: "getAllMovesOfTurn",
    value: function getAllMovesOfTurn(ship) {
      var _this4 = this;

      return ship.movement.filter(function (move) {
        return move.turn === _this4.gamedata.turn;
      });
    }
  }, {
    key: "getShipsInSameHex",
    value: function getShipsInSameHex(ship, hex) {
      var _this5 = this;

      hex = hex && this.getMostRecentMove(ship).position;
      return this.gamedata.ships.filter(function (ship2) {
        return !shipManager.isDestroyed(ship2) && ship !== ship2 && _this5.getMostRecentMove(ship2).position.equals(hex);
      });
    }
  }, {
    key: "deploy",
    value: function deploy(ship, pos) {
      var deployMove = this.getDeployMove(ship);

      if (!deployMove) {
        var lastMove = this.getMostRecentMove(ship);
        deployMove = new _.MovementOrder(-1, _.movementTypes.DEPLOY, pos, lastMove.target, lastMove.facing, lastMove.rolled, this.gamedata.turn);
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
      var newfacing = mathlib.addToHexFacing(deploymove.facing, step);
      deploymove.facing = newfacing;
    }
  }, {
    key: "getEvadeMove",
    value: function getEvadeMove(ship) {
      return this.getThisTurnMovement(ship).find(function (move) {
        return move.isEvade();
      });
    }
  }, {
    key: "getRollMove",
    value: function getRollMove(ship) {
      return this.getThisTurnMovement(ship).find(function (move) {
        return move.isRoll();
      });
    }
  }, {
    key: "getEvasion",
    value: function getEvasion(ship) {
      var evadeMove = this.getEvadeMove(ship);
      return evadeMove ? evadeMove.value : 0;
    }
  }, {
    key: "getMaximumEvasion",
    value: function getMaximumEvasion(ship) {
      var max = ship.systems.filter(function (system) {
        return !system.isDestroyed() && system.maxEvasion > 0;
      }).reduce(function (total, system) {
        return total + system.maxEvasion;
      }, 0);

      return max;
    }
  }, {
    key: "getOverChannel",
    value: function getOverChannel(ship) {
      return new _.OverChannelResolver(this.getThrusters(ship), this.getThisTurnMovement(ship)).getAmountOverChanneled();
    }
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
        return accumulated + shipManager.systems.getOutput(ship, system) - crits;
      }, 0);
    }
  }, {
    key: "getRemainingEngineThrust",
    value: function getRemainingEngineThrust(ship) {
      return this.getTotalProducedThrust(ship) - this.getUsedEngineThrust(ship);
    }
  }, {
    key: "getUsedEngineThrust",
    value: function getUsedEngineThrust(ship) {
      return this.getThisTurnMovement(ship).filter(function (move) {
        return move.requiredThrust;
      }).reduce(function (total, move) {
        return total + move.requiredThrust.getTotalAmountRequired();
      }, 0);
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
    key: "getThisTurnMovement",
    value: function getThisTurnMovement(ship) {
      var _this6 = this;

      return ship.movement.filter(function (move) {
        return move.turn === _this6.gamedata.turn || move.isEnd() && move.turn === _this6.gamedata.turn - 1 || move.isDeploy();
      });
    }
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged(ship) {
      this.phaseStrategy.onShipMovementChanged({ ship: ship });
    }
  }, {
    key: "canThrust",
    value: function canThrust(ship, direction) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).canThrust(direction);
    }
  }, {
    key: "thrust",
    value: function thrust(ship, direction) {
      new _.MovementResolver(ship, this, this.gamedata.turn).thrust(direction);
    }
  }, {
    key: "canCancel",
    value: function canCancel(ship) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).canCancel();
    }
  }, {
    key: "canRevert",
    value: function canRevert(ship) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).canRevert();
    }
  }, {
    key: "cancel",
    value: function cancel(ship) {
      new _.MovementResolver(ship, this, this.gamedata.turn).cancel();
    }
  }, {
    key: "revert",
    value: function revert(ship) {
      new _.MovementResolver(ship, this, this.gamedata.turn).revert();
    }
  }, {
    key: "canPivot",
    value: function canPivot(ship, turnDirection) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).canPivot(turnDirection);
    }
  }, {
    key: "pivot",
    value: function pivot(ship, turnDirection) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).pivot(turnDirection);
    }
  }, {
    key: "canRoll",
    value: function canRoll(ship) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).canRoll();
    }
  }, {
    key: "roll",
    value: function roll(ship) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).roll();
    }
  }, {
    key: "canEvade",
    value: function canEvade(ship, step) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).canEvade(step);
    }
  }, {
    key: "evade",
    value: function evade(ship, step) {
      return new _.MovementResolver(ship, this, this.gamedata.turn).evade(step);
    }
  }, {
    key: "getThrusters",
    value: function getThrusters(ship) {
      return ship.systems.filter(function (system) {
        return system.thruster;
      }).filter(function (system) {
        return !system.isDestroyed();
      });
    }
  }]);

  return MovementService;
}();

window.MovementService = MovementService;
exports.default = MovementService;

},{".":298}],293:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
var movementTypes = {
  START: "start",
  END: "end",
  DEPLOY: "deploy",
  SPEED: "speed",
  PIVOT: "pivot",
  EVADE: "evade",
  ROLL: "roll"
};

window.movementTypes = movementTypes;
exports.default = movementTypes;

},{}],294:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var OverChannelResolver = function () {
  function OverChannelResolver(thrusters, movement) {
    _classCallCheck(this, OverChannelResolver);

    this.thrusters = thrusters.map(function (thruster) {
      return {
        channeled: 0,
        limit: thruster.output,
        thruster: thruster
      };
    });

    this.movement = movement;
  }

  _createClass(OverChannelResolver, [{
    key: "getAmountOverChanneled",
    value: function getAmountOverChanneled() {
      var _this = this;

      this.movement.filter(function (move) {
        return move.requiredThrust;
      }).forEach(function (move) {
        return move.requiredThrust.getFulfilments().forEach(function (fulfilment) {
          _this.track(fulfilment);
        });
      });

      return this.thrusters.reduce(function (total, thruster) {
        return total + _this.getThrusterOverChannel(thruster);
      }, 0);
    }
  }, {
    key: "track",
    value: function track(fulfilments) {
      var _this2 = this;

      fulfilments.forEach(function (fulfilment) {
        var thruster = _this2.thrusters.find(function (thruster) {
          return thruster.thruster === fulfilment.thruster;
        });

        thruster.channeled += fulfilment.amount;
      });
    }
  }, {
    key: "getThrusterOverChannel",
    value: function getThrusterOverChannel(thruster) {
      if (thruster.channeled > thruster.limit) {
        return thruster.channeled - thruster.limit;
      }

      return 0;
    }
  }]);

  return OverChannelResolver;
}();

exports.default = OverChannelResolver;

},{}],295:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var RequiredThrust = function () {
  function RequiredThrust(ship, move) {
    _classCallCheck(this, RequiredThrust);

    this.requirements = {};
    this.fullfilments = {
      0: [],
      1: [],
      2: [],
      3: [],
      4: [],
      5: [],
      6: []
    };

    switch (move.type) {
      case _.movementTypes.SPEED:
        this.requireSpeed(ship, move);
        break;
      case _.movementTypes.PIVOT:
        this.requirePivot(ship);
        break;
      case _.movementTypes.ROLL:
        this.requireRoll(ship);
        break;
      case _.movementTypes.EVADE:
        this.requireEvade(ship, move);
        break;
      default:
    }
  }

  _createClass(RequiredThrust, [{
    key: "serialize",
    value: function serialize() {
      var _this = this;

      var fullfilments = {
        0: [],
        1: [],
        2: [],
        3: [],
        4: [],
        5: [],
        6: []
      };

      Object.keys(this.fullfilments).forEach(function (direction) {
        var entryArray = _this.fullfilments[direction].map(function (fulfilment) {
          return {
            amount: fulfilment.amount,
            thrusterId: fulfilment.thruster.id
          };
        });

        fullfilments[direction] = entryArray;
      });

      return {
        requirements: this.requirements,
        fullfilments: fullfilments
      };
    }
  }, {
    key: "getTotalAmountRequired",
    value: function getTotalAmountRequired() {
      var _this2 = this;

      return Object.keys(this.requirements).reduce(function (total, direction) {
        var required = _this2.requirements[direction] || 0;
        return total + required;
      }, 0);
    }
  }, {
    key: "getRequirement",
    value: function getRequirement(direction) {
      if (!this.requirements[direction]) {
        return 0;
      }

      return this.requirements[direction] - this.getFulfilledAmount(direction);
    }
  }, {
    key: "isFulfilled",
    value: function isFulfilled() {
      var _this3 = this;

      return Object.keys(this.requirements).every(function (direction) {
        return _this3.getRequirement(direction) === 0;
      });
    }
  }, {
    key: "fulfill",
    value: function fulfill(direction, amount, thruster) {
      this.fullfilments[direction].push({ amount: amount, thruster: thruster });
      if (this.requirements[direction] < this.getFulfilledAmount(direction)) {
        throw new Error("Fulfilled too much!");
      }
    }
  }, {
    key: "getFulfilledAmount",
    value: function getFulfilledAmount(direction) {
      return this.fullfilments[direction].reduce(function (total, entry) {
        return total + entry.amount;
      }, 0);
    }
  }, {
    key: "getFulfilments",
    value: function getFulfilments() {
      var _this4 = this;

      return Object.keys(this.fullfilments).map(function (key) {
        return _this4.fullfilments[key];
      }).filter(function (fulfillment) {
        return fulfillment.length > 0;
      });
    }
  }, {
    key: "requireRoll",
    value: function requireRoll(ship) {
      this.requirements[6] = ship.rollcost;
    }
  }, {
    key: "requireEvade",
    value: function requireEvade(ship, move) {
      this.requirements[6] = ship.evasioncost * move.value;
    }
  }, {
    key: "requirePivot",
    value: function requirePivot(ship) {
      this.requirements[6] = ship.pivotcost;
    }
  }, {
    key: "requireSpeed",
    value: function requireSpeed(ship, move) {
      var facing = move.facing;
      var direction = move.value;
      var actualDirection = window.mathlib.addToHexFacing(window.mathlib.addToHexFacing(direction, -facing), 3);

      this.requirements[actualDirection] = ship.accelcost;
    }
  }, {
    key: "accumulate",
    value: function accumulate(total) {
      var _this5 = this;

      Object.keys(this.requirements).forEach(function (direction) {
        total[direction] = total[direction] ? total[direction] + _this5.requirements[direction] : _this5.requirements[direction];
      });

      return total;
    }
  }]);

  return RequiredThrust;
}();

exports.default = RequiredThrust;

},{".":298}],296:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ThrustAssignment = function () {
  function ThrustAssignment(thruster) {
    _classCallCheck(this, ThrustAssignment);

    this.thruster = thruster;

    this.directions = [].concat(thruster.direction);
    this.paid = 0;
    this.channeled = 0;
    this.capacity = thruster.output;

    this.firstIgnored = window.shipManager.criticals.hasCritical(thruster, "FirstThrustIgnored");

    this.halfEfficiency = window.shipManager.criticals.hasCritical(thruster, "HalfEfficiency");

    this.damaged = this.firstIgnored || this.halfEfficiency;
  }

  _createClass(ThrustAssignment, [{
    key: "isDamaged",
    value: function isDamaged() {
      return this.damaged;
    }
  }, {
    key: "isDirection",
    value: function isDirection(direction) {
      return this.directions.includes(direction);
    }
  }, {
    key: "canChannel",
    value: function canChannel() {
      return this.channeled < this.capacity;
    }
  }, {
    key: "canOverChannel",
    value: function canOverChannel() {
      return !this.damaged && this.channeled < this.capacity * 2;
    }
  }, {
    key: "getOverChannel",
    value: function getOverChannel() {
      var overThrust = this.channeled - this.capacity;
      if (overThrust < 0) {
        overThrust = 0;
      }

      return overThrust;
    }
  }, {
    key: "getDamageLevel",
    value: function getDamageLevel() {
      if (this.firstIgnored && !this.halfEfficiency && this.channeled === 0) {
        return 1;
      } else if (this.halfEfficiency && (!this.firstIgnored || this.channeled > 0)) {
        return 2;
      } else if (this.halfEfficiency && this.firstIgnored && this.channeled === 0) {
        return 3;
      } else {
        return 0;
      }
    }
  }, {
    key: "getThrustCapacity",
    value: function getThrustCapacity() {
      var result = {
        capacity: this.capacity - this.channeled,
        overCapacity: 0,
        extraCost: this.firstIgnored && this.channeled === 0 ? 1 : 0,
        costMultiplier: this.halfEfficiency ? 2 : 1
      };

      if (!this.damaged) {
        if (this.channeled <= this.capacity) {
          result.overCapacity = this.capacity;
        } else {
          result.overCapacity = this.capacity - (this.channeled - this.capacity);
        }
      }

      if (result.capacity < 0) {
        result.capacity = 0;
      }

      return result;
    }
  }, {
    key: "overChannel",
    value: function overChannel(amount) {
      return this.channel(amount, true);
    }
  }, {
    key: "channel",
    value: function channel(amount) {
      var overthrust = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

      var _getThrustCapacity = this.getThrustCapacity(),
          capacity = _getThrustCapacity.capacity,
          overCapacity = _getThrustCapacity.overCapacity,
          extraCost = _getThrustCapacity.extraCost,
          costMultiplier = _getThrustCapacity.costMultiplier;

      var result = {
        channeled: 0,
        overChanneled: 0,
        cost: 0
      };

      if (capacity >= amount) {
        result.channeled = amount;
        amount = 0;
      } else {
        result.channeled = capacity;
        amount -= capacity;
      }

      if (amount > 0 && overthrust) {
        if (overCapacity >= amount) {
          result.overChanneled = amount;
          amount = 0;
        } else {
          result.overChanneled = overCapacity;
        }
      }

      result.cost = (result.channeled + result.overChanneled) * costMultiplier + extraCost;

      this.channeled += result.channeled + result.overChanneled;
      return result;
    }
  }, {
    key: "undoChannel",
    value: function undoChannel(amount) {
      if (this.channeled - amount < 0) {
        throw new Error("Can not undo channel more than channeled");
      }

      this.channeled = this.channeled - amount;

      var extraRefund = 0;

      if (this.channeled === 0 && this.firstIgnored) {
        extraRefund = 1;
      }

      if (this.halfEfficiency) {
        return { refund: amount * 2 + extraRefund };
      } else {
        return { refund: amount + extraRefund };
      }
    }
  }]);

  return ThrustAssignment;
}();

exports.default = ThrustAssignment;

},{}],297:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require(".");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ThrustBill = function () {
  function ThrustBill(ship, thrustAvailable, movement) {
    _classCallCheck(this, ThrustBill);

    this.ship = ship;
    this.movement = movement.map(function (move) {
      return move.clone();
    });
    this.thrusters = ship.systems.filter(function (system) {
      return system.thruster;
    }).filter(function (system) {
      return !system.isDestroyed();
    }).map(function (thruster) {
      return new _.ThrustAssignment(thruster);
    });

    this.buildRequiredThrust(this.movement);

    this.paid = null;

    this.cost = 0;
    this.thrustAvailable = thrustAvailable;
    this.directionsRequired = this.getRequiredThrustDirections();
  }

  _createClass(ThrustBill, [{
    key: "getRequiredThrustDirections",
    value: function getRequiredThrustDirections() {
      var result = this.movement.reduce(function (accumulator, move) {
        return move.requiredThrust.accumulate(accumulator);
      }, {});

      result[0] = result[0] || 0;
      result[1] = result[1] || 0;
      result[2] = result[2] || 0;
      result[3] = result[3] || 0;
      result[4] = result[4] || 0;
      result[5] = result[5] || 0;
      result[6] = result[6] || 0;

      return result;
    }
  }, {
    key: "getTotalThrustRequired",
    value: function getTotalThrustRequired() {
      var totalRequired = this.getRequiredThrustDirections();
      return totalRequired[0] + totalRequired[1] + totalRequired[2] + totalRequired[3] + totalRequired[4] + totalRequired[5] + totalRequired[6];
    }
  }, {
    key: "getCurrentThrustRequired",
    value: function getCurrentThrustRequired() {
      return this.directionsRequired[0] + this.directionsRequired[1] + this.directionsRequired[2] + this.directionsRequired[3] + this.directionsRequired[4] + this.directionsRequired[5] + this.directionsRequired[6];
    }
  }, {
    key: "isPaid",
    value: function isPaid() {
      return this.getCurrentThrustRequired() === 0;
    }
  }, {
    key: "getUndamagedThrusters",
    value: function getUndamagedThrusters(direction) {
      return this.thrusters.filter(function (thruster) {
        return thruster.getDamageLevel() === 0 && thruster.isDirection(direction);
      }).sort(this.sortThrusters);
    }
  }, {
    key: "getAllUsableThrusters",
    value: function getAllUsableThrusters(direction) {
      return this.thrusters.filter(function (thruster) {
        var _thruster$getThrustCa = thruster.getThrustCapacity(),
            capacity = _thruster$getThrustCa.capacity,
            overCapacity = _thruster$getThrustCa.overCapacity;

        return thruster.isDirection(direction) && (capacity > 0 || overCapacity > 0);
      }).sort(this.sortThrusters);
    }
  }, {
    key: "getOverChannelers",
    value: function getOverChannelers(direction) {
      return this.thrusters.filter(function (thruster) {
        return thruster.isDirection(direction);
      }).filter(function (thruster) {
        return thruster.getOverChannel() > 0;
      }).filter(function (thruster) {
        return !thruster.isDamaged();
      }).sort(this.sortThrusters);
    }
  }, {
    key: "getNonOverChannelers",
    value: function getNonOverChannelers(direction) {
      var overChannelers = this.getOverChannelers(direction);
      return this.getAllUsableThrusters(direction).filter(function (thruster) {
        return thruster.canChannel();
      }).filter(function (thruster) {
        return !overChannelers.includes(thruster);
      });
    }
  }, {
    key: "sortThrusters",
    value: function sortThrusters(a, b) {
      var damageA = a.getDamageLevel();
      var damageB = b.getDamageLevel();

      if (damageA !== damageB) {
        if (damageA > damageB) {
          return 1;
        } else {
          return -1;
        }
      }

      if (a.firstIgnored && !b.firstIgnored) {
        return -1;
      } else if (b.firstIgnored && !a.firstIgnored) {
        return 1;
      }

      var _a$getThrustCapacity = a.getThrustCapacity(),
          capacityA = _a$getThrustCapacity.capacity,
          overCapacityA = _a$getThrustCapacity.overCapacity;

      var _b$getThrustCapacity = b.getThrustCapacity(),
          capacityB = _b$getThrustCapacity.capacity,
          overCapacityB = _b$getThrustCapacity.overCapacity;

      if (capacityA !== capacityB) {
        if (capacityA > capacityB) {
          return -1;
        } else {
          return 1;
        }
      }

      if (overCapacityA !== overCapacityB) {
        if (overCapacityA > overCapacityB) {
          return -1;
        } else {
          return 1;
        }
      }

      return 0;
    }
  }, {
    key: "isOverChanneled",
    value: function isOverChanneled() {
      return this.thrusters.some(function (thruster) {
        return thruster.getOverChannel() > 0;
      });
    }
  }, {
    key: "errorIfOverBudget",
    value: function errorIfOverBudget() {
      if (this.isOverBudget()) {
        throw new Error("over budget");
      }
    }
  }, {
    key: "isOverBudget",
    value: function isOverBudget() {
      return this.cost > this.thrustAvailable;
    }
  }, {
    key: "pay",
    value: function pay() {
      var _this = this;

      if (this.paid !== null) {
        throw new Error("Thrust bill is already paid!");
      }

      try {
        if (this.getTotalThrustRequired() > this.thrustAvailable) {
          throw new Error("over budget");
        }

        if (this.process(function (direction) {
          return _this.getUndamagedThrusters(direction);
        }, false) //do not overthrust
        ) {
            this.paid = true;
            return true;
          }

        this.process(function (direction) {
          return _this.getUndamagedThrusters(direction);
        }, true); //OVERTHRUST

        this.process(function (direction) {
          return _this.getAllUsableThrusters(direction);
        }, true); //use damaged thrusters too

        this.reallocateOverChannelForAllDirections(); //try to move over channel from good thrusters to already damaged ones

        this.paid = this.isPaid();
        return this.paid;
      } catch (e) {
        if (e.message === "over budget") {
          this.paid = false;
          return this.paid;
        }

        throw e;
      }
    }
  }, {
    key: "reallocateOverChannelForAllDirections",
    value: function reallocateOverChannelForAllDirections() {
      var _this2 = this;

      Object.keys(this.directionsRequired).forEach(function (direction) {
        direction = parseInt(direction, 10);

        _this2.reallocateOverChannel(direction);
      });
    }
  }, {
    key: "reallocateOverChannel",
    value: function reallocateOverChannel(direction) {
      var _this3 = this;

      var overChannelers = this.getOverChannelers(direction);

      overChannelers.forEach(function (thruster) {
        return _this3.reallocateSingleOverChannelThruster(thruster, direction, _this3.getNonOverChannelers(direction));
      });
    }
  }, {
    key: "reallocateSingleOverChannelThruster",
    value: function reallocateSingleOverChannelThruster(thruster, direction, otherThrusters) {
      var _this4 = this;

      if (otherThrusters.length === 0) {
        return;
      }

      otherThrusters.forEach(function (otherThruster) {
        while (thruster.getOverChannel() > 0) {
          var _otherThruster$getThr = otherThruster.getThrustCapacity(),
              capacity = _otherThruster$getThr.capacity;

          if (capacity === 0) {
            return;
          }

          _this4.undoThrusterUse(thruster, direction, 1);

          _this4.useThruster(otherThruster, direction, 1);

          if (_this4.isOverBudget()) {
            _this4.undoThrusterUse(otherThruster, direction, 1);
            _this4.useThruster(thruster, direction, 1, true);
            return; //tried to, but best thruster was too expensive
          }
        }
      });
    }
  }, {
    key: "process",
    value: function process(thrusterProvider) {
      var _this5 = this;

      var overChannel = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

      Object.keys(this.directionsRequired).forEach(function (direction) {
        var required = _this5.directionsRequired[direction];
        direction = parseInt(direction, 10);

        if (required === 0) {
          return;
        }

        var thrusters = thrusterProvider(direction);
        _this5.useThrusters(direction, required, thrusters, overChannel);
      });

      return this.isPaid();
    }
  }, {
    key: "useThrusters",
    value: function useThrusters(direction, required, thrusters) {
      var _this6 = this;

      var allowOverChannel = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

      thrusters.forEach(function (thruster) {
        if (required <= 0) {
          return;
        }

        if (!thruster.isDirection(direction)) {
          throw new Error("Trying to use thruster to wrong direction");
        }

        required = _this6.useThruster(thruster, direction, required, allowOverChannel);

        _this6.errorIfOverBudget();
      });
    }
  }, {
    key: "useThruster",
    value: function useThruster(thruster, direction, amount) {
      var allowOverChannel = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

      var _thruster$channel = thruster.channel(amount, allowOverChannel),
          channeled = _thruster$channel.channeled,
          overChanneled = _thruster$channel.overChanneled,
          cost = _thruster$channel.cost;

      this.directionsRequired[direction] -= channeled;
      this.directionsRequired[direction] -= overChanneled;

      this.cost += cost;

      amount -= channeled;
      amount -= overChanneled;

      return amount;
    }
  }, {
    key: "undoThrusterUse",
    value: function undoThrusterUse(thruster, direction, amount) {
      this.cost -= thruster.undoChannel(amount).refund;
      this.directionsRequired[direction] += amount;
    }
  }, {
    key: "buildRequiredThrust",
    value: function buildRequiredThrust(movement) {
      var _this7 = this;

      movement.forEach(function (move) {
        return move.requiredThrust = new _.RequiredThrust(_this7.ship, move);
      });
    }
  }, {
    key: "getMoves",
    value: function getMoves() {
      var _this8 = this;

      this.thrusters.forEach(function (thruster) {
        var channeled = thruster.channeled;
        _this8.movement.forEach(function (move) {
          thruster.directions.forEach(function (direction) {
            if (channeled === 0) {
              return;
            }

            var required = move.requiredThrust.getRequirement(direction);

            if (required === 0) {
              return;
            }

            if (required > channeled) {
              move.requiredThrust.fulfill(direction, channeled, thruster.thruster);
              channeled = 0;
            } else {
              move.requiredThrust.fulfill(direction, required, thruster.thruster);
              channeled -= required;
            }
          });
        });
      });

      if (!this.movement.every(function (move) {
        return move.requiredThrust.isFulfilled();
      })) {
        throw new Error("Not all moves are fulfilled");
      }

      return this.movement;
    }
  }]);

  return ThrustBill;
}();

exports.default = ThrustBill;

},{".":298}],298:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.OverChannelResolver = exports.ThrustAssignment = exports.RequiredThrust = exports.ThrustBill = exports.MovementResolver = exports.movementTypes = exports.MovementPath = exports.MovementOrder = exports.MovementService = undefined;

var _MovementService = require("./MovementService");

var _MovementService2 = _interopRequireDefault(_MovementService);

var _MovementOrder = require("./MovementOrder");

var _MovementOrder2 = _interopRequireDefault(_MovementOrder);

var _MovementPath = require("./MovementPath");

var _MovementPath2 = _interopRequireDefault(_MovementPath);

var _MovementTypes = require("./MovementTypes");

var _MovementTypes2 = _interopRequireDefault(_MovementTypes);

var _MovementResolver = require("./MovementResolver");

var _MovementResolver2 = _interopRequireDefault(_MovementResolver);

var _ThrustBill = require("./ThrustBill");

var _ThrustBill2 = _interopRequireDefault(_ThrustBill);

var _RequiredThrust = require("./RequiredThrust");

var _RequiredThrust2 = _interopRequireDefault(_RequiredThrust);

var _ThrustAssignment = require("./ThrustAssignment");

var _ThrustAssignment2 = _interopRequireDefault(_ThrustAssignment);

var _OverChannelResolver = require("./OverChannelResolver");

var _OverChannelResolver2 = _interopRequireDefault(_OverChannelResolver);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.movement = {
  MovementService: _MovementService2.default,
  MovementOrder: _MovementOrder2.default,
  MovementPath: _MovementPath2.default,
  movementTypes: _MovementTypes2.default,
  MovementResolver: _MovementResolver2.default,
  ThrustBill: _ThrustBill2.default,
  RequiredThrust: _RequiredThrust2.default,
  ThrustAssignment: _ThrustAssignment2.default,
  OverChannelResolver: _OverChannelResolver2.default
};

exports.MovementService = _MovementService2.default;
exports.MovementOrder = _MovementOrder2.default;
exports.MovementPath = _MovementPath2.default;
exports.movementTypes = _MovementTypes2.default;
exports.MovementResolver = _MovementResolver2.default;
exports.ThrustBill = _ThrustBill2.default;
exports.RequiredThrust = _RequiredThrust2.default;
exports.ThrustAssignment = _ThrustAssignment2.default;
exports.OverChannelResolver = _OverChannelResolver2.default;

},{"./MovementOrder":289,"./MovementPath":290,"./MovementResolver":291,"./MovementService":292,"./MovementTypes":293,"./OverChannelResolver":294,"./RequiredThrust":295,"./ThrustAssignment":296,"./ThrustBill":297}],299:[function(require,module,exports){
arguments[4][289][0].apply(exports,arguments)
},{".":308,"dup":289}],300:[function(require,module,exports){
arguments[4][290][0].apply(exports,arguments)
},{"dup":290}],301:[function(require,module,exports){
arguments[4][291][0].apply(exports,arguments)
},{".":308,"dup":291}],302:[function(require,module,exports){
arguments[4][292][0].apply(exports,arguments)
},{".":308,"dup":292}],303:[function(require,module,exports){
arguments[4][293][0].apply(exports,arguments)
},{"dup":293}],304:[function(require,module,exports){
arguments[4][294][0].apply(exports,arguments)
},{"dup":294}],305:[function(require,module,exports){
arguments[4][295][0].apply(exports,arguments)
},{".":308,"dup":295}],306:[function(require,module,exports){
arguments[4][296][0].apply(exports,arguments)
},{"dup":296}],307:[function(require,module,exports){
arguments[4][297][0].apply(exports,arguments)
},{".":308,"dup":297}],308:[function(require,module,exports){
arguments[4][298][0].apply(exports,arguments)
},{"./MovementOrder":299,"./MovementPath":300,"./MovementResolver":301,"./MovementService":302,"./MovementTypes":303,"./OverChannelResolver":304,"./RequiredThrust":305,"./ThrustAssignment":306,"./ThrustBill":307,"dup":298}],309:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _MovementService = require("../movement/MovementService");

var _MovementService2 = _interopRequireDefault(_MovementService);

var _PhaseState = require("./PhaseState");

var _PhaseState2 = _interopRequireDefault(_PhaseState);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var PhaseDirector = function () {
  function PhaseDirector() {
    _classCallCheck(this, PhaseDirector);

    this.shipIconContainer = null;
    this.ewIconContainer = null;
    this.ballisticIconContainer = null;
    this.timeline = [];

    this.animationStrategy = null;
    this.phaseStrategy = null;
    this.coordinateConverter = null;
    this.shipWindowManager = null;
    this.movementService = new _MovementService2.default();
    this.phaseState = new _PhaseState2.default();
  }

  _createClass(PhaseDirector, [{
    key: "init",
    value: function init(coordinateConverter, scene) {
      this.coordinateConverter = coordinateConverter;
      this.shipIconContainer = new ShipIconContainer(this.coordinateConverter, scene, this.movementService);
      this.ewIconContainer = new EWIconContainer(this.coordinateConverter, scene, this.shipIconContainer);
      this.ballisticIconContainer = new BallisticIconContainer(this.coordinateConverter, scene);
      this.shipWindowManager = new ShipWindowManager(new window.UIManager($("body")[0]), this.movementService);
    }
  }, {
    key: "receiveGamedata",
    value: function receiveGamedata(gamedata, webglScene) {
      this.resolvePhaseStrategy(gamedata, webglScene);
    }
  }, {
    key: "relayEvent",
    value: function relayEvent(name, payload) {
      if (!this.phaseStrategy || this.phaseStrategy.inactive) {
        return;
      }

      this.phaseStrategy.onEvent(name, payload);
      this.shipIconContainer.onEvent(name, payload);
      this.ewIconContainer.onEvent(name, payload);
    }
  }, {
    key: "render",
    value: function render(scene, coordinateConverter, zoom) {
      if (!this.phaseStrategy || this.phaseStrategy.inactive) {
        return;
      }

      this.phaseStrategy.render(coordinateConverter, scene, zoom);
    }
  }, {
    key: "resolvePhaseStrategy",
    value: function resolvePhaseStrategy(gamedata, scene) {
      if (!gamedata.isPlayerInGame() || gamedata.replay || gamedata.status === "SURRENDERED" || gamedata.status === "FINISHED") {
        return this.activatePhaseStrategy(window.ReplayPhaseStrategy, gamedata, scene);
      }

      if (gamedata.waiting) {
        return this.activatePhaseStrategy(window.WaitingPhaseStrategy, gamedata, scene);
      }

      switch (gamedata.gamephase) {
        case -1:
          return this.activatePhaseStrategy(window.DeploymentPhaseStrategy, gamedata, scene);
        case 1:
          return this.activatePhaseStrategy(window.InitialPhaseStrategy, gamedata, scene);
        case 2:
          return this.activatePhaseStrategy(window.MovementPhaseStrategy, gamedata, scene);
        case 3:
          return this.activatePhaseStrategy(window.FirePhaseStrategy, gamedata, scene);
        default:
          return this.activatePhaseStrategy(window.WaitingPhaseStrategy, gamedata, scene);
      }
    }
  }, {
    key: "activatePhaseStrategy",
    value: function activatePhaseStrategy(phaseStrategy, gamedata, scene) {
      if (this.phaseStrategy && this.phaseStrategy instanceof phaseStrategy) {
        this.phaseStrategy.update(gamedata);
        return;
      }

      if (this.phaseStrategy) {
        this.phaseStrategy.deactivate();
      }

      this.phaseStrategy = new phaseStrategy(this.coordinateConverter, this.phaseState).activate(this.shipIconContainer, this.ewIconContainer, this.ballisticIconContainer, gamedata, scene, this.shipWindowManager, this.movementService);
    }
  }]);

  return PhaseDirector;
}();

exports.default = PhaseDirector;

},{"../movement/MovementService":302,"./PhaseState":310}],310:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var PhaseState = function () {
  function PhaseState() {
    _classCallCheck(this, PhaseState);

    this.state = {};
  }

  _createClass(PhaseState, [{
    key: "set",
    value: function set(key, payload) {
      this.state[key] = payload;
    }
  }, {
    key: "get",
    value: function get(key) {
      return this.state[key];
    }
  }]);

  return PhaseState;
}();

exports.default = PhaseState;

},{}],311:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.PhaseState = exports.PhaseDirector = undefined;

var _PhaseDirector = require("./PhaseDirector");

var _PhaseDirector2 = _interopRequireDefault(_PhaseDirector);

var _PhaseState = require("./PhaseState");

var _PhaseState2 = _interopRequireDefault(_PhaseState);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.PhaseDirector = _PhaseDirector2.default;
exports.PhaseDirector = _PhaseDirector2.default;
exports.PhaseState = _PhaseState2.default;

},{"./PhaseDirector":309,"./PhaseState":310}],312:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _SystemFactory = require("./SystemFactory");

var _SystemFactory2 = _interopRequireDefault(_SystemFactory);

var _movement = require("../handler/movement");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Ship = function () {
  function Ship(json) {
    _classCallCheck(this, Ship);

    var staticShip = window.staticShips[json.faction][json.phpclass];

    if (!staticShip) {
      throw new Error("Static ship not found for " + json.phpclass);
    }

    Object.keys(staticShip).forEach(function (key) {
      this[key] = staticShip[key];
    }, this);

    for (var i in json) {
      if (i == "systems") {
        this.systems = _SystemFactory2.default.createSystemsFromJson(json[i], this);
      } else if (i == "movement") {
        this.movement = json[i].map(function (move) {
          return new _movement.MovementOrder(move.id, move.type, new hexagon.Offset(move.position), new hexagon.Offset(move.target), move.facing, move.rolled, move.turn, move.value);
        });
      } else {
        this[i] = json[i];
      }
    }
  }

  _createClass(Ship, [{
    key: "getHitChangeMod",
    value: function getHitChangeMod(shooter) {
      var affectingSystems = Array();
      for (var i in this.systems) {
        var system = this.systems[i];

        //if (!this.checkIsValidAffectingSystem(system, shipManager.getShipPosition(shooter)))
        if (!this.checkIsValidAffectingSystem(system, shooter))
          //Marcin Sawicki: change to unit itself...
          continue;

        if (system instanceof Shield && mathlib.getDistanceBetweenShipsInHex(shooter, this) === 0 && shooter.flight) {
          // Shooter is a flight, and the flight is under the shield
          continue;
        }

        var mod = system.getDefensiveHitChangeMod(this, shooter);

        if (!affectingSystems[system.defensiveType] || affectingSystems[system.defensiveType] < mod) {
          //          console.log("getting defensive: " + system.name + " mod: " + mod);
          //          affectingSystems[system.getDefensiveType] = mod;
          affectingSystems[system.defensiveType] = mod;
        }
      }
      var sum = 0;
      for (var i in affectingSystems) {
        sum += affectingSystems[i];
      }
      return sum;
    }

    //Marcin Sawicki: this should use shooter, not pos - OR insert pos only if necessary!
    //otherwise serious trouble at range 0
    //checkIsValidAffectingSystem: function(system, pos)

  }, {
    key: "checkIsValidAffectingSystem",
    value: function checkIsValidAffectingSystem(system, shooter) {
      if (!system.defensiveType) return false;

      //If the system was destroyed last turn continue
      //(If it has been destroyed during this turn, it is still usable)
      if (system.destroyed) return false;

      //If the system is offline either because of a critical or power management, continue
      if (shipManager.power.isOffline(this, system)) return false;

      //if the system has arcs, check that the position is on arc
      if (typeof system.startArc == "number" && typeof system.endArc == "number") {
        var tf = shipManager.getShipHeadingAngle(this);

        var heading = 0;

        //get the heading of position, not ship (in case ballistic)
        //var heading = mathlib.getCompassHeadingOfPosition(this, pos);
        //Marcin Sawicki: should be otherwise in this case?...
        heading = mathlib.getCompassHeadingOfShip(this, shooter);

        //if not on arc, continue!
        if (!mathlib.isInArc(heading, mathlib.addToDirection(system.startArc, tf), mathlib.addToDirection(system.endArc, tf))) {
          return false;
        }
      }

      return true;
    }
  }, {
    key: "checkShieldGenerator",
    value: function checkShieldGenerator() {
      var shieldCapacity = 0;
      var activeShields = 0;

      for (var i in this.systems) {
        var system = this.systems[i];

        if (system.name == "shieldGenerator") {
          if (system.destroyed || shipManager.power.isOffline(this, system)) {
            continue;
          }
          shieldCapacity = system.output + shipManager.power.getBoost(system);
        }

        if (system.name == "graviticShield" && !(system.destroyed || shipManager.power.isOffline(this, system))) {
          activeShields = activeShields + 1;
        }
      }

      return shieldCapacity >= activeShields;
    }
  }]);

  return Ship;
}();

window.Ship = Ship;
exports.default = Ship;

},{"../handler/movement":308,"./SystemFactory":313}],313:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ = require("./");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var getFirstSystem = function getFirstSystem(ship) {
  var system = null;
  Object.keys(ship.systems).find(function (key) {
    system = ship.systems[key];
    return true;
  });
  return system;
};

var findSystemById = function findSystemById(systems, id) {
  return systems.find(function (system) {
    return system.id === id;
  });
};

var SystemFactory = function () {
  function SystemFactory() {
    _classCallCheck(this, SystemFactory);
  }

  _createClass(SystemFactory, [{
    key: "createSystemsFromJson",
    value: function createSystemsFromJson(systemsJson, ship, fighter) {
      var _this = this;

      if (fighter) {
        return this.createSystemsFromJsonFighter(systemsJson, ship, fighter);
      }
      var systems = Array();
      systemsJson.forEach(function (jsonSystem) {
        var staticSystem = ship.flight ? getFirstSystem(ship) : findSystemById(ship.systems, jsonSystem.id);

        var system = _this.createSystemFromJson(jsonSystem, staticSystem, ship);
        systems.push(system);
      });

      return systems;
    }
  }, {
    key: "createSystemsFromJsonFighter",
    value: function createSystemsFromJsonFighter(systemsJson, ship, fighter) {
      var _this2 = this;

      var systems = Array();

      Object.keys(systemsJson).forEach(function (key, index) {
        var jsonSystem = systemsJson[key];
        var staticSystem = fighter.systems[Object.keys(fighter.systems)[index]];

        var system = _this2.createSystemFromJson(jsonSystem, staticSystem, fighter);
        systems[system.id] = system;
      });

      return systems;
    }
  }, {
    key: "createSystemFromJson",
    value: function createSystemFromJson(systemJson, staticSystem, ship) {
      if (staticSystem.fighter) return new Fighter(systemJson, staticSystem, ship);
      var name = systemJson.name.charAt(0).toUpperCase() + systemJson.name.slice(1);

      var args = Object.assign(Object.assign({}, staticSystem), systemJson);
      var system = new _.ShipSystem(args, ship);

      return system;
    }
  }]);

  return SystemFactory;
}();

window.SystemFactory = SystemFactory;

exports.default = new SystemFactory();

},{"./":331}],314:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Animation = function () {
  function Animation() {
    _classCallCheck(this, Animation);

    this.active = false;
    this.started = false;
    this.done = false;
  }

  _createClass(Animation, [{
    key: "start",
    value: function start() {
      this.active = true;
    }
  }, {
    key: "stop",
    value: function stop() {
      this.active = false;
    }
  }, {
    key: "setIsDone",
    value: function setIsDone(done) {
      this.done = done;
      return this;
    }
  }, {
    key: "setStartCallback",
    value: function setStartCallback(callback) {
      this.startCallback = callback;
      return this;
    }
  }, {
    key: "setDoneCallback",
    value: function setDoneCallback(callback) {
      this.doneCallback = callback;
      return this;
    }
  }, {
    key: "callStartCallback",
    value: function callStartCallback(total) {
      if (!this.started && total > this.time) {
        this.startCallback && this.startCallback();
        this.started = true;
      }
    }
  }, {
    key: "callDoneCallback",
    value: function callDoneCallback(total) {
      if (total > this.time + this.duration && !this.done) {
        this.doneCallback && this.doneCallback();
        this.done = true;
      }
    }
  }, {
    key: "reset",
    value: function reset() {}
  }, {
    key: "cleanUp",
    value: function cleanUp() {}
  }, {
    key: "update",
    value: function update(gameData) {}
  }, {
    key: "render",
    value: function render(now, total, last, delta, goingBack) {}
  }]);

  return Animation;
}();

window.Animation = Animation;

exports.default = Animation;

},{}],315:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _Animation2 = require("./Animation");

var _Animation3 = _interopRequireDefault(_Animation2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ShipIdleMovementAnimation = function (_Animation) {
  _inherits(ShipIdleMovementAnimation, _Animation);

  function ShipIdleMovementAnimation(shipIcon, movementService, coordinateConverter) {
    _classCallCheck(this, ShipIdleMovementAnimation);

    var _this = _possibleConstructorReturn(this, (ShipIdleMovementAnimation.__proto__ || Object.getPrototypeOf(ShipIdleMovementAnimation)).call(this));

    _this.shipIcon = shipIcon;
    _this.ship = shipIcon.ship;
    _this.movementService = movementService;
    _this.coordinateConverter = coordinateConverter;

    _this.duration = 0;

    _this.position = _this.getPosition();
    _this.facing = _this.getFacing();
    return _this;
  }

  _createClass(ShipIdleMovementAnimation, [{
    key: "update",
    value: function update(gameData) {
      this.position = this.getPosition();
      this.facing = this.getFacing();
    }
  }, {
    key: "stop",
    value: function stop() {
      _get(ShipIdleMovementAnimation.prototype.__proto__ || Object.getPrototypeOf(ShipIdleMovementAnimation.prototype), "stop", this).call(this);
    }
  }, {
    key: "cleanUp",
    value: function cleanUp() {}
  }, {
    key: "render",
    value: function render(now, total, last, delta, zoom, back, paused) {
      this.shipIcon.setPosition(this.position);
      this.shipIcon.setFacing(-this.facing);
    }
  }, {
    key: "getPosition",
    value: function getPosition() {
      var end = this.movementService.getLastEndMove(this.ship);
      return this.coordinateConverter.fromHexToGame(end.position);
    }
  }, {
    key: "getFacing",
    value: function getFacing() {
      return mathlib.hexFacingToAngle(this.movementService.getLastEndMove(this.ship).facing);
    }
  }]);

  return ShipIdleMovementAnimation;
}(_Animation3.default);

window.ShipIdleMovementAnimation = ShipIdleMovementAnimation;

exports.default = ShipIdleMovementAnimation;

},{"./Animation":314}],316:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var getRandomPosition = function getRandomPosition(maxDistance, getRandom) {
  return {
    x: getRandom() * maxDistance - maxDistance / 2,
    y: getRandom() * maxDistance - maxDistance / 2
  };
};

var constructFirstCurve = function constructFirstCurve(nextPosition) {
  return new THREE.CubicBezierCurve(new THREE.Vector2(0, 0), new THREE.Vector2(0, 0), new THREE.Vector2(nextPosition.x * -1, nextPosition.y * -1), new THREE.Vector2(0, 0));
};

var constructEvasionCurve = function constructEvasionCurve(currentPosition, nextPosition) {
  return new THREE.CubicBezierCurve(new THREE.Vector2(0, 0), new THREE.Vector2(currentPosition.x, currentPosition.y), new THREE.Vector2(nextPosition.x * -1, nextPosition.y * -1), new THREE.Vector2(0, 0));
};

var constructEvasionCurves = function constructEvasionCurves(evasion, maxDistance, getRandom) {
  var curves = [];

  if (evasion === 0) {
    return [];
  }

  var positions = new Array(evasion + 1).fill(0).map(function (i) {
    return getRandomPosition(maxDistance, getRandom);
  });

  for (var i = 0; i < positions.length; i++) {
    var position = positions[i];
    var nextPosition = i === positions.length - 1 ? { x: 0, y: 0 } : positions[i + 1];

    if (i === 0) {
      curves.push(constructFirstCurve(nextPosition));
    } else {
      curves.push(constructEvasionCurve(position, nextPosition));
    }
  }

  return curves;
};

var ShipEvasionMovementPath = function () {
  function ShipEvasionMovementPath(seed, evasion) {
    _classCallCheck(this, ShipEvasionMovementPath);

    this.evasion = evasion;

    this.curves = constructEvasionCurves(this.evasion, 100 / this.evasion, mathlib.getSeededRandomGenerator(seed));
  }

  _createClass(ShipEvasionMovementPath, [{
    key: "getOffset",
    value: function getOffset(percent) {
      if (this.evasion === 0) {
        return { x: 0, y: 0 };
      }

      var point = (this.curves.length - 1) * percent;
      var curveNumber = Math.floor(point);
      var decimal = point - Math.floor(point);

      var curve = this.curves[curveNumber];
      return curve.getPoint(decimal);
    }
  }]);

  return ShipEvasionMovementPath;
}();

exports.default = ShipEvasionMovementPath;

},{}],317:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _Animation2 = require("../Animation");

var _Animation3 = _interopRequireDefault(_Animation2);

var _ShipEvasionMovementPath = require("./ShipEvasionMovementPath");

var _ShipEvasionMovementPath2 = _interopRequireDefault(_ShipEvasionMovementPath);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ShipMovementAnimation = function (_Animation) {
  _inherits(ShipMovementAnimation, _Animation);

  function ShipMovementAnimation(shipIcon, movementService, coordinateConverter, turn) {
    var time = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 0;
    var continious = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : false;

    _classCallCheck(this, ShipMovementAnimation);

    var _this = _possibleConstructorReturn(this, (ShipMovementAnimation.__proto__ || Object.getPrototypeOf(ShipMovementAnimation)).call(this));

    _this.shipIcon = shipIcon;
    _this.ship = shipIcon.ship;
    _this.movementService = movementService;
    _this.coordinateConverter = coordinateConverter;
    _this.continious = continious;
    _this.time = time;
    _this.turn = turn;

    _this.doneCallback = null;

    if (!_this.movementService) {
      throw new Error("movement service undefined");
    }

    _this.duration = 5000;
    _this.time = _this.time;

    _this.positionCurve = _this.buildPositionCurve();
    _this.rotations = _this.buildRotations();
    _this.evasionOffset = new _ShipEvasionMovementPath2.default(_this.turn * _this.ship.id, _this.movementService.getEvasion(_this.ship));

    _this.easeInOut = new THREE.CubicBezierCurve(new THREE.Vector2(0, 0), new THREE.Vector2(0.75, 0), new THREE.Vector2(0.25, 1), new THREE.Vector2(1, 1));

    /*
    this.turnCurve = new THREE.CubicBezierCurve(
      new THREE.Vector2(0, 0),
      new THREE.Vector2(0.25, 0.25),
      new THREE.Vector2(0.75, 0.75),
      new THREE.Vector2(1, 1)
    );
     */

    /*
    this.hexAnimations.forEach(function (animation) {
        animation.debugCurve = drawRoute(animation.curve);
    });
    */

    _this.endPause = 0;

    _Animation3.default.call(_this);
    return _this;
  }

  _createClass(ShipMovementAnimation, [{
    key: "update",
    value: function update(gameData) {}
  }, {
    key: "stop",
    value: function stop() {
      _get(ShipMovementAnimation.prototype.__proto__ || Object.getPrototypeOf(ShipMovementAnimation.prototype), "stop", this).call(this);
    }
  }, {
    key: "cleanUp",
    value: function cleanUp() {}
  }, {
    key: "render",
    value: function render(now, total, last, delta, zoom, back, paused) {
      this.callStartCallback(total);

      var _ref = !this.done ? this.getPositionAndFacingAtTime(total) : this.getPositionAndFacingAtTime(this.time + this.duration),
          position = _ref.position,
          facing = _ref.facing;

      this.shipIcon.setPosition(position);
      this.shipIcon.setFacing(-facing);

      this.callDoneCallback(total);

      /*
      if (
        total > this.time &&
        total < this.time + this.duration + this.endPause &&
        !paused
      ) {
        window.webglScene.moveCameraTo(positionAndFacing.position);
      }
      */
    }
  }, {
    key: "getPositionAndFacingAtTime",
    value: function getPositionAndFacingAtTime(time) {
      var totalDone = (time - this.time) / this.duration;

      if (totalDone > 1) {
        totalDone = 1;
      } else if (totalDone < 0) {
        totalDone = 0;
      }

      return {
        position: this.applyOffset(this.positionCurve.getPoint(totalDone), totalDone),
        facing: this.getFacing(time)
      };
    }
  }, {
    key: "applyOffset",
    value: function applyOffset(position, totalDone) {
      return {
        x: position.x + this.evasionOffset.getOffset(totalDone).x,
        y: position.y + this.evasionOffset.getOffset(totalDone).y
      };
    }
  }, {
    key: "getFacing",
    value: function getFacing(time) {
      var _this2 = this;

      if (time < this.time) {
        return this.rotations[0].start;
      }

      if (time > this.time + this.duration) {
        return this.rotations[this.rotations.length - 1].end;
      }

      var rotation = this.rotations.find(function (rotation) {
        return time >= rotation.startTime + _this2.time && time < rotation.endTime + _this2.time;
      });

      if (!rotation) {
        rotation = this.rotations[this.rotations.length - 1];
      }

      var totalDone = rotation.duration === 0 ? 1 : (time - (rotation.startTime + this.time)) / rotation.duration;

      if (totalDone > 1) {
        totalDone = 1;
      }

      return mathlib.addToDirection(rotation.start, rotation.amount * this.easeInOut.getPoint(totalDone).y);
    }
  }, {
    key: "buildRotations",
    value: function buildRotations() {
      var pivots = this.buildPivotList();

      var startTime = 0;

      pivots.forEach(function (pivot) {
        pivot.startTime = startTime;
        startTime = pivot.endTime;
      });

      if (pivots.length === 0) {
        var facing = mathlib.hexFacingToAngle(this.movementService.getLastTurnEndMove(this.ship).facing);

        pivots = [{
          amount: 0,
          start: facing,
          end: facing,
          startTime: 0,
          endTime: this.duration
        }];
      }

      pivots[pivots.length - 1].endTime = this.duration;

      pivots.forEach(function (pivot) {
        pivot.duration = pivot.endTime - pivot.startTime;
      });

      console.log(pivots);

      return pivots;
    }
  }, {
    key: "buildPivotList",
    value: function buildPivotList() {
      var startTime = 0;
      var facings = [];
      var lastTurnEndMove = this.movementService.getLastTurnEndMove(this.ship);
      var lastFacing = lastTurnEndMove.facing;

      var pivots = [];

      var pivotStarted = false;

      var totalMovementLength = this.movementService.getThisTurnMovement(this.ship).filter(function (move) {
        return move.isPivot() || move.isSpeed();
      }).length;

      var moveStep = this.duration / totalMovementLength;
      var pivotStep = moveStep > 1000 ? 1000 : moveStep;

      var moves = this.movementService.getThisTurnMovement(this.ship).filter(function (move) {
        return move.isPivot() || move.isSpeed();
      }).map(function (move) {
        if (move.isPivot()) {
          return move.facing;
        } else if (move.isSpeed()) {
          return null;
        }
      });

      moves.push(null);

      moves.forEach(function (pivot) {
        if (pivot === null && pivotStarted) {
          var direction = facings[0] - lastFacing;
          if (direction === -5) {
            direction = 1;
          } else if (direction === 5) {
            direction = -1;
          }

          var start = mathlib.hexFacingToAngle(lastFacing);
          var end = mathlib.hexFacingToAngle(facings[facings.length - 1]);
          pivots.push({
            start: start,
            end: end,
            amount: mathlib.getDistanceBetweenDirections(start, end, direction) * direction,
            endTime: startTime * pivotStep
          });

          lastFacing = facings[facings.length - 1];
          facings = [];
          pivotStarted = false;
        } else if (pivot && !pivotStarted) {
          pivotStarted = true;
          facings.push(pivot);
        } else if (pivot) {
          facings.push(pivot);
        }

        startTime++;
      });

      /*
      if (pivotStarted) {
        pivots.push({
          facings: facings,
          startTime: startTime
        });
      }
      */

      return pivots;
    }
  }, {
    key: "buildPositionCurve",
    value: function buildPositionCurve() {
      var end = this.movementService.getLastEndMove(this.ship);
      var start = this.movementService.getLastTurnEndMove(this.ship) || end;

      if (!end || end === start) {
        var position = this.coordinateConverter.fromHexToGame(start.position);
        return new THREE.CubicBezierCurve3(new THREE.Vector3(position.x, position.y, position.z), new THREE.Vector3(position.x, position.y, position.z), new THREE.Vector3(position.x, position.y, position.z), new THREE.Vector3(position.x, position.y, position.z));
      }

      var point1 = this.coordinateConverter.fromHexToGame(start.position);

      var control1 = this.coordinateConverter.fromHexToGame(start.position.add(start.target.scale(0.5)));

      var control2 = this.coordinateConverter.fromHexToGame(this.continious ? end.position.subtract(end.target.scale(0.5)) : end.position);

      var point2 = this.coordinateConverter.fromHexToGame(end.position);

      return new THREE.CubicBezierCurve3(new THREE.Vector3(point1.x, point1.y, point1.z), new THREE.Vector3(control1.x, control1.y, control1.z), new THREE.Vector3(control2.x, control2.y, control2.z), new THREE.Vector3(point2.x, point2.y, point2.z));
    }
  }]);

  return ShipMovementAnimation;
}(_Animation3.default);

window.ShipMovementAnimation = ShipMovementAnimation;

exports.default = ShipMovementAnimation;

},{"../Animation":314,"./ShipEvasionMovementPath":316}],318:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _ShipMovementAnimation = require("./ShipMovementAnimation");

var _ShipMovementAnimation2 = _interopRequireDefault(_ShipMovementAnimation);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _ShipMovementAnimation2.default;

},{"./ShipMovementAnimation":317}],319:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var AnimationStrategy = function () {
  function AnimationStrategy(shipIcons, turn) {
    _classCallCheck(this, AnimationStrategy);

    this.shipIconContainer = null;
    this.turn = 0;
    this.lastAnimationTime = 0;
    this.totalAnimationTime = 0;
    this.currentDeltaTime = 0;
    this.animations = [];
    this.paused = true;
    this.shipIconContainer = shipIcons;
    this.turn = turn;
    this.goingBack = false;
  }

  _createClass(AnimationStrategy, [{
    key: "activate",
    value: function activate() {
      this.play();

      return this;
    }
  }, {
    key: "update",
    value: function update(gameData) {
      this.animations.forEach(function (animation) {
        animation.update(gameData);
      });

      return this;
    }
  }, {
    key: "stop",
    value: function stop(gameData) {
      this.lastAnimationTime = 0;
      this.totalAnimationTime = 0;
      this.currentDeltaTime = 0;
      this.pause();
    }
  }, {
    key: "back",
    value: function back() {
      this.goingBack = true;
      this.paused = false;
    }
  }, {
    key: "play",
    value: function play() {
      this.paused = false;
      this.goingBack = false;
    }
  }, {
    key: "pause",
    value: function pause() {
      this.paused = true;
      this.goingBack = false;
    }
  }, {
    key: "isPaused",
    value: function isPaused() {
      return this.paused;
    }
  }, {
    key: "deactivate",
    value: function deactivate() {
      return this;
    }
  }, {
    key: "goToTime",
    value: function goToTime(time) {
      this.totalAnimationTime = time;
      return this;
    }
  }, {
    key: "render",
    value: function render(coordinateConverter, scene, zoom) {
      this.updateDeltaTime.call(this, this.paused);
      this.updateTotalAnimationTime.call(this, this.paused);
      this.animations.forEach(function (animation) {
        animation.render(new Date().getTime(), this.totalAnimationTime, this.lastAnimationTime, this.currentDeltaTime, zoom, this.goingBack, this.paused);
      }, this);
    }
  }, {
    key: "positionAndFaceAllIcons",
    value: function positionAndFaceAllIcons() {
      this.shipIconContainer.positionAndFaceAllIcons();
    }
  }, {
    key: "positionAndFaceIcon",
    value: function positionAndFaceIcon(icon) {
      icon.positionAndFaceIcon();
    }

    /*
      AnimationStrategy.prototype.initializeAnimations = function() {
          this.animations.forEach(function (animation) {
              animation.initialize();
          })
      };
      */

  }, {
    key: "removeAllAnimations",
    value: function removeAllAnimations() {
      this.animations.forEach(function (animation) {
        return animation.deactivate();
      });
      this.animations = [];
    }
  }, {
    key: "removeAnimation",
    value: function removeAnimation(toRemove) {
      this.animations = this.animations.filter(function (animation) {
        return animation !== animation;
      });

      toRemove.deactivate();
    }
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged() {}
  }, {
    key: "updateTotalAnimationTime",
    value: function updateTotalAnimationTime(paused) {
      if (paused) {
        return;
      }

      if (this.goingBack) {
        this.totalAnimationTime -= this.currentDeltaTime;
      } else {
        this.totalAnimationTime += this.currentDeltaTime;
      }
    }
  }, {
    key: "updateDeltaTime",
    value: function updateDeltaTime(paused) {
      var now = new Date().getTime();

      if (!this.lastAnimationTime) {
        this.lastAnimationTime = now;
        this.currentDeltaTime = 0;
      }

      if (!paused) {
        this.currentDeltaTime = now - this.lastAnimationTime;
      }

      this.lastAnimationTime = now;
    }
  }]);

  return AnimationStrategy;
}();

window.AnimationStrategy = AnimationStrategy;

exports.default = AnimationStrategy;

},{}],320:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _AnimationStrategy2 = require("./AnimationStrategy");

var _AnimationStrategy3 = _interopRequireDefault(_AnimationStrategy2);

var _ = require("..");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var IdleAnimationStrategy = function (_AnimationStrategy) {
  _inherits(IdleAnimationStrategy, _AnimationStrategy);

  function IdleAnimationStrategy(shipIcons, turn, movementService, coordinateConverter) {
    _classCallCheck(this, IdleAnimationStrategy);

    var _this = _possibleConstructorReturn(this, (IdleAnimationStrategy.__proto__ || Object.getPrototypeOf(IdleAnimationStrategy)).call(this, shipIcons, turn));

    _this.movementService = movementService;
    _this.coordinateConverter = coordinateConverter;
    return _this;
  }

  _createClass(IdleAnimationStrategy, [{
    key: "update",
    value: function update(gamedata) {
      _get(IdleAnimationStrategy.prototype.__proto__ || Object.getPrototypeOf(IdleAnimationStrategy.prototype), "update", this).call(this, gamedata);

      this.shipIconContainer.getArray().forEach(function (icon) {
        var ship = icon.ship;

        var turnDestroyed = shipManager.getTurnDestroyed(ship);
        var destroyed = shipManager.isDestroyed(ship);

        if (turnDestroyed !== null && turnDestroyed < this.turn) {
          icon.hide();
        } else if (turnDestroyed === null && destroyed) {
          icon.hide();
        } else {
          icon.show();
          this.animations.push(new _.ShipIdleMovementAnimation(icon, this.movementService, this.coordinateConverter, this.animations.length * 5000));
        }

        if (icon instanceof FlightIcon) {
          icon.hideDestroyedFighters();
        }
      }, this);
      return this;
    }
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged(ship) {
      var animation = this.animations.find(function (animation) {
        return animation.ship === ship;
      });

      animation.update();
    }
  }, {
    key: "deactivate",
    value: function deactivate() {
      if (this.shipIconContainer) {
        this.shipIconContainer.getArray().forEach(function (icon) {
          icon.show();
        }, this);
      }

      return _get(IdleAnimationStrategy.prototype.__proto__ || Object.getPrototypeOf(IdleAnimationStrategy.prototype), "deactivate", this).call(this);
    }
  }]);

  return IdleAnimationStrategy;
}(_AnimationStrategy3.default);

window.IdleAnimationStrategy = IdleAnimationStrategy;
exports.default = IdleAnimationStrategy;

},{"..":323,"./AnimationStrategy":319}],321:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _AnimationStrategy2 = require("./AnimationStrategy");

var _AnimationStrategy3 = _interopRequireDefault(_AnimationStrategy2);

var _ = require("..");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var MOVEMENT_TIME = 5000;

var isReplayed = function isReplayed(phaseState, turn, ship) {
  var state = phaseState.get("MovementShownTurn" + turn);
  if (!state) {
    return false;
  }

  return state[ship.id];
};

var setReplayed = function setReplayed(phaseState, turn, ship) {
  return function () {
    var state = phaseState.get("MovementShownTurn" + turn) || {};

    state[ship.id] = true;
    phaseState.set("MovementShownTurn" + turn, state);
    console.log("set replayed");
  };
};

var MovementAnimationStrategy = function (_AnimationStrategy) {
  _inherits(MovementAnimationStrategy, _AnimationStrategy);

  function MovementAnimationStrategy(shipIcons, turn, movementService, coordinateConverter, phaseState) {
    _classCallCheck(this, MovementAnimationStrategy);

    var _this = _possibleConstructorReturn(this, (MovementAnimationStrategy.__proto__ || Object.getPrototypeOf(MovementAnimationStrategy)).call(this, shipIcons, turn));

    _this.movementService = movementService;
    _this.coordinateConverter = coordinateConverter;
    _this.phaseState = phaseState;
    return _this;
  }

  _createClass(MovementAnimationStrategy, [{
    key: "update",
    value: function update(gamedata) {
      _get(MovementAnimationStrategy.prototype.__proto__ || Object.getPrototypeOf(MovementAnimationStrategy.prototype), "update", this).call(this, gamedata);
      this.buildAnimations();
    }
  }, {
    key: "buildAnimations",
    value: function buildAnimations() {
      this.shipIconContainer.getArray().forEach(function (icon) {
        var ship = icon.ship;

        var turnDestroyed = shipManager.getTurnDestroyed(ship);
        var destroyed = shipManager.isDestroyed(ship);

        if (turnDestroyed !== null && turnDestroyed < this.turn) {
          icon.hide();
        } else if (turnDestroyed === null && destroyed) {
          icon.hide();
        } else {
          icon.show();

          if (this.movementService.isMoved(ship, this.turn)) {
            this.animations.push(new _.ShipMovementAnimation(icon, this.movementService, this.coordinateConverter, this.turn).setIsDone(isReplayed(this.phaseState, this.turn, ship)).setStartCallback(setReplayed(this.phaseState, this.turn, ship)));
          } else {
            this.animations.push(new ShipIdleMovementAnimation(icon, this.movementService, this.coordinateConverter));
          }
        }

        if (icon instanceof FlightIcon) {
          icon.hideDestroyedFighters();
        }
      }, this);
      this.timeAnimations();
      return this;
    }
  }, {
    key: "timeAnimations",
    value: function timeAnimations() {
      var time = 0;
      this.animations.forEach(function (animation) {
        if (!animation.done) {
          animation.time = time;
          time += MOVEMENT_TIME;
        }
      });
    }
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged(ship) {}
  }, {
    key: "deactivate",
    value: function deactivate() {
      if (this.shipIconContainer) {
        this.shipIconContainer.getArray().forEach(function (icon) {
          icon.show();
        }, this);
      }

      return _get(MovementAnimationStrategy.prototype.__proto__ || Object.getPrototypeOf(MovementAnimationStrategy.prototype), "deactivate", this).call(this);
    }
  }]);

  return MovementAnimationStrategy;
}(_AnimationStrategy3.default);

window.MovementAnimationStrategy = MovementAnimationStrategy;
exports.default = MovementAnimationStrategy;

},{"..":323,"./AnimationStrategy":319}],322:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.MovementAnimationStrategy = exports.IdleAnimationStrategy = exports.AnimationStrategy = undefined;

var _AnimationStrategy = require("./AnimationStrategy");

var _AnimationStrategy2 = _interopRequireDefault(_AnimationStrategy);

var _IdleAnimationStrategy = require("./IdleAnimationStrategy");

var _IdleAnimationStrategy2 = _interopRequireDefault(_IdleAnimationStrategy);

var _MovementAnimationStrategy = require("./MovementAnimationStrategy");

var _MovementAnimationStrategy2 = _interopRequireDefault(_MovementAnimationStrategy);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.AnimationStrategy = _AnimationStrategy2.default;
exports.IdleAnimationStrategy = _IdleAnimationStrategy2.default;
exports.MovementAnimationStrategy = _MovementAnimationStrategy2.default;

},{"./AnimationStrategy":319,"./IdleAnimationStrategy":320,"./MovementAnimationStrategy":321}],323:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.particle = exports.animationStrategy = exports.Animation = exports.ShipIdleMovementAnimation = exports.ShipMovementAnimation = undefined;

var _ShipMovementAnimation = require("./ShipMovementAnimation");

var _ShipMovementAnimation2 = _interopRequireDefault(_ShipMovementAnimation);

var _ShipIdleMovementAnimation = require("./ShipIdleMovementAnimation");

var _ShipIdleMovementAnimation2 = _interopRequireDefault(_ShipIdleMovementAnimation);

var _Animation = require("./Animation");

var _Animation2 = _interopRequireDefault(_Animation);

var _animationStrategy = require("./animationStrategy");

var animationStrategy = _interopRequireWildcard(_animationStrategy);

var _particle = require("./particle");

var particle = _interopRequireWildcard(_particle);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.ShipMovementAnimation = _ShipMovementAnimation2.default;
exports.ShipIdleMovementAnimation = _ShipIdleMovementAnimation2.default;
exports.Animation = _Animation2.default;
exports.animationStrategy = animationStrategy;
exports.particle = particle;

},{"./Animation":314,"./ShipIdleMovementAnimation":315,"./ShipMovementAnimation":318,"./animationStrategy":322,"./particle":327}],324:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Animation2 = require("../Animation");

var _Animation3 = _interopRequireDefault(_Animation2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ParticleEmitterContainer = function (_Animation) {
  _inherits(ParticleEmitterContainer, _Animation);

  function ParticleEmitterContainer(scene, defaultParticleAmount, emitterClass, emitterArgs) {
    _classCallCheck(this, ParticleEmitterContainer);

    var _this = _possibleConstructorReturn(this, (ParticleEmitterContainer.__proto__ || Object.getPrototypeOf(ParticleEmitterContainer)).call(this));

    _this.emitters = [];
    _this.scene = scene;
    _this.defaultParticleAmount = defaultParticleAmount;
    _this.emitterClass = emitterClass || ParticleEmitter;
    _this.emitterArgs = emitterArgs || {};
    return _this;
  }

  _createClass(ParticleEmitterContainer, [{
    key: "getParticle",
    value: function getParticle(animation) {
      var particle;
      var emitter = null;

      for (var i in this.emitters) {
        particle = this.emitters[i].emitter.getParticle();
        if (particle) {
          emitter = this.emitters[i];
        }
      }

      if (!particle) {
        this.emitters.push({
          emitter: new this.emitterClass(this.scene, this.defaultParticleAmount, this.emitterArgs),
          reservations: []
        });
        return this.getParticle(animation);
      }

      var reservation = this.getReservation(emitter.reservations, animation, true);
      reservation.indexes.push(particle.index);
      return particle;
    }
  }, {
    key: "cleanUp",
    value: function cleanUp() {
      this.emitters.forEach(function (emitter) {
        emitter.emitter.cleanUp();
      });
      this.emitters = [];
    }

    /*
      ParticleEmitterContainer.prototype.cleanUpAnimation = function (animation) {
          this.emitters.forEach(function (emitter) {
             cleanUpAnimationFromEmitter(animation, emitter);
          });
      };
      */

  }, {
    key: "setRotation",
    value: function setRotation(rotation) {
      this.emitters.forEach(function (emitter) {
        emitter.emitter.mesh.rotation.y = rotation * Math.PI / 180;
      });
    }
  }, {
    key: "setPosition",
    value: function setPosition(pos) {
      this.emitters.forEach(function (emitter) {
        emitter.emitter.mesh.position.x = pos.x;
        emitter.emitter.mesh.position.y = pos.y;
        emitter.emitter.mesh.position.z = pos.z;
      });
    }
  }, {
    key: "lookAt",
    value: function lookAt(thing) {
      this.emitters.forEach(function (emitter) {
        emitter.emitter.mesh.quaternion.copy(thing.quaternion);
      });
    }
  }, {
    key: "render",
    value: function render(now, total, last, delta, zoom) {
      this.emitters.forEach(function (emitter) {
        emitter.emitter.render(now, total, last, delta, zoom);
      });
    }

    /*
      function cleanUpAnimationFromEmitter(animation, emitter) {
          var reservation = getReservation(emitter.reservations);
           emitter.reservations = emitter.reservations.filter(function (res) {
              return res !== reservation;
          });
           emitter.emitter.freeParticles(reservation.indexes);
      }
      */

  }, {
    key: "getReservation",
    value: function getReservation(reservations, animation, create) {
      var reservation = reservations.find(function (reservation) {
        return reservation.animation === animation;
      });

      if (!reservation && create) {
        reservation = { animation: animation, indexes: [] };
        reservations.push(reservation);
      }

      return reservation;
    }
  }]);

  return ParticleEmitterContainer;
}(_Animation3.default);

exports.default = ParticleEmitterContainer;

},{"../Animation":314}],325:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var changeAttribute = function changeAttribute(geometry, index, key, values) {
  values = [].concat(values);

  var target = geometry.attributes[key].array;

  values.forEach(function (value, i) {
    target[index * values.length + i] = value;
  });

  geometry.attributes[key].needsUpdate = true;
};

var StarParticle = function () {
  function StarParticle(material, geometry) {
    _classCallCheck(this, StarParticle);

    this.material = material;
    this.geometry = geometry;
    this.index = 0;

    this.texture = {
      gas: 0,
      bolt: 1,
      glow: 2,
      ring: 3,
      starLine: 4
    };
  }

  _createClass(StarParticle, [{
    key: "create",
    value: function create(index) {
      this.index = index;
      return this;
    }
  }, {
    key: "setInitialValues",
    value: function setInitialValues() {
      this.setPosition({ x: 0, y: 0 });
      this.setColor(new THREE.Color(0, 0, 0));
      this.setOpacity(0.0);
      this.setSize(0.0);
      this.setSizeChange(0.0);
      this.setAngle(0.0);
      this.setAngleChange(0.0);
      this.setActivationTime(0.0);
      this.setTexture(this.texture.glow);
      this.setParallaxFactor(0.0);
      this.setSineFrequency(0.0);
      this.setSineAmplitude(1);

      return this;
    }
  }, {
    key: "setTexture",
    value: function setTexture(tex) {
      changeAttribute(this.geometry, this.index, "textureNumber", tex);

      return this;
    }
  }, {
    key: "setParallaxFactor",
    value: function setParallaxFactor(parallaxFactor) {
      changeAttribute(this.geometry, this.index, "parallaxFactor", -1.0 + parallaxFactor);
      return this;
    }
  }, {
    key: "setSineFrequency",
    value: function setSineFrequency(sineFrequency) {
      changeAttribute(this.geometry, this.index, "sineFrequency", sineFrequency);
      return this;
    }
  }, {
    key: "setSineAmplitude",
    value: function setSineAmplitude(sineAmplitude) {
      changeAttribute(this.geometry, this.index, "sineAmplitude", sineAmplitude);
      return this;
    }
  }, {
    key: "setSize",
    value: function setSize(size) {
      changeAttribute(this.geometry, this.index, "size", size);
      return this;
    }
  }, {
    key: "setSizeChange",
    value: function setSizeChange(size) {
      changeAttribute(this.geometry, this.index, "sizeChange", size);
      return this;
    }
  }, {
    key: "setColor",
    value: function setColor(color) {
      changeAttribute(this.geometry, this.index, "color", [color.r, color.g, color.b]);
      return this;
    }
  }, {
    key: "setOpacity",
    value: function setOpacity(opacity) {
      changeAttribute(this.geometry, this.index, "opacity", opacity);
      return this;
    }
  }, {
    key: "setPosition",
    value: function setPosition(pos) {
      changeAttribute(this.geometry, this.index, "position", [pos.x, pos.y, pos.z || 0], true);
      return this;
    }
  }, {
    key: "setAngle",
    value: function setAngle(angle) {
      changeAttribute(this.geometry, this.index, "angle", mathlib.degreeToRadian(angle));
      return this;
    }
  }, {
    key: "setAngleChange",
    value: function setAngleChange(angle) {
      changeAttribute(this.geometry, this.index, "angleChange", mathlib.degreeToRadian(angle));
      return this;
    }
  }, {
    key: "deactivate",
    value: function deactivate() {
      this.setInitialValues();
      return this;
    }
  }, {
    key: "setActivationTime",
    value: function setActivationTime(gameTime) {
      changeAttribute(this.geometry, this.index, "activationGameTime", gameTime);
      return this;
    }
  }]);

  return StarParticle;
}();

exports.default = StarParticle;

},{}],326:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Animation2 = require("../Animation");

var _Animation3 = _interopRequireDefault(_Animation2);

var _StarParticle = require("./StarParticle");

var _StarParticle2 = _interopRequireDefault(_StarParticle);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var SHADER_VERTEX = null;
var SHADER_FRAGMENT = null;

var texture = new THREE.TextureLoader().load("img/effect/effectTextures1024.png");

var getShaders = function getShaders() {
  if (!SHADER_VERTEX) SHADER_VERTEX = document.getElementById("starVertexShader").innerHTML;

  if (!SHADER_FRAGMENT) SHADER_FRAGMENT = document.getElementById("starFragmentShader").innerHTML;

  return { vertex: SHADER_VERTEX, fragment: SHADER_FRAGMENT };
};

var StarParticleEmitter = function (_Animation) {
  _inherits(StarParticleEmitter, _Animation);

  function StarParticleEmitter(scene, particleCount, args) {
    _classCallCheck(this, StarParticleEmitter);

    var _this = _possibleConstructorReturn(this, (StarParticleEmitter.__proto__ || Object.getPrototypeOf(StarParticleEmitter)).call(this));

    if (!args) {
      args = {};
    }

    var blending = args.blending || THREE.AdditiveBlending;

    if (!particleCount) {
      particleCount = 1000;
    }

    _this.scene = scene;

    _this.free = [];
    for (var i = 0; i < particleCount; i++) {
      _this.free.push(i);
    }

    _this.effects = 0;

    var uniforms = {
      gameTime: { type: "f", value: 0.0 },
      texture: { type: "t", value: texture }
    };

    _this.particleGeometry = new THREE.BufferGeometry();

    _this.particleGeometry.addAttribute("position", new THREE.Float32BufferAttribute(new Float32Array(particleCount * 3), 3).setDynamic(true));
    _this.particleGeometry.addAttribute("size", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("sizeChange", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("color", new THREE.Float32BufferAttribute(new Float32Array(particleCount * 3), 3).setDynamic(true));
    _this.particleGeometry.addAttribute("opacity", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("activationGameTime", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("textureNumber", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("angle", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("angleChange", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("parallaxFactor", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("sineFrequency", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));
    _this.particleGeometry.addAttribute("sineAmplitude", new THREE.Float32BufferAttribute(new Float32Array(particleCount), 1).setDynamic(true));

    _this.particleGeometry.dynamic = true;

    _this.particleGeometry.setDrawRange(0, particleCount);

    var shaders = getShaders();

    _this.particleMaterial = new THREE.ShaderMaterial({
      uniforms: uniforms,
      vertexShader: shaders.vertex,
      fragmentShader: shaders.fragment,
      transparent: true,
      blending: blending,
      depthWrite: false //Try removing this if problems with transparency
    });

    /*
        THREE.NormalBlending = 0;
        THREE.AdditiveBlending = 1;
        THREE.SubtractiveBlending = 2;
        THREE.MultiplyBlending = 3;
        THREE.AdditiveAlphaBlending = 4;
        */

    _this.flyParticle = new _StarParticle2.default(_this.particleMaterial, _this.particleGeometry);

    while (particleCount--) {
      _this.flyParticle.create(particleCount).setInitialValues();
    }

    _this.mesh = new THREE.Points(_this.particleGeometry, _this.particleMaterial);
    _this.mesh.frustumCulled = false;
    //this.mesh.matrixAutoUpdate = false;
    _this.mesh.position.set(0, 0, args.z || -10);

    _this.needsUpdate = false;

    _this.scene.add(_this.mesh);
    return _this;
  }

  _createClass(StarParticleEmitter, [{
    key: "start",
    value: function start() {
      this.active = true;
    }
  }, {
    key: "stop",
    value: function stop() {
      this.active = false;
    }
  }, {
    key: "reset",
    value: function reset() {}
  }, {
    key: "cleanUp",
    value: function cleanUp() {
      this.mesh.material.dispose();
      this.scene.remove(this.mesh);
    }
  }, {
    key: "update",
    value: function update(gameData) {}
  }, {
    key: "render",
    value: function render(now, total, last, delta, zoom) {
      this.particleMaterial.uniforms.gameTime.value = total;
      this.mesh.material.needsUpdate = true;
    }
  }, {
    key: "done",
    value: function done() {
      if (this.onDoneCallback) {
        this.onDoneCallback();
      }
    }
  }, {
    key: "getParticle",
    value: function getParticle() {
      if (this.free.length === 0) {
        return false;
      }

      var i = this.free.pop();

      return this.flyParticle.create(i);
    }
  }, {
    key: "freeParticles",
    value: function freeParticles(particleIndices) {
      particleIndices.forEach(function (i) {
        this.flyParticle.create(i).setInitialValues();
      }, this);
      this.free = this.free.concat(particleIndices);
    }
  }]);

  return StarParticleEmitter;
}(_Animation3.default);

exports.default = StarParticleEmitter;

},{"../Animation":314,"./StarParticle":325}],327:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.StarParticle = exports.StarParticleEmitter = exports.ParticleEmitterContainer = undefined;

var _ParticleEmitterContainer = require("./ParticleEmitterContainer");

var _ParticleEmitterContainer2 = _interopRequireDefault(_ParticleEmitterContainer);

var _StarParticleEmitter = require("./StarParticleEmitter");

var _StarParticleEmitter2 = _interopRequireDefault(_StarParticleEmitter);

var _StarParticle = require("./StarParticle");

var _StarParticle2 = _interopRequireDefault(_StarParticle);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.ParticleEmitterContainer = _ParticleEmitterContainer2.default;
exports.ParticleEmitterContainer = _ParticleEmitterContainer2.default;
exports.StarParticleEmitter = _StarParticleEmitter2.default;
exports.StarParticle = _StarParticle2.default;

},{"./ParticleEmitterContainer":324,"./StarParticle":325,"./StarParticleEmitter":326}],328:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Offset = require("./Offset");

var _Offset2 = _interopRequireDefault(_Offset);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var PRECISION = 4;

var Cube = function () {
  function Cube(x, y, z) {
    _classCallCheck(this, Cube);

    if (x instanceof Cube || x.x !== undefined && x.y !== undefined && x.z !== undefined) {
      var cube = x;
      this.x = this._formatNumber(cube.x);
      this.y = this._formatNumber(cube.y);
      this.z = this._formatNumber(cube.z);
    } else {
      this.x = this._formatNumber(x);
      this.y = this._formatNumber(y);
      this.z = this._formatNumber(z);
    }

    this.neighbours = [{ x: 1, y: -1, z: 0 }, { x: 1, y: 0, z: -1 }, { x: 0, y: 1, z: -1 }, { x: -1, y: 1, z: 0 }, { x: -1, y: 0, z: 1 }, { x: 0, y: -1, z: 1 }];

    this._validate();
  }

  _createClass(Cube, [{
    key: "round",
    value: function round() {
      if (this.x % 1 === 0 && this.y % 1 === 0 && this.z % 1 === 0) {
        return this;
      }

      var rx = Math.round(this.x);
      var ry = Math.round(this.y);
      var rz = Math.round(this.z);

      var x_diff = Math.abs(rx - this.x);
      var y_diff = Math.abs(ry - this.y);
      var z_diff = Math.abs(rz - this.z);

      if (x_diff > y_diff && x_diff > z_diff) {
        rx = -ry - rz;
      } else if (y_diff > z_diff) {
        ry = -rx - rz;
      } else {
        rz = -rx - ry;
      }

      return new Cube(rx, ry, rz);
    }
  }, {
    key: "_validate",
    value: function _validate() {
      if (Math.abs(this.x + this.y + this.z) > 0.001) {
        throw new Error("Invalid Cube coordinates: (" + this.x + ", " + this.y + ", " + this.z + ")");
      }
    }
  }, {
    key: "getNeighbours",
    value: function getNeighbours() {
      var neighbours = [];

      this.neighbours.forEach(function (neighbour) {
        neighbours.push(this.add(neighbour));
      }, this);

      return neighbours;
    }
  }, {
    key: "moveToDirection",
    value: function moveToDirection(direction) {
      return this.add(this.neighbours[direction]);
    }
  }, {
    key: "add",
    value: function add(cube) {
      return new Cube(this.x + cube.x, this.y + cube.y, this.z + cube.z);
    }
  }, {
    key: "subtract",
    value: function subtract(cube) {
      return new Cube(this.x - cube.x, this.y - cube.y, this.z - cube.z);
    }
  }, {
    key: "scale",
    value: function scale(_scale) {
      return new Cube(this.x * _scale, this.y * _scale, this.z * _scale);
    }
  }, {
    key: "distanceTo",
    value: function distanceTo(cube) {
      return Math.max(Math.abs(this.x - cube.x), Math.abs(this.y - cube.y), Math.abs(this.z - cube.z));
    }
  }, {
    key: "equals",
    value: function equals(cube) {
      return this.x === cube.x && this.y === cube.y && this.z === cube.z;
    }
  }, {
    key: "getFacing",
    value: function getFacing(neighbour) {
      var index = -1;

      var delta = neighbour.subtract(this);

      this.neighbours.some(function (hex, i) {
        if (delta.equals(hex)) {
          index = i;
          return true;
        }
      });

      return index;
    }
  }, {
    key: "toOffset",
    value: function toOffset() {
      var q = this.x + (this.z + (this.z & 1)) / 2;
      var r = this.z;

      return new _Offset2.default(q, r); //EVEN_R
    }

    /*
          Cube.prototype.toOffset = function()
          {
              var q = this.x + (this.z - (this.z & 1)) / 2;
              var r = this.z;
      
              return new hexagon.Offset(q, r); //ODD_R
          };
      */

  }, {
    key: "toString",
    value: function toString() {
      return "(" + this.x + "," + this.y + "," + this.z + ")";
    }
  }, {
    key: "_formatNumber",
    value: function _formatNumber(number) {
      return parseFloat(number.toFixed(PRECISION));
    }
  }]);

  return Cube;
}();

if (typeof window.hexagon === "undefined") window.hexagon = {};
window.hexagon.Cube = Cube;

exports.default = window.hexagon.Cube;

},{"./Offset":329}],329:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Cube = require("./Cube");

var _Cube2 = _interopRequireDefault(_Cube);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Offset = function () {
  function Offset(q, r) {
    _classCallCheck(this, Offset);

    if (q instanceof Offset || q.q !== undefined && q.r !== undefined) {
      var offset = q;
      this.q = offset.q;
      this.r = offset.r;
    } else {
      this.q = q;
      this.r = r;
    }

    this.neighbours = [[{ q: 1, r: 0 }, { q: 1, r: -1 }, { q: 0, r: -1 }, { q: -1, r: 0 }, { q: 0, r: 1 }, { q: 1, r: 1 }], [{ q: 1, r: 0 }, { q: 0, r: -1 }, { q: -1, r: -1 }, { q: -1, r: 0 }, { q: -1, r: 1 }, { q: 0, r: 1 }]];
  }

  _createClass(Offset, [{
    key: "getNeighbours",
    value: function getNeighbours() {
      var neighbours = [];

      this.neighbours[this.r & 1].forEach(function (neighbour) {
        neighbours.push(this.add(new hexagon.Offset(neighbour)));
      }, this);

      return neighbours;
    }
  }, {
    key: "add",
    value: function add(offset) {
      return this.toCube().add(offset.toCube()).toOffset();
    }
  }, {
    key: "subtract",
    value: function subtract(offset) {
      return this.toCube().subtract(offset.toCube()).toOffset();
    }
  }, {
    key: "scale",
    value: function scale(_scale) {
      return this.toCube().scale(_scale).toOffset();
    }
  }, {
    key: "moveToDirection",
    value: function moveToDirection(direction) {
      return this.toCube().moveToDirection(direction).toOffset();
    }
  }, {
    key: "equals",
    value: function equals(offset) {
      return this.q === offset.q && this.r === offset.r;
    }
  }, {
    key: "getNeighbourAtDirection",
    value: function getNeighbourAtDirection(direction) {
      var neighbours = this.getNeighbours();

      return neighbours[direction];
    }
  }, {
    key: "distanceTo",
    value: function distanceTo(target) {
      return this.toCube().distanceTo(target.toCube());
    }
  }, {
    key: "toCube",
    value: function toCube() {
      var x = this.q - (this.r + (this.r & 1)) / 2;
      var z = this.r;
      var y = -x - z;

      /*
          var x, y, z;
          switch (this.layout) {
              case Offset.ODD_R:
                  x = this.q - (this.r - (this.r & 1)) / 2;
                  z = this.r;
                  y = -x - z;
                  break;
              case Offset.EVEN_R:
                  x = this.q - (this.r + (this.r&1)) / 2;
                  z = this.r;
                  y = -x - z;
                  break;
          }
          */

      return new _Cube2.default(x, y, z).round();
    }
  }]);

  return Offset;
}();

if (typeof window.hexagon === "undefined") window.hexagon = {};
window.hexagon.Offset = Offset;

exports.default = Offset;

},{"./Cube":328}],330:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.Offset = exports.Cube = undefined;

var _Cube = require("./Cube");

var _Cube2 = _interopRequireDefault(_Cube);

var _Offset = require("./Offset");

var _Offset2 = _interopRequireDefault(_Offset);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.Cube = _Cube2.default;
exports.Offset = _Offset2.default;

},{"./Cube":328,"./Offset":329}],331:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.terrain = exports.animation = exports.hexagon = exports.SystemFactory = exports.ShipSystem = exports.Ship = undefined;

var _Ship = require("./Ship");

var _Ship2 = _interopRequireDefault(_Ship);

var _ShipSystem = require("./system/ShipSystem");

var _ShipSystem2 = _interopRequireDefault(_ShipSystem);

var _SystemFactory = require("./SystemFactory");

var _SystemFactory2 = _interopRequireDefault(_SystemFactory);

var _hexagon = require("./hexagon/");

var hexagon = _interopRequireWildcard(_hexagon);

var _animation = require("./animation");

var animation = _interopRequireWildcard(_animation);

var _terrain = require("./terrain");

var terrain = _interopRequireWildcard(_terrain);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.Ship = _Ship2.default;
exports.ShipSystem = _ShipSystem2.default;
exports.SystemFactory = _SystemFactory2.default;
exports.hexagon = hexagon;
exports.animation = animation;
exports.terrain = terrain;

},{"./Ship":312,"./SystemFactory":313,"./animation":323,"./hexagon/":330,"./system/ShipSystem":332,"./terrain":334}],332:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ShipSystem = function () {
  function ShipSystem(json, ship) {
    _classCallCheck(this, ShipSystem);

    this.ship = ship;

    for (var i in json) {
      this[i] = json[i];
    }
  }

  _createClass(ShipSystem, [{
    key: "initBoostableInfo",
    value: function initBoostableInfo() {
      return this;
    }
  }, {
    key: "hasMaxBoost",
    value: function hasMaxBoost() {
      return false;
    }
  }, {
    key: "isScanner",
    value: function isScanner() {
      return false;
    }
  }, {
    key: "isDestroyed",
    value: function isDestroyed() {
      return this.destroyed;
    }
  }]);

  return ShipSystem;
}();

window.ShipSystem = ShipSystem;
exports.default = ShipSystem;

/*

var Fighter = function Fighter(json, staticFighter, ship) {
  Object.keys(staticFighter).forEach(function(key) {
    this[key] = staticFighter[key];
  }, this);

  for (var i in json) {
    if (i == "systems") {
      this.systems = SystemFactory.createSystemsFromJson(json[i], ship, this);
    } else {
      this[i] = json[i];
    }
  }
};

Fighter.prototype = Object.create(ShipSystem.prototype);
Fighter.prototype.constructor = Fighter;

var SuperHeavyFighter = function SuperHeavyFighter(json, ship) {
  Object.keys(staticFighter).forEach(function(key) {
    this[key] = staticFighter[key];
  }, this);

  for (var i in json) {
    if (i == "systems") {
      this.systems = SystemFactory.createSystemsFromJson(json[i], ship, this);
    } else {
      this[i] = json[i];
    }
  }
};

SuperHeavyFighter.prototype = Object.create(ShipSystem.prototype);
SuperHeavyFighter.prototype.constructor = SuperHeavyFighter;

var Weapon = function Weapon(json, ship) {
  ShipSystem.call(this, json, ship);
  this.targetsShips = true;
};

Weapon.prototype = Object.create(ShipSystem.prototype);
Weapon.prototype.constructor = Weapon;

Weapon.prototype.getAmmo = function(fireOrder) {
  return null;
};

Weapon.prototype.translateFCtoD100txt = function(fireControl) {
  var FCtxt = "";
  var i = 0;
  var toAdd;
  for (i = 0; i <= 2; i++) {
    toAdd = fireControl[i];
    if (fireControl[i] === null) {
      toAdd = "-";
    } else {
      toAdd = toAdd * 5; //d20 to d100
    }
    FCtxt = FCtxt + toAdd;
    if (i < 2) FCtxt = FCtxt + "/";
  }
  return FCtxt;
}; //endof Weapon.prototype.translateFCtoD100txt

Weapon.prototype.changeFiringMode = function() {
  var mode = this.firingMode + 1;

  if (this.firingModes[mode]) {
    this.firingMode = mode;
  } else {
    this.firingMode = 1;
  }

  //set data for that firing mode...
  //change both attributes (used in various situations) and .data array (used for display)
  if (!mathlib.arrayIsEmpty(this.maxDamageArray))
    this.maxDamage = this.maxDamageArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.minDamageArray)) {
    this.minDamage = this.minDamageArray[this.firingMode];
    this.data["Damage"] = this.minDamage;
    if (this.maxDamage > 0)
      this.data["Damage"] = this.data["Damage"] + "-" + this.maxDamage;
  }
  if (!mathlib.arrayIsEmpty(this.priorityArray)) {
    this.priority = this.priorityArray[this.firingMode];
    this.data["Resolution Priority"] = this.priority;
  }
  if (!mathlib.arrayIsEmpty(this.rangeDamagePenaltyArray))
    this.rangeDamagePenalty = this.rangeDamagePenaltyArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.rangePenaltyArray)) {
    this.rangePenalty = this.rangePenaltyArray[this.firingMode];
    this.data["Range penalty"] = this.rangePenalty + " per hex";
  }
  if (!mathlib.arrayIsEmpty(this.rangeArray)) {
    this.range = this.rangeArray[this.firingMode];
    this.data["Range"] = this.range;
  }
  if (!mathlib.arrayIsEmpty(this.fireControlArray)) {
    this.fireControl = this.fireControlArray[this.firingMode];
    this.data["Fire control (fighter/med/cap)"] = this.translateFCtoD100txt(
      this.fireControl
    );
    //this.fireControl[0]+'/'+this.fireControl[1]+'/'+this.fireControl[2];
  }
  if (!mathlib.arrayIsEmpty(this.loadingtimeArray))
    this.loadingtime = this.loadingtimeArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.turnsloadedArray))
    this.turnsloaded = this.turnsloadedArray[this.firingMode];
  this.data["Loading"] = this.turnsloaded + "/" + this.loadingtime;
  if (!mathlib.arrayIsEmpty(this.extraoverloadshotsArray))
    this.extraoverloadshots = this.extraoverloadshotsArray[this.firingMode];

  if (!mathlib.arrayIsEmpty(this.uninterceptableArray))
    this.uninterceptable = this.uninterceptableArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.shotsArray))
    this.shots = this.shotsArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.damageTypeArray)) {
    this.damageType = this.damageTypeArray[this.firingMode];
    this.data["Damage type"] = this.damageType;
  }
  if (!mathlib.arrayIsEmpty(this.weaponClassArray)) {
    this.weaponClass = this.weaponClassArray[this.firingMode];
    this.data["Weapon type"] = this.weaponClass;
  }
  if (!mathlib.arrayIsEmpty(this.defaultShotsArray))
    this.defaultShots = this.defaultShotsArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.groupingArray))
    this.grouping = this.groupingArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.gunsArray))
    this.guns = this.gunsArray[this.firingMode];

  //firing animation related...
  if (!mathlib.arrayIsEmpty(this.animationArray))
    this.animation = this.animationArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.animationImgArray))
    this.animationImg = this.animationImgArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.animationImgSpriteArray))
    this.animationImgSprite = this.animationImgSpriteArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.animationColorArray))
    this.animationColor = this.animationColorArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.animationColor2Array))
    this.animationColor2 = this.animationColor2Array[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.animationWidthArray))
    this.animationWidth = this.animationWidthArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.animationExplosionScaleArray))
    this.animationExplosionScale = this.animationExplosionScaleArray[
      this.firingMode
    ];
  if (!mathlib.arrayIsEmpty(this.animationExplosionTypeArray))
    this.animationExplosionType = this.animationExplosionTypeArray[
      this.firingMode
    ];
  if (!mathlib.arrayIsEmpty(this.explosionColorArray))
    this.explosionColor = this.explosionColorArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.trailLengthArray))
    this.trailLength = this.trailLengthArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.trailColorArray))
    this.trailColor = this.trailColorArray[this.firingMode];
  if (!mathlib.arrayIsEmpty(this.projectilespeedArray))
    this.projectilespeed = this.projectilespeedArray[this.firingMode];
}; //end of Weapon.prototype.changeFiringMode

Weapon.prototype.getTurnsloaded = function() {
  return this.turnsloaded;
};

Weapon.prototype.getInterceptRating = function() {
  return this.intercept;
};

var Ballistic = function Ballistic(json, ship) {
  Weapon.call(this, json, ship);
};

Ballistic.prototype = Object.create(Weapon.prototype);
Ballistic.prototype.constructor = Ballistic;
*/

},{}],333:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _particle = require("../animation/particle");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var StarField = function () {
  function StarField(scene) {
    _classCallCheck(this, StarField);

    this.starCount = 5000;
    this.emitterContainer = null;
    this.scene = scene;
    this.lastAnimationTime = null;
    this.totalAnimationTime = 0;
    this.zoomChanged = 0;

    this.getRandom = null;

    this.create();
  }

  _createClass(StarField, [{
    key: "create",
    value: function create() {
      //this.scene.background = new THREE.Color(10 / 255, 10 / 255, 30 / 255);

      this.cleanUp();

      this.emitterContainer = new _particle.ParticleEmitterContainer(this.scene, this.starCount, _particle.StarParticleEmitter);

      //this.webglScene.scene.background = new THREE.Color(10/255, 10/255, 30/255);
      var width = 3000; //this.webglScene.width * 1.5;
      var height = 2000; // this.webglScene.height * 1.5;

      this.getRandom = mathlib.getSeededRandomGenerator(gamedata.gameid);

      //var stars = Math.floor(this.starCount * (width / 4000));
      var stars = this.starCount;
      while (stars--) {
        this.createStar(width, height);

        if (this.getRandom() > 0.98) {
          this.createShiningStar(width, height);
        }
      }

      var gas = Math.floor(this.getRandom() * 5) + 8;

      /*
          while(gas--){
              this.createGasCloud(width, height)
          }
          */

      this.emitterContainer.start();
      this.lastAnimationTime = new Date().getTime();
      this.totalAnimationTime = 0;
      this.zoomChanged = 1;
      return this;
    }
  }, {
    key: "cleanUp",
    value: function cleanUp() {
      if (this.emitterContainer) {
        this.emitterContainer.cleanUp();
        this.emitterContainer = null;
      }
    }
  }, {
    key: "render",
    value: function render() {
      if (!this.emitterContainer) {
        this.create();
      }

      var deltaTime = new Date().getTime() - this.lastAnimationTime;
      this.totalAnimationTime += deltaTime;
      this.emitterContainer.render(0, this.totalAnimationTime, 0, 0, this.zoomChanged);

      if (this.zoomChanged === 1) {
        this.zoomChanged = 0;
      }

      this.lastAnimationTime = new Date().getTime();
    }
  }, {
    key: "createStar",
    value: function createStar(width, height) {
      var particle = this.emitterContainer.getParticle(this);

      var x = (this.getRandom() - 0.5) * width * 1.5;
      var y = (this.getRandom() - 0.5) * height * 1.5;

      particle.setActivationTime(0).setSize(2 + this.getRandom() * 2).setOpacity(this.getRandom() * 0.2 + 0.9).setPosition({ x: x, y: y }).setColor(new THREE.Color(1, 1, 1)).setParallaxFactor(0.1 + this.getRandom() * 0.1);

      if (this.getRandom() > 0.9) {
        particle.setSineFrequency(this.getRandom() * 200 + 50).setSineAmplitude(this.getRandom());
      }
    }
  }, {
    key: "createShiningStar",
    value: function createShiningStar(width, height) {
      var particle = this.emitterContainer.getParticle(this);

      var x = (this.getRandom() - 0.5) * width * 1.5;
      var y = (this.getRandom() - 0.5) * height * 1.5;

      var size = 6 + this.getRandom() * 6;
      var parallaxFactor = 0.1 + this.getRandom() * 0.1;
      var color = new THREE.Color(this.getRandom() * 0.4 + 0.6, this.getRandom() * 0.2 + 0.8, this.getRandom() * 0.4 + 0.6);

      particle.setActivationTime(0).setSize(size * 0.5).setOpacity(this.getRandom() * 0.2 + 0.9).setPosition({ x: x, y: y }).setColor(new THREE.Color(1, 1, 1)).setParallaxFactor(parallaxFactor);

      particle = this.emitterContainer.getParticle(this);
      particle.setActivationTime(0).setSize(size).setOpacity(this.getRandom() * 0.1 + 0.1).setPosition({ x: x, y: y }).setColor(color).setParallaxFactor(parallaxFactor).setSineFrequency(this.getRandom() * 200 + 100).setSineAmplitude(this.getRandom() * 0.4);

      var shines = Math.round(this.getRandom() * 8) - 3;

      if (shines <= 2) {
        return;
      }

      var angle = this.getRandom() * 360;
      var angleChange = (this.getRandom() - 0.5) * 0.01;

      while (shines--) {
        angle += this.getRandom() * 60 + 40;
        particle = this.emitterContainer.getParticle(this);
        particle.setActivationTime(0).setSize(size * this.getRandom() * 10 + 10).setOpacity(this.getRandom() * 0.1 + 0.1).setPosition({ x: x, y: y }).setColor(color).setParallaxFactor(parallaxFactor).setSineFrequency(this.getRandom() * 200 + 100).setSineAmplitude(0.1).setAngle(angle).setAngleChange(angleChange).setTexture(particle.texture.starLine);
      }
    }
  }, {
    key: "createGasCloud",
    value: function createGasCloud(width, height) {
      var gas = Math.floor(this.getRandom() * 10 + 10);

      var position = {
        x: (this.getRandom() - 0.5) * width,
        y: (this.getRandom() - 0.5) * height
      };

      var vector = {
        x: this.getRandomBand(0.5, 1) * width / 100,
        y: this.getRandomBand(0.5, 1) * width / 100
      };

      var iterations = Math.floor(this.getRandom() * 3) + 5;

      while (iterations--) {
        this.createGasCloudPart({ x: position.x, y: position.y }, width);
        position.x += this.getRandomBand(0, 1) * 50 + vector.x;
        position.y += this.getRandomBand(0, 1) * 50 + vector.y;
      }
    }
  }, {
    key: "getRandomBand",
    value: function getRandomBand(min, max) {
      var random = this.getRandom() * (max - min) + min;
      return this.getRandom() > 0.5 ? random * -1 : random;
    }
  }, {
    key: "createGasCloudPart",
    value: function createGasCloudPart(position, width) {
      var gas = Math.floor(this.getRandom() * 5 + 5);
      var baseRotation = (this.getRandom() - 0.5) * 0.002;

      while (gas--) {
        this.createGas(position, baseRotation, this.getRandom() * width * 0.4 + width * 0.4);
      }
    }
  }, {
    key: "createGas",
    value: function createGas(position, baseRotation, size) {
      var particle = this.emitterContainer.getParticle(this);

      position.x += (this.getRandom() - 0.5) * 100;
      position.y += (this.getRandom() - 0.5) * 100;

      particle.setActivationTime(0).setSize(this.getRandom() * size * 0.5 + size * 0.5).setOpacity(this.getRandom() * 0.005 + 0.005).setPosition({ x: position.x, y: position.y }).setColor(new THREE.Color(104 / 255, 204 / 255, 249 / 255)).setTexture(particle.texture.gas).setAngle(this.getRandom() * 360).setAngleChange(baseRotation + (this.getRandom() - 0.5) * 0.001).setParallaxFactor(0.1 + this.getRandom() * 0.1);

      if (this.getRandom() > 0.9) {
        particle.setActivationTime(0).setSize(this.getRandom() * size * 0.25 + size * 0.25).setOpacity(0).setPosition({ x: position.x, y: position.y }).setColor(new THREE.Color(1, 1, 1)).setTexture(particle.texture.gas).setAngle(this.getRandom() * 360).setAngleChange(baseRotation + (this.getRandom() - 0.5) * 0.01).setParallaxFactor(0.1 + this.getRandom() * 0.1).setSineFrequency(this.getRandom() * 200 + 200).setSineAmplitude(this.getRandom() * 0.02);
      }
    }
  }]);

  return StarField;
}();

exports.default = StarField;

},{"../animation/particle":327}],334:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.StarField = undefined;

var _StarField = require("./StarField");

var _StarField2 = _interopRequireDefault(_StarField);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.StarField = _StarField2.default;
exports.StarField = _StarField2.default;

},{"./StarField":333}],335:[function(require,module,exports){
"use strict";

require("@babel/polyfill");

var _ships = require("./ships");

var _ships2 = _interopRequireDefault(_ships);

var _movement = require("./handler/movement");

var Movement = _interopRequireWildcard(_movement);

var _phase = require("./handler/phase");

var Phase = _interopRequireWildcard(_phase);

var _uiStrategy = require("./uiStrategy");

var UiStrategy = _interopRequireWildcard(_uiStrategy);

var _model = require("./model/");

var Model = _interopRequireWildcard(_model);

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

window.shipObjects = _ships2.default;

},{"./handler/movement":308,"./handler/phase":311,"./model/":331,"./ships":340,"./uiStrategy":346,"@babel/polyfill":1}],336:[function(require,module,exports){
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

    _this.defaultHeight = 50;
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
        object.position.set(0, 0, _this2.shipZ);
      });
    }
  }]);

  return Capital;
}(_ShipObject3.default);

exports.default = Capital;

},{"./ShipObject":339}],337:[function(require,module,exports){
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

      _get(Gunship.prototype.__proto__ || Object.getPrototypeOf(Gunship.prototype), "create", this).call(this);

      window.Loader.loadObject("img/3d/gunship/gunship.obj", function (object) {
        window.Loader.loadTexturesAndAssign(object.children[0], {}, null, "img/3d/gunship/normal.png");

        window.Loader.loadTexturesAndAssign(object.children[1], {}, null, "img/3d/turretNormal.png");
        window.Loader.loadTexturesAndAssign(object.children[2], {}, null, "img/3d/turretNormal.png");
        window.Loader.loadTexturesAndAssign(object.children[3], {}, null, "img/3d/turretNormal.png");
        window.Loader.loadTexturesAndAssign(object.children[6], {}, null, "img/3d/turretNormal.png");
        window.Loader.loadTexturesAndAssign(object.children[7], {}, null, "img/3d/turretNormal.png");
        window.Loader.loadTexturesAndAssign(object.children[4], {}, "img/3d/diffuseThruster.png", "img/3d/normalThruster.png");
        window.Loader.loadTexturesAndAssign(object.children[5], {}, "img/3d/diffuseThruster.png", "img/3d/normalThruster.png");

        object.scale.set(5, 5, 5);
        _this2.startRotation = { x: 90, y: 90, z: 0 };
        _this2.shipObject = object;
        _this2.setRotation(_this2.rotation.x, _this2.rotation.y, _this2.rotation.z);
        _this2.mesh.add(_this2.shipObject);
        object.position.set(0, 0, _this2.shipZ);
      });
    }
  }]);

  return Gunship;
}(_ShipObject3.default);

exports.default = Gunship;

},{"./ShipObject":339}],338:[function(require,module,exports){
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
    key: "create",
    value: function create() {
      var _this2 = this;

      _get(Rhino.prototype.__proto__ || Object.getPrototypeOf(Rhino.prototype), "create", this).call(this);

      window.Loader.loadObject("img/3d/rhino/rhino.obj", function (object) {
        window.Loader.loadTexturesAndAssign(object.children[0], {
          normalScale: new THREE.Vector2(1, 1),
          shininess: 10,
          color: new THREE.Color(1, 1, 1)
        }, "img/3d/rhino/texture.png", "img/3d/rhino/sculptNormal.png");
        window.Loader.loadTexturesAndAssign(object.children[1], {}, "img/3d/diffuseDoc.png", "img/3d/normalDoc.png");
        window.Loader.loadTexturesAndAssign(object.children[2], {}, "img/3d/diffuseThruster.png", "img/3d/normalThruster.png");

        object.scale.set(2, 2, 2);
        _this2.startRotation = { x: 90, y: 90, z: 0 };

        _this2.shipObject = object;
        _this2.setRotation(_this2.rotation.x, _this2.rotation.y, _this2.rotation.z);
        _this2.mesh.add(_this2.shipObject);
        object.position.set(0, 0, _this2.shipZ);
      });
    }
  }]);

  return Rhino;
}(_ShipObject3.default);

exports.default = Rhino;

},{"./ShipObject":339}],339:[function(require,module,exports){
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
    var _this = this;

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
    this.shipZ = null;

    this.movements = null;

    this.hidden = false;

    this.startRotation = { x: 0, y: 0, z: 0 };
    this.rotation = { x: 0, y: 0, z: 0 };

    this.loadedResolve = null;
    this.loaded = new Promise(function (resolve, reject) {
      _this.loadedResolve = resolve;
    });

    this.consumeShipdata(this.ship);
  }

  _createClass(ShipObject, [{
    key: "getLoadedPromise",
    value: function getLoadedPromise() {
      return this.loaded;
    }
  }, {
    key: "consumeShipdata",
    value: function consumeShipdata(ship) {
      this.ship = ship;
      this.consumeMovement(ship.movement);
      this.consumeEW(ship);
    }
  }, {
    key: "createMesh",
    value: function createMesh() {
      if (this.shipZ === null) {
        this.shipZ = this.defaultHeight;
      }

      var opacity = 0.5;
      this.line = new window.LineSprite({ x: 0, y: 0, z: 1 }, { x: 0, y: 0, z: this.defaultHeight }, 1, this.mine ? COLOR_MINE : COLOR_ENEMY, opacity);
      this.mesh.add(this.line.mesh);

      this.shipSideSprite = new window.ShipSelectedSprite({ width: this.sideSpriteSize, height: this.sideSpriteSize }, 0.01, opacity);
      this.shipSideSprite.setOverlayColor(this.mine ? COLOR_MINE : COLOR_ENEMY);
      this.shipSideSprite.setOverlayColorAlpha(1);
      this.mesh.add(this.shipSideSprite.mesh);

      this.shipEWSprite = new window.ShipEWSprite({ width: this.sideSpriteSize * 1.5, height: this.sideSpriteSize }, this.defaultHeight);
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
      this.loadedResolve(true);
    }
  }, {
    key: "setPosition",
    value: function setPosition(x, y) {
      var z = 0;
      if ((typeof x === "undefined" ? "undefined" : _typeof(x)) === "object") {
        y = x.y;
        x = x.x;
      }

      this.position = { x: x, y: y, z: 0 };

      if (this.mesh) {
        this.mesh.position.set(x, y, 0);
      }

      if (this.shipObject) {
        this.shipObject.position.set(0, 0, this.shipZ);
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
    key: "setSideSpriteOpacity",
    value: function setSideSpriteOpacity(opacity) {
      this.shipSideSprite.multiplyOpacity(opacity);
      this.line.multiplyOpacity(opacity);
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

    /*
    positionAndFaceIcon(offset, movementService) {
      var movement = movementService.getLastEndMove(this.ship);
      var gamePosition = window.coordinateConverter.fromHexToGame(
        movement.position
      );
       if (offset) {
        gamePosition.x += offset.x;
        gamePosition.y += offset.y;
      }
       var facing = mathlib.hexFacingToAngle(movement.facing);
       gamePosition.z = this.defaultHeight;
      this.setPosition(gamePosition);
      this.setFacing(-facing);
    }
    */

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

},{"../handler/Movement":298}],340:[function(require,module,exports){
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

},{"./Capital":336,"./Gunship":337,"./Rhino":338}],341:[function(require,module,exports){
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

    var _this = _possibleConstructorReturn(this, (HighlightSelectedShip.__proto__ || Object.getPrototypeOf(HighlightSelectedShip)).call(this));

    _this.ship = null;
    _this.lastAnimationTime = null;
    _this.totalTime = 0;

    _this.amplitude = 1;
    _this.frequency = 300;
    return _this;
  }

  _createClass(HighlightSelectedShip, [{
    key: "deactivated",
    value: function deactivated() {
      this.reset();
      this.ship = null;
    }
  }, {
    key: "setShipSelected",
    value: function setShipSelected(_ref) {
      var ship = _ref.ship;

      this.ship = ship;
    }
  }, {
    key: "shipDeselected",
    value: function shipDeselected(_ref2) {
      var ship = _ref2.ship;

      this.reset();
      this.ship = null;
    }
  }, {
    key: "render",
    value: function render(_ref3) {
      var coordinateConverter = _ref3.coordinateConverter,
          scene = _ref3.scene,
          zoom = _ref3.zoom;

      var now = new Date().getTime();

      var delta = this.lastAnimationTime !== null ? now - this.lastAnimationTime : 0;

      this.totalTime += delta;

      this.lastAnimationTime = now;

      if (!this.ship) {
        return;
      }

      var opacity = this.amplitude * 0.5 * Math.sin(this.totalTime / this.frequency) + this.amplitude;

      this.shipIconContainer.getByShip(this.ship).setSideSpriteOpacity(opacity);
    }
  }, {
    key: "reset",
    value: function reset() {
      if (this.ship) this.shipIconContainer.getByShip(this.ship).setSideSpriteOpacity(1);
    }
  }]);

  return HighlightSelectedShip;
}(_UiStrategy3.default);

exports.default = HighlightSelectedShip;

},{"./UiStrategy":345}],342:[function(require,module,exports){
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

},{"./UiStrategy":345}],343:[function(require,module,exports){
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
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged(_ref4) {
      var ship = _ref4.ship;

      if (this.ship === ship) {
        this.shipIconContainer.showMovementPath(ship);
      }
    }
  }]);

  return MovementPathSelectedShip;
}(_UiStrategy3.default);

exports.default = MovementPathSelectedShip;

},{"./UiStrategy":345}],344:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _UiStrategy2 = require("./UiStrategy");

var _UiStrategy3 = _interopRequireDefault(_UiStrategy2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _asyncToGenerator(fn) { return function () { var gen = fn.apply(this, arguments); return new Promise(function (resolve, reject) { function step(key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { return Promise.resolve(value).then(function (value) { step("next", value); }, function (err) { step("throw", err); }); } } return step("next"); }); }; }

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
      this.ship = null;
    }
  }, {
    key: "setShipSelected",
    value: function () {
      var _ref2 = _asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee(_ref) {
        var ship = _ref.ship;
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                this.ship = ship;
                _context.next = 3;
                return this.shipIconContainer.getByShip(ship).getLoadedPromise();

              case 3:
                if (!(this.ship !== ship)) {
                  _context.next = 5;
                  break;
                }

                return _context.abrupt("return");

              case 5:

                console.log("SHOW 1");
                this.uiManager.showMovementUi({
                  ship: ship,
                  movementService: this.movementService
                });

                reposition(this.ship, this.shipIconContainer, this.uiManager);

              case 8:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function setShipSelected(_x) {
        return _ref2.apply(this, arguments);
      }

      return setShipSelected;
    }()
  }, {
    key: "shipDeselected",
    value: function shipDeselected(_ref3) {
      var ship = _ref3.ship;

      this.uiManager.hideMovementUi();
      this.ship = null;
    }
  }, {
    key: "onScroll",
    value: function onScroll() {
      reposition(this.ship, this.shipIconContainer, this.uiManager);
    }
  }, {
    key: "onZoom",
    value: function onZoom() {
      reposition(this.ship, this.shipIconContainer, this.uiManager);
    }
  }, {
    key: "shipMovementChanged",
    value: function shipMovementChanged(_ref4) {
      var ship = _ref4.ship;

      if (this.ship !== ship) {
        return;
      }

      console.log("SHOW 2");
      this.uiManager.showMovementUi({
        ship: ship,
        movementService: this.movementService
      });

      reposition(this.ship, this.shipIconContainer, this.uiManager);
    }
  }]);

  return SelectedShipMovementUi;
}(_UiStrategy3.default);

var reposition = function reposition(ship, shipIconContainer, uiManager) {
  if (!ship) {
    console.log("no ship");
    return;
  }

  uiManager.repositionMovementUi(window.coordinateConverter.fromGameToViewPort(shipIconContainer.getByShip(ship).getPosition()));
};

exports.default = SelectedShipMovementUi;

},{"./UiStrategy":345}],345:[function(require,module,exports){
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
  }, {
    key: "render",
    value: function render(_ref7) {
      var coordinateConverter = _ref7.coordinateConverter,
          scene = _ref7.scene,
          zoom = _ref7.zoom;
    }
  }]);

  return UiStrategy;
}();

exports.default = UiStrategy;

},{}],346:[function(require,module,exports){
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

},{"./HighlightSelectedShip":341,"./MovementPathMouseOver":342,"./MovementPathSelectedShip":343,"./SelectedShipMovementUi":344}]},{},[335]);
